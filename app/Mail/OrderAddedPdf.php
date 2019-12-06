<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;

use Illuminate\Mail\Mailable;

use Illuminate\Queue\SerializesModels;

use Illuminate\Contracts\Queue\ShouldQueue;

use App\Order;

use PDF;

class OrderAddedPdf extends Mailable
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
        $pdf = PDF::loadView('pdfs.orders.details', [
            'order' => $this->order
        ]);

        $data = $pdf->output();

        $customer = $this->order->customer;

        $filename = $customer
                    ? strtolower(str_replace(' ', '_', $customer->name)) . '_' . $customer->sap_code . '.pdf'
                    : 'order.pdf';

        return $this->view('emails.orders.added')
                    ->attachData($data, $filename);
    }
}
