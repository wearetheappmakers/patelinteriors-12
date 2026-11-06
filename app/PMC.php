<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PMC extends Model
{
    use \App\Traits\SerializeDateTrait;

    protected $table = 'pmc';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

   
}
