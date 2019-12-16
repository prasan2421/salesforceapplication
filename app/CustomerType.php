<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class CustomerType extends Moloquent
{
    public function customers() {
    	return $this->hasMany('App\Customer');
    }

    public function productType() {
        return $this->belongsTo('App\ProductType');
    }


}
