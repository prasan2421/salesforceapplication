<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Distributor extends Moloquent
{
    public function verticals() {
    	return $this->belongsToMany('App\Vertical');
    }

    public function routes() {
        return $this->belongsToMany('App\Route');
    }

    public function users() {
    	return $this->hasMany('App\User');
    }
}
