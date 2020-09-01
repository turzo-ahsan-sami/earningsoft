<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnFees extends Model {

		public $timestamps = false;

		protected $table = 'mfn_fees';

		protected $fillable = ['name',
							   'loanIdFk',
							   'savingsIdFk',
							   'loanAdditionalFee',
							   'loanFormFee',
							   'savingsFee',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}