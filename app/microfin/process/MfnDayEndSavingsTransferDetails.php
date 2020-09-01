<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnDayEndSavingsTransferDetails extends Model {

		public $timestamps = false;

		protected $table = 'mfn_day_end_savings_transfer_details';

		protected $fillable = 	[
								'branchIdFk',
								'branchIdFkTo',
								'fundingOrgIdFk',
								'genderTypeId',
								'date',
								'savingsProductIdFk ',
								'oldPrimaryProductIdFk',
								'newPrimaryProductIdFk',
								'amount',
								'transferType',
								'journalVoucherStatus',
								'createdAt'
								];
		
	}