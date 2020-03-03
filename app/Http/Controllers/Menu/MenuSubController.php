<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Models\Menu\MenuMaster;
use App\Models\Menu\MenuSub;
use Illuminate\Http\Request;
// use DB;

class MenuSubController extends BaseController
{
    public function index() {
        $query = MenuSub::with('menu_master')->select('id','nama','url', 'id_menu_master')->where('flg_aktif', 1)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        foreach ($query as $key => $val) {
            $res[$key] = [
                'id'          => $val->id,
                'nama'        => $val->nama,
                'url'         => $val->url,
                'menu_master' => $val->menu_master['nama']
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
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function store(Request $req) {
        $master = $req->input('id_menu_master');
        $reqNama   = $req->input('nama');
        $nama = strtolower($reqNama);

        $url = preg_replace("/[- ]/", "_", $nama);

        if (!$master) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'id_menu_master' harus diisi"
            ], 400);
        }

        if (!$reqNama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'nama' harus diisi"
            ], 400);
        }

        try {
            $query = MenuSub::create([
                'id_menu_master' => $master,
                'nama'           => $nama,
                'url'            => $url
            ]);

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'data berhasil dibuat'
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
        $query = MenuSub::with('menu_master')
                ->select('id','nama','url', 'id_menu_master', 'flg_aktif')
                ->where('id', $IdOrSlug)
                ->orWhere('url', $IdOrSlug)
                ->first();


        if (!$query) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        $res = [
            'id'          => $query->id,
            'nama'        => $query->nama,
            'url'         => $query->url,
            'menu_master' => $query->menu_master['nama'],
            'flg_aktif'   => (bool) $query->flg_aktif
        ];

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

    public function delete($IdOrSlug) {
        $check = MenuSub::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'Not Found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        MenuSub::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->update(['flg_aktif' => 0]);

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

        $query = MenuSub::with('menu_master')->select('id','nama','url', 'id_menu_master')->where('flg_aktif', 0)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        foreach ($query as $key => $val) {
            $res[$key] = [
                'id'          => $val->id,
                'nama'        => $val->nama,
                'url'         => $val->url,
                'menu_master' => $val->menu_master['nama']
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
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function restore($id) {
        $query = MenuSub::where('id', $id)->update(['flg_aktif' => 1]);

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
        $column = array('id', 'nama', 'url', 'menu_master');

        if($param != 'filter' && $param != 'search'){
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
                'message' => 'gunakan key yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false) {
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

        try {
            // $query = MenuSub::addSelect(['menu_master' => MenuMaster::select('nama')
            // ->whereColumn('id_menu_master', 'menu_master.id')
            // ])
            // ->where('flg_aktif', $status)
            // ->orderBy($orderBy, $orderVal);

            $query = MenuSub::addSelect(['menu_master' => function ($q) {
                $q->select('nama')
                    ->from('menu_master')
                    ->whereColumn('id_menu_master', 'menu_master.id');
            }])
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

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $result->count(),
                'data'   => $result->get()->makeHidden('id_menu_master') //->pluck('menu_master')
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
