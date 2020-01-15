<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Menu\MenuSub;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MenuSubController extends BaseController
{
    public function all() {
        try {
            $query = MenuSub::with('menu_master')->get();

            if ($query == '[]') {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Data kosong"
                ], 404);
            }

            foreach ($query as $key => $val) {
                $data[$key] = [
                    'id'          => $val->id,
                    'nama'        => $val->nama,
                    'url'         => $val->url,
                    'menu_master' => $val->menu_master['nama'],
                    'flg_aktif'   => $val->flg_aktif == 1 ? 'true' : 'false'
                ];
            }

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'message'=> $e
            ], 501);
        }
    }

    public function index() {
        try {
            $query = MenuSub::with('menu_master')->select('id','nama','url', 'id_menu_master')->where('flg_aktif', 1)->get();

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
            'flg_aktif'   => $query->flg_aktif == 0 ? "false" : "true"
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

    public function edit($IdOrSlug, Request $req) {
        $check = MenuSub::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->first();

        if (!$check) {
            return response()->json([
                'code'   => 404,
                'status' => 'not found',
                'message'=> 'Data tidak ada'
            ], 404);
        }

        $master  = empty($req->input('id_menu_master')) ? $check->id_menu_master : $req->input('id_menu_master');
        $reqNama = empty($req->input('nama')) ? $check->nama : ($req->input('nama') == $check->nama ? $check->nama : $req->input('nama'));

        $nama = strtolower($reqNama);
        $url = preg_replace("/[- ]/", "_", $nama);
        $flg_aktif = empty($req->input('flg_aktif')) ? $query->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1);

        MenuSub::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->update([
            'id_menu_master' => $master,
            'nama'           => $nama,
            'url'            => $url,
            'flg_aktif'      => $flg_aktif
        ]);

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
        $check = MenuSub::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->first();

        if (!$check) {
            return response()->json([
                'code'   => 404,
                'status' => 'not found',
                'message'=> 'Data tidak ada'
            ], 404);
        }

        MenuSub::where('id', $IdOrSlug)->orWhere('url', $IdOrSlug)->delete();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data dengan URL(IdOrSlug) '.$IdOrSlug.', berhasil dihapus'
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
