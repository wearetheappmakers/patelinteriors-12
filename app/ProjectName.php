<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectName extends Model
{
    use \App\Traits\SerializeDateTrait;

	protected $table = 'project_name';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];
}
