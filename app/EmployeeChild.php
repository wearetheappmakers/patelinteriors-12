<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'employee_child';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];
}
