<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Product;

class UpdateProductPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:update-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update prices of products';

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
        
        $products = Product::select('sap_code')
                            ->get();

        $client = new \GuzzleHttp\Client();

        foreach($products as $product) {
            $res = $client->request('GET', 'http://14.192.18.81/publish/api/getSingleItemPrice?mcode=' . $product->sap_code);

            $response = json_decode($res->getBody());

            if($response->status == 'ok') {
                $product->mrp = $response->result->mrp;
                $product->gst = $response->result->gst;
                $product->superdistributorlandingprice = round($response->result->superdistributorlandingprice, 2);
                $product->superdistributorsellingprice = round($response->result->superdistributorsellingprice, 2);
                $product->distributorsellingprice = round($response->result->distributorsellingprice, 2);
                $product->retailersellingprice = round($response->result->retailersellingprice, 2);
                $product->save();
            }
        }
    }
}
