<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnDayEndAdjustmentDetails extends Model {

		public $timestamps = false;

		protected $table = 'mfn_day_end_adjustment_details';

		protected $fillable = 	[
								'branchIdFk',
								'dayEndIdFk',
								'fundingOrgIdFk',
								'genderTypeId',
								'date',
								'productIdFk',
								'primaryProductIdFk',
								'loanProductIdFk',
								'transactionType',
								'savingWithdrawAmount',
								'loanCollectionAmount',
								'loanPrincipalAmount',
								'loanInterestAmount',
								'journalVoucherStatus',
								'createdAt'
								];
		
	}