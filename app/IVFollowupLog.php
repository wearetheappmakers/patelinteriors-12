<?php



namespace App;



use Illuminate\Database\Eloquent\Model;



class IVFollowupLog extends Model

{
    use \App\Traits\SerializeDateTrait;


    protected $table = 'iv_followup_log';

   protected $primaryKey = 'id';

   protected $guarded = ['id'];

}

