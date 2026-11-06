<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class SalesOrderInvoice extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'salesorder_invoice';
     protected $primaryKey = 'id';
     protected $guarded = ['id'];

     public function client()
     {
          return $this->belongsTo('App\Client','client_id','id');
     }
     
     public function child()
     {
          return $this->hasMany('App\SalesOrderInvoiceChild','invoice_id','id');
     }
     
     public function branch()
     {
          return $this->belongsTo('App\User','branch_id','id');
     }
     
     public function employees()
     {
          return $this->belongsTo('App\Employee','employee_id','id');
     }

     public function pendingso()
     {
          return $this->belongsTo('App\SalesOrder','pending_so','id');
     }
}