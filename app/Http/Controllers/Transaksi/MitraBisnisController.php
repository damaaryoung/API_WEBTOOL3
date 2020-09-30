<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MitraBisnisController extends BaseController
{
    public function index(Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'faspin')->orderBy('created_at', 'desc');
        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        // dd($query->get());

        if ($query->get() === '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data di SO masih kosong"
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {
            if ($val->status_das == 1) {
                $status_das = 'complete';
            } elseif ($val->status_das == 2) {
                $status_das = 'not complete';
            } else {
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            } elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            } else {
                $status_hm = 'waiting';
            }

            $data[$key] = [
                'id'              => $val->id == null ? null : (int) $val->id,
                'nomor_so'        => $val->nomor_so,
                'nama_so'         => $val->nama_so,
                'pic'             => $val->pic['nama'],
                'area'            => $val->area['nama'],
                'cabang'          => $val->cabang['nama'],
                'asal_data'       => $val->asaldata['nama'],
                'nama_marketing'  => $val->nama_marketing,
                'nama_calon_debt' => $val->debt['nama_lengkap'],
                'plafon'          => (int) $val->faspin['plafon'],
                'tenor'           => (int) $val->faspin['tenor'],
                'das' => [
                    'status'  => $status_das,
                    'catatan' => $val->catatan_das
                ],
                'hm'  => [
                    'status'  => $status_hm,
                    'catatan' => $val->catatan_hm
                ],
                'tgl_transaksi' => Carbon::parse($val->created_at)->format('d-m-Y H:m:s')
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $arr = array();
        $i=0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
          $i++;
        }   

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }   
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
          $i++;
        }  
          //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
       // dd($id_cabang);
        $scope     = $arrrr;

        $trans = TransSO::where('id', $id)->first();
        //  dd($trans->kode_mb);
        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')->where('id', $id);

        $vals = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $val  = $vals->first();

        if (!$val) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan id " . $id . " tidak ada di SO atau belum di rekomendasikan oleh bagian DAS dan HM"
            ], 404);
        }

        // $ao = TransAO::where('id_trans_so', $val->id)->first();

        $nama_anak = explode(",", $val->nama_anak);
        $tgl_anak  = explode(",", $val->tgl_lahir_anak);

        $anak = array();
        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = [
                'nama'      => $nama_anak[$i],
                'tgl_lahir' => $tgl_anak[$i]
            ];
        }

        $pen = Penjamin::whereIn('id', explode(",", $val->id_penjamin))->get();

        if ($val->status_das == 1) {
            $status_das = 'complete';
        } elseif ($val->status_das == 2) {
            $status_das = 'not complete';
        } else {
            $status_das = 'waiting';
        }

        if ($val->status_hm == 1) {
            $status_hm = 'complete';
        } elseif ($val->status_hm == 2) {
            $status_hm = 'not complete';
        } else {
            $status_hm = 'waiting';
        }


        if ($val->ao['status_ao'] == 1) {
            $status_ao = 'complete';
        } elseif ($val->ao['status_ao'] == 2) {
            $status_ao = 'not complete';
        } else {
            $status_ao = 'waiting';
        }


        if ($val->ca['status_ca'] == 1) {
            $status_ca = 'complete';
        } elseif ($val->ca['status_ca'] == 2) {
            $status_ca = 'not complete';
        } else {
            $status_ca = 'waiting';
        }


        if ($val->caa['status_caa'] == 1) {
            $status_caa = 'complete';
        } elseif ($val->caa['status_caa'] == 2) {
            $status_caa = 'not complete';
        } else {
            $status_caa = 'waiting';
        }

        $data = [
            'id'          => $val->id == null ? null : (int) $val->id,
            'nomor_so'    => $val->nomor_so,
            'nama_so'     => $val->nama_so,
            'id_pic'      => $val->id_pic == null ? null : (int) $val->id_pic,
            'nama_pic'    => $val->pic['nama'],
            'area'   => [
                'id'      => $val->id_area == null ? null : (int) $val->id_area,
                'nama'    => $val->area['nama']
            ],
            'id_cabang'   => $val->id_cabang == null ? null : (int) $val->id_cabang,
            'nama_cabang' => $val->cabang['nama'],
            'kode_mb'   => $trans->kode_mb,
            'tracking'  => [
                'das' => $status_das,
                'hm'  => $status_hm,
                'ao'  => $status_ao,
                'ca'  => $status_ca,
                'caa' => $status_caa,
            ],
            'catatan_DAS' => $val->catatan_das,
            'catatan_DSSPV' => $val->catatan_hm,
            'asal_data' => [
                'id'   => $val->id_asal_data == null ? null : (int) $val->id_asal_data,
                'nama' => $val->asaldata['nama'],
            ],
            'nama_marketing'    => $val->nama_marketing,
            'fasilitas_pinjaman'  => [
                'id'   => $val->id_fasilitas_pinjaman == null ? null : (int) $val->id_fasilitas_pinjaman
            ],
            'calon_debitur'     => [
                'id'            => $val->id_calon_debitur == null ? null : (int) $val->id_calon_debitur,
                'nama_lengkap'  => $val->debt['nama_lengkap']
            ],

            'pasangan'    => [
                'id'      => $val->id_pasangan == null ? null : (int) $val->id_pasangan,
                'nama'    => $val->pas['nama_lengkap'],
            ],
            'penjamin'    => $pen,
            'flg_aktif'   => $val->flg_aktif,
            'tgl_transaksi' => $val->created_at
        ];

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(Request $request)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

        $mj = array();
        $i=0;
        foreach ($pic as $val) {
            $mj[] = $val['id_mj_pic'];
          $i++;
        }   

        $arr = array();
        $i=0;
        foreach ($pic as $val) {
            $arr[] = $val['id_area'];
          $i++;
        }   

        $arrr = array();
        foreach ($pic as $val) {
            $arrr[] = $val['id_cabang'];
          $i++;
        }   
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
          $i++;
        }  
          //  dd($arr);
        $id_area   = $arr;
        $id_cabang = $arrr;
       // dd($id_cabang);
        $scope     = $arrrr;

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'faspin')->orderBy('created_at', 'desc');
        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        $countTSO = TransSO::latest('id', 'nomor_so')->first();

        if ($countTSO === null) {
            $lastNumb = 1;
        } else {
            $no = $countTSO->nomor_so;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;

            // $no = $countTSO + 1;
        }
        //Data Transaksi SO
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        $JPIC   = JPIC::whereIn('id', $mj)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_so = $arrr[0] . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb; //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut

       // $kode_mitra = DB::connection('dpm')->table('kre_kode_group5')->select('kode_group5')->get();
        $trans_so = array(
            'nomor_so'       => $nomor_so,
            'user_id'        => $request->input('user_id'),
            'id_pic'         => $request->input('id_pic'),
            'id_area'        => $request->input('id_area'),
            'id_cabang'      =>$request->input('id_cabang'),
            'nama_so'        => $request->input('nama_so'),
            'id_asal_data'   => $request->input('id_asal_data'),
            'kode_mb'          =>   $request->input('kode_mb'),
            'nama_marketing' => $request->input('nama_marketing')
        );

        // Data Fasilitas Pinjaman
        $dataFasPin = array(
            // 'jenis_pinjaman'  => empty($requestuest->input('jenis_pinjaman')) ? null : $requestuest->input('jenis_pinjaman'),
            // 'tujuan_pinjaman' => empty($requestuest->input('tujuan_pinjaman')) ? null : $requestuest->input('tujuan_pinjaman'),
            'plafon'          => empty($request->input('plafon_pinjaman')) ? null : $request->input('plafon_pinjaman'),
            'tenor'           => empty($request->input('tenor_pinjaman')) ? null : $request->input('tenor_pinjaman')
        );

        $dateExpires   = strtotime($now); // time to integer
        $day_in_second = 60 * 60 * 24 * 30;

        $ktp        = $request->input('no_ktp');
        // $no_ktp_kk  = $request->input('no_ktp_kk');
        $no_ktp_pas = empty($request->input('no_ktp_pas')) ? null : $request->input('no_ktp_pas');
        // $no_ktp_pen = $request->input('no_ktp_pen');


        $check_ktp_dpm = DB::connection("web")->table("view_nasabah")->where("NO_ID", $ktp)->first();

        if ($check_ktp_dpm === null) {
            $NASABAH_ID = null;
        } else {
            $NASABAH_ID = $check_ktp_dpm->NASABAH_ID;
        }

        $check_ktp_web = Debitur::select('id', 'no_ktp', 'nama_lengkap', 'created_at')->where('no_ktp', $ktp)->first();

        if ($check_ktp_web !== null) {

            $created_at = strtotime($check_ktp_web->created_at);

            $compare_day_in_second = $dateExpires - $created_at;

            if ($compare_day_in_second <= $day_in_second) {
                return response()->json([
                    "code"    => 403,
                    "status"  => "Expired",
                    'message' => "Akun belum aktif kembali, belum ada 1 bulan yang lalu, tepatnya pada tanggal '" . Carbon::parse($check_ktp_web->created_at)->format("Y-m-d") . "' debitur dengan nama '{$check_ktp_web->nama_lengkap}' telah melakukan pengajuan"
                ], 403);
            } else {
                return response()->json([
                    "code"    => 200,
                    "status"  => "success",
                    "message" => "Akun telah ada di sistem, gunakan endpoint berikut apabila ingin menggunakan datanya",
                    "endpoint" => "/api/debitur/" . $check_ktp_web->id
                ], 200);
            }
        } else {
            $check_ktp_debt = Debitur::where('no_ktp', $ktp)->first();

            if (!empty($check_ktp_debt) && !empty($check_ktp_debt->no_ktp)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no ktp telah ada yang menggunakan'
                ], 422);
            }

            if (!empty($check_ktp_debt) && !empty($check_ktp_debt->no_ktp_kk)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no ktp di kk telah ada yang menggunakan'
                ], 422);
            }

            if (!empty($check_ktp_debt) && !empty($check_ktp_debt->no_npwp)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no npwp telah ada yang menggunakan'
                ], 422);
            }

            if (!empty($check_ktp_debt) && !empty($check_ktp_debt->no_telp)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no telp telah ada yang menggunakan'
                ], 422);
            }

            if (!empty($check_ktp_debt) && !empty($check_ktp_debt->no_hp)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no hp telah ada yang menggunakan'
                ], 422);
            }

            $check_ktp_pas = Pasangan::where('no_ktp', $no_ktp_pas)->first();

            if (!empty($check_ktp_pas) && !empty($check_ktp_pas->no_ktp)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no ktp pasangan telah ada yang menggunakan'
                ], 422);
            }

            if (!empty($check_ktp_pas) && !empty($check_ktp_pas->no_ktp_kk)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no ktp di kk pasangan telah ada yang menggunakan'
                ], 422);
            }

            if (!empty($check_ktp_pas) && !empty($check_ktp_pas->no_npwp)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no npwp pasangan telah ada yang menggunakan'
                ], 422);
            }

            if (!empty($check_ktp_debt) && !empty($check_ktp_debt->no_telp)) {
                return response()->json([
                    "code"    => 422,
                    "status"  => "not valid request",
                    "message" => 'no telp pasangan telah ada yang menggunakan'
                ], 422);
            }

            $lamp_dir  = 'public/' . $ktp;
            $path_debt = $lamp_dir . '/debitur';
            $path_pas  = $lamp_dir . '/pasangan';
            $path_penj = $lamp_dir . '/penjamin';

            if ($file = $request->file('lamp_ktp')) {
                $name       = 'ktp.';
                $check_file = 'null';

                $lamp_ktp = Helper::uploadImg($check_file, $file, $path_debt, $name);
            } else {
                $lamp_ktp = null;
            }

            if ($file = $request->file('lamp_kk')) {
                $name       = 'kk.';
                $check_file = 'null';

                $lamp_kk = Helper::uploadImg($check_file, $file, $path_debt, $name);
            } else {
                $lamp_kk = null;
            }

            if ($file = $request->file('lamp_sertifikat')) {
                $name       = 'sertifikat.';
                $check_file = 'null';

                $lamp_sertifikat = Helper::uploadImg($check_file, $file, $path_debt, $name);
            } else {
                $lamp_sertifikat = null;
            }

            if ($file = $request->file('lamp_pbb')) {
                $name       = 'pbb.';
                $check_file = 'null';

                $lamp_sttp_pbb = Helper::uploadImg($check_file, $file, $path_debt, $name);
            } else {
                $lamp_sttp_pbb = null;
            }

            if ($file = $request->file('lamp_imb')) {
                $name       = 'imb.';
                $check_file = 'null';

                $lamp_imb = Helper::uploadImg($check_file, $file, $path_debt, $name);
            } else {
                $lamp_imb = null;
            }

            if ($file = $request->file('foto_agunan_rumah')) {
                $name = 'foto_agunan_rumah.';
                $check_file = 'null';

                $foto_agunan_rumah = Helper::uploadImg($check_file, $file, $path_debt, $name);
            } else {
                $foto_agunan_rumah = null;
            }

            if ($file = $request->file('lamp_surat_cerai')) {
                $name       = 'lamp_surat_cerai.';
                $check_file = 'null';

                $lamp_surat_cerai = Helper::uploadImg($check_file, $file, $path_debt, $name);
            } else {
                $lamp_surat_cerai = null;
            }

            // Data Calon Debitur
            $dataDebitur = array(
                'nama_lengkap'          => $request->input('nama_lengkap'),
                // 'gelar_keagamaan'       => $request->input('gelar_keagamaan'),
                // 'gelar_pendidikan'      => $request->input('gelar_pendidikan'),
                'jenis_kelamin'         => strtoupper($request->input('jenis_kelamin')),
                'status_nikah'          => $request->input('status_nikah'),
                //'ibu_kandung'           => $request->input('ibu_kandung'),
                'no_ktp'                => $ktp,
                //'no_ktp_kk'             => $request->input('no_ktp_kk'),
                //'no_kk'                 => $request->input('no_kk'),
                'no_npwp'               => $request->input('no_npwp'),
                'tempat_lahir'          => $request->input('tempat_lahir'),
                'tgl_lahir'             => empty($request->input('tgl_lahir')) ? null : Carbon::parse($request->input('tgl_lahir'))->format('Y-m-d'),
                'agama'                 => strtoupper($request->input('agama')),
                'alamat_ktp'            => $alamat_ktp = $request->input('alamat_ktp'),
                //'rt_ktp'                => $rt_ktp = $request->input('rt_ktp'),
                //'rw_ktp'                => $rw_ktp = $request->input('rw_ktp'),
                'id_prov_ktp'           => $id_prov_ktp = $request->input('id_provinsi_ktp'),
                'id_kab_ktp'            => $id_kab_ktp = $request->input('id_kabupaten_ktp'),
                'id_kec_ktp'            => $id_kec_ktp = $request->input('id_kecamatan_ktp'),
                'id_kel_ktp'            => $id_kel_ktp = $request->input('id_kelurahan_ktp'),
                'no_hp'                 => $request->input('no_hp')
            );


            // Data Pasangan Calon Debitur
            $dataPasangan = array(
                'nama_lengkap'     => $request->input('nama_lengkap_pas'),
                'nama_ibu_kandung' => $request->input('nama_ibu_kandung_pas'),
                'jenis_kelamin'    => strtoupper($request->input('jenis_kelamin_pas')),
                'no_ktp'           => $request->input('no_ktp_pas'),
                'no_ktp_kk'        => $request->input('no_ktp_kk_pas'),
                'no_npwp'          => $request->input('no_npwp_pas'),
                'tempat_lahir'     => $request->input('tempat_lahir_pas'),
                'tgl_lahir'        => empty($request->input('tgl_lahir_pas')) ? null : Carbon::parse($request->input('tgl_lahir_pas'))->format('Y-m-d'),
                // 'alamat_ktp'       => $alamat_ktp_pas,
                // 'no_telp'          => $request->input('no_telp_pas'),
                // 'lamp_ktp'         => $lamp_ktp_pas,
                // 'lamp_buku_nikah'  => $lamp_buku_nikah_pas
            );

            // Data Penjamin
            if ($files = $request->file('lamp_ktp_pen')) {
                $name       = 'ktp_penjamin.';
                $check_file = 'null';

                $arrayPath = array();
                foreach ($files as $file) {
                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path_penj, $name);
                }

                $lamp_ktp_pen = $arrayPath;
            } else {
                $lamp_ktp_pen = null;
            }

            if ($files = $request->file('lamp_ktp_pasangan_pen')) {
                $name       = 'ktp_pasangan.';
                $check_file = 'null';

                $arrayPath = array();
                foreach ($files as $file) {
                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path_penj, $name);
                }

                $lamp_ktp_pasangan_pen = $arrayPath;
            } else {
                $lamp_ktp_pasangan_pen = null;
            }

            if ($files = $request->file('lamp_kk_pen')) {
                $name       = 'kk_penjamin.';
                $check_file = 'null';

                $arrayPath = array();
                foreach ($files as $file) {
                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path_penj, $name);
                }

                $lamp_kk_pen = $arrayPath;
            } else {
                $lamp_kk_pen = null;
            }

            if ($files = $request->file('lamp_buku_nikah_pen')) {
                $name       = 'buku_nikah_penjamin.';
                $check_file = 'null';

                $arrayPath = array();
                foreach ($files as $file) {
                    $arrayPath[] = Helper::uploadImg($check_file, $file, $path_penj, $name);
                }

                $lamp_buku_nikah_pen = $arrayPath;
            } else {
                $lamp_buku_nikah_pen = null;
            }

            if (!empty($request->input('nama_ktp_pen'))) {
                for ($i = 0; $i < count($request->input('nama_ktp_pen')); $i++) {

                    $DP[] = [
                        'nama_ktp'         => empty($request->nama_ktp_pen[$i])         ? null : $request->nama_ktp_pen[$i],
                        'nama_ibu_kandung' => empty($request->nama_ibu_kandung_pen[$i]) ? null : $request->nama_ibu_kandung_pen[$i],
                        'no_ktp'           => empty($request->no_ktp_pen[$i])           ? null : $request->no_ktp_pen[$i],
                        'no_npwp'          => empty($request->no_npwp_pen[$i])          ? null : $request->no_npwp_pen[$i],
                        'tempat_lahir'     => empty($request->tempat_lahir_pen[$i])     ? null : $request->tempat_lahir_pen[$i],
                        'tgl_lahir'        => empty($request->tgl_lahir_pen[$i])        ? null : Carbon::parse($request->tgl_lahir_pen[$i])->format('Y-m-d'),
                        'jenis_kelamin'    => empty($request->jenis_kelamin_pen[$i])    ? null : strtoupper($request->jenis_kelamin_pen[$i]),
                        'alamat_ktp'       => empty($request->alamat_ktp_pen[$i])       ? null : $request->alamat_ktp_pen[$i],
                        'no_telp'          => empty($request->no_telp_pen[$i])          ? null : $request->no_telp_pen[$i],
                        'hubungan_debitur' => empty($request->hubungan_debitur_pen[$i]) ? null : $request->hubungan_debitur_pen[$i],
                        'lamp_ktp'         => empty($lamp_ktp_pen[$i])              ? null : $lamp_ktp_pen[$i],
                        'lamp_ktp_pasangan' => empty($lamp_ktp_pasangan_pen[$i])     ? null : $lamp_ktp_pasangan_pen[$i],
                        'lamp_kk'          => empty($lamp_kk_pen[$i])               ? null : $lamp_kk_pen[$i],
                        'lamp_buku_nikah'  => empty($lamp_buku_nikah_pen[$i])       ? null : $lamp_buku_nikah_pen[$i]
                    ];
                }
            }
        }

        DB::connection('web')->beginTransaction();
        try {
            $debt = Debitur::create($dataDebitur);

            if ($dataFasPin) {
                $FasPin    = FasilitasPinjaman::create($dataFasPin);
                $id_faspin = $FasPin->id;
            } else {
                $id_faspin = null;
            }

            if ($dataDebitur['status_nikah'] === 'Menikah') {
                $pasangan    = Pasangan::create($dataPasangan);
                $id_pasangan = $pasangan->id;
            } else {
                $id_pasangan = null;
            }

            if (!empty($request->input('nama_ktp_pen'))) {
                for ($i = 0; $i < count($DP); $i++) {

                    $penjamin = Penjamin::create($DP[$i]);

                    $id_penjamin['id'][$i] = $penjamin->id;
                }

                $penID = implode(",", $id_penjamin['id']);
            } else {
                $penID = null;
            }

            $arrTr = array(
                'id_fasilitas_pinjaman' => $id_faspin,
                'id_calon_debitur'      => $debt->id,
                'id_pasangan'           => $id_pasangan,
                'id_penjamin'           => $penID
            );

            $mergeTr  = array_merge($trans_so, $arrTr);
            $transaksi = TransSO::create($mergeTr);

            $pen_exp = explode(',', $penID);

            Penjamin::whereIn('id', $pen_exp)->update(['id_trans_so' => $transaksi->id]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data berhasil dibuat',
                'data'   => $transaksi
            ], 200);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function update($id, Request $request, BlankRequest $req)
    {
        $pic = $request->pic; // From PIC middleware

        $check = TransSO::where('id', $id)->first();

        if ($check === null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan id " . $id . " tida ada di SO atau belum di rekomendasikan oleh DAS dan HM"
            ], 404);
        }

        $trans_so = array(
            'id_asal_data'   => empty($req->input('id_asal_data'))   ? $trans->id_asal_data   : $req->input('id_asal_data'),
            'nama_marketing' => empty($req->input('nama_marketing')) ? $trans->nama_marketing : $req->input('nama_marketing')
        );

        // Data Fasilitas Pinjaman
        $dataFasPin = array(
            'jenis_pinjaman'
            => empty($req->input('jenis_pinjaman'))
                ? $trans->faspin['jenis_pinjaman']
                : $req->input('jenis_pinjaman'),

            'tujuan_pinjaman'
            => empty($req->input('tujuan_pinjaman'))
                ? $trans->faspin['tujuan_pinjaman']
                : $req->input('tujuan_pinjaman'),

            'plafon'
            => empty($req->input('plafon_pinjaman'))
                ? $trans->faspin['plafon_pinjaman']
                : $req->input('plafon_pinjaman'),

            'tenor'
            => empty($req->input('tenor_pinjaman'))
                ? $trans->faspin['tenor_pinjaman']
                : $req->input('tenor_pinjaman')
        );


        DB::connection('web')->beginTransaction();
        try {
            TransSO::where('id', $id)->update($trans_so);

            FasilitasPinjaman::where('id', $check->id_fasilitas_pinjaman)->update($dataFasPin);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data berhasil diupdate'
            ], 200);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => 'terjadi kesalahan, mohon beri laporan kepada backend'
            ], 501);
        }
    }

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $column = array(
            'id', 'nomor_so', 'user_id', 'id_pic', 'id_area', 'id_cabang', 'id_asal_data', 'nama_marketing', 'nama_so', 'id_fasilitas_pinjaman', 'id_calon_debitur', 'id_pasangan', 'id_penjamin', 'id_trans_ao', 'id_trans_ca', 'id_trans_caa', 'catatan_das', 'catatan_hm', 'status_das', 'status_hm', 'lamp_ideb', 'lamp_pefindo'
        );

        if ($param != 'filter' && $param != 'search') {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if ($param == 'search') {
            $operator   = "like";
            $func_value = "%{$value}%";
        } else {
            $operator   = "=";
            $func_value = "{$value}";
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'faspin')
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($value == 'default') {
            $res = $query;
        } else {
            $res = $query->where($key, $operator, $func_value);
        }

        if ($limit == 'default') {
            $result = $res;
        } else {
            $result = $res->limit($limit);
        }

        if ($result->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        $data = array();
        foreach ($result->get() as $key => $val) {
            $data[$key] = [
                'id'              => $val->id == null ? null : (int) $val->id,
                'nomor_so'        => $val->nomor_so,
                'nama_so'         => $val->nama_so,
                'pic'             => $val->pic['nama'],
                'area'            => $val->area['nama'],
                'cabang'          => $val->cabang['nama'],
                'asal_data'       => $val->asaldata['nama'],
                'nama_marketing'  => $val->nama_marketing,
                'nama_calon_debt' => $val->debt['nama_lengkap'],
                'tgl_transaksi'   => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function filter($year, $month = null, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        if ($month == null) {

            $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'faspin')->orderBy('created_at', 'desc')
                ->whereYear('created_at', '=', $year);
        } else {

            $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'faspin')->orderBy('created_at', 'desc')
                ->whereYear('created_at', '=', $year)
                ->whereMonth('created_at', '=', $month);
        }

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query->get() == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {
            if ($val->status_das == 1) {
                $status_das = 'complete';
            } elseif ($val->status_das == 2) {
                $status_das = 'not complete';
            } else {
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            } elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            } else {
                $status_hm = 'waiting';
            }

            $data[$key] = [
                'id'              => $val->id == null ? null : (int) $val->id,
                'nomor_so'        => $val->nomor_so,
                'nama_so'         => $val->nama_so,
                'pic'             => $val->pic['nama'],
                'area'            => $val->area['nama'],
                'cabang'          => $val->cabang['nama'],
                'asal_data'       => $val->asaldata['nama'],
                'nama_marketing'  => $val->nama_marketing,
                'nama_calon_debt' => $val->debt['nama_lengkap'],
                'plafon'          => (int) $val->faspin['plafon'],
                'tenor'           => (int) $val->faspin['tenor'],
                'das' => [
                    'status'  => $status_das,
                    'catatan' => $val->catatan_das
                ],
                'hm'  => [
                    'status'  => $status_hm,
                    'catatan' => $val->catatan_hm
                ],
                'tgl_transaksi' => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
