<?php
namespace App\Http\Controllers\pos\process;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosDayEnd;
use App\pos\PosMonthEnd;
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
use App\Http\Controllers\accounting\Accounting;

class PosProcessDayEndController extends Controller
{
        protected $MicroFinance;
 		protected $Accounting;

 	public function __construct() {

            $this->MicroFinance = New MicroFinance;
            $this->Accounting   = New Accounting;
        }

    public function index(Request $req){

	    $user = Auth::user();
		$userBranchId = Auth::user()->branchId;
		$logedUserName = $user->name;
		$monthArray = $this->MicroFinance->getMonthsOption();

        
            $yearArray = array();

            if (isset($req->filBranch)) {
                $targetBranchId = $req->filBranch;
            }
            else{
                $targetBranchId = $userBranchId;
            }

            $dayEndMinYear = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1)->min('branchDate');
            $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

            $dayEndMaxYear = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1)->max('branchDate');
            $dayEndMaxYear = (int) Carbon::parse($dayEndMaxYear)->format('Y');

            while ($dayEndMaxYear>=$dayEndMinYear) {
                $yearArray = $yearArray + [$dayEndMaxYear=>$dayEndMaxYear];
                $dayEndMaxYear--;
            }

            $softwareDate = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',0)->min('branchDate');

            if ($softwareDate=='' || $softwareDate==null) {
                $softwareDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');
            }

           $dayEnds = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1);

            if ($req->filMonth!='') {
                $firstMonth = str_pad($req->filMonth, 2, '0', STR_PAD_LEFT);
                $lastMonth = $firstMonth;
            }
            else{
                $firstMonth = '01';
                $lastMonth = '12';
            }

            if ($req->filYear!='') {
                $yearFirstDate = Carbon::parse('01-'.$firstMonth.'-'.$req->filYear);
                $yearLastDate = Carbon::parse('31-'.$lastMonth.'-'.$req->filYear);
                $dayEnds = $dayEnds->where('branchDate','>=',$yearFirstDate)->where('branchDate','<=',$yearLastDate);
            }
            elseif(count($yearArray)>0){
                $yearFirstDate = Carbon::parse('01-'.$firstMonth.'-'.max($yearArray));
                $yearLastDate = Carbon::parse('31-'.$lastMonth.'-'.max($yearArray));
                $dayEnds = $dayEnds->where('branchDate','>=',$yearFirstDate)->where('branchDate','<=',$yearLastDate);
            }

        $maxDayEndId = (int) PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',1)->orderBy('branchDate','desc')->value('id');

		$dayEnds = $dayEnds->orderBy('branchDate','desc')->paginate(31);
		$branchName = DB::table('gnr_branch')->where('id',$targetBranchId)->value('name');
        $isFirstRequest = isset($req->filBranch) ? 0:1;
		$branchList = DB::table('gnr_branch');
		if ($userBranchId!=1) {
                $branchList = $branchList->where('id',$userBranchId);
            }

       $branchList = $branchList
                       ->orderBy('branchCode')
                       ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                       ->pluck('nameWithCode', 'id')
                       ->toArray(); 


      $damageData = array(
                'softwareDate'    =>  $softwareDate,
                'dayEnds'         =>  $dayEnds,
                'branchName'      =>  $branchName,
                'monthArray'      =>  $monthArray,
                'yearArray'       =>  $yearArray,
                'maxDayEndId'     =>  $maxDayEndId,
                'lastYear'        =>  isset($yearArray[0]) ? $yearArray[0]:'',
                'userBranchId'    =>  $userBranchId,
                'isFirstRequest'  =>  $isFirstRequest,
                'branchList'      =>  $branchList,
            );
			
		return view('pos/process/posDayEndList',$damageData);
    }
 public function storeDayEnd(Request $req){

            $targetBranchId = $req->branchId;

            //$userBranchId = Auth::user()->branchId;
            //$orgId = Auth::user()->company_id_fk;

            $softwareDate = PosDayEnd::where('branchIdFk',$targetBranchId)->where('isLocked',0)->min('branchDate');

            if ($softwareDate=='' || $softwareDate==null) {
                $softwareDate = DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate');
            }

            $softwareDate = Carbon::parse($softwareDate);
            // $softwareDate = Carbon::parse('2017-11-10');
            $softwareDateFormat = $softwareDate->format('Y-m-d');
            $this->salesNServiceJVautoVoucher($softwareDateFormat,$targetBranchId);
            
            $totalSalesQuantity = DB::table('pos_sales')->where('salesDate',$softwareDateFormat)->where('branchId',$targetBranchId)->sum('totalSalesQuantity');
            $totalSalesAmount = DB::table('pos_sales')->where('salesDate',$softwareDateFormat)->where('branchId',$targetBranchId)->sum('totalSaleGrossAmount');
            $totalSalesPayAmount = DB::table('pos_sales')->where('salesDate',$softwareDateFormat)->where('branchId',$targetBranchId)->sum('salesPayAmount');
            $totalCollectionAmount = DB::table('pos_collection')->where('collectionDate',$softwareDateFormat)->where('branchId',$targetBranchId)->sum('salesPayAmount');
            
            $softwareStartDate = Carbon::parse(DB::table('gnr_branch')->where('id',$targetBranchId)->value('softwareStartDate'));
            if ($softwareStartDate->copy()->format('Y-m')==$softwareDate->copy()->format('Y-m')) {
                // this date ar in the same month (e.g. it is the starting month)
            }
            else{
                
                // first month passed, now check the month end closed or not
                $lastMonthLastDate = $softwareDate->copy()->startOfMonth()->subDay()->format('Y-m-d');
                $monthEndExits = (int) PosMonthEnd::where('branchIdFk',$targetBranchId)->where('date',$lastMonthLastDate)->value('id');
                if ($monthEndExits<1) {
                    $data = array(
                        'responseTitle' =>  'Warning!',
                        'responseText'  =>  'Executed month end first.'
                    );

                    return response::json($data);
                }
            }

            /// check is there should be transaction from auto process but not entry
         
                $isDayExits = (int) PosDayEnd::where('branchIdFk',$targetBranchId)->where('branchDate',$softwareDate)->value('id');
            if ($isDayExits>0) {
                $dayEnd = PosDayEnd::where('branchIdFk',$targetBranchId)->where('branchDate',$softwareDate)->first();
            }
            else{
                $dayEnd = new PosDayEnd;
            }     

            $dayEnd->branchDate             = $softwareDate;
            $dayEnd->branchIdFk             = $targetBranchId;            
            $dayEnd->isLocked               = 1;
            $dayEnd->totalSalesQuantity     = $totalSalesQuantity;
            $dayEnd->totalSalesAmount       = $totalSalesAmount;
            $dayEnd->totalSalesPayAmount    = $totalSalesPayAmount;
            $dayEnd->totalCollectionAmount  = $totalCollectionAmount;
            $dayEnd->executedByEmpIdFk      = Auth::user()->emp_id_fk;
            $dayEnd->createdDate            = Carbon::now();
            $dayEnd->save();

            // Insert Next working day. Holiday will not be the next working Day.
            
            $nextDay = '';
            $isNextDayHoliday = 1;
            $nextDay = $softwareDate->copy();

            while ($isNextDayHoliday==1) {
                $nextDay = $nextDay->addDay();
                $nextDateString = $nextDay->copy()->format('Y-m-d');
                $isNextDayHoliday = $this->isHoliday($nextDateString,$targetBranchId);
            }

            $isDayExits = (int) PosDayEnd::where('branchIdFk',$targetBranchId)->where('branchDate',$nextDay)->value('id');
            if ($isDayExits>0) {
                $nextDayEnd = PosDayEnd::where('branchIdFk',$targetBranchId)->where('branchDate',$nextDay)->first();
            } else {
                $nextDayEnd = new PosDayEnd;
            }

            $nextDayEnd->branchDate = $nextDay;
            $nextDayEnd->branchIdFk = $targetBranchId;            
            $nextDayEnd->isLocked   = 0;
            $nextDayEnd->save();

            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Day End executed successfully.'
            );

            return response::json($data);

        }

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


     public function deletePosDayEnd(Request $req){
            $dayEnd = PosDayEnd::find($req->id);

            // first check month end is deleted or not
            $dayEndMonthLastDate = Carbon::parse($dayEnd->branchDate)->endOfMonth()->format('Y-m-d');
            $isMonthEndExits = (int) PosMonthEnd::where('branchIdFk',$dayEnd->branchIdFk)->where('date',$dayEndMonthLastDate)->value('id');
            if ($isMonthEndExits>0) {
                $data = array(
                    'responseTitle' =>  'Warning!',
                    'responseText'  =>  'Deleted month end first.'
                );

                return response::json($data);
            }
            $dayEnd->isLocked           = 0;
            $dayEnd->executedByEmpIdFk  = 0;
            $dayEnd->save();

            DB::table('pos_day_end')->where('branchIdFk',$dayEnd->branchIdFk)->where('branchDate','>',$dayEnd->branchDate)->delete();
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Day End deleted successfully.'
            );

            return response::json($data);
        }
     public function getYears(Request $req){

            $dayEndMinYear = PosDayEnd::where('branchIdFk',$req->branchId)->where('isLocked',1)->min('branchDate');
            $dayEndMinYear = (int) Carbon::parse($dayEndMinYear)->format('Y');

            $dayEndMaxYear = PosDayEnd::where('branchIdFk',$req->branchId)->where('isLocked',1)->max('branchDate');
            $dayEndMaxYear = (int) Carbon::parse($dayEndMaxYear)->format('Y');

            $yearArray = array();

            while ($dayEndMaxYear>=$dayEndMinYear) {
                $yearArray = $yearArray + [$dayEndMaxYear=>$dayEndMaxYear];
                $dayEndMaxYear--;
            }

            return response::json($yearArray);
        }
//==============================START AUTO BOUCHER FOR POS==============================
        public function salesNServiceJVautoVoucher($softwareDateFormat,$targetBranchId){

            $totalSaleGrossAmount = DB::table('pos_sales')->where('salesType',1)->where('salesDate',$softwareDateFormat)->where('branchId',$targetBranchId)->sum('totalSaleGrossAmount');

            $posSalesId = DB::table('pos_sales')->where('salesType',1)->where('salesDate',$softwareDateFormat)->where('branchId',$targetBranchId)->pluck('id')->toArray();


             if($totalSaleGrossAmount>0){

                DB::table('acc_auto_voucher_ref')->insert([
                        'posSalesId' =>json_encode($posSalesId),
                        'salesType'  => 1,
                        'amountType' => 'totalSaleGrossAmount',
                        'createdDate'         => Carbon::now()
                ]);

                $salesReffId = DB::table('acc_auto_voucher_ref')->where('posSalesId','!=',null)->max('id');

                $voucherCodeArr = array(
                              'voucherTypeId' => 3,
                              'projectTypeId' => 1,
                              'branchId' => $targetBranchId,
                          );
                $voucherCode = $this->Accounting->getVoucherCodeForAV($voucherCodeArr);

                $salesDebitLedgerIdStr = DB::table('acc_auto_voucher_config')->where('moduleId',8)->where('misTypeId_Fk',1)->where('voucherType',3)->where('amountType',1)->value('ledgerId');
                $salesDebitLedgerIdStr =  str_replace(array('"', '[', ']'),'', $salesDebitLedgerIdStr);
                $salesDebitLedgerIdArr = array_map('intval', explode(',', $salesDebitLedgerIdStr));
                if (count($salesDebitLedgerIdArr)==1) {
                    $salesDebitLedgerId=$salesDebitLedgerIdArr[0];
                }
                $salesCreditLedgerStr = DB::table('acc_auto_voucher_config')->where('moduleId',8)->where('misTypeId_Fk',1)->where('voucherType',3)->where('amountType',2)->value('ledgerId');
                $salesCreditLedgerStr =  str_replace(array('"', '[', ']'),'', $salesCreditLedgerStr);
                $salesCreditLedgerIdArr = array_map('intval', explode(',', $salesCreditLedgerStr));
                if (count($salesCreditLedgerIdArr)==1) {
                    $salesCreditLedgerId=$salesCreditLedgerIdArr[0];
                }
                $salesLocalNarration = DB::table('acc_auto_voucher_config')->where('moduleId',8)->where('misTypeId_Fk',1)->where('voucherType',3)->value('localNarration');

                $updateVoucherReffIdArr = DB::table('acc_voucher')->where('voucherDate',$softwareDateFormat)->where('branchId',$targetBranchId)->where('moduleIdFk',8)->where('vGenerateType',1)->pluck('referenceId')->toArray();
                $updateSalesReffIdArr = DB::table('acc_auto_voucher_ref')->whereIn('id',$updateVoucherReffIdArr)->where('salesType',1)->pluck('id')->toArray();

                $updateVoucherDetailsIdArr = DB::table('acc_voucher')->whereIn('referenceId',$updateSalesReffIdArr)->pluck('id')->toArray();
               if(count($updateVoucherDetailsIdArr) > 0){

                DB::table('acc_voucher_details')->whereIn('voucherId', $updateVoucherDetailsIdArr)
                ->update(array('amount' => $totalSaleGrossAmount));

               } else {
                DB::table('acc_voucher')->insert([
                  'voucherTypeId'       => 3,
                  'projectId'           => 1,
                  'projectTypeId'       => 1,
                  'voucherDate'         => $softwareDateFormat,
                  'voucherCode'         => $voucherCode,
                  'globalNarration'     => "Auto Voucher",
                  'branchId'            => $targetBranchId,
                  'companyId'           => 1,
                  'referenceId'         => $salesReffId,
                  'vGenerateType'       => 1,
                  'moduleIdFk'          => 8,
                  'prepBy'              => Auth::user()->id,
                  'authBy'              => Auth::user()->emp_id_fk,
                  'createdDate'         => Carbon::now(),
                  'status'              => 0
                  ]);
               $voucherId = DB::table('acc_voucher')->where('voucherCode',$voucherCode)->value('id');

               DB::table('acc_voucher_details')->insert([
                  'voucherId'      => $voucherId,
                  'debitAcc'       => $salesDebitLedgerId,
                  'creditAcc'      => $salesCreditLedgerId,
                  'amount'         => $totalSaleGrossAmount,
                  'localNarration' => $salesLocalNarration,
                  'createdDate'    => Carbon::now(),
                  'status'         => 0
                 ]);
               }
             } else if($totalSaleGrossAmount==0) {

                $voucherReferenceIdArr = DB::table('acc_voucher')->where('voucherDate',$softwareDateFormat)->where('branchId',$targetBranchId)->where('moduleIdFk',8)->where('vGenerateType',1)->pluck('referenceId')->toArray();

                $deleteReferanceId = DB::table('acc_auto_voucher_ref')->whreIn('id',$voucherReferenceIdArr)->where('salesType',1)->pluck('id')->toArray();

                DB::table('acc_voucher')->whereIn('referenceId',$deleteReferanceId)->delete();


             }




            $totalServiceGrossAmount = DB::table('pos_sales')->where('salesType',2)->where('salesDate',$softwareDateFormat)->where('branchId',$targetBranchId)->sum('totalSaleGrossAmount');
            $posServiceId = DB::table('pos_sales')->where('salesType',2)->where('salesDate',$softwareDateFormat)->where('branchId',$targetBranchId)->pluck('id')->toArray();

             if($totalServiceGrossAmount > 0){

                DB::table('acc_auto_voucher_ref')->insert([
                        'posServiceId'  =>json_encode($posServiceId),
                        'salesType'     => 2,
                        'amountType'    => 'totalSaleGrossAmount',
                        'createdDate'   => Carbon::now()
                ]);
               $serviceReffId = DB::table('acc_auto_voucher_ref')->where('posServiceId','!=',null)->max('id');
                $voucherCodeArr = array(
                              'voucherTypeId' => 3,
                              'projectTypeId' => 1,
                              'branchId' => $targetBranchId,
                          );
                $voucherCode1 = $this->Accounting->getVoucherCodeForAV($voucherCodeArr);

                $serviceDebitLedgerStr = DB::table('acc_auto_voucher_config')->where('moduleId',8)->where('misTypeId_Fk',3)->where('voucherType',3)->where('amountType',1)->value('ledgerId');
                $serviceDebitLedgerStr =  str_replace(array('"', '[', ']'),'', $serviceDebitLedgerStr);
                $serviceDebitLedgerArr = array_map('intval', explode(',', $serviceDebitLedgerStr));
                if (count($serviceDebitLedgerArr)==1) {
                    $serviceDebitLedgerId=$serviceDebitLedgerArr[0];
                }

                $serviceCreditLedgerStr = DB::table('acc_auto_voucher_config')->where('moduleId',8)->where('misTypeId_Fk',3)->where('voucherType',3)->where('amountType',2)->value('ledgerId');
                $serviceCreditLedgerStr =  str_replace(array('"', '[', ']'),'', $serviceCreditLedgerStr);
                $serviceCreditLedgerArr = array_map('intval', explode(',', $serviceCreditLedgerStr));
                if (count($serviceCreditLedgerArr)==1) {
                    $serviceCreditLedgerId=$serviceCreditLedgerArr[0];
                }
                $serviceLocalNarration = DB::table('acc_auto_voucher_config')->where('moduleId',8)->where('misTypeId_Fk',3)->where('voucherType',3)->value('localNarration');

                $updateVoucherReffIdArr = DB::table('acc_voucher')->where('voucherDate',$softwareDateFormat)->where('branchId',$targetBranchId)->where('moduleIdFk',8)->where('vGenerateType',1)->pluck('referenceId')->toArray();

                $updateServiceReffIdArr = DB::table('acc_auto_voucher_ref')->whereIn('id',$updateVoucherReffIdArr)->where('salesType',2)->pluck('id')->toArray();

                $updateVoucherDetailsIdArr = DB::table('acc_voucher')->whereIn('referenceId',$updateServiceReffIdArr)->pluck('id')->toArray();

                if(count($updateVoucherDetailsIdArr) > 0) {

                    DB::table('acc_voucher_details')->whereIn('voucherId', $updateVoucherDetailsIdArr)
                ->update(array('amount' => $totalServiceGrossAmount));

                } else {

                    DB::table('acc_voucher')->insert([
                  'voucherTypeId'       => 3,
                  'projectId'           => 1,
                  'projectTypeId'       => 1,
                  'voucherDate'         => $softwareDateFormat,
                  'voucherCode'         => $voucherCode1,
                  'globalNarration'     => "Auto Voucher",
                  'branchId'             => $targetBranchId,
                  'companyId'           => 1,
                  'referenceId'         => $serviceReffId,
                  'vGenerateType'       => 1,
                  'moduleIdFk'          => 8,
                  'prepBy'              => Auth::user()->id,
                  'authBy'              => Auth::user()->emp_id_fk,
                  'createdDate'         => Carbon::now(),
                  'status'              => 0
                  ]);

                $voucherId = DB::table('acc_voucher')->where('voucherCode',$voucherCode1)->value('id');
                DB::table('acc_voucher_details')->insert([
                  'voucherId'      => $voucherId,
                  'debitAcc'       => $serviceDebitLedgerId,
                  'creditAcc'      => $serviceCreditLedgerId,
                  'amount'         => $totalServiceGrossAmount,
                  'localNarration' => $serviceLocalNarration,
                  'createdDate'    => Carbon::now(),
                  'status'         => 0
                 ]);

                }
             } else if($totalServiceGrossAmount==0) {

                $voucherReferenceIdArr = DB::table('acc_voucher')->where('voucherDate',$softwareDateFormat)->where('branchId',$targetBranchId)->where('moduleIdFk',8)->where('vGenerateType',1)->pluck('referenceId')->toArray();

                $deleteReferanceId = DB::table('acc_auto_voucher_ref')->whereIn('id',$voucherReferenceIdArr)->where('salesType',2)->pluck('id')->toArray();

                DB::table('acc_voucher')->whereIn('referenceId',$deleteReferanceId)->delete();

             }

             

        }


        
        

}