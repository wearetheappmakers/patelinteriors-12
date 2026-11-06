<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'product';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function company(){
        return $this->belongsTo('App\Company','company_id','id');
    }

    public function productcategory(){
        return $this->belongsTo('App\ProductCategory','productcategory_id','id');
    }

    protected $appends = [
        'dropdown_name',
    ];

    public function getDropdownNameAttribute()
    {
        $code = !empty($this->code) ? $this->code : '';
        $data = $code;
        return $data;
    }
}
