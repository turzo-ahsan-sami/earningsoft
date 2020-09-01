<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
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
use App\Http\Controllers\microfin\MicroFinance;
use Illuminate\Support\Facades\Route;

class HolidayController extends Controller {

  protected $MicroFinance;
  public $route;

  public function __construct() {

    $this->MicroFinance = New MicroFinance;
    $this->route = $this->layoutDynamic();
  }

  public function layoutDynamic() {

    $path = Route::current()->action['prefix'];

            // dd($path);

    if($path=='/mfn') {
      $layout = 'layouts/microfin_layout';
    } elseif($path == '/acc') {
      $layout = 'layouts/acc_layout';
    } elseif($path == '/gnr'){
      $layout = 'layouts/gnr_layout';
    } elseif($path == '/inv'){
      $layout = 'layouts/inventory_layout';
    } elseif($path == '/fams'){
      $layout = 'layouts/fams_layout';
    } elseif($path == '/pos'){
      $layout = 'layouts/pos_layout';
    }

    $route = array(
      'layout'        => $layout,
      'path'          => $path
    );

    return $route;

  }

  public function index(Request $req){

        /*$temp = date("z",strtotime('26-03'.'-'.'2012')) + 1;

        dd($temp);*/

        $today = Carbon::toDay();

        $startDate = $today->copy()->subYears(7)->startOfYear();
        if ($startDate->year%4==0) {
          $startDate->addYear();
        }
        $endDate = $startDate->copy()->endOfYear();

        $govHolidays = MfnGovHoliday::active()->get();
        $yearList = array();
        $tempYear = $startDate->copy();
        for ($i=0; $i < 20; $i++) {
          $yearList = $yearList + [$tempYear->year => $tempYear->year];
          $tempYear->addYear();
        }

            // dd($this->route);

        $data = array(
          'route'         => $this->route,
          'startDate'		=> $startDate,
          'endDate'		=> $endDate,
          'govHolidays'	=> $govHolidays,
          'yearList'		=> $yearList,
          'selectedYear'	=> $startDate->year
        );

        return view('gnr.tools.holiday.viewHolidayCalender',$data);

      }

      public function storeHoliday(Request $req){

        //dd($req);
        $samityDay = -1;
        if ($req->saturday == 'on') {
          $samityDay = 1;
        }
        elseif ($req->sunday == 'on') {
          $samityDay = 2;
        }
        elseif ($req->monday == 'on') {
          $samityDay = 3;
        }
        elseif ($req->tuesday == 'on') {
          $samityDay = 4;
        }
        elseif ($req->wednesday == 'on') {
          $samityDay = 5;
        }
        elseif ($req->thursday == 'on') {
          $samityDay = 6;
        }

        $samityDayCheck = DB::table('mfn_samity')
        ->where([['softDel', '=', 0], ['closingDate', '=', '0000-00-00'], ['samityDayId', $samityDay]])
        ->orderBy('code', 'asc')
        ->select(DB::raw("CONCAT(code, ' - ',name) AS code_name"))
        ->pluck('code_name')
        ->toArray();

            // dd($samityDayCheck);
        if (sizeof($samityDayCheck) > 0) {
          $data = array(
            'responseTitle'  =>  MicroFinance::getMessage('msgWarning'),
            'responseText'   =>  'Please Change Samity Day First!',
            'samityDayCheck' =>  $samityDayCheck
          );

          return response::json($data);
        }

        $holidayTextWeekly = array();
        $existingHolidayDeleted = MfnHoliday::where('year',$req->year)->delete();
        $startDate = Carbon::parse('01-01-'.$req->year);

        $dayCheckBoxCounter = 0;

        
        foreach ($req->day as $key => $day) {
                                //dd($req->weeklyHolidayText);
         $govHolidayText = true;
         $weeklyHolidayText = true;

            //print_r($req->weeklyHolidayText[$key]);
            //print_r([$key]);
         if (!isset($req->weeklyHolidayText[$key])){
          $weeklyHolidayText = false;
        }
        if (!isset($req->govHolidayText[$key])){
          $govHolidayText = false;
        }

        if (($weeklyHolidayText && ($req->weeklyHolidayText[$key]=='true')) || 
          ($govHolidayText && ($req->govHolidayText[$key]=='true'))) {
          /*if ($req->weeklyHolidayText[$key]=='true') || $req->govHolidayText[$key]=='true') {*/
            if ($req->dayCheckBox[$dayCheckBoxCounter] == $day) {
              $descriptionJson['govHoliday'] = $req->descriptionText[$key];
              $holiday = new MfnHoliday;
              $holiday->year					= $req->year;
              $holiday->date					= $startDate;
              $holiday->dayNo					= $day;
              $holiday->isWeeklyHoliday		= ($req->weeklyHolidayText[$key]=='true') ? 1:0;
              $holiday->isGovHoliday			= ($req->govHolidayText[$key]=='true') ? 1:0;
              $holiday->holidayTitle			= $req->finalTitle[$key];
              $holiday->holidayDescription	= json_encode($descriptionJson);
              $holiday->createdAt				= Carbon::now();
              $holiday->save();

              ++$dayCheckBoxCounter;
            }
            else {
                        // --$dayCheckBoxCounter;
            }

          }
          $startDate->addDay();
        }

        if ($dayCheckBoxCounter == 0) {
          $checkBoxCounter = 0;
          $startDate = Carbon::parse('01-01-'.$req->year);
          foreach ($req->day as $key => $day) {
            if ($checkBoxCounter < sizeof($req->dayCheckBox)) {
              if ($req->dayCheckBox[$checkBoxCounter] == $day || $req->govHolidayText[$key]=='true') {
                            // dd($startDate, $req->dayCheckBox[$checkBoxCounter], $key, $req->dayCheckBox, $req->day);
                      //dd($descriptionJson);
                $descriptionJson['govHoliday'] = $req->descriptionText[$key];

                $holiday = new MfnHoliday;
                $holiday->year					= $req->year;
                $holiday->date					= $startDate;
                $holiday->dayNo					= $day;
                $holiday->isWeeklyHoliday		= ($req->govHolidayText[$key]!='true') ? 1:0;
                $holiday->isGovHoliday			= ($req->govHolidayText[$key]=='true') ? 1:0;
                $holiday->holidayTitle			= $req->finalTitle[$key];
                $holiday->holidayDescription	= json_encode($descriptionJson);
                $holiday->createdAt				= Carbon::now();
                $holiday->save();

                ++$checkBoxCounter;
              }
            }
            $startDate->addDay();
          }
        }

            // dd($dayCheckBoxCounter, $checkBoxCounter, $req);

        $data = array(
          'responseTitle' =>  'Success!',
          'responseText'  =>  'Data saved successfully.'
        );

        $logArray = array(
          'moduleId'  => 7,
          'controllerName'  => 'HolidayController',
          'tableName'  => 'mfn_setting_holiday',
          'operation'  => 'insert',
          'primaryIds'  => [DB::table('mfn_setting_holiday')->max('id')]
        );
        Service::createLog($logArray);


        return response::json($data);

      }

      /*//////  Gov Holiday */

      public function viewGovHoliday(){
       $holidays = MfnGovHoliday::where('softDel',0)->get();
       return view('gnr.tools.holiday.govHoliday.viewGovHoliday',['holidays'=>$holidays, 'route'=>$this->route]);
     }

     public function addGovHoliday(){
       return view('gnr.tools.holiday.govHoliday.addGovHoliday', ['route'=>$this->route]);
     }

     public function storeGovHoliday(Request $req){

       foreach ($req->title as $key => $title) {
        $govHoliday = new MfnGovHoliday;
        $govHoliday->title 			= $title;
        $govHoliday->date 			= $req->date[$key];
        $govHoliday->description	= $req->description[$key];
        $govHoliday->createdAt 		= Carbon::now();
        $govHoliday->save();
      }

      $data = array(
        'responseTitle' =>  'Success!',
        'responseText'  =>  'Data inserted successfully.'
      );
      $logArray = array(
        'moduleId'  => 7,
        'controllerName'  => 'HolidayController',
        'tableName'  => 'mfn_setting_gov_holiday',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('mfn_setting_gov_holiday')->max('id')]
      );
      Service::createLog($logArray);

      return response::json($data);
    }

    public function updateGovHoliday(Request $req){

     $rules = array(
      'title'         =>  'required',
      'date'   		=>  'required'
    );

     $attributesNames = array(
      'title'         =>  'Title',
      'date'   		=>  'Date'
    );

     $validator = Validator::make(Input::all(), $rules);
     $validator->setAttributeNames($attributesNames);

     if($validator->fails()){
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    }
    else{
      $previousdata = MfnGovHoliday::find ($req->holidayId);
      $govHoliday = MfnGovHoliday::find($req->holidayId);
      $govHoliday->title 			= $req->title;
      $govHoliday->date 			= $req->date;
      $govHoliday->description	= $req->description;
      $govHoliday->save();

      $data = array(
       'responseTitle' =>  'Success!',
       'responseText'  =>  'Data updated successfully.'
     );
      $logArray = array(
        'moduleId'  => 7,
        'controllerName'  => 'HolidayController',
        'tableName'  => 'mfn_setting_gov_holiday',
        'operation'  => 'update',
        'previousData'  => $previousdata,
        'primaryIds'  => [$previousdata->id]
      );
      Service::createLog($logArray);

      return response::json($data);
    }
  }

  public function deleteGovHoliday(Request $req) {
   $previousdata=MfnGovHoliday::find($req->id);
   $govHoliday = MfnGovHoliday::find($req->id);
   //$currentData = json_encode( DB::table('mfn_setting_holiday')->whereIn('id',[8])->get() );
   //dd($previousdata->id,[(int)$previousdata->id],$currentData);
   $govHoliday->softDel = 1;
   $govHoliday->save();

   $data = array(
    'responseTitle' =>  'Success!',
    'responseText'  =>  'Holiday deleted successfully.'
  );
   $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'HolidayController',
    'tableName'  => 'mfn_setting_gov_holiday',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
   Service::createLog($logArray);

   return response::json($data);
 }

 /*//////  End Gov Holiday */





 /*///// Org/Branch/Samity Holiday  */

 public function viewOrgBranchSamityHoliday(){
  //dd('ok');
   $holidays = MfnOrgBranchSamityHoliday::active()->get();
   return view("gnr.tools.holiday.orgBranchSamityHoliday.viewOrgBranchSamityHoliday",['holidays'=>$holidays, 'route'=>$this->route]);
 }

 public function addOrgBranchSamityHoliday(){
   return view("gnr.tools.holiday.orgBranchSamityHoliday.addOrgBranchSamityHoliday", ['route'=>$this->route]);
 }

 public function storeOrgBranchSamityHoliday(Request $req){

   $rules = array(
    'dateFrom'      =>  'required',
    'dateTo'   		=>  'required',
    'holidayType'   =>  'required'
  );

   if ($req->applicableFor=="branch") {
     $rules = $rules + array(
      'branch'	=>	'required'
    );
   }

   if ($req->applicableFor=="samity") {
     $rules = $rules + array(
      'samity'	=>	'required'
    );
   }

   $attributesNames = array(
    'dateFrom'      =>  'Date From',
    'dateTo'   		=>  'Date To',
    'holidayType'   =>  'Holiday Type',
    'branch'		=>	'Branch',
    'samity'		=>	'Samity'
  );

   $validator = Validator::make(Input::all(), $rules);
   $validator->setAttributeNames($attributesNames);

   if($validator->fails()){
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  }


  if ($req->applicableFor=="org") {
    $isOrg = 1;
    $orgIdFk = DB::table('hr_emp_org_info')->where('emp_id_fk',Auth::user()->emp_id_fk)->value('company_id_fk') ;
    if (Auth::user()->id==1) {
      $orgIdFk = 1;
    }
    $isBranch = 0;
    $branchIdFk = 0;
    $isSamity = 0;
    $samityIdFk = 0;
  }
  else if($req->applicableFor=="branch"){
    $isOrg = 0;
    $orgIdFk = 0;
    $isBranch = 1;
    $branchIdFk = $req->branch;
    $isSamity = 0;
    $samityIdFk = 0;
  }
  else if($req->applicableFor=="samity"){
    $isOrg = 0;
    $orgIdFk = 0;
    $isBranch = 0;
    $branchIdFk = 0;
    $isSamity = 1;
    $samityIdFk = $req->samity;
  }

  $dateFrom = Carbon::parse($req->dateFrom);
  $dateTo = Carbon::parse($req->dateTo);

  $orgHoliday = new MfnOrgBranchSamityHoliday;
  $orgHoliday->isOrgHoliday		= $isOrg;
  $orgHoliday->ogrIdFk			= $orgIdFk;
  $orgHoliday->isBranchHoliday	= $isBranch;
  $orgHoliday->branchIdFk			= $branchIdFk;
  $orgHoliday->isSamityHoliday	= $isSamity;
  $orgHoliday->samityIdFk			= $samityIdFk;
  $orgHoliday->dateFrom 			= $dateFrom;
  $orgHoliday->dateTo 			= $dateTo;
  $orgHoliday->holidayType 		= $req->holidayType;
  $orgHoliday->description 		= $req->description;
  $orgHoliday->createdAt 			= Carbon::now();
  $orgHoliday->save();


        	// add this informastion to the holiday table
        	/*$holidays = MfnGovHoliday::active()->where('date','>=',$dateFrom->format('Y-m-d'))->where('date','<=', $dateTo->format('Y-m-d'))->get();

        	foreach ($holidays as $key => $holiday) {

        		if($isOrg==1){
        			$newOrgIds = ($holiday->orgIds=='') ? $orgIdFk: $holiday->orgIds.','.$orgIdFk;
        		}
        		elseif($isBranch==1){
        			$newBranchIds = ($holiday->branchIds=='') ? $branchIdFk: $holiday->branchIds.','.$branchIdFk;
        		}
        		elseif($isSamity==1){
        			$newSamityIds = ($holiday->samityIds=='') ? $samityIdFk: $holiday->samityIds.','.$samityIdFk;
        		}

        		$holiday->isOrgHoliday 		= $isOrg;
        		$holiday->orgIds 			= $newOrgIds;
        		$holiday->isBranchHoliday 	= $isBranch;
        		$holiday->branchIds 		= $newBranchIds;
        		$holiday->isSamityHoliday 	= $isSamity;
        		$holiday->samityIds 		= $newSamityIds;
        		$holiday->save();
        	}*/

            //  GATHERING HOLIDAY INFORMATION.
          $holidayData = $this->MicroFinance->getLoanRescheduleForHolidayForORGorBRAorSAM($dateFrom, $dateTo, $req->applicableFor, (int) $orgIdFk, (int) $branchIdFk, (int) $samityIdFk);
          $holidayData=0;

          $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data saved successfully.',
                //'dateFrom1'      =>  $dateFrom,
                //'dateFrom'      =>  $dateFrom->toDateString(),
                //'dateTo'        =>  $dateTo->toDateString(),
            'applicableFor' =>  $req->applicableFor,
            'holidayData'   =>  $holidayData
          );
          $logArray = array(
            'moduleId'  => 7,
            'controllerName'  => 'HolidayController',
            'tableName'  => 'mfn_setting_orgBranchSamity_holiday',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('mfn_setting_orgBranchSamity_holiday')->max('id')]
          );
          Service::createLog($logArray);

          return response::json($data);
        }


        public function updateOrgBranchSamityHoliday(Request $req){

        	$rules = array(
            'dateFrom'      =>  'required',
            'dateTo'   		=>  'required',
            'holidayType'   =>  'required'
          );

          if ($req->applicableFor=="branch") {
           $rules = $rules + array(
            'branch'	=>	'required'
          );
         }

         if ($req->applicableFor=="samity") {
           $rules = $rules + array(
            'samity'	=>	'required'
          );
         }

         $attributesNames = array(
          'dateFrom'      =>  'Date From',
          'dateTo'   		=>  'Date To',
          'holidayType'   =>  'Holiday Type',
          'branch'		=>	'Branch',
          'samity'		=>	'Samity'
        );

         $validator = Validator::make(Input::all(), $rules);
         $validator->setAttributeNames($attributesNames);

         if($validator->fails()){
          return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }


        if ($req->applicableFor=="org") {
          $isOrg = 1;
          $orgIdFk = DB::table('hr_emp_org_info')->where('emp_id_fk',Auth::user()->emp_id_fk)->value('company_id_fk') ;
          $isBranch = 0;
          $branchIdFk = 0;
          $isSamity = 0;
          $samityIdFk = 0;
        }
        else if($req->applicableFor=="branch"){
          $isOrg = 0;
          $orgIdFk = 0;
          $isBranch = 1;
          $branchIdFk = $req->branch;
          $isSamity = 0;
          $samityIdFk = 0;
        }
        else if($req->applicableFor=="samity"){
          $isOrg = 0;
          $orgIdFk = 0;
          $isBranch = 0;
          $branchIdFk = 0;
          $isSamity = 1;
          $samityIdFk = $req->samity;
        }

        $dateFrom = Carbon::parse($req->dateFrom);
        $dateTo = Carbon::parse($req->dateTo);
        $previousdata = MfnOrgBranchSamityHoliday::find ($req->id);

        $orgHoliday = MfnOrgBranchSamityHoliday::find($req->id);
        $orgHoliday->isOrgHoliday		= $isOrg;
        $orgHoliday->ogrIdFk			= $orgIdFk;
        $orgHoliday->isBranchHoliday	= $isBranch;
        $orgHoliday->branchIdFk			= $branchIdFk;
        $orgHoliday->isSamityHoliday	= $isSamity;
        $orgHoliday->samityIdFk			= $samityIdFk;
        $orgHoliday->dateFrom 			= $dateFrom;
        $orgHoliday->dateTo 			= $dateTo;
        $orgHoliday->holidayType 		= $req->holidayType;
        $orgHoliday->description 		= $req->description;
        $orgHoliday->save();

        $data = array(
          'responseTitle' =>  'Success!',
          'responseText'  =>  'Data updated successfully.'
        );

        $logArray = array(
          'moduleId'  => 7,
          'controllerName'  => 'HolidayController',
          'tableName'  => 'mfn_setting_orgBranchSamity_holiday',
          'operation'  => 'update',
          'previousData'  => $previousdata,
          'primaryIds'  => [$previousdata->id]
        );
        Service::createLog($logArray);

        return response::json($data);
      }

      public function deleteOrgBranchSamityHoliday(Request $req) {
       $previousdata=MfnOrgBranchSamityHoliday::find($req->id);
       $orgHoliday = MfnOrgBranchSamityHoliday::find($req->id);
       $orgHoliday->softDel = 1;
       $orgHoliday->save();

       $data = array(
        'responseTitle' =>  'Success!',
        'responseText'  =>  'Data deleted successfully.'
      );
       $logArray = array(
        'moduleId'  => 7,
        'controllerName'  => 'HolidayController',
        'tableName'  => 'mfn_setting_orgBranchSamity_holiday',
        'operation'  => 'delete',
        'previousData'  => $previousdata,
        'primaryIds'  => [$previousdata->id]
      );
       Service::createLog($logArray);

       return response::json($data);
     }

     /*/////  End Org/Branch/Samity Holiday  */

     /* START OF THE HOLIDAY LIST */

     public function getHolidayList () {
      $year = array();

      $yearList = array();

      $count = 0;

      $route = $this->route;

      $title = 'Holiday List';

      $headTitle = 'Holiday List';

      $companyInfo = DB::table('gnr_company')
      ->get();

      $yearInfo = DB::table('gnr_fiscal_year')
      ->get();

      foreach ($yearInfo as $key => $info) {

        list($first, $second) = explode(' - ', $info->name);

        $year[$count] = $first;

        ++$count;
      }

      rsort($year);

      $count = count($year);

            // $clength = count($cars);
      for($x = 0; $x < $count; $x++) {
        $yearList[] = $year[$x];
      }

      $error = '';

      return view('gnr.tools.holiday.holidayList.viewHolidaList', compact('route', 'headTitle', 'yearList', 'error'));
    }

    public function getHolidayListBarnchInfo (Request $req) {
            // dd($req->id);

      $branchInfo = DB::table('gnr_branch')
      ->where('companyId', $req->id)
      ->get();

      return response()->json($branchInfo);

    }

    public function getHolidayListSamityInfo (Request $req) {
            // dd($req->id);

      $branchInfo = DB::table('mfn_samity')
      ->where('branchId', $req->id)
      ->get();

      return response()->json($branchInfo);

    }

    public function getReportTable (Request $req) {
      $check = '';
      $error = '';
      $count = 0;
      $checkSubmit = 'Type '.$req->DateSelection;
      $date = '';
      $finalHolidayData = array();

            // dd($req->searchYear);
						// dd($req->txtDate1, $req->txtDate2, $req->searchYear);

      if ($checkSubmit == 'Type Y') {
        $req->txtDate1 = '';
        $req->txtDate2 = '';
      }
      else{
        $req->searchYear = '';
      }

      if ($req->searchYear != '' and $req->searchYear != null) {
        $holidaySettingsData = DB::table('mfn_setting_holiday')
        ->select('id', 'date', 'holidayDescription AS description')
        ->where('date', 'like', $req->searchYear.'%')
        ->get();

        $holidaySettingsDataArray = $holidaySettingsData->pluck('date')->toArray();

        $orgHolidaySettingsData = DB::table('mfn_setting_orgBranchSamity_holiday')
        ->select('id', 'dateFrom', 'dateTo', 'description')
        ->where([['dateFrom', 'like', $req->searchYear.'%'], ['dateTo', 'like', $req->searchYear.'%']])
        ->get();

        $orgHolidaySettingsDataArray = $orgHolidaySettingsData->pluck('dateFrom')->toArray();

        $mergedDates = array_merge($holidaySettingsDataArray, $orgHolidaySettingsDataArray);

        sort($mergedDates);

        $count = count($mergedDates);

                // $clength = count($cars);
        for($x = 0; $x < $count; $x++) {
          $mergedDatesList[] = $mergedDates[$x];
        }

        $orgHolidaySettingsDataFinal = DB::select("SELECT `dateFrom`, `dateTo`, `description`, CONCAT(`dateFrom`,' , ', `dateTo`) AS date FROM `mfn_setting_orgBranchSamity_holiday` WHERE `dateFrom` LIKE '$req->searchYear%'");

        foreach ($mergedDatesList as $key => $mergedDates) {
          foreach ($holidaySettingsData as $key => $holidaySettings) {
            if ($holidaySettings->date == $mergedDates) {
              $finalHolidayData[$mergedDates] = $holidaySettings;
              break;
            }
          }

          foreach ($orgHolidaySettingsDataFinal as $key => $orgHolidaySettings) {
            if ($orgHolidaySettings->dateFrom <= $mergedDates and $orgHolidaySettings->dateTo >= $mergedDates and $check != $orgHolidaySettings->description) {
              $finalHolidayData[$mergedDates] = $orgHolidaySettings;
              $check = $orgHolidaySettings->description;
              break;
            }
          }
        }

        return view('gnr.tools.holiday.holidayList.viewHolidaListReportTable', compact('data', 'error', 'finalHolidayData', 'checkSubmit', 'date'));

                // dd($finalHolidayData);

                // dd($holidaySettingsData, $orgHolidaySettingsData, $holidaySettingsDataArray, $orgHolidaySettingsDataArray, $mergedDatesList, $finalHolidayData, $orgHolidaySettingsDataFinal);
      }
      elseif ($req->txtDate1 !='' and $req->txtDate2 !='' and $req->txtDate1 != null and $req->txtDate2 != null) {
                // dd($req->txtDate1, $req->txtDate2);
        $date = $req->txtDate2.' , '.$req->txtDate1;

        $holidaySettingsData = DB::table('mfn_setting_holiday')
        ->select('id', 'date', 'holidayDescription AS description')
        ->where([['date', '<=', $req->txtDate1], ['date', '>=', $req->txtDate2]])
        ->get();

        $holidaySettingsDataArray = $holidaySettingsData->pluck('date')->toArray();

        $orgHolidaySettingsData = DB::table('mfn_setting_orgBranchSamity_holiday')
        ->select('id', 'dateFrom', 'dateTo', 'description')
        ->where([['dateFrom', '<=', $req->txtDate1], ['dateTo', '>=', $req->txtDate2]])
        ->get();

        $orgHolidaySettingsDataArray = $orgHolidaySettingsData->pluck('dateFrom')->toArray();

        $mergedDates = array_merge($holidaySettingsDataArray, $orgHolidaySettingsDataArray);

        sort($mergedDates);

        $count = count($mergedDates);

                // $clength = count($cars);
        for($x = 0; $x < $count; $x++) {
          $mergedDatesList[] = $mergedDates[$x];
        }

        $orgHolidaySettingsDataFinal = DB::select("SELECT `dateFrom`, `dateTo`, `description`, CONCAT(`dateFrom`,' , ', `dateTo`) AS date FROM `mfn_setting_orgBranchSamity_holiday` WHERE `dateFrom` LIKE '$req->searchYear%'");

        foreach ($mergedDatesList as $key => $mergedDates) {
          foreach ($holidaySettingsData as $key => $holidaySettings) {
            if ($holidaySettings->date == $mergedDates) {
              $finalHolidayData[$mergedDates] = $holidaySettings;
              break;
            }
          }

          foreach ($orgHolidaySettingsDataFinal as $key => $orgHolidaySettings) {
            if ($orgHolidaySettings->dateFrom <= $mergedDates and $orgHolidaySettings->dateTo >= $mergedDates and $check != $orgHolidaySettings->description) {
              $finalHolidayData[$mergedDates] = $orgHolidaySettings;
              $check = $orgHolidaySettings->description;
              break;
            }
          }
        }

        return view('gnr.tools.holiday.holidayList.viewHolidaListReportTable', compact('data', 'error', 'finalHolidayData', 'checkSubmit', 'date'));
      }
      elseif ($req->txtDate1 =='' || $req->txtDate2 =='' || $req->txtDate1 == null || $req->txtDate2 == null ||
        $req->searchYear == '' || $req->searchYear == null) {

                // dd($error);
        if ($checkSubmit == 'Type Y') {
          $error = 'Please select Year Properly';
        }
        else {
          $error = 'Please select Date Range Properly';
        }


        return view('gnr.tools.holiday.holidayList.viewHolidaListReportTable', compact('data', 'error', 'checkSubmit'));

      }

            // dd($req);


    }

    /* END OF THE HOLIDAY LIST */
  }
