<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\MenuSub;
use App\User;
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
                'code'   => 400,
                'status' => 'error',
                'message'=> $e
            ], 400);
        }
    }

    public function store(Request $req) {
        $master = $req->input('id_menu_master');
        $nama   = $req->input('nama');
        $slugURL = strtolower($nama);

        $url = preg_replace("/[- ]/", "_", $slugURL);

        $query = MenuSub::create([
            'id_menu_master' => $master,
            'nama' => strtolower($nama),
            'url'  => $url
        ]);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'=> $e
            ], 400);
        }
    }

    public function main($slug) {
        try {
            $query = MenuSub::where('url', $slug)->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'=> $e
            ], 400);
        }
    }

    public function edit($slug, Request $req) {
        $master = $req->input('id_menu_master');
        $nama   = $req->input('nama');
        $slugURL = strtolower($nama);

        $url = preg_replace("/[- ]/", "_", $slugURL);

        try {
            $query = MenuSub::where('url', $slug)->update([
                'id_menu_master' => $master,
                'nama' => strtolower($nama),
                'url'  => $url
            ]);

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'data updated successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'=> $e
            ], 400);
        }
    }

    public function delete($slug) {
        $query = MenuSub::where('url', $slug);
        $query->delete();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data Deleted Successfuly'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'   => $e
            ], 400);
        }
    }
}
