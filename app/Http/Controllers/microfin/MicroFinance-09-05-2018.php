<?php

	namespace App\Http\Controllers\microfin;

	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\Contracts\Encryption\DecryptException;

	use App\Http\Controllers\Controller;
	use App\Http\Requests;

	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use DateTime;

	use App\gnr\GnrBranch;
	use App\gnr\GnrDivision;
	use App\gnr\GnrDistrict;
	use App\gnr\GnrUpazila;
	use App\gnr\GnrUnion;
	use App\gnr\GnrVillage;
	use App\gnr\GnrWorkingArea;
	
	use App\microfin\samity\MfnSamity;

	use App\microfin\member\MfnMemberInformation;
	use App\microfin\member\MfnMemberSamityTransfer;
	use App\microfin\member\MfnMemberPrimaryProductTransfer;
	use App\microfin\member\MfnMemberType;
	use App\microfin\member\MfnMemberClosing;

	use App\microfin\settings\MfnPrimaryProduct;
	use App\microfin\settings\MfnMaritalStatus;
	use App\microfin\settings\MfnRelationship;
	use App\microfin\settings\MfnProfession;
	use App\microfin\settings\MfnDesignation;
	use App\microfin\settings\MfnCategoryType;
	use App\microfin\settings\MfnFundingOrganization;
	use App\microfin\settings\MfnYearsEligibleWriteOff;
	use App\microfin\settings\MfnInsuranceCalculationMethod;
	use App\microfin\settings\MfnLoanProductType;
	use App\microfin\settings\MfnRepaymentFrequency;
	use App\microfin\settings\MfnInterestPaymentMode;
	use App\microfin\settings\MfnMonthlyRepaymentMode;
	use App\microfin\settings\MfnExtraOptions;
	use App\microfin\settings\MfnGracePeriod;
	use App\microfin\settings\MfnLoanProductInterestRate;
	
	use App\hr\Exam;

	use App\microfin\loan\MfnProduct;
	use App\microfin\loan\MfnProductCategory;
	use App\microfin\loan\MfnPurposeCategory;
	use App\microfin\loan\MfnPurpose;
	use App\microfin\loan\MfnSubPurpose;
	use App\microfin\loan\MfnInterestCalculationMethod;
	use App\microfin\loan\MfnLoan; 
	use App\microfin\loan\MfnLoanCollection; 
	use App\microfin\loan\MfnLoanRepayPeriod; 
	use App\microfin\loan\MfnLoanReschedule; 
	use App\microfin\loan\MfnLoanSchedule; 

	use App\microfin\savings\MfnSavingsProduct;
	use App\microfin\savings\MfnSavingsAccount;  
	use App\microfin\savings\MfnSavingsDeposit; 
	use App\microfin\savings\MfnSavingsWithdraw;

	use App\Traits\GetSoftwareDate;

	class MicroFinance {

		/**
		 * The message for empty table.
		 * 
		 * @return [string]
		 */
		public function dataNotAvailable() {

			return 'No Data Found';
		}

		/**
		 * [Funtion for return message text]
		 * 
		 * @param  [string] $str [A string contains event short name]
		 * @return [string]      [A string contains event message]
		 */
		public static function getMessage($str) {

			$msg = [
				'msgSuccess'    				 		  =>  'Success!',
				'msgWarning'    				 		  =>  'Warning!',
				'samityDel'     				 		  =>  'Your selected samity has been deleted successfully.',
				'samityNotDel'  				 		  =>  'Your can\'t delete this samity. It contains active members.',
				'memberCreateSuccess'     		 		  =>  'New member has been added to the system successfully.',
				'memberUpdateSuccess'     		 		  =>  'Member has been updated successfully.',
				'memberTypeCreateSuccess'     	 		  =>  'New Member Type has been saved successfully.',
				'memberTypeUpdateSuccess'     	 		  =>  'Member Type has been updated successfully.',
				'memberTypeDelSuccess'     		 		  =>  'Your selected Member Type deleted successfully.',
				'memberClosingSuccess'     		 		  =>  'Member has been closed successfully.',
				'primaryProductTransferSuccess'  		  =>  'Member primary product transfer has been completed successfully.',
				'primaryProductTransferWarning'  		  =>  'Unable to complete this transfer. It contains some overdated transactions.',
				'memberSamityTransferSuccess'    		  =>  'Your selected member has been transferred successfully.',
				'memberSamityTransferWarning'    		  =>  'Unable to complete this transfer. It contains some overdated transactions or transfer.',
				'primaryProductTransferWithMemberWarning' =>  'Unable to complete this transfer with new primary product. You already made a product transfer today. You can proceed this transfer with current primary product.',
				'regularLoanCreateSuccess'     	 		  =>  'New regular loan has been issued successfully.',
				'regularLoanUpdateSuccess'     	 		  =>  'Your regular loan has been updated successfully.',
				'regularLoanDelSuccess'     	 		  =>  'Your selected loan has been deleted successfully.',
				'regularLoanDelFailed'     	 		 	  =>  'Your can\'t delete this loan.',
				'oneTimeLoanCreateSuccess'     	 		  =>  'New one time loan has been issued successfully.',
				'oneTimeLoanDelSuccess'     	 		  =>  'Your selected loan has been deleted successfully.',
				'productInterestRateWarning'     		  =>  'You need to add product interest rate for this period.',
				'rescheduleCreateSuccess'     	 		  =>  'Installments have been rescheduled successfully.',
				'loanProductCreateSuccess'     	 		  =>  'New loan product has been saved successfully.',
				'productCategoryCreateSuccess'   		  =>  'Your new product category has been saved successfully.',
				'productCategoryUpdateSuccess'   		  =>  'Your new product category has been Update successfully.',
				'purposeCreateSuccess'   		 		  =>  'Your new loan purpose has been saved successfully.',
				'purposeUpdateSuccess'   		 		  =>  'Your new loan purpose has been update successfully.',
				'purposeCategoryCreateSuccess'   		  =>  'Your new purpose category has been saved successfully.',
				'purposeCategoryUpdateSuccess'   		  =>  'Your purpose category has been updated successfully.',
				'subPurposeCreateSuccess'   	 		  =>  'Your new sub purpose has been saved successfully.',
				'subPurposeUpdateSuccess'   	 		  =>  'Your new loan sub purpose has been Update successfully.',
				'loanRepayPeriodCreateSuccess'   		  =>  'Your new Loan Repay Period name has been saved successfully.',
				'loanRepayPeriodUpdateSuccess'   		  =>  'Your loan Repay Period name has been updated successfully.',
				'interestCalculationMethodCreateSuccess'  =>  'Your new interest calculation method has been saved successfully.',
				'interestCalculationMethodUpdateSuccess'  =>  'Your interest calculation method has been updated successfully.',
			];

			return $msg[$str];
		}

		public function getActiveBranchForBranchProductAssign() {

			$branches = GnrBranch::active()
								 ->where('loanProductId', '!=', '') 
								 ->orWhere('savingsProductId', '!=', '') 
								 ->get();

			return $branches;
		}

		/**
		 * [Function for return string status]
		 * 
		 * @param  [int] 	$status [Contains string status 'Active' or 'Inactive']
		 * @return [string]         [return status as string 'Active' or 'Inactive']
		 */
		public function getStatus($status) {

			$statusFlag = array(
				0  =>  'Inactive',
				1  =>  'Active'
			);

			if($status==1)
				return '<span style="color:#72A230">' . $statusFlag[$status] . '</span>';
			else
				return '<span style="color:#F00">' . $statusFlag[$status] . '</span>';
		}

		/**
		 * [Function for retrun a boolean string status]
		 * 
		 * @param  [int] 	$status [Contains boolean status '1' or '0']
		 * @return [string]         [Return boolean status as string 'Yes' or 'No']
		 */
		public function getBooleanStatus($status) {

			$booleanStatus = array(
				0  =>  'No',
				1  =>  'Yes'
			);

			return $booleanStatus[$status];
		}

		/**
		 * An array contains Boolean options.
		 * 
		 * @return [array]	[$booleanOptions]
		 */
		public function getBooleanOptions() {

			$booleanOptions = array(
				1  => 'Yes',
				0  => 'No'
			);

			return $booleanOptions;
		}

		public function FAIcon() {

			$FAIcon = array(
				'check' => '<i class="fa fa-check" aria-hidden="true"></i>', 
				'times' => '<i class="fa fa-times" aria-hidden="true"></i>'
			);

			return $FAIcon;
		}

		public function getAllBranchOptions() {

			$branch = DB::table('gnr_branch')
						->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
						->orderBy('branchCode')
						->get()
			            ->pluck('nameWithCode', 'id')
 					    ->all();

			return $branch;
		}

		public function getDefaultBranchOptions($arg) {

			$branch = DB::table('gnr_branch')
						->where('id','=', $arg)
						->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
						->get()
			            ->pluck('nameWithCode', 'id')
 					    ->all();

			return $branch;
		}

		public function getBranchOptions($arg) {

			$branch = DB::table('gnr_branch')
						->where('id','!=', $arg)
						->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
						->get()
			            ->pluck('nameWithCode', 'id')
 					    ->all();

			return $branch;
		}

		public function getBranchOptionsForBranchProductAssign($arg) {

			$branch = DB::table('gnr_branch')
						->where([['id', '>', $arg],
							     ['loanProductId', '=', ''],
								 ['savingsProductId', '=', '']
							    ])
						->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
						->get()
			            ->pluck('nameWithCode', 'id')
 					    ->all();

			return $branch;
		}

		/**
		 * An array contains Gender type.
		 * 
		 * @return [array]	[$gender]
		 */
		public function getGender() {

			$gender = array(
				'' =>  'Select', 
				1  =>  'Male', 
				2  =>  'Female'
			);

			return $gender;
		}

		/**
		 * [getGenderOptions A function taken a specific gender type id
		 * and return an array with options.]	
		 * 				
		 * @param  [int] 	$genderTypeId  [A Gender Type ID is given]
		 * @return [array]                 [An array with Gender Type options]
		 */
		public function getGenderOptions($genderTypeId) {

			$genderOptions = [];

			if($genderTypeId!=3):
				foreach(self::getGender() as $key => $val):
					if($key==$genderTypeId):
						$genderOptions[$key] = $val;
						break;
					endif;
				endforeach;
			else:
				$genderOptions = self::getGender();
			endif;

			return $genderOptions;
		}

		/**
		 * An array contains Marital Status options.
		 * 
		 * @return [array]	[$maritalStatus]
		 */
		public function getMaritalStatus() {

			$maritalStatus = array('' => 'Select') 
							 +
							 MfnMaritalStatus::active()->pluck('name', 'id')->all();

			return $maritalStatus;
		}

		/**
		 * An array contains Current Resident Type.
		 * 
		 * @return [array]	[$curResidentType]
		 */
		public function getCurResidentType() {

			$curResidentType = array(
				'' => 'Select',
				1  => 'Own',
				2  => 'Rent',
				3  => 'Lease'
			);

			return $curResidentType;
		}

		/**
		 * An object contains all the villages 
		 * of a specific branch.  
		 * 
		 * @param  [int]	 [$branchId]
		 * @return [object]	 [$villages]
		 */
		public function getVillages($branchId) {

			$villages = array('' => 'Select') 
						+ 
						DB::table('gnr_village')
					      ->where('branchId', $branchId)
						  ->pluck('name', 'id')
						  ->all();

			return $villages;
		}

		public function getAllDivisionOptions() {
			
			$division = GnrDivision::pluck('name', 'id')->all();

			return $division;
		}

		public function getDistrictOptions($divisionId) {
			
			$district = GnrDistrict::where('divisionId', $divisionId)->pluck('name', 'id')->all();

			return $district;
		}

		public function getUpzillaOptions($divisionId, $districtId) {
			
			$upzilla = GnrUpazila::where([['divisionId', $divisionId],
								 	      ['districtId', $districtId]
								 	     ])
								 ->pluck('name', 'id')
								 ->all();
			
			return $upzilla;
		}

		public function getUnionOptions($divisionId, $districtId, $upazilaId) {
			
			$union = GnrUnion::where([['divisionId', $divisionId],
							 		  ['districtId', $districtId],
							 		  ['upzillaId', $upazilaId]
							 		 ])
							 ->pluck('name', 'id')
							 ->all();

			return $union;
		}

		public function getVillageOptions($divisionId, $districtId, $upazilaId, $unionId) {
			
			$village = GnrVillage::where([['divisionId', $divisionId],
							              ['districtId', $districtId],
							     		  ['upzillaId', $upazilaId],
							     		  ['unionId', $unionId]
							     		 ])
							     ->pluck('name', 'id')
							     ->all();

			return $village;
		}

		public function getAllWorkingArea($branchId) {

			if($branchId==1):
				$workingAreas = GnrWorkingArea::get();
			else:
				$workingAreas = GnrWorkingArea::where('branchId', $branchId)->get();
			endif;

			return $workingAreas;
		}

		public function getWorkingAreaId($samityId) {

			$samityOB = MfnSamity::where('id', $samityId)->active()->select('workingAreaId')->first();

			return $samityOB->workingAreaId;
		}

		/**
		 * [Funtion for return Field Officer Name of given Samity ID]
		 * 
		 * @param  [int] 	  $samityId 			   [Samity ID]
		 * @return [string]   $fieldOfficerName        [Field Officer name]
		 */
		public function getFieldOfficerName($samityId) {

			$fieldOfficerNameOB = DB::table('mfn_samity')
                					->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
                					->select('hr_emp_general_info.emp_name_english')
                					->where('mfn_samity.id', $samityId)
                					->first(); 

			$fieldOfficerName = $fieldOfficerNameOB->emp_name_english;

			return $fieldOfficerName;
		}

		public function getFieldOfficerNameOptionOfSamity($samityId) {

			$fieldOfficerName = DB::table('mfn_samity')
                				  ->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
                				  ->where('mfn_samity.id', $samityId)
                				  ->select(DB::raw("CONCAT(hr_emp_general_info.emp_name_english, ' - ', hr_emp_general_info.emp_id) AS nameWithCode"), 'hr_emp_general_info.id')
						          ->get()
                				  ->pluck('nameWithCode', 'id')
                				  ->all(); 

			return $fieldOfficerName;
		}

		/**
		 * [Funtion for return Field Officer Code of given Samity ID]
		 * 
		 * @param  [int] 	  $samityId 			   [Samity ID]
		 * @return [string]   $fieldOfficerCode        [Field Officer Code]
		 */
		public function getFieldOfficerCode($samityId) {

			$fieldOfficerCodeOB = DB::table('mfn_samity')
                					->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
                					->select('hr_emp_general_info.emp_id')
                					->where('mfn_samity.id', $samityId)
                					->first(); 

			$fieldOfficerCode = $fieldOfficerCodeOB->emp_id;

			return $fieldOfficerCode;
		}

		/**
		 * [Function for return all the Field Officers list]
		 * 
		 * @return [object] [An object contains all the Field Officers list]
		 */
		public function getFieldOfficerList() {

			$checkExistsFieldOfficer = DB::table('hr_emp_org_info')
					                     ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
					                     ->where('hr_emp_org_info.branch_id_fk', Auth::user()->branchId)
					                     ->count(); 

			if($checkExistsFieldOfficer>0):
				$fieldOfficerListOB = DB::table('hr_emp_org_info')
					                    ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
					                    ->where('hr_emp_org_info.branch_id_fk', Auth::user()->branchId)
					                    ->select(DB::raw("CONCAT(hr_emp_general_info.emp_name_english, ' - ', hr_emp_general_info.emp_id) AS nameWithCode"), 'hr_emp_general_info.id')
					                    ->get()
					                    ->pluck('nameWithCode', 'id')
		 							    ->all();
			else:
				$fieldOfficerListOB = [];
			endif;
		
			return $fieldOfficerListOB;
		}

		public function getFieldOfficerListAll() {

			$checkExistsFieldOfficer = DB::table('hr_emp_org_info')
					                     ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
					                     ->count(); 
			
			if($checkExistsFieldOfficer>0):
				$fieldOfficerListOB = DB::table('hr_emp_org_info')
						                ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
						                ->select(DB::raw("CONCAT(hr_emp_general_info.emp_name_english, ' - ', hr_emp_general_info.emp_id) AS nameWithCode"), 'hr_emp_general_info.id')
						                ->get()
						                ->pluck('nameWithCode', 'id')
			 							->all();
			else:
				$fieldOfficerListOB = [];
			endif;
		
			return array(''	=>	'Select') + $fieldOfficerListOB;
		}

		public function getFieldOfficerOptionBranchWise($branchId) {

			$checkExistsFieldOfficer = DB::table('mfn_samity')
					                     ->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
					                     ->where('mfn_samity.branchId', $branchId)
					                     ->count(); 

			if($checkExistsFieldOfficer>0):
				$fieldOfficerListOB = DB::table('mfn_samity')
					                    ->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
					                    ->where('mfn_samity.branchId', $branchId)
					                    ->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ', hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
					                    ->get()
					                    ->pluck('nameWithCode', 'id')
		 							    ->all();
			else:
				$fieldOfficerListOB = [];
			endif;
		
			return $fieldOfficerListOB;
		}

		public function getFieldOfficerListBranchWiseFORR($branchId) {

			$fieldOfficerList = [];

			$checkExistsFieldOfficer = DB::table('mfn_samity')
					                     ->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
					                     ->where('mfn_samity.branchId', $branchId)
					                     ->count(); 

			if($checkExistsFieldOfficer>0):
				$fieldOfficerListOB = DB::table('mfn_samity')
					                    ->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
					                    ->where('mfn_samity.branchId', $branchId)
					                    ->select('hr_emp_general_info.id', 'hr_emp_general_info.emp_id', 'hr_emp_general_info.emp_name_english')
					                    ->distinct()
					                    ->get();

				$i = 0;
				foreach($fieldOfficerListOB as $fieldOfficer):
					$fieldOfficerList[$i] = (array) $fieldOfficer;
					$i++;
				endforeach;
			else:
				$fieldOfficerList = [];
			endif;
		
			return $fieldOfficerList;
		}

		public function getSamityListFieldOfficerWiseFORR($fieldOfficerId) {

			$samityListOB = DB::table('mfn_samity')
							  ->where('fieldOfficerId', $fieldOfficerId)
							  ->select('id', 'code', 'name')
							  ->get();
			
			$samityList = [];

			$i = 0;
			foreach($samityListOB as $samity):
				$samityList[$i] = (array) $samity;
				$i++;
			endforeach;

			return $samityList;			
		}

		public function getMemberListSamityWiseFORR($samityId) {

			$memberListOB = DB::table('mfn_member_information')
							  ->where('samityId', $samityId)
							  ->select('id', 'code', 'name')
							  ->get();
			
			$memberList = [];

			$i = 0;
			foreach($memberListOB as $member):
				$memberList[$i] = (array) $member;
				$i++;
			endforeach;

			return $memberList;			
		}

		public function getComponentNameMemberWiseFromLoanFORR($memberId) {

			$componentName = DB::table('mfn_loan')->where('memberIdFk', $memberId)->value('primaryProductIdFk');

			return $componentName;
		}

		/**
		 * [Function for return Village name of given Village ID]
		 * 
		 * @param  [int] 		$villageId 			[Village ID]
		 * @return [string]    $villageName        [Village name]
		 */
		public function getVillageName($villageId) {

			$villageNameOB = DB::table('gnr_village')
						 	   ->where('id', $villageId)
						 	   ->select('name')
						 	   ->first();

			$villageName = $villageNameOB->name;

			return $villageName;
		}

		/**
		 * An array contains Country options.
		 * 
		 * @return [array]	[$country]
		 */
		public function getCountry() {

			$country = array(
				'bangladesh' => 'Bangladesh'
			);

			return $country;
		}

		public function getActiveSamity() {

			if(Auth::user()->branchId==1):
				$samity = MfnSamity::active()->get();
			else:
				$samity = MfnSamity::active()->branchWise()->get();
			endif;

			return $samity;
		}

		/**
		 * An object contains Samity.
		 * 
		 * @return [object]	 [$samity]
		 */
		public function getSamity() {

			$samity = array('' => 'Select') 
					  + 
					  MfnSamity::active()
					  		   ->branchWise()
					  		   ->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
						       ->get()
					  		   ->pluck('nameWithCode', 'id')
					  		   ->all();

			return $samity;
		}

		public function getDayWiseSamityOptions($branchId, $samityDayId) {

			$dayWiseSamity = MfnSamity::active()
						     	      ->where([['branchId', $branchId],
						     	      		   ['samityDayId', $samityDayId]
						     	      		  ])
						  		      ->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
							          ->get()
						  		      ->pluck('nameWithCode', 'id')
						  		      ->all();

			return $dayWiseSamity;
		}

		/**
		 * [Function for return Samity Name of given Samity ID]
		 * 
		 * @param  [int] 	  $samityId 		 [Samity ID]
		 * @return [string]   $samityName        [Samity Name]
		 */
		public function getSamityName($samityId) {

			$samityNameOB = DB::table('mfn_samity')
						      ->select('name')
						      ->where('id', $samityId)
							  ->first();

			return $samityNameOB->name;	
		}

		public function getSamityNameWithCode($samityId) {

			$samityNameOB = DB::table('mfn_samity')
							  ->where('id', $samityId)
							  ->select(DB::raw("CONCAT(name, ' - ', code) AS nameWithCode"))
							  ->first();

			return $samityNameOB->nameWithCode;	
		}

		/**
		 * [Function for return Samity Type Name]
		 * 
		 * @return [array] [An array contains Samity Type Name]
		 */
		public function getSamityType() {

			$samityType = array(
				1  =>  'Male', 
				2  =>  'Female',
				//3  =>  'Both'
			);

			return $samityType;
		}

		/**
		 * [Function for return Samity Type name]
		 * 
		 * @param  [int] 	$samityTypeId [Samity Type ID]
		 * @return [string]               [Samity Type Name]
		 */
		public function getSamityTypeName($samityTypeId) {

			$samityType = array(
				1  =>  'Male', 
				2  =>  'Female',
				3  =>  'Both'
			);

			return $samityType[$samityTypeId];
		}

		/**
		 * [Function for return is Samity Day Obsolete or not]
		 * [Note: If the value is '1' Samity Day not Obsolete otherwise Obsolete]
		 * 
		 * @param  [int] 	$status [Samity Day Obsolete ID]
		 * @return [string]         [Samity Day Obsolete Status]
		 */
		public function getSamityDayObsoleteStatus($status) {

			$samityDayObsoleteStatus = array(
				0  => '-',
				1  => 'No',
				2  => 'Yes'
			);

			return $samityDayObsoleteStatus[$status];
		}

		/**
		 * [Function for return Samity Day Name or Fixed Date with some string]
		 * 
		 * @param  [int] 	$samityDayId [Samity Day ID]
		 * @param  [int] 	$fixedDate   [Fixed Date value]
		 * @return [string]              [A string contains Samity Day Name or Fixed Date with some string]
		 */
		public function getSamityDayName($samityDayId, $fixedDate) {

			$samityDay = array(
				1 => 'Saturday', 
				2 => 'Sunday', 
				3 => 'Monday', 
				4 => 'Tuesday', 
				5 => 'Wednesday', 
				6 => 'Thursday', 
				7 => 'Friday'
			);

			$samityFixedDate = array(
				0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25,26, 27, 28, 29, 30, 31
			);

			$samityDaySuperScript = array(
				1 => 'st', 
				2 => 'nd', 
				3 => 'rd', 
				4 => 'th'
			);

			if($samityDayId>=1 && $samityDayId<=7):
				return $samityDay[$samityDayId] . '&nbsp;' . '(Samity Day)';
			else:
				if($samityFixedDate[$fixedDate]==1):
					$formattedFixedDate = $samityFixedDate[$fixedDate] . $samityDaySuperScript[$samityFixedDate[$fixedDate]] . ' Day';
				elseif($samityFixedDate[$fixedDate]==2):
					$formattedFixedDate = $samityFixedDate[$fixedDate] . $samityDaySuperScript[$samityFixedDate[$fixedDate]] . ' Day';
				elseif($samityFixedDate[$fixedDate]==3):
					$formattedFixedDate = $samityFixedDate[$fixedDate] . $samityDaySuperScript[$samityFixedDate[$fixedDate]] . ' Day';
				elseif($samityFixedDate[$fixedDate]>=4 || $samityFixedDate[$fixedDate]<=31):
					$formattedFixedDate = $samityFixedDate[$fixedDate] . $samityDaySuperScript[4] . ' Day';
				endif;
				
				return $formattedFixedDate . '&nbsp;' . '(Fixed Date)';
			endif;
		}

		/**
		 * [Function for return Samity Day Name]
		 * 
		 * @return [array] [An array contains Samity Day Name]
		 */
		public function getSamityDay() {

			$samityDay = array(
				1 => 'Saturday', 
				2 => 'Sunday', 
				3 => 'Monday', 
				4 => 'Tuesday', 
				5 => 'Wednesday', 
				6 => 'Thursday', 
				7 => 'Friday'
			);
			
			return $samityDay;
		}

		public function getSamityDayNameValue($samityDayId) {

			$samityDay = array(
				1 => 'Saturday', 
				2 => 'Sunday', 
				3 => 'Monday', 
				4 => 'Tuesday', 
				5 => 'Wednesday', 
				6 => 'Thursday', 
				7 => 'Friday'
			);
			
			return $samityDay[$samityDayId];
		}

		public function getSamityDayId($samityDayName) {

			foreach(self::getSamityDay() as $key => $val):
				if($samityDayName==$val):
					$samityDayId = $key;
					break;
				endif;
			endforeach;
			
			return $samityDayId;
		}

		public function getSamityDayIdOfSamity($samityId) {

			return DB::table('mfn_samity')->where('id', $samityId)->value('samityDayId');
		}

		/**
		 * [Function for return Samity Day Superscript Name]
		 * 
		 * @return [array] [An array contains Samity Day Superscript Name]
		 */
		public function getSamityDaySuperScript() {

			$samityDaySuperScript = array(
				1 => 'st', 
				2 => 'nd', 
				3 => 'rd', 
				4 => 'th'
			);

			return $samityDaySuperScript;
		}

		/**
		 * [Function for return Samity Day Class name]
		 * 
		 * @return [array] [An array contains Samity Day Class name]
		 */
		public function getSamityDayClass() {

			$samityDayClass = array(
				1  =>  'According To Samity Day', 
				2  =>  'Samity Day Obsolete'
			);

			return $samityDayClass;
		}

		/**
		 * [Function for return Samity Date Type name]
		 * 
		 * @return [array] [An array contains Samity Date Type name]
		 */
		public function getSamityDateType() {

			$samityDateType = array(
				1  =>  'Samity Day', 
				2  =>  'Fixed Date'
			);

			return $samityDateType;
		}

		/**
		 * [Function for return all the days of a month]
		 * 
		 * @return [array] [An array contains all the days of a month]
		 */
		public function getSamityFixedDate() {

			$samityFixedDate = array(
				0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31
			);

			//	WARNING! DON'T REMOVE THIS LINE.
			unset($samityFixedDate[0]);

			return array('' => 'Select') + $samityFixedDate;
		}

		/**
		 * [Function for return Samity Code of given Samity ID]
		 * 
		 * @param  [int] 		$samityId 			[Samity ID]
		 * @return [string] 	$samityCode         [Sammity Code]
		 */
		public function getSamityCode($samityId) {

			$samityCodeOB = DB::table('mfn_samity')
						      ->select('code')
						      ->where('id', $samityId)
							  ->first();

			$samityCode = $samityCodeOB->code;	

			return $samityCode;
		}

		/**
		 * [Function for return Samity details]
		 * 
		 * @param  [int] 	 $samityId 		[Samity ID]
		 * @return [object]           		[A Samity Object]
		 */
		public function getSamityDetails($samityId) {

			$samityDetailsOBJ = DB::table('mfn_samity')
								  ->where('id', $samityId)
								  ->first();
			
			return $samityDetailsOBJ;
		}

		public function getSamityOptionsSingle($samityId) {

			$samity = DB::table('mfn_samity')->where('id', $samityId)->pluck('name', 'id');

			return $samity;
		}

		public function getEmployeeList() {

			if(Auth::user()->branchId==1):
				$branchId = 1;
				$sql = 'hr_emp_org_info.branch_id_fk' . '>=' . $branchId;
			else:
				$branchId = Auth::user()->branchId;
				$sql = 'hr_emp_org_info.branch_id_fk' . '=' . Auth::user()->branchId;
			endif;
		
			
			$employeeListOB = DB::table('hr_emp_org_info')
								->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
								->whereRaw($sql)
					            ->where('hr_emp_org_info.project_id_fk', 1)
					            ->select('hr_emp_general_info.photo AS photo', 
					            		 'hr_emp_general_info.emp_name_english AS name', 
					            	     'hr_emp_general_info.emp_id AS empId',
					            	     'hr_emp_general_info.blood_group AS bloodGroup',
					            	     'hr_emp_general_info.mobile_number AS mobileNumber',
					            	     'hr_emp_org_info.branch_id_fk AS branchId',
					            	     'hr_emp_org_info.joining_date AS joiningDate',
					            	     'hr_emp_org_info.position_id_fk AS positionId',
					            	     'hr_emp_org_info.status AS status'
					            	    )
					            ->orderBy('hr_emp_org_info.joining_date')
							    ->get();

			return $employeeListOB;
		}

		/**
		 * [An object contains Primary Product]
		 * 
		 * @return [object]	[$primaryProduct]
		 */
		public function getPrimaryProduct() {

			$primaryProduct = array('' => 'Select') 
							  +
							  MfnPrimaryProduct::active()->pluck('name', 'id')->all();
			
			return $primaryProduct;
		}

		public function getPrimaryProductWithFundingOrganizationBranchWise() {

			$branchProductExists = GnrBranch::branchWise()->active()->select('id')->where('loanProductId', '!=' ,'')->count();
			
			//	GET ALL THE LOAN PRODUCT WHICH ARE ASSIGNED TO BRANCH.
			$branchProduct = GnrBranch::branchWise()->active()->select('loanProductId')->first()->toArray();
			
			//$branchProductExists = 0;

			//	GET ALL THE PRIMARY LOAN PRODUCT.
			$globalPrimaryProduct = MfnProduct::primaryProduct()->active()->select('id', 'shortName', 'fundingOrganizationId')->get();
			
			foreach($globalPrimaryProduct as $primaryProduct):
				$primaryProduct['shortName'] .=  ' (' . self::getNameValueForId($table="mfn_funding_organization", $primaryProduct['fundingOrganizationId']) . ')';
			endforeach;

			$globalPrimaryProduct = $globalPrimaryProduct->toArray();
			$globalPrimaryProduct = array_column($globalPrimaryProduct, 'shortName', 'id');

			$branchPrimaryProduct = [];

			//	GET ALL THE PRIMARY LOAN PRODUCT WHICH ARE ASSIGNED TO BRANCH.
			if($branchProductExists>0):
				foreach($globalPrimaryProduct as $key => $val):
					if(in_array($key, $branchProduct['loanProductId']))
						$branchPrimaryProduct[$key] = $val;
				endforeach;
			endif;
			
			return $branchPrimaryProduct;
		}

		/**
		 * [Function for retrun Clsoing Date if exists, otherwise retrun dash '-']
		 * 
		 * @param  [int] 	$samityId 	[Samity ID]
		 * @param  [int] 	$status   	[Status]
		 * @return [string]           	[If Closing Date exists, return it, otherwise retrun dash '-']
		 */
		public function getSamityClosingDate($samityId, $status) {

			if($status==0):
				$samityClosingOB = DB::table('mfn_samity_closing')
									 ->where('samityId', $samityId)
									 ->select('closingDate')
									 ->first();

				return date_format(date_create($samityClosingOB->closingDate), "d-m-Y");
			else:
				return '-';
			endif;
		}

		/**
		 * [An array contains Mandatory Savings Product option]
		 * 
		 * @return [array] [$mandatorySavingsProduct]
		 */
		public function getMandatorySavingsProduct() {

			$mandatorySavingsProduct = [];

			$checkExistsSavingsProduct = DB::table('mfn_saving_product')
										   ->where('depositTypeIdFk', 1)
										   ->count();
			
			if($checkExistsSavingsProduct>0):
				$mandatorySavingsProductOB = DB::table('mfn_saving_product')
											   ->where('depositTypeIdFk', 1)
									           ->select('id', 'shortName', 'code')
									           ->first();

				$mandatorySavingsProduct = array(
					$mandatorySavingsProductOB->id 	=>  $mandatorySavingsProductOB->shortName . '-' . str_pad($mandatorySavingsProductOB->code, 2, 0, STR_PAD_LEFT)
				);
			endif;

			return array('' => 'Select') + $mandatorySavingsProduct;
		}

		/**
		 * An object contains Education Level options.
		 * 
		 * @return [object]	 [$educationLevel]
		 */
		public function getEducationLevel() {

			$educationLevel = array('' => 'Select') 
							  +
							  Exam::where('status', 1)->pluck('name', 'id')->all();
			
			return $educationLevel;
		}

		/**
		 * [An object contains Relationship options]
		 * 
		 * @return [object]	[$relationship]
		 */
		public function getRelationship() {

			$relationship = array('' => 'Select') 
							+
							MfnRelationship::active()->pluck('name', 'id')->all();

			return $relationship;
		}

		/**
		 * [An object contains Designation options for Referrer]
		 * 
		 * @return [object]	[$country]
		 */
		public function getDesignationOfReferrer() {

			$designation = array('' => 'Select') 
						   +
						   MfnDesignation::active()->pluck('name', 'id')->all();

			return $designation;
		}

		/**
		 * An object contains Active Members.
		 * 
		 * @return [object]	[$members]
		 */
		public function getActiveMembers() {

			if(Auth::user()->branchId==1):
				$members = MfnMemberInformation::active()->get();
			else:
				$members = MfnMemberInformation::active()->branchWise()->get();
			endif;

			return $members;
		}

		/**
		 * [Function for return all the information of a member]
		 * 
		 * @param  [int] 	 $memberId 			 [Member ID]
		 * @return [object]  $memberDetailsOBJ   [A member object]
		 */
		public function getMembersDetails($memberId) {

			$memberDetailsOBJ = DB::table('mfn_member_information')
								  ->where('id', $memberId) 
								  ->first();
	
			return $memberDetailsOBJ;
		}

		public function getMemberOptions($branchId) {

			$memberList = MfnMemberInformation::active()->where('branchId', $branchId)->pluck('name', 'id')->all();

			return array(''	=> 'Select') + $memberList;
		}

		public function getMemberOptionsSingle($memberId) {

			$member = MfnMemberInformation::active()->where('id', $memberId)->pluck('name', 'id');

			return $member;
		}

		public function getMemberOptionsForLoan($branchId) {

			$memberList = DB::table('mfn_member_information')
							->where([['status', 1], 
								     ['softDel', 0], 
								     ['branchId', $branchId], 
								     ['admissionDate', '<=', GetSoftwareDate::getSoftwareDate()]
								    ])
						    //->select('id', DB::raw("CONCAT(name, ' - ', code) AS nameWithCode"), 'branchId', 'samityId')
						    ->select('id', 'name', 'code', 'branchId', 'samityId')
						    ->get();

			$member = [];

			$i = 0;
			foreach ($memberList as $members) {
				$member[$i]['id'] = $members->id;
				$member[$i]['memberName'] = $members->name;
				$member[$i]['memberCode'] = $members->code;
				
				$branchOB = self::getMultipleValueForId($table='gnr_branch', $members->branchId, ['name', 'branchCode']);
				$member[$i]['branchName'] = $branchOB->name . ' - ' . sprintf('%03d', $branchOB->branchCode);
				
				$samityOB = self::getMultipleValueForId($table='mfn_samity', $members->samityId, ['name', 'code', 'workingAreaId']);
				$member[$i]['samityName'] = $samityOB->name . ' - ' . $samityOB->code;

				$member[$i]['workingArea'] = self::getNameValueForId($table='gnr_working_area', $samityOB->workingAreaId);
				$i++;
			}

			return $member;
		}

		/**
		 * [An object contains Member Type options]
		 * 
		 * @return [object] [$memberType]
		 */
		public function getMemberType() {

			$memberType = MfnMemberType::active()->pluck('name', 'id')->all();

			return $memberType;
		}

		public function getMemberPrimaryProductTransfer() {

			if(Auth::user()->branchId==1):
				$memberPrimaryProductTransfer = MfnMemberPrimaryProductTransfer::active()->get();
			else:
				$memberPrimaryProductTransfer = MfnMemberPrimaryProductTransfer::active()->branchWise()->get();
			endif;
			
			return $memberPrimaryProductTransfer;
		}

		public function getMemberPrimaryProductTransferDetails($memberPrimaryProductTransferId) {

			$memberDetailsOBJ = DB::table('mfn_loan_primary_product_transfer')->where('id', $memberPrimaryProductTransferId)->first();
	
			return $memberDetailsOBJ;
		}

		public function getSavingsCodeOfSavingsAccount($savingsAccountId) {

			return MfnSavingsAccount::where('id', $savingsAccountId)->value('savingsCode');
		}

		public function getActiveMemberSamityTransfer() {

			if(Auth::user()->branchId==1):
				$memberSamityTransfer = MfnMemberSamityTransfer::active()->get();
			else:
				$memberSamityTransfer = MfnMemberSamityTransfer::active()->branchWise()->get();
			endif;

			return $memberSamityTransfer;
		}

		public function getActiveMemberClosing() {

			if(Auth::user()->branchId==1):
				$memberClosing = MfnMemberClosing::active()->get();
			else:
				$memberClosing = MfnMemberClosing::active()->branchWise()->get();
			endif;

			return $memberClosing;
		}


		/**
		 * [memberIdDecoder description]
		 * 
		 * @param  [string] $memberId 		[Encoded format of Member ID]
		 * @return [int]    $memberId       [Decoded format of Member ID]
		 */
		public function getNumericValueDecoder($val) {

			try {
			   	
			   	//$decodedVal = decrypt($val);
			   	$decodedVal = $val;

			} catch(DecryptException $e) {
				
				echo 'Exception Caught';
			}

			return $decodedVal;
		}

		/**
		 * [An object contains Member's Profession options]
		 * 
		 * @return [object] [$professions]
		 */
		public function getProfessionOfMember() {

			$professions = array('' => 'Select') 
						   +
						   MfnProfession::active()->pluck('name', 'id')->all();

			return $professions;
		}

		/**
		 * [An array contains Religion options]
		 * 
		 * @return [array] [$religion]
		 */
		public function getReligion() {

			$religion = array(
				''   		=> 'Select',
				'islam'  	=> 'Islam',
				'christian' => 'Christian',
				'hindu'  	=> 'Hindu',
				'buddhism'  => 'Buddhism',
				'judaism'   => 'Judaism',
				
			);

			return $religion;
		}

		/**
		 * [Function for return a set of requested field's value of a requested id of a requested table]
		 * @param  [string]  $table        [Requested table name]
		 * @param  [int] 	 $branchId     [Requested branch id]
		 * @param  [array]   $dataRequest  [An array that contains requested field's of the table]
		 * @return [object]                [An object that contains requested field's value]
		 */
		public function getDetails($table, $branchId, $dataRequest) {


			if(count($dataRequest)==0)
				$requestOB = DB::table($table)->where('id', $branchId)->first();
							
			if(count($dataRequest)>=1)
				$requestOB = DB::table($table)->where('id', $branchId)->select($dataRequest)->first();

			return $requestOB;
		}

		/**
		 * Image filename encoded by the following format.
		 * IMAGE NAME . MEMBER CODE [10] . BRANCH ID [4] . EXT
		 * 
		 * @param  [string]		$filename 
		 * @param  [string]		$EXT
		 * @param  [int]		$branchId
		 * @param  [string]		$memberCode
		 * @return [string]		$filename
		 */
		public function imageNameEncoder($filename, $EXT, $branchId, $memberCode) {

			$nameWithoutEXT = str_replace(array($EXT, '.'), '', $filename);
	        $filename = base64_encode($nameWithoutEXT);
	        $filename .= sprintf('%010d', $memberCode);
	        $filename .= sprintf('%04d', $branchId);
	        $filename = base64_encode($filename);
	        $filename .= '.' . $EXT;
			
			return $filename;
		}

		/**
		 * Image filename decoded by the following format 
		 * IMAGE NAME . MEMBER CODE [10] . BRANCH ID [4] . EXT
		 * 
		 * @param  [string]		$filename
		 * @param  [string]		$EXT
		 * @return [string]		$filename
		 */
		public function imageNameDecoder($filename, $EXT) {

			$filename = str_replace(array($EXT, '.'), '', $filename);
			$filename = base64_decode($filename);
			$filename = substr($filename, 0, -14);
			$filename = base64_decode($filename);
			$filename .= '.' . $EXT;
			
			return $filename;
		}

		/**
		 * [Function for retrun User fullname string]
		 * 
		 * @return [string] [User fullname string]
		 */
		public function getEntryByName($entryById) {

			$employeeOB = DB::table('hr_emp_general_info')->select('emp_name_english')->where('id', $entryById)->first();

			return $employeeOB->emp_name_english;
		}

		/**
		 * [Function for return Mandatory Savings Details of a member]
		 * 
		 * @param  [int] 	$memberId [Member Id]
		 * @return [object]           [An object contains Mandatory Savings Details of a member]
		 */
		public function getMandatorySavingsDetails($memberId) {

			$mandatorySavingsDetailsOB = DB::table('mfn_savings_account')
										   ->join('mfn_saving_product', 'mfn_savings_account.savingsProductIdFk', '=', 'mfn_saving_product.id')
										   ->join('mfn_savings_deposit_type', 'mfn_saving_product.depositTypeIdFk', '=', 'mfn_savings_deposit_type.id')
										   ->select('mfn_savings_account.savingsCode AS savingsCode', 
										   			'mfn_savings_account.savingsInterestRate AS interestRate', 
											 	    'mfn_savings_account.autoProcessAmount AS openingAmount', 
											 	    'mfn_savings_account.accountOpeningDate AS accountOpeningDate', 
											 	    'mfn_savings_deposit_type.name AS savingsType'
											 	   )
										   ->where([['mfn_savings_account.memberIdFk', $memberId],
										   	        ['mfn_saving_product.depositTypeIdFk', 1]
										   		   ])
										   ->first();

			return $mandatorySavingsDetailsOB;
		}

		/**
		 * [Function for return String contains Savings Product short name]
		 * 
		 * @param  [int] 	$savingsProductId [Saving Product ID]
		 * @return [string]                   [String contains Savings Product short name]
		 */
		public function getSavingsProductShortName($savingsProductId) {

			$savingsProductOB = DB::table('mfn_saving_product')
								  ->select('shortName')
								  ->where('id', $savingsProductId)
								  ->first(); 

			return $savingsProductOB->shortName;
		}

		/**
		 * [Function for retrun Savings Product Deposit type name] 
		 * 
		 * @param  [int] 	$savingsProductId [Savings Product ID]
		 * @return [string]                   [String contains Savings Product Deposit type name]
		 */
		public function getSavingsProductDepositType($savingsProductId) {

			$savingsProductOB = DB::table('mfn_saving_product')
							      ->join('mfn_savings_deposit_type', 'mfn_saving_product.depositTypeIdFk', '=', 'mfn_savings_deposit_type.id')
							      ->where('mfn_saving_product.id', $savingsProductId)
								  ->select('mfn_savings_deposit_type.name AS savingsProductDepositType')
								  ->first();

			return $savingsProductOB->savingsProductDepositType;
		}

		/**
		 * [Function for return Savings Product interest rate]
		 * 
		 * @param  [int] $savingsProductId [Savings Product ID]
		 * @return [int ]                  [Savings Product interest rate]
		 */
		public function getSavingsProductInterestRate($savingsProductId) {

			$savingsProductOB = DB::table('mfn_saving_product')
							      ->where('id', $savingsProductId)
								  ->select('interestRate')
							      ->first();

			return $savingsProductOB->interestRate;
		}

		public function getLoanProductShortName($loanProductId) {

			$loanProductShortName = self::getMultipleValueForId($table='mfn_loans_product', $loanProductId, ['shortName']);
			
			return $loanProductShortName->shortName;
		}

		/**
		 * [Function for return Savings Products Details]
		 * 
		 * @param  [int] 	$memberId [Member ID]
		 * @return [object]           [An object contains Savings Products Details]
		 */
		public function getSavingsProductsDetails($memberId) {

			// GET PRIMARY PRODUCT ID OF THE MEMBER.
			$memberOB = self::getMultipleValueForId($table='mfn_member_information', $memberId, ['primaryProductId']);

			//	GET THE TRANSFER DATE OF THE CURRENT PRIMARY PRODUCT.
			$primaryProductTransferDate = self::getLatestPrimaryProductTransferDate($memberId);

			$savingsProductsDetailsOB = DB::table('mfn_savings_account')
										  ->where('memberIdFk', $memberId)
										  ->select('id', 
										  	       'savingsCode', 
										  	       'accountOpeningDate', 
										  	       'savingsProductIdFk', 
												   'autoProcessAmount', 
												   'savingsInterestRate', 
												   'memberIdFk'
												  )
										  ->get();

			foreach($savingsProductsDetailsOB as $savingsProductsDetails):
				$savingsProductShortName = self::getSavingsProductShortName($savingsProductsDetails->savingsProductIdFk);
				$savingsProductDepositType = self::getSavingsProductDepositType($savingsProductsDetails->savingsProductIdFk);
				$savingsProductInterestRate = self::getSavingsProductInterestRate($savingsProductsDetails->savingsProductIdFk);
				/*$savingsTotalDepositeAmount = self::getSavingsDepositPerAccount($savingsProductsDetails->id) - 
											  self::getSavingsWithdrawPerAccount($savingsProductsDetails->id);*/

				$savingsTotalDepositeAmount = self::getSavingsDepositPerAccount($savingsProductsDetails->id, $memberOB->primaryProductId, $primaryProductTransferDate) - 
											  self::getSavingsWithdrawPerAccount($savingsProductsDetails->id, $memberOB->primaryProductId, $primaryProductTransferDate);

				$savingsProduct = $savingsProductShortName . str_repeat("&nbsp;",1) . '[' . strtoupper($savingsProductDepositType) . ']' . 
								  str_repeat("&nbsp;",1) . ' - ' . str_repeat("&nbsp;",1) . sprintf("%.2f", $savingsProductInterestRate) . 
								  str_repeat("&nbsp;",1) .'%';

				$savingsProductsDetails->savingsProduct = $savingsProduct;
				$savingsProductsDetails->savingsCode .= str_repeat("&nbsp;",1) . '[cyc - 1]';
				$savingsProductsDetails->accountOpeningDate  = date_format(date_create($savingsProductsDetails->accountOpeningDate), "d-m-Y");
				$savingsProductsDetails->autoProcessAmount = sprintf('%.2f', $savingsProductsDetails->autoProcessAmount);
				$savingsProductsDetails->savingsTotalDepositeAmount = $savingsTotalDepositeAmount;
			endforeach;

			return $savingsProductsDetailsOB;
		}

		public function getLoanProductsDetails($memberId) {

			$loanProductsDetailsOB = DB::table('mfn_loan')
									   ->where('memberIdFk', $memberId)
									   ->select('id',
									   			'loanTypeId', 
									 	        'loanCode',
									 	        'loanCycle',
									 	        'disbursementDate',
									 	        'productIdFk',
									 	        'firstRepayDate',
									 	        'loanAmount',
									 	        'repaymentNo',
									 	        'insuranceAmount',
									 	        'interestCalculationMethod',
									 	        'interestRate',
									 	        'totalRepayAmount',
									 	        'interestAmount',
									 	        'installmentAmount'
									 	       )
									   ->get();

			foreach($loanProductsDetailsOB as $loanProductsDetails):
				$loanProductsDetails->disbursementDate = self::getMicroFinanceDateFormat($loanProductsDetails->disbursementDate);
				$loanProductsDetails->firstRepayDate = self::getMicroFinanceDateFormat($loanProductsDetails->firstRepayDate);
				$loanProductsDetails->productName = self::getLoanProductShortName($loanProductsDetails->productIdFk);
				$loanProductsDetails->loanOutstanding = self::getRegularLoanOutstanding($loanProductsDetails->id, $loanProductsDetails->loanAmount);
				$loanProductsDetails->loanRecoveryAmount = self::getLoanPayment($loanProductsDetails->id);
				$loanProductsDetails->loanAdvance = self::getRegularLoanAdvance($loanProductsDetails->id);
				$loanProductsDetails->loanDue = self::getRegularLoanDue($loanProductsDetails->id, $loanProductsDetails->installmentAmount);
			endforeach;
			
			return $loanProductsDetailsOB;
		}

		

		/**
		 * [Function for return Mandatory Savings Transaction details]
		 * 
		 * @param  [int] 	$memberId [Member ID]
		 * @return [object]           [An object contains Mandatory Savings Transaction details]
		 */
		public function getMandatorySavingsTransaction($savingsAccountId) {

			$savingsDepositTransactionOB = DB::table('mfn_savings_deposit')
										     ->where('accountIdFk', $savingsAccountId)
										     ->select('depositDate AS transactionDate', 'paymentType', 'amount as depositAmount')
										     ->get()
										     ->toArray();

			$savingsDepositBalance = 0;
			$totalDeposit = 0;

			foreach($savingsDepositTransactionOB as $savingsDepositTransaction):
				$totalDeposit += $savingsDepositTransaction->depositAmount;
				$savingsDepositTransaction->withdrawAmount = 0;
			endforeach;

			$savingsWithdrawTransactionOB = DB::table('mfn_savings_withdraw')
										      ->where('accountIdFk', $savingsAccountId)
										      ->select('withdrawDate AS transactionDate', 'paymentType', 'amount as withdrawAmount')
										      ->get()
										      ->toArray();

			$totalWithdraw = 0;
			
			foreach($savingsWithdrawTransactionOB as $savingsWithdrawTransaction):
				$totalWithdraw += $savingsWithdrawTransaction->withdrawAmount;
				$savingsWithdrawTransaction->depositAmount = 0;
			endforeach;

			$savingsTransactionOB = (array) array_merge($savingsDepositTransactionOB, $savingsWithdrawTransactionOB);
			
			$transactionDate = array();
 
			foreach($savingsTransactionOB as $savingsTransaction) {
			    $transactionDate[] = $savingsTransaction->transactionDate;
			}

			array_multisort($transactionDate, SORT_ASC, $savingsTransactionOB);

			$savingsSummary = [
				'totalDeposit'   =>  $totalDeposit,
				'totalWithdraw'  =>  $totalWithdraw,
				'totalBalance'   =>  $totalDeposit - $totalWithdraw
			];

			$savingsData = [
				'savingsTransactionOB'  =>  $savingsTransactionOB,
				'savingsSummary' 	    =>  $savingsSummary
			];

			$balanace = 0;
			
			foreach($savingsTransactionOB as $savingsTransaction):
				$balanace += $savingsTransaction->depositAmount;
				$balanace -= $savingsTransaction->withdrawAmount;
				$savingsTransaction->balance = $balanace;
			endforeach;
			
			return $savingsData;
			//return $savingsTransactionOB;
		}
		
		/**
		 * [Function for return Savings Total Deposite Amount]
		 * 
		 * @param  [int] 	$memberId 		   [Member ID]
		 * @param  [int] 	$savingsProductId  [Savings Product ID]
		 * @return [int]           		   	   [Savings Total Deposite Amount]
		 */
		public function getSavingsTotalDepositeAmount($memberId, $savingsProductId) {

			$savingsTotalDepositeAmountOB = DB::table('mfn_savings_account')
										      ->where([['memberIdFk', $memberId],
											  		   ['savingsProductIdFk', $savingsProductId]
											  		  ])
											  ->selectRaw('sum(autoProcessAmount) AS totalDepositeAmount')
											  ->first();

			return $savingsTotalDepositeAmountOB->totalDepositeAmount;
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: START LOANS FUNCTION
		|--------------------------------------------------------------------------
		*/

		/**
		 * An object contains Active Loan Purpose Category.
		 * 
		 * @return [object]	[$purposeCategory]
		 */
		public function getActivePurposeCategory() {

			$purposeCategoryOB = MfnPurposeCategory::active()->get();

			return $purposeCategoryOB;
		}

		/**
		 * An object contains Active Loan Purposes.
		 * 
		 * @return [object]	[$purpose]
		 */
		public function getActivePurpose() {

			$purposeOB = MfnPurpose::active()->get();

			return $purposeOB;
		}

		/**
		 * An object contains Active Loan Purpose Category list.
		 * 
		 * @return [object]	[$loanPurposeCategoryListOB]
		 */
		public function getLoanPurposeCategoryList() {

			$loanPurposeCategoryListOB = array('' => 'Select') 
							 			 +
							 			 MfnPurposeCategory::active()->pluck('name', 'id')->all();

			return $loanPurposeCategoryListOB;
		}

		/**
		 * An object contains Active Loan Sub Purposes.
		 * 
		 * @return [object]	[$subPurposeOB]
		 */
		public function getActiveSubPurpose() {

			$subPurposeOB = MfnSubPurpose::active()->get();

			return $subPurposeOB;
		}

		/**
		 * An object contains Active Loan Purpose list.
		 * 
		 * @return [object]	[$loanPurposeListOB]
		 */
		public function getLoanPurposeList() {

			$loanPurposeListOB = array('' => 'Select')
								 +
								 MfnPurpose::active()->pluck('name', 'id')->all();

			return $loanPurposeListOB;
		}

		/**
		 * An object contains Active Loan Repay Period .
		 * 
		 * @return [object]	[$loanRepayPeriodOB]
		 */
		public function getLoanRepayPeriodList() {

			$loanRepayPeriodOB = MfnLoanRepayPeriod::active()->get();

			return $loanRepayPeriodOB;
		}

		/**
		 * An object contains Active Loan Product Category Type.
		 * 
		 * @return [object]	[$categoryTypeOB]
		 */
		public function getActiveCategoryType() {

			$categoryTypeOB = MfnCategoryType::active()->get();

			return $categoryTypeOB;
		}

		/**
		 * An object contains Active Loan Product Category Type.
		 * 
		 * @return [object]	[$categoryTypeOB]
		 */
		public function getActiveProductCategory() {

			$productCategoryOB = MfnProductCategory::active()->get();

			return $productCategoryOB;
		}

		/**
		 * An object contains Product Category List.
		 * 
		 * @return [object]	[$productCategoryListOB]
		 */
		public function getProductCategoryList() {

			$productCategoryListOB = MfnProductCategory::select(DB::raw("CONCAT(shortName,' - ',name) AS nameWithShortName"), 'id')
			 										   ->get()
			 										   ->pluck('nameWithShortName', 'id')
			 										   ->all();

			return $productCategoryListOB;
		}

		/**
		 * An object contains Active Savings Deposit Frequency.
		 * 
		 * @return [object]	[$savingsDepositFrequency]
		 */
		public function getSavingsDepositFrequency() {

			$savingsDepositFrequency = [
				''		   =>  'Select',
				'weekly'   =>  'Weekly',
				'monthly'  =>  'Monthly',
			];

			return $savingsDepositFrequency;
		}

		/**
		 * An object contains Active Monthly Collection Week.
		 * 
		 * @return [object]	[$monthlyCollectionWeek]
		 */
		public function getMonthlyCollectionWeek() {

			$monthlyCollectionWeek = [
				''		   =>  'Select',
				'first'    =>  '1st Week',
				'second'   =>  '2nd Week',
				'third'    =>  '3rd Week',
				'fourth'   =>  '4th Week',
				'last'     =>  'Last Week',
			];

			return $monthlyCollectionWeek;
		}

		/**
		 * An array contains Interest Payment Mode Options.
		 * 
		 * @return [array]	[$interestPaymentModeOptions]
		 */
		public function getInterestPaymentModeOptions() {

			$interestPaymentModeOptions = [
				1  =>  'Single/One Time',
				2  =>  'Weekly',
				3  =>  'Monthly'
			];

			return $interestPaymentModeOptions;
		}

		/**
		 * An object contains Active Category Type.
		 * 
		 * @return [object]	[$categoryTypeListOB]
		 */
		public function getCategoryTypeList() {

			$categoryTypeListOB = array('' => 'Select') 
							      +
							      MfnCategoryType::active()->pluck('name', 'id')->all();

			return $categoryTypeListOB;
		}

		/**
		 * An object contains Active Loan Product.
		 * 
		 * @return [object]	[$loanProductOB]
		 */
		public function getActiveLoanProduct() {

			$loanProductOB = MfnProduct::active()->get();

			return $loanProductOB;
		}

		public function getActiveLoanPrimaryProductOptions() {

			$loanProductBranchWiseOB = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->select('loanProductId')->first();

			$loanProductBranchWiseArr = str_replace(array('"', '[', ']'),'', $loanProductBranchWiseOB->loanProductId);
			$loanProductBranchWiseArr = explode(',', $loanProductBranchWiseArr);

			$loanProduct = MfnProduct::active()
			                         ->whereIn('id', $loanProductBranchWiseArr)
			                         ->primaryProduct()
                				     ->select('name', 'id')
						             ->get()
			                         ->pluck('name', 'id')
			                         ->all();
			
			 return $loanProduct;
		}

		public function getBranchWiseActiveLoanPrimaryProductOptions($branchId) {

			$loanProductBranchWiseOB = DB::table('gnr_branch')->where('id', $branchId)->select('loanProductId')->first();

			$loanProductBranchWiseArr = str_replace(array('"', '[', ']'),'', $loanProductBranchWiseOB->loanProductId);
			$loanProductBranchWiseArr = explode(',', $loanProductBranchWiseArr);

			$loanProduct = MfnProduct::active()
			                         ->whereIn('id', $loanProductBranchWiseArr)
			                         ->primaryProduct()
                				     ->select('name', 'id')
						             ->get()
			                         ->pluck('name', 'id')
			                         ->all();
			
			 return $loanProduct;
		}

		public function getBranchWiseActiveLoanPrimaryProductShortNameOptions($branchId) {

			$loanProductBranchWiseOB = DB::table('gnr_branch')->where('id', $branchId)->select('loanProductId')->first();

			$loanProductBranchWiseArr = str_replace(array('"', '[', ']'),'', $loanProductBranchWiseOB->loanProductId);
			$loanProductBranchWiseArr = explode(',', $loanProductBranchWiseArr);

			$loanProduct = MfnProduct::active()
			                         ->whereIn('id', $loanProductBranchWiseArr)
			                         ->primaryProduct()
                				     ->select('shortName', 'id')
						             ->get()
			                         ->pluck('shortName', 'id')
			                         ->all();
			
			 return $loanProduct;
		}

		public function getBranchAndCategoryWiseActiveLoanPrimaryProductOptions($branchId, $productCategoryId) {

			$loanProductBranchWiseOB = DB::table('gnr_branch')->where('id', $branchId)->select('loanProductId')->first();

			$loanProductBranchWiseArr = str_replace(array('"', '[', ']'),'', $loanProductBranchWiseOB->loanProductId);
			$loanProductBranchWiseArr = explode(',', $loanProductBranchWiseArr);

			$loanProduct = MfnProduct::active()
									 ->where('productCategoryId', $productCategoryId)
			                         ->whereIn('id', $loanProductBranchWiseArr)
			                         ->primaryProduct()
                				     ->select('name', 'id')
						             ->get()
			                         ->pluck('name', 'id')
			                         ->all();
			
			 return $loanProduct;
		}

		public function getActiveLoanPrimaryProductOptionsMemberWise($memberId) {

			$memberOB = DB::table('mfn_member_information')->where('id', $memberId)->select('primaryProductId')->first();

			$loanProduct = MfnProduct::active()
									 ->primaryProduct()
			                         ->where('id', $memberOB->primaryProductId)
			                         ->select('name', 'id')
						             ->get()
			                         ->pluck('name', 'id')
			                         ->all();
			
			return $loanProduct;
		}

		public function getActiveLoanOthersProductOptions() {

			$loanProductBranchWiseOB = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->select('loanProductId')->first();

			$loanProductBranchWiseArr = str_replace(array('"', '[', ']'),'', $loanProductBranchWiseOB->loanProductId);
			$loanProductBranchWiseArr = explode(',', $loanProductBranchWiseArr);

			$loanProduct = MfnProduct::active()
			                         ->whereIn('id', $loanProductBranchWiseArr)
			                         ->othersProduct()
                				     ->select('name', 'id')
						             ->get()
			                         ->pluck('name', 'id')
			                         ->all();

			return $loanProduct;
		}

		/*public function getActiveRegularLoanProductOptions() {

			$loanProduct = MfnProduct::active()->regular()->pluck('name', 'id')->all();

			return $loanProduct;
		}

		public function getActiveOthersLoanProductOptions() {

			$loanProduct = MfnProduct::active()->others()->pluck('name', 'id')->all();

			return $loanProduct;
		}*/

		/**
		 * An object contains Active Funding Organization.
		 * 
		 * @return [object]	[$fundingOrganizationOB]
		 */
		public function getActiveFundingOrganization() {

			$fundingOrganizationOB = MfnFundingOrganization::active()->get();

			return $fundingOrganizationOB;
		}

		/**
		 * An object contains Funding Organization List.
		 * 
		 * @return [object]	[$fundingOrganizationListOB]
		 */
		public function getFundingOrganizationList() {

			$fundingOrganizationListOB = MfnFundingOrganization::pluck('name', 'id')->all();

			return $fundingOrganizationListOB;
		}

		/**
		 * An object contains Active Years Eligible Write Off.
		 * 
		 * @return [object]	[$yearsEligibleWriteOffOB]
		 */
		public function getActiveYearsEligibleWriteOff() {

			$yearsEligibleWriteOffOB = MfnYearsEligibleWriteOff::active()->get();

			return $yearsEligibleWriteOffOB;
		}

		/**
		 * An object contains Years Eligible Write Off List.
		 * 
		 * @return [object]	[$yearsEligibleWriteOffOB]
		 */
		public function getYearsEligibleWriteOffList() {

			$yearsEligibleWriteOffOB = array('' => 'Select') 
									   + 
									   MfnYearsEligibleWriteOff::pluck('name', 'id')->all();

			return $yearsEligibleWriteOffOB;
		}

		/**
		 * An object contains Active Insurance Calculation Method.
		 * 
		 * @return [object]	[$insuranceCalculationOB]
		 */
		public function getActiveInsuranceCalculation() {

			$insuranceCalculationOB = MfnInsuranceCalculationMethod::active()->get();

			return $insuranceCalculationOB;
		}

		/**
		 * An object contains Insurance Calculation Method List.
		 * 
		 * @return [object]	[$insuranceCalculationMethodOB]
		 */
		public function getInsuranceCalculationMethodList() {

			$insuranceCalculationMethodOB = MfnInsuranceCalculationMethod::pluck('name', 'id')->all();

			return $insuranceCalculationMethodOB;
		} 

		/**
		 * An object contains Active Loan Product Type Method.
		 * 
		 * @return [object]	[$loanProductTypeOB]
		 */
		public function getActiveLoanProductType() {

			$loanProductTypeOB = MfnLoanProductType::active()->get();

			return $loanProductTypeOB;
		}

		/**
		 * An object contains Loan Product Type List.
		 * 
		 * @return [object]	[$loanProductTypeListOB]
		 */
		public function getLoanProductTypeList() {

			$loanProductTypeListOB = MfnLoanProductType::pluck('name', 'id')->all();

			return $loanProductTypeListOB;
		}

		public function getLoanProductTypeDefaultOption() {

			$loanProductTypeListOB = MfnLoanProductType::where('id', 1)->pluck('name', 'id')->all();

			return $loanProductTypeListOB;
		}

		/**
		 * An object contains Active Repayment Frequency.
		 * 
		 * @return [object]	[$repaymentFrequencyOB]
		 */
		public function getRepaymentFrequency() {

			$repaymentFrequencyOB = MfnRepaymentFrequency::active()->get();

			return $repaymentFrequencyOB;
		}

		public function getRepaymentFrequencyOptions() {

			$repaymentFrequency = MfnRepaymentFrequency::active()->pluck('name', 'id')->all();

			return array(''	=> 'Select') + $repaymentFrequency;
		}

		public function getRepaymentFrequencyOptionsProductWise($table, $jsonOB) {

			$getArr = json_decode($jsonOB, true);
			
			$i = 0;
			$arr = [];
			
			foreach($getArr as $key => $val):
				$arr[$key] = self::getNameValueForId($table, $key);
			endforeach;

			return $arr;
		}

		public function getRepaymentFrequencyArr($table, $jsonOB) {

			$getArr = json_decode($jsonOB, true);
			$arr = [];
			
			foreach($getArr as $key => $val):
				$arr[] = $key;
			endforeach;

			return $arr;
		}


		/**
		 * An object contains Repayment Frequency List.
		 * 
		 * @return [object]	[$repaymentFrequencyOB]
		 */
		public function getRepaymentFrequencyList() {

			$repaymentFrequencyOB = MfnRepaymentFrequency::pluck('name', 'id')->all();

			return $repaymentFrequencyOB;
		}
		
		/**
		 * An object contains Active Interest Payment Mode.
		 * 
		 * @return [object]	[$interestPayment]
		 */
		public function getInterestPayment() {

			$interestPaymentOB = MfnInterestPaymentMode::active()->get();

			return $interestPaymentOB;
		}

		/**
		 * An object contains Active Extra Option.
		 * 
		 * @return [object]	[$extraOptionOB]
		 */
		public function getExtraOptions() {

			$extraOptionOB = MfnExtraOptions::active()->get();

			return $extraOptionOB;
		}

		/**
		 * An object contains Extra Option List.
		 * 
		 * @return [object]	[$extraOptionsListOB]
		 */
		public function getExtraOptionsList() {

			$extraOptionsListOB = array('0' => 'NA') 
								  + 
								  MfnExtraOptions::pluck('name', 'id')->all();

			return $extraOptionsListOB;
		}

		/**
		 * An object contains Active Monthly Repayment.
		 * 
		 * @return [object]	[$monthlyRepaymentOB]
		 */
		public function getMonthlyRepaymentMode() {

			$monthlyRepaymentOB = MfnMonthlyRepaymentMode::active()->get();

			return $monthlyRepaymentOB;
		}

		/**
		 * An object contains Monthly Repayment Mode List.
		 * 
		 * @return [object]	[$monthlyRepaymentModeListOB]
		 */
		public function getMonthlyRepaymentModeList() {

			$monthlyRepaymentModeListOB = array('' => 'Select') 
										  +
										  MfnMonthlyRepaymentMode::pluck('name', 'id')->all();

			return $monthlyRepaymentModeListOB;
		}

		/**
		 * An object contains Active Grace Period.
		 * 
		 * @return [object]	[$gracePeriodOB]
		 */
		public function getGracePeriod() {

			$gracePeriodOB = MfnGracePeriod::active()->get();

			return $gracePeriodOB;
		} 

		/**
		 * An object contains Active Grace Period.
		 * 
		 * @return [object]	[$interestCalculationMethodOB]
		 */
		public function getInterestCalculationMethod() {

			$interestCalculationMethodOB = MfnInterestCalculationMethod::active()->get();

			return $interestCalculationMethodOB;
		} 

		/**
		 * An object contains Grace Period List.
		 * 
		 * @return [object]	[$gracePeriodListOB]
		 */
		public function getGracePeriodList() {

			$gracePeriodListOB = MfnGracePeriod::pluck('name', 'id')->all()
								 +
								 array('NA' => 'Not Applicable');

			return $gracePeriodListOB;
		}

		public function getLoanInterestCalculationMethodOptions() {

			$loanInterestCalculationMethodOB = array('' => 'Select') 
										       +
										       MfnInterestCalculationMethod::pluck('name', 'id')->all();

			return  $loanInterestCalculationMethodOB;
		}

		public function getLoanInterestCalculationMethodShortName($interestCalculationMethodId) {

			$loanInterestCalculationMethodShortNameOB = MfnInterestCalculationMethod::select('shortName')->where('id', $interestCalculationMethodId)->first();

			return $loanInterestCalculationMethodShortNameOB->shortName;
		}

		public function getInterestDeclinePeriodOptions() {

			$interestDeclinePeriod = array(
				1  =>  'Default',
				2  =>  'Daily Basis'
			);

			return $interestDeclinePeriod;
		}

		public function getInterestModeOptions() {
 
			$interestMode = array(
				1  =>  'Per Hundred',
				2  =>  'Per Thousand'
			);

			return $interestMode;
		}

		public function getInterestRepaymentFrequencyOptions() {

			$interestRepaymentFrequency = array(
				1  =>  'All',
				2  =>  'W',
				3  =>  'M'
			);

			return $interestRepaymentFrequency;
		}

		public function getInstallmentNumByProductWise($productId) {

			$productOB = DB::table('mfn_loans_product')->where('id', $productId)->select('installmentNum')->first();

			$installmentArr = explode(',', $productOB->installmentNum);

			//	TRIM WHITE SPACES FROM THE ARRAY INDEX AND VALUE. 
			foreach($installmentArr as $key => $val):
				$installmentArr[trim($key)] = trim($val);
			endforeach;

			$interestRateOB = DB::table('mfn_loan_product_interest_rate')->where('loanProductId', $productId)->select('installmentNum')->get();

			$installmentOptionsUsed = [];
			
			foreach($interestRateOB as $obj):
				$installmentOptionsUsed[] = $obj->installmentNum;
			endforeach;

			$installment = array_diff($installmentArr, $installmentOptionsUsed);

			$installmentOptions = [];

			foreach($installment as $key => $val):
				$installmentOptions[trim($val)] = trim($val);
			endforeach;

			// return $installment;
			return $installmentOptions; 
		}

		public function getLoanProductDetails($loanProductId) {

			$loanProductDetailsOB = DB::table('mfn_loans_product')
								      ->where('id', $loanProductId) 
								      ->first();
	
			return $loanProductDetailsOB;
		}

		public function getNameValueForId($table, $id) {

			$nameOB = DB::table($table)->select('name')->where('id', $id)->first();

			return $nameOB->name;
		}

		public function getSingleValueForId($table, $id, $dataRequest) {

			$valueOB = DB::table($table)->select($dataRequest)->where('id', $id)->first();

			return $valueOB->$dataRequest;
		}

		public function getMultipleValueForId($table, $id, $dataRequest) {

			$valueOB = DB::table($table)->select($dataRequest)->where('id', $id)->first();

			return $valueOB;
		}

		public function getMultipleValueForIdForLoanSchedule($table, $loanId, $installmentSl, $dataRequest) {

			$valueOB = DB::table($table)
					     ->select($dataRequest)
					     ->where([['loanIdFk', $loanId],
					     		  ['installmentSl', $installmentSl]
					     		 ])
					     ->first();

			return $valueOB;
		}

		public function getMicroFinanceDateFormat($date) {

			return date_format(date_create($date), "d-m-Y");
		}

		public function getMicroFinanceDateFormatWithoutLeadingZero($date) {

			return date_format(date_create($date), "j-n-Y");
		}

		public function getDBDateFormat($date) {

			return date_format(date_create($date), "Y-m-d");
		}

		public function getMultipleNameValueForMultipleId($table, $IdArr) {
		
			$IdArr =  str_replace(array('"', '[', ']'),'', $IdArr);
			$IdArr = explode(',', $IdArr);
			$str = '';

			$i = 0;
			foreach($IdArr as $id):
				$nameOB = DB::table($table)->select('name')->where('id', $id)->first();
				$str .= $nameOB->name;

				if($i<count($IdArr)-1)
					$str .= ',&nbsp;';
				$i++;
			endforeach;

			return $str;
		}

		public function getMultipleNameValueForFirstMultipleId($table, $jsonOB) {

			$getArr = json_decode($jsonOB, true);
			
			$i = 0;
			$str = '';
			
			foreach($getArr as $key => $val):
				$str .= self::getNameValueForId($table='mfn_repayment_frequency', $key);
				
				if($i<count($getArr)-1)
					$str .= ', ';
				$i++;
			endforeach;

			return $str;
		}

		public function getArrayForSecondMultipleId($table, $jsonOB, $dataRequest) {

			$getArr = json_decode($jsonOB, true);
			
			$arr = [];
			
			foreach($getArr as $key => $val):
				$arr[$key] = self::getSingleValueForId($table, $key, $dataRequest);
			endforeach;

			return $arr;
		}

		public function getInterestRate($loanProductId) {

			$interestRateOB = DB::table('mfn_loan_product_interest_rate')
								->where('loanProductId', $loanProductId) 
								->get();
			
			return $interestRateOB;
		}

		public function getActiveRegularLoan() {

			if(Auth::user()->branchId==1):
				$regularLoanOB = MfnLoan::active()->regularLoan()->get();
			else:
				$regularLoanOB = MfnLoan::active()->branchWise()->regularLoan()->get();
			endif;

			return $regularLoanOB;
		}

		public function getActiveOneTimeLoan() {

			if(Auth::user()->branchId==1):
				$oneTimeLoanOB = MfnLoan::active()->oneTimeLoan()->get();
			else:
				$oneTimeLoanOB = MfnLoan::active()->branchWise()->oneTimeLoan()->get();
			endif;

			return $oneTimeLoanOB;
		}

		public function getPaymentType() {

			$paymentType = [
				'Cash' => 'Cash',
				'Bank' => 'Bank'
			];

			return $paymentType;
		}

		public function getLoanPurpose() {

			$loanPurposeCatOB = MfnSubPurpose::distinct()->get(['purposeIdFK']);

			$loanPurposeCat = [];
			
			foreach($loanPurposeCatOB as $loanPurpose):
				$loanPurposeCat[] = $loanPurpose->purposeIdFK;
			endforeach;

			$loanSubPurpose = [];

			foreach($loanPurposeCat as $loanPurpose):
				$loanPurposeOB = MfnPurpose::active()->where('id', $loanPurpose)->select('name')->first();
				$loanSubPurposeArr = MfnSubPurpose::active()->where('purposeIdFK', $loanPurpose)->pluck('name', 'id')->all();
				$loanSubPurpose[$loanPurposeOB->name] =  $loanSubPurposeArr;
			endforeach;

			return array(''	=> 'Select') + $loanSubPurpose;
		}

		public function getRegularLoanSLNum($memberId, $loanProductId) {

			$regularLoanSLNum = MfnLoan::where([['memberIdFk', $memberId],
									   			['productIdFk', $loanProductId]
									   		   ])
									   ->count();

			return ++$regularLoanSLNum;
		}

		public function getNewRegularLloanCycle($memberId, $loanProductId) {

			$regularLoanCycle = MfnLoan::where([['memberIdFk', $memberId],
									   			['productIdFk', $loanProductId]
									   		   ])
									   ->max('loanCycle');

			return ++$regularLoanCycle;
		}

		public function getLoanRepayPeriod() {

			$loanRepayPeriod = MfnLoanRepayPeriod::active()->pluck('name', 'id')->all();
			
			return $loanRepayPeriod;
		}

		public function getLoanDetails($loanId) {

			$loanOB = DB::table('mfn_loan')
						->where('id', $loanId) 
						->first();
	
			return $loanOB;
		}

		public function getLoanSchedule($loanId, $loanTypeId) {

			$loanScheduleOB = DB::table('mfn_loan_schedule')
								->where([['loanIdFk', $loanId], 
										 ['loanTypeId', $loanTypeId]
										]) 
								->get();

			return $loanScheduleOB;
		}

		public function getActiveLoanReschedule() {

			$rescheduleOB = MfnLoanReschedule::active()->branchWise()->get();
	
			return $rescheduleOB;
		}

		/**
		 * [getNextSamityDate 				Function for return next samity date for a given Disbursement Date and Samity ID]
		 * @param  [date] $disbursementDate [Given Disbursement Date]
		 * @param  [int]  $samityDayId      [Given Samity Day ID]
		 * @return [date]                   [Return ]
		 */
		public function getNextSamityDate($disbursementDate, $samityDayId) {

			$dt = Carbon::parse($disbursementDate);

			$dayArr = array(
				1 => 'Sat', 
				2 => 'Sun', 
				3 => 'Mon', 
				4 => 'Tue', 
				5 => 'Wed', 
				6 => 'Thu', 
				7 => 'Fri'
			);

			$disbursementDayName = date('D', strtotime($disbursementDate));
			$samityDayName = $dayArr[$samityDayId];

			if($disbursementDayName==$samityDayName):
				$nextSamityDate = $dt->addDays(7)->toDateString();
			else:
				foreach($dayArr as $key => $day):
					if($disbursementDayName==$dayArr[$key]):
						$getDisbursementDayNum = $key;
					endif;
					if($samityDayName==$dayArr[$key]):
						$getSamityDayNum = $key;
					endif;
				endforeach;

				//	WHEN DISBURSEMENT DAY NAME IS SMALLER THAN SAMITY DAY NAME.
				if($getDisbursementDayNum<$getSamityDayNum)
					$dayNumDiff = $getSamityDayNum - $getDisbursementDayNum;

				//	WHEN DISBURSEMENT DAY NAME IS GREATER THAN SAMITY DAY NAME.
				if($getDisbursementDayNum>$getSamityDayNum)
					$dayNumDiff = count($dayArr) - ($getDisbursementDayNum - $getSamityDayNum);
				
				$nextSamityDate = $dt->addDays($dayNumDiff)->toDateString();
			endif;

			return $nextSamityDate;
		}

		/**
		 * [getFridayFilter 			   This function takea a date and return a date with Friday filter]
		 * @param  [date]  $firstRepayDate [Given a date]
		 * @return [date]                  [Return a date with Friday filter]
		 */
		public function getFridayFilter($firstRepayDate) {

			$dt = Carbon::parse($firstRepayDate);
			$firstRepayDate = $dt->addDays(1)->toDateString();
			
			return $firstRepayDate;
		}

		public function getGovtHoliday() {

			$holiday = DB::table('mfn_setting_gov_holiday')->pluck('date', 'id')->all();

			foreach($holiday as $key => $val):
				$holiday[$key] = implode('-', array_reverse(explode('-', $val)));
			endforeach;

			return $holiday;
		}

		public function getGlobalGovtHoliday() {

			$holiday = DB::table('mfn_setting_holiday')->pluck('date', 'id')->all();

			return $holiday;
		}

		public function getOrganizationHoliday($organizationId) {

			$holiday = [];

			$organizationHoliday = DB::table('mfn_setting_orgBranchSamity_holiday')
									 ->where([['isOrgHoliday', 1],
									 		  ['ogrIdFk', $organizationId]
									 		 ])
									 ->pluck('dateTo', 'dateFrom')
									 ->all();

			foreach($organizationHoliday as $key => $val):
				$dateForm = Carbon::parse($key);
				$dateTo = Carbon::parse($val);
				$daysDiff = $dateTo->diff($dateForm)->format("%a");
				
				$holiday[] = $key;
				
				for($i=1;$i<=$daysDiff;$i++):
					$holiday[] = $dateForm->addDays(1)->toDateString();
				endfor;
			endforeach;

			return $holiday;
		}

		public function getBranchHoliday() {

			$holiday = [];

			$branchHoliday = DB::table('mfn_setting_orgBranchSamity_holiday')
							   ->where([['isBranchHoliday', 1],
							   		    ['branchIdFk', Auth::user()->branchId]
							   		   ])
							   ->pluck('dateTo', 'dateFrom')
							   ->all();

			foreach($branchHoliday as $key => $val):
				$dateForm = Carbon::parse($key);
				$dateTo = Carbon::parse($val);
				$daysDiff = $dateTo->diff($dateForm)->format("%a");
				
				$holiday[] = $key;
				
				for($i=1;$i<=$daysDiff;$i++):
					$holiday[] = $dateForm->addDays(1)->toDateString();
				endfor;
			endforeach;

			return $holiday;
		}

		public function getBranchHolidayWithParam($branchId) {

			$holiday = [];

			$branchHoliday = DB::table('mfn_setting_orgBranchSamity_holiday')
							   ->where([['isBranchHoliday', 1],
							   		    ['branchIdFk', $branchId]
							   		   ])
							   ->pluck('dateTo', 'dateFrom')
							   ->all();

			foreach($branchHoliday as $key => $val):
				$dateForm = Carbon::parse($key);
				$dateTo = Carbon::parse($val);
				$daysDiff = $dateTo->diff($dateForm)->format("%a");
				
				$holiday[] = $key;
				
				for($i=1;$i<=$daysDiff;$i++):
					$holiday[] = $dateForm->addDays(1)->toDateString();
				endfor;
			endforeach;

			return $holiday;
		}

		public function getSamityHoliday($memberId) {

			$holiday = [];

			//	GET SAMITY ID OF THE SAMITY OF THE MEMBER.
			$samityDayOB = DB::table('mfn_member_information')
							 ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							 ->where('mfn_member_information.id', $memberId)
							 ->select('mfn_samity.id AS samityId')
							 ->first();

			$samityId = $samityDayOB->samityId;

			$samityHoliday = DB::table('mfn_setting_orgBranchSamity_holiday')
							   ->where([['isSamityHoliday', 1],
							   			['samityIdFk', $samityId]
							   		   ])
							   ->pluck('dateTo', 'dateFrom')
							   ->all();

			foreach($samityHoliday as $key => $val):
				$dateForm = Carbon::parse($key);
				$dateTo = Carbon::parse($val);
				$daysDiff = $dateTo->diff($dateForm)->format("%a");
				
				$holiday[] = $key;
				
				for($i=1;$i<=$daysDiff;$i++):
					$holiday[] = $dateForm->addDays(1)->toDateString();
				endfor;
			endforeach;

			return $holiday;
		}

		public function getSamityHolidayWithSamityParam($samityId) {

			$holiday = [];

			$samityHoliday = DB::table('mfn_setting_orgBranchSamity_holiday')
							   ->where([['isSamityHoliday', 1],
							   			['samityIdFk', $samityId]
							   		   ])
							   ->pluck('dateTo', 'dateFrom')
							   ->all();

			foreach($samityHoliday as $key => $val):
				$dateForm = Carbon::parse($key);
				$dateTo = Carbon::parse($val);
				$daysDiff = $dateTo->diff($dateForm)->format("%a");
				
				$holiday[] = $key;
				
				for($i=1;$i<=$daysDiff;$i++):
					$holiday[] = $dateForm->addDays(1)->toDateString();
				endfor;
			endforeach;

			return $holiday;
		}

		public function getSamityHolidayBySamityId($samityId) {

			$holiday = [];

			$samityHoliday = DB::table('mfn_setting_orgBranchSamity_holiday')
							   ->where([['isSamityHoliday', 1],
							   			['samityIdFk', $samityId]
							   		   ])
							   ->pluck('dateTo', 'dateFrom')
							   ->all();

			foreach($samityHoliday as $key => $val):
				$dateForm = Carbon::parse($key);
				$dateTo = Carbon::parse($val);
				$daysDiff = $dateTo->diff($dateForm)->format("%a");
				
				$holiday[] = $key;
				
				for($i=1;$i<=$daysDiff;$i++):
					$holiday[] = $dateForm->addDays(1)->toDateString();
				endfor;
			endforeach;

			return $holiday;
		}

		/*public function getAllHoliday() {

			$globalGovtHoliday = self::getGlobalGovtHoliday();
			$organizationHoliday = self::getOrganizationHoliday(1);
			$branchHoliday = self::getBranchHoliday();
			$samityHoliday = self::getSamityHoliday($memberId);
			$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

			return $holiday;
		}*/

		public function getFirstRepayDateFilter($firstRepayDate, $samityDayId) {

			$test = '2017-11-10';

			$firstRepayDate = $test;
			
			$dt = Carbon::parse($test);


			$dayArr = array(
				1 => 'Sat', 
				2 => 'Sun', 
				3 => 'Mon', 
				4 => 'Tue', 
				5 => 'Wed', 
				6 => 'Thu', 
				7 => 'Fri'
			);

			//	IF A DATE IS FRIDAY THEN FILTER IT TO NEXT DAY.
			if(date('D', strtotime($firstRepayDate))=='Fri'):
				$firstRepayDate = self::getFridayFilter($firstRepayDate);
			endif;

			if(date('D', strtotime($firstRepayDate))==$dayArr[$samityDayId]):
				$firstRepayDate = $firstRepayDate;
			else:
				$firstRepayDate = $dt->addDays($samityDayId)->toDateString();
			endif;
			
			return $firstRepayDate;
		}

		public function getMonthlyLoanScheduleDateFilter($monthlyScheduleDate, $memberId) {

			$originalDT = $dt = Carbon::parse($monthlyScheduleDate);
			$dt = Carbon::parse($monthlyScheduleDate);

			$dayArr = array(
				1 => 'Sat', 
				2 => 'Sun', 
				3 => 'Mon', 
				4 => 'Tue', 
				5 => 'Wed', 
				6 => 'Thu', 
				7 => 'Fri'
			);

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
							   ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							   ->where('mfn_member_information.id', $memberId)
							   ->select('mfn_samity.samityDayId AS samityDayId')
							   ->first();

			$samityDayId = $samityDayIdOB->samityDayId;

			//	GET DAY ID OF THE DATE AFTER 30 DAYS.
			foreach($dayArr as $key => $val):
				if(date('D', strtotime($monthlyScheduleDate))==$val)
					$getDayID = $key;
			endforeach;
			
			//	FALL BACK TO PREVIOUS WEEK IF SAMITY DAY IS NOT MATCH.
			//$nextMonthlyDate = $dt->subDays($getDayID - $samityDayId)->toDateString();
			
			//	SET NEXT WEEK IF SAMITY DAY IS NOT MATCH.
			if($samityDayId!=$getDayID):
				//	WHEN (7 - $getDayID) + $samityDayId) IS LOWER THAN 7.
				if(((7 - $getDayID) + $samityDayId)<7):
					$nextMonthlyDate = $dt->addDays((7 - $getDayID) + $samityDayId)->toDateString();

					//	WHEN THE DATE IS SET TO NEXT MONTH THEN FALL BACK THE DATE TO PREVIOUS MONTH OF SAMITY DATE.
					if($nextMonthlyDate>$originalDT->endOfMonth()):
						$nextMonthlyDate = $dt->subDays(7)->toDateString();
					endif;
				endif;
				//	WHEN (7 - $getDayID) + $samityDayId) IS GREATER THAN 7.
				if(((7 - $getDayID) + $samityDayId)>7):
					$nextMonthlyDate = $dt->addDays($samityDayId - $getDayID)->toDateString();

					//	WHEN THE DATE IS SET TO NEXT MONTH THEN FALL BACK THE DATE TO PREVIOUS MONTH OF SAMITY DATE.
					if($nextMonthlyDate>$originalDT->endOfMonth()):
						$nextMonthlyDate = $dt->subDays(7)->toDateString();
					endif;
				endif;
			endif;

			//	WHEN THE DATE IS MATCH TO SAMITY DATE.
			if($samityDayId==$getDayID):
				$nextMonthlyDate = $dt->subDays($getDayID - $samityDayId)->toDateString();
			endif;
			
			return $nextMonthlyDate;
			//return $monthlyScheduleDate;
			//return $originalDT->endOfMonth();
		}

		public function getMonthlyLoanScheduleDateFilterBySamityDayId($monthlyScheduleDate, $samityDayId) {

			$originalDT = $dt = Carbon::parse($monthlyScheduleDate);
			$dt = Carbon::parse($monthlyScheduleDate);

			$dayArr = array(
				1 => 'Sat', 
				2 => 'Sun', 
				3 => 'Mon', 
				4 => 'Tue', 
				5 => 'Wed', 
				6 => 'Thu', 
				7 => 'Fri'
			);

			//	GET DAY ID OF THE DATE AFTER 30 DAYS.
			foreach($dayArr as $key => $val):
				if(date('D', strtotime($monthlyScheduleDate))==$val)
					$getDayID = $key;
			endforeach;
			
			//	SET NEXT WEEK IF SAMITY DAY IS NOT MATCH.
			if($samityDayId!=$getDayID):
				//	WHEN (7 - $getDayID) + $samityDayId) IS LOWER THAN 7.
				if(((7 - $getDayID) + $samityDayId)<7):
					$nextMonthlyDate = $dt->addDays((7 - $getDayID) + $samityDayId)->toDateString();

					//	WHEN THE DATE IS SET TO NEXT MONTH THEN FALL BACK THE DATE TO PREVIOUS MONTH OF SAMITY DATE.
					if($nextMonthlyDate>$originalDT->endOfMonth()):
						$nextMonthlyDate = $dt->subDays(7)->toDateString();
					endif;
				endif;
				//	WHEN (7 - $getDayID) + $samityDayId) IS GREATER THAN 7.
				if(((7 - $getDayID) + $samityDayId)>7):
					$nextMonthlyDate = $dt->addDays($samityDayId - $getDayID)->toDateString();

					//	WHEN THE DATE IS SET TO NEXT MONTH THEN FALL BACK THE DATE TO PREVIOUS MONTH OF SAMITY DATE.
					if($nextMonthlyDate>$originalDT->endOfMonth()):
						$nextMonthlyDate = $dt->subDays(7)->toDateString();
					endif;
				endif;
			endif;

			//	WHEN THE DATE IS MATCH TO SAMITY DATE.
			if($samityDayId==$getDayID):
				$nextMonthlyDate = $dt->subDays($getDayID - $samityDayId)->toDateString();
			endif;
			
			return $nextMonthlyDate;
		}

		/**
		 * [getRegularLoanFirstRepayDate             Function for return a date of First Repay Date for weekly or monthly]
		 * @param  [int]     $memberId               [Member ID]
		 * @param  [int]     $productId              [Product ID]
		 * @param  [int]     $repaymentFrequencyId   [Repayment Frequency ID]
		 * @return [string]                          [String contains a date]
		 */
		public function getRegularLoanFirstRepayDate($memberId, $disbursementDate, $productId, $repaymentFrequencyId) {

			//$date = '2017-11-02';
			//$dt = Carbon::parse($date);
			//$disbursementDate = $date;

			$dt = Carbon::parse($disbursementDate);
			$disbursementDate = $dt->toDateString();
			
			$dayArr = array(
				1 => 'Sat', 
				2 => 'Sun', 
				3 => 'Mon', 
				4 => 'Tue', 
				5 => 'Wed', 
				6 => 'Thu', 
				7 => 'Fri'
			);

			//	GET HOLIDAY.
			$globalGovtHoliday = self::getGlobalGovtHoliday();
			$organizationHoliday = self::getOrganizationHoliday(1);
			$branchHoliday = self::getBranchHoliday();
			$samityHoliday = self::getSamityHoliday($memberId);
			$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

			//	GET SAMITY DAY ID OF THE SAMITY OF THE MEMBER.
			$samityDayIdOB = DB::table('mfn_member_information')
							   ->join('mfn_samity', 'mfn_member_information.samityId', '=', 'mfn_samity.id')
							   ->where('mfn_member_information.id', $memberId)
							   ->select('mfn_samity.samityDayId AS samityDayId')
							   ->first();

			$samityDayId = $samityDayIdOB->samityDayId;

			//	GET NEXT SAMITY DATE FROM THE DISBURSEMENT DATE.
			$nextSamityDate = self::getNextSamityDate($dt->toDateString(), $samityDayId);

			//	GET GRACE PERIOD FOR REPAYMENT FREQUENCY.
			$loanProductOB = MfnProduct::where('id', $productId)->select('eligibleRepaymentFrequencyId')->first();
			$gracePeriodArr = self::getArrayForSecondMultipleId($table='mfn_grace_period', $loanProductOB->eligibleRepaymentFrequencyId, 'inDays');                           
			
			//	GET FIRST REPAY DATE.
			$NSD = Carbon::parse($nextSamityDate);
			
			//	FIRST REPAY DATE FOR WEEKLY.
			if($repaymentFrequencyId==1):
				$firstRepayDate = $NSD->addDays($gracePeriodArr[$repaymentFrequencyId])->toDateString();
			endif;

			//	FIRST REPAY DATE FOR MONTHLY.
			if($repaymentFrequencyId==2):
				$firstRepayDate = $dt->addDays($gracePeriodArr[$repaymentFrequencyId])->toDateString();

				//	IF A DATE IS FRIDAY THEN FILTER IT TO NEXT DAY.
				if(date('D', strtotime($firstRepayDate))=='Fri'):
					$firstRepayDate = self::getFridayFilter($firstRepayDate);
				endif;

				//	SAMITY DAY CHECK FOR FIRST REPAY DATE.
				if(date('D', strtotime($firstRepayDate))==$dayArr[$samityDayId]):
					$firstRepayDate = $firstRepayDate;
				else:
					//$firstRepayDate = $dt->addDays($samityDayId)->toDateString();
					
					//	FIND NUMBER OF DAYS IN A MONTH.
					$daysInOneMonth = $dt->diffInDays($dt->copy()->addMonth(1));
					
					//	FIND DAYS DIFFERENCE BETWEEN DISBUSEMENT DATE AND DATE AFTER APPLYING GRACE PERIOD i.e 30 DAYS.
					$fromDate = new DateTime($disbursementDate);
					$toDate = new DateTime($firstRepayDate);
					$interval = $fromDate->diff($toDate);
					$daysDiff = $interval->format('%a');

					//	WHEN DIFFERENCE DAYS OF TWO DATES ARE EQUAL OR GREATER THAN 30 i.e $daysDiff>=30
					//  THEN FALL BACK TO PREVIOUS WEEK AND FIND A SAMMITY DAY.
					if($daysDiff>=30):
						//$firstRepayDate = $dt->subDays(7-($samityDayId-1))->toDateString();
						
						//	GET DAY ID OF THE DATE AFTER 30 DAYS.
						foreach($dayArr as $key => $val):
							if(date('D', strtotime($firstRepayDate))==$val)
								$getDayID = $key;
						endforeach;
						
						$firstRepayDate = $dt->subDays($getDayID - $samityDayId)->toDateString();

						//	HOLIDAY FILTER.
						foreach($holiday as $key => $val):
							if(date_create($val)>=$firstRepayDate):
								if(date_create($val)==date_create($firstRepayDate)):
									//	IF HOLIDAY MATCHES, FALL BACK 1 WEEK TO SET SAMITY DAY.
									$firstRepayDate = $dt->subDays(7)->toDateString();
									break;
								endif;
							endif;
						endforeach;
					endif;
				endif;
			endif;

			return $firstRepayDate;
		}
		
		/**
		 * [regularLoanSupportDataRepaymentNumberWise  Function for return interest and installment support data]
		 * @param  [int]     $productId                [Product ID]
		 * @param  [int]     $loanAmount               [Loan Amount]
		 * @param  [int]     $repaymentNo              [No of Repayment]
		 * @param  [int]     $repaymentFrequencyId     [Repayment Frequency ID]
		 * @return [object]                            [Object contains interest and installment support data]
		 */
		public function regularLoanSupportDataRepaymentNumberWise($productId, $loanAmount, $repaymentNo, $repaymentFrequencyId) {

			$interestRateOB = MfnLoanProductInterestRate::where([['loanProductId', $productId],
													    		 ['installmentNum', $repaymentNo]
													    		])
													    ->select('interestModeId', 
													    	     'installmentNum',
													    	     'interestCalculationMethodShortName',
													    	     'interestRate', 
													    	     'interestRateIndex'
													    	    )
													    ->first();

			$interestRateFound = MfnLoanProductInterestRate::where([['loanProductId', $productId],
													       		    ['installmentNum', $repaymentNo]
													       		   ])
													       ->count();

			if($interestRateFound>0):
				$loanProductOB = MfnProduct::where('id', $productId)->select('principalAmountOfLoan')->first();
				
				//	GET INTEREST MODE NAME.
				$interestModeArr = self::getInterestModeOptions();
				$interestMode = $interestModeArr[$interestRateOB->interestModeId];
				$interestRateOB->interestMode = $interestMode;

				//	INSURANCE AMOUNT CALCULATION.
				$insuranceAmount = sprintf("%.2f", ($loanAmount * ($loanProductOB->principalAmountOfLoan / 100)));
				$interestRateOB->insuranceAmount = $insuranceAmount;
				
				//	INTEREST AMOUNT CALCULATION.
				if($repaymentFrequencyId==1):
					$interestAmount = $loanAmount * ((fmod($interestRateOB->interestRateIndex, 1) * 100) / 100);
				endif;

				if($repaymentFrequencyId==2):
					$interestAmount = $loanAmount * ((fmod($interestRateOB->interestRateIndex, 1) * 100) / 100) * ($repaymentNo / 12);
				endif;

				$interestAmount = sprintf("%.2f", $interestAmount);
				$totalRepayAmount = sprintf("%.2f", $loanAmount + $interestAmount);
				$interestRateOB->totalRepayAmount = $totalRepayAmount;
				$interestRateOB->interestAmount = $interestAmount;

				//	ACTUAL INSTALLMENT AMOUNT CALCULATION.
				$actualInstallmentAmount = sprintf("%.2f", $totalRepayAmount / $interestRateOB->installmentNum);
				$interestRateOB->actualInstallmentAmount = $actualInstallmentAmount;

				//	INSTALLMENT AMOUNT CALCULATION.
				$repaymentFrequencyWiseConstant = [
					1  =>  ceil(((1000 * ($totalRepayAmount / $loanAmount)) / $repaymentNo)),
					2  =>  array(
							12  =>  ceil(((1000 * ($totalRepayAmount / $loanAmount)) / $repaymentNo)),
							24  =>  ceil(((1000 * ($totalRepayAmount / $loanAmount)) / $repaymentNo)),
						    36	=>  ceil(((1000 * ($totalRepayAmount / $loanAmount)) / $repaymentNo))
					)
				];
				
				if($repaymentFrequencyId==1)
					$installmentAmount = ($loanAmount / 1000) * $repaymentFrequencyWiseConstant[$repaymentFrequencyId];
				if($repaymentFrequencyId==2)
					$installmentAmount = ($loanAmount / 1000) * $repaymentFrequencyWiseConstant[$repaymentFrequencyId][$repaymentNo];

				$interestRateOB->installmentAmount = sprintf("%.2f", $installmentAmount);

				//	EXTRA INSTALLMENT AMOUNT CALCULATION.
				$extraInstallmentAmount = sprintf("%.2f", $installmentAmount - $actualInstallmentAmount);
				//$extraInstallmentAmount = sprintf("%.2f", $installmentAmount) - sprintf("%.2f", $actualInstallmentAmount);
				$interestRateOB->extraInstallmentAmount = sprintf("%.2f", $extraInstallmentAmount);

				//	LAST INSTALLMENT AMOUNT CALCULATION.
				$lastInstallmentAmount = sprintf("%.2f", ($totalRepayAmount - $installmentAmount * ($interestRateOB->installmentNum - 1)));
				$interestRateOB->lastInstallmentAmount = $lastInstallmentAmount;

				return $interestRateOB;
			else:
				return $interestRateFound;
			endif;

		}

		/**
		 * [regularLoanSupportDataForReducingMethod    Function for return interest and installment support data]
		 * @param  [int]     $productId                [Product ID]
		 * @param  [int]     $loanAmount               [Loan Amount]
		 * @param  [int]     $repaymentNo              [No of Repayment]
		 * @param  [int]     $repaymentFrequencyId     [Repayment Frequency ID]
		 * @return [object]                            [Object contains interest and installment support data]
		 */
		public function regularLoanSupportDataForReducingMethod($productId, $loanAmount, $repaymentNo, $repaymentFrequencyId) {

			$interestRateOB = MfnLoanProductInterestRate::where([['loanProductId', $productId],
													    		 ['interestCalculationMethodId', 4]
													    		])
													    ->select('interestModeId', 
													    	     'installmentNum',
													    	     'interestCalculationMethodShortName',
													    	     'interestRate', 
													    	     'interestRateIndex'
													    	    )
													    ->first();

			$interestRateFound = MfnLoanProductInterestRate::where([['loanProductId', $productId],
													       		    ['interestCalculationMethodId', 4]
													       		   ])
													       ->count();

			if($interestRateFound>0):
				$loanProductOB = MfnProduct::where('id', $productId)->select('principalAmountOfLoan')->first();
				
				//	GET INTEREST MODE NAME.
				$interestModeArr = self::getInterestModeOptions();
				$interestMode = $interestModeArr[$interestRateOB->interestModeId];
				$interestRateOB->interestMode = $interestMode;

				//	INSURANCE AMOUNT CALCULATION.
				$insuranceAmount = sprintf("%.2f", ($loanAmount * ($loanProductOB->principalAmountOfLoan / 100)));
				$interestRateOB->insuranceAmount = $insuranceAmount;
				
				//	INTEREST AMOUNT CALCULATION.
				if($repaymentFrequencyId==1):
					$interestAmount = $loanAmount * $interestRateOB->interestRateIndex * 360;
				endif;

				if($repaymentFrequencyId==2):
					$interestAmount = $loanAmount * ((fmod($interestRateOB->interestRateIndex, 1) * 100) / 100) * ($repaymentNo / 12);
				endif;

				$interestAmount = sprintf("%.2f", $interestAmount);
				$totalRepayAmount = sprintf("%.2f", $loanAmount + $interestAmount);
				$interestRateOB->totalRepayAmount = $totalRepayAmount;
				$interestRateOB->interestAmount = $interestAmount;

				//	INSTALLMENT AMOUNT AND ACTUAL INSTALLMENT AMOUNT CALCULATION.
				if($repaymentFrequencyId==1):
					$installmentAmount = ($loanAmount / 1000) * 25;
					$actualInstallmentAmount = $installmentAmount;
					$installmentNum = $loanAmount / $installmentAmount;
				endif;

				if($repaymentFrequencyId==2):
					$installmentAmount = ($loanAmount / 1000) * 100;
					$actualInstallmentAmount = $installmentAmount;
					$installmentNum = $loanAmount / $installmentAmount;
				endif;

				$interestRateOB->installmentAmount = sprintf("%.2f", $installmentAmount);
				$interestRateOB->actualInstallmentAmount = sprintf("%.2f", $actualInstallmentAmount);
				$interestRateOB->installmentNum = $installmentNum;
				$interestRateOB->totalRepayAmount = 0;
				$interestRateOB->interestAmount = 0;

				//	EXTRA INSTALLMENT AMOUNT CALCULATION.
				$interestRateOB->extraInstallmentAmount = sprintf("%.2f", $installmentAmount - $actualInstallmentAmount);

				//	LAST INSTALLMENT AMOUNT CALCULATION.
				$interestRateOB->lastInstallmentAmount = sprintf("%.2f", ($loanAmount - $installmentAmount * ($installmentNum - 1)));

				return $interestRateOB;
			else:
				return $interestRateFound;
			endif;
		}

		public function getScheduleDateToChange($loanId, $installmentSl) {

			$scheduleDate = [];
			
			$scheduleDateOB = DB::table('mfn_loan_schedule')
								->where([['loanIdFk', $loanId],
									     ['installmentSl', '>=' , $installmentSl]
										])
								->select('id', 'loanIdFk', 'installmentSl', 'scheduleDate')
								->get(); 

			return $scheduleDateOB;
		}


		public function getLoanProductsOption() {

			$loanProductsOption = MfnProduct::active()->pluck('name','id')->toArray();
			
			return $loanProductsOption;
		}

		public function getLoanProductsOptionSingle($loanProductId) {

			$loanProduct = MfnProduct::active()->where('id', $loanProductId)->pluck('name','id');
			
			return $loanProduct;
		}

		public function getLoanAccountNumberPerMember($memberId) {

			$loanAccount = MfnLoan::active()
								  ->where('memberIdFk', $memberId)
								  ->select('id', 
										   'loanCode', 
										   'productIdFk', 
										   'loanAmount', 
										   'interestAmount',
										   'totalRepayAmount', 
										   'installmentAmount',
										   'repaymentNo',
										   'interestDiscountAmount'
										  )
								  ->get();
			
			return $loanAccount;
		}

		public function getLoanPayment($loanId) {

			$completeInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)->active()->complete()->sum('installmentAmount');
			$partialInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)->active()->partial()->sum('partiallyPaidAmount');
			
			$loanPayment = $completeInstallment + $partialInstallment;

			return $loanPayment;
		}

		public function getRegularLoanOutstanding($loanId, $loanAmount) {

			$completeInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)->active()->complete()->sum('installmentAmount');
			$partialInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)->active()->partial()->sum('partiallyPaidAmount');
			
			$loanOutstanding = $loanAmount - ($completeInstallment + $partialInstallment);

			return $loanOutstanding;
		}

		public function getRegularLoanAdvance($loanId) {

			$advanceCompleteInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)
													     ->active()
													 	 ->where([['isCompleted', 1],
													 	 		 ['scheduleDate', '>', Carbon::now()->toDateString()]
													 	 		])
													 	 ->sum('installmentAmount');

			$advancePartialInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)
														->active()
														->partial()
														->where('scheduleDate', '>', Carbon::now()->toDateString())
														->sum('partiallyPaidAmount');

			$advanceLoanAmount = $advanceCompleteInstallment + $advancePartialInstallment;

			return $advanceLoanAmount;
		}

		public function getRegularLoanDue($loanId, $installmentAmount) {

			$dueCompleteInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)
													 ->active()
													 ->where([['isCompleted', 0],
													 		  ['scheduleDate', '<=', Carbon::now()->toDateString()]
													 		 ])
													 ->sum('installmentAmount');

			$duePartialInstallment = MfnLoanSchedule::where('loanIdFk', $loanId)
													->active()
													->partial()
													->where('scheduleDate', '<=', Carbon::now()->toDateString())
													->sum('partiallyPaidAmount');

			if($duePartialInstallment!=0)
				$duePartialInstallment = $installmentAmount - $duePartialInstallment;

			$dueLoanAmount = $dueCompleteInstallment + $duePartialInstallment;
			
			return $dueLoanAmount;
		}

		public function getSavingsDepositExistAfterSoftwareDate($memberId) {

			$depositNum = MfnSavingsDeposit::where([['memberIdFk', $memberId],
										  		    ['depositDate', '>', GetSoftwareDate::getSoftwareDate()]
										  		   ])
										   ->count();

			return $depositNum;
		}

		public function getSavingsWithdrawExistAfterSoftwareDate($memberId) {

			$withdrawNum = MfnSavingsWithdraw::where([['memberIdFk', $memberId],
										  		      ['withdrawDate', '>', GetSoftwareDate::getSoftwareDate()]
										  		     ])
										     ->count();

			return $withdrawNum;
		}

		public function getLoanCollectionExistAfterSoftwareDate($memberId) {

			$loanCollectionNum = MfnLoanCollection::where([['memberIdFk', $memberId],
												  		   ['collectionDate', '>', GetSoftwareDate::getSoftwareDate()]
												  		  ])
												  ->count();

			return $loanCollectionNum;
		}

		public function getCheckAnotherMemberTransferExists($memberId) {

			$transferExists = MfnMemberSamityTransfer::where([['memberIdFk', $memberId],
													          ['transferDate', '>=', GetSoftwareDate::getSoftwareDate()]
													         ])
													 ->count();

			return $transferExists;
		}

		public function getCheckAnotherProductTransferExists($memberId) {

			$transferExists = MfnMemberPrimaryProductTransfer::where([['memberIdFk', $memberId],
													                  ['transferDate', '>=', GetSoftwareDate::getSoftwareDate()]
															         ])
															 ->count();

			return $transferExists;
		}

		

    /*
	|--------------------------------------------------------------------------
	| MICRO FINANCE: END LOANS FUNCTION
	|--------------------------------------------------------------------------
	*/

   /*
   	|===================================================================================================================
   	|===================================================================================================================
	| MICRO FINANCE: Start Savings FUNCTION
   	|===================================================================================================================
   	|===================================================================================================================
	*/

		public function getSavingsProductsOption() {

			$savingsProductsOption = MfnSavingsProduct::active()->orderBy('code')->pluck('name', 'id')->toArray();
			
			return $savingsProductsOption;
		}

		public function getSavingsAccountNumberPerMember($memberId) {

			$savingsAccount = MfnSavingsAccount::active()
											   ->where('memberIdFk', $memberId)
											   ->select('id', 
														'savingsCode', 
														'accountOpeningDate', 
														'savingsProductIdFk',
														'autoProcessAmount',
														'branchIdFk',
														'samityIdFk'
													   )
											   ->get();
			
			return $savingsAccount;
		}

		public function getLatestPrimaryProductTransferDate($memberId) {

			$memberPrimaryProductTransferCount = DB::table('mfn_loan_primary_product_transfer')->where('memberIdFk', $memberId)->count();

			if($memberPrimaryProductTransferCount>0):
				$memberPrimaryProductTransferOB = DB::table('mfn_loan_primary_product_transfer')
												    ->select('transferDate')
												    ->where('memberIdFk', $memberId)
												    ->latest('transferDate')
												    ->first();

				return $memberPrimaryProductTransferOB->transferDate;
			else:
				return '0000-00-00';
			endif;
		}

		public function getSavingsDepositPerAccount($accountId, $primaryProductId, $dateForm) {

			$savingsDeposit = MfnSavingsDeposit::active()
											   ->where('accountIdFk', $accountId)
											   ->where('primaryProductIdFk', $primaryProductId)
											   ->where('depositDate', '>=', $dateForm)
											   ->sum('amount');
			
			return $savingsDeposit;
		}

		public function getSavingsWithdrawPerAccount($accountId, $primaryProductId, $dateForm) {

			$savingsWithdraw = MfnSavingsWithdraw::active()
												 ->where('accountIdFk', $accountId)
												 ->where('primaryProductIdFk', $primaryProductId)
												 ->where('withdrawDate', '>=', $dateForm)
												 ->sum('amount');
			
			return $savingsWithdraw;
		}

		public function getSavingsDepositPerAccountForProductTransferUpdate($accountId, $transferDate) {

			$savingsDeposit = MfnSavingsDeposit::active()
											   ->where([['accountIdFk', $accountId],
											   		    ['depositDate', '<=', $transferDate]
											   		   ])
											   ->sum('amount');
			
			return $savingsDeposit;
		}

		public function getSavingsWithdrawPerAccountForProductTransferUpdate($accountId, $transferDate) {

			$savingsWithdraw = MfnSavingsWithdraw::active()
												 ->where([['accountIdFk', $accountId],
												 		  ['withdrawDate', '<=', $transferDate]
												 		 ])
												 ->sum('amount');
			
			return $savingsWithdraw;
		}

		public function getCheckLoanExistsOfMember($memberId) {

			$loanCount = DB::table('mfn_loan')
						   ->where([['memberIdFk', $memberId],
						    	     ['isLoanCompleted', 0]
						    	    ])
						   ->count();

			return $loanCount>0?0:1;
		}

		/**
		 * [getNomineeOfMembers Function for return an array contains of nominee information]
		 * 
		 * @param  [json]  $nomineeJSON [Nominee information in JSON format]
		 * @return [array]              [Nominee information in array]
		 */
		public function getNomineeOfMembers($nomineeJSON) {

        	if($nomineeJSON!=''):
	        	foreach(json_decode($nomineeJSON) as $ob):
	            	$nomineeColArr[] = (array) $ob;

	            	$i=0;
	            	foreach($nomineeColArr as $nomineeSingleArr):
	            		foreach($nomineeSingleArr as $val):
	            			$nomineeMultiArr[$i] = $val;
	            		endforeach;
	            		$i++;
	            	endforeach;
	        	endforeach;

	        	$nomineeMultiArr = array_chunk($nomineeMultiArr, 3);

	        	$i=0;
	        	foreach($nomineeMultiArr as $val):
	    			$nomineeArr[$i]['name'] = $val[0];
	    			$nomineeArr[$i]['relationId'] = $val[1];
	    			$nomineeArr[$i]['share'] = $val[2];
	        		$i++;
	        	endforeach;

	        	return $nomineeArr;
	        else:
	        	return $nomineeArr = [];
	        endif;
		}

		public function getReferenceOfMembers($nomineeJSON) {

        	if($nomineeJSON!=''):
	        	foreach(json_decode($nomineeJSON) as $ob):
	            	$nomineeColArr[] = (array) $ob;

	            	$i=0;
	            	foreach($nomineeColArr as $nomineeSingleArr):
	            		foreach($nomineeSingleArr as $val):
	            			$nomineeMultiArr[$i] = $val;
	            		endforeach;
	            		$i++;
	            	endforeach;
	        	endforeach;

	        	$nomineeMultiArr = array_chunk($nomineeMultiArr, 2);

	        	$i=0;
	        	foreach($nomineeMultiArr as $val):
	    			$nomineeArr[$i]['name'] = $val[0];
	    			$nomineeArr[$i]['designationId'] = $val[1];
	        		$i++;
	        	endforeach;

	        	return $nomineeArr;
	        else:
	        	return $nomineeArr = [];
	        endif;
		}

		public function getLoanRescheduleForHoliday($dateFrom, $dateTo, $applicableFor, $orgId, $branchId, $samityId) {

			$dateF = $dateFrom->toDateString();
			$dateT = $dateTo->toDateString();
			
			if($dateFrom==$dateTo) {
				$date[] = $dateFrom->toDateString();
				$dayName[] = $dateFrom->format('l');   
			} else {
				$totalDays = $dateFrom->diffInDays($dateTo) + 1;

				if(date('D', strtotime($dateFrom->toDateString()))!='Fri'):
					$date[] = $dateFrom->toDateString();
					$dayName[] = $dateFrom->format('l'); 
				endif;
				
				for($i=0;$i<$totalDays-1;$i++):
					$curDate = $dateFrom->addDays(1)->toDateString();
					$curDayName = date('D', strtotime($curDate));

					if($curDayName!='Fri'):
						$date[] = $curDate;
						$dayName[] = $dateFrom->format('l');
					endif;
				endfor;

				$dayName = array_unique($dayName);
				$key = array_search('Friday', $dayName);
				
				if(false!==$key) {
				    unset($dayName[$key]);
				}   

				$dayName = array_values($dayName);
			}

			$newHolidayDateArr = $date;
			$master['date'] = $date;
			$master['dayName'] = $dayName;
			//$master[] = $totalDays;
			$master[] = date('D', strtotime($dateFrom->toDateString()));
			//$master[] = $key;
			

			//	GET ALL THE SAMITY LIST OF WHOSE SAMITY DAY'S ARE IN THESE DAYS.
			//	GET SAMITY DAY ID FROM SAMITY DAY.
			foreach($dayName as $key => $val):
				$samityDayIdArr[] = (int) self::getSamityDayId($val);
			endforeach;
			
			$master['samityDayIdArr'] = $samityDayIdArr;
			$samityDayIdTest = [];
			$dateFromWhereScheduleUpdate = '';
			$lastInstallmentDate = '';

			//	START LOAN SCHEDULED UPDATE FOR ORGANIZATION. 
			if($applicableFor=='org' && $orgId>0):
				$loanIdArr = [];
				$repaymentNoByloanIdArr = [];

				//	GET ALL THE BRANCH LIST OF THE ORGANIZATION.
				$branchOB = DB::table('gnr_branch')->where('companyId', $orgId)->select('id')->get();

				$branchIdArr = [];
				foreach($branchOB as $key => $val):
					$branchIdArr[] = $val->id;
				endforeach;

				foreach($branchIdArr as $branchId):
					foreach($samityDayIdArr as $samityDayId):
						if(count($date)>1):
							foreach($date as $d):
								if(Carbon::parse($d)->format('l')==self::getSamityDayNameValue($samityDayId)):
									$dateF = $d;
									break;
								endif;
							endforeach;
						endif;

						//	GET ALL THE SAMITY ID OF THIS BRANCH WHICH ARE IN THIS SAMITY DAY ID.
						$samityOB = DB::table('mfn_samity')->where('branchId', $branchId)->where('samityDayId', $samityDayId)->select('id')->get();
						
						$samityIdArr = [];
						foreach($samityOB as $key => $val):
							$samityIdArr[] = $val->id;
						endforeach;

						//	GET ALL THE LOAN ID WHICH ARE IN THESE SAMITY ID.
						foreach($samityIdArr as $key => $val):
							$loanOB = DB::table('mfn_loan')->where('samityIdFk', $val)->select('id', 'repaymentNo')->get();
							
							foreach($loanOB as $key => $val):
								$loanIdArr[] = $val->id;
								$repaymentNoByloanIdArr[$val->id] = $val->repaymentNo;
							endforeach;
						endforeach;

						//	GET HOLIDAY.
						$globalGovtHoliday = self::getGlobalGovtHoliday();
						$organizationHoliday = self::getOrganizationHoliday(1);
						//$branchHoliday = self::getBranchHolidayWithParam($branchId);
						//$samityHoliday = self::getSamityHolidayWithSamityParam($samityId);
						//$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));
						$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday));

						foreach($loanIdArr as $loanId):
							//if($loanId==1204):
							//	GET ALL THE CURRENT SCHEDULES DATES OF THE LOAN. 
							$scheduleDateArr = MfnLoanSchedule::where('loanIdFk', $loanId)->incomplete()->pluck('scheduleDate', 'installmentSl')->all();
							
							//	GET LOAN TYPE ID AND REPAYMENT FREQUENCY ID OF THE LOAN.
							$singleLoanOB = DB::table('mfn_loan')->where('id', $loanId)->select('loanTypeId', 'repaymentFrequencyIdFk')->get();

							$oldScheduleDateArr = $scheduleDateArr;
							$installmentStartFrom = key($scheduleDateArr);

							foreach($newHolidayDateArr as $newHolidayDate):
								if(Carbon::parse($newHolidayDate)->format('l')==self::getSamityDayNameValue($samityDayId)):
									$dateFromWhereScheduleUpdate = $newHolidayDate;
									break;
								endif;
							endforeach;

							$installmentSLFrom = array_search($dateFromWhereScheduleUpdate, $oldScheduleDateArr); 
							$a = $installmentSLFrom;

							//	FOR WEEKLY REPAYMENT FREQUENCY AND ONE TIME LOAN.
							if($singleLoanOB->repaymentFrequencyIdFk==1 || $singleLoanOB->loanTypeId==2):
								//	ADD 7 DAYS TO ALL THE SCHEDULES DATES.
								foreach($scheduleDateArr as $key => $val):
									$date = date_create($val);
									date_add($date,date_interval_create_from_date_string("7 days"));
									$scheduleDateArr[$key] = date_format($date,"Y-m-d");
								endforeach;

								$holidayFound = 0;
								$loanScheduleDateArr = [];
								
								//	PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
								$i = 0;
								while(count($loanScheduleDateArr)!=count($scheduleDateArr)):
									$dayDiff = (7 * $i) . 'days'; 
									$date = date_create($scheduleDateArr[$installmentStartFrom]);
									date_add($date, date_interval_create_from_date_string($dayDiff));
									
									//	CHECK IF A DATE IS MATCHES TO HOLIDAY.
									foreach($holiday as $key => $val):
										if(date_create($val)>=$date):
											if(date_create($val)==$date):
												$holidayFound = 1;
												break;
											endif;
										endif;
									endforeach;

									if($holidayFound==0)
										$loanScheduleDateArr[] = date_format($date, "Y-m-d");
									
									$holidayFound = 0;
									
									$i++;
								endwhile;

								//	UPDATE ALL THE INSTALLMENTS SCHEDULED WHICH ARE NOT COMPLETED YET.
								$j = 0;
								$testdata = [];
								foreach($loanScheduleDateArr as $key => $val):
									if($val>=$dateFromWhereScheduleUpdate):
										MfnLoanSchedule::where('loanIdFk', $loanId)
													   ->where('scheduleDate', '>=', $dateFromWhereScheduleUpdate)
													   ->incomplete()
													   ->where('installmentSl', $installmentSLFrom)
													   ->update(['scheduleDate' => $val]);

										// UPDATE FIRST REPAY DATE IN LOAN TABLE.
										if($installmentSLFrom==1)
											MfnLoan::where('id', $loanId)->update(['firstRepayDate' => $val]);

										//	GET LAST INSTALLMENT DATE.
										if($repaymentNoByloanIdArr[$loanId]==$installmentSLFrom)
											$lastInstallmentDate = $val;

										$testdata[$j] = $val;
										$installmentSLFrom++;
									endif;
									$j++;
								endforeach;

								// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
								if($lastInstallmentDate!='')
									MfnLoan::where('id', $loanId)->update(['lastInstallmentDate' => $lastInstallmentDate]);
							endif;

							//	FOR MONTHLY REPAYMENT FREQUENCY.
							if($singleLoanOB->repaymentFrequencyIdFk==2):
								$loanScheduleDateArr = $scheduleDateArr;
								
								$nextAvailableDate = Carbon::parse($dateF)->addWeeks(1);
								$curMonth = Carbon::parse($dateF)->month;

								$curMonthAvailableDates = [];
								$curMonthTotalDayFound = 0;

								//	STEP 1
								//	GET ALL THE AVAILABLE DATES OF CURRENT MONTH AFTER THE HOLIDAY START DATE.
								for($p=0;$curMonth==$nextAvailableDate->month;$p++):
									$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
									$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->addWeeks(1);
									$curMonthTotalDayFound += 1;
								endfor;

								//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
								$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
								$curMonthAvailableDates = array_values($curMonthAvailableDates);

								//	STEP 2
								//	GET ALL THE AVAILABLE DATES OF CURRENT MONTH BEFORE THE HOLIDAY START DATE.
								if(count($curMonthAvailableDates)==0):
									//$curMonthTotalDayFound += 1; 
									$nextAvailableDate = Carbon::parse($dateF)->subWeeks(1);
								 	$curMonth = Carbon::parse($dateF)->month;
								 	
									for($p=0;$curMonth==$nextAvailableDate->month;$p++):
										$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
										$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->subWeeks(1);
									endfor;

									//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
									$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
									$curMonthAvailableDates = array_values($curMonthAvailableDates);
								endif;

								//	STEP 3
								//	WHEN DATE IS NOT AVAILABLE IN CURRENT MONTH.
								//	CHECK AVAILABLE DATES IN NEXT MONTH.
								if(count($curMonthAvailableDates)==0):
									$curMonthTotalDayFound += 1; 
									$nextAvailableDate = Carbon::parse($dateF)->addWeeks($curMonthTotalDayFound);
								 	$nextMonth = $curMonth + 1;
								
								 	for($p=0;$nextMonth==$nextAvailableDate->month;$p++):
								 		$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
								 		$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->addWeeks(1);
								 	endfor;

								 	//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
									$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
									$curMonthAvailableDates = array_values($curMonthAvailableDates);
								endif;

								//	STEP 4
								//	IF THE PROPOSED NEW SCHEDULE DATE IS ALREADY IN THE SCHEDULE LIST.
								//	THEN MOVE TO THE PREVIOUS MONTH OF THE CURRENT MONTH FOR SEARCH THE AVAILABLE DATE.
								if(in_array( $curMonthAvailableDates[0], $oldScheduleDateArr)):
									$curMonthAvailableDates = [];
									$nextAvailableDate = Carbon::parse($dateF)->subWeek(1);
									$previousMonth = $curMonth - 1;

									for($p=0;$p<5;$p++):
							 			$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
							 			$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->subWeek(1);
								 	endfor;

								 	$prevMonthAvailableDates = [];
								 	foreach($curMonthAvailableDates as $date):
								 		if($previousMonth==Carbon::parse($date)->month):
								 			$prevMonthAvailableDates[] = $date;
								 		endif;
								 	endforeach;

								 	$curMonthAvailableDates = [];
								 	$curMonthAvailableDates = $prevMonthAvailableDates;

								 	//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
									$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);

									//	FILTERING ALL THE AVAILABLE DATES BY SCHEDULE DATE.
									$curMonthAvailableDates = array_diff($curMonthAvailableDates, $oldScheduleDateArr);
									$curMonthAvailableDates = array_values($curMonthAvailableDates);
								endif;

								//	UPDATE ALL THE INSTALLMENTS SCHEDULED WHICH ARE NOT COMPLETED YET.
								if(count($curMonthAvailableDates)>0):
									MfnLoanSchedule::where('loanIdFk', $loanId)
												   ->where('scheduleDate', '=', $dateFromWhereScheduleUpdate)
												   ->incomplete()
												   ->where('installmentSl', $installmentSLFrom)
												   ->update(['scheduleDate' => $curMonthAvailableDates[0]]);
								endif;

								// UPDATE FIRST REPAY DATE IN LOAN TABLE.
								if($installmentSLFrom==1)
									MfnLoan::where('id', $loanId)->update(['firstRepayDate' => $curMonthAvailableDates[0]]);

								// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
								if($repaymentNoByloanIdArr[$loanId]==$installmentSLFrom)
									MfnLoan::where('id', $loanId)->update(['lastInstallmentDate' => $curMonthAvailableDates[0]]);

							endif;
							//endif;
						endforeach;
						$samityDayIdTest[] = $samityDayId;
					endforeach;
				endforeach;
			endif;
			//	END LOAN SCHEDULED UPDATE FOR ORGANIZATION. 

			//	START LOAN SCHEDULED UPDATE FOR BRANCH. 
			if($applicableFor=='branch' && $branchId>0):
				$loanIdArr = [];
				$repaymentNoByloanIdArr = [];

				foreach($samityDayIdArr as $samityDayId):
					if(count($date)>1):
						foreach($date as $d):
							if(Carbon::parse($d)->format('l')==self::getSamityDayNameValue($samityDayId)):
								$dateF = $d;
								break;
							endif;
						endforeach;
					endif;
							
					//	GET ALL THE SAMITY ID OF THIS BRANCH WHICH ARE IN THIS SAMITY DAY ID.
					$samityOB = DB::table('mfn_samity')->where('branchId', $branchId)->where('samityDayId', $samityDayId)->select('id')->get();
					
					$samityIdArr = [];
					foreach($samityOB as $key => $val):
						$samityIdArr[] = $val->id;
					endforeach;

					//	GET ALL THE LOAN ID WHICH ARE IN THESE SAMITY ID.
					foreach($samityIdArr as $key => $val):
						$loanOB = DB::table('mfn_loan')->where('samityIdFk', $val)->select('id', 'repaymentNo')->get();
						
						foreach($loanOB as $key => $val):
							$loanIdArr[] = $val->id;
							$repaymentNoByloanIdArr[$val->id] = $val->repaymentNo;
						endforeach;
					endforeach;

					//	GET HOLIDAY.
					$globalGovtHoliday = self::getGlobalGovtHoliday();
					$organizationHoliday = self::getOrganizationHoliday(1);
					$branchHoliday = self::getBranchHolidayWithParam($branchId);
					//$samityHoliday = self::getSamityHolidayWithSamityParam($samityId);
					//$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));
					$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday));

					foreach($loanIdArr as $loanId):
						//if($loanId==1204):
						//	GET ALL THE CURRENT SCHEDULES DATES OF THE LOAN. 
						$scheduleDateArr = MfnLoanSchedule::where('loanIdFk', $loanId)->incomplete()->pluck('scheduleDate', 'installmentSl')->all();
						
						//	GET LOAN TYPE ID AND REPAYMENT FREQUENCY ID OF THE LOAN.
						$singleLoanOB = DB::table('mfn_loan')->where('id', $loanId)->select('loanTypeId', 'repaymentFrequencyIdFk')->get();

						$oldScheduleDateArr = $scheduleDateArr;
						$installmentStartFrom = key($scheduleDateArr);

						foreach($newHolidayDateArr as $newHolidayDate):
							if(Carbon::parse($newHolidayDate)->format('l')==self::getSamityDayNameValue($samityDayId)):
								$dateFromWhereScheduleUpdate = $newHolidayDate;
								break;
							endif;
						endforeach;

						$installmentSLFrom = array_search($dateFromWhereScheduleUpdate, $oldScheduleDateArr); 
						$a = $installmentSLFrom;

						//	FOR WEEKLY REPAYMENT FREQUENCY AND ONE TIME LOAN.
						if($singleLoanOB->repaymentFrequencyIdFk==1 || $singleLoanOB->loanTypeId==2):
							//	ADD 7 DAYS TO ALL THE SCHEDULES DATES.
							foreach($scheduleDateArr as $key => $val):
								$date = date_create($val);
								date_add($date,date_interval_create_from_date_string("7 days"));
								$scheduleDateArr[$key] = date_format($date,"Y-m-d");
							endforeach;

							$holidayFound = 0;
							$loanScheduleDateArr = [];
							
							//	PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
							$i = 0;
							while(count($loanScheduleDateArr)!=count($scheduleDateArr)):
								$dayDiff = (7 * $i) . 'days'; 
								$date = date_create($scheduleDateArr[$installmentStartFrom]);
								date_add($date, date_interval_create_from_date_string($dayDiff));
								
								//	CHECK IF A DATE IS MATCHES TO HOLIDAY.
								foreach($holiday as $key => $val):
									if(date_create($val)>=$date):
										if(date_create($val)==$date):
											$holidayFound = 1;
											break;
										endif;
									endif;
								endforeach;

								if($holidayFound==0)
									$loanScheduleDateArr[] = date_format($date, "Y-m-d");
								
								$holidayFound = 0;
								
								$i++;
							endwhile;

							//	UPDATE ALL THE INSTALLMENTS SCHEDULED WHICH ARE NOT COMPLETED YET.
							$j = 0;
							$testdata = [];
							foreach($loanScheduleDateArr as $key => $val):
								if($val>=$dateFromWhereScheduleUpdate):
									MfnLoanSchedule::where('loanIdFk', $loanId)
												   ->where('scheduleDate', '>=', $dateFromWhereScheduleUpdate)
												   ->incomplete()
												   ->where('installmentSl', $installmentSLFrom)
												   ->update(['scheduleDate' => $val]);

									// UPDATE FIRST REPAY DATE IN LOAN TABLE.
									if($installmentSLFrom==1)
										MfnLoan::where('id', $loanId)->update(['firstRepayDate' => $val]);

									//	GET LAST INSTALLMENT DATE.
									if($repaymentNoByloanIdArr[$loanId]==$installmentSLFrom)
										$lastInstallmentDate = $val;

									$testdata[$j] = $val;
									$installmentSLFrom++;
								endif;
								$j++;
							endforeach;

							// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
							if($lastInstallmentDate!='')
								MfnLoan::where('id', $loanId)->update(['lastInstallmentDate' => $lastInstallmentDate]);
						endif;

						//	FOR MONTHLY REPAYMENT FREQUENCY.
						if($singleLoanOB->repaymentFrequencyIdFk==2):
							$loanScheduleDateArr = $scheduleDateArr;
							
							$nextAvailableDate = Carbon::parse($dateF)->addWeeks(1);
							$curMonth = Carbon::parse($dateF)->month;

							$curMonthAvailableDates = [];
							$curMonthTotalDayFound = 0;

							//	STEP 1
							//	GET ALL THE AVAILABLE DATES OF CURRENT MONTH AFTER THE HOLIDAY START DATE.
							for($p=0;$curMonth==$nextAvailableDate->month;$p++):
								$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
								$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->addWeeks(1);
								$curMonthTotalDayFound += 1;
							endfor;

							//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
							$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
							$curMonthAvailableDates = array_values($curMonthAvailableDates);

							//	STEP 2
							//	GET ALL THE AVAILABLE DATES OF CURRENT MONTH BEFORE THE HOLIDAY START DATE.
							if(count($curMonthAvailableDates)==0):
								//$curMonthTotalDayFound += 1; 
								$nextAvailableDate = Carbon::parse($dateF)->subWeeks(1);
							 	$curMonth = Carbon::parse($dateF)->month;
							 	
								for($p=0;$curMonth==$nextAvailableDate->month;$p++):
									$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
									$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->subWeeks(1);
								endfor;

								//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
								$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
								$curMonthAvailableDates = array_values($curMonthAvailableDates);
							endif;

							//	STEP 3
							//	WHEN DATE IS NOT AVAILABLE IN CURRENT MONTH.
							//	CHECK AVAILABLE DATES IN NEXT MONTH.
							if(count($curMonthAvailableDates)==0):
								$curMonthTotalDayFound += 1; 
								$nextAvailableDate = Carbon::parse($dateF)->addWeeks($curMonthTotalDayFound);
							 	$nextMonth = $curMonth + 1;
							
							 	for($p=0;$nextMonth==$nextAvailableDate->month;$p++):
							 		$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
							 		$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->addWeeks(1);
							 	endfor;

							 	//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
								$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
								$curMonthAvailableDates = array_values($curMonthAvailableDates);
							endif;

							//	STEP 4
							//	IF THE PROPOSED NEW SCHEDULE DATE IS ALREADY IN THE SCHEDULE LIST.
							//	THEN MOVE TO THE PREVIOUS MONTH OF THE CURRENT MONTH FOR SEARCH THE AVAILABLE DATE.
							if(in_array( $curMonthAvailableDates[0], $oldScheduleDateArr)):
								$curMonthAvailableDates = [];
								$nextAvailableDate = Carbon::parse($dateF)->subWeek(1);
								$previousMonth = $curMonth - 1;

								for($p=0;$p<5;$p++):
						 			$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
						 			$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->subWeek(1);
							 	endfor;

							 	$prevMonthAvailableDates = [];
							 	foreach($curMonthAvailableDates as $date):
							 		if($previousMonth==Carbon::parse($date)->month):
							 			$prevMonthAvailableDates[] = $date;
							 		endif;
							 	endforeach;

							 	$curMonthAvailableDates = [];
							 	$curMonthAvailableDates = $prevMonthAvailableDates;

							 	//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
								$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);

								//	FILTERING ALL THE AVAILABLE DATES BY SCHEDULE DATE.
								$curMonthAvailableDates = array_diff($curMonthAvailableDates, $oldScheduleDateArr);
								$curMonthAvailableDates = array_values($curMonthAvailableDates);
							endif;

							//	UPDATE ALL THE INSTALLMENTS SCHEDULED WHICH ARE NOT COMPLETED YET.
							if(count($curMonthAvailableDates)>0):
								MfnLoanSchedule::where('loanIdFk', $loanId)
											   ->where('scheduleDate', '=', $dateFromWhereScheduleUpdate)
											   ->incomplete()
											   ->where('installmentSl', $installmentSLFrom)
											   ->update(['scheduleDate' => $curMonthAvailableDates[0]]);
							endif;

							// UPDATE FIRST REPAY DATE IN LOAN TABLE.
							if($installmentSLFrom==1)
								MfnLoan::where('id', $loanId)->update(['firstRepayDate' => $curMonthAvailableDates[0]]);

							// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
							if($repaymentNoByloanIdArr[$loanId]==$installmentSLFrom)
								MfnLoan::where('id', $loanId)->update(['lastInstallmentDate' => $curMonthAvailableDates[0]]);

						endif;
						//endif;
					endforeach;
					$samityDayIdTest[] = $samityDayId;
				endforeach;
			endif;
			//	END LOAN SCHEDULED UPDATE FOR BRANCH. 

			//	START LOAN SCHEDULED UPDATE FOR SAMITY. 
			if($applicableFor=='samity' && $samityId>0):
				//	GET SAMITY DAY ID OF THE SAMITY.
				$samityDayId = self::getSamityDayIdOfSamity($samityId);
				
				//	GET ALL THE LOAN ID WHICH ARE IN THESE SAMITY ID.
				$loanIdArr = [];
				$repaymentNoByloanIdArr = [];
				$loanOB = DB::table('mfn_loan')->where('samityIdFk', $samityId)->select('id', 'repaymentNo')->get();
				
				foreach($loanOB as $key => $val):
					$loanIdArr[] = $val->id;
					$repaymentNoByloanIdArr[$val->id] = $val->repaymentNo;
				endforeach;

				//	GET HOLIDAY.
				$globalGovtHoliday = self::getGlobalGovtHoliday();
				$organizationHoliday = self::getOrganizationHoliday(1);
				$branchHoliday = self::getBranchHolidayWithParam($branchId);
				$samityHoliday = self::getSamityHolidayWithSamityParam($samityId);
				$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

				//$loanIdArr = array(1202);

				foreach($loanIdArr as $loanId):
					//	GET ALL THE CURRENT SCHEDULES DATES OF THE LOAN. 
					$scheduleDateArr = MfnLoanSchedule::where('loanIdFk', $loanId)->incomplete()->pluck('scheduleDate', 'installmentSl')->all();
					
					//	GET LOAN TYPE ID AND REPAYMENT FREQUENCY ID OF THE LOAN.
					$singleLoanOB = DB::table('mfn_loan')->where('id', $loanId)->select('loanTypeId', 'repaymentFrequencyIdFk')->get();

					$oldScheduleDateArr = $scheduleDateArr;
					$installmentStartFrom = key($scheduleDateArr);

					foreach($newHolidayDateArr as $newHolidayDate):
						if(Carbon::parse($newHolidayDate)->format('l')==self::getSamityDayNameValue($samityDayId)):
							$dateFromWhereScheduleUpdate = $newHolidayDate;
							break;
						endif;
					endforeach;

					$installmentSLFrom = array_search($dateFromWhereScheduleUpdate, $oldScheduleDateArr); 
					$a = $installmentSLFrom;

					//	FOR WEEKLY REPAYMENT FREQUENCY AND ONE TIME LOAN.
					if($singleLoanOB->repaymentFrequencyIdFk==1 || $singleLoanOB->loanTypeId==2):
						//	ADD 7 DAYS TO ALL THE SCHEDULES DATES.
						foreach($scheduleDateArr as $key => $val):
							$date = date_create($val);
							date_add($date,date_interval_create_from_date_string("7 days"));
							$scheduleDateArr[$key] = date_format($date,"Y-m-d");
						endforeach;

						$holidayFound = 0;
						$loanScheduleDateArr = [];
						
						//	PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
						$i = 0;
						while(count($loanScheduleDateArr)!=count($scheduleDateArr)):
							$dayDiff = (7 * $i) . 'days'; 
							$date = date_create($scheduleDateArr[$installmentStartFrom]);
							date_add($date, date_interval_create_from_date_string($dayDiff));
							
							//	CHECK IF A DATE IS MATCHES TO HOLIDAY.
							foreach($holiday as $key => $val):
								if(date_create($val)>=$date):
									if(date_create($val)==$date):
										$holidayFound = 1;
										break;
									endif;
								endif;
							endforeach;

							if($holidayFound==0)
								$loanScheduleDateArr[] = date_format($date, "Y-m-d");
							
							$holidayFound = 0;
							
							$i++;
						endwhile;

						//	UPDATE ALL THE INSTALLMENTS SCHEDULED WHICH ARE NOT COMPLETED YET.
						$j = 0;
						$testdata = [];
						foreach($loanScheduleDateArr as $key => $val):
							if($val>=$dateFromWhereScheduleUpdate):
								MfnLoanSchedule::where('loanIdFk', $loanId)
											   ->where('scheduleDate', '>=', $dateFromWhereScheduleUpdate)
											   ->incomplete()
											   ->where('installmentSl', $installmentSLFrom)
											   ->update(['scheduleDate' => $val]);
								
								// UPDATE FIRST REPAY DATE IN LOAN TABLE.
								if($installmentSLFrom==1)
									MfnLoan::where('id', $loanId)->update(['firstRepayDate' => $val]);
								
								//	GET LAST INSTALLMENT DATE.
								if($repaymentNoByloanIdArr[$loanId]==$installmentSLFrom)
									$lastInstallmentDate = $val;
								
								$testdata[$j] = $val;
								$installmentSLFrom++;
							endif;
							$j++;
						endforeach;
						
						// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
						if($lastInstallmentDate!='')
							MfnLoan::where('id', $loanId)->update(['lastInstallmentDate' => $lastInstallmentDate]);
					endif;

					//	FOR MONTHLY REPAYMENT FREQUENCY.
					if($singleLoanOB->repaymentFrequencyIdFk==2):
						$loanScheduleDateArr = $scheduleDateArr;

						$nextAvailableDate = Carbon::parse($dateF)->addWeeks(1);
						$curMonth = Carbon::parse($dateF)->month;

						$curMonthAvailableDates = [];
						$curMonthTotalDayFound = 0;

						//	STEP 1
						//	GET ALL THE AVAILABLE DATES OF CURRENT MONTH AFTER THE HOLIDAY START DATE.
						for($p=0;$curMonth==$nextAvailableDate->month;$p++):
							$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
							$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->addWeeks(1);
							$curMonthTotalDayFound += 1;
						endfor;

						//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
						$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
						$curMonthAvailableDates = array_values($curMonthAvailableDates);

						//	STEP 2
						//	GET ALL THE AVAILABLE DATES OF CURRENT MONTH BEFORE THE HOLIDAY START DATE.
						if(count($curMonthAvailableDates)==0):
							//$curMonthTotalDayFound += 1; 
							$nextAvailableDate = Carbon::parse($dateF)->subWeeks(1);
						 	$curMonth = Carbon::parse($dateF)->month;
						 	
							for($p=0;$curMonth==$nextAvailableDate->month;$p++):
								$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
								$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->subWeeks(1);
							endfor;

							//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
							$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
							$curMonthAvailableDates = array_values($curMonthAvailableDates);
						endif;

						//	STEP 3
						//	WHEN DATE IS NOT AVAILABLE IN CURRENT MONTH.
						//	CHECK AVAILABLE DATES IN NEXT MONTH.
						if(count($curMonthAvailableDates)==0):
							$curMonthTotalDayFound += 1; 
							$nextAvailableDate = Carbon::parse($dateF)->addWeeks($curMonthTotalDayFound);
						 	$nextMonth = $curMonth + 1;
						
						 	for($p=0;$nextMonth==$nextAvailableDate->month;$p++):
						 		$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
						 		$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->addWeeks(1);
						 	endfor;

						 	//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
							$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);
							$curMonthAvailableDates = array_values($curMonthAvailableDates);
						endif;

						//	STEP 4
						//	IF THE PROPOSED NEW SCHEDULE DATE IS ALREADY IN THE SCHEDULE LIST.
						//	THEN MOVE TO THE PREVIOUS MONTH OF THE CURRENT MONTH FOR SEARCH THE AVAILABLE DATE.
						if(in_array( $curMonthAvailableDates[0], $oldScheduleDateArr)):
							$curMonthAvailableDates = [];
							$nextAvailableDate = Carbon::parse($dateF)->subWeek(1);
							$previousMonth = $curMonth - 1;

							for($p=0;$p<5;$p++):
					 			$curMonthAvailableDates[] = $nextAvailableDate->format('Y-m-d');
					 			$nextAvailableDate = Carbon::parse($curMonthAvailableDates[$p])->subWeek(1);
						 	endfor;

						 	$prevMonthAvailableDates = [];
						 	foreach($curMonthAvailableDates as $date):
						 		if($previousMonth==Carbon::parse($date)->month):
						 			$prevMonthAvailableDates[] = $date;
						 		endif;
						 	endforeach;

						 	$curMonthAvailableDates = [];
						 	$curMonthAvailableDates = $prevMonthAvailableDates;

						 	//	FILTERING ALL THE AVAILABLE DATES BY HOLIDAY.
							$curMonthAvailableDates = array_diff($curMonthAvailableDates, $holiday);

							//	FILTERING ALL THE AVAILABLE DATES BY SCHEDULE DATE.
							$curMonthAvailableDates = array_diff($curMonthAvailableDates, $oldScheduleDateArr);
							$curMonthAvailableDates = array_values($curMonthAvailableDates);
						endif;

						//	UPDATE ALL THE INSTALLMENTS SCHEDULED WHICH ARE NOT COMPLETED YET.
						if(count($curMonthAvailableDates)>0):
							MfnLoanSchedule::where('loanIdFk', $loanId)
										   ->where('scheduleDate', '=', $dateFromWhereScheduleUpdate)
										   ->incomplete()
										   ->where('installmentSl', $installmentSLFrom)
										   ->update(['scheduleDate' => $curMonthAvailableDates[0]]);
						endif;

						// UPDATE FIRST REPAY DATE IN LOAN TABLE.
						if($installmentSLFrom==1)
							MfnLoan::where('id', $loanId)->update(['firstRepayDate' => $curMonthAvailableDates[0]]);

						// UPDATE LAST INSTALLMENT DATE IN LOAN TABLE.
						if($repaymentNoByloanIdArr[$loanId]==$installmentSLFrom)
							MfnLoan::where('id', $loanId)->update(['lastInstallmentDate' => $curMonthAvailableDates[0]]);
					endif;
				endforeach;
				$samityDayIdTest[] = $samityDayId;
			endif;
			//	END LOAN SCHEDULED UPDATE FOR SAMITY. 

			$master['loanScheduleDateArr'] = @$loanScheduleDateArr;
			//$master['scheduleDateArr'] = @$scheduleDateArr;
			$master['oldScheduleDateArr'] = @$oldScheduleDateArr; 
			$master['testdata'] = @$testdata;
			$master['holiday'] = @$holiday;
			$master['installmentStartFrom'] = @$installmentStartFrom; 
			$master['installmentSLFrom'] = @$installmentSLFrom; 
			$master['dateFromWhereScheduleUpdate'] = @$dateFromWhereScheduleUpdate;  
			$master['a'] = @$a;  
			$master['repaymentFrequencyId'] = @$singleLoanOB->repaymentFrequencyIdFk;
			$master['lastInstallmentDate'] = @$lastInstallmentDate;

			$master['nextAvailableDate'] = @$nextAvailableDate; 
			$master['curMonthAvailableDates'] = @$curMonthAvailableDates; 
			$master['curMonthTotalDayFound'] = @$curMonthTotalDayFound;
			//$master['curMonthNum'] = @$curMonth;
			//$master['prevMonthNum'] = @$previousMonth;

			//$master['samityIdArr'] = @$samityIdArr;
			$master['samityDayIdTest'] = @$samityDayIdTest;
			$master['loanIdArr'] = @$loanIdArr;
			$master['branchIdArr'] = @$branchIdArr; 
			$master['repaymentNoByloanIdArr'] = @$repaymentNoByloanIdArr; 

			$master['globalGovtHoliday'] = @$globalGovtHoliday;
			$master['organizationHoliday'] = @$organizationHoliday;
			$master['branchHoliday'] = @$branchHoliday;

			$master['dateFrom'] = @$dateF;
			$master['dateTo'] = @$dateT;

			$master['orgId'] = @$orgId;
			$master['branchId'] = @$branchId;
			$master['samityId'] = @$samityId;

			return $master;
		}

		public function getRegularLoanCollectionStatus($regularLoanId) {

			$installmentCollectionStart = MfnLoanSchedule::where('loanIdFk', $regularLoanId)
													     ->where(function($query) {
													     		$query->where('isCompleted', 1)
													     			  ->orWhere('isPartiallyPaid', 1);	
													       })
													     ->count();

			return $installmentCollectionStart;
		}

		public function getArrayCutOff($array, $index) {

			foreach($array as $key => $val):
				if($key==$index):
					$formattedArr[$key] = $val;
					break;
				endif;
			endforeach;

			return $formattedArr;
		}

		public function getRegularLoanRescheduleExists($regularLoanId) {

			$rescheduleCount = MfnLoanReschedule::where('loanIdFk', $regularLoanId)->count();
			
			return $rescheduleCount>0?1:0;
		}

		

		
		

   /*
   	|========================================================================================================================
   	|========================================================================================================================
	| MICRO FINANCE: END Savings FUNCTION
   	|========================================================================================================================
   	|========================================================================================================================
	*/

//Check The Date is HoliDay or Not

		public static function isHoliday($date, $targetBranchId){
		    $date = Carbon::parse($date)->format('Y-m-d');
		    $isHoliday = 0;
		    //get holidays
		    $holiday = (int) DB::table('mfn_setting_holiday')->where('status',1)->where('date',$date)->value('id');
		    if ($holiday>0) {
		        $isHoliday = 1;
		    }

		    // get the organazation id and branch id of the loggedin user
		    if ($isHoliday!=1) {
		        $userBranchId = Auth::user()->branchId;
		        $userOrgId = Auth::user()->company_id_fk;

		        if($targetBranchId!=1){
		            $userBranchId = $targetBranchId;
		            $userOrgId = DB::table('gnr_branch')->where('id',$targetBranchId)->value('companyId');
		        }

		        $holiday = (int) DB::table('mfn_setting_orgBranchSamity_holiday')
		                               ->where('status',1)
		                               ->where(function ($query) use ($userBranchId,$userOrgId) {
		                                    $query->where('ogrIdFk', '=', $userOrgId)
		                                          ->orWhere('branchIdFk', '=', $userBranchId);
		                                })
		                               ->where('dateFrom','<=',$date)
		                               ->where('dateTo','>=',$date)
		                               ->value('id');
		        if($holiday>0){
		            $isHoliday = 1;
		        }
		    }

		    return $isHoliday;
		    
		}




	   /*
	   	|======================================================================================================================
	   	|======================================================================================================================
		| MICRO FINANCE: Start FUNCTIONs for Reports
	   	|======================================================================================================================
	   	|======================================================================================================================
		*/

		public function getYearsOption() {

			$yearsOption = array_combine(range(date("Y")+1, 2016), range(date("Y")+1, 2016));
			
			return $yearsOption;
		}

		public function getMonthsOption() {
			
			$monthsOption = array(
		        1   =>  'January',
		        2   =>  'February',
		        3   =>  'March',
		        4   =>  'April',
		        5   =>  'May',
		        6   =>  'June',
		        7   =>  'July',
		        8   =>  'August',
		        9   =>  'September',
		        10  =>  'October',
		        11  =>  'November',
		        12  =>  'December'
		    );
			
			return $monthsOption;
		}


		public function getWeeklyDaysOfMonth($year, $month, $selectedDayValue){

	        if($selectedDayValue==1){
	            $selectedDay='Saturday';
	        }else if($selectedDayValue==2){
	            $selectedDay='Sunday';
	        }else if($selectedDayValue==3){
	            $selectedDay='Monday';
	        }else if($selectedDayValue==4){
	            $selectedDay='Tuesday';
	        }else if($selectedDayValue==5){
	            $selectedDay='Wednesday';
	        }else if($selectedDayValue==6){
	            $selectedDay='Thursday';
	        }else if($selectedDayValue==7){
	            $selectedDay='Friday';
	        }     
	        $weekDays = array();

	        $givenDate = strtotime($year.'-'.$month.'-1');
	        $for_start = strtotime($selectedDay, $givenDate);
	        $for_end=strtotime(date("Y-m-t",$givenDate));
	        
	        for ($i = $for_start; $i <= $for_end; $i = strtotime('+1 week', $i)) {
	            $weekDays[] = date('Y-m-d', $i);;
	        }
	        return $weekDays;

	    }


		public function getProductCategoryWiseLoanProduct($productCategory) {

			$productCategoryWiseProduct = MfnProduct::active()
									     	        ->where('productCategoryId', $productCategory)
									  		        ->select(DB::raw("CONCAT(name, ' - ', shortName) AS nameWithShortName"), 'id')
										            ->get()
									  		        ->pluck('nameWithShortName', 'id')
									  		        ->all();

			return $productCategoryWiseProduct;
		}

		public function getBranchWiseSamityOptions($branchId) {

			$dayWiseSamity = MfnSamity::active()
						     	      ->where('branchId', $branchId)
						  		      ->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
							          ->get()
						  		      ->pluck('nameWithCode', 'id')
						  		      ->all();

			return $dayWiseSamity;
		}

		public function getFieldOfficerWiseSamityOptions($fieldOfficerId) {

			$samity = MfnSamity::active()
				     	       ->where('fieldOfficerId', $fieldOfficerId)
				  		       ->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
					           ->get()
				  		       ->pluck('nameWithCode', 'id')
				  		       ->all();

			return $samity;
		}

		public function getMemberNameWithCode($memberId) {

			$memberOB = MfnMemberInformation::active()
							     	        ->where('id', $memberId)
							  		        ->select(DB::raw("CONCAT(name, ' - ', code) AS nameWithCode"))
								            ->first();

			return $memberOB->nameWithCode;
		}

		public function branchWiseProductArr($branchId, $fieldName){

	        $productStr=DB::table('gnr_branch')->where('id', $branchId)->value($fieldName);
	        $productStr =  str_replace(array('"', '[', ']'),'', $productStr);
	        $productArr = array_map('intval', explode(',', $productStr));

	        return $productArr;
	    }

	    public function getSoftwareDate(){
            $userBarnchId = Auth::user()->branchId;

            $softwareDate = DB::table('mfn_day_end')->where('branchIdFk', $userBarnchId)->where('isLocked',0)->value('date');
            if ($softwareDate=='' || $softwareDate==null) {
                $softwareDate = DB::table('gnr_branch')->where('id', $userBarnchId)->value('softwareStartDate');
            }            

            return $softwareDate;
        }

        // public  function getMonthName($monthValue) {
            
        //     $monthsOption = array(
        //         1   =>  'January',
        //         2   =>  'February',
        //         3   =>  'March',
        //         4   =>  'April',
        //         5   =>  'May',
        //         6   =>  'June',
        //         7   =>  'July',
        //         8   =>  'August',
        //         9   =>  'September',
        //         10  =>  'October',
        //         11  =>  'November',
        //         12  =>  'December'
        //     );
            
        //     return $monthsOption[$monthValue];
        // }

	/*
   	|==================================================================================================================
   	|==================================================================================================================
	| MICRO FINANCE: End FUNCTIONs for Reports
   	|==================================================================================================================
   	|==================================================================================================================
	*/
	
	
	}	