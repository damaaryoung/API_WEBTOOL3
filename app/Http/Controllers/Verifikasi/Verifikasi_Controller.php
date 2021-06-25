<?php

namespace App\Http\Controllers\Verifikasi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\ActivitySo;
use App\Models\Efilling\Bi_checking;
use App\Models\Efilling\Efilling;
use App\Models\Efilling\Efilling_asset;
use App\Models\Efilling\Efilling_bi;
use App\Models\Verifikasi\ApinegCase;
use App\Models\Efilling\Efilling_ca;
use App\Models\Efilling\Efilling_legal;
use App\Models\Efilling\Efilling_spkndk;
use App\Models\Efilling\EfillingJaminan;
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;
use App\Models\Efilling\EfillingNasabah;
use App\Models\Efilling\EfillingPermohonan;
use App\Models\Efilling\Verifnpwp as EfillingVerifnpwp;
use App\Models\Verifikasi\Verifcadebt;
use App\Models\Verifikasi\VerifCadebtLog;
use App\Models\Verifikasi\Verifnpwp;
use App\Models\Verifikasi\Verifnpwppasangan;
use App\Models\Verifikasi\Verifnpwppenjamin;
use App\Models\Verifikasi\Verifpasangan;
use App\Models\Verifikasi\VerifpasanganLog;
use Illuminate\Support\Facades\Storage;

// use Intervention\Image\Image;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Transaksi\Lpdk_lampiran;
use App\Models\Verifikasi\Verifpenjamin;
use App\Models\Verifikasi\VerifpenjaminLog;
use App\Models\Verifikasi\Verifproperti;
use App\Models\Verifikasi\VerifReqcadebt;
use App\Models\Verifikasi\VerifReqnpwp;
use App\Models\Verifikasi\VerifReqnpwppasangan;
use App\Models\Verifikasi\VerifReqnpwppenjamin;
use App\Models\Verifikasi\VerifReqpasangan;
use App\Models\Verifikasi\VerifReqproperti;
use Illuminate\Support\Facades\DB;


class Verifikasi_Controller extends BaseController
{
    public function filter(Request $req)
    {
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;

        $area = $req->input('area');
        $cabang = $req->input('cabang');

        $trans_so = TransAO::select('trans_so.created_at as tgl_transaksi', 'calon_debitur.nama_lengkap as nama_debitur', 'trans_so.id_area', 'trans_so.id_cabang', 'trans_so.id as id_trans_so', 'trans_so.nomor_so')->join('trans_so', 'trans_ao.id_trans_so', 'trans_so.id')->join('calon_debitur', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.status_hm', 1)->where('trans_so.status_das', 1)->paginate(10);
        // dd(empty($cabang));
        if ($area && empty($cabang)) {
            $trans_so = TransAO::select('trans_so.created_at as tgl_transaksi', 'calon_debitur.nama_lengkap as nama_debitur', 'trans_so.id_area', 'trans_so.id_cabang', 'trans_so.id as id_trans_so', 'trans_so.nomor_so')->join('trans_so', 'trans_ao.id_trans_so', 'trans_so.id')->join('calon_debitur', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.status_hm', 1)->where('trans_so.status_das', 1)->where('trans_so.id_area', $area)->paginate(10);
        } elseif ($area && $cabang) {
            $trans_so = TransAO::select('trans_so.created_at as tgl_transaksi', 'calon_debitur.nama_lengkap as nama_debitur', 'trans_so.id_area', 'trans_so.id_cabang', 'trans_so.id as id_trans_so', 'trans_so.nomor_so')->join('trans_so', 'trans_ao.id_trans_so', 'trans_so.id')->join('calon_debitur', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.status_hm', 1)->where('trans_so.status_das', 1)->where('trans_so.id_area', $area)->where('trans_so.id_cabang', $cabang)->paginate(10);
        } else {
            $trans_so;
        }
        // $list = DB::connection('web')->select("SELECT a.id_area, a.id_cabang, a.created_at as tgl_transaksi,calon_debiturb.nama_lengkap as nama_debitur  FROM trans_so as a JOIN calon_debitur as b ON(a.id_calon_debitur=b.id)
        // WHERE a.status_das=1 AND a.status_hm=1 AND a.id_area='$area' OR a.id_cabang='$cabang'   limit 10
        // ");
        // dd($list);
        if ($trans_so === null) {
            return response()->json([
                "code" => 404,
                "message" => 'Data Tidak Ditemukan'
            ], 404);
        }
        try {
            return response()->json([
                'code' => 201,
                'status' => 'success',
                'data' => $trans_so
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
      public function showVerif($id_trans_so)
    {


        $debitur = DB::connection('web')->table('trans_so')->where('id', $id_trans_so)->first();
        $show_cadeb = DB::connection('web')->table('verif_cadebt')->select(
            'verif_cadebt.id',
            'verif_cadebt.id_trans_so',
            'verif_cadebt.no_ktp',
            'verif_cadebt.user_id',
            'verif_cadebt.nama',
            'verif_cadebt.tempat_lahir',
            'verif_cadebt.tgl_lahir',
            'verif_cadebt.alamat',
            'verif_cadebt.selfie_foto',
            'verif_cadebt.trx_id',
            'verif_cadebt.ref_id',
            'verif_cadebt.limit_call',
            'b.nama AS nama_user',
            "verif_cadebt.id_pic",
            "verif_cadebt.id_area",
            "verif_cadebt.id_cabang",
            "verif_cadebt.nominal",
            'verif_cadebt.created_at',
            'verif_cadebt.updated_at'
        )->join('dpm_online.user as b', 'verif_cadebt.user_id', 'b.user_id')->where('verif_cadebt.id_trans_so', $id_trans_so)->first();
        $show_pasangan = DB::connection('web')->table('verif_pasangan')->select(
            'verif_pasangan.id',
            'verif_pasangan.id_trans_so',
            'verif_pasangan.no_ktp',
            'verif_pasangan.user_id',
            'verif_pasangan.nama',
            'verif_pasangan.tempat_lahir',
            'verif_pasangan.tgl_lahir',
            'verif_pasangan.alamat',
            'verif_pasangan.selfie_foto',
            'verif_pasangan.trx_id',
            'verif_pasangan.ref_id',
            'verif_pasangan.limit_call',
            'b.nama AS nama_user',
            "verif_pasangan.id_pic",
            "verif_pasangan.id_area",
            "verif_pasangan.id_cabang",
            "verif_pasangan.nominal",
            'verif_pasangan.created_at',
            'verif_pasangan.updated_at'
        )->join('dpm_online.user as b', 'verif_pasangan.user_id', 'b.user_id')->where('verif_pasangan.id_trans_so', $id_trans_so)->first();
        $show_npwp = DB::connection('web')->table('verif_npwp')->select(
            'verif_npwp.id_trans_so',
            'verif_npwp.npwp',
            'verif_npwp.nik',
            'verif_npwp.match_result',
            'verif_npwp.income',
            'verif_npwp.nama',
            'verif_npwp.user_id',
            'verif_npwp.tgl_lahir',
            'verif_npwp.tmp_lahir',
            'verif_npwp.trx_id',
            'verif_npwp.ref_id',
            'verif_npwp.limit_call',
            'b.nama AS nama_user',
            "verif_npwp.id_pic",
            "verif_npwp.id_area",
            "verif_npwp.id_cabang",
            "verif_npwp.id_penjamin",
            "verif_npwp.id_pasangan",
            "verif_npwp.nominal",
            'verif_npwp.created_at',
            'verif_npwp.updated_at'
        )->join('dpm_online.user as b', 'verif_npwp.user_id', 'b.user_id')->where('verif_npwp.id_trans_so', $id_trans_so)->first();
        $show_npwp_pasangan = DB::connection('web')->table('verif_npwp_pasangan')->select(
            'verif_npwp_pasangan.id_trans_so',
            'verif_npwp_pasangan.npwp',
            'verif_npwp_pasangan.nik',
            'verif_npwp_pasangan.match_result',
            'verif_npwp_pasangan.income',
            'verif_npwp_pasangan.nama',
            'verif_npwp_pasangan.user_id',
            'verif_npwp_pasangan.tgl_lahir',
            'verif_npwp_pasangan.tmp_lahir',
            'verif_npwp_pasangan.trx_id',
            'verif_npwp_pasangan.ref_id',
            'verif_npwp_pasangan.limit_call',
            'b.nama AS nama_user',
            "verif_npwp_pasangan.id_pic",
            "verif_npwp_pasangan.id_area",
            "verif_npwp_pasangan.id_cabang",
            "verif_npwp_pasangan.nominal",
            'verif_npwp_pasangan.created_at',
            'verif_npwp_pasangan.updated_at'
        )->join('dpm_online.user as b', 'verif_npwp_pasangan.user_id', 'b.user_id')->where('verif_npwp_pasangan.id_trans_so', $id_trans_so)->first();
        $show_npwp_penjamin = DB::connection('web')->table('verif_npwp_penjamin')->select(
            'verif_npwp_penjamin.id_trans_so',
			'verif_npwp_penjamin.id_penjamin',
            'verif_npwp_penjamin.npwp',
            'verif_npwp_penjamin.nik',
            'verif_npwp_penjamin.match_result',
            'verif_npwp_penjamin.income',
            'verif_npwp_penjamin.nama',
            'verif_npwp_penjamin.user_id',
            'verif_npwp_penjamin.tgl_lahir',
            'verif_npwp_penjamin.tmp_lahir',
            'verif_npwp_penjamin.trx_id',
            'verif_npwp_penjamin.ref_id',
            'verif_npwp_penjamin.limit_call',
            'b.nama AS nama_user',
            "verif_npwp_penjamin.id_pic",
            "verif_npwp_penjamin.id_area",
            "verif_npwp_penjamin.id_cabang",
            "verif_npwp_penjamin.nominal",
            'verif_npwp_penjamin.created_at',
            'verif_npwp_penjamin.updated_at'
        )->join('dpm_online.user as b', 'verif_npwp_penjamin.user_id', 'b.user_id')->where('verif_npwp_penjamin.id_trans_so', $id_trans_so)->get();
        $show_penjamin = DB::connection('web')->table('verif_penjamin')->select(
            'verif_penjamin.id_trans_so',
            'verif_penjamin.no_ktp',
            'verif_penjamin.nama',
            'verif_penjamin.tempat_lahir',
            'verif_penjamin.tgl_lahir',
            'verif_penjamin.alamat',
            'verif_penjamin.selfie_foto',
            'verif_penjamin.trx_id',
            'verif_penjamin.ref_id',
            'verif_penjamin.limit_call',
            'b.nama AS nama_user',
            'verif_penjamin.user_id',
            "verif_penjamin.id_pic",
            "verif_penjamin.id_area",
            "verif_penjamin.id_cabang",
            "verif_penjamin.id_penjamin",
            "verif_penjamin.nominal",
            'verif_penjamin.created_at',
            'verif_penjamin.updated_at'
        )->join('dpm_online.user as b', 'verif_penjamin.user_id', 'b.user_id')->where('verif_penjamin.id_trans_so', $id_trans_so)->orderBy('verif_penjamin.id_penjamin', 'ASC')->get();

        $show_properti = DB::connection('web')->table('verif_properti')->select(
            'verif_properti.id_trans_so',
            'verif_properti.id_agunan_tanah',
            'verif_properti.no_ktp',
            'verif_properti.property_address',
            'verif_properti.property_name',
            'verif_properti.property_building_area',
            'verif_properti.property_surface_area',
            'verif_properti.property_estimation',
            'verif_properti.certificate_address',
            'verif_properti.certificate_id',
            'verif_properti.certificate_name',
            'verif_properti.certificate_type',
            'verif_properti.certificate_date',
            'verif_properti.trx_id',
            'verif_properti.ref_id',
            'b.nama AS nama_user',
            "verif_properti.id_pic",
            "verif_properti.user_id",
            "verif_properti.id_area",
            "verif_properti.id_cabang",
            "verif_properti.nominal",
            'verif_properti.created_at',
            'verif_properti.updated_at'
        )->join('dpm_online.user as b', 'verif_properti.user_id', 'b.user_id')->where('id_trans_so', $id_trans_so)->get();
        //    dd($show_npwp);
        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }

        try {

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array("cadebt" => $show_cadeb, "pasangan" => $show_pasangan, "npwp" => $show_npwp, "npwp_pasangan" => $show_npwp_pasangan, "npwp_penjamin" => $show_npwp_penjamin, "penjamin" => $show_penjamin, "property" => $show_properti)
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
    public function storecadeb(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware

        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;


        $debitur = Debitur::select('so.id AS id_trans_so', 'so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp')->join('trans_so AS so', 'so.id_calon_debitur', 'calon_debitur.id')
            ->where('so.id', $id_trans_so)->first();

        $req_deb = Debitur::join('trans_so AS so', 'so.id_calon_debitur', 'calon_debitur.id')
            ->where('so.id', $id_trans_so)->first();
        // $debitur = TransSO::where('trans_so.id', $id_trans_so)->join('calon_debitur', 'trans_so.id_calon_debitur', 'calon_debitur.id')->first();
        $verif_exists = VerifReqCadebt::where('id_trans_so', $id_trans_so)->first();

        //  dd($req_deb, $id_trans_so);
        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }
        // if ($verif_exists !== null) {
        //     return response()->json([
        //         "code" => 404,
        //         "message" => "Data Verifikasi Sudah Ada"
        //     ], 404);
        // }

        $req_cadeb = array(
            "id_trans_so" => $debitur->id_trans_so,
            "no_ktp"    => $req_deb->no_ktp,
            "nama"      => $req_deb->nama_lengkap,
            "tempat_lahir" => $req_deb->tempat_lahir,
            "tgl_lahir"     => $req_deb->tgl_lahir,
            "alamat"    => $req_deb->alamat_ktp,
            "selfie_foto" => $req_deb->foto_cadeb,
            //"limit_call" => $req_deb->no_ktp,
            // "trx_id"    => $req_deb->no_ktp,
            // "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );
        //  dd($req_cadeb);
        $verif_cadeb = array(
            "id_trans_so" => $debitur->id_trans_so,
            //   "nomor_so"  => $debitur->nomor_so,
            "no_ktp"    => $debitur->no_ktp,
            "nama"      => $req->input('nama'),
            "tempat_lahir" => $req->input('tempat_lahir'),
            "tgl_lahir"     => $req->input('tgl_lahir'),
            "alamat"    => $req->input('alamat'),
            "selfie_foto" => $req->input('selfie_foto'),
            "limit_call" => $req->input('limit_call'),
            "trx_id"    => $req->input('trx_id'),
            "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );

        //    try {
        $cadeb = VerifCadebt::create($verif_cadeb);
        $requst_cadebt = VerifReqCadebt::create($req_cadeb);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $verif_cadeb
        ]);
        // } catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $err
        //     ], 501);
        // }
    }

    public function storepasangan(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;

        $pasangan = Debitur::select('trans_so.id AS id_trans_so', 'trans_so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp', 'calon_debitur.nama_lengkap AS nama_cadeb', 'calon_debitur.tempat_lahir AS tempat_lahir_cadeb', 'calon_debitur.tgl_lahir AS  tgl_lahir_cadeb', 'calon_debitur.alamat_ktp AS alamat_ktp_cadeb')->join('trans_so', 'trans_so.id_calon_debitur',  'calon_debitur.id')->join('pasangan_calon_debitur', 'trans_so.id_pasangan', 'pasangan_calon_debitur.id')->where('trans_so.id',  $id_trans_so)->first();

        $pas_req = Pasangan::join('trans_so', 'trans_so.id_pasangan', 'pasangan_calon_debitur.id')->where('trans_so.id', $id_trans_so)->first();
        //dd($pas_req);
        if ($pas_req === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Pasangan Debitur Tidak Ditemukan"
            ], 404);
        }
        $req_pasangan = array(
            "id_trans_so" => $pas_req->id,
            // "nomor_so"  => $pasangan->nomor_so,
            "no_ktp"    => $pas_req->no_ktp,
            "nama"      => $pas_req->nama_lengkap,
            "tempat_lahir" => $pas_req->tempat_lahir,
            "tgl_lahir"     => $pas_req->tgl_lahir,
            "alamat"    => $pas_req->alamat_ktp,
            "selfie_foto" => $pas_req->foto_pasangan,
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );

        $verif_pasangan = array(
            "id_trans_so" => $pasangan->id_trans_so,
            // "nomor_so"  => $pasangan->nomor_so,
            "no_ktp"    => $pasangan->no_ktp,
            "nama"      => $req->input('nama'),
            "tempat_lahir" => $req->input('tempat_lahir'),
            "tgl_lahir"     => $req->input('tgl_lahir'),
            "alamat"    => $req->input('alamat'),
            "selfie_foto" => $req->input('selfie_foto'),
            "limit_call" => $req->input('limit_call'),
            "trx_id"    => $req->input('trx_id'),
            "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );

        try {
            $req_pas = VerifReqpasangan::create($req_pasangan);
            $pasangan = Verifpasangan::create($verif_pasangan);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_pasangan
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

  public function storenpwp(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;

        $debitur = Debitur::select('trans_so.id AS id_trans_so', 'trans_so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp', 'calon_debitur.nama_lengkap AS nama_cadeb', 'calon_debitur.tempat_lahir AS tempat_lahir_cadeb', 'calon_debitur.tgl_lahir AS tgl_lahir_cadeb', 'calon_debitur.alamat_ktp AS alamat_ktp_cadeb', 'calon_debitur.no_npwp AS no_npwp')->join('trans_so', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.id', $id_trans_so)->first();

        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }

        $verif_npwp = array(
            "id_trans_so" => $debitur->id_trans_so,
            //  "nomor_so"  => $debitur->nomor_so,
            "npwp"      => $req->input('npwp'),
            "nik" => $req->input('nik'),
            "match_result"     => $req->input('match_result'),
            "income"    => $req->input('income'),
            "nama" => $req->input('nama'),
            "tgl_lahir" => $req->input('tgl_lahir'),
            "tmp_lahir" => $req->input('tmp_lahir'),
            "limit_call" => $req->input('limit_call'),
            "trx_id"    => $req->input('trx_id'),
            "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );

        //$ao = TransAO::W
        $income = TransAO::select('kapasitas_bulanan.pemasukan_cadebt')->join('kapasitas_bulanan', 'trans_ao.id_kapasitas_bulanan', 'kapasitas_bulanan.id')->where('trans_ao.id_trans_so', $id_trans_so)->first();

        $req_npwp = array(
            "id_trans_so" => $debitur->id_trans_so,
            //  "nomor_so"  => $debitur->nomor_so,
            "npwp"      => $debitur->no_npwp,
            "nik" => $debitur->no_ktp,
            // "match_result"     => $debitur->no_npwp,
            "income"    => $income->pemasukan_cadebt,
            "nama" => $debitur->nama_cadeb,
            "tgl_lahir" => $debitur->tgl_lahir,
            "tmp_lahir" => $debitur->tempat_lahir,
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );
        try {
            $req_npwp = VerifReqnpwp::create($req_npwp);
            $verifnpwp = Verifnpwp::create($verif_npwp);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_npwp,
                'request' => $req_npwp
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
	
	public function storenpwppasangan(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;

        $pasangan = Pasangan::select('trans_so.id AS id_trans_so', 'trans_so.nomor_so AS nomor_so', 'pasangan_calon_debitur.no_ktp AS no_ktp', 'pasangan_calon_debitur.nama_lengkap AS nama_cadeb', 'pasangan_calon_debitur.tempat_lahir AS tempat_lahir_cadeb', 'pasangan_calon_debitur.tgl_lahir AS tgl_lahir_cadeb', 'pasangan_calon_debitur.alamat_ktp AS alamat_ktp_cadeb', 'pasangan_calon_debitur.no_npwp AS no_npwp')->join('trans_so', 'trans_so.id_pasangan', 'pasangan_calon_debitur.id')->where('trans_so.id', $id_trans_so)->first();

        if ($pasangan === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data pasangan Tidak Ditemukan"
            ], 404);
        }

        $verif_npwp = array(
            "id_trans_so" => $pasangan->id_trans_so,
            //  "nomor_so"  => $debitur->nomor_so,
            "npwp"      => $req->input('npwp'),
            "nik" => $req->input('nik'),
            "match_result"     => $req->input('match_result'),
            "income"    => $req->input('income'),
            "nama" => $req->input('nama'),
            "tgl_lahir" => $req->input('tgl_lahir'),
            "tmp_lahir" => $req->input('tmp_lahir'),
            "limit_call" => $req->input('limit_call'),
            "trx_id"    => $req->input('trx_id'),
            "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );

        //$ao = TransAO::W
        $income = TransAO::select('kapasitas_bulanan.pemasukan_pasangan')->join('kapasitas_bulanan', 'trans_ao.id_kapasitas_bulanan', 'kapasitas_bulanan.id')->where('trans_ao.id_trans_so', $id_trans_so)->first();

        $req_npwp = array(
            "id_trans_so" => $pasangan->id_trans_so,
            //  "nomor_so"  => $pasangan->nomor_so,
            "npwp"      => $pasangan->no_npwp,
            "nik" => $pasangan->no_ktp,
            // "match_result"     => $pasangan->no_npwp,
            "income"    => $income->pemasukan_pasangan,
            "nama" => $pasangan->nama_cadeb,
            "tgl_lahir" => $pasangan->tgl_lahir,
            "tmp_lahir" => $pasangan->tempat_lahir,
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );
        // dd($verif_npwp, $req_npwp);
        try {
            $req_npwp = VerifReqnpwppasangan::create($req_npwp);
            $verifnpwp = Verifnpwppasangan::create($verif_npwp);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_npwp,
                'request' => $req_npwp
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
	
	  public function storenpwppenjamin(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;

        $penjamin = Penjamin::select('trans_so.id AS id_trans_so', 'trans_so.nomor_so AS nomor_so', 'penjamin_calon_debitur.no_ktp AS no_ktp', 'penjamin_calon_debitur.nama_ktp AS nama_cadeb', 'penjamin_calon_debitur.tempat_lahir AS tempat_lahir_cadeb', 'penjamin_calon_debitur.tgl_lahir AS tgl_lahir_cadeb', 'penjamin_calon_debitur.alamat_ktp AS alamat_ktp_cadeb', 'penjamin_calon_debitur.no_npwp AS no_npwp')->join('trans_so', 'trans_so.id_penjamin', 'penjamin_calon_debitur.id')->where('trans_so.id', $id_trans_so)->first();

        if ($penjamin === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data penjamin Tidak Ditemukan"
            ], 404);
        }

        $verif_npwp = array(
            "id_trans_so" => $penjamin->id_trans_so,
            //  "nomor_so"  => $debitur->nomor_so,
            "npwp"      => $req->input('npwp'),
			"id_penjamin"      => $req->input('id_penjamin'),
            "nik" => $req->input('nik'),
            "match_result"     => $req->input('match_result'),
            "income"    => $req->input('income'),
            "nama" => $req->input('nama'),
            "tgl_lahir" => $req->input('tgl_lahir'),
            "tmp_lahir" => $req->input('tmp_lahir'),
            "limit_call" => $req->input('limit_call'),
            "trx_id"    => $req->input('trx_id'),
            "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );

        //$ao = TransAO::W
        $income = TransAO::select('kapasitas_bulanan.pemasukan_penjamin')->join('kapasitas_bulanan', 'trans_ao.id_kapasitas_bulanan', 'kapasitas_bulanan.id')->where('trans_ao.id_trans_so', $id_trans_so)->first();

        $req_npwp = array(
            "id_trans_so" => $penjamin->id_trans_so,
            //  "nomor_so"  => $penjamin->nomor_so,
            "npwp"      => $penjamin->no_npwp,
            "nik" => $penjamin->no_ktp,
            // "match_result"     => $penjamin->no_npwp,
            "income"    => $income->pemasukan_penjamin,
            "nama" => $penjamin->nama_cadeb,
            "tgl_lahir" => $penjamin->tgl_lahir,
            "tmp_lahir" => $penjamin->tempat_lahir,
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );
        // dd($verif_npwp, $req_npwp);
        try {
            $req_npwp = VerifReqnpwppenjamin::create($req_npwp);
            $verifnpwp = Verifnpwppenjamin::create($verif_npwp);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_npwp,
                'request' => $req_npwp
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function storeProperti(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;

        $debitur = Debitur::select('trans_so.id AS id_trans_so', 'trans_so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp', 'calon_debitur.nama_lengkap AS nama_cadeb', 'calon_debitur.tempat_lahir AS tempat_lahir_cadeb', 'calon_debitur.tgl_lahir AS tgl_lahir_cadeb', 'calon_debitur.alamat_ktp AS alamat_ktp_cadeb')->join('trans_so', 'trans_so.id_calon_debitur', 'calon_debitur.id')->join('trans_ao', 'trans_so.id', 'trans_ao.id_trans_so')->where('trans_so.id', $id_trans_so)->first();

        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }

        $verif_properti = array(
            "id_trans_so" => $debitur->id_trans_so,
            "id_agunan_tanah" => $req->input('id_agunan_tanah'),
            "property_address"      => $req->input('property_address'),
            "property_name" => $req->input('property_name'),
            "property_building_area"     => $req->input('property_building_area'),
            "property_surface_area"    => $req->input('property_surface_area'),
            "property_estimation" => $req->input('property_estimation'),
            "certificate_address" => $req->input('certificate_address'),
            "certificate_id" => $req->input('certificate_id'),
            "certificate_name" => $req->input('certificate_name'),
            "certificate_type" => $req->input('certificate_type'),
            "certificate_date" => $req->input('certificate_date'),
            "trx_id"    => $req->input('trx_id'),
            "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );

        try {
            $cadeb = VerifProperti::create($verif_properti);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_properti
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function storepenjamin(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware
        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;


        $trans_so = TransSO::where('id', $id_trans_so)->first();
        // $debitur = TransSO::where('trans_so.id', $id_trans_so)->join('calon_debitur', 'trans_so.id_calon_debitur', 'calon_debitur.id')->first();
        $verif_exists = Verifpenjamin::where('id_trans_so', $id_trans_so)->first();

        //  dd($debitur, $id_trans_so);
        // if ($trans_so === null) {
        //     return response()->json([
        //         "code" => 404,
        //         "message" => "Data Penjamin Debitur Tidak Ditemukan"
        //     ], 404);
        // }
        // if ($verif_exists !== null) {
        //     return response()->json([
        //         "code" => 404,
        //         "message" => "Data Verifikasi Sudah Ada"
        //     ], 404);
        // }

        $verif_penjamin = array(
            "id_trans_so" => $trans_so->id,
            //   "nomor_so"  => $debitur->nomor_so,
            //  "no_ktp"    => $debitur->no_ktp,
            "nama"      => $req->input('nama'),
            "tempat_lahir" => $req->input('tempat_lahir'),
            "tgl_lahir"     => $req->input('tgl_lahir'),
            "alamat"    => $req->input('alamat'),
            "selfie_foto" => $req->input('selfie_foto'),
            "limit_call" => $req->input('limit_call'),
            "trx_id"    => $req->input('trx_id'),
            "ref_id"    => $req->input('ref_id'),
            "user_id"    => $user_id,
            "id_pic"    => $pic[0]['id'],
            "id_area"    => $id_area[0],
            "id_cabang"    => $id_cabang[0],
            "id_penjamin" => $req->input('id_penjamin'),
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );
        $trans_so = TransSO::where('id', $id_trans_so)->first();

        $penjamin = Penjamin::where('id', $verif_penjamin['id_penjamin'])->first();


        if ($trans_so === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Transaksi Debitur Tidak Ditemukan"
            ], 404);
        }

        // if ($penjamin === null) {
        //     return response()->json([
        //         "code" => 404,
        //         "message" => "Data Penjamin Debitur Tidak Ditemukan"
        //     ], 404);
        // }
        // $selfie =   $penjamin->foto_selfie_penjamin;
        // $files = File::get($selfie);
        // $files = basename($selfie);
        // $nomor_so = $trans_so->nomor_so;
        // $jenis = 'penjamin';
        // $create_path = 'public/log_verifikasi/' . $nomor_so . '/' . $jenis . '/';
        // $path_to =  'public/log_verifikasi/' . $nomor_so . '/' . $jenis . '/' . $files;
        // $help = Helper::copyFile($nomor_so, $jenis, $selfie, $path_to, $create_path);
        // dd($help);
        try {
            $penjamin = Verifpenjamin::create($verif_penjamin);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_penjamin
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function updatepenjamin(Request $req, $id_pen, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $pen = Verifpenjamin::where('id_trans_so', $id_trans_so)->where('id_penjamin', $id_pen)->first();
        $trans_so = TransSO::where('id', $id_trans_so)->first();

        $penjamin = Penjamin::where('id', $id_pen)->first();


        if ($pen === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Verifikasi Penjamin Debitur Tidak Ditemukan"
            ], 404);
        }

        $verif_penjamin = array(
            "id_trans_so" => $pen->id_trans_so,
            //   "nomor_so"  => $pen->nomor_so,
            "no_ktp"    => $pen->no_ktp,
            "nama"      => empty($req->input('nama_penjamin')) ? $pen->nama : $req->input('nama_penjamin'),
            "tempat_lahir" => empty($req->input('tempat_lahir_penjamin')) ? $pen->tempat_lahir : $req->input('tempat_lahir_penjamin'),
            "tgl_lahir"     => empty($req->input('tgl_lahir_penjamin')) ? $pen->tgl_lahir : $req->input('tgl_lahir_penjamin'),
            "alamat"    => empty($req->input('alamat_penjamin')) ? $pen->alamat : $req->input('alamat_penjamin'),
            "selfie_foto" => empty($req->input('selfie_foto_penjamin')) ? $pen->selfie_foto : $req->input('selfie_foto_penjamin'),
            "trx_id"    => empty($req->input('trx_id_penjamin')) ? $pen->trx_id : $req->input('trx_id_penjamin'),
            "ref_id"    => empty($req->input('ref_id_penjamin')) ? $pen->ref_id : $req->input('ref_id_penjamin'),
            "limit_call"    => empty($req->input('limit_call_penjamin')) ? $pen->limit_call : $req->input('limit_call_penjamin'),
            "user_id" => $user_id,
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );
        //  dd($verif_penjamin);
        try {
            $penjamin = Verifpenjamin::where('id_trans_so', $id_trans_so)->where('id_penjamin', $id_pen)->update($verif_penjamin);
			 VerifpenjaminLog::create($pen->toArray());

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_penjamin
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function updateVerifCadebt(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $debitur = Debitur::select('so.id AS id_trans_so', 'so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp')->join('trans_so AS so', 'so.id_calon_debitur', 'calon_debitur.id')
            ->where('so.id', $id_trans_so)->first();

        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }

        $deb = Verifcadebt::where('id_trans_so', $id_trans_so)->first();
        $pas = Verifpasangan::where('id_trans_so', $id_trans_so)->first();
        $npwp = Verifnpwp::where('id_trans_so', $id_trans_so)->first();
        $prop = VerifProperti::where('id_trans_so', $id_trans_so)->first();
        $pen = Verifpenjamin::where('id_trans_so', $id_trans_so)->first();


        $verif_cadeb = array(
            "id_trans_so" => $deb->id_trans_so,
            //   "nomor_so"  => $deb->nomor_so,
            "no_ktp"    => $deb->no_ktp,
            "nama"      => empty($req->input('nama_cadeb')) ? $deb->nama : $req->input('nama_cadeb'),
            "tempat_lahir" => empty($req->input('tempat_lahir_cadeb')) ? $deb->tempat_lahir : $req->input('tempat_lahir_cadeb'),
            "tgl_lahir"     => empty($req->input('tgl_lahir_cadeb')) ? $deb->tgl_lahir : $req->input('tgl_lahir_cadeb'),
            "alamat"    => empty($req->input('alamat_cadeb')) ? $deb->alamat : $req->input('alamat_cadeb'),
            "selfie_foto" => empty($req->input('selfie_foto_cadeb')) ? $deb->selfie_foto : $req->input('selfie_foto_cadeb'),
            "trx_id"    => empty($req->input('trx_id_cadeb')) ? $deb->trx_id : $req->input('trx_id_cadeb'),
            "ref_id"    => empty($req->input('ref_id_cadeb')) ? $deb->ref_id : $req->input('ref_id_cadeb'),
            "limit_call"    => empty($req->input('limit_call_cadeb')) ? $deb->limit_call : $req->input('limit_call_cadeb'),
            "user_id" => $user_id,
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );

        // $verif_npwp = array(
        //     "id_trans_so" => $npwp->id_trans_so,
        //     //"nomor_so"  => $npwp->nomor_so,
        //     "npwp"      => empty($req->input('npwp_pendapatan')) ? $npwp->npwp : $req->input('npwp_pendapatan'),
        //     "nik" => empty($req->input('nik_pendapatan')) ? $npwp->nik : $req->input('nik_pendapatan'),
        //     "match_result"     => empty($req->input('match_result_pendapatan')) ? $npwp->match_result : $req->input('match_result_pendapatan'),
        //     "income"    => empty($req->input('income_pendapatan')) ? $npwp->income : $req->input('income_pendapatan'),
        //     "nama" => empty($req->input('nama_pendapatan')) ? $npwp->nama : $req->input('nama_pendapatan'),
        //     "tgl_lahir" => empty($req->input('tgl_lahir_pendapatan')) ? $npwp->tgl_lahir : $req->input('tgl_lahir_pendapatan'),
        //     "tmp_lahir" => empty($req->input('tmp_lahir_pendapatan')) ? $npwp->tmp_lahir : $req->input('tmp_lahir_pendapatan'),
        //     "trx_id"    => empty($req->input('trx_id_pendapatan')) ? $deb->trx_id : $req->input('trx_id_pendapatan'),
        //     "ref_id"    => empty($req->input('ref_id_pendapatan')) ? $deb->ref_id : $req->input('ref_id_pendapatan'),
        //     "limit_call"    => empty($req->input('limit_call_npwp')) ? $deb->limit_call : $req->input('limit_call_npwp'),
        //     "updated_at" => Carbon::now()
        // );

        // $verif_properti = array(
        //     "id_trans_so" => $debitur->id_trans_so,
        //     "id_agunan_tanah" => $debitur->id_agunan_tanah,
        //     //   "nomor_so"  => $debitur->nomor_so,
        //     "property_address"      => empty($req->input('property_address')) ? $prop->property_address : $req->input('property_address'),
        //     "property_name" => empty($req->input('property_name')) ? $prop->property_name : $req->input('property_name'),
        //     "property_building_area"     => empty($req->input('property_building_area')) ? $prop->property_building_area : $req->input('property_building_area'),
        //     "property_surface_area"    => empty($req->input('property_surface_area')) ? $prop->property_surface_area : $req->input('property_surface_area'),
        //     "property_estimation" => empty($req->input('property_estimation')) ? $prop->property_estimation : $req->input('property_estimation'),
        //     "certificate_address" => empty($req->input('certificate_address')) ? $prop->certificate_address : $req->input('certificate_address'),
        //     "certificate_id" => empty($req->input('certificate_id')) ? $prop->certificate_id : $req->input('certificate_id'),
        //     "certificate_name" => empty($req->input('certificate_name')) ? $prop->certificate_name : $req->input('certificate_name'),
        //     "certificate_type" => empty($req->input('certificate_type')) ? $prop->certificate_type : $req->input('certificate_type'),
        //     "certificate_date" => empty($req->input('certificate_date')) ? $prop->certificate_date : $req->input('certificate_date'),
        //     // "limit_call" => empty($req->input('limit_call')) ? $prop->limit_call : $req->input('limit_call'),
        //     "trx_id"    => empty($req->input('trx_id')) ? $prop->trx_id : $req->input('trx_id'),
        //     "ref_id"    => empty($req->input('ref_id')) ? $prop->ref_id : $req->input('ref_id'),
        //     "updated_at" => Carbon::now()
        // );

        try {
            // $cadeb = Verifnpwp::create($verif_npwp);

            Verifcadebt::where('id_trans_so', $id_trans_so)->update($verif_cadeb);
			 VerifCadebtLog::create($deb->toArray());
            // Verifnpwp::where('id_trans_so', $id_trans_so)->update($verif_npwp);
            // VerifProperti::where('id_trans_so', $id_trans_so)->update($verif_properti);
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array($verif_cadeb)
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
    public function updateVerifPasangan(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $debitur = Debitur::select('so.id AS id_trans_so', 'so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp')->join('trans_so AS so', 'so.id_calon_debitur', 'calon_debitur.id')
            ->where('so.id', $id_trans_so)->first();

        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }

        $deb = Verifcadebt::where('id_trans_so', $id_trans_so)->first();
        $pas = Verifpasangan::where('id_trans_so', $id_trans_so)->first();
        $npwp = Verifnpwp::where('id_trans_so', $id_trans_so)->first();
        $prop = VerifProperti::where('id_trans_so', $id_trans_so)->first();
        $pen = Verifpenjamin::where('id_trans_so', $id_trans_so)->first();

        if (empty($pas)) {
            return response()->json([
                "code" => 404,
                "message" => "Data Pasangan tidak di temukan"
            ], 404);
        }

        $verif_pasangan = array(
            "id_trans_so" => $pas->id_trans_so,
            // "nomor_so"  => $pas->nomor_so,
            "no_ktp"    => $pas->no_ktp,
            "nama"      => empty($req->input('nama_pasangan')) ? $deb->nama : $req->input('nama_pasangan'),
            "tempat_lahir" => empty($req->input('tempat_lahir_pasangan')) ? $deb->tempat_lahir : $req->input('tempat_lahir_pasangan'),
            "tgl_lahir"     => empty($req->input('tgl_lahir_pasangan')) ? $deb->tgl_lahir : $req->input('tgl_lahir_pasangan'),
            "alamat"    => empty($req->input('alamat_pasangan')) ? $deb->alamat : $req->input('alamat_pasangan'),
            "selfie_foto" => empty($req->input('selfie_foto_pasangan')) ? $deb->selfie_foto : $req->input('selfie_foto_pasangan'),
            "trx_id"    => empty($req->input('trx_id_pasangan')) ? $deb->trx_id : $req->input('trx_id_pasangan'),
            "ref_id"    => empty($req->input('ref_id_pasangan')) ? $deb->ref_id : $req->input('ref_id_pasangan'),
            "limit_call"    => empty($req->input('limit_call_pasangan')) ? $deb->limit_call : $req->input('limit_call_pasangan'),
            "user_id" => $user_id,
            "nominal" => "7000",
            "updated_at" => Carbon::now()
        );



        try {
 VerifpasanganLog::create($pas->toArray());
            Verifpasangan::where('id_trans_so', $id_trans_so)->update($verif_pasangan);
			

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_pasangan
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function updateNpwp(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $debitur = Debitur::select('so.id AS id_trans_so', 'so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp')->join('trans_so AS so', 'so.id_calon_debitur', 'calon_debitur.id')
            ->where('so.id', $id_trans_so)->first();

        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }


        $npwp = Verifnpwp::where('id_trans_so', $id_trans_so)->first();

        $verif_npwp = array(
            "id_trans_so" => $npwp->id_trans_so,
            //"nomor_so"  => $npwp->nomor_so,
            "npwp"      => empty($req->input('npwp')) ? $npwp->npwp : $req->input('npwp'),
            "nik" => empty($req->input('nik')) ? $npwp->nik : $req->input('nik'),
            "match_result"     => empty($req->input('match_result')) ? $npwp->match_result : $req->input('match_result'),
            "income"    => empty($req->input('income')) ? $npwp->income : $req->input('income'),
            "nama" => empty($req->input('nama')) ? $npwp->nama : $req->input('nama'),
            "tgl_lahir" => empty($req->input('tgl_lahir')) ? $npwp->tgl_lahir : $req->input('tgl_lahir'),
            "tmp_lahir" => empty($req->input('tmp_lahir')) ? $npwp->tmp_lahir : $req->input('tmp_lahir'),
            "trx_id"    => empty($req->input('trx_id')) ? $npwp->trx_id : $req->input('trx_id'),
            "ref_id"    => empty($req->input('ref_id')) ? $npwp->ref_id : $req->input('ref_id'),
            "id_penjamin"    => empty($req->input('id_penjamin')) ? $npwp->id_penjamin : $req->input('id_penjamin'),
            "id_pasangan"    => empty($req->input('id_pasangan')) ? $npwp->id_pasangan : $req->input('id_pasangan'),
            "limit_call"    => empty($req->input('limit_call')) ? $npwp->limit_call : $req->input('limit_call'),
            "user_id" => $user_id,
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );



        try {
            $data =  Verifnpwp::where('id_trans_so', $id_trans_so)->update($verif_npwp);
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array($verif_npwp)
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function updateNpwpPasangan(Request $req, $id_trans_so)
    {
        $user_id = $req->auth->user_id;
        $debitur = Debitur::select('so.id AS id_trans_so', 'so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp')->join('trans_so AS so', 'so.id_calon_debitur', 'calon_debitur.id')
            ->where('so.id', $id_trans_so)->first();


        $npwp = Verifnpwppasangan::where('id_trans_so', $id_trans_so)->first();
        if ($npwp === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }

        $verif_npwp = array(
            "id_trans_so" => $npwp->id_trans_so,
            //"nomor_so"  => $npwp->nomor_so,
            "npwp"      => empty($req->input('npwp')) ? $npwp->npwp : $req->input('npwp'),
            "nik" => empty($req->input('nik')) ? $npwp->nik : $req->input('nik'),
            "match_result"     => empty($req->input('match_result')) ? $npwp->match_result : $req->input('match_result'),
            "income"    => empty($req->input('income')) ? $npwp->income : $req->input('income'),
            "nama" => empty($req->input('nama')) ? $npwp->nama : $req->input('nama'),
            "tgl_lahir" => empty($req->input('tgl_lahir')) ? $npwp->tgl_lahir : $req->input('tgl_lahir'),
            "tmp_lahir" => empty($req->input('tmp_lahir')) ? $npwp->tmp_lahir : $req->input('tmp_lahir'),
            "trx_id"    => empty($req->input('trx_id')) ? $npwp->trx_id : $req->input('trx_id'),
            "ref_id"    => empty($req->input('ref_id')) ? $npwp->ref_id : $req->input('ref_id'),
            "limit_call"    => empty($req->input('limit_call')) ? $npwp->limit_call : $req->input('limit_call'),
            "user_id" => $user_id,
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );


        try {
            $data =  Verifnpwppasangan::where('id_trans_so', $id_trans_so)->update($verif_npwp);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array($verif_npwp)
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function updateNpwpPenjamin(Request $req, $id_trans_so, $id_penjamin)
    {
        $user_id = $req->auth->user_id;
        $debitur = Debitur::select('so.id AS id_trans_so', 'so.nomor_so AS nomor_so', 'calon_debitur.no_ktp AS no_ktp')->join('trans_so AS so', 'so.id_calon_debitur', 'calon_debitur.id')
            ->where('so.id', $id_trans_so)->first();

        if ($debitur === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Debitur Tidak Ditemukan"
            ], 404);
        }


        $npwp = Verifnpwppenjamin::where('id_trans_so', $id_trans_so)->where('id_penjamin', $id_penjamin)->first();
		
		 if ($npwp === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Verifikasi Tidak Ditemukan"
            ], 404);
        }

        $verif_npwp = array(
            "id_trans_so" => $npwp->id_trans_so,
            //"nomor_so"  => $npwp->nomor_so,
            "npwp"      => empty($req->input('npwp')) ? $npwp->npwp : $req->input('npwp'),
            "nik" => empty($req->input('nik')) ? $npwp->nik : $req->input('nik'),
            "match_result"     => empty($req->input('match_result')) ? $npwp->match_result : $req->input('match_result'),
            "income"    => empty($req->input('income')) ? $npwp->income : $req->input('income'),
            "nama" => empty($req->input('nama')) ? $npwp->nama : $req->input('nama'),
            "tgl_lahir" => empty($req->input('tgl_lahir')) ? $npwp->tgl_lahir : $req->input('tgl_lahir'),
            "tmp_lahir" => empty($req->input('tmp_lahir')) ? $npwp->tmp_lahir : $req->input('tmp_lahir'),
            "trx_id"    => empty($req->input('trx_id')) ? $npwp->trx_id : $req->input('trx_id'),
            "ref_id"    => empty($req->input('ref_id')) ? $npwp->ref_id : $req->input('ref_id'),
            "id_penjamin"    => empty($req->input('id_penjamin')) ? $npwp->id_penjamin : $req->input('id_penjamin'),
            "limit_call"    => empty($req->input('limit_call')) ? $npwp->limit_call : $req->input('limit_call'),
            "user_id" => $user_id,
            "nominal" => "11500",
            "updated_at" => Carbon::now()
        );



        try {
            $data =  Verifnpwppenjamin::where('id_trans_so', $id_trans_so)->where('id_penjamin', $id_penjamin)->update($verif_npwp);
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array($verif_npwp)
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }

    public function updateproperti(Request $req, $id_agunan, $id_trans_so)
    {
        $prop = Verifproperti::where('id_trans_so', $id_trans_so)->where('id_agunan_tanah', $id_agunan)->first();
        //  dd($debitur, $id_trans_so);
        if ($prop === null) {
            return response()->json([
                "code" => 404,
                "message" => "Data Verifikasi Agunan Debitur Tidak Ditemukan"
            ], 404);
        }

        $verif_properti = array(
            // "id_trans_so" => $debitur->id_trans_so,
            // "id_agunan_tanah" => $debitur->id_agunan_tanah,
            //   "nomor_so"  => $debitur->nomor_so,
            "property_address"      => empty($req->input('property_address')) ? $prop->property_address : $req->input('property_address'),
            "property_name" => empty($req->input('property_name')) ? $prop->property_name : $req->input('property_name'),
            "property_building_area"     => empty($req->input('property_building_area')) ? $prop->property_building_area : $req->input('property_building_area'),
            "property_surface_area"    => empty($req->input('property_surface_area')) ? $prop->property_surface_area : $req->input('property_surface_area'),
            "property_estimation" => empty($req->input('property_estimation')) ? $prop->property_estimation : $req->input('property_estimation'),
            "certificate_address" => empty($req->input('certificate_address')) ? $prop->certificate_address : $req->input('certificate_address'),
            "certificate_id" => empty($req->input('certificate_id')) ? $prop->certificate_id : $req->input('certificate_id'),
            "certificate_name" => empty($req->input('certificate_name')) ? $prop->certificate_name : $req->input('certificate_name'),
            "certificate_type" => empty($req->input('certificate_type')) ? $prop->certificate_type : $req->input('certificate_type'),
            "certificate_date" => empty($req->input('certificate_date')) ? $prop->certificate_date : $req->input('certificate_date'),
            // "limit_call" => empty($req->input('limit_call')) ? $prop->limit_call : $req->input('limit_call'),
            "nominal" => "11500",
            "trx_id"    => empty($req->input('trx_id')) ? $prop->trx_id : $req->input('trx_id'),
            "ref_id"    => empty($req->input('ref_id')) ? $prop->ref_id : $req->input('ref_id'),
            "updated_at" => Carbon::now()
        );
        //  dd($verif_penjamin);
        try {
            $properti = Verifproperti::where('id_trans_so', $id_trans_so)->where('id_agunan_tanah', $id_agunan)->update($verif_properti);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $verif_properti
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
    public function storeNegCase(Request $req)
    {
        $user_id = $req->auth->user_id;
        $pic = $req->pic; // From PIC middleware

        $id_area = array();
        $i = 0;
        foreach ($pic as $val) {
            $id_area[] = $val['id_area'];
            $i++;
        }

        $id_cabang = array();
        foreach ($pic as $val) {
            $id_cabang[] = $val['id_cabang'];
            $i++;
        }
        $arrrr = array();
        foreach ($pic as $val) {
            $arrrr[] = $val['jpic']['cakupan'];
            $i++;
        }

        $id_area   = $id_area;
        $id_cabang = $id_cabang;

        $scope     = $arrrr;

        //  dd($req_deb, $id_trans_so);

        // if ($verif_exists !== null) {
        //     return response()->json([
        //         "code" => 404,
        //         "message" => "Data Verifikasi Sudah Ada"
        //     ], 404);
        // }

        $data = array(
            "jenis_call" => $req->input('jenis_call'),
            "nik" => $req->input('nik'),
            "nop" => $req->input('nop'),
            "user_id" => $user_id,
            "id_area" => $id_area[0],
            "id_cabang" => $id_cabang[0],
            "messages" => $req->input('messages'),
            "rc" => $req->input('rc'),
            "created_at" => Carbon::now()
        );
        if ($data === null) {
            return response()->json([
                "code" => 404,
                "message" => "No Error Detected"
            ], 404);
        }
        try {
            $api = ApinegCase::create($data);


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
}
