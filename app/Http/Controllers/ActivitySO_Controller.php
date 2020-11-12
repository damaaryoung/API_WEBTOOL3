<?php

namespace App\Http\Controllers;

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
use App\Models\Transaksi\LogRekomCA;
use App\Models\Transaksi\LogRingAnalisa;
use App\Models\v2\Target_lending;
use App\Models\master_nilai;
use App\Models\master_transaksi;
use Image;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use Illuminate\Support\Facades\DB;


class ActivitySO_Controller extends BaseController
{

    public function storeSoActivity (Request $req) {
 $pic = $req->pic;

 $kontrak = $req->input("nomor_kontrak");
//$trans_so = TransSO::where('nomor_so',$kontrak)->first();
   $mikro = DB::connection('web')->table('view_mikro_browse_credit')->where('no_rekening',$kontrak)->first();

   $image      = $req->input('swafoto');
    $image = str_replace("data", "", $image);
    $image1 = str_replace("image", "", $image);
    $image2 = str_replace("png", "", $image1);
    $image3 = str_replace("base64", "", $image2);
    $image4 = str_replace("jpeg", "", $image3);
    $image5 = str_replace(":/;,", "", $image4);
//dd($image5);
        $image_rep = str_replace(' ', '+', $image5);

           $imageName = 'Activity' . '_' . 'SO' . '_' . $kontrak . '.' . 'jpg';
        $base = base64_decode($image_rep);
        
        $path =  (public_path() .'/Activity/SO/'. $imageName);
      //  dd($path);
        $cc = Image::make($base)->save($path);
//dd($cc);
        // $strrepktp = str_replace('C:\xampp\htdocs\API_UANGTEMAN', '', $path_ktp);
        $path_swafoto = '/public/Activity/SO/' . $imageName;




//     if(!empty($trans_so)) {
//   $path = 'public/activity/so/' . $kontrak;
// } elseif (!empty($trans_so)) {
// $path = 'public/activity/so/' . $kontrak;
// }
//         // $check_lamp_sertifikat      = $check_lamp->lampiran_sertifikat;

//          if ($file = $req->file('swafoto')) {
//             $name = 'activity.';
//             $check = 'null';
           
//             $lamp_activity = Helper::uploadImg($check, $file, $path, $name);
//         } else {
//             $lamp_activity = null;
//         }

    $data = array(
    "activity" => empty($req->input("activity")) ? null : $req->input("activity"),
    "nomor_so" => empty($req->input("nomor_kontrak")) ? null : $req->input("nomor_kontrak"),
    "nama_mb" => empty($req->input("nama_mb")) ? null : $req->input("nama_mb"),
    "hasil_maintain" => empty($req->input("hasil_maintain")) ? null : $req->input("hasil_maintain"),
    "nama_debitur" => empty($req->input("nama_debitur")) ? null : $req->input("nama_debitur"),
    "alamat_domisili" => empty($req->input("alamat_domisili")) ? null : $req->input("alamat_domisili"),
    "hasil_visit" => empty($req->input("hasil_visit")) ? null : $req->input("hasil_visit"),
    "swafoto" => $path_swafoto,
    "latitude" => empty($req->input("latitude")) ? null : $req->input("latitude"),
    "longitude" => empty($req->input("longitude")) ? null : $req->input("longitude")
    );


      if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

        ActivitySo::create($data);

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

public function updateSoActivity ($id, Request $req) {
 $pic = $req->pic;


$act = Activity::where('id',$id)->first();
    $data = array(
    "activity" => empty($req->input("activity")) ? $act->activity : $req->input("activity"),
    "nomor_so" => empty($req->input("nomor_kontrak")) ? $act->nomor_so : $req->input("nomor_kontrak"),
    "nama_mb" => empty($req->input("nama_mb")) ? $act->nama_mb : $req->input("nama_mb"),
    "hasil_maintain" => empty($req->input("hasil_maintain")) ? $act->hasil_maintain : $req->input("hasil_maintain"),
    "nama_debitur" => empty($req->input("nama_debitur")) ? $act->nama_debitur : $req->input("nama_debitur"),
    "alamat_domisili" => empty($req->input("alamat_domisili")) ? $act->alamat_domisili : $req->input("alamat_domisili"),
    "hasil_visit" => empty($req->input("hasil_visit")) ? $act->hasil_visit : $req->input("hasil_visit"),
  //  "swafoto" => empty($req->input("swafoto")) ? $act->alamat_domisili : $req->input("swafoto"),
    "latitude" => empty($req->input("latitude")) ? $act->latitude : $req->input("latitude"),
    "longitude" => empty($req->input("longitude")) ? $act->longitude : $req->input("longitude")
    );


      if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

        ActivitySo::create($data);

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
 public function indexSoActivity (Request $req)  {
 $pic = $req->pic;

   $data = ActivitySo::paginate(10);
          try {
            return response()->json([
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

public function detailSoActivity ($id,Request $req)  {
 $pic = $req->pic;

   $data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak','nama_nasabah AS nama_debitur','alamat AS alamat_domisili')->where('no_rekening',$id)->first();
          try {
            return response()->json([
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

public function filterAktivitasSo(Request $req) {


       $jenis_pic = $req->input('pic');
    //    $range_start = $req->input('range_start');
      //  $range_end = $req->input('range_end');
   // dd($nomor_so);
  
if (!empty($jenis_pic)) {
$filter = Activity::where('nama_jenis','=',$jenis_pic)
->paginate(10);
 } elseif ($jenis_pic === '0') {
$filter = Activity::paginate(10);
 } else {
    $jenis_pic = null;
 }

 if (empty($filter)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas kosong'
            ], 404);
        }

try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $filter
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }


}

public function viewNasabahMikro(Request $req) {


      $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak','nama_nasabah AS nama_debitur','alamat AS alamat_domisili')->get();
  


 if (empty($get_data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas kosong'
            ], 404);
        }

try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $get_data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }


}

public function viewMB(Request $req) {


      $get_data = DB::connection('web')->table('view_mb_kodegroup3')->select('kode_group3 AS kode_mb','deskripsi_group3 AS nama_mb')->get();
  


 if (empty($get_data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas kosong'
            ], 404);
        }

try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $get_data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }


}
public function detailMB($kode, Request $req) {


      $get_data = DB::connection('web')->table('view_mb_kodegroup3')->select('kode_group3 AS kode_mb','deskripsi_group3 AS nama_mb')->where('kode_group3',$kode)->first();
  


 if (empty($get_data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas kosong'
            ], 404);
        }

try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $get_data
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