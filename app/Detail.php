<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'details';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
