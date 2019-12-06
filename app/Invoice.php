<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Invoice extends Moloquent
{
    public function order() {
        return $this->belongsTo('App\Order');
    }

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function invoiceProducts() {
    	return $this->hasMany('App\InvoiceProduct');
    }
}
