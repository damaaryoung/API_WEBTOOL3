<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\FaspinRequest;

// Models
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use App\Models\Transaksi\TransSO;
// use App\Models\User;

// use Illuminate\Support\Facades\File;
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Carbon\Carbon;
use DB;

class FaspinController extends BaseController
{
    public function segmentasiBPR(){
        $query = DB::connection('web')->table('view_segmentasi_bpr')->select('kode', 'nama')->get();

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $query
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
        $query = FasilitasPinjaman::select('id', 'jenis_pinjaman', 'tujuan_pinjaman', 'plafon', 'tenor', 'segmentasi_bpr')->where('id', $id)->first();

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pemeriksaaan Agunan Kendaraan Kosong'
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
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, FaspinRequest $req)
    {
        $check = FasilitasPinjaman::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Fasilitas Pinjaman Kosong'
            ], 404);
        }

        $so = TransSO::where('id_fasilitas_pinjaman', $id)->first();

        if (empty($so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi SO Kosong'
            ], 404);
        }

        // FasilitasPinjaman
        $dataFasilitasPinjaman = array(
            'jenis_pinjaman'  => empty($req->input('jenis_pinjaman')) ? $check->jenis_pinjaman : $req->input('jenis_pinjaman'),
            'tujuan_pinjaman' => empty($req->input('tujuan_pinjaman')) ? $check->tujuan_pinjaman : $req->input('tujuan_pinjaman'),
            'plafon'          => empty($req->input('plafon_pinjaman')) ? $check->plafon : $req->input('plafon_pinjaman'),
            'tenor'           => empty($req->input('tenor_pinjaman')) ? $check->tenor : $req->input('tenor_pinjaman'),
            'segmentasi_bpr'  => empty($req->input('segmentasi_bpr')) ? $check->segmentasi_bpr : $req->input('segmentasi_bpr')
        );

        DB::connection('web')->beginTransaction();

        try {
            FasilitasPinjaman::where('id', $id)->update($dataFasilitasPinjaman);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Fasilitas Pinjaman Berhasil',
                'data'   => $dataFasilitasPinjaman
            ], 200);
        } catch (\Exception $e) {

            $err = DB::connection('web')->rollback();

            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $err
            ], 501);
        }
    }
}
