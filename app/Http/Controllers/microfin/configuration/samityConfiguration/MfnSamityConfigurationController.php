<?php

	namespace App\Http\Controllers\microfin\configuration\samityConfiguration;

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

	class MfnSamityConfigurationController extends controller {

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
            $configData =DB::table('mfn_cfg')->where('name','samity_cfg')->value('config');
            
            $configurationData = json_decode($configData,true);
            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'configurationData'   =>$configurationData,
			);

			return view('microfin.configuration.samityConfiguration.viewSamityConfiguration',['damageData' => $damageData]);
		}

		public function addSamityConfiguration() {
            $configData =DB::table('mfn_cfg')->where('name','samity_cfg')->value('config');
            
            $configurationData = json_decode($configData,true);
            $damageData = array(
				'boolean'=> $this->MicroFinance->getBooleanOptions(),
				'configurationData'   =>$configurationData,
			);

			return view('microfin.configuration.samityConfiguration.addSamityConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert Configuration Samity Configuration]
		 * 
		 * @param Request $req
		 */


		public function addItem(Request $req) {
      
      	      $samity=DB::table('mfn_cfg')->where('name','samity_cfg')->select('name')->first();

      	       $rules = array(
				'maximumMemberPerSamity'	              => 'required',
				'IsProductShownOnSamityPage'              => 'required',
			  );

			  $attributesNames = array(
				'maximumMemberPerSamity'	              => 'maximum Member Per Samity',
				'IsProductShownOnSamityPage'              => 'Is Product Shown On Samity Page',
			  );

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else{
              if($samity){
                 $samityConfigurations=MfnSamityConfiguration::where('name','samity_cfg')->first();
                 $samityConfigurations->config = json_encode([
                 	'maximumMemberPerSamity'=>$req->maximumMemberPerSamity,
                 	'IsProductShownOnSamityPage'=>$req->IsProductShownOnSamityPage]);
                 $samityConfigurations->save();
				  
		     }else{
		    	  $samityConfiguration=new MfnSamityConfiguration();
		          $samityConfiguration->name='samity_cfg';
		          $samityConfiguration->config = json_encode([
		          	'maximumMemberPerSamity'=>$req->maximumMemberPerSamity,
		          	'IsProductShownOnSamityPage'=>$req->IsProductShownOnSamityPage]);
		          $samityConfiguration->createdDate = Carbon::now();
		          $samityConfiguration->save();
		        }
		     }      
             $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Samity Configuration insert successfully.'
              );

               return response::json($data);

        }
    }