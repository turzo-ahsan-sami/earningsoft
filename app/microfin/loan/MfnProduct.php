<?php

	namespace App\microfin\loan;

	use Illuminate\Database\Eloquent\Model;

	class MfnProduct extends Model {

		public $timestamps = false;

		protected $table = 'mfn_loans_product';

		/*protected $casts = [
	    					'eligibleRepaymentFrequencyId' => 'array'
	    				   ];*/

		protected $fillable = ['name',
							   'shortName',
							   'code',
							   'productCategoryId',
							   'fundingOrganizationId',
							   'startDate',
							   'isPrimaryProduct',
							   'minLoanAmount',
							   'maxLoanAmount',
							   'avgLoanAmount',
							   'gracePeriodId',
							   'yearsEligibleWriteOffId',
							   'isInsuranceApplicable',
							   'insuranceCalculationMethodId',
							   'insuranceAmount',
							   'principalAmountOfLoan',
							   'maxInsuranceAmount',
							   'maxLoanAmountForInsuranceApplicable',
							   'mandatorySavingsAmountOfProposedLoanAmount',
							   'isMultipleLoanAllowed',
							   'installmentNum',
							   'productTypeId',
							   'repaymentFrequencyId',
							   'interestPaymentModeId',
							   'enforcedInstallmentAmountOnlyServiceCharge',
							   'serviceChargeTakenInitially',
							   'monthlyRepaymentMode',
							   'repaymentCollectionDay',
							   'repaymentCollectionWeek',
							   'extraOptions',
							   'eligibleRepaymentFrequencyId',
							   'formFee',
							   'healthServiceFee',
							   'riskInsurance',
							   'additionalFeeCalculationMethodId',
							   'additionalFee',
								 'additionalFeeOldLoanee',
							   'maxLoanAmountForAdditionalFeeApplicable',
							   'admissionFee',
							   'createdDate'
							  ];

		public function scopeActive($query) {

		    return $query->where('status', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopePrimaryProduct($query) {

		    return $query->where('isPrimaryProduct', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopeOthersProduct($query) {

		    return $query->where('isPrimaryProduct', '=', 0)->where('softDel', '!=', '1');
		}

		public function scopeRegular($query) {

		    return $query->where('productTypeId', '=', 1)->where('softDel', '!=', '1');
		}

		public function scopeOthers($query) {

		    return $query->where('productTypeId', '=', 2)->where('softDel', '!=', '1');
		}

	}
