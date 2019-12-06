<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Brand extends Moloquent
{
    public function division() {
        return $this->belongsTo('App\Division');
    }

    public function vertical() {
        return $this->belongsTo('App\Vertical');
    }

    public function products() {
    	return $this->hasMany('App\Product');
    }
}
