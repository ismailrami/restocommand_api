<?php

class MenuController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		if(Request::header('Accept')=="qr")
		{
			$table_id=Request::header('tableId');
			$table=Table::find($table_id);
			$menus=$table->area->restorer->menus;
		}
		else
		{
			$user = JWTAuth::login(Request::header('Accept'));
			if($user->restorer)
			{
				$menus=$user->restorer->menus;
			}
			else
			{
				$menus=$user->worker->restorer->menus;
			}
		}
		$output = array();
		foreach ($menus as $menu) 
		{
			$price=0;
			$steps=$menu->steps;
			foreach ($steps as $step) 
			{
				$products=$step->products;	
				foreach ($products as $product) 
				{	
					$price+=$product->price;
				}
			}
			$output[]= array(
					'id' => $menu->id,
					'name' => $menu->name,
					'price'=>$price,
					"steps"=>$menu->steps,
					);
		}
		
		return Response::json([
                    'menus' =>$output 
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
		DB::beginTransaction();
 		try
		{
			$menu=new Menu;
			$menu->name=Input::get('name');
			$user = JWTAuth::login(Request::header('Accept'));
			$restorer =$user->restorer;
			$menu = $restorer->menus()->save($menu);
	       	$steps=Input::get('steps'); 
	       	$products= Input::get('products'); 
	       	for($i=0;$i<count($steps);$i++)
	       	{
	       		$step=new Step;
	       		$step->title=$steps[$i];
	       		$step = $menu->steps()->save($step);
	       		$j=$i+1;
	       		//$productStep=Input::get('product'.$j);
	       		$productStep=$products[$i];
	       		foreach ($productStep as $pr) 
	       		{
	       			$product=Product::find($pr);
	       			$step->products()->attach($product);
	       		}
	        }
	    }
	    catch(Exception $ex)
		{
			DB::rollback();
			return Response::json($ex, Config::get('statuscode.BADREQUEST'));
		}
 		DB::commit();
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
			$menus=$table->area->restorer->menus->find($id);;
		}
		else
		{
			$user = JWTAuth::login(Request::header('Accept'));
			if($user->restorer)
			{
				$menu=$user->restorer->menus->find($id);
			}
			else
			{
				$menu=$user->worker->restorer->menus->find($id);
		}
		$steps=$menu->steps;
		$menuSteps=array();
		foreach ($steps as $step) 
		{
			$menuSteps[]=array(
								'step'=>$step,
								'products'=>$step->products
								);	
		}
		return Response::json([
                   			 	'menus' =>$menuSteps 
                    		  ], Config::get('statuscode.OK'));

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
			$menu=Menu::find($id);
			$menu->name=Input::get('name');
			$steps=Input::get('steps');  
			$products= Input::get('products');
	       	for($i=0;$i<count($steps);$i++)
	       	{
	       		//$stepmenu = $menu->steps();
	       		$step=$menu->steps()->where('title','=',$steps[$i])->get()->last();
	       		//return $step;
	       		if($step instanceOF Step)
	       		{
	       			$step->delete();
	       		}
				
				//$step->delete(oid)();
	       	}
	       	for($i=0;$i<count($steps);$i++)
	       	{
	       		$step=new Step;
	       		$step->title=$steps[$i];
	       		$step = $menu->steps()->save($step);
	       		$j=$i+1;
	       		$productStep=$products[$i];
	       		foreach ($productStep as $pr) 
	       		{
	       			$product=Product::find($pr);
	       			$step->products()->attach($product);
	       		}
	       	}



			/*$menu=Menu::find($id);
			$menu->name=Input::get('name');

	       	$steps=Input::get('step');  
	       	for($i=0;$i<count($steps);$i++)
	       	{
	       		$step=new Step;
	       		$step->title=$steps[$i];
	       		$step = $menu->steps()->save($step);
	       		$j=$i+1;
	       		//$products=Input::get('product'.$j);
	       		$productStep=$products[$i];
	       		foreach ($productStep as $pr) 
	       		{
	       			$product=Product::find($pr);
	       			$step->products()->attach($product);
	       		}
	        }*/
	        return Response::json('success', Config::get('statuscode.Created'));
		}
		catch(Exeption $ex)
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
		$menu=$user->restorer->menus->find($id);
		if($menu->delete())
		{
        	return Response::json('success', Config::get('statuscode.OK'));
        }
        else
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
        }
	}


}
