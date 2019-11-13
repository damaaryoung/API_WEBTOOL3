<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\KasRequest;
use App\Models\AreaKantor\Kas;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class KasController extends BaseController
{
    public function index() {
        // $query = Kas::get();
        $query = DB::connection('web')->table('m_k_kas')
            ->join('m_k_area', 'm_k_area.id',                 '=', 'm_k_kas.id_m_k_area')
            ->join('m_k_cabang', 'm_k_cabang.id',             '=', 'm_k_kas.id_m_k_cabang')
            ->join('master_provinsi', 'master_provinsi.id',   '=', 'm_k_kas.id_provinsi')
            ->join('master_kabupaten', 'master_kabupaten.id', '=', 'm_k_kas.id_kabupaten')
            ->join('master_kecamatan', 'master_kecamatan.id', '=', 'm_k_kas.id_kecamatan')
            ->join('master_kelurahan', 'master_kelurahan.id', '=', 'm_k_kas.id_kelurahan')
            ->select(
                'm_k_kas.id_m_k_area as id_area',
                'm_k_area.nama as nama_area',
                'm_k_kas.id_m_k_cabang as id_cabang',
                'm_k_cabang.nama as nama_cabang',
                'm_k_kas.id as id_kantor_kas',
                'm_k_kas.nama as nama_kantor_kas',
                'm_k_kas.id_provinsi',
                'master_provinsi.nama as nama_provinsi',
                'm_k_kas.id_kabupaten',
                'master_kabupaten.nama as nama_kabupaten',
                'm_k_kas.id_kecamatan',
                'master_kecamatan.nama as nama_kecamatan',
                'm_k_kas.id_kelurahan',
                'master_kelurahan.nama as nama_kelurahan',
                'master_kelurahan.kode_pos as kode_pos',
                'm_k_kas.flg_aktif',
                'm_k_kas.created_at',
                'm_k_kas.updated_at'
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

    public function store(KasRequest $req) {
        $data = array(
            'id_m_k_area'   => $req->input('id_m_k_area'),
            'id_m_k_kas' => $req->input('id_m_k_kas'),
            'nama'          => $req->input('nama'),
            'id_provinsi'   => $req->input('id_provinsi'),
            'id_kabupaten'  => $req->input('id_kabupaten'),
            'id_kecamatan'  => $req->input('id_kecamatan'),
            'id_kelurahan'  => $req->input('id_kelurahan'),
            'flg_aktif'     => $req->input('flg_aktif')
        );

        Kas::create($data);

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
        $check = Kas::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }else{
            $query = DB::connection('web')->table('m_k_kas')
            ->join('m_k_area', 'm_k_area.id',                 '=', 'm_k_kas.id_m_k_area')
            ->join('m_k_cabang', 'm_k_cabang.id',             '=', 'm_k_kas.id_m_k_cabang')
            ->join('master_provinsi', 'master_provinsi.id',   '=', 'm_k_kas.id_provinsi')
            ->join('master_kabupaten', 'master_kabupaten.id', '=', 'm_k_kas.id_kabupaten')
            ->join('master_kecamatan', 'master_kecamatan.id', '=', 'm_k_kas.id_kecamatan')
            ->join('master_kelurahan', 'master_kelurahan.id', '=', 'm_k_kas.id_kelurahan')
            ->select(
                'm_k_kas.id_m_k_area as id_area',
                'm_k_area.nama as nama_area',
                'm_k_kas.id_m_k_cabang as id_cabang',
                'm_k_cabang.nama as nama_cabang',
                'm_k_kas.id as id_kantor_kas',
                'm_k_kas.nama as nama_kantor_kas',
                'm_k_kas.id_provinsi',
                'master_provinsi.nama as nama_provinsi',
                'm_k_kas.id_kabupaten',
                'master_kabupaten.nama as nama_kabupaten',
                'm_k_kas.id_kecamatan',
                'master_kecamatan.nama as nama_kecamatan',
                'm_k_kas.id_kelurahan',
                'master_kelurahan.nama as nama_kelurahan',
                'master_kelurahan.kode_pos as kode_pos',
                'm_k_kas.flg_aktif',
                'm_k_kas.created_at',
                'm_k_kas.updated_at'
            )
            ->where('m_k_kas.id', $id)
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

    public function update($id, KasRequest $req) {
        $check = Kas::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'id_m_k_area'  => empty($req->input('id_m_k_area')) ? $check->id_m_k_area : $req->input('id_m_k_area'),
            'id_m_k_kas'   => empty($req->input('id_m_k_kas')) ? $check->id_m_k_kas : $req->input('id_m_k_kas'),
            'nama'         => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'id_provinsi'  => empty($req->input('id_provinsi')) ? $check->id_provinsi : $req->input('id_provinsi'),
            'id_kabupaten' => empty($req->input('id_kabupaten')) ? $check->id_kabupaten : $req->input('id_kabupaten'),
            'flg_aktif'    => empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif')
        );

        Kas::where('id', $id)->update($data);

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
        $check = Kas::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        Kas::where('id', $id)->delete();

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
