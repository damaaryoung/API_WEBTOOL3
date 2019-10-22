<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Carbon\Carbon;
use App\User;

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

        $user = User::where('user', $username)->first();

        if ($user == null) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Your Account not valid or not found !"
            ], 400);
        }

        $getNow = Carbon::now()->toDateTimeString();
        $Now = (int) Carbon::parse($getNow)->format('Ymd');

        $getExp = $user->tgl_expired;
        $Exp = (int) Carbon::parse($getExp)->format('Ymd');

        // Check request Uservame
        if (!$request->input('user') || empty($request->input('user'))) {
			return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Email must be inputted"
            ], 400);
		}

		// Check request Password
		if (!$request->input('password') || empty($request->input('password'))) {
			return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Password must be inputted"
            ], 404);
		}

		// Check User
		if ($user == null) {
			return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Sorry you are not registered"
            ], 400);
		}

		// Check request Password valid or no
        if (md5($password) != $user->password) {
            return response()->json([
                "code"    => 400,
                "status"  => "bad request",
                "message" => "Wrong Password !"
            ], 400);
        }

        // Check Expired Account
        if ($Exp <= $Now) {
            return response()->json([
                "code"    => 403,
                "status"  => "Expired",
                'message' => "Your account has expired"
            ], 403);
        }

        // Login & Create Token
        if ($user->user == $username && $user->password == md5($password)){
            return response()->json([
                "code"   => 200,
                "status" => "success",
                'token'  => $this->jwt($user)
            ], 200);
        }
    }
}
