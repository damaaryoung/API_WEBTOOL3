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

$router->post('/up_caa', 'ImgController@uploadCAA');

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

$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    //For Non User (Debitur)
    $router->group(['prefix' => '/api'], function () use ($router) {

        // $router->get('/{id_tr_so}/approval', 'Wilayah\ProvinsiController@app');

        // Logs (History)
        $router->group(['prefix' => '/logs'], function () use ($router){
            $router->get('/', 'LogsController@index'); //Log History All
            $router->get('/{id}', 'LogsController@detail'); //Log History By ID
            $router->get('/limit/{limit}', 'LogsController@limit'); //Log History Limit
            $router->get('/search/{search}', 'LogsController@search'); //Log History Search
        });

        // Users And User
        $router->get('/users', 'UserController@getUsers');
        $router->get('/users/{IdOrSearch}', 'UserController@IdOrSearch');
        $router->get('/user', 'UserController@index');
        $router->put('/user/change_password', 'UserController@changePassword');

        // Otorisasi
        $router->get('/oto', 'FlagAuthorController@otoIndex'); // Otorisasi
        $router->get('/oto/{limit}/limit', 'FlagAuthorController@otoLimit'); // Otorisasi
        $router->get('/oto/{id}', 'FlagAuthorController@otoShow');
        $router->put('/oto/{id}', 'FlagAuthorController@otoUpdate');
        $router->put('/oto/{id}/reject', 'FlagAuthorController@rejectOto');

        // Log Otorisasi
        $router->get('/log_oto', 'FlagAuthorController@otoH');
        $router->get('/log_oto/{year}', 'FlagAuthorController@otoHY');
        $router->get('/log_oto/{year}/{month}', 'FlagAuthorController@otoHYM');

        // Count Otorisasi
        $router->get('/count_oto', 'FlagAuthorController@countOto');

        // Approval
        $router->get('/apro', 'FlagAuthorController@aproIndex'); // Approval
        $router->get('/apro/{limit}/limit', 'FlagAuthorController@aproLimit'); // Approval
        $router->get('/apro/{id}', 'FlagAuthorController@aproShow');
        $router->put('/apro/{id}', 'FlagAuthorController@aproUpdate');
        $router->put('/apro/{id}/reject', 'FlagAuthorController@rejectApro');

        // Log Approval
        $router->get('/log_apro', 'FlagAuthorController@aproH');
        $router->get('/log_apro/{year}', 'FlagAuthorController@aproHY');
        $router->get('/log_apro/{year}/{month}', 'FlagAuthorController@aproHYM');

        // Count Otorisasi
        $router->get('/count_apro', 'FlagAuthorController@countApro');

        // Reset Otorisasi And Approval
        $router->post('/oto/all/reset', 'FlagAuthorController@otoReset'); // Reset Otorisasi
        $router->post('/apro/all/reset', 'FlagAuthorController@aproReset'); // Reset Otorisasi


        $router->group(['prefix' => '/master'], function () use ($router) {

            $router->group(['namespace' => 'Master\Bisnis'], function () use ($router){
                // Mitra Bisnis
                $router->get('/mitra', 'MitraController@index');
                $router->get('/mitra/{kode_mitra}', 'MitraController@show');
                $router->get('/mitra/{search}/search', 'MitraController@search');
            });

            $router->group(['namespace' => 'Master\AreaKantor'], function () use ($router){

                // Asal Data
                $router->get('/all/asal_data', 'AsalDataController@all');
                $router->group(['prefix' => '/asal_data'], function () use ($router){
                    $router->get('/', 'AsalDataController@index');
                    $router->post('/', 'AsalDataController@store');
                    $router->get('/{id}', 'AsalDataController@show');
                    $router->put('/{id}', 'AsalDataController@update');
                    $router->delete('/{id}', 'AsalDataController@delete');
                    $router->get('/{search}/search', 'AsalDataController@search');
                });

                //Area Kantor
                $router->get('/all/area_kerja', 'AreaController@all');
                $router->group(['prefix' => '/area_kerja'], function () use ($router){
                    $router->get('/', 'AreaController@index');
                    $router->post('/', 'AreaController@store');
                    $router->get('/{id}', 'AreaController@show');
                    $router->put('/{id}', 'AreaController@update');
                    $router->delete('/{id}', 'AreaController@delete');
                    $router->get('/{search}/search', 'AreaController@search');
                });

                //Cabang Kantor
                $router->get('/all/area_cabang', 'CabangController@all');
                $router->group(['prefix' => '/area_cabang'], function () use ($router){
                    $router->get('/', 'CabangController@index');
                    $router->post('/', 'CabangController@store');
                    $router->get('/{id}', 'CabangController@show');
                    $router->put('/{id}', 'CabangController@update');
                    $router->delete('/{id}', 'CabangController@delete');
                    $router->get('/{search}/search', 'CabangController@search');
                });

                // Area PIC
                $router->get('/all/area_pic', 'AreaPICController@all');
                $router->group(['prefix' => '/area_pic'], function () use ($router){
                    $router->get('/', 'AreaPICController@index');
                    $router->post('/', 'AreaPICController@store');
                    $router->get('/{id}', 'AreaPICController@show');
                    $router->put('/{id}', 'AreaPICController@update');
                    $router->delete('/{id}', 'AreaPICController@delete');
                    $router->get('/{search}/search', 'AreaPICController@search');
                });

                // Daftar PIC
                $router->get('/all/pic', 'PICController@all');
                $router->group(['prefix' => '/pic'], function () use ($router){
                    $router->get('/', 'PICController@index');
                    $router->post('/', 'PICController@store');
                    $router->get('/{id}', 'PICController@show');
                    $router->put('/{id}', 'PICController@update');
                    $router->delete('/{id}', 'PICController@delete');
                    $router->get('/{search}/search', 'PICController@search');

                });

                //Jenis PIC
                $router->group(['prefix' => '/jenis_pic'], function () use ($router){
                    $router->get('/', 'JPICController@index');
                    $router->post('/', 'JPICController@store');
                    $router->get('/{id}', 'JPICController@show');
                    $router->put('/{id}', 'JPICController@update');
                    $router->delete('/{id}', 'JPICController@delete');
                    $router->get('/{search}/search', 'JPICController@search');
                });

                // Kode Kantor from DPM_ONLINE (user)
                $router->get('/kode_kantor', 'KodeKantorController@index');

            });

            // Transaksi From SO -> CAA, etc
            $router->get('/das', 'Pengajuan\DASController@index'); //Cek HM
            $router->get('/das/{id}', 'Pengajuan\DASController@show'); //Cek HM
            $router->post('/das/{id}', 'Pengajuan\DASController@update'); //Cek HM
            $router->get('/das/{search}/search', 'Pengajuan\DASController@search');

            $router->get('/hm', 'Pengajuan\HMController@index'); //Cek HM
            $router->get('/hm/{id}', 'Pengajuan\HMController@show'); //Cek HM
            $router->put('/hm/{id}', 'Pengajuan\HMController@update'); //Cek HM
            $router->get('/hm/{search}/search', 'Pengajuan\HMController@search');

            // Transaksi From SO -> CAA, etc
            $router->group(['namespace' => 'Transaksi'], function() use ($router) {

                // Trans SO
                $router->group(['prefix' => '/mcc',], function() use ($router) {
                    $router->post('/', 'MasterCC_Controller@store'); // Memorandum Credit Checking
                    $router->get('/', 'MasterCC_Controller@index');
                    $router->get('/{id}', 'MasterCC_Controller@show');
                    $router->post('/{id}', 'MasterCC_Controller@update'); // Update MCC
                    $router->get('/{search}/search', 'MasterCC_Controller@search');
                });

                // Trans AO
                $router->group(['prefix' => '/mao'], function() use ($router) {
                    $router->get('/', 'MasterAO_Controller@index'); // All Memorandum Account Officer
                    $router->get('/{id}', 'MasterAO_Controller@show'); //GEt MAO BY ID
                    $router->post('/{id}', 'MasterAO_Controller@update'); //Update MAO BY ID
                    $router->get('/{search}/search', 'MasterAO_Controller@search');
                });

                // Trans CA
                $router->group(['prefix' => '/mca'], function() use ($router) {
                    $router->get('/', 'MasterCA_Controller@index'); // All Memorandum Credit Analyst
                    $router->get('/{id}', 'MasterCA_Controller@show'); //GEt CA BY ID
                    $router->post('/{id}', 'MasterCA_Controller@update'); //Update CA BY ID
                    $router->get('/{search}/search', 'MasterCA_Controller@search');
                });


                // Trans CAA
                $router->group(['prefix' => '/mcaa'], function() use ($router) {
                    $router->get('/{search}/search', 'MasterCAA_Controller@search');
                    // Tahap 1
                    $router->get('/', 'MasterCAA_Controller@index'); // All Memorandum Credit Analyst
                    // $router->get('/{id}', 'MasterCAA_Controller@show'); //GEt CA BY ID
                    $router->get('/{idOrString}', 'MasterCAA_Controller@idOrString'); //GEt CA BY ID Or to Route
                    $router->post('/{id}', 'MasterCAA_Controller@update'); //Update CA BY ID

                    // Tahap 2 - Team CAA
                    $router->get('/{id}/detail', 'MasterCAA_Controller@detail'); //GEt CA BY ID after caa

                    // Approval By Team CAA
                    $router->get('/{id}/approval', 'Approval_Controller@index');
                    $router->post('/{id}/approval/{id_approval}', 'Approval_Controller@approve');
                });

                $router->get('/team_caa', 'Approval_Controller@list_team');  // Get List Team CAA
                $router->get('/report/approval/{id_trans_so}', 'Approval_Controller@report_approval');
                // $router->group(['prefix' => '/approval'], function() use ($router){
                //     // $router->get('/{id}', 'Approval_Controller@show');
                // });

            });
        });

        // Menu
        $router->group(['prefix' => '/menu', 'namespace' => 'Menu'], function () use ($router) {

            // Menu Master
            $router->get('/', function () use ($router) {return redirect('/api/menu/master');});
            $router->get('/all/master', 'MenuMasterController@all'); //Get all Data
            $router->group(['prefix' => '/master'], function() use ($router){
                $router->get('/', ['as' => 'menu', 'uses' => 'MenuMasterController@index']); //Get list Data When Flg Aktif == 1 ('true')
                $router->post('/', 'MenuMasterController@store'); // Create Data
                $router->get('/{IdOrSlug}', ['as' => 'mastermenu', 'uses' => 'MenuMasterController@show']);
                $router->put('/{IdOrSlug}', 'MenuMasterController@edit');
                $router->delete('{IdOrSlug}', 'MenuMasterController@delete'); // Delete Data based on slug (URL)
                $router->get('/{search}/search', 'MenuMasterController@search');
            });

            // Menu Akses
            $router->get('/all/akses', 'MenuAccessController@all'); // Get All Data
            $router->group(['prefix' => '/akses'], function() use ($router){
                $router->get('/', 'MenuAccessController@index'); // Get List Data When Flg Aktif == 1 ('true')
                $router->post('/', 'MenuAccessController@store'); // Insert Data
                $router->get('/{id}', 'MenuAccessController@show'); // Get Data based on Id User
                $router->put('/{id}', 'MenuAccessController@update'); // Update Data based on Id User
                $router->delete('/{id}', 'MenuAccessController@delete'); // Delete Data based on Id User
            });

            // Sub Menu
            $router->get('/all/sub', 'MenuSubController@all');
            $router->group(['prefix' => '/sub'], function() use ($router){
                $router->get('/', 'MenuSubController@index');
                $router->post('/', 'MenuSubController@store');
                $router->get('/{IdOrSlug}', ['as' => 'submenu', 'uses' => 'MenuSubController@show']);
                $router->put('/{IdOrSlug}', 'MenuSubController@edit');
                $router->delete('/{IdOrSlug}', 'MenuSubController@delete'); // Delete Data based on slug(URL)
                $router->get('/{search}/search', 'MenuSubController@search');
            });

        });

        // Single Data When Transactioning From SO to CAA, etc
        $router->group(['namespace' => 'Pengajuan\Tunggal'], function() use ($router){

            // Fasilitas Pinjaman
            $router->group(['prefix' => '/faspin'], function() use ($router) {
                $router->get('/{id}', 'FasPinController@show');
                $router->post('/{id}', 'FasPinController@update');
            });

            // Calon Debitur
            $router->group(['prefix' => '/debitur'], function() use ($router) {
                $router->get('/{id}', 'DebiturController@show');
                $router->post('/{id}', 'DebiturController@update');
            });

            // Pasangan
            $router->group(['prefix' => '/pasangan'], function() use ($router) {
                $router->get('/{id}', 'PasanganController@show');
                $router->post('/{id}', 'PasanganController@update');
            });

            // Penjamin
            $router->group(['prefix' => '/penjamin'], function() use ($router) {
                $router->get('/{id}', 'PenjaminController@show');
                $router->post('/{id}', 'PenjaminController@update');
            });

            // Agunan
            $router->group(['prefix' => '/agunan'], function() use ($router) {
                // Agunan Tabah / Sertifikat
                $router->group(['prefix' => '/tanah'], function() use ($router) {
                    $router->get('/{id}', 'TanahController@show');
                    $router->post('/{id}', 'TanahController@update');
                });

                // Agunan Kendaraan
                $router->group(['prefix' => '/kendaraan'], function() use ($router) {
                    $router->get('/{id}', 'KendaraanController@show');
                    $router->post('/{id}', 'KendaraanController@update');
                });
            });

            // Pemeriksaan Agunan
            $router->group(['prefix' => '/periksa'], function() use ($router) {
                // Pemeriksaaan Agunan Tabah / Sertifikat
                $router->group(['prefix' => '/tanah'], function() use ($router) {
                    $router->get('/{id}', 'PemeriksaanTanahController@show');
                    $router->post('/{id}', 'PemeriksaanTanahController@update');
                });

                // Pemeriksaaan Agunan Kendaraan
                $router->group(['prefix' => '/kendaraan'], function() use ($router) {
                    $router->get('/{id}', 'PemeriksaanKendaraanController@show');
                    $router->post('/{id}', 'PemeriksaanKendaraanController@update');
                });
            });

            // Kapasitas Bulanan
            $router->group(['prefix' => '/kap_bul'], function() use ($router) {
                $router->get('/{id}', 'KapBulController@show');
                $router->post('/{id}', 'KapBulController@update');
            });

            // PENDAPATAN USAHA CADEBT
            $router->group(['prefix' => '/usaha_cadebt'], function() use ($router) {
                $router->get('/{id}', 'UsahaCadebtController@show');
                $router->post('/{id}', 'UsahaCadebtController@update');
            });

        });

    });
});
