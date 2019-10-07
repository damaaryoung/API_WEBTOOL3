<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return 'API - WEEBTOOL' ;
});

$router->get('/api', function () use ($router) {
    return redirect('/') ;
});

$router->get('/user', function () use ($router){return redirect('/user/login');});
$router->post('/user/login', 'UserController@login');

$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    $router->group(['prefix' => '/api'], function () use ($router) {
    	$router->get('/something', 'ApiController@devME');
    });
});
