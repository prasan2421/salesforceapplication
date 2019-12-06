<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\CustomerClass;

use stdClass;

class CustomerClassController extends Controller
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
     * Get all customer classes.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomerClasses()
    {
        $customerClasses = CustomerClass::select('name')->get();

        return response()->json([
            'success' => true,
            'data' => [
                'customer_classes' => $customerClasses
            ],
            'errors' => new stdClass
        ]);
    }
}
