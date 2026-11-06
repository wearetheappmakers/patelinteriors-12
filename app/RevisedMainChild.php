<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class RevisedMainChild extends Model
{
    use \App\Traits\SerializeDateTrait;

	protected $table = 'revised_main_child';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];

	public function employees()
	{
		return $this->belongsTo('App\Employee','employee_id','id');
	}
}
