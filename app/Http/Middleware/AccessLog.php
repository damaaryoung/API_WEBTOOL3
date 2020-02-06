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

        if($request->auth == null){
            $userID   = null;
            $userName = null;
        }else{
            $userID   = $request->auth->user_id;
            $userName = $request->auth->nama;
        }

        $route = $request->route();

        DB::connection('web')->table('access_logs')->insert([

            'subject'   => $route[1]['subject'], //$request->path(),
            'url'       => $request->getPathInfo(), // Or $request->fullUrl()
            'method'    => $request->getMethod(),
            'ip'        => $request->getClientIp(),
            'agent'     => $request->header('User-Agent'),
            'user_id'   => $userID,
            'login_name'=> $userName,
            'time'      => new DateTime
        ]);

        return $response;
    }
}
