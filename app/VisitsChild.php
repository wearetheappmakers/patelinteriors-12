<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VisitsChild extends Model
{
    use \App\Traits\SerializeDateTrait;

   protected $table = 'interior_visit_child';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

   public function products(){
      return $this->belongsTo('App\Product','product_id','id');
   }
   public function companys(){
      return $this->belongsTo('App\Company','company_id','id');
   }
   public function categories(){
      return $this->belongsTo('App\ProductCategory','category_id','id');
   }
}
