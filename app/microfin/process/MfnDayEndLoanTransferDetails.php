<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnDayEndLoanTransferDetails extends Model {

		public $timestamps = false;

		protected $table = 'mfn_day_end_loan_transfer_details';

		protected $fillable = 	[
								'branchIdFk',
								'dayEndIdFk',
								'branchIdFkTo',
								'fundingOrgIdFk',
								'genderTypeId',
								'date',
								'loanProductIdFk',
								'oldPrimaryProductIdFk',
								'newPrimaryProductIdFk',
								'loanAmount',
								'principalAmount',
								'amount',
								'transferType',
								'journalVoucherStatus',
								'createdAt'								
								];
		
	}