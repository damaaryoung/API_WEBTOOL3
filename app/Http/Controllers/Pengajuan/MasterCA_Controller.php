<?php

namespace App\Http\Controllers\Pengajuan;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\CC\PemeriksaanAgunTan;
use App\Models\CC\PemeriksaanAgunKen;
use App\Models\CC\AgunanKendaraan;
use App\Models\CC\AgunanTanah;
use App\Models\CC\KapBulanan;
use App\Models\CC\KeuanganUsaha;
// use App\Models\AreaKantor\Cabang;
// use App\Models\Wilayah\Kabupaten;
// use App\Models\Wilayah\Kecamatan;
// use App\Models\Wilayah\Kelurahan;
// use App\Models\Wilayah\Provinsi;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use App\Models\Bisnis\TransCA;
use App\Models\Bisnis\TransAO;
use App\Models\Bisnis\TransSo;
use Illuminate\Http\Request;
// use App\Models\CC\Pasangan;
use App\Models\CC\Penjamin;
// use App\Models\CC\Debitur;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
// use DB;

class MasterCA_Controller extends BaseController
{
    public function index(Request $req){
        $user_id  = $req->auth->user_id;
        $username = $req->auth->username;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC(CA). Harap daftarkan diri sebagai PIC(CA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $query = TransAO::with('so')->where('id_cabang', $id_cabang)->where('status_ao', 1)->get();

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }


        foreach ($query as $key => $val) {

            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            }elseif($val->status_ao == 2){
                $status_ao = 'not recommend';
            }else{
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so,
                // 'id'             => $val->id,
                'nomor_ao'       => $val->nomor_ao,
                // 'user_id'        => $val->user_id,
                'id_pic'         => $val->id_pic,
                'id_cabang'      => $id_cabang,
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_so'        => $val->so['nama_so'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                'status_ao'      => $status_ao
            ];
        }

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

    public function show($id, Request $req){
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();
        $id_cabang = $pic->id_mk_cabang;

        $val = TransAO::with('so')->where('id_cabang', $id_cabang)->first();

        if (!$val) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",",$val->so['id_penjamin']);

        $id_agu_ta = explode (",",$val->so['id_agunan_tanah']);
        $id_agu_ke = explode (",",$val->so['id_agunan_kendaraan']);

        $id_pe_agu_ta = explode (",",$val->so['id_periksa_agunan_tanah']);
        $id_pe_agu_ke = explode (",",$val->so['id_periksa_agunan_kendaraan']);

        $penjamin = Penjamin::whereIn('id', $id_penj)->get();

        $tanah     = AgunanTanah::whereIn('id', $id_agu_ta)->get();
        $kendaraan = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

        $pe_tanah     = PemeriksaanAgunTan::whereIn('id', $id_pe_agu_ta)->get();
        $pe_kendaraan = PemeriksaanAgunKen::whereIn('id', $id_pe_agu_ke)->get();

        $check_KapBul = KapBulanan::where('id_calon_debitur', $val->so['id_calon_debt'])->first();

        $check_usaha = KeuanganUsaha::where('id_calon_debitur', $val->so['id_calon_debt'])->first();

        if ($penjamin != '[]') {
            foreach ($penjamin as $key => $value) {
                $pen[$key] = [
                    "id"                    => $value->id,
                    "nama_ktp"              => $value->nama_ktp,
                    "nama_ibu_kandung"      => $value->nama_ibu_kandung,
                    "no_ktp"                => $value->no_ktp,
                    "no_npwp"               => $value->no_npwp,
                    "tempat_lahir"          => $value->tempat_lahir,
                    "tgl_lahir"             => Carbon::parse($value->tgl_lahir)->format('d-m-Y'),
                    "jenis_kelamin"         => $value->jenis_kelamin,
                    "alamat_ktp"            => $value->alamat_ktp,
                    "no_telp"               => $value->no_telp,
                    "hubungan_debitur"      => $value->hubungan_debitur,
                    "pekerjaan" => [
                        "nama_pekerjaan"        => $value->pekerjaan,
                        "posisi_pekerjaan"      => $value->posisi_pekerjaan,
                        "nama_tempat_kerja"     => $value->nama_tempat_kerja,
                        "jenis_pekerjaan"       => $value->jenis_pekerjaan,
                        "tgl_mulai_kerja"       => Carbon::parse($value->tgl_mulai_kerja)->format('d-m-Y'),
                        "no_telp_tempat_kerja"  => $value->no_telp_tempat_kerja,
                        'alamat' => [
                            'alamat_singkat' => $value->alamat_tempat_kerja,
                            'rt'             => $value->rt_tempat_kerja,
                            'rw'             => $value->rw_tempat_kerja,
                            'kelurahan' => [
                                'id'    => $value->penj['kel_kerja']['id'],
                                'nama'  => $value->penj['kel_kerja']['nama']
                            ],
                            'kecamatan' => [
                                'id'    => $value->penj['kec_kerja']['id'],
                                'nama'  => $value->penj['kec_kerja']['nama']
                            ],
                            'kabupaten' => [
                                'id'    => $value->penj['kab_kerja']['id'],
                                'nama'  => $value->penj['kab_kerja']['nama'],
                            ],
                            'provinsi'  => [
                                'id'   => $value->penj['prov_kerja']['id'],
                                'nama' => $value->penj['prov_kerja']['nama'],
                            ],
                            'kode_pos' => $value->penj['kel_kerja']['kode_pos']
                        ]
                    ],
                    "lampiran" => [
                        "lamp_ktp"          => $value->lamp_ktp,
                        "lamp_ktp_pasangan" => $value->lamp_ktp_pasangan,
                        "lamp_kk"           => $value->lamp_kk,
                        "lamp_buku_nikah"   => $value->lamp_buku_nikah
                    ]
                ];
            }
        }else{
            $pen = null;
        }

        if ($tanah != '[]') {
            foreach ($tanah as $value) {
                $tan[] = [
                    'id'          => $value->id,
                    'tipe_lokasi' => $value->tipe_lokasi,
                    'alamat' => [
                        'alamat_singkat' => $value->alamat,
                        'rt' => $value->rt,
                        'rw' => $value->rw,
                        'kelurahan' => [
                            'id'    => $value->id_kelurahan,
                            'nama'  => $value->kel['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $value->id_kecamatan,
                            'nama'  => $value->kec['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $value->id_kabupaten,
                            'nama'  => $value->kab['nama'],
                        ],
                        'provinsi' => [
                            'id'    => $value->id_provinsi,
                            'nama'  => $value->prov['nama']
                        ],
                        'kode_pos' => $value->kel['kode_pos'],
                        'luas_tanah'    => $value->luas_tanah,
                        'luas_bangunan' => $value->luas_bangunan,
                        'nama_pemilik_sertifikat' => $value->nama_pemilik_sertifikat,
                        'jenis_sertifikat'        => $value->jenis_sertifikat,
                        'no_sertifikat'           => $value->no_sertifikat,
                        'tgl_ukur_sertifikat'     => $value->tgl_ukur_sertifikat,
                        'tgl_berlaku_shgb'        => $value->tgl_berlaku_shgb,
                        'no_imb' => $value->no_imb,
                        'njop'   => $value->njop,
                        'nop'    => $value->nop,
                        'lampiran' => [
                            'lamp_agunan_depan' => $value->lamp_agunan_depan,
                            'lamp_agunan_kanan' => $value->lamp_agunan_kanan,
                            'lamp_agunan_kiri' => $value->lamp_agunan_kiri,
                            'lamp_agunan_belakang' => $value->lamp_agunan_belakang,
                            'lamp_agunan_dalam' => $value->lamp_agunan_dalam,
                            'lamp_sertifikat' => $value->lamp_sertifikat,
                            'lamp_imb' => $value->lamp_imb,
                            'lamp_pbb' => $value->lamp_pbb
                        ]
                    ],
                ];
            }
        }else{
            $tan = null;
        }

        if ($kendaraan != '[]') {
            foreach ($kendaraan as $value) {
                $ken[] = [
                    'id'            => $value->id,
                    'no_bpkb'       => $value->no_bpkb,
                    'nama_pemilik'  => $value->nama_pemilik,
                    'alamat_pemilik'=> $value->alamat_pemilik,
                    'merk'          => $value->merk,
                    'jenis'         => $value->jenis,
                    'no_rangka'     => $value->no_rangka,
                    'no_mesin'      => $value->no_mesin,
                    'warna'         => $value->warna,
                    'tahun'         => $value->tahun,
                    'no_polisi'     => $value->no_polisi,
                    'no_stnk'       => $value->no_stnk,
                    'tgl_kadaluarsa_pajak'=> $value->tgl_kadaluarsa_pajak,
                    'tgl_kadaluarsa_stnk' => $value->tgl_kadaluarsa_stnk,
                    'no_faktur'         => $value->no_faktur,
                    'lampiran'  => [
                        'lamp_agunan_depan' => $value->lamp_agunan_depan,
                        'lamp_agunan_kanan' => $value->lamp_agunan_kanan,
                        'lamp_agunan_kiri'  => $value->lamp_agunan_kiri,
                        'lamp_agunan_belakang' => $value->lamp_agunan_belakang,
                        'lamp_agunan_dalam' => $value->lamp_agunan_dalam
                    ]
                ];
            }
        }else{
            $ken = null;
        }

        if ($pe_tanah != '[]') {
            foreach ($pe_tanah as $value) {
                $pe_ta[] = [
                    'id'                => $value->id,
                    'id_agunan_tanah'   => $value->id_agunan_tanah,
                    'nama_penghuni'     => $value->nama_penghuni,
                    'status_penghuni'   => $value->status_penghuni,
                    'bentuk_bangunan'   => $value->bentuk_bangunan,
                    'kondisi_bangunan'  => $value->kondisi_bangunan,
                    'fasilitas'         => $value->fasilitas,
                    'listrik'           => $value->listrik,
                    'nilai_taksasi_agunan'   => $value->nilai_taksasi_agunan,
                    'nilai_taksasi_bangunan' => $value->nilai_taksasi_bangunan,
                    'tgl_taksasi'     => $value->tgl_taksasi,
                    'nilai_likuidasi' => $value->nilai_likuidasi
                ];
            }
        }else{
            $pe_ta = null;
        }

        if ($pe_kendaraan != '[]') {
            foreach ($pe_kendaraan as $value) {
                $pe_ke[] = [
                    'id'                  => $value->id,
                    'id_agunan_kendaraan' => $value->id_agunan_kendaraan,
                    'nama_pengguna'       => $value->nama_pengguna,
                    'status_pengguna'     => $value->status_pengguna,
                    'jml_roda_kendaraan'  => $value->jml_roda_kendaraan,
                    'kondisi_kendaraan'   => $value->kondisi_kendaraan,
                    'keberadaan_kendaraan'=> $value->keberadaan_kendaraan,
                    'body'                => $value->body,
                    'interior'            => $value->interior,
                    'km'                  => $value->km,
                    'modifikasi'          => $value->modifikasi,
                    'aksesoris'           => $value->aksesoris,
                ];
            }
        }else{
            $pe_ke = null;
        }

        if ($check_KapBul == null) {
            $kapbul = null;
        }else{
            $kapbul = array(
                'pemasukan' => array(
                    'debitur' => $check_KapBul->pemasukan_cadebt,
                    'pasangan'=> $check_KapBul->pemasukan_pasangan,
                    'penjamin'=> $check_KapBul->pemasukan_penjamin,
                ),
                'pengeluaran' => array(
                    'rumah_tangga'  => $check_KapBul->biaya_rumah_tangga,
                    'transport'     => $check_KapBul->biaya_transport,
                    'pendidikan'    => $check_KapBul->biaya_pendidikan,
                    'telp_list_air' => $check_KapBul->biaya_telp_listr_air,
                    'angsuran'      => $check_KapBul->angsuran,
                    'lain_lain'     => $check_KapBul->biaya_lain
                )
            );
        }

        if ($check_usaha == null) {
            $usaha = null;
        }else{
            $usaha = array(
                'pendapatan' => array(
                    'tunai' => $check_usaha->pemasukan_tunai,
                    'kredit'=> $check_usaha->pemasukan_kredit
                ),
                'pengeluaran' => array(
                    'biaya_sewa'           => $check_usaha->biaya_sewa,
                    'biaya_gaji_pegawai'   => $check_usaha->biaya_gaji_pegawai,
                    'biaya_belanja_brg'    => $check_usaha->biaya_belanja_brg,
                    'biaya_telp_listr_air' => $check_usaha->biaya_telp_listr_air,
                    'biaya_sampah_kemanan' => $check_usaha->biaya_sampah_kemanan,
                    'biaya_kirim_barang'   => $check_usaha->biaya_kirim_barang,
                    'biaya_hutang_dagang'  => $check_usaha->biaya_hutang_dagang,
                    'angsuran'             => $check_usaha->biaya_angsuran,
                    'lain_lain'            => $check_usaha->biaya_lain_lain
                )
            );
        }

        if ($val->status_ao == 1) {
            $status_ao = 'recommend';
        }elseif($val->status_ao == 2){
            $status_ao = 'not recommend';
        }else{
            $status_ao = 'waiting';
        }

        $data[] = [
            'id'        => $val->id,
            'nomor_ao'  => $val->nomor_ao,
            'id_pic'      => $val->id_pic,
            'id_cabang'   => $val->so['pic']['id_mk_cabang'],
            'nama_cabang' => $val->so['pic']['cabang']['nama'],
            'asaldata'  => [
                'id'   => $val->so['asaldata']['id'],
                'nama' => $val->so['asaldata']['nama']
            ],
            'nama_marketing' => $val->so['nama_marketing'],
            'nama_so'        => $val->so['nama_so'],
            'fasilitas_pinjaman'  => [
                'id'              => $val->so['id_fasilitas_pinjaman'],
                'jenis_pinjaman'  => $val->so['faspin']['jenis_pinjaman'],
                'tujuan_pinjaman' => $val->so['faspin']['tujuan_pinjaman'],
                'plafon'          => $val->so['faspin']['plafon'],
                'tenor'           => $val->so['faspin']['tenor'],
            ],
            'data_debitur' => [
                'id'                    => $val->so['id_calon_debt'],
                'nama_lengkap'          => $val->so['debt']['nama_lengkap'],
                'gelar_keagamaan'       => $val->so['debt']['gelar_keagamaan'],
                'gelar_pendidikan'      => $val->so['debt']['gelar_pendidikan'],
                'jenis_kelamin'         => $val->so['debt']['jenis_kelamin'],
                'status_nikah'          => $val->so['debt']['status_nikah'],
                'ibu_kandung'           => $val->so['debt']['ibu_kandung'],
                'tinggi_badan'          => $val->so['debt']['tinggi_badan'],
                'berat_badan'           => $val->so['debt']['berat_badan'],
                'no_ktp'                => $val->so['debt']['no_ktp'],
                'no_ktp_kk'             => $val->so['debt'][''],
                'no_kk'                 => $val->so['debt']['no_ktp_kk'],
                'no_npwp'               => $val->so['debt']['no_npwp'],
                'tempat_lahir'          => $val->so['debt']['tempat_lahir'],
                'tgl_lahir'             => Carbon::parse($val->so['debt']['tgl_lahir'])->format('d-m-Y'),
                'agama'                 => $val->so['debt']['agama'],

                'alamat_ktp' => [
                    'alamat_singkat' => $val->so['debt']['alamat_ktp'],
                    'rt'     => $val->so['debt']['rt_ktp'],
                    'rw'     => $val->so['debt']['rw_ktp'],
                    'kelurahan' => [
                        'id'    => $val->so['debt']['kel_ktp']['id'],
                        'nama'  => $val->so['debt']['kel_ktp']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->so['debt']['kec_ktp']['id'],
                        'nama'  => $val->so['debt']['kec_ktp']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->so['debt']['kab_ktp']['id'],
                        'nama'  => $val->so['debt']['kab_ktp']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->so['debt']['prov_ktp']['id'],
                        'nama' => $val->so['debt']['prov_ktp']['nama'],
                    ],
                    'kode_pos' => $val->so['debt']['kel_ktp']['kode_pos']
                ],
                'alamat_domisili' => [
                    'alamat_singkat' => $val->so['debt']['alamat_domisili'],
                    'rt'             => $val->so['debt']['rt_domisili'],
                    'rw'             => $val->so['debt']['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $val->so['debt']['kel_dom']['id'],
                        'nama'  => $val->so['debt']['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $val->so['debt']['kec_dom']['id'],
                        'nama'  => $val->so['debt']['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $val->so['debt']['kab_dom']['id'],
                        'nama'  => $val->so['debt']['kab_dom']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $val->so['debt']['prov_dom']['id'],
                        'nama' => $val->so['debt']['prov_dom']['nama'],
                    ],
                    'kode_pos' => $val->so['debt']['kel_dom']['kode_pos']
                ],
                "pekerjaan" => [
                    "nama_pekerjaan"        => $val->so['debt']['pekerjaan'],
                    "posisi_pekerjaan"      => $val->so['debt']['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $val->so['debt']['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $val->so['debt']['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => Carbon::parse($val->so['debt']['tgl_mulai_kerja'])->format('d-m-Y'),
                    "no_telp_tempat_kerja"  => $val->so['debt']['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $val->so['debt']['alamat_tempat_kerja'],
                        'rt'             => $val->so['debt']['rt_tempat_kerja'],
                        'rw'             => $val->so['debt']['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $val->so['debt']['kel_kerja']['id'],
                            'nama'  => $val->so['debt']['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $val->so['debt']['kec_kerja']['id'],
                            'nama'  => $val->so['debt']['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $val->so['debt']['kab_kerja']['id'],
                            'nama'  => $val->so['debt']['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'   => $val->so['debt']['prov_kerja']['id'],
                            'nama' => $val->so['debt']['prov_kerja']['nama'],
                        ],
                        'kode_pos' => $val->so['debt']['kel_kerja']['kode_pos']
                    ]
                ],
                'pendidikan_terakhir'   => $val->so['debt']['pendidikan_terakhir'],
                'jumlah_tanggungan'     => $val->so['debt']['jumlah_tanggungan'],
                'no_telp'               => $val->so['debt']['no_telp'],
                'no_hp'                 => $val->so['debt']['no_hp'],
                'alamat_surat'          => $val->so['debt']['alamat_surat'],
                'lampiran' => [
                    'lamp_ktp'              => $val->so['debt']['lamp_ktp'],
                    'lamp_kk'               => $val->so['debt']['lamp_kk'],
                    'lamp_buku_tabungan'    => $val->so['debt']['lamp_buku_tabungan'],
                    'lamp_sertifikat'       => $val->so['debt']['lamp_sertifikat'],
                    'lamp_sttp_pbb'         => $val->so['debt']['lamp_sttp_pbb'],
                    'lamp_imb'              => $val->so['debt']['lamp_imb']
                ]
            ],
            'data_pasangan' => [
                'id'               => $val->so['id_pasangan'],
                'nama_lengkap'     => $val->so['pas']['nama_lengkap'],
                'nama_ibu_kandung' => $val->so['pas']['nama_ibu_kandung'],
                'jenis_kelamin'    => $val->so['pas']['jenis_kelamin'],
                'no_ktp'           => $val->so['pas']['no_ktp'],
                'no_ktp_kk'        => $val->so['pas']['no_ktp_kk'],
                'no_npwp'          => $val->so['pas']['no_npwp'],
                'tempat_lahir'     => $val->so['pas']['tempat_lahir'],
                'tgl_lahir'        => Carbon::parse($val->so['pas']['tgl_lahir'])->format('d-m-Y'),
                'alamat_ktp'       => $val->so['pas']['alamat_ktp'],
                'no_telp'          => $val->so['pas']['no_telp'],
                'pekerjaan' => [
                    "nama_pekerjaan"        => $val->so['pas']['pekerjaan'],
                    "posisi_pekerjaan"      => $val->so['pas']['posisi_pekerjaan'],
                    "nama_tempat_kerja"     => $val->so['pas']['nama_tempat_kerja'],
                    "jenis_pekerjaan"       => $val->so['pas']['jenis_pekerjaan'],
                    "tgl_mulai_kerja"       => Carbon::parse($val->so['pas']['tgl_mulai_kerja'])->format('d-m-Y'),
                    "no_telp_tempat_kerja"  => $val->so['pas']['no_telp_tempat_kerja'],
                    'alamat' => [
                        'alamat_singkat' => $val->so['pas']['alamat_tempat_kerja'],
                        'rt'             => $val->so['pas']['rt_tempat_kerja'],
                        'rw'             => $val->so['pas']['rw_tempat_kerja'],
                        'kelurahan' => [
                            'id'    => $val->so['pas']['kel_kerja']['id'],
                            'nama'  => $val->so['pas']['kel_kerja']['nama']
                        ],
                        'kecamatan' => [
                            'id'    => $val->so['pas']['kec_kerja']['id'],
                            'nama'  => $val->so['pas']['kec_kerja']['nama']
                        ],
                        'kabupaten' => [
                            'id'    => $val->so['pas']['kab_kerja']['id'],
                            'nama'  => $val->so['pas']['kab_kerja']['nama'],
                        ],
                        'provinsi'  => [
                            'id'   => $val->so['pas']['prov_kerja']['id'],
                            'nama' => $val->so['pas']['prov_kerja']['nama'],
                        ],
                        'kode_pos' => $val->so['pas']['kel_kerja']['kode_pos']
                    ]
                ],
                'lampiran' => [
                    'lamp_ktp'         => $val->so['pas']['lamp_ktp'],
                    'lamp_buku_nikah'  => $val->so['pas']['lamp_buku_nikah']
                ]
            ],
            'data_penjamin' => $pen,
            'data_agunan' => [
                'agunan_tanah'     => $tan,
                'agunan_kendaraan' => $ken
            ],
            'pemeriksaan' => [
                'agunan_tanah' => $pe_ta,
                'agunan_kendaraan' => $pe_ke
            ],
            'kapasitas_bulanan' => $kapbul,
            'pendapatan_usaha'  => $usaha,
            'status_ao'         => $status_ao
        ];

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $data[0]
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function update($id, Request $req) {
        $user_id  = $req->auth->user_id;
        $username = $req->auth->user;

        $PIC = PIC::where('user_id', $user_id)->first();

        if ($PIC == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC(CA). Harap daftarkan diri sebagai PIC pada form PIC(CA) atau hubungi bagian IT"
            ], 404);
        }

        $countCA = TransCA::latest('id','nomor_ca')->first();

        if (!$countCA) {
            $lastNumb = 1;
        }else{
            $no = $countCA->nomor_ca;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $PIC->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ao = $PIC->id_mk_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb;

        $check = TransSo::where('id',$id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",",$check->id_penjamin);

        $lamp_dir = 'public/lamp_trans.'.$check->nomor_so;

        $now   = Carbon::now()->toDateTimeString();

        if ($req->input('nama_anak')) {
            for ($i = 0; $i < count($req->nama_anak); $i++){
                $namaAnak[] = empty($req->nama_anak[$i]) ? null[$i] : $req->nama_anak[$i];

                $tglLahirAnak[] = empty($req->tgl_lahir_anak[$i]) ? null[$i] : Carbon::parse($req->tgl_lahir_anak[$i])->format('Y-m-d');
            }

            $nama_anak    = implode(",", $namaAnak);
            $tgl_lhr_anak = implode(",", $tglLahirAnak);
        }else{
            $nama_anak = $check->debt['nama_anak'];
            $tgl_lhr_anak = $check->debt['tgl_lahir_anak'];
        }

        // Lampiran Debitur
        if($file = $req->file('lamp_ktp')){
            $path = $lamp_dir.'/debitur';
            $name = 'ktp.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_ktp']))
            {
                File::delete($check->debt['lamp_ktp']);
            }

            $file->move($path,$name);

            $ktpDebt = $path.'/'.$name;
        }else{
            $ktpDebt = $check->debt['lamp_ktp'];
        }

        if($file = $req->file('lamp_kk')){
            $path = $lamp_dir.'/debitur';
            $name = 'kk.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_kk']))
            {
                File::delete($check->debt['lamp_kk']);
            }

            $file->move($path,$name);

            $kkDebt = $path.'/'.$name;
        }else{
            $kkDebt = $check->debt['lamp_kk'];
        }

        if($file = $req->file('lamp_sertifikat')){
            $path = $lamp_dir.'/debitur';
            $name = 'sertifikat.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_sertifikat']))
            {
                File::delete($check->debt['lamp_sertifikat']);
            }

            $file->move($path,$name);

            $sertifikatDebt = $path.'/'.$name;
        }else{
            $sertifikatDebt = $check->debt['lamp_sertifikat'];
        }

        if($file = $req->file('lamp_pbb')){
            $path = $lamp_dir.'/debitur';
            $name = 'pbb.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_pbb']))
            {
                File::delete($check->debt['lamp_pbb']);
            }

            $file->move($path,$name);

            $pbbDebt = $path.'/'.$name;
        }else{
            $pbbDebt = $check->debt['lamp_pbb'];
        }

        if($file = $req->file('lamp_imb')){
            $path = $lamp_dir.'/debitur';
            $name = 'imb.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_imb']))
            {
                File::delete($check->debt['lamp_imb']);
            }

            $file->move($path,$name);

            $imbDebt = $path.'/'.$name;
        }else{
            $imbDebt = $check->debt['lamp_imb'];
        }

        if($file = $req->file('lamp_buku_tabungan')){
            $path = $lamp_dir.'/debitur';
            $name = 'buku_tabungan.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_buku_tabungan']))
            {
                File::delete($check->debt['lamp_buku_tabungan']);
            }

            $file->move($path,$name);

            $tabungan = $path.'/'.$name;
        }else{
            $tabungan = $check->debt['lamp_buku_tabungan'];
        }

        if($file = $req->file('lamp_sku')){
            $path = $lamp_dir.'/debitur';
            $name = 'sku.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_sku']))
            {
                File::delete($check->debt['lamp_sku']);
            }

            $file->move($path,$name);

            $sku = $path.'/'.$name;
        }else{
            $sku = $check->debr['lamp_sku'];
        }

        if($file = $req->file('lamp_slip_gaji')){
            $path = $lamp_dir.'/debitur';
            $name = 'slip_gaji.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_slip_gaji']))
            {
                File::delete($check->debt['lamp_slip_gaji']);
            }

            $file->move($path,$name);

            $slipGaji = $path.'/'.$name;
        }else{
            $slipGaji = $check->debt['lamp_slip_gaji'];
        }

        if($file = $req->file('lamp_foto_usaha')){
            $path = $lamp_dir.'/debitur';
            $name = 'tempat_usaha.'.$file->getClientOriginalExtension();

            if(!empty($check->debt['lamp_foto_usaha']))
            {
                File::delete($check->debt['lamp_foto_usaha']);
            }

            $file->move($path,$name);

            $fotoUsaha = $path.'/'.$name;
        }else{
            $fotoUsaha = $check->debt['lamp_foto_usaha'];
        }

        // Data Debitur
        $dataDebitur = array(
            'nama_lengkap'          => empty($req->input('nama_lengkap')) ? $check->debt['nama_lengkap'] : $req->input('nama_lengkap'),
            'gelar_keagamaan'       => empty($req->input('gelar_keagamaan')) ? $check->debt['gelar_keagamaan'] : $req->input('gelar_keagamaan'),
            'gelar_pendidikan'      => empty($req->input('gelar_pendidikan')) ? $check->debt['gelar_pendidikan'] : $req->input('gelar_pendidikan'),
            'jenis_kelamin'         => empty($req->input('jenis_kelamin')) ? strtoupper($check->debt['jenis_kelamin']) : strtoupper($req->input('jenis_kelamin')),
            'status_nikah'          => empty($req->input('status_nikah')) ? strtoupper($check->debt['status_nikah']) : strtoupper($req->input('status_nikah')),
            'ibu_kandung'           => empty($req->input('ibu_kandung')) ? $check->debt['ibu_kandung'] : $req->input('ibu_kandung'),
            'no_ktp'                => empty($req->input('no_ktp')) ? $check->debt['no_ktp'] : $req->input('no_ktp'),
            'no_ktp_kk'             => empty($req->input('no_ktp_kk')) ? $check->debt['no_ktp_kk'] : $req->input('no_ktp_kk'),
            'no_kk'                 => empty($req->input('no_kk')) ? $check->debt['no_kk'] : $req->input('no_kk'),
            'no_npwp'               => empty($req->input('no_npwp')) ? $check->debt['no_npwp'] : $req->input('no_npwp'),
            'tempat_lahir'          => empty($req->input('tempat_lahir')) ? $check->debt['tempat_lahir'] : $req->input('tempat_lahir'),
            'tgl_lahir'             => empty($req->input('tgl_lahir')) ? $check->debt['tgl_lahir'] : Carbon::parse($req->input('tgl_lahir'))->format('Y-m-d'),
            'agama'                 => empty($req->input('agama')) ? $check->debt['agama'] : strtoupper($req->input('agama')),
            'alamat_ktp'            => empty($req->input('alamat_ktp')) ? $check->debt['alamat_ktp'] : $req->input('alamat_ktp'),
            'rt_ktp'                => empty($req->input('rt_ktp')) ? $check->debt['rt_ktp'] : $req->input('rt_ktp'),
            'rw_ktp'                => empty($req->input('rw_ktp')) ? $check->debt['rw_ktp'] : $req->input('rw_ktp'),
            'id_prov_ktp'           => empty($req->input('id_provinsi_ktp')) ? $check->debt['id_prov_ktp'] : $req->input('id_provinsi_ktp'),
            'id_kab_ktp'            => empty($req->input('id_kabupaten_ktp')) ? $check->debt['id_kab_ktp'] : $req->input('id_kabupaten_ktp'),
            'id_kec_ktp'            => empty($req->input('id_kecamatan_ktp')) ? $check->debt['id_kec_ktp'] : $req->input('id_kecamatan_ktp'),
            'id_kel_ktp'            => empty($req->input('id_kelurahan_ktp')) ? $check->debt['id_kel_ktp'] : $req->input('id_kelurahan_ktp'),

            'alamat_domisili'       => empty($req->input('alamat_domisili')) ? $check->debt['alamat_domisili'] : $req->input('alamat_domisili'),
            'rt_domisili'           => empty($req->input('rt_domisili')) ? $check->debt['rt_domisili'] : $req->input('rt_domisili'),
            'rw_domisili'           => empty($req->input('rw_domisili')) ? $check->debt['rw_domisili'] : $req->input('rw_domisili'),
            'id_prov_domisili'      => empty($req->input('id_provinsi_domisili')) ? $check->debt['id_prov_domisili'] : $req->input('id_provinsi_domisili'),
            'id_kab_domisili'       => empty($req->input('id_kabupaten_domisili')) ? $check->debt['id_kab_domisili'] : $req->input('id_kabupaten_domisili'),
            'id_kec_domisili'       => empty($req->input('id_kecamatan_domisili')) ? $check->debt['id_kec_domisili'] : $req->input('id_kecamatan_domisili'),
            'id_kel_domisili'       => empty($req->input('id_kelurahan_domisili')) ? $check->debt['id_kel_domisili'] : $req->input('id_kelurahan_domisili'),

            'pendidikan_terakhir'   => empty($req->input('pendidikan_terakhir')) ? $check->debt['pendidikan_terakhir'] : $req->input('pendidikan_terakhir'),
            'jumlah_tanggungan'     => empty($req->input('jumlah_tanggungan')) ? $check->debt['jumlah_tanggungan'] : $req->input('jumlah_tanggungan'),
            'no_telp'               => empty($req->input('no_telp')) ? $check->debt['no_telp'] : $req->input('no_telp'),
            'no_hp'                 => empty($req->input('no_hp')) ? $check->debt['no_hp'] : $req->input('no_hp'),
            'alamat_surat'          => empty($req->input('alamat_surat')) ? $check->debt['alamat_surat'] : $req->input('alamat_surat'),
            'lamp_ktp'              => $ktpDebt,
            'lamp_kk'               => $kkDebt,
            'lamp_sertifikat'       => $sertifikatDebt,
            'lamp_sttp_pbb'         => $pbbDebt,
            'lamp_imb'              => $imbDebt,

            'tinggi_badan'          => empty($req->input('tinggi_badan')) ? $check->debt['tinggi_badan'] : $req->input('tinggi_badan'),
            'berat_badan'           => empty($req->input('berat_badan')) ? $check->debt['berat_badan'] : $req->input('berat_badan'),
            'nama_anak'             => $nama_anak,
            'tgl_lahir_anak'        => $tgl_lhr_anak,
            'alamat_surat'          => empty($req->input('alamat_surat')) ? $check->debt['alamat_surat'] : $req->input('alamat_surat'),
            'pekerjaan'             => empty($req->input('pekerjaan')) ? $check->debt['pekerjaan'] : $req->input('pekerjaan'),
            'posisi_pekerjaan'      => empty($req->input('posisi_pekerjaan')) ? $check->debt['posisi_pekerjaan'] : $req->input('posisi_pekerjaan'),
            'nama_tempat_kerja'     => empty($req->input('nama_tempat_kerja')) ? $check->debt['nama_tempat_kerja'] : $req->input('nama_tempat_kerja'),
            'jenis_pekerjaan'       => empty($req->input('jenis_pekerjaan')) ? $check->debt['jenis_pekerjaan'] : $req->input('jenis_pekerjaan'),

            'alamat_tempat_kerja'   => empty($req->input('alamat_tempat_kerja')) ? $check->debt['alamat_tempat_kerja'] : $req->input('alamat_tempat_kerja'),
            'id_prov_tempat_kerja'  => empty($req->input('id_prov_tempat_kerja')) ? $check->debt['id_prov_tempat_kerja'] : $req->input('id_prov_tempat_kerja'),
            'id_kab_tempat_kerja'   => empty($req->input('id_kab_tempat_kerja')) ? $check->debt['id_kab_tempat_kerja'] : $req->input('id_kab_tempat_kerja'),
            'id_kec_tempat_kerja'   => empty($req->input('id_kec_tempat_kerja')) ? $check->debt['id_kec_tempat_kerja'] : $req->input('id_kec_tempat_kerja'),
            'id_kel_tempat_kerja'   => empty($req->input('id_kel_tempat_kerja')) ? $check->debt['id_kel_tempat_kerja'] : $req->input('id_kel_tempat_kerja'),
            'rt_tempat_kerja'       => empty($req->input('rt_tempat_kerja')) ? $check->debt['rt_tempat_usaha'] : $req->input('rt_tempat_kerja'),
            'rw_tempat_kerja'       => empty($req->input('rw_tempat_kerja')) ? $check->debt['rw_tempat_usaha'] : $req->input('rw_tempat_kerja'),
            'tgl_mulai_kerja'       => empty($req->input('tgl_mulai_kerja')) ? $check->tgl_mulai_kerja : Carbon::parse($req->input('tgl_mulai_kerja'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => empty($req->input('no_telp_tempat_kerja')) ? $check->debt['no_telp_tempat_kerja'] : $req->input('no_telp_tempat_kerja'),
            'lamp_buku_tabungan'    => $tabungan,
            'lamp_sku'              => $sku,
            'lamp_slip_gaji'        => $slipGaji,
            'lamp_foto_usaha'       => $fotoUsaha
        );

        // Lampiran Pasangan
        if($file = $req->file('lamp_ktp_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'ktp.'.$file->getClientOriginalExtension();

            if(!empty($check->pas['lamp_ktp']))
            {
                File::delete($check->pas['lamp_ktp']);
            }

            $file->move($path,$name);

            $ktpPass = $path.'/'.$name;
        }else{
            $ktpPass = $check->pas['lamp_ktp'];
        }

        if($file = $req->file('lamp_buku_nikah_pas')){
            $path = $lamp_dir.'/pasangan';
            $name = 'buku_nikah.'.$file->getClientOriginalExtension();

            if(!empty($check->pas['lamp_buku_nikah']))
            {
                File::delete($check->pas['lamp_buku_nikah']);
            }

            $file->move($path,$name);

            $bukuNikahPass = $path.'/'.$name;
        }else{
            $bukuNikahPass = $check->pas['lamp_buku_nikah'];
        }

        // Data Pasangan
        $dataPasangan = array(
            'nama_lengkap'     => empty($req->input('nama_lengkap_pas')) ? $check->pas['nama_lengkap'] : $req->input('nama_lengkap_pas'),
            'nama_ibu_kandung' => empty($req->input('nama_ibu_kandung_pas')) ? $check->pas['nama_ibu_kandung'] : $req->input('nama_ibu_kandung_pas'),
            'jenis_kelamin'    => empty($req->input('jenis_kelamin_pas')) ? strtoupper($check->pas['jenis_kelamin']) : strtoupper($req->input('jenis_kelamin_pas')),
            'no_ktp'           => empty($req->input('no_ktp_pas')) ? $check->pas['no_ktp'] : $req->input('no_ktp_pas'),
            'no_ktp_kk'        => empty($req->input('no_ktp_kk_pas')) ? $check->pas['no_ktp_kk'] : $req->input('no_ktp_kk_pas'),
            'no_npwp'          => empty($req->input('no_npwp_pas')) ? $check->pas['no_npwp'] : $req->input('no_npwp_pas'),
            'tempat_lahir'     => empty($req->input('tempat_lahir_pas')) ? $check->pas['tempat_lahir'] : $req->input('tempat_lahir_pas'),
            'tgl_lahir'        => empty($req->input('tgl_lahir_pas')) ? $check->pas['tgl_lahir'] : Carbon::parse($req->input('tgl_lahir_pas'))->format('Y-m-d'),
            'alamat_ktp'       => empty($req->input('alamat_ktp_pas')) ? $check->pas['alamat_ktp'] : $req->input('alamat_ktp_pas'),
            'no_telp'          => empty($req->input('no_telp_pas')) ? $check->pas['no_telp'] : $req->input('no_telp_pas'),

            'nama_tempat_kerja'     => empty($req->input('nama_tempat_kerja_pas')) ? $check->pas['nama_tempat_kerja'] : $req->input('nama_tempat_kerja_pas'),
            'jenis_pekerjaan'       => empty($req->input('jenis_pekerjaan_pas')) ? $check->pas['jenis_pekerjaan'] : $req->input('jenis_pekerjaan_pas'),
            'alamat_tempat_kerja'   => empty($req->input('alamat_tempat_kerja')) ? $check->pas['alamat_tempat_kerja'] : $req->input('alamat_tempat_kerja_pas'),
            'id_prov_tempat_kerja'  => empty($req->input('id_prov_tempat_kerja')) ? $check->pas['id_prov_tempat_kerja'] : $req->input('id_prov_tempat_kerja_pas'),
            'id_kab_tempat_kerja'   => empty($req->input('id_kab_tempat_kerja')) ? $check->pas['id_kab_tempat_kerja'] : $req->input('id_kab_tempat_kerja_pas'),
            'id_kec_tempat_kerja'   => empty($req->input('id_kec_tempat_kerja')) ? $check->pas['id_kec_tempat_kerja'] : $req->input('id_kec_tempat_kerja_pas'),
            'id_kel_tempat_kerja'   => empty($req->input('id_kel_tempat_kerja')) ? $check->pas['id_kel_tempat_kerja'] : $req->input('id_kel_tempat_kerja_pas'),
            'rt_tempat_kerja'       => empty($req->input('rt_tempat_kerja')) ? $check->pas['rt_tempat_kerja'] : $req->input('rt_tempat_kerja_pas'),
            'rw_tempat_kerja'       => empty($req->input('rw_tempat_kerja')) ? $check->pas['rw_tempat_kerja'] : $req->input('rw_tempat_kerja_pas'),
            'tgl_mulai_kerja'       => Carbon::parse($req->input('tgl_mulai_kerja'))->format('Y-m-d'),
            'no_telp_tempat_kerja'  => empty($req->input('no_telp_tempat_kerja')) ? $check->pas['no_telp_tempat_kerja'] : $req->input('no_telp_tempat_kerja'),

            'lamp_ktp'         => $ktpPass,
            'lamp_buku_nikah'  => $bukuNikahPass,
        );

        // Penjamin
        if (!empty($check->id_penjamin)) {
            $id_penj = explode (",",$check->id_penjamin);

            $penjamin = Penjamin::whereIn('id', $id_penj)->get();

            $a = 1; $b = 1; $c = 1; $d = 1;

            if($files = $req->file('lamp_ktp_pen')){
                foreach($files as $file){

                    $name = 'ktp_penjamin'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($penjamin as $key => $val) {
                        $no_so = $val->lamp_ktp;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_ktp))
                        {
                            File::delete($val->lamp_ktp);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $ktpPen[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_ktp_pasangan_pen')){
                foreach($files as $file){

                    $name = 'ktp_pasangan'.$b.'.'.$file->getClientOriginalExtension();

                    foreach ($penjamin as $key => $val) {
                        $no_so = $val->lamp_ktp_pasangan;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_ktp_pasangan))
                        {
                            File::delete($val->lamp_ktp_pasangan);
                        }
                    }

                    $file->move($path,$name);

                    $b++;

                    $ktpPenPAS[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_kk_pen')){
                foreach($files as $file){

                    $name = 'kk_penjamin'.$c.'.'.$file->getClientOriginalExtension();

                    foreach ($penjamin as $key => $val) {
                        $no_so = $val->lamp_kk;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_kk))
                        {
                            File::delete($val->lamp_kk);
                        }
                    }

                    $file->move($path,$name);

                    $c++;

                    $kkPen[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_buku_nikah_pen')){
                foreach($files as $file){

                    $name = 'buku_nikah_penjamin'.$d.'.'.$file->getClientOriginalExtension();

                    foreach ($penjamin as $key => $val) {
                        $no_so = $val->lamp_buku_nikah;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_buku_nikah))
                        {
                            File::delete($val->lamp_buku_nikah);
                        }
                    }

                    $file->move($path,$name);

                    $d++;

                    $bukuNikahPen[] = $path.'/'.$name;
                }
            }

            // Data Penjamin
            foreach ($penjamin as $key => $value) {
                $DP[] = [
                    // 'id_calon_debitur' => $value->id_calon_debitur,
                    'nama_ktp'         => empty($req->input('nama_ktp_pen')[$key]) ? $value->nama_ktp : $req->input('nama_ktp_pen')[$key],
                    'nama_ibu_kandung' => empty($req->input('nama_ibu_kandung_pen')[$key]) ? $value->nama_ibu_kandung : $req->input('nama_ibu_kandung_pen')[$key],
                    'no_ktp'           => empty($req->input('no_ktp_pen')[$key]) ? $value->no_ktp : $req->input('no_ktp_pen')[$key],
                    'no_npwp'          => empty($req->input('no_npwp_pen')[$key]) ? $value->no_npwp : $req->input('no_npwp_pen')[$key],
                    'tempat_lahir'     => empty($req->input('tempat_lahir_pen')[$key]) ? $value->tempat_lahir : $req->input('tempat_lahir_pen')[$key],
                    'tgl_lahir'        => empty($req->input('tgl_lahir_pen')[$key]) ? $value->tgl_lahir : Carbon::parse($req->input('tgl_lahir_pen')[$key])->format('Y-m-d'),
                    'jenis_kelamin'    => empty($req->input('jenis_kelamin_pen')[$key]) ? $value->jenis_kelamin : strtoupper($req->input('jenis_kelamin_pen')[$key]),
                    'alamat_ktp'       => empty($req->input('alamat_ktp_pen')[$key]) ? $value->alamat_ktp : $req->input('alamat_ktp_pen')[$key],
                    'no_telp'          => empty($req->input('no_telp_pen')[$key]) ? $value->no_telp : $req->input('no_telp_pen')[$key],
                    'hubungan_debitur' => empty($req->input('hubungan_debitur_pen')[$key]) ? $value->hubungan_debitur : $req->input('hubungan_debitur_pen')[$key],

                    'pekerjaan'             => empty($req->input('pekerjaan_pen')[$key]) ? $value->pekerjaan : $req->input('pekerjaan_pen')[$key],
                    'nama_tempat_kerja'     => empty($req->input('nama_tempat_kerja_pen')[$key]) ? $value->nama_tempat_kerja : $req->input('nama_tempat_kerja_pen')[$key],
                    'posisi_pekerjaan'      => empty($req->input('posisi_pekerjaan_pen')[$key]) ? $value->posisi_pekerjaan : $req->input('posisi_pekerjaan_pen')[$key],
                    'jenis_pekerjaan'       => empty($req->input('jenis_pekerjaan_pen')[$key]) ? $value->jenis_pekerjaan : $req->input('jenis_pekerjaan_pen')[$key],
                    'alamat_tempat_kerja'   => empty($req->input('alamat_tempat_kerja_pen')[$key]) ? $value->alamat_tempat_kerja : $req->input('alamat_tempat_kerja_pen')[$key],

                    'id_prov_tempat_kerja'  => empty($req->input('id_prov_tempat_kerja_pen')[$key]) ? $value->id_prov_tempat_kerja : $req->input('id_prov_tempat_kerja_pen')[$key],
                    'id_kab_tempat_kerja'   => empty($req->input('id_kab_tempat_kerja_pen')[$key]) ? $value->id_kab_tempat_kerja : $req->input('id_kab_tempat_kerja_pen')[$key],
                    'id_kec_tempat_kerja'   => empty($req->input('id_kec_tempat_kerja_pen')[$key]) ? $value->id_kec_tempat_kerja : $req->input('id_kec_tempat_kerja_pen')[$key],
                    'id_kel_tempat_kerja'   => empty($req->input('id_kel_tempat_kerja_pen')[$key]) ? $value->id_kel_tempat_kerja : $req->input('id_kel_tempat_kerja_pen')[$key],
                    'rt_tempat_kerja'       => empty($req->input('rt_tempat_kerja_pen')[$key]) ? $value->rt_tempat_kerja : $req->input('rt_tempat_kerja_pen')[$key],
                    'rw_tempat_kerja'       => empty($req->input('rw_tempat_kerja_pen')[$key]) ? $value->rw_tempat_kerja : $req->input('rw_tempat_kerja_pen')[$key],
                    'tgl_mulai_kerja'       => empty($req->input('tgl_mulai_kerja_pen')[$key]) ? $value->tgl_mulai_kerja : $req->input('tgl_mulai_kerja_pen')[$key],
                    'no_telp_tempat_kerja'  => empty($req->input('no_telp_tempat_kerja_pen')[$key]) ? $value->no_telp_tempat_kerja : $req->input('no_telp_tempat_kerja_pen')[$key],
                    'lamp_ktp'         => empty($ktpPen[$key]) ? $value->lamp_ktp : $ktpPen[$key],
                    'lamp_ktp_pasangan'=> empty($ktpPenPAS[$key]) ? $value->lamp_ktp_pasangan : $ktpPenPAS[$key],
                    'lamp_kk'          => empty($kkPen[$key]) ? $value->lamp_kk : $kkPen[$key],
                    'lamp_buku_nikah'  => empty($bukuNikahPen[$key]) ? $value->lamp_buku_nikah : $bukuNikahPen[$key],
                    'updated_at'       => Carbon::now()->toDateTimeString()
                ];
            }
        }

        // Agunan Tanah
        if (!empty($check->id_agunan_tanah)) {
            $id_agu_ta = explode (",",$check->id_agunan_tanah);

            $tanah = AgunanTanah::whereIn('id', $id_agu_ta)->get();

            // Lampiran Agunan Tanah
            if($files = $req->file('lamp_agunan_depan')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_agunan_depan;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_depan))
                        {
                            File::delete($val->lamp_agunan_depan);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanDepan[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_kanan')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_agunan_kanan;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_kanan))
                        {
                            File::delete($val->lamp_agunan_kanan);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanKanan[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_kiri')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_agunan_kiri;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_kiri))
                        {
                            File::delete($val->lamp_agunan_kiri);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanKiri[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_belakang')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_agunan_belakang;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_belakang))
                        {
                            File::delete($val->lamp_agunan_belakang);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanBelakang[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_dalam')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_agunan_dalam;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_dalam))
                        {
                            File::delete($val->lamp_agunan_dalam);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanDalam[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_sertifikat')){
                $a = 1;
                foreach($files as $file){

                    $name = 'lamp_sertifikat'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_sertifikat;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_sertifikat))
                        {
                            File::delete($val->lamp_sertifikat);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $lamp_sertifikat[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_imb')){
                $a = 1;
                foreach($files as $file){

                    $name = 'lamp_imb'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_imb;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_imb))
                        {
                            File::delete($val->lamp_imb);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $lamp_imb[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_pbb')){
                $a = 1;
                foreach($files as $file){

                    $name = 'lamp_pbb'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($tanah as $key => $val) {
                        $no_so = $val->lamp_pbb;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_pbb))
                        {
                            File::delete($val->lamp_pbb);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $lamp_pbb[] = $path.'/'.$name;
                }
            }

            // Data Agunan Tanah
            foreach ($tanah as $key => $value) {
                $dataTanah[] = [
                    // 'id_calon_debitur'        => $check->id_calon_debt,
                    'tipe_lokasi'             => empty($req->input('tipe_lokasi')) ? $value->tipe_lokasi : $req->tipe_lokasi,
                    'alamat'                  => empty($req->input('alamat')[$key]) ? $value->alamat : $req->alamat[$key],
                    'id_provinsi'             => empty($req->input('id_provinsi')[$key]) ? $value->id_provinsi : $req->id_provinsi[$key],
                    'id_kabupaten'            => empty($req->input('id_kabupaten')[$key]) ? $value->id_kabupaten : $req->id_kabupaten[$key],
                    'id_kecamatan'            => empty($req->input('id_kecamatan')[$key]) ? $value->id_kecamatan : $req->id_kecamatan[$key],
                    'id_kelurahan'            => empty($req->input('id_kelurahan')[$key]) ? $value->id_kelurahan : $req->id_kelurahan[$key],
                    'rt'                      => empty($req->input('rt')[$key]) ? $value->rt : $req->rt[$key],
                    'rw'                      => empty($req->input('rw')[$key]) ? $value->rw : $req->rw[$key],
                    'luas_tanah'              => empty($req->input('luas_tanah')[$key]) ? $value->luas_tanah : $req->luas_tanah[$key],
                    'luas_bangunan'           => empty($req->input('luas_bangunan')[$key]) ? $value->luas_bangunan : $req->luas_bangunan[$key],
                    'nama_pemilik_sertifikat' => empty($req->input('nama_pemilik_sertifikat')[$key]) ? $value->nama_pemilik_sertifikat : $req->nama_pemilik_sertifikat[$key],
                    'jenis_sertifikat'        => empty($req->input('jenis_sertifikat')[$key]) ? $value->jenis_sertifikat : $req->jenis_sertifikat[$key],
                    'no_sertifikat'           => empty($req->input('no_sertifikat')[$key]) ? $value->no_sertifikat : $req->no_sertifikat[$key],
                    'tgl_ukur_sertifikat'     => empty($req->input('tgl_ukur_sertifikat')[$key]) ? $value->tgl_ukur_sertifikat : $req->tgl_ukur_sertifikat[$key],
                    'tgl_berlaku_shgb'        => empty($req->input('tgl_berlaku_shgb')[$key]) ? $value->tgl_berlaku_shgb : $req->tgl_berlaku_shgb[$key],
                    'no_imb'                  => empty($req->input('no_imb')[$key]) ? $value->no_imb : $req->no_imb[$key],
                    'njop'                    => empty($req->input('njop')[$key]) ? $value->njop : $req->njop[$key],
                    'nop'                     => empty($req->input('nop')[$key]) ? $value->nop : $req->nop[$key],

                    'lamp_agunan_depan'       => empty($req->input('lamp_agunan_depan')[$key]) ? $value->lamp_agunan_depan : $agunanDepan[$key],
                    'lamp_agunan_kanan'       => empty($req->input('lamp_agunan_kanan')[$key]) ? $value->lamp_agunan_kanan : $agunanKanan[$key],
                    'lamp_agunan_kiri'        => empty($req->input('lamp_agunan_kiri')[$key]) ? $value->lamp_agunan_kiri : $agunanKiri[$key],
                    'lamp_agunan_belakang'    => empty($req->input('lamp_agunan_belakang')[$key]) ? $value->lamp_agunan_belakang : $agunanBelakang[$key],
                    'lamp_agunan_dalam'       => empty($req->input('lamp_agunan_dalam')[$key]) ? $value->lamp_agunan_dalam : $agunanDalam[$key],
                    'lamp_sertifikat'         => empty($req->input('lamp_sertifikat')[$key]) ? $value->lamp_sertifikat : $lamp_sertifikat[$key],
                    'lamp_imb'                => empty($req->input('lamp_imb')[$key]) ? $value->lamp_imb : $lamp_imb[$key],
                    'lamp_pbb'                => empty($req->input('lamp_pbb')[$key]) ? $value->lamp_pbb : $lamp_pbb[$key]
                ];
            }
        }

        // Agunan Kendaran
        if (!empty($check->id_agunan_kendaraan)) {

            $id_agu_ke = explode (",",$check->id_agunan_kendaraan);

            $kendaraan = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            // Lampiran Agunan Kendaraan
            if($files = $req->file('lamp_agunan_depan_ken')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($kendaraan as $key => $val) {
                        $no_so = $val->lamp_agunan_depan;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_depan))
                        {
                            File::delete($val->lamp_agunan_depan);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanDepanKen[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_kanan_ken')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($kendaraan as $key => $val) {
                        $no_so = $val->lamp_agunan_kanan;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_kanan))
                        {
                            File::delete($val->lamp_agunan_kanan);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanKananKen[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_kiri_ken')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($kendaraan as $key => $val) {
                        $no_so = $val->lamp_agunan_kiri;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_kiri))
                        {
                            File::delete($val->lamp_agunan_kiri);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanKiriKen[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_belakang_ken')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($kendaraan as $key => $val) {
                        $no_so = $val->lamp_agunan_belakang;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_belakang))
                        {
                            File::delete($val->lamp_agunan_belakang);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanBelakangKen[] = $path.'/'.$name;
                }
            }

            if($files = $req->file('lamp_agunan_dalam_ken')){
                $a = 1;
                foreach($files as $file){

                    $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();

                    foreach ($kendaraan as $key => $val) {
                        $no_so = $val->lamp_agunan_dalam;

                        $arrPath = explode("/", $no_so, 4);

                        $path = $arrPath[0].'/'.$arrPath[1].'/'.$arrPath[2];

                        if(!empty($val->lamp_agunan_dalam))
                        {
                            File::delete($val->lamp_agunan_dalam);
                        }
                    }

                    $file->move($path,$name);

                    $a++;

                    $agunanDalamKen[] = $path.'/'.$name;
                }
            }

            // Data Agunan Kendaraan
            foreach ($kendaraan as $key => $val) {
                $dataKendaraan[] = [
                    'id_calon_debitur' => empty($req->input('id_calon_debitur')[$key]) ? $val->id_calon_debitur : $req->id_calon_debitur[$key],
                    'no_bpkb' => empty($req->input('no_bpkb')[$key]) ? $val->no_bpkb : $req->no_bpkb[$key],
                    'nama_pemilik' => empty($req->input('nama_pemilik')[$key]) ? $val->nama_pemilik : $req->nama_pemilik[$key],
                    'alamat_pemilik' => empty($req->input('alamat_pemilik')[$key]) ? $val->alamat_pemilik : $req->alamat_pemilik[$key],
                    'merk' => empty($req->input('merk')[$key]) ? $val->merk : $req->merk[$key],
                    'jenis' => empty($req->input('jenis')[$key]) ? $val->jenis : $req->jenis[$key],
                    'no_rangka' => empty($req->input('no_rangka')[$key]) ? $val->no_rangka : $req->no_rangka[$key],
                    'no_mesin' => empty($req->input('no_mesin')[$key]) ? $val->no_mesin : $req->no_mesin[$key],
                    'warna' => empty($req->input('warna')[$key]) ? $val->warna : $req->warna[$key],
                    'tahun' => empty($req->input('tahun')[$key]) ? $val->tahun : $req->tahun[$key],
                    'no_polisi' => empty($req->input('no_polisi')[$key]) ? $val->no_polisi : $req->no_polisi[$key],
                    'no_stnk' => empty($req->input('no_stnk')[$key]) ? $val->no_stnk : $req->no_stnk[$key],
                    'tgl_kadaluarsa_pajak' => empty($req->input('tgl_kadaluarsa_pajak')[$key]) ? $val->tgl_kadaluarsa_pajak : $req->tgl_kadaluarsa_pajak[$key],
                    'tgl_kadaluarsa_stnk' => empty($req->input('tgl_kadaluarsa_stnk')[$key]) ? $val->tgl_kadaluarsa_stnk : $req->tgl_kadaluarsa_stnk[$key],
                    'no_faktur' => empty($req->input('no_faktur')[$key]) ? $val->no_faktur : $req->no_faktur[$key],
                    'lamp_agunan_depan' => empty($req->input('lamp_agunan_depan')[$key]) ? $val->lamp_agunan_depan : $agunanDepanKen[$key],
                    'lamp_agunan_kanan' => empty($req->input('lamp_agunan_kanan')[$key]) ? $val->lamp_agunan_kanan : $agunanKananKen[$key],
                    'lamp_agunan_kiri' => empty($req->input('lamp_agunan_kiri')[$key]) ? $val->lamp_agunan_kiri : $agunanKiriKen[$key],
                    'lamp_agunan_belakang' => empty($req->input('lamp_agunan_belakang')[$key]) ? $val->lamp_agunan_belakang : $agunanBelakangKen[$key],
                    'lamp_agunan_dalam' => empty($req->input('lamp_agunan_dalam')[$key]) ? $val->lamp_agunan_dalam : $agunanDalamKen[$key]
                ];
            }
        }

        // Pemeriksaaan Agunan Tanah
        if (!empty($check->id_periksa_agunan_tanah)) {
            $id_pe_agu_ta = explode (",",$check->id_periksa_agunan_tanah);
            $pe_tanah     = PemeriksaanAgunTan::whereIn('id', $id_pe_agu_ta)->get();

            foreach ($pe_tanah as $key => $val) {
                $dataPeriksaTanah[] = [
                    // 'id_calon_debitur' => $check->id_calon_debt,
                    'id_agunan_tanah' => empty($req->input('id_agunan_tanah')[$key]) ? $val->id_agunan_tanah : $req->id_agunan_tanah[$key],
                    'nama_penghuni' => empty($req->input('nama_penghuni')[$key]) ? $val->nama_penghuni : $req->nama_penghuni[$key],
                    'status_penghuni' => empty($req->input('status_penghuni')[$key]) ? $val->status_penghuni : $req->status_penghuni[$key],
                    'bentuk_bangunan' => empty($req->input('bentuk_bangunan')[$key]) ? $val->bentuk_bangunan : $req->bentuk_bangunan[$key],
                    'kondisi_bangunan' => empty($req->input('kondisi_bangunan')[$key]) ? $val->kondisi_bangunan : $req->kondisi_bangunan[$key],
                    'fasilitas' => empty($req->input('fasilitas')[$key]) ? $val->fasilitas : $req->fasilitas[$key],
                    'listrik' => empty($req->input('listrik')[$key]) ? $val->listrik : $req->listrik[$key],
                    'nilai_taksasi_agunan' => empty($req->input('nilai_taksasi_agunan')[$key]) ? $val->nilai_taksasi_agunan : $req->nilai_taksasi_agunan[$key],
                    'nilai_taksasi_bangunan' => empty($req->input('nilai_taksasi_bangunan')[$key]) ? $val->nilai_taksasi_bangunan : $req->nilai_taksasi_bangunan[$key],
                    'tgl_taksasi' => empty($req->input('tgl_taksasi')[$key]) ? $val->tgl_taksasi : $req->tgl_taksasi[$key],
                    'nilai_likuidasi' => empty($req->input('nilai_likuidasi')[$key]) ? $val->nilai_likuidasi : $req->nilai_likuidasi[$key],
                ];
            }
        }

        // Pemeriksaaan Agunan Kendaraan
        if (!empty($check->id_periksa_agunan_kendaraan)) {
            $id_pe_agu_ke = explode (",",$check->id_periksa_agunan_kendaraan);
            $pe_kendaraan = PemeriksaanAgunKen::whereIn('id', $id_pe_agu_ke)->get();

            foreach ($pe_kendaraan as $key => $val) {
                $dataPeriksaKendaraan[] = [
                     'id_agunan_kendaraan' => empty($req->input('id_agunan_kendaraan')[$key]) ? $val->id_agunan_kendaraan : $req->id_agunan_kendaraan[$key],
                     'nama_pengguna' => empty($req->input('nama_pengguna')[$key]) ? $val->nama_pengguna : $req->nama_pengguna[$key],
                     'status_pengguna' => empty($req->input('status_pengguna')[$key]) ? $val->status_pengguna : $req->status_pengguna[$key],
                     'jml_roda_kendaraan' => empty($req->input('jml_roda_kendaraan')[$key]) ? $val->jml_roda_kendaraan : $req->jml_roda_kendaraan[$key],
                     'kondisi_kendaraan' => empty($req->input('kondisi_kendaraan')[$key]) ? $val->kondisi_kendaraan : $req->kondisi_kendaraan[$key],
                        'keberadaan_kendaraan' => empty($req->input('keberadaan_kendaraan')[$key]) ? $val->keberadaan_kendaraan : $req->keberadaan_kendaraan[$key],
                        'body' => empty($req->input('body')[$key]) ? $val->body : $req->body[$key],
                        'interior' => empty($req->input('interior')[$key]) ? $val->interior : $req->interior[$key],
                        'km' => empty($req->input('km')[$key]) ? $val->km : $req->km[$key],
                        'modifikasi' => empty($req->input('modifikasi')[$key]) ? $val->modifikasi : $req->modifikasi[$key],
                        'aksesoris' => empty($req->input('aksesoris')[$key]) ? $val->aksesoris : $req->aksesoris[$key],
                ];
            }
        }

        //Kapasitas Bulanan
        $check_KapBul = KapBulanan::where('id_calon_debitur', $check->id_calon_debt)->first();
        $data_kapbul[] = [
            'pemasukan_cadebt' => empty($req->input('pemasukan_cadebt')) ? $check_KapBul->pemasukan_cadebt : $req->input('pemasukan_cadebt'),
            'pemasukan_pasangan' => empty($req->input('pemasukan_pasangan')) ? $check_KapBul->pemasukan_pasangan : $req->input('pemasukan_pasangan'),
            'pemasukan_penjamin' => empty($req->input('pemasukan_penjamin')) ? $check_KapBul->pemasukan_penjamin : $req->input('pemasukan_penjamin'),
            'biaya_rumah_tangga' => empty($req->input('biaya_rumah_tangga')) ? $check_KapBul->biaya_rumah_tangga : $req->input('biaya_rumah_tangga'),
            'biaya_transport' => empty($req->input('biaya_transport')) ? $check_KapBul->biaya_transport : $req->input('biaya_transport'),
            'biaya_pendidikan' => empty($req->input('biaya_pendidikan')) ? $check_KapBul->biaya_pendidikan : $req->input('biaya_pendidikan'),
            'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? $check_KapBul->biaya_telp_listr_air : $req->input('biaya_telp_listr_air'),
            'angsuran' => empty($req->input('angsuran')) ? $check_KapBul->angsuran : $req->input('angsuran'),
            'biaya_lain' => empty($req->input('biaya_lain')) ? $check_KapBul->biaya_lain : $req->input('biaya_lain'),
            'total_pemasukan' => empty($req->input('total_pemasukan')) ? $check_KapBul->total_pemasukan : $req->input('total_pemasukan'),
            'total_pengeluaran' => empty($req->input('total_pengeluaran')) ? $check_KapBul->total_pengeluaran : $req->input('total_pengeluaran'),
            'penghasilan_bersih' => empty($req->input('penghasilan_bersih')) ? $check_KapBul->penghasilan_bersih : $req->input('penghasilan_bersih'),
        ];

        // Keuangan / Usaha Debitur
        // $check_usaha = KeuanganUsaha::where('id', $check->id_usaha)->first();
        if (!empty($check->id_usaha)) {
            $dataUsaha = array(
                'pemasukan_tunai' => empty($req->input('pemasukan_tunai')) ? $check->usaha['pemasukan_tunai'] : $req->input('pemasukan_tunai'),
                'pemasukan_kredit' => empty($req->input('pemasukan_kredit')) ? $check->usaha['pemasukan_kredit'] : $req->input('pemasukan_kredit'),
                'biaya_sewa' => empty($req->input('biaya_sewa')) ? $check->usaha['biaya_sewa'] : $req->input('biaya_sewa'),
                'biaya_gaji_pegawai' => empty($req->input('biaya_gaji_pegawai')) ? $check->usaha['biaya_gaji_pegawai'] : $req->input('biaya_gaji_pegawai'),
                'biaya_belanja_brg' => empty($req->input('biaya_belanja_brg')) ? $check->usaha['biaya_belanja_brg'] : $req->input('biaya_belanja_brg'),
                'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? $check->usaha['biaya_telp_listr_air'] : $req->input('biaya_telp_listr_air'),
                'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? $check->usaha['biaya_sampah_kemanan'] : $req->input('biaya_sampah_kemanan'),
                'biaya_kirim_barang' => empty($req->input('biaya_kirim_barang')) ? $check->usaha['biaya_kirim_barang'] : $req->input('biaya_kirim_barang'),
                'biaya_hutang_dagang' => empty($req->input('biaya_hutang_dagang')) ? $check->usaha['biaya_hutang_dagang'] : $req->input('biaya_hutang_dagang'),
                'biaya_angsuran' => empty($req->input('biaya_angsuran')) ? $check->usaha['biaya_angsuran'] : $req->input('biaya_angsuran'),
                'biaya_lain_lain' => empty($req->input('biaya_lain_lain')) ? $check->usaha['biaya_lain_lain'] : $req->input('biaya_lain_lain')
            );

            $keuanganUsaha = array(
                'total_pemasukan' => $dataUsaha['pemasukan_tunai'] + $dataUsaha['pemasukan_kredit'],
                'total_pengeluaran' => $dataUsaha['biaya_sewa'] + $dataUsaha['biaya_gaji_pegawai'] + $dataUsaha['biaya_belanja_brg'] + $dataUsaha['biaya_telp_listr_air'] + $dataUsaha['biaya_sampah_kemanan'] + $dataUsaha['biaya_kirim_barang'] + $dataUsaha['biaya_hutang_dagang'] + $dataUsaha['biaya_angsuran'] + $dataUsaha['biaya_lain_lain']
            );

            $labaUsaha = array(
                'laba_usaha' => $keuanganUsaha['total_pemasukan'] - $keuanganUsaha['total_pengeluaran']
            );

            $newDataUsaha = array_merge($dataUsaha, $keuanganUsaha, $labaUsaha);
        }

        // try{

        //     return response()->json([
        //         'code'   => 200,
        //         'status' => 'success',
        //         'message'=> 'Data berhasil dibuat'
        //     ], 200);
        // } catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $err
        //     ], 501);
        // }
    }
}
