<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\AgunanTanah;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransTCAA;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransSO;
use App\Models\Karyawan\TeamCAA;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class TeamCAA_Controller extends BaseController
{
    public function list_team(Request $req) {
        $user_id  = $req->auth->user_id; //1725540

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar pada PIC (Karyawan) di Sevin System. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_mk_area;
        $id_cabang = $pic->id_mk_cabang;

        if($pic->jpic['nama_jenis'] == 'DIR UT' || $pic->jpic['nama_jenis'] == 'DIR BIS' || $pic->jpic['nama_jenis'] == 'DIR RISK'){
            $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) {
                    // Query the name field in status table
                    $q->where('nama_jenis', '=', 'DIR UT'); // '=' is optional
                    $q->orWhere('nama_jenis', '=', 'DIR BIS');
                    $q->orWhere('nama_jenis', '=', 'DIR RISK');
                    $q->orWhere('nama_jenis', '=', 'CRM');
                    $q->orWhere('nama_jenis', '=', 'AM');
                    $q->orWhere('nama_jenis', '=', 'PC');
                })
                ->where('flg_aktif', 1)
                ->where('id', '!=', $pic->id)
                ->get();

        }elseif($pic->jpic['nama_jenis'] == 'CRM' || $pic->jpic['nama_jenis'] == 'AM'){
            $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) {
                    // Query the name field in status table
                    $q->where('nama_jenis', '=', 'DIR UT'); // '=' is optional
                    $q->orWhere('nama_jenis', '=', 'DIR BIS');
                    $q->orWhere('nama_jenis', '=', 'DIR RISK');
                    $q->orWhere('nama_jenis', '=', 'CRM');
                    $q->orWhere('nama_jenis', '=', 'AM');
                    $q->orWhere('nama_jenis', '=', 'PC');
                })
                ->where('flg_aktif', 1)
                ->where('id_mk_area', $id_area)
                ->where('id', '!=', $pic->id)
                ->get();
        }else{
            $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) {
                    // Query the name field in status table
                    $q->where('nama_jenis', '=', 'DIR UT'); // '=' is optional
                    $q->orWhere('nama_jenis', '=', 'DIR BIS');
                    $q->orWhere('nama_jenis', '=', 'DIR RISK');
                    $q->orWhere('nama_jenis', '=', 'CRM');
                    $q->orWhere('nama_jenis', '=', 'AM');
                    $q->orWhere('nama_jenis', '=', 'PC');
                })
                ->where('flg_aktif', 1)
                ->where('id_mk_area', $id_area)
                ->where('id_mk_cabang', $id_cabang)
                ->where('id', '!=', $pic->id)
                ->get();
        }

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query as $val) {
            $data[] = array(
                "id"        => $val->id,
                "user_id"   => $val->user_id,
                "nama_area" => $val->area['nama'],
                "cabang"    => $val->cabang['nama'],
                "jabatan"   => $val->jpic['nama_jenis'],
                "nama"      => $val->nama,
                "email"     => $val->email,
                "flg_aktif" => $val->flg_aktif == 1 ? "true" : "false"
            );
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

    public function index(Request $req){
        $user_id = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."'. Yang berhak melihat halaman ini adalah Direktur, CRM, PC dan AM. Mohon cek dimenu Team CAA untuk validasi data anda atau silahkan hubungin tim IT"
            ], 404);
        }


        $id_area   = $pic->id_mk_area;
        $id_cabang = $pic->id_mk_cabang;

        if($pic->jpic['nama_jenis'] == 'DIR UT' || $pic->jpic['nama_jenis'] == 'DIR BIS' || $pic->jpic['nama_jenis'] == 'DIR RISK'){

            $query = TransCAA::where('status_caa', 1)->get();

        }elseif($pic->jpic['nama_jenis'] == 'CRM' || $pic->jpic['nama_jenis'] == 'AM'){

            $query = TransCAA::where('status_caa', 1)
                ->where('id_area', $id_area)->where('pic_team_caa', 'like', "%{$pic->id}%")->get();

        }else{

            $query = TransCAA::where('status_caa', 1)
                ->where('id_area', $id_area)->where('id_cabang', $id_cabang)->where('pic_team_caa', 'like', "%{$pic->id}%")->get();

        }

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        // $data = array();
        foreach ($query as $key => $val) {

            $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);
            $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

            $Tan = array();
            foreach ($AguTa as $key => $value) {
                $Tan[$key] = array(
                    'id'    => $id_agu_ta[$key],
                    'jenis' => $value->jenis_sertifikat
                );
            }

            $id_agu_ke = explode (",",$val->so['ao']['id_agunan_kendaraan']);
            $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

            $Ken = array();
            foreach ($AguKe as $key => $value) {
                $Ken[$key] = array(
                    'id'    => $id_agu_ke[$key],
                    'jenis' => $value->jenis
                );
            }

            $rekomendasi_ao = array(
                'id'               => $val->so['ao']['id_recom_ao'],
                'produk'           => $val->so['ao']['recom_ao']['produk'],
                'plafon'           => $val->so['ao']['recom_ao']['plafon_kredit'],
                'tenor'            => $val->so['ao']['recom_ao']['jangka_waktu'],
                'suku_bunga'       => $val->so['ao']['recom_ao']['suku_bunga'],
                'pembayaran_bunga' => $val->so['ao']['recom_ao']['pembayaran_bunga']
            );

            $rekomendasi_ca = array(
                'id'               => $val->so['ca']['id_recom_ca'],
                'produk'           => $val->so['ca']['recom_ca']['produk'],
                'plafon'           => $val->so['ca']['recom_ca']['plafon_kredit'],
                'tenor'            => $val->so['ca']['recom_ca']['jangka_waktu'],
                'suku_bunga'       => $val->so['ca']['recom_ca']['suku_bunga'],
                'pembayaran_bunga' => $val->so['ca']['recom_ca']['pembayaran_bunga'],
                'rekomendasi_angsuran' => $val->so['ca']['recom_ca']['rekom_angsuran']
            );

            if ($val->status_caa == 0) {
                $status_caa = 'waiting';
            }elseif ($val->status_caa == 1) {
                $status_caa = 'approve';
            }elseif($val->status_caa == 2){
                $status_caa = 'return';
            }elseif($val->status_caa == 3){
                $status_caa = 'reject';
            }

            $data[] = [
                'id_trans_so'    => $val->id_trans_so,
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'nomor_caa'      => $val->nomor_caa,
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'pengajuan' => [
                    'plafon' => $val->so['faspin']['plafon'],
                    'tenor'  => $val->so['faspin']['tenor']
                ],
                'rekomendasi_ao' => $rekomendasi_ao,
                'rekomendasi_ca' => $rekomendasi_ca,
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'agunan' => [
                    'tanah'     => $Tan,
                    'kendaraan' => $Ken
                ],
                'status_caa'    => $status_caa,
                'tgl_transaksi' => Carbon::parse($val->updated_at)->format("d-m-Y H:i:s"),
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

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."'. Yang berhak melihat halaman ini adalah Direktur, CRM, PC dan AM. Mohon cek dimenu Team CAA untuk validasi data anda atau silahkan hubungin tim IT"
            ], 404);
        }


        $id_area   = $pic->id_mk_area;
        $id_cabang = $pic->id_mk_cabang;

        if($pic->jpic['nama_jenis'] == 'DIR UT' || $pic->jpic['nama_jenis'] == 'DIR BIS' || $pic->jpic['nama_jenis'] == 'DIR RISK'){

            $query = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        }elseif($pic->jpic['nama_jenis'] == 'CRM' || $pic->jpic['nama_jenis'] == 'AM'){

            $query = TransCAA::where('status_caa', 1)
                ->where('id_area', $id_area)
                ->where('id_trans_so', $id)
                ->first();

        }else{

            $query = TransCAA::where('status_caa', 1)
                ->where('id_area', $id_area)->where('id_cabang', $id_cabang)
                ->where('id_trans_so', $id)
                ->first();

        }

        if ($query == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di wilayah anda kosong'
            ], 404);
        }

        $id_agu_ta = explode (",",$query->so['ao']['id_agunan_tanah']);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

        foreach ($AguTa as $key => $value) {
            $idTan[$key] = array(
                'id'             => $value->id,
                'jenis'          => $value->jenis_sertifikat,
                'tipe_lokasi'    => $value->tipe_lokasi,
                'luas' => [
                    'tanah'    => $value->luas_tanah,
                    'bangunan' => $value->luas_bangunan
                ],
                'tgl_berlaku_shgb'        => Carbon::parse($value->tgl_berlaku_shgb)->format("d-m-Y"),
                'nama_pemilik_sertifikat' => $value->nama_pemilik_sertifikat,
                'tgl_atau_no_ukur'        => $value->tgl_ukur_sertifikat,
                'lampiran' => [
                    'agunan_depan'    => $value->lamp_agunan_depan,
                    'agunan_kanan'    => $value->lamp_agunan_kanan,
                    'agunan_kiri'     => $value->lamp_agunan_kiri,
                    'agunan_belakang' => $value->lamp_agunan_belakang,
                    'agunan_dalam'    => $value->lamp_agunan_dalam
                ]
            );
        }


        $id_agu_ke = explode (",",$query->so['ao']['id_agunan_kendaraan']);

        $AguKe = AgunanKendaraan::whereIn('id', $id_agu_ke)->get();

        foreach ($AguKe as $key => $value) {
            $idKen[$key] = array(
                'id'                    => $value->id,
                'jenis'                 => 'BPKB',
                'tipe_kendaraan'        => $value->jenis,
                'merk'                  => $value->merk,
                'tgl_kadaluarsa_pajak'  => Carbon::parse($value->tgl_kadaluarsa_pajak)->format('d-m-Y'),
                'tgl_kadaluarsa_stnk'   => Carbon::parse($value->tgl_kadaluarsa_stnk)->format('d-m-Y'),
                'nama_pemilik'          => $value->nama_pemilik,
                'no_bpkb'               => $value->no_bpkb,
                'no_polisi'             => $value->no_polisi,
                'no_stnk'               => $value->no_stnk,
                'lampiran' => [
                    'agunan_depan'    => $value->lamp_agunan_depan,
                    'agunan_kanan'    => $value->lamp_agunan_kanan,
                    'agunan_kiri'     => $value->lamp_agunan_kiri,
                    'agunan_belakang' => $value->lamp_agunan_belakang,
                    'agunan_dalam'    => $value->lamp_agunan_dalam,
                ]
            );
        }


        if ($query->status_caa == 1) {
            $status_caa = 'recommend';
        }elseif($query->status_caa == 2){
            $status_caa = 'not recommend';
        }else{
            $status_caa = 'waiting';
        }

        $data = array(
            'id_trans_so'    => $query->id_trans_so,
            'transaksi'   => [
                'so' => [
                    'nomor' => $query->so['nomor_so'],
                    'nama'  => $query->so['pic']['nama']
                ],
                'ao' => [
                    'nomor' => $query->so['ao']['nomor_ao'],
                    'nama'  => $query->so['ao']['pic']['nama']
                ],
                'ca' => [
                    'nomor' => $query->so['ca']['nomor_ca'],
                    'nama'  => $query->so['ca']['pic']['nama']
                ],
                'caa' => [
                    'nomor' => $query->nomor_caa,
                    'nama'  => $query->pic['nama']
                ]
            ],

            'nama_marketing' => $query->so['nama_marketing'],
            'pic_caa'  => [
                'id'      => $query->id_pic,
                'nama'    => $query->pic['nama'],
                'jabatan' => $query->pic['jpic']['nama_jenis']
            ],
            'pic_approval'  => [
                'id'      => $pic->id,
                'nama'    => $pic->nama,
                'jabatan' => $pic['jpic']['nama_jenis']
            ],
            'cabang' => [
                'id'   => $query->id_cabang,
                'nama' => $query->cabang['nama'],
            ],
            'asaldata' => [
                'id'   => $query->so['asaldata']['id'],
                'nama' => $query->so['asaldata']['nama'],
            ],
            'data_debitur' => [
                'id'           => $query->so['id_calon_debitur'],
                'nama_lengkap' => $query->so['debt']['nama_lengkap'],
                'alamat_domisili' => [
                    'alamat_singkat' => $query->so['debt']['alamat_domisili'],
                    'rt'             => $query->so['debt']['rt_domisili'],
                    'rw'             => $query->so['debt']['rw_domisili'],
                    'kelurahan' => [
                        'id'    => $query->so['debt']['id_kel_tempat_kerja'],
                        'nama'  => $query->so['debt']['kel_dom']['nama']
                    ],
                    'kecamatan' => [
                        'id'    => $query->so['debt']['id_kec_domisili'],
                        'nama'  => $query->so['debt']['kec_dom']['nama']
                    ],
                    'kabupaten' => [
                        'id'    => $query->so['debt']['id_kab_domisili'],
                        'nama'  => $query->so['debt']['kab_dom']['nama'],
                    ],
                    'provinsi'  => [
                        'id'   => $query->so['debt']['id_prov_domisili'],
                        'nama' => $query->so['debt']['prov_dom']['nama'],
                    ],
                    'kode_pos' => $query->so['debt']['kel_dom']['kode_pos']
                ],
                'lamp_usaha'   => $query->so['debt']['lamp_foto_usaha']
            ],
            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],
            'pendapatan_usaha' => ['id' => $query->so['ao']['id_pendapatan_usaha']],
            'penyimpangan' => $query->penyimpangan,
            'pengajuan' => [
                'plafon' => $query->so['faspin']['plafon'],
                'tenor'  => $query->so['faspin']['tenor'],
                'jenis_pinjaman' => $query->so['faspin']['jenis_pinjaman']
            ],
            'rekomendasi_ao'   => [
                'id'               => $query->so['ao']['id_recom_ao'],
                'produk'           => $query->so['ao']['recom_ao']['produk'],
                'plafon'           => $query->so['ao']['recom_ao']['plafon_kredit'],
                'tenor'            => $query->so['ao']['recom_ao']['jangka_waktu'],
                'suku_bunga'       => $query->so['ao']['recom_ao']['suku_bunga'],
                'pembayaran_bunga' => $query->so['ao']['recom_ao']['pembayaran_bunga']
            ],
            'rekomendasi_ca' => [
                'id'               => $query->so['ca']['id_recom_ca'],
                'produk'           => $query->so['ca']['recom_ca']['produk'],
                'plafon'           => $query->so['ca']['recom_ca']['plafon_kredit'],
                'tenor'            => $query->so['ca']['recom_ca']['jangka_waktu'],
                'suku_bunga'       => $query->so['ca']['recom_ca']['suku_bunga'],
                'pembayaran_bunga' => $query->so['ca']['recom_ca']['pembayaran_bunga'],
                'rekomendasi_angsuran' => $query->so['ca']['recom_ca']['rekom_angsuran']
            ],
            'data_biaya' => [
                'reguler' => $reguler = array(
                    'biaya_provisi'         => $query->so['ca']['recom_ca']['biaya_provisi'],
                    'biaya_administrasi'    => $query->so['ca']['recom_ca']['biaya_administrasi'],
                    'biaya_premi' => [
                        'asuransi_jiwa'     => $query->so['ca']['recom_ca']['biaya_asuransi_jiwa'],
                        'asuransi_jaminan'  => $query->so['ca']['recom_ca']['biaya_asuransi_jaminan']
                    ],
                    'biaya_tabungan'                    => $query->so['ca']['recom_ca']['biaya_tabungan'],
                    'biaya_notaris'                     => $query->so['ca']['recom_ca']['notaris'],
                    'angsuran_pertama_bungan_berjalan'  => $query->so['ca']['recom_ca']['angs_pertama_bunga_berjalan'],
                    'pelunasan_nasabah_ro'              => $query->so['ca']['recom_ca']['pelunasan_nasabah_ro']
                ),

                'hold_dana' => $hold_dana = array(
                    'pelunasan_tempat_lain'         => $query->so['ca']['recom_ca']['pelunasan_tempat_lain'],
                    'blokir' => [
                        'tempat_lain'               => $query->so['ca']['recom_ca']['blokir_dana'],
                        'dua_kali_angsuran_kredit'  => $query->so['ca']['recom_ca']['blokir_angs_kredit']
                    ]
                ),

                'total' => array(
                    'biaya_reguler'     => $ttl1 = array_sum($reguler + $reguler['biaya_premi']),
                    'biaya_hold_dana'   => $ttl2 = array_sum($hold_dana + $hold_dana['blokir']),
                    'jml_total'         => $ttl1 + $ttl2
                )
            ],
            'lampiran' => [
                'file_report_mao'     => $query->file_report_mao,
                'file_report_mca'     => $query->file_report_mca,
                'file_agunan'         => explode(";", $query->file_agunan),
                'file_usaha'          => explode(";", $query->file_usaha),
                'file_tempat_tinggal' => $query->file_tempat_tinggal,
                'file_lain'           => explode(";", $query->file_lain)
            ],
            'status_caa' => $status_caa
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

    public function approve($id, Request $req){
        $user_id = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."'. Yang berhak melihat halaman ini adalah Direktur, CRM, PC dan AM. Mohon cek dimenu Team CAA untuk validasi data anda atau silahkan hubungin tim IT"
            ], 404);
        }

        $check = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->where('pic_team_caa', 'like', "%{$pic->id}%")->first();

        if ($check == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data yang akan anda eksekusi tidak ada, mohon cek URL anda"
            ], 404);
        }

        // $self = TransTCAA::where('id_trans_caa', $check->id)->where('tujuan_forward', 'like', "%{$pic->id}%")->get();

        // if($self != '[]'){
        //     if ($self->tujuan_forward) {

        //     }
        // }

        $id_area   = $pic->id_mk_area;
        $id_cabang = $pic->id_mk_cabang;

        $form = array(
            'user_id'       => $user_id,
            'id_trans_so'   => $id,
            'id_trans_caa'  => $check->id,
            'id_pic'        => $pic->id,
            'id_area'       => $id_area,
            'id_cabang'     => $id_cabang,
            'plafon'        => $req->input('plafon'),
            'tenor'         => $req->input('tenor'),
            'rincian'       => $req->input('rincian'),
            'status'        => $req->input('status'),
            'tujuan_forward'=> $req->input('tujuan_forward'),
            'tanggal'       => Carbon::now()->toDateTimeString()
        );

        $self_check = TransTCAA::where('id_trans_caa', $check->id)->where('id_pic', $pic->id)->first();

        DB::connection('web')->beginTransaction();

        try {

            if ($form['status'] == 'accept' || $form['status'] == 'reject') {
                $status = $form['status'].' by user '.$user_id;
                // TransCAA::where('id_trans_so', $id)->update(['status_team_caa' => $form['status'].' by user '.$user_id]);
            }elseif ($form['status'] == 'forward' || $form['status'] == 'return') {
                $status = $form['status'].' by picID '.$user_id.' to picID '.$form['tujuan_forward'];
            }

            if ($self_check == null) {

                TransTCAA::create($form);

            }else{

                TransTCAA::where('id', $self_check->id)->update($form);

            }

            TransCAA::where('id_trans_so', $id)->update(['status_team_caa' => $status]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk berhasil di - '.$form['status']
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
