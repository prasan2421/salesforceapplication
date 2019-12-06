<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class RouteUser extends Moloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'route_user';

    public function route() {
        return $this->belongsTo('App\Route');
    }

    public function user() {
        return $this->belongsTo('App\User');
    }
}
