<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsCollectionFrequency extends Model {

		public $timestamps = false;
		protected $table = 'mfn_savings_collection_frequency';		

		public function scopeActive($query) {
		    
		    return $query->where('status',1)->where('softDel','!=',1);
		}
							  
	}