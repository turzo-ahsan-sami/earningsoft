<?php

namespace App\microfin\samity;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class MfnSamity extends Model {

	public $timestamps = false;
	protected $table = 'mfn_samity';
	
	protected $fillable = ['name', 
	'code',
	'branchId', 
	'branchCode',
	'samitySL',
	'workingAreaId',
	'registrationNo',
	'fieldOfficerId',
	'samityDayOrFixedDateId',
	'samityDayId',
	'samityDayTime',
	'fixedDate',
	'samityTypeId',
	'samityDayOptional',
	'isTransferable',
	'openingDate',
	'maxNumber',
	'latitude',
	'longitude',
	'createdDate'
];

public function scopeActive($query) {
	
	return $query->where('softDel',0)->where('status', '=', 1);
}

public function scopeBranchWise($query) {

	//print_r(Auth::user()->branchId);
	
	return $query->where('branchId', '=', Auth::user()->branchId);
}

}