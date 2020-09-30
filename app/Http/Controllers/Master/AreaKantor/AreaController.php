<?php

namespace App\Http\Controllers\Master\AreaKantor;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\AreaKantor\AreaRequest;
use App\Models\AreaKantor\Area;
use App\Models\Wilayah\Provinsi;
use App\Models\Wilayah\Kabupaten;
use Carbon\Carbon;
use Cache;
use DB;

class AreaController extends BaseController
{
    // public function __construct()
    // {
    //     $this->time_cache = config('app.cache_exp');
    // }

    public function index()
    {
        // $data = array();

        //  $query = Cache::remember('area.index', $this->time_cache, function () use ($data) {
        // $data = Area::with(['prov:nama', 'kab:nama' => function ($query) {
        //     $query->select('nama', 'id_provinsi');
        // }])->SELECT('id', 'nama as nama_area')->get();
        // $data =  Area::join('vw_master_kabupaten', 'vw_master_kabupaten.id', '=', 'mk_area.id_kabupaten')->join('vw_master_provinsi', 'vw_master_provinsi.id', '=' . 'mk_area.id_kabupaten')->get(); 

        $data =  DB::connection('web')->select("SELECT mk_area.`id` AS id, mk_area.`nama` AS nama_area,vw_master_kabupaten.`nama` AS nama_kabupaten,vw_master_provinsi.`nama` AS nama_provinsi FROM mk_area JOIN vw_master_kabupaten ON (mk_area.`id_kabupaten`=vw_master_kabupaten.`id`) JOIN vw_master_provinsi ON (mk_area.`id_provinsi`=vw_master_provinsi.`id`)");
        // Detailtaskdraft::join('taskdraft', 'taskdraft.taskcode', '=', 'detail_taskbulkdraft.taskcode')->join('task_collect', 'detail_taskbulkdraft.taskcode', '=', 'task_collect.taskcode')->join('collectresult', 'task_collect.id', '=', 'collectresult.taskid')->where('collectresult.code', 'KUNJUNGAN')->whereMonth('taskdraft.assigndate', Carbon::now())->whereYear('taskdraft.assigndate', Carbon::now())->sum('angstung');
        // ->addSelect([
        //     'nama_provinsi'  => Provinsi::select('nama')->whereColumn('id_provinsi', 'master_provinsi.id'),
        //     'nama_kabupaten' => Kabupaten::select('nama')->whereColumn('id_kabupaten', 'master_kabupaten.id')
        // ])
        // ->where('flg_aktif', 1)
        // ->orderBy('nama', 'asc')
        // ->chunk(50, function ($chunks) use (&$data) {
        //     foreach ($chunks as $chunk) {
        //         $data[] = $chunk;
        //     }
        // });

        // return $data;
        // });

        if (empty($data)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        //  try {
        return response()->json([
            'code'   => 200,
            'status' => 'success',
            // 'count'  => sizeof($data),
            'data'   => $data
        ], 200);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         "code"    => 501,
        //         "status"  => "error",
        //         "message" => $e
        //     ], 501);
        // }
    }

    public function store(AreaRequest $req)
    {
        $data = array(
            'nama'         => $req->input('nama'),
            'id_provinsi'  => $req->input('id_provinsi'),
            'id_kabupaten' => $req->input('id_kabupaten')
        );

        Area::create($data);

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
        $query = Area::with('prov', 'kab')->select(
            'id',
            'nama as nama_area',
            'id_provinsi',
            'id_kabupaten',
            'flg_aktif',
            'created_at'
        )
            // ->addSelect([
            //     'nama_provinsi'  => Provinsi::select('nama')->whereColumn('id_provinsi', 'master_provinsi.id'),
            //     'nama_kabupaten' => Kabupaten::select('nama')->whereColumn('id_kabupaten', 'master_kabupaten.id')
            // ])
            ->where('id', $id)->first();

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

    public function update($id, AreaRequest $req)
    {
        $check = Area::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ada'
            ], 404);
        }

        $data = array(
            'nama'         => empty($req->input('nama'))
                ? $check->nama : $req->input('nama'),

            'id_provinsi'  => empty($req->input('id_provinsi'))
                ? $check->id_provinsi : $req->input('id_provinsi'),

            'id_kabupaten' => empty($req->input('id_kabupaten'))
                ? $check->id_kabupaten : $req->input('id_kabupaten'),

            'flg_aktif'    => empty($req->input('flg_aktif'))
                ? $check->flg_aktif : ($req->input('flg_aktif') == 'false' ? 0 : 1)
        );

        Area::where('id', $id)->update($data);

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
        Area::where('id', $id)->update(['flg_aktif' => 0]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data dengan id ' . $id . ' berhasil dihapus'
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

        Area::select('id', 'nama as nama_area')
            ->addSelect([
                'nama_provinsi'  => Provinsi::select('nama')->whereColumn('id_provinsi', 'master_provinsi.id'),
                'nama_kabupaten' => Kabupaten::select('nama')->whereColumn('id_kabupaten', 'master_kabupaten.id')
            ])
            ->where('flg_aktif', 0)
            ->orderBy('nama', 'asc')
            ->chunk(50, function ($chunks) use (&$data) {
                foreach ($chunks as $chunk) {
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
                'code'    => 200,
                'status'  => 'success',
                'count'   => sizeof($data),
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

    public function restore($id)
    {
        Area::where('id', $id)->update(['flg_aktif' => 1]);

        try {
            return response()->json([
                'code'    => 200,
                'status'  => 'success',
                'message' => 'Data berhasil dikembalikan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'code'   => 501,
                'status' => 'error',
                'data'   => $e
            ], 501);
        }
    }

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit)
    {
        $column = array('id', 'nama', 'id_provinsi', 'id_kabupaten');

        if ($param != 'filter' && $param != 'search') {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false) {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: ' . implode(",", $column)
            ], 412);
        }

        if ($param == 'search') {
            $operator   = "like";
            $func_value = "%{$value}%";
        } else {
            $operator   = "=";
            $func_value = "{$value}";
        }

        $query = Area::where('flg_aktif', $status)->orderBy($orderBy, $orderVal);

        if ($value == 'default') {
            $res = $query;
        } else {
            $res = $query->where($key, $operator, $func_value);
        }

        if ($limit == 'default') {
            $result = $res;
        } else {
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
                "nama_area"      => $val->nama,
                "nama_provinsi"  => $val->prov['nama'],
                "nama_kabupaten" => $val->kab['nama']
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
