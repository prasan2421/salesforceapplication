<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Brand;

use App\Scheme;

use App\Vertical;

use stdClass;

class SchemeController extends Controller
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
     * Get schemes of products of brand.
     *
     * @param  int  $brand_id
     * @return \Illuminate\Http\Response
     */
    public function getSchemes($brand_id)
    {
    	$brand = Brand::find($brand_id);

        if(!$brand) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'brand_id' => 'Invalid Brand ID'
                ]
            ]);
        }

        $vertical = Vertical::whereHas('users', function($query) {
            $query->where('_id', request()->user->_id);
        })
        ->find($brand->vertical_id);

        if(!$vertical) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'brand_id' => 'Invalid Brand ID'
                ]
            ]);
        }

    	$productIds = $brand->products()->pluck('_id');

    	$currentDate = date('Y-m-d');

    	$models = Scheme::whereIn('product_id', $productIds)
    					->where('start_date', '<=', $currentDate)
    					->where('end_date', '>=', $currentDate)
    					->select('product_id', 'discount', 'end_date')
    					->with(['product:sap_code,name,distributorsellingprice,unit_id', 'product.unit:name'])
    					->get();

    	$schemes = [];

    	foreach($models as $model) {
    		$scheme = new stdClass;
    		$scheme->_id = $model->_id;
            $scheme->product_sap_code = $model->product ? $model->product->sap_code : '';
    		$scheme->product_name = $model->product ? $model->product->name : '';
            $scheme->product_distributorsellingprice = $model->product ? $model->product->distributorsellingprice : '';
            $scheme->product_unit = $model->product && $model->product->unit ? $model->product->unit->name : '';
    		$scheme->discount = $model->discount;
    		$scheme->end_date = $model->end_date;

    		$schemes[] = $scheme;
    	}

		return response()->json([
			'success' => true,
			'data' => [
				'schemes' => $schemes
			],
			'errors' => new stdClass
		]);
    }
}
