<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'delivery_log';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

    public function product(){
        return $this->belongsTo('App\Product','product_id','id');
       }

   
  
}
