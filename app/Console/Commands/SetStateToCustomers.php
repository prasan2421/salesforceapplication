<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Customer;

use App\State;

use App\TaskLog;

class SetStateToCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:set-state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set state to customers';

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

        $stateModels = State::select('id', 'abbreviation')->get();
        $states = [];

        foreach($stateModels as $model) {
            $key = strtolower($model->abbreviation);
            $states[$key] = $model;
        }

        $query = Customer::whereNull('state_id');

        while($query->count() > 0) {
            $customers = $query->select('sap_code')
                            ->take(10000)
                            ->get();

            foreach($customers as $customer) {
                $arr = explode('_', $customer->sap_code);
                $stateCode = strtolower($arr[1]);

                $state = $states[$stateCode];

                $customer->state_id = $state->id;
                $customer->save();
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
