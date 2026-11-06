<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'leaves';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id','id');
    }

    public function branch()
    {
        return $this->belongsTo(User::class,'branch_id','id');
    }
}
