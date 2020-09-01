<?php

	namespace App\Http\Controllers\microfin\configuration\memberSetting;

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

	class MfnMemberSettingController extends controller {

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

			  $configmember =DB::table('mfn_cfg')->where('name','member_cfg')->value('config');
			  $memberconfigData = json_decode($configmember,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'memberconfigData' =>$memberconfigData
				
			);

			return view('microfin.configuration.memberSetting.viewMemberSettingConfiguration',['damageData' => $damageData]);
		}


		public function addMemberSettingConfiguration() {

			  $configmember =DB::table('mfn_cfg')->where('name','member_cfg')->value('config');
			  $memberconfigData = json_decode($configmember,true);


			$damageData = array(
				'boolean'  =>  $this->MicroFinance->getBooleanOptions(),
				'memberconfigData' =>$memberconfigData
				
			);

			return view('microfin.configuration.memberSetting.addMemberSettingConfiguration',['damageData' => $damageData]);
		}

		/**
		 * [Insert settings genaral configuration]
		 * 
		 * @param Request $req
		 */
	public function addItem(Request $req) {
      		
      	    $general=DB::table('mfn_cfg')->where('name','member_cfg')->value('name');
      	    $rules = array(
				'perPassBookFee'	                  => 'required',
				'memberAdmissionFeeLabelName'         => 'required',
				
			);

			$attributesNames = array(
				'perPassBookFee'	                  => 'Per Pass Book Fee',
				'memberAdmissionFeeLabelName'         => 'Member Admission Fee Label Name',
				
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()) 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else{
      	     if($general){
		        $memberConfigurations=MfnSamityConfiguration::where('name','member_cfg')->first();
		        $memberConfigurations->config = json_encode([
		            'nomineeInfoReqForMember'=>$req->nomineeInformationRequiredForMember,
		            'bothFatherAndSpouseNameReqMember'=>$req->bothFatherAndSpouseNameRequiredForMember,
		            'IsSktRequired'=>$req->IsSktRequired,
		            'sktAmount'=>$req->sktAmount,
		            'DateOfBirthReqInMemberInfo'=>$req->isDateOfBirthRequiredInMemberInformation,
		            'NIdBirthRegNoReq'=>$req->IsNationalIdBirthRegistrationNoRequired,
		            'memberAdmissionFee'=>$req->memberAdmissionFee,
		            'passBookEntrySystemAllowed'=>$req->passBookEntrySystemAllowed,
		            'perPassBookFee'=>$req->perPassBookFee,
		            'PassBookFeeEditableOrNot'=>$req->PassBookFeeEditableOrNot,
		            'isMobileNoRequired'=>$req->isMobileNoRequired,
		            'isFamilyHomeContactNoReq'=>$req->isFamilyHomeContactNoRequired,
		            'isPassbookNumReq'=>$req->isPassbookNumberRequired,
		            'highestPriorityToAllocateExisting'=>$req->highestPriorityToAllocate,
		            'groupAndSubgroupShownInMemberPage'=>$req->groupAndSubgroupShownInMemberPage,
		            'isEducationLabelShownInMemberPage'=>$req->isEducationLabelShownInMemberPage,
		            'physicalAttributeList'=>$req->physicalAttributeList,
		            'defaultLengthOfAdmissionNo'=>$req->defaultLengthOfAdmissionNo,
		            'defaultLengthOfFormApplicationNo'=>$req->defaultLengthOfFormApplicationNo,
		            'minimumAgeOfMember'=>$req->minimumAgeOfMember,
		            'isSurnameRequiredForMember'=>$req->isSurnameRequiredForMember,
		            'isSurnameShownForNominee'=>$req->isSurnameShownForNominee,
		            'maximumAgeOfMember'=>$req->maximumAgeOfMember,
		            'memberAdmissionFeeLabelName'=>$req->memberAdmissionFeeLabelName
		                
		           ]);
		            $memberConfigurations->save();

		           }      
		    else {
                $memberConfiguration=new MfnSamityConfiguration;
                $memberConfiguration->name='member_cfg';
                $memberConfiguration->config = json_encode([
					'nomineeInfoReqForMember'=>$req->nomineeInformationRequiredForMember,
				    'bothFatherAndSpouseNameReqMember'=>$req->bothFatherAndSpouseNameRequiredForMember,
				    'IsSktRequired'=>$req->IsSktRequired,
				    'sktAmount'=>$req->sktAmount,
				    'DateOfBirthReqInMemberInfo'=>$req->isDateOfBirthRequiredInMemberInformation,
				    'NIdBirthRegNoReq'=>$req->IsNationalIdBirthRegistrationNoRequired,
				    'memberAdmissionFee'=>$req->memberAdmissionFee,
				    'passBookEntrySystemAllowed'=>$req->passBookEntrySystemAllowed,
				    'perPassBookFee'=>$req->perPassBookFee,
				    'PassBookFeeEditableOrNot'=>$req->PassBookFeeEditableOrNot,
				    'isMobileNoRequired'=>$req->isMobileNoRequired,
				    'isFamilyHomeContactNoReq'=>$req->isFamilyHomeContactNoRequired,
				    'isPassbookNumReq'=>$req->isPassbookNumberRequired, 
				    'highestPriorityToAllocateExisting'=>$req->highestPriorityToAllocate,
				    'groupAndSubgroupShownInMemberPage'=>$req->groupAndSubgroupShownInMemberPage,
				    'isEducationLabelShownInMemberPage'=>$req->isEducationLabelShownInMemberPage,
				    'physicalAttributeList'=>$req->physicalAttributeList,
				    'defaultLengthOfAdmissionNo'=>$req->defaultLengthOfAdmissionNo,
				    'defaultLengthOfFormApplicationNo'=>$req->defaultLengthOfFormApplicationNo,
				    'minimumAgeOfMember'=>$req->minimumAgeOfMember,
				    'isSurnameRequiredForMember'=>$req->isSurnameRequiredForMember,
				    'isSurnameShownForNominee'=>$req->isSurnameShownForNominee,
				    'maximumAgeOfMember'=>$req->maximumAgeOfMember,
				    'memberAdmissionFeeLabelName'=>$req->memberAdmissionFeeLabelName,	        
				]);
				$memberConfiguration->createdDate = Carbon::now();
				$memberConfiguration->save();
			}
		}      
			    $data = array(
			         'responseTitle' =>  'Success!',
			         'responseText'  =>  'Member Setting  Configuration insert successfully.'
			     );

                return response::json($data);        
			}

		
	}