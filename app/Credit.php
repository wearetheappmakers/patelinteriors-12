<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'credit';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function client()
    {
        return $this->belongsTo('App\Client','client_id','id');
    }

    public function child()
    {
        return $this->hasMany('App\CreditChild','credit_id','id');
    }

    public function branch()
    {
        return $this->belongsTo('App\User','branch_id','id');
    }

    public function soinvoice()
    {
        return $this->belongsTo('App\SalesOrderInvoice','invoice_id','id');
    }
}
