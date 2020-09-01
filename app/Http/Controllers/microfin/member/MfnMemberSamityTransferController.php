<?php

namespace App\Http\Controllers\microfin\member;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\member\MfnMemberSamityTransfer;
use App\microfin\member\MfnMemberPrimaryProductTransfer;
use App\microfin\member\MfnMemberInformation;
use App\microfin\savings\MfnSavingsProduct;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use App\microfin\loan\MfnLoan;
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

class MfnMemberSamityTransferController extends Controller {

	protected $MicroFinance;

	private $TCN;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Member Name', 0),
			array('Transfer Date', 90),
			array('Branch', 0),
			array('Previous Samity', 0),
			array('Current Samity', 0),
			array('Previous Member Code', 130),
			array('Current Member Code', 130),
			array('Entry By', 0),
			array('Action', 80)
		);
	}

	public function index(Request $req) {

		$PAGE_SIZE = 50;
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

		if(Auth::user()->branchId==1):
			$samity = [];
			$primaryProduct = $this->MicroFinance->getLoanProductsOption();
			$memberSamityTransfer = MfnMemberSamityTransfer::active();
		else:
			$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);
			$memberSamityTransfer = MfnMemberSamityTransfer::active()->branchWise();
		endif;
			// dd($memberSamityTransfer->paginate(50));

		if($req->has('branchId')) {
			$memberSamityTransfer->where('branchIdFk', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}

		if($req->has('newSamityId'))
			$memberSamityTransfer->where('newSamityIdFk', $req->get('newSamityId'));

		if($req->has('oldSamityId'))
			$memberSamityTransfer->where('previousSamityIdFk', $req->get('oldSamityId'));

		if($req->has('primaryProductId'))
			$memberSamityTransfer->where('newPrimaryProductIdFk', $req->get('primaryProductId'));

		if($req->has('newMemberCode')) {
			$memberSamityTransfer->where('newMemberCodeFk', $req->get('newMemberCode'));
		}

		if($req->has('oldMemberCode')) {
			$memberSamityTransfer->where('previousMemberCodeFk', $req->get('oldMemberCode'));
		}

		if($req->has('dateFrom'))
			$memberSamityTransfer->where('transferDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if($req->has('dateTo'))
			$memberSamityTransfer->where('transferDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE -$PAGE_SIZE;

		if($req->has('branchId') || $req->has('newSamityId') || $req->has('oldSamityId') || $req->has('primaryProductId') || $req->has('newMemberCode') || $req->has('oldMemberCode') || $req->has('dateFrom') || $req->has('dateTo')) {
				// $memberSamityTransfer = $memberSamityTransfer->get();
			$isSearch = 1;
		} else {
				// $memberSamityTransfer = $memberSamityTransfer->paginate($PAGE_SIZE);
			$isSearch = 0;
		}

		if (Auth::user()->branchId==1) {
			$branchList = MicroFin::getBranchList();

		}
		else{
			$branchList = DB::table('gnr_branch')
			->whereIn('id',$branchIdArray )
			->orderBy('branchCode')
			->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
			->pluck('nameWithCode', 'id')
			->all();
		}


		$memberSamityTransfer = $memberSamityTransfer->paginate($PAGE_SIZE);

		$damageData = array(
			'TCN' 			   		=>  $this->TCN,
				// 'memberSamityTransfer'  =>  $this->MicroFinance->getActiveMemberSamityTransfer(),
			'SL' 	   				=>	$req->has('page')?$SL:0,
			'isSearch'          	=>  $isSearch,
			'branch'  				=>  $this->MicroFinance->getAllBranchOptions(),
			'samity'				=>  $samity,
			'branchList'			=>  $branchList,
			'branchIdArray'			=>  $branchIdArray,
			'primaryProduct'  		=>  $primaryProduct,
				// 'members'  				=>  $members,
				// 'gender'   				=>  $this->MicroFinance->getGender(),
			'dataNotAvailable'		=>	$this->MicroFinance->dataNotAvailable(),
			'req'					=>  $req,
			'memberSamityTransfer'  =>  $memberSamityTransfer,
			'MicroFinance'          =>  $this->MicroFinance
		);

		return view('microfin.member.memberSamityTransfer.viewMemberSamityTransfer', ['damageData' => $damageData]);
	}







	public function index_old(Request $req) {

		$PAGE_SIZE = 50;

		if(Auth::user()->branchId==1):
			$samity = [];
			$primaryProduct = $this->MicroFinance->getLoanProductsOption();
			$memberSamityTransfer = MfnMemberSamityTransfer::active();
		else:
			$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions(Auth::user()->branchId);
			$memberSamityTransfer = MfnMemberSamityTransfer::active()->branchWise();
		endif;
			// dd($memberSamityTransfer->paginate(50));

		if($req->has('branchId')) {
			$memberSamityTransfer->where('branchIdFk', $req->get('branchId'));
			$samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
			$primaryProduct = $this->MicroFinance->getBranchWiseActiveLoanPrimaryProductOptions($req->get('branchId'));
		}

		if($req->has('newSamityId'))
			$memberSamityTransfer->where('newSamityIdFk', $req->get('newSamityId'));

		if($req->has('oldSamityId'))
			$memberSamityTransfer->where('previousSamityIdFk', $req->get('oldSamityId'));

		if($req->has('primaryProductId'))
			$memberSamityTransfer->where('newPrimaryProductIdFk', $req->get('primaryProductId'));

		if($req->has('newMemberCode')) {
			$memberSamityTransfer->where('newMemberCodeFk', $req->get('newMemberCode'));
		}

		if($req->has('oldMemberCode')) {
			$memberSamityTransfer->where('previousMemberCodeFk', $req->get('oldMemberCode'));
		}

		if($req->has('dateFrom'))
			$memberSamityTransfer->where('transferDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if($req->has('dateTo'))
			$memberSamityTransfer->where('transferDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE -$PAGE_SIZE;

		if($req->has('branchId') || $req->has('newSamityId') || $req->has('oldSamityId') || $req->has('primaryProductId') || $req->has('newMemberCode') || $req->has('oldMemberCode') || $req->has('dateFrom') || $req->has('dateTo')) {
				// $memberSamityTransfer = $memberSamityTransfer->get();
			$isSearch = 1;
		} else {
				// $memberSamityTransfer = $memberSamityTransfer->paginate($PAGE_SIZE);
			$isSearch = 0;
		}

		$memberSamityTransfer = $memberSamityTransfer->paginate($PAGE_SIZE);

		$damageData = array(
			'TCN' 			   		=>  $this->TCN,
				// 'memberSamityTransfer'  =>  $this->MicroFinance->getActiveMemberSamityTransfer(),
			'SL' 	   				=>	$req->has('page')?$SL:0,
			'isSearch'          	=>  $isSearch,
			'branch'  				=>  $this->MicroFinance->getAllBranchOptions(),
			'samity'				=>  $samity,
			'primaryProduct'  		=>  $primaryProduct,
				// 'members'  				=>  $members,
				// 'gender'   				=>  $this->MicroFinance->getGender(),
			'dataNotAvailable'		=>	$this->MicroFinance->dataNotAvailable(),
			'req'					=>  $req,
			'memberSamityTransfer'  =>  $memberSamityTransfer,
			'MicroFinance'          =>  $this->MicroFinance
		);

		return view('microfin.member.memberSamityTransfer.viewMemberSamityTransfer', ['damageData' => $damageData]);
	}

	public function addMemberSamityTransfer() {

		$damageData = array(
			'member'  		        =>  $this->MicroFinance->getMemberOptionsForLoan(Auth::user()->branchId),
			'currentBranchOption'   =>  $this->MicroFinance->getDefaultBranchOptions(Auth::user()->branchId),
			'primaryProductOption'  =>  $this->MicroFinance->getPrimaryProductWithFundingOrganizationBranchWise(),
			'transferDate'          =>  GetSoftwareDate::getSoftwareDate()
		);

		return view('microfin.member.memberSamityTransfer.addMemberSamityTransfer', ['damageData' => $damageData]);
	}

	public function loadLoanAndSavingsSummary(Request $req) {

			//	CHECK ALL LOANS ARE CLEAR OR NOT STARTED YET.
			// $loanExists = MfnLoan::where('memberIdFk', $req->memberId)->loanIncompleted()->active()->count();

			// dd($loanExists);
		$softwareDate = GetSoftwareDate::getSoftwareDate();
		$loanExists = DB::table('mfn_loan')
		->where([['memberIdFk', $req->memberId], ['loanCompletedDate', '=', '0000-00-00'], ['status', '=', 1], ['isLoanCompleted', '=', 0]])
		->orWhere([['memberIdFk', $req->memberId], ['loanCompletedDate', '>', $softwareDate], ['isLoanCompleted', '=', 1]])
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
		$getCurrentPrimaryProduct = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $req->memberId, ['samityId', 'primaryProductId']);

			//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
		$getCurProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($req->memberId);

			//	GET ALL THE LOAN ACCOUNT OF A MEMBER.
		$loanAccount = $this->MicroFinance->getLoanAccountNumberPerMember($req->memberId);

		$i = 0;
		$loanSummary = [];

			//	GET ALL THE DETAILS OF ALL THE LOAN ACCOUNT OF A MEMBER.
		foreach($loanAccount as $loanAcc):
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
		foreach($savingsAccount as $savingsAcc):
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
			'branchName'             =>  $this->MicroFinance->getNameValueForId($table='gnr_branch', Auth::user()->branchId),
			'samityName'             =>  $this->MicroFinance->getSamityNameWithCode($getCurrentPrimaryProduct->samityId),
			'currentProductName'     =>  $this->MicroFinance->getNameValueForId($table='mfn_loans_product', $getCurrentPrimaryProduct->primaryProductId),
			'workingArea'  		     =>  $this->MicroFinance->getNameValueForId($table='gnr_working_area', $getWorkingAreaId),
			'loanAccount'	  	     =>  $loanAccount,
			'loanSummary'	  	     =>  $loanSummary,
			'savingsSummary'         =>  $savingsSummary,
			'savingsAccount'	     =>  $savingsAccount,
			'loanCount'			     =>  $loanExists,
			'transferStatus'	     =>  $loanExists
		);


		return response::json($data);
	}

	public function loadNewMemberCode(Request $req) {

		$samityOB = DB::table('mfn_samity')
		->select('code',
			'samityTypeId',
			'workingAreaId',
			'openingDate'
		)
		->where('id', $req->samityId)
		->first();

		$samityCode = $samityOB->code;

			// START AUTO GENERATE MEMBER CODE.
		$numRows = DB::table('mfn_member_information')->select('id')->where('samityId', $req->samityId)->count();
		$memberOB = DB::table('mfn_member_information')->where('samityId', $req->samityId)->select('code')->get();

		$memberCodeArr = [];

            //	GET ALL THE MEMBER SL NUMBER FROM MEMBER CODE OF THE SAMITY.
		foreach($memberOB as $member):
			$memberCodeArr[] = (int) substr($member->code, -5, 5);
		endforeach;

		if($numRows<=0):
			$memberSL  = sprintf('%04d', 1);
			$code = $memberSL;
		else:
			$maxMemberSL = max($memberCodeArr) + 1;
			$memberSL  = sprintf('%04d', $maxMemberSL);
			$code = $memberSL;
		endif;
            // END AUTO GENERATE MEMBER CODE.

		$memberCode = $samityCode . '.' . str_pad($code, 5, 0, STR_PAD_LEFT);

		$data = array(
			'newMemberCode'  =>  $memberCode,
		);

		return response::json($data);
	}

	public function preventProductTransfer(Request $req) {

			//	CHECK IF THERE IS ANOTHER PRODUCT TRANSFER IN A SNGLE DAY.
		$productTransferExists = $this->MicroFinance->getCheckAnotherProductTransferExists($req->memberId);

			//	GET THE CURRENT PRIMARY PRODUCT OF THE MEMBER.
		$primaryProductId = DB::table('mfn_member_information')->where('id', $req->memberId)->value('primaryProductId');

		$data = array(
			'primaryProductId'       =>  $productTransferExists==0?(int)$req->newPrimaryProductId:$primaryProductId,
			'productTransferExists'  =>  $productTransferExists
		);

		if($productTransferExists==1):
			$data['responseTitle'] = MicroFinance::getMessage('msgWarning');
			$data['responseText'] = MicroFinance::getMessage('primaryProductTransferWithMemberWarning');
		endif;

		return response::json($data);
	}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD MEMBER SAMITY TRANSFER CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {

			$rules = array(
				'memberIdFk'	   =>  'required',
				'branchIdFk'	   =>  'required',
				'newSamityIdFk'	   =>  'required',
				'newMemberCodeFk'  =>  'required',
				'transferDate'     =>  'required',
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
				| ADD     : MEMBER SAMITY TRANSFER
				|--------------------------------------------------------------------------
				| ADD     : MEMBER PRIMARY PRODUCT TRANSFER
				|--------------------------------------------------------------------------
				| ADD     : SAVINGS WITHDRAW
				|--------------------------------------------------------------------------
				| ADD     : SAVINGS DEPOSIT
				|--------------------------------------------------------------------------
				| UPDATE  : SAVINGS ACCOUNT
				|--------------------------------------------------------------------------
				|           savingsCode
				|			samityIdFk
				|--------------------------------------------------------------------------
				| UPDATE  : MEMBER INFORMATION
				|--------------------------------------------------------------------------
				|			code
				|			samityId
				|			primaryProductId
				|--------------------------------------------------------------------------
				*/

				$transaction = [];

				//	CHECK IF THERE ARE ANY TRANSACTION WHICH DATES ARE GREATER THAN SOFTWARE DATE IN
				//	SAVINGS DEPOSIT, SAVINGS WITHDRAW AND LOAN COLLECTION.
				$transaction[] = $this->MicroFinance->getSavingsDepositExistAfterSoftwareDate($req->memberIdFk);
				$transaction[] = $this->MicroFinance->getSavingsWithdrawExistAfterSoftwareDate($req->memberIdFk);
				$transaction[] = $this->MicroFinance->getLoanCollectionExistAfterSoftwareDate($req->memberIdFk);

				$transactionClear = array_sum($transaction)==0?1:0;

				//	CHECK IF THERE IS ANOTHER MEMBER TRANSFER IN A SNGLE DAY.
				$memberTransferExists = $this->MicroFinance->getCheckAnotherMemberTransferExists($req->memberIdFk);
				$memberTransferClear = $memberTransferExists==0?1:0;

				if($transactionClear==1 && $memberTransferClear==1):
					$transferStatus = 1;
				else:
					$transferStatus = 0;
				endif;

				if($transferStatus==1):
					// GET PRIMARY PRODUCT ID OF THE MEMBER.
					$memberOB = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $req->memberIdFk, ['primaryProductId']);

					//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
					$primaryProductTransferDate = $this->MicroFinance->getLatestPrimaryProductTransferDate($req->memberIdFk);

					//	GET ALL THE SAVINGS ACCOUNT OF A MEMBER.
					$savingsAccount = $this->MicroFinance->getSavingsAccountNumberPerMember($req->memberIdFk);

					$i = 0;
					$savingsSummary = [];
					$totalTransferAmount = 0;

					//	GET ALL THE DETAILS OF ALL THE SAVINGS ACCOUNT OF A MEMBER.
					foreach($savingsAccount as $savingsAcc):
						$savingsSummary[$i]['id'] = $savingsAcc['id'];
						$savingsSummary[$i]['savingsProductId'] = $savingsAcc['savingsProductIdFk'];
						//$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']);
						//$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);
						$savingsSummary[$i]['deposit'] = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $primaryProductTransferDate);
						$savingsSummary[$i]['withdraw'] = $this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $primaryProductTransferDate);
						$savingsSummary[$i]['balance'] = $savingsSummary[$i]['deposit'] - $savingsSummary[$i]['withdraw'];
						$totalTransferAmount +=  $savingsSummary[$i]['balance'];
						$i++;
					endforeach;

					//	GET MEMBER INFO.
					$memberOB = $this->MicroFinance->getMultipleValueForId($table='mfn_member_information', $req->memberIdFk, ['code', 'samityId', 'primaryProductId']);

					//	INSERT DATA INTO MEMBER SAMITY TRANSFER TABLE.
					$req->request->add(['createdDate' => Carbon::now()]);
					$req->request->add(['entryByFk' => Auth::user()->emp_id_fk]);
					$req->request->add(['transferDate' => $this->MicroFinance->getDBDateFormat($req->transferDate)]);
					$req->request->add(['previousMemberCodeFk' => $memberOB->code]);
					$req->request->add(['previousSamityIdFk' => $memberOB->samityId]);
					$req->request->add(['previousPrimaryProductIdFk' => $memberOB->primaryProductId]);
					$req->request->add(['totalTransferAmount' => $totalTransferAmount]);
					$req->request->add(['savingsRecord' => $savingsSummary]);
					$create = MfnMemberSamityTransfer::create($req->all());

					$logArray = array(
						'moduleId'  => 6,
						'controllerName'  => 'MfnMemberSamityTransferController',
						'tableName'  => 'mfn_member_samity_transfer',
						'operation'  => 'insert',
						'primaryIds'  => [DB::table('mfn_member_samity_transfer')->max('id')]
					);
					Service::createLog($logArray);

					//	INSERT DATA INTO MEMBER PRIMARY PRDUCT TRANSFER TABLE
					//	IF PRIMARY PRODUCT WAS CHANGED.
					if((int)$req->newPrimaryProductIdFk!=$memberOB->primaryProductId):
						$req->request->add(['createdDate' => Carbon::now()]);
						$req->request->add(['branchIdFk' => Auth::user()->branchId]);
						$req->request->add(['entryBy' => Auth::user()->emp_id_fk]);
						$req->request->add(['transferDate' => $this->MicroFinance->getDBDateFormat($req->transferDate)]);
						$req->request->add(['samityIdFk' => $req->newSamityIdFk]);
						$req->request->add(['oldPrimaryProductFk' => $memberOB->primaryProductId]);
						$req->request->add(['newPrimaryProductFk' => $req->newPrimaryProductIdFk]);
						$req->request->add(['memberIdFk' => $req->memberIdFk]);
						$req->request->add(['totalTransferAmount' => $totalTransferAmount]);
						$req->request->add(['savingsRecord' => $savingsSummary]);
						$req->request->add(['viaMemberSamityTransfer' => 1]);
						$create = MfnMemberPrimaryProductTransfer::create($req->all());
					endif;

					$accountsBalanceArr = [];

					//	INSERT DATA INTO SAVINGS WITHDRAW TABLE.
					foreach($savingsAccount as $savingsAcc):
						/*$balance = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id']) -
						$this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id']);*/

						$balance = $this->MicroFinance->getSavingsDepositPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $primaryProductTransferDate) -
						$this->MicroFinance->getSavingsWithdrawPerAccount($savingsAcc['id'], $memberOB->primaryProductId, $primaryProductTransferDate);

						$withdraw = New MfnSavingsWithdraw;
						$withdraw->memberIdFk 		    =  $req->memberIdFk;
						$withdraw->branchIdFk 		    =  $savingsAcc['branchIdFk'];
						$withdraw->samityIdFk   		=  $memberOB->samityId;
						$withdraw->accountIdFk 		    =  $savingsAcc['id'];
						$withdraw->productIdFk 		    =  $savingsAcc['savingsProductIdFk'];
						$withdraw->primaryProductIdFk   =  $memberOB->primaryProductId;
						$withdraw->amount 			    =  $balance;
						$withdraw->withdrawDate 		=  GetSoftwareDate::getSoftwareDate();
						$withdraw->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
						$withdraw->isAuthorized         =  1;
						$withdraw->isTransferred        =  1;
						$withdraw->createdAt 		    =  Carbon::now();
						$withdraw->save();

						$accountsBalanceArr[$savingsAcc['id']] = $balance;
					endforeach;

					//	INSERT DATA INTO SAVINGS DEPOSIT TABLE.
					foreach($savingsAccount as $savingsAcc):
						$deposit = New MfnSavingsDeposit;
						$deposit->accountIdFk 		   =  $savingsAcc['id'];
						$deposit->productIdFk 		   =  $savingsAcc['savingsProductIdFk'];
						$deposit->primaryProductIdFk   =  $req->newPrimaryProductIdFk;
						$deposit->memberIdFk 		   =  $req->memberIdFk;
						$deposit->branchIdFk 		   =  $savingsAcc['branchIdFk'];
						$deposit->samityIdFk 		   =  $req->newSamityIdFk;
						$deposit->amount 			   =  $accountsBalanceArr[$savingsAcc['id']];
						$deposit->depositDate 		   =  GetSoftwareDate::getSoftwareDate();
						$deposit->entryByEmployeeIdFk  =  Auth::user()->emp_id_fk;
						$deposit->isAuthorized         =  1;
						$deposit->isTransferred        =  1;
						$deposit->createdAt 		   =  Carbon::now();
						$deposit->save();
					endforeach;

					//	GET PREVIOUS SAVINGS CODE FOR ALL SAVINGS ACCOUNTS.
					$savingsAccountOB = MfnSavingsAccount::where('memberIdFk', $req->memberIdFk)
					->active()
					->select('id', 'savingsCode')
					->get()
					->toArray();

					$savingsAccountIdArr = array_column($savingsAccountOB, 'savingsCode', 'id');

					//	UPDATE SAVINGS CODE, SAMITY ID IN mfn_savings_account TABLE.
					foreach($savingsAccountIdArr as $key => $val):
						$savings = MfnSavingsAccount::find($key);
						$savings->savingsCode = str_replace($memberOB->code, $req->newMemberCodeFk, $val);
						$savings->samityIdFk = $req->newSamityIdFk;
						$savings->save();
					endforeach;

					//	UPDATE MEMBER CODE, SAMITY ID AND PRIMARY PRODUCT ID IN mfn_member_information TABLE.
					$member = MfnMemberInformation::find($req->memberIdFk);
					$member->code = $req->newMemberCodeFk;
					$member->samityId = $req->newSamityIdFk;
					$member->primaryProductId = $req->newPrimaryProductIdFk;
					$member->save();
				endif;

				$data = array(
					'responseTitle'  =>  $transferStatus==1?MicroFinance::getMessage('msgSuccess'):MicroFinance::getMessage('msgWarning'),
					'responseText'   =>  $transferStatus==1?MicroFinance::getMessage('memberSamityTransferSuccess'):MicroFinance::getMessage('memberSamityTransferWarning'),
				);

				return response::json($data);
			}
		}

		public function deleteItem(Request $req) {
			$getMemberInfo = DB::table('mfn_member_samity_transfer')
			->where('id', $req->id)
				// ->select('memberIdFk')
			->first();

			$checkLoan = DB::table('mfn_loan')
			->where([['memberIdFk', $getMemberInfo->memberIdFk], ['softDel', '=', 0], ['status', '=', 1]])
			->count();

			$checkSavings = DB::table('mfn_savings_account')
			->where([['memberIdFk', $getMemberInfo->memberIdFk], ['status', '=', 1], ['softDel', '=', 0]])
			->count();

			$checkProductTransfer = DB::table('mfn_loan_primary_product_transfer')
			->where([['memberIdFk', $getMemberInfo->memberIdFk], ['samityIdFk', $getMemberInfo->newSamityIdFk]])
			->count();

			if ($checkLoan > 0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Loan Exist. Can not delete right now!'
				);

				return response()->json($data);
			}

			if ($checkSavings > 0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Savings Exist. Can not delete right now!'
				);

				return response()->json($data);
			}

			if ($checkProductTransfer > 0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Product transfer Exist. Can not delete right now!'
				);

				return response()->json($data);
			}

			$insertTransferDeletedinfos = DB::table('mfn_member_samity_transfer_deleted')
			->insert (
				[
					'memberSamityTransferIdFk'	=> $getMemberInfo->id,
					'memberIdFk'	            => $getMemberInfo->memberIdFk,
					'previousMemberCodeFk'	    => $getMemberInfo->previousMemberCodeFk,
					'dsOldMemCode'	            => $getMemberInfo->dsOldMemCode,
					'newMemberCodeFk'	        => $getMemberInfo->newMemberCodeFk,
					'dsNewMemCode'	            => $getMemberInfo->dsNewMemCode,
					'previousPrimaryProductIdFk'=> $getMemberInfo->previousPrimaryProductIdFk,
					'dsOldProductCode'		    => $getMemberInfo->dsOldProductCode,
					'newPrimaryProductIdFk'	    => $getMemberInfo->newPrimaryProductIdFk,
					'dsNewProducrtCode'		    => $getMemberInfo->dsNewProducrtCode,
					'previousSamityIdFk'	    => $getMemberInfo->previousSamityIdFk,
					'dsOldSamityCode'	        => $getMemberInfo->dsOldSamityCode,
					'newSamityIdFk'		        => $getMemberInfo->newSamityIdFk,
					'dsNewSamityCode'	        => $getMemberInfo->dsNewSamityCode,
					'branchIdFk'		        => $getMemberInfo->branchIdFk,
					'dsOldBranchCode'		    => $getMemberInfo->dsOldBranchCode,
					'dsNewBranchCode'		    => $getMemberInfo->dsNewBranchCode,
					'totalTransferAmount'	    => $getMemberInfo->totalTransferAmount,
					'savingsRecord'		        => $getMemberInfo->savingsRecord,
					'transferDate'		        => $getMemberInfo->transferDate,
					'entryByFk'			        => $getMemberInfo->entryByFk,
					'oldDateFrom'		        => $getMemberInfo->oldDateFrom,
					'oldDateTo'		            => $getMemberInfo->oldDateTo,
					'status'	                => 1,
					'softDel'			        => 0,
					'deletedDate'		        => date("Y-m-d"),
					'deletedByIdFk'             => Auth::user()->emp_id_fk
				]
			);

			$updateMemberTable = DB::table('mfn_member_information')
			->where('id', $getMemberInfo->memberIdFk)
			->update(
				[
					'code' 	   => $getMemberInfo->previousMemberCodeFk,
					'samityId' => $getMemberInfo->previousSamityIdFk
				]
			);

				// update savings account's samity id
			DB::table('mfn_savings_account')
			->where('memberIdFk',$getMemberInfo->memberIdFk)
			->update(['samityIdFk'=>$getMemberInfo->previousSamityIdFk]);

			$deleteMemberSamityTransferData = DB::table('mfn_member_samity_transfer')
			->where('id', $req->id)
			->delete();

			$logArray = array(
				'moduleId'  => 6,
				'controllerName'  => 'MfnMemberSamityTransferController',
				'tableName'  => 'mfn_member_samity_transfer',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$req->id]
			);
			Service::createLog($logArray);

			// dd($req, $getMemberInfo->memberIdFk, $checkLoan, $checkSavings, $insertTransferDeletedinfos, $updateMemberTable, $deleteMemberSamityTransferData);
			
			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected samity transfer information deleted successfully.'
			);

			return response()->json($data);
		}
	}
