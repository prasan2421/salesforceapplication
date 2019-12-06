<?php

namespace App\Exports;

use App\Customer;

use Illuminate\Support\Collection;

use Maatwebsite\Excel\Concerns\FromCollection;

use Maatwebsite\Excel\Concerns\WithHeadings;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use stdClass;

class CustomersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
	public function headings(): array {
		return [
			'SAP CODE',
            'NAME',
            'CLASS',
            'TYPE',
            'GST NUMBER',
            'TOWN',
            'LONGITUDE',
            'LATITUDE',
            'OWNER NAME',
            'OWNER EMAIL',
            'OWNER CONTACT NUMBER',
            'BILLING STATE',
            'BILLING DISTRICT',
            'BILLING CITY',
            'BILLING ADDRESS',
            'BILLING PINCODE',
            'SHIPPING STATE',
            'SHIPPING DISTRICT',
            'SHIPPING CITY',
            'SHIPPING ADDRESS',
            'SHIPPING PINCODE',
            'BEAT SAP CODE',
            'BEAT NAME'
		];
	}

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $models = Customer::with(['customerClass', 'customerType', 'billingState', 'shippingState', 'route'])
        			->get();

        $customers = [];

        foreach($models as $model) {
        	$customer = new stdClass;
        	$customer->sap_code = $model->sap_code;
        	$customer->name = $model->name;
        	$customer->customer_class = $model->customerClass ? $model->customerClass->name : '';
        	$customer->customer_type = $model->customerType ? $model->customerType->name : '';
        	$customer->gst_number = $model->gst_number;
        	$customer->town = $model->town;
        	$customer->longitude = $model->longitude;
        	$customer->latitude = $model->latitude;
        	$customer->owner_name = $model->owner_name;
        	$customer->owner_email = $model->owner_email;
        	$customer->owner_contact_number = $model->owner_contact_number;
        	$customer->billing_state = $model->billingState ? $model->billingState->name : '';
        	$customer->billing_district = $model->billing_district;
        	$customer->billing_city = $model->billing_city;
        	$customer->billing_address = $model->billing_address;
        	$customer->billing_pincode = $model->billing_pincode;
        	$customer->shipping_state = $model->shippingState ? $model->shippingState->name : '';
        	$customer->shipping_district = $model->shipping_district;
        	$customer->shipping_city = $model->shipping_city;
        	$customer->shipping_address = $model->shipping_address;
        	$customer->shipping_pincode = $model->shipping_pincode;
        	$customer->route_sap_code = $model->route ? $model->route->sap_code : '';
        	$customer->route_name = $model->route ? $model->route->name : '';

        	$customers[] = $customer;
        }

        return new Collection($customers);
    }
}
