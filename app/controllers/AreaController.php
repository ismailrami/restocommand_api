<?php

class AreaController extends \BaseController {

	public function __construct()
	{
    	$this->beforeFilter('jwt');
    	$this->beforeFilter('serviceAdmin' ,array('only' => array('store','update','destroy')));
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
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
		$areas=$restorer->areas;
			return Response::json([
                    'areas' => $areas->toArray()
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
			$validator = Validator::make(Input::all(), Area::$rules);
			if ($validator->fails()) 
			{
				
				return Response::json($validator->errors(),Config::get('statuscode.BADREQUEST'));
				
        	}
        	else
        	{
        		$user = JWTAuth::login(Request::header('Accept'));
				$restorer = $user->restorer;
        		$area=new Area;
        		$area->name=Input::get('name');
        		$area = $restorer->areas()->save($area);
        		return Response::json('success', Config::get('statuscode.Created'));

        	}
        }
        catch(Exeption $ex)
        {
        	return Response::json('error', Config::get('statuscode.BADREQUEST'));
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
		try
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

			$area=$restorer->areas->find($id);
				return Response::json([
	                    'tables' => $area->tables->toArray()
	                    ],
	                Config::get('statuscode.OK')
	            );
			}
		catch(Exeption $ex)
		{
			return Response::json([
	                   
	                    ],
	                Config::get('statuscode.BADREQUEST')
	            );
		}
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
		try
		{
			$validator = Validator::make(Input::all(), Area::$rules);
			if ($validator->fails()) 
			{
				
				return Response::json($validator->errors(), Config::get('statuscode.BADREQUEST'));
				
        	}
        	else
        	{
				$area=Area::find($id);
				$area->name=Input::get('name');
	        	$area->save();
	        	return Response::json('success', Config::get('statuscode.OK'));
	        }
        }
        catch(Exeption $ex)
        {
        	return Response::json($validator->errors(), Config::get('statuscode.BADREQUEST'));
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
		try
		{
			$area=Area::find($id);
			$area->delete();
			return Response::json('success', Config::get('statuscode.OK'));
		}
		catch(Exeption $ex)
		{
			return Response::json($validator->errors(), Config::get('statuscode.BADREQUEST'));
		}
	}


}
