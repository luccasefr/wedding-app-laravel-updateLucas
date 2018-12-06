<?php

namespace App\Http\Middleware;

use Closure;
use App\Guest;

class AuthGuest
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
        if(Guest::guest()){
            return $next($request);
        }
        else {
            return response(["message"=>"Unauthenticated."],401);
        }
    }
}
