<?php

namespace App\Http\Middleware;

use Laravel\Lumen\Routing\Controller as BaseController;
use Closure;
use DB;
use DateTime;

class AccessLog
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
        $response = $next($request);
        // buat log
        DB::table('access_logs')->insert([
        'subject' => $request->path(),
        // 'url' => $request->fullUrl(),
        // 'method' => $request->method(),
        'ip' => $request->getClientIp(),
        'agent' => $request->header('user-agent'),
        'user_id' => $request->auth->user_id,
        'created_at' => new DateTime,
        'updated_at' => new DateTime
        ]);

        return $response;
    }
}
