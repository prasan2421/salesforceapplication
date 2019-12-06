<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\URL;

class SetMineToUrls
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
        URL::defaults(['mine' => 1]);

        return $next($request);
    }
}
