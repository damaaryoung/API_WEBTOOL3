<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\CabangRequest;
use App\Models\AreaKantor\Cabang;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class CabangController extends BaseController
{
    public function index() {
        $query = DB::connection('web')->table('mk_cabang')
            ->join('mk_area', 'mk_area.id', '=', 'mk_cabang.id_mk_area')
            ->join('master_provinsi', 'master_provinsi.id', '=', 'mk_cabang.id_provinsi')
            ->join('master_kabupaten', 'master_kabupaten.id', '=', 'mk_cabang.id_kabupaten')
            ->join('master_kecamatan', 'master_kecamatan.id', '=', 'mk_cabang.id_kecamatan')
            ->join('master_kelurahan', 'master_kelurahan.id', '=', 'mk_cabang.id_kelurahan')
            ->select(
                'mk_cabang.id_mk_area as id_area',
                'mk_area.nama as nama_area',
                'mk_cabang.id as id_cabang',
                'mk_cabang.nama as nama_cabang',
                'mk_cabang.id_provinsi',
                'master_provinsi.nama as nama_provinsi',
                'mk_cabang.id_kabupaten',
                'master_kabupaten.nama as nama_kabupaten',
                'mk_cabang.id_kecamatan',
                'master_kecamatan.nama as nama_kecamatan',
                'mk_cabang.id_kelurahan',
                'master_kelurahan.nama as nama_kelurahan',
                'master_kelurahan.kode_pos as kode_pos',
                'mk_cabang.jenis_kantor',
                'mk_cabang.flg_aktif',
                'mk_cabang.created_at',
                'mk_cabang.updated_at'
            )
            ->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        if(count($query) > 1){
            $result = $query;
        }else{
            $result = $query[0];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $result
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(CabangRequest $req) {
        $data = array(
            'id_mk_area'   => $req->input('id_mk_area'),
            'nama'          => $req->input('nama'),
            'id_provinsi'   => $req->input('id_provinsi'),
            'id_kabupaten'  => $req->input('id_kabupaten'),
            'id_kecamatan'  => $req->input('id_kecamatan'),
            'id_kelurahan'  => $req->input('id_kelurahan'),
            'jenis_kantor'  => $req->input('jenis_kantor'),
            'flg_aktif'     => $req->input('flg_aktif')
        );

        $store = Cabang::create($data);

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
        $check = Cabang::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }else{
            $query = DB::connection('web')->table('mk_cabang')
            ->join('mk_area', 'mk_area.id', '=', 'mk_cabang.id_mk_area')
            ->join('master_provinsi', 'master_provinsi.id', '=', 'mk_cabang.id_provinsi')
            ->join('master_kabupaten', 'master_kabupaten.id', '=', 'mk_cabang.id_kabupaten')
            ->join('master_kecamatan', 'master_kecamatan.id', '=', 'mk_cabang.id_kecamatan')
            ->join('master_kelurahan', 'master_kelurahan.id', '=', 'mk_cabang.id_kelurahan')
            ->select(
                'mk_cabang.id_mk_area as id_area',
                'mk_area.nama as nama_area',
                'mk_cabang.id as id_cabang',
                'mk_cabang.nama as nama_cabang',
                'mk_cabang.id_provinsi',
                'master_provinsi.nama as nama_provinsi',
                'mk_cabang.id_kabupaten',
                'master_kabupaten.nama as nama_kabupaten',
                'mk_cabang.id_kecamatan',
                'master_kecamatan.nama as nama_kecamatan',
                'mk_cabang.id_kelurahan',
                'master_kelurahan.nama as nama_kelurahan',
                'master_kelurahan.kode_pos as kode_pos',
                'mk_cabang.jenis_kantor',
                'mk_cabang.flg_aktif',
                'mk_cabang.created_at',
                'mk_cabang.updated_at'
            )
            ->where('mk_cabang.id', $id)
            ->first();

            try {
                return response()->json([
                    'code'   => 200,
                    'status' => 'success',
                    'data'   => $query
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

    public function update($id, CabangRequest $req) {
        $check = Cabang::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'id_mk_area'     => empty($req->input('id_master_area')) ? $check->id_master_area : $req->input('id_master_area'),
            'nama'           => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'id_provinsi'    => empty($req->input('id_provinsi')) ? $check->id_provinsi : $req->input('id_provinsi'),
            'id_kabupaten'   => empty($req->input('id_kabupaten')) ? $check->id_kabupaten : $req->input('id_kabupaten'),
            'id_kecamatan'   => empty($req->input('id_kecamatan')) ? $check->id_kecamatan : $req->input('id_kecamatan'),
            'id_kelurahan'   => empty($req->input('id_kelurahan')) ? $check->id_kelurahan : $req->input('id_kelurahan'),
            'jenis_kantor'   => empty($req->input('jenis_kantor')) ? $check->jenis_kantor : $req->input('jenis_kantor'),
            'flg_aktif'      => empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif')
        );

        Cabang::where('id', $id)->update($data);

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
        $check = Cabang::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $delCab = Cabang::where('id', $id)->delete();

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
