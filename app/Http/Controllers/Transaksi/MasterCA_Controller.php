<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;
use App\Models\Pengajuan\CA\AsuransiJaminan;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiCA;
use App\Models\Pengajuan\CA\AsuransiJiwa;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\CA\MutasiBank;
use Illuminate\Support\Facades\File;
use App\Models\Pengajuan\CA\TabDebt;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class MasterCA_Controller extends BaseController
{
    public function index(Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CA). Harap daftarkan diri sebagai PIC(CA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1);
        $method = 'get';

        $query = Helper::checkDir($user_id, $jpic = $pic->jpic['nama_jenis'], $query_dir, $id_area, $id_cabang, $method);


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

            if ($val->so['ca']['status_ca'] == 1) {
                $status_ca = 'recommend';
            }elseif($val->so['ca']['status_ca'] == 2){
                $status_ca = 'not recommend';
            }else{
                $status_ca = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so,
                'id_trans_ca'    => $val->so['id_trans_ca'],
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->nomor_ao,
                // 'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => $val->so['faspin']['plafon'],
                'tenor'          => $val->so['faspin']['tenor'],
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->catatan_ao
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->so['ca']['catatan_ca']
                ],
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
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CA). Harap daftarkan diri sebagai PIC(CA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('id_trans_so', $id);
        $method = 'first';

        $val = Helper::checkDir($user_id, $jpic = $pic->jpic['nama_jenis'], $query_dir, $id_area, $id_cabang, $method);


        if (!$val) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $id_penj = explode (",", $val->so['id_penjamin']);

        foreach ($id_penj as $key => $value) {
            $idPen[$key] = array(
                'id' => $value
            );
        }


        $id_agu_ta = explode (",",$val->id_agunan_tanah);

        foreach ($id_agu_ta as $key => $value) {
            $idTan[$key] = array(
                'id' => $value
            );
        }


        $id_agu_ke = explode (",",$val->id_agunan_kendaraan);

        foreach ($id_agu_ke as $key => $value) {
            $idKen[$key] = array(
                'id' => $value
            );
        }


        $id_pe_agu_ta = explode (",",$val->id_periksa_agunan_tanah);

        foreach ($id_pe_agu_ta as $key => $value) {
            $idPeTan[$key] = array(
                'id' => $value
            );
        }


        $id_pe_agu_ke = explode (",",$val->id_periksa_agunan_kendaraan);

        foreach ($id_pe_agu_ke as $key => $value) {
            $idPeKen[$key] = array(
                'id' => $value
            );
        }


        if ($val->status_ao == 1) {
            $status_ao = 'recommend';
        }elseif($val->status_ao == 2){
            $status_ao = 'not recommend';
        }else{
            $status_ao = 'waiting';
        }

        if ($val->so['ca']['status_ca'] == 1) {
            $status_ca = 'recommend';
        }elseif($val->so['ca']['status_ca'] == 2){
            $status_ca = 'not recommend';
        }else{
            $status_ca = 'waiting';
        }


        $data[] = [
            'id_trans_so'    => $val->id_trans_so,
            'id_trans_ca'    => $val->so['id_trans_ca'],
            'nomor_so'       => $val->so['nomor_so'],
            'nomor_ao'       => $val->nomor_ao,
            // 'nomor_ca'       => $val->so['ca']['nomor_ca'],
            'nama_so'        => $val->so['nama_so'],
            'nama_marketing' => $val->so['nama_marketing'],
            'pic'  => [
                'id'         => $val->id_pic,
                'nama'       => $val->pic['nama'],
            ],
            'area'   => [
                'id'      => $val->id_area,
                'nama'    => $val->area['nama']
            ],
            'cabang' => [
                'id'      => $val->id_cabang,
                'nama'    => $val->cabang['nama'],
            ],

            'asaldata'  => [
                'id'   => $val->so['asaldata']['id'],
                'nama' => $val->so['asaldata']['nama']
            ],
            'fasilitas_pinjaman'  => [
                'id'              => $val->so['id_fasilitas_pinjaman']
            ],
            'data_debitur' => [
                'id'                    => $val->so['id_calon_debitur'],
                'nama_lengkap'          => $val->so['debt']['nama_lengkap'],
            ],
            'data_pasangan' => [
                'id'               => $val->so['id_pasangan'],
                'nama_lengkap'     => $val->so['pas']['nama_lengkap']
            ],
            'data_penjamin' => $idPen,
            'data_agunan' => [
                'agunan_tanah'     => $idTan,
                'agunan_kendaraan' => $idKen
            ],
            'pemeriksaan' => [
                'agunan_tanah' => $idPeTan,
                'agunan_kendaraan' => $idPeKen
            ],
            'kapasitas_bulanan' => ['id' => $val->id_kapasitas_bulanan],
            'pendapatan_usaha'  => ['id' => $val->id_pendapatan_usaha],
            'rekomendasi_ao'    => ['id' => $val->id_recom_ao],
            'status_ao'         => $status_ao,
            'status_ca'         => $status_ca
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

    public function update($id, Request $request, BlankRequest $req, Helper $help) {
        $user_id  = $request->auth->user_id;
        $username = $request->auth->user;

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

            $lastNumb = str_replace(" [revisi]","",$arr[4]) + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $PIC->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ca = $PIC->id_cabang.'-'.$JPIC->nama_jenis.'-'.$month.'-'.$year.'-'.$lastNumb;

        $check = TransSO::where('id',$id)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $transCA = array(
            'nomor_ca'    => $nomor_ca,
            'user_id'     => $user_id,
            'id_trans_so' => $id,
            'id_pic'      => $PIC->id,
            'id_area'     => $PIC->id_area,
            'id_cabang'   => $PIC->id_cabang,
            'catatan_ca'  => $req->input('catatan_ca'),
            'status_ca'   => empty($req->input('status_ca')) ? 1 : $req->input('status_ca'),
            'revisi'      => 'N'
        );

        // Mutasi Bank
        if (!empty($req->input('no_rekening_mutasi'))){
            for ($i = 0; $i < count($req->input('no_rekening_mutasi')); $i++) {
                $dataMuBa[] = array(
                    'urutan_mutasi'  => empty($req->input('urutan_mutasi')[$i]) ? null : $req->urutan_mutasi[$i],
                    'nama_bank'      => empty($req->input('nama_bank_mutasi')[$i]) ? null : $req->nama_bank_mutasi[$i],
                    'no_rekening'    => empty($req->input('no_rekening_mutasi')[$i]) ? null : $req->no_rekening_mutasi[$i],
                    'nama_pemilik'   => empty($req->input('nama_pemilik_mutasi')[$i]) ? null : $req->nama_pemilik_mutasi[$i],
                    'periode'        => empty($req->input('periode_mutasi')[$i]) ? null : implode(";", $req->periode_mutasi[$i]),
                    'frek_debet'     => empty($req->input('frek_debet_mutasi')[$i]) ? null : implode(";", $req->frek_debet_mutasi[$i]),
                    'nominal_debet'  => empty($req->input('nominal_debet_mutasi')[$i]) ? null : implode(";", $req->nominal_debet_mutasi[$i]),
                    'frek_kredit'    => empty($req->input('frek_kredit_mutasi')[$i]) ? null : implode(";", $req->frek_kredit_mutasi[$i]),
                    'nominal_kredit' => empty($req->input('nominal_kredit_mutasi')[$i]) ? null : implode(";", $req->nominal_kredit_mutasi[$i]),
                    'saldo'          => empty($req->input('saldo_mutasi')[$i]) ? null : implode(";", $req->saldo_mutasi[$i])
                );
            }
        }

        $dataTabUang = array(
            'no_rekening' => empty($req->input('no_rekening')) ? null : $req->input('no_rekening'),
            'nama_bank' => empty($req->input('nama_bank')) ? null : $req->input('nama_bank'),
            'tujuan_pembukaan_rek' => empty($req->input('tujuan_pembukaan_rek')) ? null : $req->input('tujuan_pembukaan_rek'),
            'penghasilan_per_tahun' => empty($req->input('penghasilan_per_tahun')) ? null : $req->input('penghasilan_per_tahun'),
            'sumber_penghasilan' => empty($req->input('sumber_penghasilan')) ? null : $req->input('sumber_penghasilan'),
            'pemasukan_per_bulan' => empty($req->input('pemasukan_per_bulan')) ? null : $req->input('pemasukan_per_bulan'),
            'frek_trans_pemasukan' => empty($req->input('frek_trans_pemasukan')) ? null : $req->input('frek_trans_pemasukan'),
            'pengeluaran_per_bulan' => empty($req->input('pengeluaran_per_bulan')) ? null : $req->input('pengeluaran_per_bulan'),
            'frek_trans_pengeluaran' => empty($req->input('frek_trans_pengeluaran')) ? null : $req->input('frek_trans_pengeluaran'),
            'sumber_dana_setoran' => empty($req->input('sumber_dana_setoran')) ? null : $req->input('sumber_dana_setoran'),
            'tujuan_pengeluaran_dana' => empty($req->input('tujuan_pengeluaran_dana')) ? null : $req->input('tujuan_pengeluaran_dana')
        );

        if (!empty($req->input('nama_bank_acc'))) {
            for ($i = 0; $i < count($req->input('nama_bank_acc')); $i++) {
                $dataACC[] = array(
                    'nama_bank' => empty($req->input('nama_bank_acc')[$i]) ? null : $req->nama_bank_acc[$i],
                    'plafon' => empty($req->input('plafon_acc')[$i]) ? null : $req->plafon_acc[$i],
                    'baki_debet' => empty($req->input('baki_debet_acc')[$i]) ? null : $req->baki_debet_acc[$i],
                    'angsuran' => empty($req->input('angsuran_acc')[$i]) ? null : $req->angsuran_acc[$i],
                    'collectabilitas' => empty($req->input('collectabilitas_acc')[$i]) ? null : $req->collectabilitas_acc[$i],
                    'jenis_kredit' => empty($req->input('jenis_kredit_acc')[$i]) ? null : $req->jenis_kredit_acc[$i]
                );
            }
        }


        // Rekomendasi CA
        $inputRecomCA = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => $req->input('plafon_kredit'), //45000000
            'jangka_waktu'          => $req->input('jangka_waktu'), // 48
            'suku_bunga'            => $req->input('suku_bunga'), // 1.70
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_asuransi_jiwa'   => $req->input('biaya_asuransi_jiwa'),
            'biaya_asuransi_jaminan'=> $req->input('biaya_asuransi_jaminan'),
            'notaris'               => $req->input('notaris'),
            'biaya_tabungan'        => $req->input('biaya_tabungan')
        );

        // Analisa Kuantitatif dan Kualitatif
        $staticMin = 35;
        $staticMax = 80;
        $staticPlafon = 70000000;
        $ltvMax = 70;
        $percent = 100;

        // Rekomendasi Angsuran pada table recom_ca
        $plaf  = $inputRecomCA['plafon_kredit'] == null ? 0 : $inputRecomCA['plafon_kredit'];
        $ten   = $inputRecomCA['jangka_waktu'] == null ? 0 : $inputRecomCA['jangka_waktu'];
        $bunga = $inputRecomCA['suku_bunga'] == null ? 0 : ($inputRecomCA['suku_bunga'] / $percent);
        $exec  = 0; //($plaf + ($plaf * $ten * $bunga)) / $ten;
        $recom_angs = $help->recom_angs($exec);

        $passRecomCA = array(
            'rekom_angsuran' => $recom_angs,
            'angs_pertama_bunga_berjalan' => empty($req->input('angs_pertama_bunga_berjalan')) ? null : $req->input('angs_pertama_bunga_berjalan'),
            'pelunasan_nasabah_ro' => empty($req->input('pelunasan_nasabah_ro')) ? null : $req->input('pelunasan_nasabah_ro'),
            'blokir_dana' => empty($req->input('blokir_dana')) ? null : $req->input('blokir_dana'),
            'pelunasan_tempat_lain' => empty($req->input('pelunasan_tempat_lain')) ? null : $req->input('pelunasan_tempat_lain'),
            'blokir_angs_kredit' => empty($req->input('blokir_angs_kredit')) ? null : $req->input('blokir_angs_kredit')
        );

        $recomCA = array_merge($inputRecomCA, $passRecomCA);


        $recom_pendapatan  = $check->ao['kapbul']['total_pemasukan'];
        $recom_pengeluaran = $check->ao['kapbul']['total_pengeluaran'];
        $recom_angsuran    = $check->ao['kapbul']['angsuran'];
        $recom_pend_bersih = $recom_pendapatan - $recom_pengeluaran;

        $disposable_income = $recom_pendapatan - $recom_pengeluaran - $recom_angs;


        $recom_ltv = ($plaf / $staticPlafon) * $percent;
        $recom_idir = ($recom_angs / ($recom_pendapatan - $recom_pengeluaran)) * $percent;
        $recom_dsr = ($recom_angs / ($recom_pendapatan - $recom_angsuran)) * $percent;


        if ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax && $recom_idir > 0 && $recom_idir < $staticMax) {

            $recom_hasil = 'LAYAK';

        }elseif ($recom_dsr <= $staticMin && $recom_ltv > $ltvMax) {

            $recom_hasil = 'DIPERTIMBANGKAN';

        }elseif ($recom_dsr > $staticMin && $recom_ltv <= $ltvMax) {

            $recom_hasil = 'DIPERTIMBANGKAN';

        }elseif ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax) {
            $recom_hasil = 'DIPERTIMBANGKAN';

        }else{
            $recom_hasil = 'RESIKO TINGGI';
        }

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $recom_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $recom_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $recom_pend_bersih,
            'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => round($recom_ltv, 2),
            'kuantitatif_dsr'               => round($recom_dsr, 2),
            'kuantitatif_idir'              => round($recom_idir, 2),
            'kuantitatif_hasil'             => $recom_hasil,


            'kualitatif_analisa'            => empty($req->input('kualitatif_analisa')) ? null : $req->input('kualitatif_analisa'),
            'kualitatif_strenght'           => empty($req->input('kualitatif_strenght')) ? null : $req->input('kualitatif_strenght'),
            'kualitatif_weakness'           => empty($req->input('kualitatif_weakness')) ? null : $req->input('kualitatif_weakness'),
            'kualitatif_opportunity'        => empty($req->input('kualitatif_opportunity')) ? null : $req->input('kualitatif_opportunity'),
            'kualitatif_threatness'         => empty($req->input('kualitatif_threatness')) ? null : $req->input('kualitatif_threatness'),
            'disposable_income'             => $disposable_income
        );

        $rekomPinjaman = array(
            'penyimpangan_struktur' => empty($req->input('penyimpangan_struktur')) ? null : $req->input('penyimpangan_struktur'),
            'penyimpangan_dokumen'  => empty($req->input('penyimpangan_dokumen')) ? null : $req->input('penyimpangan_dokumen'),

            'recom_nilai_pinjaman' => empty($req->input('recom_nilai_pinjaman')) ? null : $req->input('recom_nilai_pinjaman'),
            'recom_tenor' => empty($req->input('recom_tenor')) ? null : $req->input('recom_tenor'),
            'recom_angsuran' => empty($req->input('recom_angsuran')) ? null : $req->input('recom_angsuran'),
            'recom_produk_kredit' => empty($req->input('recom_produk_kredit')) ? null : $req->input('recom_produk_kredit'),
            'note_recom' => empty($req->input('note_recom')) ? null : $req->input('note_recom')
        );

        // Tambahan Rekomendasi CA
        $recomCaOL = array(
            'angs_pertama_bunga_berjalan' => $req->input('angs_pertama_bunga_berjalan'),
            'pelunasan_nasabah_ro'        => $req->input('pelunasan_nasabah_ro'),
            'blokir_dana'                 => $req->input('blokir_dana'),
            'pelunasan_tempat_lain'       => $req->input('pelunasan_tempat_lain'),
            'blokir_angs_kredit'          => $req->input('blokir_angs_kredit')
        );

        $asJiwa = array(
            'nama_asuransi'       => $req->input('nama_asuransi_jiwa'),
            'jangka_waktu'        => $req->input('jangka_waktu_as_jiwa'),
            'nilai_pertanggungan' => $req->input('nilai_pertanggungan_as_jiwa'),
            'jatuh_tempo'         => Carbon::parse($req->input('jatuh_tempo_as_jiwa'))->format('Y-m-d'),
            'berat_badan'         => $req->input('berat_badan_as_jiwa'),
            'tinggi_badan'        => $req->input('tinggi_badan_as_jiwa'),
            'umur_nasabah'        => $req->input('umur_nasabah_as_jiwa')
        );


        if ($req->input('jangka_waktu_as_jaminan')) {
            $asJaminan = array();
            for ($i = 0; $i < count($req->input('jangka_waktu_as_jaminan')); $i++) {

                $asJaminan[] = array(
                    'nama_asuransi'       => empty($req->input('nama_asuransi_jaminan')[$i]) ? 'null' : $req->input('nama_asuransi_jaminan')[$i],
                    'jangka_waktu'        => empty($req->input('jangka_waktu_as_jaminan')[$i]) ? 'null' : $req->input('jangka_waktu_as_jaminan')[$i],
                    'nilai_pertanggungan' => empty($req->input('nilai_pertanggungan_as_jaminan')[$i]) ? 'null' : $req->input('nilai_pertanggungan_as_jaminan')[$i],
                    'jatuh_tempo'         => empty($req->input('jatuh_tempo_as_jaminan')[$i]) ? 'null' : Carbon::parse($req->input('jatuh_tempo_as_jaminan')[$i])->format('Y-m-d')
                );
            }

            $jaminanImplode = array(
                'nama_asuransi'       => implode(";", array_column($asJaminan, 'nama_asuransi')),
                'jangka_waktu'        => implode(";", array_column($asJaminan, 'jangka_waktu')),
                'nilai_pertanggungan' => implode(";", array_column($asJaminan, 'nilai_pertanggungan')),
                'jatuh_tempo'         => implode(";", array_column($asJaminan, 'jatuh_tempo'))
            );
        }else{
            $jaminanImplode = array(
                'nama_asuransi'       => null,
                'jangka_waktu'        => null,
                'nilai_pertanggungan' => null,
                'jatuh_tempo'         => null
            );
        }




        $check_ca = TransCA::where('id_trans_so', $id)->first();

        try{
            DB::connection('web')->beginTransaction();

            if ($check_ca == null) {

                if (!empty($dataMuBa)) {
                    for ($i = 0; $i < count($dataMuBa); $i++) {
                        $mutasi = MutasiBank::create($dataMuBa[$i]);

                        $id_mutasi['id'][$i] = $mutasi->id;
                    }

                    $MutasiID   = implode(",", $id_mutasi['id']);
                }else{
                    $MutasiID = null;
                }

                if (!empty($dataTabUang)) {
                    $tabungan = TabDebt::create($dataTabUang);

                    $idTabungan = $tabungan->id;
                }else{
                    $idTabungan = null;
                }

                if (!empty($dataACC)) {
                    for ($i = 0; $i < count($dataACC); $i++) {
                        $IACC = InfoACC::create($dataACC[$i]);

                        $arrACC['id'][$i] = $IACC->id;
                    }

                    $idInfo = implode(",", $arrACC['id']);
                }else{
                    $idInfo = null;
                }

                if (!empty($dataRingkasan)) {
                    $analisa = RingkasanAnalisa::create($dataRingkasan);
                    $idAnalisa = $analisa->id;
                }else{
                    $idAnalisa = null;
                }

                if (!empty($rekomPinjaman)) {
                    $recomPin = RekomendasiPinjaman::create($rekomPinjaman);
                    $idrecomPin = $recomPin->id;
                }else{
                    $idrecomPin = null;
                }

                if (!empty($asJiwa)) {
                    $jiwa = AsuransiJiwa::create($asJiwa);
                    $idJiwa = $jiwa->id;
                }else{
                    $idJiwa = null;
                }

                if (!empty($asJaminan)) {
                    $jaminan = AsuransiJaminan::create($asJaminan);
                    $idJaminan = $jaminan->id;
                }else{
                    $idJaminan = null;
                }

                if (!empty($recomCA)) {
                    $newRecom = array_merge($recomCA, $recomCaOL);

                    $reCA = RekomendasiCA::create($newRecom);;
                    $idReCA = $reCA->id;
                }else{
                    $idReCA = null;
                }

                $dataID = array(
                    'id_mutasi_bank'          => $MutasiID,
                    'id_log_tabungan'         => $idTabungan,
                    'id_info_analisa_cc'      => $idInfo,
                    'id_ringkasan_analisa'    => $idAnalisa,
                    'id_recom_ca'             => $idReCA,
                    'id_rekomendasi_pinjaman' => $idrecomPin,
                    'id_asuransi_jiwa'        => $idJiwa,
                    'id_asuransi_jaminan'     => $idJaminan
                );

                $newTransCA = array_merge($transCA, $dataID);

                $CA = TransCA::create($newTransCA);
                TransSO::where('id', $id)->update(['id_trans_ca' => $CA->id]);
            }else{
                if (!empty($dataMuBa)) {

                    if (!empty($check_ca->id_mutasi_bank)) {
                        $ex_mutasi = explode(",", $check_ca->id_mutasi_bank);

                        MutasiBank::whereIn('id', $ex_mutasi)->delete();
                    }

                    for ($i = 0; $i < count($dataMuBa); $i++){
                        $mutasi = MutasiBank::create($dataMuBa[$i]);

                        $id_mutasi['id'][$i] = $mutasi->id;
                    }

                    $MutasiID   = implode(",", $id_mutasi['id']);
                }else{
                    $MutasiID = $check_ca->id_mutasi_bank;
                }


                if (!empty($check_ca->id_log_tabungan)) {
                    $tabungan = TabDebt::where('id', $check_ca->id_log_tabungan)->update($dataTabUang);

                    $idTabungan = $check_ca->id_log_tabungan;
                }else{
                    $tabungan = TabDebt::create($dataTabUang);

                    $idTabungan = $tabungan->id;
                }

                if (!empty($dataACC)) {

                    if (!empty($check_ca->id_info_analisa_cc)) {
                        $ex_info = explode(",", $check_ca->id_info_analisa_cc);
                        InfoACC::whereIn('id', $ex_info)->delete();
                    }

                    for ($i = 0; $i < count($dataACC); $i++) {
                        $IACC = InfoACC::create($dataACC[$i]);

                        $arrACC['id'][$i] = $IACC->id;
                    }

                    $idInfo = implode(",", $arrACC['id']);
                }else{
                    $idInfo = $check_ca->id_info_analisa_cc;
                }

                if (!empty($check_ca->id_ringkasan_analisa)) {
                    $analisa = RingkasanAnalisa::where('id', $check_ca->id_ringkasan_analisa)->update($dataRingkasan);
                    $idAnalisa = $check_ca->id_ringkasan_analisa;
                }else{
                    $analisa = RingkasanAnalisa::create($dataRingkasan);
                    $idAnalisa = $analisa->id;
                }

                if (!empty($check_ca->id_rekomendasi_pinjaman)) {
                    $recomPin = RekomendasiPinjaman::where('id', $check_ca->id_rekomendasi_pinjaman)->update($rekomPinjaman);
                    $idrecomPin = $check_ca->id_rekomendasi_pinjaman;
                }else{
                    $recomPin = RekomendasiPinjaman::create($rekomPinjaman);
                    $idrecomPin = $recomPin->id;
                }

                if (!empty($check_ca->id_asuransi_jiwa)) {
                    $jiwa = AsuransiJiwa::where('id', $check_ca->id_asuransi_jiwa)->update($asJiwa);
                    $idJiwa = $check_ca->id_asuransi_jiwa;
                }else{
                    $jiwa = AsuransiJiwa::create($asJiwa);
                    $idJiwa = $jiwa->id;
                }

                if (!empty($check_ca->id_asuransi_jaminan)) {
                    $jaminan = AsuransiJaminan::where('id', $check_ca->id_asuransi_jaminan)->update($asJaminan);
                    $idJaminan = $check_ca->id_asuransi_jaminan;
                }else{
                    $jaminan = AsuransiJaminan::create($asJaminan);
                    $idJaminan = $jaminan->id;
                }

                if (!empty($check_ca->id_recom_ca)) {
                    $newRecom = array_merge($recomCA, $recomCaOL);

                    $reCA = RekomendasiCA::where('id', $check_ca->id_recom_ca)->update($newRecom);
                    $idReCA = $check_ca->id_recom_ca;
                }else{
                    $newRecom = array_merge($recomCA, $recomCaOL);

                    $reCA = RekomendasiCA::create($newRecom);
                    $idReCA = $reCA->id;
                }

                $dataID = array(
                    'id_mutasi_bank'          => $MutasiID,
                    'id_log_tabungan'         => $idTabungan,
                    'id_info_analisa_cc'      => $idInfo,
                    'id_ringkasan_analisa'    => $idAnalisa,
                    'id_recom_ca'             => $idReCA,
                    'id_rekomendasi_pinjaman' => $idrecomPin,
                    'id_asuransi_jiwa'        => $idJiwa,
                    'id_asuransi_jaminan'     => $idJaminan
                );

                $newTransCA = array_merge($transCA, $dataID);

                TransCA::where('id', $check_ca->id)->update($newTransCA);
                TransSO::where('id', $id)->update(['id_trans_ca' => $check_ca->id]);
            }

            KapBulanan::where('id', $check->ao['id_kapasitas_bulanan'])->update(['disposable_income' => $disposable_income]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk CA berhasil dikirim'
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

    public function search($search, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CA). Harap daftarkan diri sebagai PIC(CA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;

        $query_dir = TransAO::with('so', 'pic', 'cabang')
                ->where('status_ao', 1)
                ->orWhere('nomor_ao', 'like', '%'.$search.'%');

        $method = 'get';

        $query = Helper::checkDir($user_id, $jpic = $pic->jpic['nama_jenis'], $query_dir, $id_area, $id_cabang, $method);

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
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->nomor_ao,
                // 'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
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

    public function revisi($id_trans_so, $id_trans_ca, Request $req) {
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

        $check = TransCA::where('id_trans_so', $id_trans_so)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $transCA = array(
            'nomor_ca'    => $check->nomor_ca . ' [revisi]',
            'user_id'     => $user_id,
            'id_trans_so' => $id_trans_so,
            'id_pic'      => $check->id_pic,
            'id_area'     => $check->id_area,
            'id_cabang'   => $check->id_cabang,
            'catatan_ca'  => $check->catatan_ca,
            'status_ca'   => $check->status_ca,
            'revisi'      => 'Y'
        );


        // Rekomendasi CA
        $inputRecomCA = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => $req->input('plafon_kredit'), //45000000
            'jangka_waktu'          => $req->input('jangka_waktu'), // 48
            'suku_bunga'            => $req->input('suku_bunga'), // 1.70
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_asuransi_jiwa'   => $req->input('biaya_asuransi_jiwa'),
            'biaya_asuransi_jaminan'=> $req->input('biaya_asuransi_jaminan'),
            'notaris'               => $req->input('notaris'),
            'biaya_tabungan'        => $req->input('biaya_tabungan')
        );

        // Analisa Kuantitatif dan Kualitatif
        $staticMin = 35;
        $staticMax = 80;
        $staticPlafon = 70000000;
        $ltvMax = 70;
        $percent = 100;

        // Rekomendasi Angsuran pada table recom_ca
        $plaf  = $check->recom_ca['plafon_kredit'];
        $ten   = $check->recom_ca['jangka_waktu'];
        $bunga = $check->recom_ca['suku_bunga'] / $percent;
        $exec  = ($plaf + ($plaf * $ten * $bunga)) / $ten;
        $recom_angs = Helper::recom_angs($exec);

        $passRecomCA = array(
            'rekom_angsuran' => $recom_angs,

            'angs_pertama_bunga_berjalan' => empty($req->input('angs_pertama_bunga_berjalan')) ? $check->recom_ca['angs_pertama_bunga_berjalan'] : $req->input('angs_pertama_bunga_berjalan'),

            'pelunasan_nasabah_ro' => empty($req->input('pelunasan_nasabah_ro')) ? $check->recom_ca['pelunasan_nasabah_ro'] : $req->input('pelunasan_nasabah_ro'),

            'blokir_dana' => empty($req->input('blokir_dana')) ? $check->recom_ca['blokir_dana'] : $req->input('blokir_dana'),

            'pelunasan_tempat_lain' => empty($req->input('pelunasan_tempat_lain')) ? $check->recom_ca['pelunasan_tempat_lain'] : $req->input('pelunasan_tempat_lain'),

            'blokir_angs_kredit' => empty($req->input('blokir_angs_kredit')) ? $check->recom_ca['blokir_angs_kredit'] : $req->input('blokir_angs_kredit')
        );

        $recomCA = array_merge($inputRecomCA, $passRecomCA);

        $recomCaOL = array(
            'angs_pertama_bunga_berjalan' => empty($req->input('angs_pertama_bunga_berjalan')) ? $check->recom_ca['angs_pertama_bunga_berjalan'] : $req->input('angs_pertama_bunga_berjalan'),
            'pelunasan_nasabah_ro'        => empty($req->input('pelunasan_nasabah_ro')) ? $check->recom_ca['pelunasan_nasabah_ro'] : $req->input('pelunasan_nasabah_ro'),
            'blokir_dana'                 => empty($req->input('blokir_dana')) ? $check->recom_ca['blokir_dana'] : $req->input('blokir_dana'),
            'pelunasan_tempat_lain'       => empty($req->input('pelunasan_tempat_lain')) ? $check->recom_ca['pelunasan_tempat_lain'] : $req->input('pelunasan_tempat_lain'),
            'blokir_angs_kredit'          => empty($req->input('blokir_angs_kredit')) ? $check->recom_ca['blokir_angs_kredit'] : $req->input('blokir_angs_kredit')
        );


        // $recom_pendapatan  = $check->ao['kapbul']['total_pemasukan'];
        $recom_pendapatan  = $check->so['ao']['kapbul']['total_pemasukan'];
        $recom_pengeluaran = $check->so['ao']['kapbul']['total_pengeluaran'];
        $recom_angsuran    = $check->so['ao']['kapbul']['angsuran'];
        $recom_pend_bersih = $recom_pendapatan - $recom_pengeluaran;

        $disposable_income = $recom_pendapatan - $recom_pengeluaran - $recom_angs;


        $recom_ltv = ($plaf / $staticPlafon) * $percent;
        $recom_idir = ($recom_angs / ($recom_pendapatan - $recom_pengeluaran)) * $percent;
        $recom_dsr = ($recom_angs / ($recom_pendapatan - $recom_angsuran)) * $percent;


        if ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax && $recom_idir > 0 && $recom_idir < $staticMax) {

            $recom_hasil = 'LAYAK';

        }elseif ($recom_dsr <= $staticMin && $recom_ltv > $ltvMax) {

            $recom_hasil = 'DIPERTIMBANGKAN';

        }elseif ($recom_dsr > $staticMin && $recom_ltv <= $ltvMax) {

            $recom_hasil = 'DIPERTIMBANGKAN';

        }elseif ($recom_dsr <= $staticMin && $recom_ltv <= $ltvMax) {
            $recom_hasil = 'DIPERTIMBANGKAN';

        }else{
            $recom_hasil = 'RESIKO TINGGI';
        }

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $recom_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $recom_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $recom_pend_bersih,
            'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => round($recom_ltv, 2),
            'kuantitatif_dsr'               => round($recom_dsr, 2),
            'kuantitatif_idir'              => round($recom_idir, 2),
            'kuantitatif_hasil'             => $recom_hasil,


            'kualitatif_analisa'            => empty($req->input('kualitatif_analisa')) ? $check->recom_ca['kualitatif_analisa'] : $req->input('kualitatif_analisa'),
            'kualitatif_strenght'           => empty($req->input('kualitatif_strenght')) ? $check->recom_ca['kualitatif_strenght'] : $req->input('kualitatif_strenght'),
            'kualitatif_weakness'           => empty($req->input('kualitatif_weakness')) ? $check->recom_ca['kualitatif_weakness'] : $req->input('kualitatif_weakness'),
            'kualitatif_opportunity'        => empty($req->input('kualitatif_opportunity')) ? $check->recom_ca['kualitatif_opportunity'] : $req->input('kualitatif_opportunity'),
            'kualitatif_threatness'         => empty($req->input('kualitatif_threatness')) ? $check->recom_ca['kualitatif_threatness'] : $req->input('kualitatif_threatness'),
            'disposable_income'             => $disposable_income
        );

        // try{
            DB::connection('web')->beginTransaction();

            if (!empty($dataRingkasan)) {
                $analisa = RingkasanAnalisa::create($dataRingkasan);
                $idAnalisa = $analisa->id;
            }else{
                $idAnalisa = null;
            }


            if (!empty($recomCA)) {
                $newRecom = array_merge($recomCA, $recomCaOL);

                $reCA = RekomendasiCA::create($newRecom);
                $idReCA = $reCA->id;
            }else{
                $idReCA = null;
            }

            $dataID = array(
                'id_mutasi_bank'          => $check->id_mutasi_bank,
                'id_log_tabungan'         => $check->id_log_tabungan,
                'id_info_analisa_cc'      => $check->id_info_analisa_cc,
                'id_ringkasan_analisa'    => $idAnalisa,
                'id_recom_ca'             => $idReCA,
                'id_rekomendasi_pinjaman' => $check->id_rekomendasi_pinjaman,
                'id_asuransi_jiwa'        => $check->id_asuransi_jiwa,
                'id_asuransi_jaminan'     => $check->id_asuransi_jaminan
            );

            $newTransCA = array_merge($transCA, $dataID);

            $CA = TransCA::create($newTransCA);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk Revisi CA berhasil dikirim'
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
}
