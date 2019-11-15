<?php

namespace App\Http\Controllers\Master\Bisnis;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Bisbis\AsalDataReq;
use App\Models\Bisnis\AsalData;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class AsalDataController extends BaseController
{
    public function index() {
        try {
            $query = AsalData::get();

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

    public function store(AsalDataReq $req) {
        $data = array(
            'nama' = $req->input('nama'),
            'info' = $req->input('info')
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

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_asal_data')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $data = array(
            'nama' => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'info' => empty($req->input('info')) ? $check->info : $req->input('info')
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
            $check = DB::connection('web')->table('master_asal_data')->where('id', $id)->first();

            if (!$check) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data Tidak Ada!!'
                ], 404);
            }

            AsalData::where('id', $id)->delete();

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
}
