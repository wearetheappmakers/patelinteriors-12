<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'task';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }

    public function clients(){
        return $this->belongsTo('App\Client','client_id','id');
    }

    public function assignto(){
        return $this->belongsTo('App\Employee','assign_to','id');
    }
}
