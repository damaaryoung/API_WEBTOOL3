<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Wilayah\Kabupaten;
use Illuminate\Http\Request;
use DB;

class KabupatenController extends BaseController
{
    public function all(){
        try {
            $query = Kabupaten::with('prov')->orderBy('nama', 'asc')->get();

            if ($query == '[]') {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Data kosong!!"
                ], 404);
            }

            foreach ($query as $key => $val) {
                $res[$key] = [
                    "id"            => $val->id,
                    "nama"          => $val->nama,
                    "nama_provinsi" => $val->prov['nama'],
                    "flg_aktif"     => $val->flg_aktif == 1 ? 'true' : 'false'
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

    public function index() {
        try {
            $query = Kabupaten::with('prov')->select('id', 'nama', 'id_provinsi')->where('flg_aktif', 1)->orderBy('nama', 'asc')->get();

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
                    "id"            => $val->id,
                    "nama"          => $val->nama,
                    "nama_provinsi" => $val->prov['nama']
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
        $data = array(
            "nama"     => $req->input('nama'),
            "provinsi" => $req->input('id_provinsi')
        );

        if (empty($data['nama'])) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["nama" => ["nama wajib diisi"]]
            ], 422);
        }

        if (empty($data['provinsi'])) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["id_provinsi" => ["id provinsi wajib diisi"]]
            ], 422);
        }

        if(!preg_match("/^[0-9]{1,}$/", $data['provinsi'])){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["id_provinsi" => ["id provinsi harus berupa angka"]]
            ], 422);
        }

        Kabupaten::create($data);

        try {
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
            $query = Kabupaten::with('prov')->select('id', 'nama', 'id_provinsi', 'flg_aktif')->where('id', $IdOrName)->first();

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
                'id_provinsi'    => $query->id_provinsi,
                'nama_provinsi'  => $query->prov['nama'],
                'flg_aktif'      => $query->flg_aktif == 0 ? "false" : "true"
            ];
        }else{
            $query = Kabupaten::with('prov')->select('id', 'nama', 'id_provinsi', 'flg_aktif')->where('nama','like','%'.$IdOrName.'%')->get();

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
                    'nama_kabupaten' => $val->nama,
                    'nama_provinsi'  => $val->prov['nama'],
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
        $check = Kabupaten::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $nama      = empty($req->input('nama')) ? $check->nama : $req->input('nama');
        $provinsi  = empty($req->input('id_provinsi')) ? $check->id_provinsi : $req->input('id_provinsi');
        $flg_aktif = empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1);

        if(!empty($provinsi) && !preg_match("/^[0-9]{1,}$/", $provinsi)){
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => [ "id_provinsi" => ["id provinsi harus berupa angka"]]
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
            $query = Kabupaten::where('id', $id)->update([
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
    }

    public function delete($id) {
        $check = Kabupaten::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong!!'
            ], 404);
        }

        try {
            $query = Kabupaten::where('id', $id)->update(['flg_aktif' => 0]);

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

    public function sector($id_prov) {
        try {
            $query = Kabupaten::where('id_provinsi', $id_prov)->orderBy('nama', 'asc')->get();

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

    public function search($search) {
        try {
            $query = Kabupaten::with('prov')->select('id', 'nama', 'id_provinsi')->where('flg_aktif', 1)->where('nama', 'like', '%'.$search.'%')->orderBy('nama', 'asc')->get();

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
                    "id"            => $val->id,
                    "nama"          => $val->nama,
                    "nama_provinsi" => $val->prov['nama']
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
}
