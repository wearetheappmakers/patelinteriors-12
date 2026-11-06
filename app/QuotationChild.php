<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuotationChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'quotation_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    protected $appends = ['image_full'];

    public function getImageFullAttribute()
    {
        if(empty($this->image)){
            return '';
        }
        return asset('quotation/'.$this->image);
    }   

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }

    public function products()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function productcategory()
    {
        return $this->belongsTo(ProductCategory::class,'productcategory_id','id');
    }

    public function companys()
    {
        return $this->belongsTo(Company::class,'company_id','id');
    }

    public function warehouses()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id','id');
    }
     public function gsts(){
        return $this->belongsTo('App\GST','gst_id','id');
    }

   
}
