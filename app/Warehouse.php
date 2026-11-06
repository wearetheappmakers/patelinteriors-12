<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'warehouses';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
