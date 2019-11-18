<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\LogActivity;
use Carbon\Carbon;
use DB;

class LogsController extends BaseController
{
    public function index(Request $req) {
        $user_id = $req->auth->user_id;
        $query = LogActivity::where('user_id', $user_id)->get();

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

    public function detail($id, Request $req) {
        $user_id = $req->auth->user_id;
        $query = LogActivity::where('user_id', $user_id)->where('id', $id)->get();

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

    public function limit($limit, Request $req) {
        $user_id = $req->auth->user_id;
        $query = LogActivity::where('user_id', $user_id)->limit($limit)->get();

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

    public function search($search, Request $req) {
        $user_id = $req->auth->user_id;
        $query = LogActivity::where('user_id', $user_id)->where('subject', 'like', '%'.$search.'%')->get();

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
}
