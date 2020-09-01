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

	class MfnBranchOpeningSavingsInformationController extends Controller {

		public function index(Request $req){
			// dd($req);
			$userBranchId = Auth::user()->branchId;
			$targetBranchId = null;
			$branchInfos = '';

			if (($userBranchId==1 && $req->filBranch!='')) {
				$targetBranchId = $req->filBranch;

				$branchInfos = DB::table('mfn_opening_info_savings')
					->where('branchIdFk', $targetBranchId)
					->groupBy('branchIdFk')
					->pluck('branchIdFk')
					->toArray();
			}
			elseif ($userBranchId==1) {
				$targetBranchId = $userBranchId;

				$branchInfos = DB::table('mfn_opening_info_savings')
						->groupBy('branchIdFk')
						->pluck('branchIdFk')
						->toArray();
			}
			else{
				$targetBranchId = $userBranchId;

				$branchInfos = DB::table('mfn_opening_info_savings')
						->where('branchIdFk', $userBranchId)
						->groupBy('branchIdFk')
						->pluck('branchIdFk')
						->toArray();
			}

		    $branchList = MicroFin::getBranchList();

		    $data = array(
		    	'userBranchId'	=> $userBranchId,
		    	'branchInfos'	=> $branchInfos,
		    	'branchList'	=> $branchList
			);

			// dd($data, $userBranchId, $branchInfos, $req->filBranch);

		    return view('microfin/configuration/openingInformation/openingBranchSavingsInformation/dataList',$data);
		}

		public function detailsData ($id) {
			$userBranchId = Auth::user()->branchId;

			list($branchId, $loanProductId, $savingsProductId, $gender) = explode('-', decrypt($id));

			// dd(decrypt($id), $branchId, $loanProductId, $savingsProductId, $gender);

			$getInfo = DB::table('mfn_opening_info_savings')
				->where([['branchIdFk', $branchId], ['productIdFk', $loanProductId], ['savingProductIdFk', $savingsProductId], ['genderTypeId', $gender]])
				->first();

			// dd($getInfo);

			return view('microfin/configuration/openingInformation/openingBranchSavingsInformation/dataDetails', compact('getInfo', 'userBranchId'));
		}

		public function editData ($id) {
			$userBranchId = Auth::user()->branchId;

			list($branchId, $loanProductId, $savingsProductId, $gender) = explode('-', decrypt($id));

			// dd(decrypt($id), $branchId, $loanProductId, $savingsProductId, $gender);

			$getInfo = DB::table('mfn_opening_info_savings')
				->where([['branchIdFk', $branchId], ['productIdFk', $loanProductId], ['savingProductIdFk', $savingsProductId], ['genderTypeId', $gender]])
				->first();

			// dd($getInfo);

			if ($userBranchId!=1) {
				$loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$userBranchId)->value('loanProductId')));

				$primaryProductList = [''=>'Select'] + DB::table('mfn_loans_product')
        				->whereIn('id',$loanProductIds)
        				->select(DB::raw("CONCAT(code,' - ',shortName) AS name"),'id')
        				->orderBy('code')
        				->pluck('name','id')
        				->all();
			}
			else{
				$primaryProductList = [''=>'Select'] + MicroFin::getAllLoanProductList();
			}

			$savingsProductList = [''=>'Select'] + MicroFin::getAllSavingsProductList();
			$genderList = [''=>'Select','1'=>'Male','2'=>'Female'];
			$serviceChargeOption = [''=>'Select','1'=>'With Service Charge','2'=>'Without Service Charge'];

			$data = array(
				'userBranchId'			=> $userBranchId,
				'primaryProductList'	=> $primaryProductList,
				'savingsProductList'	=> $savingsProductList,
				'genderList'			=> $genderList,
				'serviceChargeOption'	=> $serviceChargeOption,
				'getInfo'               => $getInfo
			);

			return view('microfin/configuration/openingInformation/openingBranchSavingsInformation/dataUpdate', $data);
		}

		public function updateData (Request $req) {
			// dd($req);

			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$isExits = (int) DB::table('mfn_opening_info_savings')->where('branchIdFk',$targetBranchId)
				->where('productIdFk',$req->primaryProduct)
				->where('savingProductIdFk',$req->savingsProduct)
				->where('genderTypeId',$req->gender)
				->where('id', '!=', $req->dataId)
				->value('id');

			// dd($isExits, $req->dataId);

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
			// $newClosingBalance = explode(',')

			// dd($req->closingBalance,  $req->depositCollection);
			$updateData = DB::table('mfn_opening_info_savings') 
				->where([['id', $req->dataId], ['branchIdFk', $targetBranchId]])
				->update(
					[
						'productIdFk' => $req->primaryProduct,
						'savingProductIdFk' => $req->savingsProduct,
						'genderTypeId' => $req->gender,
						'depositCollection' => $req->depositCollection,
						'thisMonthDepositCollection' => $req->thisMonthDepositCollection,
						'interestAmount' => $req->interestAmount,
						'thisMonthInterestAmount' => $req->thisMonthInterestAmount,
						'savingRefund' => $req->savingRefund,
						'thisMontSavingRefund' => $req->thisMontSavingRefund,
						'closingBalance' => $req->closingBalance
					]
				);

			

			if ($updateData == 1) {
				// $Success = 'True';
				// dd($isExits, $updateData, $req->dataId);
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
		}

		public function addData(){
			$userBranchId = Auth::user()->branchId;
			$branchList = [''=>'Select'] + MicroFin::getBranchList();

			if ($userBranchId!=1) {
				$loanProductIds = explode(',',str_replace(['[',']','"'],'',DB::table('gnr_branch')->where('id',$userBranchId)->value('loanProductId')));

				$primaryProductList = [''=>'Select'] + DB::table('mfn_loans_product')
        				->whereIn('id',$loanProductIds)
        				->select(DB::raw("CONCAT(code,' - ',shortName) AS name"),'id')
        				->orderBy('code')
        				->pluck('name','id')
        				->all();
			}
			else{
				$primaryProductList = [''=>'Select'] + MicroFin::getAllLoanProductList();
			}

			$savingsProductList = [''=>'Select'] + MicroFin::getAllSavingsProductList();
			$genderList = [''=>'Select','1'=>'Male','2'=>'Female'];
			$serviceChargeOption = [''=>'Select','1'=>'With Service Charge','2'=>'Without Service Charge'];

			$data = array(
				'userBranchId'			=> $userBranchId,
				'branchList'			=> $branchList,
				'primaryProductList'	=> $primaryProductList,
				'savingsProductList'	=> $savingsProductList,
				'genderList'			=> $genderList,
				'serviceChargeOption'	=> $serviceChargeOption,
			);

			// return view('microfin/configuration/openingInformation/openingBranchSavingsInformation',$data);
			return view('microfin/configuration/openingInformation/openingBranchSavingsInformation/dataAdd',$data);
		}

		public function deleteData (Request $req) {
			// dd($req->id);

			list($branchId, $loanProductId, $savingsProductId, $gender) = explode('-', $req->id);

			// dd($branchId, $loanProductId, $savingsProductId, $gender);

			$deleteData = DB::table('mfn_opening_info_savings')
				->where([['branchIdFk', $branchId], ['productIdFk', $loanProductId], ['savingProductIdFk', $savingsProductId], ['genderTypeId', $gender]])
				->delete();

			// dd($deleteData);

			if ($deleteData == 1) {
				$data = array(
					'responseTitle' =>  'Success!',
					'responseText'  =>  'Data deleted successfully.'
				);
			}
			else {
				$data = array(
					'responseTitle' =>  'Warning!',
					'responseText'  =>  'Data is not deleted successfully.'
				);
			}

			return response::json($data);
		}

		public function checkIsExits(Request $req){
			// dd($req);
			$userBranchId = Auth::user()->branchId;

			if ($userBranchId==1) {
				$targetBranchId = $req->branch;
			}
			else{
				$targetBranchId = $userBranchId;
			}

			$isExits = (int) DB::table('mfn_opening_info_savings')->where('branchIdFk',$targetBranchId)->where('productIdFk',$req->primaryProduct)->where('savingProductIdFk',$req->savingsProduct)->where('genderTypeId',$req->gender)->value('id');


			if ($isExits) {
				return response::json('data alreay exits');
			}
			else{
				return $this->storeBranchOpeningSavingsInfo($targetBranchId,$req);
			}
			
		}

		public function storeBranchOpeningSavingsInfo($targetBranchId,$req){

			$softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');

			$requestData = $req->all();
			unset($requestData['_token']);
			unset($requestData['branch']);
			unset($requestData['primaryProduct']);
			unset($requestData['savingsProduct']);
			unset($requestData['gender']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['productIdFk'] = $req->primaryProduct;
			$requestData['savingProductIdFk'] = $req->savingsProduct;
			$requestData['genderTypeId'] = $req->gender;
			$requestData['createdAt'] = Carbon::now();

			$requestData['closingBalance'] = floatval(str_replace(',', '', $req->closingBalance));


			DB::table('mfn_opening_info_savings')->insert($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data stored successfully.'
            );

			return response::json($data);
		}

		public function updateBranchOpeningSavingsInfo(Request $req){

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
			unset($requestData['primaryProduct']);
			unset($requestData['savingsProduct']);
			unset($requestData['gender']);

			$requestData['date'] = $softwareStartDate;
			$requestData['branchIdFk'] = $targetBranchId;
			$requestData['productIdFk'] = $req->primaryProduct;
			$requestData['savingProductIdFk'] = $req->savingsProduct;
			$requestData['genderTypeId'] = $req->gender;

			$requestData['closingBalance'] = floatval(str_replace(',', '', $req->closingBalance));

			DB::table('mfn_opening_info_savings')
					->where([
						'branchIdFk'		=> $targetBranchId,
						'productIdFk' 		=> $req->primaryProduct,
						'savingProductIdFk' => $req->savingsProduct,
						'genderTypeId' 		=> $req->gender
					])
					->update($requestData);

			$data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
            );

			return response::json($data);
		}
	}