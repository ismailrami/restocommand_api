<?php

class WorkerController extends \BaseController {

	public function __construct()
	{
		//$this->beforeFilter('serviceAuth');
    	$this->beforeFilter('jwt');
    	//$this->beforeFilter('serviceAdmin');
    	//$this->beforeFilter('serviceAuth' ,array('only' => array('store')));
    	//$this->beforeFilter('serviceCSRF');
    	//$this->beforeFilter('serviceAdmin' ,array('only' => array('store')));
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = JWTAuth::login(Request::header('Accept'));
		$restorer = $user->restorer;
		$workers=$restorer->workers;
		$output = array();
		foreach ($workers as $worker) {
			$output[]= array(
				'id' => $worker->id,
				'last_name'=>$worker->user->last_name,
				'first_name'=>$worker->user->first_name,
				'login'=>$worker->user->login,
				'created_at'=>$worker->user->created_at,
				'last_connexion'=>$worker->user->updated_at,
				);
		}
		return Response::json(['worker'=>$output],Config::get('statuscode.OK'));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rulesUser=User::$rules['create'];
		$rulesUser['login']='Required|Alpha|unique:users';
		$validatorUser = Validator::make(Input::all(), $rulesUser);
		if ($validatorUser->fails() ) 
		{
			return Response::json([$validatorUser->errors()],Config::get('statuscode.BADREQUEST'));	
       	}
       	else
       	{
       		DB::beginTransaction();
 			try
			{
				$user = JWTAuth::login(Request::header('Accept'));
				$user1=$user;
				$restorer = $user1->restorer;
				$user=new User;
				$user->login=Input::get('login');
				$user->first_name=Input::get('first_name');
				$user->last_name=Input::get('last_name');
				$user->password = Hash::make(Input::get('password'));
				$user->save();
				$roles=Input::get('roles');
				foreach ($roles as $roleId) 
				{
					$role=Role::find($roleId);
	        		$user->roles()->attach($role);
				}
	        	$user->save();
				$worker = new Worker;
	        	$worker->user()->associate($user);
	        	$worker = $restorer->workers()->save($worker);
	        }
	        catch(Exception $ex)
			{
				DB::rollback();
				return Response::json($ex, Config::get('statuscode.BADREQUEST'));
			}
 			DB::commit();
 			return Response::json('success', Config::get('statuscode.Created'));
       	}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$user = JWTAuth::login(Request::header('Accept'));
		$worker=$user->restorer->workers->find($id);
		return Response::json(['worker'=>[
                'id' => $worker->id,
				'last_name'=>$worker->user->last_name,
				'first_name'=>$worker->user->first_name,
				'login'=>$worker->user->login,
				'roles'=>$worker->user->roles
                    ]],
                Config::get('statuscode.OK')
            );
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$rulesUser=User::$rules['updateWorker'];
		$rulesUser['login']='Required|Alpha|unique:users,id';
		$validatorUser = Validator::make(Input::all(), $rulesUser);
		if ($validatorUser->fails() ) 
		{
			return Response::json([$validatorUser->errors(),],Config::get('statuscode.BADREQUEST'));	
        }
        else
        {
        	DB::beginTransaction();
 			try
			{
				$worker=Worker::find($id);
				$user=$worker->user;
				$user->login=Input::get('login');
				$user->first_name=Input::get('first_name');
				$user->last_name=Input::get('last_name');
				$roles=$user->roles;
				foreach ($roles as $role) 
				{
				 $user->roles()->detach($role);
				}

				$roles=Input::get('roles');
				foreach ($roles as $roleId) 
				{
					$role=Role::find($roleId);
	        		$user->roles()->attach($role);
				}
				if(!Input::get('password'))
				{
					$user->password = Hash::make(Input::get('password'));
				}
				$user->save();
	       		$worker->save();
	       	}
	       	catch(Exception $ex)
			{
				DB::rollback();
				return Response::json($ex, Config::get('statuscode.BADREQUEST'));
			}
 			DB::commit();
 			return Response::json('success', Config::get('statuscode.Created'));
       	}
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		DB::beginTransaction();
 		try
		{
			$user = JWTAuth::login(Request::header('Accept'));
			$worker=$user->restorer->workers->find($id);
			/*$user=$worker->user;

			$roles=$user->roles;
			foreach ($roles as $role) 
			{
				$user->roles()->detach($role);
			}*/
	        $worker->delete();
	        //$user->delete();
	    }
	    catch(Exception $ex)
		{
			DB::rollback();
			return Response::json($ex, Config::get('statuscode.BADREQUEST'));
		}
 		DB::commit();
 		return Response::json('success', Config::get('statuscode.OK'));
	}


}
