<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\AreaKantor\AreaPICReq;
use App\Models\AreaKantor\AreaPIC;
use App\Models\AreaKantor\PIC;
use Carbon\Carbon;

class AreaPICController extends BaseController
{
    public function index() {
        $query = AreaPIC::with('area', 'cabang', 'kel')->where('flg_aktif', 1)->orderBy('nama_area_pic', 'asc')->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $res = array();
        foreach ($query as $key => $val) {
            $pic = PIC::whereIn('id', explode(",", $val->id_pic))->select('id','nama', 'email', 'id_mj_pic')->get();

            $pics = array();
            foreach($pic as $i => $pi){
                $pics[$i]['id'] = $pi->id;
                $pics[$i]['nama'] = $pi->nama;
                $pics[$i]['email'] = $pi->email;
                $pics[$i]['jabatan'] = $pi->jpic['nama_jenis'];
            }

            $data[$key] = [
                'id'                => $val->id,
                "nama_area_pic"     => $val->nama_area_pic,
                "nama_area_kerja"   => $val->area['nama'],
                "nama_kantor_cabang"=> $val->cabang['nama'],
                "nama_kelurahan"    => $val->kel['nama'],
                "kode_pos"          => $val->kel['kode_pos'],
                "pic"               => $pics
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

    public function store(AreaPICReq $req) {
        
        if(!empty($req->input('id_pic'))){
            for($i = 0; $i < count($req->input('id_pic')); $i++){
                $pic[] = empty($req->input('id_pic')[$i]) ? null : $req->input('id_pic')[$i];
            }
        }

        $data = array(
            'id_area'       => $req->input('id_mk_area'),
            'id_cabang'     => $req->input('id_mk_cabang'),
            'nama_area_pic' => $req->input('nama_area_pic'),
            'id_provinsi'   => $req->input('id_provinsi'),
            'id_kabupaten'  => $req->input('id_kabupaten'),
            'id_kecamatan'  => $req->input('id_kecamatan'),
            'id_kelurahan'  => $req->input('id_kelurahan'),
            'id_pic'        => implode(",", $pic)
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
        $val = AreaPIC::where('id', $id)->first();

        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $pic = PIC::whereIn('id', explode(",", $val->id_pic))->select('id','nama', 'email', 'id_mj_pic')->get();

        $pics = array();
        foreach($pic as $i => $pi){
            $pics[$i]['id'] = $pi->id;
            $pics[$i]['nama'] = $pi->nama;
            $pics[$i]['email'] = $pi->email;
            $pics[$i]['jabatan'] = $pi->jpic['nama_jenis'];
        }

        $res = array(
            'id'                => $val->id,
            "nama_area_pic"     => $val->nama_area_pic,
            'id_area_kerja'     => $val->id_area,
            "nama_area_kerja"   => $val->area['nama'],
            "id_mk_cabang"      => $val->id_cabang,
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
            "pic"               => $pics,
            "flg_aktif"         => (bool) $val->flg_aktif,
            "created_at"        => Carbon::parse($val->created_at)->format('d-m-Y H:i:s')
        );
        // }

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

    public function update($id, AreaPICReq $req) {
        $check = AreaPIC::where('id', $id)->first();

        $data = array(
            'id_area'       => empty($req->input('id_area_kerja'))  ? $check->id_area : $req->input('id_area_kerja'),
            'id_cabang'     => empty($req->input('id_area_cabang')) ? $check->id_area : $req->input('id_area_cabang'),
            'nama_area_pic' => empty($req->input('nama_area_pic'))  ? $check->nama_area_pic : $req->input('nama_area_pic'),
            'id_provinsi'   => empty($req->input('id_prov')) ? $check->id_prov : $req->input('id_prov'),
            'id_kabupaten'  => empty($req->input('id_kab')) ? $check->id_kab : $req->input('id_kab'),
            'id_kecamatan'  => empty($req->input('id_kec')) ? $check->id_kec : $req->input('id_kec'),
            'id_kelurahan'  => empty($req->input('id_kel')) ? $check->id_kel : $req->input('id_kel'),
            'flg_aktif'     => empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1)
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

        AreaPIC::where('id', $id)->update(['flg_aktif' => 0]);

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
        $query = AreaPIC::where('flg_aktif', 0)->orderBy('nama_area_pic', 'asc')->get();

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
                'id'                => $val->id,
                "nama_area_pic"     => $val->nama_area_pic,
                "nama_area_kerja"   => $val->area['nama'],
                "nama_kantor_cabang"=> $val->cabang['nama'],
                "nama_kelurahan"    => $val->kel['nama'],
                "kode_pos"          => $val->kel['kode_pos']
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
        $query = AreaPIC::where('id', $id)->update(['flg_aktif' => 1]);

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
        $column = array(
            'id', 'id_area', 'id_cabang', 'nama_area_pic', 'id_provinsi', 'id_kabupaten', 'id_kecamatan', 'id_kelurahan', 'id_pic'
        );

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

        $query = AreaPIC::where('flg_aktif', $status)->orderBy($orderBy, $orderVal);

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
                'id'                => $val->id,
                "nama_area_pic"     => $val->nama_area_pic,
                "nama_area_kerja"   => $val->area['nama'],
                "nama_kantor_cabang"=> $val->cabang['nama'],
                "nama_kelurahan"    => $val->kel['nama'],
                "kode_pos"          => $val->kel['kode_pos']
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
