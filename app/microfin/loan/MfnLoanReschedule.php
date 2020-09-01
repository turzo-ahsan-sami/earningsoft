<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnLoanReschedule extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loan_reschedule';

		protected $fillable = ['loanScheduleId',
							   'loanIdFk',
							   'loanTypeId',
							   'installmentNo',
							   'rescheduleFrom',
							   'rescheduleTo',
							   'rescheduleDate',
							   'rescheduleBy',
							   'samityIdFk',
							   'branchIdFk',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchIdFk', '=', Auth::user()->branchId);
		}
							  
	}