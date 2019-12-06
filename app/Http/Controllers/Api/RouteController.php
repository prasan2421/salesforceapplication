<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Customer;

use App\Route;

use App\RouteUser;

use App\Helpers\Common;

use stdClass;

class RouteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('app.version');
        $this->middleware('token');
    }

    /**
     * Get all routes.
     *
     * @return \Illuminate\Http\Response
     */
    public function getRoutes()
    {
        // if(request()->user->role == 'sales-officer') {
        //     $routes = Route::whereHas('users', function($query) {
        //         $query->where('_id', request()->user->_id);
        //     })
        //     ->select('id', 'name')
        //     ->get();
        // }
        // else {
            $routeIds = RouteUser::where('user_id', request()->user->_id)
                            ->where('is_active', true)
                            ->pluck('route_id');

            $routes = Route::whereIn('_id', $routeIds)
                            ->select('id', 'name')
                            ->get();
        // }

        return response()->json([
            'success' => true,
            'data' => [
                'routes' => $routes
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get today's routes.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTodayRoutes()
    {
        // if(request()->user->role == 'sales-officer') {
        //     $routes = Route::whereHas('users', function($query) {
        //         $query->where('_id', request()->user->_id);
        //     })
        //     ->select('id', 'name')
        //     ->get();
        // }
        // else {
            $routeIds = RouteUser::where('user_id', request()->user->_id)
                            ->where('is_active', true)
                            ->where(function($query) {
                                $query->where('frequency', 'daily')
                                    ->orWhere(function($query1){
                                        $query1->where('frequency', 'weekly')
                                                ->where('day', strtolower(date('l')));
                                    });
                            })
                            ->pluck('route_id');

            $routes = Route::whereIn('_id', $routeIds)
                            ->select('id', 'name')
                            ->get();
        // }

        return response()->json([
            'success' => true,
            'data' => [
                'routes' => $routes
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get route details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRouteDetails($id)
    {
        $route = Route::find($id);

        if(!$route) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $customers = $route->customers()
                        ->select('name')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $route->name,
                'customers' => $customers
            ],
            'errors' => new stdClass
        ]);
    }
}
