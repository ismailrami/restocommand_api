<?php
abstract class BaseValidator implements ValidatorInterface {

    protected $regles = array();
	protected $errors = array();

	public function fails()
	{
		$validation = Validator::make(Input::all(), $this->regles);

		if($validation->fails())
		{
			$this->errors = $validation->messages();
			return true;
		}
		return false;
	}

	public function errors()
	{
		return $this->errors;
	}

}