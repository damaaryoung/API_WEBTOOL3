<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\MenuAccess;
use Carbon\Carbon;
use App\User;
use DB;

class MenuAccessController extends BaseController
{
    public function index() {
        try {
            $query = MenuAccess::get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'   => $e
            ], 400);
        }
    }

    public function show($id_user) {
        try {
            $query = MenuAccess::where('id_user', $id_user)->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'   => $e
            ], 400);
        }
    }

    public function store(Request $req) {
        $query = MenuAccess::create([
            'id_user'       => $req->input('id_user'),
            'id_menu_master'=> $req->input('id_menu_master'),
            'id_menu_sub'   => $req->input('id_menu_sub'),
            'print_access'  => $req->input('print_access'), //Enum('Y','N')
            'add_access'    => $req->input('add_access'),   //Enum('Y','N')
            'edit_access'   => $req->input('edit_access'),  //Enum('Y','N')
            'delete_access' => $req->input('delete_access') //Enum('Y','N')
        ]);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data Created Successfuly'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'   => $e
            ], 400);
        }
    }

    public function update($id_user, Request $req) {
        $query = MenuAccess::where('id_user', $id_user)->update([
            'id_menu_master'=> $req->input('id_menu_master'),
            'id_menu_sub'   => $req->input('id_menu_sub'),
            'print_access'  => $req->input('print_access'), //Enum('Y','N')
            'add_access'    => $req->input('add_access'),   //Enum('Y','N')
            'edit_access'   => $req->input('edit_access'),  //Enum('Y','N')
            'delete_access' => $req->input('delete_access') //Enum('Y','N')
        ]);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data Updated Successfuly'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'message'   => $e
            ], 400);
        }
    }

    public function delete($id_user) {
        $query = MenuAccess::where('id_user', $id_user);
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
