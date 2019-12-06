<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Feedback;

use App\Helpers\Common;

use stdClass;

class FeedbackController extends Controller
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
    public function addFeedback(Request $request)
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

        $feedback = new Feedback;
        $feedback->message = Common::nullIfEmpty($message);
        $feedback->user_id = $request->user->id;
        $feedback->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
