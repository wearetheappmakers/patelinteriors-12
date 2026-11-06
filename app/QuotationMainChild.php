<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class QuotationMainChild extends Model
{
    use \App\Traits\SerializeDateTrait;

   protected $table = 'quotation_main_child';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

   public function quotationchild()
   {
   	return $this->hasMany('App\QuotationChild','quot_main_child_id','id');
   }

   public function employees()
   {
      return $this->belongsTo('App\Employee','employee_id','id');
   }

   public function symbols()
   {
      return $this->belongsTo('App\Symbol','symbol','id');
   }
}
