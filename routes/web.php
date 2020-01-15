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

    $router->get('/area_cabang/filter', 'Master\AreaKantor\CabangController@get_cabang');

    $router->get('/provinsi', 'Wilayah\ProvinsiController@index');
    $router->get('/all/provinsi', 'Wilayah\ProvinsiController@all');
    $router->post('/provinsi', 'Wilayah\ProvinsiController@store');
    // $router->get('/provinsi/search/{search}', 'Wilayah\ProvinsiController@search'); // search Provinsi to Mitra
    $router->get('/provinsi/{IdOrName}', 'Wilayah\ProvinsiController@show');
    $router->put('/provinsi/{id}', 'Wilayah\ProvinsiController@update');
    $router->delete('/provinsi/{id}', 'Wilayah\ProvinsiController@delete');

    $router->get('/kabupaten', 'Wilayah\KabupatenController@index');
    $router->get('/all/kabupaten', 'Wilayah\KabupatenController@all');
    $router->post('/kabupaten', 'Wilayah\KabupatenController@store');
    $router->get('/kabupaten/{IdOrName}', 'Wilayah\KabupatenController@show');
    $router->put('/kabupaten/{id}', 'Wilayah\KabupatenController@update');
    $router->delete('/kabupaten/{id}', 'Wilayah\KabupatenController@delete');
    $router->get('/provinsi/{id}/kabupaten', 'Wilayah\KabupatenController@sector'); // Get Data Kabupaten By Id Provinsi

    $router->get('/kecamatan', 'Wilayah\KecamatanController@index');
    $router->get('/all/kecamatan', 'Wilayah\KecamatanController@all');
    $router->post('/kecamatan', 'Wilayah\KecamatanController@store');
    $router->get('/kecamatan/{IdOrName}', 'Wilayah\KecamatanController@show');
    $router->put('/kecamatan/{id}', 'Wilayah\KecamatanController@update');
    $router->delete('/kecamatan/{id}', 'Wilayah\KecamatanController@delete');
    $router->get('/kabupaten/{id}/kecamatan', 'Wilayah\KecamatanController@sector'); // Get Data Kecamatan By Id Kabupaten

    $router->get('/kelurahan', 'Wilayah\KelurahanController@index');
    $router->get('/all/kelurahan', 'Wilayah\KelurahanController@all');
    $router->post('/kelurahan', 'Wilayah\KelurahanController@store');
    $router->get('/kelurahan/{IdOrName}', 'Wilayah\KelurahanController@show');
    $router->put('/kelurahan/{id}', 'Wilayah\KelurahanController@update');
    $router->delete('/kelurahan/{id}', 'Wilayah\KelurahanController@delete');
    $router->get('/kecamatan/{id}/kelurahan', 'Wilayah\KelurahanController@sector'); // Get Data Kelurahan By Id Kecamatan
});

$router->post('/login', 'AuthController@login'); // Login All Level

$router->put('/api/user/reset_password', 'UserController@resetPassword'); //Reset Password

$router->group(['middleware' => 'jwt.auth'], function () use ($router) {
    //For Non User (Debitur)
    $router->group(['prefix' => '/api'], function () use ($router) {

        $router->get('/logs', 'LogsController@index'); //Log History All
        $router->get('/logs/{id}', 'LogsController@detail'); //Log History By ID
        $router->get('/logs/limit/{limit}', 'LogsController@limit'); //Log History Limit
        $router->get('/logs/search/{search}', 'LogsController@search'); //Log History Search

        $router->get('/users', 'UserController@getUsers');
        $router->get('/users/{IdOrSearch}', 'UserController@IdOrSearch');
        $router->get('/user', 'UserController@index');
        $router->put('/user/change_password', 'UserController@changePassword');

        $router->get('/oto', 'FlagAuthorController@otoIndex'); // Otorisasi
        $router->get('/oto/{limit}/limit', 'FlagAuthorController@otoLimit'); // Otorisasi
        $router->get('/oto/{id}', 'FlagAuthorController@otoShow');
        $router->put('/oto/{id}', 'FlagAuthorController@otoUpdate');
        $router->get('/log_oto', 'FlagAuthorController@otoH');
        $router->get('/log_oto/{year}', 'FlagAuthorController@otoHY');
        $router->get('/log_oto/{year}/{month}', 'FlagAuthorController@otoHYM');
        $router->get('/count_oto', 'FlagAuthorController@countOto');
        $router->put('/oto/{id}/reject', 'FlagAuthorController@rejectOto');

        $router->get('/apro', 'FlagAuthorController@aproIndex'); // Approval
        $router->get('/apro/{limit}/limit', 'FlagAuthorController@aproLimit'); // Approval
        $router->get('/apro/{id}', 'FlagAuthorController@aproShow');
        $router->put('/apro/{id}', 'FlagAuthorController@aproUpdate');
        $router->get('/log_apro', 'FlagAuthorController@aproH');
        $router->get('/log_apro/{year}', 'FlagAuthorController@aproHY');
        $router->get('/log_apro/{year}/{month}', 'FlagAuthorController@aproHYM');
        $router->get('/count_apro', 'FlagAuthorController@countApro');
        $router->put('/apro/{id}/reject', 'FlagAuthorController@rejectApro');

        $router->post('/oto/all/reset', 'FlagAuthorController@otoReset'); // Reset Otorisasi
        $router->post('/apro/all/reset', 'FlagAuthorController@aproReset'); // Reset Otorisasi

        $router->group(['prefix' => '/master'], function () use ($router) {
            $router->get('/asal_data', 'Master\AreaKantor\AsalDataController@index');
            $router->post('/asal_data', 'Master\AreaKantor\AsalDataController@store');
            $router->get('/asal_data/{id}', 'Master\AreaKantor\AsalDataController@show');
            $router->put('/asal_data/{id}', 'Master\AreaKantor\AsalDataController@update');
            $router->delete('/asal_data/{id}', 'Master\AreaKantor\AsalDataController@delete');

            $router->get('/mitra', 'Master\Bisnis\MitraController@index');
            $router->get('/mitra/{kode_mitra}', 'Master\Bisnis\MitraController@show');

            //Area Kantor
            $router->get('/area_kerja', 'Master\AreaKantor\AreaController@index');
            $router->post('/area_kerja', 'Master\AreaKantor\AreaController@store');
            $router->get('/area_kerja/{id}', 'Master\AreaKantor\AreaController@show');
            $router->put('/area_kerja/{id}', 'Master\AreaKantor\AreaController@update');
            $router->delete('/area_kerja/{id}', 'Master\AreaKantor\AreaController@delete');

            //Cabang Kantor
            $router->get('/area_cabang', 'Master\AreaKantor\CabangController@index');
            $router->post('/area_cabang', 'Master\AreaKantor\CabangController@store');
            $router->get('/area_cabang/{id}', 'Master\AreaKantor\CabangController@show');
            $router->put('/area_cabang/{id}', 'Master\AreaKantor\CabangController@update');
            $router->delete('/area_cabang/{id}', 'Master\AreaKantor\CabangController@delete');

            $router->get('/kode_kantor', 'Master\AreaKantor\KodeKantorController@index');

            //Kas Kantor
            $router->get('/area_pic', 'Master\AreaKantor\AreaPICController@index');
            $router->post('/area_pic', 'Master\AreaKantor\AreaPICController@store');
            $router->get('/area_pic/{id}', 'Master\AreaKantor\AreaPICController@show');
            $router->put('/area_pic/{id}', 'Master\AreaKantor\AreaPICController@update');
            $router->delete('/area_pic/{id}', 'Master\AreaKantor\AreaPICController@delete');

            //PIC
            $router->get('/pic', 'Master\AreaKantor\PICController@index');
            $router->get('/team_caa', 'Master\AreaKantor\PICController@teamCAA');
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


            $router->get('/das', 'Pengajuan\DASController@index'); //Cek HM
            $router->get('/das/{id}', 'Pengajuan\DASController@show'); //Cek HM
            $router->post('/das/{id}', 'Pengajuan\DASController@update'); //Cek HM

            $router->get('/hm', 'Pengajuan\HMController@index'); //Cek HM
            $router->get('/hm/{id}', 'Pengajuan\HMController@show'); //Cek HM
            $router->put('/hm/{id}', 'Pengajuan\HMController@update'); //Cek HM


            $router->group(['namespace' => 'Transaksi'], function() use ($router) {

                $router->group(['prefix' => '/mcc',], function() use ($router) {
                    $router->post('/', 'MasterCC_Controller@store'); // Memorandum Credit Checking
                    $router->get('/', 'MasterCC_Controller@index');
                    $router->get('/{id}', 'MasterCC_Controller@show');
                    $router->post('/{id}', 'MasterCC_Controller@update'); // Update MCC
                });

                $router->group(['prefix' => '/mao',], function() use ($router) {
                    $router->get('/', 'MasterAO_Controller@index'); // All Memorandum Account Officer
                    $router->get('/{id}', 'MasterAO_Controller@show'); //GEt MAO BY ID
                    $router->post('/{id}', 'MasterAO_Controller@update'); //Update MAO BY ID
                });

                $router->group(['prefix' => '/mca',], function() use ($router) {
                    $router->get('/', 'MasterCA_Controller@index'); // All Memorandum Credit Analyst
                    $router->get('/{id}', 'MasterCA_Controller@show'); //GEt CA BY ID
                    $router->post('/{id}', 'MasterCA_Controller@update'); //Update CA BY ID
                });

                $router->group(['prefix' => '/mcaa',], function() use ($router) {
                    $router->get('/', 'MasterCAA_Controller@index'); // All Memorandum Credit Analyst
                    $router->get('/{id}', 'MasterCAA_Controller@show'); //GEt CA BY ID
                    $router->post('/{id}', 'MasterCAA_Controller@update'); //Update CA BY ID
                });
            });
        });

        // Menu
        $router->group(['prefix' => '/menu'], function () use ($router) {
            $router->get('/akses', 'Menu\MenuAccessController@index'); // Get All Data
            $router->post('/akses', 'Menu\MenuAccessController@store'); // Insert Data
            $router->get('/akses/{id}', 'Menu\MenuAccessController@show'); // Get Data based on Id User
            $router->put('/akses/{id}', 'Menu\MenuAccessController@update'); // Update Data based on Id User
            $router->delete('/akses/{id}', 'Menu\MenuAccessController@delete'); // Delete Data based on Id User

            $router->get('/master', ['as' => 'menu', 'uses' => 'Menu\MenuMasterController@index']); //Get Data
            $router->post('/master', 'Menu\MenuMasterController@store'); // Create Data
            $router->get('/', function () use ($router) {return redirect('/api/menu/master');});
            $router->get('/master/{IdOrSlug}', ['as' => 'mastermenu', 'uses' => 'Menu\MenuMasterController@show']);
            $router->put('/master/{IdOrSlug}', 'Menu\MenuMasterController@edit');
            $router->delete('/master/{IdOrSlug}', 'Menu\MenuMasterController@delete'); // Delete Data based on slug (URL)

            $router->get('/sub', 'Menu\MenuSubController@index');
            $router->post('/sub', 'Menu\MenuSubController@store');
            $router->get('/sub/{IdOrSlug}', ['as' => 'submenu', 'uses' => 'Menu\MenuSubController@show']);
            $router->put('/sub/{IdOrSlug}', 'Menu\MenuSubController@edit');
            $router->delete('/sub/{IdOrSlug}', 'Menu\MenuSubController@delete'); // Delete Data based on slug(URL)
        });

        // Fasilitas Pinjaman
        $router->group(['prefix' => '/faspin', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
            $router->get('/{id}', 'FasPinController@show');
            $router->post('/{id}', 'FasPinController@update');
        });

        // Calon Debitur
        $router->group(['prefix' => '/debitur', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
            $router->get('/{id}', 'DebiturController@show');
            $router->post('/{id}', 'DebiturController@update');
        });

        // Pasangan
        $router->group(['prefix' => '/pasangan', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
            $router->get('/{id}', 'PasanganController@show');
            $router->post('/{id}', 'PasanganController@update');
        });

        // Penjamin
        $router->group(['prefix' => '/penjamin', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
            $router->get('/{id}', 'PenjaminController@show');
            $router->post('/{id}', 'PenjaminController@update');
        });

        // Agunan
        $router->group(['prefix' => '/agunan', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
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
        $router->group(['prefix' => '/periksa', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
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
        $router->group(['prefix' => '/kap_bul', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
            $router->get('/{id}', 'KapBulController@show');
            $router->post('/{id}', 'KapBulController@update');
        });

        // PENDAPATAN USAHA CADEBT
        $router->group(['prefix' => '/usaha_cadebt', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {
            $router->get('/{id}', 'UsahaCadebtController@show');
            $router->post('/{id}', 'UsahaCadebtController@update');
        });

        // Rekomendasi AO
        // $router->group(['prefix' => '/rekom_ao', 'namespace' => 'Pengajuan\Tunggal'], function() use ($router) {});
    });
});
