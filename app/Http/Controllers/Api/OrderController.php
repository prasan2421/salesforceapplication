<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;

use App\Http\Controllers\Controller;

use App\Customer;

use App\Order;

use App\OrderProduct;

use App\Product;

use App\Exports\OrderDetailsExport;

use App\Helpers\Common;

use App\Mail\OrderAddedExcel;

use App\Mail\OrderAddedPdf;

use PHPMailer\PHPMailer\PHPMailer;

use PHPMailer\PHPMailer\Exception;

use DateTime;

use Excel;

use PDF;

use stdClass;

class OrderController extends Controller
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
     * Get all orders.
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrders()
    {
        $date = request()->date;

        if(!Common::isDateValid($date)) {
            $date = date('Y-m-d');
        }

        $models = Order::where('user_id', request()->user->id)
                        ->where('created_at', '>=', new DateTime($date . ' 00:00:00'))
                        ->where('created_at', '<=', new DateTime($date . ' 23:59:59'))
                        ->select('id', 'customer_id', 'created_at')
                        ->with(['customer:name', 'invoice'])
                        ->get();

        $orders = [];
        foreach($models as $model) {
            $order = new stdClass;
            $order->_id = $model->_id;
            $order->customer = $model->customer ? $model->customer->name: '';
            $order->invoice_id = $model->invoice ? $model->invoice->_id : '';
            $order->created_at = $model->created_at;

            $orders[] = $order;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'orders' => $orders
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Get order details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getOrderDetails($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        if(!$order) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $products = [];
        foreach($order->orderProducts as $orderProduct) {
            $product = new stdClass;
            // $product->_id = $orderProduct->_id;
            $product->_id = $orderProduct->product ? $orderProduct->product->_id : '';
            $product->quantity = $orderProduct->quantity;
            $product->name = $orderProduct->product ? $orderProduct->product->name : '';
            $product->unit = $orderProduct->product && $orderProduct->product->unit
                                ? $orderProduct->product->unit->name
                                : '';
            $product->rate = $orderProduct->product ? $orderProduct->product->distributorsellingprice : null;

            $products[] = $product;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => $order->customer ? $order->customer->name : '',
                'created_at' => $order->created_at,
                'products' => $products
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Add new order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function addOrder(Request $request)
    {
        $customer_id = trim($request->customer_id);
        $products = trim($request->products);

        $errors = [];
        
        if(!$customer_id) {
            $errors['customer_id'] = 'Customer Id is required';
        }
        else if(!($customer = Customer::find($customer_id))) {
            $errors['customer_id'] = 'Customer Id is invalid';
        }
        else if(!$customer->owner_contact_number) {
            $errors['customer_id'] = 'Please update mobile number in shop';
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
            $totalQuantity = 0;
            $totalAmount = 0;
            // $orderProducts = [];

            foreach($productsDecoded as $product) {
                if(!is_object($product) || !property_exists($product, '_id') || !property_exists($product, 'quantity')) {
                    $invalidJson = true;
                    break;
                }
                else if(!($productModel = Product::find($product->_id))) {
                    $invalidProductId = true;
                    break;
                }
                else if(!is_numeric($product->quantity) || !is_int(+$product->quantity) || +$product->quantity <= 0) {
                    $invalidQuantity = true;
                    break;
                }
                else {
                    $totalQuantity += $product->quantity;

                    if($productModel->distributorsellingprice) {
                        $totalAmount += $product->quantity * $productModel->distributorsellingprice;
                    }

                    // $orderProduct = new OrderProduct;
                    // $orderProduct->product_id = $product->_id;
                    // $orderProduct->quantity = $product->quantity;

                    // $orderProducts[] = $orderProduct;
                }
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

        $order = new Order;
        $order->total_quantity = $totalQuantity;
        $order->total_amount = $totalAmount;
        $order->customer_id = Common::nullIfEmpty($customer_id);
        $order->user_id = $request->user->id;
        $order->is_pos_notified = false;
        $order->is_pos_synced = false;
        $order->save();

        // $order->orderProducts()->saveMany($orderProducts);

        foreach($productsDecoded as $product) {
            $orderProduct = new OrderProduct;
            $orderProduct->order_id = $order->_id;
            $orderProduct->product_id = $product->_id;
            $orderProduct->quantity = $product->quantity;
            $orderProduct->save();
        }

        return response()->json([
            'success' => true,
            'data' => [
                '_id' => $order->_id
            ],
            'errors' => new stdClass
        ]);
    }

    /**
     * Update order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOrder(Request $request, $id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        $errors = [];

        if(!$order) {
            $errors['id'] = 'Invalid ID';
        }
        else if($order->is_pos_synced) {
            $errors['id'] = 'Cannot edit order after it has been accepted';
        }
        else if($order->invoice) {
            $errors['id'] = 'Cannot edit order after invoice has been added';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        // $customer_id = trim($request->customer_id);
        $products = trim($request->products);
        
        // if(!$customer_id) {
        //     $errors['customer_id'] = 'Customer Id is required';
        // }
        // else if(Customer::where('_id', $customer_id)->count() == 0) {
        //     $errors['customer_id'] = 'Customer Id is invalid';
        // }
        
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
            $totalQuantity = 0;
            $totalAmount = 0;
            // $orderProducts = [];

            foreach($productsDecoded as $product) {
                if(!is_object($product) || !property_exists($product, '_id') || !property_exists($product, 'quantity')) {
                    $invalidJson = true;
                    break;
                }
                else if(!($productModel = Product::find($product->_id))) {
                    $invalidProductId = true;
                    break;
                }
                else if(!is_numeric($product->quantity) || !is_int(+$product->quantity) || +$product->quantity <= 0) {
                    $invalidQuantity = true;
                    break;
                }
                else {
                    $totalQuantity += $product->quantity;

                    if($productModel->distributorsellingprice) {
                        $totalAmount += $product->quantity * $productModel->distributorsellingprice;
                    }

                    // $orderProduct = new OrderProduct;
                    // $orderProduct->product_id = $product->_id;
                    // $orderProduct->quantity = $product->quantity;

                    // $orderProducts[] = $orderProduct;
                }
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

        $order->total_quantity = $totalQuantity;
        $order->total_amount = $totalAmount;
        // $order->customer_id = Common::nullIfEmpty($customer_id);
        $order->save();

        $order->orderProducts()->delete();

        // $order->orderProducts()->saveMany($orderProducts);

        foreach($productsDecoded as $product) {
            $orderProduct = new OrderProduct;
            $orderProduct->order_id = $order->_id;
            $orderProduct->product_id = $product->_id;
            $orderProduct->quantity = $product->quantity;
            $orderProduct->save();
        }

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Delete order.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteOrder($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        $errors = [];

        if(!$order) {
            $errors['id'] = 'Invalid ID';
        }
        else if($order->is_pos_synced) {
            $errors['id'] = 'Cannot delete order after it has been accepted';
        }
        else if($order->invoice) {
            $errors['id'] = 'Cannot delete order after invoice has been added';
        }

        if(count($errors) > 0) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => $errors
            ]);
        }

        $order->orderProducts()->delete();
        $order->delete();

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Download order details as excel.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadExcel($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        if(!$order) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        return Excel::download(new OrderDetailsExport($order), 'order.xlsx');
    }

    /**
     * Download order details as pdf.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        if(!$order) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $pdf = PDF::loadView('pdfs.orders.details', [
            'order' => $order
        ]);

        return $pdf->download('order.pdf');
    }

    /**
     * Send email with order details as excel.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function sendEmailExcel($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        if(!$order) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        if(request()->user->email) {
            Mail::to(request()->user->email)
                ->send(new OrderAddedExcel($order));
        }

        if(request()->user->salesOfficer && request()->user->salesOfficer->email) {
            Mail::to(request()->user->salesOfficer->email)
                ->send(new OrderAddedExcel($order));
        }

        if(request()->user->distributor && request()->user->distributor->email) {
            Mail::to(request()->user->distributor->email)
                ->send(new OrderAddedExcel($order));
        }

        // Mail::to(request()->user->email)
        //     // ->bcc('suraj@himshang.com.np')
        //     // ->bcc('sagun@himshang.com.np')
        //     ->send(new OrderAddedExcel($order));

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }*/

    public function sendEmailExcel($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        if(!$order) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $emails = [];

        if(request()->user->email) {
            $emails[] = request()->user->email;
        }

        if(request()->user->salesOfficer && request()->user->salesOfficer->email) {
            $emails[] = request()->user->salesOfficer->email;
        }

        if(request()->user->distributor && request()->user->distributor->email) {
            $emails[] = request()->user->distributor->email;
        }

        if(count($emails) == 0) {
            return response()->json([
                'success' => true,
                'data' => new stdClass,
                'errors' => new stdClass
            ]);
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.patanjaliayurved.org';
            $mail->SMTPAuth = true;
            $mail->Username = 'salespo@feedback.patanjaliayurved.org';
            $mail->Password = 'Qtkp#@573';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('salespo@feedback.patanjaliayurved.org', 'Patanjali');

            foreach($emails as $email) {
                $mail->addAddress($email);
            }

            $data = Excel::raw(new OrderDetailsExport($order), \Maatwebsite\Excel\Excel::XLSX);

            $filename = $order->customer
                        ? strtolower(str_replace(' ', '_', $order->customer->name)) . '_' . $order->customer->sap_code . '.xlsx'
                        : 'order.xlsx';

            $mail->addStringAttachment($data, $filename);

            $mail->isHTML(true);
            $mail->Subject = 'Order Added';

            $body = '<table>';
            $body .= '<tbody>';
            $body .= '<tr>';
            $body .= '<td>Date</td>';
            $body .= '<td>' . $order->created_at . '</td>';
            $body .= '</tr>';
            $body .= '<tr>';
            $body .= '<td>Customer</td>';
            $body .= '<td>' . ($order->customer ? $order->customer->name : '') . '</td>';
            $body .= '</tr>';
            $body .= '<tr>';
            $body .= '<td>DSM</td>';
            $body .= '<td>' . ($order->user ? $order->user->name : '') . '</td>';
            $body .= '</tr>';
            $body .= '</tbody>';
            $body .= '</table>';

            $mail->Body = $body;

            $mail->send();
        }
        catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'email' => 'Failed to send email'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }

    /**
     * Send email with order details as pdf.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*
    public function sendEmailPdf($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        if(!$order) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        if(request()->user->email) {
            Mail::to(request()->user->email)
                ->send(new OrderAddedPdf($order));
        }
        
        if(request()->user->salesOfficer && request()->user->salesOfficer->email) {
            Mail::to(request()->user->salesOfficer->email)
                ->send(new OrderAddedPdf($order));
        }
        
        if(request()->user->distributor && request()->user->distributor->email) {
            Mail::to(request()->user->distributor->email)
                ->send(new OrderAddedPdf($order));
        }

        // Mail::to(request()->user->email)
        //     // ->bcc('suraj@himshang.com.np')
        //     // ->bcc('sagun@himshang.com.np')
        //     ->send(new OrderAddedPdf($order));

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
    */

    public function sendEmailPdf($id)
    {
        $order = Order::where('user_id', request()->user->id)
                        ->find($id);

        if(!$order) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'id' => 'Invalid ID'
                ]
            ]);
        }

        $emails = [];

        if(request()->user->email) {
            $emails[] = request()->user->email;
        }

        if(request()->user->salesOfficer && request()->user->salesOfficer->email) {
            $emails[] = request()->user->salesOfficer->email;
        }

        if(request()->user->distributor && request()->user->distributor->email) {
            $emails[] = request()->user->distributor->email;
        }

        if(count($emails) == 0) {
            return response()->json([
                'success' => true,
                'data' => new stdClass,
                'errors' => new stdClass
            ]);
        }

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.patanjaliayurved.org';
            $mail->SMTPAuth = true;
            $mail->Username = 'salespo@feedback.patanjaliayurved.org';
            $mail->Password = 'Qtkp#@573';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('salespo@feedback.patanjaliayurved.org', 'Patanjali');

            foreach($emails as $email) {
                $mail->addAddress($email);
            }

            $pdf = PDF::loadView('pdfs.orders.details', [
                'order' => $order
            ]);

            $data = $pdf->output();

            $filename = $order->customer
                    ? strtolower(str_replace(' ', '_', $order->customer->name)) . '_' . $order->customer->sap_code . '.pdf'
                    : 'order.pdf';

            $mail->addStringAttachment($data, $filename);

            $mail->isHTML(true);
            $mail->Subject = 'Order Added';

            $body = '<table>';
            $body .= '<tbody>';
            $body .= '<tr>';
            $body .= '<td>Date</td>';
            $body .= '<td>' . $order->created_at . '</td>';
            $body .= '</tr>';
            $body .= '<tr>';
            $body .= '<td>Customer</td>';
            $body .= '<td>' . ($order->customer ? $order->customer->name : '') . '</td>';
            $body .= '</tr>';
            $body .= '<tr>';
            $body .= '<td>DSM</td>';
            $body .= '<td>' . ($order->user ? $order->user->name : '') . '</td>';
            $body .= '</tr>';
            $body .= '</tbody>';
            $body .= '</table>';

            $mail->Body = $body;

            $mail->send();
        }
        catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => new stdClass,
                'errors' => [
                    'email' => 'Failed to send email'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => new stdClass,
            'errors' => new stdClass
        ]);
    }
}
