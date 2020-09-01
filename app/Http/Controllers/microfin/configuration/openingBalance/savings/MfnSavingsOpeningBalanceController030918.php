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
			$openingInfos = DB::table('mfn_opening_savings_account_info as t1')
									->join('mfn_savings_account as t2','t1.savingsAccIdFk','t2.id')
									->select('t1.*','t2.savingsCode','t2.memberIdFk','t2.branchIdFk','t2.samityIdFk')
									->orderBy('t2.memberIdFk')
									->get();

			$userBranchId = Auth::user()->branchId;
			
			if ($userBranchId!=1){
				$openingInfos = $openingInfos->where('branchIdFk',$userBranchId);
			}
			elseif($req->filBranch!='-1'){
				$openingInfos = $openingInfos->where('branchIdFk',$req->filBranch);
			}

			if ($req->filSamity!='-1'){
				$openingInfos = $openingInfos->where('samityIdFk',$req->filSamity);
			}
			

			$members = DB::table('mfn_member_information')->whereIn('id',$openingInfos->pluck('memberIdFk'))->select('id','name','code')->get();

			$data = array(
				'openingInfos'	=> $openingInfos,
				'members'		=> $members
			);
			return view('microfin.configuration.openingBalance.savings.OpeningSavingsBalance.viewSavingsBalance', $data); 
		}

		public function storeOpeningData(Request $req){
			
			foreach ($req->accId as $key => $accId) {
				$savingsInfo = MfnOpeningSavingsAccountInfo::where('savingsAccIdFk',$accId)->first();
				$savingsInfo->openingPrincipal 	= floatval(str_replace(',', '', $req->deposit[$key]));
				$savingsInfo->openingInterest 	= floatval(str_replace(',', '', $req->interest[$key]));
				$savingsInfo->openingWithdraw 	= floatval(str_replace(',', '', $req->withdraw[$key]));
				$savingsInfo->openingBalance 	= floatval(str_replace(',', '', $req->balance[$key]));
				$savingsInfo->save();
			}

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data inserted successfully.'
            );

            return response::json($data);                

		}
	}