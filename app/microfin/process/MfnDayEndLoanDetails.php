<?php

	namespace App\microfin\process;

	use Illuminate\Database\Eloquent\Model;

	class MfnDayEndLoanDetails extends Model {

		public $timestamps = false;

		protected $table = 'mfn_day_end_loan_details';

		protected $fillable = 	[
								'dayEndIdFk',
								'branchIdFk',
								'fundingOrganizationId',
								'genderTypeId',
								'date',
								'productIdFk',
								'primaryProductIdFk',
								'paymentMode',
								'bankHeadId',
								'disbursmentAmount',
								'principaleCollectionAmount',
								'interestCollectionAmount',
								'rebateAmount',
								'insuranceAmount',
								'insuranceAmount2',
								'additionalFee',
								'loanProposalFee',
								'healthFee',
								'proposalAddiFee',
								'riskInsCollection',
								'riskInsRefund',
								'transferIn',
								'transferOut',
								'receiptVoucherStatus',
								'paymentVoucherStatus',
								'journalVoucherStatus',
								'totalRecoverable',
								'totalProjectedRecovery',
								'totalRecovery',
								'isMigrated',
								'createdAt'								
								];
		
	}