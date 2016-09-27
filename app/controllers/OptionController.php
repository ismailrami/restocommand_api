<?php

class OptionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = JWTAuth::login(Request::header('Accept'));
		if($user->restorer)
		{
			return Response::json([
                    'option' => $user->restorer->options
                    ],
                Config::get('statuscode.OK')
            );	
		}
		else
		{
			return Response::json([
                    'options' => $user->worker->restorer->options
                    ],
                Config::get('statuscode.OK')
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
			$user = JWTAuth::login(Request::header('Accept'));
			$restorer = $user->restorer;
        	$option=new Option;
        	$option->name=Input::get('name');
        	$option->values=implode(',',Input::get('values'));
        	$option->is_multiple=Input::get('is_multiple');
        	$option->number_min=Input::get('number_min');
        	$option->number_max=Input::get('number_max');
        	$option = $restorer->options()->save($option);
        	return Response::json('success', Config::get('statuscode.Created'));
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		if(Request::header('Accept')=="qr")
		{
			$table_id=Request::header('tableId');
			$table=Table::find($table_id);
			return Response::json([
                	    'option' => $table->area->restorer->options->find($id)
                    	],
                	Config::get('statuscode.OK')
            	);	
		}
		else
		{
			$user = JWTAuth::login(Request::header('Accept'));
			if($user->restorer)
			{
				return Response::json([
                	    'option' => $user->restorer->options->find($id)
                    	],
                	Config::get('statuscode.OK')
            	);	
			}
			else
			{
				return Response::json([
                	    'option' => $user->worker->restorer->options->find($id)
                    	],
                	Config::get('statuscode.OK')
            	);	
			}
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
		$user = JWTAuth::login(Request::header('Accept'));
        $option=$user->restorer->options->find($id);
       	$option->name=Input::get('name');
       	$option->values=implode(',',Input::get('values'));
       	$option->is_multiple=Input::get('is_multiple');
       	$option->number_min=Input::get('number_min');
       	$option->number_max=Input::get('number_max');
       	$option->save();
       	return Response::json('success', Config::get('statuscode.Created'));
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
		$option=$user->restorer->options->find($id);
		if($option->delete())
        {
        	return Response::json('success', Config::get('statuscode.OK'));
        }
        else
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
        }
	}
}
