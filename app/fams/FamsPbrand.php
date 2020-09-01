<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsPbrand extends Model
{
	public $timestamps = false;
	protected $table ='fams_product_brand';
	protected $fillable = ['name','id','brandCode','createdDate','updatedDate','status'];
}
