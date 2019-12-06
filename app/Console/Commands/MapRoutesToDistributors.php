<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Distributor;

use App\RouteUser;

use App\TaskLog;

class MapRoutesToDistributors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'distributor:map-routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Map routes to distributors';

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

        $distributors = Distributor::get();

        foreach($distributors as $distributor) {
            $userIds = $distributor->users()->pluck('_id');
            $routeIds = RouteUser::whereIn('user_id', $userIds)->pluck('route_id')->toArray();

            if(count($routeIds) > 0) {
                $distributor->routes()->attach($routeIds);
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
