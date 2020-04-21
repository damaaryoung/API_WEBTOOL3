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
        $pic = $request->pic; // From PIC middleware
        $user_id = $request->auth->user_id;

        $countTAO = TransAO::latest('id', 'nomor_ao')->first();
        //   dd($countTAO);
        if (!$countTAO) {
            $lastNumb = 1;
        } else {
            $no = $countTAO->nomor_ao;

            $arr = explode("-", $no, 5);

            $lastNumb = $arr[4] + 1;
        }

        //Data Transaksi SO
        $nows  = Carbon::now();
        $year  = $nows->year;
        $month = $nows->month;

        $JPIC   = JPIC::where('id', $pic->id_mj_pic)->first();

        //  ID-Cabang - AO / CA / SO - Bulan - Tahun - NO. Urut
        $nomor_ao = $pic->id_cabang . '-' . $JPIC->nama_jenis . '-' . $month . '-' . $year . '-' . $lastNumb;

        $check_so = TransSO::where('id', $id)->where('status_das', 1)->where('status_hm', 1)->first();
        // dd($check_so);
        if (empty($check_so)) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' belum ada di SO atau belum komplit saat pemeriksaaan DAS dan HM'
            ], 404);
        }

        /** Start Check Lampiran */
        $check_form_persetujuan_ideb = $check_so->ao['form_persetujuan_ideb'];

        // Agunan Tanah
        $check_agunan_bag_depan      = $check_so->ao['tan']['agunan_bag_depan'];
        $check_agunan_bag_jalan      = $check_so->ao['tan']['agunan_bag_jalan'];
        $check_agunan_bag_ruangtamu  = $check_so->ao['tan']['agunan_bag_ruangtamu'];
        $check_agunan_bag_kamarmandi = $check_so->ao['tan']['agunan_bag_kamarmandi'];
        $check_agunan_bag_dapur      = $check_so->ao['tan']['agunan_bag_dapur'];

        $check_lamp_imb_tan          = $check_so->ao['tan']['lamp_imb'];
        $check_lamp_pbb_tan          = $check_so->ao['tan']['lamp_pbb'];
        $check_lamp_sertifikat_tan   = $check_so->ao['tan']['lamp_sertifikat'];

        // Agunan Kendaraan
        $check_lamp_agunan_depan_ken = $check_so->ao['tan']['lamp_agunan_depan_ken'];
        $check_lamp_agunan_kanan_ken = $check_so->ao['tan']['lamp_agunan_kanan_ken'];
        $check_lamp_agunan_kiri_ken  = $check_so->ao['tan']['lamp_agunan_kiri_ken'];
        $check_lamp_agunan_belakang_ken = $check_so->ao['tan']['lamp_agunan_belakang_ken'];
        $check_lamp_agunan_dalam_ken = $check_so->ao['tan']['lamp_agunan_dalam_ken'];

        // Debitur
        $check_lamp_ktp             = $check_so->debt['lamp_ktp'];
        $check_lamp_kk              = $check_so->debt['lamp_kk'];
        $check_lamp_sertifikat      = $check_so->debt['lamp_sertifikat'];
        $check_lamp_sttp_pbb        = $check_so->debt['lamp_sttp_pbb'];
        $check_lamp_imb             = $check_so->debt['lamp_imb'];
        $check_foto_agunan_rumah    = $check_so->debt['foto_agunan_rumah'];
        $check_lamp_buku_tabungan   = $check_so->debt['lamp_buku_tabungan'];
        $check_lamp_skk             = $check_so->debt['lamp_skk'];
        $check_lamp_sku             = $check_so->debt['lamp_sku'];
        $check_lamp_slip_gaji       = $check_so->debt['lamp_slip_gaji'];
        $check_foto_pembukuan_usaha = $check_so->debt['foto_pembukuan_usaha'];
        $check_lamp_foto_usaha      = $check_so->debt['lamp_foto_usaha'];
        $check_lamp_surat_cerai     = $check_so->debt['lamp_surat_cerai'];
        $check_lamp_tempat_tinggal  = $check_so->debt['lamp_tempat_tinggal'];

        // dd($check_agunan_bag_depan, $check_agunan_bag_jalan, $check_agunan_bag_ruangtamu, $check_agunan_bag_kamarmandi, $check_agunan_bag_dapur);

        /** End Check Lampiran */

        $check_ao = TransAO::where('id_trans_so', $id)->first();

        if ($check_ao != null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Transaksi dengan id ' . $id . ' sudah ada di AO'
            ], 404);
        }

        $lamp_dir = $check_so->debt['no_ktp'];

        // Form Persetujuan Ideb
        if ($file = $req->file('form_persetujuan_ideb')) {
            $path = $lamp_dir . '/ideb';
            $name = 'form_persetujuan_ideb';

            $check_file = $check_form_persetujuan_ideb;

            $form_persetujuan_ideb = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $form_persetujuan_ideb = $check_form_persetujuan_ideb;
        }

        $TransAO = array(
            'nomor_ao'              => $nomor_ao,
            'id_trans_so'           => $id,
            'user_id'               => $user_id,
            'id_pic'                => $pic->id,
            'id_area'               => $pic->id_area,
            'id_cabang'             => $pic->id_cabang,
            'catatan_ao'            => $req->input('catatan_ao'),
            'status_ao'             => empty($req->input('status_ao')) ? 1 : $req->input('status_ao'),
            'form_persetujuan_ideb' => $form_persetujuan_ideb
        );

        $recom_AO = array(
            'produk'                => $req->input('produk'),
            'plafon_kredit'         => $req->input('plafon_kredit'),
            'jangka_waktu'          => $req->input('jangka_waktu'),
            'suku_bunga'            => $req->input('suku_bunga'),
            'pembayaran_bunga'      => $req->input('pembayaran_bunga'),
            'akad_kredit'           => $req->input('akad_kredit'),
            'ikatan_agunan'         => $req->input('ikatan_agunan'),
            'analisa_ao'            => $req->input('analisa_ao'),
            'biaya_provisi'         => $req->input('biaya_provisi'),
            'biaya_administrasi'    => $req->input('biaya_administrasi'),
            'biaya_credit_checking' => $req->input('biaya_credit_checking'),
            'biaya_tabungan'        => $req->input('biaya_tabungan'),
            'tujuan_pinjaman'        => $req->input('tujuan_pinjaman'),
            'jenis_pinjaman'        => $req->input('jenis_pinjaman')
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
            'ver_pembukuan_usaha_debt' => $req->input('ver_pembukuan_usaha_debt'),
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

        /** Lampiran Agunan Tanah */
        if ($files = $req->file('agunan_bag_depan')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_depan';

            $check_file = $check_agunan_bag_depan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_depan = $arrayPath;
        } else {
            $agunan_bag_depan = $check_agunan_bag_depan;
        }

        if ($files = $req->file('agunan_bag_jalan')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_jalan';

            $check_file = $check_agunan_bag_jalan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_jalan = $arrayPath;
        } else {
            $agunan_bag_jalan = $check_agunan_bag_jalan;
        }

        if ($files = $req->file('agunan_bag_ruangtamu')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_ruangtamu';

            $check_file = $check_agunan_bag_ruangtamu;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_ruangtamu = $arrayPath;
        } else {
            $agunan_bag_ruangtamu = $check_agunan_bag_ruangtamu;
        }

        if ($files = $req->file('agunan_bag_kamarmandi')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_kamarmandi';

            $check_file = $check_agunan_bag_kamarmandi;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_ruangtamu = $arrayPath;
        } else {
            $agunan_bag_kamarmandi = $check_agunan_bag_kamarmandi;
        }

        if ($files = $req->file('agunan_bag_dapur')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'agunan_bag_dapur';

            $check_file = $check_agunan_bag_dapur;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $agunan_bag_dapur = $arrayPath;
        } else {
            $agunan_bag_dapur = $check_agunan_bag_dapur;
        }

        /** Lampiran Agunan Kendaraan */
        if ($files = $req->file('lamp_agunan_depan_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_depan';

            $check_file = $check_lamp_agunan_depan_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_depan_ken = $arrayPath;
        } else {
            $lamp_agunan_depan_ken = $check_lamp_agunan_depan_ken;
        }


        if ($files = $req->file('lamp_agunan_kanan_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_kanan';

            $check_file = $check_lamp_agunan_kanan_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_kanan_ken = $arrayPath;
        } else {
            $lamp_agunan_kanan_ken = $check_lamp_agunan_kanan_ken;
        }


        if ($files = $req->file('lamp_agunan_kiri_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_kiri';

            $check_file = $check_lamp_agunan_kiri_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_kiri_ken = $arrayPath;
        } else {
            $lamp_agunan_kiri_ken = $check_lamp_agunan_kiri_ken;
        }


        if ($files = $req->file('lamp_agunan_belakang_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_belakang';

            $check_file = $check_lamp_agunan_belakang_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_belakang_ken = $arrayPath;
        } else {
            $lamp_agunan_belakang_ken = $check_lamp_agunan_belakang_ken;
        }

        if ($files = $req->file('lamp_agunan_dalam_ken')) {
            $path = $lamp_dir . '/agunan_kendaraan';
            $name = 'agunan_dalam';

            $check_file = $check_lamp_agunan_dalam_ken;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_agunan_dalam_ken = $arrayPath;
        } else {
            $lamp_agunan_dalam_ken = $check_lamp_agunan_dalam_ken;
        }

        // Tambahan Agunan Tanah
        if ($files = $req->file('lamp_imb')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'lamp_imb';

            $check_file = $check_lamp_imb_tan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_imb_tan = $arrayPath;
        } else {
            $lamp_imb_tan = $check_lamp_imb_tan;
        }

        if ($files = $req->file('lamp_pbb')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'lamp_pbb';

            $check_file = $check_lamp_pbb_tan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_pbb_tan = $arrayPath;
        } else {
            $lamp_pbb_tan = $check_lamp_pbb_tan;
        }

        if ($files = $req->file('lamp_sertifikat')) {
            $path = $lamp_dir . '/agunan_tanah';
            $name = 'lamp_sertifikat';

            $check_file = $check_lamp_sertifikat_tan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_sertifikat_tan = $arrayPath;
        } else {
            $lamp_sertifikat_tan = $check_lamp_sertifikat_tan;
        }

        if (!empty($req->input('nama_penghuni_agunan'))) {
            for ($i = 0; $i < count($req->input('nama_penghuni_agunan')); $i++) {
                $pemAguTa[] = [
                    'nama_penghuni'
                    => empty($req->nama_penghuni_agunan[$i])
                        ? null : $req->nama_penghuni_agunan[$i],

                    'status_penghuni'
                    => empty($req->status_penghuni_agunan[$i])
                        ? null : strtoupper($req->status_penghuni_agunan[$i]),

                    'bentuk_bangunan'
                    => empty($req->bentuk_bangunan_agunan[$i])
                        ? null : $req->bentuk_bangunan_agunan[$i],

                    'kondisi_bangunan'
                    => empty($req->kondisi_bangunan_agunan[$i])
                        ? null : $req->kondisi_bangunan_agunan[$i],

                    'fasilitas'
                    => empty($req->fasilitas_agunan[$i])
                        ? null : $req->fasilitas_agunan[$i],

                    'listrik'
                    => empty($req->listrik_agunan[$i])
                        ? null : $req->listrik_agunan[$i],

                    'nilai_taksasi_agunan'
                    => empty($req->nilai_taksasi_agunan[$i])
                        ? null : $req->nilai_taksasi_agunan[$i],

                    'nilai_taksasi_bangunan'
                    => empty($req->nilai_taksasi_bangunan[$i])
                        ? null : $req->nilai_taksasi_bangunan[$i],

                    'tgl_taksasi'
                    => empty($req->tgl_taksasi_agunan[$i])
                        ? null : Carbon::parse($req->tgl_taksasi_agunan[$i])->format('Y-m-d'),

                    'nilai_likuidasi'
                    => empty($req->nilai_likuidasi_agunan[$i])
                        ? null : $req->nilai_likuidasi_agunan[$i],

                    'nilai_agunan_independen'
                    => empty($req->nilai_agunan_independen[$i])
                        ? null : $req->nilai_agunan_independen[$i],

                    'perusahaan_penilai_independen'
                    => empty($req->perusahaan_penilai_independen[$i])
                        ? null : $req->perusahaan_penilai_independen[$i]
                ];
            }
        }

        // dd($pemAguTa);


        if (!empty($req->input('tipe_lokasi_agunan'))) {

            for ($i = 0; $i < count($req->input('tipe_lokasi_agunan')); $i++) {

                $daAguTa[] = [
                    'tipe_lokasi'
                    => empty($req->tipe_lokasi_agunan[$i])
                        ? null : strtoupper($req->tipe_lokasi_agunan[$i]),

                    'alamat'
                    => empty($req->alamat_agunan[$i])
                        ? null : $req->alamat_agunan[$i],

                    'id_provinsi'
                    => empty($req->id_prov_agunan[$i])
                        ? null : $req->id_prov_agunan[$i],

                    'id_kabupaten'
                    => empty($req->id_kab_agunan[$i])
                        ? null : $req->id_kab_agunan[$i],

                    'id_kecamatan'
                    => empty($req->id_kec_agunan[$i])
                        ? null : $req->id_kec_agunan[$i],

                    'id_kelurahan'
                    => empty($req->id_kel_agunan[$i])
                        ? null : $req->id_kel_agunan[$i],

                    'rt'
                    => empty($req->rt_agunan[$i])
                        ? null : $req->rt_agunan[$i],

                    'rw'
                    => empty($req->rw_agunan[$i])
                        ? null : $req->rw_agunan[$i],

                    'luas_tanah'
                    => empty($req->luas_tanah[$i])
                        ? null : $req->luas_tanah[$i],

                    'luas_bangunan'
                    => empty($req->luas_bangunan[$i])
                        ? null : $req->luas_bangunan[$i],

                    'nama_pemilik_sertifikat'
                    => empty($req->nama_pemilik_sertifikat[$i])
                        ? null : $req->nama_pemilik_sertifikat[$i],

                    'jenis_sertifikat'
                    => empty($req->jenis_sertifikat[$i])
                        ? null : strtoupper($req->jenis_sertifikat[$i]),

                    'no_sertifikat'
                    => empty($req->no_sertifikat[$i])
                        ? null : $req->no_sertifikat[$i],

                    'tgl_ukur_sertifikat'
                    => empty($req->tgl_ukur_sertifikat[$i])
                        ? null : $req->tgl_ukur_sertifikat[$i],

                    'tgl_berlaku_shgb'
                    => empty($req->tgl_berlaku_shgb[$i])
                        ? null : Carbon::parse($req->tgl_berlaku_shgb[$i])->format('Y-m-d'),

                    'no_imb'
                    => empty($req->no_imb[$i])
                        ? null : $req->no_imb[$i],

                    'njop'
                    => empty($req->njop[$i])
                        ? null : $req->njop[$i],

                    'nop'
                    => empty($req->nop[$i])
                        ? null : $req->nop[$i],
                    // 'lam_imb'                 => empty($req->file('lam_imb')[$i]) ? null : Helper::img64enc($req->file('lam_imb')[$i]),
                    'agunan_bag_depan'
                    => empty($agunan_bag_depan[$i])
                        ? null : $agunan_bag_depan[$i],

                    'agunan_bag_jalan'
                    => empty($agunan_bag_jalan[$i])
                        ? null : $agunan_bag_jalan[$i],

                    'agunan_bag_ruangtamu'
                    => empty($agunan_bag_ruangtamu[$i])
                        ? null : $agunan_bag_ruangtamu[$i],

                    'agunan_bag_kamarmandi'
                    => empty($agunan_bag_kamarmandi[$i])
                        ? null : $agunan_bag_kamarmandi[$i],

                    'agunan_bag_dapur'
                    => empty($agunan_bag_dapur[$i])
                        ? null : $agunan_bag_dapur[$i],

                    'lamp_imb'
                    => empty($lamp_imb_tan[$i])
                        ? null : $lamp_imb_tan[$i],

                    'lamp_pbb'
                    => empty($lamp_pbb_tan[$i])
                        ? null : $lamp_pbb_tan[$i],

                    'lamp_sertifikat'
                    => empty($lamp_sertifikat_tan[$i])
                        ? null : $lamp_sertifikat_tan[$i]
                ];
            }
        }


        if (!empty($req->input('no_bpkb_ken'))) {

            for ($i = 0; $i < count($req->input('no_bpkb_ken')); $i++) {

                $daAguKe[] = [
                    'no_bpkb'
                    => empty($req->no_bpkb_ken[$i])
                        ? null : $req->no_bpkb_ken[$i],

                    'nama_pemilik'
                    => empty($req->nama_pemilik_ken[$i])
                        ? null : $req->nama_pemilik_ken[$i],

                    'alamat_pemilik'
                    => empty($req->alamat_pemilik_ken[$i])
                        ? null : $req->alamat_pemilik_ken[$i],

                    'merk'
                    => empty($req->merk_ken[$i])
                        ? null : $req->merk_ken[$i],

                    'jenis'
                    => empty($req->jenis_ken[$i])
                        ? null : $req->jenis_ken[$i],

                    'no_rangka'
                    => empty($req->no_rangka_ken[$i])
                        ? null : $req->no_rangka_ken[$i],

                    'no_mesin'
                    => empty($req->no_mesin_ken[$i])
                        ? null : $req->no_mesin_ken[$i],

                    'warna'
                    => empty($req->warna_ken[$i])
                        ? null : $req->warna_ken[$i],

                    'tahun'
                    => empty($req->tahun_ken[$i])
                        ? null : $req->tahun_ken[$i],

                    'no_polisi'
                    => empty($req->no_polisi_ken[$i])
                        ? null : strtoupper($req->no_polisi_ken[$i]),

                    'no_stnk'
                    => empty($req->no_stnk_ken[$i])
                        ? null : $req->no_stnk_ken[$i],

                    'tgl_kadaluarsa_pajak'
                    => empty($req->tgl_exp_pajak_ken[$i])
                        ? null : Carbon::parse($req->tgl_exp_pajak_ken[$i])->format('Y-m-d'),

                    'tgl_kadaluarsa_stnk'
                    => empty($req->tgl_exp_stnk_ken[$i])
                        ? null : Carbon::parse($req->tgl_exp_stnk_ken[$i])->format('Y-m-d'),

                    'no_faktur'
                    => empty($req->no_faktur_ken[$i])
                        ? null : $req->no_faktur_ken[$i],

                    'lamp_agunan_depan'
                    => empty($lamp_agunan_depan_ken[$i])
                        ? null : $lamp_agunan_depan_ken[$i],

                    'lamp_agunan_kanan'
                    => empty($lamp_agunan_kanan_ken[$i])
                        ? null : $lamp_agunan_kanan_ken[$i],

                    'lamp_agunan_kiri'
                    => empty($lamp_agunan_kiri_ken[$i])
                        ? null : $lamp_agunan_kiri_ken[$i],

                    'lamp_agunan_belakang'
                    => empty($lamp_agunan_belakang_ken[$i])
                        ? null : $lamp_agunan_belakang_ken[$i],

                    'lamp_agunan_dalam'
                    => empty($lamp_agunan_dalam_ken[$i])
                        ? null : $lamp_agunan_dalam_ken[$i]
                ];

                $pemAguKe[] = [
                    'nama_pengguna'
                    => empty($req->nama_pengguna_ken[$i])
                        ? null : $req->nama_pengguna_ken[$i],

                    'status_pengguna'
                    => empty($req->status_pengguna_ken[$i])
                        ? null : strtoupper($req->status_pengguna_ken[$i]),

                    'jml_roda_kendaraan'
                    => empty($req->jml_roda_ken[$i])
                        ? null : $req->jml_roda_ken[$i],

                    'kondisi_kendaraan'
                    => empty($req->kondisi_ken[$i])
                        ? null : $req->kondisi_ken[$i],

                    'keberadaan_kendaraan'
                    => empty($req->keberadaan_ken[$i])
                        ? null : $req->keberadaan_ken[$i],

                    'body'
                    => empty($req->body_ken[$i])
                        ? null : $req->body_ken[$i],

                    'interior'
                    => empty($req->interior_ken[$i])
                        ? null : $req->interior_ken[$i],

                    'km'
                    => empty($req->km_ken[$i])
                        ? null : $req->km_ken[$i],

                    'modifikasi'
                    => empty($req->modifikasi_ken[$i])
                        ? null : $req->modifikasi_ken[$i],

                    'aksesoris'
                    => empty($req->aksesoris_ken[$i])
                        ? null : $req->aksesoris_ken[$i]
                ];
            }
        }

        // Start Kapasitas Bulanan
        $inputKapBul = array(

            'pemasukan_cadebt'      => $req->input('pemasukan_debitur'),
            'pemasukan_pasangan'    => $req->input('pemasukan_pasangan'),
            'pemasukan_penjamin'    => $req->input('pemasukan_penjamin'),
            'biaya_rumah_tangga'    => $req->input('biaya_rumah_tangga'),
            'biaya_transport'       => $req->input('biaya_transport'),
            'biaya_pendidikan'      => $req->input('biaya_pendidikan'),
            'telp_listr_air'        => $req->input('telp_listr_air'), // jangan lupa hampir sama dengan pendapatan usaha
            'angsuran'              => $req->input('angsuran'),
            'biaya_lain'            => $req->input('biaya_lain')
        );

        $total_KapBul = array(
            'total_pemasukan'    => $ttl1 = array_sum(array_slice($inputKapBul, 0, 3)),
            'total_pengeluaran'  => $ttl2 = array_sum(array_slice($inputKapBul, 3)),
            'penghasilan_bersih' => $ttl1 - $ttl2
        );

        $kapBul = array_merge($inputKapBul, $total_KapBul);
        // End Kapasitas Bulanan


        if (!empty($req->input('pemasukan_tunai'))) {
            // $dataKeUsaha = array(
            $inputKeUsaha = array(
                'pemasukan_tunai'
                => empty($req->input('pemasukan_tunai')) ? null
                    : (int) $req->input('pemasukan_tunai'),

                'pemasukan_kredit'
                => empty($req->input('pemasukan_kredit')) ? null
                    : (int) $req->input('pemasukan_kredit'),

                'biaya_sewa'
                => empty($req->input('biaya_sewa')) ? null
                    : (int) $req->input('biaya_sewa'),

                'biaya_gaji_pegawai'
                => empty($req->input('biaya_gaji_pegawai')) ? null
                    : (int) $req->input('biaya_gaji_pegawai'),

                'biaya_belanja_brg'
                => empty($req->input('biaya_belanja_brg')) ? null
                    : (int) $req->input('biaya_belanja_brg'),

                'biaya_telp_listr_air'
                => empty($req->input('biaya_telp_listr_air')) ? null
                    : (int) $req->input('biaya_telp_listr_air'),

                'biaya_sampah_kemanan'
                => empty($req->input('biaya_sampah_kemanan')) ? null
                    : (int) $req->input('biaya_sampah_kemanan'),

                'biaya_kirim_barang'
                => empty($req->input('biaya_kirim_barang')) ? null
                    : (int) $req->input('biaya_kirim_barang'),

                'biaya_hutang_dagang'
                => empty($req->input('biaya_hutang_dagang')) ? null
                    : (int) $req->input('biaya_hutang_dagang'),

                'biaya_angsuran'
                => empty($req->input('biaya_angsuran')) ? null
                    : (int) $req->input('biaya_angsuran'),

                'biaya_lain_lain'
                => empty($req->input('biaya_lain_lain')) ? null
                    : (int) $req->input('biaya_lain_lain')
            );

            $total_KeUsaha = array(
                'total_pemasukan'      => $ttl1 = array_sum(array_slice($inputKeUsaha, 0, 2)),
                'total_pengeluaran'    => $ttl2 = array_sum(array_slice($inputKeUsaha, 2)),
                'laba_usaha'           => $ttl1 - $ttl2
            );

            $dataKeUsaha = array_merge($inputKeUsaha, $total_KeUsaha);
        }

        // Lampiran Debitur
        if ($file = $req->file('lamp_ktp')) {
            $path = $lamp_dir . '/debitur';
            $name = 'ktp';

            $check_file = $check_lamp_ktp;

            $lamp_ktp = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_ktp = $check_lamp_ktp;
        }

        if ($file = $req->file('lamp_kk')) {
            $path = $lamp_dir . '/debitur';
            $name = 'kk';

            $check_file = $check_lamp_kk;

            $lamp_kk = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_kk = $check_lamp_kk;
        }

        if ($file = $req->file('lamp_sertifikat')) {
            $path = $lamp_dir . '/debitur';
            $name = 'sertifikat';

            $check_file = $check_lamp_sertifikat;

            $lamp_sertifikat = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_sertifikat = $check_lamp_sertifikat;
        }

        if ($file = $req->file('lamp_pbb')) {
            $path = $lamp_dir . '/debitur';
            $name = 'pbb';

            $check_file = $check_lamp_sttp_pbb;

            $lamp_sttp_pbb = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_sttp_pbb = $check_lamp_sttp_pbb;
        }

        if ($file = $req->file('lamp_imb')) {
            $path = $lamp_dir . '/debitur';
            $name = 'imb';

            $check_file = $check_lamp_imb;

            $lamp_imb = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_imb = $check_lamp_imb;
        }

        if ($file = $req->file('foto_agunan_rumah')) {
            $path = $lamp_dir . '/debitur';
            $name = 'foto_agunan_rumah';

            $check_file = $check_foto_agunan_rumah;

            $foto_agunan_rumah = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $foto_agunan_rumah = $check_foto_agunan_rumah;
        }

        if ($files = $req->file('lamp_buku_tabungan')) {
            $path = $lamp_dir . '/lamp_buku_tabungan';
            $name = 'lamp_buku_tabungan';

            $check_file = $check_lamp_buku_tabungan;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_buku_tabungan = implode(";", $arrayPath);
        } else {
            $lamp_buku_tabungan = $check_lamp_buku_tabungan;
        }

        if ($file = $req->file('lamp_skk')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_skk';

            $check_file = $check_lamp_skk;

            $lamp_skk = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_skk = $check_lamp_skk;
        }

        if ($files = $req->file('lamp_sku')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_sku';

            $check_file = $check_lamp_sku;

            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_sku = implode(";", $arrayPath);
        } else {
            $lamp_sku = $check_lamp_sku;
        }

        if ($file = $req->file('lamp_slip_gaji')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_slip_gaji'; //->getClientOriginalExtension();

            $check_file = $check_lamp_slip_gaji;

            $lamp_slip_gaji = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_slip_gaji = $check_lamp_slip_gaji;
        }


        if ($files = $req->file('foto_pembukuan_usaha')) {
            $path = $lamp_dir . '/debitur';
            $name = 'foto_pembukuan_usaha';

            $check_file = $check_foto_pembukuan_usaha;
            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $foto_pembukuan_usaha = implode(";", $arrayPath);
        } else {
            $foto_pembukuan_usaha = $check_foto_pembukuan_usaha;
        }

        if ($files = $req->file('lamp_foto_usaha')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_foto_usaha';

            $check_file = $check_lamp_foto_usaha;
            $arrayPath = array();
            foreach ($files as $file) {
                $arrayPath[] = Helper::uploadImg($check_file, $file, $path, $name);
            }

            $lamp_foto_usaha = implode(";", $arrayPath);
        } else {
            $lamp_foto_usaha = $check_lamp_foto_usaha;
        }

        if ($file = $req->file('lamp_surat_cerai')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_surat_cerai';

            $check_file = $check_lamp_surat_cerai;

            $lamp_surat_cerai = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_surat_cerai = $check_lamp_surat_cerai;
        }

        if ($file = $req->file('lamp_tempat_tinggal')) {
            $path = $lamp_dir . '/debitur';
            $name = 'lamp_tempat_tinggal';

            $check_file = $check_lamp_tempat_tinggal;

            $lamp_tempat_tinggal = Helper::uploadImg($check_file, $file, $path, $name);
        } else {
            $lamp_tempat_tinggal = $check_lamp_tempat_tinggal;
        }

        $cadebt = array(
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

            if (!empty($pemAguTa)) {
                $arrayPemTan = array();
                for ($i = 0; $i < count($pemAguTa); $i++) {
                    // $pemAguTa_N[$i] = array_merge(array('id_agunan_tanah' => $id_tanah['id'][$i]), $pemAguTa[$i]);

                    $pemTanah = PemeriksaanAgunTan::create($pemAguTa[$i]);

                    $id_pem_tan['id'][$i] = $pemTanah->id;

                    $arrayPemTan[] = $pemTanah;
                }

                $p_tanID = implode(",", $id_pem_tan['id']);
            } else {
                $arrayPemTan = null;
                $p_tanID = null;
            }

            if (!empty($daAguTa)) {
                $arrayTan = array();
                for ($i = 0; $i < count($daAguTa); $i++) {

                    $tanah = AgunanTanah::create($daAguTa[$i]);

                    $id_tanah['id'][$i] = $tanah->id;

                    $arrayTan[] = $tanah;
                }

                $tanID   = implode(",", $id_tanah['id']);
            } else {
                $arrayTan = null;
                $tanID   = null;
            }

            if (!empty($daAguKe)) {
                $arrayKen = array();
                $arrayPemKen = array();

                for ($i = 0; $i < count($daAguKe); $i++) {
                    $kendaraan = AgunanKendaraan::create($daAguKe[$i]);

                    $id_kendaraan['id'][$i] = $kendaraan->id;
                    $arrayKen[] = $kendaraan;
                }

                for ($i = 0; $i < count($pemAguKe); $i++) {
                    $pemAguKe_N[$i] = array_merge(array('id_agunan_kendaraan' => $id_kendaraan['id'][$i]), $pemAguKe[$i]);

                    $pemKendaraan = PemeriksaanAgunKen::create($pemAguKe_N[$i]);

                    $id_pem_ken['id'][$i] = $pemKendaraan->id;
                    $arrayPemKen[] = $pemKendaraan;
                }

                $kenID   = implode(",", $id_kendaraan['id']);
                $p_kenID = implode(",", $id_pem_ken['id']);
            } else {
                $arrayKen    = null;
                $arrayPemKen = null;
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
            } else {
                $keuangan = null;
                $id_usaha = null;
            }

            if (!empty($recom_AO)) {
                $recom = RekomendasiAO::create($recom_AO);
                $id_recom = $recom->id;
            } else {
                $recom = null;
                $id_recom = null;
            }

            $dataAO = array(
                'id_validasi'                 => $id_valid,
                'id_verifikasi'               => $id_verif,
                'id_agunan_tanah'             => $tanID,
                'id_agunan_kendaraan'         => $kenID,
                'id_periksa_agunan_tanah'     => $p_tanID,
                'id_periksa_agunan_kendaraan' => $p_kenID,
                'id_kapasitas_bulanan'        => $id_kapbul,
                'id_pendapatan_usaha'         => $id_usaha,
                'id_recom_ao'                 => $id_recom
            );

            $arrAO = array_merge($TransAO, $dataAO);

            $new_TransAO = TransAO::create($arrAO);

            TransSO::where('id', $id)->update(['id_trans_ao' => $new_TransAO->id]);

            Debitur::where('id', $check_so->id_calon_debitur)->update($cadebt);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message' => 'Data untuk AO berhasil dikirim',
                'data'   => [
                    'trans_ao'                      => $new_TransAO,
                    'agunan_tanah'                  => $arrayTan,
                    'pemeriksaaan_agunan_tanah'     => $arrayPemTan,
                    'agunan_kendaraan'              => $arrayKen,
                    'pemeriksaaan_agunan_kendaraan' => $arrayPemKen,
                    'validasi'                      => $valid,
                    'vierifikasi'                   => $verif,
                    'kapasitas_bulanan'             => $kap,
                    'pendapatan_usaha'              => $keuangan,
                    'rekomendasi_so'                => $recom
                ]
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
