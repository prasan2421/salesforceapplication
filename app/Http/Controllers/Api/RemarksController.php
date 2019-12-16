<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Remarks;

use App\Helpers\Common;

use stdClass;

class RemarksController extends Controller
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
     * Add new feedback.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addRemarks(Request $request)
    {
        $message = trim($request->message);

        $errors = [];
                
        if(!$message) {
            $errors['message'] = 'Message is required';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $remarks = new Remarks;
        $remarks->message = Common::nullIfEmpty($message);
        $remarks->user_id = $request->user->id;
        $remarks->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
