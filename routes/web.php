<?php

$router->get('/', function () use ($router) {
    return 'API - DEVIS' ;
});

$router->get('/api', function () use ($router) {
    return redirect('/') ;
});

// $router->post('/push', 'ImgController@push');

$router->post('/img', 'ImgController@upload');
$router->get('/img', 'ImgController@getDecode');

$router->post('test-up', 'ImgController@testUp');

// $router->post('/up_caa', 'ImgController@uploadCAA');

$router->get('produk', 'Master\CodeController@produk');

$router->get('segmentasi', 'Pengajuan\Tunggal\FaspinController@segmentasiBPR');

$router->group(['prefix' => '/wilayah'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return 'add parameters after slash';
    });

    // $router->get('/area_cabang/filter', 'Master\AreaKantor\CabangController@get_cabang');

    $router->group(['namespace' => 'Wilayah'], function() use ($router){

        // Provinsi
        $router->group(['prefix' => '/provinsi'], function () use ($router){
            $router->post('/', 'ProvinsiController@store');
            $router->get('/',  'ProvinsiController@index');
            // $router->get('/search/{search}', 'ProvinsiController@search'); // search Provinsi to Mitra
            $router->get('/{IdOrName}', 'ProvinsiController@show'); // Detail Dan Search
            $router->put('/{id}',    'ProvinsiController@update');
            $router->delete('/{id}', 'ProvinsiController@delete');

            // Trash
            $router->get('/trash/check',        'ProvinsiController@trash');
            $router->get('/trash/restore/{id}', 'ProvinsiController@restore');

            /** Search, Filter, Order By, Limit */
            $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', 
            ['subject' => 'Search Provinsi', 'uses' => 'ProvinsiController@search']);
        });

        // Kabupaten
        $router->get('/provinsi/{id}/kabupaten', 'KabupatenController@sector'); // Get Data Kabupaten By Id Provinsi
        $router->group(['prefix' => '/kabupaten'], function () use ($router){
            $router->post('/',          'KabupatenController@store');
            $router->get('/',           'KabupatenController@index');
            $router->get('/{IdOrName}', 'KabupatenController@show'); // Detail Dan Search
            $router->put('/{id}',       'KabupatenController@update');
            $router->delete('/{id}',    'KabupatenController@delete');

            //Trash
            $router->get('/trash/check',        'KabupatenController@trash');
            $router->get('/trash/restore/{id}', 'KabupatenController@restore');

            /** Search, Filter, Order By, Limit */
            $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', 
            ['subject' => 'Search Kabupaten', 'uses' => 'KabupatenController@search']);
        });

        // Kecamatan
        $router->get('/kabupaten/{id}/kecamatan', 'KecamatanController@sector'); // Get Data Kecamatan By Id Kabupaten
        $router->group(['prefix' => '/kecamatan'], function () use ($router){
            $router->post('/',          'KecamatanController@store');
            $router->get('/',           'KecamatanController@index');
            $router->get('/{IdOrName}', 'KecamatanController@show'); // Detail Dan Search
            $router->put('/{id}',       'KecamatanController@update');
            $router->delete('/{id}',    'KecamatanController@delete');

            // Trash
            $router->get('/trash/check',        'KecamatanController@trash');
            $router->get('/trash/restore/{id}', 'KecamatanController@restore');

            /** Search, Filter, Order By, Limit */
            $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', 
            ['subject' => 'Search Kecamatan', 'uses' => 'KecamatanController@search']);
        });

        // Kelurahan
        $router->get('/kecamatan/{id}/kelurahan', 'KelurahanController@sector'); // Get Data Kelurahan By Id Kecamatan
        $router->group(['prefix' => '/kelurahan'], function () use ($router){
            $router->post('/',          'KelurahanController@store');
            $router->get('/',           'KelurahanController@index');
            $router->get('/{IdOrName}', 'KelurahanController@show'); // Detail Dan Search
            $router->put('/{id}',       'KelurahanController@update');
            $router->delete('/{id}',    'KelurahanController@delete');

            // Trash
            $router->get('/trash/check',        'KelurahanController@trash');
            $router->get('/trash/restore/{id}', 'KelurahanController@restore');

            /** Search, Filter, Order By, Limit */
            $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', 
            ['subject' => 'Search Kelurahan', 'uses' => 'KelurahanController@search']);
        });
    });
});

$router->post('/login', 'AuthController@login'); // Login All Level

$router->put('/api/user/reset_password',     'UserController@resetPassword'); //Reset Password
$router->post('/api/operator/{id_trans_so}', 'Transaksi\MasterCA_Controller@operator');

// $router->group(['middleware' => ['jwt.auth', 'log'], 'prefix' => 'api'], function () use ($router) {
$router->group(['middleware' => ['jwt.auth', 'log'], 'prefix' => 'api'], function () use ($router) {

    // Logs (History)
    // $router->group(['prefix' => '/logs'], function () use ($router){
    //     $router->get('/',     ['subject' => 'Read Logs',  'uses' => 'LogsController@index']); //Log History All
    //     $router->get('/{id}', ['subject' => 'Detail Log', 'uses' => 'LogsController@detail']); //Log History By ID
    //     $router->get('/limit/{limit}', ['subject' => 'Limit Logs', 'uses' => 'LogsController@limit']); //Log History Limit
    //     $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Logs', 'uses' => 'LogsController@search']); //Log History Search
    // });

    // Users And User
    $router->get('/users',                ['subject' => 'Get All Users' ,        'uses' => 'UserController@getUsers']);
    $router->get('/users/{IdOrSearch}',   ['subject' => 'Deail Or Search User',  'uses' => 'UserController@IdOrSearch']);
    $router->get('/user',                 ['subject' => 'Detail User Login',     'uses' => 'UserController@index']);
    $router->put('/user/change_password', ['subject' => 'Change Password Login', 'uses' => 'UserController@changePassword']);

    // Otorisasi
    // $router->get('/oto', 'FlagAuthorController@otoIndex'); // Otorisasi
    // $router->get('/oto/{limit}/limit', 'FlagAuthorController@otoLimit'); // Otorisasi
    // $router->get('/oto/{id}', 'FlagAuthorController@otoShow');
    // $router->put('/oto/{id}', 'FlagAuthorController@otoUpdate');
    // $router->put('/oto/{id}/reject', 'FlagAuthorController@rejectOto');

    // Log Otorisasi
    // $router->get('/log_oto', 'FlagAuthorController@otoH');
    // $router->get('/log_oto/{year}', 'FlagAuthorController@otoHY');
    // $router->get('/log_oto/{year}/{month}', 'FlagAuthorController@otoHYM');

    // Count Otorisasi
    // $router->get('/count_oto', 'FlagAuthorController@countOto');

    // Approval
    // $router->get('/apro', 'FlagAuthorController@aproIndex'); // Approval
    // $router->get('/apro/{limit}/limit', 'FlagAuthorController@aproLimit'); // Approval
    // $router->get('/apro/{id}', 'FlagAuthorController@aproShow');
    // $router->put('/apro/{id}', 'FlagAuthorController@aproUpdate');
    // $router->put('/apro/{id}/reject', 'FlagAuthorController@rejectApro');

    // // Log Approval
    // $router->get('/log_apro', 'FlagAuthorController@aproH');
    // $router->get('/log_apro/{year}', 'FlagAuthorController@aproHY');
    // $router->get('/log_apro/{year}/{month}', 'FlagAuthorController@aproHYM');

    // // Count Otorisasi
    // $router->get('/count_apro', 'FlagAuthorController@countApro');

    // // Reset Otorisasi And Approval
    // $router->post('/oto/all/reset', 'FlagAuthorController@otoReset'); // Reset Otorisasi
    // $router->post('/apro/all/reset', 'FlagAuthorController@aproReset'); // Reset Otorisasi


    $router->group(['prefix' => '/master'], function () use ($router) {

        $router->group(['namespace' => 'Master\Bisnis'], function () use ($router){
            // Mitra Bisnis
            $router->get('/mitra',                 ['subject' => 'Read mitra',   'uses' => 'MitraController@index']);
            $router->get('/mitra/{kode_mitra}',    ['subject' => 'Detail Mitra', 'uses' => 'MitraController@show']);
            $router->get('/mitra/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Mitra', 'uses' => 'MitraController@search']);
        });

        $router->group(['namespace' => 'Master\AreaKantor'], function () use ($router){

            // Asal Data
            $router->group(['prefix' => '/asal_data'], function () use ($router){
                $router->post('/',      ['subject' => 'Create Asal_Data', 'uses' => 'AsalDataController@store']);
                $router->get('/',       ['subject' => 'Read Asal_Data',   'uses' => 'AsalDataController@index']);
                $router->put('/{id}',   ['subject' => 'Update Asal_Data', 'uses' => 'AsalDataController@update']);
                $router->delete('/{id}',['subject' => 'Delete Asal_Data', 'uses' => 'AsalDataController@delete']);
                $router->get('/{id}',   ['subject' => 'Detail Asal_Data', 'uses' => 'AsalDataController@show']);

                // Trash
                $router->get('/trash/check',        ['subject' => 'Trash of Asal_Data', 'uses' => 'AsalDataController@trash']);
                $router->get('/trash/restore/{id}', ['subject' => 'Restore Asal_Data',  'uses' => 'AsalDataController@restore']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Asal_Data', 'uses' => 'AsalDataController@search']);
            });

            //Area Kantor
            $router->group(['prefix' => '/area_kerja'], function () use ($router){
                $router->post('/',      ['subject' => 'Create Area', 'uses' => 'AreaController@store']);
                $router->get('/',       ['subject' => 'Read Area',   'uses' => 'AreaController@index']);
                $router->put('/{id}',   ['subject' => 'Update Area', 'uses' => 'AreaController@update']);
                $router->delete('/{id}',['subject' => 'Delete Area', 'uses' => 'AreaController@delete']);
                $router->get('/{id}',   ['subject' => 'Detail Area', 'uses' => 'AreaController@show']);

                // Trash
                $router->get('/trash/check',        ['subject' => 'Trash of Area', 'uses' => 'AreaController@trash']);
                $router->get('/trash/restore/{id}', ['subject' => 'Restore Area',  'uses' => 'AreaController@restore']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Area', 'uses' => 'AreaController@search']);
            });

            //Cabang Kantor
            $router->group(['prefix' => '/area_cabang'], function () use ($router){
                $router->post('/',      ['subject' => 'Create Cabang', 'uses' => 'CabangController@store']);
                $router->get('/',       ['subject' => 'Read Cabang',   'uses' => 'CabangController@index']);
                $router->put('/{id}',   ['subject' => 'Update Cabang', 'uses' => 'CabangController@update']);
                $router->delete('/{id}',['subject' => 'Delete Cabang', 'uses' => 'CabangController@delete']);
                $router->get('/{id}',   ['subject' => 'Detail Cabang', 'uses' => 'CabangController@show']);

                // Trash
                $router->get('/trash/check',        ['subject' => 'Trash of Cabang', 'uses' => 'CabangController@trash']);
                $router->get('/trash/restore/{id}', ['subject' => 'Restore Cabang',  'uses' => 'CabangController@restore']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Cabang', 'uses' => 'CabangController@search']);
            });

            // Area PIC
            $router->group(['prefix' => '/area_pic'], function () use ($router){
                $router->post('/',       ['subject' => 'Create Area PIC', 'uses' => 'AreaPICController@store']);
                $router->get('/',        ['subject' => 'Read Area PIC',   'uses' => 'AreaPICController@index']);
                $router->get('/{id}',    ['subject' => 'Detail Area PIC', 'uses' => 'AreaPICController@show']);
                $router->put('/{id}',    ['subject' => 'Update Area PIC', 'uses' => 'AreaPICController@update']);
                $router->delete('/{id}', ['subject' => 'Delete Area PIC', 'uses' => 'AreaPICController@delete']);

                // Trash
                $router->get('/trash/check',        ['subject' => 'Trash of Area_PIC', 'uses' => 'AreaPICController@trash']);
                $router->get('/trash/restore/{id}', ['subject' => 'Restore Area_PIC',  'uses' => 'AreaPICController@restore']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Area PIC', 'uses' => 'AreaPICController@search']);
            });

            // Daftar PIC
            $router->group(['prefix' => '/pic'], function () use ($router){
                $router->post('/',      ['subject' => 'Create PIC', 'uses' => 'PICController@store']);
                $router->get('/',       ['subject' => 'Read PIC',   'uses' => 'PICController@index']);
                $router->put('/{id}',   ['subject' => 'Update PIC', 'uses' => 'PICController@update']);
                $router->delete('/{id}',['subject' => 'Delete PIC', 'uses' => 'PICController@delete']);
                $router->get('/{id}',   ['subject' => 'Detail PIC', 'uses' => 'PICController@show']);

                // Trash
                $router->get('/trash/check',        ['subject' => 'Trash of PIC', 'uses' => 'PICController@trash']);
                $router->get('/trash/restore/{id}', ['subject' => 'Restore PIC',  'uses' => 'PICController@restore']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search PIC', 'uses' => 'PICController@search']);
            });

            //Jenis PIC
            $router->group(['prefix' => '/jenis_pic'], function () use ($router){
                $router->post('/',      ['subject' => 'Create jenis_pic', 'uses' => 'JPICController@store']);
                $router->get('/',       ['subject' => 'Read jenis_pic',   'uses' => 'JPICController@index']);
                $router->put('/{id}',   ['subject' => 'Update jenis_pic', 'uses' => 'JPICController@update']);
                $router->delete('/{id}',['subject' => 'Delete jenis_pic', 'uses' => 'JPICController@delete']);
                $router->get('/{id}',   ['subject' => 'Detail jenis_pic', 'uses' => 'JPICController@show']);

                // Trash
                $router->get('trash/check',        ['subject' => 'Trash of jenis_pic', 'uses' => 'JPICController@trash']);
                $router->get('trash/restore/{id}', ['subject' => 'Restore jenis_pic',  'uses' => 'JPICController@restore']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search jenis_pic', 'uses' => 'JPICController@search']);
            });
        });

        
        $router->group(['middleware' => 'pic'], function() use ($router) {
            // DAS
            $router->get('/das',       ['subject' => 'Get Trans_SO from DAS Admin',     'uses' => 'Pengajuan\DASController@index']);
            $router->get('/das/{id}',  ['subject' => 'Detail Trans_SO from DAS Admin',  'uses' => 'Pengajuan\DASController@show']);
            $router->post('/das/{id}', ['subject' => 'Give Status and Note to Trans_SO','uses' => 'Pengajuan\DASController@update']);
    
            // Search
            $router->get('/das/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Trans_SO from DAS Admin', 'uses' => 'Pengajuan\DASController@search']);

            // HM
            $router->get('/hm',      ['subject' => 'Get Trans_SO from ds_spv',         'uses' => 'Pengajuan\HMController@index']);
            $router->get('/hm/{id}', ['subject' => 'Detail Trans_SO from ds_spv',      'uses' => 'Pengajuan\HMController@show']);
            $router->put('/hm/{id}', ['subject' => 'Give Status and Note to Trans_SO', 'uses' => 'Pengajuan\HMController@update']);
    
            // Search
            $router->get('/hm/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Trans_SO from ds_spv', 'uses' => 'Pengajuan\HMController@search']);
        });

        // Transaksi From SO -> CAA, etc
        $router->group(['middleware' => 'pic', 'namespace' => 'Transaksi'], function() use ($router) {
            // Trans SO
            $router->group(['prefix' => '/mcc',], function() use ($router) {
                $router->post('/',     ['subject' => 'Create Trans_SO', 'uses' => 'MasterSO_Controller@store']);
                $router->get('/',      ['subject' => 'Read Trans_SO',   'uses' => 'MasterSO_Controller@index']);
                $router->post('/{id}', ['subject' => 'Update Trans_SO', 'uses' => 'MasterSO_Controller@update']);
                $router->get('/{id}',  ['subject' => 'Detail Trans_SO', 'uses' => 'MasterSO_Controller@show']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Trans_SO', 'uses' => 'MasterSO_Controller@search']);

                // Filter
                $router->get('/filter/{year}/{month}', ['subject' => 'Filter Trans_SO', 'uses' => 'MasterSO_Controller@filter']);
            });

            // Trans AO
            $router->group(['prefix' => '/mao'], function() use ($router) {
                $router->post('/{id}', ['subject' => 'Create Trans_AO', 'uses' => 'MasterAO_Controller@update']);
                $router->get('/',      ['subject' => 'Read Trans_SO',   'uses' => 'MasterAO_Controller@index']);
                $router->get('/{id}',  ['subject' => 'Detail Trans_SO', 'uses' => 'MasterAO_Controller@show']);
                $router->post('/{id_transaksi}/pers_ideb',  ['subject' => 'persetujuan ideb Trans_SO', 'uses' => 'LampiranController@form_ideb']);
                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Trans_SO', 'uses' => 'MasterAO_Controller@search']);

                // Filter Year
                $router->get('/filter/{year}/{month}', ['subject' => 'Filter Trans_SO', 'uses' => 'MasterAO_Controller@filter']);
                
                // Filter Status
                $router->get('/status/{ao_ca}/{status}', ['subject' => 'Filter status_ao', 'uses' => 'MasterAO_Controller@indexWait']);
            });

            // Trans CA
            $router->group(['prefix' => '/mca'], function() use ($router) {
                $router->post('/{id}', ['subject' => 'Create Trans_CA', 'uses' => 'MasterCA_Controller@update']);
                $router->get('/',      ['subject' => 'Read Trans_AO',   'uses' => 'MasterCA_Controller@index']);
                $router->get('/{id}',  ['subject' => 'Detail Trans_AO', 'uses' => 'MasterCA_Controller@show']);

                //Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Trans_AO', 'uses' => 'MasterCA_Controller@search']);

                // Filter
                $router->get('/filter/{year}/{month}', ['subject' => 'Filter Trans_AO', 'uses' => 'MasterCA_Controller@filter']);

                // Revisi
                $router->post('/{id_trans_so}/revisi/{id_trans_ca}', ['subject' => 'Revisi Trans_CA', 'uses' => 'MasterCA_Controller@revisi']); //Update CA BY ID

                // Full Show after update
                $router->get('/{id}/detail', ['subject' => 'Detail After Update Trans_AO', 'uses' => 'MasterCA_Controller@full_show']);

                $router->get('/status/{ao_ca}/{status}', ['subject' => 'Filter status_ca', 'uses' => 'MasterCA_Controller@indexWait']);
            });


            // Trans CAA
            $router->group(['prefix' => '/mcaa'], function() use ($router) {
                // Tahap 1
                $router->post('/{id}', ['subject' => 'Create Trans_CAA', 'uses' => 'MasterCAA_Controller@update']);
                $router->get('/',      ['subject' => 'Read Trans_CAA',   'uses' => 'MasterCAA_Controller@index']);
                $router->get('/{id}',  ['subject' => 'Detail Trans_CAA', 'uses' => 'MasterCAA_Controller@show']);

                // Tahap 2 - Team CAA
                $router->get('/{id}/detail', ['subject' => 'Detail Trans_CAA', 'uses' => 'MasterCAA_Controller@detail']);

                // Search
                $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Trans_CA', 'uses' => 'MasterCAA_Controller@search']);

                // Filter
                $router->get('/filter/{year}/{month}', ['subject' => 'Filter Trans_CAA', 'uses' => 'MasterCAA_Controller@filter']);

                // Approval By Team CAA
                $router->get('/{id}/approval',               ['subject' => 'Get Approval List', 'uses' => 'Approval_Controller@index']);
                $router->get('/{id}/approval/{id_approval}', ['subject' => 'Detail Approval', 'uses' => 'Approval_Controller@show']);
                $router->post('/{id}/approval/{id_approval}',['subject' => 'Make Approval', 'uses' => 'Approval_Controller@approve']);
            });

            $router->get('/team_caa', ['subject' => 'Get Komite_CAA', 'uses' => 'Approval_Controller@list_team']);  // Get List Team CAA
            $router->get('/team_caa/{id_team}', ['subject' => 'Detail Komite_CAA', 'uses' => 'Approval_Controller@detail_team']);  // Get List Team CAA
            $router->get('/report/approval/{id_trans_so}', ['subject' => 'Report Approval', 'uses' => 'Approval_Controller@report_approval']);
        });
    });

    // Menu
    $router->group(['prefix' => '/menu', 'namespace' => 'Menu'], function () use ($router) {

        // Menu Master
        $router->get('/', function () use ($router) {return redirect('/api/menu/master');});

        $router->group(['prefix' => '/master'], function() use ($router){
            $router->post('/',           ['subject' => 'Create Master Menu', 'uses' => 'MenuMasterController@store']);
            $router->get('/',            ['subject' => 'Read Master Menu',   'uses' => 'MenuMasterController@index']);
            $router->put('/{IdOrSlug}',  ['subject' => 'Update Master Menu', 'uses' => 'MenuMasterController@update']);
            $router->delete('{IdOrSlug}',['subject' => 'Delete Master Menu', 'uses' => 'MenuMasterController@delete']);
            $router->get('/{IdOrSlug}',  ['subject' => 'Detail Master Menu', 'uses' => 'MenuMasterController@show']);

            // Trash
            $router->get('/trash/check',        ['subject' => 'Trash of Master Menu', 'uses' => 'MenuMasterController@trash']);
            $router->get('/trash/restore/{id}', ['subject' => 'Restore Master Menu',  'uses' => 'MenuMasterController@restore']);

            // Search
            $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Master Menu', 'uses' => 'MenuMasterController@search']);
        });

        // Menu Akses
        $router->group(['prefix' => '/akses'], function() use ($router){
            $router->post('/',      ['subject' => 'Create Access Menu', 'uses' => 'MenuAccessController@store']);
            $router->get('/',       ['subject' => 'Read Access Menu',   'uses' => 'MenuAccessController@index']);
            $router->put('/{id}',   ['subject' => 'Update Access Menu', 'uses' => 'MenuAccessController@update']);
            $router->delete('/{id}',['subject' => 'Delete Access Menu', 'uses' => 'MenuAccessController@delete']);
            $router->get('/{id}',   ['subject' => 'Detail Access Menu', 'uses' => 'MenuAccessController@show']);

            // Trash
            $router->get('/trash/check',        ['subject' => 'Trash of Access Menu','uses' => 'MenuAccessController@trash']);
            $router->get('/trash/restore/{id}', ['subject' => 'Restore Access Menu', 'uses' => 'MenuAccessController@restore']);
        });

        // Sub Menu
        $router->group(['prefix' => '/sub'], function() use ($router){
            $router->post('/',             ['subject' => 'Create Sub Menu', 'uses' => 'MenuSubController@store']);
            $router->get('/',              ['subject' => 'Read Sub Menu',   'uses' => 'MenuSubController@index']);
            $router->put('/{IdOrSlug}',    ['subject' => 'Update Sub Menu', 'uses' => 'MenuSubController@update']);
            $router->delete('/{IdOrSlug}', ['subject' => 'Delete Sub Menu', 'uses' => 'MenuSubController@delete']);
            $router->get('/{IdOrSlug}',    ['display' => 'Detail Sub Menu', 'uses' => 'MenuSubController@show']);

            // Trash
            $router->get('/trash/check',        ['display' => 'Trash of Sub Menu', 'uses' => 'MenuSubController@trash']);
            $router->get('/trash/restore/{id}', ['display' => 'Restore Sub Menu',  'uses' => 'MenuSubController@restore']);

            // Search
            $router->get('/{param}/{key}={value}/status={status}/{orderVal}={orderBy}/limit={limit}', ['subject' => 'Search Sub Menu', 'uses' => 'MenuSubController@search']);
        });

    });

    // Single Data When Transactioning From SO to CAA, etc
    $router->group(['namespace' => 'Pengajuan\Tunggal'], function() use ($router){

        // Fasilitas Pinjaman
        $router->group(['prefix' => '/faspin'], function() use ($router) {
            $router->get('/{id}',  ['subject' => 'Detail fasilitas_pinjaman', 'uses' => 'FasPinController@show']);
            $router->post('/{id}', ['subject' => 'Update fasilitas_pinjaman', 'uses' => 'FasPinController@update']);
        });

        // Calon Debitur
        $router->group(['prefix' => '/debitur'], function() use ($router) {
            $router->get('/{id}',  ['subject' => 'Detail calon_debitur', 'uses' => 'DebiturController@show']);
            $router->post('/{id}', ['subject' => 'Update calon_debitur', 'uses' => 'DebiturController@update']);
        });

        // Pasangan
        $router->group(['prefix' => '/pasangan'], function() use ($router) {
            $router->get('/{id}',  ['subject' => 'Detail pasangan calon_debitur', 'uses' => 'PasanganController@show']);
            $router->post('/{id}', ['subject' => 'Update pasangan calon_debitur', 'uses' => 'PasanganController@update']);
        });

        // Penjamin
        $router->group(['prefix' => '/penjamin'], function() use ($router) {
            $router->get('/{id}',  ['subject' => 'Detail penjamin', 'uses' => 'PenjaminController@show']);
            $router->post('/{id}', ['subject' => 'Update penjamin', 'uses' => 'PenjaminController@update']);
        });

        // Agunan
        $router->group(['prefix' => '/agunan'], function() use ($router) {
            // Agunan Tabah / Sertifikat
            $router->group(['prefix' => '/tanah'], function() use ($router) {
                $router->post('/{id_trans}/store',  ['subject' => 'Create agunan_tanah', 'uses' => 'TanahController@store']);
                $router->get('/{id}',  ['subject' => 'Detail agunan_tanah', 'uses' => 'TanahController@show']);
                $router->post('/{id}', ['subject' => 'Update agunan_tanah', 'uses' => 'TanahController@update']);
            });

            // Agunan Kendaraan
            $router->group(['prefix' => '/kendaraan'], function() use ($router) {
                $router->get('/{id}',  ['subject' => 'Detail agunan_kendaraan', 'uses' => 'KendaraanController@show']);
                $router->post('/{id}', ['subject' => 'Update agunan_kendaraan', 'uses' => 'KendaraanController@update']);
            });
        });

        // Pemeriksaan Agunan
        $router->group(['prefix' => '/periksa'], function() use ($router) {
            // Pemeriksaaan Agunan Tabah / Sertifikat
            $router->group(['prefix' => '/tanah'], function() use ($router) {
                $router->get('/{id}',  ['subject' => 'Detail pemeriksaan_agunan_tanah', 'uses' => 'PemeriksaanTanahController@show']);
                $router->post('/{id}', ['subject' => 'Update pemeriksaan_agunan_tanah', 'uses' => 'PemeriksaanTanahController@update']);
            });

            // Pemeriksaaan Agunan Kendaraan
            $router->group(['prefix' => '/kendaraan'], function() use ($router) {
                $router->get('/{id}',  ['subject' => 'Detail pemeriksaan_agunan_kendaraan', 'uses' => 'PemeriksaanKendaraanController@show']);
                $router->post('/{id}', ['subject' => 'Update pemeriksaan_agunan_kendaraan', 'uses' => 'PemeriksaanKendaraanController@update']);
            });
        });

        // Kapasitas Bulanan
        $router->group(['prefix' => '/kap_bul'], function() use ($router) {
            $router->get('/{id}',  ['subject' => 'Read kapasitas_bulanan',   'uses' => 'KapBulController@show']);
            $router->post('/{id}', ['subject' => 'Update kapasitas_bulanan', 'uses' => 'KapBulController@update']);
        });

        // PENDAPATAN USAHA CADEBT
        $router->group(['prefix' => '/usaha_cadebt'], function() use ($router) {
            $router->get('/{id}',  ['subject' => 'Read pendapatan_calon_debitur',   'uses' => 'UsahaCadebtController@show']);
            $router->post('/{id}', ['subject' => 'Update pendapatan_calon_debitur', 'uses' => 'UsahaCadebtController@update']);
        });
    });
    
    // CA
    $router->group(['namespace' => 'Pengajuan\TunggalCA'], function() use ($router){
        // Mutasi Bank
        $router->group(['prefix' => '/mutasi_bank'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read Mutasi Bank',     'uses' => 'MutasiController@index']);
            $router->get('/{id}', ['subject' => 'Detail Mutasi Bank',   'uses' => 'MutasiController@show']);
            $router->put('/{id}', ['subject' => 'Update pendapatan_calon_debitur', 'uses' => 'MutasiController@update']);
        });

        // Data Keuangan (Tabungan) Bank Milik Nasabah
        $router->group(['prefix' => '/data_keuangan'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read Data Tabungan',   'uses' => 'LogTabController@index']);
            $router->get('/{id}', ['subject' => 'Detail Tabungan',      'uses' => 'LogTabController@show']);
            $router->put('/{id}', ['subject' => 'Update Data Tabungan', 'uses' => 'LogTabController@update']);
        });

        // Infor,asi Analisa
        $router->group(['prefix' => '/info_cc'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read Info Analisa Credit Checking',   'uses' => 'IAC_Controller@index']);
            $router->get('/{id}', ['subject' => 'Detail Info Analisa Credit Checking', 'uses' => 'IAC_Controller@show']);
            $router->put('/{id}', ['subject' => 'Update Info Analisa Credit Checking', 'uses' => 'IAC_Controller@update']);
        });

        // Ringkasan Analisa
        $router->group(['prefix' => '/ring_analisa'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read - Ringkasan Analisa',   'uses' => 'RAnalisController@index']);
            $router->get('/{id}', ['subject' => 'Detail - Ringkasan Analisa', 'uses' => 'RAnalisController@show']);
            $router->put('/{id}', ['subject' => 'Update - Ringkasan Analisa', 'uses' => 'RAnalisController@update']);
        });

        // Asuransi Jiwa
        $router->group(['prefix' => '/asuransi_jiwa'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read - Asuransi Jiwa',   'uses' => 'AsJiwaController@index']);
            $router->get('/{id}', ['subject' => 'Detail - Asuransi Jiwa', 'uses' => 'AsJiwaController@show']);
            $router->put('/{id}', ['subject' => 'Update - Asuransi Jiwa', 'uses' => 'AsJiwaController@update']);
        });

        // Asuransi Jaminan
        $router->group(['prefix' => '/asuransi_jaminan'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read - Asuransi Jaminan',   'uses' => 'AsJaminanController@index']);
            $router->get('/{id}', ['subject' => 'Detail - Asuransi Jaminan', 'uses' => 'AsJaminanController@show']);
            $router->put('/{id}', ['subject' => 'Update - Asuransi Jaminan', 'uses' => 'AsJaminanController@update']);
        });
    });

    $router->group(['namespace' => 'Pengajuan\Rekomendasi'], function() use ($router){
        // Rekomendasi AO
        $router->group(['prefix' => '/rekom_ao'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read - Rekomendasi AO',   'uses' => 'RekomAoController@index']);
            $router->get('/{id}', ['subject' => 'Detail - Rekomendasi AO', 'uses' => 'RekomAoController@show']);
            $router->put('/{id}', ['subject' => 'Update - Rekomendasi AO', 'uses' => 'RekomAoController@update']);
        });

        // Rekomendasi CA
        $router->group(['prefix' => '/rekom_ca'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read - Rekomendasi CA',   'uses' => 'RekomCaController@index']);
            $router->get('/{id}', ['subject' => 'Detail - Rekomendasi CA', 'uses' => 'RekomCaController@show']);
            $router->put('/{id}', ['subject' => 'Update - Rekomendasi CA', 'uses' => 'RekomCaController@update']);
        });

        // Rekomendasi Pinjaman
        $router->group(['prefix' => '/rekom_pinjaman'], function() use ($router) {
            $router->get('/',     ['subject' => 'Read - Rekomendasi Pinjaman',   'uses' => 'RekomPinController@index']);
            $router->get('/{id}', ['subject' => 'Detail - Rekomendasi Pinjaman', 'uses' => 'RekomPinController@show']);
            $router->put('/{id}', ['subject' => 'Update - Rekomendasi Pinjaman', 'uses' => 'RekomPinController@update']);
        });
    });

    $router->group(['namespace' => 'Pengajuan\ao'], function() use ($router){
        // Rekomendasi AO
        $router->group(['prefix' => '/verifikasi'], function() use ($router) {
            // $router->get('/',     ['subject' => 'Read - Rekomendasi AO',   'uses' => 'VerifController@index']);
            // $router->get('/{id}', ['subject' => 'Detail - Rekomendasi AO', 'uses' => 'VerifController@show']);
            $router->put('/{id}', ['subject' => 'Update - Rekomendasi AO', 'uses' => 'VerifController@update']);
        });

        $router->group(['prefix' => '/validasi'], function() use ($router) {
            // $router->get('/',     ['subject' => 'Read - Rekomendasi AO',   'uses' => 'ValidController@index']);
            // $router->get('/{id}', ['subject' => 'Detail - Rekomendasi AO', 'uses' => 'ValidController@show']);
            $router->put('/{id}', ['subject' => 'Update - Rekomendasi AO', 'uses' => 'ValidController@update']);
        });
    });
});
