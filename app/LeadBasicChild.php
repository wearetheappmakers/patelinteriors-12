<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadBasicChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'lead_basic_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function productcategory(){
        return $this->belongsTo('App\ProductCategory','productcategory_id','id');
    }

    public function company(){
        return $this->belongsTo('App\Company','company_id','id');
    }

    public function product(){
        return $this->belongsTo('App\Product','product_id','id');
    }
    public function warehouse(){
        return $this->belongsTo('App\Warehouse','warehouse_id','id');
    }
   
}
