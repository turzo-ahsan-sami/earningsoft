<?php
namespace App\Http\Controllers\pos\process;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosMonthEnd;
use App\pos\PosDayEnd;
use App\pos\PosSales;
use App\pos\PosCollection;
use App\pos\PosSalesDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\microfin\MicroFinance;

class PosProcessMonthEndController extends Controller
{
 		protected $MicroFinance;

 	public function __construct() {

            $this->MicroFinance = New MicroFinance;
        }

    public function index(Request $req){

	     $userBranchId = Auth::user()->branchId;

            if (isset($req->filBranch)) {
                $targetBranchId = $req->filBranch;
            }
            else{
                $targetBranchId = $userBranchId;
            }

            $yearArray = array();

            $dayEndMinYear = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1)->min('branchDate');
            $dayEndMinYear = Carbon::parse($dayEndMinYear)->format('Y');

            $dayEndMaxYear = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1)->max('branchDate');
            $dayEndMaxYear = Carbon::parse($dayEndMaxYear)->format('Y');

            while ($dayEndMaxYear>=$dayEndMinYear) {
                $yearArray = $yearArray + [$dayEndMaxYear=>$dayEndMaxYear];
                $dayEndMaxYear--;
            }      

            $monthEnds = PosMonthEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1);

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
            
            $damageData = array(
                'monthEnds'       =>  $monthEnds,
                'branchName'      =>  $branchName,
                'yearArray'       =>  $yearArray,
                'lastYear'        =>  isset($yearArray[0]) ? $yearArray[0]:'',
                'branchList'      =>  $branchList,
                'isFirstRequest'  =>  $isFirstRequest,
                'userBranchId'    =>  $userBranchId,
            );
			
		return view('pos/process/posMonthEndList',$damageData);
    }


    public function storeMonthEnd(Request $req){

            $targetBranchId = $req->branchId;

            //$userBranchId = Auth::user()->branchId;

            $lastDayEndDate = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1)->max('branchDate');
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
                $isMonthEndExits = (int) PosMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$monthEndDateString)->value('id');
                if ($isMonthEndExits>0) {
                    $monthEnd = PosMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$monthEndDateString)->first();
                }
                else{
                    $monthEnd = new PosMonthEnd;
                }
                  
                $monthEnd->date                     = $monthEndDate;
                $monthEnd->branchIdFk               = $targetBranchId;
                $monthEnd->executedByEmpIdFk        = Auth::user()->emp_id_fk;
                $monthEnd->executionDate            = Carbon::toDay();
                $monthEnd->isLocked                 = 1;
                $monthEnd->createdDate              = Carbon::now();

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


        public function deletePosMonthEnd(Request $req){
            
            $monthEnd = PosMonthEnd::find($req->id);

            // check is there any day end after the month end date
            $isDayEndExits = PosDayEnd::where('branchIdFk',$monthEnd->branchIdFk)->where('branchDate','>',$monthEnd->date)->where('isLocked',1)->value('id');
            if ($isDayEndExits>0) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Delete day end first.'
                );

                return response::json($data);
            }

            

            $monthEnd->delete();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Month End deleted successfully.'
            );

            return response::json($data);
        }


}