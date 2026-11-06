<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemLedger extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'item_ledger';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];   

    public function vendors(){
        return $this->belongsTo('App\Vendor','vendor_id','id');
    }

    public function clients(){
        return $this->belongsTo('App\Client','client_id','id');
    }  

    public function products(){
        return $this->belongsTo('App\Product','product_id','id');
    }
}