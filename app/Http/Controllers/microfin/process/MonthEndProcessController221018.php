<?php

    namespace App\Http\Controllers\microfin\process;

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
    use App\microfin\savings\MfnSavingsAccount;
    use App\microfin\member\MfnMemberInformation;
    use App\microfin\savings\MfnSavingsDeposit;
    use App\microfin\savings\MfnSavingsWithdraw;
    use App\microfin\samity\MfnSamity;
    use App\microfin\process\MfnDayEnd;
    use App\microfin\process\MfnMonthEnd;
    use App\microfin\process\MfnMonthEndProcessTotalMembers;
    use App\microfin\process\MfnMonthEndProcessMembers;
    use App\microfin\process\MfnMonthEndProcessSavings;
    use Auth;
    use App\Http\Controllers\microfin\process\MonthEndStoreInfo;
    use App\Http\Controllers\microfin\process\MonthEndStoreInfoFromOpening;
    use App\globalFunction\MfnHalfYearlyEmploymentInfo;

    class MonthEndProcessController extends Controller {

        private $TCN;
        
        public function __construct() {

            $this->TCN = array(
                array('SL#', 70),
                array('MONTH', 0),
                array('BRANCH', 0),
                array('ACTION', 80)
            );
        }

        public function index(Request $req) {

            $userBranchId = Auth::user()->branchId;

            if (isset($req->filBranch)) {
                $targetBranchId = $req->filBranch;
            }
            else{
                $targetBranchId = $userBranchId;
            }

            $yearArray = array();

            $dayEndMinYear = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->min('date');
            $dayEndMinYear = Carbon::parse($dayEndMinYear)->format('Y');

            $dayEndMaxYear = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->max('date');
            $dayEndMaxYear = Carbon::parse($dayEndMaxYear)->format('Y');

            while ($dayEndMaxYear>=$dayEndMinYear) {
                $yearArray = $yearArray + [$dayEndMaxYear=>$dayEndMaxYear];
                $dayEndMaxYear--;
            }      

            $monthEnds = MfnMonthEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1);

            if ($req->filYear!='') {
                $yearStartDate = Carbon::parse('01-01-'.$req->filYear);
                $yearEndDate = Carbon::parse('31-12-'.$req->filYear);
                $monthEnds = $monthEnds->where('date','>=',$yearStartDate)->where('date','<=',$yearEndDate);
            }
            $monthEnds = $monthEnds->orderBy('date','desc')->paginate(12);

            $branchName = DB::table('gnr_branch')->where('id',$targetBranchId)->value('name');

            $branchList = DB::table('gnr_branch');

            if ($userBranchId!=1) {
                $branchList = $branchList->where('id',$userBranchId);
            }

            $branchList = $branchList
                       ->orderBy('branchCode')
                       ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                       ->pluck('nameWithCode', 'id')
                       ->toArray(); 

            $isFirstRequest = isset($req->filBranch) ? 0:1;

            $maxMonthEndId = MfnMonthEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->max('id');
            
            $TCN = $this->TCN;
            $damageData = array(
                'TCN'             =>  $TCN,
                'monthEnds'       =>  $monthEnds,
                'branchName'      =>  $branchName,
                'yearArray'       =>  $yearArray,
                'lastYear'        =>  isset($yearArray[0]) ? $yearArray[0]:'',
                'branchList'      =>  $branchList,
                'isFirstRequest'  =>  $isFirstRequest,
                'userBranchId'    =>  $userBranchId,
                'maxMonthEndId'   =>  $maxMonthEndId
            );

            return view('microfin.process.monthEndProcess.viewMonthEndProcess',$damageData);
        }

        public function storeMonthEnd(Request $req){

            $userBranchId = Auth::user()->branchId;

            if (isset($req->branchId)) {
                $targetBranchId = $req->branchId;
            }
            else{
                $targetBranchId = $userBranchId;
            }

            //$userBranchId = Auth::user()->branchId;

            $lastDayEndDate = MfnDayEnd::active()->where('branchIdFk',$targetBranchId)->where('isLocked',1)->max('date');
            $monthLastDate = Carbon::parse($lastDayEndDate)->endOfMonth();
            $monthEndDate = $monthLastDate->copy();
            $monthFirstDate = $monthLastDate->copy()->startOfMonth();
            
            $isMonthLastDayHoliday = 1;            

            while ($isMonthLastDayHoliday==1) {
                $monthLastDateString = $monthLastDate->copy()->format('Y-m-d');
                if ($this->isHoliday($monthLastDateString,$targetBranchId)) {
                    $monthLastDate->subDay();
                }
                else{
                    $isMonthLastDayHoliday = 0;
                }
            }

            // All Day End Executed
            if ($lastDayEndDate==$monthLastDate->format('Y-m-d')) {
                // Execute month end
                $monthEndDateString = $monthEndDate->copy()->format('Y-m-d');
                $isMonthEndExits = (int) MfnMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$monthEndDateString)->value('id');
                if ($isMonthEndExits>0) {
                    $monthEnd = MfnMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$monthEndDateString)->first();
                }
                else{
                    $monthEnd = new MfnMonthEnd;
                }  

                $info = $this->getThisMonthTransactionInfo($targetBranchId,$monthEndDateString);

                $monthEnd->date                     = $monthEndDate;
                $monthEnd->branchIdFk               = $targetBranchId;
                $monthEnd->executionDate            = Carbon::toDay();
                $monthEnd->isLocked                 = 1;
                $monthEnd->collectionAmount         = $info['collectionAmount'];
                $monthEnd->collectionDueAmount      = $info['collectionDueAmount'];
                $monthEnd->advanceAmount            = $info['advanceAmount'];
                $monthEnd->savingsAmount            = $info['savingsAmount'];
                $monthEnd->withdrawAmount           = $info['withdrawAmount'];
                $monthEnd->createdAt                = Carbon::now();

                $monthEndStartDate = $monthFirstDate->copy()->format('Y-m-d');

                $hasMonthEnd = (int) DB::table('mfn_month_end')->where('branchIdFk',$targetBranchId)->where('date','<',$monthEndDateString)->value('id');

                if ($hasMonthEnd>0) {
                    $monthEndInfo = new MonthEndStoreInfo($targetBranchId,$monthEndStartDate,$monthEndDateString);
                    $monthEndInfo->saveData();
                }
                else{
                    $monthEndInfo = new MonthEndStoreInfoFromOpening($targetBranchId,$monthEndStartDate,$monthEndDateString);
                    $monthEndInfo->saveData();
                }                

                // if it is june or december then call a function
                if ($monthEndDate->month==6 || $monthEndDate->month==12) {
                    MfnHalfYearlyEmploymentInfo::halfYearlyEmploymentInfo($targetBranchId, $monthEndDateString);
                }

                $monthEnd->save();
                
                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Month End executed successfully.'
                );
            }

            // All Day End not completed of this month
            else{

                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Please execute all day ends first.'
                );
            }   

            return response::json($data);
        }

        /**
         * [isHoliday description]
         * @param  [date string]  $date [description]
         * @return boolean       [if it is holiday then return true or return false]
         */
        public function isHoliday($date,$targetBranchId){
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

        /**
         * [getTotalCollectionOfThisMonth description]
         * @param  [string] $monthEndDate [in 'Y-m-d' format, it should be the last date of this month]
         * @return [float]               [collection amount of this month]
         */
        public function getThisMonthTransactionInfo($targetBranchId,$monthEndDate){
            
            $monthFirstDate = Carbon::parse($monthEndDate)->startOfMonth()->format('Y-m-d');
            $userBranchId = $targetBranchId;

            // COLLECTION AMOUNT
            $collectionAmount = DB::table('mfn_loan_collection')->where('softDel',0)->where('branchIdFk',$userBranchId)->where('collectionDate','>=',$monthFirstDate)->where('collectionDate','<=',$monthEndDate)->sum('amount');
            
            // schules
            $schedule = DB::table('mfn_loan_schedule as t1')
                                ->join('mfn_loan as t2','t1.loanIdFk','t2.id')
                                ->where('t1.softDel',0)
                                ->where('t1.status',1)
                                ->where([['t1.scheduleDate','>=',$monthFirstDate],['t1.scheduleDate','<=',$monthEndDate]])
                                ->where('t2.branchIdFk',$userBranchId);
            $collectionDueAmount = clone($schedule);
            $collectionDueAmount = $collectionDueAmount->where('t1.isCompleted',0)->sum('t1.installmentAmount');
            $partiallyPaidAmount = clone($schedule);
            $partiallyPaidAmount = $partiallyPaidAmount->where('t1.isPartiallyPaid',1)->sum('partiallyPaidAmount');

            // DUE AMOUNT
            $collectionDueAmount = $collectionDueAmount - $partiallyPaidAmount;

            // advance schedule
            $advanceSchedule = DB::table('mfn_loan_schedule as t1')
                                ->join('mfn_loan as t2','t1.loanIdFk','t2.id')
                                ->where('t1.softDel',0)
                                ->where('t1.status',1)
                                ->where('t1.scheduleDate','>',$monthEndDate)
                                ->where('t2.branchIdFk',$userBranchId);
            $advancePaidAmount = clone($advanceSchedule);
            $advancePaidAmount = $advancePaidAmount->where('t1.isCompleted',1)->sum('t1.installmentAmount');
            $advancePartiallyPaidAmount = clone($advanceSchedule);
            $advancePartiallyPaidAmount = $advancePartiallyPaidAmount->where('t1.isPartiallyPaid',1)->sum('partiallyPaidAmount');

            // ADVANCE AMOUNT
            $advanceAmount = $advancePaidAmount + $advancePartiallyPaidAmount;
            
            ////////   savings begins

            // SAVINGS DEPOSIT AMOUNT
            $savingsAmount = DB::table('mfn_savings_deposit')->where('softDel',0)->where('branchIdFk',$userBranchId)->where('depositDate','>=',$monthFirstDate)->where('depositDate','<=',$monthEndDate)->sum('amount');

            // SAVINGS WITHDRAW AMOUNT
            $withdrawAmount = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('branchIdFk',$userBranchId)->where('withdrawDate','>=',$monthFirstDate)->where('withdrawDate','<=',$monthEndDate)->sum('amount');

            $data = array(
                'collectionAmount'      => $collectionAmount,
                'collectionDueAmount'   => $collectionDueAmount,
                'advanceAmount'         => $advanceAmount,
                'savingsAmount'         => $savingsAmount,
                'withdrawAmount'        => $withdrawAmount
            );

            return $data;

        }

         public function deleteMonthEnd(Request $req){
            
            $monthEnd = MfnMonthEnd::find($req->id);

            // check is there any day end after the motn end date
            $isDayEndExits = MfnDayEnd::where('branchIdFk',$monthEnd->branchIdFk)->where('date','>',$monthEnd->date)->where('isLocked',1)->value('id');
            if ($isDayEndExits>0) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Delete day end first.'
                );

                return response::json($data);
            }

            DB::table('mfn_month_end_process_members')->where('branchIdFk',$monthEnd->branchIdFk)->where('date',$monthEnd->date)->delete();
            DB::table('mfn_month_end_process_total_members')->where('branchIdFk',$monthEnd->branchIdFk)->where('date',$monthEnd->date)->delete();
            DB::table('mfn_month_end_process_savings')->where('branchIdFk',$monthEnd->branchIdFk)->where('date',$monthEnd->date)->delete();

            $monthEnd->delete();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Month End deleted successfully.'
            );

            return response::json($data);
        }

    }

        