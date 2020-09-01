<?php

	namespace App\microfin\samity;

	use Illuminate\Database\Eloquent\Model;

	class MfnSamityTransfer extends Model {

		public $timestamps = false;
		protected $table = 'mfn_samity_transfer';
		
		protected $fillable = ['samityId', 
							   'samityCode',
							   'branchId', 
							   'newBranchId',
							   'transferDate',
							   'createdDate'
							  ];
							  
	}