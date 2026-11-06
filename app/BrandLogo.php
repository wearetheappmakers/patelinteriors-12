<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BrandLogo extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'brand_logos';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
