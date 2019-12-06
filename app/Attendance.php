<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Attendance extends Moloquent
{
    protected $dates = [ 'punch_in_time', 'punch_out_time' ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
