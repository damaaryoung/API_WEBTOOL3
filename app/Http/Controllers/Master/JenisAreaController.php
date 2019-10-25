<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class JenisAreaController extends BaseController
{
    public function index() {
        $query = DB::connection('web')->table('master_jenis')->get();

        return response()->json([
            'code'   => 200,
            'status' => 'success',
            'data'   => $query
        ], 200);
    }

    public function store(Request $req) {
        $nama_jenis = $req->input('nama_jenis');

        if (!$nama_jenis) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Nama_jenis field is required !"
            ], 400);
        }

        $query = DB::connection('web')->table('master_jenis')->insert([
            'nama_jenis' => $nama_jenis
        ]);

        try {
            return response()->json([
                "code"    => 200,
                "status"  => "success",
                "message" => "Data successfully created"
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Data failed created !"
            ], 400);
        }
    }

    public function show($id) {
        $query = DB::connection('web')->table('master_jenis')->where('id', $id)->first();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 200,
                'status'  => 'error',
                'message' => $e
            ], 200);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_jenis')->where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Data not found !"
            ], 400);
        }

        $nama_jenis = empty($req->input('nama_jenis')) ? $check->nama_jenis : $req->input('nama_jenis');

        $query = DB::connection('web')->table('master_jenis')->where('id', $id)->update(['nama_jenis' => $nama_jenis]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data successfully Updated'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 200,
                'status'  => 'error',
                'message' => $e
            ], 200);
        }
    }

    public function delete($id) {
        $check = DB::connection('web')->table('master_jenis')->where('id', $id)->delete();
        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data with id '.$id.' successfully deleted'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 200,
                'status'  => 'error',
                'message' => $e
            ], 200);
        }
    }
}
