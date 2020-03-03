<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Menu\MasterMenuReq;
use App\Models\Menu\MenuMaster;
use Illuminate\Http\Request;
// use DB;

class MenuMasterController extends BaseController
{
    public function index() {

        $query = MenuMaster::select('id','nama','url')->where('flg_aktif', 1)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
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
                'count'  => $query->count(),
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function store(MasterMenuReq $req) {
        $reqNama = $req->input('nama');
        $nama = strtolower($reqNama);
        $icon = $req->input('icon');

        $url = preg_replace("/[- ]/", "_", $nama);

        $check = MenuMaster::where('url', $url)->first();

        if (!$reqNama) {
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "nama belum diisi"
            ], 422);
        }

        if (!$icon) {
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "icon belum diisi"
            ], 422);
        }

        if ($check != null) {
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "url telah ada, harap ganti nama menu yang dimasukan"
            ], 422);
        }

        $query = MenuMaster::create([
            'nama' => $nama,
            'url'  => $url,
            'icon' => $icon
        ]);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => 'data berhasil dibuat'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function show($IdOrSlug) {
        $query = MenuMaster::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->first();

        if (!$query) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $res = array(
            'id'        => $query->id,
            'nama'      => $query->nama,
            'icon'      => $query->icon,
            'url'       => $query->url,
            'flg_aktif' => (bool) $query->flg_aktif
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function update($IdOrSlug, Request $req) {
        $query = MenuMaster::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->first();

        if (!$query) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $data = array(
            'nama' => empty($req->input('nama')) ? $query->nama : ($req->input('nama') == $query->nama ? $query->nama : $req->input('nama')),
            'icon' => empty($req->input('icon')) ? $query->icon : $req->input('icon'),
            'url'  => preg_replace("/[- ]/", "_", strtolower(empty($req->input('nama')) ? $query->nama : $req->input('nama'))),
            'flg_aktif' => empty($req->input('flg_aktif')) ? $query->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1)
        );

        MenuMaster::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->update($data);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil diupdate'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function delete($IdOrSlug) {
        $check = MenuMaster::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'Not Found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        MenuMaster::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->update(['flg_aktif' => 0]);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data dengan URL(IdOrSlug) '.$IdOrSlug.', berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function trash() {

        $query = MenuMaster::select('id','nama','url')->where('flg_aktif', 0)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
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
                'count'  => $query->count(),
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function restore($id) {
        $query = MenuMaster::where('id', $id)->update(['flg_aktif' => 1]);

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

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit) 
    {
        $column = array('id', 'nama', 'url', 'icon');

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

        if ($key != 'id' && $key != 'nama' && $key != 'url' && $key != 'icon') {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: id, nama, url, icon'
            ], 412);
        }

        if ($orderBy != 'id' && $orderBy != 'nama' && $orderBy != 'url' && $orderBy != 'icon') {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: id, nama, url, icon'
            ], 412);
        }

        if($param == 'search'){
            $operator   = "like";
            $func_value = "%{$value}%";
        }else{
            $operator   = "=";
            $func_value = "{$value}";
        }
        
        $query = MenuMaster::select('id','nama','url')
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($key, $operator, $func_value);
        }

        if($limit == 'default'){
            $result = $res;
        }else{
            $result = $res->limit($limit);
        }

        if ($result->get() == '[]') {
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
                'count'  => $result->count(),
                'data'   => $result->get()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }
}
