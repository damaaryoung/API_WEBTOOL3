<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\AreaPICReq;
use App\Models\AreaKantor\AreaPIC;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class AreaPICController extends BaseController
{
    public function index() {
        $query = AreaPIC::get();

        foreach ($query as $key => $val) {
            $res[$key] = [
                'id'                => $val->id,
                "nama_area_kerja"   => $val->area['nama'],
                "nama_kantor_cabang"=> $val->cabang['nama'],
                "nama_area_pic"     => $val->nama_area_pic,
                "nama_kelurahan"    => $val->kel['nama'],
                "kode_pos"          => $val->kel['kode_pos']
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(AreaPICReq $req) {
        $data = array(
            'id_mk_area'    => $req->input('id_area_kerja'),
            'id_mk_cabang'  => $req->input('id_area_cabang'),
            'nama_area_pic' => $req->input('nama_area_pic'),
            'id_provinsi'   => $req->input('id_prov'),
            'id_kabupaten'  => $req->input('id_kab'),
            'id_kecamatan'  => $req->input('id_kec'),
            'id_kelurahan'  => $req->input('id_kel')
        );

        AreaPIC::create($data);

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
        $query = AreaPIC::where('id', $id)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {
            $res[$key] = [
                'id'                => $val->id,
                "nama_area_pic"     => $val->nama_area_pic,
                'id_area_kerja'     => $val->id_mk_area,
                "nama_area_kerja"   => $val->area['nama'],
                "id_mk_cabang"      => $val->id_mk_cabang,
                "nama_kantor_cabang"=> $val->cabang['nama'],
                "id_provinsi"       => $val->id_provinsi,
                "nama_provinsi"     => $val->prov['nama'],
                "id_kabupaten"      => $val->id_kabupaten,
                "nama_kabupaten"    => $val->kab['nama'],
                "id_kecamatan"      => $val->id_kecamatan,
                "nama_kec"          => $val->kec['nama'],
                "id_kelurahan"      => $val->id_kelurahan,
                "nama_kelurahan"    => $val->kel['nama'],
                "kode_pos"          => $val->kel['kode_pos'],
                "flg_aktif"         => $val->flg_aktif,
                "created_at"        => date($val->created_at)
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res[0]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, AreaPICReq $req) {
        $check = AreaPIC::where('id', $id)->first();
        $data = array(
            'id_mk_area'    => empty($req->input('id_area_kerja')) ? $check->id_area_kerja : $req->input('id_area_kerja'),
            'id_mk_cabang'  => empty($req->input('id_area_cabang')) ? $check->id_area_cabang : $req->input('id_area_cabang'),
            'nama_area_pic' => empty($req->input('nama_area_pic')) ? $check->nama_area_pic : $req->input('nama_area_pic'),
            'id_provinsi'   => empty($req->input('id_prov')) ? $check->id_prov : $req->input('id_prov'),
            'id_kabupaten'  => empty($req->input('id_kab')) ? $check->id_kab : $req->input('id_kab'),
            'id_kecamatan'  => empty($req->input('id_kec')) ? $check->id_kec : $req->input('id_kec'),
            'id_kelurahan'  => empty($req->input('id_kel')) ? $check->id_kel : $req->input('id_kel'),
            'flg_aktif'     => empty($req->input('flg_aktif')) ? $check->flg_aktif : $req->input('flg_aktif')
        );

        AreaPIC::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diubah'
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
        $check = AreaPIC::where('id', $id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        AreaPIC::where('id', $id)->delete();

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
