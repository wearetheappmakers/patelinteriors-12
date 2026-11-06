<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use \App\Traits\SerializeDateTrait;

	protected $table = 'galleries';
	protected $primaryKey = 'id';
	protected $guarded = ['id'];

	public function branch()
	{
		return $this->belongsTo('App\User','branch_id','id');
	}

	public function category()
	{
		return $this->belongsTo('App\ProductCategory','productcategory_id','id');
	}

	public function product()
	{
		return $this->belongsTo('App\Product','product_id','id');
	}

	public function company()
	{
		return $this->belongsTo('App\Company','company_id','id');
	}
}
