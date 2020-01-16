<?php

namespace App\Http\Requests\Transaksi;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Transaksi\TransSO;
use Illuminate\Http\JsonResponse;
use Urameshibr\Requests\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class BlankRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(Request $request)
    {
        $single = $request->segment(4);

        // if Update
        if (!empty($single)) {
            $trans = TransSO::where('id', $single)->first();

            if ($trans != null) {
                if ($trans->id_penjamin != null) {
                    $id_penj = explode (",",$trans->id_penjamin);

                    for ($i = 0; $i < count($id_penj); $i++) {
                        $rules['no_ktp_pen.'.$i]  = 'digits:16|unique:web.penjamin_calon_debitur,no_ktp,' . $id_penj[$i];
                        $rules['no_npwp_pen.'.$i] = 'digits:15|unique:web.penjamin_calon_debitur,no_npwp,' . $id_penj[$i];
                        $rules['no_telp_pen.'.$i] = 'between:11,13|unique:web.penjamin_calon_debitur,no_telp,' . $id_penj[$i];
                    }
                }

                if ($trans->id_pasangan != null) {
                    $rules['no_ktp_pas']    = 'digits:16|unique:web.pasangan_calon_debitur,no_ktp,'.$trans->id_pasangan;
                    $rules['no_ktp_kk_pas'] = 'digits:16|unique:web.pasangan_calon_debitur,no_ktp_kk,'.$trans->id_pasangan;
                    $rules['no_kk_pas']     = 'digits:16|unique:web.pasangan_calon_debitur,no_kk,'.$trans->id_pasangan;
                    $rules['no_npwp_pas']   = 'digits:15|unique:web.pasangan_calon_debitur,no_npwp,'.$trans->id_pasangan;
                    $rules['tgl_lahir_pas'] = 'date_format:d-m-Y';
                    $rules['no_telp_pas']   = 'between:11,13|unique:web.pasangan_calon_debitur,no_telp,'.$trans->id_pasangan;
                }

                if ($trans->id_calon_debt != null) {
                    $rules['no_ktp']    = 'digits:16|unique:web.calon_debitur,no_ktp,'.$trans->id_calon_debt;
                    $rules['no_ktp_kk'] = 'digits:16|unique:web.calon_debitur,no_ktp_kk,'.$trans->id_calon_debt;
                    $rules['no_kk']     = 'digits:16|unique:web.calon_debitur,no_kk,'.$trans->id_calon_debt;
                    $rules['no_npwp']   = 'digits:15|unique:web.calon_debitur,no_npwp,'.$trans->id_calon_debt;
                    $rules['no_telp']   = 'between:11,13|unique:web.calon_debitur,no_telp,'.$trans->id_calon_debt;
                    $rules['no_hp']     = 'between:11,13|unique:web.calon_debitur,no_hp,'.$trans->id_calon_debt;
                }
            }

            $rules = [
                // Fasilitas Pinjaman
                'jenis_pinjaman'        => 'in:KONSUMTIF,MODAL,INVESTASI',
                'plafon_pinjaman'       => 'integer',
                'tenor_pinjaman'        => 'integer|in:12;18;24;30;36;48;60',

                // Debitur
                'jenis_kelamin'         => 'in:L,P',
                'status_nikah'          => 'in:SINGLE,NIKAH,CERAI',
                // 'no_ktp'                => 'digits:16|unique:web.calon_debitur,no_ktp,'.$trans->id_calon_debt,
                // 'no_ktp_kk'             => 'digits:16|unique:web.calon_debitur,no_ktp_kk,'.$trans->id_calon_debt,
                // 'no_kk'                 => 'digits:16|unique:web.calon_debitur,no_kk,'.$trans->id_calon_debt,
                // 'no_npwp'               => 'digits:15|unique:web.calon_debitur,no_npwp,'.$trans->id_calon_debt,
                'tgl_lahir'             => 'date_format:d-m-Y',
                'agama'                 => 'in:ISLAM,KRISTEN,KHATOLIK,HINDU,BUDHA',
                'rt_ktp'                => 'numeric',
                'rw_ktp'                => 'numeric',
                'id_provinsi_ktp'       => 'numeric',
                'id_kabupaten_ktp'      => 'numeric',
                'id_kecamatan_ktp'      => 'numeric',
                'id_kelurahan_ktp'      => 'numeric',
                'rt_domisili'           => 'numeric',
                'rw_domisili'           => 'numeric',
                'id_provinsi_domisili'  => 'numeric',
                'id_kabupaten_domisili' => 'numeric',
                'id_kecamatan_domisili' => 'numeric',
                'id_kelurahan_domisili' => 'numeric',
                'jumlah_tanggungan'     => 'numeric',
                // 'no_telp'               => 'between:11,13|unique:web.calon_debitur,no_telp,'.$trans->id_calon_debt,
                // 'no_hp'                 => 'between:11,13|unique:web.calon_debitur,no_hp,'.$trans->id_calon_debt,

                'tgl_lahir_anak.*'      => 'date_format:d-m-Y',
                'tinggi_badan'          => 'numeric',
                'berat_badan'           => 'numeric',
                'pekerjaan'             => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'id_prov_tempat_kerja'  => 'numeric',
                'id_kab_tempat_kerja'   => 'numeric',
                'id_kec_tempat_kerja'   => 'numeric',
                'id_kel_tempat_kerja'   => 'numeric',
                'rt_tempat_kerja'       => 'numeric',
                'rw_tempat_kerja'       => 'numeric',
                // 'tgl_mulai_kerja'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja'  => 'numeric',
                'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_buku_tabungan'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_ktp'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_sku'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_slip_gaji'        => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_foto_usaha'       => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // Pasangan
                'jenis_kelamin_pas'         => 'in:L,P',
                // 'no_ktp_pas'                => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp,'.$trans->id_pasangan,
                // 'no_ktp_kk_pas'             => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp_kk,'.$trans->id_pasangan,
                // 'no_kk_pas'                 => 'digits:16|unique:web.pasangan_calon_debitur,no_kk,'.$trans->id_pasangan,
                // 'no_npwp_pas'               => 'digits:15|unique:web.pasangan_calon_debitur,no_npwp,'.$trans->id_pasangan,
                // 'tgl_lahir_pas'             => 'date_format:d-m-Y',
                // 'no_telp_pas'               => 'between:11,13|unique:web.pasangan_calon_debitur,no_telp,'.$trans->id_pasangan,
                'lamp_ktp_pas'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk_pas'               => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'pekerjaan_pas'             => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'rt_tempat_kerja_pas'       => 'numeric',
                'rw_tempat_kerja_pas'       => 'numeric',
                // 'tgl_mulai_kerja_pas'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pas'  => 'numeric',
                'rt_tempat_kerja_pas'       => 'numeric',
                'rw_tempat_kerja_pas'       => 'numeric',
                // 'tgl_mulai_kerja_pas'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pas'  => 'numeric',

                // Penjamin
                'tgl_lahir_pen.*'            => 'date_format:d-m-Y',
                'jenis_kelamin_pen.*'        => 'in:L,P',
                'lamp_ktp_pen.*'             => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_ktp_pasangan_pen.*'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk_pen.*'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_buku_nikah_pen.*'      => 'mimes:jpg,jpeg,png,pdf|max:2048',

                'pekerjaan_pen.*'            => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'rt_tempat_kerja_pen.*'       => 'numeric',
                'rw_tempat_kerja_pen.*'       => 'numeric',
                // 'tgl_mulai_kerja_pen.*'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pen.*'  => 'numeric',

                // Transaksi AO
                'jangka_waktu'          => 'integer|in:12;18;24;30;36;48;60',
                'suku_bunga'            => 'integer',
                'pembayaran_bunga'      => 'integer',
                'akad_kredit'           => 'in:ADENDUM,NOTARIS,INTERNAL',
                'ikatan_agunan'         => 'in:APHT,SKMHT,FIDUSIA',
                'biaya_provisi'         => 'integer',
                'biaya_administrasi'    => 'integer',
                'biaya_credit_checking' => 'integer',
                'biaya_tabungan'        => 'integer',

                // Verifikasi
                'ver_ktp_debt'            => 'numeric',
                'ver_kk_debt'             => 'numeric',
                'ver_akta_cerai_debt'     => 'numeric',
                'ver_akta_kematian_debt'  => 'numeric',
                'ver_rek_tabungan_debt'   => 'numeric',
                'ver_sertifikat_debt'     => 'numeric',
                'ver_sttp_pbb_debt'       => 'numeric',
                'ver_imb_debt'            => 'numeric',
                'ver_ktp_pasangan'        => 'numeric',
                'ver_akta_nikah_pasangan' => 'numeric',
                'ver_data_penjamin'       => 'numeric',
                'ver_sku_debt'            => 'numeric',
                'ver_pembukuan_usaha_debt'=> 'numeric',

                // Validasi
                'val_data_debt'       => 'numeric',
                'val_lingkungan_debt' => 'numeric',
                'val_domisili_debt'   => 'numeric',
                'val_pekerjaan_debt'  => 'numeric',
                'val_data_pasangan'   => 'numeric',
                'val_data_penjamin'   => 'numeric',
                'val_agunan'          => 'numeric',

                // Agunan Tanah
                'tipe_lokasi_agunan.*'  => 'in:PERUM,BIASA',
                'rt_agunan.*'           => 'numeric',
                'rw_agunan.*'           => 'numeric',
                'luas_tanah.*'          => 'numeric',
                'luas_bangunan.*'       => 'numeric',
                'jenis_sertifikat.*'    => 'in:SHM,SHGB',
                // 'tgl_ukur_sertifikat.*' => 'date_format:d-m-Y',
                'tgl_berlaku_shgb.*'    => 'date_format:d-m-Y',
                'lamp_agunan_depan.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kanan.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kiri.*'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_belakang.*'=> 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_dalam.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // Agunan Kendaraan
                'tahun.*'                 => 'date_format:Y',
                'tgl_kadaluarsa_pajak.*'  => 'date_format:d-m-Y',
                'tgl_kadaluarsa_stnk.*'   => 'date_format:d-m-Y',
                'lamp_agunan_depan.*'     => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kanan.*'     => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_kiri.*'      => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_belakang.*'  => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_agunan_dalam.*'     => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // Kapasitas Bulanan
                'pemasukan_debitur'     => 'integer',
                'pemasukan_pasangan'    => 'integer',
                'pemasukan_penjamin'    => 'integer',
                'biaya_rumah_tangga'    => 'integer',
                'biaya_transport'       => 'integer',
                'biaya_pendidikan'      => 'integer',
                'biaya_telp_listr_air'  => 'integer',
                'biaya_lain'            => 'integer',

                // Pendapatan Usaha
                'pemasukan_tunai'      => 'integer',
                'pemasukan_kredit'     => 'integer',
                'biaya_sewa'           => 'integer',
                'biaya_gaji_pegawai'   => 'integer',
                'biaya_belanja_brg'    => 'integer',
                'biaya_telp_listr_air' => 'integer',
                'biaya_sampah_kemanan' => 'integer',
                'biaya_kirim_barang'   => 'integer',
                'biaya_hutang_dagang'  => 'integer',
                'biaya_angsuran'       => 'integer',
                'biaya_lain_lain'      => 'integer',

                // Pemeriksaan Agunan Kendaraan
                'status_pengguna_ken.*' => 'in:PEMILIK,PENYEWA',
                'jml_roda_ken.*'        => 'integer',
                'km_ken.*'              => 'integer',

                // Pemeriksaan Agunan Tanah
                'status_penghuni.*'       => 'in:PEMILIK,PENYEWA',
                'bentuk_bangunan.*'       => 'in:RUMAH,KONTRAKAN,VILLA,RUKO,APARTMENT',
                'kondisi_bangunan.*'      => 'in:LAYAK,KURANG,TIDAK',
                'nilai_taksasi_agunan.*'  => 'integer',
                'nilai_taksasi_bangunan.*'=> 'integer',
                'tgl_taksasi.*'           => 'date_format:d-m-Y',
                'nilai_likuidasi.*'       => 'integer',

                // Mutasi Bank pada CA
                'urutan_mutasi.*'           => 'integer',
                'no_rekening_mutasi.*'      => 'numeric',
                'frek_debet_mutasi.*.*'     => 'integer',
                'nominal_debet_mutasi.*.*'  => 'integer',
                'frek_kredit_mutasi.*.*'    => 'integer',
                'nominal_kredit_mutasi.*.*' => 'integer',
                'saldo_mutasi.*.*'          => 'integer',

                // Data History Bank pada CA
                'no_rekening'             => 'integer',
                'penghasilan_per_tahun'   => 'integer',
                'pemasukan_per_bulan'     => 'integer',
                'frek_trans_pemasukan'    => 'in:A,B,C',
                'pengeluaran_per_bulan'   => 'integer',
                'frek_trans_pengeluaran'  => 'in:A,B,C,D',
                // 'sumber_dana_setoran'     =>
                'tujuan_pengeluaran_dana' => 'in:KONSUMTIF,MODAL,INVESTASI',

                // Info ACC
                'plafon_acc.*'          => 'integer',
                'baki_debet_acc.*'      => 'integer',
                'angsuran_acc.*'        => 'integer',

                // Ringkasan Analisa CA
                'kuantitatif_ttl_pendapatan'  => 'integer',
                'kuantitatif_ttl_pengeluaran' => 'integer',
                'kuantitatif_pendapatan'      => 'integer',
                'kuantitatif_angsuran'        => 'integer',
                'kuantitatif_ltv'             => 'integer',
                'kuantitatif_dsr'             => 'integer',
                'kuantitatif_idir'            => 'integer',
                'kuantitatif_hasil'           => 'integer',

                // Rekomendasi Pinjaman pada CA
                'penyimpangan_struktur' => 'in:ADA,TIDAK',
                'penyimpangan_dokumen'  => 'in:ADA,TIDAK',
                'recom_nilai_pinjaman'  => 'integer',
                'recom_tenor'           => 'integer',
                'recom_angsuran'        => 'integer',
                'recom_produk_kredit'   => 'integer',

                // Rekomendasi CA
                'plafon_kredit' => 'integer',

                // Asuransi Jiwa pada CA
                'jangka_waktu_as_jiwa'        => 'integer|in:12;18;24;30;36;48;60',
                'nilai_pertanggungan_as_jiwa' => 'integer',
                'jatuh_tempo_as_jiwa'         => 'date_format:d-m-Y',
                'berat_badan_as_jiwa'         => 'integer',
                'tinggi_badan_as_jiwa'        => 'integer',
                'umur_nasabah_as_jiwa'        => 'integer',

                // Asuransi Jaminan pada CA
                'jangka_waktu_as_jaminan'        => 'integer|in:12;18;24;30;36;48;60',
                'nilai_pertanggungan_as_jaminan' => 'integer',
                'jatuh_tempo_as_jaminan'         => 'date_format:d-m-Y',

                // Transaksi CAA
                'peyimpangan'        => 'required|in:ADA,TIDAK',
                'team_caa'           => 'required',
                'file_report_mao'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'file_report_mca'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'status_file_agunan' => 'required|in:ORIGINAL,CUSTOM',
                'status_file_usaha'  => 'required|in:ORIGINAL,CUSTOM',
                'file_agunan.*'      => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
                'file_usaha.*'       => 'required|mimes:jpg,jpeg,png,pdf|max:2048',
                'file_lain'          => 'mimes:jpg,jpeg,png,pdf|max:2048'
            ];
        }else{

            // If Create
            $rules = [
                // Fasilitas Pinjaman
                'jenis_pinjaman'        => 'required|in:KONSUMTIF,MODAL,INVESTASI',
                'plafon_pinjaman'       => 'required|integer',
                'tenor_pinjaman'        => 'required|integer|in:12;18;24;30;36;48;60',

                // Debitur
                'jenis_kelamin'         => 'required|in:L,P',
                'status_nikah'          => 'required|in:SINGLE,NIKAH,CERAI',
                'no_ktp'                => 'required|digits:16|unique:web.calon_debitur,no_ktp',
                'no_ktp_kk'             => 'required|digits:16|unique:web.calon_debitur,no_ktp_kk',
                'no_kk'                 => 'required|digits:16|unique:web.calon_debitur,no_kk',
                'no_npwp'               => 'required|digits:15|unique:web.calon_debitur,no_npwp',
                'tgl_lahir'             => 'required|date_format:d-m-Y',
                'agama'                 => 'required|in:ISLAM,KRISTEN,KHATOLIK,HINDU,BUDHA',
                'rt_ktp'                => 'required|numeric',
                'rw_ktp'                => 'required|numeric',
                'id_provinsi_ktp'       => 'required|numeric',
                'id_kabupaten_ktp'      => 'required|numeric',
                'id_kecamatan_ktp'      => 'required|numeric',
                'id_kelurahan_ktp'      => 'required|numeric',
                'rt_domisili'           => 'required|numeric',
                'rw_domisili'           => 'required|numeric',
                'id_provinsi_domisili'  => 'required|numeric',
                'id_kabupaten_domisili' => 'required|numeric',
                'id_kecamatan_domisili' => 'required|numeric',
                'id_kelurahan_domisili' => 'required|numeric',
                'jumlah_tanggungan'     => 'numeric',
                'no_telp'               => 'between:11,13|unique:web.calon_debitur,no_telp',
                'no_hp'                 => 'between:11,13|unique:web.calon_debitur,no_hp',
                'lamp_ktp'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_buku_tabungan'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_sertifikat'       => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_sttp_pbb'         => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_imb'              => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // Pasangan
                'jenis_kelamin_pas'     => 'in:L,P',
                'no_ktp_pas'            => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp',
                'no_ktp_kk_pas'         => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp_kk',
                'no_npwp_pas'           => 'digits:15|unique:web.pasangan_calon_debitur,no_npwp',
                'tgl_lahir_pas'         => 'date_format:d-m-Y',
                'no_telp_pas'           => 'between:11,13|unique:web.pasangan_calon_debitur,no_telp',
                'lamp_ktp_pas'          => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk_pas'           => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // Penjamin
                'no_ktp_pen.*'            => 'digits:16|unique:web.penjamin_calon_debitur,no_ktp',
                'no_npwp_pen.*'           => 'digits:15}unique:web.penjamin_calon_debitur,no_npwp',
                'tgl_lahir_pen.*'         => 'date_format:d-m-Y',
                'jenis_kelamin_pen.*'     => 'in:L,P',
                'no_telp_pen.*'           => 'between:11,13}unique:web.penjamin_calon_debitur,no_telp',
                'lamp_ktp_pen.*'          => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_ktp_pasangan_pen.*' => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk_pen.*'           => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_buku_nikah_pen.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
            ];
        }

        return $rules;
    }

    public function messages(){

        $required = ':attribute wajib diisi';
        $in       = ':attribute harus bertipe :values';
        $integer  = ':attribute harus berupa angka / bilangan bulat';
        $digits   = ':attribute harus berupa angka dan berjumlah :digits digit';


        return [
            // Fasilitas Pinjaman
            'jenis_pinjaman.required'  => ':attribute wajib diisi',
            'plafon_pinjaman.required' => ':attribute wajib diisi',
            'tenor_pinjaman.required'  => ':attribute wajib diisi',

            'jenis_pinjaman.in'        => ':attribute harus bertipe :values',
            'plafon_pinjaman.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'tenor_pinjaman.integer'   => ':attribute harus berupa angka / bilangan bulat',
            'tenor_pinjaman.in'        => ':attribute harus salah satu dari jenis berikut :values',

            // Debitur
            'jenis_kelamin.required'          => ':attribute wajib diisi',
            'status_nikah.required'           => ':attribute harus salah satu dari jenis berikut :values',
            'no_ktp.required'                 => ':attribute wajib diisi',
            'no_ktp.required'                 => ':attribute wajib diisi',
            'no_ktp_kk.required'              => ':attribute wajib diisi',
            'no_ktp_kk.required'              => ':attribute wajib diisi',
            'no_kk.required'                  => ':attribute wajib diisi',
            'no_kk.required'                  => ':attribute wajib diisi',
            'no_npwp.required'                => ':attribute wajib diisi',
            'no_npwp.required'                => ':attribute wajib diisi',
            'tgl_lahir.required'              => ':attribute wajib diisi',
            'agama.required'                  => ':attribute wajib diisi',
            'rt_ktp.required'                 => ':attribute wajib diisi',
            'rw_ktp.required'                 => ':attribute wajib diisi',
            'id_provinsi_ktp.required'        => ':attribute wajib diisi',
            'id_kabupaten_ktp.required'       => ':attribute wajib diisi',
            'id_kecamatan_ktp.required'       => ':attribute wajib diisi',
            'id_kelurahan_ktp.required'       => ':attribute wajib diisi',
            'rt_domisili.required'            => ':attribute wajib diisi',
            'rw_domisili.required'            => ':attribute wajib diisi',
            'id_provinsi_domisili.required'   => ':attribute wajib diisi',
            'id_kabupaten_domisili.required'  => ':attribute wajib diisi',
            'id_kecamatan_domisili.required'  => ':attribute wajib diisi',
            'id_kelurahan_domisili.required'  => ':attribute wajib diisi',

            'jenis_kelamin.in'               => ':attribute harus salah satu dari jenis berikut :values',
            'status_nikah.in'                => ':attribute harus salah satu dari jenis berikut :values',
            'no_ktp.digits'                  => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp.unique'                  => ':attribute telah ada yang menggunakan',
            'no_ktp_kk.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_kk.unique'               => ':attribute telah ada yang menggunakan',
            'no_kk.digits'                   => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_kk.unique'                   => ':attribute telah ada yang menggunakan',
            'no_npwp.digits'                 => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp.unique'                 => ':attribute telah ada yang menggunakan',
            'tgl_lahir.date_format'          => ':attribute harus berupa angka dengan format :format',
            'agama.in'                       => ':attribute harus salah satu dari jenis berikut :values',
            'rt_ktp.numeric'                 => ':attribute harus berupa angka',
            'rw_ktp.numeric'                 => ':attribute harus berupa angka',
            'id_provinsi_ktp.numeric'        => ':attribute harus berupa angka',
            'id_kabupaten_ktp.numeric'       => ':attribute harus berupa angka',
            'id_kecamatan_ktp.numeric'       => ':attribute harus berupa angka',
            'id_kelurahan_ktp.numeric'       => ':attribute harus berupa angka',
            'rt_domisili.numeric'            => ':attribute harus berupa angka',
            'rw_domisili.numeric'            => ':attribute harus berupa angka',
            'id_provinsi_domisili.numeric'   => ':attribute harus berupa angka',
            'id_kabupaten_domisili.numeric'  => ':attribute harus berupa angka',
            'id_kecamatan_domisili.numeric'  => ':attribute harus berupa angka',
            'id_kelurahan_domisili.numeric'  => ':attribute harus berupa angka',
            'jumlah_tanggungan.numeric'      => ':attribute harus berupa angka',
            'no_telp.between'                => ':attribute harus berada diantara :min - :max.',
            'no_telp.unique'                 => ':attribute telah ada yang menggunakan',
            'no_hp.between'                  => ':attribute harus berada diantara :min - :max.',
            'no_hp.unique'                   => ':attribute telah ada yang menggunakan',

            'tgl_lahir_anak.*.date_format'   => ':attribute harus berupa angka dengan format :format',
            'tinggi_badan.numeric'           => ':attribute harus berupa angka',
            'berat_badan.numeric'            => ':attribute harus berupa angka',
            'pekerjaan.in'                   => ':attribute harus salah satu dari jenis berikut :values',
            'id_prov_tempat_kerja'           => ':attribute harus berupa angka',
            'id_kab_tempat_kerja'            => ':attribute harus berupa angka',
            'id_kec_tempat_kerja'            => ':attribute harus berupa angka',
            'id_kel_tempat_kerja'            => ':attribute harus berupa angka',
            'rt_tempat_kerja.numeric'        => ':attribute harus berupa angka',
            'rw_tempat_kerja.numeric'        => ':attribute harus berupa angka',
            // 'tgl_mulai_kerja.date_format'    => ':attribute harus berupa angka dengan format :format',
            'no_telp_tempat_kerja.numeric'   => ':attribute harus berupa angka',
            'lamp_surat_cerai.mimes'         => ':attribute harus bertipe :values',
            'lamp_buku_tabungan.mimes'       => ':attribute harus bertipe :values',
            'lamp_ktp.mimes'                 => ':attribute harus bertipe :values',
            'lamp_kk.mimes'                  => ':attribute harus bertipe :values',
            'lamp_sku.mimes'                 => ':attribute harus bertipe :values',
            'lamp_slip_gaji.mimes'           => ':attribute harus bertipe :values',
            'lamp_foto_usaha.mimes'          => ':attribute harus bertipe :values',
            'lamp_surat_cerai.max'           => 'ukuran :attribute max :values',
            'lamp_buku_tabungan.max'         => 'ukuran :attribute max :max kb',
            'lamp_ktp.max'                   => 'ukuran :attribute max :max kb',
            'lamp_kk.max'                    => 'ukuran :attribute max :max kb',
            'lamp_sku.max'                   => 'ukuran :attribute max :max kb',
            'lamp_slip_gaji.max'             => 'ukuran :attribute max :max kb',
            'lamp_foto_usaha.max'            => 'ukuran :attribute max :max kb',

            // Pasangan
            'jenis_kelamin_pas.in'             => ':attribute harus salah satu dari jenis berikut :values',
            'no_ktp_pas.digits'                => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_pas.unique'                => ':attribute telah ada yang menggunakan',
            'no_ktp_kk_pas.digits'             => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_kk_pas.unique'             => ':attribute telah ada yang menggunakan',
            'no_kk_pas.digits'                 => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_kk_pas.unique'                 => ':attribute telah ada yang menggunakan',
            'no_npwp_pas.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp_pas.unique'               => ':attribute telah ada yang menggunakan',
            'id_provinsi'                      => ':attribute harus berupa angka',
            'id_kabupaten'                     => ':attribute harus berupa angka',
            'id_kecamatan'                     => ':attribute harus berupa angka',
            'id_kelurahan'                     => ':attribute harus berupa angka',
            'rt'                               => ':attribute harus berupa angka',
            'rw'                               => ':attribute harus berupa angka',
            'tgl_lahir_pas.date_format'        => ':attribute harus berupa angka dengan format :format',
            'no_telp_pas.between'              => ':attribute harus berada diantara :min - :max.',
            'no_telp_pas.unique'               => ':attribute telah ada yang menggunakan',
            'lamp_ktp_pas.mimes'               => ':attribute harus bertipe :values',
            'lamp_kk_pas.mimes'                => ':attribute harus bertipe :values',
            'lamp_ktp_pas.max'                 => 'ukuran :attribute max :max kb',
            'lamp_kk_pas.max'                  => 'ukuran :attribute max :max kb',
            'pekerjaan_pas.in'                 => ':attribute harus salah satu dari jenis berikut :values',
            'rt_tempat_kerja_pas.numeric'      => ':attribute harus berupa angka',
            'rw_tempat_kerja_pas.numeric'      => ':attribute harus berupa angka',
            // 'tgl_mulai_kerja_pas.date_format'  => ':attribute harus berupa angka dengan format :format',
            'no_telp_tempat_kerja_pas.numeric' => ':attribute harus berupa angka / bilangan bulat',

            // Penjamin
            'no_ktp_pen.*.digits'                => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_ktp_pen.*.unique'                => ':attribute telah ada yang menggunakan',
            'no_npwp_pen.*.digits'               => ':attribute harus berupa angka dan berjumlah :digits digit',
            'no_npwp_pen.*.unique'               => ':attribute telah ada yang menggunakan',
            'tgl_lahir_pen.*.date_format'        => ':attribute harus berupa angka dengan format :format',
            'jenis_kelamin_pen.*.in'             => ':attribute harus salah satu dari jenis berikut :values',
            'no_telp_pen.*.between'              => ':attribute harus berada diantara :min - :max.',
            'no_telp_pen.*.unique'               => ':attribute telah ada yang menggunakan',
            'lamp_ktp_pen.*.mimes'               => ':attribute harus bertipe :values',
            'lamp_ktp_pasangan_pen.*.mimes'      => ':attribute harus bertipe :values',
            'lamp_kk_pen.*.mimes'                => ':attribute harus bertipe :values',
            'lamp_buku_nikah_pen.*.mimes'        => ':attribute harus bertipe :values',
            'lamp_ktp_pen.*.max'                 => 'ukuran :attribute max :max kb',
            'lamp_ktp_pasangan_pen.*.max'        => 'ukuran :attribute max :max kb',
            'lamp_kk_pen.*.max'                  => 'ukuran :attribute max :max kb',
            'lamp_buku_nikah_pen.*.max'          => 'ukuran :attribute max :max kb',
            'pekerjaan_pen.*.in'                 => ':attribute harus salah satu dari jenis berikut :values',
            'rt_tempat_kerja_pen.*.numeric'      => ':attribute harus berupa angka',
            'rw_tempat_kerja_pen.*.numeric'      => ':attribute harus berupa angka',
            // 'tgl_mulai_kerja_pen.*.date_format'  => ':attribute harus berupa angka dengan format :format',
            'no_telp_tempat_kerja_pen.*.numeric' => ':attribute harus berupa angka',

            // Transaksi AO
            'jangka_waktu.integer'          => ':attribute harus berupa angka',
            'jangka_waktu.in'               => ':attribute harus salah satu dari jenis berikut :values',
            'suku_bunga.integer'            => ':attribute harus berupa angka',
            'pembayaran_bunga.integer'      => ':attribute harus berupa angka',
            'akad_kredit.in'                => ':attribute harus salah satu dari jenis berikut :values',
            'ikatan_agunan.in'              => ':attribute harus salah satu dari jenis berikut :values',
            'biaya_provisi.integer'         => ':attribute harus berupa angka',
            'biaya_administrasi.integer'    => ':attribute harus berupa angka',
            'biaya_credit_checking.integer' => ':attribute harus berupa angka',
            'biaya_tabungan.integer'        => ':attribute harus berupa angka',

            // Verifikasi
            'ver_ktp_debt.numeric'            => ':attribute harus berupa angka',
            'ver_kk_debt.numeric'             => ':attribute harus berupa angka',
            'ver_akta_cerai_debt.numeric'     => ':attribute harus berupa angka',
            'ver_akta_kematian_debt.numeric'  => ':attribute harus berupa angka',
            'ver_rek_tabungan_debt.numeric'   => ':attribute harus berupa angka',
            'ver_sertifikat_debt.numeric'     => ':attribute harus berupa angka',
            'ver_sttp_pbb_debt.numeric'       => ':attribute harus berupa angka',
            'ver_imb_debt.numeric'            => ':attribute harus berupa angka',
            'ver_ktp_pasangan.numeric'        => ':attribute harus berupa angka',
            'ver_akta_nikah_pasangan.numeric' => ':attribute harus berupa angka',
            'ver_data_penjamin.numeric'       => ':attribute harus berupa angka',
            'ver_sku_debt.numeric'            => ':attribute harus berupa angka',
            'ver_pembukuan_usaha_debt.numeric'=> ':attribute harus berupa angka',

            // Validasi
            'val_data_debt.numeric'       => ':attribute harus berupa angka',
            'val_lingkungan_debt.numeric' => ':attribute harus berupa angka',
            'val_domisili_debt.numeric'   => ':attribute harus berupa angka',
            'val_pekerjaan_debt.numeric'  => ':attribute harus berupa angka',
            'val_data_pasangan.numeric'   => ':attribute harus berupa angka',
            'val_data_penjamin.numeric'   => ':attribute harus berupa angka',
            'val_agunan.numeric'          => ':attribute harus berupa angka',

            // Agunan Tanah
            'tipe_lokasi_agunan.*.in'           => ':attribute harus salah satu dari jenis berikut :values',
            'rt_agunan.*.numeric'               => ':attribute harus berupa angka',
            'rw_agunan.*.numeric'               => ':attribute harus berupa angka',
            'luas_tanah.*.numeric'              => ':attribute harus berupa angka',
            'luas_bangunan.*.numeric'           => ':attribute harus berupa angka',
            'jenis_sertifikat.*.in'             => ':attribute harus salah satu dari jenis berikut :values',
            // 'tgl_ukur_sertifikat.*.date_format' => ':attribute harus berupa angka dengan format :format',
            'tgl_berlaku_shgb.*.date_format'    => ':attribute harus berupa angka dengan format :format',

            'lamp_agunan_depan.*.mimes'         => ':attribute harus bertipe :values',
            'lamp_agunan_kanan.*.mimes'         => ':attribute harus bertipe :values',
            'lamp_agunan_kiri.*.mimes'          => ':attribute harus bertipe :values',
            'lamp_agunan_belakang.*.mimes'      => ':attribute harus bertipe :values',
            'lamp_agunan_dalam.*.mimes'         => ':attribute harus bertipe :values',

            'lamp_agunan_depan.*.max'           => 'ukuran :attribute max :max kb',
            'lamp_agunan_kanan.*.max'           => 'ukuran :attribute max :max kb',
            'lamp_agunan_kiri.*.max'            => 'ukuran :attribute max :max kb',
            'lamp_agunan_belakang.*.max'        => 'ukuran :attribute max :max kb',
            'lamp_agunan_dalam.*.max'           => 'ukuran :attribute max :max kb',

            // Agunan Kendaraan
            'tahun.*.date_format'               => 'date_format:Y',
            'tgl_kadaluarsa_pajak.*.date_format'=> 'date_format:d-m-Y',
            'tgl_kadaluarsa_stnk.*.date_format' => 'date_format:d-m-Y',

            'lamp_agunan_depan.*.mimes'         => ':attribute harus bertipe :values',
            'lamp_agunan_kanan.*.mimes'         => ':attribute harus bertipe :values',
            'lamp_agunan_kiri.*.mimes'          => ':attribute harus bertipe :values',
            'lamp_agunan_belakang.*.mimes'      => ':attribute harus bertipe :values',
            'lamp_agunan_dalam.*.mimes'         => ':attribute harus bertipe :values',

            'lamp_agunan_depan.*.max'           => 'ukuran :attribute max :max kb',
            'lamp_agunan_kanan.*.max'           => 'ukuran :attribute max :max kb',
            'lamp_agunan_kiri.*.max'            => 'ukuran :attribute max :max kb',
            'lamp_agunan_belakang.*.max'        => 'ukuran :attribute max :max kb',
            'lamp_agunan_dalam.*.max'           => 'ukuran :attribute max :max kb',

            // Kapasitas Bulanan
            'pemasukan_debitur.integer'     => ':attribute harus berupa angka / bilangan bulat',
            'pemasukan_pasangan.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'pemasukan_penjamin.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'biaya_rumah_tangga.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'biaya_transport.integer'       => ':attribute harus berupa angka / bilangan bulat',
            'biaya_pendidikan.integer'      => ':attribute harus berupa angka / bilangan bulat',
            'biaya_telp_listr_air.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'biaya_lain.integer'            => ':attribute harus berupa angka / bilangan bulat',

            // Pendapatan Usaha
            'pemasukan_tunai.integer'      => ':attribute harus berupa angka / bilangan bulat',
            'pemasukan_kredit.integer'     => ':attribute harus berupa angka / bilangan bulat',
            'biaya_sewa.integer'           => ':attribute harus berupa angka / bilangan bulat',
            'biaya_gaji_pegawai.integer'   => ':attribute harus berupa angka / bilangan bulat',
            'biaya_belanja_brg.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'biaya_telp_listr_air.integer' => ':attribute harus berupa angka / bilangan bulat',
            'biaya_sampah_kemanan.integer' => ':attribute harus berupa angka / bilangan bulat',
            'biaya_kirim_barang.integer'   => ':attribute harus berupa angka / bilangan bulat',
            'biaya_hutang_dagang.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'biaya_angsuran.integer'       => ':attribute harus berupa angka / bilangan bulat',
            'biaya_lain_lain.integer'      => ':attribute harus berupa angka / bilangan bulat',

            // Pemeriksaan Agunan Kendaraan
            'status_pengguna_ken.*.in'  => ':attribute harus salah satu dari jenis berikut :values',
            'jml_roda_ken.*.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'km_ken.*.integer'          => ':attribute harus berupa angka / bilangan bulat',

            // Pemeriksaan Agunan Tanah
            'status_penghuni.*.in'              => ':attribute harus salah satu dari jenis berikut :values',
            'bentuk_bangunan.*.in'              => ':attribute harus salah satu dari jenis berikut :values',
            'kondisi_bangunan.*.in'             => ':attribute harus salah satu dari jenis berikut :values',
            'nilai_taksasi_agunan.*.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'nilai_taksasi_bangunan.*.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'tgl_taksasi.*.date_format'         => ':attribute harus berupa angka dengan format :format',
            'nilai_likuidasi.*.integer'         => ':attribute harus berupa angka / bilangan bulat',

            // Mutasi Bank pada CA
            'urutan_mutasi.*.integer'           => ':attribute harus berupa angka / bilangan bulat',
            'no_rekening_mutasi.*.numeric'      => ':attribute harus berupa angka',
            'frek_debet_mutasi.*.*.integer'     => ':attribute harus berupa angka / bilangan bulat',
            'nominal_debet_mutasi.*.*.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'frek_kredit_mutasi.*.*.integer'    => ':attribute harus berupa angka / bilangan bulat',
            'nominal_kredit_mutasi.*.*.integer' => ':attribute harus berupa angka / bilangan bulat',
            'saldo_mutasi.*.*.integer'          => ':attribute harus berupa angka / bilangan bulat',

            // Data History Bank pada CA
            'no_rekening.integer'           => ':attribute harus berupa angka / bilangan bulat',
            'penghasilan_per_tahun.integer' => ':attribute harus berupa angka / bilangan bulat',
            'pemasukan_per_bulan.integer'   => ':attribute harus berupa angka / bilangan bulat',
            'frek_trans_pemasukan.in'       => ':attribute harus salah satu dari jenis berikut :values, A untuk frek. 1 -  5 Kali, B untuk untuk frek. 2.6 - 10 kali, C untuk frek. lebih dari 10 kali',
            'pengeluaran_per_bulan.integer' => ':attribute harus berupa angka / bilangan bulat',
            'frek_trans_pengeluaran.in'     => ':attribute harus salah satu dari jenis berikut :values, A untuk frek. 1 -  5 Kali, B untuk untuk frek. 2.6 - 10 kali, C untuk frek. 3.1 - 15 kali, D untuk frek. lebih dari 15 kali',
            'tujuan_pengeluaran_dana.in'    => 'in:KONSUMTIF,MODAL,INVESTASI',

            // Info ACC
            'plafon_acc.*.integer'          => ':attribute harus berupa angka / bilangan bulat',
            'baki_debet_acc.*.integer'      => ':attribute harus berupa angka / bilangan bulat',
            'angsuran_acc.*.integer'        => ':attribute harus berupa angka / bilangan bulat',

            // Ringkasan Analisa CA
            'kuantitatif_ttl_pendapatan.integer'  => ':attribute harus berupa angka / bilangan bulat',
            'kuantitatif_ttl_pengeluaran.integer' => ':attribute harus berupa angka / bilangan bulat',
            'kuantitatif_pendapatan.integer'      => ':attribute harus berupa angka / bilangan bulat',
            'kuantitatif_angsuran.integer'        => ':attribute harus berupa angka / bilangan bulat',
            'kuantitatif_ltv.integer'             => ':attribute harus berupa angka / bilangan bulat',
            'kuantitatif_dsr.integer'             => ':attribute harus berupa angka / bilangan bulat',
            'kuantitatif_idir.integer'            => ':attribute harus berupa angka / bilangan bulat',
            'kuantitatif_hasil.integer'           => ':attribute harus berupa angka / bilangan bulat',

            // Rekomendasi Pinjaman pada CA
            'penyimpangan_struktur.in'     => ':attribute harus salah satu dari jenis berikut :values',
            'penyimpangan_dokumen.in'      => ':attribute harus salah satu dari jenis berikut :values',
            'recom_nilai_pinjaman.integer' => ':attribute harus berupa angka / bilangan bulat',
            'recom_tenor.integer'          => ':attribute harus berupa angka / bilangan bulat',
            'recom_angsuran.integer'       => ':attribute harus berupa angka / bilangan bulat',
            'recom_produk_kredit.integer' => ':attribute harus berupa angka / bilangan bulat',

            // Rekomendasi CA
            'plafon_kredit' => ':attribute harus berupa angka / bilangan bulat',

            // Asuransi Jiwa pada CA
            'jangka_waktu_as_jiwa.integer'        => ':attribute harus berupa angka / bilangan bulat',
            'jangka_waktu_as_jiwa.in'             => ':attribute harus salah satu dari jenis berikut :values',
            'nilai_pertanggungan_as_jiwa.integer' => ':attribute harus berupa angka / bilangan bulat',
            'jatuh_tempo_as_jiwa.date_format'     => ':attribute harus berupa angka dengan format :format',
            'berat_badan_as_jiwa.integer'         => ':attribute harus berupa angka / bilangan bulat',
            'tinggi_badan_as_jiwa.integer'        => ':attribute harus berupa angka / bilangan bulat',
            'umur_nasabah_as_jiwa.integer'        => ':attribute harus berupa angka / bilangan bulat',

            // Asuransi Jaminan pada CA
            'jangka_waktu_as_jaminan.integer'        => ':attribute harus berupa angka / bilangan bulat',
            'jangka_waktu_as_jaminan.in'             => ':attribute harus salah satu dari jenis berikut :values',
            'nilai_pertanggungan_as_jaminan.integer' => ':attribute harus berupa angka / bilangan bulat',
            'jatuh_tempo_as_jaminan.date_format'     => ':attribute harus berupa angka dengan format :format',

            // Transaksi CAA
            'peyimpangan.required'        => ':attribute wajib diisi / dipilih',
            'peyimpangan.in'              => ':attribute harus salah satu dari jenis berikut :values',
            'team_caa.required'           => ':attribute wajib diisi / dipilih',
            'file_report_mao.mimes'       => ':attribute harus bertipe :values',
            'file_report_mao.max'         => 'ukuran :attribute max :max kb',
            'file_report_mca.mimes'       => ':attribute harus bertipe :values',
            'file_report_mca.max'         => 'ukuran :attribute max :max kb',
            'status_file_agunan.required' => ':attribute wajib diisi / dipilih',
            'status_file_agunan.in'       => ':attribute harus salah satu dari jenis berikut :values',
            'status_file_usaha.required'  => ':attribute wajib diisi / dipilih',
            'status_file_usaha.in'        => ':attribute harus salah satu dari jenis berikut :values',
            'file_agunan.*.required'      => ':attribute wajib diisi / dipilih',
            'file_agunan.*.mimes'         => ':attribute harus bertipe :values',
            'file_agunan.*.max'           => 'ukuran :attribute max :max kb',
            'file_usaha.*.required'       => ':attribute wajib diisi / dipilih',
            'file_usaha.*.mimes'          => ':attribute harus bertipe :values',
            'file_usaha.*.max'            => 'ukuran :attribute max :max kb',
            'file_lain.mimes'             => ':attribute harus bertipe :values',
            'file_lain.max'               => 'ukuran :attribute max :max kb',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw new HttpResponseException(
            // response()->json(['errors' => $errors], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            response()->json([
                "code"    => 422,
                "status"  => "not valid request",
                "message" => $errors
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
