<?php

namespace App\Exports;

use Illuminate\Support\Collection;

use App\Order;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use stdClass;

class OrderDetailsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
	public function __construct(Order $order) {
		$this->order = $order;
	}

	public function headings(): array {
		return [
			'ITEM SAP CODE',
            'ITEM DESCRIPTION',
            'QTY',
            'UOM',
            'RATE',
            'AMOUNT'
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
    	$products = [];
    	
        foreach($this->order->orderProducts as $orderProduct) {
            $product = new stdClass;
            $product->sap_code = $orderProduct->product ? $orderProduct->product->sap_code : '';
            $product->name = $orderProduct->product ? $orderProduct->product->name : '';
            $product->quantity = $orderProduct->quantity;
            $product->unit = $orderProduct->product && $orderProduct->product->unit
                                ? $orderProduct->product->unit->name
                                : '';
            $product->distributorsellingprice = $orderProduct->product ? $orderProduct->product->distributorsellingprice : '';
            $product->amount = $orderProduct->quantity && $orderProduct->product && $orderProduct->product->distributorsellingprice
                                ? $orderProduct->quantity * $orderProduct->product->distributorsellingprice
                                : '';

            $products[] = $product;
        }

        return new Collection($products);
    }
}
