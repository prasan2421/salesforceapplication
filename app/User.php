<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Illuminate\Foundation\Auth\User as Authenticatable;
use Jenssegers\Mongodb\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function parent() {
        return $this->belongsTo('App\User', 'parent_id');
    }

    public function children() {
        return $this->hasMany('App\User', 'parent_id');
    }

    public function ancestors() {
        return $this->belongsToMany('App\User', null, 'descendant_ids', 'ancestor_ids');
    }

    public function descendants() {
        return $this->belongsToMany('App\User', null, 'ancestor_ids', 'descendant_ids');
    }

    public function salesOfficer() {
        return $this->belongsTo('App\User', 'sales_officer_id');
    }

    public function dsms() {
        return $this->hasMany('App\User', 'sales_officer_id');
    }

    public function distributor() {
        return $this->belongsTo('App\Distributor');
    }

    public function division() {
        return $this->belongsTo('App\Division');
    }

    public function verticals() {
        return $this->belongsToMany('App\Vertical');
    }

    public function state() {
        return $this->belongsTo('App\State');
    }

    public function attendances() {
        return $this->hasMany('App\Attendance');
    }

    public function customerVisits() {
        return $this->hasMany('App\CustomerVisit');
    }

    public function geolocations() {
        return $this->hasMany('App\Geolocation');
    }

    public function routeUsers() {
        return $this->hasMany('App\RouteUser');
    }

    public function feedbacks() {
        return $this->hasMany('App\Feedback');
    }

    public function routes() {
        return $this->belongsToMany('App\Route');
    }

    public function createdRoutes() {
        return $this->hasMany('App\Route', 'created_by');
    }
}
