<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

     public function productcategory(){
        return $this->belongsTo('App\ProductCategory','pro_category_id','id');
       }
}
