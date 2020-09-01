<?php

	namespace App\Http\Controllers\microfin\configuration\openingBalance\loan;

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

	use App\Http\Controllers\microfin\MicroFin;

	class MfnOneTimeLoanOpeningBalanceController extends Controller {

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

		public function addOneTimeLoan() {

			$damageData = array(
				'member'  			  =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
				//'disbursementDate'    =>  Carbon::today()->toDateString(),
				'disbursementDate'    =>  Carbon::parse(GetSoftwareDate::getSoftwareDate())->format('d-m-Y'),
				'repaymentFrequency'  =>  $this->MicroFinance->getRepaymentFrequencyOptions(),
				'loanRepayPeriod'     =>  $this->MicroFinance->getLoanRepayPeriod(),
				'paymentType'         =>  $this->MicroFinance->getPaymentType(),
				'loanPurpose'         =>  $this->MicroFinance->getLoanPurpose(),
				'boolean'  			  =>  $this->MicroFinance->getBooleanOptions()
			);

			// dd($damageData);

			return view('microfin.configuration.openingBalance.loan.addOneTimeLoan', ['damageData' => $damageData]);
		}
		

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

			$getBranch = DB::table('mfn_member_information')
				->where('id', $req->memberIdFk)
				->pluck('branchId')
				->toArray();

			$softDate = MicroFin::getSoftwareDateBranchWise($getBranch[0]);

				$rules = array(
					'memberIdFk'  				 =>  'required',
					'disbursementDate'  		 =>  'required',
					'productIdFk' 	 			 =>  'required',
					// 'loanCode'		   			 =>  'required|unique:mfn_loan,loanCode',
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
					$req->request->add(['isFromOpening' => 1]);
					$req->request->add(['entryBy' => Auth::user()->emp_id_fk]);

					//	FOR ONE TIME LOAN loanTypeId = 2
					$req->request->add(['loanTypeId' => 2]);

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
					$interestRateIndexOB = MfnLoanProductInterestRate::where('loanProductId', $req->productIdFk)->select('interestRateIndex')->first();
					// dd($interestRateIndexOB);
					$req->request->add(['interestRateIndex' => $interestRateIndexOB->interestRateIndex]);
					$req->request->add(['branchIdFk' => $memberOB->branchId]);
					$req->merge(['disbursementDate' => Carbon::parse($req->disbursementDate)->format('Y-m-d')]);
					$req->merge(['repayDate' => Carbon::parse($req->repayDate)->format('Y-m-d')]);
					if ($req->chequeDate!='') {
						$req->merge(['chequeDate' => Carbon::parse($req->chequeDate)->format('Y-m-d')]);
					}
					$create = MfnLoan::create($req->all());

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
																->select('interestRateIndex')
																->first();

					$interestRateIndex = (fmod($interestRateOB->interestRateIndex, 1) * 100);
					$principalAmount = $req->installmentAmount - $req->interestAmount;

					//	GET LOAN ID.
					$loanIdOB = DB::table('mfn_loan')->where('softDel',0)->where('loanCode', $req->loanCode)->select('id')->first();

					//	GENERATE LOAN SCHEDULE.
					for($i=0;$i<$req->repaymentNo;$i++):
						$req->request->add(['loanIdFk' => $loanIdOB->id]);
						$req->request->add(['installmentSl' => $i+1]);
						$req->request->add(['interestAmount' => sprintf("%.2f", $req->interestAmount)]);
						$req->request->add(['principalAmount' => sprintf("%.2f", $principalAmount)]);
						$req->request->add(['scheduleDate' => $scheduleDateArr[$i]]);
						$create = MfnLoanSchedule::create($req->all());
					endfor;

					DB::table('mfn_loan')->where('id', $loanIdOB->id)->update(['lastInstallmentDate'=>max($scheduleDateArr)]);


					//	GENERATE ADITIONAL INFORMATION FOR OPENING BALANCE OF REGULAR LOAN.
					$now = Carbon::now();
					$req->request->add(['createdDate' => $now]);
					$req->request->add(['loanIdFk' => $loanIdOB->id]);
					$req->merge(['date' => Carbon::parse($softDate)->format('Y-m-d')]);
					$create = MfnloanOpeningBalance::create($req->all());

					$data = array(
						'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
						'responseText'   =>  MicroFinance::getMessage('oneTimeLoanCreateSuccess'),
					);

					return response::json($data);
				}
			
			
		}

	}
