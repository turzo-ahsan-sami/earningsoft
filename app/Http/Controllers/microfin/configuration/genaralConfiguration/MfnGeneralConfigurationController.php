<?php

	namespace App\Http\Controllers\microfin\configuration\genaralConfiguration;

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

	class MfnGeneralConfigurationController extends controller {

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

			$configGenaral =DB::table('mfn_cfg')->where('name','general_cfg')->value('config');
			$generalconfigurationData = json_decode($configGenaral,true);

			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'generalconfigurationData' =>$generalconfigurationData
				
			);

			return view('microfin.configuration.genaralConfiguration.viewGeneralConfiguration',['damageData' => $damageData]);
		}

		public function addGeneralConfiguration() {

			$configGenaral =DB::table('mfn_cfg')->where('name','general_cfg')->value('config');
			$generalconfigurationData = json_decode($configGenaral,true);

			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'generalconfigurationData' =>$generalconfigurationData
				
			);

			return view('microfin.configuration.genaralConfiguration.addGeneralConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert settings genaral configuration]
		 * 
		 * @param Request $req
		 */
	public function addItem(Request $req) {
      		
      	$general=DB::table('mfn_cfg')->where('name','general_cfg')->value('name');
	      	    
      	if ($req->file('organizationLogo')) {
			$logoImageFile = $req->file('organizationLogo');
			$logoImageFilename =date('Y-m-d H:i:s') . '-' .str_random(10). '.' . $logoImageFile->getClientOriginalExtension();
			$destinationPath = base_path() . '/public/images/';
			$images =$logoImageFile->move($destinationPath, $logoImageFilename);
        }
        
        $rules = array(
			'organizationName'	                  => 'required',
			'organizationCode'                    => 'required',
			//'organizationLogo'                    => 'required',
			'organizationEstablismentDate'        => 'required',
			'insuranceClaimMultiplier'            => 'required',
			'forLoansInterestLabelAlternative'    => 'required',
			'forSavingInterestLabelAlternative'   => 'required',
			'jicaFundingOrganizationName'         => 'required'
			);

		$attributesNames = array(
			'organizationName'	                  => 'Organizaition name',
			'organizationCode'                    => 'Organizaition code',
			//'organizationLogo'                    => 'Organizaition Logo',
			'organizationEstablismentDate'        => 'Organization Establish date',
			'insuranceClaimMultiplier'            => 'clime multiplier',
			'forLoansInterestLabelAlternative'    => 'For Loan Interest Lavel AlterNative',
			'forSavingInterestLabelAlternative'   => 'For Saving Interest Lavel AlterNative',
			'jicaFundingOrganizationName'         => 'JIC funding Organizaition Name'
		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributesNames);

	  if($validator->fails()) {
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
	  }	
	  else{
 	   
      	if($general){
		   $generalConfigurations=MfnSamityConfiguration::where('name','general_cfg')->first();
		   $generalConfigurations->config = json_encode([
		      'orgName'=>$req->organizationName,
		      'orgCode'=>$req->organizationCode,
		      'orgEstablismentDate'=>$req->organizationEstablismentDate,
		      'organizationLogo'=>$logoImageFilename,
		      'isInsuranceReq'=>$req->isInsuranceRequired,
		      'insuranceClaimMul'=>$req->insuranceClaimMultiplier,
		      'forLoansInterestLabelAlt'=>$req->forLoansInterestLabelAlternative,
		      'forSavingInterestLabelAlt'=>$req->forSavingInterestLabelAlternative,
		      'jicaFundingOrgName'=>$req->jicaFundingOrganizationName,
		      'isMobileNumberReqForEmp'=>$req->isMobileNumberRequiresForEmployee,
		      'isEmailReqForEmp'=>$req->isEmailRequiredForEmployee,
		      'isNationalIdReqForEmp'=>$req->isNationalIdRequiredForEmployee,
		      'isFatherNameReqForEmp'=>$req->isFatherNameRequiredForEmployee,
		      'isMotherNameReqForEmp'=>$req->isMotherNameRequiredForEmployee
		    ]);
		   $generalConfigurations->save();

		}      
		else {
           $generalConfiguration=new MfnSamityConfiguration;
           $generalConfiguration->name='general_cfg';
           $generalConfiguration->config = json_encode([
			  'orgName'=>$req->organizationName,
			  'orgCode'=>$req->organizationCode,
			  'orgEstablismentDate'=>$req->organizationEstablismentDate,
			  'organizationLogo'=>$logoImageFilename,
			  'isInsuranceReq'=>$req->isInsuranceRequired,
			  'insuranceClaimMul'=>$req->insuranceClaimMultiplier,
			  'forLoansInterestLabelAlt'=>$req->forLoansInterestLabelAlternative,
			  'forSavingInterestLabelAlt'=>$req->forSavingInterestLabelAlternative,
			  'jicaFundingOrgName'=>$req->jicaFundingOrganizationName,
			  'isMobileNumberReqForEmp'=>$req->isMobileNumberRequiresForEmployee,
			  'isEmailReqForEmp'=>$req->isEmailRequiredForEmployee,
			  'isNationalIdReqForEmp'=>$req->isNationalIdRequiredForEmployee,
			  'isFatherNameReqForEmp'=>$req->isFatherNameRequiredForEmployee,
			  'isMotherNameReqForEmp'=>$req->isMotherNameRequiredForEmployee
			]);
		   $generalConfiguration->createdDate = Carbon::now();
		   $generalConfiguration->save();
		 }
		}     
		  $data = array(
			   'responseTitle' =>  'Success!',
			   'responseText'  =>  'Samity Configuration insert successfully.'
		  );
            return response::json($data); 
        }

	}