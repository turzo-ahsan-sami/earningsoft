<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnInsuranceCalculationMethod extends Model {

		public $timestamps = false;

		protected $table = 'mfn_insurance_calculation_method';

		protected $fillable = ['name', 
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}