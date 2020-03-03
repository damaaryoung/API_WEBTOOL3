<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\LogActivity;
use Carbon\Carbon;
use DB;

class LogsController extends BaseController
{
    public function index() {
        $query = LogActivity::orderBy('time', 'desc')->get();

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'data'    => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function detail($id) {
        $query = LogActivity::where('id', $id)->get();

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'data'    => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function limit($limit) {
        $query = LogActivity::orderBy('time', 'desc')->limit($limit)->get();

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'data'    => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit, Request $req)
    {
        $column = array(
            'id', 'subject', 'url', 'method', 'ip', 'agent', 'user_id', 'login_name', 'time'
        );

        if($param != 'filter' && $param != 'search'){
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if($param == 'search'){
            $operator   = "like";
            $func_value = "%{$value}%";
        }else{
            $operator   = "=";
            $func_value = "{$value}";
        }

        $query = LogActivity::orderBy($orderBy, $orderVal);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($key, $operator, $func_value);
        }

        if($limit == 'default'){
            $result = $res;
        }else{
            $result = $res->limit($limit);
        }

        try {
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'count'   => $result->count(),
                'data'    => $result->get()->toArray()
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
