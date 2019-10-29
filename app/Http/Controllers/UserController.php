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

    public function resetPassword(Helper $help,Request $req){
        $hp = $req->input('no_hp');

        if (!$hp) {
            return response()->json([
                "code"    => 400,
                'status'  => 'bad request',
                'message' => 'no_hp field is required!!'
            ], 400);
        }

        $check_hp = User::where('no_hp', $hp)->first();

        if ($check_hp == null) {
            return response()->json([
                "code"    => 400,
                'status'  => 'bad request',
                'message' => 'Your mobile number has not been registered!!'
            ], 400);
        }else{
            $kode_otp = rand(1000, 999999);

            $msg_otp = 'Your Password baru anda: '.$kode_otp;

            $inData = $help->sendOTP($hp, $msg_otp);
            $outData = json_decode($inData, true);
            $xData = $outData['messages'][0]['smsCount'];

            if ($xData == 1) {
                User::where('no_hp', $hp)
                    ->update(['password' => md5($kode_otp)]);

                return response()->json([
                    'code'    => 200,
                    'status'  => 'success',
                    'message' => 'Reset password sukses'
                ], 200);
            }else{
                return response()->json([
                    "code"    => 400,
                    'status'  => 'bad request',
                    'message'=> 'check your cellular network'
                ], 400);
            }
        }
    }

    public function changePassword(Request $req) {
        $id = $req->auth->user_id;

        $check = User::where('user_id', $id)->first();

        // dd($check);

        $originalPass = $check->password;

        $oldPass     = $req->input('password_lama');
        $newPass     = $req->input('password_baru');
        $confirmPass = $req->input('konfirmasi_password');

        if (!$oldPass) {
            return response()->json([
                "code"    => 400,
                'status'  => 'bad request',
                'message' => 'password_lama field is required!!'
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
                'status'  => 'bad request',
                'message' => 'password_baru field is required!!'
            ], 400);
        }

        if (!$confirmPass) {
            return response()->json([
                "code"    => 400,
                'status'  => 'bad request',
                'message' => 'konfirmasi_password field is required!!'
            ], 400);
        }

        if ($newPass != $confirmPass) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Confirm Password is not the same!!"
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
