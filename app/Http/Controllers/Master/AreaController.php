<?php

namespace App\Http\Controllers\Master;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class AreaController extends BaseController
{
    public function index() {
        try {
            $query = DB::connection('web')->table('master_area')
                ->join('master_kelurahan', 'master_area.id_kelurahan', '=', 'master_kelurahan.id')
                ->join('master_kecamatan', 'master_kelurahan.id_kecamatan', '=', 'master_kecamatan.id')
                ->join('master_kabupaten', 'master_kecamatan.id_kabupaten', '=', 'master_kabupaten.id')
                ->join('master_provinsi', 'master_kabupaten.id_provinsi', '=', 'master_provinsi.id')
                ->join('master_jenis', 'master_area.id_master_jenis', '=', 'master_jenis.id')
                ->select('master_area.id', 'master_jenis.nama_jenis as master_jenis', 'master_kelurahan.nama as kelurahan', 'master_kecamatan.nama as kecamatan', 'master_kabupaten.nama as kabupaten', 'master_provinsi.nama as provinsi', 'master_area.flg_aktif', 'master_area.kode')
                ->get();

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
        $id_kelurahan    = $req->input('id_kelurahan');
        $id_master_jenis = $req->input('id_master_jenis');
        $flg_aktif       = $req->input('flg_aktif');
        $kode            = $req->input('kode');

        if (!$id_kelurahan) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'id_kelurahan' harus diisi!"
            ], 400);
        }

        if (!$id_master_jenis) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'id_master_jenis' harus diisi !"
            ], 400);
        }

        if (!$flg_aktif) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'flg_aktif' harus diisi !"
            ], 400);
        }

        if (!$kode) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'kode' harus diisi !"
            ], 400);
        }

        if ($flg_aktif == 1 || $flg_aktif == 0) {
            try {
                $query = DB::connection('web')->table('master_area')->insert([
                    'id_kelurahan'    => $id_kelurahan,
                    'id_master_jenis' => $id_master_jenis,
                    'flg_aktif'       => $flg_aktif,
                    'kode'            => $kode
                ]);

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
        }else{
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Nilai Field 'flg_aktif' yang benar adalah 1 atau 0"
            ], 400);
        }
    }

    public function show($id) {
        try {
            $query = DB::connection('web')->table('master_area')
                ->join('master_kelurahan', 'master_area.id_kelurahan', '=', 'master_kelurahan.id')
                ->join('master_kecamatan', 'master_kelurahan.id_kecamatan', '=', 'master_kecamatan.id')
                ->join('master_kabupaten', 'master_kecamatan.id_kabupaten', '=', 'master_kabupaten.id')
                ->join('master_provinsi', 'master_kabupaten.id_provinsi', '=', 'master_provinsi.id')
                ->join('master_jenis', 'master_area.id_master_jenis', '=', 'master_jenis.id')
                ->select('master_area.id', 'master_jenis.nama_jenis as master_jenis', 'master_kelurahan.nama as kelurahan', 'master_kecamatan.nama as kecamatan', 'master_kabupaten.nama as kabupaten', 'master_provinsi.nama as provinsi', 'master_area.flg_aktif', 'master_area.kode')
                ->where('master_area.id', $id)
                ->first();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 400,
                'status' => 'error',
                'data'   => $e
            ], 400);
        }
    }

    public function update($id, Request $req) {
        $check = DB::connection('web')->table('master_area')->where('id', $id)->first();

        $id_kelurahan    = empty($req->input('id_kelurahan')) ? $check->id_kelurahan : $req->input('id_kelurahan');
        $id_master_jenis = empty($req->input('id_master_jenis')) ? $check->id_master_jenis : $req->input('id_master_jenis');
        $flg_aktif       = empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif');
        $kode            = empty($req->input('kode')) ? $check->kode : $req->input('kode');

        if ($flg_aktif == 1 || $flg_aktif == 0) {
            try {
                $query = DB::connection('web')->table('master_area')->where('id', $id)->update([
                    'id_kelurahan'    => $id_kelurahan,
                    'id_master_jenis' => $id_master_jenis,
                    'flg_aktif'       => $flg_aktif,
                    'kode'            => $kode
                ]);

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
        }else{
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Nilai Field 'flg_aktif' yang benar adalah 1 atau 0"
            ], 400);
        }
    }

    public function delete($id) {
        $check = DB::connection('web')->table('master_area')->where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        try {
            DB::connection('web')->table('master_area')->where('id', $id)->delete();

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
