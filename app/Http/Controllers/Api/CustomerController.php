<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Counter;

use App\Customer;

use App\CustomerClass;

use App\CustomerType;

use App\CustomerVisit;

use App\Location;

use App\Order;

use App\Route;

use App\RouteUser;

use App\State;

use App\Helpers\Common;

use DateTime;

use DB;

use stdClass;

class CustomerController extends Controller
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
     * Get all customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomers()
    {
        // if(request()->user->role == 'sales-officer') {
        //     $routeIds = request()->user->routes()->pluck('_id');
        // }
        // else {
            $routeIds = RouteUser::where('user_id', request()->user->_id)
                            ->where('is_active', true)
                            ->pluck('route_id');
        // }

        $models = Customer::whereIn('route_id', $routeIds)
                        ->select('id', 'name', 'owner_contact_number', 'billing_address', 'route_id', 'customer_type_id', 'user_id')
                        ->with('customerType')
                        ->get();

        $customers = [];
        foreach($models as $model) {
            $customer = new stdClass;
            $customer->_id = $model->_id;
            $customer->name = $model->name;
            $customer->owner_contact_number = $model->owner_contact_number;
            $customer->billing_address = $model->billing_address;
            $customer->route_id = $model->route_id;
            $customer->customer_type = $model->customerType ? $model->customerType->name : '';
            // $customer->is_editable = $model->user_id == request()->user->_id;
            $customer->is_editable = true;

            $customers[] = $customer;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'customers' => $customers
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get today's customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTodayCustomers()
    {
        // if(request()->user->role == 'sales-officer') {
        //     $routeIds = request()->user->routes()->pluck('_id');
        // }
        // else {
            $routeIds = RouteUser::where('user_id', request()->user->_id)
                            ->where('is_active', true)
                            ->where(function($query) {
                                $query->where('frequency', 'daily')
                                    ->orWhere(function($query1){
                                        $query1->where('frequency', 'weekly')
                                                ->where('day', strtolower(date('l')));
                                    });
                            })
                            ->pluck('route_id');
        // }

        $models = Customer::whereIn('route_id', $routeIds)
                        ->select('id', 'name', 'owner_contact_number', 'billing_address', 'route_id', 'customer_type_id')
                        ->with('customerType')
                        ->get();

        $date = date('Y-m-d');
        $visitedCustomerIds = CustomerVisit::where('user_id', request()->user->id)
                                ->where('check_in_time', '>=', new DateTime($date . ' 00:00:00'))
                                ->where('check_in_time', '<=', new DateTime($date . ' 23:59:59'))
                                ->pluck('customer_id')
                                ->toArray();

        $visitedCustomerIds = array_unique($visitedCustomerIds);

        $customers = [];
        foreach($models as $model) {
            $customer = new stdClass;
            $customer->_id = $model->_id;
            $customer->name = $model->name;
            $customer->owner_contact_number = $model->owner_contact_number;
            $customer->billing_address = $model->billing_address;
            $customer->route_id = $model->route_id;
            $customer->customer_type = $model->customerType ? $model->customerType->name : '';
            $customer->is_visited = in_array($customer->_id, $visitedCustomerIds);

            $customers[] = $customer;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'customers' => $customers
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get customers scheduled for the date.
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    public function getScheduledCustomers($date)
    {
        if(!Common::isDateValid($date)) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'date' => 'Date is invalid'
                ]
            ]);
        }

        // if(request()->user->role == 'sales-officer') {
        //     $routeIds = request()->user->routes()->pluck('_id');
        // }
        // else {
            $routeIds = RouteUser::where('user_id', request()->user->_id)
                            ->where('is_active', true)
                            ->where(function($query) use ($date) {
                                $query->where('frequency', 'daily')
                                    ->orWhere(function($query1) use ($date) {
                                        $query1->where('frequency', 'weekly')
                                                ->where('day', strtolower(date('l', strtotime($date))));
                                    });
                            })
                            ->pluck('route_id');
        // }

        $models = Customer::whereIn('route_id', $routeIds)
                        ->select('id', 'name', 'owner_contact_number', 'billing_address', 'route_id', 'customer_type_id')
                        ->with('customerType')
                        ->get();

        $visitedCustomerIds = CustomerVisit::where('user_id', request()->user->id)
                                ->where('check_in_time', '>=', new DateTime($date . ' 00:00:00'))
                                ->where('check_in_time', '<=', new DateTime($date . ' 23:59:59'))
                                ->pluck('customer_id')
                                ->toArray();

        $visitedCustomerIds = array_unique($visitedCustomerIds);

        $customers = [];
        $totalVisited = 0;
        $totalNotVisited = 0;
        foreach($models as $model) {
            $customer = new stdClass;
            $customer->_id = $model->_id;
            $customer->name = $model->name;
            $customer->owner_contact_number = $model->owner_contact_number;
            $customer->billing_address = $model->billing_address;
            $customer->route_id = $model->route_id;
            $customer->customer_type = $model->customerType ? $model->customerType->name : '';
            $customer->is_visited = in_array($customer->_id, $visitedCustomerIds);

            $customer->is_visited ? $totalVisited++ : $totalNotVisited++;

            $customers[] = $customer;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'customers' => $customers,
                'total_visited' => $totalVisited,
                'total_not_visited' => $totalNotVisited
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get schedule summary for the date.
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    public function getScheduleSummary($date)
    {
        if(!Common::isDateValid($date)) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'date' => 'Date is invalid'
                ]
            ]);
        }

        // if(request()->user->role == 'sales-officer') {
        //     $routeIds = request()->user->routes()->pluck('_id');
        // }
        // else {
            $routeIds = RouteUser::where('user_id', request()->user->_id)
                            ->where('is_active', true)
                            ->where(function($query) use ($date) {
                                $query->where('frequency', 'daily')
                                    ->orWhere(function($query1) use ($date) {
                                        $query1->where('frequency', 'weekly')
                                                ->where('day', strtolower(date('l', strtotime($date))));
                                    });
                            })
                            ->pluck('route_id');
        // }

        $scheduledCustomerIds = Customer::whereIn('route_id', $routeIds)
                                    ->pluck('_id')
                                    ->toArray();

        $visitedCustomerIds = CustomerVisit::where('user_id', request()->user->id)
                                ->where('check_in_time', '>=', new DateTime($date . ' 00:00:00'))
                                ->where('check_in_time', '<=', new DateTime($date . ' 23:59:59'))
                                ->pluck('customer_id')
                                ->toArray();

        $visitedCustomerIds = array_unique($visitedCustomerIds);

        // $orderCustomerIds = Order::where('user_id', request()->user->id)
        //                         ->where('created_at', '>=', new DateTime($date . ' 00:00:00'))
        //                         ->where('created_at', '<=', new DateTime($date . ' 23:59:59'))
        //                         ->pluck('customer_id')
        //                         ->toArray();

        // $orderCustomerIds = array_unique($orderCustomerIds);

        $totalVisited = 0;
        $totalNotVisited = 0;
        // $totalOrders = 0;
        foreach($scheduledCustomerIds as $customerId) {
            in_array($customerId, $visitedCustomerIds) ? $totalVisited++ : $totalNotVisited++;

            // if(in_array($customerId, $orderCustomerIds)) {
            //     $totalOrders++;
            // }
        }

        $totalOrders = Order::where('user_id', request()->user->id)
                            ->where('created_at', '>=', new DateTime($date . ' 00:00:00'))
                            ->where('created_at', '<=', new DateTime($date . ' 23:59:59'))
                            ->count();

        $totalOrderValue = Order::where('user_id', request()->user->id)
                            ->where('created_at', '>=', new DateTime($date . ' 00:00:00'))
                            ->where('created_at', '<=', new DateTime($date . ' 23:59:59'))
                            ->sum('total_amount');

        return response()->json([
            'success' => true,
            'data' => [
                'total_customers' => count($scheduledCustomerIds),
                'total_visited' => $totalVisited,
                'total_not_visited' => $totalNotVisited,
                'total_orders' => $totalOrders,
                'total_order_value' => $totalOrderValue
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get monthly summary.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMonthlySummary()
    {
        // return response()->json([
        //     'success' => true,
        //     'data' => [
        //         'in_development' => true,
        //         'weeks' => []
        //     ],
        //     'errors' => new stdClass
        // ]);

        $date = date('Y-m-t');
        $arr = explode('-', $date);
        $year = $arr[0];
        $month = $arr[1];
        $lastDay = $arr[2];

        $days = [
            [1, 7],
            [8, 14],
            [15, 21],
            [22, $lastDay]
        ];

        $weeks = [];

        $db = DB::getMongoDB();

        for($i = 0; $i < count($days); $i++) {
            $day = $days[$i];
            $startDate = $year . '-' . $month . '-' . $day[0] . ' 00:00:00';
            $endDate = $year . '-' . $month . '-' . $day[1] . ' 23:59:59';

            $week = new stdClass;
            $week->_id = 'week_' . ($i + 1);
            $week->name = 'Week ' . ($i + 1);
            $week->total_visited = 0;
            $week->total_orders = 0;

            $pipeline1 = [
                [
                    '$match' => [
                        '$expr' => [
                            '$and' => [
                                [
                                    '$eq' => [
                                        '$user_id',
                                        request()->user->id
                                    ]
                                ],
                                [
                                    '$gte' => [
                                        '$check_in_time',
                                        [
                                            '$dateFromString' => [
                                                'dateString' => $startDate,
                                                'format' => '%Y-%m-%d %H:%M:%S',
                                                'timezone' => 'Asia/Kolkata'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    '$lte' => [
                                        '$check_in_time',
                                        [
                                            '$dateFromString' => [
                                                'dateString' => $endDate,
                                                'format' => '%Y-%m-%d %H:%M:%S',
                                                'timezone' => 'Asia/Kolkata'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'date' => [
                                '$dateToString' => [
                                    'date' => '$check_in_time',
                                    'format' => '%Y-%m-%d',
                                    'timezone' => 'Asia/Kolkata'
                                ]
                            ],
                            'customer_id' => '$customer_id'
                        ]
                    ]
                ],
                [
                    '$count' => 'count'
                ]
            ];

            $results1 = $db->customer_visits->aggregate($pipeline1);
            foreach($results1 as $row) {
                $week->total_visited = $row->count;
            }

            $pipeline2 = [
                [
                    '$match' => [
                        '$expr' => [
                            '$and' => [
                                [
                                    '$eq' => [
                                        '$user_id',
                                        request()->user->id
                                    ]
                                ],
                                [
                                    '$gte' => [
                                        '$created_at',
                                        [
                                            '$dateFromString' => [
                                                'dateString' => $startDate,
                                                'format' => '%Y-%m-%d %H:%M:%S',
                                                'timezone' => 'Asia/Kolkata'
                                            ]
                                        ]
                                    ]
                                ],
                                [
                                    '$lte' => [
                                        '$created_at',
                                        [
                                            '$dateFromString' => [
                                                'dateString' => $endDate,
                                                'format' => '%Y-%m-%d %H:%M:%S',
                                                'timezone' => 'Asia/Kolkata'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    '$group' => [
                        '_id' => [
                            'date' => [
                                '$dateToString' => [
                                    'date' => '$created_at',
                                    'format' => '%Y-%m-%d',
                                    'timezone' => 'Asia/Kolkata'
                                ]
                            ],
                            'customer_id' => '$customer_id'
                        ]
                    ]
                ],
                [
                    '$count' => 'count'
                ]
            ];

            $results2 = $db->orders->aggregate($pipeline2);
            foreach($results2 as $row) {
                $week->total_orders = $row->count;
            }

            $totalOrderQuantity = Order::where('user_id', request()->user->id)
                                    ->where('created_at', '>=', new DateTime($startDate))
                                    ->where('created_at', '<=', new DateTime($endDate))
                                    ->sum('total_quantity');

            $totalOrderValue = Order::where('user_id', request()->user->id)
                                ->where('created_at', '>=', new DateTime($startDate))
                                ->where('created_at', '<=', new DateTime($endDate))
                                ->sum('total_amount');

            $week->total_order_quantity = $totalOrderQuantity;
            $week->total_order_value = $totalOrderValue;

            $weeks[] = $week;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'in_development' => false,
                'weeks' => $weeks
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get customer details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCustomerDetails($id)
    {
        $customer = Customer::find($id);

        if(!$customer) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'route_id' => $customer->route ? $customer->route->_id : '',
                'route_name' => $customer->route ? $customer->route->name : '',
                'customer_type_id' => $customer->customerType ? $customer->customerType->_id : '',
                'customer_type_name' => $customer->customerType ? $customer->customerType->name : '',
                'product_type_name' => $customer->customerType->productType ? $customer->customerType->productType->name : '',
                'customer_class_id' => $customer->customerClass ? $customer->customerClass->_id : '',
                'customer_class_name' => $customer->customerClass ? $customer->customerClass->name : '',
                'sap_code' => $customer->sap_code,
                'name' => $customer->name,
                'gst_number' => $customer->gst_number,
                'town' => $customer->town,
                'longitude' => $customer->longitude,
                'latitude' => $customer->latitude,
                'owner_name' => $customer->owner_name,
                'owner_email' => $customer->owner_email,
                'owner_contact_number' => $customer->owner_contact_number,
                'billing_state_id' => $customer->billingState ? $customer->billingState->_id : '',
                'billing_state_name' => $customer->billingState ? $customer->billingState->name : '',
                'billing_district' => $customer->billing_district,
                'billing_city' => $customer->billing_city,
                'billing_address' => $customer->billing_address,
                'billing_pincode' => $customer->billing_pincode,
                'shipping_state_id' => $customer->shippingState ? $customer->shippingState->_id : '',
                'shipping_state_name' => $customer->shippingState ? $customer->shippingState->name : '',
                'shipping_district' => $customer->shipping_district,
                'shipping_city' => $customer->shipping_city,
                'shipping_address' => $customer->shipping_address,
                'shipping_pincode' => $customer->shipping_pincode,
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Add new customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addCustomer(Request $request)
    {
        $route_id = trim($request->route_id);
        $customer_type_id = trim($request->customer_type_id);
        $customer_class_id = trim($request->customer_class_id);
        // $sap_code = trim($request->sap_code);
        $name = trim($request->name);
        $gst_number = trim($request->gst_number);
        $town = trim($request->town);
        $longitude = trim($request->longitude);
        $latitude = trim($request->latitude);
        $owner_name = trim($request->owner_name);
        $owner_email = trim($request->owner_email);
        $owner_contact_number = trim($request->owner_contact_number);
        $billing_state_id = trim($request->billing_state_id);
        $billing_district = trim($request->billing_district);
        $billing_city = trim($request->billing_city);
        $billing_address = trim($request->billing_address);
        $billing_pincode = trim($request->billing_pincode);
        $shipping_state_id = trim($request->shipping_state_id);
        $shipping_district = trim($request->shipping_district);
        $shipping_city = trim($request->shipping_city);
        $shipping_address = trim($request->shipping_address);
        $shipping_pincode = trim($request->shipping_pincode);

        $errors = [];

        if(!$route_id) {
            $errors['route_id'] = 'Route Id is required';
        }
        else {
            // if(request()->user->role == 'sales-officer') {
            //     if(Route::whereHas('users', function($query) {
            //         $query->where('_id', request()->user->_id);
            //     })
            //     ->where('_id', $route_id)
            //     ->count() == 0) {
            //         $errors['route_id'] = 'Route Id is invalid';
            //     }
            // }
            // else {
                if(RouteUser::where('user_id', request()->user->id)
                            ->where('route_id', $route_id)
                            ->where('is_active', true)
                            ->count() == 0) {
                    $errors['route_id'] = 'Route Id is invalid';
                }
            // }
        }
                
        // if($route_id
        //     && RouteUser::where('user_id', request()->user->id)
        //         ->where('route_id', $route_id)
        //         ->where('is_active', true)
        //         ->count() == 0) {
        //     $errors['route_id'] = 'Route Id is invalid';
        // }

        if($customer_type_id && CustomerType::where('_id', $customer_type_id)->count() == 0) {
            $errors['customer_type_id'] = 'Customer Type Id is invalid';
        }

        if($customer_class_id && CustomerClass::where('_id', $customer_class_id)->count() == 0) {
            $errors['customer_class_id'] = 'Customer Class Id is invalid';
        }

        // if(!$sap_code) {
        //     $errors['sap_code'] = 'Retailer Code is required';
        // }
        // else if(Customer::where('sap_code', $sap_code)->count() > 0) {
        //     $errors['sap_code'] = 'Retailer Code already exists';
        // }
        
        if(!$name) {
            $errors['name'] = 'Name is required';
        }
        else if(!preg_match('/^[a-zA-Z0-9\s.\/_-]+$/', $name)) {
            $errors['name'] = 'Name is invalid';
        }

        if(!$owner_name) {
            $errors['owner_name'] = 'Owner Name is required';
        }

        if(!$owner_contact_number) {
            $errors['owner_contact_number'] = 'Mobile No. is required';
        }
        else if(!preg_match('/^[0-9]{10}$/', $owner_contact_number)) {
            $errors['owner_contact_number'] = 'Mobile No. must be 10 digits';
        }

        if($billing_state_id && State::where('_id', $billing_state_id)->count() == 0) {
            $errors['billing_state_id'] = 'Billing State Id is invalid';
        }

        if($shipping_state_id && State::where('_id', $shipping_state_id)->count() == 0) {
            $errors['shipping_state_id'] = 'Shipping State Id is invalid';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $route = Route::with(['division', 'state'])->find($route_id);
        $division = $route->division;
        $state = $route->state;
        $prefix = $division->abbreviation . '_' . $state->abbreviation . '_';

        $counter = Counter::where('name', 'retailer-code')
                        ->where('prefix', $prefix)
                        ->first();
        if(!$counter) {
            $counter = new Counter;
            $counter->name = 'retailer-code';
            $counter->prefix = $prefix;
            $counter->count = 0;
        }

        $counter->count = $counter->count + 1;
        $counter->save();

        $customer = new Customer;
        $customer->division_id = $division->id;
        $customer->state_id = $state->id;
        $customer->route_id = Common::nullIfEmpty($route_id);
        $customer->customer_type_id = Common::nullIfEmpty($customer_type_id);
        $customer->customer_class_id = Common::nullIfEmpty($customer_class_id);
        // $customer->sap_code = Common::nullIfEmpty($sap_code);
        $customer->sap_code = $counter->prefix . $counter->count;
        $customer->name = Common::nullIfEmpty($name);
        $customer->gst_number = Common::nullIfEmpty($gst_number);
        $customer->town = Common::nullIfEmpty($town);
        $customer->longitude = Common::nullIfEmpty($longitude);
        $customer->latitude = Common::nullIfEmpty($latitude);
        $customer->owner_name = Common::nullIfEmpty($owner_name);
        $customer->owner_email = Common::nullIfEmpty($owner_email);
        $customer->owner_contact_number = Common::nullIfEmpty($owner_contact_number);
        $customer->billing_state_id = Common::nullIfEmpty($billing_state_id);
        $customer->billing_district = Common::nullIfEmpty($billing_district);
        $customer->billing_city = Common::nullIfEmpty($billing_city);
        $customer->billing_address = Common::nullIfEmpty($billing_address);
        $customer->billing_pincode = Common::nullIfEmpty($billing_pincode);
        $customer->shipping_state_id = Common::nullIfEmpty($shipping_state_id);
        $customer->shipping_district = Common::nullIfEmpty($shipping_district);
        $customer->shipping_city = Common::nullIfEmpty($shipping_city);
        $customer->shipping_address = Common::nullIfEmpty($shipping_address);
        $customer->shipping_pincode = Common::nullIfEmpty($shipping_pincode);
        $customer->created_by = $request->user->id;
        $customer->user_id = $request->user->id;
        $customer->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Update customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateCustomer(Request $request, $id)
    {
        // $customer = Customer::where('user_id', request()->user->id)
        //                 ->find($id);

        // if(request()->user->role == 'sales-officer') {
        //     $routeIds = request()->user->routes()->pluck('_id');
        // }
        // else {
            $routeIds = RouteUser::where('user_id', request()->user->_id)
                            ->where('is_active', true)
                            ->pluck('route_id');
        // }

        $customer = Customer::whereIn('route_id', $routeIds)
                        ->find($id);
        
        if(!$customer) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $route_id = trim($request->route_id);
        $customer_type_id = trim($request->customer_type_id);
        $customer_class_id = trim($request->customer_class_id);
        $name = trim($request->name);
        $gst_number = trim($request->gst_number);
        $town = trim($request->town);
        $longitude = trim($request->longitude);
        $latitude = trim($request->latitude);
        $owner_name = trim($request->owner_name);
        $owner_email = trim($request->owner_email);
        $owner_contact_number = trim($request->owner_contact_number);
        $billing_state_id = trim($request->billing_state_id);
        $billing_district = trim($request->billing_district);
        $billing_city = trim($request->billing_city);
        $billing_address = trim($request->billing_address);
        $billing_pincode = trim($request->billing_pincode);
        $shipping_state_id = trim($request->shipping_state_id);
        $shipping_district = trim($request->shipping_district);
        $shipping_city = trim($request->shipping_city);
        $shipping_address = trim($request->shipping_address);
        $shipping_pincode = trim($request->shipping_pincode);

        $errors = [];

        if(!$route_id) {
            $errors['route_id'] = 'Route Id is required';
        }
        else {
            // if(request()->user->role == 'sales-officer') {
            //     if(Route::whereHas('users', function($query) {
            //         $query->where('_id', request()->user->_id);
            //     })
            //     ->where('_id', $route_id)
            //     ->count() == 0) {
            //         $errors['route_id'] = 'Route Id is invalid';
            //     }
            // }
            // else {
                if(RouteUser::where('user_id', request()->user->id)
                            ->where('route_id', $route_id)
                            ->where('is_active', true)
                            ->count() == 0) {
                    $errors['route_id'] = 'Route Id is invalid';
                }
            // }
        }
        
        // if($route_id
        //     && RouteUser::where('user_id', request()->user->id)
        //         ->where('route_id', $route_id)
        //         ->where('is_active', true)
        //         ->count() == 0) {
        //     $errors['route_id'] = 'Route Id is invalid';
        // }

        if($customer_type_id && CustomerType::where('_id', $customer_type_id)->count() == 0) {
            $errors['customer_type_id'] = 'Customer Type Id is invalid';
        }

        if($customer_class_id && CustomerClass::where('_id', $customer_class_id)->count() == 0) {
            $errors['customer_class_id'] = 'Customer Class Id is invalid';
        }
        
        if(!$name) {
            $errors['name'] = 'Name is required';
        }
        else if(!preg_match('/^[a-zA-Z0-9\s.\/_-]+$/', $name)) {
            $errors['name'] = 'Name is invalid';
        }

        if(!$owner_name) {
            $errors['owner_name'] = 'Owner Name is required';
        }

        if(!$owner_contact_number) {
            $errors['owner_contact_number'] = 'Mobile No. is required';
        }
        else if(!preg_match('/^[0-9]{10}$/', $owner_contact_number)) {
            $errors['owner_contact_number'] = 'Mobile No. must be 10 digits';
        }

        if($billing_state_id && State::where('_id', $billing_state_id)->count() == 0) {
            $errors['billing_state_id'] = 'Billing State Id is invalid';
        }

        if($shipping_state_id && State::where('_id', $shipping_state_id)->count() == 0) {
            $errors['shipping_state_id'] = 'Shipping State Id is invalid';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $customer->route_id = Common::nullIfEmpty($route_id);
        $customer->customer_type_id = Common::nullIfEmpty($customer_type_id);
        $customer->customer_class_id = Common::nullIfEmpty($customer_class_id);
        $customer->name = Common::nullIfEmpty($name);
        $customer->gst_number = Common::nullIfEmpty($gst_number);
        $customer->town = Common::nullIfEmpty($town);
        $customer->longitude = Common::nullIfEmpty($longitude);
        $customer->latitude = Common::nullIfEmpty($latitude);
        $customer->owner_name = Common::nullIfEmpty($owner_name);
        $customer->owner_email = Common::nullIfEmpty($owner_email);
        $customer->owner_contact_number = Common::nullIfEmpty($owner_contact_number);
        $customer->billing_state_id = Common::nullIfEmpty($billing_state_id);
        $customer->billing_district = Common::nullIfEmpty($billing_district);
        $customer->billing_city = Common::nullIfEmpty($billing_city);
        $customer->billing_address = Common::nullIfEmpty($billing_address);
        $customer->billing_pincode = Common::nullIfEmpty($billing_pincode);
        $customer->shipping_state_id = Common::nullIfEmpty($shipping_state_id);
        $customer->shipping_district = Common::nullIfEmpty($shipping_district);
        $customer->shipping_city = Common::nullIfEmpty($shipping_city);
        $customer->shipping_address = Common::nullIfEmpty($shipping_address);
        $customer->shipping_pincode = Common::nullIfEmpty($shipping_pincode);
        $customer->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
