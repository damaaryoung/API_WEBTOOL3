<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use DB;

class KabupatenController extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_kabupaten')->get();

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
        $nama      = $req->input('nama');
        $provinsi  = $req->input('id_provinsi');
        $flg_aktif = $req->input('flg_aktif');

        if (!$nama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Name field is required !"
            ], 400);
        }

        if (!$provinsi) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "id provinsi field is required !"
            ], 400);
        }

        if (!$flg_aktif) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "flg aktif field is required !"
            ], 400);
        }

        try {
            $query = DB::connection('web')->table('master_kabupaten')->insert([
                'nama'        => $nama,
                'id_provinsi' => $provinsi,
                'flg_aktif'   => $flg_aktif
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
    }

    public function show($id) {
        try {
            $query = DB::connection('web')->table('master_kabupaten')->where('id', $id)->get();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_kabupaten')->where('id', $id)->first();

        $nama      = empty($req->input('nama')) ? $check->nama : $req->input('nama');
        $provinsi  = empty($req->input('id_provinsi')) ? $check->id_provinsi : $req->input('id_provinsi');
        $flg_aktif = empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif');

        if (!$nama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Name field is required !"
            ], 400);
        }

        if (!$provinsi) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "id provinsi field is required !"
            ], 400);
        }

        if (!$flg_aktif) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "flg aktif field is required !"
            ], 400);
        }

        try {
            $query = DB::connection('web')->table('master_kabupaten')->where('id', $id)->update([
                'nama'        => $nama,
                'id_provinsi' => $provinsi,
                'flg_aktif'   => $flg_aktif
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data updated successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    public function delete($id) {
        try {
            $query = DB::connection('web')->table('master_kabupaten')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data with ID '.$id.' was deleted successfully'
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
