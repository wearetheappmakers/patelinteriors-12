<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use \App\Traits\SerializeDateTrait;


     use SoftDeletes;
    protected $table = 'lead';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function lead_assign()
    {
        return $this->belongsTo('App\LeadAssign','id','lead_id');
    }

    public function lead_communication()
    {
        return $this->belongsTo('App\LeadCommunication','id','lead_id');
    }

    public function lead_followup()
    {
        return $this->hasMany('App\LeadFollowUp','lead_id','id');
    }

    public function client(){
        return $this->belongsTo('App\Client','client_id','id');
    }
    public function source(){
        return $this->belongsTo('App\LeadSource','source_id','id');
    }

    public function sourcename(){
        return $this->belongsTo('App\SourceName','source_name','id');
    }

    public function product(){
        return $this->belongsTo('App\Product','product_id','id');
    }

   

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
    public function basicdesign(){
        return $this->hasMany('App\LeadBasicDesignChild','lead_id','id');
    }
     public function mainchild(){
        return $this->hasMany('App\LeadBasicMainChild','lead_id','id');
    }
}