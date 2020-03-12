<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\AreaKantor\CabangRequest;
use App\Models\AreaKantor\Cabang;
use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use App\Models\Wilayah\Kecamatan;
use App\Models\Wilayah\Kelurahan;
// use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Cache;
use DB;

class CabangController extends BaseController
{
    public function __construct() {
        $this->time_cache = config('app.cache_exp');
    }

    public function index() 
    {
        $data = array();

        // $query = Cache::remember('cabang.index', $this->time_cache, function () use ($data) {

            Cabang::select('id', 'nama as nama_cabang', 'jenis_kantor')
                ->addSelect([
                    'nama_provinsi'  => Provinsi::select('nama')->whereColumn('id_provinsi', 'master_provinsi.id'),
                    'nama_kabupaten' => Kabupaten::select('nama')->whereColumn('id_kabupaten', 'master_kabupaten.id'),
                    'nama_kecamatan' => Kecamatan::select('nama')->whereColumn('id_kecamatan', 'master_kecamatan.id'),
                    'nama_kelurahan' => Kelurahan::select('nama')->whereColumn('id_kelurahan', 'master_kelurahan.id')
                ])
                ->where('flg_aktif', 1)
                ->orderBy('nama', 'asc')
                ->chunk(50, function($chunks) use (&$data) {
                    foreach($chunks as $chunk) {
                        $data[] = $chunk;
                    }
                });

            // return $data;
        // });

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function store(CabangRequest $req) 
    {
        $data = array(
            'id_area'      => $req->input('id_mk_area'),
            'nama'         => $req->input('nama'),
            'id_provinsi'  => $req->input('id_provinsi'),
            'id_kabupaten' => $req->input('id_kabupaten'),
            'id_kecamatan' => $req->input('id_kecamatan'),
            'id_kelurahan' => $req->input('id_kelurahan'),
            'jenis_kantor' => $req->input('jenis_kantor'),
            'iks'          => $req->input('iks')
        );

        Cabang::create($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dibuat',
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id) 
    {
        $check = Cabang::where('id', $id)->first();

        if (empty($check)) {
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
            "flg_aktif"      => $check->flg_aktif,
            "created_at"     => $check->created_at,
            "iks"            => $check->iks
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $res
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function update($id, CabangRequest $req) 
    {
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
            'flg_aktif'      => empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1),
            'iks'            => empty($req->input('iks')) ? $check->iks : $req->input('iks')
        );

        Cabang::where('id', $id)->update($data);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil diupdate',
                'data'    => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function delete($id) 
    {
        Cabang::where('id', $id)->update(['flg_aktif' => 0]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan id '.$id.' berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function trash() 
    {
        $data = array();

        Cabang::select('id', 'nama as nama_cabang', 'jenis_kantor')
        ->addSelect([
            'nama_provinsi'  => Provinsi::select('nama')->whereColumn('id_provinsi', 'master_provinsi.id'),
            'nama_kabupaten' => Kabupaten::select('nama')->whereColumn('id_kabupaten', 'master_kabupaten.id'),
            'nama_kecamatan' => Kecamatan::select('nama')->whereColumn('id_kecamatan', 'master_kecamatan.id'),
            'nama_kelurahan' => Kelurahan::select('nama')->whereColumn('id_kelurahan', 'master_kelurahan.id')
        ])
        ->where('flg_aktif', 0)
        ->orderBy('nama', 'asc')
        ->chunk(50, function($chunks) use (&$data) {
            foreach($chunks as $chunk) {
                $data[] = $chunk;
            }
        });

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function restore($id) 
    {
        Cabang::where('id', $id)->update(['flg_aktif' => 1]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'data berhasil dikembalikan'
            ], 200);
        } catch (\Exception $e) {
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
            $result = $res->get();
        }else{
            $result = $res->limit($limit)->get();
        }

        if (empty($result)) {
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
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }
}
