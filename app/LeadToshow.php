<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadToshow extends Model
{
    use \App\Traits\SerializeDateTrait;

   protected $table = 'lead_toshow';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];


}
