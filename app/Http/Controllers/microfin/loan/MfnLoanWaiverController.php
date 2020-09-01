<?php

namespace App\Http\Controllers\microfin\loan;

use Illuminate\Http\Request;
use App\Http\Requests;
use Session;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\microfin\process\MfnLoanWaiver;
use App\microfin\process\MfnLoanRebate;
use App\microfin\loan\MfnLoanSchedule;
use App\Traits\GetSoftwareDate;
use App\Http\Controllers\microfin\MicroFin;
use App\Http\Controllers\microfin\MicroFinance;
use App\Http\Controllers\gnr\Service;
use App;

class MfnLoanWaiverController extends controller {

    protected $MicroFinance;

    use GetSoftwareDate;

    public function __construct() {

        $this->MicroFinance = New MicroFinance;
    }

    public function index(Request $req){

        $softDate = GetSoftwareDate::getSoftwareDate();

        $userBranchId = Auth::user()->branchId;
        $branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);

        $PAGE_SIZE = 20;

        if($userBranchId == 1):
            $samity = [];
            $waivers = MfnLoanWaiver::where('softDel',0);
        else:
            /*$samity = $this->MicroFinance->getBranchWiseSamityOptions(Auth::user()->branchId);
            $waivers = MfnLoanWaiver::where('branchIdFk',$userBranchId);*/
            //$branchIdArray = App\Service\Service::getEngagedBranchesByUserId(Auth::user()->id);
            $samity = $this->MicroFinance->getBranchWiseSamityOptions($branchIdArray);
            $waivers = MfnLoanWaiver::whereIn('branchIdFk',$branchIdArray)->where('softDel',0);
        endif;

        if($req->has('branchId')) {
            $waivers = $waivers->where('branchIdFk', $req->get('branchId'));
            $samity = $this->MicroFinance->getBranchWiseSamityOptions($req->get('branchId'));
        }

        if($req->has('samityId'))
            $waivers = $waivers->where('samityIdFk', $req->get('samityId'));

        if($req->has('dateFrom'))
            $waivers = $waivers->where('date', '>=', $this->MicroFinance->getDBDateFormat($req->get('dateFrom')));

        if($req->has('dateTo'))
            $waivers = $waivers->where('date', '<=', $this->MicroFinance->getDBDateFormat($req->get('dateTo')));

        if($req->has('loanCode')){
            $getLoanId = DB::table('mfn_loan')
            ->where('loanCode', $req->get('loanCode'))
            ->pluck('id')
            ->toArray();

            $waivers = $waivers->where('loanIdFk', $getLoanId[0]);

			// $waivers = $waivers->join('mfn_loan', 'mfn_loan_waivers.loanIdFk', '=', 'mfn_loan.id')
			// 		->select('mfn_loan_waivers.*', 'mfn_loan.loanCode')
			// 		->where('mfn_loan.loanCode', '=', $req->get('loanCode'));
        }
        
        if($req->has('page'))
            $SL = $req->get('page') * $PAGE_SIZE - $PAGE_SIZE;

        if($req->has('branchId') || $req->has('samityId') || $req->has('dateFrom') || $req->has('dateTo') || $req->has('noi') || $req->has('loanCode')) {
            $isSearch = 1;
        } else {

            $isSearch = 0;
        }
        $waivers = $waivers->paginate($PAGE_SIZE);

        $members = DB::table('mfn_member_information')->whereIn('id',$waivers->pluck('memberIdFk'))->select('id','name','code')->get();
        $loans = DB::table('mfn_loan')->whereIn('memberIdFk',$waivers->pluck('memberIdFk'))->select('id','loanCode')->get();

        $samitys = DB::table('mfn_samity')->whereIn('id',$waivers->pluck('samityIdFk'))->select('id','code')->get();

        $employees = DB::table('hr_emp_general_info')->select('id','emp_name_english')->get();

        if ($userBranchId==1) {
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

        /*$waiverTypeList = array(
            ''   => 'Select',
            '1'  => 'Full',
            '2'  => 'Partial'
        );*/

        $waiverTypeList = array(
            ''   => 'Select',
            '1'  => 'With Service Charge',
            '2'  => 'Without Service Charge'
        );

        $data = array(
            'softDate'       => $softDate,
            'SL' 	   		 => $req->has('page')?$SL:0,
            'userBranchId'   => $userBranchId,
            'waivers'        => $waivers,
            'isSearch'       => $isSearch,
            'branch'  		 => $this->MicroFinance->getAllBranchOptions(),
            'members'        => $members,
            'loans'          => $loans,
            'samitys'        => $samitys,
            'samity'         => $samity,
            'employees'      => $employees,
            'branchIdArray'      => $branchIdArray,
            'branchList'      => $branchList,
            'waiverTypeList' => $waiverTypeList
        );

        return view('microfin/loan/loanWaiver/viewLoanWaiver', $data);
    }

    public function addLoanWaiver(){

        $userBranchId = Auth::user()->branchId;

        $softwareDate = GetSoftwareDate::getSoftwareDateInFormat();
        $softDate = GetSoftwareDate::getSoftwareDate();

        $waiverTypeList = array(
            ''   => 'Select',
            '1'  => 'With Service Charge',
            '2'  => 'Without Service Charge'
        );

        if ($userBranchId==1) {
            $branchList = [''=>'Select'] + Microfin::getBranchList();
            $samityList = [''=>'Select'];
        }
        else{
            $branchList = [''=>'Select'];
            $samityList = [''=>'Select'] + Microfin::getBranchWiseSamityList($userBranchId);
        }

        $data = array(
            'userBranchId'      =>  $userBranchId,
            'softwareDate'      =>  $softwareDate,
            'softDate'          =>  $softDate,
            'waiverTypeList'    =>  $waiverTypeList,
            'branchList'        =>  $branchList,
            'samityList'        =>  $samityList
        );

        return view('microfin/loan/loanWaiver/addLoanWaiver', $data);
    }

    public function storeLoanWaiver(Request $req){

        $rules = array(
            'member'        =>  'required',
            'loanId'        =>  'required',
            'waiverType'    =>  'required',
            'waiverAmount'  =>  'required'
        );

        $attributesNames = array(
            'member'            =>  'Member',
            'loanId'            =>  'Loan Id',
            'waiverType'        =>  'Waiver Type',
            'waiverAmount'      =>  'Waiver Amount'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        // IF WAIVER ALREADY EXISTS, THEN RETURN A WARNING
        $numberOfWaiver = (int) DB::table('mfn_loan_waivers')
        ->where('softDel',0)
        ->where('loanIdFk',$req->loanId)
        ->count('id');

        if ($numberOfWaiver>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Waiver already exists for this loan.'
            );

            return response::json($data);
        }

        $loan = DB::table('mfn_loan')->where('id',$req->loanId)->select('id','productIdFk','primaryProductIdFk','memberIdFk','samityIdFk','branchIdFk')->first();

        $waiverDate = Carbon::parse($req->waiverDate)->format('Y-m-d');
        $branchDate = Microfin::getSoftwareDateBranchWise($loan->branchIdFk);

        if ($waiverDate!=$branchDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Waiver date and the branch date are not the same.'
            );

            return response::json($data);
        }

        // IF TRANSACTION EXITS AFTER THIS DATE THEN GIVE A MESSAGE
        $transactions = (int) DB::table('mfn_loan_collection')->where('softDel',0)->where('amount','>',0)->where('loanIdFk',$req->loanId)->where('collectionDate','>',$waiverDate)->value('id');
        if ($transactions>0) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Transaction Exits Later To This Date.'
            );

            return response::json($data);
        }

        if (!((int) $req->waiverType==1 || (int) $req->waiverType==2)) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Entry Waiver Type.'
            );

            return response::json($data);
        }

        DB::beginTransaction();
        try
        {
            // STORE DATA

            // GET THE LOAN OUTSTANDING
            $outstandingInfo = MicroFin::getLoanOutstanding($req->loanId);
            $outstanding = $outstandingInfo['outstanding'];
            $outstandingPrincipal = $outstandingInfo['outstandingPrincipal'];

            if ($req->waiverType==1) {
                $waiverAmount = $outstanding;
                $principalAmount = $outstandingPrincipal;
                $interestAmount = $outstanding - $outstandingPrincipal;
                $rebateAmount = 0;
                $isWithServiceCharge = 1;
            }
            else{
                $waiverAmount = $outstandingPrincipal;
                $principalAmount = $outstandingPrincipal;
                $interestAmount = 0;
                $rebateAmount = $outstanding - $outstandingPrincipal;
                $isWithServiceCharge = 0;
            }

            $waiver = new MfnLoanWaiver;
            $waiver->loanIdFk               = $loan->id;
            $waiver->productIdFk            = $loan->productIdFk;
            $waiver->primaryProductIdFk     = $loan->primaryProductIdFk;
            $waiver->memberIdFk             = $loan->memberIdFk;
            $waiver->samityIdFk             = $loan->samityIdFk;
            $waiver->branchIdFk             = $loan->branchIdFk;
            $waiver->isWithServiceCharge    = $isWithServiceCharge;
            $waiver->date                   = $waiverDate;
            $waiver->amount                 = $waiverAmount;
            $waiver->principalAmount        = $principalAmount;
            $waiver->interestAmount         = $interestAmount;
            $waiver->entryByEmployeeIdFk    = Auth::user()->emp_id_fk;
            $waiver->reason                 = 'Member Death';
            $waiver->notes                  = $req->notes;
            $waiver->createdAt              = Carbon::now();
            $waiver->save();

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnLoanWaiverController',
                'tableName'  => 'mfn_loan_waivers',
                'operation'  => 'insert',
                'primaryIds'  => [DB::table('mfn_loan_waivers')->max('id')]
            );
            Service::createLog($logArray);

            if ($req->waiverType==2) {
                /// STORE REBATE
                $rebate = new MfnLoanRebate;
                $rebate->loanIdFk               = $loan->id;
                $rebate->productIdFk            = $loan->productIdFk;
                $rebate->primaryProductIdFk     = $loan->primaryProductIdFk;
                $rebate->memberIdFk             = $loan->memberIdFk;
                $rebate->samityIdFk             = $loan->samityIdFk;
                $rebate->branchIdFk             = $loan->branchIdFk;
                $rebate->waiverId               = $waiver->id;
                $rebate->date                   = $waiverDate;
                $rebate->amount                 = $rebateAmount;
                $rebate->notes                  = $req->notes;
                $rebate->entryByEmpIdFk         = Auth::user()->emp_id_fk;
                $rebate->createdAt              = Carbon::now();
                $rebate->save();
            }

            // UPDATE LOANSTATUS AND SCHEDULE
            Microfin::updateLoanStatusNSchedule($loan->id);
            
            DB::commit();
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data inserted successfully.'
            );

            return response::json($data);
        }

        catch(\Exception $e){
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }        

    }

    public function updateLoanWaiver(Request $req){

        if (!((int) $req->waiverType==1 || (int) $req->waiverType==2)) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Entry Waiver Type.'
            );

            return response::json($data);
        }

        $waiver = MfnLoanWaiver::find($req->waiverId);
        $previousdata = $waiver;

        $branchDate = Microfin::getSoftwareDateBranchWise($waiver->branchIdFk);

        if ($waiver->date!=$branchDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Waiver date and the branch date are not the same.'
            );

            return response::json($data);
        }

        $rules = array(
            'waiverType'    =>  'required',
            'waiverAmount'  =>  'required'
        );

        $attributesNames = array(
            'waiverType'        =>  'Waiver Type',
            'waiverAmount'      =>  'Waiver Amount'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        else{
            DB::beginTransaction();

            try{               

                // GET THE LOAN OUTSTANDING EXCEPT THIS WAIVER
                $outstandingInfo = MicroFin::getLoanOutstandingExceptSomething($waiver->loanIdFk,'mfn_loan_waivers',$waiver->id);
                $outstanding = $outstandingInfo['outstanding'];
                $outstandingPrincipal = $outstandingInfo['outstandingPrincipal'];


                if ($req->waiverType==1) {
                    $waiverAmount = $outstanding;
                    $principalAmount = $outstandingPrincipal;
                    $interestAmount = $outstanding - $outstandingPrincipal;
                    $rebateAmount = 0;
                    $isWithServiceCharge = 1;
                }
                else{
                    $waiverAmount = $outstandingPrincipal;
                    $principalAmount = $outstandingPrincipal;
                    $interestAmount = 0;
                    $rebateAmount = $outstanding - $outstandingPrincipal;
                    $isWithServiceCharge = 0;
                }
                
                $waiver->isWithServiceCharge    = $isWithServiceCharge;
                $waiver->amount                 = $waiverAmount;
                $waiver->principalAmount        = $principalAmount;
                $waiver->interestAmount         = $interestAmount;
                $waiver->entryByEmployeeIdFk    = Auth::user()->emp_id_fk;
                $waiver->notes                  = $req->notes;
                $waiver->save();

                $logArray = array(
                    'moduleId'  => 6,
                    'controllerName'  => 'MfnLoanWaiverController',
                    'tableName'  => 'mfn_loan_waivers',
                    'operation'  => 'update',
                    'previousData'  => $previousdata,
                    'primaryIds'  => [$previousdata->id]
                );
                Service::createLog($logArray);

                if ($req->waiverType==2) {
                /// STORE REBATE
                    $loan = DB::table('mfn_loan')->where('id',$waiver->loanIdFk)->select('id','productIdFk','primaryProductIdFk','memberIdFk','samityIdFk','branchIdFk')->first();

                    $rebate = MfnLoanRebate::where([['waiverId',$waiver->id], ['loanIdFk',$waiver->loanIdFk] ])->first();
                    if ($rebate==null) {
                        $rebate = new MfnLoanRebate;
                        $rebate->loanIdFk               = $loan->id;
                        $rebate->productIdFk            = $loan->productIdFk;
                        $rebate->primaryProductIdFk     = $loan->primaryProductIdFk;
                        $rebate->memberIdFk             = $loan->memberIdFk;
                        $rebate->samityIdFk             = $loan->samityIdFk;
                        $rebate->branchIdFk             = $loan->branchIdFk;
                        $rebate->waiverId               = $waiver->id;
                        $rebate->date                   = $waiver->date;
                        $rebate->amount                 = $rebateAmount;
                        $rebate->notes                  = $req->notes;
                        $rebate->entryByEmpIdFk         = Auth::user()->emp_id_fk;
                        $rebate->createdAt              = Carbon::now();
                        $rebate->save();
                    }
                    else{
                        $rebate->amount                 = $rebateAmount;
                        $rebate->notes                  = $req->notes;
                        $rebate->entryByEmpIdFk         = Auth::user()->emp_id_fk;
                        $rebate->softDel                = 0;
                        $rebate->save();
                    }                    
                }
                else{
                    // DELETE THE REBATE
                    DB::table('mfn_loan_rebates')->where('waiverId',$waiver->id)->update(['softDel'=>1]);
                }

                // UPDATE LOANSTATUS AND SCHEDULE
                Microfin::updateLoanStatusNSchedule($loan->id);

                DB::commit();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Data updated successfully.'
                );

                return response::json($data);
            }
            catch(\Exception $e){
                DB::rollback();
                $data = array(
                    'responseTitle'  =>  'Warning!',
                    'responseText'   =>  'Something went wrong. Please try again.'
                );

                if (Auth::user()->id==1) {
                    $data = array(
                        'responseTitle'  =>  'Warning!',
                        'responseText'   =>  'Line:'.$e->getLine().'; '.$e->getMessage()
                    );
                }
                return response::json($data);
            }
        }
    }

    public function updateLoanWaiverOld(Request $req){

        $rules = array(
            'waiverType'    =>  'required',
            'waiverAmount'  =>  'required'
        );

        $attributesNames = array(
            'waiverType'        =>  'Waiver Type',
            'waiverAmount'      =>  'Waiver Amount'
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributesNames);

        if($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        else{
            DB::beginTransaction();

            try{
                $waiver = MfnLoanWaiver::find($req->waiverId);

                $interestRateIndex = (float) DB::table('mfn_loan')->where('id',$waiver->loanIdFk)->value('interestRateIndex');

                $waiverAmount = (float) (str_replace(',','',$req->waiverAmount));
                $principalAmount = round($waiverAmount / $interestRateIndex,5);
                $interestAmount = round($waiverAmount - $principalAmount,5);

                $waiver->isFull                 = $req->waiverType==1 ? 1:0;
                $waiver->amount                 = $waiverAmount;
                $waiver->principalAmount        = $principalAmount;
                $waiver->interestAmount         = $interestAmount;
                $waiver->notes                  = $req->notes;
                $waiver->save();

                    /// Update Rebate
                $rebate = MfnLoanRebate::where('loanIdFk',$waiver->loanIdFk)->first();
                $rebate->amount     = $interestAmount;
                $rebate->notes      = $req->notes;
                $rebate->save();

                $this->updateLoanStatus($waiver->loanIdFk,$waiver->date,$waiverAmount);
                DB::commit();

                $data = array(
                    'responseTitle' =>  'Success!',
                    'responseText'  =>  'Data updated successfully.'
                );

                return response::json($data);
            }
            catch(\Exception $e){
                DB::rollback();
                $data = array(
                    'responseTitle'  =>  'Warning!',
                    'responseText'   =>  'Something went wrong. Please try again.'
                );
                return response::json($data);
            }
        }
    }

    public function deleteLoanWaiver(Request $req){
        $waiver = MfnLoanWaiver::find($req->id);

        $previousdata = $waiver;
        $branchDate = Microfin::getSoftwareDateBranchWise($waiver->branchIdFk);

        if ($waiver->date!=$branchDate) {
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  'Waiver date and the branch date are not the same.'
            );

            return response::json($data);
        }

        DB::beginTransaction();
        try
        {
            DB::table('mfn_loan_waivers')->where('id',$waiver->id)->update(['softDel'=>1]);

            $logArray = array(
                'moduleId'  => 6,
                'controllerName'  => 'MfnLoanWaiverController',
                'tableName'  => 'mfn_loan_waivers',
                'operation'  => 'delete',
                'previousData'  => $previousdata,
                'primaryIds'  => [$previousdata->id]
            );
            Service::createLog($logArray);
            
            DB::table('mfn_loan_rebates')->where('waiverId',$waiver->id)->update(['softDel'=>1]);
            
            // UPDATE LOANSTATUS AND SCHEDULE
            Microfin::updateLoanStatusNSchedule($waiver->loanIdFk);

            DB::commit();
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data deleted successfully.'
            );

            return response::json($data);
        }
        catch(\Exception $e){
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }
    }

    public function deleteLoanWaiverOld(Request $req){
        DB::beginTransaction();
        try
        {
            $waiver = MfnLoanWaiver::find($req->id);
            $waiverInfos = MfnLoanWaiver::where('id', $req->id)->get();
            $waiveredLoanId = MfnLoanWaiver::where('id', $req->id)->pluck('loanIdFk')->toArray();
            $rebate = MfnLoanRebate::where('loanIdFk',$waiver->loanIdFk)->first();

            foreach ($waiverInfos as $waiverInfo)   {
                $insertWaiverDeletedInfo = DB::table('mfn_loan_waiver_deleted')
                ->insert(
                    [
                        'loanWaiverId'        => $waiverInfo->id,
                        'loanIdFk'            => $waiverInfo->loanIdFk,
                        'productIdFk'         => $waiverInfo->productIdFk,
                        'primaryProductIdFk'  => $waiverInfo->primaryProductIdFk,
                        'memberIdFk'          => $waiverInfo->memberIdFk,
                        'samityIdFk'          => $waiverInfo->samityIdFk,
                        'branchIdFk'          => $waiverInfo->branchIdFk,
                        'isFull'              => $waiverInfo->isFull,
                        'date'                => $waiverInfo->date,
                        'amount'              => $waiverInfo->amount,
                        'isForDeath'          => $waiverInfo->isForDeath,
                        'ledgerIdFk'          => $waiverInfo->ledgerIdFk,
                        'principalAmount'     => $waiverInfo->principalAmount,
                        'interestAmount'      => $waiverInfo->interestAmount,
                        'reason'              => $waiverInfo->reason,
                        'authorisedByEmpIdFk' => $waiverInfo->authorisedByEmpIdFk,
                        'entryByEmployeeIdFk' => $waiverInfo->entryByEmployeeIdFk,
                        'notes'               => $waiverInfo->notes,
                        'loanRebateIdFk'      => $waiverInfo->loanRebateIdFk,
                        'waiverCreatedAt'     => $waiverInfo->createdAt,
                        'waiverUpdatedAt'     => $waiverInfo->updatedAt,
                        'deletedDate'         => date("Y-m-d"),
                        'ds'                  => $waiverInfo->ds,
                        'oldLoanCode'         => $waiverInfo->oldLoanCode,
                        'deletedByEmployeeId' => Auth::user()->emp_id_fk
                    ]
                );
            }

            $updateLoanTableInfo = DB::table('mfn_loan')
            ->where('id', $waiveredLoanId)
            ->update(
                [
                    'isLoanCompleted'   => 0,
                    'loanCompletedDate' => '0000-00-00'
                ]
            );

            $waiver->delete();
            $rebate->delete();
            DB::commit();
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  'Data deleted successfully.'
            );

            return response::json($data);
        }
        catch(\Exception $e){
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }
    }

    public function updateLoanStatus($loanId,$waiverDate,$waiverAmount){
        DB::beginTransaction();
        try{
            $loan = DB::table('mfn_loan')->where('id',$loanId)->first();

                // mark the completed installments
            $totalCollectionAmount = DB::table('mfn_loan_collection')->where('softDel',0)->where('loanIdFk',$loanId)->sum('amount') + $waiverAmount;

            $paidAmountOB = DB::table('mfn_opening_balance_loan')->where('softDel',0)->where('loanIdFk',$loanId)->sum('paidLoanAmountOB');
            $totalCollectionAmount += $paidAmountOB;

            if ($loan->totalRepayAmount<=$totalCollectionAmount) {
                DB::table('mfn_loan')->where('id',$loanId)->update(['isLoanCompleted'=>1,'loanCompletedDate'=>$waiverDate]);
            }
            else{
                DB::table('mfn_loan')->where('id',$loanId)->update(['isLoanCompleted'=>0,'loanCompletedDate'=>'0000-00-00']);
            }

            $shedules = MfnLoanSchedule::active()->where('loanIdFk',$loanId)->get();

            foreach ($shedules as $key => $shedule) {
                if ($totalCollectionAmount>=$shedule->installmentAmount) {
                    $shedule->isCompleted = 1;
                    $shedule->isPartiallyPaid = 0;
                    $shedule->partiallyPaidAmount = 0;
                    $shedule->save();
                }
                elseif ($totalCollectionAmount>0) {
                    $shedule->isCompleted = 0;
                    $shedule->isPartiallyPaid = 1;
                    $shedule->partiallyPaidAmount = $totalCollectionAmount;
                    $shedule->save();
                }
                else{
                    $shedule->isCompleted = 0;
                    $shedule->isPartiallyPaid = 0;
                    $shedule->partiallyPaidAmount = 0;
                    $shedule->save();
                }

                $totalCollectionAmount = $totalCollectionAmount - $shedule->installmentAmount;
            }
            DB::commit();

        }
        catch(\Exception $e){
            DB::rollback();
            $data = array(
                'responseTitle'  =>  'Warning!',
                'responseText'   =>  'Something went wrong. Please try again.'
            );
            return response::json($data);
        }

    }

}
