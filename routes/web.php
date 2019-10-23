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

$router->group(['prefix' => '/wilayah'], function () use ($router) {
    $router->get('/provinsi', 'Wilayah\ProvinsiController@index');
    $router->post('/provinsi', 'Wilayah\ProvinsiController@store');
    $router->get('/provinsi/{id}', 'Wilayah\ProvinsiController@show');
    $router->put('/provinsi/{id}', 'Wilayah\ProvinsiController@update');
    $router->delete('/provinsi/{id}', 'Wilayah\ProvinsiController@delete');

    $router->get('/kabupaten', 'Wilayah\KabupatenController@index');
    $router->post('/kabupaten', 'Wilayah\KabupatenController@store');
    $router->get('/kabupaten/{id}', 'Wilayah\KabupatenController@show');
    $router->put('/kabupaten/{id}', 'Wilayah\KabupatenController@update');
    $router->delete('/kabupaten/{id}', 'Wilayah\KabupatenController@delete');

    $router->get('/kecamatan', 'Wilayah\KecamatanController@index');
    $router->post('/kecamatan', 'Wilayah\KecamatanController@store');
    $router->get('/kecamatan/{id}', 'Wilayah\KecamatanController@show');
    $router->put('/kecamatan/{id}', 'Wilayah\KecamatanController@update');
    $router->delete('/kecamatan/{id}', 'Wilayah\KecamatanController@delete');

    $router->get('/kelurahan', 'Wilayah\KelurahanController@index');
    $router->post('/kelurahan', 'Wilayah\KelurahanController@store');
    $router->get('/kelurahan/{id}', 'Wilayah\KelurahanController@show');
    $router->put('/kelurahan/{id}', 'Wilayah\KelurahanController@update');
    $router->delete('/kelurahan/{id}', 'Wilayah\KelurahanController@delete');
});

$router->post('/login', 'AuthController@login');

$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    $router->group(['prefix' => '/api'], function () use ($router) {
        $router->get('/users', 'UserController@index');
        $router->put('/users/reset_password', 'UserController@resetPassword');
        $router->put('/users/change_password', 'UserController@changePassword');
        $router->put('/flag', 'UserController@flagAuthor');
        // $router->post('/users', 'UserController@create');
        // $router->get('/users/{id}', 'UserController@getId');
        // $router->put('/users', 'UserController@update');
        // $router->delete('/users', 'UserController@delete');

        $router->group(['prefix' => '/master'], function () use ($router) {
            $router->get('/asal_data', 'Master\Asal_Data_Controller@index');
            $router->post('/asal_data', 'Master\Asal_Data_Controller@create_data');
            $router->get('/area_so', 'Master\Area_SO_Controller@index');
            $router->post('/area_so', 'Master\Area_SO_Controller@create_data');
            $router->get('/area_ao', 'Master\Area_AO_Controller@index');
            $router->post('/area_ao', 'Master\Area_AO_Controller@create_data');
        });

        $router->group(['prefix' => '/menu'], function () use ($router) {
            $router->get('/akses', 'Menu\MenuAccessController@index'); // Get All Data
            $router->post('/akses', 'Menu\MenuAccessController@store'); // Insert Data
            $router->get('/akses/{id_user}', 'Menu\MenuAccessController@show'); // Get Data based on Id User
            $router->put('/akses/{id_user}', 'Menu\MenuAccessController@update'); // Update Data based on Id User
            $router->delete('/akses/{id_user}', 'Menu\MenuAccessController@delete'); // Delete Data based on Id User

            $router->get('/', ['as' => 'menu', 'uses' => 'Menu\MenuMasterController@index']); //Get Data
            $router->post('/', 'Menu\MenuMasterController@store'); // Create Data
            $router->get('/master', function () use ($router) {return redirect('/api/menu');});
            $router->get('/master/{slug}', ['as' => 'mastermenu', 'uses' => 'Menu\MenuMasterController@main']);
            $router->put('/master/{slug}', 'Menu\MenuMasterController@edit');
            $router->delete('/master/{slug}', 'Menu\MenuMasterController@delete'); // Delete Data based on slug (URL)

            $router->get('/sub', 'Menu\MenuSubController@index');
            $router->post('/sub', 'Menu\MenuSubController@store');
            $router->get('/sub/{slug}', ['as' => 'submenu', 'uses' => 'Menu\MenuSubController@main']);
            $router->put('/sub/{slug}', 'Menu\MenuSubController@edit');
            $router->delete('/sub/{slug}', 'Menu\MenuSubController@delete'); // Delete Data based on slug(URL)
        });

        $router->get('/pinjaman', 'PinjamanController@index');
        $router->post('/pinjaman', 'PinjamanController@store');
        $router->get('/pinjaman/plus', 'PinjamanController@plus');
    });
});
