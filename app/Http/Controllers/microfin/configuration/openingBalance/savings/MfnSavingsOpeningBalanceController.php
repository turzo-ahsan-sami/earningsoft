<?php

namespace App\Http\Controllers\microfin\configuration\openingBalance\savings;

use Illuminate\Http\Request;
use App\Http\Requests;
use Session;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\microfin\savings\MfnOpeningSavingsAccountInfo;
use App\Http\Controllers\microfin\MicroFin;

class MfnSavingsOpeningBalanceController extends Controller {

	public function index(){

		$userBranchId = Auth::user()->branchId;
		$branchList = DB::table('gnr_branch');
		$samityList = DB::table('mfn_samity');
		if($userBranchId!=1){
			$branchList = $branchList->where('id',$userBranchId);
			$samityList = $samityList->where('branchId',$userBranchId);
		}
		$branchList = $branchList->select(DB::raw("CONCAT(LPAD(branchCode,3,'0'),' - ',name) as 'name'"),"id")->get()->pluck('name','id')->toArray();
		$samityList = $samityList->select(DB::raw("CONCAT(code,' - ',name) as name"),'id')->get()->pluck('name','id')->toArray();

		$data = array(
			'userBranchId' 	=> $userBranchId,
			'branchList' 	=> $branchList,
			'samityList' 	=> $samityList
		);

		return view('microfin.configuration.openingBalance.savings.OpeningSavingsBalance.filteringPart', $data); 
	}

	public function filtering(Request $req){
		$userBranchId = Auth::user()->branchId;

		$openingAccounts = DB::table('mfn_savings_account')
		->where('softDel',0)
		->where('isFromOpening',1);

		if ($userBranchId!=1){
			$openingAccounts = $openingAccounts->where('branchIdFk',$userBranchId);
		}
		elseif($req->filBranch!='-1'){
			$openingAccounts = $openingAccounts->where('branchIdFk',$req->filBranch);
		}
		if ($req->filSamity!='-1'){
			$openingAccounts = $openingAccounts->where('samityIdFk',$req->filSamity);
		}
		$openingAccounts = $openingAccounts->orderBy('memberIdFk')->orderBy('savingsProductIdFk')->get();

		$openingInfos = DB::table('mfn_opening_savings_account_info')
		->whereIn('savingsAccIdFk',$openingAccounts->pluck('id'))
		->orderBy('memberIdFk')
		->get();



		$members = DB::table('mfn_member_information')->whereIn('id',$openingAccounts->pluck('memberIdFk'))->select('id','name','code')->get();

		$data = array(
			'openingAccounts'	=> $openingAccounts,
			'openingInfos'		=> $openingInfos,
			'members'			=> $members
		);
		return view('microfin.configuration.openingBalance.savings.OpeningSavingsBalance.viewSavingsBalance', $data); 
	}

	public function storeOpeningData(Request $req){

		// CHECK THAT IF THE SOFTWARE START DATE AND THE CURRENT SOFTWARE DATE IS SAME OR NOT
		$branchId = DB::table('mfn_savings_account')->where('id',$req->accId[0])->first()->branchIdFk;
		$softDate = MicroFin::getSoftwareDateBranchWise($branchId);
		$softwareStartDate = DB::table('gnr_branch')->where('id',$branchId)->first()->softwareStartDate;

		if ($softwareStartDate!=$softDate) {
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'Branch date is not in opening date.'
			);

			return response::json($data);
		}

		// IF ANY SAVINGS INTEREST GENERATED FOR ANY ACCOUNT THEN THAT ACCOUNT COULD NOT BE UPDATED
		$savAccountsHavingInterest = DB::table('mfn_savings_deposit')
		->where('softDel',0)
		->whereIn('accountIdFk',$req->accId)
		->where('paymentType','Interest')
		->pluck('accountIdFk')
		->toArray();

		$modifiledBalanceSavIds = array();

		if (count($savAccountsHavingInterest)>0) {
			foreach ($req->accId as $key => $accId) {
				if (in_array($accId, $savAccountsHavingInterest)) {
					$previosBalance = MfnOpeningSavingsAccountInfo::where('savingsAccIdFk',$accId)->first();
					if ($previosBalance!=null) {
						if ( $previosBalance->openingPrincipal != floatval(str_replace(',', '', $req->deposit[$key])) || $previosBalance->openingInterest != floatval(str_replace(',', '', $req->interest[$key])) || $previosBalance->openingWithdraw != floatval(str_replace(',', '', $req->withdraw[$key])) || $previosBalance->openingBalance != floatval(str_replace(',', '', $req->balance[$key])) ) {
							array_push($modifiledBalanceSavIds, $accId);
						}
					}
				}
			}
		}

		if (count($modifiledBalanceSavIds)>0) {
			$responseText = DB::table('mfn_savings_account')
			->whereIn('id',$modifiledBalanceSavIds)
			->select(DB::raw("GROUP_CONCAT(`savingsCode` SEPARATOR ', ') AS savingsCodes"))
			->value('savingsCodes');
			$data = array(
				'responseTitle' =>  'Warning!',
				'responseText'  =>  'You can not modify the balance because Interest is generated for the following accounts '.$responseText
			);
			return response::json($data);
		}

		$savAccounts = DB::table('mfn_savings_account as t1')
		->join('mfn_member_information as t2','t1.memberIdFk','t2.id')
		->whereIn('t1.id',$req->accId)
		->select('t1.id','t1.memberIdFk','t1.samityIdFk','t1.branchIdFk','t2.primaryProductId')
		->get();

		DB::beginTransaction();

		try{
			foreach ($req->accId as $key => $accId) {
				$savingsInfo = MfnOpeningSavingsAccountInfo::firstOrNew(['savingsAccIdFk'=>$accId]);				
				$savingsInfo->memberIdFk 			= $savAccounts->where('id',$accId)->max('memberIdFk');
				$savingsInfo->primaryProductIdFk 	= $savAccounts->where('id',$accId)->max('primaryProductId');
				$savingsInfo->samityIdFk 			= $savAccounts->where('id',$accId)->max('samityIdFk');
				$savingsInfo->openingPrincipal 		= floatval(str_replace(',', '', $req->deposit[$key]));
				$savingsInfo->openingInterest 		= floatval(str_replace(',', '', $req->interest[$key]));
				$savingsInfo->openingWithdraw 		= floatval(str_replace(',', '', $req->withdraw[$key]));
				$savingsInfo->openingBalance 		= floatval(str_replace(',', '', $req->balance[$key]));
				$savingsInfo->save();
			}

			DB::commit();

			$data = array(
				'responseTitle' =>  'Success!',
				'responseText'  =>  'Data inserted successfully.'
			);

			return response::json($data);   

		}
		catch(\Exception $e){
			DB::rollback();
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Something went wrong. Please try again.'
			);
			return response::json($data);
		}



	}
}