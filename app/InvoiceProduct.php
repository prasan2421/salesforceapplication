<?php

namespace App;

// use Illuminate\Database\Eloquent\Model;
use Moloquent;

class InvoiceProduct extends Moloquent
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invoice_product';

    public function invoice() {
        return $this->belongsTo('App\Invoice');
    }

    public function product() {
        return $this->belongsTo('App\Product');
    }
}
