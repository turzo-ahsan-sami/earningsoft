<?php

	namespace App\Http\Controllers\microfin\samity;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use App\microfin\samity\MfnSamityFieldOfficerChange;
	use App\microfin\samity\MfnSamity;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use App\Http\Controllers\Controller;
	use App\Http\Controllers\microfin\MicroFinance;
	use Illuminate\Support\Facades\Auth;
	use App\Traits\GetSoftwareDate;
	use App\Http\Controllers\microfin\MicroFin;
	//use App\Service\Service;
	use App\Http\Controllers\gnr\Service;
	use App;


	class MfnSamityFieldOfficerChangeController extends Controller {

		protected $MicroFinance;

		private $TCN;

		public function __construct() {

			$this->MicroFinance = new MicroFinance;

			$this->TCN = array(
					array('SL No.', 70),
					// array('Samity Code', 0),
					array('Samity Code & Name', 0),
					array('Branch Code & Name', 0),
					array('Previous Field Officer', 0),
					array('New Field Officer', 0),
					array('Effective Date', 0),
					array('Action', 80)
				);
		}

		public function index() {

			// $samityFieldOfficerChanges = new MfnSamityFieldOfficerChange;

			$branchId = Auth::user()->branchId;

			$TCN = $this->TCN;

			if ($branchId != 1) {
				//$branchWiseSamityList = MfnSamity::branchWise()->pluck('id')->toArray();
				//print_r($branchWiseSamityList);
				$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
								//print_r($branchIdArray);

				$branchWiseSamityList = MfnSamity::whereIn('branchId', $branchIdArray)->pluck('id')->toArray();
				//dd($branchWiseSamityList);


				$samityFieldOfficerChanges = MfnSamityFieldOfficerChange::whereIn('samityId', $branchWiseSamityList)->where('softDel', '=', 0)->paginate(20);
			}
			else {
				$samityFieldOfficerChanges = MfnSamityFieldOfficerChange::where('softDel', '=', 0)->orderBy('effectiveDate', 'desc')->paginate(20);
			}

			$damageData = array(
				'TCN' 	  					 =>  $TCN,
				'samityFieldOfficerChanges'	 =>  $samityFieldOfficerChanges,
				'MicroFinance'      		 =>  $this->MicroFinance
			);

			return view('microfin.samity.samityFieldOfficerChange.viewSamityFieldOfficerChange', ['damageData' => $damageData]);
		}

		public function addSamityFieldOfficerChange() {
			$userIdArray = array();
			$date = GetSoftwareDate::getSoftwareDate();

			$samityList = $this->MicroFinance->getSamity();
			
            $damageData = array(
				'samityList'  =>  $samityList
			);

			$branchId = Auth::user()->branchId;

			if ($branchId != 1) {
				$date = MicroFin::getSoftwareDateBranchWise($branchId);
			}

			$samityAssignedFieldOfficers = DB::table('mfn_samity')
				// ->where('branchId', $branchId)
				->where([['branchId', $branchId], ['openingDate', '<=', $date]])
				->where(function ($query) use ($date) {
					$query->where([['closingDate', '>=', $date]])
					->orWhere([['closingDate', '=', '0000-00-00']])
					->orWhere([['closingDate', '=', null]]);
				})
				->groupBy('fieldOfficerId')
				->pluck('fieldOfficerId')
				->toArray();

			$samityTransferedFieldOfficers = DB::table('mfn_samity_field_officer_change')
				->where([['branchId', $branchId], ['effectiveDate', '>=', $date]])
				->groupBy('fieldOfficerId')
				->pluck('fieldOfficerId')
				->toArray();
			
			$samityAssignedFieldOfficers = array_merge($samityAssignedFieldOfficers, $samityTransferedFieldOfficers);

			$samityAssignedFieldOfficers = array_unique($samityAssignedFieldOfficers);
			
			$curentfieldOfficerInfo = array('' => 'Select') + 
				DB::table('hr_emp_org_info')
				->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
				->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
				// ->where('hr_emp_org_info.branch_id_fk', $branchId)
				->where(function ($query) use ($date) {
					$query->where([['terminate_resignation_date', $date]])
					->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
					->orWhere([['terminate_resignation_date', '=', null]]);
				})
				->whereIn('hr_emp_org_info.emp_id_fk', $samityAssignedFieldOfficers)
				->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
				->get()
				->pluck('nameWithCode', 'id')
				->all();

			// dd($curentfieldOfficerInfo);

			$branchManager = DB::table('hr_settings_position')
				->where('name', 'like', '%Branch Manager%')
				->pluck('id')
				->toArray();

			$areaManager = DB::table('hr_settings_position')
				->where('name', 'like', '%Area Manager%')
				->pluck('id')
				->toArray();

			$zonalManager = DB::table('hr_settings_position')
				->where('name', 'like', '%Zonal Manager%')
				->pluck('id')
				->toArray();

			$cook = DB::table('hr_settings_position')
				->where('name', 'like', '%Cook%')
				->pluck('id')
				->toArray();

			// $transferedUsers = DB::table('hr_transfer')
			// 	->where([['pre_branch_id_fk', $branchId], ['effect_date', '<=' $date]])
			// 	->pluck('users_id_fk')
			// 	->toArray();

			$transferedUsers = DB::select( DB::raw("SELECT * FROM `hr_transfer` WHERE effect_date>'$date' AND pre_branch_id_fk='$branchId' AND id IN (SELECT MIN(id) FROM `hr_transfer` WHERE effect_date>'$date' AND pre_branch_id_fk='$branchId')") );

			if (sizeof($transferedUsers) > 0) {
				foreach ($transferedUsers as $key => $transferedUsersValue) {
					
					$userIdArray[] = $transferedUsersValue->users_id_fk;
					
				}

				$transferedEmployee = DB::table('users')
						->whereIn('id', $userIdArray)
						->pluck('emp_id_fk')
						->toArray();

				$transferedEmployeeRechecked = DB::table('hr_emp_org_info')
					->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
					->where([['hr_emp_org_info.joining_date', '<=', $date]])
					// ->where('hr_settings_position.id', '=', 122)
					->whereNotIn('hr_settings_position.id', $branchManager)
					->whereNotIn('hr_settings_position.id', $areaManager)
					->whereNotIn('hr_settings_position.id', $zonalManager)
					->whereNotIn('hr_settings_position.id', $cook)
					->where(function ($query) use ($date) {
						$query->where([['terminate_resignation_date', '>=', $date]])
						->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
						->orWhere([['terminate_resignation_date', '=', null]]);
					})
					->whereIn('hr_emp_org_info.emp_id_fk',$transferedEmployee)
					->pluck('hr_emp_org_info.emp_id_fk')
					->toArray();

				$newfieldOfficerInfoArray = array('' => 'Select') + 
					DB::table('hr_emp_org_info')
					->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
					->where([['hr_emp_org_info.branch_id_fk', $branchId], ['hr_emp_org_info.joining_date', '<=', $date]])
					// ->where('hr_settings_position.id', '=', 122)
					->whereNotIn('hr_settings_position.id', $branchManager)
					->whereNotIn('hr_settings_position.id', $areaManager)
					->whereNotIn('hr_settings_position.id', $zonalManager)
					->whereNotIn('hr_settings_position.id', $cook)
					->where(function ($query) use ($date) {
						$query->where([['terminate_resignation_date', '>=', $date]])
						->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
						->orWhere([['terminate_resignation_date', '=', null]]);
					})
					->pluck('hr_emp_org_info.emp_id_fk')
					->toArray();

				$newfieldOfficerInfoArray = array_merge($newfieldOfficerInfoArray, $transferedEmployeeRechecked);

				$newfieldOfficerInfo = DB::table('hr_emp_general_info')
					->whereIn('id', $newfieldOfficerInfoArray)
					->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
					->get()
					->pluck('nameWithCode', 'id')
					->all();

				// dd($transferedUsers, $transferedEmployee, $transferedEmployeeRechecked, $newfieldOfficerInfoArray, $newfieldOfficerInfo);
			}
			else {
				$newfieldOfficerInfo = array('' => 'Select') + 
					DB::table('hr_emp_org_info')
					->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
					->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
					->where([['hr_emp_org_info.branch_id_fk', $branchId], ['hr_emp_org_info.joining_date', '<=', $date]])
					// ->where('hr_settings_position.id', '=', 122)
					->whereNotIn('hr_settings_position.id', $branchManager)
					->whereNotIn('hr_settings_position.id', $areaManager)
					->whereNotIn('hr_settings_position.id', $zonalManager)
					->whereNotIn('hr_settings_position.id', $cook)
					->where(function ($query) use ($date) {
						$query->where([['terminate_resignation_date', '>=', $date]])
						->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
						->orWhere([['terminate_resignation_date', '=', null]]);
					})
					->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
					->get()
					->pluck('nameWithCode', 'id')
					->all();
			}

			
			
			// $transferedEmployee = DB::table('users')
			// 	->whereIn('id', $transferedUsers)
			// 	->pluck('emp_id_fk')
			// 	->toArray();

			

            $damageData = array(
				'curentfieldOfficerInfo'  =>  $curentfieldOfficerInfo,
				'newOfficerInfo'  		  =>  $newfieldOfficerInfo
			);

			// dd($damageData);

			// dd($date, $curentfieldOfficerInfo, $branchId, $samityAssignedFieldOfficers, $damageData);

			return view('microfin.samity.samityFieldOfficerChange.addSamityFieldOfficerChange', ['damageData' => $damageData]);
		}

		public function getfieldOfficerByBranch (Request $req) {
			$branchId = $req->id;
			$date = MicroFin::getSoftwareDateBranchWise($branchId);

			$samityAssignedFieldOfficers = DB::table('mfn_samity')
				->where([['branchId', $branchId], ['openingDate', '<=', $date]])
				->where(function ($query) use ($date) {
					$query->where([['closingDate', '>=', $date]])
					->orWhere([['closingDate', '=', '0000-00-00']])
					->orWhere([['closingDate', '=', null]]);
				})
				->groupBy('fieldOfficerId')
				->pluck('fieldOfficerId')
				->toArray();

			$samityTransferedFieldOfficers = DB::table('mfn_samity_field_officer_change')
				->where([['branchId', $branchId], ['effectiveDate', '>=', $date]])
				->groupBy('fieldOfficerId')
				->pluck('fieldOfficerId')
				->toArray();

			// dd($samityAssignedFieldOfficers, $samityTransferedFieldOfficers);
			
			$samityAssignedFieldOfficers = array_merge($samityAssignedFieldOfficers, $samityTransferedFieldOfficers);

			$samityAssignedFieldOfficers = array_unique($samityAssignedFieldOfficers);

			// dd($samityAssignedFieldOfficers, $samityTransferedFieldOfficers);
			
			
			$curentfieldOfficerInfo = DB::table('hr_emp_org_info')
				->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
				->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
				// ->where('hr_emp_org_info.branch_id_fk', $branchId)
				// ->where(function ($query) use ($date) {
				// 	$query->where([['terminate_resignation_date', '>', $date]])
				// 	->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
				// 	->orWhere([['terminate_resignation_date', '=', null]]);
				// })
				->whereIn('hr_emp_org_info.emp_id_fk', $samityAssignedFieldOfficers)
				->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
				->get();

			// dd($curentfieldOfficerInfo);

			$branchManager = DB::table('hr_settings_position')
				->where('name', 'like', '%Branch Manager%')
				->pluck('id')
				->toArray();

			$areaManager = DB::table('hr_settings_position')
				->where('name', 'like', '%Area Manager%')
				->pluck('id')
				->toArray();

			$zonalManager = DB::table('hr_settings_position')
				->where('name', 'like', '%Zonal Manager%')
				->pluck('id')
				->toArray();

			$cook = DB::table('hr_settings_position')
				->where('name', 'like', '%Cook%')
				->pluck('id')
				->toArray();

			$transferedUsers = DB::select( DB::raw("SELECT * FROM `hr_transfer` WHERE effect_date>'$date' AND pre_branch_id_fk='$branchId' AND id IN (SELECT MIN(id) FROM `hr_transfer` WHERE effect_date>'$date' AND pre_branch_id_fk='$branchId')") );

			if (sizeof($transferedUsers) > 0) {
				foreach ($transferedUsers as $key => $transferedUsersValue) {
					
					$userIdArray[] = $transferedUsersValue->users_id_fk;
					
				}

				$transferedEmployee = DB::table('users')
						->whereIn('id', $userIdArray)
						->pluck('emp_id_fk')
						->toArray();

				$transferedEmployeeRechecked = DB::table('hr_emp_org_info')
					->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
					->where([['hr_emp_org_info.joining_date', '<=', $date]])
					// ->where('hr_settings_position.id', '=', 122)
					->whereNotIn('hr_settings_position.id', $branchManager)
					->whereNotIn('hr_settings_position.id', $areaManager)
					->whereNotIn('hr_settings_position.id', $zonalManager)
					->whereNotIn('hr_settings_position.id', $cook)
					->where(function ($query) use ($date) {
						$query->where([['terminate_resignation_date', '>=', $date]])
						->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
						->orWhere([['terminate_resignation_date', '=', null]]);
					})
					->whereIn('hr_emp_org_info.emp_id_fk',$transferedEmployee)
					->pluck('hr_emp_org_info.emp_id_fk')
					->toArray();

				$newfieldOfficerInfoArray = array('' => 'Select') + 
					DB::table('hr_emp_org_info')
					->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
					->where([['hr_emp_org_info.branch_id_fk', $branchId], ['hr_emp_org_info.joining_date', '<=', $date]])
					// ->where('hr_settings_position.id', '=', 122)
					->whereNotIn('hr_settings_position.id', $branchManager)
					->whereNotIn('hr_settings_position.id', $areaManager)
					->whereNotIn('hr_settings_position.id', $zonalManager)
					->whereNotIn('hr_settings_position.id', $cook)
					->where(function ($query) use ($date) {
						$query->where([['terminate_resignation_date', '>=', $date]])
						->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
						->orWhere([['terminate_resignation_date', '=', null]]);
					})
					->pluck('hr_emp_org_info.emp_id_fk')
					->toArray();

				$newfieldOfficerInfoArray = array_merge($newfieldOfficerInfoArray, $transferedEmployeeRechecked);

				$newfieldOfficerInfo = DB::table('hr_emp_general_info')
					->whereIn('id', $newfieldOfficerInfoArray)
					->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
					->get();
					// ->pluck('nameWithCode', 'id')
					// ->all();

				// dd($transferedUsers, $transferedEmployee, $transferedEmployeeRechecked, $newfieldOfficerInfoArray, $newfieldOfficerInfo);
			}
			else {
				$newfieldOfficerInfo =
					DB::table('hr_emp_org_info')
					->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
					->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
					->where([['hr_emp_org_info.branch_id_fk', $branchId], ['hr_emp_org_info.joining_date', '<=', $date]])
					// ->where('hr_settings_position.id', '=', 122)
					->whereNotIn('hr_settings_position.id', $branchManager)
					->whereNotIn('hr_settings_position.id', $areaManager)
					->whereNotIn('hr_settings_position.id', $zonalManager)
					->whereNotIn('hr_settings_position.id', $cook)
					->where(function ($query) use ($date) {
						$query->where([['terminate_resignation_date', '>=', $date]])
						->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
						->orWhere([['terminate_resignation_date', '=', null]]);
					})
					->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
					->get();
					// ->pluck('nameWithCode', 'id')
					// ->all();
			}

			// dd($newfieldOfficerInfo);

			// $newfieldOfficerInfo = DB::table('hr_emp_org_info')
			// 	->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
			// 	->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
			// 	->where([['hr_emp_org_info.branch_id_fk', $branchId], ['hr_emp_org_info.joining_date', '<=', $date]])
			// 	// ->where('hr_settings_position.id', '=', 122)
			// 	->whereNotIn('hr_settings_position.id', $branchManager)
			// 	->whereNotIn('hr_settings_position.id', $areaManager)
			// 	->whereNotIn('hr_settings_position.id', $zonalManager)
			// 	->whereNotIn('hr_settings_position.id', $cook)
			// 	->where(function ($query) use ($date) {
			// 		$query->where([['terminate_resignation_date', '>=', $date]])
			// 		->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
			// 		->orWhere([['terminate_resignation_date', '=', null]]);
			// 	})
			// 	->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
			// 	->get();

            $damageData = array(
				'curentfieldOfficerInfo'  =>  $curentfieldOfficerInfo,
				'newOfficerInfo'  		  =>  $newfieldOfficerInfo
			);


			// dd($damageData);

			return response()->json($damageData);
		}

		public function getCurSamity(Request $req) {
			$date = MicroFin::getSoftwareDateBranchWise($req->branchId);

			$getSamityId = DB::table('mfn_samity')
				->where([['fieldOfficerId', $req->id]])
				->pluck('id')
				->toArray();

			$getSamityFieldOfficerTransferedHistory = DB::table('mfn_samity_field_officer_change')
				->where([['branchId', $req->branchId], ['fieldOfficerId', $req->id], ['effectiveDate', '>=', $date]])
				->pluck('samityId')
				->toArray();

			// dd($getSamityId, $getSamityFieldOfficerTransferedHistory, $req->id);

			$getSamityId = array_merge($getSamityId, $getSamityFieldOfficerTransferedHistory);

			$getSamityId = array_unique($getSamityId);

			$getSamity = DB::table('mfn_samity')
				// ->where([['branchId', $req->branchId], ['fieldOfficerId', $req->id]])
				->where([['openingDate', '<=', $date]])
				->where(function ($query) use ($date) {
					$query->where([['closingDate', '>=', $date]])
					->orWhere([['closingDate', '=', '0000-00-00']])
					->orWhere([['closingDate', '=', null]]);
				})
				->whereIn('id', $getSamityId)
				->select(DB::raw("CONCAT(code, ' - ', name) AS nameWithCode"), 'id')
				->get();

			$currentlyAssignedToNewFieldOfficer = DB::table('mfn_samity')
				->where([['openingDate', '<=', $date], ['fieldOfficerId', '!=', $req->id]])
				->where(function ($query) use ($date) {
					$query->where([['closingDate', '>=', $date]])
					->orWhere([['closingDate', '=', '0000-00-00']])
					->orWhere([['closingDate', '=', null]]);
				})
				->whereIn('id', $getSamityId)
				->select('fieldOfficerId', 'id')
				->get();

			$damageData = [
				'samityInfo' =>  $getSamity,
				'currentlyAssignedSamity' => $currentlyAssignedToNewFieldOfficer
			];

			// dd($getSamity, $req->id, $currentlyAssignedToNewFieldOfficer, $damageData);

			return response::json($damageData);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD SAMITY FIELD OFFICER CHANGE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {
			// dd($req);

			// $dataSelected = DB::table('mfn_samity_field_officer_change')
			// 	->select('id', 'samityId')
			// 	->get();

			// foreach ($dataSelected as $key => $dataSelectedValue) {
			// 	$encodedData = json_encode($dataSelectedValue->samityId);
			// 	$dataInsert = DB::table('mfn_samity_field_officer_change')
			// 		->where('id', $dataSelectedValue->id)
			// 		->update(
			// 			[
			// 				'samityIdFk' => $encodedData
			// 			]
			// 		);
			// }

			// dd("Query Executed");

			if ($req->SamityList == null) {
				$data = array(
					'responseTitle' =>	'Error!',
					'responseText'  =>  'Please select at least one samity!'
				);

				return response::json($data);
			}

			if ($req->fieldOfficerId == $req->newFieldOfficerId) {
				$data = array(
					'responseTitle' =>	'Error!',
					'responseText'  =>  'Current field officer id and new field officer id can not be same!'
				);

				return response::json($data);
			}

			if ($req->fieldOfficerId == "" || $req->fieldOfficerId == null || $req->newFieldOfficerId == "" || $req->newFieldOfficerId == null) {
				$data = array(
					'responseTitle' =>	'Error!',
					'responseText'  =>  'please select field officer!'
				);

				return response::json($data);
			}

			if ($req->effectiveDate == "" || $req->effectiveDate == null) {
				$data = array(
					'responseTitle' =>	'Error!',
					'responseText'  =>  'Please select a effective date!'
				);

				return response::json($data);
			}

			// $now = Carbon::now();
			// $req->request->add(['createdDate' => $now]);

			// //	CHANGE THE EFFECTIVE DATE FORMAT.
			// $effectiveDate = date_create($req->effectiveDate);
			// $req->request->add(['effectiveDate' => date_format($effectiveDate, "Y-m-d")]);
			// $create = MfnSamityFieldOfficerChange::create($req->all());

			// //	UPDATE FIELD OFFICER ID IN mfn_samity TABLE.
			// $samity = MfnSamity::find($samityId);
			// $samity->fieldOfficerId = $req->newFieldOfficerId;
			// $samity->save();

			$effectiveDate=date_create($req->effectiveDate);
			$effectiveDate = date_format($effectiveDate,"Y-m-d");

			foreach ($req->SamityList as $key => $samityId) {
				$changedResultInsert = DB::table('mfn_samity')
					->where([['id', $samityId], ['branchId', $req->branchId]])
					->update (
						[
							'fieldOfficerId' => $req->newFieldOfficerId,
							'softDel'		 => 0
						]
					);

				$changedResultInsert = DB::table('mfn_samity_field_officer_change')
					->insert (
						[
							'samityId' 			  => $samityId,
							'fieldOfficerId'      => $req->fieldOfficerId,
							'newFieldOfficerId'	  => $req->newFieldOfficerId,
							'effectiveDate'		  => $effectiveDate,
							'createdDate'		  => date("Y-m-d")
						]
					);

				$logArray = array(
				    'moduleId'  => 6,
				    'controllerName'  => 'MfnSamityFieldOfficerChangeController',
				    'tableName'  => 'mfn_samity_field_officer_change',
				    'operation'  => 'insert',
				    'primaryIds'  => [DB::table('mfn_samity_field_officer_change')->max('id')]
				);
				Service::createLog($logArray);
			}

			// $encodedSamity = json_encode($req->SamityList);

			// $changedResultInsert = DB::table('mfn_samity_field_officer_changed')
			// 	->insert (
			// 		[
			// 			'samityIdFk' 			  => $encodedSamity,
			// 			'branchIdFk'			  => $req->branchId,
			// 			'previouszFieldOfficerId' => $req->fieldOfficerId,
			// 			'newFieldOfficerId'		  => $req->newFieldOfficerId,
			// 			'changedDate'			  => $effectiveDate,
			// 			'creadtedDate'			  => date("Y-m-d"),
			// 			'softDel'				  => 0
			// 		]
			// 	);

			$data = array(
				'responseTitle' =>	'Success!',
				'responseText'  =>  'New field officer has been saved successfully.'
			);

			return response::json($data);
		}

		public function showInfoForUpdate(Request $req) {
			// dd($req);

			$callBackData = array();

			$getShamityHistoryInfos = DB::table('mfn_samity_field_officer_change')
				->where('id', $req->id)
				->get();

			foreach ($getShamityHistoryInfos as $key => $getShamityHistoryInfosValue) {
				$getSamityName = DB::table('mfn_samity')
					->where('id', $getShamityHistoryInfosValue->samityId)
					->pluck('name', 'id')
					->toArray();

				$getSamityCode = DB::table('mfn_samity')
					->where('id', $getShamityHistoryInfosValue->samityId)
					->pluck('code', 'id')
					->toArray();

				$getOldFieldOfficerName = DB::table('hr_emp_general_info')
					->where('id', $getShamityHistoryInfosValue->fieldOfficerId)
					->pluck('emp_name_english', 'id')
					->toArray();

				$getOldFieldOfficerCode = DB::table('hr_emp_general_info')
					->where('id', $getShamityHistoryInfosValue->fieldOfficerId)
					->pluck('emp_id', 'id')
					->toArray();

				$getNewFieldOfficerName = DB::table('hr_emp_general_info')
					->where('id', $getShamityHistoryInfosValue->newFieldOfficerId)
					->pluck('emp_name_english', 'id')
					->toArray();

				$getNewFieldOfficerCode = DB::table('hr_emp_general_info')
					->where('id', $getShamityHistoryInfosValue->newFieldOfficerId)
					->pluck('emp_id', 'id')
					->toArray();

				$getBranchId = DB::table('mfn_samity')
					->where('id', $getShamityHistoryInfosValue->samityId)
					->pluck('branchId')
					->toArray();

				// NEW FIELD OFFICER START............
				$branchId = $getBranchId[0];
				$date = MicroFin::getSoftwareDateBranchWise($branchId);

				$samityAssignedFieldOfficers = DB::table('mfn_samity')
					->where([['branchId', $branchId], ['openingDate', '<=', $date]])
					->where(function ($query) use ($date) {
						$query->where([['closingDate', '>=', $date]])
						->orWhere([['closingDate', '=', '0000-00-00']])
						->orWhere([['closingDate', '=', null]]);
					})
					->groupBy('fieldOfficerId')
					->pluck('fieldOfficerId')
					->toArray();

				$samityTransferedFieldOfficers = DB::table('mfn_samity_field_officer_change')
					->where([['branchId', $branchId], ['effectiveDate', '>=', $date]])
					->groupBy('fieldOfficerId')
					->pluck('fieldOfficerId')
					->toArray();
				
				$samityAssignedFieldOfficers = array_merge($samityAssignedFieldOfficers, $samityTransferedFieldOfficers);

				$samityAssignedFieldOfficers = array_unique($samityAssignedFieldOfficers);

				$branchManager = DB::table('hr_settings_position')
					->where('name', 'like', '%Branch Manager%')
					->pluck('id')
					->toArray();

				$areaManager = DB::table('hr_settings_position')
					->where('name', 'like', '%Area Manager%')
					->pluck('id')
					->toArray();

				$zonalManager = DB::table('hr_settings_position')
					->where('name', 'like', '%Zonal Manager%')
					->pluck('id')
					->toArray();

				$cook = DB::table('hr_settings_position')
					->where('name', 'like', '%Cook%')
					->pluck('id')
					->toArray();

				$transferedUsers = DB::select( DB::raw("SELECT * FROM `hr_transfer` WHERE effect_date>'$date' AND pre_branch_id_fk='$branchId' AND id IN (SELECT MIN(id) FROM `hr_transfer` WHERE effect_date>'$date' AND pre_branch_id_fk='$branchId')") );

				if (sizeof($transferedUsers) > 0) {
					foreach ($transferedUsers as $key => $transferedUsersValue) {
						
						$userIdArray[] = $transferedUsersValue->users_id_fk;
						
					}

					$transferedEmployee = DB::table('users')
							->whereIn('id', $userIdArray)
							->pluck('emp_id_fk')
							->toArray();

					$transferedEmployeeRechecked = DB::table('hr_emp_org_info')
						->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
						->where([['hr_emp_org_info.joining_date', '<=', $date]])
						->whereNotIn('hr_settings_position.id', $branchManager)
						->whereNotIn('hr_settings_position.id', $areaManager)
						->whereNotIn('hr_settings_position.id', $zonalManager)
						->whereNotIn('hr_settings_position.id', $cook)
						->where(function ($query) use ($date) {
							$query->where([['terminate_resignation_date', '>=', $date]])
							->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
							->orWhere([['terminate_resignation_date', '=', null]]);
						})
						->whereIn('hr_emp_org_info.emp_id_fk',$transferedEmployee)
						->pluck('hr_emp_org_info.emp_id_fk')
						->toArray();

					$newfieldOfficerInfoArray = array('' => 'Select') + 
						DB::table('hr_emp_org_info')
						->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
						->where([['hr_emp_org_info.branch_id_fk', $branchId], ['hr_emp_org_info.joining_date', '<=', $date]])
						->whereNotIn('hr_settings_position.id', $branchManager)
						->whereNotIn('hr_settings_position.id', $areaManager)
						->whereNotIn('hr_settings_position.id', $zonalManager)
						->whereNotIn('hr_settings_position.id', $cook)
						->where(function ($query) use ($date) {
							$query->where([['terminate_resignation_date', '>=', $date]])
							->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
							->orWhere([['terminate_resignation_date', '=', null]]);
						})
						->pluck('hr_emp_org_info.emp_id_fk')
						->toArray();

					$newfieldOfficerInfoArray = array_merge($newfieldOfficerInfoArray, $transferedEmployeeRechecked);

					$newfieldOfficerInfo = DB::table('hr_emp_general_info')
						->where([['id', '!=', $getShamityHistoryInfosValue->fieldOfficerId], ['id', '!=', $getShamityHistoryInfosValue->newFieldOfficerId]])
						->whereIn('id', $newfieldOfficerInfoArray)
						->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
						->get();
				}
				else {
					$newfieldOfficerInfo =
						DB::table('hr_emp_org_info')
						->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
						->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', 'hr_settings_position.id')
						->where([['hr_emp_org_info.branch_id_fk', $branchId], ['hr_emp_org_info.joining_date', '<=', $date]])
						->where([['hr_emp_general_info.id', '!=', $getShamityHistoryInfosValue->fieldOfficerId], ['hr_emp_general_info.id', '!=', $getShamityHistoryInfosValue->newFieldOfficerId]])
						->whereNotIn('hr_settings_position.id', $branchManager)
						->whereNotIn('hr_settings_position.id', $areaManager)
						->whereNotIn('hr_settings_position.id', $zonalManager)
						->whereNotIn('hr_settings_position.id', $cook)
						->where(function ($query) use ($date) {
							$query->where([['terminate_resignation_date', '>=', $date]])
							->orWhere([['terminate_resignation_date', '=', '0000-00-00']])
							->orWhere([['terminate_resignation_date', '=', null]]);
						})
						->select(DB::raw("CONCAT(hr_emp_general_info.emp_id, ' - ',hr_emp_general_info.emp_name_english) AS nameWithCode"), 'hr_emp_general_info.id')
						->get();
				}

				// NEW FIELD OFFICER END............

				$savingsDeposite = DB::table('mfn_savings_deposit')
					->where([['samityIdFk', $getShamityHistoryInfosValue->samityId], ['depositDate', '>=', $getShamityHistoryInfosValue->effectiveDate]])
					->sum('amount');

				$savingsWithdraw = DB::table('mfn_savings_withdraw')
					->where([['samityIdFk', $getShamityHistoryInfosValue->samityId], ['withdrawDate', '>=', $getShamityHistoryInfosValue->effectiveDate]])
					->sum('amount');

				$loanCollection = DB::table('mfn_loan_collection')
					->where([['samityIdFk', $getShamityHistoryInfosValue->samityId], ['collectionDate', '>=', $getShamityHistoryInfosValue->effectiveDate]])
					->sum('amount');

				$disburedAmount = DB::table('mfn_loan')
					->where([['samityIdFk', $getShamityHistoryInfosValue->samityId], ['disbursementDate', '>=', $getShamityHistoryInfosValue->effectiveDate]])
					->sum('loanAmount');

				$callBackData = [
					'id'				  => $getShamityHistoryInfosValue->id,
					'samityName' 		  => $getSamityCode[$getShamityHistoryInfosValue->samityId]." - ".$getSamityName[$getShamityHistoryInfosValue->samityId],
					'samityId'   		  => $getShamityHistoryInfosValue->samityId,
					'effectiveDate'		  => $getShamityHistoryInfosValue->effectiveDate,
					'NewFieldOfficerName' => $getNewFieldOfficerCode[$getShamityHistoryInfosValue->newFieldOfficerId]." - ".$getNewFieldOfficerName[$getShamityHistoryInfosValue->newFieldOfficerId],
					'NewFieldOfficerId'   => $getShamityHistoryInfosValue->newFieldOfficerId,
					'OldFieldOfficerName' => $getOldFieldOfficerCode[$getShamityHistoryInfosValue->fieldOfficerId]." - ".$getOldFieldOfficerName[$getShamityHistoryInfosValue->fieldOfficerId],
					'OldFieldOfficerId'   => $getShamityHistoryInfosValue->fieldOfficerId,
					'savingsDeposite'     => $savingsDeposite,
					'savingsWithdraw'     => $savingsWithdraw,
					'loanCollection'      => $loanCollection,
					'disburedAmount'      => $disburedAmount
				];

				foreach ($newfieldOfficerInfo as $key => $newfieldOfficerInfoValue) {
					$callBackData['fieldOfficerName'][] = $newfieldOfficerInfoValue->nameWithCode;
					$callBackData['fieldOfficerId'][] = $newfieldOfficerInfoValue->id;
				}
			}

			// dd($callBackData);

			return response::json($callBackData);
		}

		public function updateItem(Request $req) {

			$previousdata = MfnSamityFieldOfficerChange::find($req->id);
			//dd($previousdata);

			$updateFieldOfficerChangeData = DB::table('mfn_samity_field_officer_change')
				->where([['id', $req->id], ['samityId', $req->samityId], 
					['fieldOfficerId', $req->oldFieldOfficerId], ['newFieldOfficerId', $req->currentFieldOfficerId]])
				->update(
					[
						'newFieldOfficerId' => $req->newFieldOfficer
					]
				);

			$logArray = array(
		            'moduleId'  => 6,
		            'controllerName'  => 'MfnSamityFieldOfficerChangeController',
		            'tableName'  => 'mfn_samity_field_officer_change',
		            'operation'  => 'update',
		            'previousData'  => $previousdata,
		            'primaryIds'  => [$req->id]
		        );
				Service::createLog($logArray);

			$updateSamityData = DB::table('mfn_samity')
				->where([['id', $req->samityId], ['fieldOfficerId', $req->currentFieldOfficerId]])
				->update(
					[
						'fieldOfficerId' => $req->newFieldOfficer
					]
				);

			// dd($req, $updateFieldOfficerChangeData, $updateSamityData);
			
			if ($updateFieldOfficerChangeData == 1 and $updateSamityData == 1) {
				$data = array(
					'responseTitle' =>	'Success!',
					'responseText'  =>  'New field officer has been updated successfully.'
				);
			}
			elseif ($updateFieldOfficerChangeData != 1) {
				$data = array(
					'responseTitle' =>	'Warning!',
					'responseText'  =>  'New field officer has not been updated successfully.'
				);
			}
			elseif ($updateSamityData != 1) {
				$data = array(
					'responseTitle' =>	'Warning!',
					'responseText'  =>  'New field officer has not been updated successfully.'
				);
			}

			return response::json($data);
		}

		public function deleteData (Request $req) {
			$check = "Delete Function!";

			$updateSamity = DB::table('mfn_samity')
				->where([['id', $req->replicaSamityId], ['fieldOfficerId', $req->replicaCurrentFieldOfficerId]])
				->update(
					[
						'fieldOfficerId' => $req->replicaOldFieldOfficerId
					]
				);
			$previousdata = MfnSamityFieldOfficerChange::find($req->id);

			$updateSamityFieldOfficerChange = DB::table('mfn_samity_field_officer_change')
				->where([['id', $req->replicaId], ['samityId', $req->replicaSamityId], ['fieldOfficerId', $req->replicaOldFieldOfficerId], 
					['newFieldOfficerId', $req->replicaCurrentFieldOfficerId]])
				->update(
					[
						'softDel' => 1
					]
				);

				$logArray = array(
		            'moduleId'  => 6,
		            'controllerName'  => 'MfnSamityFieldOfficerChangeController',
		            'tableName'  => 'mfn_samity_field_officer_change',
		            'operation'  => 'delete',
		            'previousData'  => $previousdata,
		            'primaryIds'  => [$req->id]
		        );
				Service::createLog($logArray);

			// dd($check, $req, $updateSamity, $updateSamityFieldOfficerChange);

			if ($updateSamity == 1 and $updateSamityFieldOfficerChange == 1) {
				$data = array(
					'responseTitle' =>	'Success!',
					'responseText'  =>  'New field officer has been deleted successfully.'
				);
			}
			elseif ($updateSamity != 1) {
				$data = array(
					'responseTitle' =>	'Warning!',
					'responseText'  =>  'New field officer has not been deleted successfully.'
				);
			}
			elseif ($updateSamityFieldOfficerChange != 1) {
				$data = array(
					'responseTitle' =>	'Warning!',
					'responseText'  =>  'New field officer has not been deleted successfully.'
				);
			}

			return response::json($data);
		}
	}
