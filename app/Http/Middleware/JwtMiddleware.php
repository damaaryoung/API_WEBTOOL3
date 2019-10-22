<?php

namespace App\Http\Middleware;

use App\User;
use Closure;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class JwtMiddleware
{
    public function handle($request, Closure $next, $guard = null)
    {
        $token = $request->header('Authorization');

        if(!$token) {
            // Unauthorized response if token not there
            return response()->json([
                'code'    => 401,
                'status'  => 'Error',
                'message' => 'Token required.'
            ], 401);
        }elseif(preg_match("/Bearer /", $token)){

            $strToken = str_replace('Bearer ','', $token);

            try {
                $credentials = JWT::decode($strToken, env('JWT_SECRET'), ['HS256']);

            } catch(ExpiredException $e) {

                return response()->json([
                    'code'    => 401,
                    'status'  => 'Error',
                    'message' => 'Provided token is expired.'
                ], 401);

            } catch(Exception $e) {
                return response()->json([
                    'code'    => 401,
                    'status'  => 'Error',
                    'message' => 'An error while decoding token.'
                ], 401);
            }

            // check validitas request
            $user = User::find($credentials->id);

            // Now let's put the user in the request class so that you can grab it from there
            if(!empty($user)){

                $request->auth = $user;

            }else{

                return response()->json([
                    'code'    => 401,
                    'status'  => 'Error',
                    'message' => 'Provided token is invalid.'
                ], 401);
            }

            return $next($request);

        }else{
            return response()->json([
                'code'    => 401,
                'status'  => 'Error',
                'message' => 'Token wrong !!!.'
            ], 401);
        }
    }

    protected $except = [
        //'api/something/biller'
        // 'user/verify/*'
        // 'stripe/*',
        // 'http://example.com/foo/bar',
        // 'http://example.com/foo/*',
    ];
}
