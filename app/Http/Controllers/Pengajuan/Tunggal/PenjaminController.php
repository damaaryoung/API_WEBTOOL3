<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\PenjaminRequest;

// Models
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Transaksi\TransSO;
use App\Models\User;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Requests;
use Carbon\Carbon;
use Image;
use DB;

class PenjaminController extends BaseController
{

    public function show($id)
    {
        $check = Penjamin::with('prov_kerja', 'kab_kerja', 'kec_kerja', 'kel_kerja')
            ->where('id', $id)->first();

        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Penjamin Kosong'
            ], 404);
        }

        $data = array(
            "id"                    => $check->id == null ? null : (int) $check->id,
            "nama_ktp"              => $check->nama_ktp,
            "nama_ibu_kandung"      => $check->nama_ibu_kandung,
            "no_ktp"                => $check->no_ktp,
            "no_npwp"               => $check->no_npwp,
            "tempat_lahir"          => $check->tempat_lahir,
            "tgl_lahir"             => Carbon::parse($check->tgl_lahir)->format('d-m-Y'),
            "jenis_kelamin"         => $check->jenis_kelamin,
            "alamat_ktp"            => $check->alamat_ktp,
            "no_telp"               => $check->no_telp,
            "hubungan_debitur"      => $check->hubungan_debitur,
            "pekerjaan" => [
                "nama_pekerjaan"        => $check->pekerjaan,
                "posisi_pekerjaan"      => $check->posisi_pekerjaan,
                "nama_tempat_kerja"     => $check->nama_tempat_kerja,
                "jenis_pekerjaan"       => $check->jenis_pekerjaan,
                "tgl_mulai_kerja"       => $check->tgl_mulai_kerja, //Carbon::parse($check->tgl_mulai_kerja)->format('d-m-Y'),
                "no_telp_tempat_kerja"  => $check->no_telp_tempat_kerja,
                'alamat' => [
                    'alamat_singkat' => $check->alamat_tempat_kerja,
                    'rt'             => $check->rt_tempat_kerja == null ? null : (int) $check->rt_tempat_kerja,
                    'rw'             => $check->rw_tempat_kerja == null ? null : (int) $check->rw_tempat_kerja,
                    'kelurahan' => [
                        'id'    => $check->id_kel_tempat_kerja == null ? null : (int) $check->id_kel_tempat_kerja,
                        'nama'  => $check->kel_kerja['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $check->id_kec_tempat_kerja == null ? null : (int) $check->id_kec_tempat_kerja,
                        'nama'  => $check->kec_kerja['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $check->id_kab_tempat_kerja == null ? null : (int) $check->id_kab_tempat_kerja,
                        'nama'  => $check->kab_kerja['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $check->id_prov_tempat_kerja == null ? null : (int) $check->id_prov_tempat_kerja,
                        'nama' => $check->prov_kerja['nama'],
                    ],
                    'kode_pos' => $check->kel_kerja['kode_pos'] == null ? null : (int) $check->kel_kerja['kode_pos']
                ]
            ],
            "lampiran" => [
                "lamp_ktp"          => $check->lamp_ktp,
                "lamp_ktp_pasangan" => $check->lamp_ktp_pasangan,
                "lamp_kk"           => $check->lamp_kk,
                "lamp_buku_nikah"   => $check->lamp_buku_nikah
            ]
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

    public function update($id, PenjaminRequest $req)
    {
        $check_penj = Penjamin::where('id', $id)->first();

        if ($check_penj == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Penjamin Kosong'
            ], 404);
        }

        $so = TransSO::where('id_Penjamin', 'like', '%' . $id . '%')->first();

        if ($so == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi SO Kosong'
            ], 404);
        }

        /** Check Lampiran */
        $check_lamp_ktp_pen          = $check_penj->lamp_ktp;
        $check_lamp_ktp_pasangan_pen = $check_penj->lamp_ktp_pasangan;
        $check_lamp_kk_pen           = $check_penj->lamp_kk;
        $check_lamp_buku_nikah_pen   = $check_penj->lamp_buku_nikah;
        /** */

        $path = 'public/' . $so->debt['no_ktp'] . '/penjamin';

        if ($file = $req->file('lamp_ktp_pen')) {
            $check = $check_lamp_ktp_pen;
            $name = 'ktp_penjamin.';

            $ktpPen = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $ktpPen = $check_lamp_ktp_pen;
        }

        if ($file = $req->file('lamp_ktp_pasangan_pen')) {
            $check = $check_lamp_ktp_pasangan_pen;
            $name = 'ktp_pasangan.';

            $ktpPenPAS = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $ktpPenPAS = $check_lamp_ktp_pasangan_pen;
        }

        if ($file = $req->file('lamp_kk_pen')) {
            $check = $check_lamp_kk_pen;
            $name = 'kk_penjamin.';

            $kkPen = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $kkPen = $check_lamp_kk_pen;
        }

        if ($file = $req->file('lamp_buku_nikah_pen')) {
            $check = $check_lamp_buku_nikah_pen;
            $name = 'buku_nikah_penjamin.';

            $bukuNikahPen = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $bukuNikahPen = $check_lamp_buku_nikah_pen;
        }

        // Data Usaha Calon Debitur
        // Penjamin Lama
        $dataPenjamin = array(
            'id_trans_so'         => empty($req->input('trans_so'))
                ? $check_penj->id_trans_so : $req->input('trans_so'),

            // 'id_calon_debitur' => $check_penj->id_calon_debitur,
            'nama_ktp'         => empty($req->input('nama_ktp_pen'))
                ? $check_penj->nama_ktp : $req->input('nama_ktp_pen'),

            'nama_ibu_kandung' => empty($req->input('nama_ibu_kandung_pen'))
                ? $check_penj->nama_ibu_kandung : $req->input('nama_ibu_kandung_pen'),

            'no_ktp'           => empty($req->input('no_ktp_pen'))
                ? $check_penj->no_ktp : $req->input('no_ktp_pen'),

            'no_npwp'          => empty($req->input('no_npwp_pen'))
                ? $check_penj->no_npwp : $req->input('no_npwp_pen'),

            'tempat_lahir'     => empty($req->input('tempat_lahir_pen'))
                ? $check_penj->tempat_lahir : $req->input('tempat_lahir_pen'),

            'tgl_lahir'        => empty($req->input('tgl_lahir_pen'))
                ? $check_penj->tgl_lahir : Carbon::parse($req->input('tgl_lahir_pen'))->format('Y-m-d'),

            'jenis_kelamin'    => empty($req->input('jenis_kelamin_pen'))
                ? $check_penj->jenis_kelamin : strtoupper($req->input('jenis_kelamin_pen')),

            'alamat_ktp'       => empty($req->input('alamat_ktp_pen'))
                ? $check_penj->alamat_ktp : $req->input('alamat_ktp_pen'),

            'no_telp'          => empty($req->input('no_telp_pen'))
                ? $check_penj->no_telp : $req->input('no_telp_pen'),

            'hubungan_debitur' => empty($req->input('hubungan_debitur_pen'))
                ? $check_penj->hubungan_debitur : $req->input('hubungan_debitur_pen'),

            'pekerjaan'             => empty($req->input('pekerjaan_pen'))
                ? $check_penj->pekerjaan : $req->input('pekerjaan_pen'),

            'nama_tempat_kerja'     => empty($req->input('nama_tempat_kerja_pen'))
                ? $check_penj->nama_tempat_kerja : $req->input('nama_tempat_kerja_pen'),

            'posisi_pekerjaan'      => empty($req->input('posisi_pekerjaan_pen'))
                ? $check_penj->posisi_pekerjaan : $req->input('posisi_pekerjaan_pen'),

            'jenis_pekerjaan'       => empty($req->input('jenis_pekerjaan_pen'))
                ? $check_penj->jenis_pekerjaan : $req->input('jenis_pekerjaan_pen'),

            'alamat_tempat_kerja'   => empty($req->input('alamat_tempat_kerja_pen'))
                ? $check_penj->alamat_tempat_kerja : $req->input('alamat_tempat_kerja_pen'),

            'id_prov_tempat_kerja'  => empty($req->input('id_prov_tempat_kerja_pen'))
                ? $check_penj->id_prov_tempat_kerja : $req->input('id_prov_tempat_kerja_pen'),

            'id_kab_tempat_kerja'   => empty($req->input('id_kab_tempat_kerja_pen'))
                ? $check_penj->id_kab_tempat_kerja : $req->input('id_kab_tempat_kerja_pen'),

            'id_kec_tempat_kerja'   => empty($req->input('id_kec_tempat_kerja_pen'))
                ? $check_penj->id_kec_tempat_kerja : $req->input('id_kec_tempat_kerja_pen'),

            'id_kel_tempat_kerja'   => empty($req->input('id_kel_tempat_kerja_pen'))
                ? $check_penj->id_kel_tempat_kerja : $req->input('id_kel_tempat_kerja_pen'),

            'rt_tempat_kerja'       => empty($req->input('rt_tempat_kerja_pen'))
                ? $check_penj->rt_tempat_kerja : $req->input('rt_tempat_kerja_pen'),

            'rw_tempat_kerja'       => empty($req->input('rw_tempat_kerja_pen'))
                ? $check_penj->rw_tempat_kerja : $req->input('rw_tempat_kerja_pen'),

            'tgl_mulai_kerja'       => empty($req->input('tgl_mulai_kerja_pen'))
                ? $check_penj->tgl_mulai_kerja : $req->input('tgl_mulai_kerja_pen'),

            'no_telp_tempat_kerja'  => empty($req->input('no_telp_tempat_kerja_pen'))
                ? $check_penj->no_telp_tempat_kerja : $req->input('no_telp_tempat_kerja_pen'),

            'lamp_ktp'         => $ktpPen,
            'lamp_ktp_pasangan' => $ktpPenPAS,
            'lamp_kk'          => $kkPen,
            'lamp_buku_nikah'  => $bukuNikahPen,
        );

        DB::connection('web')->beginTransaction();

        try {

            Penjamin::where('id', $id)->update($dataPenjamin);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Penjamin Berhasil',
                'data'   => $dataPenjamin
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
