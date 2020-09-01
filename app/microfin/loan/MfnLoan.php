<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Support\Facades\Auth;

	use App\Traits\GetSoftwareDate;

	class MfnLoan extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loan';

		protected $fillable = ['loanTypeId',
							   'loanCode',
							   'memberIdFk',
							   'disbursementDate',
							   'productIdFk',
							   'primaryProductIdFk',
							   'branchIdFk',
							   'samityIdFk',
							   'loanApplicationNo',
							   'repaymentFrequencyIdFk',
							   'loanRepayPeriodIdFk',
							   'firstRepayDate',
							   'lastInstallmentDate',
							   'loanCycle',
							   'loanAmount',
							   'repaymentNo',
							   'insuranceAmount',
							   'loanSubPurposeIdFk',
							   'folioNum',
							   'interestMode',
							   'interestCalculationMethodId',
							   'interestCalculationMethod',
							   'interestRate',
							   'interestRateIndex',
							   'interestDiscountAmount',
							   'totalRepayAmount',
							   'interestAmount',
							   'installmentAmount',
							   'paymentTypeIdFk',
							   'ledgerId',
							   'chequeNo',
							   'chequeDate',
							   'extraInstallmentAmount',
							   'actualInstallmentAmount',
							   'lastInstallmentAmount',
							   'actualNumberOfInstallment',
							   'additionalFee',
							   'loanFormFee',
							   'note',
							   'firstGuarantorName',
							   'firstGuarantorRelation',
							   'firstGuarantorAddress',
							   'firstGuarantorContact',
							   'secondGuarantorName',
							   'secondGuarantorRelation',
							   'secondGuarantorAddress',
							   'secondGuarantorContact',
							   'isSelfEmployment',
							   'FEFullTimeMale',
							   'FEPartTimeMale',
							   'FEFullTimeMaleWage',
							   'FEFullTimeFemale',
							   'FEPartTimeFemale',
							   'FEFullTimeFemaleWage',
							   'OFEFullTimeMale',
							   'OFEPartTimeMale',
							   'OFEPartTimeMaleWage',
							   'OFEFullTimeFemale',
							   'OFEPartTimeFemale',
							   'OFEPartTimeFemaleWage',
							   'businessName',
							   'businessLocation',
							   'businessType',
							   'isFromOpening',
							   'guarantorImage',
							   'guarantorSignatureImage',
							   'guarantorNidImage',
							   'entryBy',
							   'createdDate'
							  ];

		public function scopeActive($query) {
		    
		    return $query->where('status', '=', 1)->where('softDel', '!=', 1);
		}

		public function scopeBranchWise($query) {
		    
		    return $query->where('branchIdFk', '=', Auth::user()->branchId);
		}

		public function scopeLoanCompleted($query) {
			
			return $query->where([['softDel', '=', 0], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', ''], ['loanCompletedDate', '<=', GetSoftwareDate::getSoftwareDate()]]);
			
		    // return $query->where('isLoanCompleted', '=', 1);
		}

		public function scopeLoanIncompleted($query) {
		    
		    return $query->where('isLoanCompleted', '=', 0);
		}

		public function scopeRegularLoan($query) {
		    
		    return $query->where('loanTypeId', '=', 1);
		}

		public function scopeOneTimeLoan($query) {
		    
		    return $query->where('loanTypeId', '=', 2);
		}
							  
	}