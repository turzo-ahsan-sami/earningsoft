<?php

namespace App\microfin\loan;

use Illuminate\Database\Eloquent\Model;

class MfnLoanCollection extends Model {

	public $timestamps = false;

	protected $table = 'mfn_loan_collection';

	public function scopeActive($query) {
		
		return $query->where('softDel', '!=', 1);
	}
	
}