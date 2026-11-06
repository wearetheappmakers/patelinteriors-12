<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'country';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];     
}