<?php

	namespace App\gnr;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class GnrWorkingArea extends Model {

		public $timestamps = false;
		
		protected $table = 'gnr_working_area';
		
		protected $fillable = ['name', 
							   //'code', 
							   'branchId',
							   'divisionId',
							   'districtId',
							   'upazilaId',
							   'unionId',
							   'villageId',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1);
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchId', '=', Auth::user()->branchId);
		}
	}