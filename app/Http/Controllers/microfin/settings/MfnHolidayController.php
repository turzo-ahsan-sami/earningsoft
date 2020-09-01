<?php

namespace App\Http\Controllers\microfin\settings;

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
use App\Http\Controllers\microfin\MicroFinance;
use Illuminate\Support\Facades\Route;

class MfnHolidayController extends Controller
{

    protected $MicroFinance;

    public function __construct()
    {

        $this->MicroFinance = new MicroFinance;
    }

    public function index(Request $req)
    {

        $path = Route::current()->action['prefix'];

        // dd($path);

        if ($path == '/mfn') {

            $layout = 'layouts/microfin_layout';
        } elseif ($path == '/acc') {

            $layout = 'layouts/acc_layout';
        } elseif ($req->session()->get('currentModule') === 'fams') {

            $layout = 'layouts/fams_layout';
        } elseif ($req->session()->get('currentModule') === 'inventory') {

            $layout = 'layouts/inventory_layout';
        } elseif ($req->session()->get('currentModule') === 'pos') {

            $layout = 'layouts/pos_layout';
        } elseif ($req->session()->get('currentModule') === 'general') {

            $layout = 'layouts/gnr_layout';
        }

        // dd($layout);


        $today = Carbon::toDay();

        $startDate = $today->copy()->subYears(10)->startOfYear();
        $endDate = $startDate->copy()->endOfYear();

        $govHolidays = MfnGovHoliday::active()->get();
        $yearList = array();
        $tempYear = $startDate->copy();
        for ($i = 0; $i < 20; $i++) {
            $yearList = $yearList + [$tempYear->year => $tempYear->year];
            $tempYear->addYear();
        }



        $data = array(
            'layout'        => $layout,
            'startDate'        => $startDate,
            'endDate'        => $endDate,
            'govHolidays'    => $govHolidays,
            'yearList'        => $yearList,
            'selectedYear'    => $startDate->year
        );

        return view('microfin.settings.holiday.viewHolidayCalender', $data);
    }

    public function storeHoliday(Request $req)
    {
        MfnHoliday::where('year', $req->year)->delete();
        $startDate = Carbon::parse('01-01-' . $req->year);
        foreach ($req->day as $key => $day) {
            if ($req->weeklyHolidayText[$key] == 'true' || $req->govHolidayText[$key] == 'true') {

                $descriptionJson['govHoliday'] = $req->descriptionText[$key];

                $holiday = new MfnHoliday;
                $holiday->year                    = $req->year;
                $holiday->date                    = $startDate;
                $holiday->dayNo                    = $day;
                $holiday->isWeeklyHoliday        = ($req->weeklyHolidayText[$key] == 'true') ? 1 : 0;
                $holiday->isGovHoliday            = ($req->govHolidayText[$key] == 'true') ? 1 : 0;
                $holiday->holidayTitle            = $req->finalTitle[$key];
                $holiday->holidayDescription    = json_encode($descriptionJson);
                $holiday->createdAt                = Carbon::now();
                $holiday->save();
            }
            $startDate->addDay();
        }

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data saved successfully.'
        );

        return response::json($data);
    }




    /*//////  Gov Holiday */

    public function viewGovHoliday()
    {
        $holidays = MfnGovHoliday::where('softDel', 0)->get();
        return view('microfin.settings.holiday.govHoliday.viewGovHoliday', ['holidays' => $holidays]);
    }

    public function addGovHoliday()
    {
        return view('microfin.settings.holiday.govHoliday.addGovHoliday');
    }

    public function storeGovHoliday(Request $req)
    {

        foreach ($req->title as $key => $title) {
            $govHoliday = new MfnGovHoliday;
            $govHoliday->title             = $title;
            $govHoliday->date             = $req->date[$key];
            $govHoliday->description    = $req->description[$key];
            $govHoliday->createdAt         = Carbon::now();
            $govHoliday->save();
        }

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data inserted successfully.'
        );

        return response::json($data);
    }

    public function updateGovHoliday(Request $req)
    {

        $rules = array(
            'title'         =>  'required',
            'date'           =>  'required'
        );

        $attributesNames = array(
            'title'         =>  'Title',
            'date'           =>  'Date'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } else {
            $govHoliday = MfnGovHoliday::find($req->holidayId);
            $govHoliday->title             = $req->title;
            $govHoliday->date             = $req->date;
            $govHoliday->description    = $req->description;
            $govHoliday->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data updated successfully.'
            );

            return response::json($data);
        }
    }

    public function deleteGovHoliday(Request $req)
    {
        $govHoliday = MfnGovHoliday::find($req->id);
        $govHoliday->softDel = 1;
        $govHoliday->save();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Holiday deleted successfully.'
        );

        return response::json($data);
    }

    /*//////  End Gov Holiday */






    /*///// Org/Branch/Samity Holiday  */

    public function viewOrgBranchSamityHoliday()
    {
        $holidays = MfnOrgBranchSamityHoliday::active()->get();
        return view("microfin.settings.holiday.orgBranchSamityHoliday.viewOrgBranchSamityHoliday", ['holidays' => $holidays]);
    }

    public function addOrgBranchSamityHoliday()
    {
        return view("microfin.settings.holiday.orgBranchSamityHoliday.addOrgBranchSamityHoliday");
    }

    public function storeOrgBranchSamityHoliday(Request $req)
    {

        $rules = array(
            'dateFrom'      =>  'required',
            'dateTo'           =>  'required',
            'holidayType'   =>  'required'
        );

        if ($req->applicableFor == "branch") {
            $rules = $rules + array(
                'branch'    =>    'required'
            );
        }

        if ($req->applicableFor == "samity") {
            $rules = $rules + array(
                'samity'    =>    'required'
            );
        }

        $attributesNames = array(
            'dateFrom'      =>  'Date From',
            'dateTo'           =>  'Date To',
            'holidayType'   =>  'Holiday Type',
            'branch'        =>    'Branch',
            'samity'        =>    'Samity'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }


        if ($req->applicableFor == "org") {
            $isOrg = 1;
            $orgIdFk = DB::table('hr_emp_org_info')->where('emp_id_fk', Auth::user()->emp_id_fk)->value('company_id_fk');
            $isBranch = 0;
            $branchIdFk = 0;
            $isSamity = 0;
            $samityIdFk = 0;
        } else if ($req->applicableFor == "branch") {
            $isOrg = 0;
            $orgIdFk = 0;
            $isBranch = 1;
            $branchIdFk = $req->branch;
            $isSamity = 0;
            $samityIdFk = 0;
        } else if ($req->applicableFor == "samity") {
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
        $orgHoliday->isOrgHoliday        = $isOrg;
        $orgHoliday->ogrIdFk            = $orgIdFk;
        $orgHoliday->isBranchHoliday    = $isBranch;
        $orgHoliday->branchIdFk            = $branchIdFk;
        $orgHoliday->isSamityHoliday    = $isSamity;
        $orgHoliday->samityIdFk            = $samityIdFk;
        $orgHoliday->dateFrom             = $dateFrom;
        $orgHoliday->dateTo             = $dateTo;
        $orgHoliday->holidayType         = $req->holidayType;
        $orgHoliday->description         = $req->description;
        $orgHoliday->createdAt             = Carbon::now();
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
        $holidayData = $this->MicroFinance->getLoanRescheduleForHoliday($dateFrom, $dateTo, $req->applicableFor, (int) $orgIdFk, (int) $branchIdFk, (int) $samityIdFk);
        //$holidayData=0;

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data saved successfully.',
            //'dateFrom1'      =>  $dateFrom,
            //'dateFrom'      =>  $dateFrom->toDateString(),
            //'dateTo'        =>  $dateTo->toDateString(),
            'applicableFor' =>  $req->applicableFor,
            'holidayData'   =>  $holidayData
        );

        return response::json($data);
    }


    public function updateOrgBranchSamityHoliday(Request $req)
    {

        $rules = array(
            'dateFrom'      =>  'required',
            'dateTo'           =>  'required',
            'holidayType'   =>  'required'
        );

        if ($req->applicableFor == "branch") {
            $rules = $rules + array(
                'branch'    =>    'required'
            );
        }

        if ($req->applicableFor == "samity") {
            $rules = $rules + array(
                'samity'    =>    'required'
            );
        }

        $attributesNames = array(
            'dateFrom'      =>  'Date From',
            'dateTo'           =>  'Date To',
            'holidayType'   =>  'Holiday Type',
            'branch'        =>    'Branch',
            'samity'        =>    'Samity'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }


        if ($req->applicableFor == "org") {
            $isOrg = 1;
            $orgIdFk = DB::table('hr_emp_org_info')->where('emp_id_fk', Auth::user()->emp_id_fk)->value('company_id_fk');
            $isBranch = 0;
            $branchIdFk = 0;
            $isSamity = 0;
            $samityIdFk = 0;
        } else if ($req->applicableFor == "branch") {
            $isOrg = 0;
            $orgIdFk = 0;
            $isBranch = 1;
            $branchIdFk = $req->branch;
            $isSamity = 0;
            $samityIdFk = 0;
        } else if ($req->applicableFor == "samity") {
            $isOrg = 0;
            $orgIdFk = 0;
            $isBranch = 0;
            $branchIdFk = 0;
            $isSamity = 1;
            $samityIdFk = $req->samity;
        }

        $dateFrom = Carbon::parse($req->dateFrom);
        $dateTo = Carbon::parse($req->dateTo);

        $orgHoliday = MfnOrgBranchSamityHoliday::find($req->id);
        $orgHoliday->isOrgHoliday        = $isOrg;
        $orgHoliday->ogrIdFk            = $orgIdFk;
        $orgHoliday->isBranchHoliday    = $isBranch;
        $orgHoliday->branchIdFk            = $branchIdFk;
        $orgHoliday->isSamityHoliday    = $isSamity;
        $orgHoliday->samityIdFk            = $samityIdFk;
        $orgHoliday->dateFrom             = $dateFrom;
        $orgHoliday->dateTo             = $dateTo;
        $orgHoliday->holidayType         = $req->holidayType;
        $orgHoliday->description         = $req->description;
        $orgHoliday->save();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data updated successfully.'
        );

        return response::json($data);
    }

    public function deleteOrgBranchSamityHoliday(Request $req)
    {
        $orgHoliday = MfnOrgBranchSamityHoliday::find($req->id);
        $orgHoliday->softDel = 1;
        $orgHoliday->save();

        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Data deleted successfully.'
        );

        return response::json($data);
    }



    /*/////  End Org/Branch/Samity Holiday  */
}
