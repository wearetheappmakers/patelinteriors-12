<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadCommunication extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'lead_communication';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

   
}
