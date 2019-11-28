<?php

$router->get('/', function () use ($router) {
    return 'API - DEVIS' ;
});

$router->get('/api', function () use ($router) {
    return redirect('/') ;
});

$router->post('/push', 'ImgController@push');

$router->post('/img', 'ImgController@upload');
$router->get('/img', 'ImgController@getDecode');

$router->group(['prefix' => '/wilayah'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return 'add parameters after slash';
    });

    $router->get('/provinsi', 'Wilayah\ProvinsiController@index');
    $router->post('/provinsi', 'Wilayah\ProvinsiController@store');
    $router->post('/provinsi', 'Wilayah\ProvinsiController@store');
    $router->get('/provinsi/search/{search}', 'Wilayah\ProvinsiController@search');
    $router->get('/provinsi/{id}', 'Wilayah\ProvinsiController@show');
    $router->put('/provinsi/{id}', 'Wilayah\ProvinsiController@update');
    $router->delete('/provinsi/{id}', 'Wilayah\ProvinsiController@delete');

    $router->get('/kabupaten', 'Wilayah\KabupatenController@index');
    $router->post('/kabupaten', 'Wilayah\KabupatenController@store');
    $router->get('/kabupaten/{id}', 'Wilayah\KabupatenController@show');
    $router->put('/kabupaten/{id}', 'Wilayah\KabupatenController@update');
    $router->delete('/kabupaten/{id}', 'Wilayah\KabupatenController@delete');
    $router->get('/provinsi/{id}/kabupaten', 'Wilayah\KabupatenController@sector'); // Get Data Kabupaten By Id Provinsi

    $router->get('/kecamatan', 'Wilayah\KecamatanController@index');
    $router->post('/kecamatan', 'Wilayah\KecamatanController@store');
    $router->get('/kecamatan/{id}', 'Wilayah\KecamatanController@show');
    $router->put('/kecamatan/{id}', 'Wilayah\KecamatanController@update');
    $router->delete('/kecamatan/{id}', 'Wilayah\KecamatanController@delete');
    $router->get('/kabupaten/{id}/kecamatan', 'Wilayah\KecamatanController@sector'); // Get Data Kecamatan By Id Kabupaten

    $router->get('/kelurahan', 'Wilayah\KelurahanController@index');
    $router->post('/kelurahan', 'Wilayah\KelurahanController@store');
    $router->get('/kelurahan/{id}', 'Wilayah\KelurahanController@show');
    $router->put('/kelurahan/{id}', 'Wilayah\KelurahanController@update');
    $router->delete('/kelurahan/{id}', 'Wilayah\KelurahanController@delete');
    $router->get('/kecamatan/{id}/kelurahan', 'Wilayah\KelurahanController@sector'); // Get Data Kelurahan By Id Kecamatan
});

$router->put('/api/users/reset_password', 'UserController@resetPassword'); //Reset Password

$router->post('/login', 'AuthController@login'); // Login All Level

$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    //For Non User (Debitur)
    $router->group(['prefix' => '/api'], function () use ($router) {

        $router->get('/logs', 'LogsController@index'); //Log History All
        $router->get('/logs/{id}', 'LogsController@detail'); //Log History By ID
        $router->get('/logs/limit/{limit}', 'LogsController@limit'); //Log History Limit
        $router->get('/logs/search/{search}', 'LogsController@search'); //Log History Search

        $router->get('/users', 'UserController@index');
        $router->put('/users/change_password', 'UserController@changePassword');

        $router->get('/oto', 'FlagAuthorController@otoIndex'); // Otorisasi
        $router->get('/oto/{id}', 'FlagAuthorController@otoShow');
        $router->put('/oto/{id}', 'FlagAuthorController@otoUpdate');
        $router->get('/log_oto', 'FlagAuthorController@AfterOto');
        $router->get('/log_oto/{id}', 'FlagAuthorController@DetailAfterOto');
        $router->get('/count_oto', 'FlagAuthorController@countOto');
        $router->put('/oto/{id}/reject', 'FlagAuthorController@rejectOto');

        $router->get('/apro', 'FlagAuthorController@aproIndex'); // Approval
        $router->get('/apro/{id}', 'FlagAuthorController@aproShow');
        $router->put('/apro/{id}', 'FlagAuthorController@aproUpdate');
        $router->get('/log_apro', 'FlagAuthorController@AfterApro');
        $router->get('/log_apro/{id}', 'FlagAuthorController@DetailAfterApro');
        $router->get('/count_apro', 'FlagAuthorController@countApro');
        $router->put('/apro/{id}/reject', 'FlagAuthorController@rejectApro');

        $router->get('/oto/{id}/reset', 'FlagAuthorController@otoReset'); // Reset Otorisasi
        $router->get('/apro/{id}/reset', 'FlagAuthorController@aproReset'); // Reset Otorisasi

        $router->group(['prefix' => '/master'], function () use ($router) {
            $router->get('/asal_data', 'Master\Bisnis\AsalDataController@index');
            $router->post('/asal_data', 'Master\Bisnis\AsalDataController@store');
            $router->get('/asal_data/{id}', 'Master\Bisnis\AsalDataController@show');
            $router->put('/asal_data/{id}', 'Master\Bisnis\AsalDataController@update');
            $router->delete('/asal_data/{id}', 'Master\Bisnis\AsalDataController@delete');

            //Area Kantor
            $router->get('/area_kerja', 'Master\AreaKantor\AreaController@index');
            $router->post('/area_kerja', 'Master\AreaKantor\AreaController@store');
            $router->get('/area_kerja/{id}', 'Master\AreaKantor\AreaController@show');
            $router->put('/area_kerja/{id}', 'Master\AreaKantor\AreaController@update');
            $router->delete('/area_kerja/{id}', 'Master\AreaKantor\AreaController@delete');

            //Cabang Kantor
            $router->get('/area_cabang', 'Master\AreaKantor\CabangController@index');
            $router->post('area_cabang', 'Master\AreaKantor\CabangController@store');
            $router->get('area_cabang/{id}', 'Master\AreaKantor\CabangController@show');
            $router->put('area_cabang/{id}', 'Master\AreaKantor\CabangController@update');
            $router->delete('/area_cabang/{id}', 'Master\AreaKantor\CabangController@delete');

            //Kas Kantor
            $router->get('/area_sales', 'Master\AreaKantor\SalesController@index');
            $router->post('/area_sales', 'Master\AreaKantor\SalesController@store');
            $router->get('/area_sales/{id}', 'Master\AreaKantor\SalesController@show');
            $router->put('/area_sales/{id}', 'Master\AreaKantor\SalesController@update');
            $router->delete('/area_sales/{id}', 'Master\AreaKantor\SalesController@delete');

            //PIC
            $router->get('/pic', 'Master\AreaKantor\PICController@index');
            $router->post('/pic', 'Master\AreaKantor\PICController@store');
            $router->get('/pic/{id}', 'Master\AreaKantor\PICController@show');
            $router->put('/pic/{id}', 'Master\AreaKantor\PICController@update');
            $router->delete('/pic/{id}', 'Master\AreaKantor\PICController@delete');

            //Jenis PIC
            $router->get('/jenis_pic', 'Master\AreaKantor\JPICController@index');
            $router->post('/jenis_pic', 'Master\AreaKantor\JPICController@store');
            $router->get('/jenis_pic/{id}', 'Master\AreaKantor\JPICController@show');
            $router->put('/jenis_pic/{id}', 'Master\AreaKantor\JPICController@update');
            $router->delete('/jenis_pic/{id}', 'Master\AreaKantor\JPICController@delete');


            $router->post('/mcc', 'Pengajuan\MasterCC_Controller@store'); // Memorandum Account Officer
            $router->get('/mao', 'Pengajuan\MasterAO_Controller@index'); // All Memorandum Account Officer
            $router->get('/mao/{id}', 'Pengajuan\MasterAO_Controller@show'); //GEt MAO BY ID
            $router->post('/mao/{id}', 'Pengajuan\MasterAO_Controller@update'); //Update MAO BY ID

            $router->get('mitra', 'Pengajuan\MasterCC_Controller@mitra');

            $router->get('/kode_area/ao', 'Master\CodeController@ao'); // AO -> dpm_online.kre_kode_group2
            $router->get('/kode_area/so', 'Master\CodeController@so'); // SO -> dpm_online.kre_kode_so
            $router->get('/kode_area/col', 'Master\CodeController@col'); // COL -> dpm_online.kre_kode_group3
            $router->get('/kode_area/mb', 'Master\CodeController@mb'); // MB -> dpm_online.kre_kode_group5
            $router->get('/kode_area/ca', 'Master\CodeController@ca'); // CA -> dpm_online.kre_kode_group6

            $router->get('/kode_area/ao/{username}', 'Master\CodeController@ao_user'); // AO -> dpm_online.kre_kode_group2 - Berdasarkan Nama User
            $router->get('/kode_area/so/{username}', 'Master\CodeController@so_user'); // SO -> dpm_online.kre_kode_so - Berdasarkan Nama User
            $router->get('/kode_area/col/{username}', 'Master\CodeController@col_user'); // COL -> dpm_online.kre_kode_group3 - Berdasarkan Nama User
            $router->get('/kode_area/mb/{username}', 'Master\CodeController@mb_user'); // MB -> dpm_online.kre_kode_group5 - Berdasarkan Nama User
            $router->get('/kode_area/ca/{username}', 'Master\CodeController@ca_user'); // CA -> dpm_online.kre_kode_group6 - Berdasarkan Nama User
        });

        $router->group(['prefix' => '/menu'], function () use ($router) {
            $router->get('/akses', 'Menu\MenuAccessController@index'); // Get All Data
            $router->post('/akses', 'Menu\MenuAccessController@store'); // Insert Data
            $router->get('/akses/{id}', 'Menu\MenuAccessController@show'); // Get Data based on Id User
            $router->put('/akses/{id}', 'Menu\MenuAccessController@update'); // Update Data based on Id User
            $router->delete('/akses/{id}', 'Menu\MenuAccessController@delete'); // Delete Data based on Id User

            $router->get('/', ['as' => 'menu', 'uses' => 'Menu\MenuMasterController@index']); //Get Data
            $router->post('/', 'Menu\MenuMasterController@store'); // Create Data
            // $router->get('/master', function () use ($router) {return redirect('/api/menu');});
            $router->get('/master/{slug}', ['as' => 'mastermenu', 'uses' => 'Menu\MenuMasterController@show']);
            $router->put('/master/{slug}', 'Menu\MenuMasterController@edit');
            $router->delete('/master/{slug}', 'Menu\MenuMasterController@delete'); // Delete Data based on slug (URL)

            $router->get('/sub', 'Menu\MenuSubController@index');
            $router->post('/sub', 'Menu\MenuSubController@store');
            $router->get('/sub/{slug}', ['as' => 'submenu', 'uses' => 'Menu\MenuSubController@show']);
            $router->put('/sub/{slug}', 'Menu\MenuSubController@edit');
            $router->delete('/sub/{slug}', 'Menu\MenuSubController@delete'); // Delete Data based on slug(URL)
        });

        $router->get('/pinjaman', 'PinjamanController@index');
        $router->post('/pinjaman', 'PinjamanController@store');
        $router->get('/pinjaman/plus', 'PinjamanController@plus');
    });

    //For User (Debitur)
    // $router->group(['prefix' => '/debt'], function () use ($router) {

    // }
});
