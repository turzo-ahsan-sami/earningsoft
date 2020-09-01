<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrModule extends Model {

	public $timestamps = false;

	protected $table = 'gnr_module';
	
	protected $fillable = [
		'id',
		'name',
		'description'
	];
}
