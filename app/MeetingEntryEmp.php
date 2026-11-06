<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MeetingEntryEmp extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'meeting_entry_emp';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }

}