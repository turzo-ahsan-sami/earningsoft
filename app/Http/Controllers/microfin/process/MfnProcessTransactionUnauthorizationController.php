<?php

namespace App\Http\Controllers\microfin\process;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\microfin\MfnMemberType;
use App\Traits\CreateForm;
use App\microfin\savings\MfnSavingsDeposit;
use App\microfin\savings\MfnSavingsWithdraw;
use Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\gnr\Service;

class MfnProcessTransactionUnauthorizationController extends Controller
{

	private $TCN;

	public function __construct()
	{

		$this->TCN = array(
			array('SL#', 70),
			array('SAMITY CODE', 0),
			array('SAMITY NAME', 0),
			array('LOAN DISBURSEMENT AMOUNT', 0),
			array('SAVINGS COLLECTION AMOUNT', 0),
			array('WITHDRAW AMOUNT', 0),
			array('LOAN TRANSACTION AMOUNT', 0),
			array('ACTION', 80)
		);
	}

	public function index()
	{

		$userBarnchId = Auth::user()->branchId;

		if ($userBarnchId == 1) {
			$branchList = MicroFin::getBranchList();
			$samityList = [];
		} else {
			$branchIdArray = Service::getEngagedBranchesByUserId(Auth::user()->id);

			$branchList = [$userBarnchId];
			//$samityList = MicroFin::getBranchWiseSamityList($userBarnchId);
			$samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
		}

		$data = array(
			'userBarnchId'  => $userBarnchId,
			'branchList'    => $branchList,
			'samityList'    => $samityList
		);

		return view('microfin/process/transactionUnauthorization/filteringPart', $data);
	}

	/**
	 * [this is only for the head office users, it filters the transaction authentication list base on branch and date]
	 * @param  Request $req [description]
	 * @return [html content]       [list of samity with transactionn information]
	 */
	public function getSearchResult(Request $req)
	{

		if (Auth::user()->branchId == 1) {
			$targetBranchId = $req->filBranch;
		} else {
			$targetBranchId = Auth::user()->branchId;
		}

		$searchDate = Carbon::parse($req->filDate)->format('Y-m-d');

		$deposits = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->where('isAuthorized', 1)
			->where('amount', '>', 0)
			->where('depositDate', $searchDate)
			->where('branchIdFk', $targetBranchId);

		$withdraws = DB::table('mfn_savings_withdraw')
			->where('softDel', 0)
			->where('isAuthorized', 1)
			->where('amount', '>', 0)
			->where('withdrawDate', $searchDate)
			->where('branchIdFk', $targetBranchId);

		$loanDisbursements = DB::table('mfn_loan')
			->where('softDel', 0)
			->where('isAuthorized', 1)
			->where('disbursementDate', $searchDate)
			->where('branchIdFk', $targetBranchId);

		$loanCollections = DB::table('mfn_loan_collection')
			->where('softDel', 0)
			->where('isAuthorized', 1)
			->where('collectionDate', $searchDate)
			->where('branchIdFk', $targetBranchId);


		if ($req->filSamity != '') {
			$loanCollections = $loanCollections->where('samityIdFk', $req->filSamity)->orderBy('samityIdFk');
			$deposits = $deposits->where('samityIdFk', $req->filSamity)->orderBy('samityIdFk');
			$withdraws = $withdraws->where('samityIdFk', $req->filSamity)->orderBy('samityIdFk');
			$loanDisbursements = $loanDisbursements->where('samityIdFk', $req->filSamity)->orderBy('samityIdFk');
			$loanCollections = $loanCollections->where('samityIdFk', $req->filSamity)->orderBy('samityIdFk');
		}

		$deposits = $deposits->orderBy('memberIdFk')->select('id', 'memberIdFk', 'accountIdFk', 'amount', 'samityIdFk')->get();
		$withdraws = $withdraws->orderBy('memberIdFk')->select('id', 'memberIdFk', 'accountIdFk', 'amount', 'samityIdFk')->get();
		$loanDisbursements = $loanDisbursements->orderBy('memberIdFk')->select('id', 'memberIdFk', 'loanCode', 'loanAmount', 'samityIdFk')->get();
		$loanCollections = $loanCollections->orderBy('memberIdFk')->select('id', 'memberIdFk', 'loanIdFk', 'amount', 'samityIdFk')->get();

		$allMemberIds = $deposits->pluck('memberIdFk')->toArray();
		$allMemberIds = array_merge($allMemberIds, $withdraws->pluck('memberIdFk')->toArray());
		$allMemberIds = array_merge($allMemberIds, $loanDisbursements->pluck('memberIdFk')->toArray());
		$allMemberIds = array_merge($allMemberIds, $loanCollections->pluck('memberIdFk')->toArray());
		$allMemberIds = array_unique($allMemberIds);

		$members = DB::table('mfn_member_information')
			->whereIn('id', $allMemberIds)
			->select('id', 'name', 'code')
			->get();

		$allSamityIds = $deposits->pluck('samityIdFk')->toArray();
		$allSamityIds = array_merge($allSamityIds, $withdraws->pluck('samityIdFk')->toArray());
		$allSamityIds = array_merge($allSamityIds, $loanDisbursements->pluck('samityIdFk')->toArray());
		$allSamityIds = array_merge($allSamityIds, $loanCollections->pluck('samityIdFk')->toArray());
		$allSamityIds = array_unique($allSamityIds);

		$samities = DB::table('mfn_samity')
			->whereIn('id', $allSamityIds)
			->select('id', 'name', 'code')
			->get();

		$savingsAccountIds = $deposits->pluck('accountIdFk')->toArray();
		$savingsAccountIds = array_merge($savingsAccountIds, $withdraws->pluck('accountIdFk')->toArray());
		$savingsAccountIds = array_unique($savingsAccountIds);

		$savingsAccounts = DB::table('mfn_savings_account')
			->whereIn('id', $savingsAccountIds)
			->select('id', 'savingsCode')
			->get();

		$loanAccounts = DB::table('mfn_loan')
			->whereIn('id', $loanCollections->pluck('loanIdFk'))
			->select('id', 'loanCode')
			->get();

		$softwareDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);

		$transactionDate = date('d-m-Y', strtotime($searchDate));

		$TCN = $this->TCN;
		$data = array(
			'TCN'               => $TCN,
			'userBarnchId'      => Auth::user()->branchId,
			'searchDate'        => $searchDate,
			'softwareDate'      => $softwareDate,
			'deposits'          => $deposits,
			'withdraws'         => $withdraws,
			'loanDisbursements' => $loanDisbursements,
			'loanCollections'   => $loanCollections,
			'members'           => $members,
			'samities'          => $samities,
			'savingsAccounts'   => $savingsAccounts,
			'loanAccounts'      => $loanAccounts,
			'transactionDate'   => $transactionDate
		);

		return view('microfin/process/transactionUnauthorization/filtertedContetnt', $data);
	}

	/**
	 * [makeCollectionWithAdditionalAttribute description]
	 * @param  [collection] $collectionObj
	 * @param  [string] $attrName
	 * @param  [string] $attrValue
	 * @return [coeelction]
	 */
	public function makeCollectionWithAdditionalAttribute($collectionObj, $attrName, $attrValue)
	{
		$collectionObj = json_decode($collectionObj, true);
		$collectionObj = collect($collectionObj);
		$collectionObj = $collectionObj->map(function ($collection) use ($attrName, $attrValue) {
			$collection[$attrName] = $attrValue;
			return $collection;
		});

		return $collectionObj;
	}

	/**
	 * [this function unauthorize the transactions samity wise]
	 * @param  Request $req [it holds the transaction type and its Id]
	 * @return [json]       [json response]
	 */
	public function unAuthorizeSamityTransaction(Request $req)
	{
		$samity = DB::table('mfn_samity')->where('id', $req->samityId)->select('id', 'branchId')->first();
		$softwareDate = MicroFin::getSoftwareDateBranchWise($samity->branchId);

		DB::table('mfn_savings_deposit')->where('softDel', 0)->where('samityIdFk', $req->samityId)->where('depositDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('samityIdFk', $req->samityId)->where('withdrawDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_loan_collection')->where('softDel', 0)->where('samityIdFk', $req->samityId)->where('collectionDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		$data = array(
			'responseTitle' =>  'Success!',
			'responseText'  =>  'Samity transaction unauthorized successfully.'
		);

		return response::json($data);
	}

	/**
	 * [this function unauthorize a single transaction]
	 * @param  Request $req [it holds the transaction type and its Id]
	 * @return [json]       [json response]
	 */
	public function unAuthorizeTransaction(Request $req)
	{
		$samity = DB::table('mfn_samity')->where('id', $req->samityId)->select('id', 'branchId')->first();
		$softwareDate = MicroFin::getSoftwareDateBranchWise($samity->branchId);

		DB::table('mfn_savings_deposit')->where('softDel', 0)->where('samityIdFk', $req->samityId)->where('depositDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('samityIdFk', $req->samityId)->where('withdrawDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_loan')->where('softDel', 0)->where('samityIdFk', $req->samityId)->where('disbursementDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_loan_collection')->where('softDel', 0)->where('samityIdFk', $req->samityId)->where('collectionDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		$data = array(
			'responseTitle' =>  'Success!',
			'responseText'  =>  'Samity transaction unauthorized successfully.'
		);

		return response::json($data);
	}

	public function unauthorizeAllTransactions(Request $req)
	{

		$userBarnchId = Auth::user()->branchId;
		if ($userBarnchId != 1) {
			$targetBranchId = $userBarnchId;
			$softwareDate = $this->getSoftwareDate();
		} else {
			$targetBranchId = $req->branchId;
			$softwareDate = MicroFin::getSoftwareDateBranchWise($req->branchId);
		}

		DB::table('mfn_savings_deposit')->where('softDel', 0)->where('branchIdFk', $targetBranchId)->where('depositDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_savings_withdraw')->where('softDel', 0)->where('branchIdFk', $targetBranchId)->where('withdrawDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_loan')->where('softDel', 0)->where('branchIdFk', $targetBranchId)->where('disbursementDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		DB::table('mfn_loan_collection')->where('softDel', 0)->where('branchIdFk', $targetBranchId)->where('collectionDate', $softwareDate)->where('isAuthorized', 1)->update(['isAuthorized' => 0]);

		$data = array(
			'responseTitle' =>  'Success!',
			'responseText'  =>  'All transaction unauthorized successfully.'
		);

		return response::json($data);
	}

	public function unAuthorizeParticularTransaction(Request $req)
	{
		$transaction = null;
		$transactionDate = null;

		if ($req->transactionName == 'Deposit') {
			$transaction = DB::table('mfn_savings_deposit')->where('id', $req->transactionId)->first();
			$transactionDate = $transaction->depositDate;
		} elseif ($req->transactionName == 'Withdraw') {
			$transaction = DB::table('mfn_savings_withdraw')->where('id', $req->transactionId)->first();
			$transactionDate = $transaction->withdrawDate;
		} elseif ($req->transactionName == 'LoanDisbursement') {
			$transaction = DB::table('mfn_loan')->where('id', $req->transactionId)->first();
			$transactionDate = $transaction->disbursementDate;
		} elseif ($req->transactionName == 'LoanCollection') {
			$transaction = DB::table('mfn_loan_collection')->where('id', $req->transactionId)->first();
			$transactionDate = $transaction->collectionDate;
		}

		if ($transaction==null) {
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Something went wrong, please try again later.'
			);

			return response::json($data);
		}

		$branchDate = MicroFin::getSoftwareDateBranchWise($transaction->branchIdFk);

		if ($branchDate != $transactionDate) {
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Branch date is not matched with this date.'
			);

			return response::json($data);
		}

		if ($req->transactionName == 'Deposit') {
			$transaction = DB::table('mfn_savings_deposit')->where('id', $req->transactionId)->update(['isAuthorized' => 0]);
		} elseif ($req->transactionName == 'Withdraw') {
			$transaction = DB::table('mfn_savings_withdraw')->where('id', $req->transactionId)->update(['isAuthorized' => 0]);
		} elseif ($req->transactionName == 'LoanDisbursement') {
			$transaction = DB::table('mfn_loan')->where('id', $req->transactionId)->update(['isAuthorized' => 0]);
		} elseif ($req->transactionName == 'LoanCollection') {
			$transaction = DB::table('mfn_loan_collection')->where('id', $req->transactionId)->update(['isAuthorized' => 0]);
		}

		$data = array(
			'responseTitle' =>  'Success!',
			'responseText'  =>  'Transaction unauthorized successfully.'
		);

		return response::json($data);
	}

	public function getSoftwareDate()
	{
		$userBarnchId = Auth::user()->branchId;

		$softwareDate = DB::table('mfn_day_end')->where('branchIdFk', $userBarnchId)->where('isLocked', 0)->value('date');
		if ($softwareDate == '' || $softwareDate == null) {
			$softwareDate = DB::table('gnr_branch')->where('id', $userBarnchId)->value('softwareStartDate');
		}

		return $softwareDate;
	}
}
