<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnPurposeCategory extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loans_purpose_category';

		protected $fillable = ['name', 
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}