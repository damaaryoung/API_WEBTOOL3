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
use App\Models\Activityhmhb;
// use Intervention\Image\Image;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use Illuminate\Support\Facades\DB;


class ActivitySO_Controller extends BaseController
{

    public function storeSoActivity(Request $req)
    {
        $pic = $req->pic;

        $kontrak = $req->input("nomor_kontrak");
        //$trans_so = TransSO::where('nomor_so',$kontrak)->first();
        $mikro = DB::connection('web')->table('view_mikro_browse_credit')->where('no_rekening', $kontrak)->first();

 $nama_mb = $req->input("nama_mb");
        $image      = $req->input('swafoto');
        $image = str_replace("data", "", $image);
        $image1 = str_replace("image", "", $image);
        $image2 = str_replace("png", "", $image1);
        $image3 = str_replace("base64", "", $image2);
        $image4 = str_replace("jpeg", "", $image3);
        $image5 = str_replace(":/;,", "", $image4);
        //dd($image5);
        $image_rep = str_replace(' ', '+', $image5);

        if (!empty($kontrak) || empty($nama_mb)) {
            $imageName = 'Activity' . '_' . 'SO' . '_' . $kontrak . '_' . Carbon::now() . '.' . 'jpg';
        } else if (empty($kontrak) || !empty($nama_mb)) {
            $imageName = 'Activity' . '_' . 'SO' . '_' . $nama_mb . '_' . Carbon::now() . '.' . 'jpg';
        } else {
            $imageName = null;
        }
        $base = base64_decode($image_rep);

        $path =  (public_path() . '/Activity/SO/' . $imageName);
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

    public function updateSo($id, Request $req)
    {
        $pic = $req->pic;

        $kontrak = $req->input("nomor_kontrak");
$nama_mb = $req->input("nama_mb");
        $image      = $req->input('swafoto');
        $image = str_replace("data", "", $image);
        $image1 = str_replace("image", "", $image);
        $image2 = str_replace("png", "", $image1);
        $image3 = str_replace("base64", "", $image2);
        $image4 = str_replace("jpeg", "", $image3);
        $image5 = str_replace(":/;,", "", $image4);
        //dd($image5);
        $image_rep = str_replace(' ', '+', $image5);

        if (!empty($kontrak) || empty($nama_mb)) {
            $imageName = 'Activity' . '_' . 'SO' . '_' . $kontrak . '_' . Carbon::now() . '.' . 'jpg';
        } else if (empty($kontrak) || !empty($nama_mb)) {
            $imageName = 'Activity' . '_' . 'SO' . '_' . $nama_mb . '_' . Carbon::now() . '.' . 'jpg';
        } else {
            $imageName = null;
        }
        $base = base64_decode($image_rep);

        $path =  (public_path() . '/Activity/SO/' . $imageName);
        //  dd($path);
        $cc = Image::make($base)->save($path);
        //dd($cc);
        // $strrepktp = str_replace('C:\xampp\htdocs\API_UANGTEMAN', '', $path_ktp);
        $path_swafoto = '/public/Activity/SO/' . $imageName;


        $act = ActivitySo::where('id', $id)->first();
        $data = array(
	    "id" => $id,
            "activity" => empty($req->input("activity")) ? $act->activity : $req->input("activity"),
            "nomor_so" => empty($req->input("nomor_kontrak")) ? $act->nomor_so : $req->input("nomor_kontrak"),
            "nama_mb" => empty($req->input("nama_mb")) ? $act->nama_mb : $req->input("nama_mb"),
            "hasil_maintain" => empty($req->input("hasil_maintain")) ? $act->hasil_maintain : $req->input("hasil_maintain"),
            "nama_debitur" => empty($req->input("nama_debitur")) ? $act->nama_debitur : $req->input("nama_debitur"),
            "alamat_domisili" => empty($req->input("alamat_domisili")) ? $act->alamat_domisili : $req->input("alamat_domisili"),
            "hasil_visit" => empty($req->input("hasil_visit")) ? $act->hasil_visit : $req->input("hasil_visit"),
            "swafoto" => empty($path_swafoto) ? $act->swafoto : $path_swafoto,
            "latitude" => empty($req->input("latitude")) ? $act->latitude : $req->input("latitude"),
            "longitude" => empty($req->input("longitude")) ? $act->longitude : $req->input("longitude")
        );


        if ($data === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'Silahkan isi Data Terlebih Dahulu'
            ]);
        }

        ActivitySo::where('id', $id)->update($data);

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
    public function indexSoActivity(Request $req)
    {
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

 public function indexdetailSoActivity($id, Request $req)
    {
        $pic = $req->pic;

        $data = ActivitySo::where('id', $id)->first();
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

 public function deleteSoActivity($id, Request $req)
    {
        $pic = $req->pic;

        $data = ActivitySo::where('id', $id)->delete();
        try {
            return response()->json([
                'message' => 'Data Berhasil Terhapus'
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

    public function detailSoActivity($id, Request $req)
    {
        $pic = $req->pic;

 $data = Activityhmhb::select('no_kontrak AS nomor_kontrak', 'nama_debitur AS nama_debitur', 'alamat_debitur AS alamat_domisili')->where('no_kontrak', $id)->first();
#################################################################################################################################
   //     $data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili')->where('no_rekening', $id)->first();
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

    public function filterAktivitasSo(Request $req)
    {

        $activity = $req->input('activity');
        // $jenis_pic = $req->input('pic');
        // //    $range_start = $req->input('range_start');
        // //  $range_end = $req->input('range_end');
        // // dd($nomor_so);
        $filter = ActivitySo::get();

        if ($activity === 'formRO') {
            $filter = ActivitySo::select('id','tanggal', 'nomor_so', 'nama_debitur', 'alamat_domisili', 'hasil_visit', 'swafoto')->where('activity', 'VISIT RO')->paginate(10);
            // $data = array();
            // foreach ($filter as $key => $val) {
            //     $data[$key]['tanggal']       = $val->tanggal;
            //     $data[$key]['nomor_so']       = $val->nomor_so;
            //     $data[$key]['nama_debitur']       = $val->nama_debitur;
            //     $data[$key]['alamat_domisili']       = $val->alamat_domisili;
            //     $data[$key]['hasil_visit']       = $val->hasil_visit;
            //     $data[$key]['swafoto']       = $val->swafoto;
            // }
        }


        if ($activity === 'formMB') {
            $filter = ActivitySo::select('id','tanggal', 'nama_mb', 'alamat_domisili', 'hasil_maintain', 'swafoto')->where('activity', 'MAINTAIN MB')->paginate(10);
            // $data = array();
            // foreach ($filter as $key => $val) {
            //     $data[$key]['tanggal']       = $val->tanggal;
            //     $data[$key]['nama_mb']       = $val->nama_mb;
            //     $data[$key]['alamat_domisili']       = $val->alamat_domisili;
            //     $data[$key]['hasil_maintain']       = $val->hasil_maintain;
            //     $data[$key]['swafoto']       = $val->swafoto;
            // }
        }
        if ($activity === 'formPromosi') {
            $filter = ActivitySo::select('id','tanggal', 'longitude', 'latitude', 'swafoto')->where('activity', 'PROMOSI')->paginate(10);
            // $data = array();
            // foreach ($filter as $key => $val) {
            //     $data[$key]['tanggal']       = $val->tanggal;
            //     $data[$key]['longitude']       = $val->longitude;
            //     $data[$key]['latitude']       = $val->latitude;
            //     $data[$key]['swafoto']       = $val->swafoto;
            // }
        }


        if (empty($filter)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Aktivitas kosong'
            ], 404);
        }

      //  try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($cek_sertifikat),
                'data'   => $filter
            ], 200);
     //   } catch (\Exception $e) {
        //    return response()->json([
          //      "code"    => 501,
           //     "status"  => "error",
          //      "message" => $e
        //    ], 501);
      //  }
    }

    public function viewNasabahMikro(Request $req)
    {

       $get_data = Activityhmhb::select('no_kontrak AS nomor_kontrak', 'nama_debitur AS nama_debitur', 'alamat_debitur AS alamat_domisili')->paginate(10);
        // $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili')->paginate(10);


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

  public function viewNasabahMikrobyId(Request $req)
    {

        $get_kontrak = $req->input('no_kontrak');
$param = $req->input('param');

$get_mb = $req->input('kode_mb');
// dd($param == 'mb');
 if (!empty($get_kontrak) && empty($get_mb) || $param === 'ro') {
            $get_data = Activityhmhb::select('no_kontrak AS nomor_kontrak', 'nama_debitur AS nama_debitur', 'alamat_debitur AS alamat_domisili')->where('no_kontrak', 'LIKE', "%{$get_kontrak}%")->paginate(10);
            ########################################################################################################
            // $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili')->where('no_rekening', 'LIKE', "%{$get_kontrak}%")->paginate(10);
        } elseif (!empty($get_mb) || empty($get_kontrak) || $param === 'mb') {
            $get_data = Activityhmhb::select('kode_mb as kode_mb','nama_mb AS nama_mb', 'alamat_mb AS alamat_domisili')->where('nama_mb', 'LIKE', "%{$get_mb}%")->paginate(10);
            #################################################################################################
            // $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->where('deskripsi_group5', 'LIKE', "%{$get_mb}%")->paginate(10);
        } elseif (empty($get_mb) || empty($get_kontrak) || $param === 'mb') {
            $get_data = Activityhmhb::select('kode_mb AS kode_mb','nama_mb AS nama_mb', 'alamat_mb AS alamat_domisili')->paginate(10);
            ###########################################################################################
            // $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->paginate(10);
        } elseif (empty($get_mb) || empty($get_kontrak) || $param === 'ro') {
            $get_data = Activityhmhb::select('no_kontrak AS nomor_kontrak', 'nama_debitur AS nama_debitur', 'alamat_debitur AS alamat_domisili')->paginate(10);
            ############################################################################################
            // $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili')->paginate(10);
        } else {
            $get_data = null;
        }



       




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

    public function viewMB(Request $req)
    {
 $get_data = ActivityHmhb::select('nama_mb AS nama_mb', 'alamat_mb AS alamat_domisili')->paginate(10);

###############################################################################################################################
  //      $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->paginate(10);



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
    public function detailMB(Request $req)
    {


        $kode = $req->input('kode_mb');

 $get_data = ActivityHmhb::select('kode_mb as kode_mb','nama_mb AS nama_mb', 'alamat_mb AS alamat_domisili')->where('kode_mb', 'LIKE', "%{$kode}%")->paginate(10);

##############################################################################################################################
        $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->where('deskripsi_group5', 'LIKE', "%{$kode}%")->paginate(10);



        if (empty($get_data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Mitra Bisnis Tidak Ditemukan'
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
public function detailidMB(Request $req)
    {


           $kode = $req->input('kode_mb');

  $get_data = ActivityHmhb::select('kode_mb AS kode_mb','nama_mb AS nama_mb', 'alamat_mb AS alamat_domisili')->where('kode_mb', '=', $kode)->first();
#############################################################################################################################
      //  $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->where('kode_group5', '=', $kode)->first();



        if (empty($get_data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Mitra Bisnis Tidak Ditemukan',
		'data' => array('kode_mb' => 'test', 'nama_mb' => 'test', 'alamat_domisili' => 'test')
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
