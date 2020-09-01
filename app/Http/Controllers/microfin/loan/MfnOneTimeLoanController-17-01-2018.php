<?php

	namespace App\Http\Controllers\microfin\loan;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnLoan;
	use App\microfin\loan\MfnProduct;
	use App\microfin\loan\MfnLoanSchedule;
	use App\microfin\loan\MfnLoanReschedule;
	use App\microfin\loan\MfnLoanRepayPeriod; 
	use App\microfin\loan\MfnFees;
	use App\microfin\settings\MfnLoanProductInterestRate;
	use App\microfin\configuration\openingBalance\MfnloanOpeningBalance;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Traits\GetSoftwareDate;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnOneTimeLoanController extends Controller {

		protected $MicroFinance;

		use GetSoftwareDate;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Loan Code', 0),
				array('Member Code', 100),
				array('Member Name', 0),
				array('Loan Amount', 0),
				array('Int. Rate', 80),
				array('Disburse Date', 0),
				array('Repay Date', 0),
				array('Auth. Status', 70),
				array('Loan Status', 0),
				array('Entry By', 0),
				array('Action', 80)
			);	
		}

		public function index() {

			$damageData = array(
				'TCN'               =>  $this->TCN,
				'oneTimeLoans'      =>  $this->MicroFinance->getActiveOneTimeLoan(),
				'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
				'MicroFinance'      =>  $this->MicroFinance
			);

			return view('microfin.loan.oneTimeLoan.viewOneTimeLoan', ['damageData' => $damageData]);
		}

		public function addOneTimeLoan() {

			$damageData = array(
				'member'  			  =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
				//'disbursementDate'    =>  Carbon::today()->toDateString(),
				'disbursementDate'    =>  GetSoftwareDate::getSoftwareDate(),
				'repaymentFrequency'  =>  $this->MicroFinance->getRepaymentFrequencyOptions(),
				'loanRepayPeriod'     =>  $this->MicroFinance->getLoanRepayPeriod(),
				'paymentType'         =>  $this->MicroFinance->getPaymentType(),
				'loanPurpose'         =>  $this->MicroFinance->getLoanPurpose(),
				'boolean'  			  =>  $this->MicroFinance->getBooleanOptions()
			);

			return view('microfin.loan.oneTimeLoan.addOneTimeLoan', ['damageData' => $damageData]);
		}

		public function loadLoanProductList(Request $req) {

			/*$checkRegularLoanCompleted = MfnLoan::where('memberIdFk', $req->memberId)->loanCompleted()->count();
			$newRegularLoan = MfnLoan::where('memberIdFk', $req->memberId)->count();

			if($checkRegularLoanCompleted==1 || $newRegularLoan==0)
				$loanProduct = $this->MicroFinance->getActiveLoanOthersProductOptions();
			else
				$loanProduct = array();*/

			//	GET MEMBER ADMISSION DATE.
			$memberOB = DB::table('mfn_member_information')->where('id', $req->memberId)->select('admissionDate')->first();

			$data = array(
				//'loanProduct'        =>  $loanProduct,
				'loanProduct'  		   =>  $this->MicroFinance->getActiveLoanOthersProductOptions(),
				'memberAdmissionDate'  =>  $memberOB->admissionDate,
				'softwareDate' 		   =>  GetSoftwareDate::getSoftwareDate()
			);
			
			return response::json($data); 
		}

		public function loadOneTimeLoanSupportData(Request $req) {

			//	START FOR GENERATE LOAN REPAY DATE.
			$getLoanRepayPeriod = DB::table('mfn_loan_repay_period')->where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
							   ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							   ->where('mfn_member_information.id', $req->memberId)
							   ->select('mfn_samity.samityDayId AS samityDayId')
							   ->first();

			$dt = Carbon::parse($req->disbursementDate);
			$loanRepayDate = $dt->addMonths($getLoanRepayPeriod->inMonths)->toDateString();
			//	END FOR GENERATE LOAN REPAY DATE.

			$getMemberCode = $this->MicroFinance->getSingleValueForId($table='mfn_member_information', $req->memberId, 'code');
			$regularLoanShortName = $this->MicroFinance->getSingleValueForId($table='mfn_loans_product', $req->id, 'shortName');
			$regularLoanSLNum = $this->MicroFinance->getRegularLoanSLNum($req->memberId, $req->id);

			$loanCode = $regularLoanShortName . '.' . $getMemberCode . '.' . $regularLoanSLNum;

			$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->id)
													    ->where('installmentNum', MfnLoanProductInterestRate::where('loanProductId', $req->id)->max('installmentNum'))
													    ->select('interestModeId', 'interestRate', 'interestCalculationMethodShortName', 'installmentNum', 'interestRateIndex', 'repaymentFrequencyId')
													    ->first();

			$interestMode = $this->MicroFinance->getInterestModeOptions();

			$loanProductOB = MfnProduct::where('id', $req->id)->select('maxLoanAmount', 'installmentNum', 'additionalFee', 'formFee', 'maxInsuranceAmount')->first();

			$loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			if($loanRepayPeriodOB->inMonths==12)
				$yearCount = 1;
			if($loanRepayPeriodOB->inMonths==24)
				$yearCount = 2;
			if($loanRepayPeriodOB->inMonths==36)
				$yearCount = 3;

			//	INTEREST AMOUNT CALCULATION.
			$interestAmount = sprintf("%.2f", ($interestRateOB->interestRateIndex / 100) * $loanProductOB->maxLoanAmount * 365 * $yearCount);
			$totalRepayAmount = $loanProductOB->maxLoanAmount + $interestAmount;

			$repaymentFrequencyWiseRate = [
				'1'	 =>  25,
				'2'  =>  96
			];

			$data = array(
				'loanRepayDate'  			 =>  $loanRepayDate,
				'loanProduct' 	 			 =>  $this->MicroFinance->getActiveLoanOthersProductOptions(),
				'repaymentFrequency'   		 =>  $interestRateOB->repaymentFrequencyId,
				'loanCode'    	 			 =>  $loanCode,
				'loanCycle'   	 			 =>  $regularLoanSLNum,
				'loanAmount'   	 			 =>  sprintf("%.2f", $loanProductOB->maxLoanAmount),
				'insuranceAmount'   	 	 =>  $loanProductOB->maxInsuranceAmount,
				'interestMode'   			 =>  $interestMode[$interestRateOB->interestModeId] . ' (Daily)',
				'interestCalculationMethod'  =>  $interestRateOB->interestCalculationMethodShortName,
				'interestRate'  			 =>  $interestRateOB->interestRate,
				'installmentNum'  			 =>  $interestRateOB->installmentNum,
				'totalRepayAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'interestAmount'  			 =>  $interestAmount,
				'installmentAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'additionalFee'  			 =>  $loanProductOB->additionalFee,
				'formFee'  			 		 =>  $loanProductOB->formFee,
			);
			
			return response::json($data); 
		}

		public function loadLoanRepayDate(Request $req) {

			$getLoanRepayPeriod = DB::table('mfn_loan_repay_period')->where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
							   ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							   ->where('mfn_member_information.id', $req->memberId)
							   ->select('mfn_samity.samityDayId AS samityDayId')
							   ->first();

			$dt = Carbon::parse($req->disbursementDate);
			$loanRepayDate = $dt->addMonths($getLoanRepayPeriod->inMonths)->toDateString();

			//	IF LOAN REPAY DATE DOESN'T MATCHES TO SAMITY DAY, THEN SET LOAN REPAY DATE TO NEXT SAMITY DAY.
			if(date('l', strtotime($loanRepayDate))!=$this->MicroFinance->getSamityDayNameValue($samityDayIdOB->samityDayId))
				$loanRepayDate = $this->MicroFinance->getNextSamityDate($dt->toDateString(), $samityDayIdOB->samityDayId);

			$data = array(
				'loanRepayDate'  =>  $loanRepayDate
			);
			
			return response::json($data); 
		}

		public function loadOneLoanSupportDataRepayPeriodWise(Request $req) {

			//	START FOR GENERATE LOAN REPAY DATE.
			/*$getLoanRepayPeriod = DB::table('mfn_loan_repay_period')->where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
							   ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							   ->where('mfn_member_information.id', $req->memberId)
							   ->select('mfn_samity.samityDayId AS samityDayId')
							   ->first();

			$dt = Carbon::parse($req->disbursementDate);
			$loanRepayDate = $dt->addMonths($getLoanRepayPeriod->inMonths)->toDateString();
			//	END FOR GENERATE LOAN REPAY DATE.

			//	IF LOAN REPAY DATE DOESN'T MATCHES TO SAMITY DAY, THEN SET LOAN REPAY DATE TO NEXT SAMITY DAY.
			if(date('l', strtotime($loanRepayDate))!=$this->MicroFinance->getSamityDayNameValue($samityDayIdOB->samityDayId))
				$loanRepayDate = $this->MicroFinance->getNextSamityDate($dt->toDateString(), $samityDayIdOB->samityDayId);

			$noOfRepaymentOB = MfnProduct::where('id', $req->productId)->select('installmentNum')->first();
			$noOfRepaymentArr = explode(',', $noOfRepaymentOB->installmentNum);

			$noOfRepayment = [];

			//	LOAN REPAY PERIOD IN MONTH.
			$loanRepayPeriod = [
				1  =>  12,
				2  =>  24,
				3  =>  36
			];

			//	GET NO. OF REPAYMENT FOR WEEKLY REPAYMENT FREQUENCY.
			if($req->repaymentFrequencyId==1):
				$noOfRepayment[$req->repaymentNo] = $req->repaymentNo;
			endif;
			
			//	GET NO. OF REPAYMENT FOR MONTHLY REPAYMENT FREQUENCY.
			if($req->repaymentFrequencyId==2):
				$noOfRepayment[$loanRepayPeriod[$req->loanRepayPeriodId]] = $loanRepayPeriod[$req->loanRepayPeriodId];
				$req->repaymentNo = $loanRepayPeriod[$req->loanRepayPeriodId];
			endif;

			$supportData = $this->MicroFinance->regularLoanSupportDataRepaymentNumberWise($req->productId, $req->loanAmount, $req->repaymentNo, $req->repaymentFrequencyId);

			$data = array(
				'loanRepayDate'  			 =>  $loanRepayDate,
				'repaymentFrequency'   		 =>  $supportData->repaymentFrequencyId, 
				'repaymentNo'   	 		 =>  $noOfRepayment,
				'insuranceAmount'  			 =>  $supportData->insuranceAmount,
				'interestMode'   			 =>  $supportData->interestMode . ' (Yearly)',
				'interestCalculationMethod'  =>  $supportData->interestCalculationMethodShortName,
				'interestRate'  			 =>  $supportData->interestRate,
				'installmentNum'  			 =>  $supportData->installmentNum,
				'totalRepayAmount'  		 =>  $supportData->totalRepayAmount,
				'interestAmount'  			 =>  $supportData->interestAmount,
				'actualInstallmentNum'  	 =>  $supportData->installmentNum,
				'actualInstallmentAmount'  	 =>  $supportData->actualInstallmentAmount,
				'installmentAmount'  		 =>  $supportData->installmentAmount,
				'extraInstallmentAmount'  	 =>  $supportData->extraInstallmentAmount,
				'lastInstallmentAmount'  	 =>  $supportData->lastInstallmentAmount,
				'supportData'  				 =>  $supportData
			);*/


			//	START FOR GENERATE LOAN REPAY DATE.
			$getLoanRepayPeriod = DB::table('mfn_loan_repay_period')->where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
							   ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							   ->where('mfn_member_information.id', $req->memberId)
							   ->select('mfn_samity.samityDayId AS samityDayId')
							   ->first();

			$dt = Carbon::parse($req->disbursementDate);
			$loanRepayDate = $dt->addMonths($getLoanRepayPeriod->inMonths)->toDateString();
			//	END FOR GENERATE LOAN REPAY DATE.

			$getMemberCode = $this->MicroFinance->getSingleValueForId($table='mfn_member_information', $req->memberId, 'code');
			$regularLoanShortName = $this->MicroFinance->getSingleValueForId($table='mfn_loans_product', $req->id, 'shortName');
			$regularLoanSLNum = $this->MicroFinance->getRegularLoanSLNum($req->memberId, $req->id);

			$loanCode = $regularLoanShortName . '.' . $getMemberCode . '.' . $regularLoanSLNum;

			$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->id)
													    ->where('installmentNum', MfnLoanProductInterestRate::where('loanProductId', $req->id)->max('installmentNum'))
													    ->select('interestModeId', 'interestRate', 'interestCalculationMethodShortName', 'installmentNum', 'interestRateIndex', 'repaymentFrequencyId')
													    ->first();

			$interestMode = $this->MicroFinance->getInterestModeOptions();

			$loanProductOB = MfnProduct::where('id', $req->id)->select('maxLoanAmount', 'installmentNum', 'additionalFee', 'formFee', 'maxInsuranceAmount')->first();

			$loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $req->loanRepayPeriodId)->select('inMonths')->first();

			if($loanRepayPeriodOB->inMonths==12)
				$yearCount = 1;
			if($loanRepayPeriodOB->inMonths==24)
				$yearCount = 2;
			if($loanRepayPeriodOB->inMonths==36)
				$yearCount = 3;

			//	INTEREST AMOUNT CALCULATION.
			$interestAmount = sprintf("%.2f", ($interestRateOB->interestRateIndex / 100) * $loanProductOB->maxLoanAmount * 365 * $yearCount);
			$totalRepayAmount = $loanProductOB->maxLoanAmount + $interestAmount;

			$repaymentFrequencyWiseRate = [
				'1'	 =>  25,
				'2'  =>  96
			];

			$data = array(
				'loanRepayDate'  			 =>  $loanRepayDate,
				'loanProduct' 	 			 =>  $this->MicroFinance->getActiveLoanOthersProductOptions(),
				'repaymentFrequency'   		 =>  $interestRateOB->repaymentFrequencyId,
				'loanCode'    	 			 =>  $loanCode,
				'loanCycle'   	 			 =>  $regularLoanSLNum,
				'loanAmount'   	 			 =>  sprintf("%.2f", $loanProductOB->maxLoanAmount),
				'insuranceAmount'   	 	 =>  $loanProductOB->maxInsuranceAmount,
				'interestMode'   			 =>  $interestMode[$interestRateOB->interestModeId] . ' (Daily)',
				'interestCalculationMethod'  =>  $interestRateOB->interestCalculationMethodShortName,
				'interestRate'  			 =>  $interestRateOB->interestRate,
				'installmentNum'  			 =>  $interestRateOB->installmentNum,
				'totalRepayAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'interestAmount'  			 =>  $interestAmount,
				'installmentAmount'  		 =>  sprintf("%.2f", $totalRepayAmount),
				'additionalFee'  			 =>  $loanProductOB->additionalFee,
				'formFee'  			 		 =>  $loanProductOB->formFee,
			);

			return response::json($data); 
		}

		public function addItem(Request $req) {

			$rules = array(
				'memberIdFk'  				 =>  'required', 
				'disbursementDate'  		 =>  'required', 
				'productIdFk' 	 			 =>  'required', 
				'loanCode'		   			 =>  'required|unique:mfn_loan,loanCode', 
				'loanRepayPeriodIdFk'  	  	 =>  'required', 
				'firstRepayDate'  		  	 =>  'required', 
				'loanAmount'  			  	 =>  'required', 
				'repaymentNo'  			  	 =>  'required', 
				'loanSubPurposeIdFk'  		 =>  'required',  
				'interestMode'  		     =>  'required', 
				'interestCalculationMethod'  =>  'required', 
				'interestRate'  			 =>  'required',    
				'totalRepayAmount'  		 =>  'required', 
				'installmentAmount'  		 =>  'required', 
			);

			$attributesNames = array(
				'loanCode'  =>	'loan code',
				
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['entryBy' => Auth::user()->emp_id_fk]);

				//	FOR ONE TIME LOAN loanTypeId = 2
				$req->request->add(['loanTypeId' => 2]);

				//	WHEN PAYMENT TYPE IS CASH, THEN SET CASH IN HAND LEDGER ID.
				if($req->paymentTypeIdFk=='Cash'):
					$cashLedgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->select('id')->first();
					$req->request->add(['ledgerId' => $cashLedgerId->id]);
				endif;

				//	GET SAMITY ID OF THE MEMBER.
				$samityIdOB = DB::table('mfn_member_information')->where('id', $req->memberIdFk)->select('samityId')->first();
				$req->request->add(['samityIdFk' => $samityIdOB->samityId]);
				
				//  GET INTEREST RATE INDEX.
				$interestRateIndexOB = MfnLoanProductInterestRate::where('installmentNum', $req->repaymentNo)->select('interestRateIndex')->first();
				$req->request->add(['interestRateIndex' => $interestRateIndexOB->interestRateIndex]);
				$req->request->add(['branchIdFk' => Auth::user()->branchId]);
				$create = MfnLoan::create($req->all());

				$discountDays = 7;
				$repaymentFrequencyWiseRepayDate = [
					'1'	 =>  7,
					'2'  =>  30
				];

				$scheduleDateArr = [];
				for($i=0;$i<$req->repaymentNo;$i++):
					$dayDiff = ($repaymentFrequencyWiseRepayDate[1] * $i) . 'days'; 
					$date=date_create($req->firstRepayDate);
					date_add($date,date_interval_create_from_date_string($dayDiff));
					$scheduleDateArr[] = date_format($date,"Y-m-d");
				endfor;

				$interestRateOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)
													    	->where('installmentNum', MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)->max('installmentNum'))
													    	->select('interestRateIndex')
													    	->first();

				$interestRateIndex = (fmod($interestRateOB->interestRateIndex, 1) * 100);
				$principalAmount = $req->installmentAmount - $req->interestAmount;

				//	GET LOAN ID.
				$loanIdOB = DB::table('mfn_loan')->where('loanCode', $req->loanCode)->select('id')->first();

				//	GENERATE LOAN SCHEDULE.
				for($i=0;$i<$req->repaymentNo;$i++):
					$req->request->add(['loanIdFk' => $loanIdOB->id]);
					$req->request->add(['installmentSl' => $i+1]);
					$req->request->add(['interestAmount' => sprintf("%.2f", $req->interestAmount)]);
					$req->request->add(['principalAmount' => sprintf("%.2f", $principalAmount)]);
					$req->request->add(['scheduleDate' => $scheduleDateArr[$i]]);
					$create = MfnLoanSchedule::create($req->all());
				endfor;

				$data = array(
					'responseTitle'  =>   'Success!',
					'responseText'   =>   'New one time loan has been issued successfully.',
				);
				
				return response::json($data);
			}
		}

		public function detailsOneTimeLoan($regularLoanId) {

			$loanDetailsTCN = [
				'loanId'					   =>  'Loan ID:',
				'product'					   =>  'Product:',
				'memberName'				   =>  'Member Name:',
				'loanCycle'					   =>  'Loan Cycle:',
				'fatherSpouseName'			   =>  'Father\'s/Spouse Name:',
				'paymentMode'				   =>  'Mode of payment:',
				'age'						   =>  'Age:',
				'mobileNo'					   =>  'Mobile No:',
				'samity'					   =>  'Samity:',
				'transferDate'				   =>  'Transfer In Date:',
				'disbursementDate'			   =>  'Disbursement Date:',
				'dueAmount'				       =>  'Due Amount:',
				'firstRepayDate'			   =>  'First Repay Date:',
				'advanceAmount'				   =>  'Advance Amount:',
				'interestRate'				   =>  'Interest Rate:',
				'recoveryAmount'			   =>  'Recovery Amount:',
				'extraInstallmentAmount'	   =>  'Extra Installment Amount:',
				'openingLoanOutstanding'	   =>  'Opening Loan Outstanding:',
				'currentStatus'		  		   =>  'Current Status:',
				'rebate'				  	   =>  'Rebate:',
				'repaymentFrequency'		   =>  'Repayment Frequency:',
				'loanOutstanding'			   =>  'Loan Outstanding:',
				'interestMode'				   =>  'Mode of interest:',
				'loanPurpose'				   =>  'Loan Purpose:',
				'loanAmount'				   =>  'Loan Amount:',
				'loanSubPurpose'			   =>  'Loan Sub Purpose:',
				'interestAmount'			   =>  'Interest Amount:',
				'guarantorNameFirst'		   =>  'Guarantor\'s Name #1:',
				'totalRepayAmount'	  		   =>  'Total Repay Amount: ',
				'guarantorRelationshipFirst'   =>  'Guarantor\'s Relationship #1:',
				'installmentNum'  			   =>  'Number of Installment:',
				'guarantorAddressFirst'		   =>  'Guarantor\'s Address #1:',
				'loanPeriodInMonth'			   =>  'Loan Period in Month:',
				'guarantorNameSecond'		   =>  'Guarantor\'s Name #2:',
				'loanApplicationNo'			   =>  'Loan Application No:',
				'guarantorRelationshipSecond'  =>  'Guarantor\'s Relationship #2:',
				'insuranceGuarantorAmount'	   =>  'Insurance/Guarantor\'s Amount:',
				'guarantorAddressSecond'	   =>  'Guarantor\'s Address #2:',
				'loanClosingDate'			   =>  'Loan Closing Date:',
				'transferOutDate'			   =>  'Transfer Out Date:',
				'installmentAmount'			   =>  'Installment Amount:',
				'folioNumber'				   =>  'Folio Number:',
				'additionalFee'				   =>  'Additional Fee:',
				'loanFormFee'				   =>  'Loan Form Fee:',
				'payment'				       =>  'Payment:',
				'employment'				   =>  'Employment:',
			];

			$loanScheduleTCN = array(
				array('Date.', 70), 
				array('Installment Amount', 0),
				array('Actual Installment Amount', 100),
				array('Extra Installment Amount', 0),
				array('Principal Amount', 0),
				array('Interest Amount', 0),
				array('Transaction Amount', 80),
				array('Status', 80)
			);	



			$regularLoanDetails = $this->MicroFinance->getLoanDetails($regularLoanId);
			$loanPurposeOB = DB::table('mfn_loans_sub_purpose')->where('id', $regularLoanDetails->loanSubPurposeIdFk)->select('purposeIdFK')->first();
			$samityInfoOB = $this->MicroFinance->getMultipleValueForId($table='mfn_samity', $regularLoanDetails->samityIdFk, ['name', 'code', 'samityDayId', 'fixedDate']);

			$damageData = array(
				'loanDetailsTCN'      =>  $loanDetailsTCN,
				'loanScheduleTCN'     =>  $loanScheduleTCN,
				'regularLoanDetails'  =>  $regularLoanDetails,
				'regularLoanDetail'   =>  array(
					'loanCode'					=>  $regularLoanDetails->loanCode,
					'loanCycle'					=>  $regularLoanDetails->loanCycle,
					'paymentTypeIdFk'			=>  $regularLoanDetails->paymentTypeIdFk,
					'disbursementDate'			=>  $regularLoanDetails->disbursementDate,
					'firstRepayDate'			=>  $regularLoanDetails->firstRepayDate,
					'loanApplicationNo'			=>  $regularLoanDetails->loanApplicationNo,
					'interestRate'			    =>  $regularLoanDetails->interestRate,
					'interestRateIndex'			=>  $regularLoanDetails->interestRateIndex,
					'interestCalculationMethod'	=>  $regularLoanDetails->interestCalculationMethod,
			   		'extraInstallmentAmount'	=>  $regularLoanDetails->extraInstallmentAmount,
					'totalRepayAmount'			=>  $regularLoanDetails->totalRepayAmount,
					'interestMode'				=>  $regularLoanDetails->interestMode,
					'loanAmount'				=>  $regularLoanDetails->loanAmount,
					'interestAmount'			=>  $regularLoanDetails->interestAmount,
					'repaymentNo'				=>  $regularLoanDetails->repaymentNo,
					'installmentAmount'			=>  $regularLoanDetails->installmentAmount,
					'actualInstallmentAmount'	=>  $regularLoanDetails->actualInstallmentAmount,
					'extraInstallmentAmount'	=>  $regularLoanDetails->extraInstallmentAmount,
					'folioNum'					=>  $regularLoanDetails->folioNum,
					'additionalFee'				=>  $regularLoanDetails->additionalFee,
					'loanFormFee'				=>  $regularLoanDetails->loanFormFee,
					'firstGuarantorName'		=>  $regularLoanDetails->firstGuarantorName,
					'firstGuarantorRelation'	=>  $regularLoanDetails->firstGuarantorRelation,
					'firstGuarantorAddress'		=>  $regularLoanDetails->firstGuarantorAddress,
					'secondGuarantorName'		=>  $regularLoanDetails->secondGuarantorName,
					'secondGuarantorRelation'	=>  $regularLoanDetails->secondGuarantorRelation,
					'secondGuarantorAddress'	=>  $regularLoanDetails->secondGuarantorAddress,
					'isSelfEmployment'			=>  $regularLoanDetails->isSelfEmployment,
					'isLoanCompleted'			=>  $regularLoanDetails->isLoanCompleted,
					'productIdFk'	 			=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_product', $regularLoanDetails->productIdFk),
					'memberInfoOB'			    =>  $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $regularLoanDetails->memberIdFk, ['name', 'code', 'age', 'spouseFatherSonName', 'mobileNo']),
					'samityName'			    =>  $samityInfoOB->name,
					'samityCode'			    =>  $samityInfoOB->code,
					'samityDay'				    =>  $this->MicroFinance->getSamityDayName($samityInfoOB->samityDayId, $samityInfoOB->fixedDate),
					//'repaymentFrequencyIdFk'	=>  $repaymentFrequencyIdFk = $this->MicroFinance->getNameValueForId($table='mfn_repayment_frequency', $regularLoanDetails->repaymentFrequencyIdFk),
					'loanRepayPeriodIdFk'		=>  $this->MicroFinance->getSingleValueForId($table='mfn_loan_repay_period', $regularLoanDetails->loanRepayPeriodIdFk, 'inMonths'),
					'loanPurpose'				=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_purpose', $loanPurposeOB->purposeIdFK),
					'loanSubPurposeIdFk'		=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_sub_purpose', $regularLoanDetails->loanSubPurposeIdFk),
				),	
				'loanSchedules'       =>  $this->MicroFinance->getLoanSchedule($regularLoanDetails->id, $regularLoanDetails->loanTypeId),
				'MicroFinance'        =>  $this->MicroFinance
			);

			return view('microfin.loan.oneTimeLoan.detailsOneTimeLoan', ['damageData' => $damageData]);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE ONE TIME LOAN CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			
			MfnLoan::find($req->id)->delete();
			MfnLoanSchedule::where('loanIdFk', $req->id)->delete();
			MfnFees::where('loanIdFk', $req->id)->delete();
			MfnLoanReschedule::where('loanIdFk', $req->id)->delete();
			MfnloanOpeningBalance::where('loanIdFk', $req->id)->delete();  
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected loan has been deleted successfully.'
			);

			return response()->json($data);
		}

	}