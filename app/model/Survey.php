<?php
namespace App\Model;

class Survey
{
	public $id;
	public $question;
	public $choices;

	public function __construct()
	{

	}

	public function getFormData()
	{
		$data = [];
		foreach($this->choices as $choice)
		{
			$data[$choice->id] = $choice->choice;
		}
		return $data;
	}
}