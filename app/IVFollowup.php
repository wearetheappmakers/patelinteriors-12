<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IVFollowup extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'iv_followup';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    public function visit(){
        return $this->belongsTo('App\Visits','iv_id','id');
    }
    public function types(){
        return $this->belongsTo('App\Type','type','id');
    }
    public function employee(){
        return $this->belongsTo('App\Employee','employee_id','id');
    }
}
