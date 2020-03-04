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
        $query = Kecamatan::with('kab')->select('id', 'nama', 'id_kabupaten')->where('flg_aktif', 1)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {
            $data[$key] = [
                "id"             => $val->id,
                "nama"           => $val->nama,
                "nama_kabupaten" => $val->kab['nama']
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
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
                "message" => [ "nama" => ["nama belum diisi"]]
            ], 422);
        }

        if (!$kabupaten) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "id_kabupaten" => ["id kabupaten belum diisi"]]
            ], 422);
        }

        if(!empty($kabupaten) && !preg_match("/^[0-9]{1,}$/", $kabupaten)){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "id_kabupaten" => ["id kabupaten harus berupa angka"]]
            ], 422);
        }

        $KQuery = array(
            'nama'         => $nama,
            'id_kabupaten' => $kabupaten
        );

        try {
            Kecamatan::create($KQuery);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dibuat',
                'data'    => $KQuery
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
            $query = Kecamatan::with('kab')->select('id', 'nama', 'id_kabupaten', 'flg_aktif')->where('id', $IdOrName)->first();

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
                'id_kabupaten'   => $query->id_kabupaten,
                'nama_kabupaten' => $query->kab['nama'],
                'flg_aktif'      => $query->flg_aktif == 0 ? "false" : "true"
            ];
        }else{
            $query = Kecamatan::with('kab')->select('id', 'nama', 'id_kabupaten', 'flg_aktif')->where('nama','like','%'.$IdOrName.'%')->get();

            if ($query == '[]') {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data kosong!!'
                ], 404);
            }

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

        if(!empty($kabupaten) && !preg_match("/^[0-9]{1,}$/", $kabupaten)){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["id_kabupaten" => ["id kabupaten harus berupa angka"]]
            ], 422);
        }

        if ($req->input('flg_aktif') != "false" && $req->input('flg_aktif') != "true" && $req->input('flg_aktif') != "") {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["flg_aktif" => ["flg aktif harus salah satu dari jenis berikut false, true"]]
            ], 422);
        }

        $query = array(
            'nama'         => $nama,
            'id_kabupaten' => $kabupaten,
            'flg_aktif'    => $flg_aktif
        );

        try {
            DB::connection('web')->table('master_kecamatan')->where('id', $id)->update($query);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate',
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
            Kecamatan::where('id', $id)->update(['flg_aktif' => 0]);

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

    public function trash(){
        $query = Kecamatan::with('kab')->select('id', 'nama', 'id_kabupaten')->where('flg_aktif', 0)->orderBy('nama', 'asc')->get();

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
        Kecamatan::where('id', $id)->update(['flg_aktif' => 1]);

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

    public function sector($id_kab) {
        $query = Kecamatan::select('id', 'nama', 'id_kabupaten')->where('id_kabupaten', $id_kab)->orderBy('nama', 'asc')->get();

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
