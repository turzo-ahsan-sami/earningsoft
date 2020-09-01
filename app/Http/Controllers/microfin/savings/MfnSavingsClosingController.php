<?php

namespace App\Http\Controllers\microfin\savings;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Traits\CreateForm;
use App\Traits\GetSoftwareDate;
use App\microfin\savings\MfnSavingsClosing;
use App\microfin\savings\MfnSavingsWithdraw;
use Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnOpeningSavingsAccountInfo;
use App\Http\Controllers\gnr\Service;
use App;

class MfnSavingsClosingController extends Controller
{
	use CreateForm;
	use GetSoftwareDate;

	private $TCN;

	public function __construct()
	{

		$this->TCN = array(
			array('SL#', 70),
			array('Member Name', 0),
			array('Member Code', 0),
			array('Savings Code', 0),
			array('Closing Date', 0),
			array('Mode of Payment', 0),
			array('Amount', 0),
			array('Entry By', 0),
			array('Status', 0),
			array('Action', 0)
		);
	}

	public function index(Request $req)
	{

		$softDate = GetSoftwareDate::getSoftwareDate();
		$softwareDate = GetSoftwareDate::getSoftwareDateInFormat();

		$userBranchId   = Auth::user()->branchId;

		$closings = MfnSavingsClosing::join('mfn_member_information', 'mfn_member_information.id', '=', 'mfn_savings_closing.memberIdFk')
			->where('mfn_savings_closing.softDel', 0)
			->select('mfn_savings_closing.*', 'mfn_member_information.samityId');
		// $withdraws = DB::table('mfn_savings_withdraw')->where('softDel',0);
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

		if ($userBranchId != 1) {
			$savingsAccIds = DB::table('mfn_savings_account')->where('softDel', 0)->where('branchIdFk', $userBranchId)->pluck('id')->toArray();
			$closings = $closings->whereIn('accountIdFk', $savingsAccIds);
			// $withdraws = $withdraws->where('branchIdFk',$userBranchId);
			$closings = $closings->where('mfn_savings_closing.closingDate', '<=', $softDate);
			$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
		} else {
			if (isset($req->filBranch)) {
				if ($req->filBranch != '' && $req->filBranch != null) {
					$samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
					$closings = $closings->where('branchIdFk', $req->filBranch);
					$softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
				} else {
					$samityList = MicroFin::getAllSamityList();
				}
			} else {
				$samityList = MicroFin::getAllSamityList();
			}
		}

		if (isset($req->filBranch)) {
			if ($req->filBranch != '' && $req->filBranch != null) {
				$closings = $closings->where('branchIdFk', '=', $req->filBranch);
			}
		}


		// dd($req->filSamity);
		if (isset($req->filSamity)) {
			if ($req->filSamity != '' && $req->filSamity != null) {
				$closings = $closings->where('samityId', $req->filSamity);
			}
		}
		if (isset($req->filMemberCode)) {
			if ($req->filMemberCode != '' && $req->filMemberCode != null) {
				$memberId = DB::table('mfn_member_information')->where('softDel', 0)->where('code', $req->filMemberCode)->value('id');
				$closings = $closings->where('memberIdFk', $memberId);
			}
		}
		if (isset($req->filProduct)) {
			if ($req->filProduct != '' && $req->filProduct != null) {
				$closings = $closings->where('savingsProductIdFk', $req->filProduct);
			}
		}
		if (isset($req->filDateFrom)) {
			if ($req->filDateFrom != '' && $req->filDateFrom != null) {
				$dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
				$closings = $closings->where('mfn_savings_closing.closingDate', '>=', $dateFrom);
			}
		}
		if (isset($req->filDateTo)) {
			if ($req->filDateTo != '' && $req->filDateTo != null) {
				$dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
				$closings = $closings->where('mfn_savings_closing.closingDate', '<=', $dateTo);
			}
		}
		// $withdraws = $withdraws->select('id','accountIdFk','withdrawDate','isAuthorized')->get();
		$closings = $closings->orderBy('mfn_savings_closing.closingDate', 'desc')->paginate(50);

		$withdraws = DB::table('mfn_savings_withdraw')->where('softDel', 0)->whereIn('accountIdFk', $closings->pluck('accountIdFk'))->select('id', 'accountIdFk', 'withdrawDate', 'isAuthorized')->get();

		$bankList = $this->getBankList();
		//$branchList = MicroFin::getBranchList();

		if ($userBranchId == 1) {
			$branchList = MicroFin::getBranchList();
		} else {
			$branchList = DB::table('gnr_branch')
				->whereIn('id', $branchIdArray)
				->orderBy('branchCode')
				->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
				->pluck('nameWithCode', 'id')
				->all();
		}

		$TCN = $this->TCN;
		$damageData = array(
			'TCN'           =>  $TCN,
			'closings'      =>  $closings,
			'withdraws'     =>  $withdraws,
			'bankList'      =>  $bankList,
			'softwareDate'  =>  $softwareDate,
			'softDate'      =>  $softDate,
			'branchList'    =>  $branchList,
			'userBranchId'  =>  $userBranchId,
			'branchIdArray' => $branchIdArray,
			'samityList'    =>  $samityList,
		);

		return view('microfin/savings/savingsClosing/viewSavingsClosing', $damageData);
	}

	public function index_old(Request $req)
	{

		$softDate = GetSoftwareDate::getSoftwareDate();
		$softwareDate = GetSoftwareDate::getSoftwareDateInFormat();

		$userBranchId   = Auth::user()->branchId;

		$closings = MfnSavingsClosing::join('mfn_member_information', 'mfn_member_information.id', '=', 'mfn_savings_closing.memberIdFk')
			->where('mfn_savings_closing.softDel', 0)
			->select('mfn_savings_closing.*', 'mfn_member_information.samityId');
		// $withdraws = DB::table('mfn_savings_withdraw')->where('softDel',0);

		if ($userBranchId != 1) {
			$savingsAccIds = DB::table('mfn_savings_account')->where('softDel', 0)->where('branchIdFk', $userBranchId)->pluck('id')->toArray();
			$closings = $closings->whereIn('accountIdFk', $savingsAccIds);
			// $withdraws = $withdraws->where('branchIdFk',$userBranchId);
			$closings = $closings->where('mfn_savings_closing.closingDate', '<=', $softDate);
			$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
		} else {
			if (isset($req->filBranch)) {
				if ($req->filBranch != '' && $req->filBranch != null) {
					$samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
					$closings = $closings->where('branchIdFk', $req->filBranch);
					$softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
				} else {
					$samityList = MicroFin::getAllSamityList();
				}
			} else {
				$samityList = MicroFin::getAllSamityList();
			}
		}

		// dd($req->filSamity);
		if (isset($req->filSamity)) {
			if ($req->filSamity != '' && $req->filSamity != null) {
				$closings = $closings->where('samityId', $req->filSamity);
			}
		}
		if (isset($req->filMemberCode)) {
			if ($req->filMemberCode != '' && $req->filMemberCode != null) {
				$memberId = DB::table('mfn_member_information')->where('softDel', 0)->where('code', $req->filMemberCode)->value('id');
				$closings = $closings->where('memberIdFk', $memberId);
			}
		}
		if (isset($req->filProduct)) {
			if ($req->filProduct != '' && $req->filProduct != null) {
				$closings = $closings->where('savingsProductIdFk', $req->filProduct);
			}
		}
		if (isset($req->filDateFrom)) {
			if ($req->filDateFrom != '' && $req->filDateFrom != null) {
				$dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
				$closings = $closings->where('mfn_savings_closing.closingDate', '>=', $dateFrom);
			}
		}
		if (isset($req->filDateTo)) {
			if ($req->filDateTo != '' && $req->filDateTo != null) {
				$dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
				$closings = $closings->where('mfn_savings_closing.closingDate', '<=', $dateTo);
			}
		}
		// $withdraws = $withdraws->select('id','accountIdFk','withdrawDate','isAuthorized')->get();
		$closings = $closings->orderBy('mfn_savings_closing.closingDate', 'desc')->paginate(50);

		$withdraws = DB::table('mfn_savings_withdraw')->where('softDel', 0)->whereIn('accountIdFk', $closings->pluck('accountIdFk'))->select('id', 'accountIdFk', 'withdrawDate', 'isAuthorized')->get();

		$bankList = $this->getBankList();
		$branchList = MicroFin::getBranchList();

		$TCN = $this->TCN;
		$damageData = array(
			'TCN'           =>  $TCN,
			'closings'      =>  $closings,
			'withdraws'     =>  $withdraws,
			'bankList'      =>  $bankList,
			'softwareDate'  =>  $softwareDate,
			'softDate'      =>  $softDate,
			'branchList'    =>  $branchList,
			'userBranchId'  =>  $userBranchId,
			'samityList'    =>  $samityList,
		);

		return view('microfin/savings/savingsClosing/viewSavingsClosing', $damageData);
	}

	public function addClosing()
	{

		$bankList = $this->getBankList();

		$softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
		$softDate = GetSoftwareDate::getSoftwareDate();
		$data = array(
			'bankList'      => $bankList,
			'softwareDate'  => $softwareDate,
			'softDate'      => $softDate
		);
		return view('microfin.savings.savingsClosing.addSavingsClosing', $data);
	}


	public function storeClosing(Request $req)
	{
		$savingsAccount = DB::table('mfn_savings_account')->where('id', $req->savingsCode)->first();
		$closingDate = Carbon::parse($req->closingDate)->format('Y-m-d');

		// MATCH THE CLOSING DATE IS EQUAL TO THE BRANCH DATE OR NOT.
		$branchDate = MicroFin::getSoftwareDateBranchWise($savingsAccount->branchIdFk);

		if ($branchDate != $closingDate) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Branch date is not matched with the closing date.'
			);
			return response::json($data);
		}

		// IF ANY PRODUCT TRANSFER TODAY OR AFTER THIS DATE, THEN IT COULD NOT BE OPERATE.
		$isAnyProductTransfer = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('memberIdFk', $savingsAccount->memberIdFk)
			->where('transferDate', '>=', $closingDate)
			->count();

		if ($isAnyProductTransfer > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Meber Primary Product Transfer exists today/after this date. So savings account closing is not possible!'
			);
			return response::json($data);
		}

		// CHECK TOTAL REPAY AMOUNT IS EQUAL TO THE WITHDRAW AMOUNT OR NOT 

		$totalSavingsDepositeAmount = MfnOpeningSavingsAccountInfo::where('savingsAccIdFk', $req->savingsCode)->where('softDel', '=', 0)->select('openingBalance')->sum('openingBalance');
		$totalSavingsDepositeAmount += MfnSavingsDeposit::where('accountIdFk', $req->savingsCode)
			->where('softDel', '=', 0)
			->sum('amount');

		$totalSavingsDepositeAmount -= MfnSavingsWithdraw::where('accountIdFk', $req->savingsCode)->where('softDel', '=', 0)->sum('amount');

		if ($totalSavingsDepositeAmount != (float) str_replace(',', '', $req->actualBalance)) {

			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Total Deposite amount is not equal to the actual balance amount! So savings account closing is not possible!'
			);

			return response::json($data);
		}


		DB::beginTransaction();

		try {

			$rules = array(
				'member'            =>  'required',
				'closingDate'       =>  'required',
				'savingsCode'       =>  'required'
			);

			if ($req->paymentMode == 'Bank') {
				$rules = $rules + array(
					'bank'          =>  'required',
					'chequeNumber'  =>  'required'
				);
			}

			$attributesNames = array(
				'member'            =>  'Member',
				'closingDate'       =>  'Closing Date',
				'savingsCode'       =>  'Savings Code',
				'bank'              =>  'Bank',
				'chequeNumber'      =>  'Cheque Number'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if ($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {

				if ($closingDate < $savingsAccount->accountOpeningDate) {
					$data = array(
						'responseTitle' =>  'Warning!',
						'responseText'  =>  'You entered date before account opening date.'
					);
					return response::json($data);
				}

				// CHECK IS TERE ANY FUTURE TRANSACTION, IF YES THEN CLOSING CAN'T BE DONE
				$depositTransactions = (int) DB::table('mfn_savings_deposit')->where('softDel', 0)->where('amount', '>', 0)->where('accountIdFk', $req->savingsCode)->where('depositDate', '>', $closingDate)->value('id');
				$withdrawTransactions = (int) DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('amount', '>', 0)->where('accountIdFk', $req->savingsCode)->where('withdrawDate', '>', $closingDate)->value('id');
				$toDayOrFutureTransactions = max($depositTransactions, $withdrawTransactions);

				if ($toDayOrFutureTransactions > 0) {
					$data = array(
						'responseTitle' =>  'Warning!',
						'responseText'  =>  'Transaction exits later to this date.'
					);
					return response::json($data);
				}

				// Store Data

				if ($req->paymentMode == "Cash") {
					// Cash In Hand ledger
					$ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');
				} else {
					$ledgerId = $req->bank;
				}

				// get the product

				$savingsProduct = DB::table('mfn_saving_product')->where('id', $savingsAccount->savingsProductIdFk)->select('depositTypeIdFk')->first();

				if (!isset($req->totalSavingInterestAmount)) {
					$req->totalSavingInterestAmount = 0;
				}
				elseif ($req->totalSavingInterestAmount == '') {
					$req->totalSavingInterestAmount = 0;
				}

				// for the temporary reason set $req->totalSavingInterestAmount to zero
				$req->totalSavingInterestAmount = 0;

				if ($savingsProduct->depositTypeIdFk == 4) {
					$closingAmount  = $req->depositAmount + $req->totalSavingInterestAmount;
					$payableAmount = $req->payableAmount;
				} elseif ($savingsProduct->depositTypeIdFk == 1 || $savingsProduct->depositTypeIdFk == 2) {
					// $closingAmount = $req->actualBalance + $req->totalSavingInterestAmount;
					$closingAmount = floatval(str_replace(',', '', $req->actualBalance)) + floatval(str_replace(',', '', $req->totalSavingInterestAmount));
					// $payableAmount = $req->actualBalance + $req->totalSavingInterestAmount;
					$payableAmount = $closingAmount;
				}

				$closingId = (int) DB::table('mfn_savings_closing')->where('softDel', 0)->where('accountIdFk', $req->savingsCode)->value('id');
				if ($closingId > 0) {
					$closing = MfnSavingsClosing::find($closingId);
				} else {
					$closing = new MfnSavingsClosing;
				}

				$closing->memberIdFk            = $req->memberId;
				//value of saving code is the account id
				$closing->accountIdFk           = $req->savingsCode;
				$closing->branchIdFk            = $savingsAccount->branchIdFk;
				$closing->depositAmount         = floatval(str_replace(',', '', $req->depositAmount));
				$closing->payableAmount         = $payableAmount;
				$closing->totalSavingInterest   = floatval(str_replace(',', '', $req->totalSavingInterestAmount));
				$closing->closingAmount         = $closingAmount;
				$closing->closingDate           = Carbon::parse($req->closingDate);
				$closing->paymentType           = $req->paymentMode;
				$closing->ledgerIdFk            = $ledgerId;
				$closing->chequeNumber          = $req->chequeNumber;
				$closing->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
				$closing->createdAt             = Carbon::now();
				$closing->save();

				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnSavingsClosingController',
					'tableName'  => 'mfn_savings_closing',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('mfn_savings_closing')->max('id')]
				);
				Service::createLog($logArray);

				$account = DB::table('mfn_savings_account')->where('id', $req->savingsCode)->select('savingsProductIdFk')->first();
				$primaryProductId = DB::table('mfn_member_information')->where('id', $req->memberId)->value('primaryProductId');

				// make a withdraw of the outstanding amount
				$member = DB::table('mfn_member_information')->where('id', $req->memberId)->first();

				$closingDate = Carbon::parse($req->closingDate)->format('Y-m-d');
				$withdrawId = DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('accountIdFk', $req->savingsCode)->where('withdrawDate', $closingDate)->where('isFromClosing', 1)->value('id');
				if ($withdrawId > 0) {
					$withdraw = MfnSavingsWithdraw::find($withdrawId);
				} else {
					$withdraw = new MfnSavingsWithdraw;
				}

				$withdrawAmount = (float) str_replace(',', '', $req->actualBalance) + (float) $req->totalSavingInterestAmount;

				if ($withdrawAmount < 0) {
					throw new Exception("Balance cannot be less than zero!");
				}

				$withdraw->memberIdFk               = $member->id;
				$withdraw->branchIdFk               = $member->branchId;
				$withdraw->samityIdFk               = $member->samityId;
				// value of saving code is the account id
				$withdraw->accountIdFk              = $req->savingsCode;
				$withdraw->productIdFk              = $account->savingsProductIdFk;
				$withdraw->primaryProductIdFk       = $primaryProductId;
				$withdraw->amount                   = $withdrawAmount;
				$withdraw->balanceBeforeWithdraw    = (float) str_replace(',', '', $req->actualBalance);
				$withdraw->WithdrawDate             = Carbon::parse($req->closingDate);
				$withdraw->paymentType              = $req->paymentMode;
				$withdraw->ledgerIdFk               = $ledgerId;
				$withdraw->chequeNumber             = $req->chequeNumber;
				$withdraw->isFromClosing            = 1;
				$withdraw->entryByEmployeeIdFk      = Auth::user()->emp_id_fk;
				$withdraw->createdAt                = Carbon::now();
				$withdraw->save();

				// Make the account Inactive
				DB::table('mfn_savings_account')->where('id', $closing->accountIdFk)->update(['status' => 0, 'closingDate' => Carbon::parse($req->closingDate)]);
				DB::commit();

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Data inserted successfully.'
				);

				return response::json($data);
			}
		} catch (\Exception $e) {
			DB::rollback();
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Something went wrong. Please try again.'
			);
			return response::json($data);
		}
	}

	public function updateClosing(Request $req)
	{
	
		$closing = MfnSavingsClosing::find($req->closingId);

		// IF ANY PRODUCT TRANSFER TODAY OR AFTER THIS DATE, THEN IT COULD NOT BE OPERATE.
		$isAnyProductTransfer = DB::table('mfn_loan_primary_product_transfer')
			->where('softDel', 0)
			->where('memberIdFk', $closing->memberIdFk)
			->where('transferDate', '>=', $closing->getOriginal()['closingDate'])
			->count();

		if ($isAnyProductTransfer > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Meber Primary Product Transfer exists today/after this date. So savings account closing is not possible!'
			);
			return response::json($data);
		}

		// CHECK IS TERE ANY FUTURE TRANSACTION, IF YES THEN CLOSING CAN'T BE DONE
		$depositTransactions = (int) DB::table('mfn_savings_deposit')->where('softDel', 0)->where('amount', '>', 0)->where('accountIdFk', $closing->accountIdFk)->where('depositDate', '>', $closing->getOriginal()['closingDate'])->value('id');
		$withdrawTransactions = (int) DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('amount', '>', 0)->where('accountIdFk', $closing->accountIdFk)->where('withdrawDate', '>', $closing->getOriginal()['closingDate'])->value('id');
		$toDayOrFutureTransactions = max($depositTransactions, $withdrawTransactions);

		if ($toDayOrFutureTransactions > 0) {
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Transaction exits later to this date.'
			);
			return response::json($data);
		}


		DB::beginTransaction();

		try {			

			$previousdata = $closing;

			$member = DB::table('mfn_member_information')
				->where('id', $closing->memberIdFk)
				->first();

			$branchSoftwaredate = MicroFin::getSoftwareDateBranchWise($member->branchId);

			if ($branchSoftwaredate != $closing->getOriginal()['closingDate']) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Branch software date is not equal to the member closing date.'
				);

				return response::json($data);
			}

			$mfnSavingsAccIdFromClosing = MfnSavingsClosing::where('id', $req->closingId)->where('softDel', '=', 0)->first()->accountIdFk;

			$totalSavingsDepositeAmount = MfnOpeningSavingsAccountInfo::where('savingsAccIdFk', $mfnSavingsAccIdFromClosing)->where('softDel', '=', 0)->select('openingBalance')->sum('openingBalance');
			$totalSavingsDepositeAmount += MfnSavingsDeposit::where('accountIdFk', $mfnSavingsAccIdFromClosing)
				->where('softDel', '=', 0)
				->sum('amount');

			$totalSavingsDepositeAmount -= MfnSavingsWithdraw::where('accountIdFk', $mfnSavingsAccIdFromClosing)->where('softDel', '=', 0)->sum('amount');

			if (($totalSavingsDepositeAmount != (float) str_replace(',', '', $req->actualBalance)) || (float) str_replace(',', '', $req->actualBalance) < 0) {

				$data = array(
					'responseTitle'  =>  'Warning!',
					'responseText'   =>  'Total Deposite amount is not equal to the actual balance amount! So savings account closing is not possible!'
				);

				return response::json($data);
			}

			if ($req->paymentMode == "Cash") {
				// Cash In Hand ledger id
				$ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');
			} else {
				$ledgerId = $req->bank;
			}

			// get the product
			$savingsAccount = DB::table('mfn_savings_account')->where('id', $closing->accountIdFk)->select('savingsProductIdFk')->first();
			$savingsProduct = DB::table('mfn_saving_product')->where('id', $savingsAccount->savingsProductIdFk)->select('depositTypeIdFk')->first();

			
			/* if ($req->totalSavingInterestAmount == '') {
				$req->totalSavingInterestAmount = 0;
			} */

			$req->totalSavingInterestAmount = $closing->totalSavingInterest;

			if ($savingsProduct->depositTypeIdFk == 4) {
				// $closingAmount  = $req->depositAmount + $req->totalSavingInterestAmount;
				$closingAmount  = floatval(str_replace(',', '', $req->depositAmount)) + floatval(str_replace(',', '', $req->totalSavingInterestAmount));
			} elseif ($savingsProduct->depositTypeIdFk == 1 || $savingsProduct->depositTypeIdFk == 2) {
				// $closingAmount = $req->actualBalance + $req->totalSavingInterestAmount;
				$closingAmount = floatval(str_replace(',', '', $req->actualBalance)) + floatval(str_replace(',', '', $req->totalSavingInterestAmount));
			}

			$closing->totalSavingInterest   = floatval(str_replace(',', '', $req->totalSavingInterestAmount));
			$closing->closingAmount         = $closingAmount;
			$closing->paymentType           = $req->paymentMode;
			$closing->ledgerIdFk            = $ledgerId;
			$closing->chequeNumber          = $req->chequeNumber;
			$closing->save();

			$logArray = array(
				'moduleId'  => 6,
				'controllerName'  => 'MfnSavingsClosingController',
				'tableName'  => 'mfn_savings_closing',
				'operation'  => 'update',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);

			// update withdraw
			$withdrawAmount = (float) str_replace(',', '', $req->actualBalance) + (float) $req->totalSavingInterestAmount;
			if ($withdrawAmount < 0) {
				throw new Exception("Balance cannot be less than zero!");
			}

			$withdraw = MfnSavingsWithdraw::where('accountIdFk', $closing->accountIdFk)->where('softDel', 0)->where('withdrawDate', $closing->getOriginal()['closingDate'])->where('isFromClosing', 1)->first();
			$withdraw->amount                   = $withdrawAmount;
			$withdraw->balanceBeforeWithdraw    = (float) str_replace(',', '', $req->actualBalance);
			$withdraw->paymentType              = $req->paymentMode;
			$withdraw->ledgerIdFk               = $ledgerId;
			$withdraw->chequeNumber             = $req->chequeNumber;
			$withdraw->save();

			DB::commit();

			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Closing updated successfully.'
			);

			return response::json($data);
		} catch (\Exception $e) {
			DB::rollback();
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Something went wrong. Please try again.'.$e->getFile().' '.$e->getLine().' '.$e->getMessage()
			);
			return response::json($data);
		}
	}

	public function deleteClosing(Request $req)
	{

		DB::beginTransaction();

		try {
			$closing = MfnSavingsClosing::find($req->id);
			$previousdata = $closing;

			// if member is not active then give a message.
			$member = DB::table('mfn_member_information')
				->where('id', $closing->memberIdFk)
				->first();
			// ->value('status');

			if ($member->status == 0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Member is closed, first active the member please.'
				);

				return response::json($data);
			}

			$branchSoftwaredate = MicroFin::getSoftwareDateBranchWise($member->branchId);

			if ($branchSoftwaredate != $closing->getOriginal()['closingDate']) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Branch software date is not equal to the member closing date.'
				);

				return response::json($data);
			}

			$withdraw = MfnSavingsWithdraw::where('withdrawDate', $closing->getOriginal()['closingDate'])->where('accountIdFk', $closing->accountIdFk)->where('softDel', 0)->where('isFromClosing', 1)->first();

			// Delete savings withdraw data
			DB::table('mfn_savings_withdraw')
				->where('id', $withdraw->id)
				->update(
					[
						'softDel' => 1
					]
				);
			// $withdraw->delete();

			// Delete savings closing data
			DB::table('mfn_savings_closing')
				->where('id', $closing->id)
				->update(
					[
						'softDel' => 1
					]
				);

			$logArray = array(
				'moduleId'  => 6,
				'controllerName'  => 'MfnSavingsClosingController',
				'tableName'  => 'mfn_savings_closing',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);
			// $closing->delete();

			// Make the Account active
			DB::table('mfn_savings_account')->where('id', $closing->accountIdFk)->update(['status' => 1, 'closingDate' => '0000-00-00']);

			DB::commit();

			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Closing deleted successfully.'
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

	public function getBankList()
	{

		$userProjectId  = Auth::user()->project_id_fk;
		$userBranchId   = Auth::user()->branchId;

		$bankFromLaser = DB::table('acc_account_ledger')
			->where('accountTypeId', 5)
			->select('projectBranchId', 'id')
			->get();

		$bankList = array();
		foreach ($bankFromLaser as $key => $bank) {
			$projectBranchString = str_replace(['"', '[', ']'], '', $bank->projectBranchId);
			$projectBranchArray = explode(',', $projectBranchString);

			foreach ($projectBranchArray as $key => $projectBranch) {
				$result = explode(':', $projectBranch);
				$bankProjectId  = $result[0];
				$bankBranchId   = $result[1];

				if (($bankProjectId == 0 && $bankBranchId == 0) || ($bankBranchId == $userBranchId) || ($userProjectId == $bankProjectId && $bankBranchId == 0)) {
					array_push($bankList, $bank->id);
				}
			}
		}

		$resultedBankList = DB::table('acc_account_ledger')
			->whereIn('id', $bankList)
			->where('id', '!=', 350)
			->select('name', 'id', 'code')
			->get();
		return $resultedBankList;
	}
}
