<?php

class CategoryController extends \BaseController {

	public function __construct()
	{
    	//$this->beforeFilter('serviceAuth');
    	//$this->beforeFilter('jwt');
    	$this->beforeFilter('serviceAdmin' ,array('only' => array('store','update','destroy')));
	}
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
			$categorys=$table->area->restorer->categorys()->where('category_id','=',null)->get();
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
			$categorys=$restorer->categorys()->where('category_id', '=',null)->get();
		}
		$output = array();
			foreach ($categorys as $category) {
				$output[]= array(
						'id' => $category->id,
						'name' => $category->name,
						'is_displayed'=>$category->is_displayed,
						'position'=>$category->position,
						'color'=>$category->color,
						);
			}
		return Response::json(["categories"=>$output],Config::get('statuscode.OK')
            );
	}

	public function categoryChildren($id=0)
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
		
		$categorys=$restorer->categorys()->where('category_id', '=',$id)->get();
		$output = array();
		foreach ($categorys as $category) {
			$output[]= array(
					'id' => $category->id,
					'name' => $category->name,
					'is_displayed'=>$category->is_displayed,
					'position'=>$category->position,
					'color'=>$category->color,
					);
			//$output[]=$array;
		}
		return Response::json(["catchild"=>$output],Config::get('statuscode.OK')
            );
	}
	public function allCatTree()
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

		$categorys=$restorer->categorys()->where('category_id', '=',null)->get();
		$res=$this->recursive($categorys,0);
		return $res;

	}
	public function recursive($categorys,$niveau)
	{
		$niv="";
		$output=array();
		$user = JWTAuth::login(Request::header('Accept'));
		if($user->roles->first()->role=='admin')
		{
			$restorer = $user->restorer;	

		}
		else
		{
			$restorer=$user->worker->restorer;
		}
		foreach ($categorys as $cat) 
		{
			$niv="";
			for($i=0;$i<$niveau;$i++)
			{
				$niv.="*";
			}
			$cat->name=$niv.$cat->name;
			array_push($output, $cat);
			$catChild=$restorer->categorys()->where('category_id', '=',$cat->id)->get();
			$niveau=$niveau+1;	
			$out=$this->recursive($catChild,$niveau);
			$niveau=$niveau-1;	
			foreach ($out as $cel) 
			{
				array_push($output, $cel);
			}
		}
		return $output;
	}

	public function categoryParent($id=0)
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
		$category=$restorer->categorys()->find($id);
		$output=array();
		array_push($output, $category);
		/*do
		{	
			$category=$restorer->categorys()->find($category->category_id);

			$output[]=$category;

		}while ($category->category_id);*/
		while($category->category_id)
		{
			$category=$restorer->categorys()->find($category->category_id);
			array_push($output, $category);
			//$output[]=$category;
		}

		return Response::json(["catparent"=>$output],Config::get('statuscode.OK')
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
		$validator = Validator::make(Input::all(), Category::$rules);
		if ($validator->fails()) 
		{
			return Response::json($validator->errors(), Config::get('statuscode.BADREQUEST'));
        }
        else
        {
        	DB::beginTransaction();
 			try
			{
				$user = JWTAuth::login(Request::header('Accept'));
				$restorer = $user->restorer;
	        	$category=new Category;
	        	$category->name=Input::get('name');
	        	$category->is_displayed=Input::get('is_displayed');
	        	$category->position=Input::get('position');
	        	$category->color=Input::get('color');
	        	$category = $restorer->categorys()->save($category);
	        	if(Input::get('category_id'))
	        	{
	        		$categoryParent=Category::find(Input::get('category_id'));
	        		$category=$categoryParent->children()->save($category);
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
		$category=$user->restorer->categorys->find($id);
		return Response::json([
                'id' => $category->id,
				'name' => $category->name,
				'is_displayed'=>$category->is_displayed,
				'position'=>$category->position,
				'category_id'=>$category->category_id,
				'color'=>$category->color,
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

		$validator = Validator::make(Input::all(), Category::$rules);
		if ($validator->fails()) 
		{
			return Response::json($validator->errors(), 400);
        }
        else
        {
        	DB::beginTransaction();
 			try
			{
				$user = JWTAuth::login(Request::header('Accept'));
				$category=$user->restorer->categorys->find($id);
	        	$category->name=Input::get('name');
	        	$category->is_displayed=Input::get('is_displayed');
	        	$category->position=Input::get('position');
	        	$category->color=Input::get('color');
	        	if(Input::get('category_id'))
	        	{
	        		$categoryParent=Category::find(Input::get('category_id'));
	        		$category=$categoryParent->children()->save($category);
	        	}
	        	$category->save();
	        	//$category = $restorer->categorys()->save($category);

				/*$category=Auth::user()->restorer->categorys->find($id);
	        	$category->name=Input::get('name');
	        	$category->is_displayed=Input::get('is_displayed');
	        	$category->position=Input::get('position');
	        	$category = $restorer->categorys()->save($category);
	        	if(Input::get('category_parent'))
	        	{
	        		$categoryParent=Category::find(Input::get('category_parent'));
	        		$category=$categoryParent->children()->save($category);
	        	}*/
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
		$user = JWTAuth::login(Request::header('Accept'));
		$category = $user->restorer->categorys()->find($id);
		if(Request::get('attached')=='true')
			{
				$categorys= Auth::user()->restorer->categorys()->where('category_id', '=',$id)->get();
				$products= $category->products();
				$catParent=Category::find($category->category_id);
				foreach ($categorys as $cat) 
				{
					if($catParent)
					{
						$cat->parent()->associate($catParent);	
					}
					else
					{
						$cat->category_id=null;	
					}
				$cat->save();
				}
				foreach ($products as $prod) 
				{
					if($catParent)
					{
						$prod->category->associate($catParent);	
					}
					else
					{
						$prod->category=null;	
					}
				$cat->save();
				}
			}	
        if($category->delete())
        {
        	return Response::json('success', Config::get('statuscode.OK'));
        }
        else
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
        }

	}
}
