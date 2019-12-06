<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Product extends Moloquent
{
    // public function category() {
    //     return $this->belongsTo('App\Category');
    // }
    
    public function division() {
        return $this->belongsTo('App\Division');
    }

    public function vertical() {
        return $this->belongsTo('App\Vertical');
    }

    public function brand() {
        return $this->belongsTo('App\Brand');
    }

    public function unit() {
        return $this->belongsTo('App\Unit');
    }

    public function schemes() {
    	return $this->hasMany('App\Scheme');
    }

    public function orderProducts() {
        return $this->hasMany('App\OrderProduct');
    }

    public function invoiceProducts() {
        return $this->hasMany('App\InvoiceProduct');
    }
}
