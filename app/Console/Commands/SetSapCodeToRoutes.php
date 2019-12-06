<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Counter;

use App\Division;

use App\Route;

use App\State;

use App\TaskLog;

class SetSapCodeToRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:set-sap-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set SAP code to routes';

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

        // $query = Route::whereNull('old_sap_code');

        // while($query->count() > 0) {
        //     $routes = $query->take(10000)
        //                     ->get();

        //     foreach($routes as $route) {
        //         $division_id = $route->division_id;
        //         $state_id = $route->state_id;

        //         $prefix = $divisions[$division_id] . '_' . $states[$state_id] . '_';

        //         $counter = Counter::where('name', 'beat-code')
        //                         ->where('prefix', $prefix)
        //                         ->first();

        //         if(!$counter) {
        //             $counter = new Counter;
        //             $counter->name = 'beat-code';
        //             $counter->prefix = $prefix;
        //             $counter->count = 0;
        //         }

        //         $counter->count = $counter->count + 1;
        //         $counter->save();

        //         $route->old_sap_code = $route->sap_code;
        //         $route->sap_code = $counter->prefix . $counter->count;
        //         $route->save();
        //     }
        // }

        // $taskLog = new TaskLog;
        // $taskLog->command = $this->signature;
        // $taskLog->filename = null;
        // $taskLog->save();
    }
}
