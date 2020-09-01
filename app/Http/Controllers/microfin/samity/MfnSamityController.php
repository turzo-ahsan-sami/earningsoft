<?php

namespace App\Http\Controllers\microfin\samity;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\samity\MfnSamity;
use App\microfin\member\MfnMemberInformation;
use App\microfin\member\MfnMemberClosing;

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

use App\microfin\settings\MfnHoliday;
use App\gnr\GnrBranch;
use App\Http\Controllers\gnr\Service;
use App\Http\Controllers\microfin\MicroFin;
use App;

class MfnSamityController extends Controller {

	protected $MicroFinance;

	private $TCN;

	public function __construct() {

		$this->MicroFinance = new MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Samity', 0),
			array('Code', 0),
			array('Branch', 0),
			array('Working Area', 0),
			array('Field Officer', 0),
			array('Type', 0),
			array('Samity Day', 80),
				//array('Is Samity Day Obsolete', 0),
			array('Opening Date', 80),
			array('Total Member', 80),
			array('Status', 70),
			array('Action', 80)
		);
	}

	public function index(Request $req) {

		$PAGE_SIZE = 50;
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
//dd(Auth::user()->id);
		if(Auth::user()->branchId==1):
			$samities = MfnSamity::active();
			$fieldOfficerList = $this->MicroFinance->getFieldOfficerListAll();
		else:
			//$samities = MfnSamity::active()->branchWise();
			$samities = MfnSamity::active();
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
			//dd($branchIdArray);
			$samities->whereIn('branchId', $branchIdArray);
			$fieldOfficerList = $this->MicroFinance->getFieldOfficerList();
		endif;

		//dd($samities);

		if($req->has('branchId'))
			$samities->where('branchId', $req->get('branchId'));

		if($req->has('keyword')) {
			$samities->where('name', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('code', 'LIKE', '%' . $req->get('keyword') . '%');
		}

		if($req->has('dateFrom'))
			$samities->where('openingDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if($req->has('dateTo'))
			$samities->where('openingDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE -$PAGE_SIZE;

		if($req->has('branchId') || $req->has('keyword') || $req->has('dateFrom') || $req->has('dateTo')) {
			$samities = $samities->get();
			$isSearch = 1;
		} else {
			$samities = $samities->paginate($PAGE_SIZE);
			$isSearch = 0;
		}

		if (Auth::user()->branchId==1) {
			$branchList = MicroFin::getBranchList();

		}
		else{
			$branchList = DB::table('gnr_branch')
			->whereIn('id',$branchIdArray )
			->orderBy('branchCode')
			->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
			->pluck('nameWithCode', 'id')
			->all();
		}
			//dd($samities);

		$damageData = array(
			'TCN' 			  		=>  $this->TCN,
			'SL' 	   			    =>	$req->has('page')?$SL:0,
			'isSearch'              =>  $isSearch,
			'branch'  			    =>  $this->MicroFinance->getAllBranchOptions(),
			'samityType' 	  		=>  $this->MicroFinance->getSamityType(),
			'samityDay' 	  		=>  $this->MicroFinance->getSamityDay(),
			'samityDaySuperScript'	=>  $this->MicroFinance->getSamityDaySuperScript(),
			'samityDayClass'  		=>  $this->MicroFinance->getSamityDayClass(),
			'samityFixedDate' 		=>  $this->MicroFinance->getSamityFixedDate(),
			'samities' 		  		=>  $samities,
			'branchList'    =>  $branchList,
			'branchIdArray'     => $branchIdArray,
			'samityDateType'  		=>  $this->MicroFinance->getSamityDateType(),
			'fieldOfficerList'  	=>  $fieldOfficerList,
			'MicroFinance'      	=>  $this->MicroFinance
		);

		return view('microfin.samity.samity.viewSamity', ['damageData' => $damageData]);
	}




	public function index_old(Request $req) {

		$PAGE_SIZE = 50;
//dd(Auth::user()->id);
		if(Auth::user()->branchId==1):
			$samities = MfnSamity::active();
			$fieldOfficerList = $this->MicroFinance->getFieldOfficerListAll();
		else:
			//$samities = MfnSamity::active()->branchWise();
			$samities = MfnSamity::active();
			$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
			//dd($branchIdArray);
			$samities->whereIn('branchId', $branchIdArray);
			$fieldOfficerList = $this->MicroFinance->getFieldOfficerList();
		endif;

		//dd($samities);

		if($req->has('branchId'))
			$samities->where('branchId', $req->get('branchId'));

		if($req->has('keyword')) {
			$samities->where('name', 'LIKE', '%' . $req->get('keyword') . '%')
			->orWhere('code', 'LIKE', '%' . $req->get('keyword') . '%');
		}

		if($req->has('dateFrom'))
			$samities->where('openingDate', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

		if($req->has('dateTo'))
			$samities->where('openingDate', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

		if($req->has('page'))
			$SL = $req->get('page') * $PAGE_SIZE -$PAGE_SIZE;

		if($req->has('branchId') || $req->has('keyword') || $req->has('dateFrom') || $req->has('dateTo')) {
			$samities = $samities->get();
			$isSearch = 1;
		} else {
			$samities = $samities->paginate($PAGE_SIZE);
			$isSearch = 0;
		}
			//dd($samities);

		$damageData = array(
			'TCN' 			  		=>  $this->TCN,
			'SL' 	   			    =>	$req->has('page')?$SL:0,
			'isSearch'              =>  $isSearch,
			'branch'  			    =>  $this->MicroFinance->getAllBranchOptions(),
			'samityType' 	  		=>  $this->MicroFinance->getSamityType(),
			'samityDay' 	  		=>  $this->MicroFinance->getSamityDay(),
			'samityDaySuperScript'	=>  $this->MicroFinance->getSamityDaySuperScript(),
			'samityDayClass'  		=>  $this->MicroFinance->getSamityDayClass(),
			'samityFixedDate' 		=>  $this->MicroFinance->getSamityFixedDate(),
			'samities' 		  		=>  $samities,
			'samityDateType'  		=>  $this->MicroFinance->getSamityDateType(),
			'fieldOfficerList'  	=>  $fieldOfficerList,
			'MicroFinance'      	=>  $this->MicroFinance
		);

		return view('microfin.samity.samity.viewSamity', ['damageData' => $damageData]);
	}

	public function addSamity() {

			//	GET SAMITY CONFIGURATION.
		$getSamityCfgOB = DB::table('mfn_cfg')->where('name', 'samity_cfg')->select('config')->first();
		$getSamityCfg = json_decode($getSamityCfgOB->config, true);
		$maxMemberPerSamity = $getSamityCfg['maximumMemberPerSamity'];

		$damageData = array(
			'samityType' 	  	=> $this->MicroFinance->getSamityType(),
			'samityDay' 	  	=> $this->MicroFinance->getSamityDay(),
			'samityDayClass'  	=> $this->MicroFinance->getSamityDayClass(),
			'samityFixedDate' 	=> $this->MicroFinance->getSamityFixedDate(),
			'samityDateType'  	=> $this->MicroFinance->getSamityDateType(),
			'fieldOfficerList' 	=> $this->MicroFinance->getFieldOfficerList(),
			'maxNumber' 	    => $maxMemberPerSamity
		);

			//dd($weeklyHoliday);

		return view('microfin.samity.samity.addSamity', ['damageData' => $damageData]);
	}

	public function loadWorkingAreaOptions(Request $req) {

		list($string, $samityCode) = explode('-', $req->qryStr);
		list($branchCode, $samityRestOfCode) = explode('.', $samityCode);

		$branchId = GnrBranch::active()
		->where('branchCode', (int) $branchCode)
		->value('id');

		$workingAreaOptionsOB = DB::table('gnr_working_area')
		->where('branchId', $branchId)
		->where('name', 'LIKE', "%$string%")
		->select('id AS val', 'name AS label')
		->get();

		$data = array(
			'workingAreaOptions'  =>  $workingAreaOptionsOB
		);

		return response::json($data);
	}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD SAMITY CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function addItem(Request $req) {
			
			$isSamityCodeExists = (int) DB::table('mfn_samity')->where('softDel',0)->where('code',$req->code)->value('id');

			if ($isSamityCodeExists>0) {
				$data = array(
					'responseTitle' =>   'Warning!',
					'responseText'  =>   'This samity Code already exists.',
				);
				
				return response::json($data);
			}

			$rules = array(
				'name'		 	 =>	'required|unique:mfn_samity,name',
				// 'code' 			 =>	'required|unique:mfn_samity,code',
				'workingAreaId'	 =>	'required',
				'registrationNo' =>	'required',
				'fieldOfficerId' =>	'required',
				'samityDayTime'	 =>	'required',
				'samityTypeId'	 =>	'required',
				'openingDate'	 =>	'required',
				'maxNumber'		 =>	'required'
			);

			$attributesNames = array(
				'name'		 	 		 =>	'samity name',
				// 'code' 			 		 =>	'samity code',
				'workingAreaId'	 		 =>	'working area',
				'registrationNo' 		 =>	'registration number',
				'fieldOfficerId'		 =>	'field officer',
				'samityDayOrFixedDateId' =>	'samity day or fixed date',
				'samityDayId'	 		 =>	'samity day',
				'samityDayTime'	 		 =>	'samity day time',
				'samityTypeId'	 		 =>	'samity type',
				'openingDate'	 		 =>	'opening date',
				'maxNumber'		 		 =>	'maximum number'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$now = Carbon::now();
				$req->request->add(['createdDate' => $now]);
				$req->request->add(['code' => str_replace('_', '', $req->code)]);
				$req->request->add(['branchId' => Session::get('branchId')]);
				$req->request->add(['branchCode' => Session::get('branchCode')]);
				$req->request->add(['samitySL' => substr($req->code, -4)]);

				$req->request->add(['registrationNo' => str_replace('_', '', $req->registrationNo)]);

				//	CHANGE THE OPENING DATE FORMAT.
				$openingDate = date_create($req->openingDate);
				$req->request->add(['openingDate' => date_format($openingDate, "Y-m-d")]);

				// SAMITY DAY WILL COMPARE WITH WEEKLY HOLIDAY
				$samityOpeningDate = date_format($openingDate, "Y-m-d");

				$startOpeningDate = date("Y-m-01", strtotime($samityOpeningDate));
				$lastOpeningDate  = date("Y-m-t", strtotime($samityOpeningDate));

				$weeklyHoliday = MfnHoliday::where('isWeeklyHoliday', '=', 1)
				->where('date', '>=',$startOpeningDate)
				->where('date', '<=', $lastOpeningDate)
				->pluck('date')
				->toArray();

				//dd($weeklyHoliday);

				$samityDayNumber = $req->samityDayId;
				$samityDayName   = '';

				switch ($samityDayNumber) {
					case "1":
					$samityDayName = 'Saturday';
					break;
					case "2":
					$samityDayName = 'Sunday';
					break;
					case "3":
					$samityDayName = 'Monday';
					break;
					case "4":
					$samityDayName = 'Tuesday';
					break;
					case "5":
					$samityDayName = 'Wednesday';
					break;
					case "6":
					$samityDayName = 'Thursday';
					break;
					case "7":
					$samityDayName = 'Friday';
					break;
				}

				$isMatched = 0;

				foreach ($weeklyHoliday as $key => $weeklyHolidayValue) {
					$unixTimestamp = strtotime($weeklyHolidayValue);
					$dayOfWeek = date("l", $unixTimestamp);
					if ($dayOfWeek == $samityDayName) {
						++$isMatched;
					}
				}

				if ($isMatched > 0) {
					$data = array(
						'responseTitle' =>   'Warning!',
						'responseText'  =>   'This samity date is a weekly holiday! So please change the samity date to a regular working day!'
					);

					return response::json($data);
				}

				// dd($samityOpeningDate, $startOpeningDate, $lastOpeningDate, $weeklyHoliday, $samityDayNumber, $samityDayName, $isMatched);

				$create = MfnSamity::create($req->all());
				//dd($create);

				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnSamityController',
					'tableName'  => 'mfn_samity',
					'operation'  => 'insert',
					'primaryIds'  => [DB::table('mfn_samity')->max('id')]
				);
				Service::createLog($logArray);

				$data = array(
					'responseTitle' =>   'Success!',
					'responseText'  =>   'New samity has been saved successfully.',
					'create'		=> $create
				);

				return response::json($data);
			}
		}

		/**
		 * [Function for return samity details of a given ID]
		 *
		 * @param  Request $req  [Request for samity details]
		 * @return [array]       [An array contains of samity details of a given ID]
		 */
		public function detailsSamity(Request $req) {

			$samityDetailsOB = $this->MicroFinance->getDetails($table='mfn_samity', $req->id, []);
			$branchNameOB = $this->MicroFinance->getDetails($table='gnr_branch', $samityDetailsOB->branchId, ['name',
				'branchCode'
			]);
			$workingAreaOB = $this->MicroFinance->getDetails($table='gnr_working_area', $samityDetailsOB->workingAreaId, ['name',
				'divisionId',
				'districtId',
				'upazilaId',
				'unionId',
				'villageId'
			]);
			$employeeOB = $this->MicroFinance->getDetails($table='hr_emp_general_info', $samityDetailsOB->fieldOfficerId, ['emp_name_english']);

			$tableTitleColFirst = array(
				'Name:',
				'Branch Name:',
				'Working Area:',
				'Thana:',
				'District:',
				'Is Samity Day Obsolete:',
				'Field Officer:',
				'Samity Day:',
				'Opening Date:',
				'Is Transferable:',
				'Status:'
			);

			$tableTitleColSecond = array(
				'Samity Code:',
				'Branch Code:',
				'Union/ Word:',
				'Village:',
				'Division:',
				'Registration No:',
				'Samity Type:',
				'Samity Time:',
				'Maximum Member:',
				'Closing Date:',
				'-'
			);

			$samityDetailsColFirst = array(
				$samityDetailsOB->name,
				$branchNameOB->name,
				$workingAreaOB->name,
				$this->MicroFinance->getDetails($table='gnr_upzilla', $workingAreaOB->upazilaId, ['name'])->name,
				$this->MicroFinance->getDetails($table='gnr_district', $workingAreaOB->districtId, ['name'])->name,
				$this->MicroFinance->getSamityDayObsoleteStatus($samityDetailsOB->samityDayOptional),
				$employeeOB->emp_name_english,
				$this->MicroFinance->getSamityDayName($samityDetailsOB->samityDayId, $samityDetailsOB->fixedDate),
				date_format(date_create($samityDetailsOB->openingDate), "d-m-Y"),
				$this->MicroFinance->getBooleanStatus($samityDetailsOB->isTransferable),
				$this->MicroFinance->getStatus($samityDetailsOB->status),
			);

			$samityDetailsColSecond = array(
				$samityDetailsOB->code,
				str_pad($branchNameOB->branchCode, 3, 0, STR_PAD_LEFT),
				$this->MicroFinance->getDetails($table='gnr_union', $workingAreaOB->unionId, ['name'])->name,
				$this->MicroFinance->getDetails($table='gnr_village', $workingAreaOB->villageId, ['name'])->name,
				$this->MicroFinance->getDetails($table='gnr_division', $workingAreaOB->divisionId, ['name'])->name,
				$samityDetailsOB->registrationNo,
				$this->MicroFinance->getSamityTypeName($samityDetailsOB->samityTypeId),
				date("h:i A", strtotime($samityDetailsOB->samityDayTime)),
				$samityDetailsOB->maxNumber,
				$this->MicroFinance->getSamityClosingDate($req->id, $samityDetailsOB->status),
				'-'
			);

			$data = array(
				'tableTitleColFirst' 	 =>   $tableTitleColFirst,
				'tableTitleColSecond' 	 =>   $tableTitleColSecond,
				'samityDetailsColFirst'  =>   $samityDetailsColFirst,
				'samityDetailsColSecond' =>   $samityDetailsColSecond,
				'membersDetails'		 =>	  $samityDetailsOB
			);

			return response()->json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: CHECK IF ANY MEMBER EXISTS IN SAMITY.
		|--------------------------------------------------------------------------
		*/
		public function checkMemberExistance(Request $req) {

			$memberExists = DB::table('mfn_member_information')->where('samityId', $req->id)->count();

			$data = array(
				'memberExists'  =>  ($memberExists>=1)?1:0
			);

			return response()->json($data);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: UPDATE SAMITY CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function updateItem(Request $req) {
			// dd($req);

			$rules = array(
				'name'		 	 	=>	'required|unique:mfn_samity,name,'.$req->id,
				'code' 			 	=>	'required',
				'workingAreaId'	 	=>	'required',
				'registrationNo' 	=>	'required',
				'fieldOfficerId' 	=>	'required',
				//'samityDayId'	 	=>	'required',
				'samityDayTime'	 	=>	'required',
				'samityTypeId'	 	=>	'required',
				//'samityDayOptional'	=>	'required',
				'openingDate'	 	=>	'required',
				'maxNumber'		 	=>	'required'
			);

			$attributesNames = array(
				'name'		 	 		 =>	'samity name',
				'code' 			 		 =>	'samity code',
				'workingAreaId'	 		 =>	'working area',
				'registrationNo' 		 =>	'registration number',
				'fieldOfficerId' 		 =>	'field officer',
				'samityDayOrFixedDateId' =>	'samity day or fixed date',
				'samityDayId'	 		 =>	'samity day',
				'samityDayTime'	 		 =>	'samity day time',
				'samityTypeId'	 		 =>	'samity type',
				'samityDayOptional'		 =>	'samity day optional',
				'openingDate'	 		 =>	'opening date',
				'maxNumber'		 		 =>	'maximum number'
			);

			$validator = Validator::make(Input::all(), $rules);
			$validator->setAttributeNames($attributesNames);

			if($validator->fails())
				return response::json(array('errors' => $validator->getMessageBag()->toArray()));
			else {
				$samity = MfnSamity::find($req->id);
				$previousdata = $samity;

				// IF ANY LOAN IS DISBURSED THEN SAMITY DAY CAN NOT BE EDITABLE
				$numberOFloanOfThisSamity = DB::table('mfn_loan')
				->where('softDel', 0)
				->where('samityIdFk', $samity->id)
				->count();
				if ($numberOFloanOfThisSamity > 0 && $samity->samityDayId != $req->samityDayId) {
				$data = array(
					'responseTitle' =>   'Warning!',
					'responseText'  =>   'Samity Day can not be changed, because Loan exists for this samity.'
				);

				return response::json($data);
				}

				$samity->name = $req->name;
				$samity->code = $req->code;
				$samity->workingAreaId = $req->workingAreaId;
				$samity->registrationNo = $req->registrationNo;
				$samity->fieldOfficerId = $req->fieldOfficerId;
				$samity->samityDayOrFixedDateId = $req->samityDayOrFixedDateId;
				$samity->samityDayId = $req->samityDayId;
				$samity->samityDayTime = $req->samityDayTime;
				$samity->samityTypeId = $req->samityTypeId;
				$samity->maxNumber = $req->maxNumber;

				//	CHANGE THE OPENING DATE FORMAT.
				$openingDate = date_create($req->openingDate);
				$samity->openingDate = date_format(date_create($req->openingDate), "Y-m-d");

				// SAMITY DAY WILL COMPARE WITH WEEKLY HOLIDAY
				$samityOpeningDate = date_format($openingDate, "Y-m-d");

				$startOpeningDate = date("Y-m-01", strtotime($samityOpeningDate));
				$lastOpeningDate  = date("Y-m-t", strtotime($samityOpeningDate));
				
				$weeklyHoliday = MfnHoliday::where('isWeeklyHoliday', '=', 1)
				->where('date', '>=',$startOpeningDate)
				->where('date', '<=', $lastOpeningDate)
				->pluck('date')
				->toArray();

				//dd($weeklyHoliday);

				$samityDayNumber = $req->samityDayId;
				$samityDayName   = '';

				switch ($samityDayNumber) {
					case "1":
					$samityDayName = 'Saturday';
					break;
					case "2":
					$samityDayName = 'Sunday';
					break;
					case "3":
					$samityDayName = 'Monday';
					break;
					case "4":
					$samityDayName = 'Tuesday';
					break;
					case "5":
					$samityDayName = 'Wednesday';
					break;
					case "6":
					$samityDayName = 'Thursday';
					break;
					case "7":
					$samityDayName = 'Friday';
					break;
				}

				$isMatched = 0;

				foreach ($weeklyHoliday as $key => $weeklyHolidayValue) {
					$unixTimestamp = strtotime($weeklyHolidayValue);
					$dayOfWeek = date("l", $unixTimestamp);

					if ($dayOfWeek == $samityDayName) {
						++$isMatched;
					}
				}

				if ($isMatched > 0) {
					$data = array(
						'responseTitle' =>   'Warning!',
						'responseText'  =>   'This samity date is a weekly holiday! So please change the samity date to a regular working day!'
					);

					return response::json($data);
				}

				$samity->save();

				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnSamityController',
					'tableName'  => 'mfn_samity',
					'operation'  => 'update',
					'previousData'  => $previousdata,
					'primaryIds'  => [$previousdata->id]
				);
				Service::createLog($logArray);
				$data = array(
					'responseTitle' => 'Success!',
					'responseText'  => 'Samity has been updated successfully.'
				);

				return response()->json($data);
			}
		}

		public function fieldOfficerSamityItem (Request $req) {
			$samityCode = $req->id;
			$branchId = DB::table('mfn_samity')
			->where('code', $samityCode)
			->pluck('branchId')
			->toArray();

			$checkExistsFieldOfficer = DB::table('hr_emp_org_info')
			->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
			->where('hr_emp_org_info.branch_id_fk', $branchId)
			->count();

			if($checkExistsFieldOfficer>0):
				$fieldOfficerListOB = DB::table('hr_emp_org_info')
				->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
				->where('hr_emp_org_info.branch_id_fk', $branchId)
				->select('hr_emp_general_info.id', 'hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id')
				->get();
			else:
				$fieldOfficerListOB = [];
			endif;

			return response()->json($fieldOfficerListOB);
		}

		/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: DELETE SAMITY CONTROLLER.
		|--------------------------------------------------------------------------
		*/
		public function deleteItem(Request $req) {

			//	CHECK IF MINIMUM A MEMBER EXISTS IN THIS SAMITY.
			$memberNum = MfnMemberInformation::where('samityId', $req->id)->active()->count();
			$memberCloseNum = MfnMemberClosing::where('samityIdFk', $req->id)->active()->count();

			//dd($memberCloseNum);
			if($memberNum==0 && $memberCloseNum==0):


				/*$previousdata = MfnSamity::where('id', $req->id)->update(['status' => 0, 'softDel' => 1]);*/
				$previousdata = MfnSamity::find($req->id);
				MfnSamity::where('id', $req->id)->update(['status' => 0, 'softDel' => 1]);
				$logArray = array(
					'moduleId'  => 6,
					'controllerName'  => 'MfnSamityController',
					'tableName'  => 'mfn_samity',
					'operation'  => 'delete',
					'previousData'  => $previousdata,
					'primaryIds'  => [$req->id]
				);
				Service::createLog($logArray);
			endif;

				//dd('dd');

			$data = array(
				'responseTitle'  =>  $memberNum==0?$this->MicroFinance->getMessage('msgSuccess'):$this->MicroFinance->getMessage('msgWarning'),
				'responseText'   =>  $memberNum==0?$this->MicroFinance->getMessage('samityDel'):$this->MicroFinance->getMessage('samityNotDel'),
			);

			return response()->json($data);
		}
	}
