<?php

namespace App;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\EmployeeResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Employee extends Authenticatable implements JWTSubject
{
    use \App\Traits\SerializeDateTrait;

   protected $table = 'employees';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

    public function branchs(){
        return $this->belongsTo('App\User','branch_id','id');
       }

        public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

      protected $appends = ['image_path'];

     public function getImagePathAttribute()
    {
        if($this->image)
        {
            return asset('employee/'.$this->image);
        }
        else{

            return Null;
        }
    }

       
}
