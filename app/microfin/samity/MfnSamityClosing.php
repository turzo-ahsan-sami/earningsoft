<?php

	namespace App\microfin\samity;

	use Illuminate\Database\Eloquent\Model;

	class MfnSamityClosing extends Model {

		public $timestamps = false;
		protected $table = 'mfn_samity_closing';
		
		protected $fillable = ['samityId', 
							   'closingDate',
							   'createdDate'
							  ];
							  
	}