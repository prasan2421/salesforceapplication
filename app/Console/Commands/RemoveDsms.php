<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\TaskLog;

use App\User;

use App\Imports\RemoveRoutesImport;

use Excel;

class RemoveDsms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:dsms';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove DSMs from excel files';

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

        // for($i = 1; $i <= 3; $i++) {
            // $filename = 'dsms-to-remove-2_chunk' . $i . '.csv';
            $filename = 'dUPLICATE-dsm.xlsx';
            $array = Excel::toArray(new RemoveRoutesImport, storage_path('app/xlsx/' . $filename));
            if(count($array) > 0) {
                $rows = $array[0];
                foreach($rows as $row) {
                    // $dsm = User::find($row['dsm_id']);
                    $dsm = User::where('emp_code', '' . $row['emp_code'])->first();

                    if($dsm) {
                        $dsm->verticals()->sync([]);
                        $dsm->routeUsers()->delete();
                        $dsm->delete();
                    }
                }
            }

            $taskLog = new TaskLog;
            $taskLog->command = $this->signature;
            $taskLog->filename = $filename;
            $taskLog->save();
        // }
    }*/

    public function handle()
    {
        ini_set('memory_limit', '8192M');

        $empCodes = ['1800', '18005230', '180050248', '180050352', '180050411', '180050417', '180050424', '180050428', '180050429', '180050448', '180050547', '180050893', '180050911', '180050964', '180050980', '180051256', '180051289', '180051418', '180051422', '180051495', '180051846', '180052571', '180052659', '180052660', '180052661', '180052662', '180052663', '180052667', '180052673', '180052675', '180052706', '180052716', '180052719', '180052743', '180052987', '180053064', '180053102', '180053106', '180053156', '180053158', '180053166', '180053461', '180053527', '180053689', '180053726', '180053823', '180053824', '180053826', '180053829', '180053830', '180053834', '180053840', '180053841', '180053842', '180053863', '180053916', '180053938', '180053993', '180054094', '180054168', '180054202', '180054206', '180054228', '180054243', '180054331', '180054356', '180054372', '180054793', '180054805', '180054807', '180055041', '180055049', '180055084', '180055092', '180059498', '180061148', '180061181', '180061268', '180061296', '180061355', '180061377', '180061387', '180061388', '180061411', '180061440', '180061514', '180061927', '180061961', '180061966', '180061981', '180061990', '180061994', '180062048', '180062063', '180062071', '180062076', '180062114', '180062120', '180062122', '180062125', '180062133', '180062223', '180062262', '180062265', '180062333', '180062344', '180062407', '180062421', '180062463', '180062577', '180062752', '180062787', '180062822', '180063065', '180063079', '180063161', '180063213', '180063341', '180063350', '180063351', '180063380', '180063384', '180063388', '180063401', '180063405', '180063515', '180063543', '180063586', '180063624', '180063627', '180063695', '180063707', '180063779', '180063792', '180063800', '180063971', '180063973', '180063982', '180063983', '180064067', '180064123', '180064131', '180064166', '180064316', '180064328', '180064329', '180064357', '180064496', '180064497', '180064536', '180064538', '180064608', '180064652', '180064768', '180064770', '180064782', '180064799', '180064813', '180064836', '180064842', '180064919', '180064941', '180066047', '180066128', '180066231', '180066246', '180066249', '180066254', '180066463', '180066482', '180066483', '180066540', '180066594', '180066598', '180066717', '180066820', '180066821', '180066860', '180066862', '180066911', '180066912', '180069111', '180069114', '180069117', '180069208', '180069266', '180069279', '180069293', '180069315', '180069336', '180069337', '180069354', '180069363', '180069396', '180069411', '180069412', '180069422', '180069434', '180069438', '180069498', '180069588', '180069591', '180069601', '180069607', '180069975', '180070017', '180070039', '180070049', '180070114', '180070115', '180070125', '180070132', '180070188', '180070227', '180070273', '180070296', '180070341', '180070375', '180070393', '180070403', '180070405', '180070412', '180070432', '180070444', '180070448', '180070474', '180070532', '180070555', '180070577', '180070590', '180070598', '180070600', '180070601', '180070619', '180070630', '180070665', '180070699', '180070711', '180070719', '180070773', '180070777', '180070778', '180070779', '180070780', '180070781', '945620101', '987321489', '6111764574', '1', '2'];

        $dsms = User::whereIn('emp_code', $empCodes)->get();

        foreach($dsms as $dsm) {
            $dsm->verticals()->sync([]);
            $dsm->routeUsers()->delete();
            $dsm->delete();
        }

        $taskLog = new TaskLog;
        $taskLog->command = $this->signature;
        $taskLog->filename = null;
        $taskLog->save();
    }
}
