<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use App\Division;

use App\Vertical;

use App\Brand;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

class BrandController extends Controller
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
        return view('brands.index');
    }

    public function getData() {
        $data = Brand::select('id', 'name', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('BrandController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('BrandController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['BrandController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        $divisions = Division::pluck('name', '_id');
        $models = Vertical::select('id', 'name', 'division_id')->get();
        $verticals = [];

        foreach($models as $model) {
            $verticals[$model->division_id][$model->id] = $model->name;
        }

        return view('brands.create', [
            'divisions' => $divisions,
            'verticalsJson' => json_encode($verticals)
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
            'division_id' => 'required|exists:divisions,_id',
            'vertical_id' => 'required|exists:verticals,_id',
            'name' => 'required'
        ]);

        $brand = new Brand;
        $brand->division_id = Common::nullIfEmpty($request->division_id);
        $brand->vertical_id = Common::nullIfEmpty($request->vertical_id);
        $brand->name = Common::nullIfEmpty($request->name);
        $brand->save();

        return redirect()
                ->action('BrandController@index')
                ->with('success', 'Brand added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $brand = Brand::findOrFail($id);

        return view('brands.show', [
            'brand' => $brand
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
        $brand = Brand::findOrFail($id);

        $divisions = Division::pluck('name', '_id');
        $models = Vertical::select('id', 'name', 'division_id')->get();
        $verticals = [];

        foreach($models as $model) {
            $verticals[$model->division_id][$model->id] = $model->name;
        }

        return view('brands.edit', [
            'brand' => $brand,
            'divisions' => $divisions,
            'verticalsJson' => json_encode($verticals)
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
        $brand = Brand::findOrFail($id);

        $request->validate([
            'division_id' => 'required|exists:divisions,_id',
            'vertical_id' => 'required|exists:verticals,_id',
            'name' => 'required'
        ]);

        $brand->division_id = Common::nullIfEmpty($request->division_id);
        $brand->vertical_id = Common::nullIfEmpty($request->vertical_id);
        $brand->name = Common::nullIfEmpty($request->name);
        $brand->save();

        return redirect()
                ->action('BrandController@index')
                ->with('success', 'Brand updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        $brand->delete();

        return redirect()
                ->action('BrandController@index')
                ->with('success', 'Brand deleted successfully');
    }
}
