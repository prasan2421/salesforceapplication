<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class Category extends Moloquent
{
    public function products() {
    	return $this->hasMany('App\Product');
    }

    public function parent() {
        return $this->belongsTo('App\Category', 'parent_id');
    }

    public function children() {
        return $this->hasMany('App\Category', 'parent_id');
    }
}
