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
                'tenor_pinjaman'        => 'integer',

                // Debitur
                'jenis_kelamin'         => 'in:L,P',
                'status_nikah'          => 'in:SINGLE,NIKAH,CERAI',
                // 'no_ktp'                => 'digits:16|unique:web.calon_debitur,no_ktp,'.$trans->id_calon_debt,
                // 'no_ktp_kk'             => 'digits:16|unique:web.calon_debitur,no_ktp_kk,'.$trans->id_calon_debt,
                // 'no_kk'                 => 'digits:16|unique:web.calon_debitur,no_kk,'.$trans->id_calon_debt,
                // 'no_npwp'               => 'digits:15|unique:web.calon_debitur,no_npwp,'.$trans->id_calon_debt,
                'tgl_lahir'             => 'date_format:d-m-Y',
                'agama'                 => 'in:ISLAM,KRISTEN,KHATOLIK,HINDU,BUDHA',
                'rt_ktp'                => 'integer',
                'rw_ktp'                => 'integer',
                'id_provinsi_ktp'       => 'integer',
                'id_kabupaten_ktp'      => 'integer',
                'id_kecamatan_ktp'      => 'integer',
                'id_kelurahan_ktp'      => 'integer',
                'rt_domisili'           => 'integer',
                'rw_domisili'           => 'integer',
                'id_provinsi_domisili'  => 'integer',
                'id_kabupaten_domisili' => 'integer',
                'id_kecamatan_domisili' => 'integer',
                'id_kelurahan_domisili' => 'integer',
                'jumlah_tanggungan'     => 'integer',
                // 'no_telp'               => 'between:11,13|unique:web.calon_debitur,no_telp,'.$trans->id_calon_debt,
                // 'no_hp'                 => 'between:11,13|unique:web.calon_debitur,no_hp,'.$trans->id_calon_debt,

                'tgl_lahir_anak.*'      => 'date_format:d-m-Y',
                'tinggi_badan'          => 'integer',
                'berat_badan'           => 'integer',
                'pekerjaan'             => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'id_prov_tempat_kerja'  => 'integer',
                'id_kab_tempat_kerja'   => 'integer',
                'id_kec_tempat_kerja'   => 'integer',
                'id_kel_tempat_kerja'   => 'integer',
                'rt_tempat_kerja'       => 'integer',
                'rw_tempat_kerja'       => 'integer',
                // 'tgl_mulai_kerja'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja'  => 'integer',
                'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_buku_tabungan.*'  => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_ktp'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_sku.*'            => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_slip_gaji'        => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_foto_usaha.*'     => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // Pasangan
                'jenis_kelamin_pas'         => 'in:L,P',
                'lamp_ktp_pas'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk_pas'               => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'pekerjaan_pas'             => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'rt_tempat_kerja_pas'       => 'integer',
                'rw_tempat_kerja_pas'       => 'integer',
                // 'tgl_mulai_kerja_pas'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pas'  => 'integer',
                'rt_tempat_kerja_pas'       => 'integer',
                'rw_tempat_kerja_pas'       => 'integer',
                // 'tgl_mulai_kerja_pas'       => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pas'  => 'integer',

                // Penjamin
                'tgl_lahir_pen.*'            => 'date_format:d-m-Y',
                'jenis_kelamin_pen.*'        => 'in:L,P',
                'lamp_ktp_pen.*'             => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_ktp_pasangan_pen.*'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_kk_pen.*'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'lamp_buku_nikah_pen.*'      => 'mimes:jpg,jpeg,png,pdf|max:2048',

                'pekerjaan_pen.*'            => 'in:KARYAWAN,PNS,WIRASWASTA,PENGURUS_RT',
                'rt_tempat_kerja_pen.*'      => 'integer',
                'rw_tempat_kerja_pen.*'      => 'integer',
                // 'tgl_mulai_kerja_pen.*'      => 'date_format:d-m-Y',
                'no_telp_tempat_kerja_pen.*' => 'integer',

                // Transaksi AO
                // 'jangka_waktu'          => 'integer|in:12;18;24;30;36;48;60',
                'pembayaran_bunga'      => 'integer',
                'akad_kredit'           => 'in:ADENDUM,NOTARIS,INTERNAL',
                'ikatan_agunan'         => 'in:APHT,SKMHT,FIDUSIA',
                'biaya_provisi'         => 'integer',
                'biaya_administrasi'    => 'integer',
                'biaya_credit_checking' => 'integer',
                'biaya_tabungan'        => 'integer',

                // Verifikasi
                'ver_ktp_debt'            => 'integer',
                'ver_kk_debt'             => 'integer',
                'ver_akta_cerai_debt'     => 'integer',
                'ver_akta_kematian_debt'  => 'integer',
                'ver_rek_tabungan_debt'   => 'integer',
                'ver_sertifikat_debt'     => 'integer',
                'ver_sttp_pbb_debt'       => 'integer',
                'ver_imb_debt'            => 'integer',
                'ver_ktp_pasangan'        => 'integer',
                'ver_akta_nikah_pasangan' => 'integer',
                'ver_data_penjamin'       => 'integer',
                'ver_sku_debt'            => 'integer',
                'ver_pembukuan_usaha_debt'=> 'integer',

                // Validasi
                'val_data_debt'       => 'integer',
                'val_lingkungan_debt' => 'integer',
                'val_domisili_debt'   => 'integer',
                'val_pekerjaan_debt'  => 'integer',
                'val_data_pasangan'   => 'integer',
                'val_data_penjamin'   => 'integer',
                'val_agunan'          => 'integer',

                // Agunan Tanah
                'tipe_lokasi_agunan.*'  => 'in:PERUM,BIASA',
                'rt_agunan.*'           => 'integer',
                'rw_agunan.*'           => 'integer',
                'luas_tanah.*'          => 'integer',
                'luas_bangunan.*'       => 'integer',
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
                'status_pengguna_ken.*' => 'in:PEMILIK,PENYEWA,KELUARGA',
                'jml_roda_ken.*'        => 'integer',
                'km_ken.*'              => 'integer',

                // Pemeriksaan Agunan Tanah
                'status_penghuni.*'       => 'in:PEMILIK,PENYEWA,KELUARGA',
                // 'bentuk_bangunan.*'       => 'in:RUMAH,KONTRAKAN,VILLA,RUKO,APARTMENT',
                'kondisi_bangunan.*'      => 'in:LAYAK,KURANG,TIDAK',
                // 'nilai_taksasi_agunan.*'  => 'integer',
                // 'nilai_taksasi_bangunan.*'=> 'integer',
                'tgl_taksasi.*'           => 'date_format:d-m-Y',
                // 'nilai_likuidasi.*'       => 'integer',

                // Mutasi Bank pada CA
                'urutan_mutasi.*'           => 'integer',
                'no_rekening_mutasi.*'      => 'integer',
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
                // 'jangka_waktu_as_jiwa'        => 'integer|in:12;18;24;30;36;48;60',
                'nilai_pertanggungan_as_jiwa' => 'integer',
                'jatuh_tempo_as_jiwa'         => 'date_format:d-m-Y',
                'berat_badan_as_jiwa'         => 'integer',
                'tinggi_badan_as_jiwa'        => 'integer',
                'umur_nasabah_as_jiwa'        => 'integer',

                // Asuransi Jaminan pada CA
                // 'jangka_waktu_as_jaminan'        => 'integer|in:12;18;24;30;36;48;60',
                'nilai_pertanggungan_as_jaminan.*' => 'integer',
                'jatuh_tempo_as_jaminan.*'         => 'date_format:d-m-Y',

                // Transaksi CAA
                'penyimpangan'       => 'in:ADA,TIDAK',
                'team_caa.*'         => 'required|integer',
                'file_report_mao'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'file_report_mca'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'status_file_agunan' => 'in:ORIGINAL,CUSTOM',
                'status_file_usaha'  => 'in:ORIGINAL,CUSTOM',
                // 'file_agunan.*'      => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'file_usaha.*'       => 'mimes:jpg,jpeg,png,pdf|max:2048',
                'file_lain'          => 'mimes:jpg,jpeg,png,pdf|max:2048'
            ];
        }else{

            // If Create
            $rules = [
                // Fasilitas Pinjaman
                'jenis_pinjaman'        => 'required|in:KONSUMTIF,MODAL,INVESTASI',
                'plafon_pinjaman'       => 'required|integer',
                'tenor_pinjaman'        => 'required|integer',

                // Debitur
                // 'jenis_kelamin'         => 'required|in:L,P',
                // 'status_nikah'          => 'required|in:SINGLE,NIKAH,CERAI',
                // 'no_ktp'                => 'required|digits:16|unique:web.calon_debitur,no_ktp',
                // 'no_ktp_kk'             => 'required|digits:16|unique:web.calon_debitur,no_ktp_kk',
                // 'no_kk'                 => 'required|digits:16|unique:web.calon_debitur,no_kk',
                // 'no_npwp'               => 'required|digits:15|unique:web.calon_debitur,no_npwp',
                // 'tgl_lahir'             => 'required|date_format:d-m-Y',
                // 'agama'                 => 'required|in:ISLAM,KRISTEN,KHATOLIK,HINDU,BUDHA',
                // 'rt_ktp'                => 'required|integer',
                // 'rw_ktp'                => 'required|integer',
                // 'id_provinsi_ktp'       => 'required|integer',
                // 'id_kabupaten_ktp'      => 'required|integer',
                // 'id_kecamatan_ktp'      => 'required|integer',
                // 'id_kelurahan_ktp'      => 'required|integer',
                // 'rt_domisili'           => 'required|integer',
                // 'rw_domisili'           => 'required|integer',
                // 'id_provinsi_domisili'  => 'required|integer',
                // 'id_kabupaten_domisili' => 'required|integer',
                // 'id_kecamatan_domisili' => 'required|integer',
                // 'id_kelurahan_domisili' => 'required|integer',
                // 'jumlah_tanggungan'     => 'integer',
                // 'no_telp'               => 'between:11,13|unique:web.calon_debitur,no_telp',
                // 'no_hp'                 => 'between:11,13|unique:web.calon_debitur,no_hp',
                // 'lamp_ktp'              => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_kk'               => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_surat_cerai'      => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_buku_tabungan'    => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_sertifikat'       => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_sttp_pbb'         => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_imb'              => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // // Pasangan
                // 'jenis_kelamin_pas'     => 'in:L,P',
                // 'no_ktp_pas'            => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp',
                // 'no_ktp_kk_pas'         => 'digits:16|unique:web.pasangan_calon_debitur,no_ktp_kk',
                // 'no_npwp_pas'           => 'digits:15|unique:web.pasangan_calon_debitur,no_npwp',
                // 'tgl_lahir_pas'         => 'date_format:d-m-Y',
                // 'no_telp_pas'           => 'between:11,13|unique:web.pasangan_calon_debitur,no_telp',
                // 'lamp_ktp_pas'          => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_kk_pas'           => 'mimes:jpg,jpeg,png,pdf|max:2048',

                // // Penjamin
                // 'no_ktp_pen.*'            => 'digits:16|unique:web.penjamin_calon_debitur,no_ktp',
                // 'no_npwp_pen.*'           => 'digits:15}unique:web.penjamin_calon_debitur,no_npwp',
                // 'tgl_lahir_pen.*'         => 'date_format:d-m-Y',
                // 'jenis_kelamin_pen.*'     => 'in:L,P',
                // 'no_telp_pen.*'           => 'between:11,13}unique:web.penjamin_calon_debitur,no_telp',
                // 'lamp_ktp_pen.*'          => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_ktp_pasangan_pen.*' => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_kk_pen.*'           => 'mimes:jpg,jpeg,png,pdf|max:2048',
                // 'lamp_buku_nikah_pen.*'   => 'mimes:jpg,jpeg,png,pdf|max:2048',
            ];
        }

        return $rules;
    }

    public function messages(){

        $required    = ':attribute wajib diisi';
        $in          = ':attribute harus bertipe :values';
        $integer     = ':attribute harus berupa angka / bilangan bulat';
        // $numeric     = ':attribute harus berupa angka';
        $digits      = ':attribute harus berupa angka dan berjumlah :digits digit';
        $unique      = ':attribute telah ada yang menggunakan';
        $date_format = ':attribute harus berupa angka dengan format :format';
        $between     = ':attribute harus berada diantara :min - :max.';
        $mimes       = ':attribute harus bertipe :values';
        $max         = ':attribute tidak boleh lebih dari :max kb';
        $size        = ':attribute tidak boleh lebih dari :size kb';

        return [
            // Fasilitas Pinjaman
            'jenis_pinjaman.required'  => $required,
            'plafon_pinjaman.required' => $required,
            'tenor_pinjaman.required'  => $required,

            'jenis_pinjaman.in'        => $in,
            'plafon_pinjaman.integer'  => $integer,
            'tenor_pinjaman.integer'   => $integer,
            'tenor_pinjaman.in'        => $in,

            // Debitur
            'jenis_kelamin.required'          => $required,
            'status_nikah.required'           => $required,
            'no_ktp.required'                 => $required,
            'no_ktp.required'                 => $required,
            'no_ktp_kk.required'              => $required,
            'no_ktp_kk.required'              => $required,
            'no_kk.required'                  => $required,
            'no_kk.required'                  => $required,
            'no_npwp.required'                => $required,
            'no_npwp.required'                => $required,
            'tgl_lahir.required'              => $required,
            'agama.required'                  => $required,
            'rt_ktp.required'                 => $required,
            'rw_ktp.required'                 => $required,
            'id_provinsi_ktp.required'        => $required,
            'id_kabupaten_ktp.required'       => $required,
            'id_kecamatan_ktp.required'       => $required,
            'id_kelurahan_ktp.required'       => $required,
            'rt_domisili.required'            => $required,
            'rw_domisili.required'            => $required,
            'id_provinsi_domisili.required'   => $required,
            'id_kabupaten_domisili.required'  => $required,
            'id_kecamatan_domisili.required'  => $required,
            'id_kelurahan_domisili.required'  => $required,

            'jenis_kelamin.in'               => $in,
            'status_nikah.in'                => $in,
            'no_ktp.digits'                  => $digits,
            'no_ktp.unique'                  => $unique,
            'no_ktp_kk.digits'               => $digits,
            'no_ktp_kk.unique'               => $unique,
            'no_kk.digits'                   => $digits,
            'no_kk.unique'                   => $unique,
            'no_npwp.digits'                 => $digits,
            'no_npwp.unique'                 => $unique,
            'tgl_lahir.date_format'          => $date_format,
            'agama.in'                       => $in,
            'rt_ktp.integer'                 => $integer,
            'rw_ktp.integer'                 => $integer,
            'id_provinsi_ktp.integer'        => $integer,
            'id_kabupaten_ktp.integer'       => $integer,
            'id_kecamatan_ktp.integer'       => $integer,
            'id_kelurahan_ktp.integer'       => $integer,
            'rt_domisili.integer'            => $integer,
            'rw_domisili.integer'            => $integer,
            'id_provinsi_domisili.integer'   => $integer,
            'id_kabupaten_domisili.integer'  => $integer,
            'id_kecamatan_domisili.integer'  => $integer,
            'id_kelurahan_domisili.integer'  => $integer,
            'jumlah_tanggungan.integer'      => $integer,
            'no_telp.between'                => $between,
            'no_telp.unique'                 => $unique,
            'no_hp.between'                  => $between,
            'no_hp.unique'                   => $unique,

            'tgl_lahir_anak.*.date_format'   => $date_format,
            'tinggi_badan.integer'           => $integer,
            'berat_badan.integer'            => $integer,
            'pekerjaan.in'                   => $in,
            'id_prov_tempat_kerja.integer'   => $integer,
            'id_kab_tempat_kerja.integer'    => $integer,
            'id_kec_tempat_kerja.integer'    => $integer,
            'id_kel_tempat_kerja.integer'    => $integer,
            'rt_tempat_kerja.integer'        => $integer,
            'rw_tempat_kerja.integer'        => $integer,
            // 'tgl_mulai_kerja.date_format'    => $date_format,
            'no_telp_tempat_kerja.integer'   => $integer,
            'lamp_surat_cerai.mimes'         => $mimes,
            'lamp_buku_tabungan.*.mimes'     => $mimes,
            'lamp_ktp.mimes'                 => $mimes,
            'lamp_kk.mimes'                  => $mimes,
            'lamp_sku.mimes'                 => $mimes,
            'lamp_slip_gaji.mimes'           => $mimes,
            'lamp_foto_usaha.mimes'          => $mimes,
            'lamp_surat_cerai.max'           => $max,
            'lamp_buku_tabungan.*.max'       => $max,
            'lamp_ktp.max'                   => $max,
            'lamp_kk.max'                    => $max,
            'lamp_sku.max.*'                 => $max,
            'lamp_slip_gaji.max'             => $max,
            'lamp_foto_usaha.*.max'          => $max,

            // Pasangan
            'jenis_kelamin_pas.in'             => $in,
            'no_ktp_pas.digits'                => $digits,
            'no_ktp_pas.unique'                => $unique,
            'no_ktp_kk_pas.digits'             => $digits,
            'no_ktp_kk_pas.unique'             => $unique,
            'no_kk_pas.digits'                 => $digits,
            'no_kk_pas.unique'                 => $unique,
            'no_npwp_pas.digits'               => $digits,
            'no_npwp_pas.unique'               => $unique,
            'id_provinsi.integer'              => $integer,
            'id_kabupaten.integer'             => $integer,
            'id_kecamatan.integer'             => $integer,
            'id_kelurahan.integer'             => $integer,
            'rt.integer'                       => $integer,
            'rw.integer'                       => $integer,
            'tgl_lahir_pas.date_format'        => $date_format,
            'no_telp_pas.between'              => $between,
            'no_telp_pas.unique'               => $unique,
            'lamp_ktp_pas.mimes'               => $mimes,
            'lamp_kk_pas.mimes'                => $mimes,
            'lamp_ktp_pas.max'                 => $max,
            'lamp_kk_pas.max'                  => $max,
            'pekerjaan_pas.in'                 => $in,
            'rt_tempat_kerja_pas.integer'      => $integer,
            'rw_tempat_kerja_pas.integer'      => $integer,
            // 'tgl_mulai_kerja_pas.date_format'  => $date_format,
            'no_telp_tempat_kerja_pas.integer' => $integer,

            // Penjamin
            'no_ktp_pen.*.digits'                => $digits,
            'no_ktp_pen.*.unique'                => $unique,
            'no_npwp_pen.*.digits'               => $digits,
            'no_npwp_pen.*.unique'               => $unique,
            'tgl_lahir_pen.*.date_format'        => $date_format,
            'jenis_kelamin_pen.*.in'             => $in,
            'no_telp_pen.*.between'              => $between,
            'no_telp_pen.*.unique'               => $unique,
            'lamp_ktp_pen.*.mimes'               => $mimes,
            'lamp_ktp_pasangan_pen.*.mimes'      => $mimes,
            'lamp_kk_pen.*.mimes'                => $mimes,
            'lamp_buku_nikah_pen.*.mimes'        => $mimes,
            'lamp_ktp_pen.*.max'                 => $max,
            'lamp_ktp_pasangan_pen.*.max'        => $max,
            'lamp_kk_pen.*.max'                  => $max,
            'lamp_buku_nikah_pen.*.max'          => $max,
            'pekerjaan_pen.*.in'                 => $in,
            'rt_tempat_kerja_pen.*.integer'      => $integer,
            'rw_tempat_kerja_pen.*.integer'      => $integer,
            // 'tgl_mulai_kerja_pen.*.date_format'  => $date_format,
            'no_telp_tempat_kerja_pen.*.integer' => $integer,

            // Transaksi AO
            'jangka_waktu.integer'          => $integer,
            // 'jangka_waktu.in'               => $in,
            'pembayaran_bunga.integer'      => $integer,
            'akad_kredit.in'                => $in,
            'ikatan_agunan.in'              => $in,
            'biaya_provisi.integer'         => $integer,
            'biaya_administrasi.integer'    => $integer,
            'biaya_credit_checking.integer' => $integer,
            'biaya_tabungan.integer'        => $integer,

            // Verifikasi
            'ver_ktp_debt.integer'            => $integer,
            'ver_kk_debt.integer'             => $integer,
            'ver_akta_cerai_debt.integer'     => $integer,
            'ver_akta_kematian_debt.integer'  => $integer,
            'ver_rek_tabungan_debt.integer'   => $integer,
            'ver_sertifikat_debt.integer'     => $integer,
            'ver_sttp_pbb_debt.integer'       => $integer,
            'ver_imb_debt.integer'            => $integer,
            'ver_ktp_pasangan.integer'        => $integer,
            'ver_akta_nikah_pasangan.integer' => $integer,
            'ver_data_penjamin.integer'       => $integer,
            'ver_sku_debt.integer'            => $integer,
            'ver_pembukuan_usaha_debt.integer'=> $integer,

            // Validasi
            'val_data_debt.integer'       => $integer,
            'val_lingkungan_debt.integer' => $integer,
            'val_domisili_debt.integer'   => $integer,
            'val_pekerjaan_debt.integer'  => $integer,
            'val_data_pasangan.integer'   => $integer,
            'val_data_penjamin.integer'   => $integer,
            'val_agunan.integer'          => $integer,

            // Agunan Tanah
            'tipe_lokasi_agunan.*.in'         => $in,
            'rt_agunan.*.integer'             => $integer,
            'rw_agunan.*.integer'             => $integer,
            'luas_tanah.*.integer'            => $integer,
            'luas_bangunan.*.integer'         => $integer,
            'jenis_sertifikat.*.in'           => $in,
            // 'tgl_ukur_sertifikat.*.date_format' => $date_format,
            'tgl_berlaku_shgb.*.date_format'  => $date_format,

            'lamp_agunan_depan.*.mimes'       => $mimes,
            'lamp_agunan_kanan.*.mimes'       => $mimes,
            'lamp_agunan_kiri.*.mimes'        => $mimes,
            'lamp_agunan_belakang.*.mimes'    => $mimes,
            'lamp_agunan_dalam.*.mimes'       => $mimes,

            'lamp_agunan_depan.*.max'         => $max,
            'lamp_agunan_kanan.*.max'         => $max,
            'lamp_agunan_kiri.*.max'          => $max,
            'lamp_agunan_belakang.*.max'      => $max,
            'lamp_agunan_dalam.*.max'         => $max,

            // Agunan Kendaraan
            'tahun_ken.*.date_format'         => $date_format,
            'tgl_exp_pajak_ken.*.date_format' => $date_format,
            'tgl_exp_stnk_ken.*.date_format'  => $date_format,

            'lamp_agunan_depan_ken.*.mimes'   => $mimes,
            'lamp_agunan_kanan.ken.*.mimes'   => $mimes,
            'lamp_agunan_kiri.ken.*.mimes'    => $mimes,
            'lamp_agunan_belakang.ken.*.mimes'=> $mimes,
            'lamp_agunan_dalam.ken.*.mimes'   => $mimes,

            'lamp_agunan_depan.ken.*.max'     => $max,
            'lamp_agunan_kanan.ken.*.max'     => $max,
            'lamp_agunan_kiri.ken.*.max'      => $max,
            'lamp_agunan_belakang.ken.*.max'  => $max,
            'lamp_agunan_dalam.ken.*.max'     => $max,

            // Kapasitas Bulanan
            'pemasukan_debitur.integer'     => $integer,
            'pemasukan_pasangan.integer'    => $integer,
            'pemasukan_penjamin.integer'    => $integer,
            'biaya_rumah_tangga.integer'    => $integer,
            'biaya_transport.integer'       => $integer,
            'biaya_pendidikan.integer'      => $integer,
            'angsuran.integer'              => $integer,
            'biaya_telp_listr_air.integer'  => $integer,
            'biaya_lain.integer'            => $integer,

            // Pendapatan Usaha
            'pemasukan_tunai.integer'      => $integer,
            'pemasukan_kredit.integer'     => $integer,
            'biaya_sewa.integer'           => $integer,
            'biaya_gaji_pegawai.integer'   => $integer,
            'biaya_belanja_brg.integer'    => $integer,
            'biaya_telp_listr_air.integer' => $integer,
            'biaya_sampah_kemanan.integer' => $integer,
            'biaya_kirim_barang.integer'   => $integer,
            'biaya_hutang_dagang.integer'  => $integer,
            'biaya_angsuran.integer'       => $integer,
            'biaya_lain_lain.integer'      => $integer,

            // Pemeriksaan Agunan Kendaraan
            'status_pengguna_ken.*.in'  => $in,
            'jml_roda_ken.*.integer'    => $integer,
            'km_ken.*.integer'          => $integer,

            // Pemeriksaan Agunan Tanah
            'status_penghuni.*.in'              => $in,
            // 'bentuk_bangunan.*.in'              => $in,
            'kondisi_bangunan.*.in'             => $in,
            // 'nilai_taksasi_agunan.*.integer'    => $integer,
            // 'nilai_taksasi_bangunan.*.integer'  => $integer,
            'tgl_taksasi.*.date_format'         => $date_format,
            // 'nilai_likuidasi.*.integer'         => $integer,

            // Mutasi Bank pada CA
            'urutan_mutasi.*.integer'           => $integer,
            'no_rekening_mutasi.*.integer'      => $integer,
            'frek_debet_mutasi.*.*.integer'     => $integer,
            'nominal_debet_mutasi.*.*.integer'  => $integer,
            'frek_kredit_mutasi.*.*.integer'    => $integer,
            'nominal_kredit_mutasi.*.*.integer' => $integer,
            'saldo_mutasi.*.*.integer'          => $integer,

            // Data History Bank pada CA
            'no_rekening.integer'           => $integer,
            'penghasilan_per_tahun.integer' => $integer,
            'pemasukan_per_bulan.integer'   => $integer,
            'frek_trans_pemasukan.in'       => ':attribute harus salah satu dari jenis berikut :values, A untuk frek. 1 -  5 Kali, B untuk untuk frek. 2.6 - 10 kali, C untuk frek. lebih dari 10 kali',
            'pengeluaran_per_bulan.integer' => $integer,
            'frek_trans_pengeluaran.in'     => ':attribute harus salah satu dari jenis berikut :values, A untuk frek. 1 -  5 Kali, B untuk untuk frek. 2.6 - 10 kali, C untuk frek. 3.1 - 15 kali, D untuk frek. lebih dari 15 kali',
            'tujuan_pengeluaran_dana.in'    => $in,

            // Info ACC
            'plafon_acc.*.integer'          => $integer,
            'baki_debet_acc.*.integer'      => $integer,
            'angsuran_acc.*.integer'        => $integer,

            // Ringkasan Analisa CA
            'kuantitatif_ttl_pendapatan.integer'  => $integer,
            'kuantitatif_ttl_pengeluaran.integer' => $integer,
            'kuantitatif_pendapatan.integer'      => $integer,
            'kuantitatif_angsuran.integer'        => $integer,
            'kuantitatif_ltv.integer'             => $integer,
            'kuantitatif_dsr.integer'             => $integer,
            'kuantitatif_idir.integer'            => $integer,
            'kuantitatif_hasil.integer'           => $integer,

            // Rekomendasi Pinjaman pada CA
            'penyimpangan_struktur.in'     => $in,
            'penyimpangan_dokumen.in'      => $in,
            'recom_nilai_pinjaman.integer' => $integer,
            'recom_tenor.integer'          => $integer,
            'recom_angsuran.integer'       => $integer,
            'recom_produk_kredit.integer'  => $integer,

            // Rekomendasi CA
            'plafon_kredit' => $integer,

            // Asuransi Jiwa pada CA
            // 'jangka_waktu_as_jiwa.integer'        => $integer,
            // 'jangka_waktu_as_jiwa.in'             => $in,
            'nilai_pertanggungan_as_jiwa.integer' => $integer,
            'jatuh_tempo_as_jiwa.date_format'     => $date_format,
            'berat_badan_as_jiwa.integer'         => $integer,
            'tinggi_badan_as_jiwa.integer'        => $integer,
            'umur_nasabah_as_jiwa.integer'        => $integer,

            // Asuransi Jaminan pada CA
            // 'jangka_waktu_as_jaminan.*.integer'        => $integer,
            // 'jangka_waktu_as_jaminan.in'             => $in,
            'nilai_pertanggungan_as_jaminan.*.integer' => $integer,
            'jatuh_tempo_as_jaminan.*.date_format'     => $date_format,

            // Transaksi CAA
            // 'penyimpangan.required'       => $required,
            'penyimpangan.in'             => $in,
            'team_caa.*.required'         => $required,
            'team_caa.*.integer'          => $integer,
            'file_report_mao.mimes'       => $in,
            'file_report_mao.max'         => $max,
            'file_report_mca.mimes'       => $in,
            'file_report_mca.max'         => $max,
            'status_file_agunan.required' => $required,
            'status_file_agunan.in'       => $in,
            'status_file_usaha.required'  => $required,
            'status_file_usaha.in'        => $in,
            // 'file_agunan.*.required'      => $required,
            // 'file_agunan.*.mimes'         => $in,
            // 'file_agunan.*.max'           => $max,
            // 'file_usaha.*.required'       => $required,
            // 'file_usaha.*.mimes'          => $in,
            // 'file_usaha.*.max'            => $max,
            'file_lain.mimes'             => $in,
            'file_lain.max'               => $max,
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
