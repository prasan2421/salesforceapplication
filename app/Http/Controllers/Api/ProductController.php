<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Vertical;

use App\Brand;

use App\Product;

use stdClass;

class ProductController extends Controller
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
     * Get all products.
     *
     * @param  int  $brand_id
     * @return \Illuminate\Http\Response
     */
    public function getProducts($brand_id)
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

        $models = Product::where('brand_id', $brand_id)
                        ->select('id', 'sap_code', 'name', 'distributorsellingprice', 'mrp', 'unit_id')
                        ->with('unit:name')
                        ->get();

        $products = [];
        foreach($models as $model) {
            $product = new stdClass;
            $product->_id = $model->_id;
            $product->sap_code = $model->sap_code;
            $product->name = $model->name;
            $product->distributorsellingprice = $model->distributorsellingprice;
            $product->mrp = $model->mrp;
            $product->unit = $model->unit ? $model->unit->name : '';

            $products[] = $product;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get scheme products.
     *
     * @param  int  $brand_id
     * @return \Illuminate\Http\Response
     */
    public function getSchemeProducts($brand_id)
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

        $currentDate = date('Y-m-d');

        $models = Product::whereHas('schemes', function($query) use ($currentDate) {
            $query->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate);
        })
        ->where('brand_id', $brand_id)
        ->select('id', 'sap_code', 'name', 'distributorsellingprice', 'unit_id')
        ->with('unit:name')
        ->with(['schemes' => function($query) use ($currentDate) {
            $query->where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate);
        }])
        ->get();

        $products = [];
        foreach($models as $model) {
            $product = new stdClass;
            $product->_id = $model->_id;
            $product->sap_code = $model->sap_code;
            $product->name = $model->name;
            $product->distributorsellingprice = $model->distributorsellingprice;
            $product->unit = $model->unit ? $model->unit->name : '';
            $product->scheme_discount = count($model->schemes) > 0 ? $model->schemes[0]->discount : '';
            $product->scheme_end_date = count($model->schemes) > 0 ? $model->schemes[0]->end_date : '';

            $products[] = $product;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get product details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getProductDetails($id)
    {
        $product = Product::find($id);

        if(!$product) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $vertical = Vertical::whereHas('users', function($query) {
            $query->where('_id', request()->user->_id);
        })
        ->find($product->vertical_id);

        if(!$vertical) {
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
                'name' => $product->name,
                'unit' => $product->unit ? $product->unit->name : ''
            ],
            'errors' => new stdClass
        ]);
    }
}
