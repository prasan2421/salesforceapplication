<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Location;

use stdClass;

class LocationController extends Controller
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
     * Get all locations.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLocations()
    {
        $locations = Location::select('id', 'name')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'locations' => $locations
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get location details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getLocationDetails($id)
    {
        $location = Location::find($id);

        if(!$location) {
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
                'name' => $location->name,
                'description' => $location->description
            ],
            'errors' => new stdClass
        ]);
    }
}
