<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsDepositType extends Model {

		public $timestamps = false;
		protected $table = 'mfn_savings_deposit_type';		

		public function scopeActive($query) {
		    
		    return $query->where('status',1)->where('softDel','!=',1);
		}
							  
	}