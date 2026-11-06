<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpeningStockChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'opening_stock_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

}
