<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
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
                "message" => "something error!"
            ], 400);
        }
    }

    public function store(Request $req) {
        $nama = $req->input('nama');

        if (!$nama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Nama field is required!"
            ], 400);
        }

        try {
            $query = DB::connection('web')->table('master_asal_data')->insert([
                'nama' => $nama
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data successfully created'
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
                "message" => "something error!"
            ], 400);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_asal_data')->where('id', $id)->first();

        $nama = empty($req->input('nama')) ? $check->nama : $req->input('nama');

        try {
            $query = DB::connection('web')->table('master_asal_data')->insert([
                'nama' => $nama
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data successfully created'
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
            $query = DB::connection('web')->table('master_asal_data')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data successfully deleted'
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
