<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Customer;

use App\CustomerVisit;

use App\Helpers\Common;

// use App\Helpers\DatabaseHelper;

use DateTime;

use DateTimezone;

use DB;

use stdClass;

class CustomerVisitController extends Controller
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
     * Get all customer visits.
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    public function getCustomerVisits($date)
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

        $models = CustomerVisit::where('user_id', request()->user->id)
                        ->where('check_in_time', '>=', new DateTime($date . ' 00:00:00'))
                        ->where('check_in_time', '<=', new DateTime($date . ' 23:59:59'))
                        ->select('customer_id', 'check_in_time', 'check_out_time')
                        ->with('customer:name')
                        ->get();

        $customerVisits = [];
        foreach($models as $model) {
            $customerVisit = new stdClass;
            $customerVisit->_id = $model->_id;
            $customerVisit->customer = $model->customer ? $model->customer->name : '';
            $customerVisit->check_in_time = $model->check_in_time
                                            ? date('g:i A', strtotime($model->check_in_time))
                                            : '';
            $customerVisit->check_out_time = $model->check_out_time
                                            ? date('g:i A', strtotime($model->check_out_time))
                                            : '';

            $customerVisits[] = $customerVisit;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'customer_visits' => $customerVisits
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get status.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatus()
    {
        $customerVisit = CustomerVisit::where('user_id', request()->user->_id)
                                ->whereNull('check_out_time')
                                ->first();

        if(!$customerVisit) {
            return response()->json([
                'success' => true,
                'data' => [
                    'status' => false
                ],
                'errors' => new stdClass
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'status' => true,
                'customer_id' => $customerVisit->customer ? $customerVisit->customer->_id : '',
                'customer_name' => $customerVisit->customer ? $customerVisit->customer->name : ''
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Save check in time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        $customer_id = trim($request->customer_id);
        $time = trim($request->time);
        $longitude = trim($request->longitude);
        $latitude = trim($request->latitude);

        $errors = [];
        
        if(!$customer_id) {
            $errors['customer_id'] = 'Customer Id is required';
        }
        else if(!($customer = Customer::find($customer_id))) {
            $errors['customer_id'] = 'Customer Id is invalid';
        }
        else if(!$customer->owner_contact_number) {
            $errors['customer_id'] = 'Please update mobile number in shop';
        }

        if(!$time) {
            $errors['time'] = 'Time is required';
        }

        if(!$longitude) {
            $errors['longitude'] = 'Longitude is required';
        }

        if(!$latitude) {
            $errors['latitude'] = 'Latitude is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $customerVisit = CustomerVisit::where('user_id', $request->user->_id)
                                ->whereNull('check_out_time')
                                ->first();

        if($customerVisit) {
            // $customerVisit->check_out_time = Common::nullIfEmpty($time);
            // $customerVisit->save();
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'check_in' => 'Please check out first'
                ]
            ]);
        }

        $customerVisit = new CustomerVisit;
        $customerVisit->customer_id = Common::nullIfEmpty($customer_id);
        $customerVisit->check_in_time = Common::nullIfEmpty($time);
        $customerVisit->check_in_longitude = Common::nullIfEmpty($longitude);
        $customerVisit->check_in_latitude = Common::nullIfEmpty($latitude);
        $customerVisit->check_out_time = null;
        $customerVisit->check_out_longitude = null;
        $customerVisit->check_out_latitude = null;
        $customerVisit->user_id = $request->user->id;
        $customerVisit->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Save check out time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function checkOut(Request $request)
    {
        $time = trim($request->time);
        $longitude = trim($request->longitude);
        $latitude = trim($request->latitude);

        $errors = [];

        if(!$time) {
            $errors['time'] = 'Time is required';
        }

        if(!$longitude) {
            $errors['longitude'] = 'Longitude is required';
        }

        if(!$latitude) {
            $errors['latitude'] = 'Latitude is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $customerVisit = CustomerVisit::where('user_id', $request->user->id)
                                ->whereNull('check_out_time')
                                ->first();

        if(!$customerVisit) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'check_out' => 'Please check in first'
                ]
            ]);
        }

        $customerVisit->check_out_time = Common::nullIfEmpty($time);
        $customerVisit->check_out_longitude = Common::nullIfEmpty($longitude);
        $customerVisit->check_out_latitude = Common::nullIfEmpty($latitude);
        $customerVisit->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
