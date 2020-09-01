<?php

	namespace App\microfin\samity;

	use Illuminate\Database\Eloquent\Model;

	class MfnSamityFieldOfficerChange extends Model {

		public $timestamps = false;
		protected $table = 'mfn_samity_field_officer_change';
		
		protected $fillable = ['samityId',
							   'fieldOfficerId',
							   'newFieldOfficerId',
							   'effectiveDate',
							   'createdDate'
							  ];
							  
	}