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
use DB;

class MasterSO_Controller extends BaseController
{
    public function index(Request $req){
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(SO). Harap daftarkan diri sebagai PIC(SO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin')->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query->get() == '[]') {
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
            }elseif($val->status_das == 2){
                $status_das = 'not complete';
            }else{
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            }elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            }else{
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
                'tgl_transaksi' => Carbon::parse($val->created_at)->format("d-m-Y H:i:s")
            ];

        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }

    }

    public function show($id, Request $req){
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(SO). Harap daftarkan diri sebagai PIC(SO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')->where('id', $id);

        $vals = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $val  = $vals->first();

        if ($val->first() == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan id ".$id." tidak ada di SO atau belum di rekomendasikan oleh bagian DAS dan HM"
            ], 404);
        }

        // $ao = TransAO::where('id_trans_so', $val->id)->first();

        $nama_anak = explode (",",$val->nama_anak);
        $tgl_anak  = explode (",",$val->tgl_lahir_anak);

        $anak = array();
        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = [
                'nama'      => $nama_anak[$i],
                'tgl_lahir' => $tgl_anak[$i]
            ];
        }

        $id_penj = explode (",",$val->id_penjamin);
        $pen = array();
        foreach ($id_penj as $value) {
            $pen[] = array(
                'id' => (int) $value
            );
        }

        // $pen = Penjamin::select('id', 'nama_ktp as nama')->whereIn('id', $id_penj)->get()->toArray();


        if ($val->status_das == 1) {
            $status_das = 'complete';
        }elseif($val->status_das == 2){
            $status_das = 'not complete';
        }else{
            $status_das = 'waiting';
        }

        if ($val->status_hm == 1) {
            $status_hm = 'complete';
        }elseif ($val->status_hm == 2) {
            $status_hm = 'not complete';
        }else{
            $status_hm = 'waiting';
        }


        if ($val->ao['status_ao'] == 1) {
            $status_ao = 'complete';
        }elseif ($val->ao['status_ao'] == 2) {
            $status_ao = 'not complete';
        }else{
            $status_ao = 'waiting';
        }


        if ($val->ca['status_ca'] == 1) {
            $status_ca = 'complete';
        }elseif ($val->ca['status_ca'] == 2) {
            $status_ca = 'not complete';
        }else{
            $status_ca = 'waiting';
        }


        if ($val->caa['status_caa'] == 1) {
            $status_caa = 'complete';
        }elseif ($val->caa['status_caa'] == 2) {
            $status_caa = 'not complete';
        }else{
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
            'tracking'  => [
                'das' => $status_das,
                'hm'  => $status_hm,
                'ao'  => $status_ao,
                'ca'  => $status_ca,
                'caa' => $status_caa,
            ],
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
            'flg_aktif'   => (bool) $val->flg_aktif,
            'tgl_transaksi' => Carbon::parse($val->created_at)->format("d-m-Y H:i:s")
        ];

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(Request $request, BlankRequest $req)
    {
        $user_id  = $request->auth->user_id;
        $username = $request->auth->user;

        $PIC = PIC::where('user_id', $user_id)->first();

        if ($PIC == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC(SO). Harap daftarkan diri sebagai PIC pada form PIC(SO) atau hubungi bagian IT"
            ], 404);
        }

        $countTSO = TransSO::latest('id','nomor_so')->first();

        if ($countTSO == null) {
            $lastNumb = 1;
        }else{
            $no = $countTSO->nomor_so;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;

            // $no = $countTSO + 1;
        }
        //Data Transaksi SO
        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        $JPIC   = JPIC::where('id', $PIC->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_so = $PIC->id_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb; //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut

        $trans_so = array(
            'nomor_so'       => $nomor_so,
            'user_id'        => $user_id,
            'id_pic'         => $PIC->id,
            'id_area'        => $PIC->id_area,
            'id_cabang'      => $PIC->id_cabang,
            'nama_so'        => $PIC->nama,
            'id_asal_data'   => $req->input('id_asal_data'),
            'nama_marketing' => $req->input('nama_marketing')
        );

        // Data Fasilitas Pinjaman
        $dataFasPin = array(
            'jenis_pinjaman'  => $req->input('jenis_pinjaman'),
            'tujuan_pinjaman' => $req->input('tujuan_pinjaman'),
            'plafon'          => $req->input('plafon_pinjaman'),
            'tenor'           => $req->input('tenor_pinjaman')
        );

        $dateExpires   = strtotime($now); // time to integer
        $day_in_second = 60 * 60 * 24 * 30;

        $ktp        = $req->input('no_ktp'); // 3216190107670001;
        // $no_ktp_kk  = $req->input('no_ktp_kk');
        $no_ktp_pas = $req->input('no_ktp_pas');
        // $no_ktp_pen = $req->input('no_ktp_pen');


        $check_ktp_dpm = DB::connection("web")->table("view_nasabah")->where("NO_ID", $ktp)->first();

        if ($check_ktp_dpm == null) {
            $NASABAH_ID = null;
        }else{
            $NASABAH_ID = $check_ktp_dpm->NASABAH_ID;
        }

        $check_ktp_web = Debitur::select('id', 'no_ktp', 'nama_lengkap', 'created_at')->where('no_ktp', $ktp)->first();

        if($check_ktp_web != null){

            $created_at = $check_ktp_web->created_at->timestamp;

            $compare_day_in_second = $dateExpires - $created_at;

            if ($compare_day_in_second <= $day_in_second) {
                return response()->json([
                    "code"    => 403,
                    "status"  => "Expired",
                    'message' => "Akun belum aktif kembali, belum ada 1 bulan yang lalu, tepatnya pada tanggal '".Carbon::parse($check_ktp_web->created_at)->format("d-m-Y")."' debitur dengan nama '{$check_ktp_web->nama_lengkap}' telah melakukan pengajuan"
                ], 403);
            }else{
                return response()->json([
                    "code"    => 200,
                    "status"  => "success",
                    "message" => "Akun telah ada di sistem, gunakan endpoint berikut apabila ingin menggunakan datanya",
                    "endpoint"=> "/api/debitur/".$check_ktp_web->id
                ], 200);
            }
        }else{
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


            $lamp_dir = 'public/'.$ktp;

            if($file = $req->file('lamp_ktp')){
                $path = $lamp_dir.'/debitur';
                $name = 'ktp.';

                $check = 'null';

                $lamp_ktp = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_ktp = null;
            }

            if($file = $req->file('lamp_kk')){
                $path = $lamp_dir.'/debitur';
                $name = 'kk.';

                $check = 'null';
            
                $lamp_kk = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_kk = null;
            }

            if($file = $req->file('lamp_sertifikat')){
                $path = $lamp_dir.'/debitur';
                $name = 'sertifikat.';

                $check = 'null';
            
                $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_sertifikat = null;
            }

            if($file = $req->file('lamp_pbb')){
                $path = $lamp_dir.'/debitur';
                $name = 'pbb.';

                $check = 'null';
            
                $lamp_sttp_pbb = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_sttp_pbb = null;
            }

            if($file = $req->file('lamp_imb')){
                $path = $lamp_dir.'/debitur';
                $name = 'imb.';

                $check = 'null';
            
                $lamp_imb = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_imb = null;
            }

            if($file = $req->file('foto_agunan_rumah')){
                $path = $lamp_dir.'/debitur';
                $name = 'foto_agunan_rumah.';

                $check = 'null';
            
                $foto_agunan_rumah = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $foto_agunan_rumah = null;
            }

            if ($file = $req->file('lamp_surat_cerai')) {
                $path = $lamp_dir.'/debitur';
                $name = 'lamp_surat_cerai.';

                $check = 'null';
            
                $lamp_surat_cerai = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_surat_cerai = null;
            }

            // Data Calon Debitur
            $dataDebitur = array(
                'nama_lengkap'          => $req->input('nama_lengkap'),
                'gelar_keagamaan'       => $req->input('gelar_keagamaan'),
                'gelar_pendidikan'      => $req->input('gelar_pendidikan'),
                'jenis_kelamin'         => strtoupper($req->input('jenis_kelamin')),
                'status_nikah'          => strtoupper($req->input('status_nikah')),
                'ibu_kandung'           => $req->input('ibu_kandung'),
                'no_ktp'                => $ktp,
                'no_ktp_kk'             => $req->input('no_ktp_kk'),
                'no_kk'                 => $req->input('no_kk'),
                'no_npwp'               => $req->input('no_npwp'),
                'tempat_lahir'          => $req->input('tempat_lahir'),
                'tgl_lahir'             => empty($req->input('tgl_lahir')) ? null : Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),
                'agama'                 => strtoupper($req->input('agama')),
                'alamat_ktp'            => $alamat_ktp = $req->input('alamat_ktp'),
                'rt_ktp'                => $rt_ktp = $req->input('rt_ktp'),
                'rw_ktp'                => $rw_ktp = $req->input('rw_ktp'),
                'id_prov_ktp'           => $id_prov_ktp = $req->input('id_provinsi_ktp'),
                'id_kab_ktp'            => $id_kab_ktp = $req->input('id_kabupaten_ktp'),
                'id_kec_ktp'            => $id_kec_ktp = $req->input('id_kecamatan_ktp'),
                'id_kel_ktp'            => $id_kel_ktp = $req->input('id_kelurahan_ktp'),
                'alamat_domisili'       => empty($req->input('alamat_domisili')) ? $alamat_ktp : $req->input('alamat_domisili'),
                'rt_domisili'           => empty($req->input('rt_domisili')) ? $rt_ktp : $req->input('rt_domisili'),
                'rw_domisili'           => empty($req->input('rw_domisili')) ? $rw_ktp : $req->input('rw_domisili'),
                'id_prov_domisili'      => empty($req->input('id_provinsi_domisili')) ? $id_prov_ktp : $req->input('id_provinsi_domisili'),
                'id_kab_domisili'       => empty($req->input('id_kabupaten_domisili')) ? $id_kab_ktp : $req->input('id_kabupaten_domisili'),
                'id_kec_domisili'       => empty($req->input('id_kecamatan_domisili')) ? $id_kec_ktp : $req->input('id_kecamatan_domisili'),
                'id_kel_domisili'       => empty($req->input('id_kelurahan_domisili')) ? $id_kel_ktp : $req->input('id_kelurahan_domisili'),
                'pendidikan_terakhir'   => $req->input('pendidikan_terakhir'),
                'jumlah_tanggungan'     => $req->input('jumlah_tanggungan'),
                'no_telp'               => $req->input('no_telp'),
                'no_hp'                 => $req->input('no_hp'),
                'alamat_surat'          => $req->input('alamat_surat'),
                'lamp_ktp'              => $lamp_ktp,
                'lamp_kk'               => $lamp_kk,
                'lamp_sertifikat'       => $lamp_sertifikat,
                'lamp_sttp_pbb'         => $lamp_sttp_pbb,
                'lamp_imb'              => $lamp_imb,
                'lamp_surat_cerai'      => $lamp_surat_cerai,
                'foto_agunan_rumah'     => $foto_agunan_rumah,
                'NASABAH_ID'            => $NASABAH_ID
            );

            if($file = $req->file('lamp_ktp_pas')){
                $path = $lamp_dir.'/pasangan';
                $name = 'ktp.';

                $check = 'null';
            
                $lamp_ktp_pas = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_ktp_pas = null;
            }

            if($file = $req->file('lamp_buku_nikah_pas')){
                $path = $lamp_dir.'/pasangan';
                $name = 'buku_nikah.';

                $check = 'null';
            
                $lamp_buku_nikah_pas = Helper::uploadImg($check, $file, $path, $name);
            }else{
                $lamp_buku_nikah_pas = null;
            }

            if (!empty($req->input('nama_lengkap_pas'))) {

                $alamat_ktp_pas = empty($req->input('alamat_ktp_pas')) ? $dataDebitur['alamat_ktp'] : $req->input('alamat_ktp_pas');

            }else{
                $alamat_ktp_pas = null;
            }

            // Data Pasangan Calon Debitur
            $dataPasangan = array(
                'nama_lengkap'     => $req->input('nama_lengkap_pas'),
                'nama_ibu_kandung' => $req->input('nama_ibu_kandung_pas'),
                'jenis_kelamin'    => strtoupper($req->input('jenis_kelamin_pas')),
                'no_ktp'           => $req->input('no_ktp_pas'),
                'no_ktp_kk'        => $req->input('no_ktp_kk_pas'),
                'no_npwp'          => $req->input('no_npwp_pas'),
                'tempat_lahir'     => $req->input('tempat_lahir_pas'),
                'tgl_lahir'        => empty($req->input('tgl_lahir_pas')) ? null : Carbon::parse($req->input('tgl_lahir_pas'))->format('Y-m-d'),
                'alamat_ktp'       => $alamat_ktp_pas,
                'no_telp'          => $req->input('no_telp_pas'),
                'lamp_ktp'         => $lamp_ktp_pas,
                'lamp_buku_nikah'  => $lamp_buku_nikah_pas
            );

            // Data Penjamin
            if($files = $req->file('lamp_ktp_pen')){
                $path = $lamp_dir.'/penjamin';
                $name = 'ktp_penjamin.';

                $check = 'null';

                $arrayPath = array();
                foreach($files as $file)
                {
                    $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
                }

                $lamp_ktp_pen = $arrayPath;
            }else{
                $lamp_ktp_pen = null;
            }

            if($files = $req->file('lamp_ktp_pasangan_pen')){
                $path = $lamp_dir.'/penjamin';
                $name = 'ktp_pasangan.';

                $check = 'null';

                $arrayPath = array();
                foreach($files as $file)
                {
                    $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
                }

                $lamp_ktp_pasangan_pen = $arrayPath;
            }else{
                $lamp_ktp_pasangan_pen = null;
            }

            if($files = $req->file('lamp_kk_pen')){
                $path = $lamp_dir.'/penjamin';
                $name = 'kk_penjamin.';

                $check = 'null';

                $arrayPath = array();
                foreach($files as $file)
                {
                    $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
                }

                $lamp_kk_pen = $arrayPath;
            }else{
                $lamp_kk_pen = null;
            }

            if($files = $req->file('lamp_buku_nikah_pen')){
                $path = $lamp_dir.'/penjamin';
                $name = 'buku_nikah_penjamin.';

                $check = 'null';

                $arrayPath = array();
                foreach($files as $file)
                {
                    $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
                }

                $lamp_buku_nikah_pen = $arrayPath;
            }else{
                $lamp_buku_nikah_pen = null;
            }

            if (!empty($req->input('nama_ktp_pen'))) {
                for ($i = 0; $i < count($req->input('nama_ktp_pen')); $i++) {

                    $DP[] = [
                        'nama_ktp'         => empty($req->nama_ktp_pen[$i])         ? null : $req->nama_ktp_pen[$i],
                        'nama_ibu_kandung' => empty($req->nama_ibu_kandung_pen[$i]) ? null : $req->nama_ibu_kandung_pen[$i],
                        'no_ktp'           => empty($req->no_ktp_pen[$i])           ? null : $req->no_ktp_pen[$i],
                        'no_npwp'          => empty($req->no_npwp_pen[$i])          ? null : $req->no_npwp_pen[$i],
                        'tempat_lahir'     => empty($req->tempat_lahir_pen[$i])     ? null : $req->tempat_lahir_pen[$i],
                        'tgl_lahir'        => empty($req->tgl_lahir_pen[$i])        ? null : Carbon::parse($req->tgl_lahir_pen[$i])->format('Y-m-d'),
                        'jenis_kelamin'    => empty($req->jenis_kelamin_pen[$i])    ? null : strtoupper($req->jenis_kelamin_pen[$i]),
                        'alamat_ktp'       => empty($req->alamat_ktp_pen[$i])       ? null : $req->alamat_ktp_pen[$i],
                        'no_telp'          => empty($req->no_telp_pen[$i])          ? null : $req->no_telp_pen[$i],
                        'hubungan_debitur' => empty($req->hubungan_debitur_pen[$i]) ? null : $req->hubungan_debitur_pen[$i],
                        'lamp_ktp'         => empty($lamp_ktp_pen[$i])              ? null : $lamp_ktp_pen[$i],
                        'lamp_ktp_pasangan'=> empty($lamp_ktp_pasangan_pen[$i])     ? null : $lamp_ktp_pasangan_pen[$i],
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
            }else{
                $id_faspin = null;
            }

            if ($dataDebitur['status_nikah'] == 'NIKAH') {
                $pasangan    = Pasangan::create($dataPasangan);
                $id_pasangan = $pasangan->id;
            }else{
                $id_pasangan = null;
            }

            if (!empty($req->input('nama_ktp_pen'))) {
                for ($i = 0; $i < count($DP); $i++) {

                    $penjamin = Penjamin::create($DP[$i]);

                    $id_penjamin['id'][$i] = $penjamin->id;
                }

                $penID = implode(",", $id_penjamin['id']);
            }else{
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

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat',
                'data'   => $transaksi
            ], 200);
        }catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function update($id, Request $request, BlankRequest $req){
        $user_id     = $request->auth->user_id;
        $username    = $request->auth->user;

        $check = TransSO::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan id ".$id." tida ada di SO atau belum di rekomendasikan oleh DAS dan HM"
            ], 404);
        }

        $PIC = PIC::where('user_id', $user_id)->first();

        if (!$PIC) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
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
        try{
            TransSO::where('id', $id)->update($trans_so);

            FasilitasPinjaman::where('id', $check->id_fasilitas_pinjaman)->update($dataFasPin);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil diupdate'
            ], 200);
        }catch (\Exception $e) {
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
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(SO). Harap daftarkan diri sebagai PIC(SO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $column = array(
            'id', 'nomor_so', 'user_id', 'id_pic', 'id_area', 'id_cabang', 'id_asal_data', 'nama_marketing', 'nama_so', 'id_fasilitas_pinjaman', 'id_calon_debitur', 'id_pasangan', 'id_penjamin', 'id_trans_ao', 'id_trans_ca', 'id_trans_caa', 'catatan_das', 'catatan_hm', 'status_das', 'status_hm', 'lamp_ideb', 'lamp_pefindo'
        );

        if($param != 'filter' && $param != 'search'){
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if($param == 'search'){
            $operator   = "like";
            $func_value = "%{$value}%";
        }else{
            $operator   = "=";
            $func_value = "{$value}";
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin')
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);
        
        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($key, $operator, $func_value);
        }

        if($limit == 'default'){
            $result = $res;
        }else{
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
                'tgl_transaksi'   => Carbon::parse($val->created_at)->format("d-m-Y H:i:s")
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function filter($year, $month=null, Request $req){
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(SO). Harap daftarkan diri sebagai PIC(SO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        if ($month == null) {

            $query_dir = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin')->orderBy('created_at', 'desc')
                    ->whereYear('created_at', '=', $year);
        }else{

            $query_dir = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin')->orderBy('created_at', 'desc')
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
            }elseif($val->status_das == 2){
                $status_das = 'not complete';
            }else{
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            }elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            }else{
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
                'tgl_transaksi' => Carbon::parse($val->created_at)->format("d-m-Y H:i:s")
            ];

        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}