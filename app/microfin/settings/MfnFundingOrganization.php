<?php

	namespace App\microfin\settings;

	use Illuminate\Database\Eloquent\Model;

	class MfnFundingOrganization extends Model {

		public $timestamps = false;

		protected $table = 'mfn_funding_organization';

		protected $fillable = ['name', 
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}