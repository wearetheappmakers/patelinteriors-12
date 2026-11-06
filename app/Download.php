<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'download';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
