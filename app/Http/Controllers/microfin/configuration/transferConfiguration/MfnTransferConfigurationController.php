<?php

	namespace App\Http\Controllers\microfin\configuration\transferConfiguration;

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

	class MfnTransferConfigurationController extends controller {

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
            $transferConfig =DB::table('mfn_cfg')->where('name','transfer_cfg')->value('config');
            
            $transferConfiguration = json_decode($transferConfig,true);


            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'transferConfiguration'   =>$transferConfiguration,
			);

			return view('microfin.configuration.transferConfiguration.viewTransferConfiguration',['damageData' => $damageData]);
		}

		public function addTransferConfiguration() {
            $transferConfig =DB::table('mfn_cfg')->where('name','transfer_cfg')->value('config');
            
            $transferConfiguration = json_decode($transferConfig,true);


            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'transferConfiguration'   =>$transferConfiguration,
			);

			return view('microfin.configuration.transferConfiguration.addTransferConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert Configuration Samity Configuration]
		 * 
		 * @param Request $req
		 */


		public function addItem(Request $req) {
      
      	      $transfer=DB::table('mfn_cfg')->where('name','transfer_cfg')->select('name')->first();
              if($transfer){
                 $transferConfigurations=MfnSamityConfiguration::where('name','transfer_cfg')->first();
                 $transferConfigurations->config = json_encode([
                 	'isTransferAllowedForOpenLoanMember'=>$req->isTransferAllowedForOpenLoanMember,
                 	'canMemberTransferFromOneBranchToAnotherBranch'=>$req->canMemberTransferFromOneBranchToAnotherBranch
                 ]);
                 $transferConfigurations->save();
				  
		     }else{
		    	  $transferConfiguration=new MfnSamityConfiguration();
		          $transferConfiguration->name='transfer_cfg';
		          $transferConfiguration->config = json_encode([
		          	'isTransferAllowedForOpenLoanMember'=>$req->isTransferAllowedForOpenLoanMember,
                 	'canMemberTransferFromOneBranchToAnotherBranch'=>$req->canMemberTransferFromOneBranchToAnotherBranch
		          ]);
		          $transferConfiguration->createdDate = Carbon::now();
		          $transferConfiguration->save();

		     }      
             $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Transfer Configuration insert successfully.'
              );

               return response::json($data);

        }
    }