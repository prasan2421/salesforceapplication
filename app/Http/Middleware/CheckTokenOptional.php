<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

use stdClass;

class CheckTokenOptional
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
        if($request->header('x-auth')) {
            $user = User::where('token', $request->header('x-auth'))
                    ->where('is_active', true)
                    ->first();

            if($user) {
                $request->user = $user;
            }
        }     

        return $next($request);
    }
}
