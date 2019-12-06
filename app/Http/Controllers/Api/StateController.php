<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Customer;

use App\State;

use stdClass;

class StateController extends Controller
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
     * Get all states.
     *
     * @return \Illuminate\Http\Response
     */
    public function getStates()
    {
        $states = State::select('id', 'name')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'states' => $states
            ],
            'errors' => new stdClass
        ]);
    }
}
