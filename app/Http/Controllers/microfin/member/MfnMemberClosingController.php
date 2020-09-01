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
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\gnr\Service;
use App;


use Exception;

class MfnMemberClosingController extends Controller
{

	protected $MicroFinance;

	private $TCN;

	public function __construct()
	{

		$this->MicroFinance = new MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Member Code', 120),
			array('Member Name', 0),
			array('Spouse/Father Name', 0),
			array('Gender', 80),
			array('Branch', 0),
			array('Samity', 0),
			array('Closing Date', 110),
			array('Status', 0),
			array('Entry By', 130),
			array('Action', 80)
		);
	}

	public function index(Request $req)
	{

		$PAGE_SIZE = 20;
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

		if (Auth::user()->branchId == 1) :
			$samity = [];
			$primaryProduct = $this->MicroFinance->getLoanProductsOption();
			$memberClosing = MfnMemberClosing::active();
		else :
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

			//$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);

			//$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);

			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);
			//$memberClosing = MfnMemberClosing::active()->branchWise();
			$memberClosing = MfnMemberClosing::active()->whereIn('branchIdFk', $branchIdArray);
		endif;

		if ($req->has('branchId')) {
			$memberClosing->where('branchIdFk', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}

		if ($req->has('samityId'))
			$memberClosing->where('samityIdFk', $req->get('samityId'));

		if ($req->has('primaryProductId'))
			$memberClosing->where('primaryProductIdFk', $req->get('primaryProductId'));

		if ($req->has('dateFrom'))
			$memberClosing->where('closingDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if ($req->has('dateTo'))
			$memberClosing->where('closingDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if ($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

		if ($req->has('branchId') || $req->has('samityId') || $req->has('primaryProductId') || $req->has('name') || $req->has('dateFrom') || $req->has('dateTo')) {
			$memberClosing = $memberClosing->get();
			$isSearch = 1;
		} else {
			$memberClosing = $memberClosing->paginate($PAGE_SIZE);
			$isSearch = 0;
		}


		if (Auth::user()->branchId == 1) {
			$branchList = MicroFin::getBranchList();
		} else {
			$branchList = DB::table('gnr_branch')
				->whereIn('id', $branchIdArray)
				->orderBy('branchCode')
				->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
				->pluck('nameWithCode', 'id')
				->all();
		}


		//$memberClosing = $memberClosing->paginate($PAGE_SIZE);

		$damageData = array(
			'TCN' 			  =>  $this->TCN,
			'SL' 	   		  =>  $req->has('page') ? $SL : 0,
			'isSearch'        =>  $isSearch,
			'branch'  		  =>  $this->MicroFinance->getAllBranchOptions(),
			'samity'		  =>  $samity,
			'branchList'		  => $branchList,
			'branchIdArray'		  => $branchIdArray,
			'primaryProduct'  =>  $primaryProduct,
			'memberClosing'   =>  $memberClosing,
			'MicroFinance'    =>  $this->MicroFinance
		);

		return view('microfin.member.memberClosing.viewMemberClosing', ['damageData' => $damageData]);
	}




	public function index_old(Request $req)
	{

		$PAGE_SIZE = 20;

		if (Auth::user()->branchId == 1) :
			$samity = [];
			$primaryProduct = $this->MicroFinance->getLoanProductsOption();
			$memberClosing = MfnMemberClosing::active();
		else :
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

			//$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);

			//$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);

			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);
			//$memberClosing = MfnMemberClosing::active()->branchWise();
			$memberClosing = MfnMemberClosing::active()->whereIn('branchIdFk', $branchIdArray);
		endif;

		if ($req->has('branchId')) {
			$memberClosing->where('branchIdFk', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}

		if ($req->has('samityId'))
			$memberClosing->where('samityIdFk', $req->get('samityId'));

		if ($req->has('primaryProductId'))
			$memberClosing->where('primaryProductIdFk', $req->get('primaryProductId'));

		if ($req->has('dateFrom'))
			$memberClosing->where('closingDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if ($req->has('dateTo'))
			$memberClosing->where('closingDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if ($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

		if ($req->has('branchId') || $req->has('samityId') || $req->has('primaryProductId') || $req->has('name') || $req->has('dateFrom') || $req->has('dateTo')) {
			$memberClosing = $memberClosing->get();
			$isSearch = 1;
		} else {
			$memberClosing = $memberClosing->paginate($PAGE_SIZE);
			$isSearch = 0;
		}

		//$memberClosing = $memberClosing->paginate($PAGE_SIZE);

		$damageData = array(
			'TCN' 			  =>  $this->TCN,
			'SL' 	   		  =>  $req->has('page') ? $SL : 0,
			'isSearch'        =>  $isSearch,
			'branch'  		  =>  $this->MicroFinance->getAllBranchOptions(),
			'samity'		  =>  $samity,
			'primaryProduct'  =>  $primaryProduct,
			'memberClosing'   =>  $memberClosing,
			'MicroFinance'    =>  $this->MicroFinance
		);

		return view('microfin.member.memberClosing.viewMemberClosing', ['damageData' => $damageData]);
	}

	public function addMemberClosing()
	{

		$damageData = array(
			'member'  	   =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
			'closingDate'  =>  GetSoftwareDate::getSoftwareDate()
		);



		return view('microfin.member.memberClosing.addMemberClosing', ['damageData' => $damageData]);
	}

	// public function view

	public function deleteItem(Request $req)
	{

		$memberClosing = DB::table('mfn_member_closing')->where('id', $req->id)->first();
		$previousdata = $memberClosing;

		// MATCH THE MEMBER CLOSING DATE AND BRANCH DATE, IF NOT MATCHED THEN GIVE AN ERROR MESSAGE.
		$branchDate = Microfin::getSoftwareDateBranchWise($memberClosing->branchIdFk);

		if ($branchDate != $memberClosing->closingDate) {
			$notification = array(
				'message' => 'Branch Date not matched!',
				'alert-type' => 'error'
			);
			return response()->json($notification);
		}

		// IF ANY PRIMARY PRODUCT TRANSFER TODAY/ AFTER THIS DATE, IT COULD NOT BE CLOSED.
		$isAnyPrimaryProductTransfer = (int) DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('memberIdFk', $memberClosing->memberIdFk)
			->where('transferDate', '>=', $memberClosing->closingDate)
			->value('id');

		if ($isAnyPrimaryProductTransfer > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Member Primary Product Transfer is detected today/later this date. You can not delete closing.'
			);
			return response::json($data);
		}

		$updateMemberInfoTable = DB::table('mfn_member_information')
			->where('id', $memberClosing->memberIdFk)
			->update(
				[
					'closingDate' => '0000-00-00',
					'status'      => 1
				]
			);


		$updateSavingsAcountInfoTable = DB::table('mfn_savings_account')
			->where([['memberIdFk', $memberClosing->memberIdFk], ['closingDate', $memberClosing->closingDate]])
			->update(
				[
					'closingDate' => '0000-00-00',
					'status'      => 1
				]
			);

		$updateMemberClosingInfo = DB::table('mfn_member_closing')
			->where('memberIdFk', $memberClosing->memberIdFk)
			->update(
				[
					'status'      => 0,
					'softDel'     => 1
				]
			);
		$logArray = array(
			'moduleId'  => 6,
			'controllerName'  => 'MfnMemberClosingController',
			'tableName'  => 'mfn_member_closing',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$req->id]
		);
		Service::createLog($logArray);


		$updateSavingsClosing = DB::table('mfn_savings_closing')
			->where([['memberIdFk', $memberClosing->memberIdFk], ['closingDate', $memberClosing->closingDate]])
			->update(
				[
					'softDel' => 1
				]
			);

		$updateSavingsWithdraw = DB::table('mfn_savings_withdraw')
			->where([['memberIdFk', $memberClosing->memberIdFk], ['withdrawDate', $memberClosing->closingDate], ['isFromClosing', '=', 1]])
			->update(
				[
					'softDel' => 1
				]
			);

		if ($updateMemberInfoTable == 1) {
			$notification = array(
				'message' => 'Data deleted successfully!',
				'alert-type' => 'success'
			);
		} else {
			$notification = array(
				'message' => 'Did not able to delete the data!',
				'alert-type' => 'error'
			);
		}

		// return redirect()->back()->with($notification);
		return response()->json($notification);
	}

	public function deleteItemOld(Request $req)
	{
		$member = $req->id;


		// dd($member);

		$colsingMemberId = DB::table('mfn_member_closing')
			// ->select('memberIdFk', 'closingDate')
			->where('id', $req->id)
			->pluck('memberIdFk')
			->toArray();

		$colsingDate = DB::table('mfn_member_closing')
			// ->select('memberIdFk', 'closingDate')
			->where('id', $req->id)
			->pluck('closingDate')
			->toArray();

		// dd($colsingTableInfo);

		$updateMemberInfoTable = DB::table('mfn_member_information')
			->where('id', $colsingMemberId[0])
			->update(
				[
					'closingDate' => '0000-00-00',
					'status'      => 1
				]
			);


		$updateSavingsAcountInfoTable = DB::table('mfn_savings_account')
			->where([['memberIdFk', $colsingMemberId[0]], ['closingDate', $colsingDate[0]]])
			->update(
				[
					'closingDate' => '0000-00-00',
					'status'      => 1
				]
			);

		$updateMemberClosingInfo = DB::table('mfn_member_closing')
			->where('memberIdFk', $colsingMemberId[0])
			->update(
				[
					'status'      => 0,
					'softDel'     => 1
				]
			);

		$updateSavingsClosing = DB::table('mfn_savings_closing')
			->where([['memberIdFk', $colsingMemberId[0]], ['closingDate', $colsingDate[0]]])
			->update(
				[
					'softDel' => 1
				]
			);

		$updateSavingsWithdraw = DB::table('mfn_savings_withdraw')
			->where([['memberIdFk', $colsingMemberId[0]], ['withdrawDate', $colsingDate[0]], ['isFromClosing', '=', 1]])
			->update(
				[
					'softDel' => 1
				]
			);


		// dd($colsingMemberId, $colsingDate, $updateMemberInfoTable, $updateSavingsAcountInfoTable);

		if ($updateMemberInfoTable == 1) {
			$notification = array(
				'message' => 'Data deleted successfully!',
				'alert-type' => 'success'
			);
		} else {
			$notification = array(
				'message' => 'Did not able to delete the data!',
				'alert-type' => 'error'
			);
		}

		// return redirect()->back()->with($notification);
		return response()->json($notification);
	}

	public function loadLoanAndSavingsSummaryForMemberClosing(Request $req)
	{

		//	GET ALL THE SAMITY LIST BRANCWISE.
		$samityList = $this->MicroFinance->getSamity();

		// FIND CURRENT SAMITY ID AND REMOVE FORM THE SAMITY LIST.
		$memberOB = DB::table('mfn_member_information')->where('id', $req->memberId)->select('samityId')->first();

		unset($samityList[""]);
		unset($samityList[$memberOB->samityId]);

		//	GET ALL THE PRIMARY PRODUCT OF THE BRANCH OF THIS SAMITY.
		$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();

		//	GET CURRENT PRIMARY PRDUCT OF THE SELECTED MEMBER.
		$getCurrentPrimaryProduct = $this->MicroFinance->getMultipleValueForId($table = 'mfn_member_information', $req->memberId, ['samityId', 'primaryProductId']);

		//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
		$curPrimaryProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($req->memberId);

		//	GET ALL THE LOAN ACCOUNT OF A MEMBER.
		$loanAccount = $this->MicroFinance->getLoanAccountNumberPerMember($req->memberId);

		$i = 0;
		$loanSummary = [];

		//	GET ALL THE DETAILS OF ALL THE LOAN ACCOUNT OF A MEMBER.
		foreach ($loanAccount as $loanAcc) :
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
		foreach ($savingsAccount as $savingsAcc) :
			$savingsSummary[$i]['savingsCode'] = $savingsAcc['savingsCode'];
			$savingsSummary[$i]['savingsProduct'] = $this->MicroFinance->getSingleValueForId($table = 'mfn_saving_product', $savingsAcc['savingsProductIdFk'], 'shortName');
			$savingsSummary[$i]['openingDate'] = $this->MicroFinance->getMicroFinanceDateFormat($savingsAcc['accountOpeningDate']);
			$savingsSummary[$i]['savingsAmount'] = $savingsAcc['autoProcessAmount'];
			//$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']);
			//$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);
			$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $getCurrentPrimaryProduct->primaryProductId, $curPrimaryProductTransferDate);
			$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $getCurrentPrimaryProduct->primaryProductId, $curPrimaryProductTransferDate);
			$savingsSummary[$i]['savingsBalance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
			$savingsSummary[$i]['remainingAmount'] = $savingsSummary[$i]['savingsBalance'];
			$savingsSummary[$i]['interestAmount'] = '<input class="form-control" type="text" name="interestAmount[]" value="0" />';
			$savingsSummary[$i]['paymentMode'] = '<select id="paymentTypeId' . $i . '" class="form-control paymentTypeId" name="paymentTypeId[]" onchange="loadBankList(this.id)"><option value="Cash">Cash</option><option value="Bank">Bank</option></select>';
			$savingsSummary[$i]['bank'] = '<select class="form-control ledgerId" name="ledgerId[]"><option value="">Select</option></select>';
			$savingsSummary[$i]['chequeNo'] = '<input class="form-control chequeNo" type="text" name="chequeNo[]" />';
			$i++;
		endforeach;

		$getWorkingAreaId = $this->MicroFinance->getWorkingAreaId($getCurrentPrimaryProduct->samityId);

		$data = array(
			'samityOption'           =>  $samityList,
			'currentPrimaryProduct'  =>  $getCurrentPrimaryProduct->primaryProductId,
			'memberName'             =>  $this->MicroFinance->getMemberNameWithCode($req->memberId),
			'branchName'             =>  $this->MicroFinance->getNameValueForId($table = 'gnr_branch', Auth::user()->branchId),
			'samityName'             =>  $this->MicroFinance->getSamityNameWithCode($getCurrentPrimaryProduct->samityId),
			'currentProductName'     =>  $this->MicroFinance->getNameValueForId($table = 'mfn_loans_product', $getCurrentPrimaryProduct->primaryProductId),
			'workingArea'  		     =>  $this->MicroFinance->getNameValueForId($table = 'gnr_working_area', $getWorkingAreaId),
			'loanSummary'	  	     =>  $loanSummary,
			'savingsSummary'         =>  $savingsSummary,
		);

		return response::json($data);
	}

	/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD MEMBER CLOSING CONTROLLER.
		|--------------------------------------------------------------------------
		*/
	public function addItem(Request $req)
	{

		$checkClosingTable = DB::table('mfn_member_closing')
			->where('softDel', 0)
			->where('memberIdFk', $req->memberIdFk)
			->count();


		if ($checkClosingTable > 0) {
			// THIS MEMBER IS ALREADY EXIST IN THE CLOSING TABLE
			return response::json(array('errors' => 'This member is already exist in the closing table. Please try another member to close!'));
		}

		$closingDate = Carbon::parse($req->closingDate)->format('Y-m-d');

		// IF ANY PRIMARY PRODUCT TRANSFER TODAY/ AFTER THIS DATE, IT COULD NOT BE CLOSED.
		$isAnyPrimaryProductTransfer = (int) DB::table('mfn_loan_primary_product_transfer')
		->where('softDel', 0)
		->where('memberIdFk', $req->memberIdFk)
		->where('transferDate', '>=', $closingDate)
		->value('id');

		if ($isAnyPrimaryProductTransfer > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Member Primary Product Transfer is detected today/later this date. You can not perform closing.'
			);
			return response::json($data);
		}

		// MATCH THE CLOSING DATE IS EQUAL TO THE BRANCH DATE OR NOT.
		$memberOB = $this->MicroFinance->getMultipleValueForId($table = 'mfn_member_information', $req->memberIdFk, ['samityId', 'primaryProductId', 'branchId']);
		$branchDate = MicroFin::getSoftwareDateBranchWise($memberOB->branchId);


		if ($branchDate != $closingDate) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Branch date is not matched with the closing date.'
			);
			return response::json($data);
		}

		$rules = array(
			'memberIdFk'  =>  'required',
		);

		$attributesNames = array(
			'memberIdFk'  =>  'member name',
		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributesNames);

		if ($validator->fails())
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
				|           status
				|--------------------------------------------------------------------------
				| UPDATE  : MEMBER 
				|--------------------------------------------------------------------------
				|           status
				|--------------------------------------------------------------------------
				*/


			DB::beginTransaction();

			try {
				// GET THE MEMBER INFORMATION

				//CURRENT TIME
				$now = Carbon::now();

				// IF ANY TRANSACTION EXITS AFTER CLOSING DATE THAN IT COULD NOT BE STORED.
				$isAnyLoanCollection = (int) DB::table('mfn_loan_collection')
					->where('softDel', 0)
					->where('amount', '>', 0)
					->where('memberIdFk', $req->memberIdFk)
					->where('collectionDate', '>', $branchDate)
					->value('id');

				$isAnyDeposit = (int) DB::table('mfn_savings_deposit')
					->where('softDel', 0)
					->where('amount', '>', 0)
					->where('memberIdFk', $req->memberIdFk)
					->where('depositDate', '>', $branchDate)
					->value('id');

				$isAnyWithdraw = (int) DB::table('mfn_savings_withdraw')
					->where('softDel', 0)
					->where('amount', '>', 0)
					->where('memberIdFk', $req->memberIdFk)
					->where('withdrawDate', '>', $branchDate)
					->value('id');

				if (max($isAnyLoanCollection, $isAnyDeposit, $isAnyWithdraw) > 0) {
					$data = array(
						'responseTitle'  =>  'Warning!',
						'responseText'   =>  'Transaction exists after this date. You can not close this member.'
					);
					return response::json($data);
				}

				//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
				$curPrimaryProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($req->memberIdFk);

				//	GET ALL THE LOAN ACCOUNT OF A MEMBER.
				$loanAccount = $this->MicroFinance->getLoanAccountNumberPerMember($req->memberIdFk);

				// if loan account is not closed
				if (!$loanAccount->isEmpty()) {
					foreach ($loanAccount as $loanAcc) :
						$loanOB = $this->MicroFinance->getMultipleValueForId($table = 'mfn_loan', $loanAcc['id'], [
							'id',
							'loanCompletedDate',
							'status',
							'loanCode',
							'softDel',
						]);

						if ($loanOB->softDel == 0) {
							if ($loanOB->loanCompletedDate == '0000-00-00' || $loanOB->status == 0) {
								$data = array(
									'responseTitle'  =>  'Warning!',
									'responseText'   =>  'Loan ' . $loanOB->loanCode . ' is not closed.'
									//'responseText'   =>  $e
								);
								return response::json($data);
								//return response::json($data);
								//return response()->json(['phpError' =>$e->getMessage()], 200);
							}
						}

					//dd($loanOB);
					endforeach;
				}


				$cashLedgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->select('id')->first();

				/*if (!$loanAccount->isEmpty()) { 
						//dd($loanAccount);
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
							dd($loanOB->loanTypeId);
							if($loanOB->loanTypeId==2):
								$loanRepayPeriodOB = MfnLoanRepayPeriod::where('id', $loanOB->loanRepayPeriodIdFk)->select('inMonths')->first();
								dd($loanRepayPeriodOB->inMonths);

								if($loanRepayPeriodOB->inMonths==12)
									$yearCount = 1;
								if($loanRepayPeriodOB->inMonths==24)
									$yearCount = 2;
								if($loanRepayPeriodOB->inMonths==36)
									$yearCount = 3;

								$interestCalculationFactor = 1 + (($loanOB->interestRateIndex / 100) * 365 * $yearCount);


							endif;

							$primaryProductId =  DB::table('mfn_member_information')
							->where('id',$loanOB->memberIdFk)
							->value('primaryProductId');


							//	INSERT DATA INTO LOAN COLLECTION TABLE.
							$collection = New MfnLoanCollection;
							$collection->loanIdFk 		  	  =  $loanOB->id;
							$collection->productIdFk   		  =  $loanOB->productIdFk;
							$collection->primaryProductIdFk	  =  $primaryProductId;
							$collection->loanTypeId   		  =  $loanOB->loanTypeId;
							$collection->memberIdFk 		  =  $loanOB->memberIdFk;
							$collection->branchIdFk 		  =  $loanOB->branchIdFk;
							$collection->samityIdFk   		  =  $loanOB->samityIdFk;
							$collection->collectionDate 	  =  $branchDate; //GetSoftwareDate::getSoftwareDate();
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

					}*/

				//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
				$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberIdFk);
				//dd($savingsAccount);
				$paymentInfo = [];
				if (!$savingsAccount->isEmpty()) {

					$i = 0;

					foreach ($req->paymentTypeId as $paymentType) :
						$paymentInfo[$savingsAccount[$i]->id]['paymentType'] = $req->paymentTypeId[$i];
						$paymentInfo[$savingsAccount[$i]->id]['ledgerId'] = $req->paymentTypeId[$i] == 'Cash' ? (int) $cashLedgerId->id : (int) $req->ledgerId[$i];
						$paymentInfo[$savingsAccount[$i]->id]['chequeNo'] = $req->chequeNo[$i];
						$paymentInfo[$savingsAccount[$i]->id]['interestAmount'] = (int) $req->interestAmount[$i];
						$i++;
					endforeach;

					$accountsBalanceArr = [];

					//	INSERT DATA INTO SAVINGS WITHDRAW TABLE.
					foreach ($savingsAccount as $savingsAcc) :
						/*$balance = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']) - 
							$this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);*/

						/*$balance = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $curPrimaryProductTransferDate) - 
							$this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $curPrimaryProductTransferDate);*/

						$balance = DB::table('mfn_savings_deposit')->where('softDel', 0)->where('accountIdFk', $savingsAcc['id'])->sum('amount') - DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('accountIdFk', $savingsAcc['id'])->sum('amount');

						$balance += DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk', $savingsAcc['id'])->sum('openingBalance');

						if ($balance < 0) {
							throw new Exception("Balance cannot be less than zero!");
						}

						$withdraw = new MfnSavingsWithdraw;
						$withdraw->memberIdFk 		    =  $req->memberIdFk;
						$withdraw->branchIdFk 		    =  $savingsAcc['branchIdFk'];
						$withdraw->samityIdFk   		=  $memberOB->samityId;
						$withdraw->accountIdFk 		    =  $savingsAcc['id'];
						$withdraw->productIdFk 		    =  $savingsAcc['savingsProductIdFk'];
						$withdraw->primaryProductIdFk   =  $memberOB->primaryProductId;
						$withdraw->amount 			    =  $balance;
						// $withdraw->amount 			    =  $balance + $paymentInfo[$savingsAcc['id']]['interestAmount'];
						$withdraw->withdrawDate 		=  $branchDate; // GetSoftwareDate::getSoftwareDate();
						$withdraw->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
						$withdraw->paymentType          =  $paymentInfo[$savingsAcc['id']]['paymentType'];
						$withdraw->ledgerIdFk  		    =  $paymentInfo[$savingsAcc['id']]['ledgerId'];
						$withdraw->chequeNumber  		=  $paymentInfo[$savingsAcc['id']]['chequeNo'];
						$withdraw->isAuthorized         =  1;
						$withdraw->isFromClosing        =  1;
						$withdraw->createdAt 		    =  Carbon::now();
						$withdraw->save();

						$accountsBalanceArr[$savingsAcc['id']] = $balance;
					endforeach;

					//	UPDATE status IN SAVINGS ACCOUNT TABLE.
					foreach ($savingsAccount as $savingsAcc) :
						MfnSavingsAccount::where('id', $savingsAcc['id'])->update(['status' => 0, 'closingDate' => $branchDate]);
					endforeach;



					// INSERT DATA FOR THE SAVINGS CLOSING TABLE
					$j = 0;
					foreach ($savingsAccount as $savingsAcc) :
						// ++$j;
						$savingsAccCode = DB::table('mfn_savings_account')
							->where([['id', $savingsAcc['id']], ['status', '=', 0]])
							->pluck('savingsCode')
							->toArray();

						//dd($savingsAccCode);

						if (sizeof($savingsAccCode) > 0) {
							$savingsDepositeAmount = DB::table('mfn_savings_deposit')
								->where([['memberIdFk', $req->memberIdFk], ['accountIdFk', $savingsAcc['id']]])
								->sum('amount');

							$savingsWithDraw = DB::table('mfn_savings_withdraw')
								->where([['memberIdFk', $req->memberIdFk], ['accountIdFk', $savingsAcc['id']]])
								->sum('amount');

							$totalSavingsInterest = $req->interestAmount[$j];
							$savingsPayableAmount = ($savingsDepositeAmount + $totalSavingsInterest) - $savingsWithDraw;
						} else {
							$savingsDepositeAmount = 0;
							$savingsPayableAmount  = 0;
							$totalSavingsInterest  = 0;
						}
						$insertSavingsClosingData = DB::table('mfn_savings_closing')
							->insert(
								[
									'branchIdFk' => $savingsAcc['branchIdFk'],
									'memberIdFk' => $req->memberIdFk,
									'accountIdFk' => $savingsAcc['id'],
									'depositAmount' => $savingsDepositeAmount,
									'payableAmount' => $savingsPayableAmount,
									'totalSavingInterest' => $totalSavingsInterest,
									'closingAmount' => $savingsDepositeAmount,
									'closingDate' => $branchDate, // GetSoftwareDate::getSoftwareDate(),
									'paymentType' => $paymentInfo[$savingsAcc['id']]['paymentType'],
									'ledgerIdFk' => $paymentInfo[$savingsAcc['id']]['ledgerId'],
									'chequeNumber' => $paymentInfo[$savingsAcc['id']]['chequeNo'],
									'entryByEmployeeIdFk' => Auth::user()->emp_id_fk,
									'status' => 1,
									'softDel' => 0,
									'savingsCode' => $savingsAccCode[0]
								]
							);
						++$j;

						$updateSavingsAccount = DB::table('mfn_savings_account')
							->where([['id', $savingsAcc['id']], ['savingsCode', $savingsAccCode[0]], ['memberIdFk', $req->memberIdFk]])
							->update(
								[
									'closingDate' => $branchDate //GetSoftwareDate::getSoftwareDate()
								]
							);

					// dd($insertSavingsClosingData, $updateSavingsAccount);
					endforeach;
				} //	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.

				//	UPDATE status IN MEMBER TABLE.
				MfnMemberInformation::where('id', $req->memberIdFk)->update(['status' => 0, 'closingDate' => $branchDate]);

				//ENTRY IN CLOSING TABLE
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['branchIdFk' => Auth::user()->branchId]);
				$req->request->add(['primaryProductIdFk' => $memberOB->primaryProductId]);
				$req->request->add(['closingDate' => $branchDate]);
				$req->request->add(['samityIdFk' => $memberOB->samityId]);
				$req->request->add(['closedByFk' => Auth::user()->emp_id_fk]);
				$create = MfnMemberClosing::create($req->all());

				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnMemberClosingController',
					'tableName'  => 'mfn_member_closing',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('mfn_member_closing')->max('id')]
				);
				Service::createLog($logArray);

				DB::commit();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('memberClosingSuccess'),
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
			} catch (\Exception $e) {
				DB::rollback();
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Something went wrong. Please try again.'
					//'responseText'   =>  $e
				);
				//return response::json($data);
				//return response::json($data);
				return response()->json(['phpError' => $e->getMessage()], 200);
			}
		}
	}

	/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: SEARCHING PARAMETERS FUNCTIONS FOR MEMBER CLOSING.
		|--------------------------------------------------------------------------
		*/
	public function loadSamityAndPrimaryProductOptions(Request $req)
	{

		$data = array(
			'samity'  		  =>  $this->MicroFinance->getBranchWiseSamityOptions($req->branchId),
			'primaryProduct'  =>  $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->branchId),
		);

		return response::json($data);
	}
}
