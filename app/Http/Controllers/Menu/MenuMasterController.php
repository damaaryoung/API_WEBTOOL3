<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\MenuMaster;
use Carbon\Carbon;
use App\User;
use DB;

class MenuMasterController extends BaseController
{
    public function index() {
        try {
            $query = MenuMaster::get();

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
        $reqNama = $req->input('nama');
        $nama = strtolower($reqNama);
        $icon = $req->input('icon');

        $url = preg_replace("/[- ]/", "_", $nama);

        if (!$reqNama) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'nama' harus diisi"
            ], 400);
        }

        if (!$icon) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'icon' harus diisi"
            ], 400);
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
            $query = MenuMaster::where('url', $slug)->get();

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
        $check = MenuMaster::where('url', $slug)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $reqNama = empty($req->input('nama')) ? $check->nama : $req->input('nama');

        $nama    = strtolower($reqNama);
        $icon    = empty($req->input('icon')) ? $check->icon : $req->input('icon');
        $url     = preg_replace("/[- ]/", "_", $nama);

        try {
            $query = MenuMaster::where('url', $slug)->update([
                'nama' => $nama,
                'url'  => $url,
                'icon' => $icon
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
        $check = MenuMaster::where('url', $slug)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'Not Found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        try {
            $query = MenuMaster::where('url', $slug);
            $query->delete();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data dengan URL(slug) '.$slug.', berhasil dihapus'
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
