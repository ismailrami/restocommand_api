<?php

class TvaController extends \BaseController {

	public function __construct()
	{
		//$this->beforeFilter('serviceAuth');
    	$this->beforeFilter('jwt');
    	$this->beforeFilter('serviceAdmin');
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
			return Response::json([
                    'tvas' =>$user->restorer->tvas->toArray()
                    ],
                Config::get('statuscode.OK')
            );
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
		try
		{
			$user = JWTAuth::login(Request::header('Accept'));
			$restorer = $user->restorer;
        	$tva=new Tva;
        	$tva->name=Input::get('name');
        	$tva->value=Input::get('value');
        	$tva = $restorer->tvas()->save($tva);
        	return Response::json('success', Config::get('statuscode.Created'));
        }
        catch(Exeption $ex)
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
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
		if($user->roles->first()->role=='admin')
		{
			$restorer = $user->restorer;	
		}
		else
		{
			$restorer=$user->worker->restorer;
		}
		$tva=$restorer->tvas->find($id);
			return Response::json([
                    'tva' => $tva
                    ],
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
		$user = JWTAuth::login(Request::header('Accept'));
		$tva=$user->restorer->tvas->find($id);
		$tva->name=Input::get('name');
		$tva->value=Input::get('value');
		if($tva->save())
		{
			return Response::json('success', Config::get('statuscode.Created'));
		}
		else
		{
			return Response::json('err', Config::get('statuscode.BADREQUEST'));
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
		$user = JWTAuth::login(Request::header('Accept'));
		if($user->restorer->tvas->find($id)->delete())
        {
        	return Response::json('success', Config::get('statuscode.OK'));
        }
        else
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
        }
	}


}
