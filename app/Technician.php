<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use \App\Traits\SerializeDateTrait;

   protected $table = 'technician';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

   public function employee(){
      return $this->belongsTo('App\Employee','employee_id','id');
   }

   public function branch(){
      return $this->belongsTo('App\User','branch_id','id');
   }

   public function company(){
      return $this->belongsTo('App\Company','company_id','id');
   }

   public function product(){
      return $this->belongsTo('App\Product','product_id','id');
   }
   
   public function category(){
      return $this->belongsTo('App\ProductCategory','productcategory_id','id');
   }
}
