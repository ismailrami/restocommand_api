<?php
class PasswordController extends \BaseController {
	public function request()
	{
  		$credentials = array('email' => Input::get('email'), 'password' => Input::get('password'));
 		return Password::remind($credentials);
	}
}