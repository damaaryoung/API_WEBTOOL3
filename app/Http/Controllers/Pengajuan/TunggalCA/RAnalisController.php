<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Transaksi\TransCA;

use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\RiAnalisReq;

// Models
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use DB;

class RAnalisController extends BaseController
{
    public function index()
    {
        $query = RingkasanAnalisa::get()->toArray();

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
        $query = RingkasanAnalisa::where('id', $id)->first();

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

    public function update($id, RiAnalisReq $req)
    {
        $check = RingkasanAnalisa::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        $check_ca = TransCA::where('id_ringkasan_analisa', $id)->first();

        $rekomen_pendapatan  = ($check_ca == null ? 0 : $check_ca->kapbul['total_pemasukan']);
        $rekomen_pengeluaran = ($check_ca == null ? 0 : $check_ca->kapbul['total_pengeluaran']);
        $rekomen_angsuran    = ($check_ca == null ? 0 : $check_ca->kapbul['angsuran']);

        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        // Rekomendasi Angsuran pada table recom_ca
        $plafonCA = ($check_ca == null ? 0 : $check_ca->recom_ca['plafon_kredit']);
        $tenorCA  = ($check_ca == null ? 0 : $check_ca->recom_ca['jangka_waktu']);
        $bunga    = ($check_ca == null ? 0 : ($check_ca->recom_ca['suku_bunga'] / 100));

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        } else {
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }

        // $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        // Check Pemeriksaan
        $id_pe_ta = ($check_ca == null ? 0 : $check_ca->so['ao']['id_periksa_agunan_tanah']);

        if ($id_pe_ta == null) {
            $PeriksaTanah = null;
        }

        // $id_pe_ke = ($check_ca == null ? 0 : $check_ca->so['ao']['id_periksa_agunan_kendaraan']);

        // if ($id_pe_ke == null) {
        //     $PeriksaKenda = null;
        // }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if ($PeriksaTanah == []) {
            $sumTaksasiTan = 0;
        } else {
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah, 'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan

        $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => empty($req->input('kuantitatif_ttl_pendapatan')) ? $check->kuantitatif_ttl_pendapatan : $req->input('kuantitatif_ttl_pendapatan'),
            'kuantitatif_ttl_pengeluaran'   => empty($req->input('kuantitatif_ttl_pengeluaran')) ? $check->kuantitatif_ttl_pengeluaran : $req->input('kuantitatif_ttl_pengeluaran'),
            'kuantitatif_pendapatan_bersih' => empty($req->input('kuantitatif_pendapatan_bersih')) ? $check->kuantitatif_pendapatan_bersih : $req->input('kuantitatif_pendapatan_bersih'),
            'kuantitatif_angsuran'          => empty($req->input('kuantitatif_angsuran')) ? $check->kuantitatif_angsuran : $req->input('kuantitatif_angsuran'),
            'kuantitatif_ltv'               => empty($req->input('kuantitatif_ltv')) ? $check->kuantitatif_ltv : $req->input('kuantitatif_ltv'),
            'kuantitatif_dsr'               =>  empty($req->input('kuantitatif_dsr')) ? $check->kuantitatif_dsr : $req->input('kuantitatif_dsr'),
            'kuantitatif_idir'              => empty($req->input('kuantitatif_idir')) ? $check->kuantitatif_idir : $req->input('kuantitatif_idir'),
            'kuantitatif_hasil'             => empty($req->input('kuantitatif_hasil')) ? $check->kuantitatif_hasil : $req->input('kuantitatif_hasil'),


            'kualitatif_analisa'
            => empty($req->input('kualitatif_analisa'))
                ? $check->kualitatif_analisa
                : $req->input('kualitatif_analisa'),

            'kualitatif_strenght'
            => empty($req->input('kualitatif_strenght'))
                ? $check->kualitatif_strenght
                : $req->input('kualitatif_strenght'),

            'kualitatif_weakness'
            => empty($req->input('kualitatif_weakness'))
                ? $check->kualitatif_weakness
                : $req->input('kualitatif_weakness'),

            'kualitatif_opportunity'
            => empty($req->input('kualitatif_opportunity'))
                ? $check->kualitatif_opportunity
                : $req->input('kualitatif_opportunity'),

            'kualitatif_threatness'
            => empty($req->input('kualitatif_threatness'))
                ? $check->kualitatif_threatness
                : $req->input('kualitatif_threatness')
        );

        DB::connection('web')->beginTransaction();

        try {
            RingkasanAnalisa::where('id', $id)->update($dataRingkasan);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Berhasil',
                'data'   => $dataRingkasan
            ], 200);
        } catch (Exception $e) {

            DB::connection('web')->rollback();

            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
