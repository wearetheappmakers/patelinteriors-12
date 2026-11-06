<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeadAssign extends Model
{
    use \App\Traits\SerializeDateTrait;

   protected $table = 'lead_assign';
   protected $primaryKey = 'id';
   protected $guarded = ['id'];

   public function stage()
   {
      return $this->belongsTo('App\LeadStage','stage_id','id');
   }

   public function employee()
   {
      return $this->belongsTo('App\Employee','employee_id','id');
   }

   public function lead()
   {
      return $this->belongsTo('App\Lead','lead_id','id');
   }

   public function section()
   {
      return $this->belongsTo('App\Section','section_id','id');
   }


}
