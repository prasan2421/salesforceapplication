<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Counter;

use App\Customer;

use App\Division;

use App\Route;

use App\State;

use App\TaskLog;

class SetSapCodeToCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:set-sap-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set SAP code to customers';

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
        // ini_set('memory_limit', '8192M');

        // $divisions = Division::pluck('abbreviation', '_id')->toArray();
        // $states = State::pluck('abbreviation', '_id')->toArray();

        // $query = Customer::whereNull('old_sap_code');

        // while($query->count() > 0) {
        //     $customers = $query->with('route')
        //                     ->take(10000)
        //                     ->get();

        //     foreach($customers as $customer) {
        //         $route = $customer->route;
        //         $division_id = $route->division_id;
        //         $state_id = $route->state_id;

        //         $prefix = $divisions[$division_id] . '_' . $states[$state_id] . '_';

        //         $counter = Counter::where('name', 'retailer-code')
        //                         ->where('prefix', $prefix)
        //                         ->first();

        //         if(!$counter) {
        //             $counter = new Counter;
        //             $counter->name = 'retailer-code';
        //             $counter->prefix = $prefix;
        //             $counter->count = 0;
        //         }

        //         $counter->count = $counter->count + 1;
        //         $counter->save();

        //         $customer->old_sap_code = $customer->sap_code;
        //         $customer->sap_code = $counter->prefix . $counter->count;
        //         $customer->save();
        //     }
        // }

        // $taskLog = new TaskLog;
        // $taskLog->command = $this->signature;
        // $taskLog->filename = null;
        // $taskLog->save();
    }
}
