<?php

namespace App\Http\Controllers\Master\Bisnis;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Bisnis\AsalDataReq;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class MitraController extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('dpm')->table('kre_kode_group5')->select('kode_group5 as kode_mitra','deskripsi_group5 as nama_mitra', 'jenis_mitra')->where('jenis_mitra', 'MB')->get();

            if ($query == '[]') {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Data kosong"
                ], 404);
            }

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function search($search) {

        $query = DB::connection('dpm')->table('kre_kode_group5')
                ->select('kode_group5 as kode_mitra','deskripsi_group5 as nama_mitra', 'jenis_mitra')
                ->where('jenis_mitra', 'MB')
                ->where('kode_group5', 'like', "%{$search}%")
                ->orWhere('deskripsi_group5', 'like', "%{$search}%")
                ->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        try{
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($kode_mitra) {
        try {
            $query = DB::connection('dpm')->table('kre_kode_group5')->select('kode_group5 as kode_mitra','deskripsi_group5 as nama_mitra', 'jenis_mitra')->where('jenis_mitra', 'MB')->where('kode_group5', $kode_mitra)->first();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
