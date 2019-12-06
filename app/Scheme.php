<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Scheme extends Moloquent
{
    public function product() {
        return $this->belongsTo('App\Product');
    }
}
