<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Customer;

use App\Route;

use App\TaskLog;

class SetStateToRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:set-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set state to routes';

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

        Route::whereNull('state_id')
        ->chunk(10000, function($routes){
            foreach($routes as $route) {
                $customer = Customer::where('route_id', $route->id)
                                ->whereNotNull('billing_state_id')
                                ->first();

                if($customer) {
                    $route->state_id = $customer->billing_state_id;
                    $route->save();
                }
            }
        });

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
