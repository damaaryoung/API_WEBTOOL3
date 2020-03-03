<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class CodeController extends BaseController
{
    public function ao() {
        $query = DB::connection('dpm')->table('kre_kode_group2')
            ->select(
                'kode_group2 as kode',
                'user_id',
                'deskripsi_group2 as nama',
                'kode_kantor',
                'TARGET_BAKI_DEBET',
                'TARGET_NPL',
                'TARGET_REALISASI',
                'kode_referal',
                'flg_aktif'
            )->get();

        return response()->json([
            "code"   => 200,
            "status" => "success",
            "count"  => $query->count(),
            "data"   => $query
        ]);
    }

    public function so() {
        $query = DB::connection('dpm')->table('kre_kode_so')
            ->select(
                'id as kode',
                'kode_kantor',
                'nik',
                'nama_so',
                'flg_aktif',
            )->get();

        return response()->json([
            "code"   => 200,
            "status" => "success",
            "count"  => $query->count(),
            "data"   => $query
        ]);
    }

    public function col() {
        $query = DB::connection('dpm')->table('kre_kode_group3')
            ->select(
                'kode_group3 as kode',
                'user_id',
                'kode_kantor',
                'deskripsi_group3 as nama',
                'jabatan',
                'TARGET_BAKI_DEBET',
                'TARGET_NPL',
                'TARGET_REALISASI',
                'flg_aktif'
            )->get();

        return response()->json([
            "code"   => 200,
            "status" => "success",
            "count"  => $query->count(),
            "data"   => $query
        ]);
    }

    public function mb() {
        $query = DB::connection('dpm')->table('kre_kode_group5')
            ->select(
                'kode_group5 as kode',
                'kode_kantor',
                'deskripsi_group5 as nama',
                'no_id_group5 as ktp',
                'flg_aktif',
            )->get();

        return response()->json([
            "code"   => 200,
            "status" => "success",
            "count"  => $query->count(),
            "data"   => $query
        ]);
    }

    public function ca() {
        $query = DB::connection('dpm')->table('kre_kode_group6')
            ->select(
                'kode_group6 as kode',
                'kode_kantor',
                'deskripsi_group6 as nama',
                'flg_aktif'
            )->get();

        return response()->json([
            "code"   => 200,
            "status" => "success",
            "count"  => $query->count(),
            "data"   => $query
        ]);
    }



    // Mulai Data berdasarka UserName
    public function ao_user($username) {
        $query = DB::connection('dpm')->table('kre_kode_group2')
            ->select(
                'kode_group2 as kode',
                'user_id',
                'deskripsi_group2 as nama',
                'kode_kantor',
                'TARGET_BAKI_DEBET',
                'TARGET_NPL',
                'TARGET_REALISASI',
                'kode_referal',
                'flg_aktif'
            )->where('deskripsi_group2', 'like', '%'.$username.'%')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan username '".$username."' tidak ditemukan"
            ]);
        }else{
            return response()->json([
                "code"   => 200,
                "status" => "success",
                "count"  => $query->count(),
                "data"   => $query
            ]);
        }
    }

    public function so_user($username) {
        $query = DB::connection('dpm')->table('kre_kode_so')
            ->select(
                'id as kode',
                'kode_kantor',
                'nik',
                'nama_so',
                'flg_aktif',
            )->where('nama_so', 'like', '%'.$username.'%')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan username '".$username."' tidak ditemukan"
            ]);
        }else{
            return response()->json([
                "code"   => 200,
                "status" => "success",
                "count"  => $query->count(),
                "data"   => $query
            ]);
        }
    }

    public function col_user($username) {
        $query = DB::connection('dpm')->table('kre_kode_group3')
            ->select(
                'kode_group3 as kode',
                'user_id',
                'kode_kantor',
                'deskripsi_group3 as nama',
                'jabatan',
                'TARGET_BAKI_DEBET',
                'TARGET_NPL',
                'TARGET_REALISASI',
                'flg_aktif'
            )->where('deskripsi_group3', 'like', '%'.$username.'%')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan username '".$username."' tidak ditemukan"
            ]);
        }else{
            return response()->json([
                "code"   => 200,
                "status" => "success",
                "count"  => $query->count(),
                "data"   => $query
            ]);
        }
    }

    public function mb_user($username) {
        $query = DB::connection('dpm')->table('kre_kode_group5')
            ->select(
                'kode_group5 as kode',
                'kode_kantor',
                'deskripsi_group5 as nama',
                'no_id_group5 as ktp',
                'flg_aktif',
            )->where('deskripsi_group5', 'like', '%'.$username.'%')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan username '".$username."' tidak ditemukan"
            ]);
        }else{
            return response()->json([
                "code"   => 200,
                "status" => "success",
                "count"  => $query->count(),
                "data"   => $query
            ]);
        }
    }

    public function ca_user($username) {
        $query = DB::connection('dpm')->table('kre_kode_group6')
            ->select(
                'kode_group6 as kode',
                'kode_kantor',
                'deskripsi_group6 as nama',
                'flg_aktif'
            )->where('deskripsi_group6', 'like', '%'.$username.'%')->get();

        if ($query == '[]') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data dengan username '".$username."' tidak ditemukan"
            ]);
        }else{
            return response()->json([
                "code"   => 200,
                "status" => "success",
                "count"  => $query->count(),
                "data"   => $query
            ]);
        }
    }
    // Akhir Data berdasarka UserName

    // Produk CA
    public function produk(){
        $query = DB::connection('web')->select("SELECT kode_produk, `DESKRIPSI_PRODUK` AS nama_produk FROM view_produk");

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                "count"  => $query->count(),
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
