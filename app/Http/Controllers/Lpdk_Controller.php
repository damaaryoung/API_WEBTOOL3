<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\AreaKantor\Cabang;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\Lpdk;
use App\Models\Transaksi\Lpdk_lampiran;
use App\Models\Transaksi\Lpdk_sertifikat;
use App\Models\Transaksi\Lpdk_penjamin;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Transaksi\Lpdk_kendaraan;
use App\Models\Transaksi\ViewApproval;
use App\Models\Transaksi\TransCA;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransSO;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\Pengajuan\SO\Pasangan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Lpdk_Controller extends BaseController
{
    public function index(Request $request)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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

      //  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
      $query_dir =  ViewApproval::with('pic','area', 'cabang');
    //   ->orderBy('created_at', 'desc');
        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        //$lpdk =  DB::connection('web')->table('view_approval_caa')->get();

//dd($pic);
        if ($query === '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data di SO masih kosong"
            ], 404);
        }
        //$lpdk = Lpdk_Cek::get();
        //  $lpdk = Lpdk::paginate(10);
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id_trans_so']       = $val->id_trans_so;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['id_cabang']       = $val->id_cabang;
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $det = explode(',', $val->id_penjamin);
            $arrData[$key]['penjamin']       = Penjamin::select('id','nama_ktp', 'nama_ibu_kandung', 'no_ktp', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'lamp_ktp')->whereIn('id', $det)->get();
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $ser = explode(',', $val->id_agunan_tanah);
            $arrData[$key]['sertifikat']       = AgunanTanah::select('id','nama_pemilik_sertifikat', 'jenis_sertifikat', 'no_sertifikat', 'tgl_berlaku_shgb', 'lamp_sertifikat', 'lamp_imb', 'lamp_pbb')->whereIn('id', $ser)->get();
            $ken = explode(',', $val->id_agunan_kendaraan);
            $arrData[$key]['kendaraan']       = AgunanKendaraan::select('id','no_bpkb', 'nama_pemilik', 'alamat_pemilik', 'merk', 'jenis', 'no_rangka', 'no_mesin', 'warna', 'tahun', 'no_polisi', 'no_stnk', 'tgl_kadaluarsa_pajak', 'tgl_kadaluarsa_stnk', 'no_faktur', 'lamp_agunan_depan', 'lamp_agunan_kanan', 'lamp_agunan_kiri', 'lamp_agunan_belakang', 'lamp_agunan_dalam')->whereIn('id', $ken)->get();

            $arrData[$key]['lampiran_kk']       = $val->lampiran_kk;
            $arrData[$key]['lampiran_npwp']       = NULL;
            $arrData[$key]['lampiran_ktp_deb']       = $val->lampiran_ktp_deb;
            $arrData[$key]['lampiran_ktp_pasangan']       = $val->lampiran_ktp_pasangan;
            $arrData[$key]['lamp_buku_nikah']       = $val->lamp_buku_nikah;
            $arrData[$key]['lampiran_surat_cerai']       = $val->lampiran_surat_cerai;
            $arrData[$key]['lampiran_surat_lahir']       = NULL;
            $arrData[$key]['lampiran_surat_kematian']       = NULL;
            $arrData[$key]['lampiran_surat_keterangan_desa']       = NULL;
            $arrData[$key]['lampiran_surat_ajb_ppjb']       = NULL;
            $arrData[$key]['lampiran_ahliwaris']       = NULL;
            $arrData[$key]['lampiran_akta_hibah']       = NULL;
        }
        // if ($lpdk === null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'Data LPDK Kosong'
        //     ], 404);
        // }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count' => count($arrData),
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function getDetailLpdk(Request $request,$id)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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

//  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
// $query_dir =  ViewApproval::with('pic','area', 'cabang');

$query_dir = Lpdk::with('pic','area', 'cabang')->where('trans_so', $id);

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
//$lpdk =  DB::connection('web')->table('view_approval_caa')->get();

//dd($pic);
if ($query === '[]') {
    return response()->json([
        "code"    => 404,
        "status"  => "not found",
        "message" => "Data di SO masih kosong"
    ], 404);
}
            
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
      //  $lpdk = Lpdk::where('trans_so', $id)->get();
        $penjamin = Lpdk_penjamin::where('trans_so', $id)->get();
        $lampiran = Lpdk_penjamin::where('trans_so', $id)->get();
        $sertifikat = Lpdk_sertifikat::where('trans_so', $id)->get();
        //  $lpdk = Lpdk::paginate(10);


        $arrData = array();
        foreach ($query as $key => $val) {
            $deb =  TransSO::where('id', $id)->first();
            $arrData[$key]['id']       = $val->id;
            $arrData[$key]['trans_so']   = $val->trans_so; 
$arrData[$key]['request_by']   = $val->request_by;
            $arrData[$key]['nomor_so']    = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['nama_cabang']       = Cabang::where('id',$val->id_cabang)->pluck('nama');
            $arrData[$key]['nama_so'] = $val->nama_so;
            $arrData[$key]['asal_data']   = $val->asal_data;
            $arrData[$key]['nama_marketing']     = $val->nama_marketing;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['plafon']   = $val->plafon;
            $arrData[$key]['tenor']    = $val->tenor;
            $id_deb = Debitur::where('id', $deb->id_calon_debitur)->first();
                $arrData[$key]['id_debitur'] =  $id_deb->id;
            $arrData[$key]['nama_debitur'] = $val->nama_debitur;
            $id_pas = Pasangan::where('id', $deb->id_pasangan)->first();
            if ( $id_pas === null) {
                $arrData[$key]['id_pasangan']   = "";
            } else {
                $arrData[$key]['id_pasangan']   =   $id_pas->id;
            }
           
            $arrData[$key]['nama_pasangan']   = $val->nama_pasangan;
           // dd($val);
            $arrData[$key]['status_nikah']     = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $arrData[$key]['alamat_ktp_vs_jaminan']   = $val->alamat_ktp_vs_jaminan;
            // $arrData[$key]['hubcadeb']    = $val->hubcadeb;
            $arrData[$key]['akta_notaris'] = $val->akta_notaris;
            $arrData[$key]['status_kredit']   = $val->status_kredit;
            $arrData[$key]['notes_progress']     = explode(',',$val->notes_progress);
            $arrData[$key]['notes_counter']     = explode(',',$val->notes_counter);
            $arrData[$key]['notes_cancel']     = $val->notes_cancel;
            $arrData[$key]['sla']     = $val->sla;
            $arrData[$key]['lain_lain']     = $val->lain_lain;
            if($arrData[$key]['penjamin'] = Lpdk_penjamin::select(
                'id',
                        'nama_penjamin',
                        'ibu_kandung_penjamin',
                        'pasangan_penjamin',
                        'lampiran_ktp_penjamin',
                'buku_nikah_penjamin'
                    )->where('trans_so', $id)->get() === null) {
                        array(
                            'id' => "",
                        'nama_penjamin' => "",
                        'ibu_kandung_penjamin' => "",
                        'pasangan_penjamin' => "",
                        'lampiran_ktp_penjamin' => "",
                        'buku_nikah_penjamin' => ""
                        );
                
            } else {
                $arrData[$key]['penjamin'] = Lpdk_penjamin::select(
                    'id',
                            'nama_penjamin',
                            'ibu_kandung_penjamin',
                            'pasangan_penjamin',
                            'lampiran_ktp_penjamin',
                    'buku_nikah_penjamin'
                        )->where('trans_so', $id)->get();
            }
          
            $arrData[$key]['sertifikat'] = Lpdk_sertifikat::select(
               'id',
 'nama_sertifikat',
                'status_sertifikat',
                'hub_cadeb',
                'nama_pas_sertifikat',
                'status_pas_sertifikat',
                'no_sertifikat',
                'jenis_sertifikat',
                'tgl_berlaku_shgb',
                'lampiran_ktp_sertifikat',
                'lampiran_ktp_pasangan_sertifikat',
                'ahli_waris',
                'akta_hibah',
                'ajb_ppjb',
                'lampiran_sertifikat',
                'lampiran_imb',
                'lampiran_pbb'
            )->where('trans_so', $id)->get();

  
   if ($deb->id_calon_debitur === null) {
    $arrData[$key]['lampiran_debitur'] = array(
        'lampiran_ktp_deb' => "",
        'lampiran_kk' => ""
    );
   }  else { 
       $arrData[$key]['lampiran_debitur'] = Debitur::select(
           'lamp_ktp AS lampiran_ktp_deb',
'lamp_kk AS lampiran_kk'
       )->where('id', $deb->id_calon_debitur)->first();

   }
   if ($deb->id_pasangan === null) {
    $arrData[$key]['lampiran_pasangan'] = array(
        'lampiran_ktp_pasangan' => "",
       'lampiran_surat_nikah' => ""
    );
} else { 
    $arrData[$key]['lampiran_pasangan'] =  Pasangan::select(
        'lamp_ktp AS lampiran_ktp_pasangan',
        'lamp_buku_nikah AS lampiran_surat_nikah'
    )->where('id', $deb->id_pasangan)->first();

}
            $arrData[$key]['lampiran'] = Lpdk_lampiran::select(
                'id',                
                'lampiran_npwp',
                'lampiran_surat_kematian',
                'lampiran_sk_desa',
                'lampiran_surat_lahir',
                'lampiran_surat_cerai',
                'lampiran_surat_nikah'
            )->where('trans_so', $id)->get();
            $arrData[$key]['kendaraan'] = Lpdk_kendaraan::select(
'id',               
 'trans_so',
                'no_bpkb',
                'nama_pemilik',
                'alamat_pemilik',
                'merk',
                'jenis',
                'no_rangka',
                'no_mesin',
                'warna',
                'tahun',
                'no_polisi',
                'no_stnk',
                'tgl_kadaluarsa_pajak',
                'tgl_kadaluarsa_stnk',
                'no_faktur',
                'lamp_agunan_depan',
                'lamp_agunan_kanan',
                'lamp_agunan_kiri',
                'lamp_agunan_belakang',
                'lamp_agunan_dalam'
            )->where('trans_so', $id)->get();


            $arrData[$key]['created_at']     = $val->created_at;
            $arrData[$key]['updated_at']     = $val->updated_at;
        }

        // dd(empty($arrData));
        if (empty($arrData)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK id transaksi ' . $id . ' Kosong'
            ], 404);
        }
        //  $data = array();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  =>  $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function indexOnprogress(Request $request)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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

//  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
// $query_dir =  ViewApproval::with('pic','area', 'cabang');



$query_dir = Lpdk::with('pic','area', 'cabang')->where('status_kredit', '=', 'ON-QUEUE');

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
//$lpdk =  DB::connection('web')->table('view_approval_caa')->get();

//dd($pic);
if ($query === '[]') {
    return response()->json([
        "code"    => 404,
        "status"  => "not found",
        "message" => "Data di SO masih kosong"
    ], 404);
}

        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
       
        //  $lpdk = Lpdk::paginate(10);
        if ($query === NULL) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }

        //    dd($lpdk);
        //  $i = 0;
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id']       = $val->id;
            $arrData[$key]['trans_so']       = $val->trans_so;
            $arrData[$key]['request_by']       = $val->request_by;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['id_cabang']       = $val->id_cabang;
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $arrData[$key]['alamat_ktp_vs_jaminan']       = $val->alamat_ktp_vs_jaminan;
            $arrData[$key]['akta_notaris']       = $val->akta_notaris;
            $arrData[$key]['status_kredit']       = $val->status_kredit;
            $arrData[$key]['note_progress']       = $val->note_progress;
            $arrData[$key]['note_counter']       = $val->note_counter;
            //$arrData[$key]['id_sertifikat']       = $val->id_sertifikat;
            $ser_exp = explode(',', $val->id_sertifikat);
            // dd($ser_exp);
            $arrData[$key]['sertifikat']       = Lpdk_sertifikat::select(
                'nama_sertifikat',
                'status_sertifikat',
                'nama_pas_sertifikat',
                'status_pas_sertifikat',
                'no_sertifikat',
                'jenis_sertifikat',
                'tgl_berlaku_shgb'
                // 'lampiran_sertifikat',
                // 'lampiran_pbb',
                // 'lampiran_imb'
            )->whereIn('id', $ser_exp)->get();
            $lamp_ser = Lpdk_sertifikat::select(
                'lampiran_sertifikat',
                'lampiran_pbb',
                'lampiran_imb',
                'lampiran_ktp_sertifikat',
                'lampiran_ktp_pasangan_sertifikat',
                'ahli_waris',
                'akta_hibah',
                'ajb_ppjb'
            )->whereIn('id', $ser_exp)->get();
            // dd($lamp_ser->implode('lampiran_sertifikat' . ','));
            $arrData[$key]['lampiran_sertifikat'] = $lamp_ser->implode('lampiran_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_sertifikat'] = $lamp_ser->implode('lampiran_ktp_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_pasangan_sertifikat'] = $lamp_ser->implode('lampiran_ktp_pasangan_sertifikat', ',');
            $arrData[$key]['ahli_waris'] = $lamp_ser->implode('ahli_waris', ',');
            $arrData[$key]['akta_hibah'] = $lamp_ser->implode('akta_hibah', ',');
            $arrData[$key]['ajb_ppjb'] = $lamp_ser->implode('ajb_ppjb', ',');
            $arrData[$key]['lampiran_pbb'] = $lamp_ser->implode('lampiran_pbb', ',');
            $arrData[$key]['lampiran_imb'] = $lamp_ser->implode('lampiran_imb', ',');
            $arrData[$key]['penjamin'] = Lpdk_penjamin::select(
                'nama_penjamin',
                'ibu_kandung_penjamin',
                'pasangan_penjamin',
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            $lamp = Lpdk_penjamin::select(
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            //$imp_ktp = implode(',', $lamp->toArray());
            // dd($lamp->implode('lampiran_ktp_penjamin', ','));
            // $i++;
            $arrData[$key]['lampiran_ktp_penjamin'] = $lamp->implode('lampiran_ktp_penjamin', ',');

            $lam = Lpdk_lampiran::select(
               'lampiran_npwp',
                'lampiran_surat_kematian',
                'lampiran_sk_desa',
                'lampiran_surat_lahir',
                'lampiran_surat_cerai',
                'lampiran_surat_nikah'
            )->where('id', $val->id_lampiran)->get();
            $arrData[$key]['lampiran_ktp_deb'] = $lam->implode('lampiran_ktp_deb', ',');
            $arrData[$key]['lampiran_ktp_pasangan'] = $lam->implode('lampiran_ktp_pasangan', ',');
            $arrData[$key]['lampiran_npwp'] = $lam->implode('lampiran_npwp', ',');
            $arrData[$key]['lampiran_surat_kematian'] = $lam->implode('lampiran_surat_kematian', ',');
            $arrData[$key]['lampiran_sk_desa'] = $lam->implode('lampiran_sk_desa', ',');
            $arrData[$key]['lampiran_skk'] = $lam->implode('lampiran_skk', ',');
            $arrData[$key]['lampiran_sku'] = $lam->implode('lampiran_sku', ',');
            $arrData[$key]['lampiran_slipgaji'] = $lam->implode('lampiran_slipgaji', ',');
            $arrData[$key]['lampiran_kk'] = $lam->implode('lampiran_kk', ',');
            $arrData[$key]['lampiran_surat_lahir'] = $lam->implode('lampiran_surat_lahir', ',');
            $arrData[$key]['lampiran_surat_cerai'] = $lam->implode('lampiran_surat_cerai', ',');
            $arrData[$key]['lampiran_surat_nikah'] = $lam->implode('lampiran_surat_nikah', ',');
            $arrData[$key]['created_at']       = $val->created_at;
            $arrData[$key]['updated_at']       = $val->updated_at;
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count' => count($arrData),
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

  public function indexAll(Request $request)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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

//  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
// $query_dir =  ViewApproval::with('pic','area', 'cabang');



$query_dir = Lpdk::with('pic','area', 'cabang');

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
      //  $lpdk = Lpdk::get();
        //  $lpdk = Lpdk::paginate(10);
        if ($query === NULL) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }

        //    dd($lpdk);
        //  $i = 0;
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id']       = $val->id;
            $arrData[$key]['trans_so']       = $val->trans_so;
            $arrData[$key]['request_by']       = $val->request_by;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['nama_cabang']       = Cabang::where('id',$val->id_cabang)->pluck('nama');
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $arrData[$key]['alamat_ktp_vs_jaminan']       = $val->alamat_ktp_vs_jaminan;
            $arrData[$key]['akta_notaris']       = $val->akta_notaris;
            $arrData[$key]['status_kredit']       = $val->status_kredit;
            $arrData[$key]['notes_progress']       = $val->notes_progress;
            $arrData[$key]['notes_counter']       = $val->notes_counter;
            $arrData[$key]['notes_cancel']       = $val->notes_cancel;
            $arrData[$key]['sla']       = $val->sla;
            //$arrData[$key]['id_sertifikat']       = $val->id_sertifikat;
            $ser_exp = explode(',', $val->id_sertifikat);
            // dd($ser_exp);
            $arrData[$key]['sertifikat']       = Lpdk_sertifikat::select(
                'nama_sertifikat',
                'status_sertifikat',
                'nama_pas_sertifikat',
                'status_pas_sertifikat',
                'no_sertifikat',
                'jenis_sertifikat',
                'tgl_berlaku_shgb'
                // 'lampiran_sertifikat',
                // 'lampiran_pbb',
                // 'lampiran_imb'
            )->whereIn('id', $ser_exp)->get();
            $lamp_ser = Lpdk_sertifikat::select(
                'lampiran_sertifikat',
                'lampiran_pbb',
                'lampiran_imb',
                'lampiran_ktp_sertifikat',
                'lampiran_ktp_pasangan_sertifikat',
                'ahli_waris',
                'akta_hibah',
                'ajb_ppjb'
            )->whereIn('id', $ser_exp)->get();
            // dd($lamp_ser->implode('lampiran_sertifikat' . ','));
            $arrData[$key]['lampiran_sertifikat'] = $lamp_ser->implode('lampiran_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_sertifikat'] = $lamp_ser->implode('lampiran_ktp_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_pasangan_sertifikat'] = $lamp_ser->implode('lampiran_ktp_pasangan_sertifikat', ',');
            $arrData[$key]['ahli_waris'] = $lamp_ser->implode('ahli_waris', ',');
            $arrData[$key]['akta_hibah'] = $lamp_ser->implode('akta_hibah', ',');
            $arrData[$key]['ajb_ppjb'] = $lamp_ser->implode('ajb_ppjb', ',');
            $arrData[$key]['lampiran_pbb'] = $lamp_ser->implode('lampiran_pbb', ',');
            $arrData[$key]['lampiran_imb'] = $lamp_ser->implode('lampiran_imb', ',');
            $arrData[$key]['penjamin'] = Lpdk_penjamin::select(
                'nama_penjamin',
                'ibu_kandung_penjamin',
                'pasangan_penjamin',
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            $lamp = Lpdk_penjamin::select(
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            //$imp_ktp = implode(',', $lamp->toArray());
            // dd($lamp->implode('lampiran_ktp_penjamin', ','));
            // $i++;
            $arrData[$key]['lampiran_ktp_penjamin'] = $lamp->implode('lampiran_ktp_penjamin', ',');

            $lam = Lpdk_lampiran::select(

                'lampiran_npwp',
                'lampiran_surat_kematian',
                'lampiran_sk_desa',
                'lampiran_surat_lahir',
                'lampiran_surat_cerai',
                'lampiran_surat_nikah'
            )->where('id', $val->id_lampiran)->get();
            $arrData[$key]['lampiran_ktp_deb'] = $lam->implode('lampiran_ktp_deb', ',');
            $arrData[$key]['lampiran_ktp_pasangan'] = $lam->implode('lampiran_ktp_pasangan', ',');
            $arrData[$key]['lampiran_npwp'] = $lam->implode('lampiran_npwp', ',');
            $arrData[$key]['lampiran_surat_kematian'] = $lam->implode('lampiran_surat_kematian', ',');
            $arrData[$key]['lampiran_sk_desa'] = $lam->implode('lampiran_sk_desa', ',');
            $arrData[$key]['lampiran_skk'] = $lam->implode('lampiran_skk', ',');
            $arrData[$key]['lampiran_sku'] = $lam->implode('lampiran_sku', ',');
            $arrData[$key]['lampiran_slipgaji'] = $lam->implode('lampiran_slipgaji', ',');
            $arrData[$key]['lampiran_kk'] = $lam->implode('lampiran_kk', ',');
            $arrData[$key]['lampiran_surat_lahir'] = $lam->implode('lampiran_surat_lahir', ',');
            $arrData[$key]['lampiran_surat_cerai'] = $lam->implode('lampiran_surat_cerai', ',');
            $arrData[$key]['lampiran_surat_nikah'] = $lam->implode('lampiran_surat_nikah', ',');
            $arrData[$key]['created_at']       = $val->created_at;
            $arrData[$key]['updated_at']       = $val->updated_at;
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count' => count($arrData),
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

 public function indexQueueReturn(Request $request)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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

//  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
// $query_dir =  ViewApproval::with('pic','area', 'cabang');



$query_dir = Lpdk::with('pic','area', 'cabang')->where('status_kredit', 'REVISI')->orWhere('status_kredit', 'ON-QUEUE')->orderBy('status_kredit', 'DESC');

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
        // $lpdk = Lpdk::where('status_kredit', 'REVISI')->orWhere('status_kredit', 'ON-QUEUE')->orderBy('status_kredit', 'DESC')->get();
        //  $lpdk = Lpdk::paginate(10);
        if ($query === NULL) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }

        //    dd($lpdk);
        //  $i = 0;
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id']       = $val->id;
            $arrData[$key]['trans_so']       = $val->trans_so;
            $arrData[$key]['request_by']       = $val->request_by;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['nama_cabang']       =  Cabang::where('id',$val->id_cabang)->pluck('nama');
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $arrData[$key]['alamat_ktp_vs_jaminan']       = $val->alamat_ktp_vs_jaminan;
            $arrData[$key]['akta_notaris']       = $val->akta_notaris;
            $arrData[$key]['status_kredit']       = $val->status_kredit;
            $arrData[$key]['notes_progress']     = explode(',',$val->notes_progress);
            $arrData[$key]['notes_counter']     = explode(',',$val->notes_counter);
            $arrData[$key]['notes_cancel']     = $val->notes_cancel;
            $arrData[$key]['sla']     = $val->sla;
            //$arrData[$key]['id_sertifikat']       = $val->id_sertifikat;
            $ser_exp = explode(',', $val->id_sertifikat);
            // dd($ser_exp);
            $arrData[$key]['sertifikat']       = Lpdk_sertifikat::select(
                'nama_sertifikat',
                'status_sertifikat',
                'nama_pas_sertifikat',
                'status_pas_sertifikat',
                'no_sertifikat',
                'jenis_sertifikat',
                'tgl_berlaku_shgb'
                // 'lampiran_sertifikat',
                // 'lampiran_pbb',
                // 'lampiran_imb'
            )->whereIn('id', $ser_exp)->get();
            $lamp_ser = Lpdk_sertifikat::select(
                'lampiran_sertifikat',
                'lampiran_pbb',
                'lampiran_imb',
                'lampiran_ktp_sertifikat',
                'lampiran_ktp_pasangan_sertifikat',
                'ahli_waris',
                'akta_hibah',
                'ajb_ppjb'
            )->whereIn('id', $ser_exp)->get();
            // dd($lamp_ser->implode('lampiran_sertifikat' . ','));
            $arrData[$key]['lampiran_sertifikat'] = $lamp_ser->implode('lampiran_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_sertifikat'] = $lamp_ser->implode('lampiran_ktp_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_pasangan_sertifikat'] = $lamp_ser->implode('lampiran_ktp_pasangan_sertifikat', ',');
            $arrData[$key]['ahli_waris'] = $lamp_ser->implode('ahli_waris', ',');
            $arrData[$key]['akta_hibah'] = $lamp_ser->implode('akta_hibah', ',');
            $arrData[$key]['ajb_ppjb'] = $lamp_ser->implode('ajb_ppjb', ',');
            $arrData[$key]['lampiran_pbb'] = $lamp_ser->implode('lampiran_pbb', ',');
            $arrData[$key]['lampiran_imb'] = $lamp_ser->implode('lampiran_imb', ',');
            $arrData[$key]['penjamin'] = Lpdk_penjamin::select(
                'nama_penjamin',
                'ibu_kandung_penjamin',
                'pasangan_penjamin',
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            $lamp = Lpdk_penjamin::select(
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            //$imp_ktp = implode(',', $lamp->toArray());
            // dd($lamp->implode('lampiran_ktp_penjamin', ','));
            // $i++;
            $arrData[$key]['lampiran_ktp_penjamin'] = $lamp->implode('lampiran_ktp_penjamin', ',');

            $lam = Lpdk_lampiran::select(

                'lampiran_npwp',
                'lampiran_surat_kematian',
                'lampiran_sk_desa',
                'lampiran_surat_lahir',
                'lampiran_surat_cerai',
                'lampiran_surat_nikah'
            )->where('id', $val->id_lampiran)->get();
            $arrData[$key]['lampiran_ktp_deb'] = $lam->implode('lampiran_ktp_deb', ',');
            $arrData[$key]['lampiran_ktp_pasangan'] = $lam->implode('lampiran_ktp_pasangan', ',');
            $arrData[$key]['lampiran_npwp'] = $lam->implode('lampiran_npwp', ',');
            $arrData[$key]['lampiran_surat_kematian'] = $lam->implode('lampiran_surat_kematian', ',');
            $arrData[$key]['lampiran_sk_desa'] = $lam->implode('lampiran_sk_desa', ',');
            $arrData[$key]['lampiran_skk'] = $lam->implode('lampiran_skk', ',');
            $arrData[$key]['lampiran_sku'] = $lam->implode('lampiran_sku', ',');
            $arrData[$key]['lampiran_slipgaji'] = $lam->implode('lampiran_slipgaji', ',');
            $arrData[$key]['lampiran_kk'] = $lam->implode('lampiran_kk', ',');
            $arrData[$key]['lampiran_surat_lahir'] = $lam->implode('lampiran_surat_lahir', ',');
            $arrData[$key]['lampiran_surat_cerai'] = $lam->implode('lampiran_surat_cerai', ',');
            $arrData[$key]['lampiran_surat_nikah'] = $lam->implode('lampiran_surat_nikah', ',');
            $arrData[$key]['created_at']       = $val->created_at;
            $arrData[$key]['updated_at']       = $val->updated_at;
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count' => count($arrData),
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }


 public function indexAllStatus(Request $request)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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

//  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
// $query_dir =  ViewApproval::with('pic','area', 'cabang');

//dd($arrr);

$query_dir = Lpdk::with('pic','area', 'cabang')->orWhere('status_kredit', 'ON-PROGRESS')->orWhere('status_kredit', 'REALISASI');

$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
        //   $lpdk = Lpdk::where('status_kredit', 'ON-PROGRESS')->orWhere('status_kredit')->orWhere('status_kredit', 'REALISASI')->get();
        //  $lpdk = Lpdk::paginate(10);
        if ($query === NULL) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }

        //    dd($lpdk);
        //  $i = 0;
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id']       = $val->id;
            $arrData[$key]['trans_so']       = $val->trans_so;
            $arrData[$key]['request_by']       = $val->request_by;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['nama_cabang']       = Cabang::where('id',$val->id_cabang)->pluck('nama');
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $arrData[$key]['alamat_ktp_vs_jaminan']       = $val->alamat_ktp_vs_jaminan;
            $arrData[$key]['akta_notaris']       = $val->akta_notaris;
            $arrData[$key]['status_kredit']       = $val->status_kredit;
            $arrData[$key]['note_progress']       = $val->note_progress;
            $arrData[$key]['notes_counter']       = $val->notes_counter;
            $arrData[$key]['notes_cancel']       = $val->notes_cancel;
            $arrData[$key]['lain_lain']       = $val->lain_lain;
            $arrData[$key]['sla']       = $val->sla;
            //$arrData[$key]['id_sertifikat']       = $val->id_sertifikat;
            $ser_exp = explode(',', $val->id_sertifikat);
            // dd($ser_exp);
            $arrData[$key]['sertifikat']       = Lpdk_sertifikat::select(
                'nama_sertifikat',
                'status_sertifikat',
                'nama_pas_sertifikat',
                'status_pas_sertifikat',
                'no_sertifikat',
                'jenis_sertifikat',
                'tgl_berlaku_shgb'
                // 'lampiran_sertifikat',
                // 'lampiran_pbb',
                // 'lampiran_imb'
            )->whereIn('id', $ser_exp)->get();
            $lamp_ser = Lpdk_sertifikat::select(
                'lampiran_sertifikat',
                'lampiran_pbb',
                'lampiran_imb',
                'lampiran_ktp_sertifikat',
                'lampiran_ktp_pasangan_sertifikat',
                'ahli_waris',
                'akta_hibah',
                'ajb_ppjb'
            )->whereIn('id', $ser_exp)->get();
            // dd($lamp_ser->implode('lampiran_sertifikat' . ','));
            $arrData[$key]['lampiran_sertifikat'] = $lamp_ser->implode('lampiran_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_sertifikat'] = $lamp_ser->implode('lampiran_ktp_sertifikat', ',');
            $arrData[$key]['lampiran_ktp_pasangan_sertifikat'] = $lamp_ser->implode('lampiran_ktp_pasangan_sertifikat', ',');
            $arrData[$key]['ahli_waris'] = $lamp_ser->implode('ahli_waris', ',');
            $arrData[$key]['akta_hibah'] = $lamp_ser->implode('akta_hibah', ',');
            $arrData[$key]['ajb_ppjb'] = $lamp_ser->implode('ajb_ppjb', ',');
            $arrData[$key]['lampiran_pbb'] = $lamp_ser->implode('lampiran_pbb', ',');
            $arrData[$key]['lampiran_imb'] = $lamp_ser->implode('lampiran_imb', ',');
            $arrData[$key]['penjamin'] = Lpdk_penjamin::select(
                'nama_penjamin',
                'ibu_kandung_penjamin',
                'pasangan_penjamin',
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            $lamp = Lpdk_penjamin::select(
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            //$imp_ktp = implode(',', $lamp->toArray());
            // dd($lamp->implode('lampiran_ktp_penjamin', ','));
            // $i++;
            $arrData[$key]['lampiran_ktp_penjamin'] = $lamp->implode('lampiran_ktp_penjamin', ',');

            $lam = Lpdk_lampiran::select(

                'lampiran_npwp',
                'lampiran_surat_kematian',
                'lampiran_sk_desa',
                'lampiran_surat_lahir',
                'lampiran_surat_cerai',
                'lampiran_surat_nikah'
            )->where('id', $val->id_lampiran)->get();
            $arrData[$key]['lampiran_ktp_deb'] = $lam->implode('lampiran_ktp_deb', ',');
            $arrData[$key]['lampiran_ktp_pasangan'] = $lam->implode('lampiran_ktp_pasangan', ',');
            $arrData[$key]['lampiran_npwp'] = $lam->implode('lampiran_npwp', ',');
            $arrData[$key]['lampiran_surat_kematian'] = $lam->implode('lampiran_surat_kematian', ',');
            $arrData[$key]['lampiran_sk_desa'] = $lam->implode('lampiran_sk_desa', ',');
            $arrData[$key]['lampiran_skk'] = $lam->implode('lampiran_skk', ',');
            $arrData[$key]['lampiran_sku'] = $lam->implode('lampiran_sku', ',');
            $arrData[$key]['lampiran_slipgaji'] = $lam->implode('lampiran_slipgaji', ',');
            $arrData[$key]['lampiran_kk'] = $lam->implode('lampiran_kk', ',');
            $arrData[$key]['lampiran_surat_lahir'] = $lam->implode('lampiran_surat_lahir', ',');
            $arrData[$key]['lampiran_surat_cerai'] = $lam->implode('lampiran_surat_cerai', ',');
            $arrData[$key]['lampiran_surat_nikah'] = $lam->implode('lampiran_surat_nikah', ',');
            $arrData[$key]['created_at']       = $val->created_at;
            $arrData[$key]['updated_at']       = $val->updated_at;
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count' => count($arrData),
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function indexRealisasi(Request $request)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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


$query_dir = Lpdk::with('pic','area', 'cabang')->where('status_kredit', '=', 'REALISASI');
$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        // $lpdk =  DB::connection('web')->table('vw_memo_ca_approve')->get();
        //  $lpdk = Lpdk::paginate(10);
        //   dd($lpdk);
        if ($query === '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data LPDK Kosong'
            ], 404);
        }

        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id']       = $val->id;
            $arrData[$key]['trans_so']       = $val->trans_so;
            $arrData[$key]['request_by']       = $val->request_by;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['id_cabang']       = Cabang::where('id',$val->id_cabang)->pluck('nama');
$arrData[$key]['request_by']   = $val->request_by;
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $arrData[$key]['alamat_ktp_vs_jaminan']       = $val->alamat_ktp_vs_jaminan;
            $arrData[$key]['akta_notaris']       = $val->akta_notaris;
            $arrData[$key]['status_kredit']       = $val->status_kredit;
            $arrData[$key]['note_progress']       = $val->note_progress;
            $arrData[$key]['note_counter']       = $val->note_counter;
            //$arrData[$key]['id_sertifikat']       = $val->id_sertifikat;
            $ser_exp = explode(',', $val->id_sertifikat);
            // dd($ser_exp);
            $arrData[$key]['sertifikat']       = Lpdk_sertifikat::select(
'id',                
'nama_sertifikat',
                'status_sertifikat',
                'hub_cadeb',
                'nama_pas_sertifikat',
                'status_pas_sertifikat',
                'no_sertifikat',
                'jenis_sertifikat',
                'tgl_berlaku_shgb',
                'lampiran_ktp_sertifikat',
                'lampiran_ktp_pasangan_sertifikat',
                'ahli_waris',
                'akta_hibah',
                'ajb_ppjb',
                'lampiran_sertifikat',
                'lampiran_imb',
                'lampiran_pbb'
            )->whereIn('id', $ser_exp)->get();
            $arrData[$key]['penjamin'] = Lpdk_penjamin::select(
'id',                
'nama_penjamin',
                'ibu_kandung_penjamin',
                'pasangan_penjamin',
                'lampiran_ktp_penjamin'
            )->whereIn('id', explode(',', $val->id_penjamin))->get();
            $arrData[$key]['lampiran'] = Lpdk_lampiran::select(
'id',                
                'lampiran_npwp',
                'lampiran_surat_kematian',
                'lampiran_sk_desa',
                'lampiran_surat_lahir',
                'lampiran_surat_cerai',
                'lampiran_surat_nikah'
            )->where('id', $val->id_lampiran)->get();
            $arrData[$key]['created_at']       = $val->created_at;
            $arrData[$key]['updated_at']       = $val->updated_at;
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count' => count($arrData),
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
    public function show(Request $request, $id)
    {
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;
//dd($pic);
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

//  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
$query_dir =  ViewApproval::with('pic','area', 'cabang')->where('id_trans_so', $id);
//   ->orderBy('created_at', 'desc');
$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        // $lpdk =  DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->get();
        //   dd($lpdk);
$deb = TransSO::where('id', $id)->first();
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id_trans_so']       = $val->id_trans_so;
//$arrData[$key]['request_by']   = $val->request_by;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['id_pic']       = $val->id_pic;
            $arrData[$key]['id_area']       = $val->id_area;
            $arrData[$key]['nama_cabang']       = Cabang::where('id',$val->id_cabang)->pluck('nama');
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            //$arrData[$key]['request_by']       = $val->request_by;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
$arrData[$key]['id_debitur']       = $deb->id_calon_debitur;
     $arrData[$key]['id_pasangan']       = $deb->id_pasangan;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            
            $det = explode(',', $val->id_penjamin);
            $arrData[$key]['penjamin']       = Penjamin::select('id','nama_ktp', 'nama_ibu_kandung', 'no_ktp', 'no_npwp', 'tempat_lahir', 'tgl_lahir', 'jenis_kelamin', 'lamp_ktp')->whereIn('id', $det)->get();

            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;
            $ser = explode(',', $val->id_agunan_tanah);
            $arrData[$key]['sertifikat']       = AgunanTanah::select('id','nama_pemilik_sertifikat', 'jenis_sertifikat', 'no_sertifikat', 'tgl_berlaku_shgb', 'lamp_sertifikat', 'lamp_imb', 'lamp_pbb')->whereIn('id', $ser)->get();
            $ken = explode(',', $val->id_agunan_kendaraan);
            $arrData[$key]['kendaraan']       = AgunanKendaraan::select('id','no_bpkb', 'nama_pemilik', 'alamat_pemilik', 'merk', 'jenis', 'no_rangka', 'no_mesin', 'warna', 'tahun', 'no_polisi', 'no_stnk', 'tgl_kadaluarsa_pajak', 'tgl_kadaluarsa_stnk', 'no_faktur', 'lamp_agunan_depan', 'lamp_agunan_kanan', 'lamp_agunan_kiri', 'lamp_agunan_belakang', 'lamp_agunan_dalam')->whereIn('id', $ken)->get();
            $arrData[$key]['lampiran_kk']       = $val->lampiran_kk;
            $arrData[$key]['lampiran_npwp']       = NULL;
            $arrData[$key]['lampiran_ktp_deb']       = $val->lampiran_ktp_deb;
            $arrData[$key]['lampiran_ktp_pasangan']       = $val->lampiran_ktp_pasangan;
            $arrData[$key]['lamp_buku_nikah']       = $val->lamp_buku_nikah;
            $arrData[$key]['lampiran_surat_cerai']       = $val->lampiran_surat_cerai;
            $arrData[$key]['lampiran_surat_lahir']       = NULL;
            $arrData[$key]['lampiran_surat_kematian']       = NULL;
            $arrData[$key]['lampiran_surat_keterangan_desa']       = NULL;
            $arrData[$key]['lampiran_surat_ajb_ppjb']       = NULL;
            $arrData[$key]['lampiran_ahliwaris']       = NULL;
            $arrData[$key]['lampiran_akta_hibah']       = NULL;
        }
        //   $lpdk = Lpdk::where('id_calon_debitur', $id)->first();
        if ($query === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Tidak Ada Data Lpdk dengan id =' . $id
            ], 404);
        }
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'  => $arrData
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store($id, Request $req)
    {

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

//dd($pic);
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

//  $query_dir = Lpdk::with('pic', 'cabang')->orderBy('created_at', 'desc');
$query_dir =  ViewApproval::with('pic','area', 'cabang')->where('id_trans_so', $id);
//   ->orderBy('created_at', 'desc');
$query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang); 
        $user_nama = $req->auth->nama;
        //   dd($pic->nama);
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['id_trans_so']       = $val->id_trans_so;
            $arrData[$key]['nomor_so']       = $val->nomor_so;
            $arrData[$key]['nama_so']       = $val->nama_so;
            $arrData[$key]['asal_data']       = $val->asal_data;
            $arrData[$key]['area_kerja']       = $val->area_kerja;
            $arrData[$key]['nama_marketing']       = $val->nama_marketing;
            $arrData[$key]['plafon']       = $val->plafon;
            $arrData[$key]['tenor']       = $val->tenor;
            $arrData[$key]['nama_debitur']       = $val->nama_debitur;
            $arrData[$key]['nama_pasangan']       = $val->nama_pasangan;
            $arrData[$key]['nama_penjamin']       = $val->nama_penjamin;
            $arrData[$key]['status_nikah']       = $val->status_nikah;
            $arrData[$key]['produk']       = $val->produk;

            $arrData[$key]['id_agunan_tanah']       = $val->id_agunan_tanah;
            $arrData[$key]['id_penjamin']       = $val->id_penjamin;
        }
        //  dd($arrData[0]['id_penjamin']);
        // $cek_lpdk =  DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->first();
       // dd($cek_lpdk);
        $explode = explode(',', $arrData[0]['id_agunan_tanah']);
        $explode_penjamin = explode(',', $arrData[0]['id_penjamin']);
    
        // $in = in_array($explode);
        $getsertifikat = AgunanTanah::select('nama_pemilik_sertifikat AS nama_sertifikat', 'jenis_sertifikat', 'no_sertifikat', 'tgl_berlaku_shgb', 'lamp_sertifikat AS lampiran_sertifikat', 'lamp_pbb', 'lamp_imb')->whereIn('id', $explode)->get();
        $getpenjamin = Penjamin::select('nama_ktp AS nama_penjamin', 'nama_ibu_kandung AS ibu_kandung_penjamin', 'lamp_ktp AS lampiran_ktp_penjamin')->whereIn('id', $explode_penjamin)->get();
        //dd($getsertifikat);




        //  dd($arrData);
        if ($query === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id)->first();
        //  $check_lpdk = Lpdk::where('id', $id)
        $check_debt = DB::connection('web')->table('view_approval_caa')->where('id_trans_so', $id)->first();

        //  dd($check_debt->id_trans_so);

        // $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/debitur';

        $check_lamp_kk             = $check_debt->lampiran_kk;
        $check_lamp_ktp             = $check_debt->lampiran_ktp_deb;
        // $check_lamp_suratcerai             = $check_debt->lampiran_surat_cerai;
        $check_lamp_ktppas              = $check_debt->lampiran_ktp_pasangan;
        // $check_lamp_bukunikah             = $check_debt->lamp_buku_nikah;
        $check_lamp_ktppen             = $check_debt->lampiran_ktp_penjamin;
        // // $check_lamp_npwp             = $check_debt->lampiran_npwp;
        $check_lamp_sertifikat      = $check_debt->lampiran_sertifikat;
        $check_lamp_sttp_pbb        = $check_debt->lampiran_pbb;
        $check_lamp_imb             = $check_debt->lampiran_imb;

    

        foreach ($getpenjamin as $value) {
            // dd($val['no_sertifikat']);

        }
        $getpen =  $getpenjamin->toArray();
        if (!empty($req->input('nama_penjamin'))) {
            for ($i = 0; $i < count($req->input('nama_penjamin')); $i++) {
                $data_penj[] = array(
                    'trans_so' => $id,
                    'nama_penjamin' => empty($req->input('nama_penjamin')[$i]) ? $getpen[$i]['nama_penjamin'] : $req->input('nama_penjamin')[$i],
                    'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')[$i]) ? $getpen[$i]['ibu_kandung_penjamin'] : $req->input('ibu_kandung_penjamin')[$i],
                    'pasangan_penjamin' => empty($req->input('pasangan_penjamin')[$i]) ? $value['pasangan_penjamin'] : $req->input('pasangan_penjamin')[$i],
                    // 'lampiran_ktp_penjamin'       => $lamp_penjamin[$i],
                );
            }
        }
        //  dd($data_penj);
        if (!empty($req->input('nama_penjamin'))) {
            for ($j = 0; $j < count($data_penj); $j++) {
                $penj = Lpdk_penjamin::create($data_penj[$j]);
                $id_penjamin['id'][$j] = $penj->id;
            }
            $penID = implode(",", $id_penjamin['id']);
        } else {
            $penID = null;
        }

        $arr = array();
        foreach ($getsertifikat as $key => $val) {
            $arr[$key]['no_sertifikat'] = $val['no_sertifikat'];
            $arr[$key]['nama_sertifikat'] = $val['nama_sertifikat'];
            $arr[$key]['jenis_sertifikat'] = $val['jenis_sertifikat'];
            $arr[$key]['tgl_berlaku_shgb'] = $val['tgl_berlaku_shgb'];
            $arr[$key]['lampiran_sertifikat'] = $val['lampiran_sertifikat'];
            $arr[$key]['lamb_imb'] = $val['lamb_imb'];
            $arr[$key]['lamp_pbb'] = $val['lamp_pbb'];
        }
        $get =  $getsertifikat->toArray();
        // for ($d = 0; $d <= $get; $d++) {
        //     dd($get[$d]['nama_sertifikat']);
        // }
        // //lampiran 
        if (!empty($req->input('no_sertifikat'))) {
            for ($i = 0; $i < count($req->input('no_sertifikat')); $i++) {
                // dd($val['no_sertifikat']);
                $data_sert[] = array(
                    'trans_so' => $id,
                    'no_sertifikat' => empty($req->input('no_sertifikat')[$i]) ? $get[$i]['no_sertifikat'] : $req->input('no_sertifikat')[$i],
                    'nama_sertifikat' => empty($req->input('nama_sertifikat')[$i]) ? $get[$i]['nama_sertifikat'] : $req->input('nama_sertifikat')[$i],
                    'status_sertifikat' => empty($req->input('status_sertifikat')[$i]) ? $val['status_sertifikat'] : $req->input('status_sertifikat')[$i],
                    'jenis_sertifikat' => empty($req->input('jenis_sertifikat')[$i]) ? $get[$i]['jenis_sertifikat'] : $req->input('jenis_sertifikat')[$i],
                    'hub_cadeb' => empty($req->input('hub_cadeb')) ? NULL : $req->input('hub_cadeb')[$i],
                    'nama_pas_sertifikat' => empty($req->input('nama_pasangan_sertifikat')[$i]) ? NULL : $req->input('nama_pasangan_sertifikat')[$i],
                    'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')) ? NULL : $req->input('status_pas_sertifikat')[$i],
                    'tgl_berlaku_shgb' => empty(Carbon::parse($req->input('tgl_berlaku_shgb')[$i])->format('Y-m-d')) ? Carbon::parse($get[$i]['tgl_berlaku_shgb'])->format('Y-m-d') : Carbon::parse($req->input('tgl_berlaku_shgb')[$i])->format('Y-m-d'),
                    // 'ahli_waris' => $lamp_ahliwaris[$i],
                    // 'akta_hibah' => $lamp_aktahibah[$i],
                    // 'ajb_ppjb' =>  $lamp_ajb_ppjb[$i],
                    // 'lampiran_ktp_sertifikat' => $lamp_ktp_sertifikat[$i],
                    // 'lampiran_ktp_pasangan_sertifikat' => $lamp_ktp_pas_sertifikat[$i],
                    // 'lampiran_sertifikat' => $lamp_sertifikat[$i],
                    // 'lampiran_imb' =>  $lamp_imb[$i],
                    // 'lampiran_pbb' => $lamp_sttp_pbb[$i]

                );
            }
        }

        //   dd($data_sert);

        if (!empty($req->input('no_sertifikat'))) {
            for ($s = 0; $s < count($data_sert); $s++) {
                $sert = Lpdk_sertifikat::create($data_sert[$s]);
                $id_sert['id'][$s] = $sert->id;
            }
            $serID = implode(",", $id_sert['id']);
        } else {
            $serID = null;
        }


        // if (!empty($lamp_ktp)) {
        //     for ($x = 0; $x < count($lamp_ktp); $x++) {

        $data_lamp = array(
            'trans_so' => $check_debt->id_trans_so,
            // 'lampiran_ktp_deb' => $lamp_ktpdeb,
            // 'lampiran_ktp_pasangan' => $lamp_ktppas,
            // //'lampiran_ktp_penjamin' => $lamp_ktp_pen,
            //   'lampiran_npwp' => $lamp_npwp,
            // 'lampiran_pbb' => $lamp_sttp_pbb[$x],
            // 'lampiran_imb' => $lamp_imb[$x],
            // // 'lampiran_skk' => $lamp_skk,
            // // 'lampiran_sku' => $lamp_sku,
            // // 'lampiran_slipgaji' => $lamp_slip_gaji,
            // 'lampiran_surat_kematian' => $lamp_sk_kematian[$x],
            // 'lampiran_sk_desa'  => $lamp_sk_desa[$x],
            // 'lampiran_ajb' => $lamp_ajb[$x],
            // 'lampiran_ahliwaris' => $lamp_ahliwaris[$x],
            // 'lampiran_aktahibah'   => $lamp_aktahibah[$x],
            // 'lampiran_kk'       => $lamp_kk,
            // 'lampiran_surat_lahir'  => $lamp_suratlahir[$x],
            'lampiran_surat_nikah'  => $check_debt->lamp_buku_nikah,
            'lampiran_surat_cerai'  => $check_debt->lampiran_surat_cerai,
            //'lampiran_sertifikat' =>  $check_debt->lampiran_surat_cerai,
            // 'lampiran_ktp_pasangan_sertifikat' => $lamp_ktppassert,
        );
        //  }
        //   }

        // dd($data_lamp);

        // if (!empty($req->file('lampiran_ktp_deb'))) {
        //     for ($y = 0; $y < count($data_lamp); $y++) {
        $lamp = Lpdk_lampiran::create($data_lamp);
        //         $id_lamp['id'][$y] = $lamp->id;
        //     }
        //     $lamID = implode(",", $id_lamp['id']);
        // } else {
        //     $lamID = null;
        // }
        // $data_sertifikat = array(
        //     'trans_so'  => $cek_lpdk->id_trans_so,
        //     'no_sertifikat' => empty($req->input('no_sertifikat')) ? $cek_lpdk->no_sertifikat : $req->input('no_sertifikat'),
        //     'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? $cek_lpdk->nama_sertifikat : $req->input('nama_sertifikat'),
        //     'nama_sertifikat' => $req->input('nama_pasangan_pemilik'),
        //     'hub_cadeb' => empty($req->input('hub_cadeb')) ? $cek_lpdk->hub_cadeb : $req->input('hub_cadeb'),
        //     'jenis_sertifikat' => empty($req->input('jenis_sertifikat')) ? $cek_lpdk->jenis_sertifikat : $req->input('jenis_sertifikat'),
        //     'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')) ? $cek_lpdk->tgl_berlaku_shgb : $req->input('tgl_berlaku_shgb'),
        // );

        //    dd($data_sertifikat['status_sertifikat']);
        // $penjamin = array(
        //     'trans_so'  => $cek_lpdk->id_trans_so,
        //     'nama_penjamin' => empty($req->input('nama_penjamin')) ? $cek_lpdk->nama_penjamin : $req->input('nama_penjamin'),
        //     'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? $cek_lpdk->ibu_kandung_penjamin : $req->input('ibu_kandung_penjamin')
        // );
        if (!empty($req->input('akta_notaris'))) {
            for ($i = 0; $i < count($req->input('akta_notaris')); $i++) {
                $akta[] = array(
                    'akta_notaris' => empty($req->input('akta_notaris')[$i]) ? $query->akta_notaris[$i] : $req->input('akta_notaris')[$i],
                );
            }
        }
        
        $pic_so = TransSO::where('id',$id)->first();
      $col = array_column($akta,'akta_notaris');
      $implode = implode(',',$col);


        $data_lpdk = array(
            'trans_so'  => $arrData[0]['id_trans_so'],
            'nomor_so' => $arrData[0]['nomor_so'],
            'id_pic' => $pic_so->id_pic,
            'id_area' => $pic_so->id_area,
            'id_cabang' => $pic_so->id_cabang,
            'nama_so' => $arrData[0]['nama_so'],
            'asal_data' => $arrData[0]['asal_data'],
            'request_by' => $user_nama,
            'nama_marketing' => empty($req->input('nama_marketing')) ? $arrData[0]['nama_marketing'] :   $req->input('nama_marketing'),
            'area_kerja' =>  $arrData[0]['area_kerja'],
            'plafon' => $arrData[0]['plafon'],
            'tenor' => $arrData[0]['tenor'],
            'nama_debitur' => empty($req->input('nama_debitur')) ? $arrData[0]['nama_debitur'] :   $req->input('nama_debitur'),
            'nama_pasangan' => empty($req->input('nama_pasangan')) ? $arrData[0]['nama_pasangan'] :  $req->input('nama_pasangan'),

            'status_nikah' => empty($req->input('status_nikah')) ? $arrData[0]['status_nikah'] : $req->input('status_nikah'),
            'produk' => empty($req->input('produk')) ? $arrData[0]['produk'] : $req->input('produk'),
            // 'hub_cadeb' => empty($req->input('hub_cadeb')) ? $query->hub_cadeb : $req->input('hub_cadeb'),
            'akta_notaris' => $implode,
            'alamat_ktp_vs_jaminan' => empty($req->input('alamat_ktp_vs_jaminan')) ? null : $req->input('alamat_ktp_vs_jaminan'),
            'lain_lain' => empty($req->input('akta_notaris_note_lainlain')) ? null : $req->input('akta_notaris_note_lainlain'),
            'id_penjamin' => $penID,
            'id_sertifikat' => $serID,
            'id_lampiran'  => $lamp->id,
            'status_kredit' => 'ON-QUEUE',
            'created_at' => Carbon::now()
        );

     
        // dd(array_merge($data_lpdk, $data_sertifikat));
        // $lampiran = array(
        //     'trans_so'  => $cek_lpdk->id_trans_so,
        //     'lampiran_ktp_deb' =>  $check_lamp_ktp,
        //     'lampiran_ktp_pasangan' =>  $check_lamp_ktppas,
        //     'lampiran_ktp_penjamin' =>  $check_lamp_ktppen,
        //     // 'lampiran_npwp' => $lamp_npwp,
        //     'lampiran_sertifikat' =>  $check_lamp_sertifikat,
        //     'lampiran_pbb' => $check_lamp_sttp_pbb,
        //     'lampiran_imb' =>  $check_lamp_imb,
        //     'lampiran_kk' => $check_lamp_kk,
        //     'lampiran_surat_cerai' => $check_lamp_suratcerai,
        //     'lampiran_surat_nikah' => $check_lamp_bukunikah
        // );
        //   dd($data);

        // if ($data_lpdk || $data_sertifikat === null) {
        //     return response()->json([
        //         'code'  => 403,
        //         'message'   => 'data harus di input',
        //         'data'  => $data_lpdk
        //     ]);
        // }

        //  dd(Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->first());
        //  dd(Lpdk::get());

        if (Lpdk::where('nomor_so', $arrData[0]['nomor_so'])->exists()) {
            return response()->json([
                "Code"      => 409,
                "Status"    => "Conflict",
                "Message"   => "Nomor SO pengajuan LPDK Sudah Ada"
            ], 409);
        }
        // $arrData = array();
        // $i = 0;
        // foreach ($getsertifikat as $key => $val) {
        //     $arrData[$key][$i]['nama_pemilik_sertifikat']       = $val->nama_pemilik_sertifikat;
        //     $arrData[$key][$i]['jenis_sertifikat']   = $val->jenis_sertifikat;
        //     $arrData[$key][$i]['no_sertifikat']    = $val->no_sertifikat;
        //     // $arrData[$key]['tgl_berlaku_shgb'] = $val->tgl_berlaku_shgb;
        //     $arrData[$key][$i]['lamp_sertifikat']   = $val->lamp_sertifikat;
        //     $arrData[$key][$i]['lamp_imb']     = $val->lamp_imb;
        //     $arrData[$key][$i]['lamp_pbb']     = $val->lamp_pbb;

        //     //  dd($arrData);




        //     //}
        //     // dd($arrData);

        //     $sertifikat = Lpdk_sertifikat::create([
        //         'trans_so' => $id,
        //         'nama_sertifikat' => $val['nama_pemilik_sertifikat'],
        //         'jenis_sertifikat' => $val['jenis_sertifikat'],
        //         'no_sertifikat' => $val['no_sertifikat'],
        //         'tgl_berlaku_shgb' => $val['tgl_berlaku_shgb'],
        //         'lampiran_sertifikat' => $val['lamp_sertifikat'],
        //         'lamp_imb' => $val['lamp_imb'],
        //         'lamp_pbb' => $val['lamp_pbb']
        //     ]);
        // }

        $get_id = Lpdk_sertifikat::select('id')->where('trans_so', $id)->get();

        $str1 = str_replace('[', '', $get_id);
        $str2 = str_replace('{', '', $str1);
        $str3 = str_replace('"', '', $str2);
        $str4 = str_replace('}', '', $str3);
        $str5 = str_replace(']', '', $str4);
        $str6 = str_replace('id:', '', $str5);

        // $arrpenj = array();
        // $i = 0;
        // foreach ($getpenjamin as $key => $pen) {
        //     $arrpenj[$key][$i]['trans_so']       = $pen->trans_so;
        //     $arrpenj[$key][$i]['nama_penjamin']   = $pen->nama_penjamin;
        //     $arrpenj[$key][$i]['ibu_kandung_penjamin']    = $pen->ibu_kandung_penjamin;
        //     $arrpenj[$key][$i]['pasangan_penjamin']   = $pen->pasangan_penjamin;
        //     $arrpenj[$key][$i]['lampiran_ktp_penjamin']     = $pen->lampiran_ktp_penjamin;




        //     $penj = Lpdk_penjamin::create([
        //         'trans_so'       => $id,
        //         'nama_penjamin'   => $pen['nama_penjamin'],
        //         'ibu_kandung_penjamin'    => $pen['ibu_kandung_penjamin'],
        //         'pasangan_penjamin'   => $pen['pasangan_penjamin'],
        //         'lampiran_ktp_penjamin'     => $pen['lampiran_ktp_penjamin']
        //     ]);
        // }
        $get_pen = Lpdk_penjamin::select('id')->where('trans_so', $id)->get();

        $str1 = str_replace('[', '', $get_pen);
        $str2 = str_replace('{', '', $str1);
        $str3 = str_replace('"', '', $str2);
        $str4 = str_replace('}', '', $str3);
        $str5 = str_replace(']', '', $str4);
        $strpen = str_replace('id:', '', $str5);

        //$lamp = Lpdk_lampiran::create($lampiran);

        Lpdk::create($data_lpdk)->update([
            'id_sertifikat' => $serID,
            //'id_lampiran' => $lamp->id, 
            'id_penjamin' => $penID
        ]);

        try {
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => array($data_lpdk, 'sertifikat' => $data_sert, 'lampiran' => $data_lamp),
                'id' => array('id_penjamin' => $penID, 'id_sertifikat' => $serID, 'id_lampiran' => $lamp->id)

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

    public function updateLampiran($id, Request $req)
    {
        // $coba = $req->input('coba');
        // dd($coba);

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

//dd($pic);
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

        $get_lpdk = Lpdk::get();

        // if ($get_lpdk === null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'data lpdk kosong'
        //     ], 404);
        // }
        $cek_lpdk = Lpdk::where('trans_so', $id)->first();

        $cek_sertif = Lpdk_sertifikat::where('trans_so', $id)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id)->first();
        //  $check_lpdk = Lpdk::where('id', $id)
        $check_debt = Lpdk::where('trans_so', $id)->first();

        $check_lamp = Lpdk_lampiran::where('trans_so', $id)->first();

        $check_sert = Lpdk_sertifikat::where('trans_so', $id)->first();

        $check_ktp_pen = Lpdk_penjamin::where('trans_so', $id)->first();
        // dd($check_debt->lampiran_ktp_deb);
        //    dd($check_debt);

        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';
        $check_lamp_ktp             = $check_lamp->lampiran_ktp_deb;
        $check_lamp_ktppas              = $check_lamp->lampiran_ktp_pasangan;
        $check_lamp_ktppen             = $check_lamp->lampiran_ktp_penjamin;
        $check_lamp_npwp             = $check_lamp->lampiran_npwp;
        $check_lamp_surat_kematian = $check_lamp->lampiran_surat_kematian;
        $check_lamp_sk_desa = $check_lamp->lampiran_sk_desa;
        $check_lamp_ajb = $check_lamp->lampiran_ajb;
        $check_lamp_ahliwaris = $check_lamp->lampiran_ahliwaris;
        $check_lamp_aktahibah = $check_lamp->lampiran_aktahibah;

        $check_lamp_sertifikat      = $check_lamp->lampiran_sertifikat;
        $check_lamp_sttp_pbb        = $check_lamp->lampiran_pbb;
        $check_lamp_imb             = $check_lamp->lampiran_imb;
        $check_lamp_skk             = $check_lamp->lampiran_skk;
        $check_lamp_sku             = $check_lamp->lampiran_sku;
        $check_lamp_slip_gaji       = $check_lamp->lampiran_slipgaji;
        $check_lamp_kk              = $check_lamp->lampiran_kk;
        $check_surat_lahir    = $check_lamp->lampiran_surat_lahir;
        $check_surat_nikah    = $check_lamp->lampiran_surat_nikah;
        $check_surat_cerai    = $check_lamp->lampiran_surat_cerai;
        $check_lamp_ktp_pemilik_sert   = $check_lamp->lampiran_ktp_pemilik_sertifikat;
        $check_lamp_ktp_pasangan_sert   = $check_lamp->lampiran_ktp_pasangan_sertifikat;
     //   $check_lamp_ktp_pasangan_penjamin   = $check_ktp_pen->lampiran_ktp_penjamin;
        // $check_foto_pembukuan_usaha = $check_lamp->foto_pembukuan_usaha;
        // $check_lamp_foto_usaha      = $check_lamp->lamp_foto_usaha;
        // $check_lamp_surat_cerai     = $check_lamp->lamp_surat_cerai;
        // $check_lamp_tempat_tinggal  = $check_lamp->lamp_tempat_tinggal;

        //$dd = $req->input('hub_cadeb');
        //lampiran 
        //dd($dd);

        //LAMPIRAN


        if ($file = $req->file('lampiran_npwp')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/updatelamp';
            $name = '';
            $check = $check_lamp_npwp;
            $lamp_npwp = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_npwp = $check_lamp_npwp;
        }



        // if ($file = $req->file('lampiran_skk')) {
        //     $name = 'lamp_skk.';
        //     $check = $check_lamp_skk;
        //     foreach ($files as $file) {
        //         $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
        //     }

        //     $lamp_skk = $arrayPath;
        // } else {
        //     $lamp_skk = $check_lamp_skk;
        // }

        // if ($files = $req->file('lampiran_sku')) {
        //     $name = 'lamp_sku.';
        //     $check = $check_lamp_sku;
        //     foreach ($files as $file) {
        //         $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
        //     }

        //     $lamp_sku = $arrayPath;
        // } else {
        //     $lamp_sku = $check_lamp_sku;
        // }


        // if ($file = $req->file('lampiran_slipgaji')) {
        //     $name = 'lamp_slip_gaji.';
        //     $check = $check_lamp_slip_gaji;

        //     $lamp_slip_gaji = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_slip_gaji = $check_lamp_slip_gaji;
        // }


        if ($file = $req->file('lampiran_surat_kematian')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/updatelamp';
            $name = '';
            $check = $check_lamp_surat_kematian;
            $lamp_sk_kematian = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sk_kematian = $check_lamp_surat_kematian;
        }

        if ($file = $req->file('lampiran_sk_desa')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/updatelamp';
            $name = '';
            $check = $check_lamp_sk_desa;
            $lamp_sk_desa = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sk_desa = $check_lamp_sk_desa;
        }

       // if ($file = $req->file('lampiran_kk')) {
         //   $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/updatelamp';
           // $name = 'lamp_kk.';
           // $check = $check_lamp_kk;
           // $lamp_kk = Helper::uploadImg($check, $file, $path, $name);
        //} else {
          //  $lamp_kk = $check_lamp_kk;
       // }

        if ($file = $req->file('lampiran_surat_lahir')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/updatelamp';
            $name = '';
            $check = $check_surat_lahir;
            $lamp_suratlahir = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratlahir = $check_surat_lahir;
        }

        if ($file = $req->file('lampiran_surat_nikah')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/updatelamp';
            $name = '';
            $check = $check_surat_nikah;
            $lamp_suratnikah = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratnikah = $check_surat_nikah;
        }

        if ($file = $req->file('lampiran_surat_cerai')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/updatelamp';
            $name = '';
            $check = $check_surat_cerai;
            $lamp_suratcerai = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_suratcerai = $check_surat_cerai;
        }


        //SERTIFIKAT

        if ($file = $req->file('lampiran_sertifikat')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';
            $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sertifikat = null;
        }


        if ($file = $req->file('lampiran_pbb')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';
            $lamp_sttp_pbb  = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sttp_pbb = null;
        }

        if ($file = $req->file('lampiran_imb')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';
            $lamp_imb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_imb = null;
        }

        if ($file = $req->file('lampiran_ajb')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';
            $lamp_ajb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ajb = null;
        }

        if ($file = $req->file('lampiran_ahliwaris')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';
            $lamp_ahliwaris = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ahliwaris = null;
        }

        if ($file = $req->file('lampiran_aktahibah')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';
            $lamp_aktahibah  = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_aktahibah = null;
        }

        if ($file = $req->file('lampiran_ktp_pem_sertifikat')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';
            $lamp_ktppemsert = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktppemsert = null;
        }

        if ($file = $req->file('lampiran_ktp_pas_sertifikat')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat/update';
            $name = '';
            $check = 'null';

            $lamp_ktppassert =  Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktppassert = null;
        }



        $note_cek = Lpdk::where('trans_so', $id)->first();

        //  DB::connection('web')->beginTransaction();


        // if (!empty($lamp_ktp)) {
        //     for ($x = 0; $x < count($lamp_ktp); $x++) {

        $data_lamp = array(
            // 'trans_so' => $id,
            //'lampiran_ktp_penjamin' => $lamp_ktp_pen,
            'lampiran_npwp' => empty($lamp_npwp) ? NULL : $lamp_npwp,
            'lampiran_surat_kematian' => empty($lamp_sk_kematian) ? NULL : $lamp_sk_kematian,
            'lampiran_sk_desa'  => empty($lamp_sk_desa) ? NULL : $lamp_sk_desa,
            // 'lampiran_ajb' => $lamp_ajb,
            // 'lampiran_ahliwaris' => $lamp_ahliwaris,
            // 'lampiran_aktahibah'   => $lamp_aktahibah,
            //'lampiran_kk'       => empty($lamp_kk) ? null : $lamp_kk,
            'lampiran_surat_lahir'  => empty($lamp_suratlahir) ? null : $lamp_suratlahir,
            'lampiran_surat_nikah'  => empty($lamp_suratnikah) ? null : $lamp_suratnikah,
            'lampiran_surat_cerai'  => empty($lamp_suratcerai) ? null : $lamp_suratcerai,
            // 'lampiran_sertifikat' => $lamp_sertifikat,
            // 'lampiran_ktp_pasangan_sertifikat' => $lamp_ktppassert,
        );
        //    }
        //   }
        // dd($data_lamp);


        $lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);


        // if (!empty($req->input('no_sertifikat'))) {
        //     for ($i = 0; $i < count($req->input('no_sertifikat')); $i++) {


        // $data_sert = array(
        //     'trans_so' => $id,
        //     'no_sertifikat' => empty($req->input('no_sertifikat')) ? NULL : $req->input('no_sertifikat'),
        //     'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? NULL : $req->input('nama_sertifikat'),
        //     'status_sertifikat' => empty($req->input('status_sertifikat')) ? NULL : $req->input('status_sertifikat'),
        //     'jenis_sertifikat' => empty($req->input('jenis_sertifikat')) ? NULL : $req->input('jenis_sertifikat'),
        //     'hub_cadeb' => empty($req->input('hub_cadeb')) ? null : $req->input('hub_cadeb'),
        //     'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')) ? NULL : $req->input('tgl_berlaku_shgb'),
        //     'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')) ? NULL : $req->input('nama_pas_sertifikat'),
        //     'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')) ? NULL : $req->input('status_pas_sertifikat'),
        //     'lampiran_sertifikat' => $lamp_sertifikat,
        //     'lampiran_ktp_sertifikat' => $lamp_ktppemsert,
        //     'lampiran_ktp_pasangan_sertifikat' => $lamp_ktppassert,
        //     'ahli_waris' => $lamp_ahliwaris,
        //     'akta_hibah' => $lamp_aktahibah,
        //     'ajb_ppjb' => $lamp_ajb,
        //     'lampiran_imb' => $lamp_imb,
        //     'lampiran_pbb' => $lamp_sttp_pbb

        // );




        // $sert = Lpdk_sertifikat::create([$data_sert]);
        //         $id_sert['id'][$s] = $sert->id;
        //     }
        //     $serID = implode(",", $id_sert['id']);
        // } else {
        //     $serID = null;
        // }

        if ($file = $req->file('lampiran_ktp_penjamin')) {
            $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/penjamin/update';
            $name = '';
            $check = 'null';

            $lamp_ktp_pen = Helper::uploadImg($check, $file, $path, $name);

            //          = $arrayPath;
            // }
        } else {
            $lamp_ktp_pen = null;
        }

        // if (!empty($req->input('nama_penjamin'))) {
        //     for ($i = 0; $i < count($req->input('nama_penjamin')); $i++) {


        // $data_penj = array(
        //     'trans_so' => $id,
        //     'nama_penjamin' => empty($req->input('nama_penjamin')) ? NULL : $req->input('nama_penjamin'),
        //     'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? NULL : $req->input('ibu_kandung_penjamin'),
        //     'pasangan_penjamin' => empty($req->input('pasangan_penjamin')) ? NULL : $req->input('pasangan_penjamin'),
        //     'lampiran_ktp_penjamin'       => $lamp_ktp_pen,
        // );

$pic_so = TransSO::where('id',$id)->first();

        // $penj = Lpdk_penjamin::create($data_penj);


        $lpdk_id = Lpdk::where('trans_so', $id)->first();
// $create = date_create(Carbon::parse($lpdk_id->created_at)->format('Y-m-d'));
// $update = date_create(Carbon::parse($lpdk_id->updated_at)->format('Y-m-d'));
//         $sla_interval = date_diff($create,$update);
      //  dd($sla_interval->days);

        $data = array(
            'trans_so'  => $id,
            'nomor_so' => $cek_lpdk->nomor_so,
            'id_pic' => $pic_so->id_pic,
            'id_area' => $pic_so->id_area,
            'id_cabang' => $pic_so->id_cabang,
            'nama_so' => $cek_lpdk->nama_so,
            'asal_data' => $cek_lpdk->asal_data,
            'nama_marketing' => $cek_lpdk->nama_marketing,
            'plafon' => $cek_lpdk->plafon,
            'tenor' => $cek_lpdk->tenor,
            'area_kerja' => empty($req->input('area_kerja')) ? $cek_lpdk->area_kerja :   $req->input('area_kerja'),
            'nama_debitur' => empty($req->input('nama_debitur')) ? $cek_lpdk->nama_debitur :   $req->input('nama_debitur'),
            'nama_pasangan' => empty($req->input('nama_pasangan')) ? $cek_lpdk->nama_pasangan :  $req->input('nama_pasangan'),
            'status_nikah' => empty($req->input('status_nikah')) ? $cek_lpdk->status_nikah : $req->input('status_nikah'),

            'produk' => empty($req->input('produk')) ? $cek_lpdk->produk : $req->input('produk'),

            'alamat_ktp_vs_jaminan' => empty($req->input('alamat_ktp_vs_jaminan')) ? $cek_lpdk->alamat_ktp_vs_jaminan : $req->input('alamat_ktp_vs_jaminan'),
            'akta_notaris' => empty($req->input('akta_notaris')) ? $cek_lpdk->akta_notaris : $req->input('akta_notaris'),
             'notes_progress' => empty($req->input('notes_progress')) ? null : $req->auth->nama . " : " . $req->input('notes_progress'),
            'notes_counter' =>  empty($req->input('notes_counter')) ? $note_cek->notes_counter : $note_cek->notes_counter.',' .$req->auth->nama . " : " . $req->input('notes_counter'),
            'notes_cancel' => empty($req->input('notes_cancel')) ? null : $req->auth->nama . " : " . $req->input('notes_cancel'),
            'lain_lain' => empty($req->input('akta_notaris_note_lainlain')) ? $cek_lpdk->lain_lain : $req->input('akta_notaris_note_lainlain'),
             'status_kredit' => empty($req->input('status_kredit')) ? $lpdk_id->status_kredit : $req->input('status_kredit') ,
            // 'id_penjamin' => $lpdk_id->id_penjamin . ',' . $penj->id,
            // 'id_sertifikat' => $lpdk_id->id_sertifikat . ',' . $sert->id,
'updated_at'  => Carbon::now() 

            // 'id_lampiran'  => $lamID

        );

        if ($data === null) {
            return response()->json([
                'code'  => 403,
                'message'   => 'data harus di input',
                'data'  => $data
            ]);
        }

        //  dd(Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->first());

        // if (Lpdk::where('nomor_so', $cek_lpdk->nomor_so)->exists()) {
        //     return response()->json([
        //         "Code"      => 409,
        //         "Status"    => "Conflict",
        //         "Message"   => "Nomor SO pengajuan LPDK Sudah Ada"
        //     ], 409);
        // }



        try {

            
            DB::connection('web')->commit();
            // //  $data_sertifikat = Lpdk_sertifikat::where('trans_so', $id)->update($data_sert);
            // $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
            // //    $upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
            Lpdk::where('trans_so', $id)->update($data);


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

    public function tambahSertifikat($id_trans, Request $req)
    {
        //$coba = $req->input('coba');
        //dd($coba);

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

//dd($pic);
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
        //  dd($user_id);
        $get_lpdk = Lpdk::get();

        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        // $cek_sertif = Lpdk_sertifikat::where('trans_so', $id_trans)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        $check_lamp = Lpdk_lampiran::where('trans_so', $id_trans)->first();


        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/sertifikat';

        $check_lamp_sertifikat      = $check_lamp->lampiran_sertifikat;


        if ($files = $req->file('lampiran_sertifikat')) {
            $name = '';
            $check = $check_lamp_sertifikat;
            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }

            $lamp_sertifikat = $arrayPath;
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }



        $note_cek = Lpdk::where('trans_so', $id_trans)->first();

        // DB::connection('web')->beginTransaction();

        if (!empty($req->input('no_sertifikat'))) {
            for ($i = 0; $i < count($req->input('no_sertifikat')); $i++) {


                $data_sert[] = array(
                    'trans_so' => $id_trans,
                    'no_sertifikat' => empty($req->input('no_sertifikat')[$i]) ? NULL : $req->input('no_sertifikat')[$i],
                    'nama_sertifikat' => empty($req->input('nama_sertifikat')[$i]) ? NULL : $req->input('nama_sertifikat')[$i],
                    'status_sertifikat' => empty($req->input('status_sertifikat')[$i]) ? NULL : $req->input('status_sertifikat')[$i],
                    'jenis_sertifikat' => empty($req->input('jenis_sertifikat')[$i]) ? NULL : $req->input('jenis_sertifikat')[$i],
                    'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')[$i]) ? NULL : $req->input('tgl_berlaku_shgb')[$i],
                    'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')[$i]) ? NULL : $req->input('nama_pas_sertifikat')[$i],
                    'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')[$i]) ? NULL : $req->input('status_pas_sertifikat')[$i],
                    'lampiran_sertifikat' => $lamp_sertifikat[$i],

                );
            }
        }


        if (!empty($req->input('no_sertifikat'))) {
            for ($j = 0; $j < count($data_sert); $j++) {
                $sert = Lpdk_sertifikat::create($data_sert[$j]);
                $id_sert['id'][$j] = $sert->id;
            }
            $serID = implode(",", $id_sert['id']);
        } else {
            $serID = null;
        }
        // }


        // try {

        $cek = Lpdk::where('trans_so', $id_trans)->first();

        Lpdk::where('trans_so', $id_trans)->update(['id_sertifikat' => $cek->id_sertifikat . "," . $serID]);


        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $data_sert
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
    public function tambahPenjamin($id_trans, Request $req)
    {
        // $coba = $req->input('coba');
        // dd($coba);

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

//dd($pic);
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

        $get_lpdk = Lpdk::get();

        // if ($get_lpdk === null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'data lpdk kosong'
        //     ], 404);
        // }
        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        $cek_sertif = Lpdk_penjamin::where('trans_so', $id_trans)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        $check_ktp_pen = Lpdk_penjamin::where('trans_so', $id_trans)->first();


        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/penjamin';

        $check_lamp_ktp_pasangan_penjamin   = $check_ktp_pen->lampiran_ktp_penjamin;



        $note_cek = Lpdk::where('trans_so', $id_trans)->first();


        if ($files = $req->file('lampiran_ktp_penjamin')) {
            $name = '';
            $check = $check_lamp_ktp_pasangan_penjamin;
            $arrayPath = array();
            foreach ($files as $file) {

                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }
            $lamp_ktppenj = $arrayPath;
        } else {
            $lamp_ktppenj = $check_lamp_ktp_pasangan_penjamin;
        }



        if (!empty($req->input('nama_penjamin'))) {
            for ($i = 0; $i < count($req->input('nama_penjamin')); $i++) {

                $data_penj[] = array(
                    'trans_so' => $id_trans,
                    'nama_penjamin' => empty($req->input('nama_penjamin')[$i]) ? NULL : $req->input('nama_penjamin')[$i],
                    'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')[$i]) ? NULL : $req->input('ibu_kandung_penjamin')[$i],
                    'pasangan_penjamin' => empty($req->input('pasangan_penjamin')[$i]) ? NULL : $req->input('pasangan_penjamin')[$i],
                    'lampiran_ktp_penjamin'       => $lamp_ktppenj[$i],
                );
            }
        }

        if (!empty($req->input('nama_penjamin'))) {
            for ($j = 0; $j < count($data_penj); $j++) {
                $penj = Lpdk_penjamin::create($data_penj[$j]);
                $id_penjamin['id'][$j] = $penj->id;
            }
            $penID = implode(",", $id_penjamin['id']);
        } else {
            //  $penID = null;
        }
        try {
            $cek = Lpdk::where('trans_so', $id_trans)->first();
            //dd($cek->id_penjamin);
            //  DB::connection('web')->commit();
            // $data_penjamin = Lpdk_penjamin::create($data_penj);
            // $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
            //$upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
            Lpdk::where('trans_so', $id_trans)->update(['id_penjamin' => $cek->id_penjamin . "," . $penID]);


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data_penj
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

    public function EditSertifikat($id_trans, $id, Request $req)
    {
        // $coba = $req->input('coba');
        // dd($coba);

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

//dd($pic);
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

        $get_lpdk = Lpdk::get();

        // if ($get_lpdk === null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'data lpdk kosong'
        //     ], 404);
        // }
        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        $cek_sertif = Lpdk_sertifikat::where('id', $id)
            ->where('trans_so', $id_trans)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        //  $check_lamp = Lpdk_lampiran::where('trans_so', $id_trans)->first();


        $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';

        $check_lamp_sertifikat      = $cek_sertif->lampiran_sertifikat;
        $check_lamp_ktp = $cek_sertif->lampiran_ktp_sertifikat;
        $check_lamp_ktp_pas      = $cek_sertif->lampiran_ktp_pasangan_sertifikat;
        $check_lamp_ahliwaris      = $cek_sertif->ahli_waris;
        $check_lamp_aktahibah      = $cek_sertif->akta_hibah;
        $check_lamp_ajb_ppjb      = $cek_sertif->ajb_ppjb;
        $check_lamp_pbb      = $cek_sertif->lampiran_pbb;
        $check_lamp_imb      = $cek_sertif->lampiran_imb;


        if ($file = $req->file('lampiran_ktp_sertifikat')) {
            $path2 = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check = $check_lamp_ktp;
            $lamp_ktp_sertifikat = Helper::uploadImg($check, $file, $path2, $name);
        } else {
            $lamp_ktp_sertifikat = $check_lamp_ktp;
        }

        if ($file = $req->file('lampiran_ktp_pasangan_sertifikat')) {
            $path3 = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check = $check_lamp_ktp_pas;
            $lamp_ktp_pas_sertifikat  = Helper::uploadImg($check, $file, $path3, $name);
        } else {
            $lamp_ktp_pas_sertifikat = $check_lamp_ktp_pas;
        }

        if ($file = $req->file('ahli_waris')) {
            $path4 = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check = $check_lamp_ahliwaris;
            $lamp_ahliwaris = Helper::uploadImg($check, $file, $path4, $name);
        } else {
            $lamp_ahliwaris = $check_lamp_ahliwaris;
        }

        if ($file = $req->file('akta_hibah')) {
            $path5 = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check = $check_lamp_aktahibah;
            $lamp_aktahibah = Helper::uploadImg($check, $file, $path5, $name);
        } else {
            $lamp_aktahibah = $check_lamp_aktahibah;
        }

        if ($file = $req->file('ajb_ppjb')) {
            $path6 = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check = $check_lamp_ajb_ppjb;
            $lamp_ajb_ppjb = Helper::uploadImg($check, $file, $path6, $name);
        } else {
            $lamp_ajb_ppjb = $check_lamp_ajb_ppjb;
        }


        if ($file = $req->file('lampiran_pbb')) {
            $path7 = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check = $check_lamp_pbb;
            $lamp_sttp_pbb  = Helper::uploadImg($check, $file, $path7, $name);
        } else {
            $lamp_sttp_pbb = $check_lamp_pbb;
        }


        if ($file = $req->file('lampiran_imb')) {
            $path8 = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check = $check_lamp_imb;
            $lamp_imb  = Helper::uploadImg($check, $file, $path8, $name);
        } else {
            $lamp_imb = $check_lamp_imb;
        }

 if ($file = $req->file('lampiran_sertifikat')) {
            $path_ser = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk/lampiran/sertifikat';
            $name = '';
            $check4 = $check_lamp_sertifikat;

            $lamp_sertifikat = Helper::uploadImg($check4, $file, $path_ser, $name);
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }




        $note_cek = Lpdk::where('trans_so', $id_trans)->first();

        // DB::connection('web')->beginTransaction();

        $data_sert = array(
            // 'trans_so' => $id_trans,
            'no_sertifikat' => empty($req->input('no_sertifikat')) ? $cek_sertif->no_sertifikat : $req->input('no_sertifikat'),
            'nama_sertifikat' => empty($req->input('nama_sertifikat')) ? $cek_sertif->nama_sertifikat : $req->input('nama_sertifikat'),
            'status_sertifikat' => empty($req->input('status_sertifikat')) ? $cek_sertif->status_sertifikat : $req->input('status_sertifikat'),
            'jenis_sertifikat' => empty($req->input('jenis_sertifikat')) ? $cek_sertif->jenis_sertifikat : $req->input('jenis_sertifikat'),
            'tgl_berlaku_shgb' => empty($req->input('tgl_berlaku_shgb')) ? $cek_sertif->tgl_berlaku_shgb : Carbon::parse($req->input('tgl_berlaku_shgb'))->format('Y-m-d'),
            'nama_pas_sertifikat' => empty($req->input('nama_pas_sertifikat')) ? $cek_sertif->nama_pas_sertifikat : $req->input('nama_pas_sertifikat'),
            'status_pas_sertifikat' => empty($req->input('status_pas_sertifikat')) ? $cek_sertif->status_pas_sertifikat : $req->input('status_pas_sertifikat'),
            'hub_cadeb' => empty($req->input('hub_cadeb')) ? $cek_sertif->hub_cadeb : $req->input('hub_cadeb'),
            'ahli_waris' => $lamp_ahliwaris,
            'akta_hibah' => $lamp_aktahibah,
            'ajb_ppjb' =>  $lamp_ajb_ppjb,
            'lampiran_ktp_sertifikat' => $lamp_ktp_sertifikat,
            'lampiran_ktp_pasangan_sertifikat' => $lamp_ktp_pas_sertifikat,
            'lampiran_sertifikat' => $lamp_sertifikat,
            'lampiran_imb' =>  $lamp_imb,
            'lampiran_pbb' => $lamp_sttp_pbb

        );

        try {
            //   $cek = Lpdk::where('trans_so', $id_trans)->first();
            //  DB::connection('web')->commit();
            $data_sertifikat = Lpdk_sertifikat::where('id', $id)->where('trans_so', $id_trans)->update($data_sert);
            // $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
            //$upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
            // Lpdk::where('trans_so', $id_trans)->update(['id_sertifikat' => $cek->id_sertifikat . "," . $data_sertifikat->id]);


            return response()->json([
                'code' => 200,
                'status' => 'success',
                'data' => $data_sert
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

     public function EditPenjamin($id_trans, $id, Request $req)
    {
        // $coba = $req->input('coba');
        // dd($coba);

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

//dd($pic);
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

        $get_lpdk = Lpdk::get();

        // if ($get_lpdk === null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'data lpdk kosong'
        //     ], 404);
        // }
        $cek_lpdk = Lpdk::where('trans_so', $id_trans)->first();

        $cek_sertif = Lpdk_penjamin::where('trans_so', $id_trans)->where('id', $id)->first();
        //dd($cek_lpdk);

        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id_trans . ' ' . 'tidak ditemukan'
            ]);
        }

        $check_debt_ktp = Debitur::where('id', $id_trans)->first();
        //  $check_lpdk = Lpdk::where('id', $id_trans)
        $check_debt = Lpdk::where('trans_so', $id_trans)->first();

        $check_ktp_pen = Lpdk_penjamin::where('trans_so', $id_trans)->first();


         $path = 'public/' . $check_debt_ktp->no_ktp . '/debitur/lpdk';

        $check_lamp_ktp_penjamin   = $check_ktp_pen->lampiran_ktp_penjamin;

        $check_lamp_buku_nikah   = $check_ktp_pen->buku_nikah_penjamin;




        $note_cek = Lpdk::where('trans_so', $id_trans)->first();

        DB::connection('web')->beginTransaction();

        if ($file = $req->file('lampiran_ktp_penjamin')) {
            $name = '';
            $check = $check_lamp_ktp_penjamin;
            $lamp_ktppenj  = Helper::uploadImg($check, $file, $path, $name);
            
        } else {
            $lamp_ktppenj = $check_lamp_ktp_penjamin;
        }

        if ($file = $req->file('buku_nikah_penjamin')) {
            $name = '';
            $check = $check_lamp_ktp_penjamin;
           

            $lamp_bukunikahpenj = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_bukunikahpenj = $check_lamp_buku_nikah;
        }

        // if ($file = $req->file('lampiran_ktp_penjamin')) {
        //     $name = 'lamp_ktppenjamin.';
        //     $check = $check_lamp_ktp_penjamin;

        //     $lamp_ktppenj = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_ktppenj = $check_lamp_ktp_penjamin;
        // }

        // if ($file = $req->file('buku_nikah_penjamin')) {
        //     $name = 'buku_nikah_penj.';
        //     $check = $check_lamp_buku_nikah;

        //     $lamp_bukunikahpenj = Helper::uploadImg($check, $file, $path, $name);
        // } else {
        //     $lamp_bukunikahpenj = $check_lamp_buku_nikah;
        // }

        $data_penj = array(
            // 'trans_so' => $id_trans,
            'nama_penjamin' => empty($req->input('nama_penjamin')) ? $cek_lpdk->nama_penjamin : $req->input('nama_penjamin'),
            'ibu_kandung_penjamin' => empty($req->input('ibu_kandung_penjamin')) ? $cek_lpdk->ibu_kandung_penjamin : $req->input('ibu_kandung_penjamin'),
            'pasangan_penjamin' => empty($req->input('pasangan_penjamin')) ? $cek_lpdk->pasangan_penjamin : $req->input('pasangan_penjamin'),
            'lampiran_ktp_penjamin'       => $lamp_ktppenj,
            'buku_nikah_penjamin'       => $lamp_bukunikahpenj,
        );

        //  try {
        $cek = Lpdk::where('trans_so', $id_trans)->first();
        //dd($cek->id_penjamin);
        DB::connection('web')->commit();
        $data_penjamin = Lpdk_penjamin::where('id', $id)->where('trans_so', $id_trans)->update($data_penj);
        // $upd_lamp = Lpdk_lampiran::where('trans_so', $id)->update($data_lamp);
        //$upd_penj = Lpdk_penjamin::where('trans_so', $id)->update($data_penj);
        // Lpdk::where('trans_so', $id_trans)->update(['id_penjamin' => $cek->id_penjamin . "," . $data_penjamin->id]);


        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $data_penj
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
  public function EditStatus($id, Request $req) {

        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

//dd($pic);
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

        $get_lpdk = Lpdk::get();

        if ($get_lpdk === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'data lpdk kosong'
            ], 404);
        }
        $cek_lpdk = Lpdk::where('trans_so', $id)->first();

        $cek_sertif = Lpdk_sertifikat::where('trans_so', $id)->first();
        //dd($cek_lpdk);
        $note_cek = Lpdk::where('trans_so', $id)->first();
        if ($cek_lpdk === null) {
            return response()->json([
                'code'  => 402,
                'message'  => 'data transaksi SO dengan id =' . ' ' . $id . ' ' . 'tidak ditemukan'
            ]);
        }


        $pic_so = TransSO::where('id',$id)->first();
        $lpdk_id = Lpdk::where('trans_so', $id)->first();
        $data = array(
            'trans_so'  => $id,
            'id_pic' => $pic_so->id_pic,
            'id_area' => $pic_so->id_area,
            'id_cabang' => $pic_so->id_cabang,
            'nomor_so' => $cek_lpdk->nomor_so,
            'nama_so' => $cek_lpdk->nama_so,
            'asal_data' => $cek_lpdk->asal_data,
            'nama_marketing' => $cek_lpdk->nama_marketing,
            'plafon' => $cek_lpdk->plafon,
            'tenor' => $cek_lpdk->tenor,
            'area_kerja' => empty($req->input('area_kerja')) ? $cek_lpdk->area_kerja :   $req->input('area_kerja'),
            'nama_debitur' => empty($req->input('nama_debitur')) ? $cek_lpdk->nama_debitur :   $req->input('nama_debitur'),
            'nama_pasangan' => empty($req->input('nama_pasangan')) ? $cek_lpdk->nama_pasangan :  $req->input('nama_pasangan'),
            'status_nikah' => empty($req->input('status_nikah')) ? $cek_lpdk->status_nikah : $req->input('status_nikah'),

            'produk' => empty($req->input('produk')) ? $cek_lpdk->produk : $req->input('produk'),

            'alamat_ktp_vs_jaminan' => empty($req->input('alamat_ktp_vs_jaminan')) ? $cek_lpdk->alamat_ktp_vs_jaminan : $req->input('alamat_ktp_vs_jaminan'),
            'akta_notaris' => empty($req->input('akta_notaris')) ? $cek_lpdk->akta_notaris : $req->input('akta_notaris'),
            'notes_progress' => empty($req->input('notes_progress')) ? $note_cek->notes_progress : $note_cek->notes_progress.','. $req->auth->nama.' '.Carbon::now() . " : " . $req->input('notes_progress'),
            'notes_counter' =>  empty($req->input('notes_counter')) ? $note_cek->notes_counter : $note_cek->notes_counter.',' .$req->auth->nama.' '.Carbon::now(). " : " . $req->input('notes_counter'),
             'status_kredit' => empty($req->input('status_kredit')) ? $lpdk_id->status_kredit : $req->input('status_kredit') ,
             'notes_cancel' =>  empty($req->input('notes_cancel')) ? null : $req->auth->nama.'  '. Carbon::now(). " : " .$req->input('notes_cancel'),
             'lain_lain' => empty($req->input('akta_notaris_note_lainlain')) ? $cek_lpdk->lain_lain : $req->input('akta_notaris_note_lainlain'),
'updated_at'  => Carbon::now()
        );

        try {

            DB::connection('web')->commit();
            Lpdk::where('trans_so', $id)->update($data);


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
