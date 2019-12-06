<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Attendance;

use App\CustomerVisit;

use App\Helpers\Common;

// use App\Helpers\DatabaseHelper;

use DateTime;

use DateTimezone;

use DB;

use stdClass;

class AttendanceController extends Controller
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
     * Get all attendances.
     *
     * @param  string  $date
     * @return \Illuminate\Http\Response
     */
    /*public function getAttendances($date)
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
                    '_id' => [
                        '$toString' => '$_id'
                    ],
                    'punch_in_date' => [
                        '$dateToString' => [
                            'date' => '$punch_in_time',
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
                            'punch_in_date' => $date
                        ]
                    ]
                ]
            ]
        ];

        $results = $db->attendances->aggregate($pipeline);

        $attendances = [];

        foreach($results as $row) {
            $attendance = new stdClass;
            $attendance->_id = $row->_id;
            $attendance->punch_in_time = property_exists($row, 'punch_in_time')
                                            ? $row->punch_in_time
                                                ->toDateTime()
                                                ->setTimezone(new DateTimezone(config('app.timezone')))
                                                ->format('g:i A')
                                            : '';

            $attendance->punch_out_time = property_exists($row, 'punch_out_time')
                                            ? $row->punch_out_time
                                                ->toDateTime()
                                                ->setTimezone(new DateTimezone(config('app.timezone')))
                                                ->format('g:i A')
                                            : '';

            $attendances[] = $attendance;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'attendances' => $attendances
            ],
            'errors' => new stdClass
        ]);
    }*/

    public function getAttendances($date)
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

        $models = Attendance::where('user_id', request()->user->id)
                        ->where('punch_in_time', '>=', new DateTime($date . ' 00:00:00'))
                        ->where('punch_in_time', '<=', new DateTime($date . ' 23:59:59'))
                        ->select('punch_in_time', 'punch_out_time')
                        ->get();

        $attendances = [];
        foreach($models as $model) {
            $attendance = new stdClass;
            $attendance->_id = $model->_id;
            $attendance->punch_in_time = $model->punch_in_time
                                            ? date('g:i A', strtotime($model->punch_in_time))
                                            : '';
            $attendance->punch_out_time = $model->punch_out_time
                                            ? date('g:i A', strtotime($model->punch_out_time))
                                            : '';

            $attendances[] = $attendance;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'attendances' => $attendances
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get punch status.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPunchStatus()
    {
        $count = Attendance::where('user_id', request()->user->_id)
                                ->whereNull('punch_out_time')
                                ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'status' => $count > 0
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Save punch in time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function punchIn(Request $request)
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

        $attendance = Attendance::where('user_id', $request->user->_id)
                                ->whereNull('punch_out_time')
                                ->first();

        if($attendance) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'punch_in' => 'Please punch out first'
                ]
            ]);
        }

        $attendance = new Attendance;
        $attendance->punch_in_time = Common::nullIfEmpty($time);
        $attendance->punch_in_longitude = Common::nullIfEmpty($longitude);
        $attendance->punch_in_latitude = Common::nullIfEmpty($latitude);
        $attendance->punch_out_time = null;
        $attendance->punch_out_longitude = null;
        $attendance->punch_out_latitude = null;
        $attendance->user_id = $request->user->id;
        $attendance->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Save punch out time.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function punchOut(Request $request)
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

        $attendance = Attendance::where('user_id', $request->user->id)
                                ->whereNull('punch_out_time')
                                ->first();

        if(!$attendance) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'punch_out' => 'Please punch in first'
                ]
            ]);
        }

        $customerVisit = CustomerVisit::where('user_id', $request->user->id)
                                ->whereNull('check_out_time')
                                ->first();

        if($customerVisit) {
            $customerVisit->check_out_time = Common::nullIfEmpty($time);
            $customerVisit->save();
        }

        $attendance->punch_out_time = Common::nullIfEmpty($time);
        $attendance->punch_out_longitude = Common::nullIfEmpty($longitude);
        $attendance->punch_out_latitude = Common::nullIfEmpty($latitude);
        $attendance->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
