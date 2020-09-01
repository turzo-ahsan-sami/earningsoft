<?php

	namespace App\Http\Controllers\microfin\configuration\dayEndConfiguration;

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

	class MfnDayEndConfigurationController extends controller {

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
  /* ------ Veiw Data--------- */
		public function index() {
            $dayEndconfig =DB::table('mfn_cfg')->where('name','dayEnd_cfg')->value('config');
            $dayEndconfiguration = json_decode($dayEndconfig,true);

            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'dayEndconfiguration'   =>$dayEndconfiguration,
			);

			return view('microfin.configuration.dayEndConfiguration.viewDayEndConfiguration',['damageData' => $damageData]);
		}

		public function addDayEndConfiguration() {
            $dayEndconfig =DB::table('mfn_cfg')->where('name','dayEnd_cfg')->value('config');
            $dayEndconfiguration = json_decode($dayEndconfig,true);

            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'dayEndconfiguration'   =>$dayEndconfiguration,
			);

			return view('microfin.configuration.dayEndConfiguration.addDayEndConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert Configuration Samity Configuration]
		 * 
		 * @param Request $req
		 */


		public function addItem(Request $req) {
      
      	      $dayEnd=DB::table('mfn_cfg')->where('name','dayEnd_cfg')->select('name')->first();
              if($dayEnd){
                 $dayEndConfigurations=MfnSamityConfiguration::where('name','dayEnd_cfg')->first();
                 $dayEndConfigurations->config = json_encode([
                 	'isMisDayEndAlignWithAisDayEnd'=>$req->isMisDayEndAlignWithAisDayEnd,
                 	'cashInHandBalanceAllowed'=>$req->cashInHandBalanceAllowed,
                 	'needDayEndValidationForExecuteConsolidatedBM'=>$req->needDayEndValidationForExecuteConsolidatedBM
                 ]);
                 $dayEndConfigurations->save();
				  
		     }else{
		    	  $dayEndConfiguration=new MfnSamityConfiguration();
		          $dayEndConfiguration->name='dayEnd_cfg';
		          $dayEndConfiguration->config = json_encode([
		          	'isMisDayEndAlignWithAisDayEnd'=>$req->isMisDayEndAlignWithAisDayEnd,
                 	'cashInHandBalanceAllowed'=>$req->cashInHandBalanceAllowed,
                 	'needDayEndValidationForExecuteConsolidatedBM'=>$req->needDayEndValidationForExecuteConsolidatedBM
		          ]);
		          $dayEndConfiguration->createdDate = Carbon::now();
		          $dayEndConfiguration->save();

		     }      
             $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Day End Configuration insert successfully.'
              );

               return response::json($data);

        }
    }