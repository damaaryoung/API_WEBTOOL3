<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Activity as ModelsActivity;
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
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;

use App\Models\TeleSales;
use App\Models\Activityhmhb;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use Illuminate\Support\Facades\DB;


class ActivityHMController extends BaseController
{
    public function store(Request $req)
    {
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }
        $data = array(
            "jenis_pic" => $req->input("pic"),
            "activity" => $req->input("activity"),
            "tgl_assign" => Carbon::now(),
            "no_kontrak" => $req->input('no_kontrak'),
            "nama_mb" => $req->input('nama_mb'),
            "nama_debitur" => $req->input('nama_debitur'),
            "alamat_mb" => $req->input('alamat_mb'),
            "alamat_debitur" => $req->input('alamat_debitur'),
            "nama_pic" => $req->input('nama_pic'),
'produk' => $req->input('produk'),
  'new_plafond' => $req->input('new_plafond'),
  'new_angsuran' => $req->input('new_angsuran'),
  'new_tenor' => $req->input('new_tenor'),
  'baki_debet' => $req->input('baki_debet') 
        );
        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Inputan kosong'
            ], 404);
        }

        try {
            Activityhmhb::create($data);
            return response()->json([
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

    public function update(Request $req, $id)
    {
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }
        $get_data = Activityhmhb::where('id', $id)->first();

 if (empty($get_data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Assignment HM/HB kosong'
            ], 404);
        }
        $data = array(
            "jenis_pic" => empty($req->input("pic")) ? $get_data->jenis_pic : $req->input("pic"),
            "activity" => empty($req->input("activity")) ? $get_data->activity : $req->input("activity"),
            "tgl_assign" => Carbon::now(),
            "no_kontrak" => empty($req->input('no_kontrak')) ? $get_data->no_kontrak : $req->input('no_kontrak'),
            "nama_mb" => empty($req->input('nama_mb')) ? $get_data->nama_mb : $req->input('nama_mb'),
            "nama_debitur" => empty($req->input('nama_debitur')) ? $get_data->nama_debitur : $req->input('nama_debitur'),
            "alamat_mb" => empty($req->input('alamat_mb')) ? $get_data->alamat_mb : $req->input('alamat_mb'),
            "alamat_debitur" => empty($req->input('alamat_debitur')) ? $get_data->alamat_debitur : $req->input('alamat_debitur'),
            "nama_pic" => empty($req->input('nama_pic')) ? $get_data->nama_pic : $req->input('nama_pic')
        );
        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Inputan kosong'
            ], 404);
        }

        try {
            Activityhmhb::where('id', $id)->update($data);
            return response()->json([
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

    public function indexActivityHmHb(Request $req)
    {
        $jenis_pic = $req->input("jenis_pic");
  $jenis_aktivitas = $req->input("jenis_aktivitas");
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }

        if ($jenis_pic === 'SO' && $jenis_aktivitas === 'VISIT RO') {
            $data = Activityhmhb::where('jenis_pic', $jenis_pic)->where('activity', 'VISIT RO')->orderBy('tgl_assign', 'desc')->paginate(10);
        } elseif ($jenis_pic === 'SO' && $jenis_aktivitas === 'MAINTAIN MB') {
            $data = Activityhmhb::where('jenis_pic', $jenis_pic)->where('activity', 'MAINTAIN MB')->orderBy('tgl_assign', 'desc')->paginate(10);
        } elseif ($jenis_pic === 'AO' && $jenis_aktivitas === 'SURVEY') {
            $data = Activityhmhb::where('jenis_pic', $jenis_pic)->where('activity', 'SURVEY')->orderBy('tgl_assign', 'desc')->paginate(10);
        } elseif ($jenis_pic === 'AO' && $jenis_aktivitas === 'VISIT CGC') {
            $data = Activityhmhb::where('jenis_pic', $jenis_pic)->where('activity', 'VISIT CGC')->orderBy('tgl_assign', 'desc')->paginate(10);
        } elseif ($jenis_pic === 'AO' && $jenis_aktivitas === 'TELESALES') {
            $data = Activityhmhb::where('jenis_pic', $jenis_pic)->where('activity', 'TELESALES')->orderBy('tgl_assign', 'desc')->paginate(10);
        }

 else {
            $data = null;
        }

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Trans So Kosong'
            ], 404);
        }
        try {
            return response()->json([
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
 public function indexdetailActivityHMHB(Request $req, $id)
    {

        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }
        $data = Activityhmhb::where('id', $id)->first();

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Trans So Kosong'
            ], 404);
        }
        try {
            return response()->json([
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

    public function indexApproveCCHM(Request $req)
    {
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }
        $data = TransSO::select('trans_so.id AS id_trans', 'calon_debitur.id AS id_debitur', 'trans_so.nomor_so', 'calon_debitur.nama_lengkap','calon_debitur.alamat_domisili AS alamat_debitur')->join('calon_debitur', 'trans_so.id_calon_debitur', 'calon_debitur.id')->join('lpdk','trans_so.id','lpdk.trans_so')->where('trans_so.status_hm', 1)->paginate(10);

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Trans So Kosong'
            ], 404);
        }
        try {
            return response()->json([
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

     public function detailApproveCCHM(Request $req, $id)
    {
        // $id_trans = $req->input('trans_so');
        $data = TransSO::select('trans_so.id AS id_trans', 'calon_debitur.id AS id_debitur', 'trans_so.nomor_so', 'calon_debitur.nama_lengkap')->join('calon_debitur', 'trans_so.id_calon_debitur', 'calon_debitur.id')->where('trans_so.id', $id)->where('trans_so.status_hm', 1)->first();

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Trans So kosong'
            ], 404);
        }
        try {
            return response()->json([
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
    public function indexKodeSOAO(Request $req)
    {
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }
        // dd($arrr);
        $jenis_pic = $req->input('jenis_pic');
        $kode_pic = $req->input('kode_pic');

        $nama = $req->input('nama');
        //  dd($nama);
        if ($jenis_pic === 'SO' && empty($nama)) {
            $data = PIC::select('id', 'user_id', 'nama')->where('id_mj_pic', 1)->whereIn('id_cabang', $cabang)->paginate(10);
        } elseif ($jenis_pic === 'AO' && empty($nama)) {
            $data = PIC::select('id', 'user_id', 'nama')->where('id_mj_pic', 2)->whereIn('id_cabang', $cabang)->paginate(10);
        } elseif ($jenis_pic === 'SO' && $nama) {
            $data = PIC::select('id', 'user_id', 'nama')->where('id_mj_pic', 1)->where('nama', 'LIKE', "%{$nama}%")->whereIn('id_cabang', $cabang)->paginate(10);
        } elseif ($jenis_pic === 'AO' && $nama) {
            $data = PIC::select('id', 'user_id', 'nama')->where('id_mj_pic', 2)->where('nama', 'LIKE', "%{$nama}%")->whereIn('id_cabang', $cabang)->paginate(10);
        } else {
            $data = null;
        }

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Index AO - SO kosong'
            ], 404);
        }
        try {
            return response()->json([
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

    public function detailKodeSOAO(Request $req, $id_pic)
    {
        // $id_pic = $req->input('id_pic');
        $pic = $req->pic; // From PIC middleware
        $user_id = $req->auth;
        $cabang = array();
        $i = 0;
        foreach ($pic as $val) {
            $cabang[] = $val['id_cabang'];
            $i++;
        }
        // dd($arrr);
        $jenis_pic = $req->input('jenis_pic');
        $kode_pic = $req->input('kode_pic');


        $data = PIC::select('id', 'user_id', 'nama')->where('id', $id_pic)->first();


        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Detail SO - AO dengan id pic' . $id_pic . ' ' . 'tidak ditemukan'
            ], 404);
        }
        try {
            return response()->json([
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
 public function deleteActivity(Request $req, $id)
    {
 $data = Activityhmhb::where('id', $id)->first();
        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas dengan id ' . $id . ' ' . 'tidak ditemukan'
            ], 404);
        }
        try {
            $data = Activityhmhb::where('id', $id)->delete();
            return response()->json([
                'message'   => 'Data Berhasil Dihapus'
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
