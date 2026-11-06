<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GST extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'gst';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
