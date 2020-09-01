<?php

	namespace App\Http\Controllers\microfin\loan;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnLoan;
	use App\microfin\loan\MfnProduct;
	use App\microfin\loan\MfnLoanSchedule;
	use App\microfin\loan\MfnLoanReschedule;
	use App\microfin\loan\MfnGracePeriod;
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

	class MfnRegularLoanController extends Controller {

		protected $MicroFinance;
		
		use GetSoftwareDate;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 50), 
				array('Loan Code', 0),
				array('Member Code', 100),
				array('Member Name', 0),
				array('Loan Amount', 80),
				array('Total Repay Amount', 100),
				array('Int. Rate', 80),
				array('Disburse Date', 90),
				array('First Repay Date', 100),
				array('NOI', 50),
				array('Auth. Status', 70),
				array('Loan Status', 70),
				array('Entry By', 0),
				array('Action', 80)
			);	
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: LIST OF REGULAR LOAN.
		|--------------------------------------------------------------------------
		*/
		public function index(Request $req) {

			$PAGE_SIZE = 20;

			if(Auth::user()->branchId==1):
				$samity = [];
				$primaryProduct = $this->MicroFinance->getLoanProductsOption();
				$loan = MfnLoan::active()->regularLoan();
			else:
				$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId); 
				$loan = MfnLoan::active()->branchWise()->regularLoan();
			endif;
			
			if($req->has('branchId')) {
			    $loan->where('branchIdFk', $req->get('branchId'));
				$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
				$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId')); 
			}

			if($req->has('samityId'))
			    $loan->where('samityIdFk', $req->get('samityId'));
			
			if($req->has('primaryProductId'))
			    $loan->where('productIdFk', $req->get('primaryProductId'));
			
			if($req->has('dateFrom'))
			    $loan->where('disbursementDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));
			
			if($req->has('dateTo'))
				$loan->where('disbursementDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

			if($req->has('loanFrom'))
			    $loan->where('loanAmount', '>=', $req->get('loanFrom'));
			
			if($req->has('loanTo'))
				$loan->where('loanAmount', '<=', $req->get('loanTo'));

			if($req->has('noi'))
				$loan->where('repaymentNo', '=', $req->get('noi'));

			if($req->has('loanCode'))
				$loan->where('loanCode', 'LIKE', '%' . $req->get('loanCode') . '%');

			if($req->has('page'))
				$SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

			if($req->has('branchId') || $req->has('samityId') || $req->has('primaryProductId') || $req->has('name') || $req->has('dateFrom') || $req->has('dateTo') || $req->has('loanFrom') || $req->has('loanTo') || $req->has('noi') || $req->has('loanCode')) {
				$loan = $loan->get();
				$isSearch = 1;
			} else {
				$loan = $loan->paginate($PAGE_SIZE);
				$isSearch = 0;
			}
			
			//$loan = $loan->paginate($PAGE_SIZE);

			$damageData = array(
				'TCN'               =>  $this->TCN,
				'SL' 	   		    =>  $req->has('page')?$SL:0,
				'isSearch'          =>  $isSearch,
				'branch'  		    =>  $this->MicroFinance->getAllBranchOptions(),
				'samity'		    =>  $samity,
				'primaryProduct'    =>  $primaryProduct,
				'regularLoans'      =>  $loan,
				'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
				'MicroFinance'      =>  $this->MicroFinance
			);

			return view('microfin.loan.regularLoan.viewRegularLoan', ['damageData' => $damageData]);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: SHOW REGULAR LOAN FORM.
		|--------------------------------------------------------------------------
		*/
		public function addRegularLoan() {

			$damageData = array(
				'member'  			  =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
				//'disbursementDate'    =>  Carbon::parse('2017-12-27')->toDateString(),
				//'disbursementDate'    =>  Carbon::today()->toDateString(),
				'disbursementDate'    =>  GetSoftwareDate::getSoftwareDate(),
				'paymentType'         =>  $this->MicroFinance->getPaymentType(),
				'loanPurpose'         =>  $this->MicroFinance->getLoanPurpose(),
				'boolean'  			  =>  $this->MicroFinance->getBooleanOptions()
			);

			return view('microfin.loan.regularLoan.addRegularLoan', ['damageData' => $damageData]);
		}

		public function loadLoanProductList(Request $req) {

			$checkRegularLoanCompleted = MfnLoan::where('memberIdFk', $req->memberId)->loanCompleted()->count();
			$newRegularLoan = MfnLoan::where('memberIdFk', $req->memberId)->count();

			if($checkRegularLoanCompleted==1 || $newRegularLoan==0):
				$loanProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptionsMemberWise($req->memberId);
			else:
				$loanProduct = array();
			endif;

			//	GET MEMBER ADMISSION DATE.
			$memberOB = DB::table('mfn_member_information')->where('id', $req->memberId)->select('admissionDate')->first();

			$data = array(
				'loanProduct'  		   =>  $loanProduct,
				'memberAdmissionDate'  =>  $memberOB->admissionDate,
				'softwareDate' 		   =>  GetSoftwareDate::getSoftwareDate()
			);
			
			return response::json($data); 
		}

		public function loadRegularLoanSupportData(Request $req) {

			$getMemberCode = $this->MicroFinance->getSingleValueForId($table='mfn_member_information', $req->memberId, 'code');
			$regularLoanShortName = $this->MicroFinance->getSingleValueForId($table='mfn_loans_product', $req->id, 'shortName');
			$regularLoanSLNum = $this->MicroFinance->getRegularLoanSLNum($req->memberId, $req->id);

			//	GENERATE LOAN CODE.
			$loanCode = $regularLoanShortName . '.' . $getMemberCode . '.' . $regularLoanSLNum;
			
			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
							   ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							   ->where('mfn_member_information.id', $req->memberId)
							   ->select('mfn_samity.samityDayId AS samityDayId')
							   ->first();

			$samityDayId = $samityDayIdOB->samityDayId;

			$loanProductOB = MfnProduct::where('id', $req->id)
			                           ->select('avgLoanAmount',
			                           			'maxLoanAmount',
			                           			'minLoanAmount', 
                                                'installmentNum', 
                                                'eligibleRepaymentFrequencyId', 
                                                'additionalFee', 
                                                'formFee', 
                                                'principalAmountOfLoan'
                                               )
			                           ->first();

			//	GET ELIGIBLE REPAYMENT FREQUENCY ARRAY.
			$eligibleRepaymentFrequencyArr = $this->MicroFinance->getRepaymentFrequencyArr($table='mfn_repayment_frequency', $loanProductOB->eligibleRepaymentFrequencyId);

			//	GET LOAN REPAY PERIOD OPTIONS.
			$loanRepayPeriodOption = $this->MicroFinance->getLoanRepayPeriod();

			//	LOAN REPAY PERIOD FOR WEEKLY REPAYMENT FREQUENCY.
			$loanRepayPeriod = [];
			
			foreach($loanRepayPeriodOption as $key => $val):
				//	FOR WEEKLY REPAYMENT FREQUENCY.
				if($eligibleRepaymentFrequencyArr[0]==1):
					if($key==1):
						$loanRepayPeriod[$key] = $val;
						break;
					endif;
				endif;

				//	FOR MONTHLY REPAYMENT FREQUENCY.
				if($eligibleRepaymentFrequencyArr[0]==2):
					if($key>=1 && $key<=3):
						$loanRepayPeriod[$key] = $val;
					endif;
				endif;

				if($key==1):
					$loanRepayPeriod[$key] = $val;
				endif;
			endforeach;

			//	FIRST REPAYMENT DATE CALCULATION.
			$gracePeriodArr = $this->MicroFinance->getArrayForSecondMultipleId($table='mfn_grace_period', $loanProductOB->eligibleRepaymentFrequencyId, 'inDays');                           
			
			//	GET DISBURSEMENT DATE.
			$dt = Carbon::parse($req->disbursementDate);
			
			//	GET NEXT SAMITY DATE FROM THE DISBURSEMENT DATE.
			$nextSamityDate = $this->MicroFinance->getNextSamityDate($dt->toDateString(), $samityDayId);

			//	GET FIRST REPAY DATE.
			$NSD = Carbon::parse($nextSamityDate);
			$firstRepayDate = $NSD->addDays($gracePeriodArr[$eligibleRepaymentFrequencyArr[0]])->toDateString();
			//$firstRepayDate = $NSD->toDateString();

			//	MANUFACTURING NO OF REPAYMENT OPTIONS.
			$repaymentNo = explode(',', $loanProductOB->installmentNum);

			$repaymentNoOptions = [];

			foreach($repaymentNo as $key => $val):
				$repaymentNoOptions[$val] = $val;
			endforeach;

			//	MANUFACTURING NO OF PAYMENT OPTIONS FOR WEEKLY FREQUENCY.
			//	BUT FOR MONTHLY FREQUENCY THE BELOW LINE WILL ACTIVE.
			if($eligibleRepaymentFrequencyArr[0]!=2):
				unset($repaymentNoOptions[12]);
			endif;
			
			unset($repaymentNoOptions[24]);
			unset($repaymentNoOptions[36]);

			//	START FIND INTEREST RATE INDEX OF THE REPAYMENT NO.
			$i = 0;
			foreach($repaymentNoOptions as $key => $val):
				if($i==0)
					$installmentNum = $val;
				$i++;
			endforeach;

			$supportData = $this->MicroFinance->regularLoanSupportDataRepaymentNumberWise($req->id, $loanProductOB->avgLoanAmount, $installmentNum, $eligibleRepaymentFrequencyArr[0]);

			$data = array(
				'loanProduct' 	 			=>  $this->MicroFinance->getActiveLoanPrimaryProductOptions(),
				'repaymentFrequencyOption'  =>  $this->MicroFinance->getRepaymentFrequencyOptionsProductWise($table='mfn_repayment_frequency', $loanProductOB->eligibleRepaymentFrequencyId),
				'repaymentFrequency'   		=>  $eligibleRepaymentFrequencyArr[0],
				'loanCode'    	 			=>  $loanCode,
				'loanRepayPeriod'		 	=>  $loanRepayPeriod,
				'loanCycle'   	 			=>  $regularLoanSLNum,
				'firstRepayDate'   	 		=>  $firstRepayDate,
				'loanAmount'   	 			=>  sprintf("%.2f", $loanProductOB->avgLoanAmount),
				'maxLoanAmount'   	 		=>  sprintf("%.2f", $loanProductOB->maxLoanAmount),
				'minLoanAmount'   	 		=>  sprintf("%.2f", $loanProductOB->minLoanAmount),
				'repaymentNo'   	 		=>  $repaymentNoOptions, 
				'additionalFee'  			=>  sprintf("%.2f", $loanProductOB->additionalFee),
				'formFee'  			 		=>  sprintf("%.2f", $loanProductOB->formFee),
				'nextSamityDate'			=>  $nextSamityDate,
				'principalAmountOfLoan'     =>  $loanProductOB->principalAmountOfLoan,
				'gracePeriodArr'			=>  $gracePeriodArr,
				'supportData'  				=>  $supportData,
			);
			
			return response::json($data); 
		}

		public function loadRegularLoanNoOfRepaymentOption(Request $req) {

			$noOfRepaymentOB = MfnProduct::where('id', $req->productId)->select('installmentNum')->first();
			$noOfRepaymentArr = explode(',', $noOfRepaymentOB->installmentNum);

			$noOfRepayment = [];

			foreach($noOfRepaymentArr as $key => $val):
				$noOfRepayment[$val] = $val;
			endforeach;

			//	MANUFACTURING NO OF PAYMENT OPTIONS FOR WEEKLY FREQUENCY.
			if($req->repaymentFrequencyId==1):
				unset($noOfRepayment[12]);
				unset($noOfRepayment[24]);
				unset($noOfRepayment[36]);
			endif;

			//	MANUFACTURING NO OF PAYMENT OPTIONS FOR MONTHLY FREQUENCY.
			if($req->repaymentFrequencyId==2):
				$noOfRepayment = []; 
			endif;

			//	GET FIRST REPAY DATE.
			$firstRepayDate = $this->MicroFinance->getRegularLoanFirstRepayDate($req->memberId, $req->disbursementDate, $req->productId, $req->repaymentFrequencyId);
			
			//	GET LOAN REPAY PERIOD OPTIONS.
			$loanRepayPeriodOption = $this->MicroFinance->getLoanRepayPeriod();

			//	LOAN REPAY PERIOD.
			$loanRepayPeriod = [];
			
			//	LOAN REPAY PERIOD FOR WEEKLY REPAYMENT FREQUENCY.
			if($req->repaymentFrequencyId==1):
				foreach($loanRepayPeriodOption as $key => $val):
					if($key==1):
						$loanRepayPeriod[$key] = $val;
						break;
					endif;
				endforeach;
			endif;

			//	LOAN REPAY PERIOD FOR MONTHLY REPAYMENT FREQUENCY.
			if($req->repaymentFrequencyId==2):
				foreach($loanRepayPeriodOption as $key => $val):
					if($key>=1 && $key<=3):
						$loanRepayPeriod[$key] = $val;
					endif;
				endforeach;
			endif;
			
			$data = array(
				'loanRepayPeriod'  =>  $loanRepayPeriod,
				'firstRepayDate'   =>  $firstRepayDate,
				'noOfRepayment'    =>  $noOfRepayment
			);

			return response::json($data); 
		}

		public function loadRegularLoanSupportDataRepaymentWise(Request $req) {

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

			if(is_object($supportData)):
				$data = array(
					'repaymentNo'  =>  $noOfRepayment,
					'supportData'  =>  $supportData,
				);
			else:
				$data = array(
					'msgStatus'      =>  1, 
					'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
					'responseText'   =>  MicroFinance::getMessage('productInterestRateWarning'), 
				);
			endif;

			return response::json($data); 
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD REGULAR LOAN.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'memberIdFk'  				 =>  'required', 
				'disbursementDate'  		 =>  'required', 
				'productIdFk' 	 			 =>  'required', 
				'loanCode'		   			 =>  'required|unique:mfn_loan,loanCode', 
				'repaymentFrequencyIdFk'  	 =>  'required', 
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

				//	FOR REGULAR LOAN loanTypeId = 1
				$req->request->add(['loanTypeId' => 1]);

				//	WHEN PAYMENT TYPE IS CASH, THEN SET CASH IN HAND LEDGER ID.
				if($req->paymentTypeIdFk=='Cash'):
					$cashLedgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->select('id')->first();
					$req->request->add(['ledgerId' => $cashLedgerId->id]);
				endif;
					
				//	GET SAMITY ID OF THE MEMBER.
				$samityIdOB = DB::table('mfn_member_information')->where('id', $req->memberIdFk)->select('samityId', 'primaryProductId')->first();
				$req->request->add(['samityIdFk' => $samityIdOB->samityId]);
				$req->request->add(['primaryProductIdFk' => $samityIdOB->primaryProductId]);
				
				//  GET INTEREST RATE INDEX.
				$interestRateIndexOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)
																 ->where('installmentNum', $req->repaymentNo)
																 ->select('interestRateIndex')
																 ->first();
																 
				$req->request->add(['interestRateIndex' => $interestRateIndexOB->interestRateIndex]);

				//	GET INSURANCE AMOUNT PERCENTAGE OF LOAN AMOUNT.
				$loanProductOB = MfnProduct::where('id', $req->productIdFk)
				                           ->select('principalAmountOfLoan')
				                           ->first();

				$req->request->add(['insuranceAmount' => $req->loanAmount * ($loanProductOB->principalAmountOfLoan / 100)]);
				$req->request->add(['branchIdFk' => Auth::user()->branchId]);
				$create = MfnLoan::create($req->all());

				$repaymentFrequencyWiseRepayDate = [
					'1'	 =>  7,
					'2'  =>  30
				];
				
				//	GET HOLIDAY.
				$globalGovtHoliday = $this->MicroFinance->getGlobalGovtHoliday();
				$organizationHoliday = $this->MicroFinance->getOrganizationHoliday(1);
				$branchHoliday = $this->MicroFinance->getBranchHoliday();
				$samityHoliday = $this->MicroFinance->getSamityHoliday($req->memberIdFk);
				$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));
				
				$holidayFound = 0;
				$scheduleDateArr = [];
				$test = [];

				//for($i=0;$i<$req->repaymentNo;$i++):
				for($i=0;$i<1000;$i++):
					$dayDiff = ($repaymentFrequencyWiseRepayDate[$req->repaymentFrequencyIdFk] * $i) . 'days'; 
					$date = date_create($req->firstRepayDate);
					date_add($date, date_interval_create_from_date_string($dayDiff));
					
					//	PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
					if($req->repaymentFrequencyIdFk==1):
						//	CHECK IF A DATE IS MATCHES TO HOLIDAY.
						foreach($holiday as $key => $val):
							if(date_create($val)>=$date):
								if(date_create($val)==$date):
									$holidayFound = 1;
									$test[] = $val;
									break;
								endif;
							endif;
						endforeach;

						if($holidayFound==0)
							$scheduleDateArr[] = date_format($date, "Y-m-d");
						
						$holidayFound = 0;
					endif;
					
					//	PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
					if($req->repaymentFrequencyIdFk==2):
						$dayDiff = ($repaymentFrequencyWiseRepayDate[$req->repaymentFrequencyIdFk] * $i) . 'days'; 
						$date = date_create($req->firstRepayDate);
						date_add($date, date_interval_create_from_date_string($dayDiff));
						$disbursementDate = date_create($req->disbursementDate);
						date_add($disbursementDate, date_interval_create_from_date_string($dayDiff));

						$tos = Carbon::parse($req->firstRepayDate);
						$sot = $tos->addMonths($i)->toDateString();

						if($i==0)
							$targetDate = date_format($date, "Y-m-d");
						else
							$targetDate = $this->MicroFinance->getMonthlyLoanScheduleDateFilter($sot, $req->memberIdFk);
						
						$originalMD = Carbon::parse($targetDate);
						$MD = Carbon::parse($targetDate);
						$targetDate = $MD->toDateString();

						//	CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
						for($j=0;$j<100;$j++):
							if(in_array($targetDate, $holiday)):
								$targetDate = $MD->addDays(7)->toDateString();
								
								if($targetDate>$originalMD->endOfMonth()):
									$targetDate = $MD->subDays(14)->toDateString();
								else:
									if(in_array($targetDate, $holiday)):
										$targetDate = $MD->addDays(7)->toDateString();
										
										if($targetDate>$originalMD->endOfMonth()):
											$targetDate = $MD->subDays(21)->toDateString();
										endif;
									else:
										break;
									endif;
								endif;
							else:
								break;
							endif;
						endfor;

						$scheduleDateArr[] = $targetDate;
					endif;

					if(count($scheduleDateArr)==$req->repaymentNo)
						break;
				endfor;

				//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT.
				$principalAmount = $req->installmentAmount / $interestRateIndexOB->interestRateIndex;
				$interestAmount = $req->installmentAmount - $principalAmount;
				
				//	GET LOAN ID.
				$loanIdOB = DB::table('mfn_loan')->where('loanCode', $req->loanCode)->select('id')->first();

				//	GENERATE LOAN SCHEDULE.
				for($i=0;$i<$req->repaymentNo;$i++):
	
					$req->request->add(['loanIdFk' => $loanIdOB->id]);
					$req->request->add(['installmentSl' => $i+1]);

					if($i==$req->repaymentNo-1):
						//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT FOR LAST INSTALLMENT.
						$installmentAmount = $req->totalRepayAmount-($req->installmentAmount*($req->repaymentNo-1));
						$principalAmount = $installmentAmount / $interestRateIndexOB->interestRateIndex;
						$interestAmount = $installmentAmount - $principalAmount;
						$req->request->add(['installmentAmount' => sprintf("%.2f", $installmentAmount)]);
						$req->request->add(['actualInstallmentAmount' => sprintf("%.2f", 0)]);
						$req->request->add(['extraInstallmentAmount' => sprintf("%.2f", 0)]);
					endif;

					$req->request->add(['principalAmount' => sprintf("%.2f", $principalAmount)]);	
					$req->request->add(['interestAmount' => sprintf("%.2f", $interestAmount)]);
					$req->request->add(['scheduleDate' => $scheduleDateArr[$i]]);
					$create = MfnLoanSchedule::create($req->all());
				endfor;

				//	GENERATE ALL LOAN FEES. 
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['name' => 'loan']);
				$req->request->add(['loanIdFk' => $loanIdOB->id]);
				$req->request->add(['loanAdditionalFee' => $req->additionalFee]);
				$req->request->add(['loanFormFee' => $req->loanFormFee]);
				$create = MfnFees::create($req->all());

				$data = array(
					'responseTitle' =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'  =>  MicroFinance::getMessage('regularLoanCreateSuccess'),
				);
				
				return response::json($data);
			}
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: SHOW EDIT REGULAR LOAN FORM.
		|--------------------------------------------------------------------------
		*/
		public function updateRegularLoan($regularLoanId) {

			//	GET ALL THE DETAILS OF THE LOAN.
			$regularLoanDetails = $this->MicroFinance->getLoanDetails($regularLoanId);

			//	GET THE DETAILS OF THE PRODUCT OF THE LOAN.
			$loanProductOB = MfnProduct::where('id', $regularLoanDetails->productIdFk)
			                           ->select('avgLoanAmount',
			                           			'maxLoanAmount',
			                           			'minLoanAmount', 
                                                'installmentNum', 
                                                'eligibleRepaymentFrequencyId', 
                                                'principalAmountOfLoan'
                                               )
			                           ->first();

			//	GET LOAN REPAY PERIOD OPTIONS.
			$loanRepayPeriodOption = $this->MicroFinance->getLoanRepayPeriod();

			//	LOAN REPAY PERIOD.
			$loanRepayPeriod = [];
			
			//	LOAN REPAY PERIOD FOR WEEKLY REPAYMENT FREQUENCY.
			if($regularLoanDetails->repaymentFrequencyIdFk==1):
				foreach($loanRepayPeriodOption as $key => $val):
					if($key==1):
						$loanRepayPeriod[$key] = $val;
						break;
					endif;
				endforeach;
			endif;

			//	LOAN REPAY PERIOD FOR MONTHLY REPAYMENT FREQUENCY.
			if($regularLoanDetails->repaymentFrequencyIdFk==2):
				foreach($loanRepayPeriodOption as $key => $val):
					if($key>=1 && $key<=3):
						$loanRepayPeriod[$key] = $val;
					endif;
				endforeach;
			endif;

			//	MANUFACTURING NO OF REPAYMENT OPTIONS.
			$repaymentNo = explode(',', $loanProductOB->installmentNum);

			$repaymentNoOptions = [];

			foreach($repaymentNo as $key => $val):
				$repaymentNoOptions[$val] = $val;
			endforeach;

			if($regularLoanDetails->repaymentFrequencyIdFk==1):
				unset($repaymentNoOptions[12]);
				unset($repaymentNoOptions[24]);
				unset($repaymentNoOptions[36]);
			else:
				unset($repaymentNoOptions[46]);
			endif;
			
			$damageData = array(
				'loanId'					=>  $regularLoanId,
				'regularLoanDetails'  		=>  $regularLoanDetails,
				'member'  			  		=>  $this->MicroFinance->getMemberOptionsSingle($regularLoanDetails->memberIdFk),
				'product'			  		=>  $this->MicroFinance->getLoanProductsOptionSingle($regularLoanDetails->productIdFk),
				'productDetails'			=>  $loanProductOB,
				'repaymentFrequencyOption'  =>  $this->MicroFinance->getRepaymentFrequencyOptionsProductWise($table='mfn_repayment_frequency', $loanProductOB->eligibleRepaymentFrequencyId),
				'loanRepayPeriod'  			=>  $loanRepayPeriod,
				'repaymentNo'   	 		=>  $repaymentNoOptions, 
				'paymentType'         		=>  $this->MicroFinance->getPaymentType(),
				'loanPurpose'         		=>  $this->MicroFinance->getLoanPurpose(),
				'boolean'  			  		=>  $this->MicroFinance->getBooleanOptions(),
			);

			return view('microfin.loan.regularLoan.editRegularLoan', $damageData);
		}

		public function updateItem(Request $req) {

			
			//	UPDATE LOAN.
			$loan = MfnLoan::find($req->loanId);
			
			//	MEMBER AND LOAN DETAILS.
			$loan->memberIdFk = $req->memberIdFk;
			$loan->disbursementDate = $req->disbursementDate;
			$loan->productIdFk = $req->productIdFk;
			$loan->loanCode = $req->loanCode;

			//	LOAN CONFIGURATION.
			$loan->loanApplicationNo = $req->loanApplicationNo;
			$loan->repaymentFrequencyIdFk = $req->repaymentFrequencyIdFk;
			$loan->loanRepayPeriodIdFk = $req->loanRepayPeriodIdFk;
			$loan->firstRepayDate = $req->firstRepayDate;
			$loan->loanCycle = $req->loanCycle;
			$loan->loanAmount = $req->loanAmount;
			$loan->repaymentNo = $req->repaymentNo;
			$loan->insuranceAmount = $req->insuranceAmount;
			$loan->loanSubPurposeIdFk = $req->loanSubPurposeIdFk;
			$loan->folioNum = $req->folioNum;

			//	INTEREST CALCULATION.
			$loan->interestMode = $req->interestMode;
			$loan->interestCalculationMethod = $req->interestCalculationMethod;
			$loan->interestRate = $req->interestRate;
			$loan->interestDiscountAmount = $req->interestDiscountAmount;

			//	PAYMENTS.
			$loan->totalRepayAmount = $req->totalRepayAmount;
			$loan->interestAmount = $req->interestAmount;
			$loan->installmentAmount = $req->installmentAmount;
			$loan->paymentTypeIdFk = $req->paymentTypeIdFk;

			//	EXTRA LOAN INFORMATION.
			$loan->extraInstallmentAmount = $req->extraInstallmentAmount;
			$loan->actualInstallmentAmount = $req->actualInstallmentAmount;
			$loan->lastInstallmentAmount = $req->lastInstallmentAmount;
			$loan->actualNumberOfInstallment = $req->actualNumberOfInstallment;
			$loan->additionalFee = $req->additionalFee;
			$loan->loanFormFee = $req->loanFormFee;
			$loan->note = $req->note;

			//	GUARANTOR'S DETAILS.
			$loan->firstGuarantorName = $req->firstGuarantorName;
			$loan->firstGuarantorRelation = $req->firstGuarantorRelation;
			$loan->firstGuarantorAddress = $req->firstGuarantorAddress;
			$loan->firstGuarantorContact = $req->firstGuarantorContact;
			$loan->secondGuarantorName = $req->secondGuarantorName;
			$loan->secondGuarantorRelation = $req->secondGuarantorRelation;
			$loan->secondGuarantorAddress = $req->secondGuarantorAddress;
			$loan->secondGuarantorContact = $req->secondGuarantorContact;
			
			//	EMPLOYMENT RELATED INFORMATION.
			$loan->isSelfEmployment = $req->isSelfEmployment;
			$loan->FEFullTimeMale = $req->FEFullTimeMale;
			$loan->FEFullTimeFemale = $req->FEFullTimeFemale;
			$loan->OFEFullTimeMale = $req->OFEFullTimeMale;
			$loan->OFEFullTimeFemale = $req->OFEFullTimeFemale;
			$loan->FEPartTimeMale = $req->FEPartTimeMale;
			$loan->FEPartTimeFemale = $req->FEPartTimeFemale;
			$loan->OFEPartTimeMale = $req->OFEPartTimeMale;
			$loan->OFEPartTimeFemale = $req->OFEPartTimeFemale;
			$loan->FEFullTimeMaleWage = $req->FEFullTimeMaleWage;
			$loan->FEFullTimeFemaleWage = $req->FEFullTimeFemaleWage;
			$loan->OFEPartTimeMaleWage = $req->OFEPartTimeMaleWage;
			$loan->OFEPartTimeFemaleWage = $req->OFEPartTimeFemaleWage;
			$loan->businessName = $req->businessName;
			$loan->businessLocation = $req->businessLocation;
			$loan->businessType = $req->businessType;
			$loan->save();

			//  GET INTEREST RATE INDEX.
			$interestRateIndexOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)
															 ->where('installmentNum', $req->repaymentNo)
															 ->select('interestRateIndex')
															 ->first();

			//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT.
			$principalAmount = $req->installmentAmount / $interestRateIndexOB->interestRateIndex;
			$interestAmount = $req->installmentAmount - $principalAmount;

			$installmentAmount = sprintf("%.2f", $req->installmentAmount);
			$actualInstallmentAmount = sprintf("%.2f", $req->actualInstallmentAmount);
			$extraInstallmentAmount = sprintf("%.2f", $req->extraInstallmentAmount);

			$repaymentNo = (int) $req->repaymentNo;
			$loanId = (int) $req->loanId;

			//	UPDATE LOAN SCHEDULE.
			for($i=0;$i<$repaymentNo;$i++):
				if($i==$repaymentNo-1):
					//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT FOR LAST INSTALLMENT.
					$installmentAmount = $req->totalRepayAmount - ($req->installmentAmount * ($repaymentNo - 1));
					$principalAmount = $installmentAmount / $interestRateIndexOB->interestRateIndex;
					$interestAmount = $installmentAmount - $principalAmount;
					$installmentAmount = sprintf("%.2f", $installmentAmount);
					$actualInstallmentAmount = sprintf("%.2f", 0);
					$extraInstallmentAmount = sprintf("%.2f", 0);
				endif;

				MfnLoanSchedule::where('loanIdFk', $loanId)
							   ->where('installmentSl', $i+1)
							   ->update(['installmentAmount' 	   => sprintf("%.2f", $installmentAmount),
							   			 'actualInstallmentAmount' => sprintf("%.2f", $actualInstallmentAmount),
							   			 'extraInstallmentAmount'  => sprintf("%.2f", $extraInstallmentAmount),
							   			 'principalAmount' 		   => sprintf("%.2f", $principalAmount),
							   			 'interestAmount' 		   => sprintf("%.2f", $interestAmount)
							   			]);
			endfor;

			$data = array(
				'responseTitle'  		   =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   		   =>  MicroFinance::getMessage('regularLoanUpdateSuccess'),
				'loanId'                   =>  (int) $req->loanId,
				'installmentAmount'        =>  $installmentAmount,
				'actualInstallmentAmount'  =>  $actualInstallmentAmount,
				'extraInstallmentAmount'   =>  $extraInstallmentAmount,
				'principalAmount'          =>  $principalAmount,
				'interestAmount'           =>  $interestAmount,
			);

			return response()->json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DETAILS OF REGULAR LOAN.
		|--------------------------------------------------------------------------
		*/
		public function detailsRegularLoan($regularLoanId) {

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
					'repaymentFrequencyIdFk'	=>  $this->MicroFinance->getNameValueForId($table='mfn_repayment_frequency', $regularLoanDetails->repaymentFrequencyIdFk),
					'loanRepayPeriodIdFk'		=>  $this->MicroFinance->getSingleValueForId($table='mfn_loan_repay_period', $regularLoanDetails->loanRepayPeriodIdFk, 'inMonths'),
					'loanPurpose'				=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_purpose', $loanPurposeOB->purposeIdFK),
					'loanSubPurposeIdFk'		=>  $this->MicroFinance->getNameValueForId($table='mfn_loans_sub_purpose', $regularLoanDetails->loanSubPurposeIdFk),
				),	
				'loanSchedules'       =>  $this->MicroFinance->getLoanSchedule($regularLoanDetails->id, $regularLoanDetails->loanTypeId),
				'MicroFinance'        =>  $this->MicroFinance
			);

			return view('microfin.loan.regularLoan.detailsRegularLoan', ['damageData' => $damageData]);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE REGULAR LOAN.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {
			
			MfnLoan::find($req->id)->delete();
			MfnLoanSchedule::where('loanIdFk', $req->id)->delete();
			MfnFees::where('loanIdFk', $req->id)->delete();
			MfnLoanReschedule::where('loanIdFk', $req->id)->delete(); 
			MfnloanOpeningBalance::where('loanIdFk', $req->id)->delete();   
			
			$data = array(
				'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   =>  MicroFinance::getMessage('regularLoanDelSuccess'),
			);

			return response()->json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: SEARCHING PARAMETERS FUNCTIONS FOR REGULAR LOAN.
		|--------------------------------------------------------------------------
		*/
		public function loadSamityAndPrimaryProductOptions(Request $req) {

			$data = array(
				'samity'  		  =>  $this->MicroFinance->getBranchWiseSamityOptions($req->branchId),
				'primaryProduct'  =>  $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->branchId),
			);
			
			return response::json($data); 
		}
		
	}