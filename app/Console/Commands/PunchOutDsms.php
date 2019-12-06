<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Attendance;

use App\CustomerVisit;

class PunchOutDsms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dsm:punch-out';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Punch out DSMs';

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

        // Doesn't save dates properly
        // Attendance::whereNull('punch_out_time')
        //             ->update(['punch_out_time' => date('Y-m-d H:i:s')]);
        
        $customerVisits = CustomerVisit::whereNull('check_out_time')->get();
        foreach($customerVisits as $customerVisit) {
            $customerVisit->check_out_time = date('Y-m-d H:i:s');
            $customerVisit->save();
        }
        
        $attendances = Attendance::whereNull('punch_out_time')->get();
        foreach($attendances as $attendance) {
            $attendance->punch_out_time = date('Y-m-d H:i:s');
            $attendance->save();
        }
    }
}
