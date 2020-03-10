<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;

use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
// use App\Models\Pengajuan\AO\PemeriksaanAgunKen;

use App\Models\Pengajuan\CA\AsuransiJaminan;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Http\Controllers\Controller as Helper;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiCA;
use App\Models\Pengajuan\CA\AsuransiJiwa;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Pengajuan\CA\MutasiBank;
use App\Models\Pengajuan\CA\TabDebt;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
// use Image;
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
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di AO masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {

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
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'id_trans_ca'    => $val->so['id_trans_ca'] == null ? null : (int) $val->so['id_trans_ca'],
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
                'tgl_transaksi' => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
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

    public function indexWait($ao_ca, $status, Request $req){
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
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)->orderBy('created_at', 'desc');

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data di AO masih kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {

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
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'id_trans_ca'    => $val->so['id_trans_ca'] == null ? null : (int) $val->so['id_trans_ca'],
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->nomor_ao,
                // 'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'pic'            => $val->pic['nama'],
                'area'           => $val->area['nama'],
                'cabang'         => $val->cabang['nama'],
                'asal_data'      => $val->so['asaldata']['nama'],
                'nama_marketing' => $val->so['nama_marketing'],
                'nama_debitur'   => $val->so['debt']['nama_lengkap'],
                'plafon'         => (int) $val->so['faspin']['plafon'],
                'tenor'          => (int) $val->so['faspin']['tenor'],
                "ao" => [
                    'status_ao'     => $status_ao,
                    'catatan_ao'    => $val->catatan_ao
                ],
                "ca" => [
                    'status_ca'     => $status_ca,
                    'catatan_ca'    => $val->so['ca']['catatan_ca']
                ],
                'tgl_transaksi' => $val->created_at
            ];
        }

        $res = array_filter($data, function ($item) use ($ao_ca, $status) {
            if (stripos($item[$ao_ca]["status_{$ao_ca}"], $status) !== false) {
                return true;
            }
            return false;
        });
        
        try {
            if($res == false){
                return response()->json([
                    'code'   => 404,
                    'status' => 'not found',
                    'count'  => 0,
                    'message'=> 'data tidak ditemukan'
                ], 404);
            }else{
                foreach($res as $val){$result[] = $val;}
                return response()->json([
                    'code'   => 200,
                    'status' => 'success',
                    'count'  => sizeof($result),
                    'data'   => $result
                ], 200);
            }
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
        $scope     = $pic->jpic['cakupan'];

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $query_dir = TransAO::with('so', 'pic', 'cabang')->where('id_trans_so', $id);

        $vals = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);
        $val = $vals->first();

        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke AO'
            ], 404);
        }

        $id_penj = explode (",", $val->so['id_penjamin']);

        foreach ($id_penj as $key => $value) {
            $idPen[$key] = array(
                'id' => $value == null ? null : (int) $value
            );
        }


        $id_agu_ta = explode (",",$val->id_agunan_tanah);

        foreach ($id_agu_ta as $key => $value) {
            $idTan[$key] = array(
                'id' => $value == null ? null : (int) $value
            );
        }


        $id_agu_ke = explode (",",$val->id_agunan_kendaraan);

        foreach ($id_agu_ke as $key => $value) {
            $idKen[$key] = array(
                'id' => $value == null ? null : (int) $value
            );
        }


        $id_pe_agu_ta = explode (",",$val->id_periksa_agunan_tanah);

        foreach ($id_pe_agu_ta as $key => $value) {
            $idPeTan[$key] = array(
                'id' => $value == null ? null : (int) $value
            );
        }


        $id_pe_agu_ke = explode (",",$val->id_periksa_agunan_kendaraan);

        foreach ($id_pe_agu_ke as $key => $value) {
            $idPeKen[$key] = array(
                'id' => $value == null ? null : (int) $value
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


        $data = array(
            'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
            'id_trans_ca'    => $val->so['id_trans_ca'] == null ? null : (int) $val->so['id_trans_ca'],
            'nomor_so'       => $val->so['nomor_so'],
            'nomor_ao'       => $val->nomor_ao,
            // 'nomor_ca'       => $val->so['ca']['nomor_ca'],
            'nama_so'        => $val->so['nama_so'],
            'nama_marketing' => $val->so['nama_marketing'],
            'pic'  => [
                'id'         => $val->id_pic == null ? null : (int) $val->id_pic,
                'nama'       => $val->pic['nama'],
            ],
            'area'   => [
                'id'      => $val->id_area == null ? null : (int) $val->id_area,
                'nama'    => $val->area['nama']
            ],
            'cabang' => [
                'id'      => $val->id_cabang == null ? null : (int) $val->id_cabang,
                'nama'    => $val->cabang['nama'],
            ],

            'asaldata'  => [
                'id'   => $val->so['id_asal_data'] == null ? null : (int) $val->so['id_asal_data'],
                'nama' => $val->so['asaldata']['nama']
            ],
            'fasilitas_pinjaman'  => [
                'id'   => $val->so['id_fasilitas_pinjaman'] == null ? null : (int) $val->so['id_fasilitas_pinjaman']
            ],
            'data_debitur' => [
                'id'                 => $val->so['id_calon_debitur'] == null ? null : (int) $val->so['id_calon_debitur'],
                'nama_lengkap'       => $val->so['debt']['nama_lengkap'],
                'foto_agunan_rumah'  => $val->so['debt']['foto_agunan_rumah']
            ],
            'data_pasangan' => [
                'id'           => $val->so['id_pasangan'] == null ? null : (int) $val->so['id_pasangan'],
                'nama_lengkap' => $val->so['pas']['nama_lengkap']
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
            'kapasitas_bulanan' => ['id' => $val->id_kapasitas_bulanan == null ? null : (int) $val->id_kapasitas_bulanan],
            'pendapatan_usaha'  => ['id' => $val->id_pendapatan_usaha  == null ? null : (int) $val->id_pendapatan_usaha],
            'rekomendasi_ao'    => ['id' => $val->id_recom_ao          == null ? null : (int) $val->id_recom_ao],
            'status_ao'         => $status_ao,
            'status_ca'         => $status_ca,
            'tgl_transaksi'     => $val->created_at
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

    public function update($id, Request $request, BlankRequest $req) {
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

        $check_so = TransSO::where('id',$id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum ada di SO atau belum komplit saat pemeriksaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so',$id)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->first();

        if ($check_ca != null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' sudah ada di CA'
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
            'status_ca'   => empty($req->input('status_ca')) ? 1 : $req->input('status_ca')
        );

        // Pendapatan Usaha Cadebt
        $dataPendapatanUsaha = array(
            'pemasukan_tunai'      => empty($req->input('pemasukan_tunai')) ? 0 : $req->input('pemasukan_tunai'),
            'pemasukan_kredit'     => empty($req->input('pemasukan_kredit')) ? 0 : $req->input('pemasukan_kredit'),
            'biaya_sewa'           => empty($req->input('biaya_sewa')) ? 0 : $req->input('biaya_sewa'),
            'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai')) ? 0 : $req->input('biaya_gaji_pegawai'),
            'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg')) ? 0 : $req->input('biaya_belanja_brg'),
            'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air'),
            'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? 0 : $req->input('biaya_sampah_kemanan'),
            'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang')) ? 0 : $req->input('biaya_kirim_barang'),
            'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? 0 : $req->input('biaya_hutang_dagang'),
            'biaya_angsuran'       => empty($req->input('biaya_angsuran')) ? 0 : $req->input('biaya_angsuran'),
            'biaya_lain_lain'      => empty($req->input('biaya_lain_lain')) ? 0 : $req->input('biaya_lain_lain')
        );

        $totalPendapatan = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($dataPendapatanUsaha, 0, 2)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($dataPendapatanUsaha, 2)),
            'laba_usaha'         => $ttl1 - $ttl2
        );

        $Pendapatan = array_merge($dataPendapatanUsaha, $totalPendapatan, array('ao_ca' => 'CA'));

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_cadebt'
                => empty($req->input('pemasukan_debitur'))    ? 0 : $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
                => empty($req->input('pemasukan_pasangan'))   ? 0 : $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
                => empty($req->input('pemasukan_penjamin'))   ? 0 : $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
                => empty($req->input('biaya_rumah_tangga'))   ? 0 : $req->input('biaya_rumah_tangga'),

            'biaya_transport'
                => empty($req->input('biaya_transport'))      ? 0 : $req->input('biaya_transport'),

            'biaya_pendidikan'
                => empty($req->input('biaya_pendidikan'))     ? 0 : $req->input('biaya_pendidikan'),

            'biaya_telp_listr_air'
                => empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air'),

            'angsuran'
                => empty($req->input('angsuran'))             ? 0 : $req->input('angsuran'),

            'biaya_lain'
                => empty($req->input('biaya_lain'))           ? 0 : $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 2)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        // Mutasi Bank
        if (!empty($req->input('no_rekening_mutasi'))){

            for ($i = 0; $i < count($req->input('no_rekening_mutasi')); $i++) {

                $dataMuBa[] = array(
                    'urutan_mutasi'
                        => empty($req->input('urutan_mutasi')[$i])
                        ? null : $req->urutan_mutasi[$i],

                    'nama_bank'
                        => empty($req->input('nama_bank_mutasi')[$i])
                        ? null : $req->nama_bank_mutasi[$i],

                    'no_rekening'
                        => empty($req->input('no_rekening_mutasi')[$i])
                        ? null : $req->no_rekening_mutasi[$i],

                    'nama_pemilik'
                        => empty($req->input('nama_pemilik_mutasi')[$i])
                        ? null : $req->nama_pemilik_mutasi[$i],

                    'periode'
                        => empty($req->input('periode_mutasi')[$i])
                        ? null : implode(";", $req->periode_mutasi[$i]),

                    'frek_debet'
                        => empty($req->input('frek_debet_mutasi')[$i])
                        ? null : implode(";", $req->frek_debet_mutasi[$i]),

                    'nominal_debet'
                        => empty($req->input('nominal_debet_mutasi')[$i])
                        ? null : implode(";", $req->nominal_debet_mutasi[$i]),

                    'frek_kredit'
                        => empty($req->input('frek_kredit_mutasi')[$i])
                        ? null : implode(";", $req->frek_kredit_mutasi[$i]),

                    'nominal_kredit'
                        => empty($req->input('nominal_kredit_mutasi')[$i])
                        ? null : implode(";", $req->nominal_kredit_mutasi[$i]),

                    'saldo'
                        => empty($req->input('saldo_mutasi')[$i])
                        ? null : implode(";", $req->saldo_mutasi[$i])
                );
            }
        }

        if (!empty($req->input('nama_bank_acc'))) {
            for ($i = 0; $i < count($req->input('nama_bank_acc')); $i++) {
                $dataACC[] = array(
                    'nama_bank'       => empty($req->input('nama_bank_acc')[$i])       ? null : $req->nama_bank_acc[$i],
                    'plafon'          => empty($req->input('plafon_acc')[$i])          ? null : $req->plafon_acc[$i],
                    'baki_debet'      => empty($req->input('baki_debet_acc')[$i])      ? null : $req->baki_debet_acc[$i],
                    'angsuran'        => empty($req->input('angsuran_acc')[$i])        ? null : $req->angsuran_acc[$i],
                    'collectabilitas' => empty($req->input('collectabilitas_acc')[$i]) ? null : $req->collectabilitas_acc[$i],
                    'jenis_kredit'    => empty($req->input('jenis_kredit_acc')[$i])    ? null : $req->jenis_kredit_acc[$i]
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

        // Rekomendasi Angsuran pada table recom_ca
        $plafonCA = $inputRecomCA['plafon_kredit'] == null ? 0 : $inputRecomCA['plafon_kredit'];
        $tenorCA  = $inputRecomCA['jangka_waktu']  == null ? 0 : $inputRecomCA['jangka_waktu'];
        $bunga    = $inputRecomCA['suku_bunga']    == null ? 0 : ($inputRecomCA['suku_bunga'] / 100);

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        }else{
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }

        $passRecomCA = array(

            'rekom_angsuran' => $recom_angs,

            'angs_pertama_bunga_berjalan'
                => empty($req->input('angs_pertama_bunga_berjalan')) ? null : $req->input('angs_pertama_bunga_berjalan'),

            'pelunasan_nasabah_ro'
                => empty($req->input('pelunasan_nasabah_ro'))        ? null : $req->input('pelunasan_nasabah_ro'),

            'blokir_dana'
                => empty($req->input('blokir_dana'))                 ? null : $req->input('blokir_dana'),

            'pelunasan_tempat_lain'
                => empty($req->input('pelunasan_tempat_lain'))       ? null : $req->input('pelunasan_tempat_lain'),

            'blokir_angs_kredit'
                => empty($req->input('blokir_angs_kredit'))          ? null : $req->input('blokir_angs_kredit')
        );

        $recomCA = array_merge($inputRecomCA, $passRecomCA);


        $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];
        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // End Kapasitas Bulanan

        // Check Pemeriksaan
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if ($id_pe_ta == null) {
            $PeriksaTanah = null;
        }

        $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        if ($id_pe_ke == null) {
            $PeriksaKenda = null;
        }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if ($PeriksaTanah == []) {
            $sumTaksasiTan = 0;
        }else{
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan


        $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $recom_ltv,
            'kuantitatif_dsr'               => $recom_dsr,
            'kuantitatif_idir'              => $recom_idir,
            'kuantitatif_hasil'             => $recom_hasil,


            'kualitatif_analisa'
                => empty($req->input('kualitatif_analisa'))
                ? null : $req->input('kualitatif_analisa'),

            'kualitatif_strenght'
                => empty($req->input('kualitatif_strenght'))
                ? null : $req->input('kualitatif_strenght'),

            'kualitatif_weakness'
                => empty($req->input('kualitatif_weakness'))
                ? null : $req->input('kualitatif_weakness'),

            'kualitatif_opportunity'
                => empty($req->input('kualitatif_opportunity'))
                ? null : $req->input('kualitatif_opportunity'),

            'kualitatif_threatness'
                => empty($req->input('kualitatif_threatness'))
                ? null : $req->input('kualitatif_threatness'),
        );

        $rekomPinjaman = array(
            'penyimpangan_struktur'
                => empty($req->input('penyimpangan_struktur'))
                ? null : $req->input('penyimpangan_struktur'),

            'penyimpangan_dokumen'
                => empty($req->input('penyimpangan_dokumen'))
                ? null : $req->input('penyimpangan_dokumen'),

            'recom_nilai_pinjaman'
                => empty($req->input('recom_nilai_pinjaman'))
                ? null : $req->input('recom_nilai_pinjaman'),

            'recom_tenor'
                => empty($req->input('recom_tenor'))
                ? null : $req->input('recom_tenor'),

            'recom_angsuran'
                => empty($req->input('recom_angsuran'))
                ? null : $req->input('recom_angsuran'),

            'recom_produk_kredit'
                => empty($req->input('recom_produk_kredit'))
                ? null : $req->input('recom_produk_kredit'),

            'note_recom'
                => empty($req->input('note_recom'))
                ? null : $req->input('note_recom')
        );

        $dataTabUang = array(

            'no_rekening'
                => empty($req->input('no_rekening'))
                ? null : $req->input('no_rekening'),

            'nama_bank'
                => empty($req->input('nama_bank'))
                ? null : $req->input('nama_bank'),

            'tujuan_pembukaan_rek'
                => empty($req->input('tujuan_pembukaan_rek'))
                ? null : $req->input('tujuan_pembukaan_rek'),

            'penghasilan_per_tahun'
                => empty($req->input('penghasilan_per_tahun'))
                ? ($rekomen_pendapatan == 0 ? 0 : $rekomen_pendapatan * 12) : $req->input('penghasilan_per_tahun'),

            'sumber_penghasilan'
                => empty($req->input('sumber_penghasilan'))
                ? null : $req->input('sumber_penghasilan'),

            'pemasukan_per_bulan'
                => empty($req->input('pemasukan_per_bulan'))
                ? null : $req->input('pemasukan_per_bulan'),

            'frek_trans_pemasukan'
                => empty($req->input('frek_trans_pemasukan'))
                ? null : $req->input('frek_trans_pemasukan'),

            'pengeluaran_per_bulan'
                => empty($req->input('pengeluaran_per_bulan'))
                ? null : $req->input('pengeluaran_per_bulan'),

            'frek_trans_pengeluaran'
                => empty($req->input('frek_trans_pengeluaran'))
                ? null : $req->input('frek_trans_pengeluaran'),

            'sumber_dana_setoran'
                => empty($req->input('sumber_dana_setoran'))
                ? null : $req->input('sumber_dana_setoran'),

            'tujuan_pengeluaran_dana'
                => empty($req->input('tujuan_pengeluaran_dana'))
                ? null : $req->input('tujuan_pengeluaran_dana')
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
            'jatuh_tempo'         => empty($req->input('jatuh_tempo_as_jiwa')) ? null : Carbon::parse($req->input('jatuh_tempo_as_jiwa'))->format('Y-m-d'),
            'berat_badan'         => $req->input('berat_badan_as_jiwa'),
            'tinggi_badan'        => $req->input('tinggi_badan_as_jiwa'),
            'umur_nasabah'        => $req->input('umur_nasabah_as_jiwa')
        );


        if (!empty(  $req->input('jangka_waktu_as_jaminan'))) {

            $asJaminan = array();
            for ($i = 0; $i < count($req->input('jangka_waktu_as_jaminan')); $i++) {

                $asJaminan[] = array(
                    'nama_asuransi'
                        => empty($req->input('nama_asuransi_jaminan')[$i])
                        ? null : $req->nama_asuransi_jaminan[$i],

                    'jangka_waktu'
                        => empty($req->input('jangka_waktu_as_jaminan')[$i])
                        ? null : $req->jangka_waktu_as_jaminan[$i],

                    'nilai_pertanggungan'
                        => empty($req->input('nilai_pertanggungan_as_jaminan')[$i])
                        ? null : $req->nilai_pertanggungan_as_jaminan[$i],

                    'jatuh_tempo'
                        => empty($req->input('jatuh_tempo_as_jaminan')[$i])
                        ? null : Carbon::parse($req->jatuh_tempo_as_jaminan[$i])->format('Y-m-d')
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

        try{
            DB::connection('web')->beginTransaction();

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

            if (!empty($jaminanImplode)) {
                $jaminan = AsuransiJaminan::create($jaminanImplode);
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

            if (!empty($Pendapatan)) {
                $pend = PendapatanUsaha::create($Pendapatan);
                $idPendUs = $pend->id;
            }else{
                $idPendUs = null;
            }

            if (!empty($kapBul)) {
                $Q_Kapbul = KapBulanan::create($kapBul);
                $idKapBul = $Q_Kapbul->id;
            }else{
                $idKapBul = null;
            }

            $dataID = array(
                'id_mutasi_bank'          => $MutasiID,
                'id_log_tabungan'         => $idTabungan,
                'id_info_analisa_cc'      => $idInfo,
                'id_ringkasan_analisa'    => $idAnalisa,
                'id_recom_ca'             => $idReCA,
                'id_rekomendasi_pinjaman' => $idrecomPin,
                'id_asuransi_jiwa'        => $idJiwa,
                'id_asuransi_jaminan'     => $idJaminan,
                'id_kapasitas_bulanan'    => $idKapBul,
                'id_pendapatan_usaha'     => $idPendUs
            );

            
            $newTransCA = array_merge($transCA, $dataID);

            $CA = TransCA::create($newTransCA);
            TransSO::where('id', $id)->update(['id_trans_ca' => $CA->id]);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk CA berhasil dikirim',
                'data'   => $CA
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

    public function search($param, $key, $value, $status, $orderVal, $orderBy, $limit, Request $req)
    {
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CA). Harap daftarkan diri sebagai PIC(CA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $column = array(
            'id', 'nomor_ao', 'id_trans_so', 'user_id', 'id_pic', 'id_area', 'id_cabang', 'id_validasi', 'id_verifikasi', 'id_agunan_tanah', 'id_agunan_kendaraan', 'id_periksa_agunan_tanah', 'id_periksa_agunan_kendaraan', 'id_kapasitas_bulanan', 'id_pendapatan_usaha', 'id_recom_ao', 'catatan_ao', 'status_ao', 'form_persetujuan_ideb'
        );

        if($param != 'filter' && $param != 'search'){
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan parameter yang valid diantara berikut: filter, search'
            ], 412);
        }

        if (in_array($key, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan key yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if (in_array($orderBy, $column) == false)
        {
            return response()->json([
                'code'    => 412,
                'status'  => 'not valid',
                'message' => 'gunakan order by yang valid diantara berikut: '.implode(",", $column)
            ], 412);
        }

        if($param == 'search'){
            $operator   = "like";
            $func_value = "%{$value}%";
        }else{
            $operator   = "=";
            $func_value = "{$value}";
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query_dir = TransAO::with('so', 'pic', 'cabang')
            ->where('status_ao', 1)
            ->where('flg_aktif', $status)
            ->orderBy($orderBy, $orderVal);
        
        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if($value == 'default'){
            $res = $query;
        }else{
            $res = $query->where($key, $operator, $func_value);
        }

        if($limit == 'default'){
            $result = $res;
        }else{
            $result = $res->limit($limit);
        }

        if ($result->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }


        foreach ($result->get() as $key => $val) {

            if ($val->status_ao == 1) {
                $status_ao = 'recommend';
            }elseif($val->status_ao == 2){
                $status_ao = 'not recommend';
            }else{
                $status_ao = 'waiting';
            }

            $data[$key] = [
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
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
                'status_ao'      => $status_ao,
                'tgl_transaksi'  => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
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

        $check_so = TransSO::where('id', $id_trans_so)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id_trans_so.' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id_trans_so)->where('status_ao', 1)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id_trans_so.' belum sampai ke AO'
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id_trans_so)->where('status_ca', 1)->first();

        if (!$check_ca) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id_trans_so.' belum sampai ke CA'
            ], 404);
        }

        $transCA = array(
            'nomor_ca'    => $check_ca->nomor_ca . ' [revisi]',
            'user_id'     => $user_id,
            'id_trans_so' => $id_trans_so,
            'id_pic'      => $check_ca->id_pic,
            'id_area'     => $check_ca->id_area,
            'id_cabang'   => $check_ca->id_cabang,
            'catatan_ca'  => $check_ca->catatan_ca,
            'status_ca'   => $check_ca->status_ca,
            'revisi'      => $check_ca->id
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

        // Rekomendasi Angsuran pada table recom_ca
        $plafonCA = $inputRecomCA['plafon_kredit'] == null ? 0 : $inputRecomCA['plafon_kredit'];
        $tenorCA  = $inputRecomCA['jangka_waktu']  == null ? 0 : $inputRecomCA['jangka_waktu'];
        $bunga    = $inputRecomCA['suku_bunga']    == null ? 0 : ($inputRecomCA['suku_bunga'] / 100);

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        }else{
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }


        $passRecomCA = array(
            'rekom_angsuran' => $recom_angs,

            'angs_pertama_bunga_berjalan'
                => empty($req->input('angs_pertama_bunga_berjalan'))
                ? $check_ca->recom_ca['angs_pertama_bunga_berjalan']
                : $req->input('angs_pertama_bunga_berjalan'),

            'pelunasan_nasabah_ro'
                => empty($req->input('pelunasan_nasabah_ro'))
                ? $check_ca->recom_ca['pelunasan_nasabah_ro']
                : $req->input('pelunasan_nasabah_ro'),

            'blokir_dana'
                => empty($req->input('blokir_dana'))
                ? $check_ca->recom_ca['blokir_dana']
                : $req->input('blokir_dana'),

            'pelunasan_tempat_lain'
                => empty($req->input('pelunasan_tempat_lain'))
                ? $check_ca->recom_ca['pelunasan_tempat_lain']
                : $req->input('pelunasan_tempat_lain'),

            'blokir_angs_kredit'
                => empty($req->input('blokir_angs_kredit'))
                ? $check_ca->recom_ca['blokir_angs_kredit']
                : $req->input('blokir_angs_kredit')
        );


        $recomCA = array_merge($inputRecomCA, $passRecomCA);

        $recomCaOL = array(
            'angs_pertama_bunga_berjalan'
                => empty($req->input('angs_pertama_bunga_berjalan'))
                ? $check_ca->recom_ca['angs_pertama_bunga_berjalan']
                : $req->input('angs_pertama_bunga_berjalan'),

            'pelunasan_nasabah_ro'
                => empty($req->input('pelunasan_nasabah_ro'))
                ? $check_ca->recom_ca['pelunasan_nasabah_ro']
                : $req->input('pelunasan_nasabah_ro'),

            'blokir_dana'
                => empty($req->input('blokir_dana'))
                ? $check_ca->recom_ca['blokir_dana']
                : $req->input('blokir_dana'),

            'pelunasan_tempat_lain'
                => empty($req->input('pelunasan_tempat_lain'))
                ? $check_ca->recom_ca['pelunasan_tempat_lain']
                : $req->input('pelunasan_tempat_lain'),

            'blokir_angs_kredit'
                => empty($req->input('blokir_angs_kredit'))
                ? $check_ca->recom_ca['blokir_angs_kredit']
                : $req->input('blokir_angs_kredit')
        );

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_cadebt'
                => empty($req->input('pemasukan_debitur'))    ? null : (int) $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
                => empty($req->input('pemasukan_pasangan'))   ? null : (int) $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
                => empty($req->input('pemasukan_penjamin'))   ? null : (int) $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
                => empty($req->input('biaya_rumah_tangga'))   ? null : (int) $req->input('biaya_rumah_tangga'),

            'biaya_transport'
                => empty($req->input('biaya_transport'))      ? null : (int) $req->input('biaya_transport'),

            'biaya_pendidikan'
                => empty($req->input('biaya_pendidikan'))     ? null : (int) $req->input('biaya_pendidikan'),

            'biaya_telp_listr_air'
                => empty($req->input('biaya_telp_listr_air')) ? null : (int) $req->input('biaya_telp_listr_air'),

            'angsuran'
                => empty($req->input('angsuran'))             ? null : (int) $req->input('angsuran'),

            'biaya_lain'
                => empty($req->input('biaya_lain'))           ? null : (int) $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 2)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 2)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];
        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // End Kapasitas Bulanan

        // Check Pemeriksaan
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if ($id_pe_ta == null) {
            $PeriksaTanah = null;
        }

        $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        if ($id_pe_ke == null) {
            $PeriksaKenda = null;
        }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if ($PeriksaTanah == []) {
            $sumTaksasiTan = 0;
        }else{
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan


        $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $recom_ltv,
            'kuantitatif_dsr'               => $recom_dsr,
            'kuantitatif_idir'              => $recom_idir,
            'kuantitatif_hasil'             => $recom_hasil,


            'kualitatif_analisa'
                => empty($req->input('kualitatif_analisa'))
                ? $check_ca->recom_ca['kualitatif_analisa']
                : $req->input('kualitatif_analisa'),

            'kualitatif_strenght'
                => empty($req->input('kualitatif_strenght'))
                ? $check_ca->recom_ca['kualitatif_strenght']
                : $req->input('kualitatif_strenght'),

            'kualitatif_weakness'
                => empty($req->input('kualitatif_weakness'))
                ? $check_ca->recom_ca['kualitatif_weakness']
                : $req->input('kualitatif_weakness'),

            'kualitatif_opportunity'
                => empty($req->input('kualitatif_opportunity'))
                ? $check_ca->recom_ca['kualitatif_opportunity']
                : $req->input('kualitatif_opportunity'),

            'kualitatif_threatness'
                => empty($req->input('kualitatif_threatness'))
                ? $check_ca->recom_ca['kualitatif_threatness']
                : $req->input('kualitatif_threatness')
        );

        try{
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
                'id_mutasi_bank'          => $check_ca->id_mutasi_bank,
                'id_log_tabungan'         => $check_ca->id_log_tabungan,
                'id_info_analisa_cc'      => $check_ca->id_info_analisa_cc,
                'id_ringkasan_analisa'    => $idAnalisa,
                'id_recom_ca'             => $idReCA,
                'id_rekomendasi_pinjaman' => $check_ca->id_rekomendasi_pinjaman,
                'id_asuransi_jiwa'        => $check_ca->id_asuransi_jiwa,
                'id_asuransi_jaminan'     => $check_ca->id_asuransi_jaminan
            );

            $newTransCA = array_merge($transCA, $dataID);

            $CA = TransCA::create($newTransCA);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk Revisi CA berhasil dikirim'
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





    // Sample
    public function operator($id_trans_so, Request $req, Helper $help) {

        $check = TransSO::where('id',$id_trans_so)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id_trans_so.' belum ada di SO atau saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id_trans_so)->where('status_ao', 1)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id_trans_so.' belum sampai ke AO'
            ], 404);
        }

        // Analisa Kuantitatif dan Kualitatif
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if ($id_pe_ta == null) {
            $PeriksaTanah = null;
        }

        $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        if ($id_pe_ke == null) {
            $PeriksaKenda = null;
        }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if ($PeriksaTanah == []) {
            $sumTaksasiTan = 0;
        }else{
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == null) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }

        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan

        // Rekomendasi CA
        $inputRecomCA = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => $req->input('plafon_kredit'), //45000000
            'jangka_waktu'          => $req->input('jangka_waktu'), // 48
            'suku_bunga'            => $req->input('suku_bunga'), // 1.70
        );

        // Rekomendasi Angsuran pada table recom_ca
        $plafonCA = $inputRecomCA['plafon_kredit'] == null ? 0 : $inputRecomCA['plafon_kredit'];
        $tenorCA  = $inputRecomCA['jangka_waktu']  == null ? 0 : $inputRecomCA['jangka_waktu'];
        $bunga    = $inputRecomCA['suku_bunga']    == null ? 0 : ($inputRecomCA['suku_bunga'] / 100);

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        }else{
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_cadebt'
                => empty($req->input('pemasukan_debitur'))    ? 0 : (int) $req->input('pemasukan_debitur'),

            'pemasukan_pasangan'
                => empty($req->input('pemasukan_pasangan'))   ? 0 : (int) $req->input('pemasukan_pasangan'),

            'pemasukan_penjamin'
                => empty($req->input('pemasukan_penjamin'))   ? 0 : (int) $req->input('pemasukan_penjamin'),

            'biaya_rumah_tangga'
                => empty($req->input('biaya_rumah_tangga'))   ? 0 : (int) $req->input('biaya_rumah_tangga'),

            'biaya_transport'
                => empty($req->input('biaya_transport'))      ? 0 : (int) $req->input('biaya_transport'),

            'biaya_pendidikan'
                => empty($req->input('biaya_pendidikan'))     ? 0 : (int) $req->input('biaya_pendidikan'),

            'biaya_telp_listr_air'
                => empty($req->input('biaya_telp_listr_air')) ? 0 : (int) $req->input('biaya_telp_listr_air'),

            'angsuran'
                => empty($req->input('angsuran'))             ? 0 : (int) $req->input('angsuran'),

            'biaya_lain'
                => empty($req->input('biaya_lain'))           ? 0 : (int) $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 2)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 2)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );


        // $rekomen_pendapatan  = $check->ao['kapbul']['total_pemasukan'];
        $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];
        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        $disposable_income = $rekomen_pend_bersih - $recom_angs;

        $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // End Kapasitas Bulanan

        // Analisa Kuantitatif dan Kualitatif
        $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $recom_ltv,
            'kuantitatif_dsr'               => $recom_dsr,
            'kuantitatif_idir'              => $recom_idir,
            'kuantitatif_hasil'             => $recom_hasil
        );

        $resultAll = array(
            'rekomendasi_ca'       => $inputRecomCA,
            'rekom_angsuran'       => $recom_angs,
            'ringkasan_analisa_ca' => $dataRingkasan
        );

        DB::connection('web')->beginTransaction();
        try{

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'data'   => $resultAll
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

    public function filter($year, $month, Request $req){
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
        $scope     = $pic->jpic['cakupan'];

        if ($month == null) {

            $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)
                    ->whereYear('created_at', '=', $year)
                    ->orderBy('created_at', 'desc');
        }else{

            $query_dir = TransAO::with('so', 'pic', 'cabang')->where('status_ao', 1)
                    ->whereYear('created_at', '=', $year)
                    ->whereMonth('created_at', '=', $month)
                    ->orderBy('created_at', 'desc');
        }

        $query = Helper::checkDir($scope, $query_dir, $id_area, $id_cabang);

        if ($query->get() == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($query->get() as $key => $val) {

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
                'id_trans_so'    => $val->id_trans_so       == null ? null : (int) $val->id_trans_so,
                'id_trans_ca'    => $val->so['id_trans_ca'] == null ? null : (int) $val->so['id_trans_ca'],
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
                'tgl_transaksi'     => $val->created_at
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => sizeof($data),
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

    public function full_show($id, Request $req){
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
        $scope     = $pic->jpic['cakupan'];

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if ($check_so == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        
        $check_ao = TransAO::with('pic', 'cabang')->where('id_trans_so', $id)->where('status_ao', 1)->first();
        
        if ($check_ao == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke AO'
            ], 404);
        }
        
        $check_ca = TransCA::with('so', 'pic', 'cabang')->where('id_trans_so', $id)->where('status_ca', 1)->first();


        if ($check_ca == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id '.$id.' belum sampai ke CA'
            ], 404);
        }

        if ($check_ca->status_ca == 1) {
            $status_ca = 'recommend';
        }elseif($check_ca->status_ca == 2){
            $status_ca = 'not recommend';
        }else{
            $status_ca = 'waiting';
        }

        $mutasi = MutasiBank::whereIn('id', explode(",", $check_ca->id_mutasi_bank))->get()->toArray();

        if($mutasi != []){

            foreach($mutasi as $i => $mut){
                $doub[$i] = array_slice($mut, 0, 5);
            }

            foreach($mutasi as $i => $mut){
                $slice[$i] = array_slice($mut, 5);
                foreach($slice as $key => $val){
                    foreach($val as $row => $col){
                        $arr[$i][$row] = explode(";",$col);
                    }
                }
            }

            foreach ($arr as $key => $subarr)
            {
                foreach ($subarr as $subkey => $subvalue)
                {
                    foreach($subvalue as $childkey => $childvalue)
                    {   
                        $out[$key][$childkey][$subkey] = ($childvalue);
                    }

                    $dataMut[$key] = array_merge($doub[$key], array('table' => $out[$key]));
                }
            }
        }else{
            $dataMut = null;
        }

        // $check_ca->getRelations(); // get all the related models
        // $check_ca->getRelation('author'); // to get only related author model

        $data[] = [
            'id_trans_so'           => $check_so->id == null ? null : (int) $check_so->id,
            'nomor_so'              => $check_so->nomor_so,
            'kapasitas_bulanan'     => $check_ca->kapbul,
            'pendapatan_usaha'      => $check_ca->usaha,
            'mutasi_bank'           => $dataMut,
            'data_keuangan'         => $check_ca->log_tab,
            'informasi_analisa_cc'  => array(
                'table'         => $iac = InfoACC::whereIn('id', explode(",", $check_ca->id_info_analisa_cc))->get()->toArray(),
                'total_plafon'  => array_sum(array_column($iac,'plafon')),
                'total_baki_debet' => array_sum(array_column($iac,'baki_debet')),
                'angsuran'         => array_sum(array_column($iac,'angsuran')),
                'collectabitas_tertinggi' => max(array_column($iac,'collectabilitas'))
            ),
            'ringkasan_analisa'     => $check_ca->ringkasan,
            'rekomendasi_pinjaman'  => $check_ca->recom_pin,
            'rekomendasi_ca'        => $check_ca->recom_ca,
            'asuransi_jiwa'         => $check_ca->as_jiwa,
            'asuransi_jaminan'      => AsuransiJaminan::whereIn('id', explode(";", $check_ca->id_asuransi_jaminan))->get()->toArray(),
            'status_ca'             => $status_ca,
            'tgl_transaksi'         => $check_ca->created_at
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
}
