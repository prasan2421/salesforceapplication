<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\CustomerCategory;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

class CustomerCategoryController extends Controller
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
        return view('customer-categories.index');
    }

    public function getData() {
        $data = CustomerCategory::select('id', 'name', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('CustomerCategoryController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('CustomerCategoryController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['CustomerCategoryController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        return view('customer-categories.create');
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
            'name' => 'required'
        ]);

        $customerCategory = new CustomerCategory;
        $customerCategory->name = Common::nullIfEmpty($request->name);
        $customerCategory->save();

        return redirect()
                ->action('CustomerCategoryController@index')
                ->with('success', 'Customer category added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerCategory = CustomerCategory::findOrFail($id);

        return view('customer-categories.show', [
            'customerCategory' => $customerCategory
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
        $customerCategory = CustomerCategory::findOrFail($id);

        return view('customer-categories.edit', [
            'customerCategory' => $customerCategory
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
        $customerCategory = CustomerCategory::findOrFail($id);

        $request->validate([
            'name' => 'required'
        ]);

        $customerCategory->name = Common::nullIfEmpty($request->name);
        $customerCategory->save();

        return redirect()
                ->action('CustomerCategoryController@index')
                ->with('success', 'Customer category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customerCategory = CustomerCategory::findOrFail($id);

        $customerCategory->delete();

        return redirect()
                ->action('CustomerCategoryController@index')
                ->with('success', 'Customer category deleted successfully');
    }
}
