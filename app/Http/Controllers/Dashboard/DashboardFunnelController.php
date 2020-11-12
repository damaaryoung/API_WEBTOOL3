<?php

namespace App\Http\Controllers\Dashboard;

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
use Image;
use Illuminate\Support\Facades\DB;

class DashboardFunnelController extends BaseController
{

     public function index(Request $req)
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

        $query_dir = TransAO::with('so', 'pic', 'cabang');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

$leads = TransSO::get();
        // $TransAO = TransAO::select('trans_so.id',
        //      // 'calon_debitur.nama_lengkap',
        //    // 'calon_debitur.alamat_ktp',
        //     'agunan_tanah.no_sertifikat','agunan_tanah.tgl_ukur_sertifikat','agunan_tanah.nama_pemilik_sertifikat','agunan_tanah.alamat',
        //     // 'agunan_tanah.tanggal_sertifikat',
        //     'agunan_tanah.luas_tanah',
        //     'trans_so.nomor_so',
        //     // 'trans_so.cabang',
        //     // 'trans_so.plafon',
        //     // 'agunan_tanah.status'
        // )->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')->join('trans_so','trans_ao.id_trans_so','=','trans_so.id')->get();

 // dd($trans_so);
        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di Agunan Tanah masih kosong'
            ], 404);
        }

//          foreach ($query as $key => $val) {
// $debitur = TransSO::select('calon_debitur.nama_lengkap','calon_debitur.alamat_ktp')->join('calon_debitur','trans_so.id_calon_debitur','=','calon_debitur.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->where('trans_ao.id_agunan_tanah',$val->id_agunan_tanah)->get();
// dd($val->id_agunan_tanah);
//  $data[$key] = [
//                 'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
//                 'nama_debitur'       => $debitur->nama_lengkap,
//                 'alamat_debitur'       => $debitur->alamat_ktp,
//                 'no_shm'       => $debitur->no_sertifikat,
//                 'no_suratukur'  => $debitur->tgl_ukur_sertifikat,
//                 'nama_pemilik_sertifikat'  => $debitur->nama_pemilik_sertifikat,
//                   'alamat_sertifikat'  => $debitur->alamat,
//                   'tanggal_sertifikat'  => $debitur->tanggal_sertifikat,
//                   'luas_tanah' => $debitur->luas_tanah,
//                 // 'nomor_so'        => $val->so['nomor_so'],
//                 // 'cabang'        => $val->so['cabang'],
//                 'plafon' => $val->so['plafon'],
//                 'status'  => $debitur->alamat
//             ];
//          }
  try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($trans_so),
                // 'data'   => $trans_so
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
      $pic = $req->pic;

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

$cek_sertifikat = Debitur::select('trans_so.nomor_so'  ,'calon_debitur.nama_lengkap','calon_debitur.alamat_ktp','agunan_tanah.no_sertifikat AS no_shm','agunan_tanah.tgl_ukur_sertifikat AS nomor_surat_ukur','agunan_tanah.nama_pemilik_sertifikat AS nama_pemilik_sertifikat','agunan_tanah.alamat','agunan_tanah.luas_tanah',
    'agunan_tanah.asli_ajb','agunan_tanah.asli_imb','agunan_tanah.asli_sppt','agunan_tanah.asli_sppt','agunan_tanah.asli_imb','asli_skmht','agunan_tanah.asli_gambar_denah','agunan_tanah.asli_imb','agunan_tanah.asli_surat_roya','agunan_tanah.asli_sht','agunan_tanah.asli_stts','agunan_tanah.asli_ssb',
    'agunan_tanah.imb','agunan_tanah.sppt','agunan_tanah.no_sppt','agunan_tanah.sppt_tahun','agunan_tanah.skmht','agunan_tanah.gambar_denah','agunan_tanah.surat_roya','agunan_tanah.sht','agunan_tanah.no_sht','agunan_tanah.sht_propinsi','agunan_tanah.sht_kota','agunan_tanah.stts','agunan_tanah.stts_tahun','agunan_tanah.ssb','agunan_tanah.ssb_atas_nama','agunan_tanah.lain_lain'
)->join('trans_so','trans_so.id_calon_debitur','=','calon_debitur.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')
 ->where('trans_so.id',$id)
->get();

 if (empty($cek_sertifikat)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Sertifikat kosong'
            ], 404);
        }

try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($cek_sertifikat),
                'data'   => $cek_sertifikat
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }



    }

    public function getAllLog(Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $log = LogRekomCA::get();
        $logRingAnalisa = LogRingAnalisa::get();

        $data = array(
            'log_rekomendasi_CA' => $log,
            'log_ringkasan_analisa' => $logRingAnalisa
        );

        if (empty($log) || empty($logRingAnalisa)) {
            return response()->json([
                'code'   => 404,
                'status' => 'not found',
                'data'   => 'data log kosong'
            ], 200);
        }
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
}
