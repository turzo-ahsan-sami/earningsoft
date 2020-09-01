<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnSubPurpose extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loans_sub_purpose';

		protected $fillable = ['name', 
							   'code',
							   'purposeIdFK',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}