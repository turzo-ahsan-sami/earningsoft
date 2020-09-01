<?php

namespace App\fams;

use Illuminate\Database\Eloquent\Model;

class FamsDepreciation extends Model
{
	public $timestamps = false;
	protected $table ='fams_depreciation';
	protected $fillable = ['id','depId','depGroupId','groupDepNo','	branchId','projectId','branchDepNo','amount','createdDate','depOpeningDate	','	status'];

	
	public function getAmountAttribute($value)
	{
		return number_format($value, 2, '.', ',');   
	}
}
