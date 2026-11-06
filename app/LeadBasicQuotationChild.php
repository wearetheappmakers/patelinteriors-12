<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadBasicQuotationChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'lead_basic_quotation_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];
}
