<?php

namespace App\Http\Middleware;

use Closure;

use stdClass;

class CheckAppVersion
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
        // if(!$request->header('version')) {
        //     return response()->json([
        //         'success' => false,
        //         'data' => new stdClass,
        //         'errors' => [
        //             'version' => 'version_required'
        //         ]
        //     ]);
        // }

        if($request->header('version') && $request->header('version') < 1) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'version' => 'version_outdated'
                ]
            ]);
        }

        return $next($request);
    }
}
