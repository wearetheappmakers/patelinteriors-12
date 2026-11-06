<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class ServiceChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'service_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function company(){
        return $this->belongsTo('App\Company','company_id','id');
    }
    public function product(){
        return $this->belongsTo('App\Product','product_id','id');
    }
    public function category(){
        return $this->belongsTo('App\ProductCategory','category_id','id');
    }
    public function technician(){
        return $this->belongsTo('App\Technician','technician_id','id');
    }
}