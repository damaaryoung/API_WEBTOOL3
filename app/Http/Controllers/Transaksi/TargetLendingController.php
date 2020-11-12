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
use Image;
use Illuminate\Support\Facades\DB;


class TargetLendingController extends BaseController
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

$target = Target_lending::paginate(10);



 // dd($trans_so);
        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di Agunan Tanah masih kosong'
            ], 404);
        }


   try {
            return response()->json([
               // 'code'   => 200,
              //  'status' => 'success',
               // 'count'  => sizeof($target),
                'data'   => $target
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

// $cek_sertifikat = Debitur::select('trans_so.nomor_so'  ,'calon_debitur.nama_lengkap','calon_debitur.alamat_ktp','agunan_tanah.no_sertifikat AS no_shm','agunan_tanah.tgl_ukur_sertifikat AS nomor_surat_ukur','agunan_tanah.nama_pemilik_sertifikat AS nama_pemilik_sertifikat','agunan_tanah.alamat','agunan_tanah.luas_tanah',
//     'agunan_tanah.asli_ajb','agunan_tanah.asli_imb','agunan_tanah.asli_sppt','agunan_tanah.asli_sppt','agunan_tanah.asli_imb','asli_skmht','agunan_tanah.asli_gambar_denah','agunan_tanah.asli_imb','agunan_tanah.asli_surat_roya','agunan_tanah.asli_sht','agunan_tanah.asli_stts','agunan_tanah.asli_ssb',
//     'agunan_tanah.imb','agunan_tanah.sppt','agunan_tanah.no_sppt','agunan_tanah.sppt_tahun','agunan_tanah.skmht','agunan_tanah.gambar_denah','agunan_tanah.surat_roya','agunan_tanah.sht','agunan_tanah.no_sht','agunan_tanah.sht_propinsi','agunan_tanah.sht_kota','agunan_tanah.stts','agunan_tanah.stts_tahun','agunan_tanah.ssb','agunan_tanah.ssb_atas_nama','agunan_tanah.lain_lain'
// )->join('trans_so','trans_so.id_calon_debitur','=','calon_debitur.id')->join('trans_ao','trans_ao.id_trans_so','=','trans_so.id')->join('agunan_tanah','trans_ao.id_agunan_tanah','=','agunan_tanah.id')
//  ->where('trans_so.id',$id)
// ->get();

$target_lending = Target_lending::where('id',$id)->paginate(10);
 if (empty($target_lending)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Sertifikat kosong'
            ], 404);
        }

try {
            return response()->json([
                // 'code'   => 200,
                // 'status' => 'success',
                // 'count'  => sizeof($target_lending),
                'data'   => $target_lending
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }



    }

    public function store(Request $req)
    {
      $pic = $req->pic;

  // if (!empty($req->input('kode_kantor'))) {
  //           for ($i = 0; $i < count($req->input('kode_kantor')); $i++) {
  //               $data[] = [
  //                   'kode_kantor'
  //                   => empty($req->kode_kantor[$i])
  //                       ? null : $req->kode_kantor[$i],

  //                   'area_kerja'
  //                   => empty($req->area_kerja[$i])
  //                       ? null : $req->area_kerja[$i],
  //                         'area'
  //                   => empty($req->area[$i])
  //                       ? null : $req->area[$i],
  //                         'target'
  //                   => empty($req->target[$i])
  //                       ? null : $req->target[$i],
  //                         'bulan'
  //                   => empty($req->bulan[$i])
  //                       ? null : $req->bulan[$i],
  //                         'tahun'
  //                   => empty($req->tahun[$i])
  //                       ? null : $req->tahun[$i],
  //               ];
  //           }
  //       }
$data = array(
'kode_kantor' => $req->input('kode_kantor'),
'area_kerja' => $req->input('area_kerja'),
'area' => $req->input('area'),
'target' => $req->input('target'),
'bulan' => $req->input('bulan'),
'tahun' => $req->input('tahun')
);

 

        
try {

Target_lending::create($data);
     // if (!empty($data)) {
     //            $arrayPemTan = array();
     //            for ($i = 0; $i < count($data); $i++) {
     //                // $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $data[$i]);

     //                $pemTanah = Target_lending::create($data[$i]);

     //                $id_pem_tan['id'][$i] = $pemTanah->id;

     //                $arrayPemTan[] = $pemTanah;
     //            }

     //            $p_tanID = implode(",", $id_pem_tan['id']);
     //        } else {
     //            $arrayPemTan = null;
     //            $p_tanID = null;
     //        }
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

     public function update($id_no,Request $req)
    {
      $pic = $req->pic;

 
      $search = Target_lending::where('id',$id_no)->first();
$data = array(
'kode_kantor' => empty($req->input('kode_kantor')) ? $search->kode_kantor : $req->input('kode_kantor'),
'area_kerja' => empty($req->input('area_kerja')) ? $search->area_kerja : $req->input('area_kerja'),
'area' => empty($req->input('area')) ? $search->area : $req->input('area'),
'target' => empty($req->input('target')) ? $search->target : $req->input('target'),
'bulan' => empty($req->input('bulan')) ? $search->bulan : $req->input('bulan'),
'tahun' => empty($req->input('tahun')) ? $search->tahun : $req->input('tahun')
);

 

        
try {

Target_lending::where('id',$id_no)->update($data);
   
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

     public function delete($id_no,Request $req)
    {
      $pic = $req->pic;

$get_id = Target_lending::where('id',$id_no)->first();
      
      if (empty($get_id)) {
return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Target Lending dengan id'.''.$id_no.''.'kosong'
            ], 404);
      } 

        
try {

Target_lending::where('id',$id_no)->delete();
   
            return response()->json([
                'code'   => 200,
                'status' => 'success',
               'message'   => 'data telah terhapus'
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
