<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsInterestCalMethod extends Model {

		public $timestamps = false;
		protected $table = 'mfn_savings_interest_cal_method';		

		public function scopeActive($query) {
		    
		    return $query->where('status',1)->where('softDel','!=',1);
		}
							  
	}