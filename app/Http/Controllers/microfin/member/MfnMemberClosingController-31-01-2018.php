<?php

	namespace App\Http\Controllers\microfin\member;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\member\MfnMemberInformation;
	use App\microfin\member\MfnMemberClosing;
	use App\microfin\loan\MfnLoan;
	use App\microfin\loan\MfnLoanCollection;
	use App\microfin\loan\MfnLoanSchedule;  
	use App\microfin\loan\MfnLoanRepayPeriod; 
	use App\microfin\savings\MfnSavingsAccount;  
	use App\microfin\savings\MfnSavingsWithdraw;  
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

	class MfnMemberClosingController extends Controller {

		protected $MicroFinance;

		private $TCN;
		
		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Member Code', 0),
				array('Member Name', 0),
				array('Spouse/Father Name', 0),
				array('Gender', 0),
				array('Branch', 0),
				array('Samity', 0),
				array('Closing Date', 0),
				array('Status', 0),
				array('Entry By', 130),
				array('Action', 80)
			);
		}

		public function index() {

			$damageData = array(
				'TCN' 			 =>  $this->TCN,
				'memberClosing'  =>  $this->MicroFinance->getActiveMemberClosing(),
				'MicroFinance'   =>  $this->MicroFinance
			);

			return view('microfin.member.memberClosing.viewMemberClosing', ['damageData' => $damageData]);
		}

		public function addMemberClosing() {

			$damageData = array(
				'member'  	   =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
				'closingDate'  =>  GetSoftwareDate::getSoftwareDate()
			);

			return view('microfin.member.memberClosing.addMemberClosing', ['damageData' => $damageData]);
		}

		public function loadLoanAndSavingsSummaryForMemberClosing(Request $req) {

			//	CHECK ALL LOANS ARE CLEAR OR NOT STARTED YET.
			$loanExists = MfnLoan::where('memberIdFk', $req->memberId)->loanIncompleted()->active()->count();

			//	GET ALL THE SAMITY LIST BRANCWISE.
			$samityList = $this->MicroFinance->getSamity();

			// FIND CURRENT SAMITY ID AND REMOVE FORM THE SAMITY LIST.
			$getSamityId = DB::table('mfn_member_information')->where('id', $req->memberId)->select('samityId')->first();
			unset($samityList[""]);
			unset($samityList[$getSamityId->samityId]);

			//	GET ALL THE PRIMARY PRODUCT OF THE BRANCH OF THIS SAMITY.
			$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();
			
			//	GET CURRENT PRIMARY PRDUCT OF THE SELECTED MEMBER.
			$getCurrentPrimaryProduct = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $req->memberId, ['samityId', 'primaryProductId']);

			//	GET ALL THE LOAN ACCOUNT OF A MEMBER.
			$loanAccount = $this->MicroFinance->getLoanAccountNumberPerMember($req->memberId);

			$i = 0;
			$loanSummary = [];
			
			//	GET ALL THE DETAILS OF ALL THE LOAN ACCOUNT OF A MEMBER.
			foreach($loanAccount as $loanAcc):
				$loanSummary[$i]['loanCode'] = $loanAcc['loanCode'];
				$loanSummary[$i]['loanAmount'] = $loanAcc['loanAmount'];
				$loanSummary[$i]['interestAmount'] = $loanAcc['interestAmount'];
				$loanSummary[$i]['discountAmount'] = $loanAcc['interestDiscountAmount'];
				$loanSummary[$i]['totalRepayAmount'] = $loanAcc['totalRepayAmount'];
				$loanSummary[$i]['installmentNum'] = $loanAcc['repaymentNo'];
				$loanSummary[$i]['totalPaymentAmount'] = $this->MicroFinance->getLoanPayment($loanAcc['id']);
				$loanSummary[$i]['outstanding'] = $this->MicroFinance->getRegularLoanOutstanding($loanAcc['id'], $loanAcc['totalRepayAmount']);
				$loanSummary[$i]['interestAmountForOTL'] = '<input class="form-control interestAmountForOTL" type="text" name="interestAmountForOTL" value="0" readonly="readonly">';
				$loanSummary[$i]['paidAmount'] = '<input class="form-control paidAmount" type="text" name="paidAmount[]" value="0">';
				$i++;
			endforeach;
			
			//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
			$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberId);

			$i = 0;
			$savingsSummary = [];
			
			//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
			foreach($savingsAccount as $savingsAcc):
				$savingsSummary[$i]['savingsCode'] = $savingsAcc['savingsCode'];
				$savingsSummary[$i]['savingsProduct'] = $this->MicroFinance->getSingleValueForId($table='mfn_saving_product', $savingsAcc['savingsProductIdFk'], 'shortName');
				$savingsSummary[$i]['openingDate'] = $this->MicroFinance->getMicroFinanceDateFormat($savingsAcc['accountOpeningDate']);
				$savingsSummary[$i]['savingsAmount'] = $savingsAcc['autoProcessAmount'];
				$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']);
				$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);
				$savingsSummary[$i]['savingsBalance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
				$savingsSummary[$i]['remainingAmount'] = $savingsSummary[$i]['savingsBalance'];
				$savingsSummary[$i]['interestAmount'] = '<input class="form-control" type="text" name="interestAmount[]" value="0" />';
				$savingsSummary[$i]['paymentMode'] = '<select id="paymentTypeId'.$i.'" class="form-control paymentTypeId" name="paymentTypeId[]" onchange="loadBankList(this.id)"><option value="Cash">Cash</option><option value="Bank">Bank</option></select>';
				$savingsSummary[$i]['bank'] = '<select class="form-control ledgerId" name="ledgerId[]"><option value="">Select</option></select>';
				$savingsSummary[$i]['chequeNo'] = '<input class="form-control chequeNo" type="text" name="chequeNo[]" />';
				$i++;
			endforeach;

			$getWorkingAreaId = $this->MicroFinance->getWorkingAreaId($getCurrentPrimaryProduct->samityId);

			$data = array(
				'samityOption'           =>  $samityList,
				//'primaryProduct'         =>  $primaryProduct,
				'currentPrimaryProduct'  =>  $getCurrentPrimaryProduct->primaryProductId,
				'memberName'             =>  $this->MicroFinance->getMemberNameWithCode($req->memberId),
				'branchName'             =>  $this->MicroFinance->getNameValueForId($table='gnr_branch', Auth::user()->branchId),
				'samityName'             =>  $this->MicroFinance->getSamityNameWithCode($getCurrentPrimaryProduct->samityId),
				'currentProductName'     =>  $this->MicroFinance->getNameValueForId($table='mfn_loans_product', $getCurrentPrimaryProduct->primaryProductId),
				'workingArea'  		     =>  $this->MicroFinance->getNameValueForId($table='gnr_working_area', $getWorkingAreaId),
				//'loanAccount'	  	     =>  $loanAccount,
				'loanSummary'	  	     =>  $loanSummary,
				'savingsSummary'         =>  $savingsSummary,
				//'savingsAccount'	     =>  $savingsAccount,
				//'loanCount'			     =>  $loanExists,
				'transferStatus'	     =>  $loanExists>0?0:1
			);
			
			return response::json($data); 
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD MEMBER CLOSING CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'memberIdFk'  =>  'required',
			);

			$attributesNames = array(
				'memberIdFk'  =>  'member name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				/*
				|--------------------------------------------------------------------------
				| ADD     : MEMBER CLOSING
				|--------------------------------------------------------------------------
				| ADD     : LOAN COLLECTION
				|--------------------------------------------------------------------------
				| UPDATE  : LOAN SCHEDULE
				|--------------------------------------------------------------------------
				|           isCompleted
				|			isPartiallyPaid
				|			partiallyPaidAmount
				|--------------------------------------------------------------------------
				| UPDATE  : LOAN 
				|--------------------------------------------------------------------------
				|           isLoanCompleted
				|--------------------------------------------------------------------------
				| ADD     : SAVINGS WITHDRAW
				|--------------------------------------------------------------------------
				| UPDATE  : SAVINGS ACCOUNT 
				|--------------------------------------------------------------------------
				|           isLoanCompleted
				|--------------------------------------------------------------------------
				| UPDATE  : MEMBER 
				|--------------------------------------------------------------------------
				|           status
				|--------------------------------------------------------------------------
				*/

				$memberOB = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $req->memberIdFk, ['samityId', 'primaryProductId']);
				
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['branchIdFk' => Auth::user()->branchId]);
				$req->request->add(['primaryProductIdFk' => $memberOB->primaryProductId]);
				$req->request->add(['closingDate' => GetSoftwareDate::getSoftwareDate()]);
				$req->request->add(['samityIdFk' => $memberOB->samityId]);
				$req->request->add(['closedByFk' => Auth::user()->emp_id_fk]);
				$create = MfnMemberClosing::create($req->all());
	
				//	GET ALL THE LOAN ACCOUNT OF A MEMBER.
				$loanAccount = $this->MicroFinance->getLoanAccountNumberPerMember($req->memberIdFk);
				
				$cashLedgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->select('id')->first();

				$i = 0;
				$interestCalculationFactor = 0;

				foreach($loanAccount as $loanAcc):
					$loanOB = $this->MicroFinance->getMultipleValueForId($table='mfn_loan', $loanAcc['id'], ['id', 
																										     'loanTypeId', 
																										     'memberIdFk',
																										     'productIdFk',
																										     'branchIdFk',
																										     'samityIdFk',
																										     'interestRateIndex',
																										     'loanRepayPeriodIdFk'
																										    ]);
					
					//	FIND OUT LOAN REPAY PERIOD AND
					//	INTEREST CALCULATION FACTOR FOR ONLY ONE TIME LOAN.
					if($loanOB->loanTypeId==2):
						$loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $loanOB->loanRepayPeriodIdFk)->select('inMonths')->first();

						if($loanRepayPeriodOB->inMonths==12)
							$yearCount = 1;
						if($loanRepayPeriodOB->inMonths==24)
							$yearCount = 2;
						if($loanRepayPeriodOB->inMonths==36)
							$yearCount = 3;

						$interestCalculationFactor = 1 + (($loanOB->interestRateIndex / 100) * 365 * $yearCount);
					endif;

					 $primaryProductId = (int) DB::table('mfn_member_information')
                                            ->where('id',$req->memberId)
                                            ->value('primaryProductId');

					//	INSERT DATA INTO LOAN COLLECTION TABLE.
					$collection = New MfnLoanCollection;
					$collection->loanIdFk 		  	  =  $loanOB->id;
					$collection->productIdFk   		  =  $loanOB->productIdFk;
					$collection->primaryProductIdFk   = $primaryProductId;
					$collection->loanTypeId   		  =  $loanOB->loanTypeId;
					$collection->memberIdFk 		  =  $loanOB->memberIdFk;
					$collection->branchIdFk 		  =  $loanOB->branchIdFk;
					$collection->samityIdFk   		  =  $loanOB->samityIdFk;
					$collection->collectionDate 	  =  GetSoftwareDate::getSoftwareDate();
					$collection->amount 			  =  $req->paidAmount[$i];
					$collection->principalAmount 	  =  $loanOB->loanTypeId==1?$req->paidAmount[$i]/$loanOB->interestRateIndex:$req->paidAmount[$i]/$interestCalculationFactor;
					$collection->interestAmount 	  =  $req->paidAmount[$i] - $collection->principalAmount;
					$collection->paymentType          =  'Cash';
					$collection->chequeNumber         =  '';
					$collection->ledgerIdFk  		  =  $cashLedgerId->id;
					$collection->isAuthorized         =  1;
					$collection->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;				
					$collection->createdAt 		      =  Carbon::now();
					$collection->save();
					$i++;
				endforeach;

				//	UPDATE isCompleted, isPartiallyPaid AND partiallyPaidAmount IN LOAN SCHEDULE TABLE.
				foreach($loanAccount as $loanAcc):
					MfnLoanSchedule::where('loanIdFk', $loanAcc['id'])
								   ->update(['isCompleted'         => 1,
								   		     'isPartiallyPaid'     => 0,
								   		     'partiallyPaidAmount' => 0
								   		    ]);
				endforeach;

				//	UPDATE isLoanCompleted IN LOAN TABLE.
				foreach($loanAccount as $loanAcc):
					MfnLoan::where('id', $loanAcc['id'])->update(['isLoanCompleted' => 1]);
				endforeach;

				//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
				$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberIdFk);

				$paymentInfo = [];
				$i = 0;

				foreach($req->paymentTypeId as $paymentType):
					$paymentInfo[$savingsAccount[$i]->id]['paymentType'] = $req->paymentTypeId[$i];
					$paymentInfo[$savingsAccount[$i]->id]['ledgerId'] = $req->ledgerId[$i];
					$paymentInfo[$savingsAccount[$i]->id]['chequeNo'] = $req->chequeNo[$i];
					$paymentInfo[$savingsAccount[$i]->id]['interestAmount'] = $req->interestAmount[$i];
					$i++;
				endforeach;

				$accountsBalanceArr = [];
				
				//	INSERT DATA INTO SAVINGS WITHDRAW TABLE.
				foreach($savingsAccount as $savingsAcc):
					$balance = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']) - $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);

					$withdraw = New MfnSavingsWithdraw;
					$withdraw->memberIdFk 		    =  $req->memberIdFk;
					$withdraw->branchIdFk 		    =  $savingsAcc['branchIdFk'];
					$withdraw->samityIdFk   		=  $memberOB->samityId;
					$withdraw->accountIdFk 		    =  $savingsAcc['id'];
					$withdraw->productIdFk 		    =  $savingsAcc['savingsProductIdFk'];
					$withdraw->primaryProductIdFk   =  $memberOB->primaryProductId;
					$withdraw->amount 			    =  $balance + $paymentInfo[$savingsAcc['id']]['interestAmount'];
					$withdraw->withdrawDate 		=  GetSoftwareDate::getSoftwareDate();
					$withdraw->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
					$withdraw->paymentType          =  $paymentInfo[$savingsAcc['id']]['paymentType'];
					$withdraw->ledgerIdFk  		    =  $paymentInfo[$savingsAcc['id']]['ledgerId'];
					$withdraw->chequeNumber  		=  $paymentInfo[$savingsAcc['id']]['chequeNo'];
					$withdraw->isAuthorized         =  1;
					$withdraw->isTransferred        =  1;
					$withdraw->createdAt 		    =  Carbon::now();
					$withdraw->save();

					$accountsBalanceArr[$savingsAcc['id']] = $balance;
				endforeach;

				//	UPDATE status IN SAVINGS ACCOUNT TABLE.
				foreach($savingsAccount as $savingsAcc):
					MfnSavingsAccount::where('id', $savingsAcc['id'])->update(['status' => 0]);
				endforeach;

				//	UPDATE status IN MEMBER TABLE.
				MfnMemberInformation::where('id', $req->memberIdFk)->update(['status' => 0]);

				$data = array(
					'responseTitle'  =>  'Success!',
					'responseText'   =>  'Member has been closed successfully.',
					'interestAmount' =>  $req->interestAmount,
					'paymentTypeId'  =>  $req->paymentTypeId,
					'ledgerId'       =>  $req->ledgerId,
					'chequeNo'       =>  $req->chequeNo,
					'paymentInfo'    =>  $paymentInfo,
					'savingsAccount' =>  $savingsAccount,
					'loanAccount'    =>  $loanAccount,
					'paidAmount'     =>  $req->paidAmount
 				);
								
				return response::json($data);
			}
		}
	}