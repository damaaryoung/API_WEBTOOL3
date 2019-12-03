<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\SalesRequest;
use App\Models\AreaKantor\Sales;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class KodeKantorController extends BaseController
{
    public function index(){
        $query = DB::connection('dpm')->table('app_kode_kantor')->select('kode_cabang','nama_area_kerja','kota_kantor','alamat_kantor')->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $value) {
            $data[$key] = [
                'kode_kantor'     => $value->kode_cabang,
                'nama_area_kerja' => $value->nama_area_kerja,
                'alamat'          => $value->alamat_kantor,
                'kota'            => $value->kota_kantor
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
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
