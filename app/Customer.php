<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Customer extends Moloquent
{
    public function division() {
        return $this->belongsTo('App\Division');
    }

    public function state() {
        return $this->belongsTo('App\State');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }

    public function location() {
        return $this->belongsTo('App\Location');
    }

	public function customerType() {
        return $this->belongsTo('App\CustomerType');
    }

    public function customerClass() {
        return $this->belongsTo('App\CustomerClass');
    }

	public function customerCategory() {
        return $this->belongsTo('App\CustomerCategory');
    }

    public function route() {
        return $this->belongsTo('App\Route');
    }

    public function billingState() {
        return $this->belongsTo('App\State', 'billing_state_id');
    }

    public function shippingState() {
        return $this->belongsTo('App\State', 'shipping_state_id');
    }

    public function schedules() {
    	return $this->belongsToMany('App\Schedule');
    }

    public function customerVisits() {
        return $this->hasMany('App\CustomerVisit');
    }
}
