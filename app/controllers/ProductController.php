<?php

class ProductController extends \BaseController {

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
		$products=$restorer->products;
		$output = array();
		foreach ($products as $product) {

			if($product->category)
			{
				$categoryName=$product->category->name;
			}
			else
			{
				$categoryName="";
			}
			$output[]= array(
					'id' => $product->id,
					'name' => $product->name,
					'category'=>$categoryName,
					'price'=>$product->price,
					'position'=>$product->position,
					'is_displayed'=>$product->is_displayed,
					'color'=>$product->color
					);
			//$output[]=$array;
		}
		return Response::json(["products"=>$output],Config::get('statuscode.OK'));
	}

	public function search($name)
	{
		$user = JWTAuth::login(Request::header('Accept'));
		$products=$user->restorer->products()->where('name','like','%'.$name.'%')->get();

		return Response::json(["product"=>$products],Config::get('statuscode.OK'));
	}

	public function productOfCategory($id)
	{
		if(Request::header('Accept')=="qr")
		{
			$table_id=Request::header('tableId');
			$table=Table::find($table_id);
			if($id==0)
			{
				$products=$table->area->restorer->products()->where('category_id','=',null)->get();
			}
			else
			{
				$products=$table->area->restorer->products()->where('category_id','=',$id)->get();
			}

		}
		else
		{
			$user = JWTAuth::login(Request::header('Accept'));
			//$user = JWTAuth::login(Request::header('csrftoken'));
			if($id==0)
			{
				$products=$user->worker->restorer->products()->where('category_id','=',null)->get();
			}
			else
			{
				$products=$user->worker->restorer->products()->where('category_id','=',$id)->get();
			}
		}
		
		return Response::json(["prod" => $products],Config::get('statuscode.OK'));

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
			$restorer = $user->restorer;
	       	$us=$restorer->user;
	       	$product=new Product;
	       	$product->name=Input::get('name');
	       	$product->short_name=Input::get('short_name');
	       	$product->description=Input::get('description');
	       	$product->is_displayed=Input::get('is_displayed');
	       	$product->position=Input::get('position');
	       	$product->price=Input::get('price');
	       	$product->color=Input::get('color');
	       	$options=Input::get('options');
	       	$files=Input::file('file');
	       	$tva=$user->restorer->tvas->find(Input::get('tva'));
	       	$product->tva_id=$tva->id;

	       	$product = $restorer->products()->save($product);
	       	if($files)
	       	{	
		       	foreach ($files as $file) 
			    {
			        $destinationPath = 'uploads';
		      		$extension = $file->getClientOriginalExtension(); 
		      		$fileName = rand(11111,99999).'.'.$extension; 
		      		$file->move($destinationPath, $fileName);
		      		$picture=new Picture;
		      		$picture->url=$destinationPath.'/'.$fileName;
			        $file = $product->pictures()->save($picture);		
			    }
			}
	      //  $product = $restorer->products()->save($product);
	        
	        $product = $tva->products()->save($product);
	        if(Input::get('category'))
	        {
	        	
	        	$category=$user->restorer->categorys->find(Input::get('category'));
	        	$product->category()->associate($category);
	        	//$product = $category->products->save($product);	
	        }
	        if($options)
	       	{	
				foreach ($options as $optionId) 
				{
					$option=Option::find($optionId);
					$product->options()->attach($option);
				}
			}
		    $product->save();
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
			$restorer=$table->area->restorer;
		}
		else
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
		}		
		$product = $restorer->products()->find($id);
		//$product = Auth::user()->restorer->products()->find($id);
		if($product->category)
		{
			$categoryId=$product->category->id;
		}
		else{$categoryId=null;}
		
		return Response::json([
                'id' => $product->id,
				'name' => $product->name,
				'short_name'=>$product->short_name,
				'description'=>$product->description,
				'category'=>$categoryId,
				'price'=>$product->price,
				'tva'=>$product->tva->id,
				'position'=>$product->position,
				'is_displayed'=>$product->is_displayed,
				'options'=>$product->options,
				'pictures'=>$product->pictures
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
		DB::beginTransaction();
   		try
		{
			$user = JWTAuth::login(Request::header('Accept'));
			$product = $user->restorer->products()->find($id);
			$product->name=Input::get('name');
		    $product->short_name=Input::get('short_name');
		    $product->description=Input::get('description');
		  	$product->is_displayed=Input::get('is_displayed');
		    $product->position=Input::get('position');
		    $product->price=Input::get('price');
		    $product->color=Input::get('color');
		    $tva=$user->restorer->tvas->find(Input::get('tva'));
		    $product->tva_id=null;

		    $product = $tva->products()->save($product);
		    $cat=$product->category;
		    $product->category_id=null;
		    if(Input::get('category'))
	        {
	        	$category=$user->restorer->categorys->find(Input::get('category'));
	        	$product->category()->associate($category);
	        }

		    $options=Input::get('options');

		    $files=Input::file('file');
		    if($files)
	       	{	
		       	foreach ($files as $file) 
			    {
			        $destinationPath = 'uploads';
		      		$extension = $file->getClientOriginalExtension(); 
		      		$fileName = rand(11111,99999).'.'.$extension; 
		      		$file->move($destinationPath, $fileName);
		      		$picture=new Picture;
		      		$picture->url=$destinationPath.'/'.$fileName;
			        $file = $product->pictures()->save($picture);		
			    }
			}
			$indexDeletedFile=Input::get('indexDeleted');
			if($indexDeletedFile)
	       	{	
		       	foreach ($indexDeletedFile as $fileId) 
			    {
			    	$pic=Picture::find($fileId);
			    	File::delete($pic->url);
			    	$pic->delete();
			    }
			}
			$options=Input::get('options');
			if($options)
	       	{	
				foreach ($options as $optionId) 
				{
					$option=Option::find($optionId);
					//if(!$product->options()->contains($option))
					//{
						$product->options()->attach($option);
					//}
					
				}
			}
			$indexDeletedOption=Input::get('indexOptionDeleted');
			if($indexDeletedOption)
	       	{	
		       	foreach ($indexDeletedOption as $optionId) 
			    {
			    	$option=Option::find($optionId);
					$product->options()->detach($option);
			    }
			}


			$product->save();
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
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = JWTAuth::login(Request::header('Accept'));
		$product = $user->restorer->products()->find($id);
		if($product->delete())
        {
        	return Response::json('success', Config::get('statuscode.OK'));
        }
        else
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
        }
	}
}
