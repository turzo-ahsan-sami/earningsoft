<?php

	namespace App\Http\Controllers\microfin\loan;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\loan\MfnProductCategory;
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

	class MfnProductCategoryController extends Controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Name', 200),
				array('Short Name', 150),
				array('Is Savings Frequency Override', 0),
				array('Savings Deposit Frequency', 0),
				array('No of Primary Loan Product', 0),
				array('No of Other Loan Product', 0),
				array('Action', 80)
			);
		}

		public function index() {

			$damageData = array(
				'TCN'	                   =>  $this->TCN,
				'productCategory'          =>  $this->MicroFinance->getActiveProductCategory(),
				'boolean'                  =>  $this->MicroFinance->getBooleanOptions(),
				'savingsDepositFrequency'  =>  $this->MicroFinance->getSavingsDepositFrequency(),
				'monthlyCollectionWeek'    =>  $this->MicroFinance->getMonthlyCollectionWeek(),
				'categoryType'             =>  $this->MicroFinance->getCategoryTypeList()
			);

			return view('microfin.loan.productCategory.viewProductCategory', ['damageData' => $damageData]);
		}

		public function addLoanProductCategory() {

			$damageData = array(
				'boolean'                  =>  $this->MicroFinance->getBooleanOptions(),
				'savingsDepositFrequency'  =>  $this->MicroFinance->getSavingsDepositFrequency(),
				'monthlyCollectionWeek'    =>  $this->MicroFinance->getMonthlyCollectionWeek(),
				'categoryType'             =>  $this->MicroFinance->getCategoryTypeList()
			);

			return view('microfin.loan.productCategory.addProductCategory', ['damageData' => $damageData]);
		}

		/**
		 * [Insert Loan Product Category Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'name'	  							=>  'required|unique:mfn_loans_product_category,name',
				'shortName' 						=>  'required|unique:mfn_loans_product_category,shortName',
				'overrideSavingsDepositeFrequency'  =>  'required',
				'categoryTypeId' 					=>  'required'
			);

			$attributesNames = array(
				'name'	 									   =>  'product category name',
				'overrideSavingsDepositeFrequency'	  		   =>  'product category name',
				'overrideSavingsDepositeFrequencyForCategory'  =>  'Override Savings Deposite Frequency for this category',
				'monthlyCollectionWeek'	  					   =>  'monthly collection week name',
				'categoryTypeId'	  						   =>  'product category Type'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
			
				$create = MfnProductCategory::create($req->all());

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('productCategoryCreateSuccess'),
				);
				
				return response::json($data);
			}
		}

		public function updateRequest(Request $req) {

			$productCategory = MfnProductCategory::select('id', 'name','shortName','overrideSavingsDepositeFrequency','overrideSavingsDepositeFrequencyForCategory','monthlyCollectionWeek','categoryTypeId')->where('id',$req->id)->first();

            $overrideSavingsDepositeFrequency=DB::table('mfn_loans_product_category')->value('overrideSavingsDepositeFrequency');
           
           $data = array(

				'productCategory'                   =>  $productCategory,
				'overrideSavingsDepositeFrequency'  =>  $overrideSavingsDepositeFrequency,
			);
			
			return response::json($data);
		}

		/**
		 * [Update Funding Organization Information]
		 * 
		 * @param Request $req
		 */
		public function updateItem(Request $req) {

			$rules = array(
                'name'	  							=>  'required',
				'shortName' 						=>  'required',
				'overrideSavingsDepositeFrequency'  =>  'required',
				'categoryTypeId' 					=>  'required',
			);
 
			$attributesNames = array(
				'name'	 									   =>  'product category name',
				'overrideSavingsDepositeFrequency'	  		   =>  'product category name',
				'overrideSavingsDepositeFrequencyForCategory'  =>  'Override Savings Deposite Frequency for this category',
				'monthlyCollectionWeek'	  					   =>  'monthly collection week name',
				'categoryTypeId'	  						   =>  'product category Type'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$productCategory = MfnProductCategory::find($req->id);
				$productCategory->name = $req->name;
				$productCategory->shortName = $req->shortName;
				$productCategory->overrideSavingsDepositeFrequency = $req->overrideSavingsDepositeFrequency;
				$productCategory->overrideSavingsDepositeFrequencyForCategory = $req->overrideSavingsDepositeFrequencyForCategory;
				$productCategory->monthlyCollectionWeek = $req->monthlyCollectionWeek;
				$productCategory->categoryTypeId = $req->categoryTypeId;
				$productCategory->updatedDate = Carbon::now();
				$productCategory->update();

				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgSuccess'),
					'responseText'   =>  MicroFinance::getMessage('productCategoryUpdateSuccess'),
				);
				
				return response()->json($data);
			}
		}

		
	}