<?php

	  namespace App\microfin\settings;

	  use Illuminate\Database\Eloquent\Model;

	  class MfnLoanProductInterestRate extends Model {
		
		public $timestamps = false;

		protected $table = 'mfn_loan_product_interest_rate';

		protected $fillable = ['name', 
							   'loanProductId',
							   'interestCalculationMethodId',
							   'interestCalculationMethodShortName',
							   'declinePeriodId',
							   'dayCountFixed',
							   'effectiveDate',
							   'interestModeId',
							   'interestRate',
							   'interestRateIndex',
							   'installmentNum',
							   'repaymentFrequencyId',
							   'isEnforceNumberInstallmentRequired',
							   'enforcedInstallmentNumber',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
	}