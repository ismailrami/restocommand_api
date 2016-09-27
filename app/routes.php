<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/
//header('Access-Control-Allow-Origin: http://restocommand-bo.web.anypli.com');
header('Access-Control-Allow-Origin: http://192.168.0.121/');

Route::get('/', function()
{
	return View::make('hello');
});

Route::group(array('prefix' => 'service'), function() {
 	Route::post('login','AuthenticationController@login');
 	Route::get('childrencat/{id}','CategoryController@categoryChildren');
    Route::get('product/search/{name}','ProductController@search');
    Route::get('category/products/{id}','ProductController@productOfCategory');
 	Route::resource('restorer', 'RestorerController');
    Route::resource('authenticate', 'AuthenticationController');
    Route::resource('area', 'AreaController');
    Route::resource('cashing', 'CashingController');
    Route::resource('worker', 'WorkerController');
    Route::resource('category', 'CategoryController');
    Route::resource('table', 'TableController');
    Route::post('table/close/{id}','TableController@setClose');
    Route::resource('option', 'OptionController');
    Route::resource('tva', 'TvaController');
    Route::resource('product', 'ProductController');
    Route::resource('menu', 'MenuController');
    Route::resource('order', 'OrderController');
    Route::resource('orderline', 'OrderLineController');
    Route::post('password/remind','RemindersController@postRemind');
    Route::post('password/reset','RemindersController@postReset');
    Route::get('breadcrumb/{id}','CategoryController@categoryParent');

    Route::get('tree','CategoryController@allCatTree');
});
Route::get('/page1', function()
{
	return "ok";
});
App::missing(function($exception)
{
	return 'page introuvable';
});
