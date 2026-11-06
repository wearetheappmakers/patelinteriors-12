<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'vendor';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];  

    public function country()
    {
        return $this->belongsTo('App\Country','country_id','id');
    }

    public function state()
    {
        return $this->belongsTo('App\State','state_id','id');
    }

    public function city()
    {
        return $this->belongsTo('App\City','city_id','id');
    }
}