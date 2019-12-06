<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Customer;

use App\Distributor;

use App\Order;

use stdClass;

class PosController extends Controller
{
    /**
     * Get order details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOrderDetails($id)
    {
        $order = Order::with('orderProducts.product')
        			->find($id);

        if(!$order) {
            return response()->json([
                'status' => 'error',
                'result' => null,
                'message' => 'Invalid ID'
            ]);
        }

        $products = [];
        foreach($order->orderProducts as $orderProduct) {
            $product = new stdClass;
            $product->MCODE = $orderProduct->product ? '' . $orderProduct->product->sap_code : '';
            $product->QUANTITY = $orderProduct->quantity;

            $products[] = $product;
        }

        return response()->json([
            'status' => 'ok',
            'result' => $products,
            'message' => null
        ]);
    }

    /**
     * Mark order as synced.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markOrderSynced($id)
    {
        $order = Order::find($id);

        $error = '';

        if(!$order) {
            $error = 'Invalid ID';
        }
        else if($order->is_pos_synced) {
            $error = 'Order has already been marked as synced';
        }

        if($error) {
            return response()->json([
                'status' => 'error',
                'result' => null,
                'message' => $error
            ]);
        }

        $order->is_pos_synced = true;
        $order->save();

        return response()->json([
            'status' => 'ok',
            'result' => null,
            'message' => null
        ]);
    }

    /**
     * Get retailers of distributor.
     *
     * @param  string  $db_code
     * @return \Illuminate\Http\Response
     */
    public function getRetailers($db_code)
    {
        $distributor = Distributor::where('sap_code', $db_code)->first();

        if(!$distributor) {
            return response()->json([
                'status' => 'error',
                'result' => null,
                'message' => 'Invalid Distributor Code'
            ]);
        }

        $routeIds = $distributor->routes()->pluck('_id');

        $models = Customer::whereIn('route_id', $routeIds)
                    ->with(['customerClass:name', 'customerType:name', 'billingState:name', 'shippingState:name'])
                    ->get();

        $customers = [];
        foreach($models as $model) {
            $customer = new stdClass;
            $customer->sap_code = $model->sap_code;
            $customer->name = $model->name;
            $customer->customer_type = $model->customerType ? $model->customerType->name : '';
            $customer->customer_class = $model->customerClass ? $model->customerClass->name : '';
            $customer->gst_number = $model->gst_number;
            $customer->town = $model->town;
            $customer->longitude = $model->longitude;
            $customer->latitude = $model->latitude;
            $customer->owner_name = $model->owner_name;
            $customer->owner_email = $model->owner_email;
            $customer->owner_contact_number = $model->owner_contact_number;
            $customer->billing_state = $model->billingState ? $model->billingState->name : '';
            $customer->billing_district = $model->billing_district;
            $customer->billing_city = $model->billing_city;
            $customer->billing_address = $model->billing_address;
            $customer->billing_pincode = $model->billing_pincode;
            $customer->shipping_state = $model->shippingState ? $model->shippingState->name : '';
            $customer->shipping_district = $model->shipping_district;
            $customer->shipping_city = $model->shipping_city;
            $customer->shipping_address = $model->shipping_address;
            $customer->shipping_pincode = $model->shipping_pincode;

            $customers[] = $customer;
        }

        return response()->json([
            'status' => 'ok',
            'result' => $customers,
            'message' => null
        ]);
    }
}
