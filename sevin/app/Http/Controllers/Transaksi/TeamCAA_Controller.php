<?php

namespace App\Http\Controllers\Transaksi;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Support\Facades\File;
use App\Models\Transaksi\TransTCAA;
use App\Models\Transaksi\TransCAA;
use App\Models\Transaksi\TransSO;
use App\Models\AreaKantor\PIC;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\User;
use Carbon\Carbon;
use DB;

class TeamCAA_Controller extends BaseController
{
    public function list_team(Request $req) {
        $user_id = $req->auth->user_id;
        $pic     = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai PIC(CAA). Harap daftarkan diri sebagai PIC(CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        $value = 'Team CAA';
        $value_dir = 'Team CAA DIR';

        if ($id_cabang == 0) {

            $query = PIC::with(['jpic', 'area','cabang'])
                ->whereHas('jpic', function($q) use($value, $value_dir) {
                    // Query the name field in status table
                    $q->where('keterangan', '=', $value); // '=' is optional
                    $q->orWhere('keterangan', '=', $value_dir);
                })
                ->get();

        }elseif ($id_cabang != 0) {

            $query = PIC::with(['jpic', 'area','cabang'])
                    ->whereHas('jpic', function($q) use($value, $value_dir) {
                        // Query the name field in status table
                        $q->where('keterangan', '=', $value); // '=' is optional
                        $q->orWhere('keterangan', '=', $value_dir);
                    })
                    ->where('id_mk_cabang', $id_cabang)
                    ->orWhere('id_mk_cabang', 0)
                    ->get();
        }


        if ($query == '[]') {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        foreach ($query as $key => $val) {
            $data[$key]= [
                "id"          => $val->id,
                "nama"        => $val->nama,
                "jenis_pic"   => $val->jpic['nama_jenis'],
                "nama_area"   => $val->area['nama'],
                "nama_cabang" => $val->cabang['nama'],
                "email"       => $val->user['email']
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

    public function approve($id, Request $req){
        $user_id  = $req->auth->user_id;

        $pic = PIC::where('user_id', $user_id)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$user_id."' dengan username '".$req->auth->user."' . Namun anda belum terdaftar sebagai Team CAA. Harap daftarkan diri sebagai PIC(Team CAA) pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $id_cabang = $pic->id_mk_cabang;

        if ($id_cabang == 0) {

            $check = TransCAA::with('so', 'pic', 'cabang')
                ->where('id_trans_so', $id)
                ->where('status_caa', '!=', 1)
                ->first();

        }elseif ($id_cabang != 0) {

            $check = TransCAA::with('so', 'pic', 'cabang')
                ->where('id_cabang', $id_cabang)
                ->where('id_trans_so', $id)
                ->where('status_caa', 1)
                ->first();
        }


        if ($check == null) {
            return response()->json([
                'code'    => 404,
                'status'  => 'not found',
                'message' => 'Data kosong'
            ], 404);
        }

        // $count_tcaa = count(explode(",", $check->pic_team_caa));
        // $im_team = $check->status_team_caa;

        $status = $req->input('status');

        // dd(implode(",", [$im_team, $status]));

        if ($status == 'approve') {
            $status_caa = 1;
        }elseif($status == 'forward'){
            $status_caa = 2;
        }elseif($status == 'return'){
            $status_caa = 3;
        }elseif ($status == 'reject') {
            $status_caa = 4;
        }

        $field = array(
            'user_id' => $user_id,
            'id_pic'  => $pic->id,
            'plafon'  => $req->input('plafon'),
            'tenor'   => $req->input('tenor'),
            'rincian' => $req->input('rincian'),
            'status'  => $status_caa,
            'tanggal' => Carbon::now()->toDateString()
        );

        DB::connection('web')->beginTransaction();

        try {


            // if ($field['status'] == 'approve') {
                TransCAA::where('id_trans_so', $id)->update(['status_team_caa' => implode(",", [$im_team, $status])]);
            // }

            TransTCAA::where('id_trans_so', $id)
                ->where('id_pic', $pic->id)
                ->where('id_cabang', $id_cabang)
                ->update($field);

            DB::connection('web')->commit();

            return response()->json([
                'code'   => 200,
                'status' => 'success',
                'message'=> 'Data untuk CAA berhasil di'.$status
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
