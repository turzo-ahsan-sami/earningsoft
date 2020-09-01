<?php

	namespace App\Http\Controllers\microfin\configuration;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\gnr\GnrBranch;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnBranchProductController extends Controller {

		protected $MicroFinance;

		private $TCN;

		public function __construct() {

			$this->MicroFinance = new MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Branch', 150),
				array('Code', 70),
				array('Loan Products', 0),
				array('Savings Products', 0),
				array('Action', 80)
			);	
		}
	
		public function index() {

			$TCN = $this->TCN;
		
			$damageData = array(
				'TCN' 	   			=>	$TCN,
				'branches'  		=>  $this->MicroFinance->getActiveBranchForBranchProductAssign(),
				'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable()
			);

			return view('microfin.configuration.branchProduct.viewBranchProduct', ['damageData' => $damageData]);
		}

		public function addBranchProduct() {

            $damageData = array(
				'branches'  =>  $this->MicroFinance->getBranchOptionsForBranchProductAssign(1)
			);

			return view('microfin.configuration.branchProduct.addBranchProduct', ['damageData' => $damageData]);
		}

		public function addItem(Request $req) {

			$rules = array(
				'branchId'  =>	'required',
			);

			$attributesNames = array(
				'branchId'  =>	'branch name',
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				//	UPDATE LOAN PRODUCT ID AND SAVINGS PRODUCT ID IN gnr_branch TABLE.
				$branch = GnrBranch::find($req->branchId);
				$branch->loanProductId = $req->loanProductId;
				$branch->savingsProductId = $req->savingsProductId;
				$branch->save(); 

				$data = array(
					'responseTitle' =>	'Success!',
					'responseText'  =>  'Loan and savings products have been assigned to branch successfully.'		
				);

				return response::json($data);
			}
		}

		public function loadBranchProductEditSupportData(Request $req) {

			$branch = DB::table('gnr_branch')->where('id', $req->id)->pluck('name', 'id')->all();

			$branchProductOB = DB::table('gnr_branch')->where('id', $req->id)->select('loanProductId', 'savingsProductId')->first();

			$data = array(
				'branchProductData'  =>	 $branchProductOB,
				'branchOB'			 =>  $branch
			);

			return response::json($data);
		}

		public function updateItem(Request $req) {

			$rules = array(
				'branchId'  =>  'required'
			);

			$attributesNames = array(
				'branchId'  =>  'branch name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				//	UPDATE LOAN PRODUCT ID AND SAVINGS PRODUCT ID IN gnr_branch TABLE.

				$branch 			= GnrBranch::find($req->branchId);
				$previousLoanArray 	= array_diff($branch->loanProductId,$req->loanProductId);

				if(!empty($previousLoanArray)){
					
					$existingLoan = DB::table('mfn_loan')->whereIn('loanTypeId', $previousLoanArray)->where('branchIdFk', $req->branchId)->where('softDel', 0)->count();

					$existingMember = DB::table('mfn_member_information')->whereIn('primaryProductId', $previousLoanArray)->where('branchId', $req->branchId)->where('softDel', 0)->count();

					/*$existingProductTransfer = DB::table('mfn_loan_primary_product_transfer')->whereIn('oldPrimaryProductFk',$previousLoanArray)->whereIn(' newPrimaryProductFk', $previousLoanArray)->where('branchIdFk', $req->branchId)->where('softDel', 0)->count();*/

					//$branchId = $req->branchId;
					
					$existingProductTransfer = DB::table('mfn_loan_primary_product_transfer')
					->where('softDel',0)
					->where('branchIdFk',$req->branchId)
					->where(function ($query) use ($previousLoanArray) {
					    $query->whereIn('newPrimaryProductFk',$previousLoanArray)
					          ->orWhereIn('oldPrimaryProductFk',$previousLoanArray);
					})->count();


					//dd($req->branchId,$existingProductTransfer);

					if(($existingLoan > 0) || ($existingMember > 0) ||( $existingProductTransfer > 0)){
						$data = array(
								'responseTitle' =>	'Warning!',
								'responseText'  =>  'Loan and Member under the unchecked loan product exists.'	
								);

						return response::json($data);

					}
				}

				$branch->loanProductId = $req->loanProductId;
				$branch->savingsProductId = $req->savingsProductId;
				$branch->save(); 

				$data = array(
					'responseTitle' =>	'Success!',
					'responseText'  =>  'Loan and savings products have been updated successfully.'		
				);

				return response::json($data);
			}
		}
	}