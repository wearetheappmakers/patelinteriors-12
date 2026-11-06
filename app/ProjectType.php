<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use \App\Traits\SerializeDateTrait;

	protected $table = 'project_type';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];
}
