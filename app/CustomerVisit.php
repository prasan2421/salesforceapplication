<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class CustomerVisit extends Moloquent
{
    protected $dates = [ 'check_in_time', 'check_out_time' ];

    public function customer() {
        return $this->belongsTo('App\Customer');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
