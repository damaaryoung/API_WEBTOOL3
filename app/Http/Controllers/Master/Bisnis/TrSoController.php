<?php

namespace App\Http\Controllers\Master\Bisnis;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Bisnis\TrSoReq;
use Illuminate\Http\Request;
use App\Models\Bisnis\TrSo;
use App\Models\User;
use Carbon\Carbon;
use DB;

class TrSoController extends BaseController
{
    public function index() {
        try {
            $query = TrSo::get();

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

    public function store(Request $req) {
        // ID-Cabang - Jenis_PIC - ID PIC - Bulan - Tahun - NO. Urut

        $user_id = $req->auth->user_id;
        $User = User::where('user_id', $user_id)->first();

        $kode_cabang = $User->kd_cabang;
        $nama_marketing = $User->nama;

        $now   = Carbon::now();
        $year  = $now->year;
        $month = $now->month;

        $nomor_so = $kode_cabang.'-'.$month.'-'.$year;

        $data = array(
            'nomor_so'       => $nomor_so,
            'user_id'        => $user_id,
            'kode_kantor'    => $kode_cabang,
            'id_asal_data'   => $req->input('id_asal_data'),
            'nama_marketing' => $nama_marketing,
            'plafon'         => $req->input('plafon'),
            'tenor'          => $req->input('tenor')
        );

        TrSo::create($data);

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
            $query = TrSo::where('id', $id)->first();

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
        $check = TrSo::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Tidak Ada!!'
            ], 404);
        }

        $data = array(
            'nomor_so'       => empty($req->input('nomor_so')) ? $check->nomor_so : $req->input('nomor_so'),
            'user_id'        => empty($req->input('user_id')) ? $check->user_id : $req->input('user_id'),
            'kode_kantor'    => empty($req->input('kode_kantor')) ? $check->kode_kantor : $req->input('kode_kantor'),
            'id_asal_data'   => empty($req->input('id_asal_data')) ? $check->id_asal_data : $req->input('id_asal_data'),
            'nama_marketing' => empty($req->input('nama_marketing')) ? $check->nama_marketing : $req->input('nama_marketing'),
            'plafon'         => empty($req->input('plafon')) ? $check->plafon : $req->input('plafon'),
            'tenor'          => empty($req->input('tenor')) ? $check->tenor : $req->input('tenor')
        );

        TrSo::where('id', $id)->update($data);

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
            $check = TrSo::where('id', $id)->first();

            if (!$check) {
                return response()->json([
                    'code'    => 404,
                    'status'  => 'not found',
                    'message' => 'Data Tidak Ada!!'
                ], 404);
            }

            TrSo::where('id', $id)->delete();

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
