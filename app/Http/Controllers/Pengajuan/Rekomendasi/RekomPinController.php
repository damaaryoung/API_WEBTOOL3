<?php

namespace App\Http\Controllers\Pengajuan\Rekomendasi;

use Laravel\Lumen\Routing\Controller as BaseController;

// Form Request
use App\Http\Requests\Rekomendasi\RekomPinReq;

// Models
use App\Models\Pengajuan\CA\RekomendasiPinjaman;
use DB;

class RekomPinController extends BaseController
{
    public function index(){
        $query = RekomendasiPinjaman::get()->toArray();

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
        $query = RekomendasiPinjaman::where('id', $id)->first();

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
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, RekomPinReq $req){
        $check = RekomendasiPinjaman::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data dengan id {$id} tidak ditemukan"
            ], 404);
        }

        $rekomPinjaman = array(
            'penyimpangan_struktur'
                => empty($req->input('penyimpangan_struktur'))
                ? $check->penyimpangan_struktur : $req->input('penyimpangan_struktur'),

            'penyimpangan_dokumen'
                => empty($req->input('penyimpangan_dokumen'))
                ? $check->penyimpangan_dokumen : $req->input('penyimpangan_dokumen'),

            'recom_nilai_pinjaman'
                => empty($req->input('recom_nilai_pinjaman'))
                ? $check->recom_nilai_pinjaman : $req->input('recom_nilai_pinjaman'),

            'recom_tenor'
                => empty($req->input('recom_tenor'))
                ? $check->recom_tenor : $req->input('recom_tenor'),

            'recom_angsuran'
                => empty($req->input('recom_angsuran'))
                ? $check->recom_angsuran : $req->input('recom_angsuran'),

            'recom_produk_kredit'
                => empty($req->input('recom_produk_kredit'))
                ? $check->recom_produk_kredit : $req->input('recom_produk_kredit'),

            'note_recom'
                => empty($req->input('note_recom'))
                ? $check->note_recom : $req->input('note_recom')
        );

        DB::connection('web')->beginTransaction();

        try {
            RekomendasiPinjaman::where('id', $id)->update($rekomPinjaman);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Data Berhasil',
                'data'   => $rekomPinjaman
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
