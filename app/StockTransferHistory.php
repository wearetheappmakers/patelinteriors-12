<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockTransferHistory extends Model
{
    use \App\Traits\SerializeDateTrait;

    //
    protected $table = 'stock_transfer_history';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

      public function branch()
    {
        return $this->belongsTo(User::class, 'branch_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
