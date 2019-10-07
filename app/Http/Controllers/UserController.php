<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Carbon\Carbon;
use App\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    
    private $request;

    protected function jwt(User $user) {
         $payload = [
         	'iss'      => "BPR Kredit Mandiri Indonesia",
            'id'       => $user->id,
            'username' => $user->user_name,
            'iat'      => time(), // created at
            'exp'      => time() + (60*60*24*7) //,expiresIn: '7d'
        ];
        return JWT::encode($payload, env('JWT_SECRET'));
    }

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function login(User $user, Request $request) {
        $username = $request->input('username');
        $password  = $request->input('password');

        $user = User::where('user_name', $username)->first();

        // Check request Uservame
        if (!$request->input('username') || empty($request->input('username'))) {
			return response()->json([
                "code" => "400",
                "status" => "bad request",
                "message" => "Username must be inputted"
            ], 400);
		}

		// Check request Password
		if (!$request->input('password') || empty($request->input('password'))) {
			return response()->json([
                "code" => "400",
                "status" => "bad request",
                "message" => "Password must be inputted"
            ], 404);
		}

		// Check User
		if ($user == null) {
			return response()->json([
                "code" => "400",
                "status" => "bad request",
                "message" => "Sorry you are not registered"
            ], 400);
		}

		// Check request Password valid or no
        if (md5($password) != $user->password) {
            return response()->json([
                "code"    => "400",
                "status"  => "bad request",
                "message" => "Wrong Password !"
            ], 400);
        }

        // Login & Create TOken
        if ($user->user_name == $username && $user->password == md5($password)){

            return response()->json([
                "code"   => "200",
                "status" => "success",
                'token'  => $this->jwt($user)
            ], 200);
        }
    }
}
