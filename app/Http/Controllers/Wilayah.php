<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class Wilayah extends BaseController
{
    public function provinsi() {
        try {
            $query = DB::connection('web')->table('master_provinsi')->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    // public function create_provinsi(Request $req) {
    //     try {
    //         $query = DB::connection('web')->table('master_provinsi')->insert([
    //             'nama'      => $req->input('nama'),
    //             'flg_aktif' => $req->input('flg_aktif')
    //         ]);

    //         return response()->json([
    //             'code'    => 200,
    //             'status'  => 'success',
    //             'message' => 'Data has been created'
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "code"    => 400,
    //             "status"  => "error",
    //             "message" => "something error!"
    //         ], 400);
    //     }
    // }

    public function kabupaten() {
        try {
            $query = DB::connection('web')->table('master_kabupaten')->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    // public function create_kabupaten(Request $req) {
    //     try {
    //         $query = DB::connection('web')->table('master_kabupaten')->insert([
    //             'nama'        => $req->input('nama'),
    //             'id_provinsi' => $req->input('id_provinsi'),
    //             'flg_aktif'   => $req->input('flg_aktif')
    //         ]);

    //         return response()->json([
    //             'code'    => 200,
    //             'status'  => 'success',
    //             'message' => 'Data has been created'
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "code"    => 400,
    //             "status"  => "error",
    //             "message" => "something error!"
    //         ], 400);
    //     }
    // }

    public function kecamatan() {
        try {
            $query = DB::connection('web')->table('master_kecamatan')->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    // public function created_kecamatan(Request $req) {
    //     try {
    //         $query = DB::connection('web')->table('master_kecamatan')->insert([
    //             'nama'         => $req->input('nama'),
    //             'id_kabupaten' => $req->input('id_kabupaten'),
    //             'flg_aktif'    => $req->input('flg_aktif')
    //         ]);

    //         return response()->json([
    //             'code'    => 200,
    //             'status'  => 'success',
    //             'message' => 'Data has been created'
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "code"    => 400,
    //             "status"  => "error",
    //             "message" => "something error!"
    //         ], 400);
    //     }
    // }

    public function kelurahan() {
        try {
            $query = DB::connection('web')->table('master_kelurahan')->get();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 400,
                "status"  => "error",
                "message" => "something error!"
            ], 400);
        }
    }

    // public function create_kelurahan(Request $req) {
    //     try {
    //         $query = DB::connection('web')->table('master_kelurahan')->insert([
    //             'nama'         => $req->input('nama'),
    //             'kode_pos'     => $req->input('kode_pos'),
    //             'id_kecamatan' => $req->input('id_kecamatan'),
    //             'flg_aktif'    => $req->input('flg_aktif')
    //         ]);

    //         return response()->json([
    //             'code'    => 200,
    //             'status'  => 'success',
    //             'message' => 'Data has been created'
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             "code"    => 400,
    //             "status"  => "error",
    //             "message" => "something error!"
    //         ], 400);
    //     }
    // }
}
