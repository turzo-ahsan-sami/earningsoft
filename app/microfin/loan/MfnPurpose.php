<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnPurpose extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loans_purpose';

		protected $fillable = ['name', 
							   'code',
							   'purposeCategoryIdFK',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}