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
    public function teamCAA(Request $req) {
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $query = PIC::with('jpic','area','cabang')->where('id_mk_cabang', $id_cabang)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {
            $data[$key]= [
                "id"          => $val->id,
                "nama"        => $val->nama,
                "jenis_pic"   => $val->jpic['nama_jenis'],
                "nama_area"   => $val->area['nama'],
                "nama_cabang" => $val->cabang['nama'],
                "email"       => $val->user['email']
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

    public function index() {
        $query = PIC::with('jpic','area','cabang')->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {
            $res[$key]= [
                "id"          => $val->id,
                "nama"        => $val->nama,
                "jenis_pic"   => $val->jpic['nama_jenis'],
                "nama_area"   => $val->area['nama'],
                "nama_cabang" => $val->cabang['nama']
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(Request $request, PICRequest $req) {
        $username = $request->auth->user;
        $data = array(
            'user_id'       => $req->input('user_id'),
            'id_mk_area'    => $req->input('id_mk_area'),
            'id_mk_cabang'  => $req->input('id_mk_cabang'),
            'id_mj_pic'     => $req->input('id_mj_pic'),
            'nama'          => empty($req->input('nama')) ? $username : $req->input('nama')
        );

        PIC::create($data);

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
        $query = PIC::with('jpic','area','cabang')->where('id', $id)->first();

        if ($query == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }else{
            $res = [
                "id"             => $query->id,
                "nama"           => $query->nama,
                "user_id"        => $query->user_id,
                "email_user"     => $query->user['email'],
                "id_jenis_pic"   => $query->id_mj_pic,
                "nama_jenis_pic" => $query->jpic['nama_jenis'],
                "id_area"        => $query->id_mk_area,
                "nama_area"      => $query->area['nama'],
                "id_cabang"      => $query->id_mk_cabang,
                "nama_cabang"    => $query->cabang['nama'],
                "flg_aktif"      => $query->flg_aktif == 0 ? "false" : "true",
                "created_at"     => date($query->created_at)
            ];

            try {
                return response()->json([
                    'code'   => 200,
                    'status' => 'success',
                    'data'   => $res
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
            'nama'         => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'user_id'      => empty($req->input('user_id')) ? $check->user_id : $req->input('user_id'),
            'id_mk_area'   => empty($req->input('id_mk_area')) ? $check->id_mk_area : $req->input('id_mk_area'),
            'id_mk_cabang' => empty($req->input('id_mk_cabang')) ? $check->id_mk_cabang : $req->input('id_mk_cabang'),
            'id_mj_pic'    => empty($req->input('id_mj_pic')) ? $check->id_mj_pic : $req->input('id_mj_pic'),
            'flg_aktif'    => empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1)
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
