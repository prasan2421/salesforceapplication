<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Counter;

use App\Division;

use App\Route;

use App\RouteUser;

use App\State;

use App\User;

use App\Exports\RoutesExport;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

use DB;

use Excel;

use Form;

class RouteController extends Controller
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
        return view('routes.index');
    }

    public function getData() {
        $data = Route::select('id', 'sap_code', 'name', 'created_at', 'updated_at');

        if(request()->user()->role == 'sales-officer') {
            $data->where('division_id', request()->user()->division_id);

            // $data->whereHas('users', function($query) {
            //     $query->where('_id', request()->user()->_id);
            // });
        }

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('RouteController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('RouteController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    if(request()->user()->role == 'admin') {
                        $content .= Form::open(['action' => ['RouteController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        // abort(403);
        $divisions = [];
        $states = State::orderBy('name')->pluck('name', '_id');

        if(request()->user()->role == 'admin') {
            $divisions = Division::pluck('name', '_id');
        }

        return view('routes.create', [
            'divisions' => $divisions,
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
        // abort(403);
        $rules = [];

        if(request()->user()->role == 'admin') {
            $rules['division_id'] = 'required|exists:divisions,_id';
        }

        $rules = array_merge($rules, [
            'state_id' => 'required|exists:states,_id',
            // 'sap_code' => 'required|unique:routes',
            'name' => 'required'
        ]);

        $request->validate($rules);

        $division_id = null;

        if(request()->user()->role == 'admin') {
            $division_id = $request->division_id;
        }
        else if(request()->user()->role == 'sales-officer') {
            $division_id = request()->user()->division_id;
        }

        $division = Division::find($division_id);
        $state = State::find($request->state_id);
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
        $route->division_id = Common::nullIfEmpty($division_id);
        $route->state_id = Common::nullIfEmpty($request->state_id);
        // $route->sap_code = Common::nullIfEmpty($request->sap_code);
        $route->sap_code = $counter->prefix . $counter->count;
        $route->name = Common::nullIfEmpty($request->name);
        $route->created_by = request()->user()->id;
        $route->save();

        // if(request()->user()->role == 'sales-officer') {
        //     request()->user()->routes()->attach($route);
        // }

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'Beat added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $route = Route::findOrFail($id);

        return view('routes.show', [
            'route' => $route
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
        // abort(403);
        $route = Route::findOrFail($id);

        // $divisions = Division::pluck('name', '_id');
        // $states = State::pluck('name', '_id');

        return view('routes.edit', [
            'route' => $route,
            // 'divisions' => $divisions,
            // 'states' => $states
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
        // abort(403);
        $route = Route::findOrFail($id);

        $request->validate([
            // 'division_id' => 'required|exists:divisions,_id',
            // 'state_id' => 'required|exists:states,_id',
            // 'sap_code' => 'required|unique:routes,sap_code,' . $route->id . ',_id',
            'name' => 'required'
        ]);

        // $route->division_id = Common::nullIfEmpty($request->division_id);
        // $route->state_id = Common::nullIfEmpty($request->state_id);
        // $route->sap_code = Common::nullIfEmpty($request->sap_code);
        $route->name = Common::nullIfEmpty($request->name);
        $route->save();

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'Beat updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $route = Route::findOrFail($id);

        $route->delete();

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'Beat deleted successfully');
    }

    public function exportExcel() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '4096M');
        
        return Excel::download(new RoutesExport(), 'beats.xlsx');
    }

    public function exportCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('beats.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'BEAT CODE',
            'BEAT NAME',
            'DIVISION',
            'STATE'
        ]);
        $writer->addRow($headingRow);

        $query = Route::select('sap_code', 'name', 'division_id', 'state_id', 'created_at')
                    ->orderBy('created_at')
                    ->with(['division:abbreviation', 'state:abbreviation']);

        if(request()->import_id) {
            $query->where('import_id', request()->import_id);
        }

        $query->chunk(10000, function($routes) use ($writer) {
            foreach($routes as $route) {
                $values = [];
                $values[] = $route->sap_code;
                $values[] = $route->name;
                $values[] = $route->division ? $route->division->abbreviation : '';
                $values[] = $route->state ? $route->state->abbreviation : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportUnassignedToCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('beats-unassigned.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'BEAT CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $assignedRouteIds = RouteUser::pluck('route_id')->toArray();

        $assignedRouteIds = array_unique($assignedRouteIds);

        Route::whereNotIn('_id', $assignedRouteIds)
        ->select('sap_code', 'name')
        ->chunk(10000, function($routes) use ($writer) {
            foreach($routes as $route) {
                $values = [];
                $values[] = $route->sap_code;
                $values[] = $route->name;

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportMultipleDistributorsCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $db = DB::getMongoDB();

        $pipeline = [
            [
                '$project' => [
                    '_id' => [
                        '$toString' => '$_id'
                    ],
                    'count' => [
                        '$size' => [
                            '$ifNull' => [
                                '$distributor_ids',
                                []
                            ]
                        ]
                    ]
                ]
            ],
            [
                '$match' => [
                    'count' => [
                        '$gt' => 1
                    ]
                ]
            ]
        ];

        $results = $db->routes->aggregate($pipeline);

        $ids = [];

        foreach($results as $row) {
            $ids[] = $row->_id;
        }
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('beats-multiple-distributors.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'BEAT CODE',
            'BEAT NAME',
            'DB CODE',
            'DB NAME'
        ]);
        $writer->addRow($headingRow);

        Route::with('distributors')
        ->whereIn('_id', $ids)
        ->chunk(10000, function($routes) use ($writer) {
            foreach($routes as $route) {
                foreach($route->distributors as $distributor) {
                    $values = [];
                    $values[] = $route->sap_code;
                    $values[] = $route->name;
                    $values[] = $distributor->sap_code;
                    $values[] = $distributor->name;

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            }
        });

        $writer->close();
    }

    public function search() {
        $page = (int) request()->page;
        $term = request()->term;
        $state_id = request()->state_id;
        $limit = 10;

        $query = Route::select('_id', 'sap_code', 'name');

        if(request()->user()->role == 'sales-officer') {
            $query->where('division_id', request()->user()->division_id);

            // $query->whereHas('users', function($query1) {
            //     $query1->where('_id', request()->user()->_id);
            // });
        }

        if($state_id) {
            $query->where('state_id', $state_id);
        }

        if($term) {
            $query->where(function($query1) use ($term) {
                $query1->where('sap_code', 'LIKE', '%' . $term . '%')
                    ->orWhere('name', 'LIKE', '%' . $term . '%');
            });
        }

        $totalRecords = $query->count();
        $totalPages = ceil($totalRecords / $limit);

        if($page < 1 || $page > $totalPages) {
            $page = 1;
        }

        $start = ($page - 1) * $limit;

        // $results = $query->skip($start)->limit($limit)->get();

        $results = [];

        $models = $query->skip($start)->limit($limit)->get();
        foreach($models as $model) {
            $results[] = [
                'id' => $model->_id,
                'text' => $model->sap_code . ' ' . $model->name
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $page < $totalPages
            ]
        ]);
    }

    public function generateSapCode() {
        $routes = Route::get();

        $count = 10001;
        foreach($routes as $route) {
            $route->sap_code = '' . $count++;
            $route->save();
        }

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'SAP code generated successfully');
    }

    public function setCounter() {
        $counter = Counter::where('name', 'beat-code')->first();
        if(!$counter) {
            $counter = new Counter;
            $counter->name = 'beat-code';
            $counter->prefix = 'BN-';
        }

        $counter->count = 820;
        $counter->save();

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'Counter set successfully');
    }

    public function removeRoutes() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $empCodes = ['180053099', '180053100', '180053101', '180053895', '180053897', '180053899', '180053901', '180054016', '180054018', '180063687', '180066069', '180066242', '180066243', '180066446', '180066447', '180069278', '180069279', '180069280', '180069549', '180053960', '180054030', '180063719', '180066200', '180066813', '180069117', '180069438', '180054351', '180063692', '180054302', '180054310', '180054318', '180054320', '180066234', '180066805', '180069148'];

        foreach($empCodes as $empCode) {
            $user = User::where('emp_code', $empCode)->first();

            if($user) {
                $routeIds = $user->routeUsers()
                                ->whereNotNull('route_id')
                                ->pluck('route_id');
                
                $routes = Route::whereIn('_id', $routeIds)->with('customers')->get();
                foreach($routes as $route) {
                    $route->routeUsers()->delete();
                    foreach($route->customers as $customer) {
                        $customer->customerVisits()->delete();
                    }
                    $route->customers()->delete();
                    // $route->delete();
                }

                Route::whereIn('_id', $routeIds)->delete();
            }
        }

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'Routes removed successfully');
    }

    public function removeRoutesWithoutCustomers() {
        Route::whereNull('state_id')
        ->chunk(10000, function($routes){
            foreach($routes as $route) {
                if($route->customers()->count() == 0) {
                    $route->delete();
                }
            }
        });

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'Routes without customers removed successfully');
    }

    public function removeEmptyRouteUsers() {
        $routeIds = Route::pluck('_id');

        RouteUser::whereNotIn('route_id', $routeIds)
                ->delete();

        return redirect()
                ->action('RouteController@index')
                ->with('success', 'Empty Route Users removed successfully');
    }

    public function exportRouteUsersCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        // $beatCodes = [
        //     'BN-46864',
        //     'BN-47375',
        //     'BN-46865',
        //     'BN-47230',
        //     'BN-47065',
        //     'BN-47066',
        //     'BN-10585',
        //     'BN-48857',
        //     'BN-48945',
        //     'BN-48952',
        //     'BN-49177',
        //     'BN-49178',
        //     'BN-18631',
        //     'BN-18642',
        //     'BN-18644',
        //     'BN-18658',
        //     'BN-18662',
        //     'BN-18673',
        //     'BN-18694',
        //     'BN-18695',
        //     'BN-18708',
        //     'BN-18710',
        //     'BN-18713',
        //     'BN-18714',
        //     'BN-18935',
        //     'BN-23987',
        //     'BN-24960',
        //     'BN-24780',
        //     'BN-24845',
        //     'BN-24731',
        //     'BN-24199',
        //     'BN-24687',
        //     'BN-24732',
        //     'BN-24031',
        //     'BN-24665',
        //     'BN-24318',
        //     'BN-23986',
        //     'BN-24477',
        //     'BN-24576',
        //     'BN-47122',
        //     'BN-11308',
        //     'BN-11345',
        //     'BN-11111',
        //     'BN-11059',
        //     'BN-11346',
        // ];

        // $routes = Route::whereIn('sap_code', $beatCodes)
        //             ->with('routeUsers.user')
        //             ->get();

        // $routes = Route::whereNull('division_id')
        //             ->with('routeUsers.user')
        //             ->get();

        $db = DB::getMongoDB();

        $pipeline = [
            [
                '$project' => [
                    '_id' => [
                        '$toString' => '$_id'
                    ],
                    'count' => [
                        '$size' => [
                            '$ifNull' => [
                                '$distributor_ids',
                                []
                            ]
                        ]
                    ]
                ]
            ],
            [
                '$match' => [
                    'count' => [
                        '$gt' => 1
                    ]
                ]
            ]
        ];

        $results = $db->routes->aggregate($pipeline);

        $ids = [];

        foreach($results as $row) {
            $ids[] = $row->_id;
        }

        $routes = Route::whereIn('_id', $ids)
                    ->with(['routeUsers.user.distributor', 'routeUsers.user.salesOfficer'])
                    ->get();
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('beat-users.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'BEAT CODE',
            'BEAT NAME',
            'DSM CODE',
            'DSM NAME',
            'DSM DB CODE',
            'DSM DB NAME',
            'SO CODE',
            'SO NAME'
        ]);
        $writer->addRow($headingRow);

        foreach($routes as $route) {
            foreach($route->routeUsers as $routeUser) {
                if(($user = $routeUser->user) && $user->role == 'dsm') {
                    $values = [];
                    $values[] = $route->sap_code;
                    $values[] = $route->name;
                    $values[] = $user->emp_code;
                    $values[] = $user->name;
                    $values[] = $user->distributor ? $user->distributor->sap_code : '';
                    $values[] = $user->distributor ? $user->distributor->name : '';
                    $values[] = $user->salesOfficer ? $user->salesOfficer->emp_code : '';
                    $values[] = $user->salesOfficer ? $user->salesOfficer->name : '';

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            }
        }

        $writer->close();
    }
}
