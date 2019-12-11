<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Wilayah\Kecamatan;
use Illuminate\Http\Request;
use DB;

class KecamatanController extends BaseController
{
    public function index() {
        try {
            $query = Kecamatan::get();

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
                    "nama"           => $val->nama,
                    "nama_kabupaten" => $val->kab['nama']
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
        $kabupaten = $req->input('id_kabupaten');

        if (!$nama) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "nama belum diisi"
            ], 422);
        }

        if (!$kabupaten) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => "id kabupaten belum diisi"
            ], 422);
        }

        try {
            $query = Kecamatan::create([
                'nama'         => $nama,
                'id_kabupaten' => $kabupaten
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
        if(preg_match("/^[0-9]{1,}$/", $IdOrName)){
            $query = Kecamatan::where('id', $IdOrName)->first();

            $res = [
                'id'             => $query->id,
                'nama'           => $query->nama,
                'id_kabupaten'   => $query->id_kabupaten,
                'nama_kabupaten' => $query->kab['nama'],
                'flg_aktif'      => $query->flg_aktif == 0 ? "false" : "true"
            ];
        }else{
            $query = Kecamatan::where('nama','like','%'.$IdOrName.'%')->get();

            foreach ($query as $key => $val) {
                $res[$key] = [
                    'id'            => $val->id,
                    'nama_kecamatan'=> $val->nama,
                    'id_kabupaten'  => $val->id_kabupaten,
                    'nama_kabupaten'=> $val->kab['nama']
                ];
            }
        }

        try {
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
        $check = Kecamatan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong!!'
            ], 404);
        }

        $nama      = empty($req->input('nama')) ? $check->nama : $req->input('nama');
        $kabupaten = empty($req->input('id_kabupaten')) ? $check->id_kabupaten : $req->input('id_kabupaten');
        $flg_aktif = empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1);


        if ($req->input('flg_aktif') != "false" && $req->input('flg_aktif') != "true" && $req->input('flg_aktif') != "") {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [
                    "flg_aktif" => ["flg aktif harus salah satu dari jenis berikut false, true"]
                ]
            ], 422);
        }

        try {
            $query = DB::connection('web')->table('master_kecamatan')->where('id', $id)->update([
                'nama'         => $nama,
                'id_kabupaten' => $kabupaten,
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
        $check = Kecamatan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong!!'
            ], 404);
        }

        try {
            Kecamatan::where('id', $id)->delete();

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

    public function sector($id_kab) {
        try {
            $query = Kecamatan::where('id_kabupaten', $id_kab)->get();

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
