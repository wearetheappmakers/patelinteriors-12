<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Complain extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'complain';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
