<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\AreaKantor\CabangRequest;
use App\Models\AreaKantor\Cabang;
// use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
// use DB;

class CabangController extends BaseController
{
    public function index() {
        $query = Cabang::where('flg_aktif', 1)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $key => $val) {
            $data[$key] = [
                "id"             => $val->id,
                "nama_area"      => $val->area['nama'],
                "nama_cabang"    => $val->nama,
                "nama_provinsi"  => $val->prov['nama'],
                "nama_kabupaten" => $val->kab['nama'],
                "nama_kecamatan" => $val->kec['nama'],
                "nama_kelurahan" => $val->kel['nama'],
                "jenis_kantor"   => $val->jenis_kantor
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
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

    public function store(CabangRequest $req) {
        $data = array(
            'id_area'      => $req->input('id_mk_area'),
            'nama'         => $req->input('nama'),
            'id_provinsi'  => $req->input('id_provinsi'),
            'id_kabupaten' => $req->input('id_kabupaten'),
            'id_kecamatan' => $req->input('id_kecamatan'),
            'id_kelurahan' => $req->input('id_kelurahan'),
            'jenis_kantor' => $req->input('jenis_kantor')
        );

        Cabang::create($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dibuat',
                'data'    => $data
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
        }

        $res = array(
            "id"             => $check->id,
            "nama_cabang"    => $check->nama,
            "id_area"        => $check->id_area,
            "nama_area"      => $check->area['nama'],
            "id_provinsi"    => $check->id_provinsi,
            "nama_provinsi"  => $check->prov['nama'],
            "id_kabupaten"   => $check->id_kabupaten,
            "nama_kabupaten" => $check->kab['nama'],
            "id_kecamatan"   => $check->id_kecamatan,
            "nama_kecamatan" => $check->kec['nama'],
            "id_kelurahan"   => $check->id_kelurahan,
            "nama_kelurahan" => $check->kel['nama'],
            "kode_pos"       => $check->kel['kode_pos'],
            "jenis_kantor"   => $check->jenis_kantor,
            "flg_aktif"      => (bool) $check->flg_aktif,
            "created_at"     => Carbon::parse($check->created_at)->format('d-m-Y H:i:s')
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
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
            'id_area'        => empty($req->input('id_mk_area')) ? $check->id_area : $req->input('id_mk_area'),
            'nama'           => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'id_provinsi'    => empty($req->input('id_provinsi')) ? $check->id_provinsi : $req->input('id_provinsi'),
            'id_kabupaten'   => empty($req->input('id_kabupaten')) ? $check->id_kabupaten : $req->input('id_kabupaten'),
            'id_kecamatan'   => empty($req->input('id_kecamatan')) ? $check->id_kecamatan : $req->input('id_kecamatan'),
            'id_kelurahan'   => empty($req->input('id_kelurahan')) ? $check->id_kelurahan : $req->input('id_kelurahan'),
            'jenis_kantor'   => empty($req->input('jenis_kantor')) ? $check->jenis_kantor : $req->input('jenis_kantor'),
            'flg_aktif'      => empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1)
        );

        Cabang::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate',
                'data'    => $data
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

        $delCab = Cabang::where('id', $id)->update(['flg_aktif' => 0]);

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

    public function trash() {
        $query = Cabang::where('flg_aktif', 0)->orderBy('nama', 'asc')->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $res = array();
        foreach ($query as $key => $val) {
            $res[$key] = [
                "id"             => $val->id,
                "nama_area"      => $val->area['nama'],
                "nama_cabang"    => $val->nama,
                "nama_provinsi"  => $val->prov['nama'],
                "nama_kabupaten" => $val->kab['nama'],
                "nama_kecamatan" => $val->kec['nama'],
                "nama_kelurahan" => $val->kel['nama'],
                "jenis_kantor"   => $val->jenis_kantor
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
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

    public function restore($id) {
        $query = Cabang::where('id', $id)->update(['flg_aktif' => 1]);

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

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit)
    {
        $column = array('id', 'id_area', 'nama', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'jenis_kantor');

        if($param != 'filter' && $param != 'search'){
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if($param == 'search'){
            $operator   = "like";
            $func_value = "%{$value}%";
        }else{
            $operator   = "=";
            $func_value = "{$value}";
        }

        $query = Cabang::where('flg_aktif', $status)->orderBy($orderBy, $orderVal);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($key, $operator, $func_value);
        }

        if($limit == 'default'){
            $result = $res;
        }else{
            $result = $res->limit($limit);
        }

        if ($result->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($result->get() as $key => $val) {
            $data[$key] = [
                "id"             => $val->id,
                "nama_area"      => $val->area['nama'],
                "nama_cabang"    => $val->nama,
                "nama_provinsi"  => $val->prov['nama'],
                "nama_kabupaten" => $val->kab['nama'],
                "nama_kecamatan" => $val->kec['nama'],
                "nama_kelurahan" => $val->kel['nama'],
                "jenis_kantor"   => $val->jenis_kantor
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
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
}
