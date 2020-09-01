<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsMonthlyCollectionType extends Model {

		public $timestamps = false;
		protected $table = 'mfn_saving_monthly_collection_type';
		

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel','!=',1);
		}
							  
	}