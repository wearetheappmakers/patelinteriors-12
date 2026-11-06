<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'types';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
