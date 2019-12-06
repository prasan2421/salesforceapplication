<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Category;

use App\Product;

use stdClass;

class CategoryController extends Controller
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
     * Get catgories or products.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCategories($id = null)
    {
    	if(!$id) {
    		$categories = Category::whereNull('parent_id')
    						->select('id', 'name')
    						->get();

    		return response()->json([
    			'success' => true,
    			'data' => [
    				'categories' => $categories,
    				'products' => []
    			],
    			'errors' => new stdClass
    		]);
    	}

    	$category = Category::find($id);

    	if(!$category) {
    		return response()->json([
    			'success' => false,
    			'data' => new stdClass,
    			'errors' => [
    				'id' => 'Invalid ID'
    			]
    		]);
    	}

    	$categories = $category->children()
                            ->select('id', 'name')
                            ->get();

    	if(count($categories) > 0) {
    		return response()->json([
    			'success' => true,
    			'data' => [
    				'categories' => $categories,
    				'products' => []
    			],
    			'errors' => new stdClass
    		]);
    	}

    	$models = $category->products()
                        ->orderBy('is_featured', 'desc')
                        ->select('id', 'sap_code', 'name', 'is_featured', 'unit_id')
                        ->with('unit:name')
                        ->get();

        $products = [];
        foreach($models as $model) {
            $product = new stdClass;
            $product->_id = $model->_id;
            $product->sap_code = $model->sap_code;
            $product->name = $model->name;
            $product->unit = $model->unit ? $model->unit->name : '';
            $product->is_featured = $model->is_featured;

            $products[] = $product;
        }

    	return response()->json([
    		'success' => true,
    		'data' => [
    			'categories' => [],
    			'products' => $products
    		],
    		'errors' => new stdClass
    	]);
    }
}
