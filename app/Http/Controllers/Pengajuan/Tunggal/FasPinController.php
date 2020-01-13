<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\FaspinRequest;

// Models
use App\Models\Pengajuan\SO\FasilitasPinjaman;
use App\Models\Transaksi\TransSO;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class FaspinController extends BaseController
{

    public function show($id){
        $check = FasilitasPinjaman::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pemeriksaaan Agunan Kendaraan Kosong'
            ], 404);
        }

        $data = array(
            'id'              => $check->id,
            'jenis_pinjaman'  => $check->jenis_pinjaman,
            'tujuan_pinjaman' => $check->tujuan_pinjaman,
            'plafon'          => $check->plafon,
            'tenor'           => $check->tenor
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
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

    public function update($id, FaspinRequest $req){
        $check = FasilitasPinjaman::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Fasilitas Pinjaman Kosong'
            ], 404);
        }

        $so = TransSO::where('id_fasilitas_pinjaman', $id)->first();

        if ($so == null) {
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
            'tenor'           => empty($req->input('tenor_pinjaman')) ? $check->tenor : $req->input('tenor_pinjaman')
        );

        DB::connection('web')->beginTransaction();

        try {
            FasilitasPinjaman::where('id', $id)->update($dataFasilitasPinjaman);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Fasilitas Pinjaman Berhasil'
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