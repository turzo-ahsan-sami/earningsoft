<?php

	namespace App\Http\Controllers\microfin\settings\ajax;

	use Illuminate\Http\Request;
	use App\Http\Requests;
	use Session;
	use Validator;
	use Response;
	use DB;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\Input;
	use Illuminate\Support\Facades\Hash;
	use Illuminate\Support\Facades\Auth;
	use App\Http\Controllers\Controller;
	use App\microfin\settings\MfnHoliday;
	use App\microfin\settings\MfnGovHoliday;
	use App\microfin\settings\MfnOrgBranchSamityHoliday;


	class MfnSettingAjaxController extends Controller {

		public function getHolidayDetails(Request $req){

			$holiday = MfnGovHoliday::find($req->id);
			
			return response::json($holiday);
		}

		public function getHolidayYearDetails(Request $req){

			// Get the year days
			$today = Carbon::toDay();
			$today->year = $req->year;
			$startDate = $today->copy()->startOfYear();
			$endDate = $startDate->copy()->endOfYear();
			
			$startDate = $startDate->format('Y-m-d');
			$endDate = $endDate->format('Y-m-d');

			// Get Holidays

			$holiday = MfnHoliday::active()->where('year',$req->year)->get();
			if (count($holiday)<1) {
				$weekDayNoArray = 'empty';
			}
			else{
				// get weekly holiday
				$weeklyDays = MfnHoliday::active()->where('year',$req->year)->where('isWeeklyHoliday',1)->limit(2)->pluck('date')->toArray();

				$weekDayNoArray = [Carbon::parse($weeklyDays[0])->dayOfWeek,Carbon::parse($weeklyDays[1])->dayOfWeek];
				$weekDayNoArray = array_unique($weekDayNoArray);
			}
			

			// get gov. holiday
			$hasGovHolidays = (int) MfnHoliday::active()->where('year',$req->year)->where('isGovHoliday',1)->value('id');

			// get Org/Branch/Samity Holiday
			$orgHolidays = MfnOrgBranchSamityHoliday::active()->where('dateFrom','>=',$startDate)->where('dateTo','<=',$endDate)->get();
			$orgHolidayList[] = array();
			$i = 0;
			foreach ($orgHolidays as $key => $orgHoliday) {
				$dateFrom = Carbon::parse($orgHoliday->dateFrom);
				$dateTo = Carbon::parse($orgHoliday->dateTo);
				while ($dateFrom->lte($dateTo)) {
					$orgHolidayList[$i] =  array(
						'date'			=>	$dateFrom->format('Y-m-d'),
						'holidayType'	=>	$orgHoliday->holidayType,
						'description'	=>	$orgHoliday->description
					);
					$dateFrom->addDay();
					$i++;
				}
			}

			$data = array(
				'startDate'			=> $startDate,
				'endDate'			=> $endDate,
				'weekDayNoArray' 	=> $weekDayNoArray,
				'hasGovHolidays' 	=> $hasGovHolidays,
				'orgHolidays' 		=> $orgHolidayList
			);

			return response::json($data);		
		}

		/**
		 * returns details information of Org\Branch\Samity Holiday
		 * @param  Request $req [holiday id]
		 * @return [array]       [details information]
		 */
		public function getOrgHolidayDetails(Request $req){
			$holiday = MfnOrgBranchSamityHoliday::find($req->id);

			if($holiday->isOrgHoliday==1){
				$radioValue = "org";
			}
			elseif($holiday->isBranchHoliday==1){
				$radioValue = "branch";
			}
			elseif($holiday->isSamityHoliday==1){
				$radioValue = "samity";
			}

			$data = array(
				'holiday'		=> $holiday,
				'radioValue'	=> $radioValue,
				'dateFrom'		=> date('d-m-Y',strtotime($holiday->dateFrom)),
				'dateTo'		=> date('d-m-Y',strtotime($holiday->dateTo))
			);

			return response::json($data);
		}
	}