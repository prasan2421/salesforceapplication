<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Validation\Rule;

use App\Attendance;

use App\Counter;

use App\CustomerVisit;

use App\Distributor;

use App\Division;

use App\Geolocation;

use App\RouteUser;

use App\State;

use App\User;

use App\Vertical;

use App\Exports\AttendancesExport;

use App\Exports\DsmsExport;

use App\Helpers\Common;

// use App\Helpers\DatabaseHelper;

use App\Helpers\DataTableHelper;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

use DateTime;

use DateTimezone;

use DB;

use Excel;

use Form;

use stdClass;

class DsmController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin', [ 'only' => [ 'destroy' ] ]);
        $this->middleware('role:admin|sales-officer', [ 'except' => [ 'destroy' ] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('dsms.index');
    }

    public function getData() {
        $data = User::where('role', 'dsm')
                    ->select('id', 'name', 'username', 'email', 'sales_officer_id', 'is_active', 'created_at', 'updated_at');

        if(request()->user()->role == 'sales-officer') {
            $data->where('division_id', request()->user()->division_id);

            if(request()->mine) {
                $data->where('sales_officer_id', request()->user()->_id);
            }
        }

        return DataTableHelper::of($data)
                ->addColumn('is_active', function($model) {
                    return '<i class="fas ' . ($model->is_active ? 'fa-check' : 'fa-times') . '"></i>';
                })
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('DsmController@show', Common::addMineParam($model->id)) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    if(in_array(request()->user()->role, ['admin', 'sales-officer'])) {
                        $content .= '<a href="' . action('DsmController@edit', Common::addMineParam($model->id)) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';
                    }

                    // if(in_array(request()->user()->role, ['admin', 'sales-officer'])) {
                    //     $content .= '<a href="' . action('DsmController@map', $model->id) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Map"><i class="fas fa-map-marked-alt"></i></a>';
                    // }

                    // if(in_array(request()->user()->role, ['admin', 'sales-officer'])) {
                    //     $content .= '<a href="' . action('DsmController@attendances', $model->id) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Attendances"><i class="fas fa-tasks"></i></a>';
                    // }

                    // if(in_array(request()->user()->role, ['admin', 'sales-officer'])) {
                    //     $content .= '<a href="' . action('DsmRouteController@index', $model->id) . '" class="btn btn-icon btn-primary" data-toggle="tooltip" data-placement="top" title="Beats"><i class="fas fa-route"></i></a>';
                    // }

                    if(request()->user()->role == 'admin'
                        || (request()->user()->role == 'sales-officer' && $model->sales_officer_id == request()->user()->id)) {
                        $content .= '<a href="' . action('DsmController@map', Common::addMineParam($model->id)) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Map"><i class="fas fa-map-marked-alt"></i></a>';
                    }

                    if(request()->user()->role == 'admin'
                        || (request()->user()->role == 'sales-officer' && $model->sales_officer_id == request()->user()->id)) {
                        $content .= '<a href="' . action('DsmController@attendances', Common::addMineParam($model->id)) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Attendances"><i class="fas fa-tasks"></i></a>';
                    }

                    if(request()->user()->role == 'admin'
                        || (request()->user()->role == 'sales-officer' && $model->sales_officer_id == request()->user()->id)) {
                        $content .= '<a href="' . action('DsmRouteController@index', Common::addMineParam($model->id)) . '" class="btn btn-icon btn-primary" data-toggle="tooltip" data-placement="top" title="Beats"><i class="fas fa-route"></i></a>';
                    }

                    if(request()->user()->role == 'admin') {
                        if($model->is_active) {
                            $content .= '<a href="' . action('DsmController@markInactive', $model->id) . '" class="btn btn-icon btn-danger" data-toggle="tooltip" data-placement="top" title="Mark as Inactive"><i class="fas fa-times"></i></a>';
                        }
                        else {
                            $content .= '<a href="' . action('DsmController@markActive', $model->id) . '" class="btn btn-icon btn-success" data-toggle="tooltip" data-placement="top" title="Mark as Active"><i class="fas fa-check"></i></a>';
                        }
                    }

                    if(request()->user()->role == 'admin') {
                        $content .= Form::open(['action' => ['DsmController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        // $salesOfficers = User::where('role', 'sales-officer')->pluck('name', '_id');
        // $distributors = Distributor::pluck('name', '_id');
        $divisions = [];
        $verticalsJson = json_encode(new stdClass);
        $verticals = [];
        $states = State::pluck('name', '_id');

        if(request()->user()->role == 'admin') {
            $divisions = Division::pluck('name', '_id');
            $models = Vertical::select('id', 'name', 'division_id')->get();
            $arr = [];

            foreach($models as $model) {
                $arr[$model->division_id][$model->id] = $model->name;
            }

            $verticalsJson = json_encode($arr);
        }
        else if(request()->user()->role == 'sales-officer') {
            if(request()->user()->division_id) {
                $verticals = Vertical::where('division_id', request()->user()->division_id)
                                    ->pluck('name', '_id');
            }
        }

        return view('dsms.create', [
            // 'salesOfficers' => $salesOfficers,
            // 'distributors' => $distributors,
            'divisions' => $divisions,
            'verticalsJson' => $verticalsJson,
            'verticals' => $verticals,
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
        $rules = [];

        if(request()->user()->role == 'admin') {
            $rules['sales_officer_id'] = [
                'nullable',
                Rule::exists('users', '_id')->where(function($query){
                    $query->where('role', 'sales-officer');
                })
            ];

            $rules['division_id'] = 'required|exists:divisions,_id';
        }

        if(!$request->is_non_parakram) {
            $rules['emp_code'] = 'required|digits:9|unique:users';
            $rules['username'] = 'required|unique:users';
        }

        $rules = array_merge($rules, [
            'distributor_id' => 'nullable|exists:distributors,_id',
            'vertical_ids' => 'required|array|exists:verticals,_id',
            'state_id' => 'required|exists:states,_id',
            'name' => 'required',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|confirmed'
        ]);

        $request->validate($rules);

        $user = new User;
        $user->role = 'dsm';

        if(request()->user()->role == 'admin') {
            $user->sales_officer_id = Common::nullIfEmpty($request->sales_officer_id);
            $user->division_id = Common::nullIfEmpty($request->division_id);
        }
        else if(request()->user()->role == 'sales-officer') {
            $user->sales_officer_id = request()->user()->id;
            $user->division_id = request()->user()->division_id;
        }
        else {
            $user->sales_officer_id = null;
            $user->division_id = null;
        }

        if($request->is_non_parakram) {
            $division = Division::find($user->division_id);
            $state = State::find($request->state_id);
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

            $user->emp_code = $counter->prefix . $counter->count;
            $user->username = $user->emp_code;
        }
        else {
            $user->emp_code = Common::nullIfEmpty($request->emp_code);
            $user->username = Common::nullIfEmpty($request->username);
        }

        $user->distributor_id = Common::nullIfEmpty($request->distributor_id);
        $user->state_id = Common::nullIfEmpty($request->state_id);
        $user->name = Common::nullIfEmpty($request->name);
        $user->email = Common::nullIfEmpty($request->email);
        $user->password = bcrypt($request->password);
        $user->is_active = true;
        $user->is_non_parakram = $request->is_non_parakram ? true : false;
        $user->save();

        $user->verticals()->attach($request->vertical_ids);

        return redirect()
                ->action('DsmController@index', Common::addMineParam())
                ->with('success', 'DSM added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = User::where('role', 'dsm');
        
        if(request()->user()->role == 'sales-officer') {
            $query->where('division_id', request()->user()->division_id);
        }

        $user = $query->findOrFail($id);

        return view('dsms.show', [
            'user' => $user
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
        $query = User::where('role', 'dsm');

        if(request()->user()->role == 'sales-officer') {
            $query->where('division_id', request()->user()->division_id);
        }

        $user = $query->findOrFail($id);

        // $salesOfficers = User::where('role', 'sales-officer')->pluck('name', '_id');
        // $distributors = Distributor::pluck('name', '_id');
        $divisions = [];
        $verticalsJson = json_encode(new stdClass);
        $verticals = [];
        $states = State::pluck('name', '_id');

        if(request()->user()->role == 'admin') {
            if($user->is_non_parakram) {
                $verticals = Vertical::where('division_id', $user->division_id)
                                    ->pluck('name', '_id');
            }
            else {
                $divisions = Division::pluck('name', '_id');
                $models = Vertical::select('id', 'name', 'division_id')->get();
                $arr = [];

                foreach($models as $model) {
                    $arr[$model->division_id][$model->id] = $model->name;
                }

                $verticalsJson = json_encode($arr);
            }
        }
        else if(request()->user()->role == 'sales-officer') {
            if(request()->user()->division_id) {
                $verticals = Vertical::where('division_id', request()->user()->division_id)
                                    ->pluck('name', '_id');
            }
        }

        return view('dsms.edit', [
            'user' => $user,
            // 'salesOfficers' => $salesOfficers,
            // 'distributors' => $distributors,
            'divisions' => $divisions,
            'verticalsJson' => $verticalsJson,
            'verticals' => $verticals,
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
        $query = User::where('role', 'dsm');

        if(request()->user()->role == 'sales-officer') {
            $query->where('division_id', request()->user()->division_id);
        }

        $user = $query->findOrFail($id);

        $rules = [];

        if(request()->user()->role == 'admin') {
            $rules['sales_officer_id'] = [
                'nullable',
                Rule::exists('users', '_id')->where(function($query){
                    $query->where('role', 'sales-officer');
                })
            ];

            if(!$user->is_non_parakram) {
                $rules['division_id'] = 'required|exists:divisions,_id';
            }
        }

        if(!$user->is_non_parakram) {
            $rules['state_id'] = 'required|exists:states,_id';
            $rules['emp_code'] = 'required|digits:9|unique:users,emp_code,' . $user->id . ',_id';
            $rules['username'] = 'required|unique:users,username,' . $user->id . ',_id';
        }

        $rules = array_merge($rules, [
            'distributor_id' => 'nullable|exists:distributors,_id',
            'vertical_ids' => 'required|array|exists:verticals,_id',
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email,' . $user->id . ',_id',
            'password' => 'confirmed'
        ]);

        $request->validate($rules);

        if(request()->user()->role == 'admin') {
            $user->sales_officer_id = Common::nullIfEmpty($request->sales_officer_id);
            if(!$user->is_non_parakram) {
                $user->division_id = Common::nullIfEmpty($request->division_id);
            }
        }
        else if(request()->user()->role == 'sales-officer') {
            if($request->set_sales_officer) {
                $user->sales_officer_id = request()->user()->id;
            }
        }

        if(!$user->is_non_parakram) {
            $user->state_id = Common::nullIfEmpty($request->state_id);
            $user->emp_code = Common::nullIfEmpty($request->emp_code);
            $user->username = Common::nullIfEmpty($request->username);
        }

        $user->distributor_id = Common::nullIfEmpty($request->distributor_id);
        $user->name = Common::nullIfEmpty($request->name);
        $user->email = Common::nullIfEmpty($request->email);
        if($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $user->verticals()->sync($request->vertical_ids);

        $routeIds = $user->routeUsers()->pluck('route_id')->toArray();

        if($user->distributor && count($routeIds) > 0) {
            $user->distributor->routes()->attach($routeIds);
        }

        return redirect()
                ->action('DsmController@index', Common::addMineParam())
                ->with('success', 'DSM updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('role', 'dsm')
                    ->findOrFail($id);

        $user->verticals()->sync([]);

        $user->delete();

        return redirect()
                ->action('DsmController@index')
                ->with('success', 'DSM deleted successfully');
    }

    /**
     * Display map of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function map($id)
    {
        $query = User::where('role', 'dsm');

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }
        
        $user = $query->findOrFail($id);

        $date = request()->date;

        if(!Common::isDateValid($date)) {
            $date = date('Y-m-d');
        }

        // $db = DatabaseHelper::getDatabase();

        $db = DB::getMongoDB();

        $pipeline = [
            [
                '$addFields' => [
                    'created_date' => [
                        '$dateToString' => [
                            'date' => '$created_at',
                            'format' => '%Y-%m-%d',
                            'timezone' => config('app.timezone')
                        ]
                    ]
                ],
            ],
            [
                '$match' => [
                    '$and' => [
                        [
                            'user_id' => $user->id
                        ],
                        [
                            'created_date' => $date
                        ]
                    ]
                ]
            ]
        ];

        $results = $db->geolocations->aggregate($pipeline);

        $geolocations = [];

        foreach($results as $row) {
            $geolocation = new stdClass;
            $geolocation->longitude = $row->longitude;
            $geolocation->latitude = $row->latitude;

            $geolocations[] = $geolocation;
        }

        return view('dsms.map', [
            'user' => $user,
            'date' => $date,
            'geolocations' => $geolocations
        ]);
    }*/

    public function map($id)
    {
        $query = User::where('role', 'dsm');

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }
        
        $user = $query->findOrFail($id);

        $date = request()->date;

        if(!Common::isDateValid($date)) {
            $date = date('Y-m-d');
        }

        $models = Geolocation::where('user_id', $user->id)
                        ->where('mobile_created_at', '>=', new DateTime($date . ' 00:00:00'))
                        ->where('mobile_created_at', '<=', new DateTime($date . ' 23:59:59'))
                        ->select('longitude', 'latitude', 'mobile_created_at')
                        ->get();

        $geolocations = [];
        foreach($models as $model) {
            $geolocation = new stdClass;
            $geolocation->longitude = $model->longitude;
            $geolocation->latitude = $model->latitude;

            $geolocations[] = $geolocation;
        }

        return view('dsms.map', [
            'user' => $user,
            'date' => $date,
            'geolocations' => $geolocations
        ]);
    }

    /**
     * Display attendances of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function attendances($id)
    {
        $query = User::where('role', 'dsm');

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }
        
        $user = $query->findOrFail($id);

        $date = request()->date;

        if(!Common::isDateValid($date)) {
            $date = date('Y-m-d');
        }

        // $db = DatabaseHelper::getDatabase();

        $db = DB::getMongoDB();

        $pipeline = [
            [
                '$addFields' => [
                    'punch_in_date' => [
                        '$dateToString' => [
                            'date' => '$punch_in_time',
                            'format' => '%Y-%m-%d',
                            'timezone' => config('app.timezone')
                        ]
                    ]
                ],
            ],
            [
                '$match' => [
                    '$and' => [
                        [
                            'user_id' => $user->id
                        ],
                        [
                            'punch_in_date' => $date
                        ]
                    ]
                ]
            ],
            [
                '$sort' => [
                    'punch_in_time' => 1
                ]
            ]
        ];

        $results = $db->attendances->aggregate($pipeline);

        $attendances = [];

        foreach($results as $row) {
            $attendance = new stdClass;
            $attendance->punch_in_time = property_exists($row, 'punch_in_time')
                                            ? $row->punch_in_time
                                                ->toDateTime()
                                                ->setTimezone(new DateTimezone(config('app.timezone')))
                                                ->format('g:i A')
                                            : '';

            $attendance->punch_out_time = property_exists($row, 'punch_out_time')
                                            ? $row->punch_out_time
                                                ->toDateTime()
                                                ->setTimezone(new DateTimezone(config('app.timezone')))
                                                ->format('g:i A')
                                            : '';

            $attendances[] = $attendance;
        }

        return view('dsms.attendances', [
            'user' => $user,
            'date' => $date,
            'attendances' => $attendances
        ]);
    }*/

    public function attendances($id)
    {
        $query = User::where('role', 'dsm');

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }
        
        $user = $query->findOrFail($id);

        $date = request()->date;

        if(!Common::isDateValid($date)) {
            $date = date('Y-m-d');
        }

        $models = Attendance::where('user_id', $user->id)
                        ->where('punch_in_time', '>=', new DateTime($date . ' 00:00:00'))
                        ->where('punch_in_time', '<=', new DateTime($date . ' 23:59:59'))
                        ->select('punch_in_time', 'punch_out_time')
                        ->get();

        $attendances = [];
        foreach($models as $model) {
            $attendance = new stdClass;
            $attendance->punch_in_time = $model->punch_in_time
                                            ? date('g:i A', strtotime($model->punch_in_time))
                                            : '';
            $attendance->punch_out_time = $model->punch_out_time
                                            ? date('g:i A', strtotime($model->punch_out_time))
                                            : '';

            $attendances[] = $attendance;
        }

        return view('dsms.attendances', [
            'user' => $user,
            'date' => $date,
            'attendances' => $attendances
        ]);
    }

    /**
     * Mark as active.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markActive($id)
    {
        $user = User::where('role', 'dsm')
                    ->findOrFail($id);

        $user->is_active = true;
        $user->save();

        return redirect()
                ->action('DsmController@index')
                ->with('success', 'DSM marked as active successfully');
    }

    /**
     * Mark as inactive.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markInactive($id)
    {
        $user = User::where('role', 'dsm')
                    ->findOrFail($id);

        $user->is_active = false;
        $user->save();

        return redirect()
                ->action('DsmController@index')
                ->with('success', 'DSM marked as inactive successfully');
    }

    public function generateDemoDsms() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '4096M');
        
        $verticalIds = Vertical::pluck('_id')->toArray();

        for($i = 1; $i <= 25; $i++) {
            $salesOfficer = new User;
            $salesOfficer->role = 'sales-officer';
            $salesOfficer->name = 'Sales Officer ' . $i;
            $salesOfficer->email = 'sales.officer.' . $i . '@patanjaliayurved.org';
            $salesOfficer->username = 'sales.officer.' . $i;
            $salesOfficer->password = bcrypt('123456');
            $salesOfficer->save();

            for($j = 1; $j <= 25; $j++) {
                $dsm = new User;
                $dsm->role = 'dsm';
                $dsm->sales_officer_id = $salesOfficer->id;
                $dsm->name = 'DSM ' . $i . ' ' . $j;
                $dsm->email = 'dsm.' . $i . '.' . $j . '@patanjaliayurved.org';
                $dsm->username = 'dsm.' . $i . '.' . $j;
                $dsm->password = bcrypt('123456');
                $dsm->save();

                $dsm->verticals()->attach($verticalIds);
            }
        }
    }

    public function exportExcel() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '4096M');
        
        return Excel::download(new DsmsExport(), 'dsms.xlsx');
    }

    public function exportCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('dsms.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'EMP CODE',
            'NAME',
            'GENDER',
            'DATE OF BIRTH',
            'EMAIL',
            'CONTACT NUMBER',
            'ADDRESS',
            'VERTICALS',
            'SO EMP CODE',
            'SO NAME',
            'DB CODE',
            'DB NAME'
        ]);
        $writer->addRow($headingRow);

        $query = User::where('role', 'dsm')
                    ->select('emp_code', 'name', 'gender', 'date_of_birth', 'email', 'contact_number', 'address', 'sales_officer_id', 'distributor_id')
                    ->with(['verticals', 'salesOfficer:emp_code,name', 'distributor:sap_code,name']);

        if(request()->import_id) {
            $query->where('import_id', request()->import_id);
        }

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $query->chunk(10000, function($users) use ($writer) {
            foreach($users as $user) {
                $values = [];
                $values[] = $user->emp_code;
                $values[] = $user->name;
                $values[] = $user->gender;
                $values[] = $user->date_of_birth;
                $values[] = $user->email;
                $values[] = $user->contact_number;
                $values[] = $user->address;
                $values[] = $user->verticals()->pluck('name')->implode(',');
                $values[] = $user->salesOfficer ? $user->salesOfficer->emp_code : '';
                $values[] = $user->salesOfficer ? $user->salesOfficer->name : '';
                $values[] = $user->distributor ? $user->distributor->sap_code : '';
                $values[] = $user->distributor ? $user->distributor->name : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportWithBeatsCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('dsms-with-beats.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'EMP CODE',
            'NAME',
            'GENDER',
            'DATE OF BIRTH',
            'EMAIL',
            'CONTACT NUMBER',
            'ADDRESS',
            'VERTICALS',
            'SO EMP CODE',
            'SO NAME',
            'DB CODE',
            'DB NAME',
            'BEAT CODE',
            'BEAT NAME',
            'BEAT FREQUENCY',
            'DAY OF VISIT'
        ]);
        $writer->addRow($headingRow);

        $query = User::where('role', 'dsm')
                    ->select('emp_code', 'name', 'gender', 'date_of_birth', 'email', 'contact_number', 'address', 'sales_officer_id', 'distributor_id')
                    ->with(['verticals', 'salesOfficer:emp_code,name', 'distributor:sap_code,name', 'routeUsers.route']);

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $query->chunk(10000, function($users) use ($writer) {
            foreach($users as $user) {
                $emp_code = $user->emp_code;
                $name = $user->name;
                $gender = $user->gender;
                $date_of_birth = $user->date_of_birth;
                $email = $user->email;
                $contact_number = $user->contact_number;
                $address = $user->address;
                $so_code = $user->salesOfficer ? $user->salesOfficer->emp_code : '';
                $so_name = $user->salesOfficer ? $user->salesOfficer->name : '';
                $db_code = $user->distributor ? $user->distributor->sap_code : '';
                $db_name = $user->distributor ? $user->distributor->name : '';
                $verticals = $user->verticals()->pluck('name')->implode(',');

                $routeUsers = $user->routeUsers;

                if(count($routeUsers) > 0) {
                    foreach($routeUsers as $routeUser) {
                        $values = [];
                        $values[] = $emp_code;
                        $values[] = $name;
                        $values[] = $gender;
                        $values[] = $date_of_birth;
                        $values[] = $email;
                        $values[] = $contact_number;
                        $values[] = $address;
                        $values[] = $verticals;
                        $values[] = $so_code;
                        $values[] = $so_name;
                        $values[] = $db_code;
                        $values[] = $db_name;
                        $values[] = $routeUser->route ? $routeUser->route->sap_code : '';
                        $values[] = $routeUser->route ? $routeUser->route->name : '';
                        $values[] = $routeUser->frequency;
                        $values[] = $routeUser->day;

                        $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                        $writer->addRow($rowFromValues);
                    }
                }
                else {
                    $values = [];
                    $values[] = $emp_code;
                    $values[] = $name;
                    $values[] = $gender;
                    $values[] = $date_of_birth;
                    $values[] = $email;
                    $values[] = $contact_number;
                    $values[] = $address;
                    $values[] = $verticals;
                    $values[] = $so_code;
                    $values[] = $so_name;
                    $values[] = $db_code;
                    $values[] = $db_name;
                    $values[] = '';
                    $values[] = '';
                    $values[] = '';
                    $values[] = '';

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            }
        });

        $writer->close();
    }

    public function exportAttendancesExcel() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '4096M');
        
        return Excel::download(new AttendancesExport(), 'attendances.xlsx');
    }

    public function exportAttendancesCsv() {
        return view('dsms.export-attendances-csv');
    }

    public function submitExportAttendancesCsv(Request $request) {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $db = DB::getMongoDB();

        $collection = $db->selectCollection('attendances');

        $conditions = [
            [
                '$gte' => [
                    '$punch_in_time',
                    [
                        '$dateFromString' => [
                            'dateString' => $request->start_date . ' 00:00:00',
                            'format' => '%Y-%m-%d %H:%M:%S',
                            'timezone' => config('app.timezone')
                        ]
                    ]
                ],
            ],
            [
                '$lte' => [
                    '$punch_in_time',
                    [
                        '$dateFromString' => [
                            'dateString' => $request->end_date . ' 23:59:59',
                            'format' => '%Y-%m-%d %H:%M:%S',
                            'timezone' => config('app.timezone')
                        ]
                    ]
                ]
            ]
        ];

        if(request()->user()->role == 'sales-officer') {
            $conditions[] = [
                '$in' => [
                    '$user_id',
                    request()->user()->dsms()->pluck('_id')->toArray()
                ]
            ];
        }

        $pipeline = [
            [
                '$match' => [
                    '$expr' => [
                        '$and' => $conditions
                    ]
                ]
            ],
            [
                '$group' => [
                    '_id' => [
                        'date' => [
                            '$dateToString' => [
                                'date' => '$punch_in_time',
                                'format' => '%Y-%m-%d',
                                'timezone' => config('app.timezone')
                            ]
                        ],
                        'user_id' => '$user_id'
                    ],
                    'punch_in_time' => [
                        '$min' => '$punch_in_time'
                    ],
                    'punch_out_time' => [
                        '$max' => '$punch_out_time'
                    ]
                ]
            ],
            [
                '$addFields' => [
                    'user_id' => [
                        '$toObjectId' => '$_id.user_id'
                    ]
                ]
            ],
            [
                '$lookup' => [
                    'from' => 'users',
                    'localField' => 'user_id',
                    'foreignField' => '_id',
                    'as' => 'user'
                ]
            ],
            [
                '$unwind' => [
                    'path' => '$user',
                    'preserveNullAndEmptyArrays' => true
                ]
            ],
            [
                '$project' => [
                    'date' => '$_id.date',
                    'punch_in_time' => '$punch_in_time',
                    'punch_out_time' => '$punch_out_time',
                    'emp_code' => '$user.emp_code',
                    'name' => '$user.name'
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
        $writer->openToBrowser('attendances.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'DATE',
            'DSM CODE',
            'DSM NAME',
            'PUNCH IN',
            'PUNCH OUT'
        ]);
        $writer->addRow($headingRow);

        for($page = 1; $page <= $totalPages; $page++) {
            $start = ($page - 1) * $limit;

            $results = $collection->aggregate(
                array_merge(
                    $pipeline,
                    [
                        [
                            '$sort' => [
                                'date' => 1,
                                '_id' => 1
                            ]
                        ],
                        [ '$skip' => $start ],
                        [ '$limit' => $limit ]
                    ]
                )
            );

            foreach($results as $row) {
                $values = [];
                $values[] = property_exists($row, 'date') ? $row->date : '';
                $values[] = property_exists($row, 'emp_code') ? $row->emp_code : '';
                $values[] = property_exists($row, 'name') ? $row->name : '';
                $values[] = property_exists($row, 'punch_in_time') && $row->punch_in_time
                                ? $row->punch_in_time
                                    ->toDateTime()
                                    ->setTimezone(new DateTimezone(config('app.timezone')))
                                    ->format('g:i A')
                                : '';

                $values[] = property_exists($row, 'punch_out_time') && $row->punch_out_time
                                ? $row->punch_out_time
                                    ->toDateTime()
                                    ->setTimezone(new DateTimezone(config('app.timezone')))
                                    ->format('g:i A')
                                : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        }

        $writer->close();
    }

    public function exportCustomerVisitsCsv() {
        return view('dsms.export-customer-visits-csv');
    }

    public function submitExportCustomerVisitsCsv(Request $request) {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('customer-visits.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'DATE',
            'DSM CODE',
            'DSM NAME',
            'RETAILER CODE',
            'RETAILER NAME',
            'CHECK IN TIME',
            'CHECK IN LATITUDE',
            'CHECK IN LONGITUDE',
            'CHECK OUT TIME',
            'CHECK OUT LATITUDE',
            'CHECK OUT LONGITUDE'
        ]);
        $writer->addRow($headingRow);

        $query = CustomerVisit::where('check_in_time', '>=', new DateTime($request->start_date . ' 00:00:00'))
                    ->where('check_in_time', '<=', new DateTime($request->end_date . ' 23:59:59'))
                    ->with(['user', 'customer']);

        if(request()->user()->role == 'sales-officer') {
            $userIds = request()->user()->dsms()->pluck('_id')->toArray();
            $userIds[] = request()->user()->id;

            $query->whereIn('user_id', $userIds);
        }

        $query->chunk(10000, function($customerVisits) use ($writer) {
            foreach($customerVisits as $customerVisit) {
                $values = [];
                $values[] = date('Y-m-d', strtotime($customerVisit->check_in_time));
                $values[] = $customerVisit->user ? $customerVisit->user->emp_code : '';
                $values[] = $customerVisit->user ? $customerVisit->user->name : '';
                $values[] = $customerVisit->customer ? $customerVisit->customer->sap_code : '';
                $values[] = $customerVisit->customer ? $customerVisit->customer->name : '';
                $values[] = $customerVisit->check_in_time;
                $values[] = $customerVisit->check_in_latitude;
                $values[] = $customerVisit->check_in_longitude;
                $values[] = $customerVisit->check_out_time;
                $values[] = $customerVisit->check_out_latitude;
                $values[] = $customerVisit->check_out_longitude;

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportDsmStatesCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('dsm-states.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'EMP CODE',
            'NAME',
            'STATE',
            'STATE CODE'
        ]);
        $writer->addRow($headingRow);

        User::where('role', 'dsm')
        ->select('emp_code', 'name')
        ->with('routeUsers.route.state')
        ->chunk(10000, function($users) use ($writer) {
            foreach($users as $user) {
                foreach($user->routeUsers as $routeUser) {
                    if($route = $routeUser->route) {
                        $values = [];
                        $values[] = $user->emp_code;
                        $values[] = $user->name;
                        $values[] = $route->state->name;
                        $values[] = $route->state->abbreviation;

                        $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                        $writer->addRow($rowFromValues);
                    }
                }
            }
        });

        $writer->close();
    }

    public function assignFoodVerticals() {
        $verticalIds = [];

        $division = Division::where('name', 'Food')->first();
        if($division) {
            $verticalIds = $division->verticals()->pluck('_id')->toArray();
        }

        $dsms = User::where('role', 'dsm')->get();

        foreach($dsms as $dsm) {
            $dsm->verticals()->attach($verticalIds);
        }

        return redirect()
                ->action('DsmController@index')
                ->with('success', 'Food verticals assigned successfully');
    }

    public function updateUsername() {
        $users = User::where('role', 'dsm')
                    ->whereNotNull('emp_code')
                    ->get();

        foreach($users as $user) {
            $user->username = '' . $user->emp_code;
            $user->save();
        }

        return redirect()
                ->action('DsmController@index')
                ->with('success', 'Username updated successfully');
    }

    public function removeEmptyRouteUsers() {
        $userIds = User::pluck('_id');

        // $count = RouteUser::whereNotIn('user_id', $userIds)->count();

        // echo $count; die;

        RouteUser::whereNotIn('user_id', $userIds)
                ->delete();

        return redirect()
                ->action('DsmController@index')
                ->with('success', 'Empty Route Users removed successfully');
    }
}
