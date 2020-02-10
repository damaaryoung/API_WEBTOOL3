<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\AreaKantor\AsalData;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AsalDataController extends BaseController
{
    public function index() {
        $query = AsalData::select('id', 'nama', 'info')->where('flg_aktif', 1)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
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

    public function store(Request $req) {
        $data = array(
            'nama' => $req->input('nama'),
            'info' => $req->input('info')
        );

        AsalData::create($data);

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
        try {
            $query = AsalData::where('id', $id)->first();

            if ($query == null) {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Data kosong"
                ], 404);
            }

            $data[] = array(
                'nama'      => $query->nama,
                'info'      => $query->info,
                'flg_aktif' => (bool) $query->flg_aktif,
                'created_at'=> Carbon::parse($query->created_at)->format('d-m-Y H:i:s'),
                'updated_at'=> Carbon::parse($query->updated_at)->format('d-m-Y H:i:s')
            );

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

    public function update($id, Request $req) {
        $check = AsalData::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $data = array(
            'nama'      => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'info'      => empty($req->input('info')) ? $check->info : $req->input('info'),
            'flg_aktif' => empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'true' ? 1 : 0)
        );

        AsalData::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function delete($id) {
        try {
            $check = AsalData::where('id', $id)->first();

            if (!$check) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data Tidak Ada!!'
                ], 404);
            }

            AsalData::where('id', $id)->update(['flg_aktif' => 0]);

            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan id '.$id.' berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function trash(){
        $query = AsalData::select('id', 'nama', 'info')->where('flg_aktif', 0)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
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
        $query = AsalData::where('id', $id)->update(['flg_aktif', 1]);

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

    public function search($search) {
        try {
            $query = AsalData::select('nama', 'info')->where('flg_aktif', 1)->where('nama', 'like', '%'.$search.'%')->orderBy('nama', 'asc')->get();

            if ($query == '[]') {
                return response()->json([
                    "code"    => 404,
                    "status"  => "not found",
                    "message" => "Data kosong"
                ], 404);
            }

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
