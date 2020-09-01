<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnGovHoliday extends Model {

		public $timestamps = false;

		protected $table = 'mfn_setting_gov_holiday';
		

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}