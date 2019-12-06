<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class OrderProduct extends Moloquent
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_product';

    public function order() {
        return $this->belongsTo('App\Order');
    }
    
    public function product() {
        return $this->belongsTo('App\Product');
    }
}
