<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class UserController extends BaseController
{
    public function getUsers(){
        // $data = User::where('flg_block', 'N')->get();
        $data = DB::connection('dpm')->select("SELECT user_id, user as username, level, kode_area, kd_cabang, nama, divisi_id, kode_jabatan, user_id_induk, email, no_hp  FROM user WHERE flg_block='N' AND tgl_expired > CURDATE() ORDER BY tgl_expired desc");

        return response()->json([
            "code"   => 200,
            'status' => 'success',
            'data'   => $data
        ], 200);
    }

    public function search($search){
        $data = DB::connection('dpm')->select("SELECT user_id, user as username, level, kode_area, kd_cabang, nama, divisi_id, kode_jabatan, user_id_induk, email, no_hp  FROM user WHERE flg_block='N' AND tgl_expired > CURDATE() AND nama LIKE '%".$search."%' ORDER BY tgl_expired desc");

        return response()->json([
            "code"   => 200,
            'status' => 'success',
            'data'   => $data
        ], 200);
    }

    public function index(Request $req){
        $user_id = $req->auth->user_id;

        $data = User::where('user_id', '=', $user_id)->first();

        return response()->json([
            "code"   => 200,
            'status' => 'success',
            'data'   => $data
        ], 200);
    }

    public function resetPassword(Helper $help,Request $req){
        $hp = $req->input('no_hp');

        if (!$hp) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "FIeld 'no_hp' harus diisi!!"
            ], 400);
        }

        $check_hp = User::where('no_hp', $hp)->first();

        if ($check_hp == null) {
            return response()->json([
                "code"    => 404,
                'status'  => 'not found',
                'message' => 'No. HP anda tidak terdaftar!!'
            ], 404);
        }else{
            $kode_otp = rand(1000, 999999);

            $msg_otp = 'Password baru anda adalah '.$kode_otp;

            $inData = $help->sendOTP($hp, $msg_otp);
            $outData = json_decode($inData, true);
            $xData = $outData['messages'][0]['smsCount'];

            if ($xData == 1) {
                User::where('no_hp', $hp)
                    ->update(['password' => md5($kode_otp)]);

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'message' => 'Reset password berhasil'
                ], 200);
            }else{
                return response()->json([
                    "code"    => 400,
                    'status'  => 'bad request',
                    'message' => 'cek koneksi seluler anda'
                ], 400);
            }
        }
    }

    public function changePassword(Request $req) {
        $id = $req->auth->user_id;

        $check = User::where('user_id', $id)->first();

        $originalPass = $check->password;

        $oldPass     = $req->input('password_lama');
        $newPass     = $req->input('password_baru');
        $confirmPass = $req->input('konfirmasi_password');

        if (!$oldPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'password_lama' harus diisi!!"
            ], 400);
        }

        if (md5($oldPass) != $originalPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Password lama salah!!"
            ], 400);
        }

        if (!$newPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'password_baru' harus diisi!!"
            ], 400);
        }

        if (!$confirmPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Field 'konfirmasi_password' harus diisi!!"
            ], 400);
        }

        if ($newPass != $confirmPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Konfirmasi password'tidak sama dengan password baru!!"
            ], 400);
        }

        try {
            $query = User::where('user_id', $id)->update(['password' => md5($newPass)]);
            return response()->json([
                "code"    => 200,
                "status"  => "success",
                "message" => "Password berhasil perbarui"
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
