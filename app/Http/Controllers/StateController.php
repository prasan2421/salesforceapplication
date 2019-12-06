<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\State;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

class StateController extends Controller
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
        return view('states.index');
    }

    public function getData() {
        $data = State::select('id', 'code', 'name', 'abbreviation', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('StateController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('StateController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['StateController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        return view('states.create');
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
            'code' => 'required|unique:states',
            'name' => 'required',
            'abbreviation' => 'required|unique:states'
        ]);

        $state = new State;
        $state->code = Common::nullIfEmpty($request->code);
        $state->name = Common::nullIfEmpty($request->name);
        $state->abbreviation = Common::nullIfEmpty($request->abbreviation);
        $state->save();

        return redirect()
                ->action('StateController@index')
                ->with('success', 'State added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $state = State::findOrFail($id);

        return view('states.show', [
            'state' => $state
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
        $state = State::findOrFail($id);

        return view('states.edit', [
            'state' => $state
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
        $state = State::findOrFail($id);

        $request->validate([
            'code' => 'required|unique:states,code,' . $state->id . ',_id',
            'name' => 'required',
            'abbreviation' => 'required|unique:states,abbreviation,' . $state->id . ',_id'
        ]);

        $state->code = Common::nullIfEmpty($request->code);
        $state->name = Common::nullIfEmpty($request->name);
        $state->abbreviation = Common::nullIfEmpty($request->abbreviation);
        $state->save();

        return redirect()
                ->action('StateController@index')
                ->with('success', 'State updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $state = State::findOrFail($id);

        $state->delete();

        return redirect()
                ->action('StateController@index')
                ->with('success', 'State deleted successfully');
    }
}
