<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Vertical extends Moloquent
{
    public function division() {
        return $this->belongsTo('App\Division');
    }

    public function brands() {
    	return $this->hasMany('App\Brand');
    }

    public function products() {
    	return $this->hasMany('App\Product');
    }

    public function distributors() {
    	return $this->belongsToMany('App\Distributor');
    }

    public function users() {
        return $this->belongsToMany('App\User');
    }
}
