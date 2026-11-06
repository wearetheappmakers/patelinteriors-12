<?php

namespace App;

use App\Notifications\UserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Attendance extends Authenticatable 
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'attendance';
     protected $primaryKey = 'id';
     protected $guarded = ['id'];


     public function employee()
     {
       return $this->belongsTo(Employee::class,'employee_id','id');
  }

  public function branch()
     {
       return $this->belongsTo(User::class,'branch_id','id');
  }
}
