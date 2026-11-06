<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'group';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
