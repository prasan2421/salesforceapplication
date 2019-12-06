<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Import extends Moloquent
{
    public function user() {
        return $this->belongsTo('App\User');
    }
}
