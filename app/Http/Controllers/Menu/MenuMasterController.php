<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Menu\MasterMenuReq;
use Illuminate\Http\Request;
use App\Models\MenuMaster;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MenuMasterController extends BaseController
{
    public function index() {
        try {
            $query = MenuMaster::select('id','nama','url')->get();

            if ($query == '[]') {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Data kosong"
                ], 404);
            }

            return response()->json([
                'code'   => 200,
                'status' => 'success',
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
        if(preg_match("/^[0-9]{1,}$/", $IdOrSlug)){
            $query = MenuMaster::where('id', $IdOrSlug)->first();
        }else{
            $query = MenuMaster::where('url', $IdOrSlug)->first();
        }

        if ($query == null) {
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
            'flg_aktif' => $query->flg_aktif == 0 ? "false" : "true"
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

    public function edit($IdOrSlug, Request $req) {
        if(preg_match("/^[0-9]{1,}$/", $IdOrSlug)){
            $query = MenuMaster::where('id', $IdOrSlug)->first();
        }else{
            $query = MenuMaster::where('url', $IdOrSlug)->first();
        }

        if (!$query) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $data = array(
            'nama' => empty($req->input('nama')) ? $query->nama : $req->input('nama'),
            'icon' => empty($req->input('icon')) ? $check->icon : $req->input('icon'),
            'url'  => preg_replace("/[- ]/", "_", strtolower(empty($req->input('nama')) ? $query->nama : $req->input('nama')))
        );

        $check = MenuMaster::where('url', $data['url'])->first();

        if ($check != null) {
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "url telah ada, harap ganti nama menu yang dimasukan"
            ], 422);
        }

        if(preg_match("/^[0-9]{1,}$/", $IdOrSlug)){
            $query = MenuMaster::where('id', $IdOrSlug)->update($data);
        }else{
            $query = MenuMaster::where('url', $IdOrSlug)->update($data);
        }

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
        $check = MenuMaster::where('url', $IdOrSlug)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'Not Found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        try {
            $query = MenuMaster::where('url', $IdOrSlug);
            $query->delete();

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
}
