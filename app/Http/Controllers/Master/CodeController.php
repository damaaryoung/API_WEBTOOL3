<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use Cache;
use DB;

class CodeController extends BaseController
{
    // Produk CA
    public function produk()
    {
        $query = DB::connection('web')->select("SELECT kode_produk, `DESKRIPSI_PRODUK` AS nama_produk FROM view_produk ORDER BY kode_produk ='38' DESC ");
        // $query = Cache::rememberForever('produk', function () {
        //     return DB::connection('web')->select("SELECT kode_produk, `DESKRIPSI_PRODUK` AS nama_produk FROM kre_produk");
        // });

        if (empty($query)) {
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
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
