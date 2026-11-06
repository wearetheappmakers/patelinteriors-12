<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class RevisedQuotation extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'revised_quotation';
     protected $primaryKey = 'id';
     protected $guarded = ['id'];

     public function client()
     {
          return $this->belongsTo('App\Client','client_id','id');
     }
     
     public function quotationchild()
     {
          return $this->hasMany('App\QuotationChild','quotation_id','id');
     }
     
     public function branch()
     {
          return $this->belongsTo('App\User','branch_id','id');
     }
     
     public function employee()
     {
          return $this->belongsTo('App\Employee','employee_id','id');
     }
     
     public function delivery()
     {
          return $this->belongsTo(Delivery::class,'id','quotation_id');
     }
     
     public function revisedchild()
     {
          return $this->hasMany('App\RevisedChild','revised_id','id');
     }
}
