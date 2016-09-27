<?php

class OrderLineController extends \BaseController {

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
		DB::beginTransaction();
 		try
		{
			$user = JWTAuth::login(Request::header('Accept'));
			$worker=$user->worker;
			if(Input::get('isMenu')==1)
			{
				$order=Order::find(Input::get('order'));
				$products=Input::get('product');
				$values=Input::get('values');
				$option=Input::get('options');
				$j=0;
				$i=0;
				foreach ($products as $product) 
				{
					$prod=Product::find($product);
					$orderLine=new OrderLine;
					$orderLine->order_id=$order->id;
					$orderLine->product_id=$prod->id;
					$orderLine->push();
					$op=$option[$j];
					$options=explode(",", $op);
					
					if($options[0])
					{
						foreach ($options as $optionId) 
						{
							$option=Option::find($optionId);
							$selectedOption=new SelectedOption;
							$selectedOption->orderLine_id=$orderLine->id;
							$selectedOption->option_id=$option->id;

							$selectedOption->values=$values[$i];                                                                         
							$selectedOption->push();
							$i++;
						}
					}
					$j++;
					$orderLine->save();
				}
			}
			else
			{
				$orderLine=new OrderLine;
				$order=Order::find(Input::get('order'));
				$product=Product::find(Input::get('product'));
				$options=Input::get('options');
				
				$values=Input::get('values');
				$orderLine->order_id=$order->id;
				$orderLine->product_id=$product->id;
				$orderLine->push();
				$i=0;
				if($options[0])
				{
					foreach ($options as $optionId) 
					{
						$option=Option::find($optionId);
						$selectedOption=new SelectedOption;
						$selectedOption->orderLine_id=$orderLine->id;
						$selectedOption->option_id=$option->id;
						$selectedOption->values=$values[$i];
						$selectedOption->push();
						$i++;
					}
				}
				$orderLine->save();
			}
			
		}
		catch(Exception $ex)
		{
			DB::rollback();
			return Response::json($ex, Config::get('statuscode.BADREQUEST'));
		}
 		DB::commit();
 		return Response::json(['orderLine'=>$orderLine->id], Config::get('statuscode.Created'));

	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$order = Order::find($id);
		$orderlines = $order->orderLines;
		$output = array();
		if(!$orderlines->isEmpty())
		{
			foreach ($orderlines as $orderLine) 
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
		return Response::json(['products'=>$output], Config::get('statuscode.OK'));
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
		if(OrderLine::find($id))
        { 
		$orderLine = OrderLine::find($id);
		$order = $orderLine->order;
		$idworker = $order->worker->id;
		$orderLine->is_serve = Input::get('state');
		$orderLine->save();
		$table=Table::find($order->table->id);
		$product=Product::find($orderLine->product_id);
		if( $orderLine->is_serve==2)
		{
			$status=1;
		   $message = ($status == 0) ? "no" : "ok";
		   $response = Httpful::post('https://api.parse.com/1/push')
		        ->sendsJson()
		        ->addHeaders(array(
		            'X-Parse-Application-Id' => "pNaLhZf9QYShDKIbTAzGbI3q69YdYhCXgr0ZzGlW",
		            'X-Parse-REST-API-Key' => "M69LPHVpqrk6rseDru9U4HhcMMUHhrZvnfrf4L06",
		            'Content-Type' => 'application/json',
		           )) 
		      ->body('{
		        "channels": [
		                "id"
		              ],
		       
		        "data": {
		               "alert": "la '.$product->name.' de table '.$table->name.' est prêt",
		                
		           "status": "'.$status.'"
		              }
		      }')->send();
		}
		
        	return Response::json('success', Config::get('statuscode.OK'));
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
		$orderLine=OrderLine::find($id);
		if($orderLine->is_serve==0)
		{
			DB::beginTransaction();
 			try
			{
				$options= SelectedOption::where('orderline_id','=',$orderLine->id)->get();
					foreach ($options as $op) {
						$op->delete();
					}
				$orderLine->delete();
			}
			 catch(Exception $ex)
			{
				DB::rollback();
				return Response::json($ex, Config::get('statuscode.BADREQUEST'));
			}
 			DB::commit();
 			return Response::json('success', Config::get('statuscode.ACCEPTED'));
		}
		return Response::json($ex, Config::get('statuscode.BADREQUEST'));
	}

}
