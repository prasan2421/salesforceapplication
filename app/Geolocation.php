<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Geolocation extends Moloquent
{
	protected $dates = ['mobile_created_at'];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
