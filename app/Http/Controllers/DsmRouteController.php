<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

use App\Route;

use App\RouteUser;

use App\State;

use App\User;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

class DsmRouteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|sales-officer', [ 'except' => [ 'destroy' ] ]);
        $this->middleware('role:admin', [ 'only' => [ 'destroy' ] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function index($user_id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        return view('dsm-routes.index',[
            'user' => $user
        ]);
    }

    public function getData($user_id) {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $pipeline = [
            [
                '$addFields' => [
                    'route_id' => [
                        '$toObjectId' => '$route_id'
                    ],
                    'created_at' => [
                        '$dateToString' => [
                            'date' => '$created_at',
                            'format' => '%Y-%m-%d %H:%M:%S',
                            'timezone' => config('app.timezone')
                        ]
                    ],
                    'updated_at' => [
                        '$dateToString' => [
                            'date' => '$updated_at',
                            'format' => '%Y-%m-%d %H:%M:%S',
                            'timezone' => config('app.timezone')
                        ]
                    ]
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'routes',
                    'localField' => 'route_id',
                    'foreignField' => '_id',
                    'as' => 'route'
                ]
            ],
            [
                '$unwind' => [
                    'path' => '$route',
                    'preserveNullAndEmptyArrays' => true
                ]
            ],
            [
                '$project' => [
                    '_id' => 1,
                    'frequency' => 1,
                    'day' => 1,
                    'user_id' => 1,
                    'is_active' => 1,
                    'created_at' => 1,
                    'updated_at' => 1,
                    'route_sap_code' => [
                        '$ifNull' => [ '$route.sap_code', '' ]
                    ],
                    'route_name' => [
                        '$ifNull' => [ '$route.name', '' ]
                    ]
                ]
            ],
            [
                '$match' => [
                    'user_id' => $user_id
                ]
            ]
        ];

        return DataTableHelper::aggregate('route_user', $pipeline)
                ->addColumn('is_active', function($model) {
                    return '<i class="fas ' . ($model->is_active ? 'fa-check' : 'fa-times') . '"></i>';
                })
                ->addColumn('action', function($model) use ($user_id) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('DsmRouteController@show', ['user_id' => $user_id, 'id' => $model->_id]) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('DsmRouteController@edit', ['user_id' => $user_id, 'id' => $model->_id]) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    if($model->is_active) {
                        $content .= '<a href="' . action('DsmRouteController@markInactive', ['user_id' => $user_id, 'id' => $model->_id]) . '" class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Mark as Inactive"><i class="fas fa-times"></i></a>';
                    }
                    else {
                        $content .= '<a href="' . action('DsmRouteController@markActive', ['user_id' => $user_id, 'id' => $model->_id]) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Mark as Active"><i class="fas fa-check"></i></a>';
                    }

                    if(request()->user()->role == 'admin') {
                        $content .= Form::open(['action' => ['DsmRouteController@destroy', $user_id, $model->_id], 'method' => 'DELETE', 'style' => 'display: inline;']);
                        $content .= '<button class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" type="submit" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fas fa-trash-alt"></i></button>';
                        $content .= Form::close();
                    }

                    $content .= '</div>';

                    return $content;
                })
                ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function create($user_id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        // $routesQuery = Route::query();

        // if(request()->user()->role == 'sales-officer') {
        //     $routesQuery->whereHas('users', function($query) {
        //         $query->where('_id', request()->user()->_id);
        //     });
        // }

        // $routes = $routesQuery->pluck('name', '_id');

        $states = State::orderBy('name')->pluck('name', '_id');
        
        $frequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            // 'fortnightly' => 'Fortnightly'
        ];

        $days = [
            'sunday' => 'Sunday',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday'
        ];

        return view('dsm-routes.create', [
            'user' => $user,
            // 'routes' => $routes,
            'states' => $states,
            'frequencies' => $frequencies,
            'days' => $days
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $user_id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $request->validate([
            'route_id' => 'required|exists:routes,_id',
            'frequency' => [
                'required',
                Rule::in(['daily', 'weekly', /*'fortnightly'*/])
            ],
            'day' => [
                'nullable',
                Rule::in(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])
            ],
        ]);

        $routeUser = new RouteUser;
        $routeUser->user_id = $user_id;
        $routeUser->route_id = Common::nullIfEmpty($request->route_id);
        $routeUser->frequency = Common::nullIfEmpty($request->frequency);
        $routeUser->day = Common::nullIfEmpty($request->day);
        $routeUser->is_active = true;
        $routeUser->save();

        if($user->salesOfficer) {
            $user->salesOfficer->routes()->attach($request->route_id);
        }

        if($user->distributor) {
            $user->distributor->routes()->attach($request->route_id);
        }

        return redirect()
                ->action('DsmRouteController@index', $user_id)
                ->with('success', 'DSM Beat added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $user_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($user_id, $id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $routeUser = RouteUser::findOrFail($id);

        return view('dsm-routes.show', [
            'user' => $user,
            'routeUser' => $routeUser
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $user_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($user_id, $id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $routeUser = RouteUser::findOrFail($id);

        // $routesQuery = Route::query();

        // if(request()->user()->role == 'sales-officer') {
        //     $routesQuery->whereHas('users', function($query) {
        //         $query->where('_id', request()->user()->_id);
        //     });
        // }

        // $routes = $routesQuery->pluck('name', '_id');

        $states = State::orderBy('name')->pluck('name', '_id');

        $frequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            // 'fortnightly' => 'Fortnightly'
        ];
        
        $days = [
            'sunday' => 'Sunday',
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday'
        ];

        return view('dsm-routes.edit', [
            'user' => $user,
            'routeUser' => $routeUser,
            // 'routes' => $routes,
            'states' => $states,
            'frequencies' => $frequencies,
            'days' => $days
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $user_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $user_id, $id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $routeUser = RouteUser::findOrFail($id);

        $request->validate([
            'route_id' => 'required|exists:routes,_id',
            'frequency' => [
                'required',
                Rule::in(['daily', 'weekly', /*'fortnightly'*/])
            ],
            'day' => [
                'nullable',
                Rule::in(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])
            ],
        ]);

        $routeUser->route_id = Common::nullIfEmpty($request->route_id);
        $routeUser->frequency = Common::nullIfEmpty($request->frequency);
        $routeUser->day = Common::nullIfEmpty($request->day);
        $routeUser->save();

        if($user->salesOfficer) {
            $user->salesOfficer->routes()->attach($request->route_id);
        }

        if($user->distributor) {
            $user->distributor->routes()->attach($request->route_id);
        }

        return redirect()
                ->action('DsmRouteController@index', $user_id)
                ->with('success', 'DSM Beat updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $user_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id, $id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $routeUser = RouteUser::findOrFail($id);

        $routeUser->delete();

        return redirect()
                ->action('DsmRouteController@index', $user_id)
                ->with('success', 'DSM Beat deleted successfully');
    }

    /**
     * Mark as active.
     *
     * @param  int  $user_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markActive($user_id, $id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $routeUser = RouteUser::findOrFail($id);

        $routeUser->is_active = true;
        $routeUser->save();

        return redirect()
                ->action('DsmRouteController@index', $user_id)
                ->with('success', 'DSM Beat marked as active successfully');
    }

    /**
     * Mark as inactive.
     *
     * @param  int  $user_id
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markInactive($user_id, $id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $user = $query->findOrFail($user_id);

        $routeUser = RouteUser::findOrFail($id);

        $routeUser->is_active = false;
        $routeUser->save();

        return redirect()
                ->action('DsmRouteController@index', $user_id)
                ->with('success', 'DSM Beat marked as inactive successfully');
    }
}
