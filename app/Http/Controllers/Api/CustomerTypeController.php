<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\CustomerType;

use stdClass;

class CustomerTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('app.version');
        $this->middleware('token');
    }

    /**
     * Get all customer types.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerTypes()
    {
        $customerTypes = CustomerType::select('name','visitor_type_id')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_types' => $customerTypes
            ],
            'errors' => new stdClass
        ]);
    }
}
