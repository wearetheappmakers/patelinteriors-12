<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'purchase_invoice';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function vendor(){
        return $this->belongsTo('App\Vendor','vendor_id','id');
    }
    public function child()
    {
        return $this->hasMany('App\PurchaseInvoiceChild','invoice_id','id');
    }
    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }  
}