<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Customer;

use App\Schedule;

use App\Helpers\Common;

use stdClass;

class ScheduleController extends Controller
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
     * Get all schedules.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSchedules()
    {
        $schedules = Schedule::where('user_id', request()->user->id)
                        ->select('id', 'date', 'created_at')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'schedules' => $schedules
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get schedule details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getScheduleDetails($id)
    {
        $schedule = Schedule::where('user_id', request()->user->id)
                        ->find($id);

        if(!$schedule) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $customers = $schedule->customers()
                        ->select('name')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $schedule->date,
                'created_at' => $schedule->created_at,
                'customers' => $customers
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Add new schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addSchedule(Request $request)
    {
        $date = trim($request->date);
        $customers = $request->customers;

        $errors = [];
        
        if(!$date) {
            $errors['date'] = 'Date is required';
        }
        else if(!Common::isDateValid($date)) {
            $errors['date'] = 'Date is invalid';
        }
        else if(Schedule::where('date', $date)
                    ->where('user_id', $request->user->id)
                    ->count() > 0) {
            $errors['date'] = 'A record on this date already exists';
        }
        
        if(!is_array($customers) || count($customers) == 0) {
            $errors['customers'] = 'One or more customers are required';
        }
        else {
        	$isInvalid = false;

        	foreach($customers as $customer) {
        		if(Customer::where('_id', $customer)->count() == 0) {
        			$isInvalid = true;
        			break;
        		}
        	}

        	if($isInvalid) {
        		$errors['customers'] = 'One or more customers are invalid';
        	}
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $schedule = new Schedule;
        $schedule->date = Common::nullIfEmpty($date);
        $schedule->user_id = $request->user->id;
        $schedule->save();

        $schedule->customers()->attach($customers);

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Update schedule.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::where('user_id', request()->user->id)
                        ->find($id);

        if(!$schedule) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $date = trim($request->date);
        $customers = $request->customers;

        $errors = [];
        
        if(!$date) {
            $errors['date'] = 'Date is required';
        }
        else if(!Common::isDateValid($date)) {
            $errors['date'] = 'Date is invalid';
        }
        else if(Schedule::where('date', $date)
                    ->where('user_id', $request->user->id)
                    ->where('_id', '!=', $id)
                    ->count() > 0) {
            $errors['date'] = 'A record on this date already exists';
        }
        
        if(!is_array($customers) || count($customers) == 0) {
            $errors['customers'] = 'One or more customers are required';
        }
        else {
            $isInvalid = false;

            foreach($customers as $customer) {
                if(Customer::where('_id', $customer)->count() == 0) {
                    $isInvalid = true;
                    break;
                }
            }

            if($isInvalid) {
                $errors['customers'] = 'One or more customers are invalid';
            }
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $schedule->date = Common::nullIfEmpty($date);
        $schedule->save();

        $schedule->customers()->sync($customers);

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Delete schedule.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteSchedule($id)
    {
        $schedule = Schedule::where('user_id', request()->user->id)
                        ->find($id);

        if(!$schedule) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $schedule->customers()->detach();
        $schedule->delete();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
