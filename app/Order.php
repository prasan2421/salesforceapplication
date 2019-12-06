<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Order extends Moloquent
{
    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function orderProducts() {
    	return $this->hasMany('App\OrderProduct');
    }

    public function invoice() {
    	return $this->hasOne('App\Invoice');
    }
}
