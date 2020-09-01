<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsAccountNominee extends Model {

		public $timestamps = false;

		protected $table = 'mfn_savings_fdr_acc_nominee_info';


		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', 1);
		}
	}