<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\KapbulRequest;

// Models
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Transaksi\TransAO;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class KapBulController extends BaseController
{

    public function show($id){
        $check = KapBulanan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Kapasitas Bulanan Kosong'
            ], 404);
        }

        $data = array(
            'pemasukan' => array(
                'debitur' => (int) $check->pemasukan_cadebt,
                'pasangan'=> (int) $check->pemasukan_pasangan,
                'penjamin'=> (int) $check->pemasukan_penjamin,
                'total'   => (int) $check->total_pemasukan
            ),
            'pengeluaran' => array(
                'rumah_tangga'  => (int) $check->biaya_rumah_tangga,
                'transport'     => (int) $check->biaya_transport,
                'pendidikan'    => (int) $check->biaya_pendidikan,
                'telp_list_air' => (int) $check->biaya_telp_listr_air,
                'angsuran'      => (int) $check->angsuran,
                'lain_lain'     => (int) $check->biaya_lain,
                'total'         => (int) $check->total_pengeluaran
            ),
            'penghasilan_bersih' => (int) $check->penghasilan_bersih,
            'disposable_income'  => (int) $check->disposable_income
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

    public function update($id, KapbulRequest $req){
        $check = KapBulanan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Kapasitas Bulanan Kosong'
            ], 404);
        }

        $ao = TransAO::where('id_kapasitas_bulanan', $id)->first();

        if ($ao == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi AO Kosong'
            ], 404);
        }

        // KapBulanan
        $dataKapBulanan = array(
            'pemasukan_cadebt'      => empty($req->input('pemasukan_debitur')) ? 
                ($check->pemasukan_cadebt == null ? 0 : $check->pemasukan_cadebt) : $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'    => empty($req->input('pemasukan_pasangan')) ? 
                ($check->pemasukan_pasangan == null ? 0 : $check->pemasukan_pasangan) : $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'    => empty($req->input('pemasukan_penjamin')) ? 
                ($check->pemasukan_penjamin == null ? 0 : $check->pemasukan_penjamin) : $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'    => empty($req->input('biaya_rumah_tangga')) ? 
                ($check->biaya_rumah_tangga == null ? 0 : $check->biaya_rumah_tangga) : $req->input('biaya_rumah_tangga'),

            'biaya_transport'       => empty($req->input('biaya_transport')) ? 
                ($check->biaya_transport == null ? 0 : $check->biaya_transport) : $req->input('biaya_transport'),

            'biaya_pendidikan'      => empty($req->input('biaya_pendidikan')) ? 
                ($check->biaya_pendidikan == null ? 0 : $check->biaya_pendidikan) : $req->input('biaya_pendidikan'),

            'biaya_telp_listr_air'  => empty($req->input('biaya_telp_listr_air')) ? 
                ($check->biaya_telp_listr_air == null ? 0 : $check->biaya_telp_listr_air) : $req->input('biaya_telp_listr_air'),

            'angsuran'              => empty($req->input('angsuran')) ? 
                ($check->angsuran == null ? 0 : $check->angsuran) : $req->input('angsuran'),

            'biaya_lain'            => empty($req->input('biaya_lain')) ? 
                ($check->biaya_lain == null ? 0 : $check->biaya_lain) : $req->input('biaya_lain')
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 2)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        $KapBUl = array_merge($dataKapBulanan, $total_KapBul);

        DB::connection('web')->beginTransaction();

        try {
            KapBulanan::where('id', $id)->update($KapBUl);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Kapasitas Bulanan Berhasil'
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
