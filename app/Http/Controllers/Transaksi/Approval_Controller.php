<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\PemeriksaanAgunTan;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Models\Pengajuan\AO\PemeriksaanAgunKen;
use App\Http\Requests\Transaksi\ApprovalReq;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanTanah;
use App\Http\Requests\Transaksi\BlankRequest;
use App\Models\Pengajuan\AO\PendapatanUsaha;
use App\Models\Pengajuan\AO\RekomendasiAO;
use App\Models\Pengajuan\CA\TabDebt;
use App\Models\Pengajuan\CA\MutasiBank;
use App\Models\Pengajuan\CA\InfoACC;
use App\Models\Pengajuan\CA\RingkasanAnalisa;
use App\Models\Pengajuan\CA\RekomendasiPinjaman;
use App\Models\Pengajuan\CA\AsuransiJiwa;
use App\Models\Pengajuan\CA\AsuransiJaminan;
use App\Models\Pengajuan\CA\RekomendasiCA;
// use Illuminate\Support\Facades\File;
use App\Models\Pengajuan\AO\KapBulanan;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransSO;
use App\Models\Pengajuan\AO\ValidModel;
use App\Models\Pengajuan\AO\VerifModel;
use App\Models\Pengajuan\SO\Debitur;
use App\Models\AreaKantor\JPIC;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Approval_Controller extends BaseController
{
    public function list_team(Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query = PIC::with(['jpic', 'area', 'cabang'])
            ->whereHas('jpic', function ($q) {
                // Query the name field in status table
                $q->where('bagian', '=', 'team_caa');
            })
            ->where('flg_aktif', 1);

        if ($scope == 'CABANG') {

            $parQuery = $query->whereHas('cabang', function ($q) use ($id_cabang) {
                $q->where('id', $id_cabang);
                $q->orWhere('nama', 'Pusat');
            })
                ->get()
                ->sortByDesc('jpic.urutan_jabatan');
        } elseif ($scope == 'AREA') {

            $parQuery = $query->whereHas('area', function ($q) use ($id_area) {
                $q->where('id', $id_area);
                $q->orWhere('nama', 'Pusat');
            })
                ->get()
                ->sortByDesc('jpic.urutan_jabatan');
        } else {

            $parQuery = $query->get()->sortByDesc('jpic.urutan_jabatan');
        }

        if ($parQuery == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        $data = array();
        foreach ($parQuery as $key => $val) {

            if ($key == 0) {
                $checked = true;
            } else {
                $checked = false;
            }

            $data[] = array(
                "id"        => $val->id == null ? null : (int) $val->id,
                "user_id"   => $val->user_id,
                "plafon_max" => (int) $val->plafon_caa,
                "nama_area" => $val->area['nama'],
                "cabang"    => $val->cabang['nama'],
                "jabatan"   => $val->jpic['nama_jenis'],
                "nama"      => $val->nama,
                "email"     => $val->email,
                // "flg_aktif" => (bool) $val->flg_aktif,
                "checked"   => $checked
            );
        }

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

    public function detail_team($id_team, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $val = PIC::with(['jpic', 'area', 'cabang'])
            ->where('flg_aktif', 1)
            ->where('id', $id_team)
            ->first();

        if ($val == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }


        $data = array(
            "id"        => $val->id      == null ? null : (int) $val->id,
            "user_id"   => $val->user_id == null ? null : (int) $val->user_id,
            "id_area"   => $val->id_area == null ? null : (int) $val->id_area,
            "nama_area" => $val->area['nama'],
            "id_cabang" => $val->id_cabang == null ? null : (int) $val->id_cabang,
            "cabang"    => $val->cabang['nama'],
            "jabatan"   => $val->jpic['nama_jenis'],
            "nama"      => $val->nama,
            "email"     => $val->email,
            "plafon_max" => (int) $val->plafon_caa,
            "flg_aktif" => (bool) $val->flg_aktif
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

    public function index($id, Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $query = Approval::with('so', 'caa', 'pic')
            ->where('id_trans_so', $id)
            ->get()
            ->sortByDesc('pic.jpic.urutan_jabatan');

        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        // $data = array();
        foreach ($query as $key => $val) {

            if ($val->status) {
                $status = $val->status;
            } else {
                $status = 'waiting';
            }

            $data[] = [
                'id_approval'    => $val->id          == null ? null : (int) $val->id,
                'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
                'user_id'        => $val->user_id     == null ? null : (int) $val->user_id,
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'nomor_caa'      => $val->caa['nomor_caa'],
                'id_pic'         => $val->id_pic == null ? null : (int) $val->id_pic,
                'batas_plafon'   => (int) $val->pic['plafon_caa'],
                'nama_pic'       => $val->pic['nama'],
                // 'id_jenis_pic'   => $val->pic['id_mj_pic'],
                'jabatan'        => $val->pic['jpic']['nama_jenis'],

                'pengajuan_so'   => [
                    'plafon'  => (int) $val->so['faspin']['plafon'],
                    'tenor'   => (int) $val->so['faspin']['tenor']
                ],
                'plafon'         => (int) $val->plafon,
                'tenor'          => (int) $val->tenor,
                'rincian'        => $val->rincian,
                'status_approval' => $status,
                'tanggal'        => empty($val->updated_at) ? null : Carbon::parse($val->updated_at)->format("d-m-Y H:i:s"),
            ];
        }

        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'count'  => $query->count(),
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

    public function show($id, $id_approval, Request $req)
    {
        $check_caa = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        if ($check_caa == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => 'Transaksi dengan id ' . $id . ' belum sampai ke CAA'
            ], 404);
        }

        $val = Approval::where('id_trans_so', $id)->where('id', $id_approval)->first();

        if ($val == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => 'Transaksi dengan id ' . $id . ' belum sampai ke Approval'
            ], 404);
        }

        if ($val->status) {
            $status = $val->status;
        } else {
            $status = 'waiting';
        }

        if ($val->so['faspin']['plafon'] <= $val->pic['plafon_caa']) {

            $list_status = array('accept' => true, 'forward' => false, 'reject' => true, 'return' => true);
        } else {

            $list_status = array('accept' => false, 'forward' => true, 'reject' => true, 'return' => true);
        }

        $data = [
            'id_approval'    => $val->id          == null ? null : (int) $val->id,
            'id_trans_so'    => $val->id_trans_so == null ? null : (int) $val->id_trans_so,
            'user_id'        => $val->user_id     == null ? null : (int) $val->user_id,
            'nomor_so'       => $val->so['nomor_so'],
            'nomor_ao'       => $val->so['ao']['nomor_ao'],
            'nomor_ca'       => $val->so['ca']['nomor_ca'],
            'nomor_caa'      => $val->caa['nomor_caa'],
            'id_pic'         => $val->id_pic == null ? null : (int) $val->id_pic,
            'batas_plafon'   => (int) $val->pic['plafon_caa'],
            'nama_pic'       => $val->pic['nama'],
            'jabatan'        => $val->pic['jpic']['nama_jenis'],

            'pengajuan_so'   => [
                'plafon'  => (int) $val->so['faspin']['plafon'],
                'tenor'   => (int) $val->so['faspin']['tenor']
            ],
            'plafon'         => (int) $val->plafon,
            'tenor'          => (int) $val->tenor,
            'rincian'        => $val->rincian,
            'status_approval' => $status,
            'tanggal'        => empty($val->updated_at) ? null : Carbon::parse($val->updated_at)->format("d-m-Y H:i:s"),

            'list_status' => $list_status
        ];

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

    public function approve($id, $id_approval, Request $req, ApprovalReq $request)
    {
        $pic     = $req->pic; // From PIC middleware
        $user_id = $req->auth->user_id;

        $check_so = TransSO::where('id', $id)->first();

        if ($check_so == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum ada di SO"
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();

        if ($check_ao == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke AO"
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->where('status_ca', 1)->first();

        if ($check_ca == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke ca"
            ], 404);
        }

        $check_caa = TransCAA::where('id_trans_so', $id)->where('status_caa', 1)->first();

        if ($check_caa == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke caa"
            ], 404);
        }

        $check = Approval::where('id', $id_approval)->where('id_trans_so', $id)->first();

        if ($check == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " dan dengan id approval " . $id_approval . " tidak ada di daftar antrian Approval"
            ], 404);
        }

        if ($check->status != 'waiting') {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id `{$id}` dan dengan id approval `{$id_approval}` sudah sudah dalam proses dengan status `{$check->status}`"
            ], 404);
        }

        $forward_q = Approval::where('id_trans_so', $id)->where('id', '>', $id_approval)->first();

        if ($forward_q == null) {
            $to_forward = null;
        } else {
            $to_forward = $forward_q->id_pic;
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;

        $form = array(
            'user_id'       => $user_id,
            'id_area'       => $id_area,
            'id_cabang'     => $id_cabang,
            'plafon'        => $request->input('plafon'),
            'tenor'         => $request->input('tenor'),
            'rincian'       => $request->input('rincian'),
            'status'        => $st = $request->input('status'),
            'tujuan_forward' => $st == 'forward' ? $to_forward : null //$request->input('tujuan_forward'),
            // 'tanggal'       => Carbon::now()->toDateTimeString()
        );

        DB::connection('web')->beginTransaction();

        try {


            if ($form['status'] == 'accept' || $form['status'] == 'reject' || $form['status'] == 'return') {
                $status = $form['status'] . " by picID {$pic->id}";
                // TransCAA::where('id_trans_so', $id)->update(['status_team_caa' => $form['status'].' by user '.$user_id]);
            } elseif ($form['status'] == 'forward') {
                $status = $form['status'] . " by picID {$pic->id} to picID {$form['tujuan_forward']}";
            }

            if ($form['status'] === 'return') {
                TransCAA::where('id_trans_so', $id)->where('id_trans_so', $id)->delete();
                Approval::where('id_trans_so', $id)->delete();

                $check_nst = Penyimpangan::where('id_trans_so', $id)->first();

                if ($check_nst != null) {
                    Penyimpangan::where('id_trans_so', $id)->delete();
                }
            }

            $trans_caa = TransCAA::where('id_trans_so', $check->id_trans_so)->update(['status_team_caa' => $status]);

            $approval = Approval::where('id', $id_approval)->update($form);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data untuk berhasil di - ' . $form['status'],
                'data'   => array(
                    'approval'  => $approval,
                    'transaksi' => $trans_caa
                )
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

    // Team Caa
    public function report_approval($id)
    {

        $check_so = TransSO::where('id', $id)->first();

        if (empty($check_so)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum ada di SO"
            ], 404);
        }

        $check_ao = TransAO::where('status_ao', 1)->where('id_trans_so', $id)->first();

        if (empty($check_ao)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke AO"
            ], 404);
        }

        $check_ca = TransCA::where('status_ca', 1)->where('id_trans_so', $id)->latest()->first();

        if (empty($check_ca)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke CA"
            ], 404);
        }

        $check_caa = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        if (empty($check_caa)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke CAA"
            ], 404);
        }


        $check_team = Approval::where('id_trans_so', $id)->whereIn('id_pic', explode(",", $check_caa->pic_team_caa))->get();

        if (empty($check_team)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id " . $id . " belum sampai ke Approval"
            ], 404);
        }

        $data = array();;
        foreach ($check_team as $key => $val) {
            $data[] = [
                'jabatan' => $val->pic['jpic']['nama_jenis'],
                'id_pic'  => $val->id_pic  == null ? null : (int) $val->id_pic,
                'user_id' => $val->user_id == null ? null : (int) $val->user_id,
                'nama_pic' => $val->pic['nama'],
                'plafon'  => $val->plafon,
                'tenor'   => $val->tenor,
                'status'  => $val->status,
                'rincian' => $val->rincian
            ];
        }

        $AguTa = AgunanTanah::whereIn('id', explode(",", $check_ao->id_agunan_tanah))->get();

        $idTan = array();
        foreach ($AguTa as $key => $value) {

            $idTan[$key] = $value->jenis_sertifikat . ' / ' . ($value->tgl_ukur_sertifikat == null ? 'null' : $value->tgl_ukur_sertifikat);
        }

        $imTan = implode("; ", $idTan);


        // Agunan Kendaraan
        $AguKe = AgunanKendaraan::whereIn('id', explode(",", $check_ao->id_agunan_kendaraan))->get();

        $idKen = array();
        foreach ($AguKe as $key => $value) {

            $idKen[$key] = 'BPKB / ' . ($value->no_bpkb == null ? 'null' : $value->no_bpkb);
        }

        $imKen = implode("; ", $idKen);


        if ($imTan == "" && $imKen == "") {
            $jaminan = null;
        } elseif ($imTan != "" && $imKen != "") {
            $jaminan = $imTan . '; ' . $imKen;
        } elseif ($imTan == "" && $imKen != "") {
            $jaminan = $imKen;
        } elseif ($imTan != "" && $imKen == "") {
            $jaminan = $imTan;
        }

        // $url_in_array = in_array('accept', $status_in_array);


        $num_sts = array_search('accept', array_column($data, 'status'), true);;

        if ($num_sts == false) {
            $tenor  = null;
            $plafon = null;
        } else {
            $tenor  = $data[$num_sts]['tenor'];
            $plafon = $data[$num_sts]['plafon'];
        }



        $result = array(
            'id_transaksi' => $check_caa->id_trans_so == null ? null : (int) $check_caa->id_trans_so,
            'debitur' => [
                'id'   => $check_so->id_calon_debitur == null ? null : (int) $check_so->id_calon_debitur,
                'nama' => $check_so->debt['nama_lengkap']
            ],
            'approved' => [
                'id_pic'  => $check_caa->id_pic  == null ? null : (int) $check_caa->id_pic,
                'user_id' => $check_caa->user_id == null ? null : (int) $check_caa->user_id,
                'nama_ca' => $check_caa->pic['nama'],
                'plafon'  => (int) $plafon,
                'tenor'   => (int) $tenor,
                'jaminan' => $jaminan
            ],
            'list_approver' => $data
        );


        try {
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => $result
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

    public function update($id, Request $request, BlankRequest $req)
    {
        $pic     = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

        $countCA = TransCA::latest('id', 'nomor_ca')->first();

        if (!$countCA) {
            $lastNumb = 1;
        } else {
            $no = $countCA->nomor_ca;

            $arr = explode("-", $no, 5);

            $lastNumb = str_replace(" [revisi]", "", $arr[4]) + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $pic->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ca = $pic->id_cabang . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();

        if (!$check_so) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaan DAS dan HM'
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        if (!$check_ao) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum sampai ke AO'
            ], 404);
        }

        // $check_ca = TransCA::where('id_trans_so', $id)->first();

        // if ($check_ca != null) {
        //     return response()->json([
        //         'code'    => 404,
        //         'status'  => 'not found',
        //         'message' => 'Transaksi dengan id ' . $id . ' sudah ada di CA'
        //     ], 404);
        // }

        $transCA = array(
            'nomor_ca'    => $nomor_ca,
            'user_id'     => $user_id,
            'id_trans_so' => $id,
            'id_pic'      => $pic->id,
            'id_area'     => $pic->id_area,
            'id_cabang'   => $pic->id_cabang,
            'catatan_ca'  => $req->input('catatan_ca'),
            'status_ca'   => empty($req->input('status_ca')) ? 1 : $req->input('status_ca')
        );

        // Pendapatan Usaha Cadebt
        $dataPendapatanUsaha = array(
            'pemasukan_tunai'      => empty($req->input('pemasukan_tunai'))     ? 0 : $req->input('pemasukan_tunai'),
            'pemasukan_kredit'     => empty($req->input('pemasukan_kredit'))    ? 0 : $req->input('pemasukan_kredit'),
            'biaya_sewa'           => empty($req->input('biaya_sewa'))          ? 0 : $req->input('biaya_sewa'),
            'biaya_gaji_pegawai'   => empty($req->input('biaya_gaji_pegawai'))  ? 0 : $req->input('biaya_gaji_pegawai'),
            'biaya_belanja_brg'    => empty($req->input('biaya_belanja_brg'))   ? 0 : $req->input('biaya_belanja_brg'),
            'biaya_telp_listr_air' => empty($req->input('biaya_telp_listr_air')) ? 0 : $req->input('biaya_telp_listr_air'),
            'biaya_sampah_kemanan' => empty($req->input('biaya_sampah_kemanan')) ? 0 : $req->input('biaya_sampah_kemanan'),
            'biaya_kirim_barang'   => empty($req->input('biaya_kirim_barang'))  ? 0 : $req->input('biaya_kirim_barang'),
            'biaya_hutang_dagang'  => empty($req->input('biaya_hutang_dagang')) ? 0 : $req->input('biaya_hutang_dagang'),
            'biaya_angsuran'       => empty($req->input('biaya_angsuran'))      ? 0 : $req->input('biaya_angsuran'),
            'biaya_lain_lain'      => empty($req->input('biaya_lain_lain'))     ? 0 : $req->input('biaya_lain_lain')
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

            'telp_listr_air'
            => empty($req->input('telp_listr_air'))       ? 0 : $req->input('telp_listr_air'),

            'angsuran'
            => empty($req->input('angsuran'))             ? 0 : $req->input('angsuran'),

            'biaya_lain'
            => empty($req->input('biaya_lain'))           ? 0 : $req->input('biaya_lain'),
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        // Ceiling Recomendasi Pinjaman
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
                ? null : $req->input('note_recom'),

            'bunga_pinjaman'
            => empty($req->input('bunga_pinjaman'))
                ? null : $req->input('bunga_pinjaman'),

            'nama_ca'
            => empty($req->input('nama_ca'))
                ? $pic->nama : $req->input('nama_ca')
        );

        // Rekomendasi Angsuran pada table rrekomendasi_pinjaman
        $plafonCA = $rekomPinjaman['recom_nilai_pinjaman'] == null ? 0 : $rekomPinjaman['recom_nilai_pinjaman'];
        $tenorCA  = $rekomPinjaman['recom_tenor']          == null ? 0 : $rekomPinjaman['recom_tenor'];
        $bunga    = $rekomPinjaman['bunga_pinjaman']       == null ? 0 : ($rekomPinjaman['bunga_pinjaman'] / 100);

        $rekomen_pendapatan  = $total_KapBul['total_pemasukan']   == null ? 0 : $total_KapBul['total_pemasukan'];
        $rekomen_pengeluaran = $total_KapBul['total_pengeluaran'] == null ? 0 : $total_KapBul['total_pengeluaran'];
        $rekomen_angsuran    = $inputKapBul['angsuran']           == null ? 0 : $inputKapBul['angsuran'];

        if ($plafonCA == 0 && $tenorCA == 0 && $bunga == 0) {
            $recom_angs = 0;
        } else {
            $recom_angs = Helper::recom_angs($plafonCA, $tenorCA, $bunga);
        }

        $rekomen_pend_bersih = $rekomen_pendapatan - $rekomen_pengeluaran;

        $disposable_income   = $rekomen_pend_bersih - $recom_angs;

        $kapBul = array_merge($inputKapBul, $total_KapBul, array('disposable_income'  => $disposable_income, 'ao_ca' => 'CA'));
        // End Kapasitas Bulanan

        // Check Pemeriksaan
        $id_pe_ta = $check_ao->id_periksa_agunan_tanah;

        if (empty($id_pe_ta)) {
            $PeriksaTanah = null;
        }

        // $id_pe_ke = $check_ao->id_periksa_agunan_kendaraan;

        // if ($id_pe_ke == null) {
        //     $PeriksaKenda = null;
        // }

        $PeriksaTanah = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        if (empty($PeriksaTanah)) {
            $sumTaksasiTan = 0;
        } else {
            $sumTaksasiTan = array_sum(array_column($PeriksaTanah, 'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        }

        // $PeriksaKenda = PemeriksaanAgunTan::select('nilai_taksasi_agunan')->whereIn('id', explode(",", $id_pe_ta))->get()->toArray();

        // if ($PeriksaKenda == []) {
        //     $sumTaksasiKen = 0;
        // }else{
        //     $sumTaksasiKen = array_sum(array_column($PeriksaTanah,'nilai_taksasi_agunan')); //array_sum($PeriksaTanah);
        // }
        // $sumAllTaksasi = $sumTaksasiTan + $sumTaksasiKen; // Semua Nilai Taksasi dari semua agunan
        $sumAllTaksasi = $sumTaksasiTan; // Semua Nilai Taksasi dari semua agunan


        // $recom_ltv   = Helper::recom_ltv($plafonCA, $sumAllTaksasi);
        // $recom_idir  = Helper::recom_idir($recom_angs, $rekomen_pendapatan, $rekomen_pengeluaran);
        // $recom_dsr   = Helper::recom_dsr($recom_angs, $rekomen_pendapatan, $rekomen_angsuran);
        // $recom_hasil = Helper::recom_hasil($recom_dsr, $recom_ltv, $recom_idir);

        // Data Ringkasan Analisa CA
        $dataRingkasan = array(
            'kuantitatif_ttl_pendapatan'    => $req->input('kuantitatif_ttl_pendapatan'),
            'kuantitatif_ttl_pengeluaran'   => $req->input('kuantitatif_ttl_pengeluaran'),
            'kuantitatif_pendapatan_bersih' => $req->input('kuantitatif_pendapatan'),
            'kuantitatif_angsuran'          => $req->input('kuantitatif_angsuran'),
            // 'kuantitatif_ttl_pendapatan'    => $rekomen_pendapatan,
            // 'kuantitatif_ttl_pengeluaran'   => $rekomen_pengeluaran,
            // 'kuantitatif_pendapatan_bersih' => $rekomen_pend_bersih,
            // 'kuantitatif_angsuran'          => $recom_angs,
            'kuantitatif_ltv'               => $req->input('kuantitatif_ltv'),
            'kuantitatif_dsr'               => $req->input('kuantitatif_dsr'),
            'kuantitatif_idir'              => $req->input('kuantitatif_idir'),
            'kuantitatif_hasil'             => $req->input('kuantitatif_hasil'),


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

        // Mutasi Bank
        if (!empty($req->input('no_rekening_mutasi'))) {

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

        // Rekomendasi CA
        $recomCA = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => $req->input('plafon_kredit'),
            'jangka_waktu'          => $req->input('jangka_waktu'),
            'suku_bunga'            => $req->input('suku_bunga'),
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_asuransi_jiwa'   => $req->input('biaya_asuransi_jiwa'),
            'biaya_asuransi_jaminan' => $req->input('biaya_asuransi_jaminan'),
            'notaris'               => $req->input('notaris'),
            'biaya_tabungan'        => $req->input('biaya_tabungan'),

            'rekom_angsuran'        => $recom_angs,

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


        if (!empty($req->input('jangka_waktu_as_jaminan'))) {

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
        } else {
            $jaminanImplode = array(
                'nama_asuransi'       => null,
                'jangka_waktu'        => null,
                'nilai_pertanggungan' => null,
                'jatuh_tempo'         => null
            );
        }

        try {
            DB::connection('web')->beginTransaction();

            if (!empty($dataMuBa)) {
                for ($i = 0; $i < count($dataMuBa); $i++) {
                    $mutasi = MutasiBank::create($dataMuBa[$i]);

                    $id_mutasi['id'][$i] = $mutasi->id;
                }

                $MutasiID   = implode(",", $id_mutasi['id']);
            } else {
                $MutasiID = null;
            }

            if (!empty($dataTabUang)) {
                $tabungan = TabDebt::create($dataTabUang);

                $idTabungan = $tabungan->id;
            } else {
                $idTabungan = null;
            }

            if (!empty($dataACC)) {
                for ($i = 0; $i < count($dataACC); $i++) {
                    $IACC = InfoACC::create($dataACC[$i]);

                    $arrACC['id'][$i] = $IACC->id;
                }

                $idInfo = implode(",", $arrACC['id']);
            } else {
                $idInfo = null;
            }

            if (!empty($dataRingkasan)) {
                $analisa = RingkasanAnalisa::create($dataRingkasan);
                $idAnalisa = $analisa->id;
            } else {
                $idAnalisa = null;
            }

            if (!empty($rekomPinjaman)) {
                $recomPin = RekomendasiPinjaman::create($rekomPinjaman);
                $idrecomPin = $recomPin->id;
            } else {
                $idrecomPin = null;
            }

            if (!empty($asJiwa)) {
                $jiwa = AsuransiJiwa::create($asJiwa);
                $idJiwa = $jiwa->id;
            } else {
                $idJiwa = null;
            }

            if (!empty($jaminanImplode)) {
                $jaminan = AsuransiJaminan::create($jaminanImplode);
                $idJaminan = $jaminan->id;
            } else {
                $idJaminan = null;
            }

            if (!empty($recomCA)) {
                $reCA = RekomendasiCA::create($recomCA);;
                $idReCA = $reCA->id;
            } else {
                $idReCA = null;
            }

            if (!empty($Pendapatan)) {
                $pend = PendapatanUsaha::create($Pendapatan);
                $idPendUs = $pend->id;
            } else {
                $idPendUs = null;
            }

            if (!empty($kapBul)) {
                $Q_Kapbul = KapBulanan::create($kapBul);
                $idKapBul = $Q_Kapbul->id;
            } else {
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
                'message' => 'Data untuk CA berhasil dikirim',
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
}
