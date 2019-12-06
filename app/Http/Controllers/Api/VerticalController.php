<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Vertical;

use stdClass;

class VerticalController extends Controller
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
     * Get all verticals.
     *
     * @return \Illuminate\Http\Response
     */
    public function getVerticals()
    {
        $verticals = Vertical::whereHas('users', function($query) {
            $query->where('_id', request()->user->_id);
        })
        ->select('name')
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'verticals' => $verticals
            ],
            'errors' => new stdClass
        ]);
    }
}
