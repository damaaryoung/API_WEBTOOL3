<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\AreaKantor\JenisPICReq;
use App\Models\AreaKantor\JPIC;
use Cache;
// use Illuminate\Http\Request;
// use App\Models\User;
use Carbon\Carbon;

class JPICController extends BaseController
{
    public function __construct() {
        $this->time_cache = config('app.cache_exp');
    }

    public function index() 
    {
        // $query = Cache::remember('jpic.index', $this->time_cache, function () {
            $query = JPIC::select('id', 'nama_jenis','keterangan')->orderBy('nama_jenis', 'asc')->get();
        // });

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
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

    public function store(JenisPICReq $req) 
    {
        $data = array(
            'nama_jenis' => $req->input('nama_jenis'),
            'cakupan'    => $req->input('cakupan'), // Schope
            'keterangan' => $req->input('keterangan'),
            'bagian'     => $req->input('bagian')
        );

        JPIC::create($data);

        try {
            return response()->json([
                "code"    => 200,
                "status"  => "success",
                "message" => "Data berhasil dibuat",
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

    public function show($id) 
    {
        $query = JPIC::select('id', 'nama_jenis', 'keterangan', 'created_at', 'updated_at')->where('id', $id)->first();

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function update($id, JenisPICReq $req) 
    {
        $check = JPIC::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'nama_jenis' => empty($req->input('nama_jenis')) ? $check->nama_jenis : $req->input('nama_jenis'),
            'cakupan'    => empty($req->input('cakupan')) ? $check->cakupan : $req->input('cakupan'), // Schope
            'keterangan' => empty($req->input('keterangan')) ? $check->keterangan : $req->input('keterangan'),
            'bagian'     => empty($req->input('bagian')) ? $check->bagian : $req->input('bagian')
        );

        JPIC::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate',
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function delete($id) 
    {
        JPIC::where('id', $id)->delete();

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan id '.$id.' berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function trash()
    {
        $query = JPIC::select('id', 'nama_jenis','keterangan')->orderBy('nama_jenis', 'asc')->onlyTrashed()->get();

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
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
        JPIC::onlyTrashed()->where('id',$id)->restore();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => 'Data berhasil dikembalikan ke daftar'
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
        $column = array('id', 'nama_jenis', 'cakupan', 'urutan_jabatan', 'keterangan', 'bagian');

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

        $query = JPIC::select('id', 'nama_jenis','keterangan')->orderBy($orderBy, $orderVal);

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
                'message' => 'Data kosong'
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
