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

	class MfnBranchOpeningTotalSamityInformationController extends Controller {

		public function index(){
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$funOrgListList = [''=>'Select'] + MicroFin::getFundingOrgList();
			$serviceChargeOption = [''=>'Select','1'=>'With Service Charge','2'=>'Without Service Charge'];
			$categoryListName = [''=>'Select'] + DB::table('mfn_loans_product_category')
				->pluck('name', 'id')
				->toArray();

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'funOrgListList'		=> $funOrgListList,
				'serviceChargeOption'	=> $serviceChargeOption,
				'categoryListName'      => $categoryListName,
				'funOrgListList'		=> $funOrgListList
			);

			// dd($data, $categoryList);

			return view('microfin/configuration/openingInformation/openingBranchCategorySamityInformation/dataAdd',$data);
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

			if ($userBranchId != 1) {
				$ProductCategoryInfos = DB::table('mfn_opening_info_samity_total')
					->where('branchIdFk', $userBranchId)
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}
			else {
				$ProductCategoryInfos = DB::table('mfn_opening_info_samity_total')
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}
			
			$searchBranch = ''; // AT THE TIME OF LOADING PAGE, THERE IS NO SEARCH EXIST

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'categoryListList'		=> $categoryListList,
				'productCategoryInfos'  => $ProductCategoryInfos,
				'searchBranch'          => $searchBranch
			);

			// dd($data);

			return view('microfin/configuration/openingInformation/openingBranchCategorySamityInformation/dataList',$data);
		}

		public function getSearchData (Request $req) {
			// dd($req);
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$categoryListList = [''=>'Select'] + MicroFin::getAllProductCategoryList();

			if ($userBranchId != 1) {
				$ProductCategoryInfos = DB::table('mfn_opening_info_samity_total')
					->where('branchIdFk', $userBranchId)
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}
			else {
				$ProductCategoryInfos = DB::table('mfn_opening_info_samity_total')
					->select('id', 'branchIdFk', 'maleSamityNo', 'femaleSamityNo', 'categoryIdFk', 'fundingOrgIdFk')
					->get();
			}

			$searchBranch = $req->filBranch;

			// AT THE TIME OF SEARCH WE NEED PAGE LOADING INFORMATION BEACAUSE OF THE CAMPARISON HAS DONE IN THE VIEW PAGE
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$funOrgListList = [''=>'Select'] + MicroFin::getFundingOrgList();
			$serviceChargeOption = [''=>'Select','1'=>'With Service Charge','2'=>'Without Service Charge'];
			$categoryListName = [''=>'Select'] + DB::table('mfn_loans_product_category')
				->pluck('name', 'id')
				->toArray();

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'categoryListList'		=> $categoryListList,
				'productCategoryInfos'  => $ProductCategoryInfos,
				'searchBranch'          => $searchBranch
			);

			return view('microfin/configuration/openingInformation/openingBranchCategorySamityInformation/dataList',$data);
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
				
				$categoryName = DB::table('mfn_loans_product_category')
					->where('id', $distinctBranchInfo->categoryIdFk)
					->pluck('name')
					->toArray();
			}
			else {
				$categoryName = DB::table('mfn_loans_product_category')
					->where('id', $distinctBranchInfo->categoryIdFk)
					->pluck('name')
					->toArray();
			}

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchName'			=> $branchName,
				'categoryName'		    => $categoryName,
				'distinctBranchInfo'    => $distinctBranchInfo
			);

			return view('microfin/configuration/openingInformation/openingBranchCategorySamityInformation/dataDetails',$data);
		}

		public function checkIsExitsCategory(Request $req){
			
			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$isExits = (int) DB::table('mfn_opening_info_samity_total')->where('branchIdFk',$targetBranchId)->where([['categoryIdFk',$req->category], ['fundingOrgIdFk',$req->fundingOrganization]])->value('id');

			// $isExitsFOG = (int) DB::table('mfn_opening_info_samity_total')->where('branchIdFk',$targetBranchId)->where('fundingOrgIdFk',$req->fundingOrganization)->value('id');
			// dd($req, $isExits);

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
			unset($requestData['fundingOrganization']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['fundingOrgIdFk'] = $req->fundingOrganization;
			$requestData['categoryIdFk'] = $req->category;
			$requestData['createdAt'] = Carbon::now();

			DB::table('mfn_opening_info_samity_total')->insert($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data stored successfully.'
            );

			return response::json($data);
		}

		public function editData($id) {
			// dd(decrypt($id));

			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();
			$categoryList = [''=>'Select'] + MicroFin::getAllProductCategoryList();
			$funOrgListList = [''=>'Select'] + MicroFin::getFundingOrgList();
			$branchName = '';

			$distinctBranchInfo = DB::table('mfn_opening_info_samity_total')
				->where('id', decrypt($id))
				->first();

			$branchName    = $distinctBranchInfo->branchIdFk;
			$categoryName  = $distinctBranchInfo->categoryIdFk;
			$fundingOrganozationName  = $distinctBranchInfo->fundingOrgIdFk;

			$data = array(
				'userBranchId'			  => $userBranchId,
				'branchList'			  => $branchList,
				'categoryList'		      => $categoryList,
				'branchName'			  => $branchName,
				'categoryName'		      => $categoryName,
				'distinctBranchInfo'      => $distinctBranchInfo,
				'fundingOrganozationName' => $fundingOrganozationName,
				'funOrgListList'		  => $funOrgListList
			);

			// dd($data);

			return view('microfin/configuration/openingInformation/openingBranchCategorySamityInformation/dataEdit',$data);
		}

		public function updateBranchOpeningCategorySamityInfo(Request $req){
			// dd($req, $req->dataId);
			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$isExits = (int) DB::table('mfn_opening_info_samity_total')->where('branchIdFk',$targetBranchId)
				->where('categoryIdFk',$req->category)
				->where('id', '!=',  $req->tableId)
				->value('id');
			
			if ($isExits !=  $req->tableId) {
				if ($isExits > 0) {
					// Give Error Message... 
					$notification = array(
						'message' => 'Data not updated!',
						'alert-type' => 'warning'
					);

					return redirect()->back()->with($notification);
				}
			}

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['category']);
			unset($requestData['tableId']);
			unset($requestData['fundingOrganization']);

			$requestData['id'] = $req->tableId;
			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['categoryIdFk'] = $req->category;
			$requestData['fundingOrgIdFk'] = $req->fundingOrganization;

			// dd($requestData);

			$SuccessQuery = DB::table('mfn_opening_info_samity_total')
					->where([
						'branchIdFk'		=> $targetBranchId,
						'categoryIdFk' 		=> $req->category
					])
					->update($requestData);

			// $data = array(
            //     'responseTitle' =>  'Success!',
            //     'responseText'  =>  'Data updated successfully.'
			// );
			
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

		public function dataDelete (Request $req) {

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

	}