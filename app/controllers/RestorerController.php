<?php

class RestorerController extends \BaseController {

	public function __construct()
	{
    	//$this->beforeFilter('serviceAuth');
    	$this->beforeFilter('jwt');
    	$this->beforeFilter('serviceSuperAdmin');
	}
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$restorers=Restorer::all();
		$output = array();
		foreach ($restorers as $restorer) {
			$output[]= array(
					'id' => $restorer->id,
					'name_restaurant' => $restorer->name_restaurant,
					'last_name'=>$restorer->user->last_name,
					'first_name'=>$restorer->user->first_name,
					'login'=>$restorer->user->login,
					'created_at'=>$restorer->user->created_at,
					'last_connexion'=>$restorer->user->updated_at,
					);
			//$output[]=$array;
		}
		return Response::json(["restorers"=>$output],Config::get('statuscode.OK')
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

	public function genererMDP (){
	$longueur = 8;
	// initialiser la variable $mdp
	$mdp = "";
 
	// Définir tout les caractères possibles dans le mot de passe, 
	// Il est possible de rajouter des voyelles ou bien des caractères spéciaux
	$possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
 
	// obtenir le nombre de caractères dans la chaîne précédente
	// cette valeur sera utilisé plus tard
	$longueurMax = strlen($possible);
 
	if ($longueur > $longueurMax) {
		$longueur = $longueurMax;
	}
 
	// initialiser le compteur
	$i = 0;
 
	// ajouter un caractère aléatoire à $mdp jusqu'à ce que $longueur soit atteint
	while ($i < $longueur) {
		// prendre un caractère aléatoire
		$caractere = substr($possible, mt_rand(0, $longueurMax-1), 1);
 
		// vérifier si le caractère est déjà utilisé dans $mdp
		if (!strstr($mdp, $caractere)) {
			// Si non, ajouter le caractère à $mdp et augmenter le compteur
			$mdp .= $caractere;
			$i++;
		}
	}
 
	// retourner le résultat final
	return $mdp;
}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$rulesUser=User::$rules['create'];
		$rulesUser['login']='Required|Alpha|unique:users';
		$rulesUser['email']='Required|email|unique:users';
		$validatorUser = Validator::make(Input::all(), $rulesUser);
		$validatorRestorer = Validator::make(Input::all(), Restorer::$rules);
		if ($validatorUser->fails() || $validatorRestorer->fails() ) 
		{
			return Response::json([$validatorUser->errors(),$validatorRestorer->errors()],Config::get('statuscode.BADREQUEST'));	
    	}
    	else
    	{
    		DB::beginTransaction();
   			try
			{	
				$user=new User;
				$user->email=Input::get('email');
				$user->login=Input::get('login');
				$user->first_name=Input::get('first_name');
				$user->last_name=Input::get('last_name');

				if(Input::get('generateMdp'))
				{
					$mdp=$this->genererMDP();
					$user->password = Hash::make($mdp);
					$password=$mdp;
				}
				else
				{
					$password=Input::get('password');
					$user->password = Hash::make(Input::get('password'));	
				}
				
				$user->save();

				$role=Role::find(2);
	        	$user->roles()->attach($role);
	        	$user->save();

				$restorer = new Restorer;
		    	$restorer->name_restaurant= Input::get('name_restaurant');
		    	$restorer->user()->associate($user);
		    	$restorer->push();
		    	if(Input::get('send') || Input::get('generateMdp'))
		    	{
		    		$email=$user->email;
		    		Mail::send('email',array('login'=>Input::get('login'),'password'=>$password,'email'=>Input::get('email'),'firstName'=>Input::get('first_name'),'lastName'=>Input::get('last_name'),'restaurant'=>Input::get('name_restaurant')), function($message) use ($email){
        			$message->to($email)->subject('Welcome!');
   					});
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
		if(Restorer::find($id))
		{
			$restorer=Restorer::find($id);
			return Response::json([
                  				'id' => $restorer->id,
								'first_name'=>$restorer->user->first_name,
								'last_name'=>$restorer->user->last_name,
								'name_restaurant' => $restorer->name_restaurant,
								'email'=>$restorer->user->email,
								'login'=>$restorer->user->login,
                    ],
                Config::get('statuscode.OK')
            );	
		}
		else
		{
			return 'null';
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
		$validatorUser = Validator::make(Input::all(), User::$rules['editRestorer']);
		$validatorRestorer = Validator::make(Input::all(), Restorer::$rules);
		if ($validatorUser->fails() || $validatorRestorer->fails()) 
		{
			return Response::json([
                  $validatorUser->errors(),
              	  $validatorRestorer->errors()    
                    ],
                Config::get('statuscode.BADREQUEST')
            	);	
        }
        else
        {
        	DB::beginTransaction();
 			try
			{
				$restorer=Restorer::find($id);
				$restorer->name_restaurant= Input::get('name_restaurant');
				$user=$restorer->user;
				$user->email=Input::get('email');
				$user->login=Input::get('login');
				$user->first_name=Input::get('first_name');
				$user->last_name=Input::get('last_name');
				if(Input::get('generateMdp'))
				{
					$mdp=$this->genererMDP();
					$user->password = Hash::make($mdp);
					$password=$mdp;
				}
				else
				{
					if(Input::get('password'))
					{
						$user->password = Hash::make(Input::get('password'));
					}
				}
				
				$user->save();
	        	$restorer->save();
	    	    if(Input::get('send') || Input::get('generateMdp'))
		    	{
		    		$email=$user->email;
		    		$login=Input::get('login');
		    		Mail::send('email',array('login'=>Input::get('login'),'password'=>$password,'email'=>Input::get('email'),'firstName'=>Input::get('first_name'),'lastName'=>Input::get('last_name'),'restaurant'=>Input::get('name_restaurant')), function($message) use ($email){
        			$message->to($email)->subject('Welcome!');
   					});
		    	}
	    	}
	    	catch(Exception $ex)
			{
				DB::rollback();
				return Response::json($ex, Config::get('statuscode.BADREQUEST'));
			}
 			DB::commit();
 			return Response::json('success', Config::get('statuscode.OK'));
       	}
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	/*public function destroy($id)
	{
		$restorer=Restorer::find($id);
		if($restorer->delete())
        {
        	return Response::json('success', Config::get('statuscode.OK'));
        }
        else
        {
        	return Response::json('err', Config::get('statuscode.BADREQUEST'));
        }
	}*/

	public function destroy($id)
	{
		DB::beginTransaction();
 		try
		{
			$restorer=Restorer::find($id);
			$user=$restorer->user;

			//$roles=$user->roles;
			//foreach ($roles as $role) 
			//{
			//	$user->roles()->detach($role);
			//}
	        $restorer->delete();
	        //$user->delete();
	    }
	    catch(Exception $ex)
		{
			DB::rollback();
			return Response::json($ex, Config::get('statuscode.BADREQUEST'));
		}
 		DB::commit();
 		return Response::json('success', Config::get('statuscode.OK'));
	}
}
