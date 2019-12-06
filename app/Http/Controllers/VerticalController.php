<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Validation\Rule;

use App\Customer;

use App\CustomerVisit;

use App\Division;

use App\Distributor;

use App\Vertical;

use App\Route;

use App\User;

use App\Helpers\Common;

use App\Helpers\DataTableHelper;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

use Form;

class VerticalController extends Controller
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
        return view('verticals.index');
    }

    public function getData() {
        $data = Vertical::select('id', 'name', 'created_at', 'updated_at');

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('VerticalController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '<a href="' . action('VerticalController@edit', $model->id) . '" class="btn btn-icon btn-warning" data-toggle="tooltip" data-placement="top" title="Edit"><i class="fas fa-edit"></i></a>';

                    $content .= Form::open(['action' => ['VerticalController@destroy', $model->id], 'method' => 'DELETE', 'style' => 'display: inline;']);
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
        $divisions = Division::pluck('name', '_id');

        return view('verticals.create', [
            'divisions' => $divisions
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
            'division_id' => 'required|exists:divisions,_id',
            'name' => 'required'
        ]);

        $vertical = new Vertical;
        $vertical->division_id = Common::nullIfEmpty($request->division_id);
        $vertical->name = Common::nullIfEmpty($request->name);
        $vertical->save();

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Vertical added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $vertical = Vertical::findOrFail($id);

        return view('verticals.show', [
            'vertical' => $vertical
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
        $vertical = Vertical::findOrFail($id);

        $divisions = Division::pluck('name', '_id');

        return view('verticals.edit', [
            'vertical' => $vertical,
            'divisions' => $divisions
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
        $vertical = Vertical::findOrFail($id);

        $request->validate([
            'division_id' => 'required|exists:divisions,_id',
            'name' => 'required'
        ]);

        $vertical->division_id = Common::nullIfEmpty($request->division_id);
        $vertical->name = Common::nullIfEmpty($request->name);
        $vertical->save();

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Vertical updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vertical = Vertical::findOrFail($id);

        $vertical->delete();

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Vertical deleted successfully');
    }

    public function removeRelationships() {
        $names = [
            'FLOUR and SUGAR',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'FROZEN PRODUCTS',
            'DAIRY DIVISION',
            'DIVYA JAL',
            'PHYSICAL OIL and GHEE',
            'RICE/PULSES/SPICES',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                $vertical->distributors()->sync([]);
                $vertical->users()->sync([]);
            }
        }

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Relationships removed successfully');
    }

    // Step 1
    public function removeDistributors() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                $vertical->distributors()->delete();
            }
        }

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Distributors removed successfully');
    }

    public function removeDistributors2() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $ids = [
            '5d344f7b8788724e04373328',
            '5d35a9d38788728a2e1e1acd',
            '5d52986d8788728f2d5f3fd4',
            '5d344f7a8788724e0437326e',
            '5d344f7a8788724e0437326c',
            '5d344f7a8788724e04373268',
            '5d344f7a8788724e04373266',
            '5d344f7a8788724e04373267',
            '5d344f7a8788724e0437326b',
            '5d344f7a8788724e04373276',
            '5d344f7a8788724e04373277',
            '5d344f7a8788724e04373278',
            '5d344f7b8788724e043733ec',
            '5d344f7b8788724e043733ed',
            '5d344f7b8788724e043733ee',
            '5d344f7b8788724e043733ef',
            '5d344f7b8788724e04373312',
            '5d344f7b8788724e04373315',
            '5d344f7b8788724e0437331a',
            '5d344f7b8788724e04373316',
            '5d344f7b8788724e0437331f',
            '5d344f7b8788724e0437331e',
            '5d344f7b8788724e04373345',
            '5d344f7b8788724e04373341',
            '5d344f7a8788724e043732a9',
            '5d344f7a8788724e043732aa',
            '5d344f7a8788724e043732ac',
            '5d344f7a8788724e043732ad',
            '5d344f7a8788724e043732ae',
            '5d344f7a8788724e043732b0',
            '5d344f7a8788724e043732e6',
            '5d344f7a8788724e043732e5',
            '5d344f7a8788724e043732e7',
            '5d344f7b8788724e043732e9',
            '5d344f7b8788724e043732ea',
            '5d344f7a8788724e043732ca',
            '5d344f7a8788724e043732cb',
            '5d344f7b8788724e04373386',
            '5d344f7b8788724e0437338d',
            '5d344f7b8788724e0437338e',
            '5d344f7b8788724e043733b0',
            '5d344f7b8788724e043733ab',
            '5d344f7b8788724e043733c2',
            '5d344f7b8788724e043733c3',
            '5d344f7b8788724e043733d9',
            '5d344f7b8788724e043733d8',
            '5d344f7b8788724e043733cf',
            '5d344f7b8788724e043733e0',
            '5d440c93878872983c492acf',
            '5d07a8272886572d567e4e06',
            '5d36ecfe878872204b18feef',
            '5d5d120d8788728a2f36cff5',
        ];

        Distributor::whereIn('_id', $ids)->delete();

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Distributors removed successfully');
    }

    public function exportRoutesToRemoveCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('routes-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'ID',
            'BEAT CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                foreach($vertical->users as $user) {
                    foreach($user->routeUsers as $routeUser) {
                        if($routeUser->route) {
                            $values = [];
                            $values[] = $routeUser->route->_id;
                            $values[] = $routeUser->route->sap_code;
                            $values[] = $routeUser->route->name;

                            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                            $writer->addRow($rowFromValues);
                        }
                    }
                }
            }
        }

        $writer->close();
    }

    public function exportRoutesToRemoveCsv2() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('routes-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'ID',
            'BEAT CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $empCodes = ['1800', '18005230', '180050248', '180050352', '180050411', '180050417', '180050424', '180050428', '180050429', '180050448', '180050547', '180050893', '180050911', '180050964', '180050980', '180051256', '180051289', '180051418', '180051422', '180051495', '180051846', '180052571', '180052659', '180052660', '180052661', '180052662', '180052663', '180052667', '180052673', '180052675', '180052706', '180052716', '180052719', '180052743', '180052987', '180053064', '180053102', '180053106', '180053156', '180053158', '180053166', '180053461', '180053527', '180053689', '180053726', '180053823', '180053824', '180053826', '180053829', '180053830', '180053834', '180053840', '180053841', '180053842', '180053863', '180053916', '180053938', '180053993', '180054094', '180054168', '180054202', '180054206', '180054228', '180054243', '180054331', '180054356', '180054372', '180054793', '180054805', '180054807', '180055041', '180055049', '180055084', '180055092', '180059498', '180061148', '180061181', '180061268', '180061296', '180061355', '180061377', '180061387', '180061388', '180061411', '180061440', '180061514', '180061927', '180061961', '180061966', '180061981', '180061990', '180061994', '180062048', '180062063', '180062071', '180062076', '180062114', '180062120', '180062122', '180062125', '180062133', '180062223', '180062262', '180062265', '180062333', '180062344', '180062407', '180062421', '180062463', '180062577', '180062752', '180062787', '180062822', '180063065', '180063079', '180063161', '180063213', '180063341', '180063350', '180063351', '180063380', '180063384', '180063388', '180063401', '180063405', '180063515', '180063543', '180063586', '180063624', '180063627', '180063695', '180063707', '180063779', '180063792', '180063800', '180063971', '180063973', '180063982', '180063983', '180064067', '180064123', '180064131', '180064166', '180064316', '180064328', '180064329', '180064357', '180064496', '180064497', '180064536', '180064538', '180064608', '180064652', '180064768', '180064770', '180064782', '180064799', '180064813', '180064836', '180064842', '180064919', '180064941', '180066047', '180066128', '180066231', '180066246', '180066249', '180066254', '180066463', '180066482', '180066483', '180066540', '180066594', '180066598', '180066717', '180066820', '180066821', '180066860', '180066862', '180066911', '180066912', '180069111', '180069114', '180069117', '180069208', '180069266', '180069279', '180069293', '180069315', '180069336', '180069337', '180069354', '180069363', '180069396', '180069411', '180069412', '180069422', '180069434', '180069438', '180069498', '180069588', '180069591', '180069601', '180069607', '180069975', '180070017', '180070039', '180070049', '180070114', '180070115', '180070125', '180070132', '180070188', '180070227', '180070273', '180070296', '180070341', '180070375', '180070393', '180070403', '180070405', '180070412', '180070432', '180070444', '180070448', '180070474', '180070532', '180070555', '180070577', '180070590', '180070598', '180070600', '180070601', '180070619', '180070630', '180070665', '180070699', '180070711', '180070719', '180070773', '180070777', '180070778', '180070779', '180070780', '180070781', '945620101', '987321489', '6111764574', '1', '2'];

        $users = User::whereIn('emp_code', $empCodes)
                    ->with('routeUsers.route')
                    ->get();

        foreach($users as $user) {
            foreach($user->routeUsers as $routeUser) {
                if($route = $routeUser->route) {
                    $values = [];
                    $values[] = $route->_id;
                    $values[] = $route->sap_code;
                    $values[] = $route->name;

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            }
        }

        $writer->close();
    }

    public function exportRoutesToRemoveCsv3() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('routes-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'ID',
            'BEAT CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $empCodes = [
            '180050721',
            '180050722',
            '180050723',
            '180050730',
            '180050731',
            '180051878',
            '180051879',
            '180051880',
            '180051881',
            '180051887',
            '180051893',
            '180051894',
            '180051946',
            '180051947',
            '180051950',
            '180051951',
            '180057736',
            '180059285',
            '180059286',
            '180059288',
            '180059289',
            '180059315',
            '180059316',
            '180059317',
            '180059322',
            '180060332',
            '180060333',
            '180060334',
            '180060335',
            '180060336',
            '180060342',
            '180060343',
            '180060344',
            '180060432',
            '180061114',
            '180061115',
            '180061117',
            '180061118',
            '180068450',
            '180072145',
        ];

        $users = User::whereIn('emp_code', $empCodes)
                    ->with('routeUsers.route.routeUsers.user')
                    ->get();

        foreach($users as $user) {
            foreach($user->routeUsers as $routeUser) {
                if($route = $routeUser->route) {
                    $toRemove = true;
                    foreach($route->routeUsers as $routeUser1) {
                        if($routeUser1->user && !in_array($routeUser1->user->emp_code, $empCodes)) {
                            $toRemove = false;
                            break;
                        }
                    }

                    if($toRemove) {
                        $values = [];
                        $values[] = $route->_id;
                        $values[] = $route->sap_code;
                        $values[] = $route->name;

                        $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                        $writer->addRow($rowFromValues);
                    }
                }
            }
        }

        $writer->close();
    }

    public function exportRoutesToRemoveCsv4() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('routes-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'ID',
            'BEAT CODE',
            'BEAT NAME'
        ]);
        $writer->addRow($headingRow);

        $soCodes = [
            '60008751',
            '60007978',
            '60008137',
            '60003624',
            '60009059',
            '60009375',
            '60007408',
            '60011673',
            '60003200',
            '60007218',
            '60009796',
            '60011627',
            '60005853',
            '60003260',
            '60003008',
            '60002513',
            '60007279',
            '60007116',
            '60008078',
            '60007219',
            '60006342',
            '60007220',
            '60005208',
            '60006425',
            '60009423',
            '60006371',
            '60007947',
            '60003007',
            '60007414',
            '60010468',
            '60008279',
            '60005494',
            '60011950',
            '60006885',
            '60004862',
            '60011985',
            '60010406',
            '60007418',
            '60012412',
            '60011590',
            '60004663',
            '60011435',
            '60010003',
            '60007624',
            '60011803',
            '60005058',
            '60007512',
            '60006747',
            '60010831',
            '60007016',
            '60007591',
            '60005610',
            '60007251',
            '60010492',
            '60007657',
            '60012597',
            '60004855',
            '60007829',
            '60003598',
            '60004239',
            '60009611',
            '60004076',
            '60006956',
            '60007078',
            '60007905',
            '60008432',
            '60002767',
            '60004055',
            '60009763',
            '60009801',
            '60003613',
            '60011529',
            '60001115',
            '60011968',
            '60009249',
            '60008768',
            '60005471',
            '60008217',
            '60006886',
            '60005311',
            '60003190',
            '60003527',
            '60011104',
            '60012469',
            '60007946',
            '60011094',
            '60009319',
            '60006303',
            '60005135',
            '60003693',
            '60010380',
            '60012282',
            '60005851',
            '9299',
            '9420',
            '9377',
            '9678',
            '9671',
            '9957',
            '12657',
            '12641',
            '9471',
            '9962',
            '9563',
            '12225',
            '9679',
            '9395',
            '9604',
            '12328',
            '9629',
            '9874',
            '10012',
            '9677',
            '9368',
            '9665',
            '9692',
            '12782',
            '12830',
            '11217',
            '12995',
            '12521',
            '11106',
            '11110',
            '9809',
            '3622',
            '9615',
        ];

        $salesOfficers = User::whereIn('emp_code', $soCodes)
                    ->with('dsms.routeUsers.route')
                    ->get();

        foreach($salesOfficers as $salesOfficer) {
            foreach($salesOfficer->dsms as $dsm) {
                foreach($dsm->routeUsers as $routeUser) {
                    if($route = $routeUser->route) {
                        $values = [];
                        $values[] = $route->_id;
                        $values[] = $route->sap_code;
                        $values[] = $route->name;

                        $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                        $writer->addRow($rowFromValues);
                    }
                }
            }
        }

        $writer->close();
    }

    public function exportDistributorsToRemoveCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('distributors-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'ID',
            'DB CODE',
            'DB NAME'
        ]);
        $writer->addRow($headingRow);

        $empCodes = [
            '180064947',
            '180064431',
            '180062170',
            '180070290',
            '180050590',
            '180061579',
            '180062294',
            '180063348',
            '180063358',
            '180063450',
            '180064437',
            '180061468',
            '180054963',
            '180070604',
            '180063238',
        ];

        $users = User::whereIn('emp_code', $empCodes)
                    ->with('distributor')
                    ->get();

        foreach($users as $user) {
            if($distributor = $user->distributor) {
                $values = [];
                $values[] = $distributor->_id;
                $values[] = $distributor->sap_code;
                $values[] = $distributor->name;

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        }

        $writer->close();
    }

    public function exportDsmsToRemoveCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('dsms-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'DSM ID',
            'DSM CODE',
            'DSM NAME',
            'SO ID',
            'SO CODE',
            'SO NAME'
        ]);
        $writer->addRow($headingRow);

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                $dsms = $vertical->users()->where('role', 'dsm')->get();
                foreach($dsms as $dsm) {
                    $salesOfficer = $dsm->salesOfficer;

                    $values = [];
                    $values[] = $dsm->_id;
                    $values[] = $dsm->emp_code;
                    $values[] = $dsm->name;
                    $values[] = $salesOfficer ? $salesOfficer->_id : '';
                    $values[] = $salesOfficer ? $salesOfficer->emp_code : '';
                    $values[] = $salesOfficer ? $salesOfficer->name : '';

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            }
        }

        $writer->close();
    }

    public function exportDsmsToRemoveCsv2() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('dsms-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'DSM ID',
            'DSM CODE',
            'DSM NAME',
            'SO ID',
            'SO CODE',
            'SO NAME'
        ]);
        $writer->addRow($headingRow);

        $empCodes = [
            '180064947',
            '180064431',
            '180062170',
            '180070290',
            '180050590',
            '180061579',
            '180062294',
            '180063348',
            '180063358',
            '180063450',
            '180064437',
            '180061468',
            '180054963',
            '180070604',
            '180063238',
        ];

        $dsms = User::whereIn('emp_code', $empCodes)
                    ->with('salesOfficer')
                    ->get();

        foreach($dsms as $dsm) {
            $salesOfficer = $dsm->salesOfficer;

            $values = [];
            $values[] = $dsm->_id;
            $values[] = $dsm->emp_code;
            $values[] = $dsm->name;
            $values[] = $salesOfficer ? $salesOfficer->_id : '';
            $values[] = $salesOfficer ? $salesOfficer->emp_code : '';
            $values[] = $salesOfficer ? $salesOfficer->name : '';

            $rowFromValues = WriterEntityFactory::createRowFromArray($values);
            $writer->addRow($rowFromValues);
        }

        $writer->close();
    }

    public function exportDsmsToRemoveCsv3() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('dsms-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'DSM ID',
            'DSM CODE',
            'DSM NAME',
            'SO ID',
            'SO CODE',
            'SO NAME',
            'DB ID',
            'DB CODE',
            'DB NAME'
        ]);
        $writer->addRow($headingRow);

        $soCodes = [
            '60008751',
            '60007978',
            '60008137',
            '60003624',
            '60009059',
            '60009375',
            '60007408',
            '60011673',
            '60003200',
            '60007218',
            '60009796',
            '60011627',
            '60005853',
            '60003260',
            '60003008',
            '60002513',
            '60007279',
            '60007116',
            '60008078',
            '60007219',
            '60006342',
            '60007220',
            '60005208',
            '60006425',
            '60009423',
            '60006371',
            '60007947',
            '60003007',
            '60007414',
            '60010468',
            '60008279',
            '60005494',
            '60011950',
            '60006885',
            '60004862',
            '60011985',
            '60010406',
            '60007418',
            '60012412',
            '60011590',
            '60004663',
            '60011435',
            '60010003',
            '60007624',
            '60011803',
            '60005058',
            '60007512',
            '60006747',
            '60010831',
            '60007016',
            '60007591',
            '60005610',
            '60007251',
            '60010492',
            '60007657',
            '60012597',
            '60004855',
            '60007829',
            '60003598',
            '60004239',
            '60009611',
            '60004076',
            '60006956',
            '60007078',
            '60007905',
            '60008432',
            '60002767',
            '60004055',
            '60009763',
            '60009801',
            '60003613',
            '60011529',
            '60001115',
            '60011968',
            '60009249',
            '60008768',
            '60005471',
            '60008217',
            '60006886',
            '60005311',
            '60003190',
            '60003527',
            '60011104',
            '60012469',
            '60007946',
            '60011094',
            '60009319',
            '60006303',
            '60005135',
            '60003693',
            '60010380',
            '60012282',
            '60005851',
            '9299',
            '9420',
            '9377',
            '9678',
            '9671',
            '9957',
            '12657',
            '12641',
            '9471',
            '9962',
            '9563',
            '12225',
            '9679',
            '9395',
            '9604',
            '12328',
            '9629',
            '9874',
            '10012',
            '9677',
            '9368',
            '9665',
            '9692',
            '12782',
            '12830',
            '11217',
            '12995',
            '12521',
            '11106',
            '11110',
            '9809',
            '3622',
            '9615',
        ];

        $salesOfficers = User::whereIn('emp_code', $soCodes)
                    ->with('dsms.distributor')
                    ->get();

        foreach($salesOfficers as $salesOfficer) {
            foreach($salesOfficer->dsms as $dsm) {
                $distributor = $dsm->distributor;

                $values = [];
                $values[] = $dsm->_id;
                $values[] = $dsm->emp_code;
                $values[] = $dsm->name;
                $values[] = $salesOfficer->_id;
                $values[] = $salesOfficer->emp_code;
                $values[] = $salesOfficer->name;
                $values[] = $distributor ? $distributor->_id : '';
                $values[] = $distributor ? $distributor->sap_code : '';
                $values[] = $distributor ? $distributor->name : '';

                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        }

        $writer->close();
    }

    public function exportSoToRemoveCsv() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('so-to-remove.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'SO ID',
            'SO CODE',
            'SO NAME'
        ]);
        $writer->addRow($headingRow);

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                $salesOfficers = $vertical->users()->where('role', 'sales-officer')->get();
                foreach($salesOfficers as $salesOfficer) {
                    $values = [];
                    $values[] = $salesOfficer->_id;
                    $values[] = $salesOfficer->emp_code;
                    $values[] = $salesOfficer->name;

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            }
        }

        $writer->close();
    }

    // Step 2
    public function removeCustomerVisits() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                foreach($vertical->users as $user) {
                    $user->customerVisits()->delete();

                    $routeIds = $user->routeUsers()
                                ->whereNotNull('route_id')
                                ->pluck('route_id');

                    $customerIds = Customer::whereIn('route_id', $routeIds)->pluck('_id');
                    CustomerVisit::whereIn('customer_id', $customerIds)->delete();
                }
            }
        }

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Customer Visits removed successfully');
    }

    // Step 3
    public function removeCustomers() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                foreach($vertical->users as $user) {
                    $routeIds = $user->routeUsers()
                                ->whereNotNull('route_id')
                                ->pluck('route_id');
                
                    $routes = Route::whereIn('_id', $routeIds)->get();
                    foreach($routes as $route) {
                        $route->customers()->delete();
                    }
                }
            }
        }

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Customers removed successfully');
    }

    // Step 4
    public function removeRoutes() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                foreach($vertical->users as $user) {
                    $routeIds = $user->routeUsers()
                                ->whereNotNull('route_id')
                                ->pluck('route_id');
                
                    Route::whereIn('_id', $routeIds)->delete();
                }
            }
        }

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Routes removed successfully');
    }

    // Step 5
    public function removeUsersData() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                $users = $vertical->users()->where('role', 'dsm')->get();

                foreach($users as $user) {
                    if($user->salesOfficer) {
                        $user->salesOfficer->delete();
                    }
                    
                    $user->attendances()->delete();
                    $user->geolocations()->delete();
                    $user->routeUsers()->delete();
                }
            }
        }

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Users Data removed successfully');
    }

    // Step 6
    public function removeUsers() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $names = [
            'PHYSICAL OIL and GHEE',
            'FLOUR and SUGAR',
            'RICE/PULSES/SPICES',
            'JUICES and NATURAL BEVERAGES',
            'MEDICINES',
            'OTHERS',
        ];

        foreach($names as $name) {
            $vertical = Vertical::whereRaw([
                'name' => [
                    '$regex' => '^' . $name . '$',
                    '$options' => 'i'
                ]
            ])->first();

            if($vertical) {
                $vertical->users()->delete();
            }
        }

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Users removed successfully');
    }

    public function removeSo() {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');

        $soCodes = [
            '60008751',
            '60007978',
            '60008137',
            '60003624',
            '60009059',
            '60009375',
            '60007408',
            '60011673',
            '60003200',
            '60007218',
            '60009796',
            '60011627',
            '60005853',
            '60003260',
            '60003008',
            '60002513',
            '60007279',
            '60007116',
            '60008078',
            '60007219',
            '60006342',
            '60007220',
            '60005208',
            '60006425',
            '60009423',
            '60006371',
            '60007947',
            '60003007',
            '60007414',
            '60010468',
            '60008279',
            '60005494',
            '60011950',
            '60006885',
            '60004862',
            '60011985',
            '60010406',
            '60007418',
            '60012412',
            '60011590',
            '60004663',
            '60011435',
            '60010003',
            '60007624',
            '60011803',
            '60005058',
            '60007512',
            '60006747',
            '60010831',
            '60007016',
            '60007591',
            '60005610',
            '60007251',
            '60010492',
            '60007657',
            '60012597',
            '60004855',
            '60007829',
            '60003598',
            '60004239',
            '60009611',
            '60004076',
            '60006956',
            '60007078',
            '60007905',
            '60008432',
            '60002767',
            '60004055',
            '60009763',
            '60009801',
            '60003613',
            '60011529',
            '60001115',
            '60011968',
            '60009249',
            '60008768',
            '60005471',
            '60008217',
            '60006886',
            '60005311',
            '60003190',
            '60003527',
            '60011104',
            '60012469',
            '60007946',
            '60011094',
            '60009319',
            '60006303',
            '60005135',
            '60003693',
            '60010380',
            '60012282',
            '60005851',
            '9299',
            '9420',
            '9377',
            '9678',
            '9671',
            '9957',
            '12657',
            '12641',
            '9471',
            '9962',
            '9563',
            '12225',
            '9679',
            '9395',
            '9604',
            '12328',
            '9629',
            '9874',
            '10012',
            '9677',
            '9368',
            '9665',
            '9692',
            '12782',
            '12830',
            '11217',
            '12995',
            '12521',
            '11106',
            '11110',
            '9809',
            '3622',
            '9615',
        ];

        User::whereIn('emp_code', $soCodes)->delete();

        return redirect()
                ->action('VerticalController@index')
                ->with('success', 'Users removed successfully');
    }
}
