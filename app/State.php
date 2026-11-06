<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'state';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];     
}