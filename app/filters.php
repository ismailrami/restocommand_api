<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
     header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header("Access-Control-Allow-Headers:X-Requested-With, Content-Type, X-Auth-Token, Origin, Authorization");

    if (Request::getMethod() == "OPTIONS") {
        // The client-side application can set only headers allowed in Access-Control-Allow-Headers
        $headers = [
            'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers'=> 'X-Requested-With, Content-Type, X-Auth-Token, Origin, Authorization'
        ];
        return Response::make('You are connected to the API', 200, $headers);
    }
});


App::after(function($request, $response)
{
	//
});

Route::filter('serviceSuperAdmin', function(){
    $user = JWTAuth::login(Request::header('Accept'));
    $role=$user->roles->first();
    if($role->role!="superadmin"){
        return Response::json([
            'flash' => 'you should be connect from superadmin'
        ], 401);
    }
});
Route::filter('serviceAdmin', function(){

    $user = JWTAuth::login(Request::header('Accept'));
    $role=$user->roles->first();
    if($role->role!="admin"){
        return Response::json([
            'flash' => 'you should be connect from admin'
        ], 401);
    }
});
Route::filter('serviceAuth', function(){
    //if(!Auth::check()){
    if (Auth::guest())
    {
        return Response::json([
            'flash' => 'you should be connect to access this URL'
        ], 401);
    }
});
Route::filter('serviceCSRF',function(){
    if (Session::token() !== Request::header('Accept')) {
        return Response::json([
            'message' => 'I’m a teapot !!! you stupid hacker :D session: '.Session::token().'=='.Request::header('Accept')
        ], 418);
    }
});
Route::filter('jwt',function()
{
    try {
        $user = JWTAuth::login(Request::header('Accept'));    
    } catch (Exception $e) {
        return Response::json([
            'flash' => 'you should be connect to access this URL'
        ], 401);
    }

});



/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		return Redirect::guest('login');
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});