<?php

namespace App\Http\Controllers\microfin\samity;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\microfin\samity\MfnSamityDayChange;
use App\microfin\samity\MfnSamity;
use App\microfin\loan\MfnLoan;
use App\microfin\loan\MfnLoanSchedule;
use Session;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\microfin\MicroFin;
use App\microfin\settings\MfnHoliday;
//use App\Service\Service;
use App\Http\Controllers\gnr\Service;
use App;

use Illuminate\Support\Facades\Auth;

class MfnSamityDayChangeController extends Controller
{

	protected $MicroFinance;

	use GetSoftwareDate;

	private $TCN;

	public function __construct()
	{

		$this->MicroFinance = new MicroFinance;

		$this->TCN = array(
			array('SL No.', 70),
			array('Samity', 0),
			array('Code', 0),
			array('Branch', 0),
			array('Previous Samity Day', 150),
			array('New Samity Day', 150),
			array('New Collection Date', 150),
			array('Date of Samity Change', 160),
			array('Action', 80)
		);
	}

	public function index()
	{

		// dd($this->MicroFinance->getSamityDay(), MfnSamityDayChange::all());
		$samityDayChange = MfnSamityDayChange::all();
		//$samityDayChange = $samityDayChange->where('branchId', Auth::user()->branchId);
		$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
		$samityDayChange = $samityDayChange->whereIn('branchId', $branchIdArray);

		$damageData = array(
			'TCN'					=>	$this->TCN,
			'samityDayChanges'		=>	$samityDayChange,
			'samityDay'				=>	$this->MicroFinance->getSamityDay(),
			'MicroFinance'          =>  $this->MicroFinance
		);

		// dd($damageData, Auth::user()->branchId, $samityDayChange);

		return view('microfin.samity.samityDayChange.viewSamityDayChange', $damageData);
	}

	public function addSamityDayChange()
	{
		// SAMITY DAY CHNAGE IS CURRENT AVAILABLE FROM BRANCH.
		// samity day can change only on Thursday
		$branchId = Auth::user()->branchId;
		$softwareDate = Microfin::getSoftwareDateBranchWise($branchId);
		$weekDay = Carbon::parse($softwareDate)->format('l');
		$canChangeSamityDay = false;
		if ($weekDay == 'Thursday') {
			$canChangeSamityDay = true;
		}

		$damageData = array(
			'canChangeSamityDay' 	=>  $canChangeSamityDay,
			'samityList' 	 		=>  $this->MicroFinance->getSamity(),
			'effectiveDate'  		=>  Carbon::parse($softwareDate)->addDay()->format('Y-m-d'),
		);

		return view('microfin.samity.samityDayChange.addSamityDayChange', $damageData);
	}

	public function deleteItem(Request $req)
	{
		$branchId = DB::table('mfn_samity_day_change')
			->where([['id', $req->id],])
			->pluck('branchId')
			->toArray();

		$softDate = MicroFin::getSoftwareDateBranchWise($branchId[0]);

		$samityId = DB::table('mfn_samity_day_change')
			->where([['id', $req->id], ['effectiveDate', $softDate]])
			->pluck('samityId')
			->toArray();

		// dd($softDate, $samityId, $req->id);

		if (sizeof($samityId) == 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'The effective date of the samity day change is not equal to the software date!'
			);

			return response()->json($data);
		}

		$mfnMemberInformation = DB::table('mfn_member_information')
			->where('softDel', '=', 0)
			->where(function ($query) use ($softDate) {
				$query->where([['closingDate', '>',  $softDate]])
					->orWhere([['closingDate', '=',  '0000-00-00']])
					->orWhere([['closingDate', '=',  null]]);
			})
			->whereIn('samityId', $samityId)
			->pluck('id')
			->toArray();

		$mfnloanInformation = DB::table('mfn_loan')
			->where('softDel', '=', 0)
			->where(function ($query) use ($softDate) {
				$query->where([['loanCompletedDate', '>',  $softDate]])
					->orWhere([['loanCompletedDate', '=',  '0000-00-00']])
					->orWhere([['loanCompletedDate', '=',  null]]);
			})
			->whereIn('memberIdFk', $mfnMemberInformation)
			->pluck('id')
			->toArray();

		$mfnSamityDayId = DB::table('mfn_samity_day_change')
			->where('id', $req->id)
			->select('samityId', 'samityDayId', 'fixedDate', 'newSamityDayId', 'newFixedDate')
			->get();

		$previousdata = MfnSamityDayChange::find($req->id);


		// dd($mfnMemberInformation, $mfnloanInformation, $mfnSamityDayId, $softDate, $samityId, $req->id);

		// Update samity Info table
		foreach ($mfnSamityDayId as $key => $samityInfo) {

			if ($samityInfo->samityDayId > 0) {
				$updateSamityDay = DB::table('mfn_samity')
					->where('id', $samityInfo->samityId)
					->update(
						[
							'samityDayId' => $samityInfo->samityDayId
						]
					);
			} else {
				$updateSamityDay = DB::table('mfn_samity')
					->where('id', $samityInfo->samityId)
					->update(
						[
							'fixedDate'   => $samityInfo->fixedDate
						]
					);
			}
			// dd($req->id, $samityInfo, $updateSamityDay, $samityInfo->id, $samityInfo->samityDayId, $samityInfo->fixedDate);
		}
		// End of samity info update

		$mfnSamityDayChangeInfo = DB::table('mfn_samity_day_change')
			->where('id', $req->id)
			->get();

		$repaymentFrequencyWiseRepayDate = [
			'1'	 =>  7,
			'2'  =>  30
		];

		// Start of the loan reschedule
		//	GET HOLIDAY.
		$globalGovtHoliday = $this->MicroFinance->getGlobalGovtHoliday();
		$organizationHoliday = $this->MicroFinance->getOrganizationHoliday(1);
		$branchHoliday = $this->MicroFinance->getBranchHoliday();
		$samityHoliday = $this->MicroFinance->getSamityHolidayBySamityId($samityId[0]);
		$holiday = array_unique(array_merge($globalGovtHoliday, $organizationHoliday, $branchHoliday, $samityHoliday));

		for ($k = 0; $k < count($mfnloanInformation); $k++) :
			//	GET INSTALLMENT NUMBER AND SCHEDULE DATE FROM WHICH THE NEW SAMITY DAY WILL UPDATED.
			$getPartialInstallmentNum = MfnLoanSchedule::where('loanIdFk', $mfnloanInformation[$k])
				// ->where('isCompleted', 0)
				->where('scheduleDate', '>=', date_format(date_create($softDate), 'Y-m-d'))
				->where('isPartiallyPaid', '<=', 1)
				->select('installmentSl', 'scheduleDate')
				->first();

			//	GET FIRST SCHEDULE DATE FROM WHICH NEW SAMITY DAY WILL UPDATED.
			$scheduleDateWillUpdateFrom = Carbon::parse($getPartialInstallmentNum->scheduleDate);
			Carbon::setTestNow($scheduleDateWillUpdateFrom);

			foreach ($mfnSamityDayChangeInfo as $key => $mfnSamityDayChange) {
				if ($mfnSamityDayChange->fixedDate > 0) {
					$newScheduleDayName = $this->MicroFinance->getSamityDayNameValue($mfnSamityDayChange->fixedDated);
				} else {
					$newScheduleDayName = $this->MicroFinance->getSamityDayNameValue($mfnSamityDayChange->samityDayId);
				}
			}
			$newScheduleDateStart = new Carbon('next ' . $newScheduleDayName);
			$newScheduleDateStart = $newScheduleDateStart->toDateString();

			$holidayFound = 0;
			$scheduleDateArr = [];

			//	GET LOAN PARAMETERS.
			$loanOB = MfnLoan::where('id', $mfnloanInformation[$k])->select('repaymentFrequencyIdFk', 'repaymentNo')->first();

			$repaymentFrequencyId = $loanOB->repaymentFrequencyIdFk;
			$repaymentNo = $loanOB->repaymentNo;

			//	GATHERING NEW SCHEDULE DATE FOR NEW SAMITY DAY.
			if ($repaymentNo > 1) :
				for ($i = 0; $i < 1000; $i++) :
					$dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
					$date = date_create($newScheduleDateStart);
					date_add($date, date_interval_create_from_date_string($dayDiff));

					//	PROPAGATE SCHEDULE DATE FOR WEEKLY FREQUENCY.
					if ($repaymentFrequencyId == 1) :
						//	CHECK IF A DATE IS MATCHES TO HOLIDAY.
						foreach ($holiday as $key => $val) :
							if (date_create($val) >= $date) :
								if (date_create($val) == $date) :
									// dd(date_create($val), $date, $val, $key, $holiday);
									$holidayFound = 1;
									break;
								endif;
							endif;
						endforeach;

						if ($holidayFound == 0)
							$scheduleDateArr[] = date_format($date, "Y-m-d");

						$holidayFound = 0;
					endif;

					//	PROPAGATE SCHEDULE DATE FOR MONTHLY FREQUENCY.
					if ($repaymentFrequencyId == 2) :
						$dayDiff = ($repaymentFrequencyWiseRepayDate[$repaymentFrequencyId] * $i) . 'days';
						$date = date_create($newScheduleDateStart);
						date_add($date, date_interval_create_from_date_string($dayDiff));
						//$disbursementDate = date_create($req->disbursementDate);
						//date_add($disbursementDate, date_interval_create_from_date_string($dayDiff));

						$tos = Carbon::parse($newScheduleDateStart);
						$sot = $tos->addMonths($i)->toDateString();

						if ($i == 0)
							$targetDate = date_format($date, "Y-m-d");
						else
							foreach ($mfnSamityDayChangeInfo as $key => $mfnSamityDayChange) {
								if ($mfnSamityDayChange->fixedDate > 0) {
									$targetDate = $this->MicroFinance->getMonthlyLoanScheduleDateFilterBySamityDayId($sot, $mfnSamityDayChange->fixedDate);
								} else {
									$targetDate = $this->MicroFinance->getMonthlyLoanScheduleDateFilterBySamityDayId($sot, $mfnSamityDayChange->newSamityDayId);
								}
							}

						$originalMD = Carbon::parse($targetDate);
						$MD = Carbon::parse($targetDate);
						$targetDate = $MD->toDateString();

						//	CHECK IF A DATE IS MATCHES TO HOLIDAY, THEN SAMITY DATE SET TO NEXT WEEK.
						for ($j = 0; $j < 100; $j++) :
							if (in_array($targetDate, $holiday)) :
								$targetDate = $MD->addDays(7)->toDateString();

								if ($targetDate > $originalMD->endOfMonth()) :
									$targetDate = $MD->subDays(14)->toDateString();
								else :
									if (in_array($targetDate, $holiday)) :
										$targetDate = $MD->addDays(7)->toDateString();

										if ($targetDate > $originalMD->endOfMonth()) :
											$targetDate = $MD->subDays(21)->toDateString();
										endif;
									else :
										break;
									endif;
								endif;
							else :
								break;
							endif;
						endfor;

						$scheduleDateArr[] = $targetDate;
					endif;

					if (count($scheduleDateArr) == $repaymentNo)
						break;
				endfor;
			else :
				$scheduleDateArr = [];
				for ($i = 0; $i < $repaymentNo; $i++) :
					$dayDiff = ($repaymentFrequencyWiseRepayDate[1] * $i) . 'days';
					$date = date_create($newScheduleDateStart);
					date_add($date, date_interval_create_from_date_string($dayDiff));
					$scheduleDateArr[] = date_format($date, "Y-m-d");
				endfor;
			endif;
			dd($scheduleDateArr, $newScheduleDayName, $loanOB, $mfnloanInformation, $mfnSamityDayId, $mfnloanInformation[$k], $samityId[0], $req->id, $samityInfo);
			//	UPDATE LOAN SCHEDULE IN LOAN SCHEDULE TABLE FOR DATE CHANGE.
			for ($i = 0; $i < $repaymentNo; $i++) :
				MfnLoanSchedule::where('loanIdFk', $mfnloanInformation[$k])
					// ->where('isCompleted', 0)
					->where('scheduleDate', '>=', date_format(date_create($softDate), 'Y-m-d'))
					->where('installmentSl', $getPartialInstallmentNum->installmentSl)
					->update(['scheduleDate' => $scheduleDateArr[$i]]);

				$getPartialInstallmentNum->installmentSl++;
			endfor;

			//	UPDATE FIRST REPAY DATE WHEN NO INSTALLMENT IS COMPLETED IN LOAN TABLE.
			$noInstallmentCompletedYet = MfnLoanSchedule::where('loanIdFk', $mfnloanInformation[$k])
				->where('installmentSl', 1)
				// ->where('isCompleted', 0)
				->where('scheduleDate', '>=', date_format(date_create($softDate), 'Y-m-d'))
				->where('isPartiallyPaid', 0)
				->count();

			if ($noInstallmentCompletedYet == 1) :
				MfnLoan::where('id', $mfnloanInformation[$k])->update(['firstRepayDate' => $newScheduleDateStart]);
			endif;
		endfor;
		// End of the loan reschedule

		foreach ($mfnSamityDayChangeInfo as $key => $mfnSamityDayChange) {
			$insertDeletedData = DB::table('mfn_samity_day_change_deleted')
				->insert(
					[
						'samity_day_change_id'   => $mfnSamityDayChange->id,
						'samityId'               => $mfnSamityDayChange->samityId,
						'dsOldSamityCode'        => $mfnSamityDayChange->dsOldSamityCode,
						'branchId'               => $mfnSamityDayChange->branchId,
						'samityDayOrFixedDateId' => $mfnSamityDayChange->samityDayOrFixedDateId,
						'samityDayId'            => $mfnSamityDayChange->samityDayId,
						'dsOldSamityDay'         => $mfnSamityDayChange->dsOldSamityDay,
						'fixedDate'              => $mfnSamityDayChange->fixedDate,
						'newSamityDayId'         => $mfnSamityDayChange->newSamityDayId,
						'oldNewSamityDay'        => $mfnSamityDayChange->oldNewSamityDay,
						'newFixedDate'           => $mfnSamityDayChange->newFixedDate,
						'samityDayOptional'      => $mfnSamityDayChange->samityDayOptional,
						'effectiveDate'          => $mfnSamityDayChange->effectiveDate,
						'changeDate'             => $mfnSamityDayChange->changeDate,
						'createdDate'            => $softDate,
						'updatedDate'            => $softDate,
						'status'                 => $mfnSamityDayChange->status,
						'ds'                     => $mfnSamityDayChange->ds
					]
				);
		}

		$deleteSamityDayChange = DB::table('mfn_samity_day_change')
			->where('id', $req->id)
			->delete();
		$logArray = array(
			'moduleId'  => 6,
			'controllerName'  => 'MfnSamityDayChangeController',
			'tableName'  => 'mfn_samity_day_change',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$req->id]
		);
		Service::createLog($logArray);

		$data = array(
			'responseTitle'  =>  'Success!',
			'responseText'   =>  'Samity day has been deleted successfully.'
		);

		return response()->json($data);
	}

	public function getCurrentSamityDay(Request $req)
	{

		//	GET SAMITY INFORMATION.
		$getSamityOBJ = DB::table('mfn_samity')->select('name', 'branchId', 'samityDayId')->where('id', $req->id)->first();

		//	GET SAMITY BRANCH NAME.
		$getCurBranchName = DB::table('gnr_branch')->where('id', $getSamityOBJ->branchId)->value('name');

		$samityDay = $this->MicroFinance->getSamityDay();

		//	ASSIGN CURRENT SAMITY DAY NAME.
		$getCurSamityDayName = $samityDay[$getSamityOBJ->samityDayId];

		//	DELETE CURRENT SAMITY DAY AND FRIDAY FROM THE LIST.
		unset($samityDay[$getSamityOBJ->samityDayId]);

		// UNSET THE WEEKLY HOLIDAY
		$weeklyHolidays = explode(',', DB::table('mfn_setting_weekly_holiday')->where('status', 1)->value('weeklyHolidayIds'));
		foreach ($weeklyHolidays as  $weeklyHoliday) {
			unset($samityDay[$weeklyHoliday]);
		}

		$data = array(
			'getCurSamityName'	   =>  $getSamityOBJ->name,
			'getCurBranchId'	   =>  $getSamityOBJ->branchId,
			'getCurBranchName'	   =>  $getCurBranchName,
			'getCurSamityDayId'	   =>  $getSamityOBJ->samityDayId,
			'getCurSamityDayName'  =>  $getCurSamityDayName,
			'samityDay'			   =>  $samityDay
		);

		return response::json($data);
	}

	/*
		|--------------------------------------------------------------------------
		| MICRO FINANCE: ADD SAMITY DAY CHANGE CONTROLLER.
		|--------------------------------------------------------------------------
		*/
	public function addItem(Request $req)
	{
		// SAMITY DAY CHNAGE IS CURRENT AVAILABLE FROM BRANCH.
		// samity day can change only on Thursday
		$branchId = Auth::user()->branchId;
		$softwareDate = Microfin::getSoftwareDateBranchWise($branchId);
		$weekDay = Carbon::parse($softwareDate)->format('l');
		if ($weekDay != 'Thursday') {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Samity Day can be changed only on Thursday.'
			);
			return response::json($data);
		}

		$samity = MfnSamity::find($req->samityIdFk);

		// IF SAMITY DAY CHANGE CAUSES ANY LOAN SCEDULE PROBLEM THEN IT CLOUD NOT BE DONE.
		$effectiveDate = date_format(date_create($req->changeDate), 'Y-m-d');
		$info = $this->canChangeSamityDate($samity->id, $req->newSamityDayId, $effectiveDate);

		if ($info['canChangeSamityDate'] == false) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  $info['msg'],
			);
			return response::json($data);
		}

		// IF ANY SAMITY DAY CHANGE TODAY/AFTER THIS DATE, IT CAN NOT BE CHANGED.
		$isAnySamityDayChnageExists = DB::table('mfn_samity_day_change')
			->where('samityId', $samity->id)
			->where('effectiveDate', '>=', $effectiveDate)
			->count();

		if ($isAnySamityDayChnageExists > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Samity Day change exists today/after this date.',
			);
			return response::json($data);
		}

		// IF ANY TRANSACTION EXISTS FROM EFFTECTIVE DATE TO FUTURE DATE THEN IT CAN NOT BE CHANGED.
		$isAnyTrnsactionFromLoanCollection = DB::table('mfn_loan_collection')
			->where('softDel', 0)
			->where('samityIdFk', $samity->id)
			->where('collectionDate', '>=', $effectiveDate)
			->count();

		$isAnyTrnsactionFromSavingsDeposit = DB::table('mfn_savings_deposit')
			->where('softDel', 0)
			->where('samityIdFk', $samity->id)
			->where('depositDate', '>=', $effectiveDate)
			->count();

		if ($isAnyTrnsactionFromLoanCollection + $isAnyTrnsactionFromSavingsDeposit > 0) {
			$data = array(
				'responseTitle'  =>  'Warning!',
				'responseText'   =>  'Trsaction exists for this samity today/after this date.',
			);
			return response::json($data);
		}

		$rules = array(
			'samityIdFk'  =>  'required',
		);

		$attributesNames = array(
			'samityIdFk'  =>  'samity name',
		);

		$validator = Validator::make(Input::all(), $rules);
		$validator->setAttributeNames($attributesNames);

		if ($validator->fails())
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else {
			/*
				|--------------------------------------------------------------------------
				| ADD     : MEMBER SAMITY DAY CHANGE
				|--------------------------------------------------------------------------
				| UPDATE  : SAMITY
				|--------------------------------------------------------------------------
				|           samityDayId
				|--------------------------------------------------------------------------
				| UPDATE  : LOAN SCHEDULE
				|--------------------------------------------------------------------------
				| 		    scheduleDate
				|--------------------------------------------------------------------------
				| UPDATE  : LOAN [optional]
				|--------------------------------------------------------------------------
				|			firstRepayDate
				|--------------------------------------------------------------------------
				*/

			//	GET ALL THE LOAN ID OF THE SAMITY.
			$loanOB = MfnLoan::where('samityIdFk', $req->samityIdFk)
				->where('branchIdFk', $req->branchIdFk)
				// ->loanIncompleted()						// WE ARE HIDDING THIS SECTION BECAUSE WE ARE CHECKING THE COLLECTION IS COMING FROM isFromAutoProcess OR NOT FOR ALL LOANS ACCORDING TO NEW RULES FOR TWO WEEKLY HOLIDAY
				->select('id')
				->get();

			$loanIdArr = [];

			foreach ($loanOB as $loanId) :
				$loanIdArr[] = $loanId->id;
			endforeach;

			$checkIsFromAutoProcess = DB::table('mfn_loan_collection')
				->where([['isFromAutoProcess', '=', 1], ['collectionDate', '>=', date_format(date_create($req->effectiveDate), 'Y-m-d')], ['softDel', '=', 0], ['amount', '>', 0]])
				->whereIn('loanIdFk', $loanIdArr)
				->count('id');

			if ($checkIsFromAutoProcess > 0) {
				$data = array(
					'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
					'responseText'   =>  MicroFinance::getMessage('isFromAutoProcessErrors'),
					// 'loan'		     =>  $loanIdArr
				);
				return response::json($data);
			}

			$now = Carbon::now();
			$req->request->add(['createdDate' => $now]);
			$req->request->add(['effectiveDate' => $this->MicroFinance->getDBDateFormat($req->effectiveDate)]);
			// dd($req, $req->samityIdFk);
			// $create = MfnSamityDayChange::create($req->all());

			// SAMITY DAY WILL COMPARE WITH WEEKLY HOLIDAY
			$samityDayChangeEffectiveDate = $this->MicroFinance->getDBDateFormat($req->effectiveDate);  // ErrorExceptionErrorException : date_format() expects parameter 1 to be DateTimeInterface, string given

			$startDayChangeEffectiveDate = date("Y-m-01", strtotime($samityDayChangeEffectiveDate));
			$lastDayChangeEffectiveDate  = date("Y-m-t", strtotime($samityDayChangeEffectiveDate));

			$weeklyHoliday = MfnHoliday::where('isWeeklyHoliday', '=', 1)
				->where('date', '>=', $startDayChangeEffectiveDate)
				->where('date', '<=', $lastDayChangeEffectiveDate)
				->pluck('date')
				->toArray();

			$samityDayNumber = $req->newSamityDayId;
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

			// dd($req->newSamityDayId, $req->effectiveDate, $isMatched, $samityDayName, $weeklyHoliday);

			if ($isMatched > 0) {
				$data = array(
					'responseTitle' =>   'Warning!',
					'responseText'  =>   'This samity day is a weekly holiday! So please change the samity day to a regular working day!'
				);

				return response::json($data);
			}

			// INSERT DATA AT THE SAMITY DAY CHANGE TABLE
			$insertSamityDayChange = DB::table('mfn_samity_day_change')
				->insert(
					[
						'samityId'       		 => $req->samityIdFk,
						'branchId'       		 => $req->branchIdFk,
						'samityDayOrFixedDateId' => 1,
						'samityDayId'    		 => $req->oldSamityDayId,
						'newSamityDayId' 		 => $req->newSamityDayId,
						'effectiveDate'  		 => date_format(date_create($req->changeDate), 'Y-m-d'),
						'changeDate'     		 => date_format(date_create($req->changeDate), 'Y-m-d'),
						'createdDate'    		 => $req->createdDate
					]
				);
			$logArray = array(
				'moduleId'  => 6,
				'controllerName'  => 'MfnSamityDayChangeController',
				'tableName'  => 'mfn_samity_day_change',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('mfn_samity_day_change')->max('id')]
			);
			Service::createLog($logArray);

			//	UPDATE SAMITY DAY ID IN SAMITY TABLE.
			$samity->samityDayId = $req->newSamityDayId;
			$samity->save();

			// UPDATE THE SCHEDULE OF THIS SAMITY AFTER THE EFFECT DATE

			$data = array(
				'responseTitle'  =>  'Success!',
				'responseText'   =>  'New samity day has been saved successfully.',
				'loan'		     =>  $loanIdArr
			);

			return response::json($data);
		}
	}

	public function canChangeSamityDate($samityId, $newSamityDayId, $effectiveDate)
	{

		$samity = DB::table('mfn_samity')->where('id', $samityId)->first();

		$branchSoftwareDate = MicroFin::getSoftwareDateBranchWise($samity->branchId);

		$customSamityDayChange = (object) array(
			'effectiveDate' => $effectiveDate,
			'samityDayId' => $samity->samityDayId,
			'newSamityDayId' => $newSamityDayId
		);

		// GET THE MONTHLY LOANS WHICH HAVE SCHEDULE FROM THE EFFETIVE MONTH TO THAT MONTH END DATE.
		$loans = DB::table('mfn_loan AS loan')
			->join('mfn_loan_schedule AS ls', 'ls.loanIdFk', 'loan.id')
			->where([['loan.softDel', 0], ['ls.softDel', 0]])
			->where('loan.samityIdFk', $samityId)
			->where('loan.loanTypeId', 1)
			->where('loan.repaymentFrequencyIdFk', 2)
			->where('ls.scheduleDate', '>=', $effectiveDate)
			->where('ls.scheduleDate', '<=', Carbon::parse($effectiveDate)->endOfMonth()->format('Y-m-d'))
			->groupBy('loan.id')
			->select('loan.id', 'loan.loanCode', 'loan.firstRepayDate', 'loan.disbursementDate', 'loan.actualNumberOfInstallment', 'loan.samityIdFk')
			->get();

		if (count($loans) == 0) {
			return ['canChangeSamityDate' => true, 'msg' => ''];
		}

		// GET THE HOLIDAYS
		$holidays = MicroFin::getHolidaysForSamity($samityId, $loans->min('disbursementDate'));

		// FIXED GOV. HOLIDAYS MAY NOT ASSIGN TO THE CALLENDER. BUT WE WILL CONSIDER THE FIXED GOV. HOLIDAYS FOR THE UPCOMMING YEARS. WE ASSUME THE EXPECTED LOAN COMPLETE YEAR AFTER 5 YEARS FROM THE MIN LOAN DIS. DATE.
		$expectedLoanCompleteYear = (int) Carbon::parse($loans->min('disbursementDate'))->format('Y') + 5;
		$maxGovHolidayYearFromCallender = (int) DB::table('mfn_setting_holiday')->where('isGovHoliday', 1)->max('year');

		if ($maxGovHolidayYearFromCallender < $expectedLoanCompleteYear) {
			$fixedGovHolidays = MicroFin::getFixedGovHolidaysByYears(Carbon::parse($loans->min('disbursementDate'))->format('Y'), $expectedLoanCompleteYear);
			$weeklyHolidays = MicroFin::getWeeklyHolidaysByYears(Carbon::parse($loans->min('disbursementDate'))->format('Y'), $expectedLoanCompleteYear);
			$holidays = array_unique(array_merge($holidays, $fixedGovHolidays, $weeklyHolidays));
		}

		sort($holidays);

		$loanHavingsLoanScheduleProblem = [];
		foreach ($loans as $loan) {
			$scheduleDates = app('App\Http\Controllers\microfin\process\MiscellaneousController')->makeLoanScheduleForMonthlyLoan($loan, $holidays, $customSamityDayChange);
			// $scheduleDates = DB::table('mfn_loan_schedule')->where('softDel', 0)->where('loanIdFk', $loan->id)->pluck('scheduleDate')->toArray();

			// dd($scheduleDates);

			$targetScheduleDate = array_filter($scheduleDates, function ($value) use ($effectiveDate) {
				return $value >= Carbon::parse($effectiveDate)->startOfMonth()->format('Y-m-d') && $value <= Carbon::parse($effectiveDate)->endOfMonth()->format('Y-m-d');
			});

			if (count($targetScheduleDate) > 0) {
				$targetScheduleDate = array_values($targetScheduleDate);
				if ($targetScheduleDate[0] <= $branchSoftwareDate) {
					array_push($loanHavingsLoanScheduleProblem, $loan->loanCode . ' New Schedule: ' . $targetScheduleDate[0]);
				}
			}
		}

		if (count($loanHavingsLoanScheduleProblem) > 0) {
			return ['canChangeSamityDate' => false, 'msg' => 'This Loans would be have schedule problem on corresponding dates if samity day changed: ' . implode(', ', $loanHavingsLoanScheduleProblem)];
		}

		return ['canChangeSamityDate' => true, 'msg' => ''];
	}
}
