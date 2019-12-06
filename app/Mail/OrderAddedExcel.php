<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;

use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;

use App\Order;

use App\Exports\OrderDetailsExport;

use Excel;

class OrderAddedExcel extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * The order instance.
     *
     * @var Order
     */
    public $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = Excel::raw(new OrderDetailsExport($this->order), \Maatwebsite\Excel\Excel::XLSX);

        $customer = $this->order->customer;

        $filename = $customer
                    ? strtolower(str_replace(' ', '_', $customer->name)) . '_' . $customer->sap_code . '.xlsx'
                    : 'order.xlsx';

        return $this->view('emails.orders.added')
                    ->attachData($data, $filename);
    }
}
