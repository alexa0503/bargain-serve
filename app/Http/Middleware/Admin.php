<?php

namespace App\Http\Middleware;

use Closure;

class Admin
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
        $admin = auth('admin')->user();
        if( $admin->sate == 0 ){
            return response()->json(['message'=>'无权限'], 403);
        }
        return $next($request);
    }
}
