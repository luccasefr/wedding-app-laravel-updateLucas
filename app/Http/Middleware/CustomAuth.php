<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Guest;

class CustomAuth
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
        if($request->user('api')!==null || Guest::guest()!==null){
            if($request->user('api')!==null){
                Auth::login($request->user('api'));
            }
            return $next($request);

        }else {
            return response(["message"=>"Unauthenticated."],401);
        }
    }
}
