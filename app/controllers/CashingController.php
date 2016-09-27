<?php

class CashingController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
		$table=Table::find(Input::get('table'));
		$amount=Input::get('amount');
		$type=Input::get('type');
		$order=Order::where('table_id','=',$table->id)->get()->last();
		
		if($table->is_open)
		{
			DB::beginTransaction();
	 		try
			{
				$user = JWTAuth::login(Request::header('Accept'));
				$worker=$user->worker;
				$caching=new Caching();
				$caching->amount=$amount;
				$caching->type=$type;
				$caching->order_id=$order->id;
				$caching->save();
			}
			catch(Exception $ex)
			{
				DB::rollback();
				return Response::json($ex, Config::get('statuscode.BADREQUEST'));
			}
	 		DB::commit();
	 		return Response::json("success", Config::get('statuscode.Created'));
	 	}
	 	else
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
		$order=Order::where('table_id','=',$id)->get()->last();
		$cachings=$order->cachings;
		$output = array();

		foreach ($cachings as $caching) 
		{
			$output[]=array(
							'id' => $caching->id,
							'type'=>$caching->type,
							'amount'=>$caching->amount
							);
		}
		return Response::json(['caching'=>$output],Config::get('statuscode.OK'));
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
