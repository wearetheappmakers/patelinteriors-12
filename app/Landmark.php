<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Landmark extends Model
{
    use \App\Traits\SerializeDateTrait;

	protected $table = 'landmark';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];
}
