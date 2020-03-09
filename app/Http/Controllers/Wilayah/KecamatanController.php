<?php

namespace App\Http\Controllers\Wilayah;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Wilayah\Kecamatan;
use Illuminate\Http\Request;
use Cache;
use DB;

class KecamatanController extends BaseController
{
    public function __construct() {
        $this->time_cache = config('app.cache_exp');
    }
    
    public function index()
    {
        $data = array();
        $query = Cache::rememberForever("kec.index", function () use ($data)
        {
            foreach(
                Kecamatan::withCount(['kab as nama_kabupaten' => function($sub)
                {
                    $sub->select('nama');
                }])
                ->where('flg_aktif', 1)
                ->orderBy('nama', 'asc')
                ->cursor() as $cursor
            )
            {
                $data[] = $cursor;                
            }

            return $data;
        });

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

        $form = array(
            'nama'         => $nama,
            'id_kabupaten' => $kabupaten
        );

        Kecamatan::create($form); //703ms
        
        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dibuat',
                'data'    => $form
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
        // if(preg_match("/^[0-9]{1,}$/", $IdOrName)){
            
            $query = Kecamatan::withCount(['kab as nama_kabupaten' => function($sub){
                $sub->select('nama');
            }])->where('id', $IdOrName)->first()->makeHidden(['id_kabupaten', 'flg_aktif']);
        // }else{
        //     $query = Kecamatan::withCount(['kab as nama_kabupaten' => function($sub){
        //         $sub->select('nama');
        //     }])->select('id', 'nama', 'id_kabupaten', 'flg_aktif')->where('nama','like','%'.$IdOrName.'%')->get();
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

    public function update($id, Request $req) {
        $check = Kecamatan::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong!!'
            ], 404);
        }

        $nama      = empty($req->input('nama'))         ? $check->nama : $req->input('nama');
        $kabupaten = empty($req->input('id_kabupaten')) ? $check->id_kabupaten : $req->input('id_kabupaten');
        $flg_aktif = empty($req->input('flg_aktif'))    ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1);

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

        $data = array(
            'nama'         => $nama,
            'id_kabupaten' => $kabupaten,
            'flg_aktif'    => (bool) $flg_aktif
        );

        Kecamatan::where('id', $id)->update($data);

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
        Kecamatan::where('id', $id)->update(['flg_aktif' => 0]);
        
        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan ID '.$id.', berhasil dihapus'
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
        $query = Kecamatan::withCount(['kab as nama_kabupaten' => function($sub)
        {
            $sub->select('nama_kabupaten');
        }])
        ->where('flg_aktif', 0)
        ->orderBy('nama', 'asc')
        ->get();

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
        return Kecamatan::where('id', $id)->update(['flg_aktif' => 1]);

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

    public function sector($id_kab)
    {
        $data = array();

        foreach
        (
            Kecamatan::select('id', 'nama', 'id_kabupaten')->where('id_kabupaten', $id_kab)->orderBy('nama', 'asc')->cursor() as $cursor
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
        $column = array('id', 'nama', 'id_kabupaten');

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

        $query = Kecamatan::where('flg_aktif', $status)->orderBy($orderBy, $orderVal);

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
