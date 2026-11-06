<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Symbol extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'symbol';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
