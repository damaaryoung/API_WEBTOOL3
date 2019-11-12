<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Requests\Debt\DebtPenjaminRequest;
use App\Http\Requests\Debt\DebtPasanganRequest;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Debt\FasPinRequest;
use App\Http\Requests\Debt\UsahaRequest;
use App\Http\Requests\Debt\DebtRequest;
use App\Models\CC\FasilitasPinjaman;
use Illuminate\Http\Request;
use App\Models\CC\Pasangan;
use App\Models\CC\Penjamin;
use App\Models\CC\MasterCC;
use App\Models\CC\Debitur;
use App\Models\CC\Usaha;
use App\Http\Requests;
use Carbon\Carbon;
use Validator;
use DB;

class MasterCC_Controller extends BaseController
{
    public function store(FasPinRequest $reqFasPin, DebtRequest $req, DebtPasanganRequest $reqPas, DebtPenjaminRequest $reqPen, UsahaRequest $reqUs) {
        $dataFasPin = array(
            'nomor_so'        => $reqFasPin->input('nomor_so'),
            'jenis_pinjaman'  => $reqFasPin->input('jenis_pinjaman'),
            'tujuan_pinjaman' => $reqFasPin->input('tujuan_pinjaman'),
            'plafon'          => $reqFasPin->input('plafon'),
            'tenor'           => $reqFasPin->input('tenor')
        );

        $dataDebitur = array(
            'nama_lengkap'          => $req->input('nama_lengkap'),
            'jenis_kelamin'         => $req->input('jenis_kelamin'),
            'status_nikah'          => $req->input('status_nikah'),
            'ibu_kandung'           => $req->input('ibu_kandung'),
            'gelar_keagamaan'       => $req->input('gelar_keagamaan'),
            'gelar_pendidikan'      => $req->input('gelar_pendidikan'),
            'no_ktp'                => $req->input('no_ktp'),
            'no_ktp_kk'             => $req->input('no_ktp_kk'),
            'no_kk'                 => $req->input('no_kk'),
            'no_npwp'               => $req->input('no_npwp'),
            'tempat_lahir'          => $req->input('tempat_lahir'),
            'tgl_lahir'             => Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),
            'agama'                 => $req->input('agama'),
            'alamat_ktp'            => $req->input('alamat_ktp'),
            'rt_ktp'                => $req->input('rt_ktp'),
            'rw_ktp'                => $req->input('rw_ktp'),
            'id_provinsi_ktp'       => $req->input('id_provinsi_ktp'),
            'id_kabupaten_ktp'      => $req->input('id_kabupaten_ktp'),
            'id_kecamatan_ktp'      => $req->input('id_kecamatan_ktp'),
            'id_kelurahan_ktp'      => $req->input('id_kelurahan_ktp'),
            'alamat_domisili'       => $req->input('alamat_domisili'),
            'rt_domisili'           => $req->input('rt_domisili'),
            'rw_domisili'           => $req->input('rw_domisili'),
            'id_provinsi_domisili'  => $req->input('id_provinsi_domisili'),
            'id_kabupaten_domisili' => $req->input('id_kabupaten_domisili'),
            'id_kecamatan_domisili' => $req->input('id_kecamatan_domisili'),
            'id_kelurahan_domisili' => $req->input('id_kelurahan_domisili'),
            'pendidikan_terakhir'   => $req->input('pendidikan_terakhir'),
            'jumlah_tanggungan'     => $req->input('jumlah_tanggungan'),
            'no_telp'               => $req->input('no_telp'),
            'alamat_surat'          => $req->input('alamat_surat'),
            'nama_anak1'            => $req->input('nama_anak1'),
            'tgl_lahir_anak1'       => Carbon::parse($req->input('tgl_lahir_anak1'))->format('Y-m-d'),
            'nama_anak2'            => $req->input('nama_anak2'),
            'tgl_lahir_anak2'       => Carbon::parse($req->input('tgl_lahir_anak2'))->format('Y-m-d'),
            'pekerjaan'             => $req->input('pekerjaan')
            // 'verifikasi'            => $req->input('verifikasi')
        );

        if ($req->hasFile('lamp_surat_cerai')) {
            $file = $req->file('lamp_surat_cerai');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp']);
            $name = 'debt.suratcerai.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataDebitur['lamp_surat_cerai'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/'.$name;
        }

        if ($req->hasFile('lamp_buku_tabungan')) {
            $file = $req->file('lamp_buku_tabungan');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp']);
            $name = 'bukutabungan.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataDebitur['lamp_buku_tabungan'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/'.$name;
        }

        if ($req->hasFile('lamp_kk')) {
            $file = $req->file('lamp_kk');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp']);
            $name = 'kk.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataDebitur['lamp_kk'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/'.$name;
        }

        $dataPenjamin = array(
            'nama_ktp'          => $reqPen->input('nama_ktp_pen'),
            'nama_ibu_kandung'  => $reqPen->input('nama_ibu_kandung_pen'),
            'no_ktp'            => $reqPen->input('no_ktp_pen'),
            'no_npwp'           => $reqPen->input('no_npwp_pen'),
            'tempat_lahir'      => $reqPen->input('tempat_lahir_pen'),
            'tgl_lahir'         => Carbon::parse($reqPen->input('tgl_lahir_pen'))->format('Y-m-d'),
            'jenis_kelamin'     => $reqPen->input('jenis_kelamin_pen'),
            'alamat_ktp'        => $reqPen->input('alamat_ktp_pen'),
            'no_telp'           => $reqPen->input('no_telp_pen'),
            'hubungan_debitur'  => $reqPen->input('hubungan_debitur_pen'),
            'pendapatan'        => $reqPen->input('pendapatan_pen')
        );

        if ($reqPen->hasFile('lamp_ktp_pen')) {
            $file = $reqPen->file('lamp_ktp_pen');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin');
            $name = 'ktp.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataPenjamin['lamp_ktp'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin/'.$name;
        }

        if ($reqPen->hasFile('lamp_ktp_pasangan_pen')) {
            $file = $reqPen->file('lamp_ktp_pasangan_pen');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin');
            $name = 'ktppasangan.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataPenjamin['lamp_ktp_pasangan'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin/'.$name;
        }

        if ($reqPen->hasFile('lamp_kk_pen')) {
            $file = $reqPen->file('lamp_kk_pen');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin');
            $name = 'kk.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataPenjamin['lamp_kk'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin/'.$name;
        }

        if ($reqPen->hasFile('lamp_buku_nikah_pen')) {
            $file = $reqPen->file('lamp_buku_nikah_pen');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin');
            $name = 'bukunikah.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataPenjamin['lamp_buku_nikah'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/penjamin/'.$name;
        }

        $dataPasangan = array(
            'nama_lengkap'     => $reqPas->input('nama_lengkap_pas'),
            'nama_ibu_kandung' => $reqPas->input('nama_ibu_kandung_pas'),
            'jenis_kelamin'    => $reqPas->input('jenis_kelamin_pas'),
            'no_ktp'           => $reqPas->input('no_ktp_pas'),
            'no_ktp_kk'        => $reqPas->input('no_ktp_kk_pas'),
            'no_ktp'           => $reqPas->input('no_kk_pas'),
            'no_npwp'          => $reqPas->input('no_npwp_pas'),
            'tempat_lahir'     => $reqPas->input('tempat_lahir_pas'),
            'tgl_lahir'        => Carbon::parse($reqPas->input('tgl_lahir_pas'))->format('Y-m-d'),
            'alamat_ktp'       => $reqPas->input('alamat_ktp_pas'),
            'no_telp'          => $reqPas->input('no_telp_pas'),
            'pendapatan'       => $reqPas->input('pendapatan_pas')
            // 'verifikasi'       => $req->input('')
        );

        if ($reqPas->hasFile('lamp_ktp_pas')) {
            $file = $reqPas->file('lamp_ktp_pas');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/pasangan');
            $name = 'ktp.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataPasangan['lamp_ktp'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/pasangan/'.$name;
        }

        if ($reqPas->hasFile('lamp_kk_pas')) {
            $file = $reqPas->file('lamp_kk_pas');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/pasangan');
            $name = 'kk.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataPasangan['lamp_kk'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/pasangan/'.$name;
        }

        $dataUsaha = array(
            'nama_tempat_usaha'     => $reqUs->input('nama_tempat_usaha'),
            'jenis_usaha'           => $reqUs->input('jenis_usaha'),
            'alamat'                => $reqUs->input('alamat_usaha'),
            'tunai'                 => $reqUs->input('tunai_usaha'),
            'kredit'                => $reqUs->input('kredit_usaha'),
            'biaya_sewa'            => $reqUs->input('biaya_sewa'),
            'gaji_pegawai'          => $reqUs->input('gaji_pegawai'),
            'belanja_brg'           => $reqUs->input('belanja_brg'),
            'telp-listr-air'        => $reqUs->input('telp-listr-air'),
            'sampah-kemanan'        => $reqUs->input('sampah-kemanan'),
            'biaya_ongkir'          => $reqUs->input('biaya_ongkir'),
            'hutang_dagang'         => $reqUs->input('hutang_dagang'),
            'lain_lain'             => $reqUs->input('lain_lain'),
            'laba'                  => $reqUs->input('laba'),
            // 'lamp_surat_ket_usaha'  => $reqUs->input('lamp_surat_ket_usaha'),
            // 'lamp_pembukuan_usaha'  => $reqUs->input('lamp_pembukuan_usaha'),
            // 'lamp_rek_tabungan'     => $reqUs->input('lamp_rek_tabungan'),
            // 'lamp_persetujuan_ideb' => $reqUs->input('lamp_persetujuan_ideb'),
            'lamp_tempat_usaha'     => $reqUs->input('lamp_tempat_usaha'),
            'lama_usaha'            => $reqUs->input('lama_usaha'),
            'telp_tempat_usaha'     => $reqUs->input('telp_tempat_usaha')
        );

        if ($reqUs->hasFile('lamp_surat_ket_usaha')) {
            $file = $reqUs->file('lamp_surat_ket_usaha');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha');
            $name = 'SKU.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataUsaha['lamp_surat_ket_usaha'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha/'.$name;
        }

        if ($reqUs->hasFile('lamp_pembukuan_usaha')) {
            $file = $reqUs->file('lamp_pembukuan_usaha');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha');
            $name = 'pembukuan.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataUsaha['lamp_pembukuan_usaha'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha/'.$name;
        }

        if ($reqUs->hasFile('lamp_rek_tabungan')) {
            $file = $reqUs->file('lamp_rek_tabungan');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha');
            $name = 'rektabungan.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataUsaha['lamp_rek_tabungan'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha/'.$name;
        }

        if ($reqUs->hasFile('lamp_persetujuan_ideb')) {
            $file = $reqUs->file('lamp_persetujuan_ideb');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha');
            $name = 'ijinideb.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataUsaha['lamp_persetujuan_ideb'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha/'.$name;
        }

        if ($reqUs->hasFile('lamp_tempat_usaha')) {
            $file = $reqUs->file('lamp_tempat_usaha');
            $path = base_path('uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha');
            $name = 'tempat.'.$file->getClientOriginalExtension();
            $file->move($path, $name);

            $dataUsaha['lamp_tempat_usaha'] = 'uploads/debiturs/'.$dataDebitur['no_ktp'].'/usaha/'.$name;
        }

        try {
            $debt = Debitur::create($dataDebitur);
            $id_debt = $debt->id;

            $arrIdDebt = array('id_calon_debitur' => $id_debt);
            $newFasPin = array_merge($arrIdDebt, $dataFasPin);

            $fasPin = FasilitasPinjaman::create($newFasPin);
            $id_faspin = $fasPin->id;

            if ($dataDebitur['pekerjaan'] == 'usaha') {
                $newUsaha = array_merge($arrIdDebt, $dataUsaha);
                $usaha    = Usaha::create($newUsaha);
                $id_usaha = $usaha->id;
            }else{
                $id_usaha = null;
            }

            if ($dataDebitur['status_nikah'] == 'NIKAH') {
                $newPas      = array_merge($arrIdDebt, $dataPasangan);
                $pasangan    = Pasangan::create($newPas);
                $id_pasangan = $pasangan->id;
            }else{
                $id_pasangan = null;
            }

            if (!$dataPenjamin) {
                $id_penjamin = null;
            }else{
                $newPenj     = array_merge($arrIdDebt, $dataPenjamin);
                $penjamin    = Penjamin::create($newPenj);
                $id_penjamin = $penjamin->id;
            }

            $masterCC = MasterCC::create([
                'id_fasilitas_pinjaman'       => $id_faspin,
                'id_calon_debt'               => $id_debt,
                'id_pasangan'                 => $id_pasangan,
                'id_penjamin'                 => $id_penjamin,
                'id_verifikasi'               => null,
                'id_validasi'                 => null,
                'id_agunan_tanah'             => null,
                'id_agunan_kendarran'         => null,
                'id_periksa_agunan_tanah'     => null,
                'id_periksa_agunan_kendarran' => null,
                'id_usaha'                    => $id_usaha,
                'id_recomendasi_ao'           => null
            ]);

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data berhasil dibuat'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'code'    => 501,
                'status'  => 'error',
                'message' => $e
            ], 501);
        }
    }
}
