<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadBasicMainChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'lead_basic_main_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id']; 

     public function leadbasicchild(){
        return $this->hasMany('App\LeadBasicChild','lead_basic_main_id','id');
    } 

    public function employees(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
}
