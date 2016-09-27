<?php
class AreaValidator extends BaseValidator {

    public function __construct()
	{
		$this->regles = array(
			'name' => 'required|min:5|max:20'
		);
	}

}