<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\JenisPICReq;
use App\Models\AreaKantor\JPIC;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class JPICController extends BaseController
{
    public function index() {
        $query = JPIC::select('id', 'nama_jenis','keterangan')->orderBy('nama_jenis', 'asc')->get();

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
                'count'  => $query->count(),
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

    public function store(JenisPICReq $req) {
        $data = array(
            'nama_jenis' => $req->input('nama_jenis'),
            'cakupan'    => $req->input('cakupan'), // Schope
            'keterangan' => $req->input('keterangan'),
            'bagian'     => $req->input('bagian')
        );

        JPIC::create($data);

        try {
            return response()->json([
                "code"    => 200,
                "status"  => "success",
                "message" => "Data berhasil dibuat"
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
        $query = JPIC::where('id', $id)->first();

        if ($query == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'id'         => $query->id,
            'nama_jenis' => $query->nama_jenis,
            'keterangan' => $query->keterangan,
            'created_at' => Carbon::parse($query->created_at)->format('d-m-Y H:i:s'),
            'updated_at' => Carbon::parse($query->updated_at)->format('d-m-Y H:i:s')
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function update($id, JenisPICReq $req) {
        $check = JPIC::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'nama_jenis' => empty($req->input('nama_jenis')) ? $check->nama_jenis : $req->input('nama_jenis'),
            'cakupan'    => empty($req->input('cakupan')) ? $check->cakupan : $req->input('cakupan'), // Schope
            'keterangan' => empty($req->input('keterangan')) ? $check->keterangan : $req->input('keterangan'),
            'bagian'     => empty($req->input('bagian')) ? $check->bagian : $req->input('bagian')
        );

        JPIC::where('id', $id)->update($data);

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
        $check = JPIC::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        JPIC::where('id', $id)->delete();

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

    public function trash(){
        $query = JPIC::select('id', 'nama_jenis','keterangan')->orderBy('nama_jenis', 'asc')->onlyTrashed()->get();

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
                'count'  => $query->count(),
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

    public function restore($id){
        $query = JPIC::onlyTrashed()->where('id',$id)->restore();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => 'Data berhasil dikembalikan ke daftar'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function search($search) {
        $query = JPIC::select('id', 'nama_jenis','keterangan')->where('nama_jenis', 'like', '%'.$search.'%')->orderBy('nama_jenis', 'asc')->get();

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
}
