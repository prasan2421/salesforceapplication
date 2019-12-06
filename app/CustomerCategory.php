<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class CustomerCategory extends Moloquent
{
    public function customers() {
    	return $this->hasMany('App\Customer');
    }
}
