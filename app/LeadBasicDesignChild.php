<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadBasicDesignChild extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'lead_basic_design_child';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

     protected $appends = ['image_full_path'];

    public function getImageFullPathAttribute()
    {
        if(empty($this->design)){
            return '';
        }
        return asset('design/'.$this->design);
    }
}
