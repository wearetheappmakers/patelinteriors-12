<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class SalesOrder extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'salesorder';
     protected $primaryKey = 'id';
     protected $guarded = ['id'];
     
     public function salesorderchild()
     {
          return $this->hasMany('App\SalesOrderChild','salesorder_id','id');
     }
     
     public function branch()
     {
          return $this->belongsTo('App\User','branch_id','id');
     }
     
     public function employee()
     {
          return $this->belongsTo('App\Employee','employee_id','id');
     }
     
     public function client()
     {
          return $this->belongsTo('App\Client','name','id');
     }
}