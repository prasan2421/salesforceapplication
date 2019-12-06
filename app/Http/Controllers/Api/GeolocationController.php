<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Attendance;

use App\Geolocation;

use App\Helpers\Common;

// use App\Helpers\DatabaseHelper;

use DateTime;

use DateTimezone;

use DB;

use stdClass;

class GeolocationController extends Controller
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
     * Get all geolocations.
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    /*public function getGeolocations($date)
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

        // $db = DatabaseHelper::getDatabase();

        $db = DB::getMongoDB();

        $pipeline = [
            [
                '$addFields' => [
                    'mobile_created_date' => [
                        '$dateToString' => [
                            'date' => '$mobile_created_at',
                            'format' => '%Y-%m-%d',
                            'timezone' => config('app.timezone')
                        ]
                    ]
                ],
            ],
            [
                '$match' => [
                    '$and' => [
                        [
                            'user_id' => request()->user->id
                        ],
                        [
                            'mobile_created_date' => $date
                        ]
                    ]
                ]
            ]
        ];

        $results = $db->geolocations->aggregate($pipeline);

        $geolocations = [];

        foreach($results as $row) {
            $geolocation = new stdClass;
            $geolocation->longitude = $row->longitude;
            $geolocation->latitude = $row->latitude;
            $geolocation->mobile_created_at = property_exists($row, 'mobile_created_at')
                                            ? $row->mobile_created_at
                                                ->toDateTime()
                                                ->setTimezone(new DateTimezone(config('app.timezone')))
                                                ->format('g:i A')
                                            : '';

            $geolocations[] = $geolocation;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'geolocations' => $geolocations
            ],
            'errors' => new stdClass
        ]);
    }*/

    public function getGeolocations($date)
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

        $models = Geolocation::where('user_id', request()->user->id)
                        ->where('mobile_created_at', '>=', new DateTime($date . ' 00:00:00'))
                        ->where('mobile_created_at', '<=', new DateTime($date . ' 23:59:59'))
                        ->select('longitude', 'latitude', 'mobile_created_at')
                        ->get();

        $geolocations = [];
        foreach($models as $model) {
            $geolocation = new stdClass;
            $geolocation->_id = $model->_id;
            $geolocation->longitude = $model->longitude;
            $geolocation->latitude = $model->latitude;
            $geolocation->mobile_created_at = $model->mobile_created_at
                                            ? date('g:i A', strtotime($model->mobile_created_at))
                                            : '';

            $geolocations[] = $geolocation;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'geolocations' => $geolocations
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Add new geolocation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addGeolocation(Request $request)
    {
        $count = Attendance::where('user_id', request()->user->_id)
                                ->whereNull('punch_out_time')
                                ->count();

        if($count == 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => new stdClass
            ]);
        }

        $longitude = trim($request->longitude);
        $latitude = trim($request->latitude);
        $mobile_created_at = trim($request->mobile_created_at);

        $errors = [];
        
        if(!$longitude) {
            $errors['longitude'] = 'Longitude is required';
        }

        if(!$latitude) {
            $errors['latitude'] = 'Latitude is required';
        }

        if(!$mobile_created_at) {
            $errors['mobile_created_at'] = 'Mobile Created At is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $geolocation = new Geolocation;
        $geolocation->longitude = Common::nullIfEmpty($longitude);
        $geolocation->latitude = Common::nullIfEmpty($latitude);
        $geolocation->mobile_created_at = Common::nullIfEmpty($mobile_created_at);
        $geolocation->user_id = $request->user->id;
        $geolocation->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /*public function addGeolocation(Request $request)
    {
        $count = Attendance::where('user_id', request()->user->_id)
                                ->whereNull('punch_out_time')
                                ->count();

        if($count == 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => new stdClass
            ]);
        }

        foreach($request->input() as $input) {
            $geolocation = new Geolocation;
            $geolocation->longitude = Common::nullIfEmpty($input['longitude']);
            $geolocation->latitude = Common::nullIfEmpty($input['latitude']);
            $geolocation->mobile_created_at = Common::nullIfEmpty($input['mobile_created_at']);
            $geolocation->user_id = $request->user->id;
            $geolocation->save();
        }

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }*/
}
