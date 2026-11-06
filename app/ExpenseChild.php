<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'expense_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];


    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
    
    public function expense(){
        return $this->belongsTo('App\Expense','expense_id','id');
    }
    
    
}