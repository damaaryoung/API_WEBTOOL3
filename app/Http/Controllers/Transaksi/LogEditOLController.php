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
use Image;
use Illuminate\Support\Facades\DB;

class LogEditOLController extends BaseController
{


    public function show($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $check_so = TransSO::with('pic', 'cabang')->where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (empty($check_so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atatu belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::with('pic', 'cabang')->where('id_trans_so', $id)->where('status_ao', 1)->first();

        if (empty($check_ao)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        $query_dir = TransCA::with('pic', 'cabang')->where('id_trans_so', $id)
            ->where('status_ca', 1);

        $ca = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $check_ca = $ca->first();

        if (empty($check_ca)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke CA'
            ], 404);
        }

        $trans_ca = TransCA::where('id_trans_so', $id)->first();
        $log = LogRekomCA::where('id', $trans_ca->id_recom_ca)->first();

        if (empty($log)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . 'tidak ada data revisi'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $log
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

        if (empty($log)) {
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
                'data'   => $log
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
