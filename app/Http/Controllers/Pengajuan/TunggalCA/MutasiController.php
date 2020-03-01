<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;

// Form Request
use App\Http\Requests\Pengajuan\MutasiRequest;

// Models
use App\Models\Pengajuan\CA\MutasiBank;
use DB;

class MutasiController extends BaseController
{
    public function index(){
        $query = MutasiBank::select('id', 'urutan_mutasi', 'nama_bank', 'no_rekening', 'nama_pemilik')->get();

        if($query == '[]'){
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

    public function show($id){
        $query = MutasiBank::where('id', $id)->first();

        if($query == '[]'){
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'data kosong'
            ], 404);
        }

        $head  = array_slice($query->toArray(), 0, 5);
        $table = array_slice($query->toArray(), 5);

        foreach($table as $key => $val){
            $column[$key] = explode(";", $val);
        }

        $row['table'] = Helper::second_flip_array($column);
        
        $result = array_merge($head, $row);

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

    public function update($id, MutasiRequest $req){
        $check = MutasiBank::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data Mutasi Bank dengan id {$id} tidak ditemukan"
            ], 404);
        }

        $dataMuBa = array(
            'urutan_mutasi' => empty($req->input('urutan_mutasi')) ? $check->urutan_mutasi : $req->input('urutan_mutasi'),

            'nama_bank' => empty($req->input('nama_bank_mutasi')) ? $check->nama_bank_mutasi : $req->input('nama_bank_mutasi'),

            'no_rekening' => empty($req->input('no_rekening_mutasi')) ? $check->no_rekening_mutasi : $req->input('no_rekening_mutasi'),

            'nama_pemilik' => empty($req->input('nama_pemilik_mutasi')) ? $check->nama_pemilik_mutasi : $req->input('nama_pemilik_mutasi'),

            'periode' => empty($req->input('periode_mutasi')) ? $check->periode_mutasi : implode(";", $req->input('periode_mutasi')),

            'frek_debet' => empty($req->input('frek_debet_mutasi')) ? $check->frek_debet_mutasi : implode(";", $req->input('frek_debet_mutasi')),

            'nominal_debet' => empty($req->input('nominal_debet_mutasi')) ? $check->nominal_debet_mutasi : implode(";", $req->input('nominal_debet_mutasi')),

            'frek_kredit' => empty($req->input('frek_kredit_mutasi')) ? $check->frek_kredit_mutasi : implode(";", $req->input('frek_kredit_mutasi')),

            'nominal_kredit' => empty($req->input('nominal_kredit_mutasi')) ? $check->nominal_kredit_mutasi : implode(";", $req->input('nominal_kredit_mutasi')),

            'saldo' => empty($req->input('saldo_mutasi')) ? $check->saldo_mutasi : implode(";", $req->input('saldo_mutasi'))
        );

        DB::connection('web')->beginTransaction();

        try {
            MutasiBank::where('id', $id)->update($dataMuBa);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Mutasi Bank Berhasil',
                'data'   => $dataMuBa
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
