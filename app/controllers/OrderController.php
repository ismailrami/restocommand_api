<?php

class OrderController extends \BaseController {

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
			//return $restorer->workers;
			$users = array();
			$workers=$restorer->workers;
			foreach ($workers as $worker) {
				$users[] = $worker->user;
			}
			//$users = $restorer->workers->user();
			//return $users;
			$caissier = array();
			
			foreach ($users as $user) {
				$roles = array();
				//return $user->roles;
				$role = $user->roles;
				foreach ($role as $r ) {
					if($r->id == 5){
						$roles[] = $r;
					}
				}

				if(count($roles)>0)
				{
					$caissier[] = $user;
				}

			}
			$orders= array();
			$ord = array();
			foreach ($caissier as $c) {
				$ord[]= $c->worker->orders;
			}
			foreach ($ord as $o) {
				foreach ($o as $k) {
					$orders[] = $k;
				}
					
			}
			return Response::json(['orders'=>$orders], Config::get('statuscode.Created'));
			
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
		if(Input::get('table')==0)
		{
			DB::beginTransaction();
		 	try
			{
				$user = JWTAuth::login(Request::header('Accept'));
				$worker=$user->worker;
				$order=new Order;
				$order->worker()->associate($worker);
				$order->is_take_away=1;
				$order->number_persone=1;
				$order->save();
			}
			catch(Exception $ex)
			{
				DB::rollback();
				return Response::json($ex, Config::get('statuscode.BADREQUEST'));
			}
		 	DB::commit();
		 	return Response::json(['order_id'=>$order->id], Config::get('statuscode.Created'));
		}
		else
		{
			$table=Table::find(Input::get('table'));

			if(!$table->is_open)
			{
				DB::beginTransaction();
		 		try
				{
					if(Request::header('Accept')=="qr")
					{
						$order=new Order;
						$order->table()->associate($table);
						$order->number_persone=Input::get('number_persone');
						$order->is_take_away=false;
						//$order->worker_id=0;
						$table->is_open=true;
						$table->save();
						$order->save();
					}
					else
					{
						$user = JWTAuth::login(Request::header('Accept'));
						$worker=$user->worker;
						$order=new Order;
						$order->worker()->associate($worker);
						$order->table()->associate($table);
						$order->number_persone=Input::get('number_persone');
						$order->is_take_away=false;
						$table->is_open=true;
						$table->save();
						$order->save();
					}
					
				}
				catch(Exception $ex)
				{
					DB::rollback();
					return Response::json($ex, Config::get('statuscode.BADREQUEST'));
				}
		 		DB::commit();
		 		return Response::json(['order_id'=>$order->id], Config::get('statuscode.Created'));
		 	}
		 	else
		 	{
		 		return Response::json('is_open', Config::get('statuscode.BADREQUEST'));
		 	}
		 }	

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($idTable)
	{
		$order=Order::where('table_id','=',$idTable)->get()->last();
		$orderLines=$order->orderLines;

		$output = array();
		if(!$orderLines->isEmpty())
		{
			foreach ($orderLines as $orderLine) 
			{
				$options= SelectedOption::where('orderline_id','=',$orderLine->id)->get();
				$out=array();
					foreach ($options as $op) 
					{
						$opt=Option::find($op->option_id);
						$out[]=array(
							'id'=>$op->id,
							'name'=>$opt->name,
							'values'=>explode(",", $op->values),

						);
					}
					if(!$orderLine->product->category)
					{
						$cat=0;
					}
					else
					{
						$cat=$orderLine->product->category->id;
					}

				$output[]=array(
								'order'=>$order->id,
								'orderLine' => $orderLine->id,
								'categoryId'=>$orderLine->product->category->id,
								'categoryName'=>$orderLine->product->category->name,
								'productId'=>$orderLine->product->id,
								'productName'=>$orderLine->product->name,
								'productPrice'=>$orderLine->product->price,
								'tva'=>$orderLine->product->tva->value,
								'served'=>$orderLine->is_serve,
								'option' => $out,
								);
			}
		}
		else
		{
			$output[]=array(
								'order'=>$order->id,
								);
		}
		return Response::json(['products'=>$output],Config::get('statuscode.OK'));
		/*}
		catch(Exception $ex)
		{
			return Response::json("ee",Config::get('statuscode.BADREQUEST'));
		}*/
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
