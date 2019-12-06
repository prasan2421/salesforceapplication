<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Order;

use DateInterval;

use DateTime;

class NotifyNewOrdersToPos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pos:notify-new-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to POS about new orders';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ini_set('memory_limit', '8192M');

        $datetime = (new DateTime)->sub(new DateInterval('PT2H'));

        $client = new \GuzzleHttp\Client();

        Order::where('is_pos_notified', false)
        ->where('created_at', '<', $datetime)
        ->with(['customer.route', 'user.distributor'])
        ->chunk(10000, function($orders) use ($client) {
            foreach($orders as $order) {
                if(!$order->customer || !$order->customer->route || !$order->user || !$order->user->distributor) {
                    $order->is_pos_notified = true;
                    $order->save();

                    continue;
                }

                $orderTo = $order->user && $order->user->distributor ? $order->user->distributor->sap_code : '';
                $orderFrom = $order->customer ? $order->customer->sap_code : '';
                $orderId = $order->id;
                // $orderedUser = $order->user ? $order->user->emp_code : '';
                $orderDate = date('Y-m-d H:i:s', strtotime($order->created_at));
                $dsmCode = $order->user ? $order->user->emp_code : '';
                $dsmName = $order->user ? $order->user->name : '';
                $beat = $order->customer && $order->customer->route ? $order->customer->route->name : '';

                try {
                    $res = $client->request('POST', 'http://14.192.18.81:8010/retailerorder/api/notification', [
                        'json' => [
                            'ORDERFROM' => $orderFrom,
                            'ORDERTO' => $orderTo,
                            'ORDERID' => $orderId,
                            // 'ORDERED_USER' => $orderedUser,
                            'ORDERDATE' => $orderDate,
                            'DSMCODE' => $dsmCode,
                            'DSMNAME' => $dsmName,
                            'BEAT' => $beat
                        ]
                    ]);

                    $response = json_decode($res->getBody());

                    if($response->status == 'ok') {
                        $order->is_pos_notified = true;
                        $order->save();
                    }
                }
                catch(\Exception $e) {

                }
            }
        });
    }
}
