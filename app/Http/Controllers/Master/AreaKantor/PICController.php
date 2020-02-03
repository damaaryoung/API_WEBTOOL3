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
    public function all() {
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
                "email"       => $val->email,
                "jenis_pic"   => $val->jpic['nama_jenis'],
                "nama_area"   => $val->area['nama'],
                "nama_cabang" => $val->cabang['nama'],
                "plafon_max"  => $val->plafon_caa,
                "flg_aktif"   => $val->flg_aktif == 1 ? "true" : "false",
                "created_at"  => Carbon::parse($val->created_at)->format('d-m-Y H:i:s'),
                "updated_at"  => Carbon::parse($val->updated_at)->format('d-m-Y H:i:s')
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

    public function index() {

        $value = 'Team CAA';
        $value_dir = 'Team CAA DIR';

        $query = PIC::with('jpic','area','cabang')
                ->whereHas('jpic', function($q) use($value, $value_dir) {
                    // Query the name field in status table
                    $q->where('keterangan', '!=', $value); // '=' is optional
                    $q->where('keterangan', '!=', $value_dir);
                })
                ->where('flg_aktif', 1)
                ->get();

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
                "email"       => $val->email,
                "jenis_pic"   => $val->jpic['nama_jenis'],
                "id_area"     => $val->id_area,
                "nama_area"   => $val->area['nama'],
                "id_cabang"   => $val->id_cabang,
                "nama_cabang" => $val->cabang['nama'],
                "plafon_max"  => $val->plafon_caa
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
        $email    = $request->auth->email;
        $data = array(
            'user_id'       => $req->input('user_id'),
            'id_area'       => $req->input('id_mk_area'),
            'id_cabang'     => empty($req->input('id_mk_cabang')) ? 0 : $req->input('id_mk_cabang'),
            'id_mj_pic'     => $req->input('id_mj_pic'),
            'nama'          => empty($req->input('nama')) ? $username : $req->input('nama'),
            'email'         => empty($req->input('email')) ? $email : $req->input('email')
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
                "email"          => $query->email,
                "user_id"        => $query->user_id,
                "email_user"     => $query->user['email'],
                "id_jenis_pic"   => $query->id_mj_pic,
                "nama_jenis_pic" => $query->jpic['nama_jenis'],
                "id_area"        => $query->id_area,
                "nama_area"      => $query->area['nama'],
                "id_cabang"      => $query->id_cabang,
                "nama_cabang"    => $query->cabang['nama'],
                "plafon_max"     => $query->plafon_caa,
                "flg_aktif"      => $query->flg_aktif == 0 ? "false" : "true",
                "created_at"     => Carbon::parse($query->created_at)->format('d-m-Y H:i:s')
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
            'email'        => empty($req->input('email')) ? $check->email : $req->input('email'),
            'user_id'      => empty($req->input('user_id')) ? $check->user_id : $req->input('user_id'),
            'id_area'      => empty($req->input('id_mk_area')) ? $check->id_area : $req->input('id_mk_area'),
            'id_cabang'    => empty($req->input('id_mk_cabang')) ? $check->id_cabang : $req->input('id_mk_cabang'),
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

    public function search($search) {
        $query = PIC::with('jpic','area','cabang')->where('flg_aktif', 1)->where('nama', 'like', '%'.$search.'%')->get();

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
                "email"       => $val->email,
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
}
