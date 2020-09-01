<?php

	namespace App\Http\Controllers\microfin\configuration\loanSettingConfiguration;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\configuration\MfnSamityConfiguration;
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

	class MfnLoanSettingConfigurationController extends controller {

		protected $MicroFinance;

		public function __construct() {

			$this->MicroFinance = New MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
	            array('Name', 0),
	            array('Status', 70),
			    array('Action', 80)
			);	
		}
        public function index() {

			  $loanConfig =DB::table('mfn_cfg')->where('name','loan_cfg')->value('config');
			  $loanConfigurationData = json_decode($loanConfig,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'loanConfigurationData' =>$loanConfigurationData
				
			);

			return view('microfin.configuration.loanSettingConfiguration.viewLoanSettingConfiguration',['damageData' => $damageData]);
		}

		public function addLoanSettingConfiguration() {

			  $loanConfig =DB::table('mfn_cfg')->where('name','loan_cfg')->value('config');
			  $loanConfigurationData = json_decode($loanConfig,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'loanConfigurationData' =>$loanConfigurationData
				
			);

			return view('microfin.configuration.loanSettingConfiguration.addLoanSettingConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert settings genaral configuration]
		 * 
		 * @param Request $req
		 */
	public function addItem(Request $req) {
      		
      	    $loanSetting=DB::table('mfn_cfg')->where('name','loan_cfg')->value('name');
      	    $rules = array(
				'defaultInterestCalculationMethod'	                  => 'required',
				'isOtherInterestCalculationMethodAllowed'             => 'required',
				'isMultipleLoanAllowedForPrimaryProducts'             => 'required',
				'loanDisbursementAmountForBankPayment'                => 'required',
				'maximumNumberOfInstallment'                          => 'required'
				
			);

			$attributesNames = array(
				'defaultInterestCalculationMethod'	                  => 'default Interest Calculation Method',
				'isOtherInterestCalculationMethodAllowed'             => 'is Other Interest Calculation Method Allowed',
				'isMultipleLoanAllowedForPrimaryProducts'             => 'is Multiple Loan Allowed For Primary Products',
				'loanDisbursementAmountForBankPayment'                => 'loan Disbursement Amount For Bank Payment',
				'maximumNumberOfInstallment'                          => 'maximum Number Of Installment'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else{
      	     if($loanSetting){
		          $loanSettingConfigurations=MfnSamityConfiguration::where('name','loan_cfg')->first();
		          $loanSettingConfigurations->config = json_encode([
		                'defaultInterestCalculationMethod'=>$req->defaultInterestCalculationMethod,
		                'isOtherInterestCalculationMethodAllowed'=>$req->isOtherInterestCalculationMethodAllowed,
		                'isMultipleLoanAllowedForPrimaryProducts'=>$req->isMultipleLoanAllowedForPrimaryProducts,
		                'additionalFeeLabelName'=>$req->additionalFeeLabelName,
		                'insuranceAmountLabelName'=>$req->insuranceAmountLabelName,
		                'isLoanRebateAllowedDuringLoanWaiverForDeathMember'=>$req->isLoanRebateAllowedDuringLoanWaiverForDeathMember,
		                'isInsuranceAmountEditable'=>$req->isInsuranceAmountEditable,
		                'isMultipleOneTimeLoanDisburseAllowed'=>$req->isMultipleOneTimeLoanDisburseAllowed,
		                'isFirstRepaymentDateEditable'=>$req->isFirstRepaymentDateEditable,
		                'IsLoanCycleEditable'=>$req->IsLoanCycleEditable,
		                'loanDisbursementAmountForBankPayment'=>$req->loanDisbursementAmountForBankPayment,
		                'maximumNumberOfInstallment'=>$req->maximumNumberOfInstallment,
		                'IeligibleInterestCalculateForDailyBasisDFDLoan'=>$req->IeligibleInterestCalculateForDailyBasisDFDLoan,
		                'isLoanFormFeeWillBeShownInLoanDisbursePage'=>$req->isLoanFormFeeWillBeShownInLoanDisbursePage,
		                'maximumMemberAgeForGettingLoan'=>$req->maximumMemberAgeForGettingLoan,
		                'isLoanSecurityOptionEnable'=>$req->isLoanSecurityOptionEnable,
		                ]);
		                $loanSettingConfigurations->save();

		              }      
		    else {
                  $loanSettingConfiguration=new MfnSamityConfiguration;
                  $loanSettingConfiguration->name='loan_cfg';
                  $loanSettingConfiguration->config = json_encode([
				       'defaultInterestCalculationMethod'=>$req->defaultInterestCalculationMethod,
		               'isOtherInterestCalculationMethodAllowed'=>$req->isOtherInterestCalculationMethodAllowed,
		               'isMultipleLoanAllowedForPrimaryProducts'=>$req->isMultipleLoanAllowedForPrimaryProducts,
			           'additionalFeeLabelName'=>$req->additionalFeeLabelName,
			           'insuranceAmountLabelName'=>$req->insuranceAmountLabelName,
			           'isLoanRebateAllowedDuringLoanWaiverForDeathMember'=>$req->isLoanRebateAllowedDuringLoanWaiverForDeathMember,
			           'isInsuranceAmountEditable'=>$req->isInsuranceAmountEditable,
			           'isMultipleOneTimeLoanDisburseAllowed'=>$req->isMultipleOneTimeLoanDisburseAllowed,
			           'isFirstRepaymentDateEditable'=>$req->isFirstRepaymentDateEditable,
			           'IsLoanCycleEditable'=>$req->IsLoanCycleEditable,
			           'loanDisbursementAmountForBankPayment'=>$req->loanDisbursementAmountForBankPayment,
			           'maximumNumberOfInstallment'=>$req->maximumNumberOfInstallment,
			           'IeligibleInterestCalculateForDailyBasisDFDLoan'=>$req->IeligibleInterestCalculateForDailyBasisDFDLoan,
			           'isLoanFormFeeWillBeShownInLoanDisbursePage'=>$req->isLoanFormFeeWillBeShownInLoanDisbursePage,
			           'maximumMemberAgeForGettingLoan'=>$req->maximumMemberAgeForGettingLoan,
			           'isLoanSecurityOptionEnable'=>$req->isLoanSecurityOptionEnable,
				     ]);
				  $loanSettingConfiguration->createdDate = Carbon::now();
				  $loanSettingConfiguration->save();
				} 
			  }	     
			    $data = array(
			          'responseTitle' =>  'Success!',
			          'responseText'  =>  'Loan Setting Configuration insert successfully.'
			    );

                  return response::json($data);        
		}
    }