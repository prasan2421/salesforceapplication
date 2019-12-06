<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Division;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

class DivisionController extends Controller
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
        return view('divisions.index');
    }

    public function getData() {
        $data = Division::select('id', 'name', 'abbreviation', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('DivisionController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('DivisionController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['DivisionController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        return view('divisions.create');
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
            'abbreviation' => 'required|unique:divisions'
        ]);

        $division = new Division;
        $division->name = Common::nullIfEmpty($request->name);
        $division->abbreviation = Common::nullIfEmpty($request->abbreviation);
        $division->save();

        return redirect()
                ->action('DivisionController@index')
                ->with('success', 'Division added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $division = Division::findOrFail($id);

        return view('divisions.show', [
            'division' => $division
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
        $division = Division::findOrFail($id);

        return view('divisions.edit', [
            'division' => $division
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
        $division = Division::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'abbreviation' => 'required|unique:divisions,abbreviation,' . $division->id . ',_id'
        ]);

        $division->name = Common::nullIfEmpty($request->name);
        $division->abbreviation = Common::nullIfEmpty($request->abbreviation);
        $division->save();

        return redirect()
                ->action('DivisionController@index')
                ->with('success', 'Division updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $division = Division::findOrFail($id);

        $division->delete();

        return redirect()
                ->action('DivisionController@index')
                ->with('success', 'Division deleted successfully');
    }
}
