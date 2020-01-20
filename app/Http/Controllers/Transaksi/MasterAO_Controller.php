<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Pengajuan\AO\RekomendasiAO;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\AO\ValidModel;
use App\Models\Pengajuan\AO\VerifModel;
use App\Models\Pengajuan\SO\Penjamin;
use App\Models\Pengajuan\SO\Pasangan;
use App\Models\Pengajuan\SO\Debitur;
use Illuminate\Support\Facades\File;
use App\Models\AreaKantor\Cabang;
use App\Models\Transaksi\TransSO;
use App\Models\Transaksi\TransAO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MasterAO_Controller extends BaseController
{
    public function index(Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(AO). Harap daftarkan diri sebagai PIC(AO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $query = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')->where('id_cabang', $id_cabang)->get();
        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {

            if ($val->status_das == 1) {
                $status_das = 'complete';
            }elseif($val->status_das == 2){
                $status_das = 'not complete';
            }else{
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            }elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            }else{
                $status_hm = 'waiting';
            }

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            }elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            }else{
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                // 'nomor_ao'       => $val->ao['nomor_ao'],
                'pic'            => $val->pic['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => $val->faspin['plafon'],
                'tenor'          => $val->faspin['tenor'],
                'das'            => [
                    'status'  => $status_das,
                    'catatan' => $val->catatan_das
                ],
                'hm'            => [
                    'status'  => $status_hm,
                    'catatan' => $val->catatan_hm
                ],
                'ao'            => [
                    'status'  => $status_ao,
                    'catatan' => $val->catatan_ao
                ]
                // 'das_status'     => $status_das,
                // 'das_note'       => $val->catatan_das,
                // 'hm_status'      => $status_das,
                // 'hm_note'        => $val->catatan_hm,
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

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(AO). Harap daftarkan diri sebagai PIC(AO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $val = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')->where('id', $id)->where('id_cabang', $id_cabang)->first();

        if (!$val) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",",$val->id_penjamin);

        $penjamin = Penjamin::whereIn('id', $id_penj)->get();

        if ($penjamin != '[]') {
            foreach ($penjamin as $key => $value) {
                $pen[$key] = [
                    "id"                    => $value->id,
                    // "nama_ktp"              => $value->nama_ktp,
                    // "nama_ibu_kandung"      => $value->nama_ibu_kandung,
                    // "no_ktp"                => $value->no_ktp,
                    // "no_npwp"               => $value->no_npwp,
                    // "tempat_lahir"          => $value->tempat_lahir,
                    // "tgl_lahir"             => Carbon::parse($value->tgl_lahir)->format('d-m-Y'),
                    // "jenis_kelamin"         => $value->jenis_kelamin,
                    // "alamat_ktp"            => $value->alamat_ktp,
                    // "no_telp"               => $value->no_telp,
                    // "hubungan_debitur"      => $value->hubungan_debitur,
                    // "pekerjaan" => [
                    //     "nama_pekerjaan"        => $value->pekerjaan,
                    //     "posisi_pekerjaan"      => $value->posisi_pekerjaan,
                    //     "nama_tempat_kerja"     => $value->nama_tempat_kerja,
                    //     "jenis_pekerjaan"       => $value->jenis_pekerjaan,
                    //     "tgl_mulai_kerja"       => $value->tgl_mulai_kerja, //Carbon::parse($value->tgl_mulai_kerja)->format('d-m-Y'),
                    //     "no_telp_tempat_kerja"  => $value->no_telp_tempat_kerja,
                    //     'alamat' => [
                    //         'alamat_singkat' => $value->alamat_tempat_kerja,
                    //         'rt'             => $value->rt_tempat_kerja,
                    //         'rw'             => $value->rw_tempat_kerja,
                    //         'kelurahan' => [
                    //             'id'    => $value->penj['kel_kerja']['id'],
                    //             'nama'  => $value->penj['kel_kerja']['nama']
                    //         ],
                    //         'kecamatan' => [
                    //             'id'    => $value->penj['kec_kerja']['id'],
                    //             'nama'  => $value->penj['kec_kerja']['nama']
                    //         ],
                    //         'kabupaten' => [
                    //             'id'    => $value->penj['kab_kerja']['id'],
                    //             'nama'  => $value->penj['kab_kerja']['nama'],
                    //         ],
                    //         'provinsi'  => [
                    //             'id'   => $value->penj['prov_kerja']['id'],
                    //             'nama' => $value->penj['prov_kerja']['nama'],
                    //         ],
                    //         'kode_pos' => $value->penj['kel_kerja']['kode_pos']
                    //     ]
                    // ],
                    // "lampiran" => [
                    //     "lamp_ktp"          => $value->lamp_ktp,
                    //     "lamp_ktp_pasangan" => $value->lamp_ktp_pasangan,
                    //     "lamp_kk"           => $value->lamp_kk,
                    //     "lamp_buku_nikah"   => $value->lamp_buku_nikah
                    // ]
                ];
            }
        }else{
            $pen = null;
        }

        if ($val->status_das == 1) {
            $status_das = 'complete';
        }elseif($val->status_das == 2){
            $status_das = 'not complete';
        }else{
            $status_das = 'waiting';
        }

        if ($val->status_hm == 1) {
            $status_hm = 'complete';
        }elseif ($val->status_hm == 2) {
            $status_hm = 'not complete';
        }else{
            $status_hm = 'waiting';
        }

        if ($val->ao['status_ao'] == 1) {
            $status_ao = 'recommend';
        }elseif ($val->ao['status_ao'] == 2) {
            $status_ao = 'not recommend';
        }else{
            $status_ao = 'waiting';
        }

        $data[] = [
            'id'          => $val->id,
            'nomor_so'    => $val->nomor_so,
            // 'nomor_ao'    => $val->ao['nomor_ao'],
            'nama_so'     => $val->nama_so,
            'id_pic'      => $val->id_pic,
            'nama_pic'    => $val->pic['nama'],
            'id_cabang'   => $val->id_cabang,
            'nama_cabang' => $val->cabang['nama'],
            'asaldata'  => [
                'id'   => $val->asaldata['id'],
                'nama' => $val->asaldata['nama']
            ],
            'nama_marketing' => $val->nama_marketing,
            'fasilitas_pinjaman'  => [
                'id'              => $val->id_fasilitas_pinjaman,
                // 'jenis_pinjaman'  => $val->faspin->jenis_pinjaman,
                // 'tujuan_pinjaman' => $val->faspin->tujuan_pinjaman,
                // 'plafon'         => (int) $val->faspin->plafon,
                // 'tenor'          => (int) $val->faspin->tenor,
            ],
            'data_debitur' => [
                'id'                    => $val->id_calon_debitur,
                // 'nama_lengkap'          => $val->debt['nama_lengkap'],
                // 'gelar_keagamaan'       => $val->debt['gelar_keagamaan'],
                // 'gelar_pendidikan'      => $val->debt['gelar_pendidikan'],
                // 'jenis_kelamin'         => $val->debt['jenis_kelamin'],
                // 'status_nikah'          => $val->debt['status_nikah'],
                // 'ibu_kandung'           => $val->debt['ibu_kandung'],
                // 'tinggi_badan'          => $val->debt['tinggi_badan'],
                // 'berat_badan'           => $val->debt['berat_badan'],
                // 'no_ktp'                => $val->debt['no_ktp'],
                // 'no_ktp_kk'             => $val->debt[''],
                // 'no_kk'                 => $val->debt['no_ktp_kk'],
                // 'no_npwp'               => $val->debt['no_npwp'],
                // 'tempat_lahir'          => $val->debt['tempat_lahir'],
                // 'tgl_lahir'             => Carbon::parse($val->debt['tgl_lahir'])->format('d-m-Y'),
                // 'agama'                 => $val->debt['agama'],

                // 'alamat_ktp' => [
                //     'alamat_singkat' => $val->debt['alamat_ktp'],
                //     'rt'     => $val->debt['rt_ktp'],
                //     'rw'     => $val->debt['rw_ktp'],
                //     'kelurahan' => [
                //         'id'    => $val->debt['kel_ktp']['id'],
                //         'nama'  => $val->debt['kel_ktp']['nama']
                //     ],
                //     'kecamatan' => [
                //         'id'    => $val->debt['kec_ktp']['id'],
                //         'nama'  => $val->debt['kec_ktp']['nama']
                //     ],
                //     'kabupaten' => [
                //         'id'    => $val->debt['kab_ktp']['id'],
                //         'nama'  => $val->debt['kab_ktp']['nama'],
                //     ],
                //     'provinsi'  => [
                //         'id'   => $val->debt['prov_ktp']['id'],
                //         'nama' => $val->debt['prov_ktp']['nama'],
                //     ],
                //     'kode_pos' => $val->debt['kel_ktp']['kode_pos']
                // ],
                // 'alamat_domisili' => [
                //     'alamat_singkat' => $val->debt['alamat_domisili'],
                //     'rt'             => $val->debt['rt_domisili'],
                //     'rw'             => $val->debt['rw_domisili'],
                //     'kelurahan' => [
                //         'id'    => $val->debt['kel_dom']['id'],
                //         'nama'  => $val->debt['kel_dom']['nama']
                //     ],
                //     'kecamatan' => [
                //         'id'    => $val->debt['kec_dom']['id'],
                //         'nama'  => $val->debt['kec_dom']['nama']
                //     ],
                //     'kabupaten' => [
                //         'id'    => $val->debt['kab_dom']['id'],
                //         'nama'  => $val->debt['kab_dom']['nama'],
                //     ],
                //     'provinsi'  => [
                //         'id'   => $val->debt['prov_dom']['id'],
                //         'nama' => $val->debt['prov_dom']['nama'],
                //     ],
                //     'kode_pos' => $val->debt['kel_dom']['kode_pos']
                // ],
                // "pekerjaan" => [
                //     "nama_pekerjaan"        => $val->debt['pekerjaan'],
                //     "posisi_pekerjaan"      => $val->debt['posisi_pekerjaan'],
                //     "nama_tempat_kerja"     => $val->debt['nama_tempat_kerja'],
                //     "jenis_pekerjaan"       => $val->debt['jenis_pekerjaan'],
                //     "tgl_mulai_kerja"       => $val->debt['tgl_mulai_kerja'], //Carbon::parse($val->debt['tgl_mulai_kerja'])->format('d-m-Y'),
                //     "no_telp_tempat_kerja"  => $val->debt['no_telp_tempat_kerja'],
                //     'alamat' => [
                //         'alamat_singkat' => $val->debt['alamat_tempat_kerja'],
                //         'rt'             => $val->debt['rt_tempat_kerja'],
                //         'rw'             => $val->debt['rw_tempat_kerja'],
                //         'kelurahan' => [
                //             'id'    => $val->debt['kel_kerja']['id'],
                //             'nama'  => $val->debt['kel_kerja']['nama']
                //         ],
                //         'kecamatan' => [
                //             'id'    => $val->debt['kec_kerja']['id'],
                //             'nama'  => $val->debt['kec_kerja']['nama']
                //         ],
                //         'kabupaten' => [
                //             'id'    => $val->debt['kab_kerja']['id'],
                //             'nama'  => $val->debt['kab_kerja']['nama'],
                //         ],
                //         'provinsi'  => [
                //             'id'   => $val->debt['prov_kerja']['id'],
                //             'nama' => $val->debt['prov_kerja']['nama'],
                //         ],
                //         'kode_pos' => $val->debt['kel_kerja']['kode_pos']
                //     ]
                // ],
                // 'pendidikan_terakhir'   => $val->debt['pendidikan_terakhir'],
                // 'jumlah_tanggungan'     => $val->debt['jumlah_tanggungan'],
                // 'no_telp'               => $val->debt['no_telp'],
                // 'no_hp'                 => $val->debt['no_hp'],
                // 'alamat_surat'          => $val->debt['alamat_surat'],
                // 'lampiran' => [
                //     'lamp_ktp'              => $val->debt['lamp_ktp'],
                //     'lamp_kk'               => $val->debt['lamp_kk'],
                //     'lamp_buku_tabungan'    => $val->debt['lamp_buku_tabungan'],
                //     'lamp_sertifikat'       => $val->debt['lamp_sertifikat'],
                //     'lamp_sttp_pbb'         => $val->debt['lamp_sttp_pbb'],
                //     'lamp_imb'              => $val->debt['lamp_imb']
                // ]
            ],
            'data_pasangan' => [
                'id'               => $val->id_pasangan,
                // 'nama_lengkap'     => $val->pas['nama_lengkap'],
                // 'nama_ibu_kandung' => $val->pas['nama_ibu_kandung'],
                // 'jenis_kelamin'    => $val->pas['jenis_kelamin'],
                // 'no_ktp'           => $val->pas['no_ktp'],
                // 'no_ktp_kk'        => $val->pas['no_ktp_kk'],
                // 'no_npwp'          => $val->pas['no_npwp'],
                // 'tempat_lahir'     => $val->pas['tempat_lahir'],
                // 'tgl_lahir'        => Carbon::parse($val->pas['tgl_lahir'])->format('d-m-Y'),
                // 'alamat_ktp'       => $val->pas['alamat_ktp'],
                // 'no_telp'          => $val->pas['no_telp'],
                // 'pekerjaan' => [
                //     "nama_pekerjaan"        => $val->pas['pekerjaan'],
                //     "posisi_pekerjaan"      => $val->pas['posisi_pekerjaan'],
                //     "nama_tempat_kerja"     => $val->pas['nama_tempat_kerja'],
                //     "jenis_pekerjaan"       => $val->pas['jenis_pekerjaan'],
                //     "tgl_mulai_kerja"       => $val->pas['tgl_mulai_kerja'], //Carbon::parse($val->pas['tgl_mulai_kerja'])->format('d-m-Y'),
                //     "no_telp_tempat_kerja"  => $val->pas['no_telp_tempat_kerja'],
                //     'alamat' => [
                //         'alamat_singkat' => $val->pas['alamat_tempat_kerja'],
                //         'rt'             => $val->pas['rt_tempat_kerja'],
                //         'rw'             => $val->pas['rw_tempat_kerja'],
                //         'kelurahan' => [
                //             'id'    => $val->pas['kel_kerja']['id'],
                //             'nama'  => $val->pas['kel_kerja']['nama']
                //         ],
                //         'kecamatan' => [
                //             'id'    => $val->pas['kec_kerja']['id'],
                //             'nama'  => $val->pas['kec_kerja']['nama']
                //         ],
                //         'kabupaten' => [
                //             'id'    => $val->pas['kab_kerja']['id'],
                //             'nama'  => $val->pas['kab_kerja']['nama'],
                //         ],
                //         'provinsi'  => [
                //             'id'   => $val->pas['prov_kerja']['id'],
                //             'nama' => $val->pas['prov_kerja']['nama'],
                //         ],
                //         'kode_pos' => $val->pas['kel_kerja']['kode_pos']
                //     ]
                // ],
                // 'lampiran' => [
                //     'lamp_ktp'         => $val->pas['lamp_ktp'],
                //     'lamp_buku_nikah'  => $val->pas['lamp_buku_nikah']
                // ]
            ],
            'data_penjamin' => $pen,
            'das'=> [
                'status'  => $status_das,
                'catatan' => $val->catatan_das
            ],
            'hm' => [
                'status'  => $status_hm,
                'catatan' => $val->catatan_hm
            ],
            'ao' => [
                'status'  => $status_ao,
                'catatan' => $val->catatan_ao
            ],
            'lampiran'  => [
                "ideb"      => explode(";", $val->lamp_ideb),
                "pefindo"   => explode(";", $val->lamp_pefindo)
            ]
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

    public function update($id, Request $request, BlankRequest $req) {

        $user_id  = $request->auth->user_id;
        $username = $request->auth->user;

        $PIC = PIC::where('user_id', $user_id)->first();

        if ($PIC == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$username."' . Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $countTAO = TransAO::latest('id','nomor_ao')->first();

        if (!$countTAO) {
            $lastNumb = 1;
        }else{
            $no = $countTAO->nomor_ao;

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

        $check = TransSO::where('id',$id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",",$check->id_penjamin);

        $TransAO = array(
            'nomor_ao'              => $nomor_ao,
            'id_trans_so'           => $id,
            'user_id'               => $user_id,
            'id_pic'                => $PIC->id,
            'id_cabang'             => $PIC->id_mk_cabang,
            'catatan_ao'            => $req->input('catatan_ao'),
            'status_ao'             => empty($req->input('status_ao')) ? 1 : $req->input('status_ao')
        );

        $recom_AO = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => empty($req->input('plafon_kredit')) ? $check->faspin['plafon'] : $req->input('plafon_kredit'),
            'jangka_waktu'          => $req->input('jangka_waktu'),
            'suku_bunga'            => $req->input('suku_bunga'),
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'analisa_ao'            => $req->input('analisa_ao'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_tabungan'        => $req->input('biaya_tabungan')
        );

        $dataVerifikasi = array(
            'ver_ktp_debt'            => $req->input('ver_ktp_debt'),
            'ver_kk_debt'             => $req->input('ver_kk_debt'),
            'ver_akta_cerai_debt'     => $req->input('ver_akta_cerai_debt'),
            'ver_akta_kematian_debt'  => $req->input('ver_akta_kematian_debt'),
            'ver_rek_tabungan_debt'   => $req->input('ver_rek_tabungan_debt'),
            'ver_sertifikat_debt'     => $req->input('ver_sertifikat_debt'),
            'ver_sttp_pbb_debt'       => $req->input('ver_sttp_pbb_debt'),
            'ver_imb_debt'            => $req->input('ver_imb_debt'),
            'ver_ktp_pasangan'        => $req->input('ver_ktp_pasangan'),
            'ver_akta_nikah_pasangan' => $req->input('ver_akta_nikah_pasangan'),
            'ver_data_penjamin'       => $req->input('ver_data_penjamin'),
            'ver_sku_debt'            => $req->input('ver_sku_debt'),
            'ver_pembukuan_usaha_debt'=> $req->input('ver_pembukuan_usaha_debt'),
            'catatan'                 => $req->input('catatan_verifikasi')
        );

        $dataValidasi = array(
            'val_data_debt'       => $req->input('val_data_debt'),
            'val_lingkungan_debt' => $req->input('val_lingkungan_debt'),
            'val_domisili_debt'   => $req->input('val_domisili_debt'),
            'val_pekerjaan_debt'  => $req->input('val_pekerjaan_debt'),
            'val_data_pasangan'   => $req->input('val_data_pasangan'),
            'val_data_penjamin'   => $req->input('val_data_penjamin'),
            'val_agunan'          => $req->input('val_agunan'),
            'catatan'             => $req->input('catatan_validasi')
        );

        $lamp_dir = 'public/'.$check->debt['no_ktp'];

        if($files = $req->file('lamp_agunan_depan')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDepan[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_depan_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_depan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDepanKen[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('lamp_agunan_kanan')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKanan[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_kanan_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kanan'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKananKen[] = $path.'/'.$name;
            }
        }

        if($files = $req->file('lamp_agunan_kiri')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKiri[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_kiri_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_kiri'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanKiriKen[] = $path.'/'.$name;
            }
        }


        if($files = $req->file('lamp_agunan_belakang')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanBelakang[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_belakang_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_belakang'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanBelakangKen[] = $path.'/'.$name;
            }
        }


        if($files = $req->file('lamp_agunan_dalam')){
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDalam[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_agunan_dalam_ken')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_kendaraan';
                $name = 'agunan_dalam'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $agunanDalamKen[] = $path.'/'.$name;
            }
        }

        // Tambahan Agunan Tanah
        if ($files = $req->file('lamp_imb')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'lamp_imb'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $lamp_imb[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_pbb')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'lamp_pbb'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $lamp_pbb[] = $path.'/'.$name;
            }
        }

        if ($files = $req->file('lamp_sertifikat')) {
            $a = 1;
            foreach($files as $file){
                $path = $lamp_dir.'/agunan_tanah';
                $name = 'lamp_sertifikat'.$a.'.'.$file->getClientOriginalExtension();
                $file->move($path,$name);
                $a++;

                $lamp_sertifikat[] = $path.'/'.$name;
            }
        }


        if (!empty($req->input('tipe_lokasi_agunan'))) {
            for ($i = 0; $i < count($req->input('tipe_lokasi_agunan')); $i++){
                $daAguTa[] = [
                    'tipe_lokasi'             => empty($req->tipe_lokasi_agunan[$i]) ? null[$i] : strtoupper($req->tipe_lokasi_agunan[$i]),
                    'alamat'                  => empty($req->alamat_agunan[$i]) ? null[$i] : $req->alamat_agunan[$i],
                    'id_provinsi'             => empty($req->id_prov_agunan[$i]) ? null[$i] : $req->id_prov_agunan[$i],
                    'id_kabupaten'            => empty($req->id_kab_agunan[$i]) ? null[$i] : $req->id_kab_agunan[$i],
                    'id_kecamatan'            => empty($req->id_kec_agunan[$i]) ? null[$i] : $req->id_kec_agunan[$i],
                    'id_kelurahan'            => empty($req->id_kel_agunan[$i]) ? null[$i] : $req->id_kel_agunan[$i],
                    'rt'                      => empty($req->rt_agunan[$i]) ? null[$i] : $req->rt_agunan[$i],
                    'rw'                      => empty($req->rw_agunan[$i]) ? null[$i] : $req->rw_agunan[$i],
                    'luas_tanah'              => empty($req->luas_tanah[$i]) ? null[$i] : $req->luas_tanah[$i],
                    'luas_bangunan'           => empty($req->luas_bangunan[$i]) ? null[$i] : $req->luas_bangunan[$i],
                    'nama_pemilik_sertifikat' => empty($req->nama_pemilik_sertifikat[$i]) ? null[$i] : $req->nama_pemilik_sertifikat[$i],
                    'jenis_sertifikat'        => empty($req->jenis_sertifikat[$i]) ? null[$i] : strtoupper($req->jenis_sertifikat[$i]),
                    'no_sertifikat'           => empty($req->no_sertifikat[$i]) ? null[$i] : $req->no_sertifikat[$i],
                    'tgl_ukur_sertifikat'     => empty($req->tgl_ukur_sertifikat[$i]) ? null[$i] : $req->tgl_ukur_sertifikat[$i],
                    'tgl_berlaku_shgb'        => empty($req->tgl_berlaku_shgb[$i]) ? null[$i] : Carbon::parse($req->tgl_berlaku_shgb[$i])->format('Y-m-d'),
                    'no_imb'                  => empty($req->no_imb[$i]) ? null[$i] : $req->no_imb[$i],
                    'njop'                    => empty($req->njop[$i]) ? null[$i] : $req->njop[$i],
                    'nop'                     => empty($req->nop[$i]) ? null[$i] : $req->nop[$i],
                    // 'lam_imb'                 => empty($req->file('lam_imb')[$i]) ? null : Helper::img64enc($req->file('lam_imb')[$i]),
                    'lamp_agunan_depan'       => empty($agunanDepan[$i]) ? null[$i] : $agunanDepan[$i],
                    'lamp_agunan_kanan'       => empty($agunanKanan[$i]) ? null[$i] : $agunanKanan[$i],
                    'lamp_agunan_kiri'        => empty($agunanKiri[$i]) ? null[$i] : $agunanKiri[$i],
                    'lamp_agunan_belakang'    => empty($agunanBelakang[$i]) ? null[$i] : $agunanBelakang[$i],
                    'lamp_agunan_dalam'       => empty($agunanDalam[$i]) ? null[$i] : $agunanDalam[$i],

                    'lamp_imb'                => empty($lamp_imb[$i]) ? null[$i] : $lamp_imb[$i],
                    'lamp_pbb'                => empty($lamp_pbb[$i]) ? null[$i] : $lamp_pbb[$i],
                    'lamp_sertifikat'         => empty($lamp_sertifikat[$i]) ? null[$i] : $lamp_sertifikat[$i]
                ];

                $pemAguTa[] = [
                    'nama_penghuni'     => empty($req->nama_penghuni_agunan[$i]) ? null[$i] : $req->nama_penghuni_agunan[$i],
                    'status_penghuni'   => empty($req->status_penghuni_agunan[$i]) ? null[$i] : strtoupper($req->status_penghuni_agunan[$i]),
                    'bentuk_bangunan'   => empty($req->bentuk_bangunan_agunan[$i]) ? null[$i] : $req->bentuk_bangunan_agunan[$i],
                    'kondisi_bangunan'  => empty($req->kondisi_bangunan_agunan[$i]) ? null[$i] : $req->kondisi_bangunan_agunan[$i],
                    'fasilitas'         => empty($req->fasilitas_agunan[$i]) ? null[$i] : $req->fasilitas_agunan[$i],
                    'listrik'           => empty($req->listrik_agunan[$i]) ? null[$i] : $req->listrik_agunan[$i],
                    'nilai_taksasi_agunan'   => empty($req->nilai_taksasi_agunan[$i]) ? null[$i] : $req->nilai_taksasi_agunan[$i],
                    'nilai_taksasi_bangunan' => empty($req->nilai_taksasi_bangunan[$i]) ? null[$i] : $req->nilai_taksasi_bangunan[$i],
                    'tgl_taksasi'       => empty($req->tgl_taksasi_agunan[$i]) ? null[$i] : Carbon::parse($req->tgl_taksasi_agunan[$i])->format('Y-m-d'),
                    'nilai_likuidasi'   => empty($req->nilai_likuidasi_agunan[$i]) ? null[$i] : $req->nilai_likuidasi_agunan[$i]
                ];
            }

        }

        if (!empty($req->input('no_bpkb_ken'))) {
            for ($i = 0; $i < count($req->input('no_bpkb_ken')); $i++) {
                $daAguKe[] = [
                    'no_bpkb'               => empty($req->no_bpkb_ken[$i]) ? null[$i] : $req->no_bpkb_ken[$i],
                    'nama_pemilik'          => empty($req->nama_pemilik_ken[$i]) ? null[$i] : $req->nama_pemilik_ken[$i],
                    'alamat_pemilik'        => empty($req->alamat_pemilik_ken[$i]) ? null[$i] : $req->alamat_pemilik_ken[$i],
                    'merk'                  => empty($req->merk_ken[$i]) ? null[$i] : $req->merk_ken[$i],
                    'jenis'                 => empty($req->jenis_ken[$i]) ? null[$i] : $req->jenis_ken[$i],
                    'no_rangka'             => empty($req->no_rangka_ken[$i]) ? null[$i] : $req->no_rangka_ken[$i],
                    'no_mesin'              => empty($req->no_mesin_ken[$i]) ? null[$i] : $req->no_mesin_ken[$i],
                    'warna'                 => empty($req->warna_ken[$i]) ? null[$i] : $req->warna_ken[$i],
                    'tahun'                 => empty($req->tahun_ken[$i]) ? null[$i] : $req->tahun_ken[$i],
                    'no_polisi'             => empty($req->no_polisi_ken[$i]) ? null[$i] : strtoupper($req->no_polisi_ken[$i]),
                    'no_stnk'               => empty($req->no_stnk_ken[$i]) ? null[$i] : $req->no_stnk_ken[$i],
                    'tgl_kadaluarsa_pajak'  => empty($req->tgl_exp_pajak_ken[$i]) ? null[$i] : Carbon::parse($req->tgl_exp_pajak_ken[$i])->format('Y-m-d'),
                    'tgl_kadaluarsa_stnk'   => empty($req->tgl_exp_stnk_ken[$i]) ? null[$i] : Carbon::parse($req->tgl_exp_stnk_ken[$i])->format('Y-m-d'),
                    'no_faktur'             => empty($req->no_faktur_ken[$i]) ? null[$i] : $req->no_faktur_ken[$i],
                    'lamp_agunan_depan'     => empty($agunanDepanKen[$i]) ? null[$i] : $agunanDepanKen[$i],
                    'lamp_agunan_kanan'     => empty($agunanKananKen[$i]) ? null[$i] : $agunanKananKen[$i],
                    'lamp_agunan_kiri'      => empty($agunanKiriKen[$i]) ? null[$i] : $agunanKiriKen[$i],
                    'lamp_agunan_belakang'  => empty($agunanBelakangKen[$i]) ? null[$i] : $agunanBelakangKen[$i],
                    'lamp_agunan_dalam'     => empty($agunanDalamKen[$i]) ? null[$i] : $agunanDalamKen[$i]
                ];

                $pemAguKe[] = [
                    'nama_pengguna'         => empty($req->nama_pengguna_ken[$i]) ? null[$i] : $req->nama_pengguna_ken[$i],
                    'status_pengguna'       => empty($req->status_pengguna_ken[$i]) ? null[$i] : strtoupper($req->status_pengguna_ken[$i]),
                    'jml_roda_kendaraan'    => empty($req->jml_roda_ken[$i]) ? null[$i] : $req->jml_roda_ken[$i],
                    'kondisi_kendaraan'     => empty($req->kondisi_ken[$i]) ? null[$i] : $req->kondisi_ken[$i],
                    'keberadaan_kendaraan'  => empty($req->keberadaan_ken[$i]) ? null[$i] : $req->keberadaan_ken[$i],
                    'body'                  => empty($req->body_ken[$i]) ? null[$i] : $req->body_ken[$i],
                    'interior'              => empty($req->interior_ken[$i]) ? null[$i] : $req->interior_ken[$i],
                    'km'                    => empty($req->km_ken[$i]) ? null[$i] : $req->km_ken[$i],
                    'modifikasi'            => empty($req->modifikasi_ken[$i]) ? null[$i] : $req->modifikasi_ken[$i],
                    'aksesoris'             => empty($req->aksesoris_ken[$i]) ? null[$i] : $req->aksesoris_ken[$i]
                ];
            }
        }

        // Start Kapasitas Bulanan
        $inputKapBul = array(
            'pemasukan_cadebt'      => empty($req->input('pemasukan_debitur')) ? null : (int) $req->input('pemasukan_debitur'),
            'pemasukan_pasangan'    => empty($req->input('pemasukan_pasangan')) ? null : (int) $req->input('pemasukan_pasangan'),
            'pemasukan_penjamin'    => empty($req->input('pemasukan_penjamin')) ? null : (int) $req->input('pemasukan_penjamin'),
            'biaya_rumah_tangga'    => empty($req->input('biaya_rumah_tangga')) ? null : (int) $req->input('biaya_rumah_tangga'),
            'biaya_transport'       => empty($req->input('biaya_transport')) ? null : (int) $req->input('biaya_transport'),
            'biaya_pendidikan'      => empty($req->input('biaya_pendidikan')) ? null : (int) $req->input('biaya_pendidikan'),
            'biaya_telp_listr_air'  => empty($req->input('biaya_telp_listr_air')) ? null : (int) $req->input('biaya_telp_listr_air'),
            'angsuran'              => empty($req->input('angsuran')) ? null : $req->input('angsuran'),
            'biaya_lain'            => empty($req->input('biaya_lain')) ? null : (int) $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'       => $ttl1 = array_sum(array_slice($inputKapBul, 0, 2)),
            'total_pengeluaran'     => $ttl2 = array_sum(array_slice($inputKapBul, 2)),
            'penghasilan_bersih'    => $ttl1 - $ttl2
        );

        $kapBul = array_merge($inputKapBul, $total_KapBul);
        // End Kapasitas Bulanan


        if (!empty($req->input('pemasukan_tunai'))) {
            // $dataKeUsaha = array(
            $inputKeUsaha = array(
                'pemasukan_tunai'      => empty($req->input('pemasukan_tunai')) ? null : (int) $req->input('pemasukan_tunai'),
                'pemasukan_kredit'     => empty($req->input('pemasukan_kredit')) ? null : (int) $req->input('pemasukan_kredit'),
                'biaya_sewa'           => empty($req->input('biaya_sewa')) ? null : (int) $req->input('biaya_sewa'),
                'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai')) ? null : (int) $req->input('biaya_gaji_pegawai'),
                'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg')) ? null : (int) $req->input('biaya_belanja_brg'),
                'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? null : (int) $req->input('biaya_telp_listr_air'),
                'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? null : (int) $req->input('biaya_sampah_kemanan'),
                'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang')) ? null : (int) $req->input('biaya_kirim_barang'),
                'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? null : (int) $req->input('biaya_hutang_dagang'),
                'biaya_angsuran'       => empty($req->input('biaya_angsuran')) ? null : (int) $req->input('biaya_angsuran'),
                'biaya_lain_lain'      => empty($req->input('biaya_lain_lain')) ? null : (int) $req->input('biaya_lain_lain')
            );

            $total_KeUsaha = array(
                'total_pemasukan'      => $ttl1 = array_sum(array_slice($inputKeUsaha, 0, 2)),
                'total_pengeluaran'    => $ttl2 = array_sum(array_slice($inputKeUsaha, 2)),
                'laba_usaha'           => $ttl1 - $ttl2
            );

            $dataKeUsaha = array_merge($inputKeUsaha, $total_KeUsaha);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        DB::connection('web')->beginTransaction();
        // try{

            if ($check_ao == null) {

                if (!empty($daAguTa)) {
                    for ($i = 0; $i < count($daAguTa); $i++) {

                        $tanah = AgunanTanah::create($daAguTa[$i]);

                        $id_tanah['id'][$i] = $tanah->id;
                    }

                    for ($i = 0; $i < count($pemAguTa); $i++) {
                        $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $pemAguTa[$i]);

                        $pemTanah = PemeriksaanAgunTan::create($pemAguTa_N[$i]);

                        $id_pem_tan['id'][$i] = $pemTanah->id;
                    }

                    $tanID   = implode(",", $id_tanah['id']);
                    $p_tanID = implode(",", $id_pem_tan['id']);
                }else{
                    $tanID   = null;
                    $p_tanID = null;
                }


                if (!empty($daAguKe)) {
                    for ($i = 0; $i < count($daAguKe); $i++) {
                        $kendaraan = AgunanKendaraan::create($daAguKe[$i]);

                        $id_kendaraan['id'][$i] = $kendaraan->id;
                    }

                    for ($i = 0; $i < count($pemAguKe); $i++) {
                        $pemAguKe_N[$i] = array_merge(array('id_agunan_kendaraan' => $id_kendaraan['id'][$i]), $pemAguKe[$i]);

                        $pemKendaraan = PemeriksaanAgunKen::create($pemAguKe_N[$i]);

                        $id_pem_ken['id'][$i] = $pemKendaraan->id;
                    }

                    $kenID   = implode(",", $id_kendaraan['id']);
                    $p_kenID = implode(",", $id_pem_ken['id']);
                }else{
                    $kenID   = null;
                    $p_kenID = null;
                }

                $valid = ValidModel::create($dataValidasi);
                $id_valid = $valid->id;

                $verif = VerifModel::create($dataVerifikasi);
                $id_verif = $verif->id;


                $kap = KapBulanan::create($kapBul);
                $id_kapbul = $kap->id;

                if (!empty($dataKeUsaha)) {
                    $keuangan = PendapatanUsaha::create($dataKeUsaha);
                    $id_usaha = $keuangan->id;
                }else{
                    $id_usaha = null;
                }

                if (!empty($recom_AO)) {
                    $recom = RekomendasiAO::create($recom_AO);
                    $id_recom = $recom->id;
                }else{
                    $id_recom = null;
                }

                $dataAO = array(
                    'id_validasi'          => $id_valid,
                    'id_verifikasi'        => $id_verif,
                    'id_agunan_tanah'      => $tanID,
                    'id_agunan_kendaraan'  => $kenID,
                    'id_periksa_agunan_tanah'     => $p_tanID,
                    'id_periksa_agunan_kendaraan' => $p_kenID,
                    'id_kapasitas_bulanan' => $id_kapbul,
                    'id_pendapatan_usaha'  => $id_usaha,
                    'id_recom_ao'          => $id_recom
                );

                $arrAO = array_merge($TransAO, $dataAO);

                $new_TransAO = TransAO::create($arrAO);

                TransSO::where('id', $id)->update(['id_trans_ao' => $new_TransAO->id]);
            }else{
                if (!empty($daAguTa)) {

                    if (!empty($check_ao->id_agunan_tanah)) {
                        $id_aguta = explode(",", $check_ao->id_agunan_tanah);

                        AgunanTanah::whereIn('id', $id_aguta)->delete();
                    }


                    for ($i = 0; $i < count($daAguTa); $i++) {
                        // $x[] = $id_aguta[$i];
                        $tanah = AgunanTanah::create($daAguTa[$i]);

                        $id_tanah['id'][$i] = $tanah->id;
                    }


                    if (!empty($check_ao->id_periksa_agunan_tanah)) {
                        $id_pe_aguta = explode(",", $check_ao->id_periksa_agunan_tanah);

                        PemeriksaanAgunTan::whereIn('id', $id_pe_aguta)->delete();
                    }

                    for ($i = 0; $i < count($pemAguTa); $i++) {

                        $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $pemAguTa[$i]);

                        $pemTanah = PemeriksaanAgunTan::create($pemAguTa_N[$i]);

                        $id_pem_tan['id'][$i] = $pemTanah->id;
                    }


                    $tanID   = implode(",", $id_tanah['id']);
                    $p_tanID = implode(",", $id_pem_tan['id']);
                }else{
                    $tanID   = $check_ao->id_agunan_tanah;
                    $p_tanID = $check_ao->id_periksa_agunan_tanah;
                }


                if (!empty($daAguKe)) {

                    if (!empty($check_ao->id_agunan_kendaraan)) {
                        $id_aguke = explode(",", $check_ao->id_agunan_kendaraan);

                        AgunanKendaraan::whereIn('id', $id_aguke)->delete();
                    }

                    for ($i = 0; $i < count($daAguKe); $i++) {

                        $kendaraan = AgunanKendaraan::create($daAguKe[$i]);

                        $id_kendaraan['id'][$i] = $kendaraan->id;
                    }


                    if (!empty($check_ao->id_periksa_agunan_kendaraan)) {
                        $id_pe_aguke = explode(",", $check_ao->id_periksa_agunan_kendaraan);

                        PemeriksaanAgunKen::where('id', $id_pe_aguke)->delete();
                    }

                    for ($i = 0; $i < count($pemAguKe); $i++) {

                        $pemAguKe_N[$i] = array_merge(array('id_agunan_kendaraan' => $id_kendaraan['id'][$i]), $pemAguKe[$i]);

                        $pemKendaraan = PemeriksaanAgunKen::create($pemAguKe_N[$i]);

                        $id_pem_ken['id'][$i] = $pemKendaraan->id;
                    }


                    $kenID   = implode(",", $id_kendaraan['id']);
                    $p_kenID = implode(",", $id_pem_ken['id']);
                }else{
                    $kenID   = $check_ao->id_agunan_kendaraan;
                    $p_kenID = $check_ao->id_periksa_agunan_kendaraan;
                }

                if (!empty($check_ao->id_validasi)) {
                    $valid = ValidModel::where('id', $check_ao->id_validasi)->update($dataValidasi);
                    $id_valid = $check_ao->id_validasi;
                }else{
                    $valid = ValidModel::create($dataValidasi);
                    $id_valid = $valid->id;
                }

                if (!empty($check_ao->id_verifikasi)) {
                    $verif = VerifModel::where('id', $check_ao->id_verifikasi)->update($dataVerifikasi);
                    $id_verif = $check_ao->id_verifikasi;
                }else{
                    $verif = VerifModel::create($dataVerifikasi);
                    $id_verif = $verif->id;
                }

                if (!empty($check_ao->id_kapasitas_bulanan)) {
                    $kap = KapBulanan::where('id', $check_ao->id_kapasitas_bulanan)->update($kapBul);
                    $id_kapbul = $check_ao->id_kapasitas_bulanan;
                }else{
                    $kap = KapBulanan::create($kapBul);
                    $id_kapbul = $kap->id;
                }

                if (!empty($check_ao->id_pendapatan_usaha)) {
                    if (!empty($dataKeUsaha)) {
                        $keuangan = PendapatanUsaha::where('id', $check_ao->id_pendapatan_usaha)->update($dataKeUsaha);
                        $id_usaha = $check_ao->id_pendapatan_usaha;
                    }else{
                        $id_usaha = $check_ao->id_pendapatan_usaha;
                    }
                }else{
                    if (!empty($dataKeUsaha)){
                        $keuangan = PendapatanUsaha::create($dataKeUsaha);
                        $id_usaha = $keuangan->id;
                    }else{
                        $id_usaha = null;
                    }
                }

                if (!empty($check_ao->id_recom_ao)){
                    if (!empty($recom_AO)) {
                        $recom = RekomendasiAO::where('id', $check_ao->id_recom_ao)->update($recom_AO);
                        $id_recom = $check_ao->id_recom_ao;
                    }else{
                        $id_recom = $check_ao->id_recom_ao;
                    }
                }else{
                    if (!empty($recom_AO)) {
                        $recom = RekomendasiAO::create($recom_AO);
                        $id_recom = $recom->id;
                    }else{
                        $id_recom = null;
                    }
                }

                $dataAO = array(
                    'id_validasi'          => $id_valid,
                    'id_verifikasi'        => $id_verif,
                    'id_agunan_tanah'      => $tanID,
                    'id_agunan_kendaraan'  => $kenID,
                    'id_periksa_agunan_tanah'     => $p_tanID,
                    'id_periksa_agunan_kendaraan' => $p_kenID,
                    'id_kapasitas_bulanan' => $id_kapbul,
                    'id_pendapatan_usaha'  => $id_usaha,
                    'id_recom_ao'          => $id_recom
                );

                $arrAO = array_merge($TransAO, $dataAO);

                $new_TransAO = TransAO::where('id', $check_ao->id)->update($arrAO);

                TransSO::where('id', $id)->update(['id_trans_ao' => $check_ao->id]);
            }

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk AO berhasil dikirim'
                // 'message'=> $msg
            ], 200);
        // } catch (\Exception $e) {
        //     $err = DB::connection('web')->rollback();
        //     return response()->json([
        //         'code'    => 501,
        //         'status'  => 'error',
        //         'message' => $err
        //     ], 501);
        // }
    }

    public function search($search, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(AO). Harap daftarkan diri sebagai PIC(AO) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $query = TransSO::with('pic', 'cabang', 'asaldata', 'debt', 'pas', 'faspin', 'ao', 'ca')
                ->where('id_cabang', $id_cabang)
                ->where('nomor_so', 'like', '%'.$search.'%')
                ->get();
        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {

            if ($val->status_das == 1) {
                $status_das = 'complete';
            }elseif($val->status_das == 2){
                $status_das = 'not complete';
            }else{
                $status_das = 'waiting';
            }

            if ($val->status_hm == 1) {
                $status_hm = 'complete';
            }elseif ($val->status_hm == 2) {
                $status_hm = 'not complete';
            }else{
                $status_hm = 'waiting';
            }

            if ($val->ao['status_ao'] == 1) {
                $status_ao = 'recommend';
            }elseif ($val->ao['status_ao'] == 2) {
                $status_ao = 'not recommend';
            }else{
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id'             => $val->id,
                'nomor_so'       => $val->nomor_so,
                // 'nomor_ao'       => $val->ao['nomor_ao'],
                'pic'            => $val->pic['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->asaldata['nama'],
                'nama_marketing' => $val->nama_marketing,
                'nama_debitur'   => $val->debt['nama_lengkap'],
                'plafon'         => $val->faspin['plafon'],
                'tenor'          => $val->faspin['tenor'],
                'das'            => [
                    'status'  => $status_das,
                    'catatan' => $val->catatan_das
                ],
                'hm'            => [
                    'status'  => $status_hm,
                    'catatan' => $val->catatan_hm
                ],
                'ao'            => [
                    'status'  => $status_ao,
                    'catatan' => $val->catatan_ao
                ]
                // 'das_status'     => $status_das,
                // 'das_note'       => $val->catatan_das,
                // 'hm_status'      => $status_das,
                // 'hm_note'        => $val->catatan_hm,
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
}
