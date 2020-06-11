<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Exception;
use App\Models\Pengajuan\AO\PendapatanUsaha;

// Form Request

// Models
use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\DB;

class PendapatanUsahaController extends BaseController
{
    public function index()
    {
        $query = PendapatanUsaha::select('id', 'pemasukan_tunai', 'pemasukan_kredit', 'biaya_sewa', 'biaya_gaji_pegawai', 'biaya_belanja_brg', 'biaya_telp_listr_air', 'biaya_sampah_kemanan', 'biaya_kirim_barang', 'biaya_hutang_dagang', 'biaya_angsuran', 'biaya_lain_lain', 'total_pemasukan', 'total_pengeluaran', 'laba_usaha', 'ao_ca')->get();

        if ($query === NULL) {
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

    public function show($id)
    {
        $query = PendapatanUsaha::where('id', $id)->first();

        if (empty($query)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'data kosong'
            ], 404);
        }

        if ($query === null) {
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

    public function update($id, Request $req)
    {
        $check = PendapatanUsaha::where('id', $id)->first();


        if ($check === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data Pendapatan Usaha dengan id {$id} tidak ditemukan"
            ], 404);
        }
        // Pendapatan Usaha Cadebt
        $dataPendapatanUsaha = array(
            'pemasukan_tunai'      => empty($req->input('pemasukan_tunai'))     ? $check->pemasukan_tunai : $req->input('pemasukan_tunai'),
            'pemasukan_kredit'     => empty($req->input('pemasukan_kredit'))    ? $check->pemasukan_kredit : $req->input('pemasukan_kredit'),
            'biaya_sewa'           => empty($req->input('biaya_sewa'))          ? $check->biaya_sewa : $req->input('biaya_sewa'),
            'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai'))  ? $check->biaya_gaji_pegawai : $req->input('biaya_gaji_pegawai'),
            'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg'))   ? $check->biaya_belanja_brg : $req->input('biaya_belanja_brg'),
            'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? $check->biaya_telp_listr_air : $req->input('biaya_telp_listr_air'),
            'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? $check->biaya_sampah_kemanan : $req->input('biaya_sampah_kemanan'),
            'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang'))  ? $check->biaya_kirim_barang : $req->input('biaya_kirim_barang'),
            'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? $check->biaya_hutang_dagang : $req->input('biaya_hutang_dagang'),
            'biaya_angsuran'       => empty($req->input('biaya_angsuran'))      ? $check->biaya_angsuran : $req->input('biaya_angsuran'),
            'biaya_lain_lain'      => empty($req->input('biaya_lain_lain'))     ? $check->biaya_lain_lain : $req->input('biaya_lain_lain')
        );

        $totalPendapatan = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($dataPendapatanUsaha, 0, 2)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($dataPendapatanUsaha, 2)),
            'laba_usaha'         => $ttl1 - $ttl2
        );

        $Pendapatan = array_merge($dataPendapatanUsaha, $totalPendapatan, array('ao_ca' => 'CA'));


        DB::connection('web')->beginTransaction();

        try {
            PendapatanUsaha::where('id', $id)->update($Pendapatan);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Data Kapasitas Bulanan Berhasil',
                'data'   => $Pendapatan
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
