<?php

class AuthenticationController extends BaseController {


	public function login()
	{
		$rulesUser=User::$rules['loginWorker'];
		$validatorUser = Validator::make(Input::all(), $rulesUser);
		$validatorRestorer = Validator::make(Input::all(), Restorer::$rules);
		if ($validatorUser->fails() ) 
		{
			return Response::json([$validatorUser->errors()],Config::get('statuscode.BADREQUEST'));	
        }
        else
        {
		 	$credentials = array('login' =>  Input::get('login'),'password' =>  Input::get('password'));
 			if ( Auth::attempt($credentials,true) ) 
		    {
		    	$token = JWTAuth::fromUser(Auth::user());
            	return Response::json([
                    'user' => Auth::user()->toArray(),
                    'roles'=>Auth::user()->roles,
                    'token' =>$token
                    ],
                Config::get('statuscode.ACCEPTED')
            	);
        	}
        	else
        	{
            	return Response::json([
                    'flash' => 'Authentication failed'],
                Config::get('statuscode.UNAUTHORIZED')
                
            	);
            }
        }
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		if(Auth::logout())
		{
        	return Response::json([
                'flash' => 'you have been disconnected'],
            Config::get('statuscode.OK')
        	);
    	}
    	else
    	{
    		return Response::json([
                'flash' => 'you have been connected'],
            Config::get('statuscode.UNAUTHORIZED')
        	);
    	}
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
		$rulesUser=User::$rules['loginEmail'];
		$validatorUser = Validator::make(Input::all(), $rulesUser);
		$validatorRestorer = Validator::make(Input::all(), Restorer::$rules);
		if ($validatorUser->fails() ) 
		{
			return Response::json([$validatorUser->errors()],Config::get('statuscode.BADREQUEST'));	
        }
        else
        {
		 	$credentials = array('email' =>  Input::get('email'),'password' =>  Input::get('password'));
		    if ( Auth::attempt($credentials) ) 
		    {
		    	$token = JWTAuth::fromUser(Auth::user());
		        return Response::json([
		                'user' => Auth::user()->toArray(),
		                'token' =>$token,
		                'roles'=>Auth::user()->roles()->first()
		                    ],
		                Config::get('statuscode.ACCEPTED')
		            );
		 
		    }
		    else
		    {
		        return Response::json(['flash' => 'Authentication failed'],Config::get('statuscode.UNAUTHORIZED'));
		    }
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
		//
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
		//
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
