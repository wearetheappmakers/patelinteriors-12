<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'city';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];     
}