<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Route;

use App\RouteUser;

use App\TaskLog;

class SetDivisionToRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:set-division';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set division to routes';

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

        $query = Route::whereNull('division_id');

        while($query->count() > 0) {
            $routes = $query->take(10000)->get();

            foreach($routes as $route) {
                $routeUser = RouteUser::where('route_id', $route->id)
                                ->with('user.verticals')
                                ->first();

                if($routeUser && $routeUser->user && count($routeUser->user->verticals) > 0) {
                    $route->division_id = $routeUser->user->verticals[0]->division_id;
                    $route->save();
                }
            }
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
