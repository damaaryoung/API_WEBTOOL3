<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use DB;

class ProvinsiController extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_provinsi')->get();

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
        $nama        = $req->input('nama');
        $flg_aktif   = $req->input('flg_aktif');

        if (!$nama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Name field is required !"
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
            $query = DB::connection('web')->table('master_provinsi')->insert([
                'nama'      => $nama,
                'flg_aktif' => $flg_aktif
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
            $query = DB::connection('web')->table('master_provinsi')->where('id', $id)->get();

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
        $check = DB::connection('web')->table('master_provinsi')->where('id', $id)->first();

        $nama        = empty($req->input('nama')) ? $check->nama : $req->input('nama');
        $flg_aktif   = empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif');

        try {
            $query = DB::connection('web')->table('master_provinsi')->where('id', $id)->update([
                'nama'      => $nama,
                'flg_aktif' => $flg_aktif
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
            $query = DB::connection('web')->table('master_provinsi')->where('id', $id)->delete();

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
