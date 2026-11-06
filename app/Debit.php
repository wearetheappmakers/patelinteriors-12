<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Debit extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'debit';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function vendor(){
        return $this->belongsTo('App\Vendor','vendor_id','id');
    }
    public function invoices(){
        return $this->belongsTo('App\PurchaseInvoice','invoice_id','id');
    }
    public function child()
    {
        return $this->hasMany('App\DebitChild','debit_id','id');
    }

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }
}
