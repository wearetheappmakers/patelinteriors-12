<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'product_category';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
