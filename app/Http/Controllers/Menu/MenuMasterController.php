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
                'code'   => 400,
                'status' => 'error',
                'message'=> $e
            ], 400);
        }
    }

    public function store(Request $req) {
        $nama = $req->input('nama');
        $slugURL = strtolower($nama);
        $icon = $req->input('icon');

        $url = preg_replace("/[- ]/", "_", $slugURL);

        $query = MenuMaster::create([
            'nama' => strtolower($nama),
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
                'code'   => 400,
                'status' => 'error',
                'message'=> $e
            ], 400);
        }
    }

    public function main($slug) {
        try {
            $query = MenuMaster::where('url', $slug)->get();

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
        $nama = $req->input('nama');
        $slugURL = strtolower($nama);
        $icon = $req->input('icon');

        $url = preg_replace("/[- ]/", "_", $slugURL);

        try {
            $query = MenuMaster::where('url', $slug)->update([
                'nama' => $slugURL,
                'url'  => $url,
                'icon' => $icon
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
        $query = MenuMaster::where('url', $slug);
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
