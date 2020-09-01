<?php

	namespace App\microfin\savings;

	use Illuminate\Database\Eloquent\Model;

	class MfnSavingsAccount extends Model {

		public $timestamps = false;

		protected $table = 'mfn_savings_account';

		protected $fillable = ['savingsCode', 
							   'accountOpeningDate',
							   'savingsProductIdFk',
							   'savingsInterestRate',
							   'memberIdFk',
							   'branchIdFk',
							   'samityIdFk',
							   'workingAreaIdFk',
							   'depositTypeIdFk',
							   'autoProcessAmount',
							   'savingCycle',
							   'initialAmount',
							   'transactionType',
							   'entryByEmployeeIdFk',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', 1);
		}
	}