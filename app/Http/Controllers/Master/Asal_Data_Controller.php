<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class Asal_Data_Controller extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_asal_data')->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    public function create_data(Request $req) {
        try {
            $query = DB::connection('web')->table('master_asal_data')->insert([
                'nama' => $req->input('nama')
            ]);

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => 'Data has been created'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }
}
