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
	use App\Http\Controllers\microfin\MicroFin;
	use App\microfin\member\MfnMemberInformation;
	use App\microfin\samity\MfnSamity;

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

			// if loan code exits than return an error message
			$isLoanCodeExits = (int) DB::table('mfn_loan')->where('softDel',0)->where('loanCode',$req->loanCode)->value('id');

			if ($isLoanCodeExits>0) {
				$data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Loan Code alreary exits!'
                );

                return response::json($data);
			}

			$rules = array(
				'memberIdFk'  				 =>  'required',
				'disbursementDate'  		 =>  'required',
				'productIdFk' 	 			 =>  'required',
				// 'loanCode'		   			 =>  'required|unique:mfn_loan,loanCode',
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

				//	GET HOLIDAY.
				$globalGovtHoliday = $this->MicroFinance->getGlobalGovtHoliday();
				$organizationHoliday = $this->MicroFinance->getOrganizationHoliday(1);
				$branchHoliday = $this->MicroFinance->getBranchHoliday();
				$samityHoliday = $this->MicroFinance->getSamityHoliday($req->memberIdFk);
				$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

				// If first repay date in not in samity day or in holiady, give warning message.
				$firstRepayDate = Carbon::parse($req->firstRepayDate)->format('Y-m-d');

				if (in_array($firstRepayDate,$holiday)) {
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'First Repay Date in Holiday.',
					);
					return response::json($data);
				}

				$samityId = MfnMemberInformation::where('id',$req->memberIdFk)->first()->samityId;
				$samity =  MfnSamity::where('id',$samityId)->select('samityDayOrFixedDateId','samityDayId','fixedDate')->first();

				$firstRepayDate = Carbon::parse($req->firstRepayDate);
	            $weekDayNumber = $firstRepayDate->dayOfWeek == 6 ? 1: $firstRepayDate->dayOfWeek+2;
	            $dayNumber = $firstRepayDate->day;

	            $inSamityDayFlag = 1;

	            // if samity day is weekly wise.
				if ($samity->samityDayOrFixedDateId==1) {
					if ($weekDayNumber != $samity->samityDayId) {
						$inSamityDayFlag = 0;
					}					
				}

				// if samity day is monthly wise.
				else {
					if ($dayNumber != $samity->fixedDate) {
						$inSamityDayFlag = 0;
					}					
				}

				if ($inSamityDayFlag==0) {
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'First Repay Date not in Samity Day.',
					);
					return response::json($data);
				}


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
				/*$interestRateIndexOB = MfnLoanProductInterestRate::where('installmentNum', $req->repaymentNo)->select('interestRateIndex')->first();*/

				//  GET INTEREST RATE INDEX.
				$interestRateIndexOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)
																->where('installmentNum', $req->repaymentNo)
																->where('status', 1)
																->select('interestRateIndex')
																->first();

				// get the years to set the actual interets rate index;
				$months = DB::table('mfn_loan_repay_period')->where('id',$req->loanRepayPeriodIdFk)->first()->inMonths;
				$years = $months/12;
				$inetrestRate = $interestRateIndexOB->interestRateIndex - 1;
				$inetrestRate = $inetrestRate * $years;
				$inetrestRateIndex = 1 + $inetrestRate;


				$req->request->add(['interestRateIndex' => $inetrestRateIndex]);
				$req->request->add(['branchIdFk' => $memberOB->branchId]);
				$req->merge(['disbursementDate' => Carbon::parse($req->disbursementDate)->format('Y-m-d')]);
				$req->merge(['firstRepayDate' => Carbon::parse($req->firstRepayDate)->format('Y-m-d')]);
				if ($req->chequeDate!='') {
					$req->merge(['chequeDate' => Carbon::parse($req->chequeDate)->format('Y-m-d')]);
				}
				// $create = MfnLoan::create($req->all());
				$loanIdOB = MfnLoan::create($req->all());

				//	HOLD THE INSTALLMENT AMOUNT DATA HERE.
				$holdInstallmentAmount = $req->installmentAmount;

				$repaymentFrequencyWiseRepayDate = [
					'1'	 =>  7,
					'2'  =>  30
				];

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
						// $sot = $tos->addMonths($i)->toDateString();
						$sot = $tos->addMonthsNoOverflow($i)->toDateString();

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

					// if(count($scheduleDateArr)==$req->repaymentNo)
					if(count($scheduleDateArr)==$loanIdOB->repaymentNo)
						break;
				endfor;

				//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT.
				$principalAmount = round($req->installmentAmount / $inetrestRateIndex,2);
				$interestAmount = $req->installmentAmount - $principalAmount;

				//	GET LOAN
				// $loanIdOB = DB::table('mfn_loan')->where([['loanCode', $req->loanCode], ['softDel', '=', 0]])->select('id','branchIdFk')->first();

				$totalPrincipal = 0;

				//	GENERATE LOAN SCHEDULE.
				for($i=0;$i<$req->repaymentNo;$i++):
					$req->request->add(['loanIdFk' => $loanIdOB->id]);
					$req->request->add(['installmentSl' => $i+1]);

					if($i==$req->repaymentNo-1):
						//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT FOR LAST INSTALLMENT.
						$installmentAmount = $req->totalRepayAmount-($req->installmentAmount*($req->repaymentNo-1));
						// $principalAmount = $installmentAmount / $inetrestRateIndex;
						$principalAmount = $req->loanAmount - $totalPrincipal;
						$interestAmount = $installmentAmount - $principalAmount;
						$req->request->add(['installmentAmount' => sprintf("%.2f", $installmentAmount)]);
						$req->request->add(['actualInstallmentAmount' => sprintf("%.2f", 0)]);
						$req->request->add(['extraInstallmentAmount' => sprintf("%.2f", 0)]);
					endif;

					$req->request->add(['principalAmount' => sprintf("%.2f", $principalAmount)]);
					$req->request->add(['interestAmount' => sprintf("%.2f", $interestAmount)]);
					$req->request->add(['scheduleDate' => $scheduleDateArr[$i]]);
					$create = MfnLoanSchedule::create($req->all());
					$totalPrincipal += $principalAmount;
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
				$softDate = MicroFin::getSoftwareDateBranchWise($loanIdOB->branchIdFk);
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['loanIdFk' => $loanIdOB->id]);
				$req->merge(['date' => Carbon::parse($softDate)->format('Y-m-d')]);
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

		public function editOpeningBalanceLoan($loanId){

			$loanId = decrypt($loanId);

			$loan = DB::table('mfn_loan')->where('id',$loanId)->first();

			$memberName = DB::table('mfn_member_information')
							->where('id',$loan->memberIdFk)
							->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"))
							->value('nameWithCode');

			$productName = DB::table('mfn_loans_product')
								->where('id',$loan->productIdFk)
								->select(DB::raw("CONCAT(LPAD(code, 3, 0), ' - ', name) AS nameWithCode"))
								->value('nameWithCode');

			$repaymentFrequencyName = DB::table('mfn_repayment_frequency')
										->where('id',$loan->repaymentFrequencyIdFk)
										->value('name');

			$repayPeriodName = DB::table('mfn_loan_repay_period')
									->where('inMonths',$loan->repaymentNo)
									->value('name');

			$loanPurposeName = DB::table('mfn_loans_purpose')
									->where('id',$loan->loanSubPurposeIdFk)
									->value('name');

			$interestCalculationMethodName = DB::table('mfn_loan_interest_calculation_method')
													->where('id',$loan->interestCalculationMethodId)
													->value('name');

			$openingBalance = DB::table('mfn_opening_balance_loan')
									->where('softDel',0)
									->where('loanIdFk',$loan->id)
									->first();

			$ledgerName = DB::table('acc_account_ledger')
								->where('id',$loan->ledgerId)
								->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"))
								->value('nameWithCode');


			$outstandingAmount = $loan->totalRepayAmount - $openingBalance->paidLoanAmountOB;

			$data = array(
				'memberName'  			  		=>  $memberName,
				'loan'  			  			=>  $loan,
				'productName'  			  		=>  $productName,
				'repaymentFrequencyName'  		=>  $repaymentFrequencyName,
				'repayPeriodName'  			  	=>  $repayPeriodName,
				'loanPurposeName'  			  	=>  $loanPurposeName,
				'interestCalculationMethodName' =>  $interestCalculationMethodName,
				'openingBalance'  			  	=>  $openingBalance,
				'ledgerName'  			  		=>  $ledgerName,
				'outstandingAmount'  			=>  $outstandingAmount
			);

			return view('microfin.configuration.openingBalance.loan.editOpeningBalanceLoan', $data);
		}

		public function updateLoan(Request $req){

			$loanId = decrypt($req->loanId);
			$loan = MfnLoan::find($loanId);

			$principalAmountOB = floatval(str_replace(',', '', $req->principalAmountOB));
			$interestAmountOB = floatval(str_replace(',', '', $req->interestAmountOB));

			// IF THE TOTAL OPENING AMOUNT AND COLLECTION AMMOUNT IS LARGE THAN THE REPAY AMOUNT, THAN GIVE A WARNING.
			$collectionAmount = DB::table('mfn_loan_collection')
									->where('softDel',0)
									->where('loanIdFk',$loanId)
									->sum('amount');

			if ($principalAmountOB + $interestAmountOB + $collectionAmount > $loan->totalRepayAmount) {
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Including Loan collection amount, the outstanding is going to be negetive. Please recheck the opening balance amount and collections.',
				);

				return response::json($data);
			}

			// BEFORE STORE INFORMATION FOR REGULAR LOAN CHECK THAT NUMBER OF SCHEDULE IS CORRECT
			$schedules = MfnLoanSchedule::where('softDel',0)->where('loanIdFk',$loanId)->get();

			
			if ($loan->loanTypeId && count($schedules)!=$loan->repaymentNo) {
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Number of Schedule not correct.',
				);

				return response::json($data);
			}

			// STORE INFORMARMATION
			
			$loan->extraInstallmentAmount 	= floatval(str_replace(',', '', $req->extraInstallmentAmount));
			$loan->lastInstallmentAmount 	= floatval(str_replace(',', '', $req->lastInstallmentAmount));
			$loan->installmentAmount 		= floatval(str_replace(',', '', $req->installmentAmount));
			$loan->lastInstallmentAmount 	= floatval(str_replace(',', '', $req->lastInstallmentAmount));
			$loan->note 					= $req->note;
			$loan->save();

			$openingBalance = MfnloanOpeningBalance::where('loanIdFk',$loanId)->first();
			$openingBalance->principalAmountOB 	= $principalAmountOB;
			$openingBalance->interestAmountOB 	= $interestAmountOB;
			$openingBalance->paidLoanAmountOB 	= $openingBalance->principalAmountOB + $openingBalance->interestAmountOB;
			$openingBalance->dueAmountOB 		= $loan->totalRepayAmount - $openingBalance->paidLoanAmountOB;
			$openingBalance->save();

			//	CALCULATING PRINCIPAL AMOUNT AND INTEREST AMOUNT.
			$principalAmount = round($loan->installmentAmount / $loan->interestRateIndex,2);
			$interestAmount = $loan->installmentAmount - $principalAmount;

			// UPDATE SCHEDULE AMOUNT IN SCHEDULE TABLE		

			$totalPrincipal = 0;
			$totalInstallment = 0;

			foreach ($schedules as $key => $schedule) {
				if ($schedule->installmentSl < $loan->repaymentNo) {
					$schedule->installmentAmount 		= $loan->installmentAmount;
					$schedule->extraInstallmentAmount 	= $loan->extraInstallmentAmount;
					$schedule->principalAmount 			= $principalAmount;
					$schedule->interestAmount 			= $interestAmount;
				}
				else{

					$principalAmount = $loan->loanAmount - $totalPrincipal;
					$interestAmount = $loan->totalRepayAmount - $totalInstallment - $principalAmount;

					// $schedule->installmentAmount 		= $loan->lastInstallmentAmount;
					$schedule->installmentAmount 		= $principalAmount + $interestAmount;
					$schedule->principalAmount 			= $principalAmount;
					$schedule->interestAmount 			= $interestAmount;
				}

				$schedule->save();

				$totalPrincipal += $principalAmount;
				$totalInstallment += $loan->installmentAmount;
			}

			// UPDATE LOAN AND SCHEDULE STATUS
			MicroFin::updateLoanStatusNSchedule($loanId);

			$data = array(
				'responseTitle'  =>  'Success!',
				'responseText'   =>  'Information Updated Successfully.'
			);

			return response::json($data);

		}

	}
