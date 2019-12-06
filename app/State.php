<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class State extends Moloquent
{
    public function billingCustomers() {
    	return $this->hasMany('App\Customer', 'billing_state_id');
    }

    public function shippingCustomers() {
    	return $this->hasMany('App\Customer', 'shipping_state_id');
    }

    public function routes() {
    	return $this->hasMany('App\Route');
    }
}
