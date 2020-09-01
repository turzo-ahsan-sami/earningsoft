<?php

	namespace App\microfin\samity;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnSamityDayChange extends Model {

		public $timestamps = false;
		
		protected $table = 'mfn_samity_day_change';
		
		// protected $fillable = ['samityIdFk',
		// 					   'branchIdFk',
		// 					   'oldSamityDayId',
		// 					   'newSamityDayId',
		// 					   'effectiveDate',
		// 					   'createdDate'
		// 					  ];

		protected $fillable = ['samityId',
							   'branchId',
							   'samityDayId',
							   'newSamityDayId',
							   'effectiveDate',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', 1);
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchIdFk', '=', Auth::user()->branchId);
		}
							  
	}