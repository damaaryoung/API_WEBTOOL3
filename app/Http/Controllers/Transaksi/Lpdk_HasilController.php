<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\Lpdk;
use App\Models\Transaksi\Lpdk_lampiran;
use App\Models\Transaksi\Lpdk_sertifikat;
use App\Models\Transaksi\Lpdk_penjamin;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Transaksi\Lpdk_kendaraan;
use App\Models\Transaksi\TransCA;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransSO;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Transaksi\Lpdk_hasil;
use App\Models\Transaksi\TransCAA;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Lpdk_HasilController extends BaseController
{
    public function getHasilLPDK(Request $request ,$id)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

     

        $mj = array();
$i=0;
foreach ($pic as $val) {
    $mj[] = $val['id_mj_pic'];
  $i++;
}   
$id_pic = array();
$i=0;
foreach ($pic as $val) {
    $id_pic[] = $val['id'];
  $i++;
}   
$arrr = array();
foreach ($pic as $val) {
    $arrr[] = $val['id_cabang'];
  $i++;
}  
$area = array();
$i=0;
foreach ($pic as $val) {
    $area[] = $val['id_area'];
  $i++;
} 
$nama = array();
$i=0;
foreach ($pic as $val) {
    $nama[] = $val['nama'];
  $i++;
} 

$arrrr = array();
foreach ($pic as $val) {
    $arrrr[] = $val['jpic']['cakupan'];
  $i++;
}       

$id_area   = $area;
$id_cabang = $arrr;
// dd($id_cabang);
$scope     = $arrrr;

// dd($pic);
$query_dir = Lpdk::with('pic','area', 'cabang')->where('trans_so', $id);
//dd($query_dir);

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
//$lpdk =  DB::connection('web')->table('view_approval_caa')->get();
//dd($pic);
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
      //  $hasil = Lpdk_hasil::where('trans_so', $id)->first();

        $real = Lpdk::where('trans_so', $id)->first();

        if ($real === null) {
          return response()->json([
              'code'    => 404,
              'status'  => 'not found',
              'message' => 'Data Hasil LPDK id transaksi ' . $id . ' Kosong'
          ], 404);
      }


        //   dd(empty($hasil));
        if ($query === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Hasil LPDK id transaksi ' . $id . ' Kosong'
            ], 404);
        }

        // if (empty($real)) {
        //     return response()->json([
        //         'code'    => 402,
        //         'status'  => 'not found',
        //         'message' => 'Data Hasil LPDK id transaksi ' . $id . 'Belum Melalui Proses REALISASI'
        //     ], 402);
        // }
        //  $data = array();
        $arrData = array(
            'trans_so' => $real->trans_so,
            'request_by' => $real->request_by,
            'nomor_so' => $real->nomor_so,
            'nama_so' => $real->nama_so,
            'asal_data' => $real->asal_data,
            'nama_marketing' => $real->nama_marketing,
            'area_kerja' => $real->area_kerja,
            'plafon' => $real->plafon,
            'tenor' => $real->tenor
        );
        // foreach ($real as $key => $val) {
        //     $arrData[$key]['trans_so'] = $val->trans_so;
        //     $arrData[$key]['request_by'] = $val->request_by;
        //     $arrData[$key]['nomor_so'] = $val->nomor_so;
        //     $arrData[$key]['asal_data'] = $val->asal_data;
        //     $arrData[$key]['nama_marketing'] = $val->nama_marketing;
        //     $arrData[$key]['area_kerja'] = $val->area_kerja;
        //     $arrData[$key]['plafon'] = $val->plafon;
        //     $arrData[$key]['tenor'] = $val->tenor;
        // }
       $hasil = Lpdk_hasil::where('trans_so',$id)->first();
       //$merge = array_merge($query,$hasil);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  =>  $query,
                'hasil'  =>  $hasil
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
    public function store($id_trans, Request $req)
    {
        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;
        $user_nama = $req->auth->user;


        $mj = array();
$i=0;
foreach ($pic as $val) {
    $mj[] = $val['id_mj_pic'];
  $i++;
}   
$id_pic = array();
$i=0;
foreach ($pic as $val) {
    $id_pic[] = $val['id'];
  $i++;
}   
$arrr = array();
foreach ($pic as $val) {
    $arrr[] = $val['id_cabang'];
  $i++;
}  
$area = array();
$i=0;
foreach ($pic as $val) {
    $area[] = $val['id_area'];
  $i++;
} 
$nama = array();
$i=0;
foreach ($pic as $val) {
    $nama[] = $val['nama'];
  $i++;
} 

$arrrr = array();
foreach ($pic as $val) {
    $arrrr[] = $val['jpic']['cakupan'];
  $i++;
}       

$id_area   = $area;
$id_cabang = $arrr;
// dd($id_cabang);
$scope     = $arrrr;

$query_dir = Lpdk::with('pic','area', 'cabang')->where('status_kredit', 'ON-QUEUE')->where('trans_so', $id_trans);

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        //   dd($pic->nama);

        $cek_marketing = DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id_trans)->first();
        // $cek_lpdk =  Lpdk::where('status_kredit', 'ON-QUEUE')->where('trans_so', $id_trans)->first();
        if ($cek_marketing === null) {
            return response()->json([
                'code'  => 403,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'Tidak ada di Proses Approve OL'
            ],403);
        }
        if ($query === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'Tidak ada di Proses Approve OL'
            ],402);
        }
    
        $data = array(
            'trans_so' => $id_trans,
            'id_pic' => $id_pic[0],
            'id_area' => $id_area[0],
            'id_cabang' => $id_cabang[0],
            'ktp_deb' => $req->input('ktp_deb'),
            'ktp_deb_ket' => $req->input('ktp_deb_ket'),
            'ktp_pas' => $req->input('ktp_pas'),
            'ktp_pas_ket' => $req->input('ktp_pas_ket'),
            'kk' => $req->input('kk'),
            'kk_ket' => $req->input('kk_ket'),
            'akta_nikah' => $req->input('akta_nikah'),
            'akta_nikah_ket' => $req->input('akta_nikah_ket'),
            'akta_cerai' => $req->input('akta_cerai'),
            'akta_cerai_ket' => $req->input('akta_cerai_ket'),
            'akta_lahir' => $req->input('akta_lahir'),
            'akta_lahir_ket' => $req->input('akta_lahir_ket'),
            'surat_kematian' => $req->input('surat_kematian'),
            'surat_kematian_ket' => $req->input('surat_kematian_ket'),
            'npwp' => $req->input('npwp'),
            'npwp_ket' => $req->input('npwp_ket'),
            'skd_pmi' => $req->input('skd_pmi'),
            'skd_pmi_ket' => $req->input('skd_pmi_ket'),
            'shm_shgb' => $req->input('shm_shgb'),
            'shm_shgb_ket' => $req->input('shm_shgb_ket'),
            'imb' => $req->input('imb'),
            'imb_ket' => $req->input('imb_ket'),
            'pbb' => $req->input('pbb'),
            'pbb_ket' => $req->input('pbb_ket'),
            'sttpbb' => $req->input('sttpbb'),
            'sttpbb_ket' => $req->input('sttpbb_ket'),
            'fotocopy_ktp_ortu' => $req->input('fotocopy_ktp_ortu'),
            'fotocopy_ktp_ortu_ket' => $req->input('fotocopy_ktp_ortu_ket'),
            'fotocopy_kk_ortu' => $req->input('fotocopy_kk_ortu'),
            'fotocopy_kk_ortu_ket' => $req->input('fotocopy_kk_ortu_ket'),
            'pg_ortu' => $req->input('pg_ortu'),
            'pg_ortu_ket' => $req->input('pg_ortu_ket'),
            'akta_nikah_ortu' => $req->input('akta_nikah_ortu'),
            'akta_nikah_ortu_ket' => $req->input('akta_nikah_ortu_ket'),
            'sk_waris' => $req->input('sk_waris'),
            'sk_waris_ket' => $req->input('sk_waris_ket'),
            'akta_lahir_waris' => $req->input('akta_lahir_waris'),
            'akta_lahir_waris_ket' => $req->input('akta_lahir_waris_ket'),
            'sk_anak' => $req->input('sk_anak'),
            'sk_anak_ket' => $req->input('sk_anak_ket'),
            'ktp_penjamin' => $req->input('ktp_penjamin'),
            'ktp_penjamin_ket' => $req->input('ktp_penjamin_ket'),
            'ktp_pasangan_pen' => $req->input('ktp_pasangan_pen'),
            'ktp_pasangan_pen_ket' => $req->input('ktp_pasangan_pen_ket'),
            'kk_penjamin' => $req->input('kk_penjamin'),
            'kk_penjamin_ket' => $req->input('kk_penjamin_ket'),
            'aktanikah_penj' => $req->input('aktanikah_penj'),
            'aktanikah_penj_ket' => $req->input('aktanikah_penj_ket'),
            'aktacerai_penj' => $req->input('aktacerai_penj'),
            'aktacerai_penj_ket' => $req->input('aktacerai_penj_ket'),
            'akta_lahir_penj' => $req->input('akta_lahir_penj'),
            'akta_lahir_penj_ket' => $req->input('akta_lahir_penj_ket'),
            'skematian_penjamin' => $req->input('skematian_penjamin'),
            'skematian_penjamin_ket' => $req->input('skematian_penjamin_ket'),
            'npwp_penjamin' => $req->input('npwp_penjamin'),
            'npwp_penjamin_ket' => $req->input('npwp_penjamin_ket'),
            'skd_penjamin' => $req->input('skd_penjamin'),
            'skd_penjamin_ket' => $req->input('skd_penjamin_ket'),
            'ktp_penjual' => $req->input('ktp_penjual'),
            'ktp_penjual_ket' => $req->input('ktp_penjual_ket'),
            'ktp_pas_penjual' => $req->input('ktp_pas_penjual'),
            'ktp_pas_penjual_ket' => $req->input('ktp_pas_penjual_ket'),
            'kk_penjual' => $req->input('kk_penjual'),
            'kk_penjual_ket' => $req->input('kk_penjual_ket'),
            'aktanikah_penjual' => $req->input('aktanikah_penjual'),
            'aktanikah_penjual_ket' => $req->input('aktanikah_penjual_ket'),
            'aktacerai_penjual' => $req->input('aktacerai_penjual'),
            'aktacerai_penjual_ket' => $req->input('aktacerai_penjual_ket'),
            'aktalahir_penjual' => $req->input('aktalahir_penjual'),
            'aktalahir_penjual_ket' => $req->input('aktalahir_penjual_ket'),
            'skematian_penjual' => $req->input('skematian_penjual'),
            'skematian_penjual_ket' => $req->input('skematian_penjual_ket'),
            'npwp_penjual' => $req->input('npwp_penjual'),
            'npwp_penjual_ket' => $req->input('npwp_penjual_ket'),
            'skd_penjual' => $req->input('skd_penjual'),
            'skd_penjual_ket' => $req->input('skd_penjual_ket')
        );

        $lpdk_id = Lpdk::where('trans_so', $id_trans)->first();
        $create = date_create(Carbon::parse($lpdk_id->created_at)->format('Y-m-d'));
        $update = date_create(Carbon::parse($lpdk_id->updated_at)->format('Y-m-d'));
                $sla_interval = date_diff($create,$update);
      
        try {

            if (Lpdk_hasil::where('trans_so', $id_trans)->exists()) {
                return response()->json([
                    'code'  => 402,
                    'status' => 'conflict',
                    'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'sudah ada di hasil Lpdk'
                ], 402);
            }
            
            $has = Lpdk_hasil::create($data);

            $create = date_create(Carbon::parse($lpdk_id->created_at)->format('Y-m-d'));
            $update = date_create(Carbon::parse($has['updated_at'])->format('Y-m-d'));
                    $sla_interval = date_diff($create,$update);
            
           // dd($has['updated_at']);
            Lpdk::where('trans_so', $id_trans)->update(['status_kredit' => 'ON-PROGRESS','sla' => $sla_interval->days]);

            TransCAA::where('id_trans_so',$id_trans)->update(['status_team_caa' => 'ON-PROGRESS By picID'.' '.$id_pic[0]]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function update($id_trans, Request $req)
    {
        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;
        $user_nama = $req->auth->user;


        $mj = array();
$i=0;
foreach ($pic as $val) {
    $mj[] = $val['id_mj_pic'];
  $i++;
}   
$id_pic = array();
$i=0;
foreach ($pic as $val) {
    $id_pic[] = $val['id'];
  $i++;
}   
$arrr = array();
foreach ($pic as $val) {
    $arrr[] = $val['id_cabang'];
  $i++;
}  
$area = array();
$i=0;
foreach ($pic as $val) {
    $area[] = $val['id_area'];
  $i++;
} 
$nama = array();
$i=0;
foreach ($pic as $val) {
    $nama[] = $val['nama'];
  $i++;
} 

$arrrr = array();
foreach ($pic as $val) {
    $arrrr[] = $val['jpic']['cakupan'];
  $i++;
}       

$id_area   = $area;
$id_cabang = $arrr;
// dd($id_cabang);
$scope     = $arrrr;

$query_dir = Lpdk::with('pic','area', 'cabang')->where('status_kredit', 'ON-PROGRESS')->where('trans_so', $id_trans);

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 

        $cek_marketing = DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id_trans)->first();
        // $cek_lpdk =  Lpdk::where('status_kredit', 'ON-PROGRESS')->where('trans_so', $id_trans)->first();

        $hasil = Lpdk_hasil::where('trans_so', $id_trans)->first();

    //    if ($cek_marketing === null) {
       //     return response()->json([
          //      'code'  => 402,
            //    'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'Tidak ada di Proses Approve OL'
          //  ]);
     //   }
        // if ($cek_lpdk === null) {
        //     return response()->json([
        //         'code'  => 402,
        //         'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'Belum Berstatus ON-PROGRESS Pengajuan LPDK'
        //     ]);
        // }

        $data = array(
            //'trans_so' => $id_trans,
            'ktp_deb' => empty($req->input('ktp_deb')) ? null : $req->input('ktp_deb'),
            'ktp_deb_ket' => empty($req->input('ktp_deb_ket')) ? null : $req->input('ktp_deb_ket'),
            'ktp_pas' => empty($req->input('ktp_pas')) ? null : $req->input('ktp_pas'),
            'ktp_pas_ket' => empty($req->input('ktp_pas_ket')) ? null : $req->input('ktp_pas_ket'),
            'kk' => empty($req->input('kk')) ? null : $req->input('kk'),
            'kk_ket' => empty($req->input('kk_ket')) ? null : $req->input('kk_ket'),
            'akta_nikah' => empty($req->input('akta_nikah')) ? null : $req->input('akta_nikah'),
            'akta_nikah_ket' => empty($req->input('akta_nikah_ket')) ? null : $req->input('akta_nikah_ket'),
            'akta_cerai' => empty($req->input('akta_cerai')) ? null : $req->input('akta_cerai'),
            'akta_cerai_ket' => empty($req->input('akta_cerai_ket')) ? null : $req->input('akta_cerai_ket'),
            'akta_lahir' => empty($req->input('akta_lahir')) ? null : $req->input('akta_lahir'),
            'akta_lahir_ket' => empty($req->input('akta_lahir_ket')) ? null : $req->input('akta_lahir_ket'),
            'surat_kematian' => empty($req->input('surat_kematian')) ? null : $req->input('surat_kematian'),
            'surat_kematian_ket' => empty($req->input('surat_kematian_ket')) ? null : $req->input('surat_kematian_ket'),
            'npwp' => empty($req->input('npwp')) ? null : $req->input('npwp'),
            'npwp_ket' => empty($req->input('npwp_ket')) ? null : $req->input('npwp_ket'),
            'skd_pmi' => empty($req->input('skd_pmi')) ? null : $req->input('skd_pmi'),
            'skd_pmi_ket' => empty($req->input('skd_pmi_ket')) ? null : $req->input('skd_pmi_ket'),
            'shm_shgb' => empty($req->input('shm_shgb')) ? null : $req->input('shm_shgb'),
            'shm_shgb_ket' => empty($req->input('shm_shgb_ket')) ? null : $req->input('shm_shgb_ket'),
            'imb' => empty($req->input('imb')) ? null : $req->input('imb'),
            'imb_ket' => empty($req->input('imb_ket')) ? null : $req->input('imb_ket'),
            'pbb' => empty($req->input('pbb')) ? null : $req->input('pbb'),
            'pbb_ket' => empty($req->input('pbb_ket')) ? null : $req->input('pbb_ket'),
            'sttpbb' => empty($req->input('sttpbb')) ? null : $req->input('sttpbb'),
            'sttpbb_ket' => empty($req->input('sttpbb_ket')) ? null : $req->input('sttpbb_ket'),
            'fotocopy_ktp_ortu' => empty($req->input('fotocopy_ktp_ortu')) ? null : $req->input('fotocopy_ktp_ortu'),
            'fotocopy_ktp_ortu_ket' => empty($req->input('fotocopy_ktp_ortu_ket')) ? null : $req->input('fotocopy_ktp_ortu_ket'),
            'fotocopy_kk_ortu' => empty($req->input('fotocopy_kk_ortu')) ? null : $req->input('fotocopy_kk_ortu'),
            'fotocopy_kk_ortu_ket' => empty($req->input('fotocopy_kk_ortu_ket')) ? null : $req->input('fotocopy_kk_ortu_ket'),
            'pg_ortu' => empty($req->input('pg_ortu')) ? null : $req->input('pg_ortu'),
            'pg_ortu_ket' => empty($req->input('pg_ortu_ket')) ? null : $req->input('pg_ortu_ket'),
            'akta_nikah_ortu' => empty($req->input('akta_nikah_ortu')) ? null : $req->input('akta_nikah_ortu'),
            'akta_nikah_ortu_ket' => empty($req->input('akta_nikah_ortu_ket')) ? null : $req->input('akta_nikah_ortu_ket'),
            'sk_waris' => empty($req->input('sk_waris')) ? null : $req->input('sk_waris'),
            'sk_waris_ket' => empty($req->input('sk_waris_ket')) ? null : $req->input('sk_waris_ket'),
            'akta_lahir_waris' => empty($req->input('akta_lahir_waris')) ? null : $req->input('akta_lahir_waris'),
            'akta_lahir_waris_ket' => empty($req->input('akta_lahir_waris_ket')) ? null : $req->input('akta_lahir_waris_ket'),
            'sk_anak' => empty($req->input('sk_anak')) ? null : $req->input('sk_anak'),
            'sk_anak_ket' => empty($req->input('sk_anak_ket')) ? null : $req->input('sk_anak_ket'),
            'ktp_penjamin' => empty($req->input('ktp_penjamin')) ? null : $req->input('ktp_penjamin'),
            'ktp_penjamin_ket' => empty($req->input('ktp_penjamin_ket')) ? null : $req->input('ktp_penjamin_ket'),
            'ktp_pasangan_pen' => empty($req->input('ktp_pasangan_pen')) ? null : $req->input('ktp_pasangan_pen'),
            'ktp_pasangan_pen_ket' => empty($req->input('ktp_pasangan_pen_ket')) ? null : $req->input('ktp_pasangan_pen_ket'),
            'kk_penjamin' => empty($req->input('kk_penjamin')) ? null : $req->input('kk_penjamin'),
            'kk_penjamin_ket' => empty($req->input('kk_penjamin_ket')) ? null : $req->input('kk_penjamin'),
            'aktanikah_penj' => empty($req->input('aktanikah_penj')) ? null : $req->input('aktanikah_penj'),
            'aktanikah_penj_ket' => empty($req->input('aktanikah_penj_ket')) ? null : $req->input('aktanikah_penj_ket'),
            'aktacerai_penj' => empty($req->input('aktacerai_penj')) ? null : $req->input('aktacerai_penj'),
            'aktacerai_penj_ket' => empty($req->input('aktacerai_penj_ket')) ? null : $req->input('aktacerai_penj_ket'),
            'akta_lahir_penj' => empty($req->input('akta_lahir_penj')) ? null : $req->input('akta_lahir_penj'),
            'akta_lahir_penj_ket' => empty($req->input('akta_lahir_penj_ket')) ? null : $req->input('akta_lahir_penj_ket'),
            'skematian_penjamin' => empty($req->input('skematian_penjamin')) ? null : $req->input('skematian_penjamin'),
            'skematian_penjamin_ket' => empty($req->input('skematian_penjamin_ket')) ? null : $req->input('skematian_penjamin_ket'),
            'npwp_penjamin' => empty($req->input('npwp_penjamin')) ? null : $req->input('npwp_penjamin'),
            'npwp_penjamin_ket' => empty($req->input('npwp_penjamin_ket')) ? null : $req->input('npwp_penjamin_ket'),
            'skd_penjamin' => empty($req->input('skd_penjamin')) ? null : $req->input('skd_penjamin'),
            'skd_penjamin_ket' => empty($req->input('skd_penjamin_ket')) ? null : $req->input('skd_penjamin_ket'),
            'ktp_penjual' => empty($req->input('ktp_penjual')) ? null : $req->input('ktp_penjual'),
            'ktp_penjual_ket' => empty($req->input('ktp_penjual_ket')) ? null : $req->input('ktp_penjual_ket'),
            'ktp_pas_penjual' => empty($req->input('ktp_pas_penjual')) ? null : $req->input('ktp_pas_penjual'),
            'ktp_pas_penjual_ket' => empty($req->input('ktp_pas_penjual_ket')) ? null : $req->input('ktp_pas_penjual_ket'),
            'kk_penjual' => empty($req->input('kk_penjual')) ? null : $req->input('kk_penjual'),
            'kk_penjual_ket' => empty($req->input('kk_penjual_ket')) ? null : $req->input('kk_penjual_ket'),
            'aktanikah_penjual' => empty($req->input('aktanikah_penjual')) ? null : $req->input('aktanikah_penjual'),
            'aktanikah_penjual_ket' => empty($req->input('aktanikah_penjual_ket')) ? null : $req->input('aktanikah_penjual_ket'),
            'aktacerai_penjual' => empty($req->input('aktacerai_penjual')) ? null : $req->input('aktacerai_penjual'),
            'aktacerai_penjual_ket' => $req->input('aktacerai_penjual_ket'),
            'aktalahir_penjual' => $req->input('aktalahir_penjual'),
            'aktalahir_penjual_ket' => $req->input('aktalahir_penjual_ket'),
            'skematian_penjual' => $req->input('skematian_penjual'),
            'skematian_penjual_ket' => $req->input('skematian_penjual_ket'),
            'npwp_penjual' => $req->input('npwp_penjual'),
            'npwp_penjual_ket' => $req->input('npwp_penjual_ket'),
            'skd_penjual' => $req->input('skd_penjual'),
            'skd_penjual_ket' => $req->input('skd_penjual_ket')
        );

        // $status = $req->input('status_kredit');
        try {

            // if (Lpdk_hasil::where('trans_so', $id_trans)->exists()) {
            //     return response()->json([
            //         'code'  => 402,
            //         'status' => 'conflict',
            //         'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'sudah ada di hasil Lpdk'
            //     ], 402);
            // }

            Lpdk_hasil::where('trans_so',$id_trans)->update($data);

          //  Lpdk::where('trans_so', $id_trans)
            // ->update(['status_kredit' => $status]);

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            // $err = DB::connection('web')->rollback();
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
