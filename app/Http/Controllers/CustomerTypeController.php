<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\CustomerType;
use App\ProductType;

use Form;
use DB;
use App\Helpers\Common;

use App\Helpers\DataTableHelper;

class CustomerTypeController extends Controller
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
        return view('customer-types.index');
    }

    public function getData() {

        $data = CustomerType::select('id', 'name', 'product_type_id','created_at', 'updated_at');

        $pipeline = [
            [
                '$addFields' => [
                    'product_type_id' => [
                        '$toObjectId' => '$product_type_id'
                    ],
                    'created_at' => [
                        '$dateToString' => [
                            'date' => '$created_at',
                            'format' => '%Y-%m-%d %H:%M:%S',
//                            'timezone' => config('app.timezone')
                        ]
                    ],
                    'updated_at' => [
                        '$dateToString' => [
                            'date' => '$updated_at',
                            'format' => '%Y-%m-%d %H:%M:%S',
//                            'timezone' => config('app.timezone')
                        ]
                    ]
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'product_types',
                    'localField' => 'product_type_id',
                    'foreignField' => '_id',
                    'as' => 'info'
                ]
            ],
            [
                '$unwind' => [
                    'path' => '$info',
                    'preserveNullAndEmptyArrays' => true
                ]
            ],
            [
                '$project' => [
                    '_id' => 1,
                    'name' => 1,
                    'created_at' => 1,
                    'updated_at' => 1,
                    'visitor_type' => [
                        '$ifNull' => [ '$info.name', '' ]
                    ],
                     'product_id' => [

            '$ifNull' => [ '$info._id', '' ]
        ]

                ]
            ]

        ];


        return DataTableHelper::aggregate('customer_types',$pipeline)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('CustomerTypeController@show', $model->_id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('CustomerTypeController@edit', $model->_id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['CustomerTypeController@destroy', $model->_id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        $productTypes = ProductType::pluck('name', '_id');
        return view('customer-types.create', [
             'productTypes' => $productTypes,]);
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

        $customerType = new CustomerType;
        $customerType->name = Common::nullIfEmpty($request->name);
        $customerType->product_type_id = Common::nullIfEmpty($request->product_type_id);
        $customerType->save();

        return redirect()
                ->action('CustomerTypeController@index')
                ->with('success', 'Customer type added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customerType = CustomerType::select('id', 'name', 'product_type_id')
            ->with('productType')
            ->findOrFail($id);

//        $customerType = CustomerType::findOrFail($id);
//        $productType = ProductType::whereIn('name', '_id');
        return view('customer-types.show', [
            'customerType' => $customerType,
//            'productType' => $productType,
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
        $customerType = CustomerType::findOrFail($id);
        $productTypes = ProductType::pluck('name', '_id');


        return view('customer-types.edit', [
            'customerType' => $customerType,
            'productTypes' => $productTypes,
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
        $customerType = CustomerType::findOrFail($id);

        $request->validate([
            'name' => 'required'
        ]);

        $customerType->name = Common::nullIfEmpty($request->name);
        $customerType->product_type_id = Common::nullIfEmpty($request->product_type_id);
        $customerType->save();

        return redirect()
                ->action('CustomerTypeController@index')
                ->with('success', 'Customer type updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customerType = CustomerType::findOrFail($id);

        $customerType->delete();

        return redirect()
                ->action('CustomerTypeController@index')
                ->with('success', 'Customer type deleted successfully');
    }
}
