<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnDayEndSavingsDetails extends Model {

		public $timestamps = false;

		protected $table = 'mfn_day_end_saving_details';

		protected $fillable = 	[
								'date',
								'branchIdFk',
								'fundingOrganizationIdFk',
								'genderTypeId',
								'date',
								'productIdFk',
								'primayProductIdFk',
								'transactionType',
								'paymentMode',
								'bankHeadId',
								'savingDepositAmount',
								'savingWithdrawAmount',
								'savingInterestAmount',
								'savingClosingAmount',
								'sktAmount',
								'transferDeposit',
								'transferWithdraw',
								'receiptVoucherStatus',
								'paymentVoucherStatus',
								'journalVoucherStatus',
								'isMigrated',
								'createdAt'
								];
		
	}