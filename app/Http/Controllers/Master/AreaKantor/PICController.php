<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\PICRequest;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class PICController extends BaseController
{
    public function index() {
        $query = PIC::get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
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

    public function store(PICRequest $req) {
        $data = array(
            'user_id'       => $req->input('user_id'),
            'id_mk_area'    => $req->input('id_mk_area'),
            'id_mk_cabang'  => $req->input('id_mk_cabang'),
            'id_mj_pic'     => $req->input('id_mj_pic'),
            'nama'          => $req->input('nama'),
            'flg_aktif'     => $req->input('flg_aktif')
        );

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dibuat'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id) {
        $query = PIC::where('id', $id)->first();

        if ($query == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }else{
            try {
                return response()->json([
                    'code'   => 200,
                    'status' => 'success',
                    'data'   => $query
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'code'   => 501,
                    'status' => 'error',
                    'data'   => $e
                ], 501);
            }
        }
    }

    public function update($id, PICRequest $req) {
        $check = PIC::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'user_id'      => empty($req->input('user_id')) ? $check->user_id : $req->input('user_id'),
            'id_mk_area'   => empty($req->input('id_mk_area')) ? $check->id_mk_area : $req->input('id_mk_area'),
            'id_mk_cabang' => empty($req->input('id_mk_cabang')) ? $check->id_mk_cabang : $req->input('id_mk_cabang'),
            'id_mj_pic'    => empty($req->input('id_mj_pic')) ? $check->id_mj_pic : $req->input('id_mj_pic'),
            'nama'         => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'flg_aktif'    => empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif')
        );

        PIC::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function delete($id) {
        $check = PIC::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        PIC::where('id', $id)->delete();

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan id '.$id.' berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }
}
