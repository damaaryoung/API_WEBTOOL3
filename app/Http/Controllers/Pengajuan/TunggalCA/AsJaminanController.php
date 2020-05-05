<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;

// Form Request
use App\Http\Requests\Pengajuan\AsJaminanReq;

// Models
use App\Models\Pengajuan\CA\AsuransiJaminan;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class AsJaminanController extends BaseController
{
    public function index()
    {
        $check = AsuransiJaminan::first();
        $query = AsuransiJaminan::get();
        //dd($query);
        if (!$check) {
            return response()->json([
                'code'  => 404,
                'status'   => 'not found',
                'message'   => 'data not found'
            ], 404);
        }
        $arrData = array();
        foreach ($query as $key => $val) {
            $arrData[$key]['nama_asuransi']       = $val->nama_asuransi;
            $arrData[$key]['jangka_waktu']   = $val->jangka_waktu;
            $arrData[$key]['nilai_pertanggungan']    = $val->nilai_pertanggungan;
            $arrData[$key]['jatuh_tempo']   = Carbon::parse($val->jatuh_tempo)->format('d-m-Y');
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $arrData
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function show($id)
    {
        $query = AsuransiJaminan::where('id', $id)->first();

        if ($query == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'data kosong'
            ], 404);
        }

        try {
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

    public function update($id, AsJaminanReq $req)
    {
        $check = AsuransiJaminan::where('id', $id)->first();
        //    dd($check);
        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        $asJaminan = array(
            'nama_asuransi'
            => $req->input('nama_asuransi_jaminan'),

            'jangka_waktu'
            => $req->input('jangka_waktu_as_jaminan'),

            'nilai_pertanggungan'
            => $req->input('nilai_pertanggungan_as_jaminan'),

            'jatuh_tempo'
            => Carbon::parse($req->input('jatuh_tempo_as_jaminan'))->format('Y-m-d')
        );

        DB::connection('web')->beginTransaction();

        try {
            AsuransiJaminan::where('id', $id)->update($asJaminan);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Data Berhasil',
                'data'   => $asJaminan
            ], 200);
        } catch (Exception $e) {

            $err = DB::connection('web')->rollback();

            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
}
