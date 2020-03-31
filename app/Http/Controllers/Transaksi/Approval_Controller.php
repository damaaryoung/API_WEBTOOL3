<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
// use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Http\Requests\Transaksi\ApprovalReq;
use App\Models\Pengajuan\CAA\Penyimpangan;
use App\Models\Pengajuan\AO\AgunanTanah;
// use Illuminate\Support\Facades\File;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransCA;
use App\Models\Transaksi\TransAO;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class Approval_Controller extends BaseController
{
    public function list_team(Request $req)
    {
        $pic = $req->pic; // From PIC middleware

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $scope     = $pic->jpic['cakupan'];

        $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) {
                    // Query the name field in status table
                    $q->where('bagian', '=', 'team_caa');
                })
                ->where('flg_aktif', 1);

        if($scope == 'CABANG'){

            $parQuery = $query->whereHas('cabang', function($q) use($id_cabang) {
                                $q->where('id', $id_cabang);
                                $q->orWhere('nama', 'Pusat');
                            })
                            ->get()
                            ->sortByDesc('jpic.urutan_jabatan');

        }elseif($scope == 'AREA'){

            $parQuery = $query->whereHas('area', function($q) use($id_area) {
                                $q->where('id', $id_area);
                                $q->orWhere('nama', 'Pusat');
                            })
                            ->get()
                            ->sortByDesc('jpic.urutan_jabatan');

        }else{

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
            }else{
                $checked = false;
            }

            $data[] = array(
                "id"        => $val->id == null ? null : (int) $val->id,
                "user_id"   => $val->user_id,
                "plafon_max"=> (int) $val->plafon_caa,
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

        $val = PIC::with(['jpic', 'area','cabang'])
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
            "plafon_max"=> (int) $val->plafon_caa,
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
            }else{
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
                'status_approval'=> $status,
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
                "message" => 'Transaksi dengan id '.$id.' belum sampai ke CAA'
            ], 404);
        }

        $val = Approval::where('id_trans_so', $id)->where('id', $id_approval)->first();

        if ($val == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => 'Transaksi dengan id '.$id.' belum sampai ke Approval'
            ], 404);
        }

        if ($val->status) {
            $status = $val->status;
        }else{
            $status = 'waiting';
        }

        if($val->so['faspin']['plafon'] <= $val->pic['plafon_caa']) {

            $list_status = array('accept' => true, 'forward' => false, 'reject' => true, 'return' => true);

        }else{

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
            'status_approval'=> $status,
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
        $pic = $req->pic; // From PIC middleware

        $check_so = TransSO::where('id', $id)->first();

        if ($check_so == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum ada di SO"
            ], 404);
        }

        $check_ao = TransAO::where('id_trans_so', $id)->where('status_ao', 1)->first();

        if ($check_ao == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum sampai ke AO"
            ], 404);
        }

        $check_ca = TransCA::where('id_trans_so', $id)->where('status_ca', 1)->first();

        if ($check_ca == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum sampai ke ca"
            ], 404);
        }

        $check_caa = TransCAA::where('id_trans_so', $id)->where('status_caa', 1)->first();

        if ($check_caa == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum sampai ke caa"
            ], 404);
        }

        $check = Approval::where('id', $id_approval)->where('id_trans_so', $id)->first();

        if ($check == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." dan dengan id approval ".$id_approval." tidak ada di daftar antrian Approval"
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
        }else{
            $to_forward = $forward_q->id_pic;
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;

        $form = array(
            'user_id'       => $req->auth->user_id,
            'id_area'       => $id_area,
            'id_cabang'     => $id_cabang,
            'plafon'        => $request->input('plafon'),
            'tenor'         => $request->input('tenor'),
            'rincian'       => $request->input('rincian'),
            'status'        => $st = $request->input('status'),
            'tujuan_forward'=> $st == 'forward' ? $to_forward : null //$request->input('tujuan_forward'),
            // 'tanggal'       => Carbon::now()->toDateTimeString()
        );

        DB::connection('web')->beginTransaction();

        try {


            if ($form['status'] == 'accept' || $form['status'] == 'reject' || $form['status'] == 'return') {
                $status = $form['status']." by picID {$pic->id}";
                // TransCAA::where('id_trans_so', $id)->update(['status_team_caa' => $form['status'].' by user '.$user_id]);
            }elseif ($form['status'] == 'forward') {
                $status = $form['status']." by picID {$pic->id} to picID {$form['tujuan_forward']}";
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
                'message'=> 'Data untuk berhasil di - '.$form['status'],
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
                "message" => "Transaksi dengan id ".$id." belum ada di SO"
            ], 404);
        }

        $check_ao = TransAO::where('status_ao', 1)->where('id_trans_so', $id)->first();

        if (empty($check_ao)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum sampai ke AO"
            ], 404);
        }

        $check_ca = TransCA::where('status_ca', 1)->where('id_trans_so', $id)->latest()->first();

        if (empty($check_ca)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum sampai ke CA"
            ], 404);
        }

        $check_caa = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        if (empty($check_caa)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum sampai ke CAA"
            ], 404);
        }


        $check_team = Approval::where('id_trans_so', $id)->whereIn('id_pic', explode(",", $check_caa->pic_team_caa))->get();

        if (empty($check_team)) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Transaksi dengan id ".$id." belum sampai ke Approval"
            ], 404);
        }

        $data = array();;
        foreach ($check_team as $key => $val) {
            $data[] = [
                'jabatan' => $val->pic['jpic']['nama_jenis'],
                'id_pic'  => $val->id_pic  == null ? null : (int) $val->id_pic,
                'user_id' => $val->user_id == null ? null : (int) $val->user_id,
                'nama_pic'=> $val->pic['nama'],
                'plafon'  => $val->plafon,
                'tenor'   => $val->tenor,
                'status'  => $val->status,
                'rincian' => $val->rincian
            ];
        }

        $AguTa = AgunanTanah::whereIn('id', explode(",", $check_ao->id_agunan_tanah))->get();

        $idTan = array();
        foreach ($AguTa as $key => $value) {

            $idTan[$key] = $value->jenis_sertifikat .' / '. ($value->tgl_ukur_sertifikat == null ? 'null' : $value->tgl_ukur_sertifikat);
        }

        $imTan = implode("; ", $idTan);


        // Agunan Kendaraan
        $AguKe = AgunanKendaraan::whereIn('id', explode (",",$check_ao->id_agunan_kendaraan))->get();

        $idKen = array();
        foreach ($AguKe as $key => $value) {

            $idKen[$key] = 'BPKB / '. ($value->no_bpkb == null ? 'null' : $value->no_bpkb);
        }

        $imKen = implode("; ", $idKen);


        if ($imTan == "" && $imKen == "") {
            $jaminan = null;
        }elseif($imTan != "" && $imKen != ""){
            $jaminan = $imTan.'; '.$imKen;
        }elseif($imTan == "" && $imKen != ""){
            $jaminan = $imKen;
        }elseif($imTan != "" && $imKen == ""){
            $jaminan = $imTan;
        }

        // $url_in_array = in_array('accept', $status_in_array);


        $num_sts = array_search('accept', array_column($data, 'status'), true);;

        if ($num_sts == false) {
            $tenor  = null;
            $plafon = null;
        }else{
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


        try{
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> $result
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
