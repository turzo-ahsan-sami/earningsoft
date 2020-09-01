<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsFdrProductRepayAmount extends Model {

		public $timestamps = false;
		protected $table = 'mfn_savings_fdr_product_repay_amount';
		

		public function scopeActive($query) {		    
		    return $query->where('status', '=', 1)->where('softDel','!=',1);
		}
		
							  
	}