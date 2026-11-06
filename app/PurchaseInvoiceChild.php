<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'purchase_invoice_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function invoices(){
        return $this->belongsTo('App\PurchaseInvoice','invoice_id','id');
    }
    public function productcategory(){
        return $this->belongsTo('App\ProductCategory','category_id','id');
    }
    public function company(){
        return $this->belongsTo('App\Company','company_id','id');
    }
    public function product(){
        return $this->belongsTo('App\Product','product_id','id');
    }
    public function warehouse(){
        return $this->belongsTo('App\Warehouse','warehouse_id','id');
    }  
    public function pro_gst(){
        return $this->belongsTo('App\GST','gst','id');
    }
}