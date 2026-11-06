<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConsentPerson extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'consent_person';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }
}