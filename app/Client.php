<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'client';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];


    public function referred(){
        return $this->belongsTo('App\LeadSource','referred_type','id');
    }

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }

    public function clientdocument(){
        return $this->belongsTo('App\ClientDocument','id','client_id');
    }

    public function landmarks(){
        return $this->belongsTo('App\Landmark','landmark_id','id');
    } 

    public function countrys()
    {
        return $this->belongsTo('App\Country','country_id','id');
    }

    public function states()
    {
        return $this->belongsTo('App\State','state_id','id');
    }

    public function citys()
    {
        return $this->belongsTo('App\City','city_id','id');
    }     
}