<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\PasanganRequest;

// Models
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Transaksi\TransSO;

use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Image;
use DB;

class PasanganController extends BaseController
{

    public function show($id){
        $val = Pasangan::with('prov_kerja','kab_kerja','kec_kerja','kel_kerja')
            ->where('id', $id)->first();

        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pasangan Kosong'
            ], 404);
        }

        $data = array(
            'id'               => $val->id == null ? null : (int) $val->id,
            'nama_lengkap'     => $val->nama_lengkap,
            'nama_ibu_kandung' => $val->nama_ibu_kandung,
            'jenis_kelamin'    => $val->jenis_kelamin,
            'no_ktp'           => $val->no_ktp,
            'no_ktp_kk'        => $val->no_ktp_kk,
            'no_npwp'          => $val->no_npwp,
            'tempat_lahir'     => $val->tempat_lahir,
            'tgl_lahir'        => Carbon::parse($val->tgl_lahir)->format('d-m-Y'),
            'alamat_ktp'       => $val->alamat_ktp,
            'no_telp'          => $val->no_telp,
            'pekerjaan' => [
                "nama_pekerjaan"        => $val->pekerjaan,
                "posisi_pekerjaan"      => $val->posisi_pekerjaan,
                "nama_tempat_kerja"     => $val->nama_tempat_kerja,
                "jenis_pekerjaan"       => $val->jenis_pekerjaan,
                "tgl_mulai_kerja"       => $val->tgl_mulai_kerja, //Carbon::parse($val->tgl_mulai_kerja)->format('d-m-Y'),
                "no_telp_tempat_kerja"  => $val->no_telp_tempat_kerja,
                'alamat' => [
                    'alamat_singkat' => $val->alamat_tempat_kerja,
                    'rt'             => $val->rt_tempat_kerja == null ? null : (int) $val->rt_tempat_kerja,
                    'rw'             => $val->rw_tempat_kerja == null ? null : (int) $val->rw_tempat_kerja,
                    'kelurahan' => [
                        'id'    => $val->id_kel_tempat_kerja == null ? null : (int) $val->id_kel_tempat_kerja,
                        'nama'  => $val->kel_kerja['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->id_kec_tempat_kerja == null ? null : (int) $val->id_kec_tempat_kerja,
                        'nama'  => $val->kec_kerja['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->id_kab_tempat_kerja == null ? null : (int) $val->id_kab_tempat_kerja,
                        'nama'  => $val->kab_kerja['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->id_prov_tempat_kerja == null ? null : (int) $val->id_prov_tempat_kerja,
                        'nama' => $val->prov_kerja['nama'],
                    ],
                    'kode_pos' => $val->kel_kerja['kode_pos'] == null ? null : (int) $val->kel_kerja['kode_pos']
                ]
            ],
            'lampiran' => [
                'lamp_ktp'         => $val->lamp_ktp,
                'lamp_buku_nikah'  => $val->lamp_buku_nikah
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

    public function update($id, PasanganRequest $req){
        $check_pas = Pasangan::where('id', $id)->first();

        if ($check_pas == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Pasangan Kosong'
            ], 404);
        }

        $so = TransSO::where('id_pasangan', $id)->first();

        if ($so == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Transaksi SO Kosong'
            ], 404);
        }

        
        /** Check Lampiran */
        $check_lamp_ktp_pas        = $check_pas->lamp_ktp;
        $check_lamp_buku_nikah_pas = $check_pas->lamp_buku_nikah;
        /** */
        $path  = 'public/' . $so->debt['no_ktp'] . '/penjamin';

        // Lampiran Pasangan
        if($file = $req->file('lamp_ktp_pas')){
            $name = 'ktp.';
            $check = $check_lamp_ktp_pas;
            
            $ktpPass = Helper::uploadImg($check, $file, $path, $name);
        }else{
            $ktpPass = $check_lamp_ktp_pas;
        }

        if($file = $req->file('lamp_buku_nikah_pas')){
            $name = 'buku_nikah.';
            $check = $check_lamp_buku_nikah_pas;
            
            $bukuNikahPass = Helper::uploadImg($check, $file, $path, $name);
        }else{
            $bukuNikahPass = $check_lamp_buku_nikah_pas;
        }

        // Data Usaha Calon Debitur
        // Pasangan Lama
        $dataPasangan = array(
            'nama_lengkap'     => empty($req->input('nama_lengkap_pas')) 
                ? $check_pas->nama_lengkap : $req->input('nama_lengkap_pas'),

            'nama_ibu_kandung' => empty($req->input('nama_ibu_kandung_pas')) 
                ? $check_pas->nama_ibu_kandung : $req->input('nama_ibu_kandung_pas'),

            'jenis_kelamin'    => empty($req->input('jenis_kelamin_pas')) 
                ? strtoupper($check_pas->jenis_kelamin) : strtoupper($req->input('jenis_kelamin_pas')),

            'no_ktp'           => empty($req->input('no_ktp_pas')) 
                ? $check_pas->no_ktp : $req->input('no_ktp_pas'),

            'no_ktp_kk'        => empty($req->input('no_ktp_kk_pas')) 
                ? $check_pas->no_ktp_kk : $req->input('no_ktp_kk_pas'),

            'no_npwp'          => empty($req->input('no_npwp_pas')) 
                ? $check_pas->no_npwp : $req->input('no_npwp_pas'),

            'tempat_lahir'     => empty($req->input('tempat_lahir_pas')) 
                ? $check_pas->tempat_lahir : $req->input('tempat_lahir_pas'),

            'tgl_lahir'        => empty($req->input('tgl_lahir_pas')) 
                ? $check_pas->tgl_lahir : Carbon::parse($req->input('tgl_lahir_pas'))->format('Y-m-d'),

            'alamat_ktp'       => empty($req->input('alamat_ktp_pas')) 
                ? $check_pas->alamat_ktp : $req->input('alamat_ktp_pas'),

            'no_telp'          => empty($req->input('no_telp_pas')) 
                ? $check_pas->no_telp : $req->input('no_telp_pas'),
                
            'lamp_ktp'         => $ktpPass,
            'lamp_buku_nikah'  => $bukuNikahPass,

            // Pasangan Baru
            'pekerjaan'             => $req->input('pekerjaan_pas'),
            'posisi_pekerjaan'      => $req->input('posisi_pekerjaan_pas'),
            'nama_tempat_kerja'     => $req->input('nama_tempat_kerja_pas'),
            'alamat_tempat_kerja'   => $req->input('alamat_tempat_kerja_pas'),
            'jenis_pekerjaan'       => $req->input('jenis_pekerjaan_pas'),
            'id_prov_tempat_kerja'  => $req->input('id_prov_tempat_kerja_pas'),
            'id_kab_tempat_kerja'   => $req->input('id_kab_tempat_kerja_pas'),
            'id_kec_tempat_kerja'   => $req->input('id_kec_tempat_kerja_pas'),
            'id_kel_tempat_kerja'   => $req->input('id_kel_tempat_kerja_pas'),
            'rt_tempat_kerja'       => $req->input('rt_tempat_kerja_pas'),
            'rw_tempat_kerja'       => $req->input('rw_tempat_kerja_pas'),
            'tgl_mulai_kerja'       => $req->input('tgl_mulai_kerja_pas'), //Carbon::parse($req->input('tgl_mulai_kerja_pas'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => $req->input('no_telp_tempat_kerja_pas')
        );

        DB::connection('web')->beginTransaction();

        try {

            Pasangan::where('id', $id)->update($dataPasangan);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Update Pasangan Berhasil',
                'data'   => $dataPasangan
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
