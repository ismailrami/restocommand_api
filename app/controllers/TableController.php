<?php

class TableController extends \BaseController {

	public function __construct()
	{
		//$this->beforeFilter('serviceAuth');
    	//$this->beforeFilter('jwt');
    	//$this->beforeFilter('serviceAdmin',['except' => ['index']]);
    	//$this->beforeFilter('serviceAuth' ,array('only' => array('store')));
    	//$this->beforeFilter('serviceCSRF');
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
		$areas=$user->worker->restorer->areas;
		$out=array();
		$tables=array();
		foreach ($areas as $area) 
		{
			$tables[]=$area->tables()->where('is_open','=','1')->get();
		}
		foreach ($tables as $tab) 
		{
			foreach ($tab as $t) {
				$order=Order::where('table_id','=',$t->id)->get()->last();
				$orderLines=$order->orderLines;
				$count=$orderLines->count();
				if($count==0)
				{
					$out[]=array(
						"id"=>$t->id,
           			 	"deleted_at"=>$t->deleted_at,
            			"area_id"=>$t->area_id,
			            "name"=>$t->name,
			            "width"=>$t->width,
			            "height"=>$t->height,
			            "coordinate_x"=>$t->coordinate_x,
			            "coordinate_y"=>$t->coordinate_y,
			            "is_open"=>$t->is_open,
			            "shape"=>$t->shape,
			            "created_at"=>$t->created_at,
			            "updated_at"=>$t->updated_at,
			            "state"=>"En attent de commande"
								);
				}
				else
				{
					$i=-1;
					$state;
					do
					{
						$i++;
						$state=$orderLines[$i]->is_serve;
					}while($i<$count-1 && $state!=0);
					//return $orderLines[$i];
					if($state!=0)
					{
						$out[]=array(
						"id"=>$t->id,
           			 	"deleted_at"=>$t->deleted_at,
            			"area_id"=>$t->area_id,
			            "name"=>$t->name,
			            "width"=>$t->width,
			            "height"=>$t->height,
			            "coordinate_x"=>$t->coordinate_x,
			            "coordinate_y"=>$t->coordinate_y,
			            "is_open"=>$t->is_open,
			            "shape"=>$t->shape,
			            "created_at"=>$t->created_at,
			            "updated_at"=>$t->updated_at,
			            "state"=>"Commande servie"
								);
					}
					else
					{
						$out[]=array(
						"id"=>$t->id,
           			 	"deleted_at"=>$t->deleted_at,
            			"area_id"=>$t->area_id,
			            "name"=>$t->name,
			            "width"=>$t->width,
			            "height"=>$t->height,
			            "coordinate_x"=>$t->coordinate_x,
			            "coordinate_y"=>$t->coordinate_y,
			            "is_open"=>$t->is_open,
			            "shape"=>$t->shape,
			            "created_at"=>$t->created_at,
			            "updated_at"=>$t->updated_at,
			            "state"=>"En attent de ".$orderLines[$i]->product->name
								);
					}
					
				}

			}
		}
		return Response::json(["tables"=>$out],Config::get('statuscode.OK'));
		//return $tables;
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
		$validator = Validator::make(Input::all(), Table::$rules);
		if ($validator->fails()) 
		{
			return Response::json($validator->errors(), Config::get('statuscode.BADREQUEST'));	
        }
        else
	    {
	    	DB::beginTransaction();
   			try
			{
				$table=new Table;
				$table->name=Input::get('name');
				$table->width=Input::get('width');
				$table->height=Input::get('height');
				$table->coordinate_x=Input::get('coordinate_x');
				$table->coordinate_y=Input::get('coordinate_y');
				$table->shape=Input::get('shape');
				$table->is_open=false;
				$area=Area::find(Input::get('area'));
				$table = $area->tables()->save($table);
			}
			catch(Exception $ex)
			{
				DB::rollback();
				return Response::json($ex, Config::get('statuscode.BADREQUEST'));
			}
	 		DB::commit();

	 		QrCode::format('png');
		QrCode::generate($table->id, '..\public\\'.$table->name.'_'.$table->id.'.png');
		$url=App::make('url')->to('/');
		$urll=$url.'\\'.$table->name.'_'.$table->id.'.png';
	 		return Response::json( ["url"=>$urll], Config::get('statuscode.Created'));
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
		if(Request::header('Accept')=="qr")
		{
			$table=Table::find($id);

			return Response::json( ["table"=>$table], Config::get('statuscode.Created'));
		}
		else
		{
			return Response::json( "err", Config::get('statuscode.BADREQUEST'));
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
		$table=Table::find($id);
		$table->coordinate_x=Input::get('coordinate_x');
		$table->coordinate_y=Input::get('coordinate_y');
		if($table->save())
		{
			return Response::json('success', Config::get('statuscode.Created'));
		}
		else
		{
			return Response::json('err', Config::get('statuscode.BADREQUEST'));
		}

	}


	public function setClose($id)
	{
		try{
		$table=Table::find($id);
		$table->is_open=false;
		$table->save();
			return Response::json('success', Config::get('statuscode.Created'));
		}
		catch(Exception $ex)
		{
			return Response::json($ex, Config::get('statuscode.BADREQUEST'));
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
		$table=Table::find($id);
        if($table->delete())
        {
        	return Response::json('success', Config::get('statuscode.OK'));
        }
        else
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
        }
	}


}
