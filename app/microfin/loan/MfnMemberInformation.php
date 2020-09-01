<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnMemberInformation extends Model {

		public $timestamps = false;

		protected $table = 'mfn_member_information';

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', 1);
		}
							  
	}