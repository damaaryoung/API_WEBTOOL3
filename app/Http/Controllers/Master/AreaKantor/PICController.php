<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\PICRequest;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
// use DB;

class PICController extends BaseController
{
    public function index() {

        $query = PIC::with('jpic','area','cabang')
                // ->whereHas('jpic', function($q) {
                //     // Query the name field in status table
                //     $q->where('nama_jenis', 'SO'); // '=' is optional
                //     $q->orWhere('nama_jenis', 'AO');
                //     $q->orWhere('nama_jenis', 'CA');
                // })
                ->where('flg_aktif', 1)
                ->orderBy('nama', 'asc')
                ->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {
            $data[$key]= [
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
                'count'  => sizeof($data),
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
                "flg_aktif"      => (bool) $query->flg_aktif,
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

        PIC::where('id', $id)->update(['flg_aktif' => 0]);

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

    public function trash() {
        $query = PIC::with('jpic','area','cabang')
                // ->whereHas('jpic', function($q) {
                //     $q->where('nama_jenis', 'SO');
                //     $q->orWhere('nama_jenis', 'AO');
                //     $q->orWhere('nama_jenis', 'CA');
                // })
                ->where('flg_aktif', 0)
                ->orderBy('nama', 'asc')
                ->get();

        if ($query == '[]'){
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
                'count'  => $query->count(),
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

    public function restore($id) {
        $query = PIC::where('id', $id)->update(['flg_aktif' => 1]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'data berhasil dikembalikan'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit)
    {
        $column = array(
            'id', 'user_id', 'id_area', 'id_cabang', 'id_mj_pic', 'nama', 'email'
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

        $query = PIC::with('jpic','area','cabang')
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);

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

        if ($result->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($result->get() as $key => $val) {
            $exec[$key]= [
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
                'count'  => $result->count(),
                'data'   => $exec
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
