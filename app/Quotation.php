<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'quotation';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo('App\Client','client_id','id');
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
    public function lead()
    {
        return $this->belongsTo(Lead::class,'id','lead_id');
    }
    public function sourcename()
    {
        return $this->belongsTo('App\SourceName','source_name','id');
    }
    public function mainchild()
    {
        return $this->hasMany('App\QuotationMainChild','quot_id','id');
    }
}
