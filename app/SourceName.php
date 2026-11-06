<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SourceName extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'source_names';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
