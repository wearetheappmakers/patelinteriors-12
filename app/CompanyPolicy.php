<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyPolicy extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'company_policy';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }
    public function createdByAdmin()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function createdByEmployee()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
    
}    