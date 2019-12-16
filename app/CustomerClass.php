<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class CustomerClass extends Moloquent
{
    public function customers() {
    	return $this->hasMany('App\Customer');
    }

    public function customer_type()
    {
        return $this->belongsTo('App\CustomerType');
    }
}
