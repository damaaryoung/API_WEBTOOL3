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
use App\Models\Pengajuan\SO\Pasangan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Lpdk_DetailController extends BaseController
{
  public function detailPenjamin($id_penj) {

    $lpdk_penjamin = Lpdk_penjamin::where('id',$id_penj)->first();
  // dd($lpdk_penjamin);
    if($lpdk_penjamin === null) {
        return response()->json([
'code' => 404,
'status' => 'not found',
'message' => 'data penjamin dengan id'.' '.$id_penj.' '.'tidak di temukan'
        ]);
    }
        $arrData = array(
            'id' => $lpdk_penjamin->id,
            'trans_so' =>  $lpdk_penjamin->trans_so,
            'nama_penjamin' => $lpdk_penjamin->nama_penjamin,
            'ibu_kandung_penjamin' => $lpdk_penjamin->ibu_kandung_penjamin,
            'pasangan_penjamin' => $lpdk_penjamin->pasangan_penjamin,
            'lampiran_ktp_penjamin' => $lpdk_penjamin->lampiran_ktp_penjamin,
    'buku_nikah_penjamin' => $lpdk_penjamin->buku_nikah_penjamin,
            'created_at' => $lpdk_penjamin->created_at,
            'updated_at' => $lpdk_penjamin->updated_at
        );

     //   dd($arrData);
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

    public function detailSertifikat($id_sert) {

        $lpdk_sertifikat = Lpdk_sertifikat::where('id',$id_sert)->first();
      // dd($lpdk_sertifikat);
        if($lpdk_sertifikat === null) {
            return response()->json([
    'code' => 404,
    'status' => 'not found',
    'message' => 'data penjamin dengan id'.' '.$id_sert.' '.'tidak di temukan'
            ]);
        }
            $arrData = array(
                'id' => $lpdk_sertifikat->id,
                'trans_so' =>  $lpdk_sertifikat->trans_so,
                'nama_sertifikat' => $lpdk_sertifikat->nama_sertifikat,
                'status_sertifikat' => $lpdk_sertifikat->status_sertifikat,
                'hub_cadeb' => $lpdk_sertifikat->hub_cadeb,
                'nama_pas_sertifikat' => $lpdk_sertifikat->nama_pas_sertifikat,
        'status_pas_sertifikat' => $lpdk_sertifikat->status_pas_sertifikat,
        'no_sertifikat' => $lpdk_sertifikat->no_sertifikat,
        'jenis_sertifikat' => $lpdk_sertifikat->jenis_sertifikat,
        'tgl_berlaku_shgb' => Carbon::parse($lpdk_sertifikat->tgl_berlaku_shgb)->format('d-m-Y'),
        'lampiran_ktp_sertifikat' => $lpdk_sertifikat->lampiran_ktp_sertifikat,
        'lampiran_ktp_pasangan_sertifikat' => $lpdk_sertifikat->status_pas_sertifikat,
        'ahli_waris' => $lpdk_sertifikat->ahli_waris,
        'akta_hibah' => $lpdk_sertifikat->akta_hibah,
        'ajb_ppjb' => $lpdk_sertifikat->ajb_ppjb,
        'lampiran_sertifikat' => $lpdk_sertifikat->lampiran_sertifikat,
        'lampiran_imb' => $lpdk_sertifikat->lampiran_imb,
        'lampiran_pbb' => $lpdk_sertifikat->lampiran_pbb,
                'created_at' => $lpdk_sertifikat->created_at,
                'updated_at' => $lpdk_sertifikat->updated_at
            );
    
         //   dd($arrData);
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

}
