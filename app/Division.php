<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Division extends Moloquent
{
    public function verticals() {
    	return $this->hasMany('App\Vertical');
    }

    public function brands() {
    	return $this->hasMany('App\Brand');
    }

    public function products() {
    	return $this->hasMany('App\Product');
    }

    public function routes() {
    	return $this->hasMany('App\Route');
    }
}
