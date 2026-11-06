<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadFollowUp extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'lead_followup';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function lead(){
        return $this->belongsTo('App\Lead','lead_id','id');
    }
    public function types(){
        return $this->belongsTo('App\Type','type','id');
    }
     public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
}
