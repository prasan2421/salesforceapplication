<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

// use App\Category;

use App\Division;

use App\Vertical;

use App\Brand;

use App\Unit;

use App\Product;

use Excel;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

use App\Imports\ProductsImport;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('products.index');
    }

    public function getData() {
        $data = Product::select('id', 'name', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('ProductController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('ProductController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['ProductController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
                    $content .= '<button class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" type="submit" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fas fa-trash-alt"></i></button>';
                    $content .= Form::close();

                    $content .= '</div>';

                    return $content;
                })
                ->make();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $categories = Category::pluck('name', '_id');
        $units = Unit::pluck('name', '_id');
        $divisions = Division::pluck('name', '_id');
        $models = Vertical::select('id', 'name', 'division_id')->get();
        $models1 = Brand::select('id', 'name', 'vertical_id')->get();
        $verticals = [];
        $brands = [];

        foreach($models as $model) {
            $verticals[$model->division_id][$model->id] = $model->name;
        }

        foreach($models1 as $model1) {
            $brands[$model1->vertical_id][$model1->id] = $model1->name;
        }

        return view('products.create', [
            // 'categories' => $categories,
            'units' => $units,
            'divisions' => $divisions,
            'verticalsJson' => json_encode($verticals),
            'brandsJson' => json_encode($brands)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'category_id' => 'required|exists:categories,_id',
            'division_id' => 'required|exists:divisions,_id',
            'vertical_id' => 'required|exists:verticals,_id',
            'brand_id' => 'required|exists:brands,_id',
            'unit_id' => 'required|exists:units,_id',
            'sap_code' => 'required|unique:products',
            'name' => 'required'
        ]);

        $product = new Product;
        // $product->category_id = Common::nullIfEmpty($request->category_id);
        $product->division_id = Common::nullIfEmpty($request->division_id);
        $product->vertical_id = Common::nullIfEmpty($request->vertical_id);
        $product->brand_id = Common::nullIfEmpty($request->brand_id);
        $product->unit_id = Common::nullIfEmpty($request->unit_id);
        $product->sap_code = Common::nullIfEmpty($request->sap_code);
        $product->name = Common::nullIfEmpty($request->name);
        $product->is_featured = $request->is_featured ? true : false;
        $product->save();

        return redirect()
                ->action('ProductController@index')
                ->with('success', 'Product added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);

        return view('products.show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        // $categories = Category::pluck('name', '_id');
        $units = Unit::pluck('name', '_id');
        $divisions = Division::pluck('name', '_id');
        $models = Vertical::select('id', 'name', 'division_id')->get();
        $models1 = Brand::select('id', 'name', 'vertical_id')->get();
        $verticals = [];
        $brands = [];

        foreach($models as $model) {
            $verticals[$model->division_id][$model->id] = $model->name;
        }

        foreach($models1 as $model1) {
            $brands[$model1->vertical_id][$model1->id] = $model1->name;
        }

        return view('products.edit', [
            'product' => $product,
            // 'categories' => $categories,
            'units' => $units,
            'divisions' => $divisions,
            'verticalsJson' => json_encode($verticals),
            'brandsJson' => json_encode($brands)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            // 'category_id' => 'required|exists:categories,_id',
            'division_id' => 'required|exists:divisions,_id',
            'vertical_id' => 'required|exists:verticals,_id',
            'brand_id' => 'required|exists:brands,_id',
            'unit_id' => 'required|exists:units,_id',
            'sap_code' => 'required|unique:products,sap_code,' . $product->id . ',_id',
            'name' => 'required'
        ]);

        // $product->category_id = Common::nullIfEmpty($request->category_id);
        $product->division_id = Common::nullIfEmpty($request->division_id);
        $product->vertical_id = Common::nullIfEmpty($request->vertical_id);
        $product->brand_id = Common::nullIfEmpty($request->brand_id);
        $product->unit_id = Common::nullIfEmpty($request->unit_id);
        $product->sap_code = Common::nullIfEmpty($request->sap_code);
        $product->name = Common::nullIfEmpty($request->name);
        $product->is_featured = $request->is_featured ? true : false;
        $product->save();

        return redirect()
                ->action('ProductController@index')
                ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $product->delete();

        return redirect()
                ->action('ProductController@index')
                ->with('success', 'Product deleted successfully');
    }

    public function import() {
        return view('products.import');
    }

    public function saveImport(Request $request) {
        // Excel::import(new ProductsImport, $request->file('csv'));

        $array = Excel::toArray(new ProductsImport, $request->file('csv'));
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $division = Division::where('name', $row['divisions'])->first();
                if(!$division) {
                    $division = new Division;
                    $division->name = $row['divisions'];
                    $division->save();
                }

                $vertical = Vertical::where('name', $row['vertical'])->first();
                if(!$vertical) {
                    $vertical = new Vertical;
                    $vertical->division_id = $division->id;
                    $vertical->name = $row['vertical'];
                    $vertical->save();
                }

                $brand = Brand::where('name', $row['products_brands'])->first();
                if(!$brand) {
                    $brand = new Brand;
                    $brand->division_id = $division->id;
                    $brand->vertical_id = $vertical->id;
                    $brand->name = $row['products_brands'];
                    $brand->save();
                }

                $unit = Unit::where('name', 'piece')->first();

                $product = new Product;
                $product->division_id = $division->id;
                $product->vertical_id = $vertical->id;
                $product->brand_id = $brand->id;
                $product->unit_id = $unit ? $unit->id : null;
                $product->sap_code = $row['item_alias'];
                $product->name = $row['item_name'];
                $product->save();
            }
        }

        return redirect()
                ->action('ProductController@index')
                ->with('success', 'Products imported successfully');
    }

    public function updatePrices() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '4096M');
        
        $products = Product::get();

        $client = new \GuzzleHttp\Client();

        foreach($products as $product) {
            $res = $client->request('GET', 'http://14.192.18.81/publish/api/getSingleItemPrice?mcode=' . $product->sap_code);

            $response = json_decode($res->getBody());

            if($response->status == 'ok') {
                $product->mrp = $response->result->mrp;
                $product->gst = $response->result->gst;
                $product->superdistributorlandingprice = round($response->result->superdistributorlandingprice, 2);
                $product->superdistributorsellingprice = round($response->result->superdistributorsellingprice, 2);
                $product->distributorsellingprice = round($response->result->distributorsellingprice, 2);
                $product->retailersellingprice = round($response->result->retailersellingprice, 2);
                $product->save();
            }
        }

        return redirect()
                ->action('ProductController@index')
                ->with('success', 'Products imported successfully');
    }

    public function exportZeroPriceCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('products-zero-price.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME'
        ]);
        $writer->addRow($headingRow);

        Product::where('distributorsellingprice', 0)
        ->select('sap_code', 'name')
        ->chunk(10000, function($products) use ($writer) {
            foreach($products as $product) {
                $values = [];
                $values[] = $product->sap_code;
                $values[] = $product->name;

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }
}
