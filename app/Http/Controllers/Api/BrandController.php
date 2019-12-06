<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Vertical;

use App\Brand;

use stdClass;

class BrandController extends Controller
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
     * Get all brands.
     *
     * @param  int  $vertical_id
     * @return \Illuminate\Http\Response
     */
    public function getBrands($vertical_id)
    {
        $vertical = Vertical::whereHas('users', function($query) {
            $query->where('_id', request()->user->_id);
        })
        ->find($vertical_id);

        if(!$vertical) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'vertical_id' => 'Invalid Vertical ID'
                ]
            ]);
        }

        $brands = Brand::where('vertical_id', $vertical_id)
                        ->select('name')
                        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'brands' => $brands
            ],
            'errors' => new stdClass
        ]);
    }
}
