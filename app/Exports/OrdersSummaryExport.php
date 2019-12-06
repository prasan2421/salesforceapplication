<?php

namespace App\Exports;

use Illuminate\Support\Collection;

use App\Order;

use App\User;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use DateTime;

use DateTimezone;

use DB;

use stdClass;

class OrdersSummaryExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

	public function headings(): array {
		return [
            'DSM Code',
            'DSM Name',
            'Ordered Qty',
            'Order Amount',
            'Invoice Qty',
            'Invoice Amount',
            'Order Date'
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Order::where('created_at', '>=', new DateTime($this->startDate . ' 00:00:00'))
                        ->where('created_at', '<=', new DateTime($this->endDate . ' 23:59:59'))
                        ->select('user_id', 'created_at')
                        ->with(['user', 'orderProducts.product', 'invoice.invoiceProducts']);

        if(request()->user()->role == 'sales-officer') {
            $query->whereIn('user_id', request()->user()->dsms()->pluck('_id'));
        }

        $models = $query->get();

        $orders = [];

        foreach($models as $model) {
            $order_quantity = 0;
            $order_amount = 0;
            $invoice_quantity = 0;

            foreach($model->orderProducts as $orderProduct) {
                $order_quantity += $orderProduct->quantity;
                if($orderProduct->quantity
                    && $orderProduct->product
                    && $orderProduct->product->distributorsellingprice) {
                    $order_amount += $orderProduct->quantity * $orderProduct->product->distributorsellingprice;
                }
            }

            if($model->invoice) {
                foreach($model->invoice->invoiceProducts as $invoiceProduct) {
                    $invoice_quantity += $invoiceProduct->quantity;
                }
            }

            $order = new stdClass;
            $order->dsm_code = $model->user ? $model->user->emp_code : '';
            $order->dsm_name = $model->user ? $model->user->name : '';
            $order->order_quantity = $order_quantity;
            $order->order_amount = $order_amount;
            $order->invoice_quantity = $invoice_quantity;
            $order->invoice_amount = $model->invoice ? $model->invoice->total_amount : '';
            $order->order_date = date('Y-m-d', strtotime($model->created_at));
            
            $orders[] = $order;
        }

        return new Collection($orders);
    }

    /*public function collection()
    {
        $db = DB::getMongoDB();

        $pipeline = [
        	[
        		'$addFields' => [
        			'user_id' => [
        				'$toObjectId' => '$user_id'
        			],
        			'_id' => [
        				'$toString' => '$_id'
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
        		'$lookup' => [
        			'from' => 'order_product',
        			'localField' => '_id',
        			'foreignField' => 'order_id',
        			'as' => 'order_product'
        		]
        	],
        	[
        		'$project' => [
        			'created_at' => 1,
        			'dsm_code' => '$user.emp_code',
        			'dsm_name' => '$user.name',
        			'order_quantity' => [
        				'$sum' => '$order_product.quantity'
        			]
        		]
        	]
        ];

        $results = $db->orders->aggregate($pipeline);

        $orders = [];

        foreach($results as $row) {
        	$order = new stdClass;
        	$order->dsm_code = property_exists($row, 'dsm_code') ? $row->dsm_code : '';
        	$order->dsm_name = property_exists($row, 'dsm_name') ? $row->dsm_name : '';
        	$order->order_quantity = property_exists($row, 'order_quantity') ? $row->order_quantity : '';
        	$order->order_amount = '';
        	$order->invoice_amount = '';
        	$order->created_at = property_exists($row, 'created_at')
        							? $row->created_at
                                        ->toDateTime()
                                        ->setTimezone(new DateTimezone(config('app.timezone')))
                                        ->format('Y-m-d')
        							: '';

        	$orders[] = $order;
        }

        return new Collection($orders);
    }*/
}
