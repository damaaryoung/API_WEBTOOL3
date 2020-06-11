<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\PICRequest;
use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
use App\Models\AreaKantor\ParameterAcc;
// use DB;

class ParameterAccessController extends BaseController
{
    public function index()
    {
        $params = ParameterAcc::get();

        if ($params === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Akses Parameter Kosong'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'data'  => $params
        ]);
    }

    public function show($id)
    {
        $params = ParameterAcc::where('id', $id)->first();
        if ($params === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Akses Parameter Kosong'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'data'  => $params
        ]);
    }
}
