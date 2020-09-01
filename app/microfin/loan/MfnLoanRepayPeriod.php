<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnLoanRepayPeriod extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loan_repay_period';

		protected $fillable = ['name',
							   'inMonths', 
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}