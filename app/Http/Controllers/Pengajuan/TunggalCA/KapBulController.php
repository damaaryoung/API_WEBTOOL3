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
            => empty($req->input('pemasukan_debitur'))   ? $check->pemasukan_cadebt : $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
            => empty($req->input('pemasukan_pasangan'))   ? $check->pemasukan_pasangan : $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
            => empty($req->input('pemasukan_penjamin'))   ? $check->pemasukan_penjamin : $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
            => empty($req->input('biaya_rumah_tangga'))   ? $check->biaya_rumah_tangga : $req->input('biaya_rumah_tangga'),

            'biaya_transport'
            => empty($req->input('biaya_transport'))      ? $check->biaya_transport : $req->input('biaya_transport'),

            'biaya_pendidikan'
            => empty($req->input('biaya_pendidikan'))     ? $check->biaya_pendidikan : $req->input('biaya_pendidikan'),

            'telp_listr_air'
            => empty($req->input('telp_listr_air'))       ? $check->telp_listr_air : $req->input('telp_listr_air'),

            'angsuran'
            => empty($req->input('angsuran'))             ? $check->angsuran : $req->input('angsuran'),

            'biaya_lain'
            => empty($req->input('biaya_lain'))           ? $check->biaya_lain : $req->input('biaya_lain'),
        );

        DB::connection('web')->beginTransaction();

        try {
            KapBulanan::where('id', $id)->update($inputKapBul);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Data Kapasitas Bulanan Berhasil',
                'data'   => $inputKapBul
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
