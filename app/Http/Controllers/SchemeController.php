<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use App\Product;

use App\Scheme;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

class SchemeController extends Controller
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
        return view('schemes.index');
    }

    public function getData() {
        $data = Scheme::select('id', 'name', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('SchemeController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('SchemeController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['SchemeController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        $products = Product::orderBy('name')->pluck('name', '_id');

        return view('schemes.create', [
            'products' => $products
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
            'product_id' => 'required|exists:products,_id',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'discount' => 'required|numeric'
        ]);

        $scheme = new Scheme;
        $scheme->product_id = Common::nullIfEmpty($request->product_id);
        $scheme->start_date = Common::nullIfEmpty($request->start_date);
        $scheme->end_date = Common::nullIfEmpty($request->end_date);
        $scheme->discount = Common::nullIfEmpty($request->discount);
        $scheme->save();

        return redirect()
                ->action('SchemeController@index')
                ->with('success', 'Scheme added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $scheme = Scheme::findOrFail($id);

        return view('schemes.show', [
            'scheme' => $scheme
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
        $scheme = Scheme::findOrFail($id);

        $products = Product::orderBy('name')->pluck('name', '_id');

        return view('schemes.edit', [
            'scheme' => $scheme,
            'products' => $products
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
        $scheme = Scheme::findOrFail($id);

        $request->validate([
            'product_id' => 'required|exists:products,_id',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'discount' => 'required|numeric'
        ]);

        $scheme->product_id = Common::nullIfEmpty($request->product_id);
        $scheme->start_date = Common::nullIfEmpty($request->start_date);
        $scheme->end_date = Common::nullIfEmpty($request->end_date);
        $scheme->discount = Common::nullIfEmpty($request->discount);
        $scheme->save();

        return redirect()
                ->action('SchemeController@index')
                ->with('success', 'Scheme updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $scheme = Scheme::findOrFail($id);

        $scheme->delete();

        return redirect()
                ->action('SchemeController@index')
                ->with('success', 'Scheme deleted successfully');
    }
}
