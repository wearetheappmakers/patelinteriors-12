<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ServiceImage extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'service_image';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

     protected $appends = ['uri'];

     public function getUriAttribute()
    {
        if($this->image)
        {
            return asset('service/'.$this->image);
        }
        else{

            return Null;
        }
    }
}
