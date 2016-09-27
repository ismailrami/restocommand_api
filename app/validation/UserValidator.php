<?php
class UserValidator extends BaseValidator {

    public function __construct()
	{
		$this->regles = array(
			'email'=>'email|unique:users,email,id'
		);
	}

}