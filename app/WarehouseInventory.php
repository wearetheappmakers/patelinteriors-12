<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WarehouseInventory extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'warehouse_inventories';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function warehouse(){
        return $this->belongsTo('App\Warehouse','warehouse_id','id');
    }

    public function product(){
        return $this->belongsTo('App\Product','product_id','id');
    }
}
