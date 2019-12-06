<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Customer;

use App\Route;

use App\TaskLog;

use App\Imports\RemoveRoutesImport;

use Excel;

class RemoveCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:customers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove customers associated with routes from excel files';

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

        // for($i = 1; $i <= 240; $i++) {
            // $filename = 'unassigned-beats_chunk' . $i . '.csv';
            $filename = 'routes-to-remove-6.csv';
            $array = Excel::toArray(new RemoveRoutesImport, storage_path('app/csv/' . $filename));
            if(count($array) > 0) {
                $rows = $array[0];
                foreach($rows as $row) {
                    $route = Route::where('sap_code', '' . $row['beat_code'])->first();

                    if($route) {
                        $route->customers()->delete();
                    }
                }
            }

            $taskLog = new TaskLog;
            $taskLog->command = $this->signature;
            $taskLog->filename = $filename;
            $taskLog->save();
        // }
    }*/

    /*public function handle()
    {
        ini_set('memory_limit', '8192M');

        $retailerCodes = [];

        Customer::whereIn('sap_code', $retailerCodes)->delete();

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }*/

    public function handle()
    {
        ini_set('memory_limit', '8192M');

        $routeIds = ['5d95c3e48788728ef4405add', '5d95c41a87887289ac14c763', '5d95c44b87887289cb3178b5', '5d95c49687887293dd745e90', '5d95c55487887285cb34ea8e', '5d95c59f8788729441142e50', '5d37e519878872299d2d7762', '5d394b0887887214e80e7c03', '5d35673e8788723aec2b0cd2', '5d32e05b878872493e0da0b5', '5d32e630878872507f55a183', '5d32e7da878872525d7d2c25', '5d32e9bc87887256033c7492', '5d32f4a087887264684d2ae3', '5d32f55887887265605dd772', '5d359f318788727ff71a9922', '5d2dcd008788726741716233', '5d2dcd1487887267997d0902', '5d2dcd2b87887266d85e3796', '5d341aae8788723c5e616e45', '5d341aae8788723c5e616e47', '5d341aae8788723c5e616e48', '5d341aae8788723c5e616e49', '5d341aae8788723c5e616e43', '5d341aae8788723c5e616e3f', '5d341aad8788723c5e616d33', '5d341aae8788723c5e616e40', '5d341aae8788723c5e616e3e', '5d341aae8788723c5e616f01', '5d341aae8788723c5e616e53', '5d341aae8788723c5e616e54', '5d341aae8788723c5e616e55', '5d341aae8788723c5e616e56', '5d341aae8788723c5e616e57', '5d341aae8788723c5e616e58', '5d341aae8788723c5e616e5f', '5d341aae8788723c5e616e60', '5d341aae8788723c5e616e61', '5d341aae8788723c5e616e62', '5d341aae8788723c5e616e63', '5d341aae8788723c5e616e59', '5d341aae8788723c5e616e5a', '5d341aae8788723c5e616e5b', '5d341aae8788723c5e616e5c', '5d341aae8788723c5e616e5d', '5d341aae8788723c5e616e5e', '5d341aae8788723c5e616e65', '5d341aae8788723c5e616e75', '5d341aae8788723c5e616ea7', '5d341aae8788723c5e616ea9', '5d341aae8788723c5e616eaa', '5d341aae8788723c5e616eab', '5d341aae8788723c5e616e2e', '5d341aae8788723c5e616eac', '5d341aae8788723c5e616eb0', '5d341aae8788723c5e616eb1', '5d341aae8788723c5e616e7c', '5d341aae8788723c5e616eaf', '5d341aae8788723c5e616eae', '5d341aae8788723c5e616e94', '5d341aae8788723c5e616e99', '5d341aae8788723c5e616e89', '5d341aae8788723c5e616e81', '5d341aae8788723c5e616e9c', '5d341aae8788723c5e616e9f', '5d341aae8788723c5e616e9d', '5d341aae8788723c5e616e9e', '5d341aae8788723c5e616ea0', '5d341aae8788723c5e616e50', '5d341aae8788723c5e616ec0', '5d341aae8788723c5e616e46', '5d341aae8788723c5e616ec5', '5d341aae8788723c5e616ec8', '5d341aae8788723c5e616ecb', '5d341aaa8788723c5e6166e3', '5d341aae8788723c5e616eda', '5d341aac8788723c5e616a36', '5d341aae8788723c5e616ef9', '5d341aae8788723c5e616f27', '5d341aae8788723c5e616efa', '5d341aae8788723c5e616efd', '5d341aae8788723c5e616efb', '5d341aae8788723c5e616f6d', '5d341aae8788723c5e616f6b', '5d341aae8788723c5e616f6e', '5d341aae8788723c5e616f77', '5d341aae8788723c5e616f86', '5d341aae8788723c5e616f8d', '5d341aae8788723c5e616fa1', '5d341aae8788723c5e616fa8', '5d341aae8788723c5e616fb3', '5d341aae8788723c5e616f9c', '5d341aae8788723c5e616f9d', '5d341aae8788723c5e616fa9', '5d341aae8788723c5e616fa2', '5d341aae8788723c5e616f9f', '5d341aae8788723c5e616fb4', '5d341aae8788723c5e616fab', '5d341aae8788723c5e616fad', '5d341aae8788723c5e616fac', '5d341aae8788723c5e616faf', '5d341aae8788723c5e616fb7', '5d341aae8788723c5e616fb5', '5d341aae8788723c5e616fb2', '5d341aae8788723c5e616fa7', '5d341aae8788723c5e616fae', '5d341aae8788723c5e616fb6', '5d341aae8788723c5e616f9e', '5d341aae8788723c5e616fa5', '5d341aae8788723c5e616fa6', '5d341aab8788723c5e616753', '5d341aac8788723c5e616b01', '5d45272c8788720fca2b99f3', '5d452a5f8788721562699e62', '5d452755878872103d5158f2', '5d452a4b87887214894281ed', '5d452715878872100a49afd2', '5d761a9c878872631054fbbc', '5d761a9c878872631054fbbd', '5d761a9c878872631054fbbe', '5d341aab8788723c5e61675b', '5d341aab8788723c5e616759', '5d341aab8788723c5e61676b', '5d341aab8788723c5e61676c', '5d341aab8788723c5e61676d', '5d341aab8788723c5e61676e', '5d341aab8788723c5e616766', '5d341aab8788723c5e616767', '5d341aab8788723c5e616768', '5d341ab08788723c5e61728a', '5d341ab08788723c5e61728b', '5d341ab08788723c5e61728c', '5d341ab08788723c5e61726f', '5d341ab08788723c5e6172af', '5d341aaf8788723c5e6171e3', '5d3430bc87887243d032aa05', '5d3430bc87887243d032aa06', '5d3430bc87887243d032aa07', '5d3430bc87887243d032a9ef', '5d3430bc87887243d032aa09', '5d3430bc87887243d032aa0a', '5d3430bc87887243d032a9db', '5d3430bc87887243d032a9dc', '5d3430bc87887243d032a9dd', '5d3430bc87887243d032a9de', '5d3430bc87887243d032a9df', '5d3430bc87887243d032a9e0', '5d3430bc87887243d032a9d5', '5d3430bc87887243d032a9d6', '5d3430bc87887243d032a9d7', '5d3430bc87887243d032a9d8', '5d3430bc87887243d032a9d9', '5d3430bc87887243d032a9da', '5d3430bc87887243d032a9ed', '5d3430bc87887243d032a9ee', '5d3430bc87887243d032a9f0', '5d3430bc87887243d032a9f1', '5d3430bc87887243d032a9f2', '5d3430bc87887243d032a988', '5d3430bc87887243d032a989', '5d3430bc87887243d032a98a', '5d3430bc87887243d032a98b', '5d3430bc87887243d032a98c', '5d3430bc87887243d032a98d', '5d3430bc87887243d032aa0b', '5d3430bc87887243d032aa0c', '5d3430bc87887243d032aa0d', '5d3430bc87887243d032aa0e', '5d3430bc87887243d032aa0f', '5d3430bc87887243d032aa10', '5d3430bc87887243d032a9e7', '5d3430bc87887243d032a9e8', '5d3430bc87887243d032a9e9', '5d3430bc87887243d032a9ea', '5d3430bc87887243d032a9eb', '5d3430bc87887243d032a9ec', '5d3430bc87887243d032a99f', '5d3430bc87887243d032a9a0', '5d3430bc87887243d032a9a1', '5d3430bc87887243d032a9a2', '5d3430bc87887243d032a9a3', '5d3430bc87887243d032a9a4', '5d3430bc87887243d032a999', '5d3430bc87887243d032a99a', '5d3430bc87887243d032a99b', '5d3430bc87887243d032a99c', '5d3430bc87887243d032a99d', '5d3430bc87887243d032a99e', '5d3430bc87887243d032a9f9', '5d3430bc87887243d032a9fa', '5d3430bc87887243d032a9fb', '5d3430bc87887243d032a9fc', '5d3430bc87887243d032a9fd', '5d3430bc87887243d032a9fe', '5d3430bc87887243d032a9ff', '5d3430bc87887243d032aa00', '5d3430bc87887243d032aa01', '5d3430bc87887243d032aa02', '5d3430bc87887243d032aa03', '5d3430bc87887243d032aa04', '5d3430bc87887243d032a993', '5d3430bc87887243d032a994', '5d3430bc87887243d032a995', '5d3430bc87887243d032a996', '5d3430bc87887243d032a997', '5d3430bc87887243d032a998', '5d3430bc87887243d032a9d2', '5d3430bc87887243d032a9d4', '5d3430bc87887243d032a9d0', '5d3430bc87887243d032a9d3', '5d3430bc87887243d032a9cf', '5d3430bc87887243d032a9d1', '5d3430bc87887243d032aaac', '5d3430bc87887243d032aaad', '5d3430bc87887243d032aaae', '5d3430bc87887243d032aaaf', '5d3430bc87887243d032aab0', '5d3430bc87887243d032aab1', '5d4a6e1a87887203ee4dc132', '5d4a6e28878872040d4955b2', '5d4a6e3287887203b6636863', '5d4a6e3b878872027751d82a', '5d4a6e468788720331688b98', '5d4a6e518788720331688b99', '5d3430bd87887243d032aba8', '5d3430bd87887243d032aba9', '5d3430bd87887243d032abaa', '5d3430bd87887243d032abab', '5d3430bd87887243d032abac', '5d3430bd87887243d032abad', '5d3430bd87887243d032abf1', '5d3430bd87887243d032ac0e', '5d3430bd87887243d032ac0c', '5d3430bd87887243d032ac09', '5d3430bd87887243d032abf8', '5d3430bd87887243d032ac13', '5d3430bc87887243d032aa4c', '5d3430bd87887243d032ac06', '5d3430bd87887243d032ac01', '5d3430bd87887243d032abf7', '5d3430bd87887243d032ac0b', '5d3430bd87887243d032abf6', '5d36e2d2878872126723d584', '5d6ba554878872662d6834ce', '5d6ba554878872662d6834d0', '5d6ba554878872662d6834d4', '5d6ba554878872662d6834d5', '5d6ba554878872662d6834d6', '5d6ba554878872662d6834d7', '5d6ba554878872662d6834d8', '5d6ba554878872662d6834d9', '5d6ba554878872662d6834da', '5d6ba554878872662d6834db', '5d6ba554878872662d6834dd', '5d6ba554878872662d6834de', '5d36e2d2878872126723d5bc', '5d36e2d2878872126723d5c3', '5d36e2d2878872126723d5fd', '5d6ba554878872662d683497', '5d6ba554878872662d683498', '5d6ba554878872662d683499', '5d6ba554878872662d68349a', '5d6ba554878872662d68349c', '5d6ba554878872662d68349d', '5d36e2d2878872126723d674', '5d36e2d2878872126723d675', '5d36e2d2878872126723d676', '5d36e2d2878872126723d677', '5d36e2d2878872126723d678', '5d36e2d2878872126723d680', '5d36e2d2878872126723d681', '5d36e2d2878872126723d682', '5d36e2d2878872126723d683', '5d36e2d2878872126723d684', '5d36e2d2878872126723d685', '5d36e2d2878872126723d68c', '5d36e2d2878872126723d68d', '5d36e2d2878872126723d68e', '5d36e2d2878872126723d68f', '5d36e2d2878872126723d690', '5d36e2d2878872126723d691', '5d36e2d2878872126723d6bd', '5d36e2d2878872126723d6be', '5d36e2d2878872126723d6bf', '5d36e2d2878872126723d6c0', '5d36e2d2878872126723d6c1', '5d36e2d2878872126723d6c2', '5d36e2d2878872126723d6c3', '5d36e2d2878872126723d6c4', '5d36e2d2878872126723d6c5', '5d36e2d2878872126723d6c6', '5d36e2d2878872126723d6c7', '5d36e2d2878872126723d6fb', '5d36e2d2878872126723d6fc', '5d36e2d2878872126723d6fd', '5d36e2d2878872126723d6fe', '5d36e2d2878872126723d6ff', '5d36e2d2878872126723d700', '5d36e2d2878872126723d72c', '5d36e2d2878872126723d72b', '5d36e2d2878872126723d703', '5d36e2d2878872126723d704', '5d36e2d2878872126723d705', '5d36e2d2878872126723d706', '5d36e2d2878872126723d707', '5d36e2d2878872126723d708', '5d36e2d2878872126723d709', '5d36e2d2878872126723d70a', '5d36e2d2878872126723d70b', '5d36e2d2878872126723d70c', '5d36e2d2878872126723d6c8', '5d36e2d2878872126723d745', '5d36e2d2878872126723d746', '5d36e2d2878872126723d748', '5d36e2d2878872126723d749', '5d36e2d2878872126723d74a', '5d36e2d3878872126723d772', '5d36e2d3878872126723d773', '5d36e2d3878872126723d774', '5d36e2d3878872126723d775', '5d36e2d3878872126723d776', '5d36e2d3878872126723d77f', '5d36e2d3878872126723d780', '5d36e2d3878872126723d782', '5d36e2d3878872126723d783', '5d36e2d3878872126723d78b', '5d36e2d3878872126723d78d', '5d36e2d3878872126723d78e', '5d36e2d3878872126723d78f', '5d36e2d3878872126723d790', '5d36e2d3878872126723d791', '5d36e2d3878872126723d792', '5d36e2d3878872126723d793', '5d36e2d2878872126723d606', '5d36e2d3878872126723d79b', '5d36e2d2878872126723d6f5', '5d36e2d3878872126723d79d', '5d36e2d3878872126723d79e', '5d36e2d3878872126723d79f', '5d36e2d3878872126723d7a0', '5d36e2d3878872126723d7a1', '5d36e2d2878872126723d750', '5d36e2d3878872126723d7a4', '5d36e2d3878872126723d7a5', '5d36e2d3878872126723d7b9', '5d36e2d3878872126723d7ba', '5d36e2d3878872126723d7bb', '5d36e2d3878872126723d7bc', '5d36e2d3878872126723d7bd', '5d36e2d3878872126723d7be', '5d36e2d3878872126723d7bf', '5d36e2d3878872126723d7c0', '5d36e2d3878872126723d7c1', '5d36e2d3878872126723d7c2', '5d36e2d3878872126723d7c3', '5d36e2d3878872126723d7fa', '5d36e2d3878872126723d7fb', '5d36e2d3878872126723d7fc', '5d36e2d3878872126723d7fe', '5d36e2d3878872126723d7ff', '5d36e2d3878872126723d800', '5d36e2d3878872126723d825', '5d36e2d3878872126723d826', '5d36e2d3878872126723d827', '5d36e2d3878872126723d828', '5d36e2d3878872126723d829', '5d36e2d3878872126723d82a', '5d36e2d3878872126723d82b', '5d36e2d2878872126723d722', '5d36e2d3878872126723d82d', '5d36e2d3878872126723d82e', '5d36e2d3878872126723d82f', '5d36e2d3878872126723d830', '5d36e2d3878872126723d850', '5d36e2d3878872126723d807', '5d36e2d3878872126723d852', '5d36e2d3878872126723d853', '5d36e2d3878872126723d854', '5d36e2d3878872126723d855', '5d36e2d3878872126723d856', '5d36e2d3878872126723d857', '5d36e2d3878872126723d858', '5d36e2d3878872126723d859', '5d36e2d3878872126723d8d1', '5d36e2d3878872126723d8d2', '5d36e2d3878872126723d8d3', '5d36e2d3878872126723d8d4', '5d36e2d3878872126723d8d5', '5d36e2d3878872126723d8d6', '5d36e2d3878872126723d8d7', '5d36e2d3878872126723d8d8', '5d36e2d3878872126723d8d9', '5d36e2d3878872126723d8da', '5d36e2d3878872126723d8db', '5d36e2d3878872126723d912', '5d36e2d3878872126723d948', '5d36e2d3878872126723d949', '5d36e2d3878872126723d94a', '5d36e2d3878872126723d94b', '5d36e2d3878872126723d94c', '5d36e2d3878872126723d94d', '5d36e2d3878872126723d914', '5d36e2d3878872126723d95b', '5d36e2d3878872126723d95d', '5d36e2d3878872126723d95e', '5d36e2d3878872126723d95f', '5d36e2d3878872126723d961', '5d36e2d3878872126723d96e', '5d36e2d3878872126723d94e', '5d36e2d3878872126723d865', '5d36e2d3878872126723d971', '5d36e2d3878872126723d972', '5d36e2d3878872126723d973', '5d36e2d2878872126723d617', '5d38014d87887256460da480', '5d38014d87887256460da49e', '5d4bac7d878872a369416e02', '5d4bace1878872a3281556d3', '5d3b2d0d8788722e566d4862', '5d3d4e1487887295cb7bd6e3', '5d3d4e1487887295cb7bd6e4', '5d3d4e1487887295cb7bd6e5', '5d3d4e1487887295cb7bd6e6', '5d3d4e1487887295cb7bd6e7', '5d3d4e1487887295cb7bd6ee', '5d3d4e1487887295cb7bd6ef', '5d3d4e1487887295cb7bd6f0', '5d3d4e1487887295cb7bd6f1', '5d3d4e1487887295cb7bd6f2', '5d3d4e1487887295cb7bd6f3', '5d3d4e1487887295cb7bd6b3', '5d3d4e1487887295cb7bd70d', '5d3d4e1487887295cb7bd70e', '5d3d4e1487887295cb7bd70f', '5d3d4e1487887295cb7bd710', '5d3d4e1487887295cb7bd711', '5d3d4e1487887295cb7bd71e', '5d3d4e1487887295cb7bd71f', '5d3d4e1487887295cb7bd720', '5d3d4e1487887295cb7bd721', '5d3d4e1487887295cb7bd723', '5d3d4e1487887295cb7bd724', '5d3d4e1487887295cb7bd725', '5d3d4e1487887295cb7bd726', '5d3d4e1487887295cb7bd727', '5d3d4e1487887295cb7bd729', '5d3d4e1487887295cb7bd754', '5d3d4e1487887295cb7bd755', '5d3d4e1487887295cb7bd756', '5d3d4e1487887295cb7bd757', '5d3d4e1487887295cb7bd758', '5d3d4e1487887295cb7bd759', '5d396f8687887253db749934', '5d3d4e1487887295cb7bd761', '5d3d4e1487887295cb7bd762', '5d3d4e1487887295cb7bd763', '5d3d4e1487887295cb7bd6dd', '5d3d4e1487887295cb7bd765', '5d3d4e1487887295cb7bd78a', '5d3d4e1487887295cb7bd78b', '5d3d4e1487887295cb7bd78c', '5d3d4e1487887295cb7bd78d', '5d3d4e1487887295cb7bd78e', '5d3d4e1487887295cb7bd78f', '5d3d4e1487887295cb7bd7a8', '5d3d4e1487887295cb7bd7a9', '5d3d4e1487887295cb7bd7aa', '5d3d4e1487887295cb7bd7ab', '5d3d4e1487887295cb7bd7ac', '5d3d4e1487887295cb7bd7ad', '5d43da298788723ff010ba9c', '5d43da298788723ff010ba9d', '5d43da298788723ff010ba9e', '5d43da298788723ff010ba9f', '5d43da298788723ff010baa0', '5d43da298788723ff010baa1', '5d43da298788723ff010baa2', '5d43da298788723ff010baa3', '5d43da298788723ff010baa4', '5d43da298788723ff010baa5', '5d43da298788723ff010baa6', '5d43da298788723ff010baa7', '5d43da298788723ff010baf5', '5d43da298788723ff010baf6', '5d43da298788723ff010baf7', '5d43da298788723ff010baf8', '5d43da298788723ff010baf9', '5d43da298788723ff010bafa', '5d43da298788723ff010bafb', '5d43da298788723ff010bafc', '5d43da298788723ff010bafd', '5d43da298788723ff010bafe', '5d43da298788723ff010baff', '5d43da298788723ff010bb00', '5d43da298788723ff010bb01', '5d43da298788723ff010bb02', '5d43da298788723ff010bb03', '5d43da298788723ff010bb04', '5d43da298788723ff010bb05', '5d43da298788723ff010bb06', '5d43da298788723ff010bb07', '5d43da298788723ff010bb08', '5d43da298788723ff010bb09', '5d43da298788723ff010bb0a', '5d43da298788723ff010bb0b', '5d43da298788723ff010bb0c', '5d43da298788723ff010bb25', '5d43da298788723ff010bb26', '5d43da298788723ff010bb27', '5d43da298788723ff010bb28', '5d43da298788723ff010bb29', '5d43da298788723ff010bb2a', '5d43da298788723ff010bb2b', '5d43da298788723ff010bb2c', '5d43da298788723ff010bb2d', '5d43da298788723ff010bb2e', '5d43da298788723ff010bb2f', '5d43da298788723ff010bb30', '5d43da298788723ff010bb60', '5d43da298788723ff010bb61', '5d43da298788723ff010bb62', '5d43da298788723ff010bb63', '5d43da298788723ff010bb64', '5d43da298788723ff010bb65', '5d43da298788723ff010bb82', '5d43da298788723ff010bb83', '5d43da298788723ff010bb84', '5d43da298788723ff010bb85', '5d43da298788723ff010bb86', '5d43da298788723ff010bb87', '5d43da2a8788723ff010bbb0', '5d43da2a8788723ff010bbb1', '5d43da2a8788723ff010bbb2', '5d43da2a8788723ff010bbb3', '5d43da2a8788723ff010bbb4', '5d43da2a8788723ff010bbb5', '5d43da2a8788723ff010bbb6', '5d43da2a8788723ff010bbb7', '5d43da2a8788723ff010bbb8', '5d43da2a8788723ff010bbb9', '5d43da2a8788723ff010bbba', '5d43da2a8788723ff010bbbb', '5d88775d8788727c45457707', '5d89b5558788727d1d266e76', '5d89b61a87887277dd4f1dac', '5d89b6348788727d6653fe4f', '5d89b64f8788727bce5ce6e4', '5d89b668878872733b0647ac', '5d761aa9878872631054fbe2', '5d761aaa878872631054fbe3', '5d761aaa878872631054fbe4', '5d761aaa878872631054fbe5', '5d7c8c05878872484b1068f3', '5d7cb67c87887207681c820b'];

        Customer::whereIn('route_id', $routeIds)->delete();

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
