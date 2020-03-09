<?php

namespace App\Http\Controllers\Menu;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;
use App\Models\Menu\MenuAccess;
use App\Models\Menu\MenuMaster;
use App\Models\Menu\MenuSub;
use Illuminate\Http\Request;
use Cache;
// use DB;

class MenuAccessController extends BaseController
{
    public function __construct() 
    {
        $this->time_cache = config('app.cache_exp');
        $this->chunk      = 100;
    }
    
    public function index() 
    {
        $query = Cache::remember('mAccess', $this->time_cache, function () {
            
            return MenuAccess::select('id','id_user')
            ->addSelect([
                'menu_master' => MenuMaster::select('nama')->whereColumn('id_menu_master', 'menu_master.id'),
                'menu_sub'    => MenuSub::select('nama')->whereColumn('id_menu_sub', 'menu_sub.id'),
            ])
            ->where('flg_aktif', 1)->orderBy('id_menu_master', 'asc')->get();
            
        });
        
        if (empty($query)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => count($query),
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function show($id)
    {
        $query = MenuAccess::addSelect([
            'menu_master' => MenuMaster::select('nama')->whereColumn('id_menu_master', 'menu_master.id'),
            'menu_sub'    => MenuSub::select('nama')->whereColumn('id_menu_sub', 'menu_sub.id'),
        ])->where('id', $id)->first();

        if (empty($query)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function store(Request $req) 
    {
        $data = [
            'id_user'       => $req->input('id_user'),
            'id_menu_master'=> $req->input('id_menu_master'),
            'id_menu_sub'   => $req->input('id_menu_sub'),
            'print_access'  => $req->input('print_access'), //Enum('Y','N')
            'add_access'    => $req->input('add_access'),   //Enum('Y','N')
            'edit_access'   => $req->input('edit_access'),  //Enum('Y','N')
            'delete_access' => $req->input('delete_access') //Enum('Y','N')
        ];

        extract($data);

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

        MenuAccess::create($data);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function update($id, Request $req) 
    {
        $check = MenuAccess::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $data = array(
            'id_menu_master' => empty($req->input('id_menu_master')) ? $check->id_menu_master : $req->input('id_menu_master'),

            'id_menu_sub'    => empty($req->input('id_menu_sub')) ? $check->id_menu_sub : $req->input('id_menu_sub'),

            'print_access'   => empty($req->input('print_access')) ? $check->print_access : $req->input('print_access'), //Enum('Y','N')

            'add_access'     => empty($req->input('add_access')) ? $check->add_access : $req->input('add_access'),   //Enum('Y','N')

            'edit_access'    => empty($req->input('edit_access')) ? $check->edit_access : $req->input('edit_access'),  //Enum('Y','N')

            'delete_access'  => empty($req->input('delete_access')) ? $check->delete_access : $req->input('delete_access'), //Enum('Y','N')

            'flg_aktif'      => empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1)
        );

        MenuAccess::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil diupdate'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function delete($id_user) 
    {
        MenuAccess::where('id_user', $id_user)->update(['flg_aktif' => 0]);
       
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data dengan Id User '.$id_user.' berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function trash() 
    {
        $query = MenuAccess::select('id','id_user')
        ->addSelect([
            'menu_master' => MenuMaster::select('nama')->whereColumn('id_menu_master', 'menu_master.id'),
            'menu_sub'    => MenuSub::select('nama')->whereColumn('id_menu_sub', 'menu_sub.id'),
        ])->where('flg_aktif', 0)->orderBy('id_menu_master', 'asc')->get();

        if (empty($query)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data kosong"
            ], 404);
        }

        try{
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => count($query),
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }

    public function restore($id) 
    {
        MenuAccess::where('id', $id)->update(['flg_aktif' => 1]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'data berhasil dikembalikan'
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
