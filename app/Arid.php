<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Arid extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'arid';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
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