<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use DB;

class KelurahanController extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_kelurahan')->get();

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

    public function store(Request $req) {
        $nama      = $req->input('nama');
        $kode_pos  = $req->input('kode_pos');
        $kecamatan = $req->input('id_kecamatan');
        $flg_aktif = $req->input('flg_aktif');

        if (!$nama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'nama' harus diisi !"
            ], 400);
        }

        if (!$kode_pos) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'kode_pos' harus diisi !"
            ], 400);
        }

        if (!$kecamatan) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'kecamatan' harus diisi !"
            ], 400);
        }

        if (!$flg_aktif) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'flg_aktif' harus diisi !"
            ], 400);
        }

        if (strlen($kode_pos) != 5) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'kode_pos' harus 5 digit angka !"
            ], 400);
        }

        if ($flg_aktif == 1 || $flg_aktif == 0) {
            try {
                $query = DB::connection('web')->table('master_kelurahan')->insert([
                    'nama'         => $nama,
                    'kode_pos'     => $kode_pos,
                    'id_kecamatan' => $kecamatan,
                    'flg_aktif'    => $flg_aktif
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
            $query = DB::connection('web')->table('master_kelurahan')->where('id', $id)->first();

            if (!$query) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data tidak ada!!'
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
        $check = DB::connection('web')->table('master_kelurahan')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada!!'
            ], 404);
        }

        $nama      = empty($req->input('nama')) ? $check->nama : $req->input('nama');
        $kode_pos  = empty($req->input('kode_pos')) ? $check->kode_pos : $req->input('kode_pos');
        $kecamatan = empty($req->input('id_kecamatan')) ? $check->id_kecamatan : $req->input('id_kecamatan');
        $flg_aktif = empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif');

        if (strlen($kode_pos) != 5) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'kode_pos' harus 5 digit angka!"
            ], 400);
        }

        if ($flg_aktif == 1 || $flg_aktif == 0) {
            try {
                $query = DB::connection('web')->table('master_kelurahan')->where('id', $id)->update([
                    'nama'         => $nama,
                    'kode_pos'     => $kode_pos,
                    'id_kecamatan' => $kecamatan,
                    'flg_aktif'    => $flg_aktif
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
        $check = DB::connection('web')->table('master_kelurahan')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada!!'
            ], 404);
        }

        try {
            DB::connection('web')->table('master_kelurahan')->where('id', $id)->delete();

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data with ID '.$id.' was deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function display($id_kec) {
        try {
            $query = DB::connection('web')->table('master_kelurahan')->where('id_kecamatan', $id_kec)->get();

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
