<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\Pe_TanahRequest;

// Models
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Transaksi\TransAO;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use DB;

class PemeriksaanTanahController extends BaseController
{

    public function show($id){
        $check = PemeriksaanAgunTan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pemeriksaaan Agunan Tanah Kosong'
            ], 404);
        }

        $data = array(
            'id'                            => $check->id,
            'id_agunan_tanah'               => $check->id_agunan_tanah,
            'nama_penghuni'                 => $check->nama_penghuni,
            'status_penghuni'               => $check->status_penghuni,
            'bentuk_bangunan'               => $check->bentuk_bangunan,
            'kondisi_bangunan'              => $check->kondisi_bangunan,
            'fasilitas'                     => $check->fasilitas,
            'listrik'                       => $check->listrik,
            'nilai_taksasi_agunan'          => $check->nilai_taksasi_agunan,
            'nilai_taksasi_bangunan'        => $check->nilai_taksasi_bangunan,
            'tgl_taksasi'                   => $check->tgl_taksasi,
            'nilai_likuidasi'               => $check->nilai_likuidasi,
            'nilai_agunan_independen'       => $check->nilai_agunan_independen,
            'perusahaan_penilai_independen' => $check->perusahaan_penilai_independen
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

    public function update($id, Pe_TanahRequest $req){
        $check = PemeriksaanAgunTan::where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pemeriksaan Agunan Tanah Kosong'
            ], 404);
        }

        $ao = TransAO::where('id_periksa_agunan_tanah', 'like', '%'.$id.'%')->first();

        if ($ao == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi AO Kosong'
            ], 404);
        }

        // PemeriksaanAgunTan
        $dataPemeriksaanAgunTan = array(
            'nama_penghuni'                 => empty($req->input('nama_penghuni_agunan')) ? $check->nama_penghuni : $req->input('nama_penghuni_agunan'),
            'status_penghuni'               => empty($req->input('status_penghuni_agunan')) ? $check->status_penghuni : strtoupper($req->input('status_penghuni_agunan')),
            'bentuk_bangunan'               => empty($req->input('bentuk_bangunan_agunan')) ? $check->bentuk_bangunan : $req->input('bentuk_bangunan_agunan'),
            'kondisi_bangunan'              => empty($req->input('kondisi_bangunan_agunan')) ? $check->kondisi_bangunan : $req->input('kondisi_bangunan_agunan'),
            'fasilitas'                     => empty($req->input('fasilitas_agunan')) ? $check->fasilitas : $req->input('fasilitas_agunan'),
            'listrik'                       => empty($req->input('listrik_agunan')) ? $check->listrik : $req->input('listrik_agunan'),
            'nilai_taksasi_agunan'          => empty($req->input('nilai_taksasi_agunan')) ? $check->nilai_taksasi_agunan : $req->input('nilai_taksasi_agunan'),
            'nilai_taksasi_bangunan'        => empty($req->input('nilai_taksasi_bangunan')) ? $check->nilai_taksasi_bangunan : $req->input('nilai_taksasi_bangunan'),
            'tgl_taksasi'                   => empty($req->input('tgl_taksasi_agunan')) ? $check->tgl_taksasi : Carbon::parse($req->input('tgl_taksasi_agunan'))->format('Y-m-d'),
            'nilai_likuidasi'               => empty($req->input('nilai_likuidasi_agunan')) ? $check->nilai_likuidasi : $req->input('nilai_likuidasi_agunan'),
            'nilai_agunan_independen'       => empty($req->nilai_agunan_independen) ? $check->nilai_agunan_independen : $req->nilai_agunan_independen,
            'perusahaan_penilai_independen' => empty($req->perusahaan_penilai_independen) ? $check->perusahaan_penilai_independen : $req->perusahaan_penilai_independen
        );

        DB::connection('web')->beginTransaction();

        try {
            PemeriksaanAgunTan::where('id', $id)->update($dataPemeriksaanAgunTan);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Pemeriksaaan Agunan Tanah Berhasil'
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
