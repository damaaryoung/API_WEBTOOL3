<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\AreaKantor\PICRequest;
use App\Models\AreaKantor\PIC;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\Area;
use App\Models\AreaKantor\Cabang;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Cache;
// use DB;

class PICController extends BaseController
{
    public function __construct() {
        $this->time_cache = config('app.cache_exp');
        $this->chunk      = 50;
    }

    public function index() 
    {
        $data = array();

        // $query = Cache::remember('pic.index', $this->time_cache, function () use ($data) {
            
            PIC::select('id', 'nama', 'email', 'user_id', 'id_area', 'id_cabang', 'plafon_caa as plafon_max')
            ->addSelect([
                'jenis_pic'   => JPIC::select('nama_jenis')->whereColumn('id_mj_pic', 'mj_pic.id'),
                'nama_area'   => Area::select('nama')->whereColumn('id_area', 'mk_area.id'),
                'nama_cabang' => Cabang::select('nama')->whereColumn('id_cabang', 'mk_cabang.id'),
            ])
            ->where('flg_aktif', 1)
            ->orderBy('nama', 'asc')
            ->chunk($this->chunk, function($chunks) use (&$data) 
            {
               foreach($chunks as $chunk)
               {
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
                'count'  => count($data),
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

    public function store(PICRequest $req)
    {
        $data = array(
            'user_id'       => $req->input('user_id'),
            'id_area'       => $req->input('id_mk_area'),
            'id_cabang'     => $req->input('id_mk_cabang'),
            'id_mj_pic'     => $req->input('id_mj_pic'),
            'nama'          => $req->input('nama'),
            'email'         => $req->input('email')
        );

        PIC::create($data);

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
        $query = PIC::select(
            "id","nama","email","user_id","id_mj_pic as id_jenis_pic","id_area","id_cabang","plafon_caa as plafon_max","flg_aktif", "created_at"
        )->addSelect([
            'nama_jenis_pic' => JPIC::select('nama_jenis')->whereColumn('id_mj_pic', 'mj_pic.id'),
            'nama_area'      => Area::select('nama')->whereColumn('id_area', 'mk_area.id'),
            'nama_cabang'    => Cabang::select('nama')->whereColumn('id_cabang', 'mk_cabang.id'),
        ])->where('id', $id)->first();

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }
    
        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function update($id, PICRequest $req) 
    {
        $check = PIC::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'nama'         => empty($req->input('nama')) ? $check->nama : $req->input('nama'),
            'email'        => empty($req->input('email')) ? $check->email : $req->input('email'),
            'user_id'      => empty($req->input('user_id')) ? $check->user_id : $req->input('user_id'),
            'id_area'      => empty($req->input('id_mk_area')) ? $check->id_area : $req->input('id_mk_area'),
            'id_cabang'    => empty($req->input('id_mk_cabang')) ? $check->id_cabang : $req->input('id_mk_cabang'),
            'id_mj_pic'    => empty($req->input('id_mj_pic')) ? $check->id_mj_pic : $req->input('id_mj_pic'),
            'flg_aktif'    => empty($req->input('flg_aktif')) ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1)
        );

        PIC::where('id', $id)->update($data);

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
        PIC::where('id', $id)->update(['flg_aktif' => 0]);

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

        PIC::select('id', 'nama', 'email', 'user_id', 'id_area', 'id_cabang', 'plafon_caa as plafon_max')
        ->addSelect([
            'jenis_pic'   => JPIC::select('nama_jenis')->whereColumn('id_mj_pic', 'mj_pic.id'),
            'nama_area'   => Area::select('nama')->whereColumn('id_area', 'mk_area.id'),
            'nama_cabang' => Cabang::select('nama')->whereColumn('id_cabang', 'mk_cabang.id'),
        ])
        ->where('flg_aktif', 1)
        ->orderBy('nama', 'asc')
        ->chunk($this->chunk, function($chunks) use (&$data) 
        {
            foreach($chunks as $chunk)
            {
                $data[] = $chunk;
            }
        });

        if (empty($data)){
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
        PIC::where('id', $id)->update(['flg_aktif' => 1]);

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
        $column = array(
            'id', 'user_id', 'id_area', 'id_cabang', 'id_mj_pic', 'nama', 'email'
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

        $query = PIC::with('jpic','area','cabang')
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);

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

        foreach ($result as $key => $val) {
            $data[$key]= [
                "id"          => $val->id,
                "nama"        => $val->nama,
                "email"       => $val->email,
                "jenis_pic"   => $val->jpic['nama_jenis'],
                "nama_area"   => $val->area['nama'],
                "nama_cabang" => $val->cabang['nama']
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
