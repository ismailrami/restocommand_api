<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');

	public function roles()
	{
		return $this->belongsToMany('Role');
	}
	
	public function restorer()
    {
        return $this->hasOne('Restorer');
    }
    public function worker()
    {
        return $this->hasOne('Worker');
    }
    public static $rules = [
    'create' => [
         'email' => 'email|unique:users',
         'login' => 'Alpha|unique:users',
         'first_name' => 'Alpha',
         'last_name' => 'Alpha',
         'password' => 'Required|min:8'
        ],
    'editRestorer'   => [
        'email' => 'Required|email|unique:users,id',
        'login' => 'Required|Alpha|unique:users,id',
        'first_name' => 'Alpha',
        'last_name' => 'Alpha',
        ],
    'loginEmail'   => [
        'email' => 'Required|email',
        'login' => 'Alpha',
        'password' => 'Required',
        ],
    'loginWorker'   => [
        'login' => 'Required',
        'password' => 'Required',
        ],
    'updateWorker' =>[
        'login' => 'Required|Alpha|unique:users,id',
        'first_name' => 'Alpha',
        'last_name' => 'Alpha',
    ]
     ];
}
