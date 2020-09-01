<?php

namespace App\gnr;

use Illuminate\Database\Eloquent\Model;
use DB;

class GnrArea extends Model 
{
	public $timestamps = false;
	
	protected $table = 'gnr_area';
	
	protected $casts = [
		'branchId' => 'array'
	];
	
	protected $fillable = [
		'name', 
		'code', 
		'branchId', 
		'createdDate'
	];

	public static function findByBranchId( $branchId ) 
	{
		return  static::whereRaw('branchId Like :bid', [':bid'=>'%"'.$branchId.'"%'])->first();
	}
}