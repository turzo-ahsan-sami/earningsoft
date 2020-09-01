<?php

	namespace App\Http\Controllers\microfin\configuration\openingInformation;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFin;

	class MfnBranchOpeningMemberSamityInformationController extends Controller {

		public function index(Request $req){

			$userBranchId = Auth::user()->branchId;
			$targetBranchId = null;

			if ($userBranchId==1 && isset($req->filBranch)) {
				if ($req->filBranch!='') {
					$targetBranchId = $req->filBranch;
				}
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$openingData = DB::table('mfn_opening_info_member_samity')
									->where('branchIdFk',$targetBranchId)
									->groupBy('productIdFk')
									->select('id','productIdFk')
									->get();

			$loanProductIds = $openingData->unique('productIdFk')->pluck('productIdFk')->toArray();

			$loanProducts = DB::table('mfn_loans_product')
		        				->whereIn('id',$loanProductIds)
		        				->select(DB::raw("CONCAT(code,' - ',shortName) AS name"),'id')
		        				->orderBy('code')
		        				->select('name','id')
		        				->get();

		    $branchList = MicroFin::getBranchList();

		    $data = array(
		    	'userBranchId'	=> $userBranchId,
		    	'openingData'	=> $openingData,
		    	'loanProducts'	=> $loanProducts,
		    	'branchList'	=> $branchList
		    );

		    return view('microfin/configuration/openingInformation/openingBranchMemberSamityInformation/dataList',$data);

		}

		public function viewDetails($id){

			$userBranchId = Auth::user()->branchId;

			$openingData = DB::table('mfn_opening_info_member_samity')->where('id',decrypt($id))->first();

			$branch = DB::table('gnr_branch')
								->where('id', $openingData->branchIdFk)
								->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS name"))
								->first();

			$loanProduct = DB::table('mfn_loans_product')
		        				->where('id',$openingData->productIdFk)
		        				->select(DB::raw("CONCAT(code,' - ',shortName) AS name"),'id')
		        				->first();

			$data = array(
				'userBranchId'	=> $userBranchId,
				'openingData'	=> $openingData,
				'branch'		=> $branch,
				'loanProduct'	=> $loanProduct
			);

			return view('microfin/configuration/openingInformation/openingBranchMemberSamityInformation/dataDetails',$data);
		}

		public function addData(){
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			if ($userBranchId!=1) {
				$loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$userBranchId)->value('loanProductId')));

				$primaryProductList = DB::table('mfn_loans_product')
        				->whereIn('id',$loanProductIds)
        				->select(DB::raw("CONCAT(code,' - ',shortName) AS name"),'id')
        				->orderBy('code')
        				->pluck('name','id')
        				->all();
			}

			else{
				$primaryProductList = [''=>'Select'] + MicroFin::getAllLoanProductList();
			}
			$serviceChargeOption = [''=>'Select','1'=>'With Service Charge','2'=>'Without Service Charge'];

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'primaryProductList'	=> $primaryProductList,
				'serviceChargeOption'	=> $serviceChargeOption,
			);

			return view('microfin/configuration/openingInformation/openingBranchMemberSamityInformation/dataAdd',$data);
		}

		public function editData($id){

			$userBranchId = Auth::user()->branchId;

			$openingData = DB::table('mfn_opening_info_member_samity')->where('id',decrypt($id))->first();
			
			$loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$openingData->branchIdFk)->value('loanProductId')));

			$primaryProductList = DB::table('mfn_loans_product')
    				->whereIn('id',$loanProductIds)
    				->select(DB::raw("CONCAT(code,' - ',shortName) AS name"),'id')
    				->orderBy('code')
    				->pluck('name','id')
    				->all();

			$loanProduct = DB::table('mfn_loans_product')
		        				->where('id',$openingData->productIdFk)
		        				->select(DB::raw("CONCAT(code,' - ',shortName) AS name"),'id')
		        				->first();

		    $branchList = MicroFin::getBranchList();

			$data = array(
				'userBranchId'			=> $userBranchId,
				'openingData'			=> $openingData,
				'loanProduct'			=> $loanProduct,
				'primaryProductList'	=> $primaryProductList,
				'branchList'			=> $branchList
			);

			return view('microfin/configuration/openingInformation/openingBranchMemberSamityInformation/dataEdit',$data);
		}

		public function checkIsExits(Request $req){
			
			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$isExits = (int) DB::table('mfn_opening_info_member_samity')->where('branchIdFk',$targetBranchId)->where('productIdFk',$req->primaryProduct)->value('id');


			if ($isExits) {
				return response::json('data alreay exits');
			}
			else{
				return $this->storeBranchOpeningMemberSamityInfo($targetBranchId,$req);
			}
			
		}

		public function storeBranchOpeningMemberSamityInfo($targetBranchId,$req){

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['primaryProduct']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['productIdFk'] = $req->primaryProduct;
			$requestData['fundingOrgIdFk'] = DB::table('mfn_loans_product')->where('id',$req->primaryProduct)->value('fundingOrganizationId');
			$requestData['createdAt'] = Carbon::now();

			DB::table('mfn_opening_info_member_samity')->insert($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data stored successfully.'
            );

			return response::json($data);
		}

		public function updateBranchOpeningMemberSamityInfo(Request $req){

			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$dataId = decrypt($req->dataId);

			$isExits = (int) DB::table('mfn_opening_info_member_samity')->where('id','!=',$dataId)->where('branchIdFk',$targetBranchId)->where('productIdFk',$req->primaryProduct)->value('id');


			if ($isExits>0) {
				return response::json('data alreay exits');
			}
			

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['primaryProduct']);
			unset($requestData['dataId']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['productIdFk'] = $req->primaryProduct;

			DB::table('mfn_opening_info_member_samity')
					->where([
						'id'		=> $dataId
					])
					->update($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
            );

			return response::json($data);
		}

		public function deleteData(Request $req){
			$dataId = decrypt($req->id);

			DB::table('mfn_opening_info_member_samity')
					->where('id', $dataId)
					->delete();

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data deleted successfully.'
            );

			return response::json($data);
		}
	}