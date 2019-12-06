<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Route extends Moloquent
{
    public function division() {
        return $this->belongsTo('App\Division');
    }

    public function state() {
        return $this->belongsTo('App\State');
    }

    public function creator() {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function customers() {
        return $this->hasMany('App\Customer');
    }

    public function routeUsers() {
        return $this->hasMany('App\RouteUser');
    }

    public function users() {
        return $this->belongsToMany('App\User');
    }

    public function distributors() {
        return $this->belongsToMany('App\Distributor');
    }
}
