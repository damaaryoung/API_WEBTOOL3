<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\DebUsahaRequest;

// Models
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Transaksi\TransAO;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class UsahaCadebtController extends BaseController
{

    public function show($id){
        $check = PendapatanUsaha::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pendapatan Usaha Calon Debitur Kosong'
            ], 404);
        }

        $data = array(
            'pendapatan' => array(
                'tunai' => $check->pemasukan_tunai,
                'kredit'=> $check->pemasukan_kredit,
                'total' => $check->total_pemasukan
            ),
            'pengeluaran' => array(
                'biaya_sewa'           => $check->biaya_sewa,
                'biaya_gaji_pegawai'   => $check->biaya_gaji_pegawai,
                'biaya_belanja_brg'    => $check->biaya_belanja_brg,
                'biaya_telp_listr_air' => $check->biaya_telp_listr_air,
                'biaya_sampah_kemanan' => $check->biaya_sampah_kemanan,
                'biaya_kirim_barang'   => $check->biaya_kirim_barang,
                'biaya_hutang_dagang'  => $check->biaya_hutang_dagang,
                'angsuran'             => $check->biaya_angsuran,
                'lain_lain'            => $check->biaya_lain_lain,
                'total'                => $check->total_pengeluaran
            ),
            'penghasilan_bersih' => $check->laba_usaha
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

    public function update($id, DebUsahaRequest $req){
        $check = PendapatanUsaha::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pendapatan Usaha Calon Debitur Kosong'
            ], 404);
        }

        $ao = TransAO::where('id_pendapatan_usaha', $id)->first();

        if ($ao == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi AO Kosong'
            ], 404);
        }

        // Pendapatan Usaha Calon Debitur
        $dataPendapatanUsaha = array(
            'pemasukan_tunai'      => empty($req->input('pemasukan_tunai')) ? $check->pemasukan_tunai : $req->input('pemasukan_tunai'),
            'pemasukan_kredit'     => empty($req->input('pemasukan_kredit')) ? $check->pemasukan_kredit : $req->input('pemasukan_kredit'),
            'biaya_sewa'           => empty($req->input('biaya_sewa')) ? $check->biaya_sewa : $req->input('biaya_sewa'),
            'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai')) ? $check->biaya_gaji_pegawai : $req->input('biaya_gaji_pegawai'),
            'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg')) ? $check->biaya_belanja_brg : $req->input('biaya_belanja_brg'),
            'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? $check->biaya_telp_listr_air : $req->input('biaya_telp_listr_air'),
            'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? $check->biaya_sampah_kemanan : $req->input('biaya_sampah_kemanan'),
            'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang')) ? $check->biaya_kirim_barang : $req->input('biaya_kirim_barang'),
            'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? $check->biaya_hutang_dagang : $req->input('biaya_hutang_dagang'),
            'biaya_angsuran'       => empty($req->input('biaya_angsuran')) ? $check->biaya_angsuran : $req->input('biaya_angsuran'),
            'biaya_lain_lain'      => empty($req->input('biaya_lain_lain')) ? $check->biaya_lain_lain : $req->input('biaya_lain_lain'),

            'total_pemasukan'      => (empty($req->input('pemasukan_tunai')) ? $check->pemasukan_tunai : $req->input('pemasukan_tunai')) + (empty($req->input('pemasukan_kredit')) ? $check->pemasukan_kredit : $req->input('pemasukan_kredit')),

            'total_pengeluaran'    => (empty($req->input('biaya_sewa')) ? $check->biaya_sewa : $req->input('biaya_sewa')) + (empty($req->input('biaya_gaji_pegawai')) ? $check->biaya_gaji_pegawai : $req->input('biaya_gaji_pegawai')) + (empty($req->input('biaya_belanja_brg')) ? $check->biaya_belanja_brg : $req->input('biaya_belanja_brg')) + (empty($req->input('biaya_telp_listr_air')) ? $check->biaya_telp_listr_air : $req->input('biaya_telp_listr_air')) + (empty($req->input('biaya_sampah_kemanan')) ? $check->biaya_sampah_kemanan : $req->input('biaya_sampah_kemanan')) + (empty($req->input('biaya_kirim_barang')) ? $check->biaya_kirim_barang : $req->input('biaya_kirim_barang')) + (empty($req->input('biaya_hutang_dagang')) ? $check->biaya_hutang_dagang : $req->input('biaya_hutang_dagang')) + (empty($req->input('biaya_angsuran')) ? $check->biaya_angsuran : $req->input('biaya_angsuran')) + (empty($req->input('biaya_lain_lain')) ? $check->biaya_lain_lain : $req->input('biaya_lain_lain')),

            'laba_usaha'           => ((empty($req->input('pemasukan_tunai')) ? $check->pemasukan_tunai : $req->input('pemasukan_tunai')) + (empty($req->input('pemasukan_kredit')) ? $check->pemasukan_kredit : $req->input('pemasukan_kredit'))) - ((empty($req->input('biaya_sewa')) ? $check->biaya_sewa : $req->input('biaya_sewa')) + (empty($req->input('biaya_gaji_pegawai')) ? $check->biaya_gaji_pegawai : $req->input('biaya_gaji_pegawai')) + (empty($req->input('biaya_belanja_brg')) ? $check->biaya_belanja_brg : $req->input('biaya_belanja_brg')) + (empty($req->input('biaya_telp_listr_air')) ? $check->biaya_telp_listr_air : $req->input('biaya_telp_listr_air')) + (empty($req->input('biaya_sampah_kemanan')) ? $check->biaya_sampah_kemanan : $req->input('biaya_sampah_kemanan')) + (empty($req->input('biaya_kirim_barang')) ? $check->biaya_kirim_barang : $req->input('biaya_kirim_barang')) + (empty($req->input('biaya_hutang_dagang')) ? $check->biaya_hutang_dagang : $req->input('biaya_hutang_dagang')) + (empty($req->input('biaya_angsuran')) ? $check->biaya_angsuran : $req->input('biaya_angsuran')) + (empty($req->input('biaya_lain_lain')) ? $check->biaya_lain_lain : $req->input('biaya_lain_lain')))
        );

        DB::connection('web')->beginTransaction();

        try {
            PendapatanUsaha::where('id', $id)->update($dataPendapatanUsaha);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Pendapatan Usaha Calon Debitur Berhasil'
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
