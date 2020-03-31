<?php

namespace App\Http\Middleware;

use Laravel\Lumen\Routing\Controller as BaseController;
use Closure;
use DB;
use DateTime;
use App\Models\Master\PIC;

class PICMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userID   = $request->auth->user_id;

        $pic = PIC::where('user_id', $userID)->first();

        if ($pic == null) {
            return response()->json([
                "code"    => 404,
                "status"  => "not found",
                "message" => "User_ID anda adalah '".$userID."'. Namun anda belum terdaftar sebagai PIC. Harap daftarkan diri sebagai PIC pada form PIC atau hubungi bagian IT"
            ], 404);
        }

        $request->pic = $pic;

        return $next($request);
    }
}
