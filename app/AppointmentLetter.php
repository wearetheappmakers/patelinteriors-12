<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AppointmentLetter extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'appointment_letter';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
    public function createdByAdmin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdByEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    
}    