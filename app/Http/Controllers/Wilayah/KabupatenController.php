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
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
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
                "message" => "Field 'nama' harus diisi!!"
            ], 400);
        }

        if (!$provinsi) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'provinsi' harus diisi!!"
            ], 400);
        }

        if (!$flg_aktif) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'flg_aktif' harus diisi!!"
            ], 400);
        }

        if ($flg_aktif == 1 || $flg_aktif == 0) {
            try {
                $query = DB::connection('web')->table('master_kabupaten')->insert([
                    'nama'        => $nama,
                    'id_provinsi' => $provinsi,
                    'flg_aktif'   => $flg_aktif
                ]);

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'message' => 'Data berhasil dibuat'
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    "code"    => 501,
                    "status"  => "error",
                    "message" => $e
                ], 501);
            }
        }else{
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Nilai Field 'flg_aktif' yang benar adalah 1 atau 0"
            ], 400);
        }
    }

    public function show($id) {
        try {
            $query = DB::connection('web')->table('master_kabupaten')->where('id', $id)->first();

            if (!$query) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data tidak ada'
                ], 404);
            }else{
                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $query
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_kabupaten')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $nama      = empty($req->input('nama')) ? $check->nama : $req->input('nama');
        $provinsi  = empty($req->input('id_provinsi')) ? $check->id_provinsi : $req->input('id_provinsi');
        $flg_aktif = empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif');

        if ($flg_aktif == 1 || $flg_aktif == 0) {
            try {
                $query = DB::connection('web')->table('master_kabupaten')->where('id', $id)->update([
                    'nama'        => $nama,
                    'id_provinsi' => $provinsi,
                    'flg_aktif'   => $flg_aktif
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
        }else{
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Nilai Field 'flg_aktif' yang benar adalah 1 atau 0"
            ], 400);
        }
    }

    public function delete($id) {
        $check = DB::connection('web')->table('master_kabupaten')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        try {
            $query = DB::connection('web')->table('master_kabupaten')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan ID '.$id.', berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function display($id_prov) {
        try {
            $query = DB::connection('web')->table('master_kabupaten')->where('id_provinsi', $id_prov)->get();

            if (!$query) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data tidak ada'
                ], 404);
            }else{
                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $query
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
