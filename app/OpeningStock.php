<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningStock extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'opening_stock';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
