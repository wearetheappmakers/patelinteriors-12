<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadStage extends Model
{
    use \App\Traits\SerializeDateTrait;

     protected $table = 'lead_stage';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
