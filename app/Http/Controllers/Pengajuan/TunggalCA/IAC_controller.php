<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

// Form Request
use App\Http\Requests\Pengajuan\IACRequest;

// Models
use App\Models\Pengajuan\CA\InfoACC;
use DB;

class IAC_Controller extends BaseController
{
    public function index(){
        $query = InfoACC::get()->toArray();

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
        $query = InfoACC::where('id', $id)->first();

        if($query == null){
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

    public function update($id, IACRequest $req){
        $check = InfoACC::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        // dd($check->id);

        $check_ca = TransCA::where('id_log_tabungan', $check->id)->first();
        // dd($check_ca);
        $rekomen_pendapatan = ($check_ca == null ? 0 : $check_ca->kapbul['total_pemasukan']);

        $dataTabUang = array(

            'no_rekening'
                => empty($req->input('no_rekening')) ? $check->no_rekening : $req->input('no_rekening'),

            'nama_bank'
                => empty($req->input('nama_bank')) ? $check->nama_bank : $req->input('nama_bank'),

            'tujuan_pembukaan_rek'
                => empty($req->input('tujuan_pembukaan_rek')) ? $check->tujuan_pembukaan_rek : $req->input('tujuan_pembukaan_rek'),

            'penghasilan_per_tahun'
                => empty($req->input('penghasilan_per_tahun')) ? ($rekomen_pendapatan * 12) : $req->input('penghasilan_per_tahun'),

            'sumber_penghasilan'
                => empty($req->input('sumber_penghasilan')) ? $check->sumber_penghasilan : $req->input('sumber_penghasilan'),

            'pemasukan_per_bulan'
                => empty($req->input('pemasukan_per_bulan')) ? $check->pemasukan_per_bulan : $req->input('pemasukan_per_bulan'),

            'frek_trans_pemasukan'
                => empty($req->input('frek_trans_pemasukan')) ? $check->frek_trans_pemasukan : $req->input('frek_trans_pemasukan'),

            'pengeluaran_per_bulan'
                => empty($req->input('pengeluaran_per_bulan')) ? $check->pengeluaran_per_bulan : $req->input('pengeluaran_per_bulan'),

            'frek_trans_pengeluaran'
                => empty($req->input('frek_trans_pengeluaran')) ? $check->frek_trans_pengeluaran : $req->input('frek_trans_pengeluaran'),

            'sumber_dana_setoran'
                => empty($req->input('sumber_dana_setoran')) ? $check->sumber_dana_setoran : $req->input('sumber_dana_setoran'),

            'tujuan_pengeluaran_dana'
                => empty($req->input('tujuan_pengeluaran_dana')) ? $check->tujuan_pengeluaran_dana : $req->input('tujuan_pengeluaran_dana')
        );

        DB::connection('web')->beginTransaction();

        try {
            InfoACC::where('id', $id)->update($dataTabUang);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Data Tabungan Nasabah Berhasil',
                'data'   => $dataTabUang
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
