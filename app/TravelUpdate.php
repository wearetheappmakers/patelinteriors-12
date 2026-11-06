<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TravelUpdate extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'travel_update';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
    
}