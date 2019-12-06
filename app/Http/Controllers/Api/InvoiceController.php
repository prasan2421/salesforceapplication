<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;

use App\Customer;

use App\Invoice;

use App\InvoiceProduct;

use App\Order;

use App\Product;

use App\Helpers\Common;

use stdClass;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('app.version');
        $this->middleware('token');
    }

    /**
     * Get all invoices.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInvoices()
    {
        $models = Invoice::where('user_id', request()->user->id)
                        ->select('id', 'total_amount', 'customer_id', 'created_at')
                        ->with('customer:name')
                        ->get();

        $invoices = [];
        foreach($models as $model) {
            $invoice = new stdClass;
            $invoice->_id = $model->_id;
            $invoice->total_amount = $model->total_amount;
            $invoice->customer = $model->customer ? $model->customer->name : '';
            $invoice->created_at = $model->created_at;

            $invoices[] = $invoice;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'invoices' => $invoices
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get invoice details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getInvoiceDetails($id)
    {
        $invoice = Invoice::where('user_id', request()->user->id)
                        ->find($id);

        if(!$invoice) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $products = [];
        foreach($invoice->invoiceProducts as $invoiceProduct) {
            $product = new stdClass;
            $product->_id = $invoiceProduct->_id;
            $product->quantity = $invoiceProduct->quantity;
            $product->name = $invoiceProduct->product ? $invoiceProduct->product->name : '';
            $product->unit = $invoiceProduct->product && $invoiceProduct->product->unit
                                ? $invoiceProduct->product->unit->name
                                : '';

            $products[] = $product;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_amount' => $invoice->total_amount,
                'customer' => $invoice->customer ? $invoice->customer->name : '',
                'created_at' => $invoice->created_at,
                'products' => $products
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Add new invoice.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addInvoice(Request $request)
    {
        $order_id = trim($request->order_id);
        $total_amount = trim($request->total_amount);
        $products = trim($request->products);

        $errors = [];
        
        if(!$order_id) {
            $errors['order_id'] = 'Order Id is required';
        }
        else if(!($order = Order::where('user_id', request()->user->id)->find($order_id))) {
            $errors['order_id'] = 'Order Id is invalid';
        }
        else if($order->invoice) {
            $errors['order_id'] = 'Invoice already added';
        }

        if(!$total_amount) {
            $errors['total_amount'] = 'Total Amount is required';
        }
        
        if(!$products) {
            $errors['products'] = 'Products is required';
        }
        else if(!($productsDecoded = json_decode($products)) || !is_array($productsDecoded)) {
            $errors['products'] = 'Products is invalid';
        }
        else {
            $invalidJson = false;
            $invalidProductId = false;
            $invalidQuantity = false;
            // $invoiceProducts = [];

            foreach($productsDecoded as $product) {
                if(!is_object($product) || !property_exists($product, '_id') || !property_exists($product, 'quantity')) {
                    $invalidJson = true;
                    break;
                }
                else if(Product::where('_id', $product->_id)->count() == 0) {
                    $invalidProductId = true;
                    break;
                }
                else if(!is_numeric($product->quantity) || !is_int(+$product->quantity) || +$product->quantity < 0) {
                    $invalidQuantity = true;
                    break;
                }
                // else {
                //     $invoiceProduct = new InvoiceProduct;
                //     $invoiceProduct->product_id = $product->_id;
                //     $invoiceProduct->quantity = $product->quantity;

                //     $invoiceProducts[] = $invoiceProduct;
                // }
            }

            if($invalidJson) {
                $errors['products'] = 'Products is invalid';
            }
            else if($invalidProductId) {
                $errors['products'] = 'One or more product IDs are invalid';
            }
            else if($invalidQuantity) {
                $errors['products'] = 'One or more quantities are invalid';
            }
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $invoice = new Invoice;
        $invoice->total_amount = Common::nullIfEmpty($total_amount);
        $invoice->order_id = Common::nullIfEmpty($order_id);
        $invoice->customer_id = $order->customer_id;
        $invoice->user_id = $request->user->id;
        $invoice->save();

        // $invoice->invoiceProducts()->saveMany($invoiceProducts);

        foreach($productsDecoded as $product) {
            $invoiceProduct = new InvoiceProduct;
            $invoiceProduct->invoice_id = $invoice->_id;
            $invoiceProduct->product_id = $product->_id;
            $invoiceProduct->quantity = $product->quantity;
            $invoiceProduct->save();
        }

        return response()->json([
            'success' => true,
            'data' => [
                '_id' => $invoice->_id
            ],
            'errors' => new stdClass
        ]);
    }
}
