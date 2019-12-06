<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use App\Order;

use App\Exports\OrdersExport;

use App\Exports\OrdersSummaryExport;

use App\Helpers\DataTableHelper;

use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

use DateTime;

use Excel;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin', [ 'only' => [ 'create', 'store', 'edit', 'update', 'destroy' ] ]);
        $this->middleware('role:admin|sales-officer', [ 'only' => [ 'index', 'getData', 'show' ] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('orders.index');
    }

    public function getData() {
        $data = Order::select('id', 'created_at', 'updated_at');

        if(request()->user()->role == 'sales-officer') {
            $userIds = request()->user()->dsms()->pluck('_id')->toArray();
            $userIds[] = request()->user()->id;

            $data->whereIn('user_id', $userIds);
        }

        return DataTableHelper::of($data)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('OrderController@show', $model->id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '</div>';

                    return $content;
                })
                ->make();
    }

    /*public function getData() {
        $pipeline = [
            [
                '$addFields' => [
                    '_id' => [
                        '$toString' => '$_id'
                    ],
                    'user_id' => [
                        '$toObjectId' => '$user_id'
                    ],
                    'created_at' => [
                        '$dateToString' => [
                            'date' => '$created_at',
                            'format' => '%Y-%m-%d %H:%M:%S',
                            'timezone' => config('app.timezone')
                        ]
                    ],
                    'updated_at' => [
                        '$dateToString' => [
                            'date' => '$updated_at',
                            'format' => '%Y-%m-%d %H:%M:%S',
                            'timezone' => config('app.timezone')
                        ]
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
                    '_id' => 1,
                    'created_at' => 1,
                    'updated_at' => 1,
                    'user_name' => [
                        '$ifNull' => [ '$user.name', '' ]
                    ]
                ]
            ]
        ];

        if(request()->user()->role == 'sales-officer') {
            $pipeline = array_merge([
                [
                    '$match' => [
                        '$expr' => [
                            '$in' => [
                                '$user_id',
                                request()->user()->dsms()->pluck('_id')->toArray()
                            ]
                        ]
                    ]
                ]
            ], $pipeline);
        }

        return DataTableHelper::aggregate('orders', $pipeline)
                ->addColumn('action', function($model) {
                    $content = '<div class="buttons">';

                    $content .= '<a href="' . action('OrderController@show', $model->_id) . '" class="btn btn-icon btn-info" data-toggle="tooltip" data-placement="top" title="View"><i class="fas fa-eye"></i></a>';

                    $content .= '</div>';

                    return $content;
                })
                ->make();
    }*/

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = Order::query();

        if(request()->user()->role == 'sales-officer') {
            $userIds = request()->user()->dsms()->pluck('_id')->toArray();
            $userIds[] = request()->user()->id;

            $query->whereIn('user_id', $userIds);
        }
        
        $order = $query->findOrFail($id);

        return view('orders.show', [
            'order' => $order
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function exportExcel() {
        return view('orders.export-excel');
    }

    public function submitExportExcel(Request $request) {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        return Excel::download(new OrdersExport($request->start_date, $request->end_date), 'orders.xlsx');
    }

    public function exportSummaryExcel() {
        return view('orders.export-summary-excel');
    }

    public function submitExportSummaryExcel(Request $request) {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        return Excel::download(new OrdersSummaryExport($request->start_date, $request->end_date), 'orders_summary.xlsx');
    }

    public function exportCsv() {
        return view('orders.export-csv');
    }

    public function submitExportCsv(Request $request) {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('orders.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'Retailer Code',
            'Retailer Name',
            'State',
            'DB Code',
            'DB Name',
            'DSM Code',
            'DSM Name',
            'SO Code',
            'SO Name',
            'Item SAP Code',
            'Item Name',
            'Ordered Qty',
            'Order Amount',
            'Invoice Qty',
            'Invoice Amount',
            'Bill Date'
        ]);
        $writer->addRow($headingRow);

        $query = Order::where('created_at', '>=', new DateTime($request->start_date . ' 00:00:00'))
                        ->where('created_at', '<=', new DateTime($request->end_date . ' 23:59:59'))
                        ->select('customer_id', 'user_id', 'created_at')
                        ->with(['customer.state', 'user.distributor', 'user.salesOfficer', 'orderProducts.product', 'invoice.invoiceProducts.product']);

        if(request()->user()->role == 'sales-officer') {
            $userIds = request()->user()->dsms()->pluck('_id')->toArray();
            $userIds[] = request()->user()->id;

            $query->whereIn('user_id', $userIds);
        }

        $query->chunk(10000, function($orders) use ($writer) {
            foreach($orders as $order) {
                $retailer_code = $order->customer ? $order->customer->sap_code : '';
                $retailer_name = $order->customer ? $order->customer->name : '';
                $state = $order->customer && $order->customer->state
                                    ? $order->customer->state->name
                                    : '';
                $db_code = $order->user && $order->user->distributor
                                    ? $order->user->distributor->sap_code
                                    : '';
                $db_name = $order->user && $order->user->distributor
                                    ? $order->user->distributor->name
                                    : '';
                $dsm_code = $order->user ? $order->user->emp_code : '';
                $dsm_name = $order->user ? $order->user->name : '';
                $so_code = $order->user && $order->user->salesOfficer
                                    ? $order->user->salesOfficer->emp_code
                                    : '';
                $so_name = $order->user && $order->user->salesOfficer
                                    ? $order->user->salesOfficer->name
                                    : '';
                $bill_date = date('Y-m-d', strtotime($order->created_at));

                $orderProducts = [];
                $invoiceProducts = [];

                foreach($order->orderProducts as $orderProduct) {
                    if(!($product = $orderProduct->product)) {
                        continue;
                    }

                    $orderProducts[$product->_id] = $orderProduct;
                }

                if($invoice = $order->invoice) {
                    foreach($invoice->invoiceProducts as $invoiceProduct) {
                        if(!($product = $invoiceProduct->product)) {
                            continue;
                        }

                        $invoiceProducts[$product->_id] = $invoiceProduct;
                    }
                }

                $productIds = array_unique(array_merge(array_keys($orderProducts), array_keys($invoiceProducts)));

                foreach($productIds as $productId) {
                    $orderProduct = isset($orderProducts[$productId]) ? $orderProducts[$productId] : null;
                    $invoiceProduct = isset($invoiceProducts[$productId]) ? $invoiceProducts[$productId] : null;

                    $values = [];
                    $values[] = $retailer_code;
                    $values[] = $retailer_name;
                    $values[] = $state;
                    $values[] = $db_code;
                    $values[] = $db_name;
                    $values[] = $dsm_code;
                    $values[] = $dsm_name;
                    $values[] = $so_code;
                    $values[] = $so_name;
                    $values[] = $orderProduct
                                            ? $orderProduct->product->sap_code
                                            : $invoiceProduct->product->sap_code;
                    $values[] = $orderProduct
                                            ? $orderProduct->product->name
                                            : $invoiceProduct->product->name;
                    $values[] = $orderProduct ? $orderProduct->quantity : null;
                    $values[] = $orderProduct && $orderProduct->quantity && $orderProduct->product->distributorsellingprice
                            ? $orderProduct->quantity * $orderProduct->product->distributorsellingprice
                            : '';
                    $values[] = $invoiceProduct ? $invoiceProduct->quantity : null;
                    $values[] = $invoiceProduct && $invoiceProduct->quantity && $invoiceProduct->product->distributorsellingprice
                            ? $invoiceProduct->quantity * $invoiceProduct->product->distributorsellingprice
                            : '';
                    $values[] = $bill_date;

                    $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                    $writer->addRow($rowFromValues);
                }
            }
        });

        $writer->close();
    }

    public function exportSummaryCsv() {
        return view('orders.export-summary-csv');
    }

    public function submitExportSummaryCsv(Request $request) {
        $request->validate([
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date'
        ]);

        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '8192M');
        
        $writer = WriterEntityFactory::createCSVWriter();
        $writer->openToBrowser('orders.csv');

        $headingRow = WriterEntityFactory::createRowFromArray([
            'DSM Code',
            'DSM Name',
            'Ordered Qty',
            'Order Amount',
            'Invoice Qty',
            'Invoice Amount',
            'Order Date'
        ]);
        $writer->addRow($headingRow);

        $query = Order::where('created_at', '>=', new DateTime($request->start_date . ' 00:00:00'))
                        ->where('created_at', '<=', new DateTime($request->end_date . ' 23:59:59'))
                        ->select('user_id', 'created_at')
                        ->with(['user', 'orderProducts.product', 'invoice.invoiceProducts']);

        if(request()->user()->role == 'sales-officer') {
            $userIds = request()->user()->dsms()->pluck('_id')->toArray();
            $userIds[] = request()->user()->id;

            $query->whereIn('user_id', $userIds);
        }

        $query->chunk(10000, function($orders) use ($writer) {
            foreach($orders as $order) {
                $order_quantity = 0;
                $order_amount = 0;
                $invoice_quantity = 0;

                foreach($order->orderProducts as $orderProduct) {
                    $order_quantity += $orderProduct->quantity;
                    if($orderProduct->quantity
                        && $orderProduct->product
                        && $orderProduct->product->distributorsellingprice) {
                        $order_amount += $orderProduct->quantity * $orderProduct->product->distributorsellingprice;
                    }
                }

                if($order->invoice) {
                    foreach($order->invoice->invoiceProducts as $invoiceProduct) {
                        $invoice_quantity += $invoiceProduct->quantity;
                    }
                }

                $values = [];
                $values[] = $order->user ? $order->user->emp_code : '';
                $values[] = $order->user ? $order->user->name : '';
                $values[] = $order_quantity;
                $values[] = $order_amount;
                $values[] = $invoice_quantity;
                $values[] = $order->invoice ? $order->invoice->total_amount : '';
                $values[] = date('Y-m-d', strtotime($order->created_at));
                
                $rowFromValues = WriterEntityFactory::createRowFromArray($values);
                $writer->addRow($rowFromValues);
            }
        });

        $writer->close();
    }

    public function setPosNotifiedStatus() {
        Order::where('created_at', '>=', new DateTime('2019-09-30 00:00:00'))
        ->update([
            'is_pos_notified' => false
        ]);

        return redirect()
                ->action('OrderController@index')
                ->with('success', 'POS notified status set successfully');
    }
}
