<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Exception;

// Form Request
use App\Http\Requests\Pengajuan\KapbulRequest;
use App\Models\Pengajuan\AO\KapBulanan;
// Models
use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\DB;

class KapBulController extends BaseController
{
    public function index()
    {
        $query = KapBulanan::select('id', 'pemasukan_cadebt', 'pemasukan_pasangan', 'pemasukan_penjamin', 'biaya_rumah_tangga', 'biaya_transport', 'biaya_pendidikan', 'telp_listr_air', 'angsuran', 'biaya_lain', 'total_pemasukan', 'total_pengeluaran', 'penghasilan_bersih', 'disposable_income', 'ao_ca')->get();

        if ($query == '[]') {
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
        $query = KapBulanan::where('id', $id)->first();

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

    public function update($id, KapbulRequest $req)
    {
        $check = KapBulanan::where('id', $id)->first();


        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data Mutasi Bank dengan id {$id} tidak ditemukan"
            ], 404);
        }
        //     dd($check->pemasukan_cadebt);
        $inputKapBul = array(

            'pemasukan_cadebt'
            => empty($req->input('pemasukan_debitur')) && $req->input('pemasukan_debitur') === 0  ? $check->pemasukan_cadebt : $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
            => empty($req->input('pemasukan_pasangan')) && $req->input('pemasukan_pasangan') === 0  ? $check->pemasukan_pasangan : $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
            => empty($req->input('pemasukan_penjamin')) && $req->input('pemasukan_penjamin') === 0  ? $check->pemasukan_penjamin : $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
            => empty($req->input('biaya_rumah_tangga')) && $req->input('biaya_rumah_tangga') === 0  ? $check->biaya_rumah_tangga : $req->input('biaya_rumah_tangga'),

            'biaya_transport'
            => empty($req->input('biaya_transport'))   && $req->input('biaya_transport') === 0   ? $check->biaya_transport : $req->input('biaya_transport'),

            'biaya_pendidikan'
            => empty($req->input('biaya_pendidikan'))   && $req->input('biaya_pendidikan') === 0  ? $check->biaya_pendidikan : $req->input('biaya_pendidikan'),

            'telp_listr_air'
            => empty($req->input('telp_listr_air'))   && $req->input('telp_listr_air') === 0    ? $check->telp_listr_air : $req->input('telp_listr_air'),

            'angsuran'
            => empty($req->input('angsuran'))      && $req->input('angsuran') === 0       ? $check->angsuran : $req->input('angsuran'),

            'biaya_lain'
            => empty($req->input('biaya_lain'))    && $req->input('biaya_lain') === 0       ? $check->biaya_lain : $req->input('biaya_lain'),
        );

 $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2,
            'ao_ca'              => empty($req->input('ao_ca') ? $check->ao_ca : $req->input('ao_ca'))
        );
        //  dd($dataKapBulanan);
        $KapBUl = array_merge( $inputKapBul, $total_KapBul);

        DB::connection('web')->beginTransaction();

        try {
            KapBulanan::where('id', $id)->update($KapBUl);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Data Kapasitas Bulanan Berhasil',
                'data'   => $KapBUl
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
