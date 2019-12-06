<?php

namespace App\Http\Middleware;

use Closure;

use App\User;

use stdClass;

class CheckToken
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
        if(!$request->header('x-auth')) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'token' => 'token_required'
                ]
            ]);
        }

        $user = User::where('token', $request->header('x-auth'))
                    ->where('is_active', true)
                    ->first();
        if(!$user) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'token' => 'token_invalid'
                ]
            ]);
        }

        $request->user = $user;

        return $next($request);
    }
}
