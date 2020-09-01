<?php

	namespace App\microfin\member;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	class MfnMemberClosing extends Model {

		public $timestamps = false;
		
		protected $table = 'mfn_member_closing';
		
		protected $fillable = ['memberIdFk',
							   'primaryProductIdFk',
							   'closingDate',
							   'note',
							   'transactionMemoir',
							   'branchIdFk',
							   'samityIdFk',
							   'isTransferred',
							   'closedByFk',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchIdFk', '=', Auth::user()->branchId);
		}
							  
	}