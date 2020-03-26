<?php

namespace App\Http\Controllers\Pengajuan\Rekomendasi;

use Laravel\Lumen\Routing\Controller as BaseController;

// Form Request
use App\Http\Requests\Rekomendasi\RekomCaReq;

// Models
use App\Models\Pengajuan\CA\RekomendasiCA;
use DB;

class RekomCaController extends BaseController
{
    public function index(){
        $query = RekomendasiCA::get();

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

    public function show($id){
        $query = RekomendasiCA::where('id', $id)->first();

        if(empty($query)){
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
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, RekomCaReq $req){
        $check = RekomendasiCA::where('id', $id)->first();

        if (empty($check)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        $rekomCA = array(
            'produk' => empty($req->input('produk')) ? $check->produk : $req->input('produk'),
            'plafon_kredit' => empty($req->input('plafon_kredit')) ? $check->plafon_kredit : $req->input('plafon_kredit'),
            'jangka_waktu' => empty($req->input('jangka_waktu')) ? $check->jangka_waktu : $req->input('jangka_waktu'),
            'suku_bunga' => empty($req->input('suku_bunga')) ? $check->suku_bunga : $req->input('suku_bunga'),
            'pembayaran_bunga' => empty($req->input('pembayaran_bunga')) ? $check->pembayaran_bunga : $req->input('pembayaran_bunga'),
            'akad_kredit' => empty($req->input('akad_kredit')) ? $check->akad_kredit : $req->input('akad_kredit'),
            'ikatan_agunan' => empty($req->input('ikatan_agunan')) ? $check->ikatan_agunan : $req->input('ikatan_agunan'),
            'biaya_provisi' => empty($req->input('biaya_provisi')) ? $check->biaya_provisi : $req->input('biaya_provisi'),
            'biaya_administrasi' => empty($req->input('biaya_administrasi')) ? $check->biaya_administrasi : $req->input('biaya_administrasi'),
            'biaya_credit_checking' => empty($req->input('biaya_credit_checking')) ? $check->biaya_credit_checking : $req->input('biaya_credit_checking'),
            'biaya_asuransi_jiwa' => empty($req->input('biaya_asuransi_jiwa')) ? $check->biaya_asuransi_jiwa : $req->input('biaya_asuransi_jiwa'),
            'biaya_asuransi_jaminan' => empty($req->input('biaya_asuransi_jaminan')) ? $check->biaya_asuransi_jaminan : $req->input('biaya_asuransi_jaminan'),
            'notaris' => empty($req->input('notaris')) ? $check->notaris : $req->input('notaris'),
            'biaya_tabungan' => empty($req->input('biaya_tabungan')) ? $check->biaya_tabungan : $req->input('biaya_tabungan'),
            'rekom_angsuran' => empty($req->input('rekom_angsuran')) ? $check->rekom_angsuran : $req->input('rekom_angsuran'),
            'angs_pertama_bunga_berjalan' => empty($req->input('angs_pertama_bunga_berjalan')) ? $check->angs_pertama_bunga_berjalan : $req->input('angs_pertama_bunga_berjalan'),
            'pelunasan_nasabah_ro' => empty($req->input('pelunasan_nasabah_ro')) ? $check->pelunasan_nasabah_ro : $req->input('pelunasan_nasabah_ro'),
            'blokir_dana' => empty($req->input('blokir_dana')) ? $check->blokir_dana : $req->input('blokir_dana'),
            'pelunasan_tempat_lain' => empty($req->input('pelunasan_tempat_lain')) ? $check->pelunasan_tempat_lain : $req->input('pelunasan_tempat_lain'),
            'blokir_angs_kredit' => empty($req->input('blokir_angs_kredit')) ? $check->blokir_angs_kredit : $req->input('blokir_angs_kredit'),
        );

        DB::connection('web')->beginTransaction();

        try {
            RekomendasiCA::where('id', $id)->update($rekomCA);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Data Berhasil',
                'data'   => $rekomCA
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
