<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class CodeController extends BaseController
{
    // Produk CA
    public function produk(){
        $query = DB::connection('web')->select("SELECT kode_produk, `DESKRIPSI_PRODUK` AS nama_produk FROM view_produk");
        
        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Kosong'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                "count"  => sizeof($query),
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
