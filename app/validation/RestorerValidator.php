<?php
class RestorerValidator extends BaseValidator {

    public function __construct()
	{
		$this->regles = array(
			'name_restaurant' => 'required|min:5|max:20',
			'email'=>'email|unique:users,email,id'
		);
	}

}