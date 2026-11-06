<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'expense';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];  
    
    public function children()
    {
        return $this->hasMany(ExpenseChild::class, 'expense_id', 'id');
    }
}