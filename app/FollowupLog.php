<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FollowupLog extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'followup_log';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
