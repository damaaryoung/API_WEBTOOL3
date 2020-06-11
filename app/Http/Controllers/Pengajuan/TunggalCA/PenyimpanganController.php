<?php

namespace App\Http\Controllers\Pengajuan\TunggalCA;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Exception;
use App\Models\Pengajuan\CAA\Penyimpangan;

// Form Request

// Models
use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\DB;

class PenyimpanganController extends BaseController
{
    public function index()
    {
        $query = Penyimpangan::select('id', 'id_trans_so', 'id_trans_caa', 'biaya_provisi', 'biaya_admin', 'biaya_kredit', 'ltv', 'tenor', 'kartu_pinjaman', 'sertifikat_diatas_50', 'sertifikat_diatas_150', 'profesi_beresiko', 'jaminan_kp_tenor_48')->get();

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
        $query = Penyimpangan::where('id', $id)->first();

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
        $check = Penyimpangan::where('id', $id)->first();


        if ($check === null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => "Data Penyimpangan dengan id {$id} tidak ditemukan"
            ], 404);
        }
        // Penyimpangan Cadebt
        $dataPenyimpangan = array(
            'biaya_provisi'      => empty($req->input('biaya_provisi'))     ? $check->biaya_provisi : $req->input('biaya_provisi'),
            'biaya_admin'     => empty($req->input('biaya_admin'))    ? $check->biaya_admin : $req->input('biaya_admin'),
            'biaya_kredit'           => empty($req->input('biaya_kredit'))          ? $check->biaya_kredit : $req->input('biaya_kredit'),
            'ltv'   => empty($req->input('ltv'))  ? $check->ltv : $req->input('ltv'),
            'tenor'    => empty($req->input('tenor'))   ? $check->tenor : $req->input('tenor'),
            'kartu_pinjaman' => empty($req->input('kartu_pinjaman')) ? $check->kartu_pinjaman : $req->input('kartu_pinjaman'),
            'sertifikat_diatas_50' => empty($req->input('sertifikat_diatas_50')) ? $check->sertifikat_diatas_50 : $req->input('sertifikat_diatas_50'),
            'sertifikat_diatas_150'   => empty($req->input('sertifikat_diatas_150'))  ? $check->sertifikat_diatas_150 : $req->input('sertifikat_diatas_150'),
            'profesi_beresiko'  => empty($req->input('profesi_beresiko')) ? $check->profesi_beresiko : $req->input('profesi_beresiko'),
            'jaminan_kp_tenor_48'       => empty($req->input('jaminan_kp_tenor_48'))      ? $check->jaminan_kp_tenor_48 : $req->input('jaminan_kp_tenor_48'),
            'biaya_lain_lain'      => empty($req->input('biaya_lain_lain'))     ? $check->biaya_lain_lain : $req->input('biaya_lain_lain')
        );






        DB::connection('web')->beginTransaction();

        try {
            Penyimpangan::where('id', $id)->update($dataPenyimpangan);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Data Kapasitas Bulanan Berhasil',
                'data'   => $dataPenyimpangan
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
