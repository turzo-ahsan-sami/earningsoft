<?php

	namespace App\Http\Controllers\microfin\configuration\openingBalance\member;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\member\MfnMemberInformation;
	use App\microfin\savings\MfnSavingsAccount;
	use App\microfin\samity\MfnSamity;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\Traits\GetSoftwareDate;
	use App\Http\Controllers\microfin\MicroFinance;

	class MfnMemberOpeningBalanceController extends Controller {

		protected $MicroFinance;
		
		use GetSoftwareDate;

		private $TCN;

		public function __construct() {

			$this->MicroFinance = new MicroFinance;

			$this->TCN = array(
				array('SL No.', 70), 
				array('Code', 100),
				array('Name', 0),
				array('Primary Product', 0),
				array('Spouse/Father/Son Name', 0),				
				array('Gender', 80),
				array('Branch', 0),
				array('Samity', 0),
				array('Admission Date', 100),
				array('Status', 70),
				array('Entry By', 120),
				array('Action', 80)
			);	
		}
	
		public function index() {

			$TCN = $this->TCN;
		
			$damageData = array(
				'TCN' 	   			=>	$TCN,
				'members'  			=>  $this->MicroFinance->getActiveMembers(),
				'gender'   			=>  $this->MicroFinance->getGender(),
				'dataNotAvailable'	=>	$this->MicroFinance->dataNotAvailable(),
				'MicroFinance'      =>  $this->MicroFinance
			);

			return view('microfin.member.member.viewMember', ['damageData' => $damageData]);
		}

		public function addOpeningBalanceMember() {

			$samities = $this->MicroFinance->getSamity();
			$villages = $this->MicroFinance->getVillages(Session::get('branchId'));
			$curResidentType = $this->MicroFinance->getCurResidentType();
			$primaryProduct = $this->MicroFinance->getActiveLoanPrimaryProductOptions();
			$mandatorySavingsProduct = $this->MicroFinance->getMandatorySavingsProduct();
			$maritalStatus = $this->MicroFinance->getMaritalStatus();
			$relationship = $this->MicroFinance->getRelationship();
			$educationLevel = $this->MicroFinance->getEducationLevel();
			$country = $this->MicroFinance->getCountry();
			$designation = $this->MicroFinance->getDesignationOfReferrer();
			$memberType = $this->MicroFinance->getMemberType();
			$professions = $this->MicroFinance->getProfessionOfMember();
			$religion = $this->MicroFinance->getReligion();

			$damageData = array(
				'samities'  	   		  =>  $samities,
				'admissionDate'    	  	  =>  $this->MicroFinance->getMicroFinanceDateFormat(GetSoftwareDate::getSoftwareDate()),
				'villages'  	   		  =>  $villages,
				'curResidentType'  		  =>  $curResidentType,
				'primaryProduct'   		  =>  $primaryProduct,
				'mandatorySavingsProduct' =>  $mandatorySavingsProduct,
				'maritalStatus'    		  =>  $maritalStatus,
				'relationship'     		  =>  $relationship,
				'educationLevel'   		  =>  $educationLevel,
				'country'   	   		  =>  $country,
				'designation'			  =>  $designation,
				'memberType'			  =>  $memberType,
				'professions'			  =>  $professions,
				'religion'				  =>  $religion
			);

			return view('microfin.configuration.openingBalance.member.addOpeningBalanceMember', ['damageData' => $damageData]);
		}

		/**
		 * [Function for return Member Code]
		 * 
		 * @param  Request $req [Request for Member Code]
		 * @return [type]       [An array contains Member Code]
		 */
		public function loadMemberCode(Request $req) {

			$samityOB = DB::table('mfn_samity')
						  ->select('code', 
						  		   'samityTypeId', 
						  		   'workingAreaId', 
						  		   'openingDate'
						  		  )
						  ->where('id', $req->id)
						  ->first();

			$samityCode = $samityOB->code;

			//	GET GENDER TYPE OPTIONS FOR SPECIFIC SAMITY TYPE ID.
			$samityType = $this->MicroFinance->getGenderOptions($samityOB->samityTypeId);

			// START AUTO GENERATE MEMBER CODE.
            $numRows = DB::table('mfn_member_information')->select('id')->where('samityId', $req->id)->count();
            $memberOB = DB::table('mfn_member_information')->where('samityId', $req->id)->select('code')->get();

            $memberCodeArr = [];

            //	GET ALL THE MEMBER SL NUMBER FROM MEMBER CODE OF THE SAMITY.
            foreach($memberOB as $member):
            	$memberCodeArr[] = (int) substr($member->code, -5, 5);
            endforeach;
                                            
            if($numRows<=0):
                $memberSL  = sprintf('%04d', 1);
                $code = $memberSL;
            else:
            	$maxMemberSL = max($memberCodeArr) + 1;
                $memberSL  = sprintf('%04d', $maxMemberSL);
                $code = $memberSL;
            endif;
            // END AUTO GENERATE MEMBER CODE.

			$memberCode = $samityCode . '.' . str_pad($code, 5, 0, STR_PAD_LEFT);

			//	START MANUFACTURING WORKING AREA OF SAMITY AS PRESENT ADDRESS.
			$workingAreaOB = DB::table('gnr_working_area')
							   ->where('id', $samityOB->workingAreaId)
							   ->select('villageId',
							   	        'unionId',
							   	        'upazilaId',
							   	        'districtId'
							   	       )
							   ->first();

			$presentAddress = '';
			$table = ['gnr_village', 'gnr_union', 'gnr_upzilla', 'gnr_district'];
			$title = ['Vill: ', 'Union: ', 'Upzilla: ', 'District: '];

			$i = 0;
			foreach($workingAreaOB as $val):
				$presentAddress .= $title[$i];
				$presentAddress .= $this->MicroFinance->getNameValueForId($table[$i], $val);
				$presentAddress .= ', ';
				$i++;
			endforeach;
			//	END MANUFACTURING WORKING AREA OF SAMITY AS PRESENT ADDRESS.

			$data = array(
				'memberCode'         =>  $memberCode,
				'samityTypeOptions'  =>  $samityType,
				'presentAddress'	 =>	 substr($presentAddress, 0, -2),
				'samityOpeningDate'  =>  $samityOB->openingDate,
				'softwareDate'  	 =>  GetSoftwareDate::getSoftwareDate(),
			);
			
			return response::json($data); 
		}

		public function loadMemberAge(Request $req) {

			$catchErr = 0;
			$ageErrMsg = '';
			
			$dob = Carbon::parse($req->dob);
			$memberAge = $dob->diffInYears(Carbon::now());
			$monthsDiff = $dob->diffInMonths(Carbon::now());
			$daysDiff = $dob->diffInDays(Carbon::now());
			
			$actualDiff = $dob->diff(Carbon::now());
			$daysRemainder = $actualDiff->days - ($actualDiff->y * 365);

			if($daysRemainder>180)
				$memberAge = $memberAge + 1;
			if($daysRemainder>0 && $daysRemainder<180)
				$memberAge = $memberAge;

			if($actualDiff->y * 365<18 * 365):
				$ageErrMsg = 'The member\'s age is below 18 years. Please select a member within 18-60 years range.';
				$catchErr = 1;
			endif;
			if($actualDiff->y * 365>60 * 365):
				$ageErrMsg = 'The member\'s age is above 60 years. Please select a member within 18-60 years range.';
				$catchErr = 1;
			endif;

			$data = array(
				'dob'        =>  $req->dob,
				'memberAge'  =>  $memberAge,
				'months'     =>  $monthsDiff,
				'days'       =>  $daysDiff,
				'actualDiff' =>  $actualDiff,
				'catchErr'   =>  $catchErr,
				'errMsg'     =>  $ageErrMsg
			);
			
			return response::json($data); 
		}

		public function nIDDuplicacyCheck(Request $req) {

			$nIDExists = DB::table('mfn_member_information')->where('nID', $req->nID)->count();

			$data = array(
				'nIDexists'  =>  ($nIDExists>=1)?1:0
			);
			
			return response::json($data); 
		}

		public function birthRegNoDuplicacyCheck(Request $req) {

			$birthRegNoExists = DB::table('mfn_member_information')->where('birthRegNo', $req->birthRegNo)->count();

			$data = array(
				'birthRegNoExists'  =>  ($birthRegNoExists>=1)?1:0
			);
			
			return response::json($data); 
		}

		public function passportNoDuplicacyCheck(Request $req) {

			$passportNoExists = DB::table('mfn_member_information')->where('passportNo', $req->passportNo)->count();

			$data = array(
				'passportNoExists'  =>  ($passportNoExists>=1)?1:0
			);
			
			return response::json($data); 
		}

		public function mobileNoDuplicacyCheck(Request $req) {

			$mobileNoExists = DB::table('mfn_member_information')->where('mobileNo', $req->mobileNo)->count();

			$data = array(
				'mobileNoExists'  =>  ($mobileNoExists>=1)?1:0
			);
			
			return response::json($data); 
		}

		/**
		 * [Insert Member Information]
		 * 
		 * @param Request $req
		 */
		public function addItem(Request $req) {

			$rules = array(
				'samityId'		 	   =>  'required',
				'name'		 	 	   =>  'required', 
				'surName'		 	   =>  'required', 
				'admissionDate'  	   =>  'required',
				'primaryProductId'	   =>  'required',
				'code'	 		 	   =>  'required|unique:mfn_member_information,code',
				'gender'  		 	   =>  'required',
				'age'  		 		   =>  'required',
				'dob'  		 		   =>  'required',
				'maritalStatus'  	   =>  'required',
				'spouseFatherSonName'  =>  'required',
				'relationship'  	   =>  'required',
				'nID'  		 		   =>  'required|unique:mfn_member_information,nID',
				'birthRegNo'  		   =>  'unique:mfn_member_information,birthRegNo',
				'passportNo'  		   =>  'unique:mfn_member_information,passportNo',
				'mobileNo'  		   =>  'required|unique:mfn_member_information,mobileNo',
				'curVillageWard'  	   =>  'required',
				'permVillageWard'  	   =>  'required',
			);

			$attributesNames = array(
				'name'  =>  'name'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails()): 
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else:
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['branchId' => Session::get('branchId')]);
				$req->request->add(['entryBy' => Auth::user()->id]);
				
				//	CHANGE THE ADMISSION  DATE FORMAT.
				$admissionDate = date_create($req->admissionDate);
				$req->request->add(['admissionDate' => date_format($admissionDate, "Y-m-d")]);

				//	CHANGE THE DATE OF BIRTH FORMAT.
				$dob = date_create($req->dob);
				$req->request->add(['dob' => date_format($dob, "Y-m-d")]);

				//	PROFILE IMAGE STORE. 
				if($req->hasFile('profileImage')):
	                $profileImage = $req->file('profileImage');
	                $filename = $profileImage->getClientOriginalName();
	                $EXT = $profileImage->getClientOriginalExtension();
	                $profileImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
                    $req->file('profileImage')->move('uploads/images/member/profile/', $profileImageFileName);
	            endif;

	            //	REGULAR SIGNATURE IMAGE STORE. 
	            if($req->hasFile('regularSignatureImage')):
	                $regularSignature = $req->file('regularSignatureImage');
	                $filename = $regularSignature->getClientOriginalName();
	                $EXT = $regularSignature->getClientOriginalExtension();
	                $regularSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
                    $req->file('regularSignatureImage')->move('uploads/images/member/regular-signature/', $regularSignatureImageFileName);
	            endif;

	            //	NATIONAL ID SIGNATURE IMAGE STORE. 
	            if($req->hasFile('nIDSignatureImage')):
	                $nIDSignature = $req->file('nIDSignatureImage');
	                $filename = $nIDSignature->getClientOriginalName();
	                $EXT = $nIDSignature->getClientOriginalExtension();
	                $nIDSignatureImageFileName = $this->MicroFinance->imageNameEncoder($filename, $EXT, Session::get('branchId'), $req->code);
                    $req->file('nIDSignatureImage')->move('uploads/images/member/nid-signature/', $nIDSignatureImageFileName);
	            endif;

				$create = MfnMemberInformation::create($req->all());

				//	FOR PROFILE, REGULAR SIGNATURE AND NATIONAL ID SINGNATURE IMAGE NAME SAVE.
				$fileNameSave = MfnMemberInformation::where('code', '=', $req->code)->first();
				
				if($req->hasFile('profileImage')):
					$fileNameSave->profileImage = $profileImageFileName;
				endif;

				if($req->hasFile('regularSignatureImage')):
					$fileNameSave->regularSignatureImage = $regularSignatureImageFileName;
				endif;
				
				if($req->hasFile('nIDSignatureImage')):
					$fileNameSave->nIDSignatureImage = $nIDSignatureImageFileName;
				endif;
				
				$fileNameSave->save();

				$data = array(
					'responseTitle'  =>  'Success!',
					'responseText'   =>  'New member has been added to the system successfully.'
				);
				
				return response::json($data);
			endif;
		}
	}