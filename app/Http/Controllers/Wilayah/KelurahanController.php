<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Wilayah\Kelurahan;
use Illuminate\Http\Request;
use DB;

class KelurahanController extends BaseController
{
    public function index() {
        try {
            $query = Kelurahan::get();

            if ($query == '[]') {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Data kosong!!"
                ], 404);
            }

            $res = array();
            foreach ($query as $key => $val) {
                $res[$key] = [
                    "id"             => $val->id,
                    "nama_kelurahan" => $val->nama,
                    "nama_kecamatan" => $val->kec['nama']
                ];
            }

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
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

        if (!$nama) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "nama belum diisi"
            ], 422);
        }

        if (!$kecamatan) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "id kecamatan belum diisi"
            ], 422);
        }

        if (!$kode_pos) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "kode pos belum diisi"
            ], 422);
        }

        if (strlen($kode_pos) != 5) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "kode pos harus berjumlah 5 digit"
            ], 422);
        }

        try {
            $query = Kelurahan::create([
                'nama'         => $nama,
                'kode_pos'     => $kode_pos,
                'id_kecamatan' => $kecamatan
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
    }

    public function show($IdOrName) {
        $res = array();
        if(preg_match("/^([0-9])$/", $IdOrName)){
            $query = Kelurahan::where('id', $IdOrName)->first();

            $res = [
                'id'             => $query->id,
                'nama_kelurahan' => $query->nama,
                'id_kecamatan'   => $query->id_kecamatan,
                'nama_kecamatan' => $query->kec['nama'],
                'flg_aktif'      => $query->flg_aktif
            ];
        }else{
            $query = Kelurahan::where('nama','like','%'.$IdOrName.'%')->get();

            foreach ($query as $key => $val) {
                $res[$key] = [
                    'id'             => $val->id,
                    'nama_kelurahan' => $val->nama,
                    'nama_kecamatan' => $val->kec['nama']
                ];
            }
        }

        try {
            if ($query == null || $query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong!!'
                ], 404);
            }else{
                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'data'    => $res
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
        $check = Kelurahan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong!!'
            ], 404);
        }

        $nama      = empty($req->input('nama')) ? $check->nama : $req->input('nama');
        $kode_pos  = empty($req->input('kode_pos')) ? $check->kode_pos : $req->input('kode_pos');
        $kecamatan = empty($req->input('id_kecamatan')) ? $check->id_kecamatan : $req->input('id_kecamatan');
        $flg_aktif = empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif');

        if (strlen($kode_pos) != 5) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "kode pos harus berjumlah 5 digit"
            ], 422);
        }

        try {
            $query = Kelurahan::where('id', $id)->update([
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
    }

    public function delete($id) {
        $check = Kelurahan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong!!'
            ], 404);
        }

        try {
            Kelurahan::where('id', $id)->delete();

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

    public function sector($id_kec) {
        try {
            $query = Kelurahan::where('id_kecamatan', $id_kec)->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong!!'
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
