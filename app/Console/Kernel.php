<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();


        $schedule->command('pos:notify-new-orders')
        ->hourly()
        ->timezone(config('app.timezone'))
        ->runInBackground()
        // ->withoutOverlapping()
        ->appendOutputTo(storage_path('app/cron-log.txt'));


        $schedule->command('dsm:punch-out')
        ->dailyAt('20:00')
        ->timezone(config('app.timezone'))
        ->runInBackground()
        ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('remove:customers')
        // ->dailyAt('17:33')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('remove:route-users')
        // ->dailyAt('17:34')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('remove:routes')
        // ->dailyAt('17:58')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('remove:dsms')
        // ->dailyAt('18:08')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('import:customers')
        // ->dailyAt('21:19')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('route:set-division')
        // ->dailyAt('13:39')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('route:set-state')
        // ->dailyAt('14:48')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('route:set-sap-code')
        // ->dailyAt('22:35')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('customer:set-sap-code')
        // ->dailyAt('18:51')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('distributor:merge-duplicates')
        // ->dailyAt('18:22')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('customer:set-division')
        // ->dailyAt('18:24')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));


        // $schedule->command('customer:set-state')
        // ->dailyAt('18:24')
        // ->timezone(config('app.timezone'))
        // ->runInBackground()
        // ->appendOutputTo(storage_path('app/cron-log.txt'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
