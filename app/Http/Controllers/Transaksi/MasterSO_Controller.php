<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Pengajuan\SO\Debitur;
use Illuminate\Support\Facades\File;
use App\Models\AreaKantor\Cabang;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use App\Models\KeuanganUsaha;
use Illuminate\Http\Request;
use App\Http\Requests;
Use App\Models\User;
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

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin');

        $method = 'get';

        $query = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);


        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        foreach ($query as $key => $val) {
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

            $res[$key] = [
                'id'              => $val->id,
                'nomor_so'        => $val->nomor_so,
                'nama_so'         => $val->nama_so,
                'pic'             => $val->pic['nama'],
                'area'            => $val->area['nama'],
                'cabang'          => $val->cabang['nama'],
                'asal_data'       => $val->asaldata['nama'],
                'nama_marketing'  => $val->nama_marketing,
                'nama_calon_debt' => $val->debt['nama_lengkap'],
                'plafon'          => $val->faspin['plafon'],
                'tenor'           => $val->faspin['tenor'],
                'das' => [
                    'status'  => $status_das,
                    'catatan' => $val->catatan_das
                ],
                'hm'  => [
                    'status'  => $status_hm,
                    'catatan' => $val->catatan_hm
                ]
            ];

        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
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
        $method = 'first';

        $val = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);


        if (!$val) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        // $ao = TransAO::where('id_trans_so', $val->id)->first();

        $nama_anak = explode (",",$val->nama_anak);
        $tgl_anak  = explode (",",$val->tgl_lahir_anak);

        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = [
                'nama'  => $nama_anak[$i],
                'tgl_lahir' => $tgl_anak[$i]
            ];
        }

        $id_penj = explode (",",$val->id_penjamin);

        $pen = Penjamin::whereIn('id', $id_penj)->get();

        if ($pen == '[]') {
            $penjamin = null;
        }else{
            foreach ($pen as $key => $value) {
                $penjamin[$key] = [
                    "id"    => $value->id,
                    "nama"  => $value->nama_ktp,
                ];
            }
        }

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

        $res = [
            'id'          => $val->id,
            'nomor_so'    => $val->nomor_so,
            'nama_so'     => $val->nama_so,
            'id_pic'      => $val->id_pic,
            'nama_pic'    => $val->pic['nama'],
            'area'   => [
                'id'      => $val->id_area,
                'nama'    => $val->area['nama']
            ],
            'id_cabang'   => $val->id_cabang,
            'nama_cabang' => $val->cabang['nama'],
            'tracking'  => [
                'das' => $status_das,
                'hm'  => $status_hm,
                'ao'  => $status_ao,
                'ca'  => $status_ca,
                'caa' => $status_caa,
            ],
            'asal_data' => [
                'id'   => $val->id_asal_data,
                'nama' => $val->asaldata['nama'],
            ],
            'nama_marketing'    => $val->nama_marketing,
            'fasilitas_pinjaman'  => [
                'id'              => $val->id_fasilitas_pinjaman
            ],
            'calon_debitur'     => [
                'id'            => $val->id_calon_debitur,
                'nama_lengkap'  => $val->debt['nama_lengkap']
            ],

            'pasangan'    => [
                'id'      => $val->id_pasangan,
                'nama'    => $val->pas['nama_lengkap'],
            ],
            'penjamin'  => $penjamin,
            'flg_aktif' => $val->flg_aktif == 0 ? "false" : "true"
        ];

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
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

        if (!$countTSO) {
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

        $ktp = 3216190107670001; //$req->input('no_ktp');

        // $check_ktp_web = DB::connection('web')->select("SELECT no_ktp, UNIX_TIMESTAMP(created_at) as tgl FROM calon_debitur WHERE no_ktp=? limit 1",[$ktp]);
        $check_ktp_web = Debitur::select('no_ktp', 'created_at')->where('no_ktp', $ktp)->first();


        if($check_ktp_web != null){

            $created_at = $check_ktp_web->created_at->timestamp;

            $compare_day_in_second = $dateExpires - $created_at;

            if ($compare_day_in_second <= $day_in_second) {
                return response()->json([
                    "code"    => 403,
                    "status"  => "Expired",
                    'message' => "Akun belum aktif kembali, belum ada 1 bulan yang lalu no ktp melakukan pengajuan"
                ], 403);
            // }else{
            //     return response()->json([
            //         "code"    => 200,
            //         "status"  => "success",
            //         "message" => "Akun telah ada di sistem, apakah anda ingin menggunakan data lama ?",
            //         "redirect"=> ""
            //     ], 200);
            }
        }else{

            $check_ktp_dpm = DB::connection("web")->table("view_nasabah")->where("NO_ID", $ktp)->first();

            if ($check_ktp_dpm != null) {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Akun berdasarkan no ktp sudah pernah melakukan pengajuan sebelumnya"
                ], 404);
            }
        }


        $lamp_dir = 'public/'.$ktp;

        if($file = $req->file('lamp_ktp')){
            $path = $lamp_dir.'/debitur';
            $name = 'ktp.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $ktpDebt = $path.'/'.$name;
        }else{
            $ktpDebt = null;
        }

        if($file = $req->file('lamp_kk')){
            $path = $lamp_dir.'/debitur';
            $name = 'kk.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $kkDebt = $path.'/'.$name;
        }else{
            $kkDebt = null;
        }

        if($file = $req->file('lamp_sertifikat')){
            $path = $lamp_dir.'/debitur';
            $name = 'sertifikat.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $sertifikatDebt = $path.'/'.$name;
        }else{
            $sertifikatDebt = null;
        }

        if($file = $req->file('lamp_pbb')){
            $path = $lamp_dir.'/debitur';
            $name = 'pbb.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $pbbDebt = $path.'/'.$name;
        }else{
            $pbbDebt = null;
        }

        if($file = $req->file('lamp_imb')){
            $path = $lamp_dir.'/debitur';
            $name = 'imb.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $imbDebt = $path.'/'.$name;
        }else{
            $imbDebt = null;
        }

        if($file = $req->file('foto_agunan_rumah')){
            $path = $lamp_dir.'/debitur';
            $name = 'foto_agunan_rumah.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $foto_agunan_rumah = $path.'/'.$name;
        }else{
            $foto_agunan_rumah = null;
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
            'tgl_lahir'             => Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),
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
            'lamp_ktp'              => $ktpDebt,
            'lamp_kk'               => $kkDebt,
            'lamp_sertifikat'       => $sertifikatDebt,
            'lamp_sttp_pbb'         => $pbbDebt,
            'lamp_imb'              => $imbDebt,
            'foto_agunan_rumah'     => $foto_agunan_rumah
        );

        if($file = $req->file('lamp_ktp_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'ktp.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $ktpPass = $path.'/'.$name;
        }else{
            $ktpPass = null;
        }

        if($file = $req->file('lamp_buku_nikah_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'buku_nikah.' . $file->getClientOriginalName();
            $file->move($path,$name);

            $bukuNikahPass = $path.'/'.$name;
        }else{
            $bukuNikahPass = null;
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
            'tgl_lahir'        => Carbon::parse($req->input('tgl_lahir_pas'))->format('Y-m-d'),
            'alamat_ktp'       => $alamat_ktp_pas,
            'no_telp'          => $req->input('no_telp_pas'),
            'lamp_ktp'         => $ktpPass,
            'lamp_buku_nikah'  => $bukuNikahPass
        );

        // Data Penjamin
        // $a = 1; $b = 1; $c = 1; $d = 1;

        if($files = $req->file('lamp_ktp_pen')){
            foreach($files as $file){
                $path = $lamp_dir.'/penjamin';
                $name = 'ktp_penjamin.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $ktpPen[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('lamp_ktp_pasangan_pen')){
            foreach($files as $file){
                $path = $lamp_dir.'/penjamin';
                $name = 'ktp_pasangan.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $ktpPenPAS[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('lamp_kk_pen')){
            foreach($files as $file){
                $path = $lamp_dir.'/penjamin';
                $name = 'kk_penjamin.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $kkPen[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('lamp_buku_nikah_pen')){
            foreach($files as $file){
                $path = $lamp_dir.'/penjamin';
                $name = 'buku_nikah_penjamin.' . $file->getClientOriginalName();
                $file->move($path,$name);

                $bukuNikahPen[] = $path.'/'.$name;
            }
        }

        if (!empty($req->input('nama_ktp_pen'))) {
            for ($i = 0; $i < count($req->input('nama_ktp_pen')); $i++) {

                $DP[] = [
                    'nama_ktp'         => empty($req->nama_ktp_pen[$i]) ? null : $req->nama_ktp_pen[$i],
                    'nama_ibu_kandung' => empty($req->nama_ibu_kandung_pen[$i]) ? null : $req->nama_ibu_kandung_pen[$i],
                    'no_ktp'           => empty($req->no_ktp_pen[$i]) ? null : $req->no_ktp_pen[$i],
                    'no_npwp'          => empty($req->no_npwp_pen[$i]) ? null : $req->no_npwp_pen[$i],
                    'tempat_lahir'     => empty($req->tempat_lahir_pen[$i]) ? null : $req->tempat_lahir_pen[$i],
                    'tgl_lahir'        => empty($req->tgl_lahir_pen[$i]) ? null : Carbon::parse($req->tgl_lahir_pen[$i])->format('Y-m-d'),
                    'jenis_kelamin'    => empty($req->jenis_kelamin_pen[$i]) ? null : strtoupper($req->jenis_kelamin_pen[$i]),
                    'alamat_ktp'       => empty($req->alamat_ktp_pen[$i]) ? null : $req->alamat_ktp_pen[$i],
                    'no_telp'          => empty($req->no_telp_pen[$i]) ? null : $req->no_telp_pen[$i],
                    'hubungan_debitur' => empty($req->hubungan_debitur_pen[$i]) ? null : $req->hubungan_debitur_pen[$i],
                    'lamp_ktp'         => empty($ktpPen[$i]) ? null : $ktpPen[$i],
                    'lamp_ktp_pasangan'=> empty($ktpPenPAS[$i]) ? null : $ktpPenPAS[$i],
                    'lamp_kk'          => empty($kkPen[$i]) ? null : $kkPen[$i],
                    'lamp_buku_nikah'  => empty($bukuNikahPen[$i]) ? null : $bukuNikahPen[$i]
                ];

                /*if ($DP[$i]['lamp_ktp'] == null) {
                    return response()->json([
                        "code"    => 422,
                        "status"  => "not valid request",
                        "message" => "lamp_ktp_pen ada yang belum diisi"
                    ], 422);
                }

                if ($DP[$i]['lamp_ktp_pasangan'] == null) {
                    return response()->json([
                        "code"    => 422,
                        "status"  => "not valid request",
                        "message" => "lamp_ktp_pasangan_penjamin ada yang belum diisi"
                    ], 422);
                }

                if ($DP[$i]['lamp_kk'] == null) {
                    return response()->json([
                        "code"    => 422,
                        "status"  => "not valid request",
                        "message" => "lamp_kk ada yang belum diisi"
                    ], 422);
                }

                if ($DP[$i]['lamp_buku_nikah'] == null) {
                    return response()->json([
                        "code"    => 422,
                        "status"  => "not valid request",
                        "message" => "lamp_buku_nikah ada yang belum diisi"
                    ], 422);
                }*/
            }
        }

        DB::connection('web')->beginTransaction();
        // try {
            $debt = Debitur::create($dataDebitur);
            $id_debt = $debt->id;

            $arrIdDebt = array('id_calon_debitur' => $id_debt);

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
                'id_calon_debitur'      => $id_debt,
                'id_pasangan'           => $id_pasangan,
                'id_penjamin'           => $penID
            );

            $mergeTr  = array_merge($trans_so, $arrTr);
            TransSO::create($mergeTr);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat'
            ], 200);
        // }catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $err
        //     ], 501);
        // }
    }

    public function update($id, Request $request, BlankRequest $req){
        $user_id     = $request->auth->user_id;
        $username    = $request->auth->user;

        $trans = TransSO::where('id', $id)->first();

        if (!$trans) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
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
            'id_asal_data'   => empty($req->input('id_asal_data')) ? $trans->id_asal_data : $req->input('id_asal_data'),
            'nama_marketing' => empty($req->input('nama_marketing')) ? $trans->nama_marketing : $req->input('nama_marketing')
        );

        // Data Fasilitas Pinjaman
        $dataFasPin = array(
            'jenis_pinjaman'  => empty($req->input('jenis_pinjaman')) ? $trans->faspin['jenis_pinjaman'] : $req->input('jenis_pinjaman'),
            'tujuan_pinjaman' => empty($req->input('tujuan_pinjaman')) ? $trans->faspin['tujuan_pinjaman'] : $req->input('tujuan_pinjaman'),
            'plafon'          => empty($req->input('plafon_pinjaman')) ? $trans->faspin['plafon_pinjaman'] : $req->input('plafon_pinjaman'),
            'tenor'           => empty($req->input('tenor_pinjaman')) ? $trans->faspin['tenor_pinjaman'] : $req->input('tenor_pinjaman')
        );

        $lamp_dir = 'public/lamp_trans.'.$trans->nomor_so;

        if($file = $req->file('lamp_ktp')){
            $path = $lamp_dir.'/debitur';
            $name = 'ktp.' . $file->getClientOriginalName();

            if(!empty($trans->debt['lamp_ktp']))
            {
                File::delete($trans->debt['lamp_ktp']);
            }

            $file->move($path,$name);

            $ktpDebt = $path.'/'.$name;
        }else{
            $ktpDebt = $trans->debt['lamp_ktp'];
        }

        if($file = $req->file('lamp_kk')){
            $path = $lamp_dir.'/debitur';
            $name = 'kk.' . $file->getClientOriginalName();

            if(!empty($trans->debt['lamp_kk']))
            {
                File::delete($trans->debt['lamp_kk']);
            }

            $file->move($path,$name);

            $kkDebt = $path.'/'.$name;
        }else{
            $kkDebt = $trans->debt['lamp_kk'];
        }

        if($file = $req->file('lamp_sertifikat')){
            $path = $lamp_dir.'/debitur';
            $name = 'sertifikat.' . $file->getClientOriginalName();

            if(!empty($trans->debt['lamp_sertifikat']))
            {
                File::delete($trans->debt['lamp_sertifikat']);
            }

            $file->move($path,$name);

            $sertifikatDebt = $path.'/'.$name;
        }else{
            $sertifikatDebt = $trans->debt['lamp_sertifikat'];
        }

        if($file = $req->file('lamp_pbb')){
            $path = $lamp_dir.'/debitur';
            $name = 'pbb.' . $file->getClientOriginalName();

            if(!empty($trans->debt['lamp_pbb']))
            {
                File::delete($trans->debt['lamp_pbb']);
            }

            $file->move($path,$name);

            $pbbDebt = $path.'/'.$name;
        }else{
            $pbbDebt = $trans->debt['lamp_pbb'];
        }

        if($file = $req->file('lamp_imb')){
            $path = $lamp_dir.'/debitur';
            $name = 'imb.' . $file->getClientOriginalName();

            if(!empty($trans->debt['lamp_imb']))
            {
                File::delete($trans->debt['lamp_imb']);
            }

            $file->move($path,$name);

            $imbDebt = $path.'/'.$name;
        }else{
            $imbDebt = $trans->debt['lamp_imb'];
        }

        // Data Calon Debitur
        $dataDebitur = array(
            'nama_lengkap'          => empty($req->input('nama_lengkap')) ? $trans->debt['nama_lengkap'] : $req->input('nama_lengkap'),
            'gelar_keagamaan'       => empty($req->input('gelar_keagamaan')) ? $trans->debt['gelar_keagamaan'] : $req->input('gelar_keagamaan'),
            'gelar_pendidikan'      => empty($req->input('gelar_pendidikan')) ? $trans->debt['gelar_pendidikan'] : $req->input('gelar_pendidikan'),
            'jenis_kelamin'         => empty($req->input('jenis_kelamin')) ? $trans->debt['jenis_kelamin'] : strtoupper($req->input('jenis_kelamin')),
            'status_nikah'          => empty($req->input('status_nikah')) ? $trans->debt['status_nikah'] : strtoupper($req->input('status_nikah')),
            'ibu_kandung'           => empty($req->input('ibu_kandung')) ? $trans->debt['ibu_kandung']: $req->input('ibu_kandung'),
            'no_ktp'                => empty($req->input('no_ktp')) ? $trans->debt['no_ktp'] : $req->input('no_ktp'),
            'no_ktp_kk'             => empty($req->input('no_ktp_kk')) ? $trans->debt['no_ktp_kk'] : $req->input('no_ktp_kk'),
            'no_kk'                 => empty($req->input('no_kk')) ? $trans->debt['no_kk'] : $req->input('no_kk'),
            'no_npwp'               => empty($req->input('no_npwp')) ? $trans->debt['no_npwp'] : $req->input('no_npwp'),
            'tempat_lahir'          => empty($req->input('tempat_lahir')) ? $trans->debt['tempat_lahir']: $req->input('tempat_lahir'),
            'tgl_lahir'             => empty($req->input('tgl_lahir')) ? $trans->debt['tgl_lahir'] : Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),
            'agama'                 => empty($req->input('agama')) ? $trans->debt['agama'] : strtoupper($req->input('agama')),
            'alamat_ktp'            => empty($req->input('alamat_ktp')) ? $trans->debt['alamat_ktp'] : $req->input('alamat_ktp'),
            'rt_ktp'                => empty($req->input('rt_ktp')) ? $trans->debt['rt_ktp'] : $req->input('rt_ktp'),
            'rw_ktp'                => empty($req->input('rw_ktp')) ? $trans->debt['rw_ktp'] : $req->input('rw_ktp'),
            'id_prov_ktp'           => empty($req->input('id_provinsi_ktp')) ? $trans->debt['id_prov_ktp'] : $req->input('id_provinsi_ktp'),
            'id_kab_ktp'            => empty($req->input('id_kabupaten_ktp')) ? $trans->debt['id_kab_ktp'] : $req->input('id_kabupaten_ktp'),
            'id_kec_ktp'            => empty($req->input('id_kecamatan_ktp')) ? $trans->debt['id_kec_ktp'] : $req->input('id_kecamatan_ktp'),
            'id_kel_ktp'            => empty($req->input('id_kelurahan_ktp')) ? $trans->debt['id_kel_ktp'] : $req->input('id_kelurahan_ktp'),
            'alamat_domisili'       => empty($req->input('alamat_domisili')) ? $trans->debt['alamat_domisili'] : $req->input('alamat_domisili'),
            'rt_domisili'           => empty($req->input('rt_domisili')) ? $trans->debt['rt_domisili'] : $req->input('rt_domisili'),
            'rw_domisili'           => empty($req->input('rw_domisili')) ? $trans->debt['rw_domisili'] : $req->input('rw_domisili'),
            'id_prov_domisili'      => empty($req->input('id_provinsi_domisili')) ? $trans->debt['id_prov_domisili'] : $req->input('id_provinsi_domisili'),
            'id_kab_domisili'       => empty($req->input('id_kabupaten_domisili')) ? $trans->debt['id_kab_domisili'] : $req->input('id_kabupaten_domisili'),
            'id_kec_domisili'       => empty($req->input('id_kecamatan_domisili')) ? $trans->debt['id_kec_domisili'] : $req->input('id_kecamatan_domisili'),
            'id_kel_domisili'       => empty($req->input('id_kelurahan_domisili')) ? $trans->debt['id_kel_domisili'] : $req->input('id_kelurahan_domisili'),
            'pendidikan_terakhir'   => empty($req->input('pendidikan_terakhir')) ? $trans->debt['pendidikan_terakhir'] : $req->input('pendidikan_terakhir'),
            'jumlah_tanggungan'     => empty($req->input('jumlah_tanggungan')) ? $trans->debt['jumlah_tanggungan'] : $req->input('jumlah_tanggungan'),
            'no_telp'               => empty($req->input('no_telp')) ? $trans->debt['no_telp'] : $req->input('no_telp'),
            'no_hp'                 => empty($req->input('no_hp')) ? $trans->debt['no_hp'] : $req->input('no_hp'),
            'alamat_surat'          => empty($req->input('alamat_surat')) ? $trans->debt['alamat_surat'] : $req->input('alamat_surat'),
            'lamp_ktp'              => $ktpDebt,
            'lamp_kk'               => $kkDebt,
            'lamp_sertifikat'       => $sertifikatDebt,
            'lamp_sttp_pbb'         => $pbbDebt,
            'lamp_imb'              => $imbDebt
        );

        if($file = $req->file('lamp_ktp_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'ktp.' . $file->getClientOriginalName();

            if(!empty($trans->pas['lamp_ktp']))
            {
                File::delete($trans->pas['lamp_ktp']);
            }

            $file->move($path,$name);

            $ktpPass = $path.'/'.$name;
        }else{
            $ktpPass = $trans->pas['lamp_ktp'];
        }

        if($file = $req->file('lamp_buku_nikah_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'buku_nikah.' . $file->getClientOriginalName();

            if(!empty($trans->pas['lamp_buku_nikah']))
            {
                File::delete($trans->pas['lamp_buku_nikah']);
            }

            $file->move($path,$name);

            $bukuNikahPass = $path.'/'.$name;
        }else{
            $bukuNikahPass = $trans->pas['lamp_buku_nikah'];
        }

        // Data Pasangan Calon Debitur
        $dataPasangan = array(
            'nama_lengkap'     => empty($req->input('nama_lengkap_pas')) ? $trans->nama_lengkap : $req->input('nama_lengkap_pas'),
            'nama_ibu_kandung' => empty($req->input('nama_ibu_kandung_pas')) ? $trans->nama_ibu_kandung : $req->input('nama_ibu_kandung_pas'),
            'jenis_kelamin'    => strtoupper($req->input('jenis_kelamin_pas')),
            'no_ktp'           => empty($req->input('no_ktp_pas')) ? $trans->no_ktp : $req->input('no_ktp_pas'),
            'no_ktp_kk'        => $req->input('no_ktp_kk_pas'),
            'no_npwp'          => $req->input('no_npwp_pas'),
            'tempat_lahir'     => $req->input('tempat_lahir_pas'),
            'tgl_lahir'        => Carbon::parse($req->input('tgl_lahir_pas'))->format('Y-m-d'),
            'alamat_ktp'       => $req->input('alamat_ktp_pas'),
            'no_telp'          => $req->input('no_telp_pas'),
            'lamp_ktp'         => $ktpPass,
            'lamp_buku_nikah'  => $bukuNikahPass
        );

        // Data Penjamin
        if (!$trans->id_penjamin) {
            $id_penjamin = null;
        }else{
            $id_penj = explode (",",$trans->id_penjamin);

            $penjamin = Penjamin::whereIn('id', $id_penj)->get();

            if ($penjamin != '[]') {

                // $a = 1; $b = 1; $c = 1; $d = 1;

                if($files = $req->file('lamp_ktp_pen')){
                    foreach($files as $file){

                        $name = 'ktp_penjamin.' . $file->getClientOriginalName();

                        foreach ($penjamin as $key => $val) {

                            if ($val->lamp_ktp != null) {
                                $no_so = $val->lamp_ktp;
                            }else{
                                $no_so = $val->lamp_ktp;
                            }

                            $arrPath = explode("/", $no_so, 4);

                            $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                            if(!empty($val->lamp_ktp))
                            {
                                File::delete($val->lamp_ktp);
                            }
                        }

                        $file->move($path,$name);

                        $ktpPen[] = $path.'/'.$name;
                    }
                }

                if($files = $req->file('lamp_ktp_pasangan_pen')){
                    foreach($files as $file){

                        $name = 'ktp_pasangan.' . $file->getClientOriginalName();

                        foreach ($penjamin as $key => $val) {
                            $no_so = $val->lamp_ktp_pasangan;

                            $arrPath = explode("/", $no_so, 4);

                            $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                            if(!empty($val->lamp_ktp_pasangan))
                            {
                                File::delete($val->lamp_ktp_pasangan);
                            }
                        }

                        $file->move($path,$name);

                        $ktpPenPAS[] = $path.'/'.$name;
                    }
                }

                if($files = $req->file('lamp_kk_pen')){
                    foreach($files as $file){

                        $name = 'kk_penjamin.' . $file->getClientOriginalName();

                        foreach ($penjamin as $key => $val) {
                            $no_so = $val->lamp_kk;

                            $arrPath = explode("/", $no_so, 4);

                            $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                            if(!empty($val->lamp_kk))
                            {
                                File::delete($val->lamp_kk);
                            }
                        }

                        $file->move($path,$name);

                        $kkPen[] = $path.'/'.$name;
                    }
                }

                if($files = $req->file('lamp_buku_nikah_pen')){
                    foreach($files as $file){

                        $name = 'buku_nikah_penjamin.' . $file->getClientOriginalName();

                        foreach ($penjamin as $key => $val) {
                            $no_so = $val->lamp_buku_nikah;

                            $arrPath = explode("/", $no_so, 4);

                            $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                            if(!empty($val->lamp_buku_nikah))
                            {
                                File::delete($val->lamp_buku_nikah);
                            }
                        }

                        $file->move($path,$name);

                        $bukuNikahPen[] = $path.'/'.$name;
                    }
                }

                $i = 0;
                foreach ($penjamin as $key => $value) {
                    $DP[$key] = [
                        'nama_ktp'         => empty($req->input('nama_ktp_pen')[$i]) ? $value->nama_ktp : $req->input('nama_ktp_pen')[$i],
                        'nama_ibu_kandung' => empty($req->input('nama_ibu_kandung_pen')[$i]) ? $value->nama_ibu_kandung : $req->input('nama_ibu_kandung_pen')[$i],
                        'no_ktp'           => empty($req->input('no_ktp_pen')[$i]) ? $value->no_ktp : $req->input('no_ktp_pen')[$i],
                        'no_npwp'          => empty($req->input('no_npwp_pen')[$i]) ? $value->no_npwp : $req->input('no_npwp_pen')[$i],
                        'tempat_lahir'     => empty($req->input('tempat_lahir_pen')[$i]) ? $value->tempat_lahir : $req->input('tempat_lahir_pen')[$i],
                        'tgl_lahir'        => empty($req->input('tgl_lahir_pen')[$i]) ? $value->tgl_lahir : Carbon::parse($req->input('tgl_lahir_pen')[$i])->format('Y-m-d'),
                        'jenis_kelamin'    => empty($req->input('jenis_kelamin_pen')[$i]) ? $value->jenis_kelamin : strtoupper($req->input('jenis_kelamin_pen')[$i]),
                        'alamat_ktp'       => empty($req->input('alamat_ktp_pen')[$i]) ? $value->alamat_ktp : $req->input('alamat_ktp_pen')[$i],
                        'no_telp'          => empty($req->input('no_telp_pen')[$i]) ? $value->no_telp : $req->input('no_telp_pen')[$i],
                        'hubungan_debitur' => empty($req->input('hubungan_debitur_pen')[$i]) ? $value->hubungan_debitur : $req->input('hubungan_debitur_pen')[$i],
                        'lamp_ktp'         => empty($ktpPen[$i]) ? $value->lamp_ktp : $ktpPen[$i],
                        'lamp_ktp_pasangan'=> empty($ktpPenPAS[$i]) ? $value->lamp_ktp_pasangan : $ktpPenPAS[$i],
                        'lamp_kk'          => empty($kkPen[$i]) ? $value->lamp_kk : $kkPen[$i],
                        'lamp_buku_nikah'  => empty($bukuNikahPen[$i]) ? $value->lamp_buku_nikah : $bukuNikahPen[$i],
                        'updated_at'       => Carbon::now()->toDateTimeString()
                    ];
                    $i++;
                }
            }
        }


        DB::connection('web')->beginTransaction();
        // try{
            TransSO::where('id', $id)->update($trans_so);
            FasilitasPinjaman::where('id', $trans->id_fasilitas_pinjaman)->update($dataFasPin);
            Debitur::where('id', $trans->id_calon_debt)->update($dataDebitur);
            Pasangan::where('id', $trans->id_pasangan)->update($dataPasangan);

            if ($trans->id_penjamin != null) {
                for($i=0; $i < count($id_penj); $i++){
                    Penjamin::where('id', $id_penj[$i])->update($DP[$i]);
                }
            }

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil diupdate'
            ], 200);
        // }catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => 'terjadi kesalahan, mohon beri laporan kepada backend'
        //     ], 501);
        // }
    }

    public function search($search, Request $req){
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

        $query_dir = TransSO::with('pic', 'cabang', 'asaldata','debt', 'faspin')->where('nomor_so', 'like', '%'.$search.'%');
        $method = 'get';

        $query = Helper::checkDir($user_id, $scope, $query_dir, $id_area, $id_cabang, $method);


        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }else{
            foreach ($query as $key => $val) {
                $res[$key] = [
                    'id'              => $val->id,
                    'nomor_so'        => $val->nomor_so,
                    'nama_so'         => $val->nama_so,
                    'pic'             => $val->pic['nama'],
                    'area'            => $val->area['nama'],
                    'cabang'          => $val->cabang['nama'],
                    'asal_data'       => $val->asaldata['nama'],
                    'nama_marketing'  => $val->nama_marketing,
                    'nama_calon_debt' => $val->debt['nama_lengkap']
                ];
            }

            try {
                return response()->json([
                    'code'   => 200,
                    'status' => 'success',
                    'data'   => $res
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
}
