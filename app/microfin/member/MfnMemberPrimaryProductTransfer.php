<?php

	namespace App\microfin\member;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnMemberPrimaryProductTransfer extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loan_primary_product_transfer';

		protected $casts = [
	    					'savingsRecord'   => 'array'
	    				   ];

		protected $fillable = ['memberIdFk', 
							   'oldPrimaryProductFk',
							   'newPrimaryProductFk', 
							   'totalTransferAmount',
							   'savingsRecord',
							   'transferDate',
							   'note',
							   'entryBy',
							   'branchIdFk',
							   'samityIdFk',
							   'viaMemberSamityTransfer',
							   'createdDate'
							  ];

		
		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchIdFk', '=', Auth::user()->branchId);
		}
							  
	}