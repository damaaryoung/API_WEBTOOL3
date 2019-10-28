<?php

$router->get('/', function () use ($router) {
    return 'API - WEBTOOL' ;
});

$router->get('/api', function () use ($router) {
    return redirect('/') ;
});

$router->group(['prefix' => '/wilayah'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return 'add parameters after slash';
    });

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

$router->put('/api/users/reset_password', 'UserController@resetPassword'); //Reset Password

$router->get('/otorisasi', 'FlagAuthorController@updateOtorisasi');
$router->get('/flag', 'FlagAuthorController@index');
$router->post('/flag', 'FlagAuthorController@store');
$router->get('/flag/{id}', 'FlagAuthorController@show');
$router->put('/flag/{id}', 'FlagAuthorController@update');
$router->delete('/flag/{id}', 'FlagAuthorController@delete');

$router->post('/login', 'AuthController@login');

$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    $router->group(['prefix' => '/api'], function () use ($router) {
        $router->get('/users', 'UserController@index');
        $router->put('/users/change_password', 'UserController@changePassword');
        // $router->post('/users', 'UserController@create');
        // $router->get('/users/{id}', 'UserController@getId');
        // $router->put('/users', 'UserController@update');
        // $router->delete('/users', 'UserController@delete');

        $router->group(['prefix' => '/master'], function () use ($router) {
            $router->get('/asal_data', 'Master\AsalDataController@index');
            $router->post('/asal_data', 'Master\AsalDataController@store');
            $router->get('/asal_data/{id}', 'Master\AsalDataController@show');
            $router->put('/asal_data/{id}', 'Master\AsalDataController@update');
            $router->delete('/asal_data/{id}', 'Master\AsalDataController@delete');

            $router->get('/area', 'Master\AreaController@index');
            $router->post('/area', 'Master\AreaController@store');
            $router->get('/area/{id}', 'Master\AreaController@show');
            $router->put('/area/{id}', 'Master\AreaController@update');
            $router->delete('/area/{id}', 'Master\AreaController@delete');

            $router->get('/jenis_area', 'Master\JenisAreaController@index');
            $router->post('/jenis_area', 'Master\JenisAreaController@store');
            $router->get('/jenis_area/{id}', 'Master\JenisAreaController@show');
            $router->put('/jenis_area/{id}', 'Master\JenisAreaController@update');
            $router->delete('/jenis_area/{id}', 'Master\JenisAreaController@delete');
        });

        $router->group(['prefix' => '/menu'], function () use ($router) {
            $router->get('/akses', 'Menu\MenuAccessController@index'); // Get All Data
            $router->post('/akses', 'Menu\MenuAccessController@store'); // Insert Data
            $router->get('/akses/{id_user}', 'Menu\MenuAccessController@show'); // Get Data based on Id User
            $router->put('/akses/{id_user}', 'Menu\MenuAccessController@update'); // Update Data based on Id User
            $router->delete('/akses/{id_user}', 'Menu\MenuAccessController@delete'); // Delete Data based on Id User

            $router->get('/', ['as' => 'menu', 'uses' => 'Menu\MenuMasterController@index']); //Get Data
            $router->post('/', 'Menu\MenuMasterController@store'); // Create Data
            // $router->get('/master', function () use ($router) {return redirect('/api/menu');});
            $router->get('/master/{slug}', ['as' => 'mastermenu', 'uses' => 'Menu\MenuMasterController@show']);
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
