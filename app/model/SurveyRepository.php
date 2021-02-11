<?php

namespace App\Model;

use Nette;

class SurveyRepository
{
	use Nette\SmartObject;

	private Nette\Database\Explorer $db;
	private $surveysById = [];

	public function __construct(Nette\Database\Explorer $db)
	{
		$this->db = $db;
	}

	public function getAll()
	{
		$surveys = [];
		foreach($this->db->table('survey')->fetchAll() as $surveyRow)
		{
			$survey = new Survey();
			$survey->id = $surveyRow->id;
			$survey->question = $surveyRow->question;
			$surveys[$survey->id] = $survey;
		}

		foreach($this->db->table('survey_choice')->fetchAll() as $surveyChoiceRow)
		{
			if(isset($surveys[$surveyChoiceRow->survey_id]))
			{
				$surveys[$surveyChoiceRow->survey_id]->choices[] = $surveyChoiceRow;
			}
		}

		return $surveys;
	}

	public function getSurveyById(int $surveyId): ?Survey
	{
		if(!isset($this->surveysById[$surveyId]))
		{
			$this->surveysById[$surveyId] = FALSE;
			$surveyRow = $this->db->table('survey')->where('id', $surveyId)->fetch();
			if($surveyRow)
			{
				$survey = new Survey();
				$survey->id = $surveyRow->id;
				$survey->question = $surveyRow->question;
				$this->surveysById[$surveyId] = $survey;
			}


			foreach($this->db->table('survey_choice')->where('survey_id', $surveyId)->fetchAll() as $surveyChoiceRow)
			{
				$this->surveysById[$surveyId]->choices[] = $surveyChoiceRow;
			}
		}
		return $this->surveysById[$surveyId] ?: NULL;
	}

	public function getDoneSurveyIds(int $userId):array
	{
		return $this->db->table('survey_user_tree')
			->where('user_id', $userId)
			->fetchPairs('survey_id', 'survey_id');
	}

	public function isUserVoteInSurvey(int $userId,int $surveyId):bool
	{
		return $this->db->table('survey_user_tree')
			->where('user_id', $userId)
			->where('survey_id', $surveyId)
			->fetch() ? TRUE : FALSE;
	}

	public function saveVote(int $userId,int $surveyId, int $surveyChoiceId)
	{
		$choice = $this->db->table('survey_choice')
			->where('survey_id', $surveyId)
			->where('id', $surveyChoiceId)
			->fetch();
		if(!$choice)
		{
			throw new \Exception('Volba nebyla v aknetě nalezena.');
		}

		$this->db->beginTransaction();

		$this->db->table('survey_user_tree')->insert([
			'user_id' => $userId,
			'survey_id' => $surveyId,
			'survey_choice_id' => $surveyChoiceId,
			'date_insert' => new \DateTime()
		]);

		$sql = 'UPDATE `survey_choice` SET 
				`count` = `count` + 1
				WHERE `id` = ?';
		$this->db->query($sql, $surveyChoiceId);

		$this->db->commit();
	}

	public function importData():void
	{
		$file = __DIR__ . '/../../files/import/surveys.xml';
		if(!file_exists($file))
		{
			echo "Soubor pro import nenalezen. Data vložte do souboru \"$file\" ";
			return;
		}

		$xml = simplexml_load_file($file);
		if(!$xml)
		{
			echo "Data nejsou validní";
			return;
		}

		$this->db->query('TRUNCATE `survey`');
		$this->db->query('TRUNCATE `survey_choice`');
		$this->db->query('TRUNCATE `survey_user_tree`');

		foreach($xml->survey as $survey)
		{
			$surveyData = [
				'question' => trim((string)$survey->question),
			];
			$surveyRow = $this->db->table('survey')
				->insert($surveyData);

			foreach($survey->choices->choice as $choice)
			{
				$surveyChoiceData = [
					'survey_id' => (int)$surveyRow->id,
					'choice' => trim((string)$choice),
				];
				$this->db->table('survey_choice')
					->insert($surveyChoiceData);
			}
		}

		echo 'OK';
	}
}