<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsWithdraw extends Model {

		public $timestamps = false;
		protected $table = 'mfn_savings_withdraw';
		

		public function scopeActive($query) {		    
		    return $query->where('status', '=', 1)->where('softDel','!=',1);
		}

		public function getWithdrawDateAttribute($value){
			return date('d-m-Y', strtotime($value));
		}

		public function scopeFromTransferred($query) {
		    
		    return $query->where('isTransferred', '=', 1);
		}

		public function scopeNotTransferred($query) {
		    
		    return $query->where('isTransferred', '=', 0);
		}
							  
	}