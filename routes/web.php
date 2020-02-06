<?php

$router->get('/', function () use ($router) {
    return 'API - DEVIS' ;
});

$router->get('/api', function () use ($router) {
    return redirect('/') ;
});

// $router->post('/push', 'ImgController@push');

// $router->post('/img', 'ImgController@upload');
// $router->get('/img', 'ImgController@getDecode');

// $router->post('/up_caa', 'ImgController@uploadCAA');

// $router->get('produk', 'Master\CodeController@produk');

$router->group(['prefix' => '/wilayah'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return 'add parameters after slash';
    });

    // $router->get('/area_cabang/filter', 'Master\AreaKantor\CabangController@get_cabang');

    $router->group(['namespace' => 'Wilayah'], function() use ($router){

        // Provinsi
        $router->get('/all/provinsi', 'ProvinsiController@all');
        $router->group(['prefix' => '/provinsi'], function () use ($router){
            $router->get('/', 'ProvinsiController@index');
            $router->post('/', 'ProvinsiController@store');
            // $router->get('/search/{search}', 'ProvinsiController@search'); // search Provinsi to Mitra
            $router->get('/{IdOrName}', 'ProvinsiController@show');
            $router->put('/{id}', 'ProvinsiController@update');
            $router->delete('/{id}', 'ProvinsiController@delete');
            $router->get('/{search}/search', 'ProvinsiController@search');
        });

        // Kabupaten
        $router->get('/all/kabupaten', 'KabupatenController@all');
        $router->get('/provinsi/{id}/kabupaten', 'KabupatenController@sector'); // Get Data Kabupaten By Id Provinsi
        $router->group(['prefix' => '/kabupaten'], function () use ($router){
            $router->get('/', 'KabupatenController@index');
            $router->post('/', 'KabupatenController@store');
            $router->get('/{IdOrName}', 'KabupatenController@show');
            $router->put('/{id}', 'KabupatenController@update');
            $router->delete('/{id}', 'KabupatenController@delete');
            $router->get('/{search}/search', 'KabupatenController@search');
        });

        // Kecamatan
        $router->get('/all/kecamatan', 'KecamatanController@all');
        $router->get('/kabupaten/{id}/kecamatan', 'KecamatanController@sector'); // Get Data Kecamatan By Id Kabupaten
        $router->group(['prefix' => '/kecamatan'], function () use ($router){
            $router->get('/', 'KecamatanController@index');
            $router->post('/', 'KecamatanController@store');
            $router->get('/{IdOrName}', 'KecamatanController@show');
            $router->put('/{id}', 'KecamatanController@update');
            $router->delete('/{id}', 'KecamatanController@delete');
            $router->get('/{search}/search', 'KecamatanController@search');
        });

        // Kelurahan
        $router->get('/all/kelurahan', 'KelurahanController@all');
        $router->get('/kecamatan/{id}/kelurahan', 'KelurahanController@sector'); // Get Data Kelurahan By Id Kecamatan
        $router->group(['prefix' => '/kelurahan'], function () use ($router){
            $router->get('/', 'KelurahanController@index');
            $router->post('/', 'KelurahanController@store');
            $router->get('/{IdOrName}', 'KelurahanController@show');
            $router->put('/{id}', 'KelurahanController@update');
            $router->delete('/{id}', 'KelurahanController@delete');
            $router->get('/{search}/search', 'KelurahanController@search');
        });
    });
});

$router->post('/login', 'AuthController@login'); // Login All Level

$router->put('/api/user/reset_password', 'UserController@resetPassword'); //Reset Password
$router->post('/api/operator/{id_trans_so}', 'Transaksi\MasterCA_Controller@operator');

// $router->group(['middleware' => ['jwt.auth', 'log'], 'prefix' => 'api'], function () use ($router) {
$router->group(['middleware' => ['jwt.auth'], 'prefix' => 'api'], function () use ($router) {

    // Logs (History)
    // $router->group(['prefix' => '/logs'], function () use ($router){
    //     $router->get('/', 'LogsController@index'); //Log History All
    //     $router->get('/{id}', 'LogsController@detail'); //Log History By ID
    //     $router->get('/limit/{limit}', 'LogsController@limit'); //Log History Limit
    //     $router->get('/search/{search}', 'LogsController@search'); //Log History Search
    // });

    // Users And User
    $router->get('/users', ['subject' => 'Get All Users' ,'uses' => 'UserController@getUsers']);
    $router->get('/users/{IdOrSearch}', ['subject' => 'Deail Or Search User', 'uses' => 'UserController@IdOrSearch']);
    $router->get('/user', ['subject' => 'Detail User Login', 'uses' => 'UserController@index']);
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
            $router->get('/mitra', ['subject' => 'Get Mitra with active status', 'uses' => 'MitraController@index']);
            $router->get('/mitra/{kode_mitra}', ['subject' => 'Detail Mitra', 'uses' => 'MitraController@show']);
            $router->get('/mitra/{search}/search', ['subject' => 'Search Mitra', 'uses' => 'MitraController@search']);
        });

        $router->group(['namespace' => 'Master\AreaKantor'], function () use ($router){

            // Asal Data
            $router->get('/all/asal_data', ['subject' => 'Display Asal_Data with all status', 'uses' => 'AsalDataController@all']);
            $router->group(['prefix' => '/asal_data'], function () use ($router){
                $router->get('/', ['subject' => 'Get Asal_Data with active status', 'uses' => 'AsalDataController@index']);
                $router->post('/', ['subject' => 'Insert Asal_Data', 'uses' => 'AsalDataController@store']);
                $router->get('/{id}', ['subject' => 'Detail Asal_Data', 'uses' => 'AsalDataController@show']);
                $router->put('/{id}', ['subject' => 'Update Asal_Data', 'uses' => 'AsalDataController@update']);
                $router->delete('/{id}', ['subject' => 'Delete Asal_Data', 'uses' => 'AsalDataController@delete']);
                $router->get('/{search}/search', ['subject' => 'Search Asal_Data', 'uses' => 'AsalDataController@search']);
            });

            //Area Kantor
            $router->get('/all/area_kerja', ['subject' => 'All Area all status', 'uses' => 'AreaController@all']);
            $router->group(['prefix' => '/area_kerja'], function () use ($router){
                $router->get('/', ['subject' => 'Get Area with active status', 'uses' => 'AreaController@index']);
                $router->post('/', ['subject' => 'Create Area', 'uses' => 'AreaController@store']);
                $router->get('/{id}', ['subject' => 'Detail Area', 'uses' => 'AreaController@show']);
                $router->put('/{id}', ['subject' => 'Update Area', 'uses' => 'AreaController@update']);
                $router->delete('/{id}', ['subject' => 'Delete Area', 'uses' => 'AreaController@delete']);
                $router->get('/{search}/search', ['subject' => 'Search Area', 'uses' => 'AreaController@search']);
            });

            //Cabang Kantor
            $router->get('/all/area_cabang', ['subject' => 'Display All Cabang with all status', 'uses' => 'CabangController@all']);
            $router->group(['prefix' => '/area_cabang'], function () use ($router){
                $router->get('/', ['subject' => 'Get Cabang with active status', 'uses' => 'CabangController@index']);
                $router->post('/', ['subject' => 'Create Cabang', 'uses' => 'CabangController@store']);
                $router->get('/{id}', ['subject' => 'Detail Cabang', 'uses' => 'CabangController@show']);
                $router->put('/{id}', ['subject' => 'Update Cabang', 'uses' => 'CabangController@update']);
                $router->delete('/{id}', ['subject' => 'Delete Cabang', 'uses' => 'CabangController@delete']);
                $router->get('/{search}/search', ['subject' => 'Search Cabang', 'uses' => 'CabangController@search']);
            });

            // Area PIC
            $router->get('/all/area_pic', ['subject' => 'Display All Area PIC with all status', 'uses' => 'AreaPICController@all']);
            $router->group(['prefix' => '/area_pic'], function () use ($router){
                $router->get('/', ['subject' => 'Get Area PIC with active status', 'uses' => 'AreaPICController@index']);
                $router->post('/', ['subject' => 'Create Area PIC', 'uses' => 'AreaPICController@store']);
                $router->get('/{id}', ['subject' => 'Detail Area PIC', 'uses' => 'AreaPICController@show']);
                $router->put('/{id}', ['subject' => 'Update Area PIC', 'uses' => 'AreaPICController@update']);
                $router->delete('/{id}', ['subject' => 'Delete Area PIC', 'uses' => 'AreaPICController@delete']);
                $router->get('/{search}/search', ['subject' => 'Search Area PIC', 'uses' => 'AreaPICController@search']);
            });

            // Daftar PIC
            $router->get('/all/pic', ['subject' => 'Display All PIC with all status', 'uses' => 'PICController@all']);
            $router->group(['prefix' => '/pic'], function () use ($router){
                $router->get('/', ['subject' => 'Get PIC with active status', 'uses' => 'PICController@index']);
                $router->post('/', ['subject' => 'Create PIC', 'uses' => 'PICController@store']);
                $router->get('/{id}', ['subject' => 'Detail PIC', 'uses' => 'PICController@show']);
                $router->put('/{id}', ['subject' => 'Update PIC', 'uses' => 'PICController@update']);
                $router->delete('/{id}', ['subject' => 'Delete PIC', 'uses' => 'PICController@delete']);
                $router->get('/{search}/search', ['subject' => 'Search PIC', 'uses' => 'PICController@search']);
            });

            //Jenis PIC
            $router->group(['prefix' => '/jenis_pic'], function () use ($router){
                $router->get('/', ['subject' => 'Get jenis_pic', 'uses' => 'JPICController@index']);
                $router->post('/', ['subject' => 'Create jenis_pic', 'uses' => 'JPICController@store']);
                $router->get('/{id}', ['subject' => 'Detail jenis_pic', 'uses' => 'JPICController@show']);
                $router->put('/{id}', ['subject' => 'Update jenis_pic', 'uses' => 'JPICController@update']);
                $router->delete('/{id}', ['subject' => 'Delete jenis_pic', 'uses' => 'JPICController@delete']);
                $router->get('/{search}/search', ['subject' => 'Search jenis_pic', 'uses' => 'JPICController@search']);
            });

            // Kode Kantor from DPM_ONLINE (user)
            // $router->get('/kode_kantor', 'KodeKantorController@index');

        });

        // Transaksi From SO -> CAA, etc
        $router->get('/das', ['subject' => 'Get Trans_SO from DAS Admin', 'uses' => 'Pengajuan\DASController@index']); //Cek HM
        $router->get('/das/{id}', ['subject' => 'Detail Trans_SO from DAS Admin', 'uses' => 'Pengajuan\DASController@show']); //Cek HM
        $router->post('/das/{id}', ['subject' => 'Give Status and Note to Trans_SO', 'uses' => 'Pengajuan\DASController@update']); //Cek HM
        $router->get('/das/{search}/search', ['subject' => 'Search Trans_SO from DAS Admin', 'uses' => 'Pengajuan\DASController@search']);

        $router->get('/hm', ['subject' => 'Get Trans_SO from ds_spv', 'uses' => 'Pengajuan\HMController@index']); //Cek HM
        $router->get('/hm/{id}', ['subject' => 'Detail Trans_SO from ds_spv', 'uses' => 'Pengajuan\HMController@show']); //Cek HM
        $router->put('/hm/{id}', ['subject' => 'Give Status and Note to Trans_SO', 'uses' => 'Pengajuan\HMController@update']); //Cek HM
        $router->get('/hm/{search}/search', ['subject' => 'Search Trans_SO from ds_spv', 'uses' => 'Pengajuan\HMController@search']);

        // Transaksi From SO -> CAA, etc
        $router->group(['namespace' => 'Transaksi'], function() use ($router) {

            // Trans SO
            $router->group(['prefix' => '/mcc',], function() use ($router) {
                $router->get('/', ['subject' => 'Get Trans_SO', 'uses' => 'MasterSO_Controller@index']);
                $router->post('/', ['subject' => 'Create Trans_SO', 'uses' => 'MasterSO_Controller@store']); // Memorandum Credit Checking
                $router->get('/{id}', ['subject' => 'Detail Trans_SO', 'uses' => 'MasterSO_Controller@show']);
                $router->post('/{id}', ['subject' => 'Update Trans_SO', 'uses' => 'MasterSO_Controller@update']); // Update MCC
                $router->get('/{search}/search', ['subject' => 'Search Trans_SO', 'uses' => 'MasterSO_Controller@search']);
            });

            // Trans AO
            $router->group(['prefix' => '/mao'], function() use ($router) {
                $router->get('/', ['subject' => 'Get Trans_SO', 'uses' => 'MasterAO_Controller@index']); // All Memorandum Account Officer
                $router->get('/{id}', ['subject' => 'Detail Trans_SO', 'uses' => 'MasterAO_Controller@show']); //GEt MAO BY ID
                $router->post('/{id}', ['subject' => 'Create Trans_AO', 'uses' => 'MasterAO_Controller@update']); //Update MAO BY ID
                $router->get('/{search}/search', ['subject' => 'Search Trans_SO', 'uses' => 'MasterAO_Controller@search']);
            });

            // Trans CA
            $router->group(['prefix' => '/mca'], function() use ($router) {
                $router->get('/', ['subject' => 'Get Trans_AO', 'uses' => 'MasterCA_Controller@index']); // All Memorandum Credit Analyst
                $router->get('/{id}', ['subject' => 'Detail Trans_AO', 'uses' => 'MasterCA_Controller@show']); //GEt CA BY ID
                $router->post('/{id}', ['subject' => 'Create Trans_CA', 'uses' => 'MasterCA_Controller@update']); //Update CA BY ID
                $router->post('/{id_trans_so}/revisi/{id_trans_ca}', ['subject' => 'Revisi Trans_CA', 'uses' => 'MasterCA_Controller@revisi']); //Update CA BY ID
                $router->get('/{search}/search', ['subject' => 'Search Trans_AO', 'uses' => 'MasterCA_Controller@search']);
            });


            // Trans CAA
            $router->group(['prefix' => '/mcaa'], function() use ($router) {
                // Tahap 1
                $router->get('/', ['subject' => 'Get Trans_CA', 'uses' => 'MasterCAA_Controller@index']); // All Memorandum Credit Analyst
                // $router->get('/{id}', 'MasterCAA_Controller@show'); //GEt CA BY ID
                $router->get('/{id}', ['subject' => 'Detail Trans_CA', 'uses' => 'MasterCAA_Controller@show']); //GEt CA BY ID Or to Route
                $router->post('/{id}', ['subject' => 'Update Trans_CA', 'uses' => 'MasterCAA_Controller@update']); //Update CA BY ID

                // Tahap 2 - Team CAA
                $router->get('/{id}/detail', ['subject' => 'Detail Trans_CAA', 'uses' => 'MasterCAA_Controller@detail']); //GEt CA BY ID after caa

                $router->get('/{search}/search', ['subject' => 'Search Trans_CA', 'uses' => 'MasterCAA_Controller@search']);

                // Approval By Team CAA
                $router->get('/{id}/approval', ['subject' => 'Get Approval List', 'uses' => 'Approval_Controller@index']);
                $router->get('/{id}/approval/{id_approval}', ['subject' => 'Detail Approval', 'uses' => 'Approval_Controller@show']);
                $router->post('/{id}/approval/{id_approval}', ['subject' => 'Make Approval', 'uses' => 'Approval_Controller@approve']);
            });

            $router->get('/team_caa', ['subject' => 'Get Komite_CAA', 'uses' => 'Approval_Controller@list_team']);  // Get List Team CAA
            $router->get('/team_caa/{id_team}', ['subject' => 'Detail Komite_CAA', 'uses' => 'Approval_Controller@detail_team']);  // Get List Team CAA
            $router->get('/report/approval/{id_trans_so}', ['subject' => 'Report Approval', 'uses' => 'Approval_Controller@report_approval']);
            // $router->group(['prefix' => '/approval'], function() use ($router){
            //     // $router->get('/{id}', 'Approval_Controller@show');
            // });

        });
    });

    // Menu
    $router->group(['prefix' => '/menu', 'namespace' => 'Menu'], function () use ($router) {

        // Menu Master
        $router->get('/', function () use ($router) {return redirect('/api/menu/master');});
        $router->get('/all/master', ['subject' => 'Display Master Menu with all status', 'uses' => 'MenuMasterController@all']); //Get all Data
        $router->group(['prefix' => '/master'], function() use ($router){
            $router->get('/', ['subject' => 'Get Master Menu with active status', 'uses' => 'MenuMasterController@index']); //Get list Data When Flg Aktif == 1 ('true')
            $router->post('/', ['subject' => 'Create Master Menu', 'uses' => 'MenuMasterController@store']); // Create Data
            $router->get('/{IdOrSlug}', ['subject' => 'Display Master Menu', 'uses' => 'MenuMasterController@show']);
            $router->put('/{IdOrSlug}', ['subject' => 'Update Master Menu', 'uses' => 'MenuMasterController@edit']);
            $router->delete('{IdOrSlug}', ['subject' => 'Delete Master Menu', 'uses' => 'MenuMasterController@delete']); // Delete Data based on slug (URL)
            $router->get('/{search}/search', ['subject' => 'Search Master Menu', 'uses' => 'MenuMasterController@search']);
        });

        // Menu Akses
        $router->get('/all/akses', ['subject' => 'Display Access Menu with all status', 'uses' => 'MenuAccessController@all']); // Get All Data
        $router->group(['prefix' => '/akses'], function() use ($router){
            $router->get('/', ['subject' => 'Get Access Menu with active status', 'uses' => 'MenuAccessController@index']); // Get List Data When Flg Aktif == 1 ('true')
            $router->post('/', ['subject' => 'Create Access Menu', 'uses' => 'MenuAccessController@store']); // Insert Data
            $router->get('/{id}', ['subject' => 'Detail Access Menu', 'uses' => 'MenuAccessController@show']); // Get Data based on Id User
            $router->put('/{id}', ['subject' => 'Update Access Menu', 'uses' => 'MenuAccessController@update']); // Update Data based on Id User
            $router->delete('/{id}', ['subject' => 'Delete Access Menu', 'uses' => 'MenuAccessController@delete']); // Delete Data based on Id User
        });

        // Sub Menu
        $router->get('/all/sub', ['subject' => 'Display Sub Menu with all status', 'uses' => 'MenuSubController@all']);
        $router->group(['prefix' => '/sub'], function() use ($router){
            $router->get('/', ['subject' => 'Get Sub Menu with active status', 'uses' => 'MenuSubController@index']);
            $router->post('/', ['subject' => 'Create Sub Menu', 'uses' => 'MenuSubController@store']);
            $router->get('/{IdOrSlug}', ['display' => 'Detail Sub Menu', 'uses' => 'MenuSubController@show']);
            $router->put('/{IdOrSlug}', ['subject' => 'Update Sub Menu', 'uses' => 'MenuSubController@edit']);
            $router->delete('/{IdOrSlug}', ['subject' => 'Delete Sub Menu', 'uses' => 'MenuSubController@delete']); // Delete Data based on slug(URL)
            $router->get('/{search}/search', ['subject' => 'Search Sub Menu', 'uses' => 'MenuSubController@search']);
        });

    });

    // Single Data When Transactioning From SO to CAA, etc
    $router->group(['namespace' => 'Pengajuan\Tunggal'], function() use ($router){

        // Fasilitas Pinjaman
        $router->group(['prefix' => '/faspin'], function() use ($router) {
            $router->get('/{id}', ['subject' => 'Detail fasilitas_pinjaman', 'uses' => 'FasPinController@show']);
            $router->post('/{id}', ['subject' => 'Update fasilitas_pinjaman', 'uses' => 'FasPinController@update']);
        });

        // Calon Debitur
        $router->group(['prefix' => '/debitur'], function() use ($router) {
            $router->get('/{id}', ['subject' => 'Detail calon_debitur', 'uses' => 'DebiturController@show']);
            $router->post('/{id}', ['subject' => 'Update calon_debitur', 'uses' => 'DebiturController@update']);
        });

        // Pasangan
        $router->group(['prefix' => '/pasangan'], function() use ($router) {
            $router->get('/{id}', ['subject' => 'Detail pasangan calon_debitur', 'uses' => 'PasanganController@show']);
            $router->post('/{id}', ['subject' => 'Update pasangan calon_debitur', 'uses' => 'PasanganController@update']);
        });

        // Penjamin
        $router->group(['prefix' => '/penjamin'], function() use ($router) {
            $router->get('/{id}', ['subject' => 'Detail penjamin', 'uses' => 'PenjaminController@show']);
            $router->post('/{id}', ['subject' => 'Update penjamin', 'uses' => 'PenjaminController@update']);
        });

        // Agunan
        $router->group(['prefix' => '/agunan'], function() use ($router) {
            // Agunan Tabah / Sertifikat
            $router->group(['prefix' => '/tanah'], function() use ($router) {
                $router->get('/{id}', ['subject' => 'Detail agunan_tanah', 'uses' => 'TanahController@show']);
                $router->post('/{id}', ['subject' => 'Update agunan_tanah', 'uses' => 'TanahController@update']);
            });

            // Agunan Kendaraan
            $router->group(['prefix' => '/kendaraan'], function() use ($router) {
                $router->get('/{id}', ['subject' => 'Detail agunan_kendaraan', 'uses' => 'KendaraanController@show']);
                $router->post('/{id}', ['subject' => 'Update agunan_kendaraan', 'uses' => 'KendaraanController@update']);
            });
        });

        // Pemeriksaan Agunan
        $router->group(['prefix' => '/periksa'], function() use ($router) {
            // Pemeriksaaan Agunan Tabah / Sertifikat
            $router->group(['prefix' => '/tanah'], function() use ($router) {
                $router->get('/{id}', ['subject' => 'Detail pemeriksaan_agunan_tanah', 'uses' => 'PemeriksaanTanahController@show']);
                $router->post('/{id}', ['subject' => 'Update pemeriksaan_agunan_tanah', 'uses' => 'PemeriksaanTanahController@update']);
            });

            // Pemeriksaaan Agunan Kendaraan
            $router->group(['prefix' => '/kendaraan'], function() use ($router) {
                $router->get('/{id}', ['subject' => 'Detail pemeriksaan_agunan_kendaraan', 'uses' => 'PemeriksaanKendaraanController@show']);
                $router->post('/{id}', ['subject' => 'Update pemeriksaan_agunan_kendaraan', 'uses' => 'PemeriksaanKendaraanController@update']);
            });
        });

        // Kapasitas Bulanan
        $router->group(['prefix' => '/kap_bul'], function() use ($router) {
            $router->get('/{id}', ['subject' => 'Get kapasitas_bulanan', 'uses' => 'KapBulController@show']);
            $router->post('/{id}', ['subject' => 'Update kapasitas_bulanan', 'uses' => 'KapBulController@update']);
        });

        // PENDAPATAN USAHA CADEBT
        $router->group(['prefix' => '/usaha_cadebt'], function() use ($router) {
            $router->get('/{id}', ['subject' => 'Get pendapatan_calon_debitur ', 'uses' => 'UsahaCadebtController@show']);
            $router->post('/{id}', ['subject' => 'Update pendapatan_calon_debitur', 'uses' => 'UsahaCadebtController@update']);
        });

    });
});
