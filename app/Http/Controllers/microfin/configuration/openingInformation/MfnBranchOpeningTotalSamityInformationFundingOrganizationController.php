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

	class MfnBranchOpeningTotalSamityInformationFundingOrganizationController extends Controller {

		public function indexList() {
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$categoryListList = [''=>'Select'] + MicroFin::getAllProductCategoryList();

			if ($userBranchId != 1) {
				$fundingOrgInfos = DB::table('mfn_opening_info_samity_total')
					->where('branchIdFk', $userBranchId)
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}
			else {
				$fundingOrgInfos = DB::table('mfn_opening_info_samity_total')
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}
			
			$searchBranch = ''; // AT THE TIME OF LOADING PAGE, THERE IS NO SEARCH EXIST

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'categoryListList'		=> $categoryListList,
				'fundingOrgInfos' 	    => $fundingOrgInfos,
				'searchBranch'          => $searchBranch
			);

			// dd($data);

			return view('microfin/configuration/openingInformation/openingBranchFundingOrganizationSamityInformation/dataList',$data);
		}

		public function getSearchData (Request $req) {
			// dd($req);
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$categoryListList = [''=>'Select'] + MicroFin::getAllProductCategoryList();

			$searchBranch = $req->filBranch;

			if ($userBranchId != 1) {
				$fundingOrgInfos = DB::table('mfn_opening_info_samity_total')
					->where('branchIdFk', $userBranchId)
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}
			else {
				$fundingOrgInfos = DB::table('mfn_opening_info_samity_total')
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'categoryListList'		=> $categoryListList,
				'fundingOrgInfos' 	    => $fundingOrgInfos,
				'searchBranch'          => $searchBranch
			);
			
			return view('microfin/configuration/openingInformation/openingBranchFundingOrganizationSamityInformation/dataList',$data);
		}

		public function index() {
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$funOrgListList = [''=>'Select'] + MicroFin::getFundingOrgList();
			$serviceChargeOption = [''=>'Select','1'=>'With Service Charge','2'=>'Without Service Charge'];

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'funOrgListList'		=> $funOrgListList,
				'serviceChargeOption'	=> $serviceChargeOption,
			);

			return view('microfin/configuration/openingInformation/openingBranchFundingOrganizationSamityInformation/dataAdd',$data);
		}

		public function dataDetails ($id) {
			$userBranchId = Auth::user()->branchId;
			$branchName = '';

			$distinctBranchInfo = DB::table('mfn_opening_info_samity_total')
				->where('id', decrypt($id))
				->first();

			if ($userBranchId == 1) {
				$branchName = DB::table('gnr_branch')
					->where('id', $distinctBranchInfo->branchIdFk)
					->pluck('name')
					->toArray();
				
				$fundingOrgName = DB::table('mfn_funding_organization')
					->where('id', $distinctBranchInfo->fundingOrgIdFk)
					->pluck('name')
					->toArray();
			}
			else {
				$fundingOrgName = DB::table('mfn_funding_organization')
					->where('id', $distinctBranchInfo->fundingOrgIdFk)
					->pluck('name')
					->toArray();
			}

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchName'			=> $branchName,
				'fundingOrgName'		=> $fundingOrgName,
				'distinctBranchInfo'    => $distinctBranchInfo
			);

			// dd($data);

			return view('microfin/configuration/openingInformation/openingBranchFundingOrganizationSamityInformation/dataDetails',$data);
		}

		public function editData ($id) {
			// dd(decrypt($id));
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$fundingOrgList = [''=>'Select'] + DB::table('mfn_funding_organization')
				->pluck('name', 'id')
				->toArray();
			$branchName = '';

			$distinctBranchInfo = DB::table('mfn_opening_info_samity_total')
				->where('id', decrypt($id))
				->first();

			$branchName    = $distinctBranchInfo->branchIdFk;
			$fundingOrgName  = $distinctBranchInfo->fundingOrgIdFk;

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'fundingOrgList'		=> $fundingOrgList,
				'branchName'			=> $branchName,
				'fundingOrgName'		=> $fundingOrgName,
				'distinctBranchInfo'    => $distinctBranchInfo
			);

			// dd($data);

			return view('microfin/configuration/openingInformation/openingBranchFundingOrganizationSamityInformation/dataEdit',$data);
		}

		public function updateData(Request $req){
			// dd($req);

			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$isExits = (int) DB::table('mfn_opening_info_samity_total')->where('branchIdFk',$targetBranchId)
				->where('fundingOrgIdFk',$req->fundingOrg)
				->where('id', '!=', $req->tableId)
				->value('id');

			if ($isExits != $req->dataId) {
				if ($isExits > 0) {
					// Give Error Message... 
					$notification = array(
						'message' => 'Data not updated!',
						'alert-type' => 'warning'
					);

					return redirect()->back()->with($notification);
				}
			}

			// $requestData = $req->all();
			// unset($requestData['_token']);
			// unset($requestData['branch']);
			// unset($requestData['category']);
			// unset($requestData['fundingOrg']);

			// dd($requestData);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['fundingOrgIdFk'] = $req->fundingOrg;
			$requestData['maleSamityNo'] = $req->maleSamityNo;
			$requestData['thisMonthNewMaleSamityNo'] = $req->thisMonthNewMaleSamityNo;
			$requestData['thisMonthCloseMaleSamityNo'] = $req->thisMonthCloseMaleSamityNo;
			$requestData['femaleSamityNo'] = $req->femaleSamityNo;
			$requestData['thisMonthNewFemaleSamityNo'] = $req->thisMonthNewFemaleSamityNo;
			$requestData['thisMonthCloseFemaleSamityNo'] = $req->thisMonthCloseFemaleSamityNo; 

			// $requestData['date'] = $softwareStartDate;
			// $requestData['branchIdFk'] = $targetBranchId;
			// $requestData['fundingOrgIdFk'] = $req->fundingOrg;

			// dd($requestData);

			$SuccessQuery = DB::table('mfn_opening_info_samity_total')
					->where([
						'branchIdFk'		=> $targetBranchId,
						'fundingOrgIdFk' 		=> $req->fundingOrg
					])
					->update($requestData);
			
			if ($SuccessQuery == true) {
				// $Success = 'True';
				$notification = array(
					'message' => 'You have successfully updated the information!',
					'alert-type' => 'success'
				);
			}
			else {
				$notification = array(
					'message' => 'Data not updated!',
					'alert-type' => 'warning'
				);
			}

			return redirect()->back()->with($notification);

			// return response::json($data);
		}

		public function deleteData (Request $req) {
			// dd($req->id);
			$deleteData = DB::table('mfn_opening_info_samity_total')
				->where('id', $req->id)
				->delete();

			if ($deleteData == 1) {
				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Data deleted successfully.'
				);
			}
			else {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Data not deleted.'
				);
			}

			return response::json($data);
		}

		public function checkIsExits(Request $req){
			
			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$isExits = (int) DB::table('mfn_opening_info_samity_total')->where('branchIdFk',$targetBranchId)->where('fundingOrgIdFk',$req->funOrg)->value('id');


			if ($isExits) {
				return response::json('data alreay exits');
			}
			else{
				return $this->storeBranchOpeningTotalSamityInfo($targetBranchId,$req);
			}
			
		}

		public function storeBranchOpeningTotalSamityInfo($targetBranchId,$req){

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['funOrg']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['fundingOrgIdFk'] = $req->funOrg;
			$requestData['categoryIdFk'] = 0;
			$requestData['createdAt'] = Carbon::now();

			DB::table('mfn_opening_info_samity_total')->insert($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data stored successfully.'
            );

			return response::json($data);
		}

		public function updateBranchOpeningTotalSamityInfo(Request $req){

			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['funOrg']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['fundingOrgIdFk'] = $req->funOrg;

			DB::table('mfn_opening_info_samity_total')
					->where([
						'branchIdFk'		=> $targetBranchId,
						'fundingOrgIdFk' 	=> $req->funOrg
					])
					->update($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
            );

			return response::json($data);
		}

		// Product Category wise information

		public function indexCategory(){
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$categoryListList = [''=>'Select'] + MicroFin::getAllProductCategoryList();

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'categoryListList'		=> $categoryListList
			);

			return view('microfin/configuration/openingInformation/openingBranchCategorySamityInformation',$data);
		}

		public function checkIsExitsCategory(Request $req){
			
			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$isExits = (int) DB::table('mfn_opening_info_samity_total')->where('branchIdFk',$targetBranchId)->where('categoryIdFk',$req->category)->value('id');


			if ($isExits) {
				return response::json('data alreay exits');
			}
			else{
				return $this->storeBranchOpeningCategorySamityInfo($targetBranchId,$req);
			}
			
		}

		public function storeBranchOpeningCategorySamityInfo($targetBranchId,$req){

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['category']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['fundingOrgIdFk'] = 0;
			$requestData['categoryIdFk'] = $req->category;
			$requestData['createdAt'] = Carbon::now();

			DB::table('mfn_opening_info_samity_total')->insert($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data stored successfully.'
            );

			return response::json($data);
		}

		public function updateBranchOpeningCategorySamityInfo(Request $req){

			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['category']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['categoryIdFk'] = $req->category;

			DB::table('mfn_opening_info_samity_total')
					->where([
						'branchIdFk'		=> $targetBranchId,
						'categoryIdFk' 		=> $req->category
					])
					->update($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
            );

			return response::json($data);
		}

	}