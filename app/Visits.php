<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Visits extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'interior_visit';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }
    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
    public function arids(){
        return $this->belongsTo('App\Arid','arid','id');
    }
    public function sourcenm(){
        return $this->belongsTo('App\SourceName','architect_id','id');
    }
    public function child(){
        return $this->hasMany('App\VisitsChild','interior_visit_id','id');
    }
}