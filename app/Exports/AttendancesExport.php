<?php

namespace App\Exports;

use App\Attendance;

use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use stdClass;

class AttendancesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
	public function headings(): array {
		return [
			'DATE',
			'DSM CODE',
            'DSM NAME'
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $models = Attendance::orderBy('punch_in_time')
        			->select('punch_in_time', 'user_id')
		        	->with('user:emp_code,name')
		        	->get();

		$users = [];

        foreach($models as $model) {
        	if($model->user) {
	        	$date = date('Y-m-d', strtotime($model->punch_in_time));

	        	$users[$date][$model->user->emp_code] = $model->user->name;
	        }
        }

        $attendances = [];

        foreach($users as $key=>$value) {
        	foreach($value as $key1=>$value1) {
        		$attendance = new stdClass;
        		$attendance->date = $key;
        		$attendance->dsm_code = $key1;
        		$attendance->dsm_name = $value1;

        		$attendances[] = $attendance;
        	}
        }

        return new Collection($attendances);
    }
}
