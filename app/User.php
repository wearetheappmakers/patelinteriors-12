<?php

namespace App;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Notifications\UserResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'users';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

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
            return asset('user/'.$this->image);
        }
        else{

            return Null;
        }
    }
}
