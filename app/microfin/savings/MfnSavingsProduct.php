<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsProduct extends Model {

		public $timestamps = false;
		protected $table = 'mfn_saving_product';
		

		public function scopeActive($query) {		    
		    return $query->where('status', '=', 1)->where('softDel','!=',1);
		}

		public function getStartDateAttribute($value){
			return date('d-m-Y', strtotime($value));
		}
							  
	}