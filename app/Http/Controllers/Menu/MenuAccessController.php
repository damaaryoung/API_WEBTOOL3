<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\MenuAccess;
use App\Models\User;
use Carbon\Carbon;
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
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
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
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function store(Request $req) {
        $id_user        => $req->input('id_user');
        $id_menu_master => $req->input('id_menu_master');
        $id_menu_sub    => $req->input('id_menu_sub');
        $print_access   => $req->input('print_access'); //Enum('Y','N')
        $add_access     => $req->input('add_access');   //Enum('Y','N')
        $edit_access    => $req->input('edit_access');  //Enum('Y','N')
        $delete_access  => $req->input('delete_access'); //Enum('Y','N')

        if (!$id_user) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'id_user' harus diisi"
            ], 400);
        }

        if (!$id_menu_master) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'id_menu_master' harus diisi"
            ], 400);
        }

        if (!$id_menu_sub) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'id_menu_sub' harus diisi"
            ], 400);
        }

        if (!$print_access) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'print_access' harus diisi"
            ], 400);
        }

        if (!$add_access) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'add_access' harus diisi"
            ], 400);
        }

        if (!$edit_access) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'edit_access' harus diisi"
            ], 400);
        }

        if (!$delete_access) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'delete_access' harus diisi"
            ], 400);
        }

        $query = MenuAccess::create([
            'id_user'       => $id_user,
            'id_menu_master'=> $id_menu_master,
            'id_menu_sub'   => $id_menu_sub,
            'print_access'  => $print_access, //Enum('Y','N')
            'add_access'    => $add_access,   //Enum('Y','N')
            'edit_access'   => $edit_access,  //Enum('Y','N')
            'delete_access' => $delete_access //Enum('Y','N')
        ]);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function update($id_user, Request $req) {
        $check = MenuAccess::where('id_user', $id_user)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $id_menu_master => empty($req->input('id_menu_master')) ? $check->id_menu_master : $req->input('id_menu_master');
        $id_menu_sub    => empty($req->input('id_menu_sub')) ? $check->id_menu_sub : $req->input('id_menu_sub');
        $print_access   => empty($req->input('print_access')) ? $check->print_access : $req->input('print_access'); //Enum('Y','N')
        $add_access     => empty($req->input('add_access')) ? $check->add_access : $req->input('add_access');   //Enum('Y','N')
        $edit_access    => empty($req->input('edit_access')) ? $check->edit_access : $req->input('edit_access');  //Enum('Y','N')
        $delete_access  => empty($req->input('delete_access')) ? $check->delete_access : $req->input('delete_access'); //Enum('Y','N')

        $query = MenuAccess::where('id_user', $id_user)->update([
            'id_menu_master'=> $id_menu_master,
            'id_menu_sub'   => $id_menu_sub,
            'print_access'  => $print_access, //Enum('Y','N')
            'add_access'    => $add_access,   //Enum('Y','N')
            'edit_access'   => $edit_access,  //Enum('Y','N')
            'delete_access' => $delete_access //Enum('Y','N')
        ]);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil diupdate'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function delete($id_user) {
        $check = MenuAccess::where('id_user', $id_user)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        try {
            $query = MenuAccess::where('id_user', $id_user);
            $query->delete();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data dengan Id User '.$id_user.' berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
