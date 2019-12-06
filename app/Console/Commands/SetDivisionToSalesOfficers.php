<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

use App\TaskLog;

class SetDivisionToSalesOfficers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales-officer:set-division';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set division to sales officers';

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
    /*public function handle()
    {
        ini_set('memory_limit', '8192M');

        $users = User::where('role', 'sales-officer')
                    ->whereNull('division_id')
                    ->with('verticals')
                    ->get();

        foreach($users as $user) {
            if(count($user->verticals) > 0) {
                $user->division_id = $user->verticals[0]->division_id;
                $user->save();
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }*/

    /*public function handle()
    {
        ini_set('memory_limit', '8192M');

        $salesOfficers = User::where('role', 'sales-officer')
                    ->whereNull('division_id')
                    ->with('dsms')
                    ->get();

        foreach($salesOfficers as $salesOfficer) {
            if(count($salesOfficer->dsms) > 0) {
                $salesOfficer->division_id = $salesOfficer->dsms[0]->division_id;
                $salesOfficer->save();
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }*/

    public function handle()
    {
        ini_set('memory_limit', '8192M');

        $salesOfficers = User::where('role', 'sales-officer')
                    ->whereNull('division_id')
                    ->with('routes')
                    ->get();

        foreach($salesOfficers as $salesOfficer) {
            if(count($salesOfficer->routes) > 0) {
                $salesOfficer->division_id = $salesOfficer->routes[0]->division_id;
                $salesOfficer->save();
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
