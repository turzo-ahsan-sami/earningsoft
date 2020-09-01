<?php

	namespace App\microfin\member;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnMemberSamityTransfer extends Model {

		public $timestamps = false;
		
		protected $table = 'mfn_member_samity_transfer';

		protected $casts = [
	    					'savingsRecord'   => 'array'
	    				   ];
		
		protected $fillable = ['memberIdFk',
							   'previousMemberCodeFk',
							   'newMemberCodeFk',
							   'previousPrimaryProductIdFk',
							   'newPrimaryProductIdFk',
							   'previousSamityIdFk',
							   'newSamityIdFk',
							   'branchIdFk',
							   'totalTransferAmount',
							   'savingsRecord',
							   'transferDate',
							   'entryByFk',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchIdFk', '=', Auth::user()->branchId);
		}
							  
	}