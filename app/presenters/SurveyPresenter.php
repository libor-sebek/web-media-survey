<?php

class SurveyPresenter extends Nette\Application\UI\Presenter
{

	private \App\Model\SurveyRepository $surveyRepository;
	private \App\Model\UserRepository $userRepository;

	public function __construct(
		\App\Model\SurveyRepository $surveyRepository,
		\App\Model\UserRepository $userRepository
	)
	{
		$this->surveyRepository = $surveyRepository;
		$this->userRepository = $userRepository;
	}

	public function startup()
	{
		parent::startup();
	}

	public function renderImport()
	{
		$this->surveyRepository->importData();
		$this->terminate();
	}

	public function renderDefault()
	{
		$this->template->surveys = $this->surveyRepository->getAll();
		$request = $this->getHttpRequest();
		$user = $this->userRepository->getUser($request->getRemoteAddress() ,$request->getHeader('user-agent'));
		$this->template->doneSurveyIds = [];
		if($user)
		{
			$this->template->doneSurveyIds = $this->surveyRepository->getDoneSurveyIds((int)$user->id);
		}
	}

	protected function createComponentSurveyForm(): \Nette\Application\UI\Multiplier
	{
		return new \Nette\Application\UI\Multiplier(function ($surveyId) {
			$survey = $this->surveyRepository->getSurveyById((int)$surveyId);
			$form = new Nette\Application\UI\Form;
			$form->addHidden('surveyId', $surveyId);
			$form->addRadioList('surveyChoiceId', $survey->question, $survey->getFormData())
				->setRequired();
			$form->addSubmit('send', 'Hlasovat');
			$form->onSuccess[] = [$this, 'surveyFormSucceeded'];
			return $form;
		});
	}

	public function surveyFormSucceeded(Nette\Application\UI\Form $form, $data): void
	{
		$surveyId = (int)$data->surveyId;
		$surveyChoiceId = (int)$data->surveyChoiceId;
		$request = $this->getHttpRequest();
		$user = $this->userRepository->getUser($request->getRemoteAddress() ,$request->getHeader('user-agent'));

		if($user && $this->surveyRepository->isUserVoteInSurvey($user->id, $surveyId))
		{
			$this->flashMessage('V anketě jste již hlasoval', 'error');
			return;
		}

		try
		{
			$this->surveyRepository->saveVote($user->id, $surveyId, $surveyChoiceId);
		}
		catch(\Exception $e)
		{
			$this->flashMessage($e->getMessage(), 'error');
			return;
		}

		$this->flashMessage('Hlasování bylo započteno', 'success');
		$this->redirect('Survey:');
	}


}
