<?php

	namespace App\microfin\configuration\openingBalance;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnloanOpeningBalance extends Model {

		public $timestamps = false;

		protected $table = 'mfn_opening_balance_loan';

		protected $fillable = ['loanIdFk',
							   'oldLoanCode',
							   'paidLoanAmountOB',
							   'principalAmountOB',
							   'interestAmountOB',
							   'dueAmountOB',
							   'date',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', 1);
		}
							  
	}