<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

use App\TaskLog;

class SetDivisionToDsms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsm:set-division';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set division to dsms';

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

        $dsms = User::where('role', 'dsm')
                    ->whereNull('division_id')
                    ->with('verticals')
                    ->get();

        foreach($dsms as $dsm) {
            if(count($dsm->verticals) > 0) {
                $dsm->division_id = $dsm->verticals[0]->division_id;
                $dsm->save();
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
