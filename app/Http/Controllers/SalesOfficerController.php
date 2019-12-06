<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Division;

use App\Route;

use App\RouteUser;

use App\State;

use App\User;

use App\Vertical;

use Form;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class SalesOfficerController extends Controller
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
        return view('sales-officers.index');
    }

    public function getData() {
        $data = User::where('role', 'sales-officer')
                    ->select('id', 'name', 'username', 'email', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('SalesOfficerController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('SalesOfficerController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= '<a href="' . action('SalesOfficerRouteController@index', $model->id) . '" class="btn btn-icon btn-primary" data-toggle="tooltip" data-placement="top" title="Beats"><i class="fas fa-route"></i></a>';

                    $content .= Form::open(['action' => ['SalesOfficerController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        // $routes = Route::pluck('name', '_id');
        $divisions = Division::pluck('name', '_id');
        $models = Vertical::select('id', 'name', 'division_id')->get();
        $verticals = [];
        $states = State::pluck('name', '_id');

        foreach($models as $model) {
            $verticals[$model->division_id][$model->id] = $model->name;
        }

        return view('sales-officers.create', [
            // 'routes' => $routes,
            'divisions' => $divisions,
            'verticalsJson' => json_encode($verticals),
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
            // 'route_ids' => 'required|array|exists:routes,_id',
            'division_id' => 'required|exists:divisions,_id',
            'vertical_ids' => 'required|array|exists:verticals,_id',
            'state_id' => 'required|exists:states,_id',
            'emp_code' => 'required|digits:8|unique:users',
            'name' => 'required',
            'email' => 'nullable|email|unique:users',
            'username' => 'required|unique:users',
            'password' => 'required|confirmed'
        ]);

        $user = new User;
        $user->role = 'sales-officer';
        $user->division_id = Common::nullIfEmpty($request->division_id);
        $user->state_id = Common::nullIfEmpty($request->state_id);
        $user->emp_code = Common::nullIfEmpty($request->emp_code);
        $user->name = Common::nullIfEmpty($request->name);
        $user->email = Common::nullIfEmpty($request->email);
        $user->username = Common::nullIfEmpty($request->username);
        $user->password = bcrypt($request->password);
        $user->is_active = true;
        $user->save();

        // $user->routes()->attach($request->route_ids);

        $user->verticals()->attach($request->vertical_ids);

        return redirect()
                ->action('SalesOfficerController@index')
                ->with('success', 'Sales officer added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('role', 'sales-officer')
                    ->findOrFail($id);

        return view('sales-officers.show', [
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
        $user = User::where('role', 'sales-officer')
                    ->findOrFail($id);

        // $routes = Route::pluck('name', '_id');
        $divisions = Division::pluck('name', '_id');
        $models = Vertical::select('id', 'name', 'division_id')->get();
        $verticals = [];
        $states = State::pluck('name', '_id');

        foreach($models as $model) {
            $verticals[$model->division_id][$model->id] = $model->name;
        }

        return view('sales-officers.edit', [
            'user' => $user,
            // 'routes' => $routes,
            'divisions' => $divisions,
            'verticalsJson' => json_encode($verticals),
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
        $user = User::where('role', 'sales-officer')
                    ->findOrFail($id);

        $request->validate([
            // 'route_ids' => 'required|array|exists:routes,_id',
            'division_id' => 'required|exists:divisions,_id',
            'vertical_ids' => 'required|array|exists:verticals,_id',
            'state_id' => 'required|exists:states,_id',
            'emp_code' => 'required|digits:8|unique:users,emp_code,' . $user->id . ',_id',
            'name' => 'required',
            'email' => 'nullable|email|unique:users,email,' . $user->id . ',_id',
            'username' => 'required|unique:users,username,' . $user->id . ',_id',
            'password' => 'confirmed'
        ]);

        $user->division_id = Common::nullIfEmpty($request->division_id);
        $user->state_id = Common::nullIfEmpty($request->state_id);
        $user->emp_code = Common::nullIfEmpty($request->emp_code);
        $user->name = Common::nullIfEmpty($request->name);
        $user->email = Common::nullIfEmpty($request->email);
        $user->username = Common::nullIfEmpty($request->username);
        if($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        // $user->routes()->sync($request->route_ids);

        $user->verticals()->sync($request->vertical_ids);

        return redirect()
                ->action('SalesOfficerController@index')
                ->with('success', 'Sales officer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::where('role', 'sales-officer')
                    ->findOrFail($id);

        $user->verticals()->sync([]);

        $user->delete();

        return redirect()
                ->action('SalesOfficerController@index')
                ->with('success', 'Sales officer deleted successfully');
    }

    public function exportCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('sales-officers.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'EMP CODE',
            'NAME',
            'EMAIL',
            'VERTICALS'
        ]);
        $writer->addRow($headingRow);

        User::where('role', 'sales-officer')
        ->select('emp_code', 'name', 'email')
        ->with('verticals')
        ->chunk(10000, function($users) use ($writer) {
            foreach($users as $user) {
                $values = [];
                $values[] = $user->emp_code;
                $values[] = $user->name;
                $values[] = $user->email;
                $values[] = $user->verticals()->pluck('name')->implode(',');

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function search() {
        $page = (int) request()->page;
        $term = request()->term;
        $limit = 10;

        $query = User::where('role', 'sales-officer')
                    ->select('_id', 'emp_code', 'name');

        if($term) {
            $query->where(function($query1) use ($term) {
                $query1->where('emp_code', 'LIKE', '%' . $term . '%')
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
                'text' => $model->emp_code . ' ' . $model->name
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $page < $totalPages
            ]
        ]);
    }

    public function getRoutesData() {
        $data = Route::select('id', 'sap_code', 'name', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->make();
    }

    public function routes($id) {
        $user = User::where('role', 'sales-officer')
                    ->findOrFail($id);

        return view('sales-officers.routes', [
            'user' => $user
        ]);
    }

    public function saveRoutes(Request $request, $id) {
        $user = User::where('role', 'sales-officer')
                    ->findOrFail($id);

        $request->validate([
            'route_ids' => 'required|array|exists:routes,_id'
        ]);

        $user->routes()->sync($request->route_ids);

        return redirect()
                ->action('SalesOfficerController@routes', $id)
                ->with('success', 'Sales officer beats saved successfully');
    }

    public function updateUsername() {
        $users = User::where('role', 'sales-officer')
                    ->whereNotNull('emp_code')
                    ->get();

        foreach($users as $user) {
            $user->username = '' . $user->emp_code;
            $user->save();
        }

        return redirect()
                ->action('SalesOfficerController@index')
                ->with('success', 'Username updated successfully');
    }

    public function assignRoutes() {
        $users = User::where('role', 'sales-officer')->get();

        foreach($users as $user) {
            $dsmIds = $user->dsms()->pluck('_id');

            $routeIds = RouteUser::whereIn('user_id', $dsmIds)
                                ->whereNotNull('route_id')
                                ->pluck('route_id')
                                ->toArray();

            $routeIds = array_unique($routeIds);

            if(count($routeIds) > 0) {
                $user->routes()->attach($routeIds);
            }
        }

        return redirect()
                ->action('SalesOfficerController@index')
                ->with('success', 'Beats assigned successfully');
    }

    public function exportWithoutVerticalsCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('sales-officers-without-verticals.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'EMP CODE',
            'NAME',
            'EMAIL'
        ]);
        $writer->addRow($headingRow);

        User::where('role', 'sales-officer')
        ->doesntHave('verticals')
        ->chunk(10000, function($users) use ($writer) {
            foreach($users as $user) {
                $values = [];
                $values[] = $user->emp_code;
                $values[] = $user->name;
                $values[] = $user->email;

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportWithoutDivisionCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('sales-officers-without-division.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'EMP CODE',
            'NAME',
            'EMAIL'
        ]);
        $writer->addRow($headingRow);

        User::where('role', 'sales-officer')
        ->whereNull('division_id')
        ->chunk(10000, function($users) use ($writer) {
            foreach($users as $user) {
                $values = [];
                $values[] = $user->emp_code;
                $values[] = $user->name;
                $values[] = $user->email;

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }
}
