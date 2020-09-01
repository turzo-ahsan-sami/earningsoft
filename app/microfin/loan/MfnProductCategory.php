<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnProductCategory extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loans_product_category';

		protected $fillable = ['name', 
							   'shortName',
							   'overrideSavingsDepositeFrequency',
							   'overrideSavingsDepositeFrequencyForCategory',
							   'monthlyCollectionWeek',
							   'categoryTypeId',
 							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}
							  
	}