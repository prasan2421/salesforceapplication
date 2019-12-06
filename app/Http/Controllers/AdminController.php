<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

use Form;

use App\Helpers\DataTableHelper;

class AdminController extends Controller
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
        return view('admins.index');
    }

    public function getData() {
        $data = User::where('role', 'admin')
                    ->select('id', 'name', 'username', 'email', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('AdminController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('AdminController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['AdminController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        return view('admins.create');
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
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = new User;
        $user->role = 'admin';
        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->password = bcrypt($request->password);
        $user->save();

        return redirect()
                ->action('AdminController@index')
                ->with('success', 'Admin added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('role', 'admin')
                    ->findOrFail($id);

        return view('admins.show', [
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
        $user = User::where('role', 'admin')
                    ->findOrFail($id);

        return view('admins.edit', [
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
        $user = User::where('role', 'admin')
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
                ->action('AdminController@index')
                ->with('success', 'Admin updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('role', 'admin')
                    ->findOrFail($id);

        $user->delete();

        return redirect()
                ->action('AdminController@index')
                ->with('success', 'Admin deleted successfully');
    }

    public function removeRouteRelationships() {
        $users = User::where('role', 'admin')
                ->get();

        foreach($users as $user) {
            $user->routes()->sync([]);
        }

        return redirect()
                ->action('AdminController@index')
                ->with('success', 'Route relationships removed successfully');
    }
}
