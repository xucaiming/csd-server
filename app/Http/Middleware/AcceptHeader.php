<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptHeader
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $request->headers->set('Accept', 'application/json'); // 为每个请求加上请求头用以返回json
        return $next($request);
    }
}
