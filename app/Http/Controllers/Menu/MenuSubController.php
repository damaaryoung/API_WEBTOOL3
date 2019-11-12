<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\MenuSub;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MenuSubController extends BaseController
{
    public function index() {
        try {
            $query = MenuSub::get();

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

    public function store(Request $req) {
        $master = $req->input('id_menu_master');
        $reqNama   = $req->input('nama');
        $nama = strtolower($reqNama);

        $url = preg_replace("/[- ]/", "_", $nama);

        if (!$id_menu_master) {
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

    public function show($slug) {
        try {
            $query = MenuSub::where('url', $slug)->first();

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

    public function edit($slug, Request $req) {
        $check = MenuSub::where('url', $slug)->first();

        if (!$check) {
            return response()->json([
                'code'   => 404,
                'status' => 'not found',
                'message'=> 'Data tidak ada'
            ], 404);
        }

        $master  = empty($req->input('id_menu_master')) ? $check->id_menu_master : $req->input('id_menu_master');
        $reqNama = empty($req->input('nama')) ? $check->nama : $req->input('nama');

        $nama = strtolower($reqNama);
        $url = preg_replace("/[- ]/", "_", $nama);

        try {
            $query = MenuSub::where('url', $slug)->update([
                'id_menu_master' => $master,
                'nama'           => $nama,
                'url'            => $url
            ]);

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

    public function delete($slug) {
        $check = MenuSub::where('url', $slug)->first();

        if (!$check) {
            return response()->json([
                'code'   => 404,
                'status' => 'not found',
                'message'=> 'Data tidak ada'
            ], 404);
        }

        try {
            $query = MenuSub::where('url', $slug);
            $query->delete();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data dengan URL(slug) '.$slug.', berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'   => $e
            ], 501);
        }
    }
}
