<?php

namespace App\Http\Controllers\Pengajuan\Tunggal;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;

// Form Request
use App\Http\Requests\Pengajuan\DebiturRequest;

// Models
use App\Models\Pengajuan\SO\Debitur;
// use App\Models\Transaksi\TransSO;

// use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DebiturController extends BaseController
{

    public function show($id)
    {
        $val = Debitur::with('prov_ktp', 'kab_ktp', 'kec_ktp', 'kel_ktp', 'prov_dom', 'kab_dom', 'kec_dom', 'kel_dom', 'prov_kerja', 'kab_kerja', 'kec_kerja', 'kel_kerja')
            ->where('id', $id)->first();

        if (empty($val)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Debitur Kosong'
            ], 404);
        }

        $nama_anak = explode(",", $val->nama_anak);
        $tgl_anak  = explode(",", $val->tgl_lahir_anak);

        for ($i = 0; $i < count($nama_anak); $i++) {
            $anak[] = array(
                'nama'      => $nama_anak[$i],
                'tgl_lahir' => empty($tgl_anak[$i]) ? null : Carbon::parse($tgl_anak[$i])->format("d-m-Y")
            );
        }

        $data = array(
            'id'                    => $val->id == null ? null : (int) $val->id,
            'nama_lengkap'          => $val->nama_lengkap,
            'gelar_keagamaan'       => $val->gelar_keagamaan,
            'gelar_pendidikan'      => $val->gelar_pendidikan,
            'jenis_kelamin'         => $val->jenis_kelamin,
            'status_nikah'          => $val->status_nikah,
            'ibu_kandung'           => $val->ibu_kandung,
            'tinggi_badan'          => $val->tinggi_badan,
            'berat_badan'           => $val->berat_badan,
            'no_ktp'                => $val->no_ktp,
            'no_ktp_kk'             => $val->no_ktp_kk,
            'no_kk'                 => $val->no_kk,
            'no_npwp'               => $val->no_npwp,
            'tempat_lahir'          => $val->tempat_lahir,
            'tgl_lahir'             => Carbon::parse($val->tgl_lahir)->format('d-m-Y'),
            'agama'                 => $val->agama,
            'anak'                  => $anak,
            'alamat_ktp' => [
                'alamat_singkat' => $val->alamat_ktp,
                'rt'     => $val->rt_ktp == null ? null : (int) $val->rt_ktp,
                'rw'     => $val->rw_ktp == null ? null : (int) $val->rw_ktp,
                'kelurahan' => [
                    'id'    => $val->id_kel_ktp == null ? null : (int) $val->id_kel_ktp,
                    'nama'  => $val->kel_ktp['nama']
                ],
                'kecamatan' => [
                    'id'    => $val->id_kec_ktp == null ? null : (int) $val->id_kec_ktp,
                    'nama'  => $val->kec_ktp['nama']
                ],
                'kabupaten' => [
                    'id'    => $val->id_kab_ktp == null ? null : (int) $val->id_kab_ktp,
                    'nama'  => $val->kab_ktp['nama'],
                ],
                'provinsi'  => [
                    'id'   => $val->id_prov_ktp == null ? null : (int) $val->id_prov_ktp,
                    'nama' => $val->prov_ktp['nama'],
                ],
                'kode_pos' => $val->kel_ktp['kode_pos'] == null ? null : (int) $val->kel_ktp['kode_pos']
            ],
            'alamat_domisili' => [
                'alamat_singkat' => $val->alamat_domisili,
                'rt'             => $val->rt_domisili == null ? null : (int) $val->rt_domisili,
                'rw'             => $val->rw_domisili == null ? null : (int) $val->rw_domisili,
                'kelurahan' => [
                    'id'    => $val->id_kel_domisili == null ? null : (int) $val->id_kel_domisili,
                    'nama'  => $val->kel_dom['nama']
                ],
                'kecamatan' => [
                    'id'    => $val->id_kec_domisili == null ? null : (int) $val->id_kec_domisili,
                    'nama'  => $val->kec_dom['nama']
                ],
                'kabupaten' => [
                    'id'    => $val->id_kab_domisili == null ? null : (int) $val->id_kab_domisili,
                    'nama'  => $val->kab_dom['nama'],
                ],
                'provinsi'  => [
                    'id'   => $val->id_prov_domisili == null ? null : (int) $val->id_prov_domisili,
                    'nama' => $val->prov_dom['nama'],
                ],
                'kode_pos' => $val->kel_dom['kode_pos'] == null ? null : (int) $val->kel_dom['kode_pos']
            ],
            "pekerjaan" => [
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
                        'id'    => $val->id_prov_tempat_kerja == null ? null : (int) $val->id_prov_tempat_kerja,
                        'nama'  => $val->prov_kerja['nama'],
                    ],
                    'kode_pos'  => $val->kel_kerja['kode_pos'] == null ? null : (int) $val->kel_kerja['kode_pos']
                ]
            ],
            'pendidikan_terakhir'   => $val->pendidikan_terakhir,
            'jumlah_tanggungan'     => $val->jumlah_tanggungan,
            'no_telp'               => $val->no_telp,
            'no_hp'                 => $val->no_hp,
            'alamat_surat'          => $val->alamat_surat,
            'lampiran' => [
                'lamp_ktp'              => $val->lamp_ktp,
                'lamp_kk'               => $val->lamp_kk,
                'lamp_buku_tabungan'    => $val->lamp_buku_tabungan,
                'lamp_sertifikat'       => $val->lamp_sertifikat,
                'lamp_sttp_pbb'         => $val->lamp_sttp_pbb,
                'lamp_imb'              => $val->lamp_imb,
                'foto_agunan_rumah'     => $val->foto_agunan_rumah
            ]
        );

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, DebiturRequest $req)
    {
        $check_debt = Debitur::where('id', $id)->first();

        if (empty($check_debt)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data Debitur Kosong'
            ], 404);
        }

        $check_lamp_ktp             = $check_debt->lamp_ktp;
        $check_lamp_kk              = $check_debt->lamp_kk;
        $check_lamp_sertifikat      = $check_debt->lamp_sertifikat;
        $check_lamp_sttp_pbb        = $check_debt->lamp_sttp_pbb;
        $check_lamp_imb             = $check_debt->lamp_imb;
        $check_foto_agunan_rumah    = $check_debt->foto_agunan_rumah;
        $check_lamp_buku_tabungan   = $check_debt->lamp_buku_tabungan;
        $check_lamp_skk             = $check_debt->lamp_skk;
        $check_lamp_sku             = $check_debt->lamp_sku;
        $check_lamp_slip_gaji       = $check_debt->lamp_slip_gaji;
        $check_foto_pembukuan_usaha = $check_debt->foto_pembukuan_usaha;
        $check_lamp_foto_usaha      = $check_debt->lamp_foto_usaha;
        $check_lamp_surat_cerai     = $check_debt->lamp_surat_cerai;
        $check_lamp_tempat_tinggal  = $check_debt->lamp_tempat_tinggal;

        $path = 'public/' . $check_debt->no_ktp . '/debitur';

        // Lampiran Debitur
        if ($file = $req->file('lamp_ktp')) {
            $name = 'ktp.';
            $check = $check_lamp_ktp;

            $lamp_ktp = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_ktp = $check_lamp_ktp;
        }

        if ($file = $req->file('lamp_kk')) {
            $name = 'kk.';
            $check = $check_lamp_kk;

            $lamp_kk = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_kk = $check_lamp_kk;
        }

        if ($file = $req->file('lamp_sertifikat')) {
            $name = 'sertifikat.';
            $check = $check_lamp_sertifikat;

            $lamp_sertifikat = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }

        if ($file = $req->file('lamp_pbb')) {
            $name = 'pbb.';
            $check = $check_lamp_sttp_pbb;

            $lamp_sttp_pbb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_sttp_pbb = $check_lamp_sttp_pbb;
        }

        if ($file = $req->file('lamp_imb')) {
            $name = 'imb.';
            $check = $check_lamp_imb;

            $lamp_imb = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_imb = $check_lamp_imb;
        }

        if ($file = $req->file('foto_agunan_rumah')) {
            $name = 'foto_agunan_rumah.';
            $check = $check_foto_agunan_rumah;

            $foto_agunan_rumah = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $foto_agunan_rumah = $check_foto_agunan_rumah;
        }

        if ($file = $req->file('lamp_skk')) {
            $name = 'lamp_skk.';
            $check = $check_lamp_skk;

            $lamp_skk = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_skk = $check_lamp_skk;
        }

        if ($file = $req->file('lamp_slip_gaji')) {
            $name = 'lamp_slip_gaji.';
            $check = $check_lamp_slip_gaji;

            $lamp_slip_gaji = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_slip_gaji = $check_lamp_slip_gaji;
        }

        if ($file = $req->file('lamp_surat_cerai')) {
            $name = 'lamp_surat_cerai.';
            $check = $check_lamp_surat_cerai;

            $lamp_surat_cerai = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_surat_cerai = $check_lamp_surat_cerai;
        }

        if ($file = $req->file('lamp_tempat_tinggal')) {
            $name = 'lamp_tempat_tinggal.';
            $check = $check_lamp_tempat_tinggal;

            $lamp_tempat_tinggal = Helper::uploadImg($check, $file, $path, $name);
        } else {
            $lamp_tempat_tinggal = $check_lamp_tempat_tinggal;
        }

        if ($files = $req->file('lamp_buku_tabungan')) {
            $name = 'lamp_buku_tabungan.';
            $check = $check_lamp_buku_tabungan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }

            $lamp_buku_tabungan = implode(";", $arrayPath);
        } else {
            $lamp_buku_tabungan = $check_lamp_buku_tabungan;
        }

        if ($files = $req->file('lamp_sku')) {
            $name = 'lamp_sku.';
            $check = $check_lamp_sku;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }

            $lamp_sku = implode(";", $arrayPath);
        } else {
            $lamp_sku = $check_lamp_sku;
        }

        if ($files = $req->file('foto_pembukuan_usaha')) {
            $name = 'foto_pembukuan_usaha.';
            $check = $check_foto_pembukuan_usaha;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }

            $foto_pembukuan_usaha = implode(";", $arrayPath);
        } else {
            $foto_pembukuan_usaha = $check_foto_pembukuan_usaha;
        }

        if ($files = $req->file('lamp_foto_usaha')) {
            $name = 'lamp_foto_usaha.';
            $check = $check_lamp_foto_usaha;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check, $file, $path, $name);
            }

            $lamp_foto_usaha = implode(";", $arrayPath);
        } else {
            $lamp_foto_usaha = $check_lamp_foto_usaha;
        }

        /** Nama dan Tgl Lahir Anak */
        if (empty($req->input('nama_anak'))) {
            $nama_anak = $check_debt->nama_anak;
            $tgl_lhr_anak = $check_debt->tgl_lhr_anak;
        } else {
            for ($i = 0; $i < count($req->input('nama_anak')); $i++) {
                $namaAnak[] = empty($req->nama_anak[$i]) ? $check_debt->nama_anak[$i] : $req->nama_anak[$i];
            }

            for ($i = 0; $i < count($req->input('tgl_lahir_anak')); $i++) {
                $tglLahirAnak[] = empty($req->tgl_lahir_anak[$i]) ? $check_debt->tgl_lahir_anak[$i] : Carbon::parse($req->tgl_lahir_anak[$i])->format('Y-m-d');
            }

            $nama_anak    = implode(",", $namaAnak);
            $tgl_lhr_anak = implode(",", $tglLahirAnak);
        }
        /** */

        // dd($tgl_lhr_anak);
        // Data Debitur
        $dataDebitur = array(
            'nama_lengkap'          => empty($req->input('nama_lengkap'))
                ? $check_debt->nama_lengkap : $req->input('nama_lengkap'),

            'gelar_keagamaan'       => empty($req->input('gelar_keagamaan'))
                ? $check_debt->gelar_keagamaan : $req->input('gelar_keagamaan'),

            'gelar_pendidikan'      => empty($req->input('gelar_pendidikan'))
                ? $check_debt->gelar_pendidikan : $req->input('gelar_pendidikan'),

            'jenis_kelamin'         => empty($req->input('jenis_kelamin'))
                ? strtoupper($check_debt->jenis_kelamin) : strtoupper($req->input('jenis_kelamin')),

            'status_nikah'          => empty($req->input('status_nikah'))
                ? strtoupper($check_debt->status_nikah) : $req->input('status_nikah'),

            'ibu_kandung'           => empty($req->input('ibu_kandung'))
                ? $check_debt->ibu_kandung : $req->input('ibu_kandung'),

            'no_ktp'                => empty($req->input('no_ktp'))
                ? $check_debt->no_ktp : $req->input('no_ktp'),

            'no_ktp_kk'             => empty($req->input('no_ktp_kk'))
                ? $check_debt->no_ktp_kk : $req->input('no_ktp_kk'),

            'no_kk'                 => empty($req->input('no_kk'))
                ? $check_debt->no_kk : $req->input('no_kk'),

            'no_npwp'               => empty($req->input('no_npwp'))
                ? $check_debt->no_npwp : $req->input('no_npwp'),

            'tempat_lahir'          => empty($req->input('tempat_lahir'))
                ? $check_debt->tempat_lahir : $req->input('tempat_lahir'),

            'tgl_lahir'             => empty($req->input('tgl_lahir'))
                ? $check_debt->tgl_lahir : Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),

            'agama'                 => empty($req->input('agama'))
                ? $check_debt->agama : strtoupper($req->input('agama')),

            'alamat_ktp'            => empty($req->input('alamat_ktp'))
                ? $check_debt->alamat_ktp : $req->input('alamat_ktp'),

            'rt_ktp'                => empty($req->input('rt_ktp'))
                ? $check_debt->rt_ktp : $req->input('rt_ktp'),

            'rw_ktp'                => empty($req->input('rw_ktp'))
                ? $check_debt->rw_ktp : $req->input('rw_ktp'),

            'id_prov_ktp'           => empty($req->input('id_prov_ktp'))
                ? $check_debt->id_prov_ktp : $req->input('id_prov_ktp'),

            'id_kab_ktp'            => empty($req->input('id_kab_ktp'))
                ? $check_debt->id_kab_ktp : $req->input('id_kab_ktp'),

            'id_kec_ktp'            => empty($req->input('id_kec_ktp'))
                ? $check_debt->id_kec_ktp : $req->input('id_kec_ktp'),

            'id_kel_ktp'            => empty($req->input('id_kel_ktp'))
                ? $check_debt->id_kel_ktp : $req->input('id_kel_ktp'),

            'alamat_domisili'       => empty($req->input('alamat_domisili'))
                ? $check_debt->alamat_domisili : $req->input('alamat_domisili'),

            'rt_domisili'           => empty($req->input('rt_domisili'))
                ? $check_debt->rt_domisili : $req->input('rt_domisili'),

            'rw_domisili'           => empty($req->input('rw_domisili'))
                ? $check_debt->rw_domisili : $req->input('rw_domisili'),

            'id_prov_domisili'      => empty($req->input('id_prov_domisili'))
                ? $check_debt->id_prov_domisili : $req->input('id_prov_domisili'),

            'id_kab_domisili'       => empty($req->input('id_kab_domisili'))
                ? $check_debt->id_kab_domisili : $req->input('id_kab_domisili'),

            'id_kec_domisili'       => empty($req->input('id_kec_domisili'))
                ? $check_debt->id_kec_domisili : $req->input('id_kec_domisili'),

            'id_kel_domisili'       => empty($req->input('id_kel_domisili'))
                ? $check_debt->id_kel_domisili : $req->input('id_kel_domisili'),

            'pendidikan_terakhir'   => empty($req->input('pendidikan_terakhir'))
                ? $check_debt->pendidikan_terakhir : $req->input('pendidikan_terakhir'),

            'jumlah_tanggungan'     => empty($req->input('jumlah_tanggungan'))
                ? $check_debt->jumlah_tanggungan : $req->input('jumlah_tanggungan'),

            'no_telp'               => empty($req->input('no_telp'))
                ? $check_debt->no_telp : $req->input('no_telp'),

            'no_hp'                 => empty($req->input('no_hp'))
                ? $check_debt->no_hp : $req->input('no_hp'),

            'alamat_surat'          => empty($req->input('alamat_surat'))
                ? $check_debt->alamat_surat : $req->input('alamat_surat'),

            'tinggi_badan'          => empty($req->input('tinggi_badan'))
                ? $check_debt->tinggi_badan : $req->input('tinggi_badan'),

            'berat_badan'           => empty($req->input('berat_badan'))
                ? $check_debt->berat_badan : $req->input('berat_badan'),

            'nama_anak'             => $nama_anak,
            'tgl_lahir_anak'        =>  $tgl_lhr_anak == null ? $check_debt->tgl_lahir_anak : implode(",", $req->input('tgl_lahir_anak')),

            'pekerjaan'             => empty($req->input('pekerjaan'))
                ? $check_debt->pekerjaan : $req->input('pekerjaan'),

            'posisi_pekerjaan'      => empty($req->input('posisi_pekerjaan'))
                ? $check_debt->posisi_pekerjaan : $req->input('posisi_pekerjaan'),

            'nama_tempat_kerja'     => empty($req->input('nama_tempat_kerja'))
                ? $check_debt->nama_tempat_kerja : $req->input('nama_tempat_kerja'),

            'jenis_pekerjaan'       => empty($req->input('jenis_pekerjaan'))
                ? $check_debt->jenis_pekerjaan : $req->input('jenis_pekerjaan'),

            'alamat_tempat_kerja'   => empty($req->input('alamat_tempat_kerja'))
                ? $check_debt->alamat_tempat_kerja : $req->input('alamat_tempat_kerja'),

            'id_prov_tempat_kerja'  => empty($req->input('id_prov_tempat_kerja'))
                ? $check_debt->id_prov_tempat_kerja : $req->input('id_prov_tempat_kerja'),

            'id_kab_tempat_kerja'   => empty($req->input('id_kab_tempat_kerja'))
                ? $check_debt->id_kab_tempat_kerja : $req->input('id_kab_tempat_kerja'),

            'id_kec_tempat_kerja'   => empty($req->input('id_kec_tempat_kerja'))
                ? $check_debt->id_kec_tempat_kerja : $req->input('id_kec_tempat_kerja'),

            'id_kel_tempat_kerja'   => empty($req->input('id_kel_tempat_kerja'))
                ? $check_debt->id_kel_tempat_kerja : $req->input('id_kel_tempat_kerja'),

            'rt_tempat_kerja'       => empty($req->input('rt_tempat_kerja')) ?
                $check_debt->rt_tempat_usaha : $req->input('rt_tempat_kerja'),

            'rw_tempat_kerja'       => empty($req->input('rw_tempat_kerja')) ?
                $check_debt->rw_tempat_usaha : $req->input('rw_tempat_kerja'),

            'tgl_mulai_kerja'       => empty($req->input('tgl_mulai_kerja'))
                ? $check_debt->tgl_mulai_kerja : Carbon::parse($req->input('tgl_mulai_kerja'))->format('Y-m-d'),

            'no_telp_tempat_kerja'  => empty($req->input('no_telp_tempat_kerja'))
                ? $check_debt->no_telp_tempat_kerja : $req->input('no_telp_tempat_kerja'),

            'lamp_ktp'              => $lamp_ktp,
            'lamp_kk'               => $lamp_kk,
            'lamp_sertifikat'       => $lamp_sertifikat,
            'lamp_sttp_pbb'         => $lamp_sttp_pbb,
            'lamp_imb'              => $lamp_imb,
            'lamp_buku_tabungan'    => $lamp_buku_tabungan,
            'lamp_skk'              => $lamp_skk,
            'lamp_sku'              => $lamp_sku,
            'lamp_slip_gaji'        => $lamp_slip_gaji,
            'foto_pembukuan_usaha'  => $foto_pembukuan_usaha,
            'lamp_foto_usaha'       => $lamp_foto_usaha,
            'foto_agunan_rumah'     => $foto_agunan_rumah,
            'lamp_surat_cerai'      => $lamp_surat_cerai,
            'lamp_tempat_tinggal'   => $lamp_tempat_tinggal
        );

        DB::connection('web')->beginTransaction();

        try {

            Debitur::where('id', $id)->update($dataDebitur);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Update Debitur Berhasil',
                'data'   => $dataDebitur
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
