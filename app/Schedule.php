<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Schedule extends Moloquent
{
    public function customers() {
    	return $this->belongsToMany('App\Customer');
    }
}
