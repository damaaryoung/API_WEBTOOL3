<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\FaspinRequest;

// Models
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use App\Models\Transaksi\TransSO;
// use App\Models\User;

// use Illuminate\Support\Facades\File;
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ViewController extends BaseController
{
    public function segmentasiBPR()
    {
        $query = DB::connection('web')->table('view_segmentasi_bpr')->select('kode', 'nama')->get();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function segmentasiSektorEkonomi()
    {
        $query = DB::connection('web')->table('view_sektor_ekonomi')->select('kode', 'nama')->get();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function namaAsuransi()
    {
        $query = DB::connection('web')->table('view_asuransi_jiwa')->select('kode_asuransi', 'nm_asuransi')->get();


        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function viewReportAo($id)
    {
        $reportAo = DB::connection('web')->table('view_report_ao')->where('id_trans_so', $id)->first();

        if ($reportAo === null) {
            return response()->json([
                'code'   => 404,
                'status' => 'data transaksi tidak ditemukan',
                'data'   => 'kosong'
            ], 404);
        }
        //  dd($reportAo);
        $id_penjamin = TransSO::select('id_penjamin')->where('id', $id)->first();
        $viewPenjaminDebitur = DB::connection('web')->table('view_penjamin_debitur')->where('id', $id_penjamin->id_penjamin)->first();

        $agunan_tanah = DB::connection('web')->table('view_agunan_tanah')->where('id', $reportAo->id_agunan_tanah)->first();

        // $arr = array();
        // foreach ($reportAo as $key => $val) {
        //     $arr['report_ao'] = $val;
        // }
        // $merge = array_merge($id_penjamin, $viewPenjaminDebitur, $agunan_tanah);


        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => array('report_ao' => $reportAo, 'id_penjamin' => $id_penjamin, 'penjamin_debitur' => $viewPenjaminDebitur, 'agunan_tanah' => $agunan_tanah)
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

 public function viewPekerjaan()
    {
        $pekerjaan = DB::connection('web')->table('view_pekerjaan')->get();

        if ($pekerjaan === null) {
            return response()->json([
                'code'   => 404,
                'status' => 'data transaksi tidak ditemukan',
                'data'   => 'kosong'
            ], 404);
        }



        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $pekerjaan
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
