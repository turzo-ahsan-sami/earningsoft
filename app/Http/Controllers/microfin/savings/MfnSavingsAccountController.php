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
use App\microfin\MfnMemberType;
use App\Traits\CreateForm;
use App\Traits\GetSoftwareDate;
use App\microfin\savings\MfnSavingsProduct;
use App\microfin\savings\MfnSavingsFdrProductRepayAmount;
use App\microfin\savings\MfnSavingsAccount;
use App\microfin\savings\MfnOpeningSavingsAccountInfo;
use App\microfin\savings\MfnSavingsAccountNominee;
use App\microfin\savings\MfnSavingsDeposit;
use Auth;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\gnr\Service;
use App;

class MfnSavingsAccountController extends Controller
{
	use CreateForm;
	use GetSoftwareDate;

	private $TCN;

	public function __construct()
	{

		$this->TCN = array(
			array('SL#', 55),
			array('Savings Code', 170),
			array('Member Code', 0),
			array('Member Name', 0),
			array('Samity Code', 0),
			array('Samity Name', 0),
			array('Auto Process Amount', 120),
			array('Opening Date', 0),
			array('Savings <br> Status', 80),
			array('Closing Date', 0),
			array('Entry By', 0),
			array('Action', 100),
		);
	}

	public function index(Request $req)
	{

		$userBranchId = Auth::user()->branchId;
		$softDate = GetSoftwareDate::getSoftwareDate();

		$accounts = MfnSavingsAccount::where('softDel', 0);
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
		if ($userBranchId != 1) {

			//$accounts = $accounts->where('branchIdFk',$userBranchId)->where('accountOpeningDate','<=',$softDate);

			$accounts = $accounts->whereIn('branchIdFk', $branchIdArray)->where('accountOpeningDate', '<=', $softDate);


			//$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
			$samityList = MicroFin::getBranchWiseSamityList($branchIdArray);
		} else {
			if (isset($req->filBranch)) {
				if ($req->filBranch != '' && $req->filBranch != null) {
					$samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
					$accounts = $accounts->where('branchIdFk', $req->filBranch);
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
				$accounts = $accounts->where('branchIdFk', '=', $req->filBranch);
			}
		}

		if (isset($req->filSamity)) {
			if ($req->filSamity != '' && $req->filSamity != null) {
				$accounts = $accounts->where('samityIdFk', $req->filSamity);
			}
		}
		if (isset($req->filMemberCode)) {
			if ($req->filMemberCode != '' && $req->filMemberCode != null) {
				$memberId = DB::table('mfn_member_information')->where('softDel', 0)->where('code', $req->filMemberCode)->value('id');
				$accounts = $accounts->where('memberIdFk', $memberId);
			}
		}
		if (isset($req->filProduct)) {
			if ($req->filProduct != '' && $req->filProduct != null) {
				$accounts = $accounts->where('savingsProductIdFk', $req->filProduct);
			}
		}
		if (isset($req->filDateFrom)) {
			if ($req->filDateFrom != '' && $req->filDateFrom != null) {
				$dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
				$accounts = $accounts->where('accountOpeningDate', '>=', $dateFrom);
			}
		}
		if (isset($req->filDateTo)) {
			if ($req->filDateTo != '' && $req->filDateTo != null) {
				$dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
				$accounts = $accounts->where('accountOpeningDate', '<=', $dateTo);
			}
		}

		$accounts = $accounts->paginate(15);
		$TCN = $this->TCN;

		$softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
		$bankList = $this->getBankList();

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

		$productList = MicroFin::getAllSavingsProductList();

		$damageData = array(
			'TCN'           =>  $TCN,
			'accounts'      =>  $accounts,
			'softwareDate'  =>  $softwareDate,
			'softDate'      =>  $softDate,
			'bankList'      =>  $bankList,
			'userBranchId'  =>  $userBranchId,
			'branchList'    =>  $branchList,
			'samityList'    =>  $samityList,
			'productList'   =>  $productList,
			'branchIdArray'   =>  $branchIdArray
		);

		return view('microfin/savings/savingsAccount/viewSavingsAccount', $damageData);
	}

	public function viewOpenigAccounts(Request $req)
	{

		$userBranchId = Auth::user()->branchId;
		$softDate = GetSoftwareDate::getSoftwareDate();
		// dd($userBarnchId);
		$accounts = MfnSavingsAccount::where('softDel', 0)->where('isFromOpening', 1);

		if ($userBranchId != 1) {
			// $accounts = $accounts->where('branchIdFk',$userBarnchId);
		}

		if ($userBranchId != 1) {
			// $accounts = $accounts->where('branchIdFk',$userBranchId)->where('accountOpeningDate','<=',$softDate);
			$accounts = MfnSavingsAccount::where('softDel', 0)->where('isFromOpening', 1)->where('branchIdFk', $userBranchId);
			$samityList = MicroFin::getBranchWiseSamityList($userBranchId);
		} else {
			if (isset($req->filBranch)) {
				if ($req->filBranch != '' && $req->filBranch != null) {
					$samityList = MicroFin::getBranchWiseSamityList($req->filBranch);
					$accounts = $accounts->where('branchIdFk', $req->filBranch);
					$softDate = MicroFin::getSoftwareDateBranchWise($req->filBranch);
				} else {
					$samityList = MicroFin::getAllSamityList();
				}
			} else {
				$samityList = MicroFin::getAllSamityList();
			}
		}

		if (isset($req->filSamity)) {
			if ($req->filSamity != '' && $req->filSamity != null) {
				$accounts = $accounts->where('samityIdFk', $req->filSamity);
			}
		}
		if (isset($req->filMemberCode)) {
			if ($req->filMemberCode != '' && $req->filMemberCode != null) {
				$memberId = DB::table('mfn_member_information')->where('softDel', 0)->where('code', $req->filMemberCode)->value('id');
				$accounts = $accounts->where('memberIdFk', $memberId);
			}
		}
		if (isset($req->filProduct)) {
			if ($req->filProduct != '' && $req->filProduct != null) {
				$accounts = $accounts->where('savingsProductIdFk', $req->filProduct);
			}
		}
		if (isset($req->filDateFrom)) {
			if ($req->filDateFrom != '' && $req->filDateFrom != null) {
				$dateFrom = Carbon::parse($req->filDateFrom)->format('Y-m-d');
				$accounts = $accounts->where('accountOpeningDate', '>=', $dateFrom);
			}
		}
		if (isset($req->filDateTo)) {
			if ($req->filDateTo != '' && $req->filDateTo != null) {
				$dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
				$accounts = $accounts->where('accountOpeningDate', '<=', $dateTo);
			}
		}

		// $accounts = $accounts->paginate(15);

		$openingInfo = DB::table('mfn_opening_savings_account_info')->whereIn('savingsAccIdFk', $accounts->pluck('id'))->select('savingsAccIdFk', 'manualSavingCode')->get();

		// $TCN = $this->TCN;

		// 
		$accounts = $accounts->paginate(15);
		$TCN = $this->TCN;

		$softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
		$bankList = $this->getBankList();

		$branchList = MicroFin::getBranchList();
		$productList = MicroFin::getAllSavingsProductList();

		$damageData = array(
			'TCN'           =>  $TCN,
			'accounts'      =>  $accounts,
			'softwareDate'  =>  $softwareDate,
			'softDate'      =>  $softDate,
			'bankList'      =>  $bankList,
			'userBranchId'  =>  $userBranchId,
			'branchList'    =>  $branchList,
			'samityList'    =>  $samityList,
			'productList'   =>  $productList,
			'openingInfo'   =>  $openingInfo
		);
		//

		// $damageData = array(
		//     'TCN'           =>  $TCN,
		//     'accounts'      =>  $accounts,
		//     'openingInfo'   =>  $openingInfo
		// );

		return view('microfin.configuration.openingBalance.savings.OpeningSavingsAccount.viewSavingsAccount', $damageData);
	}

	public function addAccount()
	{
		$userBarnchId = Auth::user()->branchId;
		$softwareDate = GetSoftwareDate::getSoftwareDate();
		$softwareDateInFormat = GetSoftwareDate::getSoftwareDateInFormat();
		$bankList = $this->getBankList();
		$samityList = ['' => '--All--'] + MicroFin::getBranchWiseSamityList($userBarnchId);
		$data = array(
			'softwareDate'          =>  $softwareDate,
			'softwareDateInFormat'  =>  $softwareDateInFormat,
			'bankList'              =>  $bankList,
			'samityList'            =>  $samityList
		);

		return view('microfin.savings.savingsAccount.addSavingsAccount', $data);
	}

	public function addWeeklySavingsAccount()
	{
		$userBarnchId = Auth::user()->branchId;
		$softwareDate = GetSoftwareDate::getSoftwareDate();
		$softwareDateInFormat = GetSoftwareDate::getSoftwareDateInFormat();
		$bankList = $this->getBankList();
		$branchList = array();
		$samityList = array();

		if ($userBarnchId == 1) {
			$branchList = ['' => '--Select--'] + MicroFin::getBranchList();
		} else {
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

			/*$samityList = [''=>'--Select--'] + MicroFin::getBranchWiseSamityList($userBarnchId);*/

			$samityList = ['' => '--Select--'] + MicroFin::getBranchWiseSamityList($branchIdArray);
		}
		$data = array(
			'softwareDate'          =>  $softwareDate,
			'softwareDateInFormat'  =>  $softwareDateInFormat,
			'bankList'              =>  $bankList,
			'samityList'            =>  $samityList,
			'userBarnchId'          =>  $userBarnchId,
			'branchList'            =>  $branchList
		);

		// dd($data, $userBarnchId);

		return view('microfin.savings.savingsAccount.addWeeklySavingsAccount', $data);
	}

	public function getBranchWiseSamityDropDownList(Request $req)
	{
		if ($req->branchId == 1) {
			$samityList = '';
		} else {
			$samityList = MicroFin::getBranchWiseSamityList($req->branchId);
		}

		return response::json($samityList);
	}

	// SAMITY WISE MEMBER WHO ARE ELIGIBLE FOR WEEKLY SAVINGS
	public function getSamityWiseWeeklySavingsMemeberDropDownList(Request $req)
	{

		$userBranchId = Auth::user()->branchId;
		if ($userBranchId == 1 && $req->samityId == '') {
			return '';
		}

		if ($userBranchId != 1) {
			$targetBranchId = $userBranchId;
		} else {
			$targetBranchId = (int) DB::table('mfn_samity')->where('id', $req->samityId)->value('branchId');
		}

		$branchSoftwareDate = MicroFin::getSoftwareDateBranchWise($targetBranchId);

		$weeklySavingsExistMembers = DB::table('mfn_savings_account')
			->where([['savingsProductIdFk', '=', 1], ['branchIdFk', '=', $targetBranchId], ['softDel', '=', 0]])
			->where(function ($query) use ($branchSoftwareDate) {
				$query->where([['closingDate', '>=', $branchSoftwareDate]])
					->orWhere([['closingDate', '=', '0000-00-00']])
					->orWhere([['closingDate', '=', null]]);
			})
			->pluck('memberIdFk')
			->toArray();

		$members = DB::table('mfn_member_information')->where('softDel', 0)->where('status', 1)->where('branchId', $targetBranchId)->where('admissionDate', '<=', $branchSoftwareDate)->whereNotIn('id', $weeklySavingsExistMembers);

		if ($req->samityId != '' || $req->samityId != 0) {
			$members = $members->where('samityId', $req->samityId);
		}
		$members = $members->orderBy('code')->select('id', 'name', 'code', 'branchId', 'samityId')->get();

		$branches = DB::table('gnr_branch')->select('id', 'name')->get();
		$samities = DB::table('mfn_samity')->select('id', 'name', 'workingAreaId')->get();
		$workingAreas = DB::table('gnr_working_area')->select('id', 'name')->get();

		$concatString = '';
		foreach ($members as $member) {
			$branchName = $branches->where('id', $member->branchId)->max('name');
			$samity = $samities->where('id', $member->samityId)->first();
			$workingAreaName = $workingAreas->where('id', $samity->workingAreaId)->max('name');
			$concatString = $concatString . "<tr>
            <td memberId=" . $member->id . " branchId=" . $member->branchId . " samityId=" . $member->samityId . " workingAreaId=" . $samity->workingAreaId . " style='text-align: left;'>
            <span class='memberName' style='font-size: 11;font-weight: bold;'>" . $member->name . "</span> - 
            <span class='memberCode' style='font-size: 11;font-weight: bold;'>" . $member->code . "</span><br>
            Branch: " . $branchName . "<br>
            Samity: " . $samity->name . "<br>
            Working Area: " . $workingAreaName . "
            </td>
            </tr>";
		}
		return response::json($concatString);
	}
	// END OF SAMITY WISE MEMBER WHO ARE ELIGIBLE FOR WEEKLY SAVINGS 

	// STORE WEEKLY SAVINGS ACCOUNT INFOS.
	public function storeWeeklySavingsAccount(Request $req)
	{

		$branchSoftwareDate = MicroFin::getSoftwareDateBranchWise($req->branchId);
		$requestAccountOppeningDate = date_create($req->openingDate);
		$requestAccountOppeningDate = date_format($requestAccountOppeningDate, 'Y-m-d');

		$interestRate = (int) $req->interestRate;

		$memberAdmissionDate = DB::table('mfn_member_information')
			->where('id', $req->memberId)
			->pluck('admissionDate')
			->toArray();

		// dd($req, $requestAccountOppeningDate, $branchSoftwareDate, $memberAdmissionDate, $memberAdmissionDate[0], $interestRate);

		if ($requestAccountOppeningDate != $branchSoftwareDate) {
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Account opening date is not equal to the branch software date!!'
			);

			return response::json($data);
		} elseif ($memberAdmissionDate[0] > $requestAccountOppeningDate) {
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Member admission date is less than account opening date!!'
			);

			return response::json($data);
		}

		$insertSavingsAccount = DB::table('mfn_savings_account')
			->insert(
				[
					'savingsCode'                => $req->savingCode,
					'accountOpeningDate'         => $requestAccountOppeningDate,
					'savingsProductIdFk'         => 1,
					'memberIdFk'                 => $req->memberId,
					'branchIdFk'                 => $req->branchId,
					'samityIdFk'                 => $req->samityId,
					'workingAreaIdFk'            => $req->workingAreaId,
					'depositTypeIdFk'            => 1,
					'savingsAmount'              => 0,
					'savingsInterestRate'        => $interestRate,
					'autoProcessAmount'          => $req->autoProcessAmount,
					'savingCycle'                => $req->savingCycle,
					'periodYear'                 => 0,
					'periodMonth'                => 0,
					'initialAmount'              => 0,
					'payableAmount'              => 0,
					'fixedDepositAmount'         => 0,
					'isPartialWithdrawAllowed'   => 1,
					'entryByEmployeeIdFk'        => Auth::user()->emp_id_fk,
					'isFromOpening'              => 0,
					'createdDate'                => date('Y-m-d'),
					'status'                     => 1,
					'softDel'                    => 0,
					'ds'                         => 0,
					'closingDate'                => '0000-00-00',
					'openingSavingDepositAmount' => 0
				]
			);
		$logArray = array(
			'moduleId'  => 6,
			'controllerName'  => 'MfnSavingsAccountController',
			'tableName'  => 'mfn_savings_account',
			'operation'  => 'insert',
			'primaryIds'  => [DB::table('mfn_savings_account')->max('id')]
		);
		Service::createLog($logArray);


		$data = array(
			'responseTitle' =>  'Success!',
			'responseText'  =>  'Account inserted successfully.'
		);

		return response::json($data);
	}
	// END OF STORE WEEKLY SAVINGS ACCOUNT INFOS.

	public function addOpeningAccount()
	{
		$userBarnchId = Auth::user()->branchId;
		$softwareDate = GetSoftwareDate::getSoftwareDate();
		$softwareDateInFormat = GetSoftwareDate::getSoftwareDateInFormat();
		$bankList = $this->getBankList();
		$samityList = ['' => '--All--'] + MicroFin::getBranchWiseSamityList($userBarnchId);
		$data = array(
			'softwareDate'          =>  $softwareDate,
			'softwareDateInFormat'  =>  $softwareDateInFormat,
			'bankList'              =>  $bankList,
			'samityList'            =>  $samityList
		);

		return view('microfin/configuration/openingBalance/savings/OpeningSavingsAccount/addSavingsAccount', $data);
	}

	/*
        |--------------------------------------------------------------------------
        | MICRO FRENANCE: STORE PRODUCT
        |--------------------------------------------------------------------------
        */
	public function storeAccount(Request $req)
	{
		DB::beginTransaction();
		try {
			$member = DB::table('mfn_member_information')->where('id', $req->memberId)->first();
			$product = MfnSavingsProduct::find($req->product);
			$customError = array();

			$rules = array(
				'member'        =>  'required',
				'product'       =>  'required'
			);

			/* if (isset($req->isOpeningData)) {
                $rules = $rules + array(
                    'manualSavingCode'  => 'required|unique:mfn_opening_savings_account_info'
                );
            }*/

			// Deposit Type Voluntary Monthly
			if ($product->depositTypeIdFk == 2 && $product->savingCollectionFrequencyIdFk == 2) {
				$rules = $rules + array(
					'periodYear'        => 'required',
					'periodMonth'       => 'required',
					/*'initialAmount'     => 'required',*/
					'autoProcessAmount' => 'required',
					'matureDate'        => 'required',
					'payableAmount'     => 'required'
				);
			}

			// Deposit Type FDR (Voluntary)
			if ($product->depositTypeIdFk == 4) {
				$rules = $rules + array(
					'periodOts'             => 'required',
					'fixedDepositAmount'    => 'required',
					'matureDate'            => 'required',
					'payableAmount'         => 'required'

				);

				if (!isset($req->isOpeningData)) {
					$rules = $rules + array(
						'transactionType'       => 'required'
					);
				}
			}
			if ($req->transactionType == 'Bank' && !isset($req->isOpeningData)) {
				$rules = $rules + array(
					'bank'          => 'required',
					'chequeNumber'  => 'required'
				);
			}

			if ($product->isNomineeRequired == 1 && !isset($req->isOpeningData)) {

				// If Nominee information is empty give custom error
				if (isset($req->nomineeName)) {
					$totalShare = 0;
					foreach ($req->nomineeName as $key => $nomineeName) {
						if ($nomineeName == '' || $req->nomineeRealtion[$key] == '' || $req->nomineeShare[$key] == '') {
							$customError =  $customError + array(
								'emptyTableError'   => 'Please fill all the fields.'
							);
						}
						$totalShare = $totalShare + (float) $req->nomineeShare[$key];
					}

					if ($totalShare != 100) {
						$customError =  $customError + array(
							'nomineeShareError'   => 'Sum of Share should be 100%.'
						);
					}
				} else {
					// No nominee added
					$customError =  $customError + array(
						'emptyTableError'   => 'Please add atleast one nominee.'
					);
				}
			}

			$attributesNames = array(
				'member'                => 'Member',
				'product'               => 'Product',
				'manualSavingCode'      => 'Saving Code',
				'periodYear'            => 'Year',
				'periodMonth'           => 'Month',
				'autoProcessAmount'     => 'Auto Process Amount',
				'matureDate'            => 'Mature Date',
				'payableAmount'         => 'Payable Amount',
				'periodOts'             => 'Period',
				'fixedDepositAmount'    => 'Fixed Deposit Amount',
				'transactionType'       => 'Transaction Type'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if ($validator->fails() || count($customError) > 0) {
				return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customError));
			} else {
				// Store Data                

				$savingCycle = DB::table('mfn_savings_account')->where('softDel', 0)->where('memberIdFk', $member->id)/*->where('depositTypeIdFk','!=',1)*/->where('savingsProductIdFk', $product->id)->count() + 1;

				$numberOfAcc = $savingCycle;
				$numberOfAcc = str_pad($numberOfAcc, 3, '0', STR_PAD_LEFT);
				$savingCode = $product->shortName . '.' . $member->code . '.' . $numberOfAcc;

				if (isset($req->isPartialWithdrawAllowed) && $product->depositTypeIdFk == 2) {
					$isPartialWithdrawAllowed = $req->isPartialWithdrawAllowed;
				} else {
					$isPartialWithdrawAllowed = $product->partialWithdrawAllowId;
				}

				if (isset($req->isOpeningData)) {
					$oldSavingsCode = $req->previousSavingCode;
				} else {
					$oldSavingsCode = '';
				}

				$account = new MfnSavingsAccount;
				$account->savingsCode               =   $savingCode;
				$account->oldSavingsCode            =   $oldSavingsCode;
				$account->accountOpeningDate        =   Carbon::parse($req->openingDate);
				$account->savingsProductIdFk        =   $product->id;
				$account->memberIdFk                =   $member->id;
				$account->depositTypeIdFk           =   $product->depositTypeIdFk;
				$account->savingsInterestRate       =   $product->interestRate;
				$account->branchIdFk                =   $req->branchId;
				$account->samityIdFk                =   $req->samityId;
				$account->workingAreaIdFk           =   $req->workingAreaId;
				$account->isPartialWithdrawAllowed  =   $isPartialWithdrawAllowed;
				$account->entryByEmployeeIdFk       =   Auth::user()->emp_id_fk;
				$account->createdDate               =   Carbon::now();

				// Deposit Type Mendatory
				if ($product->depositTypeIdFk == 1) {
					$account->autoProcessAmount     =  $req->autoProcessAmount;
				}

				// Deposit Type Voluntary
				if ($product->depositTypeIdFk == 2 && $product->savingCollectionFrequencyIdFk == 1) {
					$account->autoProcessAmount     =  $req->autoProcessAmount;
				}

				// Deposit Type Voluntary Monthly
				if ($product->depositTypeIdFk == 2 && $product->savingCollectionFrequencyIdFk == 2) {

					$account->periodYear            =  $req->periodYear;
					$account->periodMonth           =  $req->periodMonth;
					$account->initialAmount         =  $req->initialAmount;
					$account->autoProcessAmount     =  $req->autoProcessAmount;
					$account->accountMatureDate     =  Carbon::parse($req->matureDate);
					$account->payableAmount         =  str_replace(',', '', $req->payableAmount);
				}

				// Deposit Type FDR (Voluntary)
				if ($product->depositTypeIdFk == 4) {

					$period = explode('-', $req->periodOts);

					$account->periodYear            =  $period[0];
					$account->periodMonth           =  $period[1];
					$account->fixedDepositAmount    =  $req->fixedDepositAmount;
					$account->accountMatureDate     =  Carbon::parse($req->matureDate);
					$account->payableAmount         =  str_replace(',', '', $req->payableAmount);
					$account->transactionType       =  $req->transactionType;
				} else {
					$account->transactionType       =  '';
				}

				if ($product->isMultipleSavingAllowed) {
					$account->savingCycle       =  $savingCycle;
				}

				if (isset($req->isOpeningData)) {
					$account->isFromOpening = 1;
				}

				$account->save();

				// Deposit Type FDR (Voluntary)
				if ($product->isNomineeRequired == 1) {
					if (count($req->nomineeName) > 0) {
						foreach ($req->nomineeName as $key => $nomineeName) {
							$nominee = new MfnSavingsAccountNominee;
							$nominee->memberIdFk            =   $member->id;
							$nominee->savingsAccountIdFk    =   $account->id;
							$nominee->name                  =   $nomineeName;
							$nominee->relation              =   $req->nomineeRealtion[$key];
							$nominee->share                 =   $req->nomineeShare[$key];
							$nominee->createdAt             =   Carbon::now();
							$nominee->save();
						}
					}
				}

				// Here deposit will be saved when it is one time savings account
				if ($req->transactionType == "Cash") {
					// Cash In Hand ledger id
					$ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');;
				} else {
					$ledgerId = $req->bank;
				}
				if ($product->depositTypeIdFk == 4 && !isset($req->isOpeningData)) {
					$primaryProductId = DB::table('mfn_member_information')->where('id', $account->memberIdFk)->value('primaryProductId');
					$deposit = new MfnSavingsDeposit;
					$deposit->memberIdFk            = $member->id;
					$deposit->branchIdFk            = $req->branchId;
					$deposit->samityIdFk            = $req->samityId;
					$deposit->accountIdFk           = $account->id;
					$deposit->productIdFk           = $account->savingsProductIdFk;
					$deposit->primaryProductIdFk    = $primaryProductId;
					$deposit->amount                = str_replace(',', '', $req->fixedDepositAmount);
					$deposit->balanceBeforeDeposit  = 0;
					$deposit->depositDate           = Carbon::parse($req->openingDate);
					$deposit->paymentType           = $req->transactionType;
					$deposit->ledgerIdFk            = $ledgerId;
					$deposit->chequeNumber          = $req->chequeNumber;
					$deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
					$deposit->isFromAutoProcess     = 0;
					$deposit->createdAt             = Carbon::now();
					$deposit->save();
				}


				// Store Data for the Opening Accounts
				/*if (isset($req->isOpeningData)) {
                    $savingsInfo = new MfnOpeningSavingsAccountInfo;
                    $savingsInfo->savingsAccIdFk    = $account->id;
                    $savingsInfo->manualSavingCode  = $req->manualSavingCode;
                    $savingsInfo->save();
                }*/
				DB::commit();
				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Account inserted successfully.'
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


	public function updateAccount(Request $req)
	{
		// dd($req);
		DB::beginTransaction();
		try {
			$branchOB = DB::table('mfn_savings_account')->where('branchIdFk', $req->branchId)->select('accountOpeningDate', 'isFromOpening')->first();
			$getSoftDate = MicroFin::getSoftwareDateBranchWise($req->branchId);
			$branchSoftwareDate = DB::table('gnr_branch')->where('id', $req->branchId)->select('softwareStartDate')->first();

			// dd($branchOB, $getSoftDate, $branchSoftwareDate);

			if ($branchOB->isFromOpening == 1) {
				if ($getSoftDate != $branchSoftwareDate->softwareStartDate) {
					$data = array(
						'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
						'responseText'   =>  MicroFinance::getMessage('oneTimeLoanUpdateWarning'),
					);

					return response()->json($data);
				}
			} else {
				if ($getSoftDate != $branchOB->accountOpeningDate) {
					$data = array(
						'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
						'responseText'   =>  MicroFinance::getMessage('oneTimeLoanUpdateWarning'),
					);

					return response()->json($data);
				}
			}



			$member = DB::table('mfn_member_information')->where('id', $req->memberId)->first();
			$product = MfnSavingsProduct::find($req->product);
			$customError = array();

			$rules = array(
				'member'        =>  'required',
				'product'       =>  'required'
			);

			/*if (isset($req->isOpeningData)) {                
                $savingsInfoId = MfnOpeningSavingsAccountInfo::where('savingsAccIdFk',$req->accId)->value('id');
                $rules = $rules + array(
                    'manualSavingCode'  => 'required|unique:mfn_opening_savings_account_info,manualSavingCode,'.$savingsInfoId
                );
            }*/

			// Deposit Type Voluntary Monthly
			if ($product->depositTypeIdFk == 2 && $product->savingCollectionFrequencyIdFk == 2) {
				$rules = $rules + array(
					'periodYear'        => 'required',
					'periodMonth'       => 'required',
					/*'initialAmount'     => 'required',*/
					'autoProcessAmount' => 'required',
					'matureDate'        => 'required',
					'payableAmount'     => 'required'
				);
			}

			// Deposit Type FDR (Voluntary)
			if ($product->depositTypeIdFk == 4) {
				$rules = $rules + array(
					'periodOts'             => 'required',
					'fixedDepositAmount'    => 'required',
					'matureDate'            => 'required',
					'payableAmount'         => 'required'

				);

				if (!isset($req->isOpeningData)) {
					$rules = $rules + array(
						'transactionType'       => 'required'
					);
				}
			}
			if ($req->transactionType == 'Bank' && !isset($req->isOpeningData)) {
				$rules = $rules + array(
					'bank'          => 'required',
					'chequeNumber'  => 'required'
				);
			}

			if ($product->isNomineeRequired == 1 && !isset($req->isOpeningData)) {

				// If Nominee information is empty give custom error
				if (isset($req->nomineeName)) {
					$totalShare = 0;
					foreach ($req->nomineeName as $key => $nomineeName) {
						if ($nomineeName == '' || $req->nomineeRealtion[$key] == '' || $req->nomineeShare[$key] == '') {
							$customError =  $customError + array(
								'emptyTableError'   => 'Please fill all the fields.'
							);
						}
						$totalShare = $totalShare + (float) $req->nomineeShare[$key];
					}

					if ($totalShare != 100) {
						$customError =  $customError + array(
							'nomineeShareError'   => 'Sum of Share should be 100%.'
						);
					}
				} else {
					// No nominee added
					$customError =  $customError + array(
						'emptyTableError'   => 'Please add atleast one nominee.'
					);
				}
			}

			$attributesNames = array(
				'member'                => 'Member',
				'product'               => 'Product',
				'manualSavingCode'      => 'Saving Code',
				'periodYear'            => 'Year',
				'periodMonth'           => 'Month',
				'autoProcessAmount'     => 'Auto Process Amount',
				'matureDate'            => 'Mature Date',
				'payableAmount'         => 'Payable Amount',
				'periodOts'             => 'Period',
				'fixedDepositAmount'    => 'Fixed Deposit Amount',
				'transactionType'       => 'Transaction Type'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if ($validator->fails() || count($customError) > 0) {
				return response::json(array('errors' => $validator->getMessageBag()->toArray() + $customError));
			} else {
				// Store Data                

				$savingCycle = DB::table('mfn_savings_account')->where('softDel', 0)->where('memberIdFk', $member->id)/*->where('depositTypeIdFk','!=',1)*/->where('savingsProductIdFk', $product->id)->where('id', '!=', $req->accId)->count() + 1;

				$numberOfAcc = $savingCycle;
				$numberOfAcc = str_pad($numberOfAcc, 3, '0', STR_PAD_LEFT);
				$savingCode = $product->shortName . '.' . $member->code . '.' . $numberOfAcc;

				if (isset($req->isPartialWithdrawAllowed) && $product->depositTypeIdFk == 2) {
					$isPartialWithdrawAllowed = $req->isPartialWithdrawAllowed;
				} else {
					$isPartialWithdrawAllowed = $product->partialWithdrawAllowId;
				}

				$account = MfnSavingsAccount::find($req->accId);
				if ($account->savingsProductIdFk != $product->id) {
					$account->savingsCode           =   $savingCode;
					if ($product->isMultipleSavingAllowed) {
						$account->savingCycle       =  $savingCycle;
					}
				}
				$account->oldSavingsCode            =   $req->previousSavingCode;
				$account->accountOpeningDate        =   Carbon::parse($req->openingDate);
				$account->savingsProductIdFk        =   $product->id;
				$account->memberIdFk                =   $member->id;
				$account->depositTypeIdFk           =   $product->depositTypeIdFk;
				$account->savingsInterestRate       =   $product->interestRate;
				$account->branchIdFk                =   $req->branchId;
				$account->samityIdFk                =   $req->samityId;
				$account->workingAreaIdFk           =   $req->workingAreaId;
				$account->isPartialWithdrawAllowed  =   $isPartialWithdrawAllowed;
				$account->entryByEmployeeIdFk       =   Auth::user()->emp_id_fk;
				$account->createdDate               =   Carbon::now();

				// Deposit Type Mendatory
				if ($product->depositTypeIdFk == 1) {
					$account->autoProcessAmount     =  $req->autoProcessAmount;
				}

				// Deposit Type Voluntary
				if ($product->depositTypeIdFk == 2 && $product->savingCollectionFrequencyIdFk == 1) {
					$account->autoProcessAmount     =  $req->autoProcessAmount;
				}

				// Deposit Type Voluntary Monthly
				if ($product->depositTypeIdFk == 2 && $product->savingCollectionFrequencyIdFk == 2) {

					$account->periodYear            =  $req->periodYear;
					$account->periodMonth           =  $req->periodMonth;
					$account->initialAmount         =  $req->initialAmount;
					$account->autoProcessAmount     =  $req->autoProcessAmount;
					$account->accountMatureDate     =  Carbon::parse($req->matureDate);
					$account->payableAmount         =  str_replace(',', '', $req->payableAmount);
				}

				// Deposit Type FDR (Voluntary)
				if ($product->depositTypeIdFk == 4) {

					$period = explode('-', $req->periodOts);

					$account->periodYear            =  $period[0];
					$account->periodMonth           =  $period[1];
					$account->fixedDepositAmount    =  $req->fixedDepositAmount;
					$account->accountMatureDate     =  Carbon::parse($req->matureDate);
					$account->payableAmount         =  str_replace(',', '', $req->payableAmount);
					$account->transactionType       =  $req->transactionType;
				} else {
					$account->transactionType       =  '';
				}

				if (isset($req->isOpeningData)) {
					$account->isFromOpening = 1;
				}

				$account->save();

				DB::table('mfn_savings_fdr_acc_nominee_info')->where('savingsAccountIdFk', $account->id)->delete();
				// Deposit Type FDR (Voluntary)
				if ($product->isNomineeRequired == 1) {
					if (count($req->nomineeName) > 0) {
						foreach ($req->nomineeName as $key => $nomineeName) {
							$nominee = new MfnSavingsAccountNominee;
							$nominee->memberIdFk            =   $member->id;
							$nominee->savingsAccountIdFk    =   $account->id;
							$nominee->name                  =   $nomineeName;
							$nominee->relation              =   $req->nomineeRealtion[$key];
							$nominee->share                 =   $req->nomineeShare[$key];
							$nominee->createdAt             =   Carbon::now();
							$nominee->save();
						}
					}
				}

				// Here deposit will be saved when it is one time savings account
				if ($req->transactionType == "Cash") {
					// Cash In Hand ledger id
					$ledgerId = DB::table('acc_account_ledger')->where('accountTypeId', 4)->where('isGroupHead', 0)->value('id');
				} else {
					$ledgerId = $req->bank;
				}
				if ($product->depositTypeIdFk == 4 && !isset($req->isOpeningData)) {
					$primaryProductId = DB::table('mfn_member_information')->where('id', $account->memberIdFk)->value('primaryProductId');

					$deposit = MfnSavingsDeposit::firstOrNew(['accountIdFk' => $account->id]);
					$deposit->memberIdFk            = $member->id;
					$deposit->branchIdFk            = $req->branchId;
					$deposit->samityIdFk            = $req->samityId;
					$deposit->accountIdFk           = $account->id;
					$deposit->productIdFk           = $account->savingsProductIdFk;
					$deposit->primaryProductIdFk    = $primaryProductId;
					$deposit->amount                = str_replace(',', '', $req->fixedDepositAmount);
					$deposit->balanceBeforeDeposit  = 0;
					$deposit->depositDate           = Carbon::parse($req->openingDate);
					$deposit->paymentType           = $req->transactionType;
					$deposit->ledgerIdFk            = $ledgerId;
					$deposit->chequeNumber          = $req->chequeNumber;
					$deposit->entryByEmployeeIdFk   = Auth::user()->emp_id_fk;
					$deposit->isFromAutoProcess     = 0;
					$deposit->createdAt             = Carbon::now();
					$deposit->save();
				}

				// Store Data for the Opening Accounts
				/*if (isset($req->isOpeningData)) {
                    $savingsInfo = MfnOpeningSavingsAccountInfo::where('savingsAccIdFk',$account->id)->first();
                    $savingsInfo->manualSavingCode  = $req->manualSavingCode;
                    $savingsInfo->save();
                }*/
				DB::commit();

				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Account updated successfully.'
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



	public function deleteAccount(Request $req)
	{

		// dd($req);
		DB::beginTransaction();
		try {
			$savAcc = MfnSavingsAccount::find($req->id);

			$branchDate = MicroFin::getSoftwareDateBranchWise($savAcc->branchIdFk);

			$saviDepositExits = DB::table('mfn_savings_deposit')
				->where('accountIdFk', $req->id)
				->where('softDel', 0)
				->where('isTransferred', 0)
				->where('amount', '>', 0)
				// ->where('depositDate','<',$branchDate)
				->count();

			$saviDepositExits = DB::table('mfn_savings_withdraw')
				->where('accountIdFk', $req->id)
				->where('softDel', 0)
				->where('isTransferred', 0)
				->where('amount', '>', 0)
				// ->where('withdrawDate','<',$branchDate)
				->count();

			if ($saviDepositExits > 0 || $saviDepositExits > 0) {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Please delete transaction first.'
				);

				return response::json($data);
			}

			DB::table('mfn_savings_deposit')
				->where('accountIdFk', $req->id)
				->update(['softDel' => 1]);

			DB::table('mfn_savings_withdraw')
				->where('accountIdFk', $req->id)
				->update(['softDel' => 1]);

			$savAcc->softDel = 1;
			$savAcc->save();

			DB::table('mfn_opening_savings_account_info')->where('savingsAccIdFk', $savAcc->id)->update(['softDel' => 1]);

			DB::commit();

			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Your selected Acoount deleted successfully.'
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
