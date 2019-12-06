<?php

namespace App\Exports;

use Illuminate\Support\Collection;

use App\Order;

use App\User;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use DateTime;

use stdClass;

class OrdersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct($startDate, $endDate) {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

	public function headings(): array {
		return [
			'Retailer Name',
            'State',
            'DB Code',
            'DB Name',
            'DSM Code',
            'DSM Name',
            'Item SAP Code',
            'Item Name',
            'Ordered Qty',
            'Order Amount',
            'Invoice Qty',
            'Invoice Amount',
            'Bill Date'
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $query = Order::where('created_at', '>=', new DateTime($this->startDate . ' 00:00:00'))
                        ->where('created_at', '<=', new DateTime($this->endDate . ' 23:59:59'))
                        ->select('customer_id', 'user_id', 'created_at')
        				->with(['customer.billingState', 'user.distributor', 'orderProducts.product', 'invoice.invoiceProducts.product']);

        if(request()->user()->role == 'sales-officer') {
            $query->whereIn('user_id', request()->user()->dsms()->pluck('_id'));
        }

        $orders = $query->get();

        $products = [];

        foreach($orders as $order) {
        	$retailer = $order->customer ? $order->customer->name : '';
    		$state = $order->customer && $order->customer->billingState
    							? $order->customer->billingState->name
    							: '';
    		$db_code = $order->user && $order->user->distributor
    							? $order->user->distributor->sap_code
    							: '';
    		$db_name = $order->user && $order->user->distributor
    							? $order->user->distributor->name
    							: '';
    		$dsm_code = $order->user ? $order->user->emp_code : '';
    		$dsm_name = $order->user ? $order->user->name : '';
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

                $product = new stdClass;
                $product->retailer = $retailer;
                $product->state = $state;
                $product->db_code = $db_code;
                $product->db_name = $db_name;
                $product->dsm_code = $dsm_code;
                $product->dsm_name = $dsm_name;
                $product->item_code = $orderProduct
                                        ? $orderProduct->product->sap_code
                                        : $invoiceProduct->product->sap_code;
                $product->item_name = $orderProduct
                                        ? $orderProduct->product->name
                                        : $invoiceProduct->product->name;
                $product->order_quantity = $orderProduct ? $orderProduct->quantity : null;
                $product->order_amount = $orderProduct && $orderProduct->quantity && $orderProduct->product->distributorsellingprice
                        ? $orderProduct->quantity * $orderProduct->product->distributorsellingprice
                        : '';
                $product->invoice_quantity = $invoiceProduct ? $invoiceProduct->quantity : null;
                $product->invoice_amount = $invoiceProduct && $invoiceProduct->quantity && $invoiceProduct->product->distributorsellingprice
                        ? $invoiceProduct->quantity * $invoiceProduct->product->distributorsellingprice
                        : '';
                $product->bill_date = $bill_date;

                $products[] = $product;
            }

        	// foreach($order->orderProducts as $orderProduct) {
        	// 	$product = new stdClass;
        	// 	$product->retailer = $retailer;
        	// 	$product->state = $state;
        	// 	$product->db_code = $db_code;
        	// 	$product->db_name = $db_name;
        	// 	$product->dsm_code = $dsm_code;
        	// 	$product->dsm_name = $dsm_name;
        	// 	$product->item_code = $orderProduct->product ? $orderProduct->product->sap_code : '';
        	// 	$product->item_name = $orderProduct->product ? $orderProduct->product->name : '';
        	// 	$product->order_quantity = $orderProduct->quantity;
        	// 	$product->order_amount = '';
        	// 	$product->invoice_quantity = '';
        	// 	$product->invoice_amount = '';
        	// 	$product->bill_date = $bill_date;

        	// 	$products[] = $product;
        	// }
        }

        return new Collection($products);
    }
}
