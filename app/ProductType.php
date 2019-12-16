<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class ProductType extends Moloquent
{
    public function products() {
    	return $this->hasMany('App\Product');
    }
}
