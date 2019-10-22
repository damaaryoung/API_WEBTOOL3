<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class Area_AO_Controller extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_area_ao')
                ->join('master_kelurahan', 'master_area_ao.id_kelurahan', '=', 'master_kelurahan.id')
                ->join('master_kecamatan', 'master_kelurahan.id_kecamatan', '=', 'master_kecamatan.id')
                ->join('master_kabupaten', 'master_kecamatan.id_kabupaten', '=', 'master_kabupaten.id')
                ->join('master_provinsi', 'master_kabupaten.id_provinsi', '=', 'master_provinsi.id')
                ->select('master_area_ao.id', 'master_area_ao.kode_group2', 'master_kelurahan.nama as kelurahan', 'master_kecamatan.nama as kecamatan', 'master_kabupaten.nama as kabupaten', 'master_provinsi.nama as provinsi', 'master_area_ao.flg_aktif')
                ->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    public function create_data(Request $req) {
        try {
            $query = DB::connection('web')->table('master_area_ao')->insert([
                'id_kelurahan' => $req->input('id_kelurahan'),
                'kode_group2'  => $req->input('kode_group2'),
                'flg_aktif'    => $req->input('flg_aktif')
            ]);

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => 'Data has been created'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }
}
