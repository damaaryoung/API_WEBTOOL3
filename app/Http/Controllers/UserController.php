<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Http\Controllers\Controller as Helper;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use DB;

class UserController extends BaseController
{
    public function index(Request $req){
        $id = $req->auth->user_id;
        $data = User::where('user_id', '=', $id)->first();

        return response()->json([
            "code"   => 200,
            'status' => 'success',
            'data'   => $data
        ], 200);
    }

    // public function create(Request $req){
    //     $users = new User;
    //     $users['username'] = $req->input('username');
    //     $users['password'] = md5($req->input('password'));
    //     $users['level']    = $req->input('level');
    //     $users->save();

    //     if ($users->save()) {
    //         return response()->json([
    //             "code"   => 200,
    //             'status' => 'success',
    //             'message'=> 'Users has been cretaed',
    //             'data'   => $users
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             "code"    => 403,
    //             'status'  => 'error',
    //             'message' => 'Feiled Create Users'
    //         ], 403);
    //     }
    // }

    // public function getId($id){
    //     $user = User::where('id', $id)->get();

    //     return response()->json([
    //         "code"   => 200,
    //         'status' => 'success',
    //         'data'   => $user
    //     ], 200);
    // }

    // public function update(Request $req, $id){
    //     $users = User::find($id);
    //     $users['username'] = $req->input('username');
    //     $users['password'] = md5($req->input('password'));
    //     $users['level']    = $req->input('level');
    //     $users->save();

    //     if ($users->save()) {
    //         return response()->json([
    //             "code"   => 200,
    //             'status' => 'success',
    //             'message'=> 'Users has been updated',
    //             'data'   => $users
    //         ], 200);
    //     } else {
    //         return response()->json([
    //             "code"    => 403,
    //             'status'  => 'error',
    //             'message' => 'Feiled Update Users'
    //         ], 403);
    //     }
    // }

    // public function delete(Request $req, $id){
    //     $users = User::find($id);
    //     $users->delete();

    //     try {
    //         return response()->json([
    //             "code"    => 200,
    //             'status'  => 'success',
    //             'message' => 'User with id '.$id.' successfully deleted'
    //         ], 200);
    //     } catch(Exception $e) {
    //         return response()->json([
    //             "code"    => 403,
    //             'status'  => 'error',
    //             'message' => 'Failed Delete a User'
    //         ], 403);
    //     }
    // }

    public function flagAuthor(Request $req, Helper $help) {
        $id   = $req->auth->user_id; // 855 -> indra
        $user = User::select('reg_id_gcm')->where('user_id', $id)->first();
        // $fcm_token=$user->reg_id_gcm;

        $Now = Carbon::now()->toDateTimeString();

        $data = DB::connection('dpm')->table('flg_otorisasi')
            ->where('user_id', $id)
            ->where('otorisasi', 0)
            ->orderBy('tgl','asc')
            ->orderBy('jam', 'asc')
            ->first(); // 167346

        if($data == null){
            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'Empty Data'
            ], 200);
        }

        $update = DB::connection('dpm')->table('flg_otorisasi')
        ->where('user_id', $id)
        ->where('id', $data->id)
        ->update([
            'otorisasi' => 1,
            'waktu_otorisasi' => $Now
        ]);

        // $title   = 'Permintaan otorisasi anda telah disetujui';
        // $message = $data->pesan;

        try {
            // // $inData = $help->push_notif($fcm_token, $title, $message);
            // // $outData = json_decode($inData, true);

            // // if($outData['success'] == 1){
            // //     $update = DB::table('flg_otorisasi')
            // //     ->where('user_id', $id)
            // //     ->where('id', $data->id)
            // //     ->update([
            // //         'sent_android' => 1
            // //     ]);

            //     return response()->json([
            //         "code"    => 200,
            //         'status'  => 'success',
            //         'message' => 'authorization and sent android update to 1 succeeded'
            //     ], 200);
            // }

            return response()->json([
                "code"    => 200,
                'status'  => 'success',
                'message' => 'authorization update to 1 succeeded'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 403,
                'status'  => 'error',
                'message' => 'authorization update failed!!'
            ], 403);
        }
    }

    public function resetPassword(Helper $help,Request $req){
        $hp = $req->input('no_hp');
        $check_hp = User::where('no_hp', $hp)->first();

        if ($check_hp == null) {
            return response()->json([
               'message' => 'No HP salah atau belum terdaftar !'
            ], 400);
        }else{
            $kode_otp = rand(1000, 999999);

            $msg_otp = 'Your Password baru anda: '.$kode_otp;

            $inData = $help->OTP($hp, $msg_otp);
            $outData = json_decode($inData, true);
            $xData = $outData['messages'][0]['smsCount'];

            if ($xData == 1) {
                User::where('no_hp', $hp)
                    ->update(['no_hp_verified' => 1, 'password' => md5($kode_otp)]);

                return response()->json([
                   'message' => 'Reset password sukses'
                ], 200);
            }else{
                return response()->json([
                    'message'=> 'Gagal mereset password via OTP, periksa jaringan anda dan silahkan coba kembali'
                ], 400);
            }
        }
    }

    public function changePassword(Request $req) {
        $id = $req->auth->user_id;

        $check = User::where('user_id', $id)->first();
        $originalPass = $check->password;

        // dd($originalPass);

        $oldPass     = $req->input('password_lama');
        $newPass     = $req->input('password_baru');
        $confirmPass = $req->input('konfirmasi_password');

        if (md5($oldPass) != $originalPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "wrong old password!!"
            ], 400);
        }

        if ($newPass != $confirmPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "passwor is not the same!!"
            ], 400);
        }

        try {
            $query = User::where('user_id', $id)->update(['password' => md5($newPass)]);
            return response()->json([
                "code"    => 200,
                "status"  => "success",
                "message" => "The password was updated successfully"
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                "code"    => 403,
                "status"  => "error",
                "message" => "The password failed to update!!"
            ], 403);
        }
    }
}
