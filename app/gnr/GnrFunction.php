<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;

class GnrFunction extends Model 
{

	public $timestamps = false;		
	
	protected $table = 'gnr_function';

	protected $fillable = [
		'name',
		'moduleIdFK',
		'description',
		'createdAt'
	];
}
