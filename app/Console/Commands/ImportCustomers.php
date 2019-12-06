<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Customer;

use App\CustomerClass;

use App\CustomerType;

use App\CustomerVisit;

use App\Import;

use App\Route;

use App\State;

use App\Imports\CustomersImport;

use Excel;

use Storage;

class ImportCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import customers from excel files';

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

        for($i = 23; $i <= 48; $i++) {
            $filename = 'Book' . $i . '.xlsx';

            if(!Storage::exists('xlsx/' . $filename)) {
                continue;
            }

            $import = new Import;
            $import->type = 'customers_sap_code';
            $import->filename = $filename;
            $import->remarks = 'Cron Task';
            $import->user_id = null;
            $import->status = 'running';
            $import->is_success = false;
            $import->save();

            try {
                $array = Excel::toArray(new CustomersImport, storage_path('app/xlsx/' . $filename));
                if(count($array) > 0) {
                    $rows = $array[0];
                    foreach($rows as $row) {
                        $customerType = null;
                        if($row['type']) {
                            $customerType = CustomerType::whereRaw([
                                'name' => [
                                    '$regex' => '^' . $row['type'] . '$',
                                    '$options' => 'i'
                                ]
                            ])->first();

                            if(!$customerType) {
                                $customerType = new CustomerType;
                                $customerType->name = $row['type'];
                                $customerType->import_id = $import->id;
                                $customerType->save();
                            }
                        }

                        $customerClass = null;
                        if($row['class']) {
                            $customerClass = CustomerClass::whereRaw([
                                'name' => [
                                    '$regex' => '^' . $row['class'] . '$',
                                    '$options' => 'i'
                                ]
                            ])->first();

                            if(!$customerClass) {
                                $customerClass = new CustomerClass;
                                $customerClass->name = $row['class'];
                                $customerClass->import_id = $import->id;
                                $customerClass->save();
                            }
                        }

                        $billingState = State::whereRaw([
                            'name' => [
                                '$regex' => '^' . $row['billing_state'] . '$',
                                '$options' => 'i'
                            ]
                        ])->first();

                        $shippingState = State::whereRaw([
                            'name' => [
                                '$regex' => '^' . $row['shipping_state'] . '$',
                                '$options' => 'i'
                            ]
                        ])->first();

                        $route = Route::where('sap_code', '' . $row['beat_sap_code'])->first();

                        $customer = new Customer;
                        $customer->customer_type_id = $customerType ? $customerType->id : null;
                        $customer->customer_class_id = $customerClass ? $customerClass->id : null;
                        $customer->sap_code = '' . $row['sap_code'];
                        $customer->name = $row['name'];
                        // $customer->class = $row['class'];
                        $customer->gst_number = $row['gst_number'];
                        $customer->town = $row['town'];
                        $customer->longitude = '' . $row['longitude'];
                        $customer->latitude = '' . $row['latitude'];
                        $customer->owner_name = $row['owner_name'];
                        $customer->owner_email = strtolower($row['owner_email']);
                        $customer->owner_contact_number = '' . $row['owner_contact_number'];
                        $customer->billing_state = $row['billing_state'];
                        $customer->billing_state_id = $billingState ? $billingState->id : null;
                        $customer->billing_district = $row['billing_district'];
                        $customer->billing_city = $row['billing_city'];
                        $customer->billing_address = $row['billing_address'];
                        $customer->billing_pincode = '' . $row['billing_pincode'];
                        $customer->shipping_state = $row['shipping_state'];
                        $customer->shipping_state_id = $shippingState ? $shippingState->id : null;
                        $customer->shipping_district = $row['shipping_district'];
                        $customer->shipping_city = $row['shipping_city'];
                        $customer->shipping_address = $row['shipping_address'];
                        $customer->shipping_pincode = '' . $row['shipping_pincode'];
                        $customer->route_id = $route ? $route->id : null;
                        $customer->import_id = $import->id;
                        $customer->save();
                    }
                }

                $import->status = 'success';
                $import->is_success = true;
                $import->save();
            }
            catch(\Exception $e) {
                $import->status = 'error';
                $import->is_success = false;
                $import->error_code = $e->getCode();
                $import->error_message = $e->getMessage();
                $import->error_trace = $e->getTraceAsString();
                $import->save();
            }
        }
    }
}
