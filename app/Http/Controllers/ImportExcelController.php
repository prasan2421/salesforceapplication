<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Brand;

use App\Distributor;

use App\Division;

use App\Import;

use App\Product;

use App\Route;

use App\RouteUser;

use App\Counter;

use App\Customer;

use App\CustomerType;

use App\CustomerClass;

use App\CustomerVisit;

use App\State;

use App\Unit;

use App\User;

use App\Vertical;

use App\Imports\DistributorsImport;

use App\Imports\DsmsImport;

use App\Imports\ProductsImport;

use App\Imports\RoutesImport;

use App\Imports\StatesImport;

use App\Imports\CustomersImport;

use App\Imports\MapDsmSoImport;

use App\Imports\ReplaceBeatCodesImport;

use App\Imports\RemoveDsmRelationshipsImport;

use App\Imports\RemoveRoutesImport;

use App\Imports\SalesOfficersImport;

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

use Excel;

class ImportExcelController extends Controller
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

    public function index() {
        $tables = [
            'states' => 'States',
            'products' => 'Products',
            'routes' => 'Routes',
            'routes_sap_code' => 'Routes with SAP Code',
            'distributors' => 'Distributors',
            'sales_officers' => 'Sales Officers',
            'dsms' => 'DSMs',
            'dsms_non_parakram' => 'DSMs (Non Parakram)',
            'dsms_food' => 'DSMs (All Food Verticals)',
            'dsm_routes' => 'DSM Routes',
            'customers' => 'Customers',
            'customers_sap_code' => 'Customers with SAP Code',
            'replace_beat_codes' => 'Replace Beat Codes',
            'map_dsm_so' => 'Map DSM SO',
            'update_dsms' => 'Update DSMs',
            'map_customer_route' => 'Map Customer Route',
            'map_dsm_route' => 'Map DSM Route',
            'set_customer_name' => 'Set Customer Name',
            'set_state_abbreviation' => 'Set State Abbreviation',
            'set_route_division' => 'Set Route Division',
            'set_route_state' => 'Set Route State',
            'remove_dsm_relationships' => 'Remove DSM Relationships',
            'remove_customer_visits' => 'Remove Customer Visits',
            'remove_customers' => 'Remove Customers',
            'remove_route_users' => 'Remove Route Users',
            'remove_routes' => 'Remove Routes',
        ];

        $fileTypes = [
            'csv' => 'CSV',
            'xlsx' => 'XLXS',
            'ods' => 'ODS'
        ];

        return view('import-excel.index', [
            'tables' => $tables,
            'fileTypes' => $fileTypes
        ]);
    }

    public function store(Request $request) {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '4096M');

        $request->validate([
            'table' => 'required',
            'excel' => 'required|file',
            // 'file_type' => 'required'
        ]);
        
        $table = $request->table;
        $file = $request->file('excel');
        $remarks = $request->remarks;
        // $file_type = $request->file_type;

        // $reader = null;

        // if($file_type == 'csv') {
        //     $reader = ReaderEntityFactory::createCSVReader();
        // }
        // else if($file_type == 'xlsx') {
        //     $reader = ReaderEntityFactory::createXLSXReader();
        // }
        // else {
        //     $reader = ReaderEntityFactory::createODSReader();
        // }

        if($table == 'states') {
            $this->importStates($file);
        }
        else if($table == 'products') {
            $this->importProducts($file);
        }
        else if($table == 'routes') {
            $this->importRoutes($file, $remarks);
        }
        else if($table == 'routes_sap_code') {
            $this->importRoutesSapCode($file, $remarks);
        }
        else if($table == 'distributors') {
            $this->importDistributors($file, $remarks);
        }
        else if($table == 'sales_officers') {
            $this->importSalesOfficers($file);
        }
        else if($table == 'dsms') {
            $this->importDsms($file, $remarks);
        }
        else if($table == 'dsms_non_parakram') {
            $this->importDsmsNonParakram($file, $remarks);
        }
        else if($table == 'dsms_food') {
            $this->importDsmsFood($file);
        }
        else if($table == 'dsm_routes') {
            $this->importDsmRoutes($file, $remarks);
        }
        else if($table == 'customers') {
            $this->importCustomers($file, $remarks);
            // $this->importCustomers($file, $reader);
        }
        else if($table == 'customers_sap_code') {
            $this->importCustomersSapCode($file, $remarks);
        }
        else if($table == 'replace_beat_codes') {
            $this->replaceBeatCodes($file);
        }
        else if($table == 'map_dsm_so') {
            $this->mapDsmSo($file);
        }
        else if($table == 'update_dsms') {
            $this->updateDsms($file);
        }
        else if($table == 'map_customer_route') {
            $this->mapCustomerRoute($file);
        }
        else if($table == 'map_dsm_route') {
            $this->mapDsmRoute($file);
        }
        else if($table == 'set_customer_name') {
            $this->setCustomerName($file);
        }
        else if($table == 'set_state_abbreviation') {
            $this->setStateAbbreviation($file);
        }
        else if($table == 'set_route_division') {
            $this->setRouteDivision($file);
        }
        else if($table == 'set_route_state') {
            $this->setRouteState($file);
        }
        else if($table == 'remove_dsm_relationships') {
            $this->removeDsmRelationships($file);
        }
        else if($table == 'remove_customer_visits') {
            $this->removeCustomerVisits($file);
        }
        else if($table == 'remove_customers') {
            $this->removeCustomers($file);
        }
        else if($table == 'remove_route_users') {
            $this->removeRouteUsers($file);
        }
        else if($table == 'remove_routes') {
            $this->removeRoutes($file);
        }

        return redirect()
                ->action('ImportExcelController@index')
                ->with('success', 'Data imported successfully');
    }

    private function formatHeadingCell($cell) {
        return str_replace(' ', '_', strtolower(trim($cell->getValue())));
    }

    private function getCellValue($cell) {
        return $cell->isString()
                ? utf8_encode(utf8_decode($cell->getValue()))
                : $cell->getValue();
    }

    private function importStates($file) {
        $array = Excel::toArray(new StatesImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $state = new State;
                $state->code = '' . $row['state_code'];
                $state->name = $row['state_name'];
                $state->save();
            }
        }
    }

    private function importProducts($file) {
        // Excel::import(new ProductsImport, $file);

        $array = Excel::toArray(new ProductsImport, $file);
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
                $product->sap_code = '' . $row['item_alias'];
                $product->name = $row['item_name'];
                $product->save();
            }
        }
    }

    private function importRoutes($file, $remarks) {
        $import = new Import;
        $import->type = 'routes';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new RoutesImport, $file);
            if(count($array) > 0) {
                $divisionModels = Division::select('id', 'abbreviation')->get();
                $stateModels = State::select('id', 'abbreviation')->get();
                $divisions = [];
                $states = [];

                foreach($divisionModels as $model) {
                    $key = strtolower($model->abbreviation);
                    $divisions[$key] = $model;
                }

                foreach($stateModels as $model) {
                    $key = strtolower($model->abbreviation);
                    $states[$key] = $model;
                }

                $rows = $array[0];
                foreach($rows as $row) {
                    $divisionCode = strtolower($row['division']);
                    $stateCode = strtolower($row['state']);

                    if(!$row['name']) {
                        continue;
                    }

                    if(!isset($divisions[$divisionCode])) {
                        continue;
                    }

                    if(!isset($states[$stateCode])) {
                        continue;
                    }

                    $division = $divisions[$divisionCode];
                    $state = $states[$stateCode];

                    $prefix = $division->abbreviation . '_' . $state->abbreviation . '_';
                    
                    $counter = Counter::where('name', 'beat-code')
                                    ->where('prefix', $prefix)
                                    ->first();

                    if(!$counter) {
                        $counter = new Counter;
                        $counter->name = 'beat-code';
                        $counter->prefix = $prefix;
                        $counter->count = 0;
                    }

                    $counter->count = $counter->count + 1;
                    $counter->save();

                    $route = new Route;
                    $route->division_id = $division->id;
                    $route->state_id = $state->id;
                    $route->sap_code = $counter->prefix . $counter->count;
                    $route->name = $row['name'];
                    $route->import_id = $import->id;
                    $route->save();
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    private function importRoutesSapCode($file, $remarks) {
        $import = new Import;
        $import->type = 'routes_sap_code';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new RoutesImport, $file);
            if(count($array) > 0) {
                $rows = $array[0];
                foreach($rows as $row) {
                    if(!$row['sap_code']) {
                        continue;
                    }

                    if(Route::where('sap_code', '' . $row['sap_code'])->count() > 0) {
                        continue;
                    }

                    $route = new Route;
                    $route->sap_code = '' . $row['sap_code'];
                    $route->name = $row['name'];
                    $route->import_id = $import->id;
                    $route->save();
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    private function importDistributors($file, $remarks) {
        $import = new Import;
        $import->type = 'distributors';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new DistributorsImport, $file);
            if(count($array) > 0) {
                $arr = Vertical::pluck('_id', 'name');
                $verticals = [];
                foreach($arr as $key=>$value) {
                    $key = strtolower($key);
                    $verticals[$key] = $value;
                }

                $rows = $array[0];
                foreach($rows as $row) {
                    $distributor = Distributor::where('sap_code', '' . $row['sap_code'])->first();
                    if(!$distributor) {
                        $distributor = new Distributor;
                        $distributor->sap_code = '' . $row['sap_code'];
                        $distributor->name = $row['db_name'];
                        $distributor->email = strtolower($row['db_email']);
                        $distributor->contact_number = '' . $row['contact_number'];
                        $distributor->city = $row['city'];
                        $distributor->district = $row['district'];
                        $distributor->state = $row['state'];
                        $distributor->region = $row['region'];
                        $distributor->import_id = $import->id;
                        $distributor->save();
                    }
                    else {
                        $distributor->name = $row['db_name'];
                        $distributor->email = strtolower($row['db_email']);
                        $distributor->contact_number = '' . $row['contact_number'];
                        $distributor->city = $row['city'];
                        $distributor->district = $row['district'];
                        $distributor->state = $row['state'];
                        $distributor->region = $row['region'];
                        $distributor->save();
                    }

                    // if(isset($row['vertical1'])) {
                    //     $vertical1 = Vertical::whereRaw([
                    //         'name' => [
                    //             '$regex' => '^' . $row['vertical1'] . '$',
                    //             '$options' => 'i'
                    //         ]
                    //     ])->first();

                    //     if($vertical1) {
                    //         $distributor->verticals()->attach($vertical1->id);
                    //     }
                    // }

                    // if(isset($row['vertical2'])) {
                    //     $vertical2 = Vertical::whereRaw([
                    //         'name' => [
                    //             '$regex' => '^' . $row['vertical2'] . '$',
                    //             '$options' => 'i'
                    //         ]
                    //     ])->first();

                    //     if($vertical2) {
                    //         $distributor->verticals()->attach($vertical2->id);
                    //     }
                    // }

                    // if(isset($row['vertical3'])) {
                    //     $vertical3 = Vertical::whereRaw([
                    //         'name' => [
                    //             '$regex' => '^' . $row['vertical3'] . '$',
                    //             '$options' => 'i'
                    //         ]
                    //     ])->first();

                    //     if($vertical3) {
                    //         $distributor->verticals()->attach($vertical3->id);
                    //     }
                    // }

                    // if(isset($row['vertical4'])) {
                    //     $vertical4 = Vertical::whereRaw([
                    //         'name' => [
                    //             '$regex' => '^' . $row['vertical4'] . '$',
                    //             '$options' => 'i'
                    //         ]
                    //     ])->first();

                    //     if($vertical4) {
                    //         $distributor->verticals()->attach($vertical4->id);
                    //     }
                    // }

                    $verticalIds = [];

                    if(isset($row['vertical1'])) {
                        $vertical1 = strtolower($row['vertical1']);

                        if(isset($verticals[$vertical1])) {
                            $verticalIds[] = $verticals[$vertical1];
                        }
                    }

                    if(isset($row['vertical2'])) {
                        $vertical2 = strtolower($row['vertical2']);

                        if(isset($verticals[$vertical2])) {
                            $verticalIds[] = $verticals[$vertical2];
                        }
                    }

                    if(isset($row['vertical3'])) {
                        $vertical3 = strtolower($row['vertical3']);

                        if(isset($verticals[$vertical3])) {
                            $verticalIds[] = $verticals[$vertical3];
                        }
                    }

                    if(isset($row['vertical4'])) {
                        $vertical4 = strtolower($row['vertical4']);

                        if(isset($verticals[$vertical4])) {
                            $verticalIds[] = $verticals[$vertical4];
                        }
                    }

                    if(isset($row['vertical5'])) {
                        $vertical5 = strtolower($row['vertical5']);

                        if(isset($verticals[$vertical5])) {
                            $verticalIds[] = $verticals[$vertical5];
                        }
                    }

                    if(isset($row['vertical6'])) {
                        $vertical6 = strtolower($row['vertical6']);

                        if(isset($verticals[$vertical6])) {
                            $verticalIds[] = $verticals[$vertical6];
                        }
                    }

                    if(count($verticalIds) > 0) {
                        $distributor->verticals()->attach($verticalIds);
                    }
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    private function importSalesOfficers($file) {
        $array = Excel::toArray(new SalesOfficersImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                // if(!filter_var($row['company_mail_id'], FILTER_VALIDATE_EMAIL)) {
                //     continue;
                // }

                $salesOfficer = new User;
                $salesOfficer->role = 'sales-officer';
                $salesOfficer->emp_code = '' . $row['emp_id'];
                $salesOfficer->name = $row['emp_name'];
                $salesOfficer->email = $row['company_mail_id'] ? strtolower($row['company_mail_id']) : null;
                $salesOfficer->username = $salesOfficer->emp_code;
                $salesOfficer->password = bcrypt('123456');
                $salesOfficer->is_active = true;
                $salesOfficer->save();
            }
        }
    }

    /*
    private function importDsms($file) {
        $array = Excel::toArray(new DsmsImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $salesOfficer = User::where('emp_code', '' . $row['so_emp_code'])->first();
                if(!$salesOfficer) {
                    $salesOfficer = new User;
                    $salesOfficer->role = 'sales-officer';
                    $salesOfficer->emp_code = '' . $row['so_emp_code'];
                    $salesOfficer->name = $row['so_name'];
                    $salesOfficer->email = strtolower(str_replace(' ', '_', $row['so_name'])) . '@example.com';
                    $salesOfficer->username = explode('@', $salesOfficer->email)[0];
                    $salesOfficer->password = bcrypt('123456');
                    $salesOfficer->save();
                }

                $dsm = User::where('emp_code', '' . $row['emp_code'])->first();
                if(!$dsm) {
                    $distributor = Distributor::where('sap_code', $row['db_sap_code'])->first();

                    $dsm = new User;
                    $dsm->role = 'dsm';
                    $dsm->sales_officer_id = $salesOfficer->id;
                    $dsm->distributor_id = $distributor ? $distributor->id : null;
                    $dsm->emp_code = '' . $row['emp_code'];
                    $dsm->name = $row['name'];
                    $dsm->gender = strtolower($row['gender']);
                    $dsm->date_of_birth = date('Y-m-d', strtotime($row['date_of_birth']));
                    $dsm->email = strtolower($row['email']);
                    $dsm->contact_number = '' . $row['contact_number'];
                    $dsm->address = $row['address'];
                    $dsm->username = explode('@', $dsm->email)[0];
                    $dsm->password = bcrypt('123456');
                    $dsm->save();

                    $vertical1 = Vertical::where('name', $row['vertical1'])->first();
                    if($vertical1) {
                        $dsm->verticals()->attach($vertical1->id);
                    }

                    $vertical2 = Vertical::where('name', $row['vertical2'])->first();
                    if($vertical2) {
                        $dsm->verticals()->attach($vertical2->id);
                    }
                }

                $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();

                $routeUser = new RouteUser;
                $routeUser->route_id = $route ? $route->id : null;
                $routeUser->user_id = $dsm->id;
                $routeUser->frequency = strtolower($row['beat_frequency']);
                $routeUser->day = strtolower($row['day_of_visit']);
                $routeUser->save();
            }
        }
    }
    */

    private function importDsms($file, $remarks) {
        $import = new Import;
        $import->type = 'dsms';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new DsmsImport, $file);
            if(count($array) > 0) {
                $arr = Vertical::pluck('_id', 'name');
                $verticals = [];
                foreach($arr as $key=>$value) {
                    $key = strtolower($key);
                    $verticals[$key] = $value;
                }

                $rows = $array[0];
                foreach($rows as $row) {
                    // if(!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                    //     continue;
                    // }

                    $salesOfficer = null;

                    if($row['so_emp_code']) {
                        $salesOfficer = User::where('emp_code', '' . $row['so_emp_code'])->first();
                        if(!$salesOfficer) {
                            $salesOfficer = new User;
                            $salesOfficer->role = 'sales-officer';
                            $salesOfficer->emp_code = '' . $row['so_emp_code'];
                            $salesOfficer->name = $row['so_name'];
                            if(isset($row['so_email'])) {
                                $salesOfficer->email = $row['so_email'];
                            }
                            else {
                                $salesOfficer->email = null;
                            }
                            // $salesOfficer->email = strtolower(str_replace(' ', '_', $row['so_name'])) . '_' . $row['so_emp_code'] . '@example.com';
                            // $salesOfficer->username = explode('@', $salesOfficer->email)[0];
                            $salesOfficer->username = $salesOfficer->emp_code;
                            $salesOfficer->password = bcrypt('123456');
                            $salesOfficer->is_active = true;
                            $salesOfficer->import_id = $import->id;
                            $salesOfficer->save();
                        }
                    }

                    $dsm = User::where('emp_code', '' . $row['emp_code'])->first();
                    if(!$dsm) {
                        // if(User::where('email', strtolower($row['email']))->count() > 0) {
                        //     continue;
                        // }

                        $distributor = Distributor::where('sap_code', '' . $row['db_sap_code'])->first();

                        $dsm = new User;
                        $dsm->role = 'dsm';
                        $dsm->sales_officer_id = $salesOfficer ? $salesOfficer->id : null;
                        $dsm->distributor_id = $distributor ? $distributor->id : null;
                        $dsm->emp_code = '' . $row['emp_code'];
                        $dsm->name = $row['name'];
                        $dsm->gender = strtolower($row['gender']);
                        $dsm->date_of_birth = $row['date_of_birth']
                                                ? date('Y-m-d', strtotime($row['date_of_birth']))
                                                : null;
                        $dsm->email = $row['email'] ? strtolower($row['email']) : null;
                        $dsm->contact_number = '' . $row['contact_number'];
                        $dsm->address = $row['address'];
                        // $dsm->username = explode('@', $dsm->email)[0];
                        $dsm->username = $dsm->emp_code;
                        $dsm->password = bcrypt('123456');
                        $dsm->is_active = true;
                        $dsm->import_id = $import->id;
                        $dsm->save();

                        // if(isset($row['vertical1'])) {
                        //     $vertical1 = Vertical::whereRaw([
                        //         'name' => [
                        //             '$regex' => '^' . $row['vertical1'] . '$',
                        //             '$options' => 'i'
                        //         ]
                        //     ])->first();

                        //     if($vertical1) {
                        //         $dsm->verticals()->attach($vertical1->id);
                        //     }
                        // }

                        // if(isset($row['vertical2'])) {
                        //     $vertical2 = Vertical::whereRaw([
                        //         'name' => [
                        //             '$regex' => '^' . $row['vertical2'] . '$',
                        //             '$options' => 'i'
                        //         ]
                        //     ])->first();
                            
                        //     if($vertical2) {
                        //         $dsm->verticals()->attach($vertical2->id);
                        //     }
                        // }

                        // if(isset($row['vertical3'])) {
                        //     $vertical3 = Vertical::whereRaw([
                        //         'name' => [
                        //             '$regex' => '^' . $row['vertical3'] . '$',
                        //             '$options' => 'i'
                        //         ]
                        //     ])->first();

                        //     if($vertical3) {
                        //         $dsm->verticals()->attach($vertical3->id);
                        //     }
                        // }

                        // if(isset($row['vertical4'])) {
                        //     $vertical4 = Vertical::whereRaw([
                        //         'name' => [
                        //             '$regex' => '^' . $row['vertical4'] . '$',
                        //             '$options' => 'i'
                        //         ]
                        //     ])->first();
                            
                        //     if($vertical4) {
                        //         $dsm->verticals()->attach($vertical4->id);
                        //     }
                        // }

                        $verticalIds = [];

                        if(isset($row['vertical1'])) {
                            $vertical1 = strtolower($row['vertical1']);

                            if(isset($verticals[$vertical1])) {
                                $verticalIds[] = $verticals[$vertical1];
                            }
                        }

                        if(isset($row['vertical2'])) {
                            $vertical2 = strtolower($row['vertical2']);

                            if(isset($verticals[$vertical2])) {
                                $verticalIds[] = $verticals[$vertical2];
                            }
                        }

                        if(isset($row['vertical3'])) {
                            $vertical3 = strtolower($row['vertical3']);

                            if(isset($verticals[$vertical3])) {
                                $verticalIds[] = $verticals[$vertical3];
                            }
                        }

                        if(isset($row['vertical4'])) {
                            $vertical4 = strtolower($row['vertical4']);

                            if(isset($verticals[$vertical4])) {
                                $verticalIds[] = $verticals[$vertical4];
                            }
                        }

                        if(count($verticalIds) > 0) {
                            $dsm->verticals()->attach($verticalIds);
                            if($salesOfficer) {
                                $salesOfficer->verticals()->attach($verticalIds);
                            }
                        }
                    }

                    $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();

                    if($route) {
                        $routeUser = new RouteUser;
                        $routeUser->route_id = $route->id;
                        $routeUser->user_id = $dsm->id;
                        $routeUser->frequency = strtolower($row['beat_frequency']);
                        $routeUser->day = strtolower($row['day_of_visit']);
                        $routeUser->is_active = true;
                        $routeUser->import_id = $import->id;
                        $routeUser->save();

                        if($salesOfficer) {
                            $salesOfficer->routes()->attach($route->id);
                        }

                        if($distributor) {
                            $distributor->routes()->attach($route->id);
                        }
                    }
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    private function importDsmsNonParakram($file, $remarks) {
        $import = new Import;
        $import->type = 'dsms_non_parakram';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new DsmsImport, $file);
            if(count($array) > 0) {
                $divisionModels = Division::select('id', 'abbreviation')->get();
                $stateModels = State::select('id', 'abbreviation')->get();
                $divisions = [];
                $states = [];

                foreach($divisionModels as $model) {
                    $key = strtolower($model->abbreviation);
                    $divisions[$key] = $model;
                }

                foreach($stateModels as $model) {
                    $key = strtolower($model->abbreviation);
                    $states[$key] = $model;
                }

                $arr = Vertical::pluck('_id', 'name');
                $verticals = [];
                foreach($arr as $key=>$value) {
                    $key = strtolower($key);
                    $verticals[$key] = $value;
                }

                $rows = $array[0];
                foreach($rows as $row) {
                    $divisionCode = strtolower($row['division']);
                    $stateCode = strtolower($row['state_code']);

                    if(!isset($divisions[$divisionCode])) {
                        continue;
                    }

                    if(!isset($states[$stateCode])) {
                        continue;
                    }

                    $division = $divisions[$divisionCode];
                    $state = $states[$stateCode];

                    $distributor = Distributor::where('sap_code', '' . $row['db_sap_code'])->first();

                    $salesOfficer = null;

                    if($row['so_emp_code']) {
                        $salesOfficer = User::where('emp_code', '' . $row['so_emp_code'])->first();
                        if(!$salesOfficer) {
                            $salesOfficer = new User;
                            $salesOfficer->role = 'sales-officer';
                            $salesOfficer->division_id = $division->id;
                            $salesOfficer->emp_code = '' . $row['so_emp_code'];
                            $salesOfficer->name = $row['so_name'];
                            if(isset($row['so_email'])) {
                                $salesOfficer->email = $row['so_email'];
                            }
                            else {
                                $salesOfficer->email = null;
                            }
                            $salesOfficer->username = $salesOfficer->emp_code;
                            $salesOfficer->password = bcrypt('123456');
                            $salesOfficer->is_active = true;
                            $salesOfficer->import_id = $import->id;
                            $salesOfficer->save();
                        }
                    }

                    $prefix = $division->abbreviation . '_NP_' . $state->abbreviation . '_';
                    
                    $counter = Counter::where('name', 'dsm-code')
                                    ->where('prefix', $prefix)
                                    ->first();

                    if(!$counter) {
                        $counter = new Counter;
                        $counter->name = 'dsm-code';
                        $counter->prefix = $prefix;
                        $counter->count = 0;
                    }

                    $counter->count = $counter->count + 1;
                    $counter->save();

                    $dsm = new User;
                    $dsm->role = 'dsm';
                    $dsm->sales_officer_id = $salesOfficer ? $salesOfficer->id : null;
                    $dsm->distributor_id = $distributor ? $distributor->id : null;
                    $dsm->division_id = $division->id;
                    $dsm->state_id = $state->id;
                    $dsm->emp_code = $counter->prefix . $counter->count;
                    $dsm->name = $row['name'];
                    $dsm->gender = strtolower($row['gender']);
                    $dsm->date_of_birth = $row['date_of_birth']
                                            ? date('Y-m-d', strtotime($row['date_of_birth']))
                                            : null;
                    $dsm->email = $row['email'] ? strtolower($row['email']) : null;
                    $dsm->contact_number = '' . $row['contact_number'];
                    $dsm->address = $row['address'];
                    $dsm->username = $dsm->emp_code;
                    $dsm->password = bcrypt('123456');
                    $dsm->is_active = true;
                    $dsm->is_non_parakram = true;
                    $dsm->import_id = $import->id;
                    $dsm->save();

                    $verticalIds = [];

                    if(isset($row['vertical1'])) {
                        $vertical1 = strtolower($row['vertical1']);

                        if(isset($verticals[$vertical1])) {
                            $verticalIds[] = $verticals[$vertical1];
                        }
                    }

                    if(isset($row['vertical2'])) {
                        $vertical2 = strtolower($row['vertical2']);

                        if(isset($verticals[$vertical2])) {
                            $verticalIds[] = $verticals[$vertical2];
                        }
                    }

                    if(isset($row['vertical3'])) {
                        $vertical3 = strtolower($row['vertical3']);

                        if(isset($verticals[$vertical3])) {
                            $verticalIds[] = $verticals[$vertical3];
                        }
                    }

                    if(isset($row['vertical4'])) {
                        $vertical4 = strtolower($row['vertical4']);

                        if(isset($verticals[$vertical4])) {
                            $verticalIds[] = $verticals[$vertical4];
                        }
                    }

                    if(count($verticalIds) > 0) {
                        $dsm->verticals()->attach($verticalIds);
                        if($salesOfficer) {
                            $salesOfficer->verticals()->attach($verticalIds);
                        }
                    }
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    private function importDsmsFood($file) {
        $array = Excel::toArray(new DsmsImport, $file);
        if(count($array) > 0) {
            $verticalIds = [];

            $division = Division::where('name', 'Food')->first();
            if($division) {
                $verticalIds = $division->verticals()->pluck('_id')->toArray();
            }

            $rows = $array[0];
            foreach($rows as $row) {
                // if(!filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                //     continue;
                // }

                $salesOfficer = User::where('emp_code', '' . $row['so_emp_code'])->first();
                if(!$salesOfficer) {
                    $salesOfficer = new User;
                    $salesOfficer->role = 'sales-officer';
                    $salesOfficer->emp_code = '' . $row['so_emp_code'];
                    $salesOfficer->name = $row['so_name'];
                    // $salesOfficer->email = strtolower(str_replace(' ', '_', $row['so_name'])) . '_' . $row['so_emp_code'] . '@example.com';
                    // $salesOfficer->username = explode('@', $salesOfficer->email)[0];
                    $salesOfficer->email = null;
                    $salesOfficer->username = $salesOfficer->emp_code;
                    $salesOfficer->password = bcrypt('123456');
                    $salesOfficer->is_active = true;
                    $salesOfficer->save();
                }

                $dsm = User::where('emp_code', '' . $row['emp_code'])->first();
                if(!$dsm) {
                    // if(User::where('email', strtolower($row['email']))->count() > 0) {
                    //     continue;
                    // }

                    $distributor = Distributor::where('sap_code', '' . $row['db_sap_code'])->first();

                    $dsm = new User;
                    $dsm->role = 'dsm';
                    $dsm->sales_officer_id = $salesOfficer->id;
                    $dsm->distributor_id = $distributor ? $distributor->id : null;
                    $dsm->emp_code = '' . $row['emp_code'];
                    $dsm->name = $row['name'];
                    $dsm->gender = strtolower($row['gender']);
                    $dsm->date_of_birth = $row['date_of_birth']
                                            ? date('Y-m-d', strtotime($row['date_of_birth']))
                                            : null;
                    $dsm->email = $row['email'] ? strtolower($row['email']) : null;
                    $dsm->contact_number = '' . $row['contact_number'];
                    $dsm->address = $row['address'];
                    // $dsm->username = explode('@', $dsm->email)[0];
                    $dsm->username = $dsm->emp_code;
                    $dsm->password = bcrypt('123456');
                    $dsm->is_active = true;
                    $dsm->save();

                    // $vertical1 = Vertical::where('name', $row['vertical1'])->first();
                    // if($vertical1) {
                    //     $dsm->verticals()->attach($vertical1->id);
                    // }

                    // $vertical2 = Vertical::where('name', $row['vertical2'])->first();
                    // if($vertical2) {
                    //     $dsm->verticals()->attach($vertical2->id);
                    // }

                    // foreach($verticalIds as $verticalId) {
                    //     $dsm->verticals()->attach($verticalId);
                    // }

                    $dsm->verticals()->attach($verticalIds);
                }

                $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();

                $routeUser = new RouteUser;
                $routeUser->route_id = $route ? $route->id : null;
                $routeUser->user_id = $dsm->id;
                $routeUser->frequency = strtolower($row['beat_frequency']);
                $routeUser->day = strtolower($row['day_of_visit']);
                $routeUser->is_active = true;
                $routeUser->save();

                if($salesOfficer && $route) {
                    $salesOfficer->routes()->attach($route->id);
                }
            }
        }
    }

    private function importDsmRoutes($file, $remarks) {
        $import = new Import;
        $import->type = 'dsm_routes';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new DsmsImport, $file);
            if(count($array) > 0) {
                $rows = $array[0];
                foreach($rows as $row) {
                    $dsm = User::where('emp_code', '' . $row['emp_code'])
                                ->with(['salesOfficer', 'distributor'])
                                ->first();
                    $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();

                    if($dsm && $route) {
                        $routeUser = new RouteUser;
                        $routeUser->route_id = $route->id;
                        $routeUser->user_id = $dsm->id;
                        $routeUser->frequency = strtolower($row['beat_frequency']);
                        $routeUser->day = strtolower($row['day_of_visit']);
                        $routeUser->is_active = true;
                        $routeUser->import_id = $import->id;
                        $routeUser->save();

                        if($dsm->salesOfficer) {
                            $dsm->salesOfficer->routes()->attach($route->id);
                        }

                        if($dsm->distributor) {
                            $dsm->distributor->routes()->attach($route->id);
                        }
                    }
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    private function importCustomers($file, $remarks) {
        $import = new Import;
        $import->type = 'customers';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new CustomersImport, $file);
            if(count($array) > 0) {
                $divisions = Division::pluck('abbreviation', '_id')->toArray();
                $states = State::pluck('abbreviation', '_id')->toArray();

                $rows = $array[0];
                foreach($rows as $row) {
                    $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();

                    if(!$route) {
                        continue;
                    }

                    $customerType = null;
                    if($row['type']) {
                        $customerType = CustomerType::whereRaw([
                            'name' => [
                                '$regex' => '^' . $row['type'] . '$',
                                '$options' => 'i'
                            ]
                        ])->first();

                        if(!$customerType) {
                            $customerType = new CustomerType;
                            $customerType->name = $row['type'];
                            $customerType->import_id = $import->id;
                            $customerType->save();
                        }
                    }

                    $customerClass = null;
                    if($row['class']) {
                        $customerClass = CustomerClass::whereRaw([
                            'name' => [
                                '$regex' => '^' . $row['class'] . '$',
                                '$options' => 'i'
                            ]
                        ])->first();

                        if(!$customerClass) {
                            $customerClass = new CustomerClass;
                            $customerClass->name = $row['class'];
                            $customerClass->import_id = $import->id;
                            $customerClass->save();
                        }
                    }

                    // $billingState = State::whereRaw([
                    //     'name' => [
                    //         '$regex' => '^' . $row['billing_state'] . '$',
                    //         '$options' => 'i'
                    //     ]
                    // ])->first();

                    // $shippingState = State::whereRaw([
                    //     'name' => [
                    //         '$regex' => '^' . $row['shipping_state'] . '$',
                    //         '$options' => 'i'
                    //     ]
                    // ])->first();

                    $division_id = $route->division_id;
                    $state_id = $route->state_id;

                    $prefix = $divisions[$division_id] . '_' . $states[$state_id] . '_';

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
                    $customer->division_id = $division_id;
                    $customer->state_id = $state_id;
                    $customer->route_id = $route->id;
                    $customer->customer_type_id = $customerType ? $customerType->id : null;
                    $customer->customer_class_id = $customerClass ? $customerClass->id : null;
                    // $customer->sap_code = '' . $row['sap_code'];
                    $customer->sap_code = $counter->prefix . $counter->count;
                    $customer->name = $row['name'];
                    // $customer->class = $row['class'];
                    $customer->gst_number = $row['gst_number'];
                    $customer->town = $row['town'];
                    $customer->longitude = '' . $row['longitude'];
                    $customer->latitude = '' . $row['latitude'];
                    $customer->owner_name = $row['owner_name'];
                    $customer->owner_email = strtolower($row['owner_email']);
                    $customer->owner_contact_number = '' . $row['owner_contact_number'];
                    $customer->billing_state = $row['billing_state'];
                    // $customer->billing_state_id = $billingState ? $billingState->id : null;
                    $customer->billing_state_id = $state_id;
                    $customer->billing_district = $row['billing_district'];
                    $customer->billing_city = $row['billing_city'];
                    $customer->billing_address = $row['billing_address'];
                    $customer->billing_pincode = '' . $row['billing_pincode'];
                    $customer->shipping_state = $row['shipping_state'];
                    // $customer->shipping_state_id = $shippingState ? $shippingState->id : null;
                    $customer->shipping_state_id = $state_id;
                    $customer->shipping_district = $row['shipping_district'];
                    $customer->shipping_city = $row['shipping_city'];
                    $customer->shipping_address = $row['shipping_address'];
                    $customer->shipping_pincode = '' . $row['shipping_pincode'];
                    $customer->import_id = $import->id;
                    $customer->save();
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    /*private function importCustomers($file, $reader) {
        $reader->open($file->path());

        foreach ($reader->getSheetIterator() as $sheet) {
            $firstRow = true;
            $headings = [];
            foreach ($sheet->getRowIterator() as $row) {
                $cells = $row->getCells();
                
                if($firstRow) {
                    $firstRow = false;
                    $headings = array_map([$this, 'formatHeadingCell'], $cells);

                    continue;
                }

                $values = array_map([$this, 'getCellValue'], $cells);
                $data = array_combine($headings, $values);

                $customerType = null;
                if($data['type']) {
                    $customerType = CustomerType::whereRaw([
                        'name' => [
                            '$regex' => '^' . $data['type'] . '$',
                            '$options' => 'i'
                        ]
                    ])->first();

                    if(!$customerType) {
                        $customerType = new CustomerType;
                        $customerType->name = $data['type'];
                        $customerType->save();
                    }
                }

                $customerClass = null;
                if($data['class']) {
                    $customerClass = CustomerClass::whereRaw([
                        'name' => [
                            '$regex' => '^' . $data['class'] . '$',
                            '$options' => 'i'
                        ]
                    ])->first();

                    if(!$customerClass) {
                        $customerClass = new CustomerClass;
                        $customerClass->name = $data['class'];
                        $customerClass->save();
                    }
                }

                $billingState = State::whereRaw([
                    'name' => [
                        '$regex' => '^' . $data['billing_state'] . '$',
                        '$options' => 'i'
                    ]
                ])->first();

                $shippingState = State::whereRaw([
                    'name' => [
                        '$regex' => '^' . $data['shipping_state'] . '$',
                        '$options' => 'i'
                    ]
                ])->first();

                $route = Route::where('sap_code', '' . $data['beat_sap_code'])->first();

                $counter = Counter::where('name', 'retailer-code')->first();
                if(!$counter) {
                    $counter = new Counter;
                    $counter->name = 'retailer-code';
                    $counter->prefix = 'R-';
                    $counter->count = 0;
                }

                $counter->count = $counter->count + 1;
                $counter->save();

                $customer = new Customer;
                $customer->customer_type_id = $customerType ? $customerType->id : null;
                $customer->customer_class_id = $customerClass ? $customerClass->id : null;
                // $customer->sap_code = '' . $data['sap_code'];
                $customer->sap_code = $counter->prefix . $counter->count;
                $customer->name = $data['name'];
                // $customer->class = $data['class'];
                $customer->gst_number = $data['gst_number'];
                $customer->town = $data['town'];
                $customer->longitude = '' . $data['longitude'];
                $customer->latitude = '' . $data['latitude'];
                $customer->owner_name = $data['owner_name'];
                $customer->owner_email = strtolower($data['owner_email']);
                $customer->owner_contact_number = '' . $data['owner_contact_number'];
                $customer->billing_state = $data['billing_state'];
                $customer->billing_state_id = $billingState ? $billingState->id : null;
                $customer->billing_district = $data['billing_district'];
                $customer->billing_city = $data['billing_city'];
                $customer->billing_address = $data['billing_address'];
                $customer->billing_pincode = '' . $data['billing_pincode'];
                $customer->shipping_state = $data['shipping_state'];
                $customer->shipping_state_id = $shippingState ? $shippingState->id : null;
                $customer->shipping_district = $data['shipping_district'];
                $customer->shipping_city = $data['shipping_city'];
                $customer->shipping_address = $data['shipping_address'];
                $customer->shipping_pincode = '' . $data['shipping_pincode'];
                $customer->route_id = $route ? $route->id : null;
                $customer->save();
            }
        }

        $reader->close();
    }
    */

    private function importCustomersSapCode($file, $remarks) {
        $import = new Import;
        $import->type = 'customers_sap_code';
        $import->filename = $file->getClientOriginalName();
        $import->remarks = $remarks;
        $import->user_id = request()->user()->id;
        $import->status = 'running';
        $import->is_success = false;
        $import->save();

        try {
            $array = Excel::toArray(new CustomersImport, $file);
            if(count($array) > 0) {
                $rows = $array[0];
                foreach($rows as $row) {
                    $customerType = null;
                    if($row['type']) {
                        $customerType = CustomerType::whereRaw([
                            'name' => [
                                '$regex' => '^' . $row['type'] . '$',
                                '$options' => 'i'
                            ]
                        ])->first();

                        if(!$customerType) {
                            $customerType = new CustomerType;
                            $customerType->name = $row['type'];
                            $customerType->import_id = $import->id;
                            $customerType->save();
                        }
                    }

                    $customerClass = null;
                    if($row['class']) {
                        $customerClass = CustomerClass::whereRaw([
                            'name' => [
                                '$regex' => '^' . $row['class'] . '$',
                                '$options' => 'i'
                            ]
                        ])->first();

                        if(!$customerClass) {
                            $customerClass = new CustomerClass;
                            $customerClass->name = $row['class'];
                            $customerClass->import_id = $import->id;
                            $customerClass->save();
                        }
                    }

                    $billingState = State::whereRaw([
                        'name' => [
                            '$regex' => '^' . $row['billing_state'] . '$',
                            '$options' => 'i'
                        ]
                    ])->first();

                    $shippingState = State::whereRaw([
                        'name' => [
                            '$regex' => '^' . $row['shipping_state'] . '$',
                            '$options' => 'i'
                        ]
                    ])->first();

                    $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();

                    $customer = new Customer;
                    $customer->customer_type_id = $customerType ? $customerType->id : null;
                    $customer->customer_class_id = $customerClass ? $customerClass->id : null;
                    $customer->sap_code = '' . $row['sap_code'];
                    $customer->name = $row['name'];
                    // $customer->class = $row['class'];
                    $customer->gst_number = $row['gst_number'];
                    $customer->town = $row['town'];
                    $customer->longitude = '' . $row['longitude'];
                    $customer->latitude = '' . $row['latitude'];
                    $customer->owner_name = $row['owner_name'];
                    $customer->owner_email = strtolower($row['owner_email']);
                    $customer->owner_contact_number = '' . $row['owner_contact_number'];
                    $customer->billing_state = $row['billing_state'];
                    $customer->billing_state_id = $billingState ? $billingState->id : null;
                    $customer->billing_district = $row['billing_district'];
                    $customer->billing_city = $row['billing_city'];
                    $customer->billing_address = $row['billing_address'];
                    $customer->billing_pincode = '' . $row['billing_pincode'];
                    $customer->shipping_state = $row['shipping_state'];
                    $customer->shipping_state_id = $shippingState ? $shippingState->id : null;
                    $customer->shipping_district = $row['shipping_district'];
                    $customer->shipping_city = $row['shipping_city'];
                    $customer->shipping_address = $row['shipping_address'];
                    $customer->shipping_pincode = '' . $row['shipping_pincode'];
                    $customer->route_id = $route ? $route->id : null;
                    $customer->import_id = $import->id;
                    $customer->save();
                }
            }

            $import->status = 'success';
            $import->is_success = true;
            $import->save();
        }
        catch(\Exception $e) {
            $import->status = 'error';
            $import->is_success = false;
            $import->error_code = $e->getCode();
            $import->error_message = $e->getMessage();
            $import->error_trace = $e->getTraceAsString();
            $import->save();
        }
    }

    private function replaceBeatCodes($file) {
        $array = Excel::toArray(new ReplaceBeatCodesImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $route = Route::where('sap_code', '' . $row['beat_code'])->first();
                if($route) {
                    $route->sap_code = $row['beat_codes_new'];
                    $route->save();
                }
            }
        }
    }

    private function mapDsmSo($file) {
        $array = Excel::toArray(new MapDsmSoImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $salesOfficer = null;

                if($row['so_emp_code']) {
                    $salesOfficer = User::where('emp_code', '' . $row['so_emp_code'])->first();
                    if(!$salesOfficer) {
                        $salesOfficer = new User;
                        $salesOfficer->role = 'sales-officer';
                        $salesOfficer->emp_code = '' . $row['so_emp_code'];
                        $salesOfficer->name = $row['so_name'];
                        if(isset($row['so_email'])) {
                            $salesOfficer->email = $row['so_email'];
                        }
                        else {
                            $salesOfficer->email = null;
                        }
                        $salesOfficer->username = $salesOfficer->emp_code;
                        $salesOfficer->password = bcrypt('123456');
                        $salesOfficer->is_active = true;
                        $salesOfficer->save();
                    }
                }

                $dsm = User::where('emp_code', '' . $row['emp_code'])->first();

                if($salesOfficer && $dsm) {
                    // $dsm->sales_officer_id = $salesOfficer ? $salesOfficer->id : null;
                    $dsm->sales_officer_id = $salesOfficer->id;
                    $dsm->save();
                }
            }
        }
    }

    private function updateDsms($file) {
        $array = Excel::toArray(new DsmsImport, $file);
        if(count($array) > 0) {
            $arr = Vertical::pluck('_id', 'name');
            $verticals = [];
            foreach($arr as $key=>$value) {
                $key = strtolower($key);
                $verticals[$key] = $value;
            }

            $rows = $array[0];
            foreach($rows as $row) {
                $salesOfficer = null;

                if($row['so_emp_code']) {
                    $salesOfficer = User::where('emp_code', '' . $row['so_emp_code'])->first();
                    if(!$salesOfficer) {
                        $salesOfficer = new User;
                        $salesOfficer->role = 'sales-officer';
                        $salesOfficer->emp_code = '' . $row['so_emp_code'];
                        $salesOfficer->name = $row['so_name'];
                        if(isset($row['so_email'])) {
                            $salesOfficer->email = $row['so_email'];
                        }
                        else {
                            $salesOfficer->email = null;
                        }
                        $salesOfficer->username = $salesOfficer->emp_code;
                        $salesOfficer->password = bcrypt('123456');
                        $salesOfficer->is_active = true;
                        $salesOfficer->save();
                    }
                    else {
                        $salesOfficer->name = $row['so_name'];
                        if(isset($row['so_email'])) {
                            $salesOfficer->email = $row['so_email'];
                        }
                        else {
                            $salesOfficer->email = null;
                        }
                        $salesOfficer->save();
                    }
                }

                $distributor = null;
                if(isset($row['db_sap_code'])) {
                    $distributor = Distributor::where('sap_code', '' . $row['db_sap_code'])->first();
                }

                $dsm = User::where('emp_code', '' . $row['emp_code'])->first();
                if(!$dsm) {
                    $dsm = new User;
                    $dsm->role = 'dsm';
                    $dsm->sales_officer_id = $salesOfficer ? $salesOfficer->id : null;
                    $dsm->distributor_id = $distributor ? $distributor->id : null;
                    $dsm->emp_code = '' . $row['emp_code'];
                    $dsm->name = $row['name'];
                    $dsm->gender = strtolower($row['gender']);
                    $dsm->date_of_birth = $row['date_of_birth']
                                            ? date('Y-m-d', strtotime($row['date_of_birth']))
                                            : null;
                    $dsm->email = $row['email'] ? strtolower($row['email']) : null;
                    if(isset($row['contact_number'])) {
                        $dsm->contact_number = '' . $row['contact_number'];
                    }
                    else {
                        $dsm->contact_number = null;
                    }
                    $dsm->address = $row['address'];
                    $dsm->username = $dsm->emp_code;
                    $dsm->password = bcrypt('123456');
                    $dsm->is_active = true;
                    $dsm->save();
                }
                else {
                    $dsm->sales_officer_id = $salesOfficer ? $salesOfficer->id : null;
                    $dsm->distributor_id = $distributor ? $distributor->id : null;
                    $dsm->name = $row['name'];
                    $dsm->gender = strtolower($row['gender']);
                    $dsm->date_of_birth = $row['date_of_birth']
                                            ? date('Y-m-d', strtotime($row['date_of_birth']))
                                            : null;
                    $dsm->email = $row['email'] ? strtolower($row['email']) : null;
                    if(isset($row['contact_number'])) {
                        $dsm->contact_number = '' . $row['contact_number'];
                    }
                    else {
                        $dsm->contact_number = null;
                    }
                    $dsm->address = $row['address'];
                    $dsm->save();
                }

                $verticalIds = [];

                if(isset($row['vertical1'])) {
                    $vertical1 = strtolower($row['vertical1']);

                    if(isset($verticals[$vertical1])) {
                        $verticalIds[] = $verticals[$vertical1];
                    }
                }

                if(isset($row['vertical2'])) {
                    $vertical2 = strtolower($row['vertical2']);

                    if(isset($verticals[$vertical2])) {
                        $verticalIds[] = $verticals[$vertical2];
                    }
                }

                if(isset($row['vertical3'])) {
                    $vertical3 = strtolower($row['vertical3']);

                    if(isset($verticals[$vertical3])) {
                        $verticalIds[] = $verticals[$vertical3];
                    }
                }

                if(isset($row['vertical4'])) {
                    $vertical4 = strtolower($row['vertical4']);

                    if(isset($verticals[$vertical4])) {
                        $verticalIds[] = $verticals[$vertical4];
                    }
                }

                if(count($verticalIds) > 0) {
                    $dsm->verticals()->attach($verticalIds);
                }
            }
        }
    }

    private function mapCustomerRoute($file) {
        $array = Excel::toArray(new MapDsmSoImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $customer = Customer::where('sap_code', '' . $row['sap_code'])->first();
                $route = Route::where('old_sap_code', '' . $row['beat_sap_code'])->first();

                if($customer && $route) {
                    $customer->route_id = $route->id;
                    $customer->save();
                }
            }
        }
    }

    private function mapDsmRoute($file) {
        $array = Excel::toArray(new MapDsmSoImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $dsm = User::where('emp_code', '' . $row['dsm_code'])->first();
                $route = Route::where('old_sap_code', '' . $row['beat_sap_code'])->first();

                if($dsm && $route) {
                    $routeUser = new RouteUser;
                    $routeUser->route_id = $route->id;
                    $routeUser->user_id = $dsm->id;
                    $routeUser->frequency = strtolower($row['frequency']);
                    $routeUser->day = strtolower($row['day_of_visit']);
                    $routeUser->is_active = true;
                    $routeUser->save();

                    if($dsm->salesOfficer) {
                        $dsm->salesOfficer->routes()->attach($route->id);
                    }

                    if($dsm->distributor) {
                        $dsm->distributor->routes()->attach($route->id);
                    }
                }
            }
        }
    }

    private function setCustomerName($file) {
        $array = Excel::toArray(new CustomersImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $customer = Customer::where('sap_code', $row['sap_code'])->first();

                if($customer) {
                    $customer->name = $row['english_names'];
                    $customer->save();
                }
            }
        }
    }

    private function setStateAbbreviation($file) {
        $array = Excel::toArray(new StatesImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $state = State::whereRaw([
                    'name' => [
                        '$regex' => '^' . $row['state_name'] . '$',
                        '$options' => 'i'
                    ]
                ])->first();

                if($state) {
                    $state->abbreviation = $row['abbreviation'];
                    $state->save();
                }
            }
        }
    }

    private function setRouteDivision($file) {
        $array = Excel::toArray(new RoutesImport, $file);
        if(count($array) > 0) {
            $arr = Division::pluck('_id', 'name');
            $divisions = [];
            foreach($arr as $key=>$value) {
                $key = strtolower($key);
                $divisions[$key] = $value;
            }

            $rows = $array[0];
            foreach($rows as $row) {
                if(!$row['division']) {
                    continue;
                }
                
                $route = Route::where('sap_code', '' . $row['beat_code'])->first();
                $division = strtolower($row['division']);

                if($route && isset($divisions[$division])) {
                    $route->division_id = $divisions[$division];
                    $route->save();
                }
            }
        }
    }

    private function setRouteState($file) {
        $array = Excel::toArray(new RoutesImport, $file);
        if(count($array) > 0) {
            $arr = State::pluck('_id', 'abbreviation');
            $states = [];
            foreach($arr as $key=>$value) {
                $key = strtolower($key);
                $states[$key] = $value;
            }

            $rows = $array[0];
            foreach($rows as $row) {
                if(!$row['state_code']) {
                    continue;
                }
                
                $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();
                $state = strtolower($row['state_code']);

                if($route && isset($states[$state])) {
                    $route->state_id = $states[$state];
                    $route->save();
                }
            }
        }
    }

    private function removeDsmRelationships($file) {
        $array = Excel::toArray(new RemoveDsmRelationshipsImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $user = User::where('emp_code', '' . $row['emp_code'])->first();

                if($user) {
                    $user->sales_officer_id = null;
                    $user->distributor_id = null;
                    $user->save();

                    $user->verticals()->sync([]);
                    $user->routeUsers()->delete();
                }
            }
        }
    }

    private function removeCustomerVisits($file) {
        $array = Excel::toArray(new RemoveRoutesImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $route = Route::where('sap_code', '' . $row['beat_code'])->first();

                if($route) {
                    $customerIds = Customer::where('route_id', $route->id)->pluck('_id');
                    CustomerVisit::whereIn('customer_id', $customerIds)->delete();
                }
            }
        }
    }

    private function removeCustomers($file) {
        $array = Excel::toArray(new RemoveRoutesImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $route = Route::where('sap_code', '' . $row['beat_code'])->first();

                if($route) {
                    $route->customers()->delete();
                }
            }
        }
    }

    private function removeRouteUsers($file) {
        $array = Excel::toArray(new RemoveRoutesImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $route = Route::where('sap_code', '' . $row['beat_codes'])->first();

                if($route) {
                    $route->routeUsers()->delete();
                }
            }
        }
    }

    private function removeRoutes($file) {
        $array = Excel::toArray(new RemoveRoutesImport, $file);
        if(count($array) > 0) {
            $rows = $array[0];
            foreach($rows as $row) {
                $route = Route::where('sap_code', '' . $row['beat_codes'])->first();

                if($route) {
                    $route->delete();
                }
            }
        }
    }
}
