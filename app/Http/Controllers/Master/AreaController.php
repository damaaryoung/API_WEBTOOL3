<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class AreaController extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_area')
                ->join('master_kelurahan', 'master_area.id_kelurahan', '=', 'master_kelurahan.id')
                ->join('master_kecamatan', 'master_kelurahan.id_kecamatan', '=', 'master_kecamatan.id')
                ->join('master_kabupaten', 'master_kecamatan.id_kabupaten', '=', 'master_kabupaten.id')
                ->join('master_provinsi', 'master_kabupaten.id_provinsi', '=', 'master_provinsi.id')
                ->select('master_area.id', 'master_area.kode_group2 as kode_group', 'master_area.id_kre_kode_so', 'master_area.jenis', 'master_kelurahan.nama as kelurahan', 'master_kecamatan.nama as kecamatan', 'master_kabupaten.nama as kabupaten', 'master_provinsi.nama as provinsi')
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

    public function store(Request $req) {
        $id_kelurahan   = $req->input('id_kelurahan');
        $kode_group2    = $req->input('kode_group2');
        $id_kre_kode_so = $req->input('id_kre_kode_so');
        $jenis          = $req->input('jenis');
        $flg_aktif      = $req->input('flg_aktif');


        if (!$id_kelurahan) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "ID Kelurahan field is required !"
            ], 400);
        }

        if (!$kode_group2) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Kode group field is required !"
            ], 400);
        }

        if (!$id_kre_kode_so) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "ID kre_kode_so field is required !"
            ], 400);
        }

        if (!$jenis) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Jenis field is required !"
            ], 400);
        }

        if (!$id_kelurahan) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "ID Kelurahan field is required !"
            ], 400);
        }

        if (!$flg_aktif) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "flg aktif field is required !"
            ], 400);
        }

        if ($jenis == "AO" || $jenis == "SO" || $jenis == "COL" || $jenis == "MB" || $jenis == "CA") {
            try {
                $query = DB::connection('web')->table('master_area')->insert([
                    'id_kelurahan'   => $id_kelurahan,
                    'kode_group2'    => $kode_group2,
                    'id_kre_kode_so' => $id_kre_kode_so,
                    'jenis'          => $jenis,
                    'flg_aktif'      => $flg_aktif
                ]);

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'message' => 'Data has been created'
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    "code"    => 400,
                    "status"  => "error",
                    "message" => "something error!"
                ], 400);
            }
        }else{
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "the exact values ​​are 'AO', 'SO', 'COL', 'MB' or 'CA'"
            ], 400);
        }
    }

    public function show($id) {
        try {
            $query = DB::connection('web')->table('master_area')->where('id', $id)->first();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'data'   => $e
            ], 400);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_area')->where('id', $id)->first();

        $id_kelurahan = empty($req->input('id_kelurahan')) ? $check->id_kelurahan : $req->input('id_kelurahan');
        $kode_group2  = empty($req->input('kode_group2')) ? $check->kode_group2 : $req->input('kode_group2');
        $id_kre_kode_so = empty($req->input('id_kre_kode_so') ? $check->id_kre_kode_so : $req->input('id_kre_kode_so'));
        $jenis          = empty($req->input('jenis')) ? $check->jenis : $req->input('jenis');
        $flg_aktif    = empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif');

        try {
            $query = DB::connection('web')->table('master_area')->where('id', $id)->update([
                'id_kelurahan'   => $id_kelurahan,
                'kode_group2'    => $kode_group2, // reference kre_kode_group2
                'id_kre_kode_so' => $id_kre_kode_so,
                'jenis'          => $jenis,
                'flg_aktif'      => $flg_aktif
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data has been updated'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'data'   => $e
            ], 400);
        }
    }

    public function delete($id) {
        try {
            $query = DB::connection('web')->table('master_area')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data with ID '.$id.' has been deleted'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'data'   => $e
            ], 400);
        }
    }
}
