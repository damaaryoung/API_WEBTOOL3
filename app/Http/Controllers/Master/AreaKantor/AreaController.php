<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\AreaRequest;
use App\Models\AreaKantor\Area;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class AreaController extends BaseController
{
    public function index() {
        // $query = Area::get();
        $query = DB::connection('web')->table('m_k_area')
            ->join('master_provinsi', 'master_provinsi.id', '=', 'm_k_area.id_provinsi')
            ->join('master_kabupaten', 'master_kabupaten.id', '=', 'm_k_area.id_kabupaten')
            ->select(
                'm_k_area.id as id_area',
                'm_k_area.nama as nama_area',
                'm_k_area.id_provinsi',
                'master_provinsi.nama as nama_provinsi',
                'm_k_area.id_kabupaten',
                'master_kabupaten.nama as nama_kabupaten',
                'm_k_area.jml_cabang',
                'm_k_area.flg_aktif',
                'm_k_area.created_at',
                'm_k_area.updated_at'
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

    public function store(AreaRequest $req) {
        $data = array(
            'nama'         => $req->input('nama'),
            'id_provinsi'  => $req->input('id_provinsi'),
            'id_kabupaten' => $req->input('id_kabupaten'),
            'flg_aktif'    => $req->input('flg_aktif')
        );

        Area::create($data);

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
        $check = Area::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }else{
            $query = DB::connection('web')->table('m_k_area')
            ->join('master_provinsi', 'master_provinsi.id', '=', 'm_k_area.id_provinsi')
            ->join('master_kabupaten', 'master_kabupaten.id', '=', 'm_k_area.id_kabupaten')
            ->select(
                'm_k_area.id as id_area',
                'm_k_area.nama as nama_area',
                'm_k_area.id_provinsi',
                'master_provinsi.nama as nama_provinsi',
                'm_k_area.id_kabupaten',
                'master_kabupaten.nama as nama_kabupaten',
                'm_k_area.jml_cabang',
                'm_k_area.flg_aktif',
                'm_k_area.created_at',
                'm_k_area.updated_at'
            )
            ->where('m_k_area.id', $id)
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

    public function update($id, AreaRequest $req) {
        $check = Area::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'nama'         => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'id_provinsi'  => empty($req->input('id_provinsi')) ? $check->id_provinsi : $req->input('id_provinsi'),
            'id_kabupaten' => empty($req->input('id_kabupaten')) ? $check->id_kabupaten : $req->input('id_kabupaten'),
            'flg_aktif'    => empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif')
        );

        Area::where('id', $id)->update($data);

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
        $check = Area::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        Area::where('id', $id)->delete();

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
