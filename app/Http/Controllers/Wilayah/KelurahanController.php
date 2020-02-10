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
        $query = Kelurahan::with('kec')->select('id', 'nama', 'id_kecamatan','kode_pos')->where('flg_aktif', 1)->orderBy('nama', 'asc')->get();

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
                "nama_kecamatan" => $val->kec['nama'],
                'kode_pos'       => (string) $val->kode_pos
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
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
                "message" => [ "nama" => ["nama wajib diisi"]]
            ], 422);
        }

        if (!$kecamatan) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "id_kecamatan" => ["id kecamatan wajib diisi"]]
            ], 422);
        }

        if(!empty($kecamatan) && !preg_match("/^[0-9]{1,}$/", $kecamatan)){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "id_kecamatan" => ["id kecamatan harus berupa angka"]]
            ], 422);
        }

        if (!$kode_pos) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "kode_pos" => ["kode pos wajib diisi"]]
            ], 422);
        }

        if(!empty($kode_pos) && !preg_match("/^[0-9]{1,}$/", $kode_pos)){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "kode_pos" => ["kode pos harus berupa angka"]]
            ], 422);
        }

        if (!empty($kode_pos) && strlen($kode_pos) != 5) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "kode_pos" => ["kode pos harus berjumlah 5 digit"]]
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
        if(preg_match("/^[0-9]{1,}$/", $IdOrName)){
            $query = Kelurahan::with('kec')->select('id', 'nama', 'id_kecamatan', 'kode_pos', 'flg_aktif')->where('id', $IdOrName)->first();

            if ($query == null) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong!!'
                ], 404);
            }

            $res = [
                'id'             => $query->id,
                'nama'           => $query->nama,
                'id_kecamatan'   => $query->id_kecamatan,
                'nama_kecamatan' => $query->kec['nama'],
                'kode_pos'       => $query->kode_pos,
                'flg_aktif'      => $query->flg_aktif == 0 ? "false" : "true"
            ];
        }else{
            $query = Kelurahan::with('kec')->select('id', 'nama', 'id_kecamatan', 'flg_aktif')->where('nama','like','%'.$IdOrName.'%')->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong!!'
                ], 404);
            }

            foreach ($query as $key => $val) {
                $res[$key] = [
                    'id'             => $val->id,
                    'nama_kelurahan' => $val->nama,
                    'nama_kecamatan' => $val->kec['nama']
                ];
            }
        }

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'data'    => $res
            ], 200);
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
        $flg_aktif = empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1);

        if(!empty($kecamatan) && !preg_match("/^[0-9]{1,}$/", $kecamatan)){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "id_kecamatan" => ["id kecamatan harus berupa angka"]]
            ], 422);
        }

        if(!empty($kode_pos) && !preg_match("/^[0-9]{1,}$/", $kode_pos)){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "kode_pos" => ["kode pos harus berupa angka"]]
            ], 422);
        }

        if (!empty($kode_pos) && strlen($kode_pos) != 5) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["kode_pos" => ["kode pos harus berjumlah 5 digit"]]
            ], 422);
        }

        if ($req->input('flg_aktif') != "false" && $req->input('flg_aktif') != "true" && $req->input('flg_aktif') != "") {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["flg_aktif" => ["flg aktif harus salah satu dari jenis berikut false, true"]]
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
            Kelurahan::where('id', $id)->update(['flg_aktif' => 0]);

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

    public function trash(){
        $query = Kelurahan::with('kec')->select('id', 'nama', 'id_kecamatan','kode_pos')->where('flg_aktif', 0)->orderBy('nama', 'asc')->get();

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
                "nama_kecamatan" => $val->kec['nama'],
                'kode_pos'       => (string) $val->kode_pos
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
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

    public function restore($id){
        Kelurahan::where('id', $id)->update(['flg_aktif' => 1]);

        try {

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'data berhasil dikembalikan'
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
        $query = Kelurahan::select('id', 'nama', 'kode_pos', 'id_kecamatan')->where('id_kecamatan', $id_kec)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong!!'
            ], 404);
        }

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'count'   => $query->count(),
                'data'    => $query
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
