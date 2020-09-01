<?php

	namespace App\Http\Controllers\microfin\configuration\operationalPolicy;

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

	class MfnOperationalPolicyController extends controller {

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
            $operationalConfig =DB::table('mfn_cfg')->where('name','operationalPolicy_cfg')->value('config');
            
            $operationalConfiguration = json_decode($operationalConfig,true);


            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'operationalConfiguration'   =>$operationalConfiguration
			);

			return view('microfin.configuration.operationalPolicy.viewOperationalPolicy',['damageData' => $damageData]);
		}
		public function addOperationalPolicy() {
            $operationalConfig =DB::table('mfn_cfg')->where('name','operationalPolicy_cfg')->value('config');
            
            $operationalConfiguration = json_decode($operationalConfig,true);


            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'operationalConfiguration'   =>$operationalConfiguration
			);

			return view('microfin.configuration.operationalPolicy.addOperationalPolicy',['damageData' => $damageData]);
		}

		/**
		 * [Insert Configuration Samity Configuration]
		 * 
		 * @param Request $req
		 */


		public function addItem(Request $req) {
      
      	      $operationalPolicy=DB::table('mfn_cfg')->where('name','operationalPolicy_cfg')->select('name')->first();
      	      $rules = array(
				'isServiceRulesPresent'	                  => 'required',
				'isRecruitmentPolicyPresent'              => 'required',
				'IsFinancialPolicyPresent'	              => 'required',
				'isSavingsAndCreditPolicyPresent'         => 'required',
				
			  );

			  $attributesNames = array(
				'isServiceRulesPresent'	                  => 'is Service Rules Present',
				'isRecruitmentPolicyPresent'              => 'is Recruitment Policy Present',
				'IsFinancialPolicyPresent'	              => 'Is Financial Policy Present',
				'isSavingsAndCreditPolicyPresent'         => 'is Savings And Credit Policy Present'
				
			  );

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else{
              if($operationalPolicy){
                 $operationalConfigurations=MfnSamityConfiguration::where('name','operationalPolicy_cfg')->first();
                 $operationalConfigurations->config = json_encode([

                 	'isServiceRulesPresent'=>$req->isServiceRulesPresent,
                 	'isRecruitmentPolicyPresent'=>$req->isRecruitmentPolicyPresent,
                 	'IsFinancialPolicyPresent'=>$req->IsFinancialPolicyPresent,
                 	'isSavingsAndCreditPolicyPresent'=>$req->isSavingsAndCreditPolicyPresent,
                 	'araLicenceNo'=>$req->mraLicenceNo,
                 	

                 ]);
                 $operationalConfigurations->save();
				  
		     }else{

		    	  $operationalConfiguration=new MfnSamityConfiguration();
		          $operationalConfiguration->name='operationalPolicy_cfg';
		          $operationalConfiguration->config = json_encode([

		          	'isServiceRulesPresent'=>$req->isServiceRulesPresent,
                 	'isRecruitmentPolicyPresent'=>$req->isRecruitmentPolicyPresent,
                 	'IsFinancialPolicyPresent'=>$req->IsFinancialPolicyPresent,
                 	'isSavingsAndCreditPolicyPresent'=>$req->isSavingsAndCreditPolicyPresent,
                 	'araLicenceNo'=>$req->mraLicenceNo,
                 	
		          ]);
		          $operationalConfiguration->createdDate = Carbon::now();
		          $operationalConfiguration->save();
		        }
		     }      
             $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  ' Configuration insert successfully.'
              );

               return response::json($data);

        }
    }