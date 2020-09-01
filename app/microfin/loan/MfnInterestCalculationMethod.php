<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnInterestCalculationMethod extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loan_interest_calculation_method';

		protected $fillable = ['name', 
								'shortName',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}