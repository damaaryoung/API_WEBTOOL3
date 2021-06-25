<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\ActivityAo;
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
use App\Models\Activityhmhb;
use Image;

// use Intervention\Image\ImageManagerStatic as Image;


use App\Models\MasterActivity\Activity;
use App\Models\MasterActivity\TargetPeriodik;
use App\Models\MasterActivity\TargetApproval;
use Illuminate\Support\Facades\DB;


class ActivityAO_Controller extends BaseController
{

    public function storeAoActivity(Request $req)
    {
        $pic = $req->pic;

        $kontrak = $req->input("nomor_kontrak");
        //$trans_so = TransSO::where('nomor_so',$kontrak)->first();
        $mikro = DB::connection('web')->table('view_mikro_browse_credit')->where('no_rekening', $kontrak)->first();

        $image      = $req->input('swafoto');
        $image = str_replace("data", "", $image);
        $image1 = str_replace("image", "", $image);
        $image2 = str_replace("png", "", $image1);
        $image3 = str_replace("base64", "", $image2);
        $image4 = str_replace("jpeg", "", $image3);
        $image5 = str_replace(":/;,", "", $image4);
        //dd($image5);
        $image_rep = str_replace(' ', '+', $image5);

        $imageName = 'Activity' . '_' . 'AO' . '_' . $kontrak .'_'.Carbon::now(). '.' . 'jpg';
        $base = base64_decode($image_rep);

        $path =  (public_path() . '/Activity/AO/' . $imageName);
        //  dd($path);
        $cc = Image::make($base)->save($path);
        //dd($cc);
        // $strrepktp = str_replace('C:\xampp\htdocs\API_UANGTEMAN', '', $path_ktp);
        $path_swafoto = '/public/Activity/AO/' . $imageName;




        //     if(!empty($trans_AO)) {
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
            "plafon_pengajuan" => empty($req->input("plafon_pengajuan")) ? null : $req->input("plafon_pengajuan"),
            "hasil_survey" => empty($req->input("hasil_survey")) ? null : $req->input("hasil_survey"),
            "keterangan_survey" => empty($req->input("keterangan_survey")) ? null : $req->input("keterangan_survey"),
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

        ActivityAo::create($data);

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

       public function updateAoActivity($id, Request $req)
    {
        $pic = $req->pic;
        $kontrak = $req->input("nomor_kontrak");
        $image      = $req->input('swafoto');
        $image = str_replace("data", "", $image);
        $image1 = str_replace("image", "", $image);
        $image2 = str_replace("png", "", $image1);
        $image3 = str_replace("base64", "", $image2);
        $image4 = str_replace("jpeg", "", $image3);
        $image5 = str_replace(":/;,", "", $image4);
        //dd($image5);
        $image_rep = str_replace(' ', '+', $image5);

        $imageName = 'Activity' . '_' . 'AO' . '_' . $kontrak .'_'.Carbon::now(). '.' . 'jpg';
        $base = base64_decode($image_rep);

        $path =  (public_path() . '/Activity/AO/' . $imageName);
        //  dd($path);
        $cc = Image::make($base)->save($path);
        //dd($cc);
        // $strrepktp = str_replace('C:\xampp\htdocs\API_UANGTEMAN', '', $path_ktp);
        $path_swafoto = '/public/Activity/AO/' . $imageName;



        $act = ActivityAo::where('id', $id)->first();
        $data = array(
            "activity" => empty($req->input("activity")) ? $act->activity : $req->input("activity"),
            "nomor_so" => empty($req->input("nomor_kontrak")) ? $act->nomor_so : $req->input("nomor_kontrak"),
            "plafon_pengajuan" => empty($req->input("plafon_pengajuan")) ? $act->plafon_pengajuan : $req->input("plafon_pengajuan"),
            "hasil_survey" => empty($req->input("hasil_survey")) ? $act->hasil_survey : $req->input("hasil_survey"),
            "keterangan_survey" => empty($req->input("keterangan_survey")) ? $act->keterangan_survey : $req->input("keterangan_survey"),
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

        ActivityAo::where('id',$id)->update($data);

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
    public function indexAoActivity(Request $req)
    {
        $pic = $req->pic;

        $data = ActivityAo::paginate(10);
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

 public function indexdetailAoActivity($id, Request $req)
    {
        $pic = $req->pic;

        $data = ActivityAo::where('id', $id)->first();
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

    public function detailAoActivity($id, Request $req)
    {
        $pic = $req->pic;
$data = ActivityHmhb::select('activity_hmhb.no_kontrak AS nomor_kontrak', 'activity_hmhb.nama_debitur AS nama_debitur', 'activity_hmhb.alamat_debitur AS alamat_domisili','lpdk.plafon AS plafon_pengajuan')->join('lpdk','activity_hmhb.no_kontrak','lpdk.nomor_so')->where('jenis_pic','AO')->where('activity_hmhb.no_kontrak', $id)->first();
####################################################################################################################################
  //      $data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili','jml_pinjaman AS plafon_pengajuan')->where('no_rekening', $id)->first();
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

  public function deleteAoActivity($id, Request $req)
    {
        $pic = $req->pic;

        $data = ActivityAo::where('id', $id)->delete();
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

    public function filterAktivitasAo(Request $req)
    {

        $activity = $req->input('activity');
        // $jenis_pic = $req->input('pic');
        // //    $range_start = $req->input('range_start');
        // //  $range_end = $req->input('range_end');
        // // dd($nomor_so);
        $filter = ActivityAo::get();

        if ($activity === 'survey') {
            $filter = ActivityAo::select('id', 'tanggal', 'nama_debitur', 'alamat_domisili', 'plafon_pengajuan', 'plafon_pengajuan', 'hasil_survey', 'keterangan_survey', 'swafoto')->where('activity', 'SURVEY')->paginate(10);
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


        if ($activity === 'visit') {
            $filter = ActivityAo::select('id', 'tanggal', 'nomor_so', 'nama_debitur', 'alamat_domisili', 'hasil_visit', 'swafoto')->where('activity', 'VISIT CGC')->paginate(10);
            // $data = array();
            // foreach ($filter as $key => $val) {
            //     $data[$key]['tanggal']       = $val->tanggal;
            //     $data[$key]['nama_mb']       = $val->nama_mb;
            //     $data[$key]['alamat_domisili']       = $val->alamat_domisili;
            //     $data[$key]['hasil_maintain']       = $val->hasil_maintain;
            //     $data[$key]['swafoto']       = $val->swafoto;
            // }
        }
        if ($activity === 'promosi') {
            $filter = ActivityAo::select('id', 'tanggal', 'longitude', 'latitude', 'swafoto')->where('activity', 'PROMOSI')->paginate(10);
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

    public function viewNasabahMikro(Request $req)
    {

$get_data = ActivityHmhb::select('no_kontrak AS nomor_kontrak', 'nama_debitur AS nama_debitur', 'alamat_debitur AS alamat_domisili','lpdk.plafon AS plafon_pengajuan')->join('lpdk','activity_hmhb.no_kontrak','lpdk.nomor_so')->where('jenis_pic','AO')->paginate(10);
##################################################################################################################################
    //    $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili','jml_pinjaman AS plafon_pengajuan')->paginate(10);



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

  public function viewNasabahMikroKontrak(Request $req)
    {


        $get_kontrak = $req->input('no_kontrak');
        if (!empty($get_kontrak)) {
  $get_data = ActivityHmhb::select('activity_hmhb.no_kontrak AS nomor_kontrak', 'activity_hmhb.nama_debitur AS nama_debitur', 'activity_hmhb.alamat_debitur AS alamat_domisili', 'lpdk.plafon AS plafon_pengajuan')->join('lpdk','activity_hmhb.no_kontrak','lpdk.nomor_so')->where('no_kontrak', 'LIKE', "%{$get_kontrak}%")->paginate(10);
#####################################################################################################################################
   //         $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili', 'jml_pinjaman AS plafon_pengajuan')->where('no_rekening', 'LIKE', "%{$get_kontrak}%")->paginate(10);
        }

        if (empty($get_kontrak)) {
   $get_data = ActivityHmhb::select('activity_hmhb.no_kontrak AS nomor_kontrak', 'activity_hmhb.nama_debitur AS nama_debitur', 'activity_hmhb.alamat_debitur AS alamat_domisili', 'lpdk.plafon AS plafon_pengajuan')->join('lpdk','activity_hmhb.no_kontrak','lpdk.nomor_so')->where('activity_hmhb.jenis_pic','AO')->where('activity_hmhb.activity','SURVEY')->paginate(10);
####################################################################################################################################
       //     $get_data = DB::connection('web')->table('view_mikro_browse_credit')->select('no_rekening AS nomor_kontrak', 'nama_nasabah AS nama_debitur', 'alamat AS alamat_domisili', 'jml_pinjaman AS plafon_pengajuan')->paginate(10);
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


        $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->paginate(10);



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


        $kode = $req->input('kode');

        $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->where('deskripsi_group5', '=', $kode)->get();



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
    public function detailidMB($id, Request $req)
    {


        //   $kode = $req->input('kode_mb');

        $get_data = DB::connection('web')->table('view_mb_kodegroup5')->select('kode_group5 AS kode_mb', 'deskripsi_group5 AS nama_mb', 'alamat_group5 AS alamat_domisili')->where('kode_group5', '=', $id)->first();



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
}
