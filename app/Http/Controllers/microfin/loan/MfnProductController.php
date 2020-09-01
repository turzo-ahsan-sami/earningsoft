<?php

	namespace App\Http\Controllers\microfin\loan;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnProduct;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnProductController extends Controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70),
				array('Name', 0),
				array('Short Name', 0),
				array('Loan Product Code', 0),
				array('Funding Organization', 0),
				array('Product Category', 0),
				array('Product Type', 0),
				array('Interest Rate', 0),
				array('Start Date', 0),
				array('Total Loan', 0),
				array('Action', 80)
			);
		}

		public function index() {

			$damageData = array(
				'TCN'	       =>  $this->TCN,
				'loanProduct'  =>  $this->MicroFinance->getActiveLoanProduct()
			);

			return view('microfin.loan.product.viewProduct', ['damageData' => $damageData]);
		}

		public function addProduct() {

			$damageData = array(
				'productCategory'             =>  $this->MicroFinance->getProductCategoryList(),
				'fundingOrganization'         =>  $this->MicroFinance->getFundingOrganizationList(),
				'boolean'                  	  =>  $this->MicroFinance->getBooleanOptions(),
				'gracePeriod'          		  =>  $this->MicroFinance->getGracePeriodList(),
				'yearsEligibleWriteOff'       =>  $this->MicroFinance->getYearsEligibleWriteOffList(),
				'insuranceCalculationMethod'  =>  $this->MicroFinance->getInsuranceCalculationMethodList(),
				'loanProductType'             =>  $this->MicroFinance->getLoanProductTypeDefaultOption(),
				'repaymentFrequency'          =>  $this->MicroFinance->getRepaymentFrequencyList(),
				'monthlyRepaymentMode'        =>  $this->MicroFinance->getMonthlyRepaymentModeList(),
				'extraOptions'                =>  $this->MicroFinance->getExtraOptionsList(),
				'interestPaymentMode'         =>  $this->MicroFinance->getInterestPaymentModeOptions(),
				'repaymentCollectionDay'      =>  $this->MicroFinance->getSamityFixedDate(),
				'repaymentCollectionWeek'     =>  $this->MicroFinance->getMonthlyCollectionWeek(),
			);

			// dd($damageData);

			return view('microfin.loan.product.addProduct', ['damageData' => $damageData]);
		}

		public function loadLoanProductType(Request $req) {

			if($req->id==1):
				$req->id = $req->id;
				$isMultipleLoanAllowed = array( 0 => 'No');
			endif;

			if($req->id==0):
				$req->id = 2;
				$isMultipleLoanAllowed = array( 1 => 'Yes');
			endif;

			$loanProductTypeOB = DB::table('mfn_loan_product_type')->where('id', $req->id)->pluck('name', 'id')->all();

			$data = array(
				'loanProductType' 		 =>  $loanProductTypeOB,
				'isMultipleLoanAllowed'  =>  $isMultipleLoanAllowed
			);

			return response::json($data);
		}

		public function addItem(Request $req) {
			// dd('OK');
			$rules = array(
				'name'		 				   =>  'required|unique:mfn_loans_product,name',
				'shortName'  				   =>  'required|unique:mfn_loans_product,shortName',
				'code' 		 				   =>  'required|unique:mfn_loans_product,code',
				'eligibleRepaymentFrequencyId' =>  'required',
			);

			$attributesNames = array(
				'eligibleRepaymentFrequencyId'  =>  'eligible repayment frequency',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['shortName' => str_replace(' ', '-', $req->shortName)]);
				$req->request->add(['branchId' => Session::get('branchId')]);

				//	MANUFACTURING $eligibleRepaymentFrequencyId ARRAY.
				$i=1;
				$eligibleRepaymentFrequencyId = [];

				foreach($req->eligibleRepaymentFrequencyId as $key => $val):
					$eligibleRepaymentFrequencyId[$i] = $val;
					$i++;
				endforeach;

				//	MANUFACTURING $gracePeriodForRepaymentFrequency ARRAY.
				$i=1;
				$gracePeriodForRepaymentFrequency = [];

				foreach($req->gracePeriodForRepaymentFrequency as $key => $val):
					$gracePeriodForRepaymentFrequency[$i] = $val;
					$i++;
				endforeach;

				//	MANUFACTURING $repaymentFrequencyWithGracePeriod ARRAY.
				$i=1;
				$repaymentFrequencyWithGracePeriod = [];

				foreach($req->eligibleRepaymentFrequencyId as $key => $val):
					$repaymentFrequencyWithGracePeriod[$val] = $gracePeriodForRepaymentFrequency[$val];
					$i++;
				endforeach;

				dd($req->eligibleRepaymentFrequencyId, $gracePeriodForRepaymentFrequency, $repaymentFrequencyWithGracePeriod, $req);

				$req->request->add(['eligibleRepaymentFrequencyId' => json_encode($repaymentFrequencyWithGracePeriod)]);

				//	CHANGE THE START DATE FORMAT.
				$startDate = date_create($req->startDate);
				$req->request->add(['startDate' => date_format($startDate, "Y-m-d")]);
				// $create = MfnProduct::create($req->all());

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('loanProductCreateSuccess'),
					//'eligibleRepaymentFrequencyId'       =>  $eligibleRepaymentFrequencyId,
					//'gracePeriodForRepaymentFrequency'   =>  $gracePeriodForRepaymentFrequency,
					//'repaymentFrequencyWithGracePeriod'  =>  $repaymentFrequencyWithGracePeriod
				);

				return response::json($data);
			}
		}

		public function detailsProduct($productId) {

			$productDetailsTCN = [
				'name'										  =>  'Name:',
				'shortName'									  =>  'Short Name:',
				'loanProductCode'							  =>  'Loan Product Code:',
				'fundingOrganization'						  =>  'Funding Organization:',
				'loanProductCategory'						  =>  'Loan Product Category:',
				'isPrimaryProduct'							  =>  'Is Primary Product:',
				'startDate'									  =>  'Start Date:',
				'isMultipleLoanAllowed'						  =>  'Is Multiple Loan Allowed:',
				'numberOfInstallment'						  =>  'Number of Installment:',
				'loanProductType'							  =>  'Loan Product Type:',
				'repaymentFrequency'					      =>  'Repayment Frequency:',
				'isServiceChargeTakenInitially'				  =>  'Is Service Charge Taken Initially:',
				'eligibleRepaymentFrequency'				  =>  'Eligible Repayment Frequency:',
				'isInsuranceApplicable'						  =>  'Is Insurance Applicable:',
				'insuranceCalculationMethod'				  =>  'Insurance Calculation Method:',
				'insuranceAmount'							  =>  'Insurance Amount:',
				'monthlyCollectionMode'						  =>  'Mode of Monthly Collection:',
				'maxInsuranceAmount'						  =>  'Maximum Insurance Amount:',
				'maxLoanAmountForInsuranceApplicable'		  =>  'Maximum Loan Amount For Insurance Applicable:',
				'monthlyCollectionDayOrWeek'				  =>  'Monthly Collection Day Or Week:',
				'yearsToEligibleWriteOff'					  =>  'Years to Eligible Write Off:',
				'minLoanAmount'								  =>  'Minimum Loan Amount:',
				'formFee'									  =>  'Form Fee:',
				'maxLoanAmount'								  =>  'Maximum Loan Amount:',
				'healthServiceFee'							  =>  'Health Service Fee:',
				'avgLoanAmount'								  =>  'Average Loan Amount:',
				'riskInsurance'								  =>  'Risk Insurance:',
				'additionalFee'								  =>  'Additional Fee:',
				'maxLoanAmountForAdditionalFeeApplicable'	  =>  'Maximum Loan Amount for Additional Fee Applicable: ',
				'memberAdmissionFee'						  =>  'Member Admission Fee:',
				'mandatorySavingsAmountOfProposedLoanAmount'  =>  'Mandatory Savings Amount()% of Proposed Loan Amount:',
			];

			$interestRateDetailsTCN = array(
				array('IR', 100),
				array('Dec. P', 0),
				array('MOI', 0),
				array('Installment No.', 0),
				array('E. Date', 0),
				array('ENOI', 0),
				array('LPDF', 0),
				array('Last Update Date', 0),
				array('Status', 70),
				array('Action', 80)
			);

			$productDetails = $this->MicroFinance->getLoanProductDetails($productId);
			// dd($productDetails->interestRateIndex);
			// dd($productDetails->eligibleRepaymentFrequencyId);
			$monthlyRepaymentMode = ($productDetails->monthlyRepaymentMode==0)?'':$this->MicroFinance->getNameValueForId($table='mfn_monthly_repayment_mode', $productDetails->monthlyRepaymentMode);
			$eligibleRepaymentFrequency =  $this->MicroFinance->getMultipleNameValueForFirstMultipleId($table='mfn_repayment_frequency', $productDetails->eligibleRepaymentFrequencyId);
			$yearsToEligibleWriteOff = $productDetails->yearsEligibleWriteOffId>0?$this->MicroFinance->getNameValueForId($table='mfn_years_eligible_write_off', $productDetails->yearsEligibleWriteOffId):0;
			// dd($productDetails->repaymentFrequencyId);
			// dd($this->MicroFinance->getNameValueForId($table='mfn_repayment_frequency', $productDetails->repaymentFrequencyId));
			// $interestRatesIndexArr = $this->MicroFinance->getInterestRate($productId)->pluck('interestRateIndex')->toArray();
			$disbursementIndexArr = DB::table('mfn_loan')
									->where('productIdFk', $productId)
									->distinct('interestRateIndex')
									->pluck('interestRateIndex')
									->toArray();
			// dd($interestRateDetailsTCN);

			$damageData = array(
				'productDetailsTCN'  			 =>  $productDetailsTCN,
				'interestRateDetailsTCN'  		 =>  $interestRateDetailsTCN,
				'productDetails'     			 =>  array(
					'id'	                 					  =>  $productDetails->id,
					'name'	                 					  =>  $productDetails->name,
					'shortName'	             					  =>  $productDetails->shortName,
					'loanProductCode'	     					  =>  $productDetails->code,
					'fundingOrganization'	 					  =>  $this->MicroFinance->getNameValueForId($table='mfn_funding_organization', $productDetails->fundingOrganizationId),
					'loanProductCategory'	 					  =>  $this->MicroFinance->getNameValueForId($table='mfn_loans_product_category', $productDetails->productCategoryId),
					'isPrimaryProduct'		 					  =>  $this->MicroFinance->getBooleanStatus($productDetails->isPrimaryProduct),
					'startDate'				 					  =>  $this->MicroFinance->getMicroFinanceDateFormat($productDetails->startDate),
					'isMultipleLoanAllowed'	 					  =>  $this->MicroFinance->getBooleanStatus($productDetails->isMultipleLoanAllowed),
					'numberOfInstallment'	 					  =>  $productDetails->installmentNum,
					'loanProductType'	     				      =>  $this->MicroFinance->getNameValueForId($table='mfn_loan_product_type', $productDetails->productTypeId),
					'repaymentFrequency'	 					  =>  $this->MicroFinance->getNameValueForId($table='mfn_repayment_frequency', $productDetails->repaymentFrequencyId),
					'isServiceChargeTakenInitially'		 		  =>  $this->MicroFinance->getBooleanStatus($productDetails->serviceChargeTakenInitially),
					'eligibleRepaymentFrequency'	     		  =>  $eligibleRepaymentFrequency,
					'isInsuranceApplicable'	 					  =>  $this->MicroFinance->getBooleanStatus($productDetails->isInsuranceApplicable),
					'insuranceCalculationMethod'	 			  =>  $this->MicroFinance->getNameValueForId($table='mfn_insurance_calculation_method', $productDetails->insuranceCalculationMethodId),
					'insuranceAmount'	             			  =>  $productDetails->insuranceAmount,
					'monthlyCollectionMode'	 					  =>  $monthlyRepaymentMode,
					'maxInsuranceAmount'	             		  =>  $productDetails->maxInsuranceAmount,
					'maxLoanAmountForInsuranceApplicable'	 	  =>  $productDetails->maxLoanAmountForInsuranceApplicable,
					'yearsToEligibleWriteOff'	 				  =>  $yearsToEligibleWriteOff,
					'minLoanAmount'	 							  =>  $productDetails->minLoanAmount,
					'formFee'	 								  =>  $productDetails->formFee,
					'maxLoanAmount'	 							  =>  $productDetails->maxLoanAmount,
					'healthServiceFee'	 						  =>  $productDetails->healthServiceFee,
					'avgLoanAmount'	 							  =>  $productDetails->avgLoanAmount,
					'riskInsurance'	 							  =>  $productDetails->riskInsurance,
					'additionalFee'	 							  =>  $productDetails->additionalFee,
					'maxLoanAmountForAdditionalFeeApplicable'	  =>  $productDetails->maxLoanAmountForAdditionalFeeApplicable,
					'memberAdmissionFee'	 					  =>  $productDetails->admissionFee,
					'mandatorySavingsAmountOfProposedLoanAmount'  =>  $productDetails->mandatorySavingsAmountOfProposedLoanAmount
				),
				'boolean'            			 =>  $this->MicroFinance->getBooleanOptions(),
				'loanInterestRate'  			 =>  $this->MicroFinance->getInterestRate($productId),
				'disbursementIndexArr'  		 =>  $disbursementIndexArr,
				'interestDeclinePeriodOptions'   =>  $this->MicroFinance->getInterestDeclinePeriodOptions(),
				'interestModeOptions'  			 =>  $this->MicroFinance->getInterestModeOptions()
			);
			// dd($this->MicroFinance->getInterestRate($productId));

			return view('microfin.loan.product.detailsProduct ', ['damageData' => $damageData]);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: EDIT LOAN PRODUCT.
		|--------------------------------------------------------------------------
		*/
		public function updateProduct($productId) {

			$loanProductDetails = $this->MicroFinance->getLoanProductDetails($productId);

			$damageData = array(
				'product'  	   	  		      =>  $loanProductDetails,
				'productCategory'             =>  $this->MicroFinance->getProductCategoryList(),
				'fundingOrganization'         =>  $this->MicroFinance->getFundingOrganizationList(),
				'boolean'                  	  =>  $this->MicroFinance->getBooleanOptions(),
				'gracePeriod'          		  =>  $this->MicroFinance->getGracePeriodList(),
				'yearsEligibleWriteOff'       =>  $this->MicroFinance->getYearsEligibleWriteOffList(),
				'insuranceCalculationMethod'  =>  $this->MicroFinance->getInsuranceCalculationMethodList(),
				'loanProductType'             =>  $this->MicroFinance->getLoanProductTypeDefaultOption(),
				'repaymentFrequency'          =>  $this->MicroFinance->getRepaymentFrequencyList(),
				'monthlyRepaymentMode'        =>  $this->MicroFinance->getMonthlyRepaymentModeList(),
				'extraOptions'                =>  $this->MicroFinance->getExtraOptionsList(),
				'interestPaymentMode'         =>  $this->MicroFinance->getInterestPaymentModeOptions(),
				'repaymentCollectionDay'      =>  $this->MicroFinance->getSamityFixedDate(),
				'repaymentCollectionWeek'     =>  $this->MicroFinance->getMonthlyCollectionWeek(),
			);

			return view('microfin.loan.product.editProduct', $damageData);
		}

		public function updateItem(Request $req) {

			//	UPDATE MEMBER.
			$product = MfnProduct::find($req->productId);

			//	MEMBER.
			$product->name = $req->name;
			$product->save();

			$data = array(
				'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   =>  MicroFinance::getMessage('memberUpdateSuccess'),
			);

			return response::json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE LOAN PRODUCT.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {

			//	CHECK IF THERE IS ANY LOAN PRODUCT IS IN USED.
			$loanProductUsed = $this->MicroFinance->checkLoanProductUsed($req->id);

			//	CHECK IF THERE IS ANY LOAN PRODUCT INTEREST RATE EXISTS FOR THIS LOAN PRODUCT.
			$loanProductInterestRateExists = $this->MicroFinance->checkLoanProductInterestRateExists($req->id);

			if($loanProductUsed==0 && $loanProductInterestRateExists==0):
				$lockDelete = 0;
			else:
				$lockDelete = 1;
			endif;

			if($lockDelete==0):
				$loanProductDelete = $this->MicroFinance->softDelete($req->id, ['mfn_loans_product'], ['id']);
			endif;

			$data = array(
				'responseTitle' =>  $lockDelete==0?MicroFinance::getMessage('msgSuccess'):MicroFinance::getMessage('msgWarning'),
				'responseText'  =>  $lockDelete==0?MicroFinance::getMessage('loanProductDelSuccess'):MicroFinance::getMessage('loanProductDelFailed'),
			);

			return response()->json($data);
		}

	}
