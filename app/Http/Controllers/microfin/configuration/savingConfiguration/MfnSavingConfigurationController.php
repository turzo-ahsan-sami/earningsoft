<?php

	namespace App\Http\Controllers\microfin\configuration\savingConfiguration;

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

	class MfnSavingConfigurationController extends controller {

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

			  $configSaving =DB::table('mfn_cfg')->where('name','saving_cfg')->value('config');
			  $savingConfiguration = json_decode($configSaving,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'savingConfiguration' =>$savingConfiguration
				
			);

			return view('microfin.configuration.savingConfiguration.viewSavingConfiguration',['damageData' => $damageData]);
		}
		public function addSavingConfiguration() {

			  $configSaving =DB::table('mfn_cfg')->where('name','saving_cfg')->value('config');
			  $savingConfiguration = json_decode($configSaving,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'savingConfiguration' =>$savingConfiguration
				
			);

			return view('microfin.configuration.savingConfiguration.addSavingConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert settings genaral configuration]
		 * 
		 * @param Request $req
		 */
	public function addItem(Request $req) {
      		
      	    $saving=DB::table('mfn_cfg')->where('name','saving_cfg')->value('name');
      	     $rules = array(
				'MaximumWithdrawRateWithoutLoan'	      => 'required',
				'MaximumWithdrawRateWithLoan'              => 'required',
			  );

			  $attributesNames = array(
				'MaximumWithdrawRateWithoutLoan'	   => 'Maximum Withdraw Rate Without Loan',
				'MaximumWithdrawRateWithLoan'          => 'Maximum Withdraw Rate With Loan',
			  );

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else{
      	     if($saving){
		          $savingsConfigurations=MfnSamityConfiguration::where('name','saving_cfg')->first();
		          $savingsConfigurations->config = json_encode([
		                'financialYearStartMonth'=>$req->financialYearStartMonth,
		                'MaximumWithdrawRateWithoutLoan'=>$req->MaximumWithdrawRateWithoutLoan,
		                'MaximumWithdrawRateWithLoan'=>$req->MaximumWithdrawRateWithLoan,
		                'savingBalanceUsedForInterestCalculatio'=>$req->savingBalanceUsedForInterestCalculatio,
		                //'minimumBalanceRequiredForInterestCalculation'=>$req->minimumBalanceRequiredForInterestCalculation,
		                'minimumAccountDurationToReceiveInterest'=>$req->minimumAccountDurationToReceiveInterest,
		                'isInactiveMemberEligibleToReceiveInterest'=>$req->isInactiveMemberEligibleToReceiveInterest,
		                'frequencyOfInterestPostingToAccounts'=>$req->frequencyOfInterestPostingToAccounts,
		                'interestCalculationClosingMonth'=>$req->interestCalculationClosingMonth,
		                'interestDisbursementMonth'=>$req->interestDisbursementMonth,
		                'IsSavingsClosingMonthIncludeDuringInterestCalculation'=>$req->IsSavingsClosingMonthIncludeDuringInterestCalculation,
		                'isNonCashSavingsInterestWithdrawAllowed'=>$req->isNonCashSavingsInterestWithdrawAllowed,
		                'isNonCashTransactionApplicableForDeathInactiveMember'=>$req->isNonCashTransactionApplicableForDeathInactiveMember,
		                 'isInterestAutoCalculationAllowedDuringSavingsClosing'=>$req->isInterestAutoCalculationAllowedDuringSavingsClosing,
		                'IsGeneralSavingsCloseAllowedWithoutMemberClosing'=>$req->IsGeneralSavingsCloseAllowedWithoutMemberClosing,
		                'MaximumSavingsWithdrawOrAdjustmentAllowedForGettingSaving'=>$req->MaximumSavingsWithdrawOrAdjustmentAllowedForGettingSaving,
		                'isOverdueMemberGettingInterest'=>$req->isOverdueMemberGettingInterest,
		                'isInterestAmountEditableDuringSavingClosing'=>$req->isInterestAmountEditableDuringSavingClosing,
		                'IsFrdDspSavingsEditableAfterOpeningDate'=>$req->IsFrdDspSavingsEditableAfterOpeningDate,
		                'ConsiderDayBasisCalculationForOnClosingSavingsInterest'=>$req->ConsiderDayBasisCalculationForOnClosingSavingsInterest
		                ]);
		                $savingsConfigurations->save();

		              }      
		    else {
                         $savingsConfiguration=new MfnSamityConfiguration;
                         $savingsConfiguration->name='saving_cfg';
                         $savingsConfiguration->config = json_encode([
				          	'financialYearStartMonth'=>$req->financialYearStartMonth,
		                    'MaximumWithdrawRateWithoutLoan'=>$req->MaximumWithdrawRateWithoutLoan,
		                    'MaximumWithdrawRateWithLoan'=>$req->MaximumWithdrawRateWithLoan,
		                    'savingBalanceUsedForInterestCalculatio'=>$req->savingBalanceUsedForInterestCalculatio,
			                'minimumAccountDurationToReceiveInterest'=>$req->minimumAccountDurationToReceiveInterest,
			                'isInactiveMemberEligibleToReceiveInterest'=>$req->isInactiveMemberEligibleToReceiveInterest,
			                'frequencyOfInterestPostingToAccounts'=>$req->frequencyOfInterestPostingToAccounts,
			                'interestCalculationClosingMonth'=>$req->interestCalculationClosingMonth,
			                'interestDisbursementMonth'=>$req->interestDisbursementMonth,
			                'IsSavingsClosingMonthIncludeDuringInterestCalculation'=>$req->IsSavingsClosingMonthIncludeDuringInterestCalculation,
			                'isNonCashSavingsInterestWithdrawAllowed'=>$req->isNonCashSavingsInterestWithdrawAllowed,
			                'isNonCashTransactionApplicableForDeathInactiveMember'=>$req->isNonCashTransactionApplicableForDeathInactiveMember,
			                 'isInterestAutoCalculationAllowedDuringSavingsClosing'=>$req->isInterestAutoCalculationAllowedDuringSavingsClosing,
			                'IsGeneralSavingsCloseAllowedWithoutMemberClosing'=>$req->IsGeneralSavingsCloseAllowedWithoutMemberClosing,
			                'MaximumSavingsWithdrawOrAdjustmentAllowedForGettingSaving'=>$req->MaximumSavingsWithdrawOrAdjustmentAllowedForGettingSaving,
			                'isOverdueMemberGettingInterest'=>$req->isOverdueMemberGettingInterest,
			                'isInterestAmountEditableDuringSavingClosing'=>$req->isInterestAmountEditableDuringSavingClosing,
			                'IsFrdDspSavingsEditableAfterOpeningDate'=>$req->IsFrdDspSavingsEditableAfterOpeningDate,
			                'ConsiderDayBasisCalculationForOnClosingSavingsInterest'=>$req->ConsiderDayBasisCalculationForOnClosingSavingsInterest
				          ]);
				          $savingsConfiguration->createdDate = Carbon::now();
				          $savingsConfiguration->save();
				      }   

				    }      
			        $data = array(
			             'responseTitle' =>  'Success!',
			             'responseText'  =>  'Saving Configuration insert successfully.'
			        );

                  return response::json($data);        
		}

		
	}