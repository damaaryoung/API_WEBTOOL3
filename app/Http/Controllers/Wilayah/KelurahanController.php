<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Wilayah\Kelurahan;
use Illuminate\Http\Request;
use Cache;
use DB;

class KelurahanController extends BaseController
{

    public function index() 
    {    
        $data = array();

       $query = Kelurahan::get();
        // $query = Cache::remember("prov.index", $this->time_cache, function () use ($data) {
        //     foreach (Provinsi::select('id', 'nama')->where('flg_aktif', 1)->orderBy('nama', 'asc')->cursor()
        //         as $cursor) {
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

        $data = array(
            'nama'         => $nama,
            'kode_pos'     => $kode_pos,
            'id_kecamatan' => $kecamatan
        );

        Kelurahan::create($data);

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

    public function show($IdOrName) {
        // $res = array();
        // if(preg_match("/^[0-9]{1,}$/", $IdOrName)){
        $query = Kelurahan::withCount(['kec as nama_kecamatan' => function ($sub) 
        {
            $sub->select('nama');
        }])->where('id', $IdOrName)->first();
        // }else{
        //     Kelurahan::withCount(['kec as nama_kecamatan' => function ($sub) 
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
        $check = Kelurahan::where('id', $id)->first();

        if (empty($check)) {
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

        $data = array(
            'nama'         => $nama,
            'kode_pos'     => $kode_pos,
            'id_kecamatan' => $kecamatan,
            'flg_aktif'    => (bool) $flg_aktif
        );

        Kelurahan::where('id', $id)->update($data);

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
        Kelurahan::where('id', $id)->update(['flg_aktif' => 0]);
        
        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data with ID '.$id.' was deleted successfully'
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
        $query = Kelurahan::withCount(['kec as nama_kecamatan' => function ($sub) 
        {
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
        Kelurahan::where('id', $id)->update(['flg_aktif' => 1]);

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

    public function sector($id_kec) 
    {
        $data = array();

        foreach
        (
            Kelurahan::select('id', 'nama', 'kode_pos', 'id_kecamatan')->where('id_kecamatan', $id_kec)->orderBy('nama', 'asc')->cursor()
            as $cursor
        )
        {
            $data[] = $cursor;
        }

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
        $column = array('id', 'nama', 'kode_pos', 'id_kecamatan');

        if($param != 'filter' && $param != 'search'){
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if($param == 'search'){
            $operator   = "like";
            $func_value = "%{$value}%";
        }else{
            $operator   = "=";
            $func_value = "{$value}";
        }

        $query = Kelurahan::where('flg_aktif', $status)->orderBy($orderBy, $orderVal);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($key, $operator, $func_value);
        }

        
        if($limit == 'default'){
            $result = $res->get();
        }else{
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
