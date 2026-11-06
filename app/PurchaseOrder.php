<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'purchase_order';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function vendor(){
        return $this->belongsTo('App\Vendor','vendor_id','id');
    }

    public function child()
    {
        return $this->hasMany('App\PurchaseOrderChild','purchase_order_id','id');
    }

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }
}