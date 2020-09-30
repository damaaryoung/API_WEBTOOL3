<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Wilayah\Kabupaten;
use Illuminate\Http\Request;
use Cache;
use DB;

class KabupatenController extends BaseController
{
    public function __construct()
    {
        $this->time_cache = config('app.cache_exp');
    }

    public function index()
    {
        $data = array();

        $query = Kabupaten::get();
        // $query = Cache::remember('kab.index', $this->time_cache, function () use ($data) {
        //     foreach (Kabupaten::withCount(['prov as nama_provinsi' => function ($sub) {
        //         $sub->select('nama');
        //     }])
        //         ->where('flg_aktif', 1)->orderBy('nama', 'asc')->cursor() as $cursor) {
        //         $data[] = $cursor;
        //     }

        //     return $data;
        // });

        if (empty($query)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($query),
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(Request $req)
    {
        $data = array(
            "nama"     => $req->input('nama'),
            "id_provinsi" => $req->input('id_provinsi')
        );

        if (empty($data['nama'])) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["nama" => ["nama wajib diisi"]]
            ], 422);
        }

        if (empty($data['id_provinsi'])) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["id_provinsi" => ["id provinsi wajib diisi"]]
            ], 422);
        }

        if (!preg_match("/^[0-9]{1,}$/", $data['id_provinsi'])) {
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
                'message' => 'Data berhasil dibuat',
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($IdOrName)
    {
        // $res = array();
        $query = Kabupaten::where('id', $IdOrName)->first();
        // if(preg_match("/^[0-9]{1,}$/", $IdOrName)){
        // $query = Kabupaten::withCount(['prov as nama_provinsi' => function ($sub) {
        //     $sub->select('nama');
        // }])->where('id', $IdOrName)->first();
        // }else{
        //     $query = Kabupaten::withCount(['prov as nama_provinsi' => function($sub) 
        //     {
        //         $sub->select('nama');
        //     }])->where('nama','like','%'.$IdOrName.'%')->get();
        // }

        if (empty($query)) {
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
                'data'    => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, Request $req)
    {
        $check = Kabupaten::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $nama      = empty($req->input('nama'))         ? $check->nama : $req->input('nama');
        $provinsi  = empty($req->input('id_provinsi'))  ? $check->id_provinsi : $req->input('id_provinsi');
        $flg_aktif = empty($req->input('flg_aktif'))    ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1);

        if (!empty($provinsi) && !preg_match("/^[0-9]{1,}$/", $provinsi)) {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["id_provinsi" => ["id provinsi harus berupa angka"]]
            ], 422);
        }

        if ($req->input('flg_aktif') != "false" && $req->input('flg_aktif') != "true" && $req->input('flg_aktif') != "") {
            return response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => ["flg_aktif" => ["flg aktif harus salah satu dari jenis berikut false, true"]]
            ], 422);
        }

        $data = array(
            'nama'        => $nama,
            'id_provinsi' => $provinsi,
            'flg_aktif'   => (bool) $flg_aktif
        );

        Kabupaten::where('id', $id)->update($data);

        try {

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate',
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function delete($id)
    {
        Kabupaten::where('id', $id)->update(['flg_aktif' => 0]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan ID ' . $id . ', berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function trash()
    {
        $query = Kabupaten::withCount(['prov as nama_provinsi' => function ($sub) {
            $sub->select('nama');
        }])->where('flg_aktif', 0)->orderBy('nama', 'asc')->get();

        if (empty($query)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong!!"
            ], 404);
        }

        try {

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function restore($id)
    {
        Kabupaten::where('id', $id)->update(['flg_aktif' => 1]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'data berhasil dikembalikan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function sector($id_prov)
    {
        // $data = array();
        $data = Kabupaten::select('id', 'nama', 'id_povinsi')
            ->where('vw_master_kabupaten.id_povinsi', $id_prov)->get();
        // $data = Kabupaten::select('id', 'nama', 'id_provinsi')->where('id_provinsi', $id_prov)->get();


        if (empty($data)) {
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
                'count'   => count($data),
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit)
    {
        $column = array('id', 'nama', 'id_provinsi');

        if ($param != 'filter' && $param != 'search') {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if ($param == 'search') {
            $operator   = "like";
            $func_value = "%{$value}%";
        } else {
            $operator   = "=";
            $func_value = "{$value}";
        }

        $query = Kabupaten::where('flg_aktif', $status)->orderBy($orderBy, $orderVal);

        if ($value == 'default') {
            $res = $query;
        } else {
            $res = $query->where($key, $operator, $func_value);
        }

        if ($limit == 'default') {
            $result = $res->get();
        } else {
            $result = $res->limit($limit)->get();
        }

        if (empty($result)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $result->count(),
                'data'   => $result
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
