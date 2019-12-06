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

class MyRouteController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:sales-officer');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('my-routes.index');
    }

    public function getData() {
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
                    'user_id' => request()->user()->_id
                ]
            ]
        ];

        return DataTableHelper::aggregate('route_user', $pipeline)
                ->addColumn('is_active', function($model) {
                    return '<i class="fas ' . ($model->is_active ? 'fa-check' : 'fa-times') . '"></i>';
                })
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('MyRouteController@show', $model->_id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('MyRouteController@edit', $model->_id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    if($model->is_active) {
                        $content .= '<a href="' . action('MyRouteController@markInactive', $model->_id) . '" class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Mark as Inactive"><i class="fas fa-times"></i></a>';
                    }
                    else {
                        $content .= '<a href="' . action('MyRouteController@markActive', $model->_id) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Mark as Active"><i class="fas fa-check"></i></a>';
                    }

                    // $content .= Form::open(['action' => ['MyRouteController@destroy', $model->_id], 'method' => 'DELETE', 'style' => 'display: inline;']);
                    // $content .= '<button class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" type="submit" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fas fa-trash-alt"></i></button>';
                    // $content .= Form::close();

                    $content .= '</div>';

                    return $content;
                })
                ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::orderBy('name')->pluck('name', '_id');
        
        $frequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly'
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

        return view('my-routes.create', [
            'states' => $states,
            'frequencies' => $frequencies,
            'days' => $days
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'route_id' => 'required|exists:routes,_id',
            'frequency' => [
                'required',
                Rule::in(['daily', 'weekly'])
            ],
            'day' => [
                'nullable',
                Rule::in(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'])
            ],
        ]);

        $routeUser = new RouteUser;
        $routeUser->user_id = request()->user()->_id;
        $routeUser->route_id = Common::nullIfEmpty($request->route_id);
        $routeUser->frequency = Common::nullIfEmpty($request->frequency);
        $routeUser->day = Common::nullIfEmpty($request->day);
        $routeUser->is_active = true;
        $routeUser->save();

        return redirect()
                ->action('MyRouteController@index')
                ->with('success', 'My Beat added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $routeUser = RouteUser::where('user_id', request()->user()->_id)->findOrFail($id);

        return view('my-routes.show', [
            'routeUser' => $routeUser
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $routeUser = RouteUser::where('user_id', request()->user()->_id)->findOrFail($id);

        $states = State::orderBy('name')->pluck('name', '_id');
        
        $frequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly'
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

        return view('my-routes.edit', [
            'routeUser' => $routeUser,
            'states' => $states,
            'frequencies' => $frequencies,
            'days' => $days
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $routeUser = RouteUser::where('user_id', request()->user()->_id)->findOrFail($id);

        $request->validate([
            'route_id' => 'required|exists:routes,_id',
            'frequency' => [
                'required',
                Rule::in(['daily', 'weekly'])
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

        return redirect()
                ->action('MyRouteController@index')
                ->with('success', 'My Beat updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $routeUser = RouteUser::where('user_id', request()->user()->_id)->findOrFail($id);

        // $routeUser->delete();

        // return redirect()
        //         ->action('MyRouteController@index')
        //         ->with('success', 'My Beat deleted successfully');
    }

    /**
     * Mark as active.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markActive($id)
    {
        $routeUser = RouteUser::where('user_id', request()->user()->_id)->findOrFail($id);

        $routeUser->is_active = true;
        $routeUser->save();

        return redirect()
                ->action('MyRouteController@index')
                ->with('success', 'My Beat marked as active successfully');
    }

    /**
     * Mark as inactive.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markInactive($id)
    {
        $routeUser = RouteUser::where('user_id', request()->user()->_id)->findOrFail($id);

        $routeUser->is_active = false;
        $routeUser->save();

        return redirect()
                ->action('MyRouteController@index')
                ->with('success', 'My Beat marked as inactive successfully');
    }
}
