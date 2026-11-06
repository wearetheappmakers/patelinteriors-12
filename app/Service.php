<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Service extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'service';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
    public function child(){
        return $this->hasMany('App\ServiceChild','service_id','id');
    }
    public function image(){
        return $this->hasMany('App\ServiceImage','service_id','id');
    }
    public function branch(){
        return $this->belongsTo('App\User','branch_id','id');
    }
    public function client(){
        return $this->belongsTo('App\Client','client_id','id');
    }
    public function consentperson(){
        return $this->belongsTo('App\ConsentPerson','consent_person_id','id');
    }
}