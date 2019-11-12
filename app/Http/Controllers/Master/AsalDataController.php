<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class AsalDataController extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_asal_data')->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => $e
            ], 400);
        }
    }

    public function store(Request $req) {
        $nama = $req->input('nama');

        if (!$nama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'Nama' harus diisi!"
            ], 400);
        }

        try {
            $query = DB::connection('web')->table('master_asal_data')->insert([
                'nama' => $nama
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dibuat'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => $e
            ], 400);
        }
    }

    public function show($id) {
        try {
            $query = DB::connection('web')->table('master_asal_data')->where('id', $id)->first();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => $e
            ], 400);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_asal_data')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $nama = empty($req->input('nama')) ? $check->nama : $req->input('nama');

        try {
            $query = DB::connection('web')->table('master_asal_data')->where('id', $id)->update([
                'nama' => $nama
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function delete($id) {
        try {
            $check = DB::connection('web')->table('master_asal_data')->where('id', $id)->first();

            if (!$check) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data Tidak Ada!!'
                ], 404);
            }

            DB::connection('web')->table('master_asal_data')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan id '.$id.' berhasil dihapus'
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
