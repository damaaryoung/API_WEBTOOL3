<?php

namespace App\Http\Controllers\Master\Bisnis;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;
use Cache;
use DB;

class MitraController extends BaseController
{
    public function __construct() {
        $this->time_cache = config('app.cache_exp');
        $this->chunk      = 100;
    }
    
    public function index() 
    {
        $data = array();

        $query = Cache::remember('mitra.index', $this->time_cache, function () use ($data) {
            
            foreach(
                DB::connection('dpm')->table('kre_kode_group5')->select('kode_group5 as kode_mitra','deskripsi_group5 as nama_mitra', 'jenis_mitra')->where('jenis_mitra', 'MB')->orderBy('deskripsi_group5', 'asc')
                ->cursor() as $cursor
            ){
                $data[] = $cursor;
            }
        
            return $data;

        });

        if (empty($query)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => count($query),
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

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit) 
    {
        $column = array('kode_mitra', 'nama_mitra');

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

        if($key == 'kode_mitra'){
            $orderVal == $keyPar = 'kode_group5';
        }elseif($key == 'nama_mitra'){
            $orderVal == $keyPar = 'deskripsi_group5';
        }
        
        $query = DB::connection('dpm')->table('kre_kode_group5')
            ->select('kode_group5 as kode_mitra','deskripsi_group5 as nama_mitra')
            ->where('jenis_mitra', 'MB')
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($keyPar, $operator, $func_value);
        }

        if($limit == 'default'){
            $result = $res->get();
        }else{
            $result = $res->limit($limit)->get();
        }

        if (empty($result)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        try{
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

    public function show($kode_mitra) 
    {
        $query = DB::connection('dpm')->table('kre_kode_group5')->select('kode_group5 as kode_mitra','deskripsi_group5 as nama_mitra', 'jenis_mitra')->where('jenis_mitra', 'MB')->where('kode_group5', $kode_mitra)->first();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
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
}
