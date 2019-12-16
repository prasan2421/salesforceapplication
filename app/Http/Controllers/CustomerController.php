<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use App\Counter;

use App\Customer;

use App\CustomerType;

use App\CustomerClass;

use App\Division;

use App\Route;

use App\RouteUser;

use App\State;

use App\Exports\CustomersExport;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

use DB;

use Excel;

use Form;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|sales-officer', [ 'except' => [ 'destroy' ] ]);
        $this->middleware('role:admin', [ 'only' => [ 'destroy' ] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('customers.index');
    }

    public function getData() {
        $data = Customer::select('id', 'sap_code', 'name', 'created_at', 'updated_at');

        if(request()->user()->role == 'sales-officer') {
            $routeIds = Route::where('division_id', request()->user()->division_id)->pluck('_id');

            $data->whereIn('route_id', $routeIds);

            if(request()->mine) {
                $data->whereIn('route_id', request()->user()->routes()->pluck('_id'));
            }
        }

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('CustomerController@show', Common::addMineParam($model->id)) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('CustomerController@edit', Common::addMineParam($model->id)) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    if(request()->user()->role == 'admin') {
                        $content .= Form::open(['action' => ['CustomerController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
                        $content .= '<button class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Delete" type="submit" onclick="return confirm(\'Are you sure you want to delete?\')"><i class="fas fa-trash-alt"></i></button>';
                        $content .= Form::close();
                    }

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
        // $routes = Route::pluck('name', '_id');
        $customerTypes = CustomerType::pluck('name', '_id');
        $customerClasses = CustomerClass::pluck('name', '_id');
        $states = State::pluck('name', '_id');

        return view('customers.create', [
            // 'routes' => $routes,
            'customerTypes' => $customerTypes,
            'customerClasses' => $customerClasses,
            'states' => $states
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
            'route_id' => 'required|exists:routes,_id',
            'customer_type_id' => 'required|exists:customer_types,_id',
            'customer_class_id' => 'required|exists:customer_classes,_id',
            // 'sap_code' => 'required|unique:customers',
            'name' => 'required|regex:/^[a-zA-Z0-9\s.\/_-]+$/',
            'billing_state_id' => 'nullable|exists:states,_id',
            'shipping_state_id' => 'nullable|exists:states,_id'
        ]);

        $route = Route::with(['division', 'state'])->find($request->route_id);
        $division = $route->division;
        $state = $route->state;
        $prefix = $division->abbreviation . '_' . $state->abbreviation . '_';

        $counter = Counter::where('name', 'retailer-code')
                        ->where('prefix', $prefix)
                        ->first();
        if(!$counter) {
            $counter = new Counter;
            $counter->name = 'retailer-code';
            $counter->prefix = $prefix;
            $counter->count = 0;
        }

        $counter->count = $counter->count + 1;
        $counter->save();

        $customer = new Customer;
        $customer->division_id = $division->id;
        $customer->state_id = $state->id;
        $customer->route_id = Common::nullIfEmpty($request->route_id);
        $customer->customer_type_id = Common::nullIfEmpty($request->customer_type_id);
        $customer->customer_class_id = Common::nullIfEmpty($request->customer_class_id);
        // $customer->sap_code = Common::nullIfEmpty($request->sap_code);
        $customer->sap_code = $counter->prefix . $counter->count;
        $customer->name = Common::nullIfEmpty($request->name);
        // $customer->class = Common::nullIfEmpty($request->class);
        $customer->gst_number = Common::nullIfEmpty($request->gst_number);
        $customer->town = Common::nullIfEmpty($request->town);
        $customer->longitude = Common::nullIfEmpty($request->longitude);
        $customer->latitude = Common::nullIfEmpty($request->latitude);
        $customer->owner_name = Common::nullIfEmpty($request->owner_name);
        $customer->owner_email = Common::nullIfEmpty($request->owner_email);
        $customer->owner_contact_number = Common::nullIfEmpty($request->owner_contact_number);
        // $customer->billing_state = Common::nullIfEmpty($request->billing_state);
        $customer->billing_state_id = Common::nullIfEmpty($request->billing_state_id);
        $customer->billing_district = Common::nullIfEmpty($request->billing_district);
        $customer->billing_city = Common::nullIfEmpty($request->billing_city);
        $customer->billing_address = Common::nullIfEmpty($request->billing_address);
        $customer->billing_pincode = Common::nullIfEmpty($request->billing_pincode);
        // $customer->shipping_state = Common::nullIfEmpty($request->shipping_state);
        $customer->shipping_state_id = Common::nullIfEmpty($request->shipping_state_id);
        $customer->shipping_district = Common::nullIfEmpty($request->shipping_district);
        $customer->shipping_city = Common::nullIfEmpty($request->shipping_city);
        $customer->shipping_address = Common::nullIfEmpty($request->shipping_address);
        $customer->shipping_pincode = Common::nullIfEmpty($request->shipping_pincode);
        $customer->created_by = request()->user()->id;
        $customer->user_id = request()->user()->id;
        $customer->save();

        return redirect()
                ->action('CustomerController@index', Common::addMineParam())
                ->with('success', 'Customer added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = Customer::query();
//        $customer = Customer::find($id);
//        dd( $customer->customerType->productType->name );

        if(request()->user()->role == 'sales-officer') {
            $routeIds = Route::where('division_id', request()->user()->division_id)->pluck('_id');

            $query->whereIn('route_id', $routeIds);
        }

        $customer = $query->findOrFail($id);

        return view('customers.show', [
            'customer' => $customer
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
        $query = Customer::query();

        if(request()->user()->role == 'sales-officer') {
            $routeIds = Route::where('division_id', request()->user()->division_id)->pluck('_id');

            $query->whereIn('route_id', $routeIds);
        }

        $customer = $query->findOrFail($id);

        // $routes = Route::pluck('name', '_id');
        $customerTypes = CustomerType::pluck('name', '_id');
        $customerClasses = CustomerClass::pluck('name', '_id');
        $states = State::pluck('name', '_id');

        return view('customers.edit', [
            'customer' => $customer,
            // 'routes' => $routes,
            'customerTypes' => $customerTypes,
            'customerClasses' => $customerClasses,
            'states' => $states
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
        $query = Customer::query();

        if(request()->user()->role == 'sales-officer') {
            $routeIds = Route::where('division_id', request()->user()->division_id)->pluck('_id');

            $query->whereIn('route_id', $routeIds);
        }

        $customer = $query->findOrFail($id);

        $request->validate([
            'route_id' => 'required|exists:routes,_id',
            'customer_type_id' => 'required|exists:customer_types,_id',
            'customer_class_id' => 'required|exists:customer_classes,_id',
            // 'sap_code' => 'required|unique:customers,sap_code,' . $customer->id . ',_id',
            'name' => 'required|regex:/^[a-zA-Z0-9\s.\/_-]+$/',
            'billing_state_id' => 'nullable|exists:states,_id',
            'shipping_state_id' => 'nullable|exists:states,_id'
        ]);

        $customer->route_id = Common::nullIfEmpty($request->route_id);
        $customer->customer_type_id = Common::nullIfEmpty($request->customer_type_id);
        $customer->customer_class_id = Common::nullIfEmpty($request->customer_class_id);
        // $customer->sap_code = Common::nullIfEmpty($request->sap_code);
        $customer->name = Common::nullIfEmpty($request->name);
        // $customer->class = Common::nullIfEmpty($request->class);
        $customer->gst_number = Common::nullIfEmpty($request->gst_number);
        $customer->town = Common::nullIfEmpty($request->town);
        $customer->longitude = Common::nullIfEmpty($request->longitude);
        $customer->latitude = Common::nullIfEmpty($request->latitude);
        $customer->owner_name = Common::nullIfEmpty($request->owner_name);
        $customer->owner_email = Common::nullIfEmpty($request->owner_email);
        $customer->owner_contact_number = Common::nullIfEmpty($request->owner_contact_number);
        // $customer->billing_state = Common::nullIfEmpty($request->billing_state);
        $customer->billing_state_id = Common::nullIfEmpty($request->billing_state_id);
        $customer->billing_district = Common::nullIfEmpty($request->billing_district);
        $customer->billing_city = Common::nullIfEmpty($request->billing_city);
        $customer->billing_address = Common::nullIfEmpty($request->billing_address);
        $customer->billing_pincode = Common::nullIfEmpty($request->billing_pincode);
        // $customer->shipping_state = Common::nullIfEmpty($request->shipping_state);
        $customer->shipping_state_id = Common::nullIfEmpty($request->shipping_state_id);
        $customer->shipping_district = Common::nullIfEmpty($request->shipping_district);
        $customer->shipping_city = Common::nullIfEmpty($request->shipping_city);
        $customer->shipping_address = Common::nullIfEmpty($request->shipping_address);
        $customer->shipping_pincode = Common::nullIfEmpty($request->shipping_pincode);
        $customer->save();

        return redirect()
                ->action('CustomerController@index', Common::addMineParam())
                ->with('success', 'Customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        $customer->delete();

        return redirect()
                ->action('CustomerController@index')
                ->with('success', 'Customer deleted successfully');
    }

    public function exportExcel() {
        return Excel::download(new CustomersExport(), 'retailers.xlsx');
    }

    public function exportCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customers.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'CLASS',
            'TYPE',
            'GST NUMBER',
            'TOWN',
            'LONGITUDE',
            'LATITUDE',
            'OWNER NAME',
            'OWNER EMAIL',
            'OWNER CONTACT NUMBER',
            'BILLING STATE',
            'BILLING DISTRICT',
            'BILLING CITY',
            'BILLING ADDRESS',
            'BILLING PINCODE',
            'SHIPPING STATE',
            'SHIPPING DISTRICT',
            'SHIPPING CITY',
            'SHIPPING ADDRESS',
            'SHIPPING PINCODE',
            'BEAT SAP CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        Customer::orderBy('_id')
        ->with(['customerClass', 'customerType', 'billingState', 'shippingState', 'route'])
        ->chunk(10000, function($customers) use ($writer) {
            foreach($customers as $customer) {
                $values = [];
                $values[] = $customer->sap_code;
                $values[] = $customer->name;
                $values[] = $customer->customerClass ? $customer->customerClass->name : '';
                $values[] = $customer->customerType ? $customer->customerType->name : '';
                $values[] = $customer->gst_number;
                $values[] = $customer->town;
                $values[] = $customer->longitude;
                $values[] = $customer->latitude;
                $values[] = $customer->owner_name;
                $values[] = $customer->owner_email;
                $values[] = $customer->owner_contact_number;
                $values[] = $customer->billingState ? $customer->billingState->name : '';
                $values[] = $customer->billing_district;
                $values[] = $customer->billing_city;
                $values[] = $customer->billing_address;
                $values[] = $customer->billing_pincode;
                $values[] = $customer->shippingState ? $customer->shippingState->name : '';
                $values[] = $customer->shipping_district;
                $values[] = $customer->shipping_city;
                $values[] = $customer->shipping_address;
                $values[] = $customer->shipping_pincode;
                $values[] = $customer->route ? $customer->route->sap_code : '';
                $values[] = $customer->route ? $customer->route->name : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportUnassignedToCsv() {
        ini_set('max_execution_time', 1200);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customers-unassigned.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'CLASS',
            'TYPE',
            'GST NUMBER',
            'TOWN',
            'LONGITUDE',
            'LATITUDE',
            'OWNER NAME',
            'OWNER EMAIL',
            'OWNER CONTACT NUMBER',
            'BILLING STATE',
            'BILLING DISTRICT',
            'BILLING CITY',
            'BILLING ADDRESS',
            'BILLING PINCODE',
            'SHIPPING STATE',
            'SHIPPING DISTRICT',
            'SHIPPING CITY',
            'SHIPPING ADDRESS',
            'SHIPPING PINCODE',
            'BEAT SAP CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $assignedRouteIds = RouteUser::pluck('route_id')->toArray();

        $assignedRouteIds = array_unique($assignedRouteIds);

        Customer::whereNotIn('route_id', $assignedRouteIds)
        ->with(['customerClass', 'customerType', 'billingState', 'shippingState', 'route'])
        ->chunk(10000, function($customers) use ($writer) {
            foreach($customers as $customer) {
                $values = [];
                $values[] = $customer->sap_code;
                $values[] = $customer->name;
                $values[] = $customer->customerClass ? $customer->customerClass->name : '';
                $values[] = $customer->customerType ? $customer->customerType->name : '';
                $values[] = $customer->gst_number;
                $values[] = $customer->town;
                $values[] = $customer->longitude;
                $values[] = $customer->latitude;
                $values[] = $customer->owner_name;
                $values[] = $customer->owner_email;
                $values[] = $customer->owner_contact_number;
                $values[] = $customer->billingState ? $customer->billingState->name : '';
                $values[] = $customer->billing_district;
                $values[] = $customer->billing_city;
                $values[] = $customer->billing_address;
                $values[] = $customer->billing_pincode;
                $values[] = $customer->shippingState ? $customer->shippingState->name : '';
                $values[] = $customer->shipping_district;
                $values[] = $customer->shipping_city;
                $values[] = $customer->shipping_address;
                $values[] = $customer->shipping_pincode;
                $values[] = $customer->route ? $customer->route->sap_code : '';
                $values[] = $customer->route ? $customer->route->name : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportWithoutStateCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customers-without-state.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'CLASS',
            'TYPE',
            'GST NUMBER',
            'TOWN',
            'LONGITUDE',
            'LATITUDE',
            'OWNER NAME',
            'OWNER EMAIL',
            'OWNER CONTACT NUMBER',
            'BILLING STATE',
            'BILLING DISTRICT',
            'BILLING CITY',
            'BILLING ADDRESS',
            'BILLING PINCODE',
            'SHIPPING STATE',
            'SHIPPING DISTRICT',
            'SHIPPING CITY',
            'SHIPPING ADDRESS',
            'SHIPPING PINCODE',
            'BEAT SAP CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $routeIds = Route::whereNull('state_id')->pluck('_id');

        Customer::whereIn('route_id', $routeIds)
        ->with(['customerClass', 'customerType', 'billingState', 'shippingState', 'route'])
        ->chunk(10000, function($customers) use ($writer) {
            foreach($customers as $customer) {
                $values = [];
                $values[] = $customer->sap_code;
                $values[] = $customer->name;
                $values[] = $customer->customerClass ? $customer->customerClass->name : '';
                $values[] = $customer->customerType ? $customer->customerType->name : '';
                $values[] = $customer->gst_number;
                $values[] = $customer->town;
                $values[] = $customer->longitude;
                $values[] = $customer->latitude;
                $values[] = $customer->owner_name;
                $values[] = $customer->owner_email;
                $values[] = $customer->owner_contact_number;
                $values[] = $customer->billingState ? $customer->billingState->name : '';
                $values[] = $customer->billing_district;
                $values[] = $customer->billing_city;
                $values[] = $customer->billing_address;
                $values[] = $customer->billing_pincode;
                $values[] = $customer->shippingState ? $customer->shippingState->name : '';
                $values[] = $customer->shipping_district;
                $values[] = $customer->shipping_city;
                $values[] = $customer->shipping_address;
                $values[] = $customer->shipping_pincode;
                $values[] = $customer->route ? $customer->route->sap_code : '';
                $values[] = $customer->route ? $customer->route->name : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportWithoutRouteCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customers-without-beat.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'CLASS',
            'TYPE',
            'GST NUMBER',
            'TOWN',
            'LONGITUDE',
            'LATITUDE',
            'OWNER NAME',
            'OWNER EMAIL',
            'OWNER CONTACT NUMBER',
            'BILLING STATE',
            'BILLING DISTRICT',
            'BILLING CITY',
            'BILLING ADDRESS',
            'BILLING PINCODE',
            'SHIPPING STATE',
            'SHIPPING DISTRICT',
            'SHIPPING CITY',
            'SHIPPING ADDRESS',
            'SHIPPING PINCODE',
            'BEAT SAP CODE',
            'BEAT NAME',
            'DSM CODE',
            'DSM NAME'
        ]);
        $writer->addRow($headingRow);

        $routeIds = Route::pluck('_id');

        Customer::whereNotIn('route_id', $routeIds)
        ->with(['customerClass', 'customerType', 'billingState', 'shippingState', 'route', 'user'])
        ->chunk(10000, function($customers) use ($writer) {
            foreach($customers as $customer) {
                $values = [];
                $values[] = $customer->sap_code;
                $values[] = $customer->name;
                $values[] = $customer->customerClass ? $customer->customerClass->name : '';
                $values[] = $customer->customerType ? $customer->customerType->name : '';
                $values[] = $customer->gst_number;
                $values[] = $customer->town;
                $values[] = $customer->longitude;
                $values[] = $customer->latitude;
                $values[] = $customer->owner_name;
                $values[] = $customer->owner_email;
                $values[] = $customer->owner_contact_number;
                $values[] = $customer->billingState ? $customer->billingState->name : '';
                $values[] = $customer->billing_district;
                $values[] = $customer->billing_city;
                $values[] = $customer->billing_address;
                $values[] = $customer->billing_pincode;
                $values[] = $customer->shippingState ? $customer->shippingState->name : '';
                $values[] = $customer->shipping_district;
                $values[] = $customer->shipping_city;
                $values[] = $customer->shipping_address;
                $values[] = $customer->shipping_pincode;
                $values[] = $customer->route ? $customer->route->sap_code : '';
                $values[] = $customer->route ? $customer->route->name : '';
                $values[] = $customer->user ? $customer->user->emp_code : '';
                $values[] = $customer->user ? $customer->user->name : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportBackupCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customers-backup.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'CLASS',
            'TYPE',
            'GST NUMBER',
            'TOWN',
            'LONGITUDE',
            'LATITUDE',
            'OWNER NAME',
            'OWNER EMAIL',
            'OWNER CONTACT NUMBER',
            'BILLING STATE',
            'BILLING DISTRICT',
            'BILLING CITY',
            'BILLING ADDRESS',
            'BILLING PINCODE',
            'SHIPPING STATE',
            'SHIPPING DISTRICT',
            'SHIPPING CITY',
            'SHIPPING ADDRESS',
            'SHIPPING PINCODE',
            'BEAT SAP CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $beatCodes = [];

        $routeIds = Route::whereIn('sap_code', $beatCodes)->pluck('_id');

        Customer::whereIn('route_id', $routeIds)
        ->with(['customerClass', 'customerType', 'billingState', 'shippingState', 'route'])
        ->chunk(10000, function($customers) use ($writer) {
            foreach($customers as $customer) {
                $values = [];
                $values[] = $customer->sap_code;
                $values[] = $customer->name;
                $values[] = $customer->customerClass ? $customer->customerClass->name : '';
                $values[] = $customer->customerType ? $customer->customerType->name : '';
                $values[] = $customer->gst_number;
                $values[] = $customer->town;
                $values[] = $customer->longitude;
                $values[] = $customer->latitude;
                $values[] = $customer->owner_name;
                $values[] = $customer->owner_email;
                $values[] = $customer->owner_contact_number;
                $values[] = $customer->billingState ? $customer->billingState->name : '';
                $values[] = $customer->billing_district;
                $values[] = $customer->billing_city;
                $values[] = $customer->billing_address;
                $values[] = $customer->billing_pincode;
                $values[] = $customer->shippingState ? $customer->shippingState->name : '';
                $values[] = $customer->shipping_district;
                $values[] = $customer->shipping_city;
                $values[] = $customer->shipping_address;
                $values[] = $customer->shipping_pincode;
                $values[] = $customer->route ? $customer->route->sap_code : '';
                $values[] = $customer->route ? $customer->route->name : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportMobileNumbersCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $db = DB::getMongoDB();

        $collection = $db->selectCollection('customers');

        $pipeline = [
            [
                '$group' => [
                    '_id' => '$owner_contact_number'
                ]
            ]
        ];

        $limit = 10000;
        $totalRecords = 0;

        $results = $collection->aggregate(array_merge($pipeline, [ [ '$count' => 'count' ] ]));
        foreach($results as $row) {
            $totalRecords = $row->count;
        }

        $totalPages = ceil($totalRecords / $limit);
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customer-mobile-numbers.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'OWNER CONTACT NUMBER'
        ]);
        $writer->addRow($headingRow);

        for($page = 1; $page <= $totalPages; $page++) {
            $start = ($page - 1) * $limit;

            $results = $collection->aggregate(
                array_merge(
                    $pipeline,
                    [
                        [ '$skip' => $start ],
                        [ '$limit' => $limit ]
                    ]
                )
            );

            foreach($results as $row) {
                $values = [];
                $values[] = $row->_id;

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        }

        $writer->close();
    }

    public function exportWrongDivisionBeatsCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customers-wrong-division-beats.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'DIVISION',
            'BEAT SAP CODE',
            'BEAT NAME',
            'BEAT DIVISION'
        ]);
        $writer->addRow($headingRow);

        $divisions = Division::get();

        foreach($divisions as $division) {
            $routeIds = $division->routes()->pluck('_id');

            Customer::whereIn('route_id', $routeIds)
            ->where('division_id', '!=', $division->id)
            ->with(['division', 'route.division'])
            ->chunk(10000, function($customers) use ($writer) {
                foreach($customers as $customer) {
                    $values = [];
                    $values[] = $customer->sap_code;
                    $values[] = $customer->name;
                    $values[] = $customer->division ? $customer->division->name : '';
                    $values[] = $customer->route ? $customer->route->sap_code : '';
                    $values[] = $customer->route ? $customer->route->name : '';
                    $values[] = $customer->route && $customer->route->division ? $customer->route->division->name : '';

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            });
        }

        $writer->close();
    }

    public function exportWrongStateBeatsCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customers-wrong-state-beats.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'STATE',
            'BEAT SAP CODE',
            'BEAT NAME',
            'BEAT STATE'
        ]);
        $writer->addRow($headingRow);

        $states = State::get();

        foreach($states as $state) {
            $routeIds = $state->routes()->pluck('_id');

            Customer::whereIn('route_id', $routeIds)
            ->where('state_id', '!=', $state->id)
            ->with(['state', 'route.state'])
            ->chunk(10000, function($customers) use ($writer) {
                foreach($customers as $customer) {
                    $values = [];
                    $values[] = $customer->sap_code;
                    $values[] = $customer->name;
                    $values[] = $customer->state ? $customer->state->name : '';
                    $values[] = $customer->route ? $customer->route->sap_code : '';
                    $values[] = $customer->route ? $customer->route->name : '';
                    $values[] = $customer->route && $customer->route->state ? $customer->route->state->name : '';

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            });
        }

        $writer->close();
    }

    public function setCustomerClassId() {
        $customerClasses = CustomerClass::pluck('_id', 'name');
        $customers = Customer::get();

        foreach($customers as $customer) {
            $class = $customer->class;
            
            if(isset($customerClasses[$class])) {
                $customer->customer_class_id = $customerClasses[$class];
                $customer->save();
            }
        }

        return redirect()
                ->action('CustomerController@index')
                ->with('success', 'Customer class ID set successfully');
    }

    /*public function setStateId() {
        $states = State::pluck('_id', 'name');
        $customers = Customer::get();

        foreach($customers as $customer) {
            $billing_state = $customer->billing_state;
            $shipping_state = $customer->shipping_state;
            
            if(isset($states[$billing_state])) {
                $customer->billing_state_id = $states[$billing_state];
                $customer->save();
            }
            
            if(isset($states[$shipping_state])) {
                $customer->shipping_state_id = $states[$shipping_state];
                $customer->save();
            }
        }

        return redirect()
                ->action('CustomerController@index')
                ->with('success', 'State ID set successfully');
    }*/

    public function setStateId() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '4096M');

        $models = State::select('_id', 'name')->get();

        $states = [];
        foreach($models as $model) {
            $states[strtolower($model->name)] = $model->_id;
        }

        Customer::whereNull('billing_state_id')
        ->orWhereNull('shipping_state_id')
        ->chunk(10000, function($customers) use ($states) {
            foreach($customers as $customer) {
                $billing_state = strtolower($customer->billing_state);
                $shipping_state = strtolower($customer->shipping_state);
                
                if(!$customer->billing_state_id && isset($states[$billing_state])) {
                    $customer->billing_state_id = $states[$billing_state];
                    $customer->save();
                }
                
                if(!$customer->shipping_state_id && isset($states[$shipping_state])) {
                    $customer->shipping_state_id = $states[$shipping_state];
                    $customer->save();
                }
            }
        });

        return redirect()
                ->action('CustomerController@index')
                ->with('success', 'State ID set successfully');
    }

    public function generateSapCode() {
        $customers = Customer::get();
        foreach($customers as $customer) {
            $counter = Counter::where('name', 'retailer-code')->first();
            if(!$counter) {
                $counter = new Counter;
                $counter->name = 'retailer-code';
                $counter->prefix = 'R-';
                $counter->count = 0;
            }

            $counter->count = $counter->count + 1;
            $counter->save();

            $customer->sap_code = $counter->prefix . $counter->count;
            $customer->save();
        }

        return redirect()
                ->action('CustomerController@index')
                ->with('success', 'SAP code set successfully');
    }

    public function removeCustomersWithoutRoute() {
        $routeIds = Route::pluck('_id');

        // $count = Customer::whereNotIn('route_id', $routeIds)->count();

        // echo $count; die;

        Customer::whereNotIn('route_id', $routeIds)->delete();

        return redirect()
                ->action('CustomerController@index')
                ->with('success', 'Customers without route removed successfully');
    }

    public function removeCustomers() {
        $retailerCodes = [];

        Customer::whereIn('sap_code', $retailerCodes)->delete();

        return redirect()
                ->action('CustomerController@index')
                ->with('success', 'Customers removed successfully');
    }
}
