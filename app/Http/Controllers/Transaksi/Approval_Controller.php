<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use App\Models\Pengajuan\AO\AgunanKendaraan;
use App\Http\Requests\Transaksi\ApprovalReq;
use App\Models\Pengajuan\AO\AgunanTanah;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\Approval;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransSO;
use App\Models\Karyawan\TeamCAA;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class Approval_Controller extends BaseController
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

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;
        $jpic      = $pic['nama_jenis'];

        // $query = Helper::checkDir($user_id, $jpic = $pic->jpic['nama_jenis'], $query_dir, $id_area, $id_cabang, $method);
        if($jpic == 'CRM' || $jpic == 'AM' || $jpic == 'CA'){

            $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) {
                    // Query the name field in status table
                    $q->where('nama_jenis', '=', 'DIR UT'); // '=' is optional
                    $q->orWhere('nama_jenis', '=', 'DIR BIS');
                    $q->orWhere('nama_jenis', '=', 'DIR RISK');
                    $q->orWhere('nama_jenis', '=', 'KEPATUHAN');
                    $q->orWhere('nama_jenis', '=', 'CRM');
                    $q->orWhere('nama_jenis', '=', 'CA');
                    $q->orWhere('nama_jenis', '=', 'AM');
                    $q->orWhere('nama_jenis', '=', 'PC');
                })
                ->where('flg_aktif', 1)
                ->where('id_area', $id_area)
                ->where('id', '!=', $pic->id)
                ->get();

        }elseif($jpic == 'PC'){

            $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) {
                    // Query the name field in status table
                    $q->where('nama_jenis', '=', 'DIR UT'); // '=' is optional
                    $q->orWhere('nama_jenis', '=', 'DIR BIS');
                    $q->orWhere('nama_jenis', '=', 'DIR RISK');
                    $q->orWhere('nama_jenis', '=', 'KEPATUHAN');
                    $q->orWhere('nama_jenis', '=', 'CRM');
                    $q->orWhere('nama_jenis', '=', 'CA');
                    $q->orWhere('nama_jenis', '=', 'AM');
                    $q->orWhere('nama_jenis', '=', 'PC');
                })
                ->where('flg_aktif', 1)
                ->where('id', '!=', $pic->id)
                ->where('id_area', $id_area)
                ->where('id_cabang', $id_cabang)
                ->get();
        }else{
            $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) {
                    // Query the name field in status table
                    $q->where('nama_jenis', '=', 'DIR UT'); // '=' is optional
                    $q->orWhere('nama_jenis', '=', 'DIR BIS');
                    $q->orWhere('nama_jenis', '=', 'DIR RISK');
                    $q->orWhere('nama_jenis', '=', 'KEPATUHAN');
                    $q->orWhere('nama_jenis', '=', 'CRM');
                    $q->orWhere('nama_jenis', '=', 'CA');
                    $q->orWhere('nama_jenis', '=', 'AM');
                    $q->orWhere('nama_jenis', '=', 'PC');
                })
                ->where('flg_aktif', 1)
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

    public function index($id, Request $req){
        $user_id = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();


        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."'. Yang berhak melihat halaman ini adalah Direktur, CRM, PC dan AM. Mohon cek dimenu Team CAA untuk validasi data anda atau silahkan hubungin tim IT"
            ], 404);
        }

        $id_area   = $pic->id_area;
        $id_cabang = $pic->id_cabang;

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
                'id_approval'    => $val->id,
                'id_trans_so'    => $val->id_trans_so,
                'user_id'        => $val->user_id,
                'nomor_so'       => $val->so['nomor_so'],
                'nomor_ao'       => $val->so['ao']['nomor_ao'],
                'nomor_ca'       => $val->so['ca']['nomor_ca'],
                'nomor_caa'      => $val->caa['nomor_caa'],
                'id_pic'         => $val->id_pic,
                'batas_plafon'   => $val->pic['plafon_caa'],
                'nama_pic'       => $val->pic['nama'],
                // 'id_jenis_pic'   => $val->pic['id_mj_pic'],
                'jabatan'        => $val->pic['jpic']['nama_jenis'],
                // 'urutan_jabatan' => $val->pic['jpic']['urutan_jabatan'],
                'plafon'         => $val->plafon,
                'tenor'          => $val->tenor,
                'rincian'        => $val->rincian,
                'status_approval'=> $status,
                'tanggal'        => empty($val->updated_at) ? null : Carbon::parse($val->updated_at)->format("d-m-Y H:i:s"),
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

    public function show($id, $id_approval, Request $req){
        $check_caa = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        if ($check_caa == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data yang akan anda eksekusi tidak ada, mohon cek URL anda"
            ], 404);
        }

        $val = Approval::where('id_trans_so', $id)->where('id', $id_approval)->first();

        if ($val == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data Approval masih kosong, mohon cek URL anda"
            ], 404);
        }

        if ($val->status) {
            $status = $val->status;
        }else{
            $status = 'waiting';
        }

        if($val->pic['jpic'] == 'DIR UT') {
            $list_status = array('accept', 'reject', 'return');
        }else{
            $list_status = array('accept', 'forward', 'reject', 'return');
        }

        $data = [
            'id_approval'    => $val->id,
            'id_trans_so'    => $val->id_trans_so,
            'user_id'        => $val->user_id,
            'nomor_so'       => $val->so['nomor_so'],
            'nomor_ao'       => $val->so['ao']['nomor_ao'],
            'nomor_ca'       => $val->so['ca']['nomor_ca'],
            'nomor_caa'      => $val->caa['nomor_caa'],
            'id_pic'         => $val->id_pic,
            'batas_plafon'   => $val->pic['plafon_caa'],
            'nama_pic'       => $val->pic['nama'],
            // 'id_jenis_pic'   => $val->pic['id_mj_pic'],
            'jabatan'        => $val->pic['jpic']['nama_jenis'],
            // 'urutan_jabatan' => $val->pic['jpic']['urutan_jabatan'],
            'plafon'         => $val->plafon,
            'tenor'          => $val->tenor,
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
        } catch (Exception $e) {
            return response()->json([
                "code"    => 501,
                "status"  => "error",
                "message" => $e
            ], 501);
        }
    }

    public function approve($id, $id_approval, Request $req, ApprovalReq $request){
        $user_id = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."'. Yang berhak melihat halaman ini adalah Direktur, CRM, PC dan AM. Mohon cek dimenu Team CAA untuk validasi data anda atau silahkan hubungin tim IT"
            ], 404);
        }

        // $check = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->where('pic_team_caa', 'like', "%{$pic->id}%")->first();

        $check = Approval::where('id', $id_approval)->first();

        if ($check == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data yang akan anda eksekusi tidak ada, mohon cek URL anda"
            ], 404);
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
            'status'        => $request->input('status'),
            'tujuan_forward'=> $request->input('tujuan_forward'),
            // 'tanggal'       => Carbon::now()->toDateTimeString()
        );

        DB::connection('web')->beginTransaction();

        try {

            if ($form['status'] == 'accept' || $form['status'] == 'reject') {
                $status = $form['status'].' by user '.$user_id;
                // TransCAA::where('id_trans_so', $id)->update(['status_team_caa' => $form['status'].' by user '.$user_id]);
            }elseif ($form['status'] == 'forward' || $form['status'] == 'return') {
                $status = $form['status'].' by picID '.$user_id.' to picID '.$form['tujuan_forward'];
            }

            if ($form['status'] == 'return') {
                TransCAA::where('id_trans_so', $id)->update(['status_caa' => 0, 'status_team_caa' => $status]);
            }

            TransCAA::where('id_trans_so', $check->id_trans_so)->update(['status_team_caa' => $status]);

            Approval::where('id', $id_approval)->update($form);

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

    // Team Caa
    public function report_approval($id){

        $check_caa = TransCAA::where('status_caa', 1)->where('id_trans_so', $id)->first();

        if ($check_caa == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data yang akan anda eksekusi tidak ada, mohon cek URL anda"
            ], 404);
        }

        $check_team = Approval::where('id_trans_so', $id)->whereIn('id_pic', explode(",", $check_caa->pic_team_caa))->get();

        if ($check_team == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "Data Approval masih kosong, mohon cek URL anda"
            ], 404);
        }

        $data = array();;
        foreach ($check_team as $key => $val) {
            $data[] = [
                'jabatan' => $val->pic['jpic']['nama_jenis'],
                'id_pic'  => $val->id_pic,
                'user_id' => $val->user_id,
                'nama_pic'=> $val->pic['nama'],
                'plafon'  => $val->plafon,
                'tenor'   => $val->plafon,
                'status'  => $val->status,
                'rincian' => $check_caa->rincian
            ];

            // $approved_user = array_search('accept', $data[$key], true);
        }

        $id_agu_ta = explode (",",$val->so['ao']['id_agunan_tanah']);

        $AguTa = AgunanTanah::whereIn('id', $id_agu_ta)->get();

        $idTan = array();
        foreach ($AguTa as $key => $value) {

            $idTan[$key] = $value->jenis_sertifikat .' / '. ($value->tgl_ukur_sertifikat == null ? 'null' : $value->tgl_ukur_sertifikat);
        }

        $imTan = implode("; ", $idTan);


        $url_in_array = in_array('accept', array_column($data, 'status'));

        if($url_in_array != true){

            $result = array(
                'id_transaksi' => $check_caa->id_trans_so,
                'debitur' => [
                    'id'   => $check_caa->so['id_calon_debitur'],
                    'nama' => $check_caa->so['debt']['nama_lengkap']
                ],
                'approved' => [
                    'id_pic'  => $check_caa->id_pic,
                    'user_id' => $check_caa->user_id,
                    'nama'    => $check_caa->pic['nama'],
                    'tenor'   => null,
                    'plafon'  => null,
                    'jaminan' => $imTan
                ],
                'list_approver' => $data
            );

        }else{

            $num_sts = array_search('accept', array_column($data, 'status'), true);

            $result = array(
                'id_transaksi' => $check_caa->id_trans_so,
                'debitur' => [
                    'id'   => $check_caa->so['id_calon_debitur'],
                    'nama' => $check_caa->so['debt']['nama_lengkap']
                ],
                'approved' => [
                    'id_pic'  => $check_caa->id_pic,
                    'user_id' => $check_caa->user_id,
                    'nama'    => $check_caa->pic['nama'],
                    'tenor'   => $data[$num_sts]['tenor'],
                    'plafon'  => $data[$num_sts]['plafon'],
                    'jaminan' => $imTan
                ],
                'list_approver' => $data
            );
        }


        try{
            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> $result
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
