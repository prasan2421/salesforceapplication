<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Distributor;

use App\Vertical;

use App\RouteUser;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

use DB;

use Form;

class DistributorController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin', [ 'except' => [ 'search' ] ]);
        $this->middleware('role:admin|sales-officer', [ 'only' => [ 'search' ] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('distributors.index');
    }

    public function getData() {
        $data = Distributor::select('id', 'sap_code', 'name', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('DistributorController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('DistributorController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['DistributorController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        $verticals = Vertical::pluck('name', '_id');

        return view('distributors.create', [
            'verticals' => $verticals
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
            'vertical_ids' => 'required|array|exists:verticals,_id',
            'sap_code' => 'required|unique:distributors',
            'name' => 'required',
            'email' => 'nullable|email',
        ]);

        $distributor = new Distributor;
        $distributor->sap_code = Common::nullIfEmpty($request->sap_code);
        $distributor->name = Common::nullIfEmpty($request->name);
        $distributor->email = Common::nullIfEmpty($request->email);
        $distributor->contact_number = Common::nullIfEmpty($request->contact_number);
        $distributor->city = Common::nullIfEmpty($request->city);
        $distributor->district = Common::nullIfEmpty($request->district);
        $distributor->state = Common::nullIfEmpty($request->state);
        $distributor->region = Common::nullIfEmpty($request->region);
        $distributor->save();

        $distributor->verticals()->attach($request->vertical_ids);

        return redirect()
                ->action('DistributorController@index')
                ->with('success', 'Distributor added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $distributor = Distributor::findOrFail($id);

        return view('distributors.show', [
            'distributor' => $distributor
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
        $distributor = Distributor::findOrFail($id);

        $verticals = Vertical::pluck('name', '_id');

        return view('distributors.edit', [
            'distributor' => $distributor,
            'verticals' => $verticals
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
        $distributor = Distributor::findOrFail($id);

        $request->validate([
            'vertical_ids' => 'required|array|exists:verticals,_id',
            'sap_code' => 'required|unique:distributors,sap_code,' . $distributor->id . ',_id',
            'name' => 'required',
            'email' => 'nullable|email',
        ]);

        $distributor->sap_code = Common::nullIfEmpty($request->sap_code);
        $distributor->name = Common::nullIfEmpty($request->name);
        $distributor->email = Common::nullIfEmpty($request->email);
        $distributor->contact_number = Common::nullIfEmpty($request->contact_number);
        $distributor->city = Common::nullIfEmpty($request->city);
        $distributor->district = Common::nullIfEmpty($request->district);
        $distributor->state = Common::nullIfEmpty($request->state);
        $distributor->region = Common::nullIfEmpty($request->region);
        $distributor->save();

        $distributor->verticals()->sync($request->vertical_ids);

        return redirect()
                ->action('DistributorController@index')
                ->with('success', 'Distributor updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $distributor = Distributor::findOrFail($id);

        $distributor->verticals()->detach();

        $distributor->delete();

        return redirect()
                ->action('DistributorController@index')
                ->with('success', 'Distributor deleted successfully');
    }

    public function exportCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('distributors.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'EMAIL',
            'CONTACT NUMBER',
            'CITY',
            'DISTRICT',
            'STATE',
            'REGION',
            'VERTICALS'
        ]);
        $writer->addRow($headingRow);

        Distributor::with(['verticals'])
        ->chunk(10000, function($distributors) use ($writer) {
            foreach($distributors as $distributor) {
                $values = [];
                $values[] = $distributor->sap_code;
                $values[] = $distributor->name;
                $values[] = $distributor->email;
                $values[] = $distributor->contact_number;
                $values[] = $distributor->city;
                $values[] = $distributor->district;
                $values[] = $distributor->state;
                $values[] = $distributor->region;
                $values[] = $distributor->verticals()->pluck('name')->implode(',');

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function exportDuplicatesCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $db = DB::getMongoDB();

        $pipeline = [
            [
                '$group' => [
                    '_id' => '$sap_code',
                    'count' => [
                        '$sum' => 1
                    ]
                ],
            ],
            [
                '$match' => [
                    'count' => [
                        '$gt' => 1
                    ]
                ]
            ]
        ];

        $results = $db->distributors->aggregate($pipeline);

        $sapCodes = [];

        foreach($results as $row) {
            $sapCodes[] = $row->_id;
        }
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('distributors-duplicate.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SAP CODE',
            'NAME',
            'EMAIL',
            'CONTACT NUMBER',
            'CITY',
            'DISTRICT',
            'STATE',
            'REGION',
            'VERTICALS'
        ]);
        $writer->addRow($headingRow);

        Distributor::with(['verticals', 'routes', 'users'])
        ->whereIn('sap_code', $sapCodes)
        ->orderBy('sap_code')
        ->chunk(10000, function($distributors) use ($writer) {
            foreach($distributors as $distributor) {
                $values = [];
                $values[] = $distributor->sap_code;
                $values[] = $distributor->name;
                $values[] = $distributor->email;
                $values[] = $distributor->contact_number;
                $values[] = $distributor->city;
                $values[] = $distributor->district;
                $values[] = $distributor->state;
                $values[] = $distributor->region;
                $values[] = $distributor->verticals()->pluck('name')->implode(',');

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

        $query = Distributor::select('_id', 'sap_code', 'name');

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

    public function mapRoutes() {
        $distributors = Distributor::get();

        foreach($distributors as $distributor) {
            $userIds = $distributor->users()->pluck('_id');
            $routeIds = RouteUser::whereIn('user_id', $userIds)->pluck('route_id')->toArray();

            if(count($routeIds) > 0) {
                $distributor->routes()->attach($routeIds);
            }
        }

        return redirect()
                ->action('DistributorController@index')
                ->with('success', 'Routes mapped successfully');
    }

    public function mapVerticals() {
        $vertical = Vertical::where('name', 'Biscuits & Confectionery')->first();

        $sapCodes = [ '117793', '111951', '117621', '101006', '101327', '116442', '109993', '112758', '110179', '117548', '101323', '111055', '108561', '108729', '104248', '109050', '108638', '105176', '104784', '100547', '108876', '118233', '118232', '108483', '114401', '109756', '108551', '117770', '110154', '109504', '101931', '114057', '106410', '111292', '106411', '112656', '101924', '112736', '110348', '108186', '111293', '111295', '110634', '111724', '117838', '117849', '108226', '111753', '111086', '117644', '116783', '105555' ];

        $distributors = Distributor::whereIn('sap_code', $sapCodes)->get();

        foreach($distributors as $distributor) {
            $distributor->verticals()->attach($vertical->id);
        }

        return redirect()
                ->action('DistributorController@index')
                ->with('success', 'Verticals mapped successfully');
    }
}
