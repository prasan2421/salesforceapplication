<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Customer;

use App\Division;

use App\TaskLog;

class SetDivisionToCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:set-division';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set division to customers';

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

        $divisionModels = Division::select('id', 'abbreviation')->get();
        $divisions = [];

        foreach($divisionModels as $model) {
            $key = strtolower($model->abbreviation);
            $divisions[$key] = $model;
        }

        $query = Customer::whereNull('division_id');

        while($query->count() > 0) {
            $customers = $query->select('sap_code')
                            ->take(10000)
                            ->get();

            foreach($customers as $customer) {
                $arr = explode('_', $customer->sap_code);
                $divisionCode = strtolower($arr[0]);

                $division = $divisions[$divisionCode];

                $customer->division_id = $division->id;
                $customer->save();
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
