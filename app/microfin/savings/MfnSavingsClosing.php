<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsClosing extends Model {

		public $timestamps = false;
		protected $table = 'mfn_savings_closing';
		

		public function scopeActive($query) {		    
		    return $query->where('status', '=', 1)->where('softDel','!=',1);
		}

		public function getClosingDateAttribute($value){
			return date('d-m-Y', strtotime($value));
		}
							  
	}