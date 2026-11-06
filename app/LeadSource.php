<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadSource extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'lead_source';
     protected $primaryKey = 'id';
     protected $guarded = ['id'];
}
