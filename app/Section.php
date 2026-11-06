<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'section';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
