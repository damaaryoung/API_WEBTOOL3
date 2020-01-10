<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\Pe_KendaraanRequest;

// Models
use App\Models\Pengajuan\PemeriksaanAgunKen;
use App\Models\Bisnis\TransSo;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class PemeriksaanKendaraanController extends BaseController
{

    public function show($id){
        $check = PemeriksaanAgunKen::with('debt')
            ->where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pemeriksaaan Agunan Kendaraan Kosong'
            ], 404);
        }

        $data = array(
            'id'                  => $check->id,
            'id_agunan_kendaraan' => $check->id_agunan_kendaraan,
            'nama_pengguna'       => $check->nama_pengguna,
            'status_pengguna'     => $check->status_pengguna,
            'jml_roda_kendaraan'  => $check->jml_roda_kendaraan,
            'kondisi_kendaraan'   => $check->kondisi_kendaraan,
            'keberadaan_kendaraan'=> $check->keberadaan_kendaraan,
            'body'                => $check->body,
            'interior'            => $check->interior,
            'km'                  => (int) $check->km,
            'modifikasi'          => $check->modifikasi,
            'aksesoris'           => $check->aksesoris,
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

    public function update($id, Pe_KendaraanRequest $req){
        $check = PemeriksaanAgunKen::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pemeriksaan Agunan Kendaraan Kosong'
            ], 404);
        }

        $so = TransSo::where('id_periksa_agunan_kendaraan', 'like', '%'.$check->id.'%')->get();

        if ($so == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi SO Kosong'
            ], 404);
        }

        // PemeriksaanAgunKen
        $dataPemeriksaanAgunKen = array(
            'nama_pengguna'         => empty($req->input('nama_pengguna_ken')) ? $check->nama_pengguna : $req->input('nama_pengguna_ken'),
            'status_pengguna'       => empty($req->input('status_pengguna_ken')) ? $check->status_pengguna : strtoupper($req->input('status_pengguna_ken')),
            'jml_roda_kendaraan'    => empty($req->input('jml_roda_ken')) ? $check->jml_roda_kendaraan : $req->input('jml_roda_ken'),
            'kondisi_kendaraan'     => empty($req->input('kondisi_ken')) ? $check->kondisi_kendaraan : $req->input('kondisi_ken'),
            'keberadaan_kendaraan'  => empty($req->input('keberadaan_ken')) ? $check->keberadaan_kendaraan : $req->input('keberadaan_ken'),
            'body'                  => empty($req->input('body_ken')) ? $check->body : $req->input('body_ken'),
            'interior'              => empty($req->input('interior_ken')) ? $check->interior : $req->input('interior_ken'),
            'km'                    => empty($req->input('km_ken')) ? $check->km : $req->input('km_ken'),
            'modifikasi'            => empty($req->input('modifikasi_ken')) ? $check->modifikasi : $req->input('modifikasi_ken'),
            'aksesoris'             => empty($req->input('aksesoris_ken')) ? $check->aksesoris : $req->input('aksesoris_ken'),
        );

        DB::connection('web')->beginTransaction();

        try {
            PemeriksaanAgunKen::where('id', $id)->update($dataPemeriksaanAgunKen);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Pemeriksaaan Agunan Kendaraan Berhasil'
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
