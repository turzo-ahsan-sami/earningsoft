<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnLoanSchedule extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loan_schedule';

		protected $fillable = ['loanIdFk',
							   'loanTypeId',
							   'installmentSl',
							   'installmentAmount',
							   'actualInstallmentAmount',
							   'extraInstallmentAmount',
							   'principalAmount',
							   'interestAmount',
							   'scheduleDate',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopeComplete($query) {
		    
		    return $query->where('isCompleted', '=', 1);
		}

		public function scopeIncomplete($query) {
		    
		    return $query->where('isCompleted', '=', 0);
		}

		public function scopePartial($query) {
		    
		    return $query->where('isPartiallyPaid', '=', 1);
		}
									  
	}