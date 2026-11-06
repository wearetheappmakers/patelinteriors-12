<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'role';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    
}