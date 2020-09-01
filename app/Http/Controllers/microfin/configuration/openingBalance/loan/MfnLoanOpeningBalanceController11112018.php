<?php

	namespace App\Http\Controllers\microfin\configuration\openingBalance\loan;

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

	class MfnLoanOpeningBalanceController extends Controller {

		protected $MicroFinance;

		use GetSoftwareDate;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: SHOW OPENING BALANCE REGULAR LOAN FORM.
		|--------------------------------------------------------------------------
		*/
		public function addOpeningBalanceLoan() {

			$damageData = array(
				'member'  			  =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
				//'disbursementDate'    =>  Carbon::today()->toDateString(),
				'disbursementDate'    =>  Carbon::parse(GetSoftwareDate::getSoftwareDate())->format('d-m-Y'),
				'paymentType'         =>  $this->MicroFinance->getPaymentType(),
				'loanPurpose'         =>  $this->MicroFinance->getLoanPurpose(),
				'boolean'  			  =>  $this->MicroFinance->getBooleanOptions()
			);

			// dd($damageData);

			return view('microfin.configuration.openingBalance.loan.addOpeningBalanceLoan', ['damageData' => $damageData]);
		}

		public function generateLoanCodeLoanCycleWise(Request $req) {

			//	GENERATE LOAN CODE LOAN CYCLE WISE.
			$getMemberCode = $this->MicroFinance->getSingleValueForId($table='mfn_member_information', $req->memberId, 'code');
			$regularLoanShortName = $this->MicroFinance->getSingleValueForId($table='mfn_loans_product', $req->id, 'shortName');

			//	GENERATE LOAN CODE.
			$loanCode = $regularLoanShortName . '.' . $getMemberCode . '.' . $req->loanCycle;

			$data = array(
				'loanCode' => $loanCode
			);

			// dd($data);

			return response::json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD OPENING BALANCE REGULAR LOAN.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {
			// dd($req);

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
				'paidLoanAmountOB'  		 =>  'required',
				'principalAmountOB'  		 =>  'required',
				'interestAmountOB'  		 =>  'required',
				// 'date'  		 			 =>  'required',
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
				$req->request->add(['isFromOpening' => 1]);

				//	FOR REGULAR LOAN loanTypeId = 1
				$req->request->add(['loanTypeId' => 1]);

				//	WHEN PAYMENT TYPE IS CASH, THEN SET CASH IN HAND LEDGER ID.
				if($req->paymentTypeIdFk=='Cash'):
					$cashLedgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->select('id')->first();
					$req->request->add(['ledgerId' => $cashLedgerId->id]);
				endif;

				//	GET SAMITY ID OF THE MEMBER.
				$memberOB = DB::table('mfn_member_information')->where('id', $req->memberIdFk)->select('samityId','primaryProductId','branchId')->first();
				$req->request->add(['samityIdFk' => $memberOB->samityId]);
				$req->request->add(['primaryProductIdFk' => $memberOB->primaryProductId]);

				//  GET INTEREST RATE INDEX.
				$interestRateIndexOB = MfnLoanProductInterestRate::where('installmentNum', $req->repaymentNo)->select('interestRateIndex')->first();
				$req->request->add(['interestRateIndex' => $interestRateIndexOB->interestRateIndex]);
				$req->request->add(['branchIdFk' => $memberOB->branchId]);
				$req->merge(['disbursementDate' => Carbon::parse($req->disbursementDate)->format('Y-m-d')]);
					if ($req->chequeDate!='') {
						$req->merge(['chequeDate' => Carbon::parse($req->chequeDate)->format('Y-m-d')]);
					}
				$create = MfnLoan::create($req->all());

				//	HOLD THE INSTALLMENT AMOUNT DATA HERE.
				$holdInstallmentAmount = $req->installmentAmount;

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

				DB::table('mfn_loan')->where('id', $loanIdOB->id)->update(['lastInstallmentDate'=>max($scheduleDateArr)]);

				//	GENERATE ALL LOAN FEES.
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['name' => 'loan']);
				$req->request->add(['loanIdFk' => $loanIdOB->id]);
				$req->request->add(['loanAdditionalFee' => $req->additionalFee]);
				$req->request->add(['loanFormFee' => $req->loanFormFee]);
				$create = MfnFees::create($req->all());

				//	GENERATE ADITIONAL INFORMATION FOR OPENING BALANCE OF REGULAR LOAN.
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['loanIdFk' => $loanIdOB->id]);
				// $req->merge(['date' => Carbon::parse($softDate)->format('Y-m-d')]);
				$create = MfnloanOpeningBalance::create($req->all());

				//	UPDATE LOAN SCHEDULE FOR INSTALLMENT COMPLETED.
				//$installmentComplted = floor($req->principalAmountOB / $holdInstallmentAmount);
				$installmentComplted = floor($req->paidLoanAmountOB / $holdInstallmentAmount);
				//$partialInstallment = $req->principalAmountOB % $holdInstallmentAmount;
				$partialInstallment = $req->paidLoanAmountOB % $holdInstallmentAmount;

				MfnLoanSchedule::where('loanIdFk', $loanIdOB->id)->where('installmentSl', '<=', $installmentComplted)->update(['isCompleted' => 1]);

				if($partialInstallment!=0):
					MfnLoanSchedule::where('loanIdFk', $loanIdOB->id)->where('installmentSl', '=', $installmentComplted+1)->update(['isPartiallyPaid' => 1, 'partiallyPaidAmount' => $partialInstallment]);
				endif;

				$data = array(
					'responseTitle'  =>  'Success!',
					'responseText'   =>  'New regular loan has been issued successfully.',
				);

				return response::json($data);
			}
		}

	}
