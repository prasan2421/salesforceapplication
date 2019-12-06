<?php

namespace App\Exports;

use App\Route;

use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use stdClass;

class RoutesExport implements FromCollection, WithHeadings, ShouldAutoSize
{
	public function headings(): array {
		return [
			'BEAT CODE',
            'BEAT NAME'
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $models = Route::select('sap_code', 'name')->get();

        $routes = [];

        foreach($models as $model) {
        	$route = new stdClass;
        	$route->sap_code = $model->sap_code;
        	$route->name = $model->name;

        	$routes[] = $route;
        }

        return new Collection($routes);
    }
}
