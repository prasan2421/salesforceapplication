<?php

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {
        $roles = explode('|', $role);
        if(!$request->user() || !in_array($request->user()->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
