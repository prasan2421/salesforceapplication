<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\ErrorLog;

use App\Helpers\Common;

use stdClass;

class ErrorLogController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('token.optional');
    }

    /**
     * Add error log.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addErrorLog(Request $request)
    {
        $url = trim($request->url);
        $method = trim($request->method);
        $status_code = trim($request->status_code);
        $body = trim($request->body);

        $errorLog = new ErrorLog;
        $errorLog->url = Common::nullIfEmpty($url);
        $errorLog->method = Common::nullIfEmpty($method);
        $errorLog->status_code = Common::nullIfEmpty($status_code);
        $errorLog->body = Common::nullIfEmpty($body);
        $errorLog->user_id = $request->user ? $request->user->id : null;
        $errorLog->save();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
