<?php

namespace App\Http\Controllers\Transaksi;

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
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;

use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use Illuminate\Support\Facades\DB;


class MasterActivityController extends BaseController
{

    public function storeAktivitas ($req Request) {
 $pic = $req->pic;

 	$data = array(
 	"nama_jenis" => $req->input("pic"),
 	"nama_aktivitas" => $req->input("nama_aktivitas"),
 	"target_aktivitas" => $req->input("target_aktivitas"),
 	"durasi_aktivitas" => $req->input("durasi_aktivitas"),
 	);


 	  if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

        Activity::create($data);

          try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data Aktivitas Berhasil Di Input',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

 public function storeTargetPeriodik ($req Request) {
 $pic = $req->pic;

 	$data = array(
 	"pilih_bulan" => $req->input("pilih_bulan"),
 	"periode" => $req->input("periode"),
 	"target_persen" => $req->input("target_persen"),

 	);


 	  if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

TargetPeriodik::create($data);

          try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data Aktivitas Berhasil Di Input',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }

public function storeTargetApprovalPeriodik ($req Request) {
 $pic = $req->pic;

 	$data = array(
 	"bulan_periode" => $req->input("pilih_bulan"),
 	"periode" => $req->input("periode"),
 	"target_persen" => $req->input("target_persen"),

 	);


 	  if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

TargetApproval::create($data);
          try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Data Aktivitas Berhasil Di Input',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }



}
