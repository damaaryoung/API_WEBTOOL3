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
        $keterangan = $req->input('keterangan');

        if (!$nama_jenis) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'Nama_jenis' harus diisi!!"
            ], 400);
        }

        if (!$keterangan) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'keterangan' harus diisi!!"
            ], 400);
        }

        $query = DB::connection('web')->table('master_jenis')->insert([
            'nama_jenis' => $nama_jenis,
            'keterangan' => $keterangan
        ]);

        try {
            return response()->json([
                "code"    => 200,
                "status"  => "success",
                "message" => "Data berhasil dibuat"
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
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
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_jenis')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $nama_jenis = empty($req->input('nama_jenis')) ? $check->nama_jenis : $req->input('nama_jenis');
        $keterangan = empty($req->input('keterangan')) ? $check->keterangan : $req->input('keterangan');

        try {
            $query = DB::connection('web')->table('master_jenis')->where('id', $id)->update([
                'nama_jenis' => $nama_jenis,
                'keterangan' => $keterangan
            ]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil di update'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function delete($id) {
        try {
            $check = DB::connection('web')->table('master_jenis')->where('id', $id)->first();

            if (!$check) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data tidak ada'
                ], 404);
            }

            DB::connection('web')->table('master_jenis')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan id '.$id.' berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
