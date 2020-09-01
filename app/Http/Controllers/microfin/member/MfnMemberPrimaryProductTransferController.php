<?php

namespace App\Http\Controllers\microfin\member;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\member\MfnMemberPrimaryProductTransfer;
use App\microfin\member\MfnMemberInformation;
use App\microfin\savings\MfnSavingsDeposit;
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

class MfnMemberPrimaryProductTransferController extends Controller
{

	protected $MicroFinance;

	use GetSoftwareDate;

	private $TCN;

	public function __construct()
	{

		$this->MicroFinance = new MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Member Name', 0),
			array('Old Product Name', 0),
			array('New Product Name', 0),
			array('Transfer Date', 120),
			array('Entry By', 180),
			array('Action', 80)
		);
	}

	public function index(Request $req)
	{

		$PAGE_SIZE = 50;
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

		if (Auth::user()->branchId == 1) :
			$samity = [];
			$primaryProduct = [];
			$memberProductTransfer = MfnMemberPrimaryProductTransfer::where('mfn_loan_primary_product_transfer.status', 1);
		else :
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

			//$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);

			//$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);

			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);

			//$memberProductTransfer = MfnMemberPrimaryProductTransfer::where('mfn_loan_primary_product_transfer.status', 1)->branchWise();

			$memberProductTransfer = MfnMemberPrimaryProductTransfer::where('mfn_loan_primary_product_transfer.status', 1)->whereIn('branchIdFk', $branchIdArray);
		endif;
		// dd($memberSamityTransfer->paginate(50));

		if ($req->has('branchId')) {
			$memberProductTransfer->where('branchIdFk', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}

		if ($req->has('samityId'))
			$memberProductTransfer->where('samityIdFk', $req->get('newSamityId'));

		if ($req->has('newPrimaryProductId'))
			$memberProductTransfer->where('newPrimaryProductFk', $req->get('newPrimaryProductId'));

		if ($req->has('oldPrimaryProductId'))
			$memberProductTransfer->where('oldPrimaryProductFk', $req->get('oldPrimaryProductId'));

		if ($req->has('memberCode')) {
			$memberProductTransfer->join('mfn_member_information', 'mfn_member_information.id', '=', 'mfn_loan_primary_product_transfer.memberIdFk')
				->select('mfn_loan_primary_product_transfer.*', 'mfn_member_information.code')
				->where('code', $req->get('memberCode'));
		}

		if ($req->has('dateFrom'))
			$memberProductTransfer->where('transferDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if ($req->has('dateTo'))
			$memberProductTransfer->where('transferDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if ($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

		if ($req->has('branchId') || $req->has('samityId') || $req->has('newPrimaryProductId') || $req->has('oldPrimaryProductId') || $req->has('memberCode') || $req->has('dateFrom') || $req->has('dateTo')) {
			// $memberSamityTransfer = $memberSamityTransfer->get();
			$isSearch = 1;
		} else {
			// $memberSamityTransfer = $memberSamityTransfer->paginate($PAGE_SIZE);
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


		$memberProductTransfer = $memberProductTransfer->where('mfn_loan_primary_product_transfer.softDel', 0)->paginate($PAGE_SIZE);

		$TCN = $this->TCN;

		$damageData = array(
			'TCN' 	   					    =>	$TCN,
			'SL' 	   						=>	$req->has('page') ? $SL : 0,
			'isSearch'          			=>  $isSearch,
			'branch'  						=>  $this->MicroFinance->getAllBranchOptions(),
			'samity'						=>  $samity,
			'branchList'						=> $branchList,
			'branchIdArray'						=> $branchIdArray,
			'primaryProduct'  				=>  $primaryProduct,
			// 'memberPrimaryProductTransfer'  =>  $this->MicroFinance->getMemberPrimaryProductTransfer(),
			'memberPrimaryProductTransfer'  =>  $memberProductTransfer,
			'gender'   						=>  $this->MicroFinance->getGender(),
			'dataNotAvailable'				=>	$this->MicroFinance->dataNotAvailable(),
			'MicroFinance'      			=>  $this->MicroFinance
		);

		return view('microfin.member.memberPrimaryProductTransfer.viewMemberPrimaryProductTransfer', ['damageData' => $damageData]);
	}


	public function index_old(Request $req)
	{

		$PAGE_SIZE = 50;

		if (Auth::user()->branchId == 1) :
			$samity = [];
			$primaryProduct = [];
			$memberProductTransfer = MfnMemberPrimaryProductTransfer::where('mfn_loan_primary_product_transfer.status', 1);
		else :
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

			//$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);

			//$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);

			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($branchIdArray);

			//$memberProductTransfer = MfnMemberPrimaryProductTransfer::where('mfn_loan_primary_product_transfer.status', 1)->branchWise();

			$memberProductTransfer = MfnMemberPrimaryProductTransfer::where('mfn_loan_primary_product_transfer.status', 1)->whereIn('branchIdFk', $branchIdArray);
		endif;
		// dd($memberSamityTransfer->paginate(50));

		if ($req->has('branchId')) {
			$memberProductTransfer->where('branchIdFk', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}

		if ($req->has('samityId'))
			$memberProductTransfer->where('samityIdFk', $req->get('newSamityId'));

		if ($req->has('newPrimaryProductId'))
			$memberProductTransfer->where('newPrimaryProductFk', $req->get('newPrimaryProductId'));

		if ($req->has('oldPrimaryProductId'))
			$memberProductTransfer->where('oldPrimaryProductFk', $req->get('oldPrimaryProductId'));

		if ($req->has('memberCode')) {
			$memberProductTransfer->join('mfn_member_information', 'mfn_member_information.id', '=', 'mfn_loan_primary_product_transfer.memberIdFk')
				->select('mfn_loan_primary_product_transfer.*', 'mfn_member_information.code')
				->where('code', $req->get('memberCode'));
		}

		if ($req->has('dateFrom'))
			$memberProductTransfer->where('transferDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if ($req->has('dateTo'))
			$memberProductTransfer->where('transferDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if ($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

		if ($req->has('branchId') || $req->has('samityId') || $req->has('newPrimaryProductId') || $req->has('oldPrimaryProductId') || $req->has('memberCode') || $req->has('dateFrom') || $req->has('dateTo')) {
			// $memberSamityTransfer = $memberSamityTransfer->get();
			$isSearch = 1;
		} else {
			// $memberSamityTransfer = $memberSamityTransfer->paginate($PAGE_SIZE);
			$isSearch = 0;
		}

		$memberProductTransfer = $memberProductTransfer->where('mfn_loan_primary_product_transfer.softDel', 0)->paginate($PAGE_SIZE);

		$TCN = $this->TCN;

		$damageData = array(
			'TCN' 	   					    =>	$TCN,
			'SL' 	   						=>	$req->has('page') ? $SL : 0,
			'isSearch'          			=>  $isSearch,
			'branch'  						=>  $this->MicroFinance->getAllBranchOptions(),
			'samity'						=>  $samity,
			'primaryProduct'  				=>  $primaryProduct,
			// 'memberPrimaryProductTransfer'  =>  $this->MicroFinance->getMemberPrimaryProductTransfer(),
			'memberPrimaryProductTransfer'  =>  $memberProductTransfer,
			'gender'   						=>  $this->MicroFinance->getGender(),
			'dataNotAvailable'				=>	$this->MicroFinance->dataNotAvailable(),
			'MicroFinance'      			=>  $this->MicroFinance
		);

		return view('microfin.member.memberPrimaryProductTransfer.viewMemberPrimaryProductTransfer', ['damageData' => $damageData]);
	}


	public function addMemberPrimaryProductTransfer()
	{

		$damageData = array(
			'member'  		=>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
			'transferDate'  =>  GetSoftwareDate::getSoftwareDate()
		);

		return view('microfin.member.memberPrimaryProductTransfer.addMemberPrimaryProductTransfer', ['damageData' => $damageData]);
	}

	public function loadPrimaryPrduct(Request $req)
	{

		$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();

		//	GET CURRENT PRIMARY PRDUCT OF THE SELECTED MEMBER.
		$getCurrentPrimaryProduct = $this->MicroFinance->getMultipleValueForId($table = 'mfn_member_information', $req->memberId, ['samityId', 'primaryProductId']);

		//	CUT THE CURRENT PRIMARY PRODUCT FROM THE PRODUCT LIST.
		foreach ($primaryProduct as $key => $val) :
			if ($key == $getCurrentPrimaryProduct->primaryProductId) :
				unset($primaryProduct[$key]);
			endif;
		endforeach;

		//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
		$getCurProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($req->memberId);

		//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
		$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberId);

		$i = 0;
		$savingsSummary = [];

		//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
		foreach ($savingsAccount as $savingsAcc) :
			$savingsSummary[$i]['savingsCode'] = $savingsAcc['savingsCode'];
			$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $getCurrentPrimaryProduct->primaryProductId, $getCurProductTransferDate);
			$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $getCurrentPrimaryProduct->primaryProductId, $getCurProductTransferDate);
			$savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
			$i++;
		endforeach;

		$data = array(
			'primaryProduct'      =>  $primaryProduct,
			'memberName'          =>  $this->MicroFinance->getMemberNameWithCode($req->memberId),
			'samityName'          =>  $this->MicroFinance->getSamityNameWithCode($getCurrentPrimaryProduct->samityId),
			'currentProductName'  =>  $this->MicroFinance->getNameValueForId($table = 'mfn_loans_product', $getCurrentPrimaryProduct->primaryProductId),
			'savingsSummary'      =>  $savingsSummary,
			'savingsAccount'	  =>  $savingsAccount,
		);

		return response::json($data);
	}

	public function loadLoanAndSavingsSummaryForPrimaryProductTransfer(Request $req)
	{

		//	CHECK ALL LOANS ARE CLEAR OR NOT STARTED YET.
		// $loanExists = MfnLoan::where('memberIdFk', $req->memberId)->loanIncompleted()->active()->count();

		$primaryProductLoanIds = DB::table('mfn_loans_product')
			->where('isPrimaryProduct', 1)
			->pluck('id')
			->toArray();

		// dd($loanExists);
		$softwareDate = GetSoftwareDate::getSoftwareDate();

		$checkMemberId = $req->memberId;

		$loanExists = DB::table('mfn_loan')
			// ->where([['memberIdFk', $req->memberId], ['loanCompletedDate', '=', '0000-00-00'], ['status', '=', 1], ['isLoanCompleted', '=', 0]])
			// ->orWhere([['memberIdFk', $req->memberId], ['loanCompletedDate', '>', $softwareDate], ['isLoanCompleted', '=', 1]])
			->where(function ($query) use ($checkMemberId, $softwareDate) {
				$query->where([['memberIdFk', $checkMemberId], ['loanCompletedDate', '=', '0000-00-00']])
					->orWhere([['memberIdFk', $checkMemberId], ['loanCompletedDate', '>', $softwareDate]]);
			})
			->whereIn('productIdFk', $primaryProductLoanIds)
			->count();



		//	GET ALL THE SAMITY LIST BRANCWISE.
		$samityList = $this->MicroFinance->getSamity();

		// FIND CURRENT SAMITY ID AND REMOVE FORM THE SAMITY LIST.
		$getSamityId = DB::table('mfn_member_information')->where('id', $req->memberId)->select('samityId')->first();
		unset($samityList[""]);
		unset($samityList[$getSamityId->samityId]);

		//	GET ALL THE PRIMARY PRODUCT OF THE BRANCH OF THIS SAMITY.
		$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();

		//	GET CURRENT PRIMARY PRDUCT OF THE SELECTED MEMBER.
		$getCurrentPrimaryProduct = $this->MicroFinance->getMultipleValueForId($table = 'mfn_member_information', $req->memberId, ['samityId', 'primaryProductId']);

		//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
		$getCurProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($req->memberId);

		//	GET ALL THE LOAN ACCOUNT OF A MEMBER.
		$loanAccount = $this->MicroFinance->getLoanAccountNumberPerMember($req->memberId);

		$i = 0;
		$loanSummary = [];

		//	GET ALL THE DETAILS OF ALL THE LOAN ACCOUNT OF A MEMBER.
		foreach ($loanAccount as $loanAcc) :
			$loanSummary[$i]['loanCode'] = $loanAcc['loanCode'];
			$loanSummary[$i]['loanAmount'] = $loanAcc['loanAmount'];
			$loanSummary[$i]['totalRepayAmount'] = $loanAcc['totalRepayAmount'];
			$loanSummary[$i]['a'] = '';
			$loanSummary[$i]['b'] = '';
			$i++;
		endforeach;

		//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
		$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberId);

		$i = 0;
		$savingsSummary = [];

		//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
		foreach ($savingsAccount as $savingsAcc) :
			$savingsSummary[$i]['savingsCode'] = $savingsAcc['savingsCode'];
			$savingsSummary[$i]['openingDate'] = $savingsAcc['accountOpeningDate'];
			//$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']);
			//$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);
			$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $getCurrentPrimaryProduct->primaryProductId, $getCurProductTransferDate);
			$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $getCurrentPrimaryProduct->primaryProductId, $getCurProductTransferDate);
			$savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
			$i++;
		endforeach;

		$getWorkingAreaId = $this->MicroFinance->getWorkingAreaId($getCurrentPrimaryProduct->samityId);

		$data = array(
			'samityOption'           =>  $samityList,
			'primaryProduct'         =>  $primaryProduct,
			'currentPrimaryProduct'  =>  $getCurrentPrimaryProduct->primaryProductId,
			'memberName'             =>  $this->MicroFinance->getMemberNameWithCode($req->memberId),
			'branchName'             =>  $this->MicroFinance->getNameValueForId($table = 'gnr_branch', Auth::user()->branchId),
			'samityName'             =>  $this->MicroFinance->getSamityNameWithCode($getCurrentPrimaryProduct->samityId),
			'currentProductName'     =>  $this->MicroFinance->getNameValueForId($table = 'mfn_loans_product', $getCurrentPrimaryProduct->primaryProductId),
			'workingArea'  		     =>  $this->MicroFinance->getNameValueForId($table = 'gnr_working_area', $getWorkingAreaId),
			'loanAccount'	  	     =>  $loanAccount,
			'loanSummary'	  	     =>  $loanSummary,
			'savingsSummary'         =>  $savingsSummary,
			'savingsAccount'	     =>  $savingsAccount,
			'loanCount'			     =>  $loanExists,
			'transferStatus'	     =>  $loanExists
		);


		return response::json($data);
	}

	public function addItem(Request $req)
	{

		$rules = array(
			'memberIdFk'		   =>  'required',
			'newPrimaryProductFk'  =>  'required',
		);

		$attributesNames = array(
			'memberIdFk'  =>  'member name'
		);

		$transferDate = Carbon::parse($req->transferDate)->format('Y-m-d');

		$branchId = DB::table('mfn_member_information')->where('id', $req->memberIdFk)->first()->branchId;

		$branchDate = MicroFin::getSoftwareDateBranchWise($branchId);

		if ($branchDate != $transferDate) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, Transfer date is not matched with the branch date.',
			);
			return response()->json($data);
		}

		$checkSavingsDeposite = DB::table('mfn_savings_deposit')
			->where([['memberIdFk', (int) $req->memberIdFk], ['depositDate', '>', $transferDate], ['softDel', '=', 0]])
			->sum('amount');

		$checkSavingsWithdraw = DB::table('mfn_savings_withdraw')
			->where([['memberIdFk', (int) $req->memberIdFk], ['withdrawDate', '>', $transferDate], ['softDel', '=', 0]])
			->sum('amount');

		if ($checkSavingsDeposite > 0 || $checkSavingsWithdraw > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, You can not update this transfer, because Savings Transaction Exist!',
			);
			return response()->json($data);
		}

		// IF ANY TODAY/FUTURITY TRANSFER EXISTS AFTER SOFTWARE DATE, THEN IT CANT BE TRANSFERED.
		$isFuturityTranferExists = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('memberIdFk', (int) $req->memberIdFk)
			->where('transferDate', '>=', $transferDate)
			->value('id');

		if ($isFuturityTranferExists > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, You can not update this transfer, because Today/Futurity Transfer Exist!',
			);
			return response()->json($data);
		}

		// IF CURRENTLY IT HAS ANY PRIMARY PRODUCT LOAN, THAN TRANSFER COULD NOT BE UPDATED.
		$primaryProductLoanIds = DB::table('mfn_loans_product')
			->where('isPrimaryProduct', 1)
			->pluck('id')
			->toArray();

		$runningPrimaryLoanId = (int) DB::table('mfn_loan')
			->where('softDel', 0)
			->where('memberIdFk', (int) $req->memberIdFk)
			->where(function ($query) use ($transferDate) {
				$query->where('loanCompletedDate', '0000-00-00')
					->orWhere('loanCompletedDate', '>', $transferDate)
					->orWhere('isLoanCompleted', 0);
			})
			->whereIn('productIdFk', $primaryProductLoanIds)
			->value('id');

		if ($runningPrimaryLoanId > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, You can not do this transfer, because you have a running loan.',
			);
			return response()->json($data);
		}

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributesNames);

		if ($validator->fails())
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else {
			/*
			|--------------------------------------------------------------------------
			| ADD     : MEMBER PRIMARY PRODUCT TRANSFER
			|--------------------------------------------------------------------------
			| ADD     : SAVINGS WITHDRAW
			|--------------------------------------------------------------------------
			| ADD     : SAVINGS DEPOSIT
			|--------------------------------------------------------------------------
			| UPDATE  : MEMBER INFORMATION
			|--------------------------------------------------------------------------
			|           primaryProductId
			|--------------------------------------------------------------------------
			*/

			DB::beginTransaction();

			try {



				$transaction = [];

				//	CHECK IF THERE ARE ANY TRANSACTION WHICH DATES ARE GREATER THAN SOFTWARE DATE IN
				//	SAVINGS DEPOSIT, SAVINGS WITHDRAW AND LOAN COLLECTION.
				$transaction[] = $this->MicroFinance->getSavingsDepositExistAfterSoftwareDate($req->memberIdFk);
				$transaction[] = $this->MicroFinance->getSavingsWithdrawExistAfterSoftwareDate($req->memberIdFk);
				$transaction[] = $this->MicroFinance->getLoanCollectionExistAfterSoftwareDate($req->memberIdFk);

				$transactionClear = array_sum($transaction) == 0 ? 1 : 0;

				//	CHECK IF THERE IS ANOTHER PRODUCT TRANSFER IN A SNGLE DAY.
				$productTransferExists = $this->MicroFinance->getCheckAnotherProductTransferExists($req->memberIdFk);
				$productTransferClear = $productTransferExists == 0 ? 1 : 0;

				if ($transactionClear == 1 && $productTransferClear == 1) :
					$transferStatus = 1;
				else :
					$transferStatus = 0;
				endif;

				if ($transferStatus == 1) :
					$now = Carbon::now();
					$req->request->add(['createdDate' => $now]);
					$req->request->add(['branchIdFk' => Auth::user()->branchId]);
					$req->request->add(['entryBy' => Auth::user()->emp_id_fk]);
					$req->request->add(['transferDate' => $this->MicroFinance->getDBDateFormat($req->transferDate)]);

					// GET SAMITY ID OF THE MEMBER.
					$memberOB = $this->MicroFinance->getMultipleValueForId($table = 'mfn_member_information', $req->memberIdFk, ['samityId', 'primaryProductId']);
					$req->request->add(['oldPrimaryProductFk' => $memberOB->primaryProductId]);
					$req->request->add(['samityIdFk' => $memberOB->samityId]);

					//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
					$getCurProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($req->memberIdFk);

					//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
					$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberIdFk);

					$i = 0;
					$savingsSummary = [];
					$totalTransferAmount = 0;

					//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
					foreach ($savingsAccount as $savingsAcc) :
						$savingsSummary[$i]['id'] = $savingsAcc['id'];
						$savingsSummary[$i]['savingsProductId'] = $savingsAcc['savingsProductIdFk'];
						$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $getCurProductTransferDate);
						$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $getCurProductTransferDate);
						$savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
						$totalTransferAmount +=  $savingsSummary[$i]['balance'];
						$i++;
					endforeach;

					$req->request->add(['totalTransferAmount' => $totalTransferAmount]);
					$req->request->add(['savingsRecord' => $savingsSummary]);
					$create = MfnMemberPrimaryProductTransfer::create($req->all());

					$logArray = array(
						'moduleId'  => 6,
						'controllerName'  => 'MfnMemberPrimaryProductTransferController',
						'tableName'  => 'mfn_loan_primary_product_transfer',
						'operation'  => 'insert',
						'primaryIds'  => [DB::table('mfn_loan_primary_product_transfer')->max('id')]
					);
					Service::createLog($logArray);

					$accountsBalanceArr = [];
					$i = 0;

					//	INSERT DATA INTO SAVINGS WITHDRAW TABLE.
					foreach ($savingsAccount as $savingsAcc) :
						$balance = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $getCurProductTransferDate) -
							$this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $getCurProductTransferDate);

						if ($balance < 0) {
							throw new Exception("Balance cannot be less than zero!");
						}

						$withdraw = new MfnSavingsWithdraw;
						$withdraw->memberIdFk 		    =  $req->memberIdFk;
						$withdraw->branchIdFk 		    =  $savingsAcc['branchIdFk'];
						$withdraw->samityIdFk   		=  $savingsAcc['samityIdFk'];
						$withdraw->accountIdFk 		    =  $savingsAcc['id'];
						$withdraw->productIdFk 		    =  $savingsAcc['savingsProductIdFk'];
						$withdraw->primaryProductIdFk   =  $memberOB->primaryProductId;
						$withdraw->amount 			    =  $balance;
						$withdraw->withdrawDate 		=  $transferDate;
						$withdraw->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
						$withdraw->isAuthorized         =  1;
						$withdraw->isTransferred        =  1;
						$withdraw->createdAt 		    =  Carbon::now();
						$withdraw->save();

						$accountsBalanceArr[$savingsAcc['id']] = $balance;
						$i++;
					endforeach;

					//	INSERT DATA INTO SAVINGS DEPOSIT TABLE.
					foreach ($savingsAccount as $savingsAcc) :
						if ($accountsBalanceArr[$savingsAcc['id']] < 0) {
							throw new Exception("Balance cannot be less than zero!");
						}
						$deposit = new MfnSavingsDeposit;
						$deposit->accountIdFk 		   =  $savingsAcc['id'];
						$deposit->productIdFk 		   =  $savingsAcc['savingsProductIdFk'];
						$deposit->primaryProductIdFk   =  $req->newPrimaryProductFk;
						$deposit->memberIdFk 		   =  $req->memberIdFk;
						$deposit->branchIdFk 		   =  $savingsAcc['branchIdFk'];
						$deposit->samityIdFk 		   =  $savingsAcc['samityIdFk'];
						$deposit->amount 			   =  $accountsBalanceArr[$savingsAcc['id']];
						$deposit->depositDate 		   =  $transferDate;
						$deposit->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
						$deposit->isAuthorized         =  1;
						$deposit->isTransferred        =  1;
						$deposit->createdAt 		   =  Carbon::now();
						$deposit->save();
					endforeach;

					//	UPDATE PRIMARY PRODUCT ID IN mfn_samity TABLE.
					$member = MfnMemberInformation::find($req->memberIdFk);
					$member->primaryProductId = $req->newPrimaryProductFk;
					// dd($member->primaryProductId, $req->newPrimaryProductFk, $req->memberIdFk);
					$member->save();

					// UPDATE RUNNIGN OPTIONAL PRODUCT LOANS PRIMARY PRODUCT ID
					DB::table('mfn_loan')
						->where('softDel', 0)
						->where('memberIdFk', (int) $req->memberIdFk)
						->where('isLoanCompleted', 0)
						->whereNotIn('productIdFk', $primaryProductLoanIds)
						->update(['primaryProductIdFk' => $req->newPrimaryProductFk]);


				endif;

				DB::commit();

				$data = array(
					'responseTitle'  =>  $transferStatus == 1 ? MicroFinance::getMessage('msgSuccess') : MicroFinance::getMessage('msgWarning'),
					'responseText'   =>  $transferStatus == 1 ? MicroFinance::getMessage('primaryProductTransferSuccess') : MicroFinance::getMessage('primaryProductTransferWarning'),
				);

				return response::json($data);
			} catch (\Exception $e) {
				DB::rollback();
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Something went wrong. Please try again.'
				);
				return response::json($data);
			}
		}
	}

	public function detailsMemberPrimaryProductTransfer($memberPrimaryProductTransferId)
	{

		$primaryProductDetailsTCN = array(
			'oldProductInfo'	  =>  'Old Product Info',
			'newProductInfo'	  =>  'New Product Info',
			'memberName'	      =>  'Member Name',
			'memberCode'	      =>  'Member Code',
			'branchName'	      =>  'Branch Name',
			'samityName'	      =>  'Samity Name',
			'primaryProductName'  =>  'Primary Product Name',
			'transferDate'  	  =>  'Transfer Date'
		);

		$savingsDetailsTCN = array(
			'saving'  	  =>  'Saving',
			'savingCode'  =>  'Saving Code',
			'deposite'	  =>  'Deposite',
			'withdraw'	  =>  'Withdraw',
			'balance'	  =>  'Balance'
		);

		$memberPrimaryProductTransferDetails = $this->MicroFinance->getMemberPrimaryProductTransferDetails($memberPrimaryProductTransferId);
		$getMemberDetails = $this->MicroFinance->getMultipleValueForId($table = 'mfn_member_information', $memberPrimaryProductTransferDetails->memberIdFk, ['name', 'code', 'branchId', 'samityId', 'primaryProductId']);
		$oldProductName = $this->MicroFinance->getMultipleValueForId($table = 'mfn_loans_product', $memberPrimaryProductTransferDetails->oldPrimaryProductFk, ['name']);
		$newProductName = $this->MicroFinance->getMultipleValueForId($table = 'mfn_loans_product', $memberPrimaryProductTransferDetails->newPrimaryProductFk, ['name']);

		$damageData = array(
			'primaryProductDetailsTCN'  		   =>  $primaryProductDetailsTCN,
			'savingsDetailsTCN'  				   =>  $savingsDetailsTCN,
			'memberDetails'						   =>  $getMemberDetails,
			'branchName'						   =>  $this->MicroFinance->getNameValueForId($table = 'gnr_branch', $getMemberDetails->branchId),
			'samityName'						   =>  $this->MicroFinance->getNameValueForId($table = 'mfn_samity', $getMemberDetails->samityId),
			'oldProductName'					   =>  $oldProductName->name,
			'newProductName'					   =>  $newProductName->name,
			'memberPrimaryProductTransferDetails'  =>  $memberPrimaryProductTransferDetails,
			'savingsDetails'  					   =>  json_decode($memberPrimaryProductTransferDetails->savingsRecord),
			'MicroFinance'        				   =>  $this->MicroFinance,
		);

		return view('microfin.member.memberPrimaryProductTransfer.detailsMemberPrimaryProductTransfer', ['damageData' => $damageData]);
	}

	public function updateMemberPrimaryProductTransfer($memberPrimaryProductTransferId)
	{

		$memberPrimaryProductTransferDetails = $this->MicroFinance->getMemberPrimaryProductTransferDetails($memberPrimaryProductTransferId);

		$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();

		//	CUT THE OLD PRIMARY PRODUCT FROM THE PRODUCT LIST.
		foreach ($primaryProduct as $key => $val) :
			if ($key == $memberPrimaryProductTransferDetails->oldPrimaryProductFk) :
				unset($primaryProduct[$key]);
			endif;
		endforeach;

		$damageData = array(
			'memberPrimaryProductTransferId'      => $memberPrimaryProductTransferId,
			'memberPrimaryProductTransferDetails' => $memberPrimaryProductTransferDetails,
			//'member'  							  =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
			'member'  							  =>  array($memberPrimaryProductTransferDetails->memberIdFk => $this->MicroFinance->getMemberNameWithCode($memberPrimaryProductTransferDetails->memberIdFk)),
			'transferDate'  					  =>  GetSoftwareDate::getSoftwareDate(),

			'primaryProduct'      =>  $primaryProduct,
		);

		// dd($damageData);

		return view('microfin.member.memberPrimaryProductTransfer.editMemberPrimaryProductTransfer', $damageData);
	}

	public function updateItem(Request $req)
	{

		// IF CURRENTLY IT HAS ANY PRIMARY PRODUCT LOAN, THAN TRANSFER COULD NOT BE UPDATED.
		$primaryProductLoanIds = DB::table('mfn_loans_product')
			->where('isPrimaryProduct', 1)
			->pluck('id')
			->toArray();

		$transferDate = Carbon::parse($req->transferDate)->format('Y-m-d');

		$branchId = DB::table('mfn_member_information')->where('id', $req->memberIdFk)->first()->branchId;

		$branchDate = MicroFin::getSoftwareDateBranchWise($branchId);

		if ($branchDate != $transferDate) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, Transfer date is not matched with the branch date.',
			);
			return response()->json($data);
		}

		// IF ANY FUTURITY TRANSFER EXISTS AFTER SOFTWARE DATE, THEN IT CANT BE TRANSFERED.
		$isFuturityTranferExists = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('memberIdFk', (int) $req->memberIdFk)
			->where('transferDate', '>', $transferDate)
			->value('id');

		if ($isFuturityTranferExists > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, You can not update this transfer, because Futurity Transfer Exist!',
			);
			return response()->json($data);
		}

		$runningPrimaryLoanId = (int) DB::table('mfn_loan')
			->where('softDel', 0)
			->where('memberIdFk', (int) $req->memberIdFk)
			->where(function ($query) use ($transferDate) {
				$query->where('loanCompletedDate', '0000-00-00')
					->orWhere('loanCompletedDate', '>', $transferDate)
					->orWhere('isLoanCompleted', 0);
			})
			->whereIn('productIdFk', $primaryProductLoanIds)
			->value('id');

		if ($runningPrimaryLoanId > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, You can not update this transfer, because you have a running loan.',
			);
			return response()->json($data);
		}

		$memberBranchId = MfnMemberInformation::where('id', (int) $req->memberIdFk)->where('softDel', '=', 0)->first()->branchId;

		$branchWiseSoftwareDate = MicroFin::getSoftwareDateBranchWise($memberBranchId);


		if ($branchWiseSoftwareDate != $transferDate) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, You can not update this transfer, because branch software date is not equal to the transfer date!',
			);
			return response()->json($data);
		}

		$checkSavingsDeposite = DB::table('mfn_savings_deposit')
			->where([['memberIdFk', (int) $req->memberIdFk], ['depositDate', '>', $transferDate], ['softDel', '=', 0]])
			->sum('amount');

		$checkSavingsWithdraw = DB::table('mfn_savings_withdraw')
			->where([['memberIdFk', (int) $req->memberIdFk], ['withdrawDate', '>', $transferDate], ['softDel', '=', 0]])
			->sum('amount');

		if ($checkSavingsDeposite > 0 || $checkSavingsWithdraw > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Sorry, You can not update this transfer, because Savings Transaction Exist!',
			);
			return response()->json($data);
		}

		DB::beginTransaction();

		try {

			//	CURRENT MEMBER PRIMARY PRODUCT TRANSFER INFORMATION.
			// $newProductName = $this->MicroFinance->getMultipleValueForId($table='mfn_loan_primary_product_transfer', $req->newPrimaryProductFk, ['name']);
			// dd($newProductName);
			//	UPDATE MEMBER INFORMATION.
			MfnMemberInformation::where('id', (int) $req->memberIdFk)->update(['primaryProductId' => (int) $req->newPrimaryProductFk]);


			$previousdata = MfnMemberPrimaryProductTransfer::find($req->memberPrimaryProductTransferId);
			// UPDATE PRIMARY PRODUCT TRANSFER INFORMATION.
			DB::table('mfn_loan_primary_product_transfer')
				->where([['id', $req->memberPrimaryProductTransferId], ['newPrimaryProductFk', $req->previouslyNewPrimaryProductFk]])
				->update(['newPrimaryProductFk' => $req->newPrimaryProductFk]);

			$logArray = array(
				'moduleId'  => 6,
				'controllerName'  => 'MfnMemberPrimaryProductTransferController',
				'tableName'  => 'mfn_loan_primary_product_transfer',
				'operation'  => 'update',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->memberPrimaryProductTransferId]
			);
			Service::createLog($logArray);

			// UPDATE RUNNIGN OPTIONAL PRODUCT LOANS PRIMARY PRODUCT ID
			DB::table('mfn_loan')
				->where('softDel', 0)
				->where('memberIdFk', (int) $req->memberIdFk)
				->where('isLoanCompleted', 0)
				->whereNotIn('productIdFk', $primaryProductLoanIds)
				->update(['primaryProductIdFk' => $req->newPrimaryProductFk]);

			//	UPDATE SAVINGS DEPOSIT.
			MfnSavingsDeposit::where('memberIdFk', (int) $req->memberIdFk)
				->where([['primaryProductIdFk', $req->previouslyNewPrimaryProductFk], ['depositDate', '=', $req->transferDate]])
				// ->fromTransferred()
				->update(['primaryProductIdFk' => (int) $req->newPrimaryProductFk]);

			MfnSavingsWithdraw::where('memberIdFk', (int) $req->memberIdFk)
				->where([['primaryProductIdFk', $req->previouslyNewPrimaryProductFk], ['withdrawDate', '=', $req->transferDate]])
				// ->fromTransferred()
				->update(['primaryProductIdFk' => (int) $req->newPrimaryProductFk]);

			DB::commit();
			$successStatus = 1;

			$data = array(
				'responseTitle'  =>  $successStatus == 1 ? MicroFinance::getMessage('msgSuccess') : MicroFinance::getMessage('msgWarning'),
				'responseText'   =>  $successStatus == 1 ? MicroFinance::getMessage('primaryProductTransferSuccess') : MicroFinance::getMessage('primaryProductTransferWarning')
				// 'memberIdFk'		   =>  (int) $req->memberIdFk,
				// 'newPrimaryProductId'  =>  (int) $req->newPrimaryProductFk,
				// 'transferDate'		   =>  $req->transferDate,
				// 'note'			       =>  $req->note
			);

			return response::json($data);
		} catch (\Exception $e) {
			DB::rollback();
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Something went wrong. Please try again.'
			);
			return response::json($data);
		}
	}

	/*
	|--------------------------------------------------------------------------
	| MICRO FINANCE: DELETE MEMBER PRIMARY PRODUCT TRANSFER.
	|--------------------------------------------------------------------------
	*/
	public function deleteItem(Request $req)
	{

		// dd($req);

		/*
		|--------------------------------------------------------------------------
		| UPDATE  : MEMBER INFORMATION
		|--------------------------------------------------------------------------
		|           primaryProductId
		|--------------------------------------------------------------------------
		| DELETE  : SAVINGS WITHDRAW
		|--------------------------------------------------------------------------
		| DELETE  : SAVINGS DEPOSIT
		|--------------------------------------------------------------------------
		| UPDATE  : SAVINGS DEPOSIT
		|--------------------------------------------------------------------------
		|           primaryProductIdFk
		|--------------------------------------------------------------------------
		| DELETE  : MEMBER PRIMARY PRODUCT TRANSFER
		|--------------------------------------------------------------------------
		*/

		DB::beginTransaction();

		try {

			//	GET MEMBER PRIMARY PRODUCT TRANSFER INFORMATION.
			$MPPTOB = MfnMemberPrimaryProductTransfer::where('id', $req->id)
				->select(
					'memberIdFk',
					'oldPrimaryProductFk',
					'newPrimaryProductFk',
					'branchIdFk',
					'transferDate'
				)
				->first();

			$checkSavingsDeposite = DB::table('mfn_savings_deposit')
				->where([['memberIdFk', (int) $MPPTOB->memberIdFk], ['depositDate', '>', $MPPTOB->transferDate], ['softDel', '=', 0]])
				->sum('amount');

			$checkSavingsWithdraw = DB::table('mfn_savings_withdraw')
				->where([['memberIdFk', (int) $MPPTOB->memberIdFk], ['withdrawDate', '>', $MPPTOB->transferDate], ['softDel', '=', 0]])
				->sum('amount');

			if ($checkSavingsDeposite > 0 || $checkSavingsWithdraw > 0) {
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Sorry, You can not delete this transfer, because Savings Transaction Exist!',
				);
				return response()->json($data);
			}

			$branchWiseSoftwareDate = MicroFin::getSoftwareDateBranchWise($MPPTOB->branchIdFk);

			if ($branchWiseSoftwareDate != $MPPTOB->transferDate) {
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Sorry, You can not delete this transfer, because branch software date is not equal to the transfer date!',
				);
				return response()->json($data);
			}

			$transferDate = $MPPTOB->transferDate;

			// IF ANY FUTURITY TRANSFER EXISTS AFTER SOFTWARE DATE, THEN IT CANT BE TRANSFERED.
			$isFuturityTranferExists = DB::table('mfn_loan_primary_product_transfer')
				->where('softDel', 0)
				->where('memberIdFk', (int) $MPPTOB->memberIdFk)
				->where('transferDate', '>', $transferDate)
				->value('id');

			if ($isFuturityTranferExists > 0) {
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Sorry, You can not delete this transfer, because Futurity Transfer Exist!',
				);
				return response()->json($data);
			}

			// IF CURRENTLY IT HAS ANY PRIMARY PRODUCT LOAN, THAN TRANSFER COULD NOT BE DELETED.
			$primaryProductLoanIds = DB::table('mfn_loans_product')
				->where('isPrimaryProduct', 1)
				->pluck('id')
				->toArray();



			$runningPrimaryLoanId = (int) DB::table('mfn_loan')
				->where('softDel', 0)
				->where('memberIdFk', $MPPTOB->memberIdFk)
				->where(function ($query) use ($transferDate) {
					$query->where('loanCompletedDate', '0000-00-00')
						->orWhere('loanCompletedDate', '>', $transferDate)
						->orWhere('isLoanCompleted', 0);
				})
				->whereIn('productIdFk', $primaryProductLoanIds)
				->value('id');

			if ($runningPrimaryLoanId > 0) {
				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Sorry, You can not delete this transfer, because you have a running loan.',
				);
				return response()->json($data);
			}

			//	UPDATE MEMBER INFORMATION.
			$memberInformationtable = MfnMemberInformation::where('id', $MPPTOB->memberIdFk)->update(['primaryProductId' => $MPPTOB->oldPrimaryProductFk]);

			// UPDATE RUNNIGN OPTIONAL PRODUCT LOANS PRIMARY PRODUCT ID
			DB::table('mfn_loan')
				->where('softDel', 0)
				->where('memberIdFk', $MPPTOB->memberIdFk)
				->where('isLoanCompleted', 0)
				->whereNotIn('productIdFk', $primaryProductLoanIds)
				->update(['primaryProductIdFk' => $MPPTOB->oldPrimaryProductFk]);

			//	DELETE SAVINGS WITHDRAW BY USING SOFT DELL.
			MfnSavingsWithdraw::where('memberIdFk', $MPPTOB->memberIdFk)
				->fromTransferred()
				->where('withdrawDate', $MPPTOB->transferDate)
				->update(['softDel' => 1]);

			//	DELETE SAVINGS DEPOSIT BY USING SOFT DELL.
			MfnSavingsDeposit::where('memberIdFk', $MPPTOB->memberIdFk)
				->where('primaryProductIdFk', $MPPTOB->newPrimaryProductFk)
				->fromTransferred()
				->where('depositDate', $MPPTOB->transferDate)
				->update(['softDel' => 1]);

			//	UPDATE SAVINGS DEPOSIT WHICH ARE INVOLVED WITH AUTO PROCESS.
			MfnSavingsDeposit::where('memberIdFk', $MPPTOB->memberIdFk)
				->where('primaryProductIdFk', $MPPTOB->newPrimaryProductFk)
				->where('depositDate', $MPPTOB->transferDate)
				->update(['primaryProductIdFk' => $MPPTOB->oldPrimaryProductFk]);

			$previousdata = MfnMemberPrimaryProductTransfer::find($req->id);
			//	DELETE MEMBER PRIMARY PRODUCT TRANSFER BY USING SOFT DELL.
			MfnMemberPrimaryProductTransfer::where('id', $req->id)
				->update(['softDel' => 1]);
			$logArray = array(
				'moduleId'  => 6,
				'controllerName'  => 'MfnMemberPrimaryProductTransferController',
				'tableName'  => 'mfn_loan_primary_product_transfer',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);


			DB::commit();

			$data = array(
				'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
				'responseText'   =>  MicroFinance::getMessage('regularLoanDelSuccess'),
			);

			return response()->json($data);
		} catch (\Exception $e) {
			DB::rollback();
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Something went wrong. Please try again.'
			);
			return response::json($data);
		}
	}
}
