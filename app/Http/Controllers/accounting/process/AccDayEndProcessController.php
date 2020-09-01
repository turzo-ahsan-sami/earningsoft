<?php

namespace App\Http\Controllers\accounting\process;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\microfin\MfnMemberType;
use App\Traits\CreateForm;
// use App\microfin\process\MfnDayEnd;
// // use App\microfin\process\MfnMonthEnd;
use Auth;

use App\accounting\process\AccDayEnd;
use App\accounting\process\AccMonthEnd;
use App\accounting\process\AccYearEnd;

use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\accounting\process\AccYearEndProcessController;
use App\Service\Service;

class AccDayEndProcessController extends Controller {

    protected $MicroFinance;
    protected $YearEnd;
    protected $Service;

    public function __construct() {

        $this->MicroFinance = New MicroFinance;
        $this->YearEnd = New AccYearEndProcessController;
        $this->Service = New Service;
    }

    public function accDayEndProcess(Request $request) {

        $userBranch = Auth::user()->branch;
        $userCompanyId = Auth::user()->company_id_fk;

        $monthsOption = $this->MicroFinance->getMonthsOption();
        $yearsOption = array();

        if (isset($request->filBranch)) {
            $targetBranchId = $request->filBranch;
        }
        else{
            $targetBranchId = $userBranch->id;
        }


        // $dayEndMinYear = AccDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isDayEnd',1)->min('date');
        // $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

        $dayEndMinYear = Carbon::parse(DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate'))->format('Y');
        // $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

        $dayEndMaxYear = AccDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isDayEnd',1)->max('date');
        $dayEndMaxYear = (int) Carbon::parse($dayEndMaxYear)->format('Y');

        $yearsOption = array_combine(range($dayEndMaxYear, $dayEndMinYear), range($dayEndMaxYear, $dayEndMinYear));

        // $branchList = DB::table('gnr_branch');
         $branchList = DB::table('gnr_branch')->where('companyId',$userCompanyId);

        if ($userBranch->branchCode!=0) {
            $branchList = $branchList->where('id',$userBranch->id);
        }

        $branchOption = $branchList
                   ->orderBy('branchCode')
                   ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                   ->pluck('nameWithCode', 'id')
                   ->toArray();

        $filteringArr = array(
            'monthsOption'  =>  $monthsOption,
            'yearsOption'   =>  $yearsOption,
            'userBranchCode'  =>  $userBranch->branchCode,
            'branchOption'  =>  $branchOption,
        );

        // test query///
        // $branchList = $branchList->pluck('id')->toArray();
        // $monthEndDatas = DB::table('acc_month_end_balance')->get();
        // $monthsArray = $monthEndDatas->unique('monthEndDate')->pluck('monthEndDate')->toArray();
        //
        // foreach ($branchList as $key => $branch) {
        //     foreach ($monthsArray as $key => $month) {
        //
        //         $monthDebit = $monthEndDatas->where('branchId', $branch)->where('monthEndDate', $month)->sum('debitAmount');
        //         $monthCredit = $monthEndDatas->where('branchId', $branch)->where('monthEndDate', $month)->sum('creditAmount');
        //
        //         if (round($monthDebit) != round($monthCredit)) {
        //             $array[] = array(
        //                 'debit'     => $monthDebit,
        //                 'credit'    => $monthCredit,
        //                 'diff'      => number_format($monthDebit - $monthCredit, 2),
        //                 'branch'    => $branch,
        //                 'month'     => $month,
        //             );
        //         }
        //
        //     }
        // }
        // dd($array);

        // test query///

        return view('accounting.process.dayEndProcess.filteringPart',$filteringArr);
        // return "আপনাদের সকলের অবগতির জন্য জানানো যাচ্ছে যে, 2018-19 অর্থবছর সমাপ্তি কিছু কাজের প্রয়োজনে পরবর্তী নির্দেশ না দেয়া পর্যন্ত সকল শাখার ক্ষুদ্রঋণ কার্যক্রমের মাইক্রোফিনপ্লাস সফট্ওয়্যারের এআইএস এর সকল কাজ বন্ধ রাখার নির্দেশ দেয়া হলো।
        // অতএব, পরবর্তী নির্দেশনা না দেয়া পর্যন্ত এআইএস সফটওয়্যারে কেউ কোন কাজ করবেন না। বিষয়টি অতি গুরুত্বের সাথে গ্রহণ করার জন্য অনুরোধ করা হলো।";
    }


    public function loadAccDayEndProcess(Request $request){

        $userBranch = Auth::user()->branch;


        if($userBranch->code==0){
            $targetBranchId = $request->filBranch;
        }else{
            $targetBranchId = $userBranch->id;
        }


        // if (isset($request->filBranch)) {
        //     $targetBranchId = $request->filBranch;
        // }
        // else{
        //     $targetBranchId = $userBranchId;
        // }


        $dayEndInfomation = AccDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isDayEnd',1);

        // if ($request->checkFirstLoad==0) {

        //     // $dayEndInfomation = $dayEndInfomation->orderBy('date','desc')->paginate(2);

        // }else

        if($request->checkFirstLoad==1){

            if ($request->filMonth==-1) {
                $firstMonth = '01';
                $lastMonth = '12';
            }
            else{
                $firstMonth = str_pad($request->filMonth, 2, '0', STR_PAD_LEFT);
                $lastMonth = $firstMonth;
            }

            $startDate = Carbon::parse('01-'.$firstMonth.'-'.$request->filYear);
            $endDate = Carbon::parse('31-'.$lastMonth.'-'.$request->filYear);
            $dayEndInfomation = $dayEndInfomation->where('date','>=',$startDate)->where('date','<=',$endDate);

        }

        $dayEndInfomation = $dayEndInfomation->orderBy('date','desc')->paginate(60);

        $currentDate=DB::table('acc_day_end')->where('branchIdFk', $targetBranchId)->where('isDayEnd', 0)->value('date');
        if ($currentDate==null) {
            $currentDate=DB::table('gnr_branch')->where('id', $targetBranchId)->value('softwareStartDate');
        }
        $currentDate=Carbon::parse($currentDate)->format('d-m-Y');
        // $currentDate=Carbon::parse($currentDate)->format('l, F d, Y');


        // if ($request->filMonth==-1) {
        //     $firstMonth = '01';
        //     $lastMonth = '12';
        // }
        // else{
        //     $firstMonth = str_pad($request->filMonth, 2, '0', STR_PAD_LEFT);
        //     $lastMonth = $firstMonth;
        // }

        // $yearFirstDate = Carbon::parse('01-'.$firstMonth.'-'.$request->filYear);
        // $yearLastDate = Carbon::parse('31-'.$lastMonth.'-'.$request->filYear);
        // $dayEndInfomation = $dayEndInfomation->where('date','>=',$yearFirstDate)->where('date','<=',$yearLastDate);

        $activeDeleteDayEndId = (int) AccDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isDayEnd',1)->orderBy('date','desc')->value('id');
        // dd($activeDeleteDayEndId);

        // $dayEndInfomation = $dayEndInfomation->orderBy('date','desc')->paginate(2);



        $branchName=DB::table('gnr_branch')->where('id',$targetBranchId)->value('name');


        $loadingArr = array(
                            'dayEndInfomation'      => $dayEndInfomation,
                            'activeDeleteDayEndId'  => $activeDeleteDayEndId,
                            'branchName'            =>  $branchName,
                            'targetBranchId'        =>  $targetBranchId,
                            'userBranchId'        =>  $userBranch->id,
                            'currentDate'        =>  $currentDate,
                            // 'targetMonth' => $request->filMonth,
                            // 'targetYear' => $request->filYear,
                        );
                        // dd($loadingArr);

        return view('accounting.process.dayEndProcess.dayEndProcess',$loadingArr);

    }


    public function addAccDayEndProcessItem(Request $request){

        $targetBranchId = $request->branchId;
        $branchInfo = DB::table('gnr_branch')->where('id', $targetBranchId)->first();

        //$userBranchId = Auth::user()->branchId;
        //$orgId = Auth::user()->company_id_fk;

        $softwareDate = AccDayEnd::active()->where('branchIdFk',$targetBranchId)->max('date');
        // dd($softwareDate);
        $softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('aisStartDate');

        if ($softwareDate=='' || $softwareDate==null) {
            $softwareDate = $softwareStartDate;
        }

        $softwareDate = Carbon::parse($softwareDate);
        $softwareDateFormat = $softwareDate->format('Y-m-d');
        // dd(Auth::user());

        // this year start date
		$firstDayofThisFiYr =DB::table('gnr_fiscal_year')->where('companyId', Auth::user()->company_id_fk)->where('fyStartDate','<=', $softwareDateFormat)->where('fyEndDate','>=', $softwareDateFormat)->value('fyStartDate');
        // dd($firstDayofThisFiYr);

        // check month end is executed or not
        // if it is the first month then skip the month end cheking
        $softwareStartDate = Carbon::parse(DB::table('gnr_branch')->where('id',$targetBranchId)->value('aisStartDate'));
        if ($softwareStartDate->copy()->format('Y-m')==$softwareDate->copy()->format('Y-m')) {
            // this date ar in the same month (e.g. it is the starting month)
            // dd($softwareDate->copy()->format('Y-m'));
        }
        else{
            // first month passed, now check the month end closed or not
            $lastMonthLastDate = $softwareDate->copy()->startOfMonth()->subDay()->format('Y-m-d');
            $monthEndExits = AccMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$lastMonthLastDate)->first();
            // dd(2);
            if (!$monthEndExits) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Please Executed Month End (<b><em><u>'.Carbon::parse($lastMonthLastDate)->format('F Y').'<u></b></em>) First.'
                );

                return response::json($data);
            }

        }

        //check year end is executed or not
        // dd($softwareDateFormat, $firstDayofThisFiYr);
        if($softwareDateFormat == $firstDayofThisFiYr){

            if ($softwareStartDate->copy()->format('Y-m-d')==$softwareDate->copy()->format('Y-m-d')) {
                // if branchdate is the software date then skip year end checking
            }
            else {
                $yearEndExists = $this->YearEnd->checkYearEndToExecuteDayEnd($softwareDateFormat, $targetBranchId, $branchInfo->companyId);

                if($yearEndExists == false){
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Please Execute Previous Fiscal Year End First.'
                    );

                    return response::json($data);

                }
            }

        }
        $unauthorizedVoucherCounter=DB::table('acc_voucher')->where('vGenerateType', 2)->where('branchId', $targetBranchId)->where('voucherDate', $softwareDate)->where('authBy', 0)->count('id');

        if ($unauthorizedVoucherCounter>=1) {

            $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Please Authorize All Pending Vouchers of <em><u>'.Carbon::parse($softwareDate)->format('l, F d, Y').'</em></u>'
                );

                return response::json($data);
        }

        $isDayExits = (int) AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDate)->value('id');
        if ($isDayExits>0) {
            $dayEnd = AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDate)->first();
        }
        else{
            $dayEnd = new AccDayEnd;
        }

        $dayEnd->date               = $softwareDate;
        $dayEnd->branchIdFk         = $targetBranchId;
        $dayEnd->isDayEnd           = 1;
        $dayEnd->executedByEmpIdFk  = Auth::user()->emp_id_fk;
        $dayEnd->createdAt          = Carbon::now();
        $dayEnd->save();

        // Insert Next working day. Holiday will not be the next working Day.

        $nextDay = '';
        $isNextDayHoliday = 1;
        $nextDay = $softwareDate->copy();

        while ($isNextDayHoliday==1) {
            $nextDay = $nextDay->addDay();
            $nextDateString = $nextDay->copy()->format('Y-m-d');
            $isNextDayHoliday = MicroFinance::isHoliday($nextDateString,$targetBranchId);
        }

        $isDayExits = (int) AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->value('id');
        if ($isDayExits>0) {
            $nextDayEnd = AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->first();
        }
        else{
            $nextDayEnd = new AccDayEnd;
        }

        $nextDayEnd->date       = $nextDay;
        $nextDayEnd->branchIdFk = $targetBranchId;
        $nextDayEnd->isDayEnd   = 0;
        $nextDayEnd->createdAt  = Carbon::now();
        $nextDayEnd->save();


        $currentDate=Carbon::parse($nextDay)->format('l, F d, Y');


        $data = array(
            'currentDate' =>  $currentDate,
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Day End Executed Successfully.<br/><b><em>Date: <u>'.Carbon::parse($softwareDate)->format('l, F d, Y').'</u></b></em>'
        );

        // $data = array(
        //     'dayEnd' =>$dayEnd,
        //     'nextDayEnd' =>$nextDayEnd,
        // );

        return response::json($data);
    }


    public function deleteAccDayEndItem(Request $request){
        // dd($request->all());
        $dayEnd = AccDayEnd::find($request->id);

        // first check month end is deleted or not
        $dayEndMonthLastDate = Carbon::parse($dayEnd->date)->endOfMonth()->format('Y-m-d');
        $isMonthEndExits = (int) AccMonthEnd::active()->where('branchIdFk',$dayEnd->branchIdFk)->where('date',$dayEndMonthLastDate)->value('id');
        if ($isMonthEndExits>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Delete Month End (<b><em><u>'.Carbon::parse($dayEndMonthLastDate)->format('F Y').'</u></b></em>) First.'
            );

            return response::json($data);
        }

        $dayEnd->isDayEnd           = 0;
        $dayEnd->executedByEmpIdFk  = 0;
        $dayEnd->save();

        $deletedDate=DB::table('acc_day_end')->where('branchIdFk',$dayEnd->branchIdFk)->where('date','>', $dayEnd->date)->delete();

        $currentDate=Carbon::parse($dayEnd->date)->format('l, F d, Y');


        // $data = array(
        //     'dayEnd' =>  $dayEnd,
        //     'deletedDate' =>  $deletedDate,
        // );


        $data = array(
            'currentDate' =>  $currentDate,
            'responseTitle' =>  'Success!',
            'responseText'  =>  'Day End Deleted Successfully.<br/><b><em>Date: <u>'.$currentDate.'</u></b></em>'
        );

        return response::json($data);
    }



    // =======================================Ajax=======================================

    public function getYearsOption(Request $request){

        // $dayEndMinYear = AccDayEnd::active()->where('branchIdFk',$request->branchId)->where('isDayEnd',1)->min('date');
        // $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

        $dayEndMinYear = Carbon::parse(DB::table('gnr_branch')->where('id',$request->branchId)->value('softwareStartDate'))->format('Y');
        // $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

        $dayEndMaxYear = AccDayEnd::active()->where('branchIdFk',$request->branchId)->where('isDayEnd',1)->max('date');
        $dayEndMaxYear = (int) Carbon::parse($dayEndMaxYear)->format('Y');

        $yearsOption = array_combine(range($dayEndMaxYear, $dayEndMinYear), range($dayEndMaxYear, $dayEndMinYear));

        return response::json($yearsOption);
    }

    public function manualDayEnd(){

        $branchList = DB::table('gnr_branch')
                    ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS branchName"), 'id')
                    ->pluck('branchName','id')
                    ->toArray();

        $data = array(
            'branchList'    =>  $branchList
        );
        // dd($data);

        return view('accounting.process.dayEndProcess.massExecute', $data);
    }


    public function executeDayEnd(Request $req) {

        $branchIdArr = $req->filBranch;

        foreach ($branchIdArr as $key => $branchId) {

            $targetBranchId = $branchId;
            $branchInfo = DB::table('gnr_branch')->where('id', $targetBranchId)->first();

            $softwareDate = AccDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isDayEnd',0)->min('date');
            $softwareStartDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('aisStartDate');

            if ($softwareDate=='' || $softwareDate==null) {
                $softwareDate = $softwareStartDate;
            }

            $softwareDate = Carbon::parse($softwareDate);
            $softwareDateFormat = $softwareDate->format('Y-m-d');
            // dd($softwareDateFormat);

            // this year start date
    		$firstDayofThisFiYr =DB::table('gnr_fiscal_year')->where('fyStartDate','<=', $softwareDateFormat)->where('fyEndDate','>=', $softwareDateFormat)->value('fyStartDate');
            // dd($firstDayofThisFiYr);

            // check month end is executed or not
            // if it is the first month then skip the month end cheking
            $softwareStartDate = Carbon::parse(DB::table('gnr_branch')->where('id',$targetBranchId)->value('aisStartDate'));
            if ($softwareStartDate->copy()->format('Y-m')==$softwareDate->copy()->format('Y-m')) {
                // this date ar in the same month (e.g. it is the starting month)
                // dd($softwareDate->copy()->format('Y-m'));
            }
            else{
                // first month passed, now check the month end closed or not
                $lastMonthLastDate = $softwareDate->copy()->startOfMonth()->subDay()->format('Y-m-d');
                $monthEndExits = (int) AccMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$lastMonthLastDate)->value('id');
                // dd(2);
                if ($monthEndExits < 1) {
                    $data[] = array(
                        'branchId'      => $branchId,
                        'date'          => $softwareDate->format('d M, Y'),
                        'responseTitle' => 'Warning!',
                        'responseText'  => 'Please Execute Month End of <b><em><u>'.Carbon::parse($lastMonthLastDate)->format('F Y').'<u></b></em> First.'
                    );

                    continue;
                }

            }

            //check year end is executed or not
            if($softwareDateFormat == $firstDayofThisFiYr){

                if ($softwareStartDate->copy()->format('Y-m-d')==$softwareDate->copy()->format('Y-m-d')) {
                    // if branchdate is the software date then skip year end checking
                }
                else {
                    $yearEndExists = $this->YearEnd->checkYearEndToExecuteDayEnd($softwareDateFormat, $targetBranchId, $branchInfo->companyId);

                    if($yearEndExists == false){

                        $data[] = array(
                            'branchId'      => $branchId,
                            'date'          => $softwareDate->format('d M, Y'),
                            'responseTitle' => 'Warning!',
                            'responseText'  => 'Please Execute Previous Fiscal Year End First.'
                        );

                        continue;

                    }
                }

            }
            $unauthorizedVoucherCounter=DB::table('acc_voucher')->where('vGenerateType', 2)->where('branchId', $targetBranchId)->where('voucherDate', $softwareDate)->where('authBy', 0)->count('id');

            if ($unauthorizedVoucherCounter>=1) {

                $data[] = array(
                    'branchId'      => $branchId,
                    'date'          => $softwareDate->format('d M, Y'),
                    'responseTitle' => 'Warning!',
                    'responseText'  => 'Please Authorize All Pending Vouchers of <b><em><u>'.Carbon::parse($softwareDate)->format('l, F d, Y').'</em></u></b>.'
                );

                continue;
            }

            $isDayExits = (int) AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDate)->value('id');
            if ($isDayExits>0) {
                $dayEnd = AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$softwareDate)->first();
            }
            else{
                $dayEnd = new AccDayEnd;
            }

            $dayEnd->date               = $softwareDate;
            $dayEnd->branchIdFk         = $targetBranchId;
            $dayEnd->isDayEnd           = 1;
            $dayEnd->executedByEmpIdFk  = Auth::user()->emp_id_fk;
            $dayEnd->createdAt          = Carbon::now();
            $dayEnd->save();

            // Insert Next working day. Holiday will not be the next working Day.

            $nextDay = '';
            $isNextDayHoliday = 1;
            $nextDay = $softwareDate->copy();

            while ($isNextDayHoliday==1) {
                $nextDay = $nextDay->addDay();
                $nextDateString = $nextDay->copy()->format('Y-m-d');
                $isNextDayHoliday = MicroFinance::isHoliday($nextDateString,$targetBranchId);
            }

            $isDayExits = (int) AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->value('id');
            if ($isDayExits>0) {
                $nextDayEnd = AccDayEnd::where('branchIdFk',$targetBranchId)->where('date',$nextDay)->first();
            }
            else{
                $nextDayEnd = new AccDayEnd;
            }

            $nextDayEnd->date       = $nextDay;
            $nextDayEnd->branchIdFk = $targetBranchId;
            $nextDayEnd->isDayEnd   = 0;
            $nextDayEnd->createdAt  = Carbon::now();
            $nextDayEnd->save();

            $currentDate=Carbon::parse($nextDay)->format('l, F d, Y');


            $data[] = array(
                'branchId'      => $branchId,
                'date'          => $softwareDate->format('d M, Y'),
                'responseTitle' => 'Success!',
                'responseText'  => 'Day End Executed Successfully. <b><em>Date: <u>'.Carbon::parse($softwareDate)->format('l, F d, Y').'</u></b></em>'
            );



        }
        // dd($data);

        return view('accounting.process.dayEndProcess.massExecuteReport', ['dataArr' => $data]);

    }

}
