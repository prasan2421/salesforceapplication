<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use App\User;

use Form;

use App\Helpers\Common;

// use App\Helpers\DatabaseHelper;

use App\Helpers\DataTableHelper;

use DB;

use stdClass;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.index');
    }

    public function getData() {
        $data = User::where('role', 'normal')
                    ->select('id', 'name', 'username', 'email', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('UserController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('UserController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= '<a href="' . action('UserController@map', $model->id) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Map"><i class="fas fa-map-marked-alt"></i></a>';

                    $content .= Form::open(['action' => ['UserController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
                    $content .= '<button class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" type="submit" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fas fa-trash-alt"></i></button>';
                    $content .= Form::close();

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
        $users = User::where('role', 'normal')->pluck('name', '_id');
        
        return view('users.create', [
            'users' => $users
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
            'parent_id' => [
                'nullable',
                Rule::exists('users', '_id')->where(function($query){
                    $query->where('role', 'normal');
                })
            ],
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = new User;
        $user->role = 'normal';
        $user->parent_id = Common::nullIfEmpty($request->parent_id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->save();

        if($user->parent) {
            $user->ancestors()->attach($user->parent);

            if(count($user->parent->ancestors) > 0) {
                $user->ancestors()->attach($user->parent->ancestors);
            }
        }

        return redirect()
                ->action('UserController@index')
                ->with('success', 'User added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('role', 'normal')
                    ->findOrFail($id);

        return view('users.show', [
            'user' => $user
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
        $user = User::where('role', 'normal')
                    ->findOrFail($id);

        return view('users.edit', [
            'user' => $user
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
        $user = User::where('role', 'normal')
                    ->findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id . ',_id',
            'username' => 'required|unique:users,username,' . $user->id . ',_id',
            'password' => 'confirmed'
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        if($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return redirect()
                ->action('UserController@index')
                ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('role', 'normal')
                    ->findOrFail($id);

        $user->delete();

        return redirect()
                ->action('UserController@index')
                ->with('success', 'User deleted successfully');
    }

    /**
     * Display map of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function map($id)
    {
        $user = User::where('role', 'normal')
                    ->findOrFail($id);

        // $db = DatabaseHelper::getDatabase();

        $db = DB::getMongoDB();

        $pipeline = [
            [
                '$addFields' => [
                    'created_date' => [
                        '$dateToString' => [
                            'date' => '$created_at',
                            'format' => '%Y-%m-%d',
                            'timezone' => config('app.timezone')
                        ]
                    ]
                ],
            ],
            [
                '$match' => [
                    '$and' => [
                        [
                            'user_id' => $user->id
                        ],
                        [
                            'created_date' => date('Y-m-d')
                        ]
                    ]
                ]
            ]
        ];

        $results = $db->geolocations->aggregate($pipeline);

        $geolocations = [];

        foreach($results as $row) {
            $geolocation = new stdClass;
            $geolocation->longitude = $row->longitude;
            $geolocation->latitude = $row->latitude;

            $geolocations[] = $geolocation;
        }

        return view('users.map', [
            'user' => $user,
            'geolocations' => $geolocations
        ]);
    }
}
