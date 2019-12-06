<?php

namespace App\Exports;

use App\User;

use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use stdClass;

class DsmsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
	public function headings(): array {
		return [
			'EMP CODE',
            'NAME',
            'GENDER',
            'DATE OF BIRTH',
            'EMAIL',
            'CONTACT NUMBER',
            'ADDRESS',
            'VERTICALS',
            'SO EMP CODE',
            'SO NAME',
            'DB CODE',
            'DB NAME',
            'BEAT CODE',
            'BEAT NAME',
            'BEAT FREQUENCY',
            'DAY OF VISIT'
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    /*public function collection()
    {
        $query = User::where('role', 'dsm')
        			->select('emp_code', 'name', 'gender', 'date_of_birth', 'email', 'contact_number', 'address', 'sales_officer_id', 'distributor_id')
        			->with(['verticals', 'salesOfficer:emp_code,name', 'distributor:sap_code,name']);

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $models = $query->get();

        $dsms = [];

        foreach($models as $model) {
        	$dsm = new stdClass;
        	$dsm->emp_code = $model->emp_code;
        	$dsm->name = $model->name;
        	$dsm->gender = $model->gender;
        	$dsm->date_of_birth = $model->date_of_birth;
        	$dsm->email = $model->email;
        	$dsm->contact_number = $model->contact_number;
        	$dsm->address = $model->address;
            $dsm->verticals = $model->verticals()->pluck('name')->implode(',');
        	$dsm->so_code = $model->salesOfficer ? $model->salesOfficer->emp_code : '';
        	$dsm->so_name = $model->salesOfficer ? $model->salesOfficer->name : '';
        	$dsm->db_code = $model->distributor ? $model->distributor->sap_code : '';
        	$dsm->db_name = $model->distributor ? $model->distributor->name : '';

        	$dsms[] = $dsm;
        }

        return new Collection($dsms);
    }*/

    public function collection()
    {
        $query = User::where('role', 'dsm')
                    ->select('emp_code', 'name', 'gender', 'date_of_birth', 'email', 'contact_number', 'address', 'sales_officer_id', 'distributor_id')
                    ->with(['verticals', 'salesOfficer:emp_code,name', 'distributor:sap_code,name', 'routeUsers.route']);

        if(request()->user()->role == 'sales-officer') {
            $query->where('sales_officer_id', request()->user()->_id);
        }

        $models = $query->get();

        $dsms = [];

        foreach($models as $model) {
            $emp_code = $model->emp_code;
            $name = $model->name;
            $gender = $model->gender;
            $date_of_birth = $model->date_of_birth;
            $email = $model->email;
            $contact_number = $model->contact_number;
            $address = $model->address;
            $so_code = $model->salesOfficer ? $model->salesOfficer->emp_code : '';
            $so_name = $model->salesOfficer ? $model->salesOfficer->name : '';
            $db_code = $model->distributor ? $model->distributor->sap_code : '';
            $db_name = $model->distributor ? $model->distributor->name : '';
            $verticals = $model->verticals()->pluck('name')->implode(',');

            $routeUsers = $model->routeUsers;

            if(count($routeUsers) > 0) {
                foreach($routeUsers as $routeUser) {
                    $dsm = new stdClass;
                    $dsm->emp_code = $emp_code;
                    $dsm->name = $name;
                    $dsm->gender = $gender;
                    $dsm->date_of_birth = $date_of_birth;
                    $dsm->email = $email;
                    $dsm->contact_number = $contact_number;
                    $dsm->address = $address;
                    $dsm->verticals = $verticals;
                    $dsm->so_code = $so_code;
                    $dsm->so_name = $so_name;
                    $dsm->db_code = $db_code;
                    $dsm->db_name = $db_name;
                    $dsm->route_code = $routeUser->route ? $routeUser->route->sap_code : '';
                    $dsm->route_name = $routeUser->route ? $routeUser->route->name : '';
                    $dsm->route_frequency = $routeUser->frequency;
                    $dsm->route_day = $routeUser->day;

                    $dsms[] = $dsm;
                }
            }
            else {
                $dsm = new stdClass;
                $dsm->emp_code = $emp_code;
                $dsm->name = $name;
                $dsm->gender = $gender;
                $dsm->date_of_birth = $date_of_birth;
                $dsm->email = $email;
                $dsm->contact_number = $contact_number;
                $dsm->address = $address;
                $dsm->verticals = $verticals;
                $dsm->so_code = $so_code;
                $dsm->so_name = $so_name;
                $dsm->db_code = $db_code;
                $dsm->db_name = $db_name;
                $dsm->route_code = '';
                $dsm->route_name = '';
                $dsm->route_frequency = '';
                $dsm->route_day = '';

                $dsms[] = $dsm;
            }
        }

        return new Collection($dsms);
    }
}
