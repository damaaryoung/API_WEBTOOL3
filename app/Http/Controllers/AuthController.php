<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $request;

    protected function jwt(User $user) {
         $payload = [
         	'iss'       => "BPR Kredit Mandiri Indonesia",
            'id'        => $user->user_id,
            'nik'       => $user->nik,
            'usename'   => $user->user,
            'kd_cabang' => $user->kd_cabang,
            'divisi_id' => $user->divisi_id,
            'jabatan'   => $user->jabatan,
            'email'     => $user->email,
            'nama'      => $user->nama,
            'iat'       => time(), // created at
            'exp'       => time() + (60*60*24*7) //,expiresIn: '7d'
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function login(User $user, Request $request) {
        $username = $request->input('user');
        $password = $request->input('password');
        $fcm_token= $request->input('fcm_token');

        $user = User::where('user', $username)->first();

        if ($user == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "bad request",
                "message" => "Akun tidak valid atau tidak ada !!"
            ], 404);
        }

        $getNow = Carbon::now()->toDateTimeString();
        $Now = (int) Carbon::parse($getNow)->format('Ymd');

        $getExp = $user->tgl_expired;
        $Exp = (int) Carbon::parse($getExp)->format('Ymd');

        // Check request Uservame
        if (!$request->input('user') || empty($request->input('user'))) {
			return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "Nama user harus diinput!!"
            ], 422);
		}

		// Check request Password
		if (!$request->input('password') || empty($request->input('password'))) {
			return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "Password harus diinput!!"
            ], 422);
		}

		// Check request Password valid or no
        if (md5($password) != $user->password) {
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "Password salah!!"
            ], 422);
        }

        // Check Expired Account
        if ($Exp <= $Now) {
            return response()->json([
                "code"    => 403,
                "status"  => "Expired",
                'message' => "Akun anda telah kadaluarsa"
            ], 403);
        }

        if($user->fcm_token && $user->fcm_token != $fcm_token){
            return response()->json([
                "code"    => 422,
                "status"  => "bad request",
                "message" => "Anda telah login di device lain"
            ], 422);
        }

        // if ($user->email == $reqEmail && $user->email_verified == '1'){

        //     DB::update('UPDATE kmi_user SET `imei`=?, fcm_token=? WHERE `email`=? ',[$reqImei, $reqFcm, $user->email]);

        //     return response()->json([
        //         'message'=>'Success Login',
        //         'token'=> $this->jwt($user)
        //     ], 200);
        // }else{
        //     return response()->json([
        //         "code"    => "400",
        //         "status"  => "bad request",
        //         "message" => "please verify email"
        //     ], 400);
        // }
        // Login & Create Token
        if ($user->user == $username && $user->password == md5($password)){

            User::where('user', $username)->update(['fcm_token' => $fcm_token]);

            try {
                return response()->json([
                    "code"   => 200,
                    "status" => "success",
                    "token"  => $this->jwt($user)
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
}
