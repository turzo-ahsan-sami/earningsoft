<?php
namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\accounting\AccOTSRegisterAccount;
use App\accounting\AccOTSRegisterMember;
use App\accounting\AccOTSRegisterInterest;
use App\accounting\AccOTSRegisterInterestDetails;
use App\accounting\AccOTSRegisterPayment;
use App\accounting\AccOTSRegisterPaymentDetails;
use App\accounting\AccOTSRegisterPrincipalPayment;
use App\accounting\AccOTSRegisterPeriod;
use Validator;
use Illuminate\Support\Facades\Input;
use Response;
use Carbon\Carbon;
use DB;

class AccOtsRegisterController extends Controller
{
	public function index(){

        $infos = DB::table('acc_ots_account as t1')
                                ->join('acc_ots_member as t2','t1.memberId_fk','t2.id')
                                ->select('t1.*','t2.name')
                                ->orderBy('id','asc')
                                ->get();        

        return view('accounting.register.otsRegister.viewOtsRegister',['infos'=>$infos]);      
    }

    public function addOts(){      
      return view('accounting.register.otsRegister.addOtsRegister');      
    }

     public function storeOts(Request $request){

        $rules = array(
            'memberName' => 'required',
            'mobileNo' => 'numeric|digits:11',
            'branchLocation' => 'required',
            'accNo' => 'required',
            'certificateNo' => 'max:11',
            'period' => 'required',
            'amount' => 'required',
            'dateFrom' => 'required'
            
          );
        $attributeNames = array(
            'memberName' => 'Member Name',
            'spouseOrFatherName' => 'Spouse Or FatherName',
            'nidNo' => 'NID No',
            'mobileNo' => 'Mobile No',
            'branchLocation' => 'Branch Location',
            'employeeReference' => 'Employee Reference',
            'accNo' => 'Account No',
            'certificateNo' => 'Certificate No',
            'period' => 'Period',
            'amount' => 'Amount',
            'dateFrom' => 'Date From'            
        );

        $validator = Validator::make ( Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }


        /// Store Information
        else{
            $account = new AccOTSRegisterAccount;
            $member = new AccOTSRegisterMember;

            $member->name = $request->memberName;
            $member->spouseOrFatherName = $request->spouseOrFatherName;
            $member->memberNo = AccOTSRegisterMember::max('memberNo')+1;
            $member->nidNo = $request->nidNo;
            $member->mobileNo = $request->mobileNo;
            $member->branchId_fk = $request->branchLocation;
            $member->branchMemberNo = AccOTSRegisterMember::where('branchId_fk',$request->branchLocation)->max('branchMemberNo')+1;
            $member->employeeId_fk = $request->employeeReference;
            /*$member->address = $request->address;*/
            $member->accNo = $request->accNo;
            $member->createdAt = Carbon::toDay();
            $member->save();

            $dateFrom = Carbon::parse($request->dateFrom);
            $matureDate = Carbon::parse($request->matureDate);
            $days = $dateFrom->diffInDays($matureDate);

            
            $interestAmount = (float)$request->amount * (float)$request->interestRate* $days/(365*100);

            $account->accNo = $request->accNo;
            $account->certificateNo = str_pad($request->certificateNo,11,'0',STR_PAD_LEFT);
            $account->memberId_fk = AccOTSRegisterMember::max('id');
            $account->projectId_fk = DB::table('gnr_branch')->where('id',$request->branchLocation)->value('projectId');
            $account->branchId_fk = $request->branchLocation;
            $account->employeeId_fk = $request->employeeReference;
            $account->periodId_fk = $request->period;
            $account->amount = $request->amount;
            $account->openingBalance = $request->openingBalance;
            $account->interestRate = $request->interestRate;
            $account->interestAmount = $interestAmount;
            if ($request->effectiveDate=="") {
                $effectiveDate = $dateFrom;
            }
            else{
                $effectiveDate = Carbon::parse($request->effectiveDate);
            }
            $account->effectiveDate = $effectiveDate;
            $account->openingDate = $dateFrom;
            $account->matureDate = $matureDate;
            $account->craetedAt = Carbon::toDay();
            $account->save();

             return response::json('success');
        }    
    }



    public function editOts(Request $request){

        $rules = array(
            'EMmemberName'          => 'required',
            /*'spouseOrFatherName'  => 'required',
            'nidNo'                 => 'required',*/
            /*'EMnidNo' => 'required|numeric|digits:13,19',*/
            'EMmobileNo' => 'numeric|digits:11',
            'EMbranchLocation' => 'required',
            /*'employeeReference' => 'required',*/
            'EMaccNo' => 'required',
            'certificateNo' => 'max:11',
            'EMperiod' => 'required',
            'EMamount' => 'required',
            'EMopeningDate' => 'required'            
        );

        $attributeNames = array(
            'EMmemberName'          => 'Member Name',
            'EMspouseOrFatherName'  => 'Spouse Or FatherName',
            'EMnidNo'               => 'NID No',
            'EMmobileNo'            => 'Mobile No',
            'EMbranchLocation'      => 'Branch Location',
            'EMemployeeReference'   => 'Employee Reference',
            'EMaccNo'               => 'Account No',
            'EMcertificateNo'       => 'Certificate No',
            'EMperiod'              => 'Period',
            'EMamount'              => 'Amount',
            'EMopeningDate'         => 'Opening Date'            
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
          if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
          }


          ///Edit Information
        else{
                $account = AccOTSRegisterAccount::find($request->EMaccountId);
                $member =  AccOTSRegisterMember::find($account->memberId_fk);

                $member->name = $request->EMmemberName;
                $member->spouseOrFatherName = $request->EMspouseOrFatherName;
                $member->nidNo = $request->EMnidNo;
                $member->mobileNo = $request->EMmobileNo;
                $member->branchId_fk = $request->EMbranchLocation;
                $member->employeeId_fk = $request->EMemployeeReference;
                /*$member->address = $request->EMaddress;*/
                $member->accNo = $request->EMaccNo;
                $member->save();

                $account->accNo = $request->EMaccNo;
                $account->certificateNo = str_pad($request->EMcertificateNo,11,'0',STR_PAD_LEFT);
                $account->projectId_fk = DB::table('gnr_branch')->where('id',$request->EMbranchLocation)->value('projectId');
                $account->branchId_fk = $request->EMbranchLocation;
                $account->employeeId_fk = $request->EMemployeeReference;
                $account->periodId_fk = $request->EMperiod;
                $account->amount = $request->EMamount;
                $account->openingBalance = $request->EMopeningBalance;
                $account->openingDate = Carbon::parse($request->EMopeningDate);
                $account->matureDate = Carbon::parse($request->EMmaturityDate);
                $account->effectiveDate = Carbon::parse($request->EMeffectiveDate);
                $account->save();

                 //return redirect('otsRegisterList');
                return response::json('success');
            }    
    }


    public function deleteOts(Request $request){

        $account = AccOTSRegisterAccount::find($request->accId);
        $member =  AccOTSRegisterMember::find($account->memberId_fk);

        $member->delete();
        $account->delete();

        //return redirect('otsRegisterList');
        return response::json('success');
    }



    public function getEmployeeBaseOnBranch(Request $request){                

        if ($request->branchId=="") {
            $employee = DB::table('hr_emp_general_info')->select('id','emp_id','emp_name_english')->get();
        }
        
        else{
            $employeeIds = DB::table('hr_emp_org_info')->where('branch_id_fk',$request->branchId)->pluck('emp_id_fk')->toArray();
            $employee = DB::table('hr_emp_general_info')->whereIn('id',$employeeIds)->select('id','emp_id','emp_name_english')->get();
        }

        return response::json($employee);
        
    }


     public function getAccountBaseOnBranch(Request $request){

        if ($request->branchId=="") {
            $account = DB::table('acc_ots_account')->select('id','accNo')->get();
        }
        
        else{
            $account = DB::table('acc_ots_account')->where('branchId_fk',$request->branchId)->select('id','accNo')->get();
        }

        return response::json($account);
        
    }

     public function getAccountBaseOnEmployee(Request $request){

        $branchId = array();
        $employeeId = array();

        if ($request->branchId=="") {
            $branchId = DB::table('gnr_branch')->pluck('id')->toArray();
        }
        else{
            array_push($branchId, $request->branchId);
        }

         if ($request->employeeId=="") {
            $employee = DB::table('hr_emp_org_info')->whereIn('branch_id_fk',$branchId)->pluck('emp_id_fk')->toArray();
            $employeeId = DB::table('hr_emp_general_info')->whereIn('id',$employee)->pluck('id')->toArray();
        }
        else{
            array_push($employeeId, $request->employeeId);
        }
    
        $account = DB::table('acc_ots_account')->whereIn('branchId_fk',$branchId)->whereIn('employeeId_fk',$employeeId)->select('id','accNo')->get();

        return response::json($account);
    }

    public function getAccountInfo(Request $request){

        $accId = $request->accId;
        $account = AccOTSRegisterAccount::find($accId);
        $memeber = AccOTSRegisterMember::find($account->memberId_fk);

        $branchName = DB::table('gnr_branch')->where('id',$account->branchId_fk)->value('name');
        $employeeName = DB::table('hr_emp_general_info')->where('id',$account->employeeId_fk)->value('emp_id') .'-'. DB::table('hr_emp_general_info')->where('id',$account->employeeId_fk)->value('emp_name_english');
        $paymentNature = DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('name');

        $totalInterests = (float) DB::table('acc_ots_interest_details')->where('accId_fk',$accId)->sum('amount') + (float) $account->openingBalance;
        $totalPayments = (float) DB::table('acc_ots_payment_details')->where('accId_fk',$accId)->sum('amount');
        $dueAmount =  $totalInterests - $totalPayments;

        if ($account->status==1) {
            $payableAmount = (float) $account->amount + $dueAmount;
        }
        else{
           $payableAmount = 0; 
        }

        $data = array(
            'account' => $account,
            'memeber' => $memeber,
            'branchName' => $branchName,
            'employeeName' => $employeeName,
            'paymentNature' => $paymentNature,
            'openingDate' => $account->openingDate,
            'matureDate' => $account->matureDate,
            'effectiveDate' => $account->effectiveDate,
            'totalInterests' => $totalInterests,
            'totalPayments' => $totalPayments,
            'dueAmount' => $dueAmount,
            'payableAmount' => $payableAmount
        );

        return response::json($data);
    }


   /* public function getAccountData(Request $request)
    {
        $memberId = DB::table('acc_ots_account')->where('id',$request->accNo)->value('memberId_fk');
        $accHolderName = DB::table('acc_ots_member')->where('id',$memberId)->value('name');
        $dueAmount = round(DB::table('acc_ots_interest_details')->where('accId_fk',$request->accNo)->sum('amount'),2);

        $data = array(
            'accHolderName' => $accHolderName,
            'dueAmount' => $dueAmount
        );

        return response::json($data);
    }*/


    public function viewInterest()
    {
        $interests = AccOTSRegisterInterest::all();
        if (sizeof($interests)>0) {
            $minDate = DB::table('acc_ots_interest')->orderBy('dateTo','desc')->value('dateTo');
        }
        else{
            $minDate = DB::table('acc_ots_account')->orderBy('effectiveDate','asc')->value('effectiveDate');
        }
        
        return view('accounting.register.otsRegister.viewOtsInterest',['interests'=>$interests,'minDate'=>$minDate]);
    }


    
    /*Generate Interest*/

    public function generateInterest(Request $req) {

        $firstDayOfMonth = Carbon::parse($req->date)->startOfMonth();        
        $lastDayOfMonth = Carbon::parse($req->date)->endOfMonth();

        /*Validation*/
        $startingMonth = Carbon::parse('2017-10-01');

        if ($lastDayOfMonth->lt($startingMonth)) {
            $data = array(
                'responseTitle' =>  'Info!',
                'responseText'  =>  "You can't generate interest before October 2017."
            );
            return response::json($data);
        }

        $lastGenerationDate = Carbon::parse(AccOTSRegisterInterest::max('dateTo'));
         
        $diffInYear = $lastGenerationDate->diffInYears($lastDayOfMonth);
        $diffInMonth = $lastGenerationDate->diffInMonths($lastDayOfMonth);
        if ($diffInYear>0 || $diffInMonth>1) {
            $data = array(
                'responseTitle' =>  'Info!',
                'responseText'  =>  "You need to generate the previous month/months first."
            );
            return response::json($data);
        }
        /*End Validation*/   

        /*Check Is there any interest generated in this month*/
        $hasInterestInThisMonth = (int) AccOTSRegisterInterest::where('dateTo','>=',$firstDayOfMonth)->where('dateTo','<=',$lastDayOfMonth)->value('id');        
        /*End Check Is there any interest generated in this month*/        

        /*If has existing data*/
        if ($hasInterestInThisMonth>0) {
            $interest = AccOTSRegisterInterest::where('dateTo','>=',$firstDayOfMonth)->where('dateTo','<=',$lastDayOfMonth)->first();
            AccOTSRegisterInterestDetails::where('interestId_fk',$interest->id)->delete();
        }
        /*End If has existing data*/
        /*Create new Interest*/
        else{
            $maxInterestNumber = AccOTSRegisterInterest::max('interestNumber')+1;

            $interest = new AccOTSRegisterInterest;
            $interest->interestId = "OTS".str_pad($maxInterestNumber,5,'0',STR_PAD_LEFT);
            $interest->interestNumber = $maxInterestNumber;
            $interest->amount = 0;            
            $interest->dateTo = $lastDayOfMonth;            
            $interest->createdAt = Carbon::toDay();
            $interest->save();            
        }
        /*End Create new Interest*/
       
        
        $totalAmount = 0;
        $amount = 0;

        /*Monthly accounts*/

        $monthlyAccounts = DB::table('acc_ots_account')->whereIn('periodId_fk',[1,5])->where('status',1)->get();

        foreach ($monthlyAccounts as $key => $monthlyAccount) {
            $effectiveDate = Carbon::parse($monthlyAccount->effectiveDate);
            $interestRate = (float) DB::table('acc_ots_period')->where('id',$monthlyAccount->periodId_fk)->value('interestRate');
            if ($effectiveDate->lte($firstDayOfMonth)) {
                $amount = round((float)$monthlyAccount->amount * $interestRate /(12*100));
                $dateFrom = $firstDayOfMonth->copy();
            }            
            else{
                $monthDays = $firstDayOfMonth->diffInDays($lastDayOfMonth) + 1;
                $days = $effectiveDate->diffInDays($lastDayOfMonth) + 1;
                $amount = round((float)$monthlyAccount->amount * $interestRate * $days /($monthDays*12*100));
                $dateFrom = $effectiveDate->copy();
            }

            $periodNumber = DB::table('acc_ots_interest_details')->where('accId_fk',$monthlyAccount->id)->max('periodNumber')+1;
            
            $interestDetails = new AccOTSRegisterInterestDetails;
            $interestDetails->interestId_fk = $interest->id;
            $interestDetails->accId_fk = $monthlyAccount->id;
            $interestDetails->accNo = $monthlyAccount->accNo;
            $interestDetails->memberId_fk = $monthlyAccount->memberId_fk;
            $interestDetails->dateFrom = $dateFrom;
            $interestDetails->dateTo = $lastDayOfMonth;
            $interestDetails->generateDate = $lastDayOfMonth;
            $interestDetails->amount = $amount;
            $interestDetails->periodNumber = $periodNumber;
            $interestDetails->save();            

            $totalAmount = $totalAmount + $amount;
        } 

        /*end monthly accounts*/

        //$totalAmount = $totalAmount + $this->generateInterestForMonthlyAcc($interest->id);

        // Other Accounts
        $accounts = DB::table('acc_ots_account')->whereNotIn('periodId_fk',[1,5])->where('status',1)->get();
        foreach ($accounts as $key => $account) {


            $lastInterestGeneratedDate = DB::table('acc_ots_interest_details')->where('accId_fk',$account->id)->orderBy('dateTo','desc')->value('dateTo');
            if ($lastInterestGeneratedDate!=null || $lastInterestGeneratedDate!='') {
                $dateFrom = Carbon::parse($lastInterestGeneratedDate)->addDay();
            }
            else{
                $dateFrom = Carbon::parse($account->effectiveDate);
            }           

            
            $periodLenght = (int) DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('months');
            $periodEndDate = $dateFrom->copy()->addMonthsNoOverflow($periodLenght);

            $period = $periodLenght;

            while($periodEndDate->copy()->addMonthsNoOverflow($periodLenght)->lte($lastDayOfMonth)) {
                $periodEndDate->addMonthsNoOverflow($periodLenght);
                $period = $period + $periodLenght;
            }
            if ($lastDayOfMonth->gte($periodEndDate)) {
                $interestRate = (float) DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('interestRate');
                $amount = round((float) $account->amount * $interestRate* $period/(12*100));
                $periodNumber = DB::table('acc_ots_interest_details')->where('accId_fk',$account->id)->max('periodNumber')+1;

                $lastInterestGeneratedDate = DB::table('acc_ots_interest_details')->where('accId_fk',$account->id)->orderBy('dateTo','desc')->value('dateTo');
                

                /*Store Data to Database*/
                $interestDetails = new AccOTSRegisterInterestDetails;
                $interestDetails->interestId_fk = $interest->id;
                $interestDetails->accId_fk = $account->id;
                $interestDetails->accNo = $account->accNo;
                $interestDetails->memberId_fk = $account->memberId_fk;
                $interestDetails->dateFrom = $dateFrom;
                $interestDetails->dateTo = $periodEndDate;
                $interestDetails->generateDate = $lastDayOfMonth;
                $interestDetails->amount = $amount;
                $interestDetails->periodNumber = $periodNumber;
                $interestDetails->save();
                /*End Store Data to Database*/
                $totalAmount = $totalAmount + $amount;
            }
            else{
                continue;
            }
        } /*end foreach for other accounts*/

        $isAnyData = (int) AccOTSRegisterInterestDetails::where('interestId_fk',$interest->id)->value('id');
        if ($isAnyData>0) {
            $interest->amount = $totalAmount;
            $interest->save();
        }
        else{
            $interest->delete();
        }
            
        $data = array(
            'responseTitle' =>  'Success!',
            'responseText'  =>  "Interest generated successfully."
        );
            return response::json($data);

        return response::json('success');
        
    }
    /*End Generate Interest*/


    public function generateInterestForMonthlyAcc($interestId) {
         $monthlyAccounts = DB::table('acc_ots_account')->whereIn('periodId_fk',[1,5])->where('status',1)->get();

         $totalAmount = 0;

        foreach ($monthlyAccounts as $key => $monthlyAccount) {
            
            $lastInterestGeneratedDate = AccOTSRegisterInterestDetails::where('accId_fk',$monthlyAccount->id)->max('dateTo');

            if ($lastInterestGeneratedDate!='' || $lastInterestGeneratedDate!=null) {
                $lastInterestGeneratedDate = Carbon::parse($lastInterestGeneratedDate);
            }
            else{
                $lastInterestGeneratedDate = Carbon::parse($monthlyAccount->effectiveDate)->subDay();
            }
            


            $startDate = $lastInterestGeneratedDate->copy()->addDay();
            $endDate = Carbon::parse('2017-09-30');

            $interestRate = (float) DB::table('acc_ots_period')->where('id',$monthlyAccount->periodId_fk)->value('interestRate');

            $firstDayOfMonth = Carbon::parse('2017-09-01');

            if ($startDate->lt($firstDayOfMonth)) {
                $days = $startDate->diffInDays($endDate);                
            }
            else{
                $days = $startDate->diffInDays($endDate) + 1;
            }

            $amount = round((float)$monthlyAccount->amount * $interestRate * $days /(360*100));

            $periodNumber = DB::table('acc_ots_interest_details')->where('accId_fk',$monthlyAccount->id)->max('periodNumber')+1;


            /*Store Data to Database*/
            $interestDetails = new AccOTSRegisterInterestDetails;
            $interestDetails->interestId_fk = $interestId;
            $interestDetails->accId_fk = $monthlyAccount->id;
            $interestDetails->accNo = $monthlyAccount->accNo;
            $interestDetails->memberId_fk = $monthlyAccount->memberId_fk;
            $interestDetails->dateFrom = $startDate;
            $interestDetails->dateTo = $endDate;
            $interestDetails->generateDate = Carbon::parse('2017-09-30');
            $interestDetails->amount = $amount;
            $interestDetails->periodNumber = $periodNumber;
            $interestDetails->save();
            /*End Store Data to Database*/

            $totalAmount = $totalAmount + $amount;
        }

        return $totalAmount;
    }

    public function deleteInterest(Request $request){

        AccOTSRegisterInterest::find($request->interestId)->delete();
        AccOTSRegisterInterestDetails::where('interestId_fk',$request->interestId)->delete();

        return response::json('success');
    }


    public function getInterestInfo(Request $request){

        $interest = AccOTSRegisterInterest::find($request->interestId);

        $interestDetails = DB::table('acc_ots_interest_details as t1')
                                ->join('acc_ots_account as t2','t1.accId_fk','=','t2.id')
                                ->join('acc_ots_member as t3','t1.memberId_fk','=','t3.id')
                                ->where('interestId_fk',$interest->id)
                                ->orderBy('t2.branchId_fk')
                                ->select('t1.*','t2.amount as pAmount','t2.periodId_fk as periodId_fk','t3.*')
                                ->get();
        $branchId = DB::table('acc_ots_account')->pluck('branchId_fk')->toArray();
        $branches = DB::table('gnr_branch')->whereIn('id',$branchId)->select('id','name')->get();

        $periods = DB::table('acc_ots_period')->select('id','name')->get();

        $data = array(
            'interestDetails' => $interestDetails,
            'branches' => $branches,
            'periods' => $periods
        );

        return response::json($data);
    }


    /*/////////////Payment*/


    public function viewOtsPayment(){

        $payments = AccOTSRegisterPayment::all();
        return view('accounting.register.otsRegister.viewOtsPayment',['payments'=>$payments]);  
    }

    public function addOtsPayment(){

        $paymentNo = DB::table('acc_ots_payment')->max('paymentNo')+1;
        $paymentId = "OTSP".str_pad($paymentNo,5,'0',STR_PAD_LEFT);
        return view('accounting.register.otsRegister.addOtsPayment',['paymentId'=>$paymentId]);  
    }

    public function storeOtsPayment(Request $request){

        $paymentNo = DB::table('acc_ots_payment')->max('paymentNo')+1;
        $paymentId = "OTSP".str_pad($paymentNo,5,'0',STR_PAD_LEFT);
        if ($request->paymentDate=="") {
            $paymentDate = Carbon::toDay();
        }
        else{
            $paymentDate = Carbon::parse($request->paymentDate);
        }

        $payment = new AccOTSRegisterPayment;

        $payment->paymentId = $paymentId;
        $payment->paymentNo = $paymentNo;
        $payment->paymentDate = $paymentDate;
        $payment->amount = 0;
        $payment->createdAt = Carbon::toDay();
        $payment->save();

        $array_size = count($request->accId);

        $totalAmount = 0;

         for ($i=0; $i<$array_size; $i++){
            $amount = (float)json_decode($request->payableAmount[$i]);
            if ($amount<=0) {
                continue;
            }

            //Get current interest rate of this account
            $accId= (int) json_decode($request->accId[$i]);
            $periodId = (int) DB::table('acc_ots_account')->where('id',$accId)->value('periodId_fk');
            $inetestRate = (float) DB::table('acc_ots_period')->where('id',$periodId)->value('interestRate');

            $payDetails = new AccOTSRegisterPaymentDetails;

            $payDetails->paymentId_fk =  AccOTSRegisterPayment::max('id');
            $payDetails->accId_fk =  json_decode($request->accId[$i]);
            $payDetails->amount =  json_decode($request->payableAmount[$i]);
            $payDetails->dueAmount =  json_decode($request->dueAmount[$i]);
            $payDetails->interestRate =  $inetestRate;
            $payDetails->paymentDate =  $paymentDate;
            $payDetails->createdAt =  Carbon::today();
            $payDetails->save();

            $totalAmount = $totalAmount + (float) json_decode($request->payableAmount[$i]);
        }

        $isAnyData = AccOTSRegisterPaymentDetails::where('paymentId_fk',AccOTSRegisterPayment::max('id'))->value('id');
        if ($isAnyData!=null || $isAnyData!='') {
            $payment->amount = $totalAmount;
            $payment->save();
        }
        else{
            $payment->delete();
        }

        return response::json('success');
    }

    public function getAccountPaymentData(Request $request){

        $branchId = array();
        $periodId = array();

        //Branch
        if ($request->branchId==null) {
            array_push($branchId, 0);
        }
        elseif ($request->branchId==0) {
            $branchId = Db::table('gnr_branch')->pluck('id')->toArray();
        }
        
        else{
            array_push($branchId, $request->branchId);
        }

        //Period
        if ($request->periodId==null) {
            $periodId = DB::table('acc_ots_period')->pluck('id')->toArray();
        }
        else{
            array_push($periodId, $request->periodId);
        }

        $accounts = DB::table('acc_ots_account as t1')
                            ->join('acc_ots_member as t2','t1.memberId_fk','t2.id')
                            ->join('acc_ots_period as t3','t1.periodId_fk','t3.id')
                            ->select('t1.*','t2.name as memberName','t3.name as accNature')
                            ->whereIn('t1.branchId_fk',$branchId)
                            ->whereIn('t3.id',$periodId)
                            ->where('t1.status',1)
                            ->orderBy('id','asc')
                            ->get();

        $totalDue = DB::table('acc_ots_interest_details')
                            ->select('accId_fk','amount')
                            ->get();

        $payments = DB::table('acc_ots_payment_details')->select('accId_fk','amount')->get();

        $data = array(
            'accounts' => $accounts,
            'totalDue' => $totalDue,
            'payments' => $payments          
        );

        return response::json($data);
    }

    public function getPaymentDueInfo($payDetailsId, $accId)
    {
        $payments = (float) DB::table('acc_ots_payment_details')->where('id','!=',$payDetailsId)->where('accId_fk',$accId)->sum('amount');
        $openingBalance = (float) DB::table('acc_ots_account')->where('id',$accId)->value('openingBalance');
        $interests = (float) DB::table('acc_ots_interest_details')->where('accId_fk',$accId)->sum('amount');

        $totalDue = $openingBalance + $interests - $payments;

        return $totalDue;

    }


    public function getPaymentInfo(Request $request){

        $payments = DB::table('acc_ots_payment_details as t1')
                            ->join('acc_ots_account as t2','t1.accId_fk','t2.id')
                            ->join('acc_ots_member as t3','t2.memberId_fk','t3.id')
                            ->join('acc_ots_period as t4','t2.periodId_fk','t4.id')
                            ->select('t1.*','t2.id as accId','t2.accNo as accNo','t2.amount as principalAmount','t2.branchId_fk as branchId','t3.name as memberName','t4.name as accNature','t4.interestRate as interestRate')
                            ->where('t1.paymentId_fk',$request->paymentId)                         
                            ->where('t2.status',1)                         
                            ->get();

        $accIds = DB::table('acc_ots_payment_details')->where('paymentId_fk',$request->paymentId)->pluck('accId_fk')->toArray();
        $branchIds = DB::table('acc_ots_account')->whereIn('id',$accIds)->pluck('branchId_fk');

        $branches = DB::table('gnr_branch')->whereIn('id',$branchIds)->select('id','name')->get();

        $dues = array();
        
        foreach ($payments as $payment) {
            $payDetailsId = $payment->id;
            $accId = $payment->accId;
            
            $due = $this->getPaymentDueInfo($payDetailsId,$accId);
            array_push($dues, $due);
        }

        $data = array(            
            'payments' => $payments,        
            'branches' => $branches,       
            'dues' => $dues       
            );
      

        return response::json($data);
    }


    public function updatePayment(Request $request){

        $array_size = count($request->paymentDetailsId);

        $totalAmount = 0;
        $message = '';


         for ($i=0; $i<$array_size; $i++){



            $payDetails = AccOTSRegisterPaymentDetails::find((int) json_decode($request->paymentDetailsId[$i]));

            if ((float)(json_decode($request->amount[$i]))<=0) {
                $payDetails->delete();
                $message = 'delete';
            }
            else{
                $message = 'update';
                $payDetails->amount =  (float)(json_decode($request->amount[$i]));
                
                $payDetails->save();
            }

            $totalAmount = $totalAmount + (float) json_decode($request->amount[$i]);
        }

        $payment = AccOTSRegisterPayment::find($request->paymentId);

        if ($totalAmount<=0) {
            $payment->delete();
        }
        else{
            $payment->amount = $totalAmount;
            $payment->save();
        }

        return response::json($payDetails);
    }

    public function deletePayment(Request $request)
    {
        $payment = AccOTSRegisterPayment::find($request->DMpaymentId);
        $paymentDetails = AccOTSRegisterPaymentDetails::where('paymentId_fk',$request->DMpaymentId);

        $payment->delete();
        $paymentDetails->delete();

        return redirect('viewOtsPayment');
    }


    /*/////////////End Payment*/

    ///////////////////Pricipal Payment///////

    public function getInterestDue($accId){

        $payments = (float) DB::table('acc_ots_payment_details')->where('accId_fk',$accId)->sum('amount');
        $openingBalance = (float) DB::table('acc_ots_account')->where('id',$accId)->value('openingBalance');
        $interests = (float) DB::table('acc_ots_interest_details')->where('accId_fk',$accId)->sum('amount');

        $totalDue = $openingBalance + $interests - $payments;

        return $totalDue;

    }

     public function viewOtsPrincipalPayment(){

        $payments = AccOTSRegisterPrincipalPayment::all();
        $memberNames = array();
        $branchNames = array();
        $principalAmounts = array();
        /*$interestDues = array();*/

        $accClosingCharge = DB::table('acc_ots_closing_charge')->where('status',1)->orderBy('id','desc')->value('amount');

        foreach ($payments as $payment) {
            $account = AccOTSRegisterAccount::find($payment->accId_fk);
            $member = DB::table('acc_ots_member')->where('id',$account->memberId_fk)->value('name');
            $branch = DB::table('gnr_branch')->where('id',$account->branchId_fk)->value('name');
            /*$interestDue = $this->getInterestDue($account->id);*/

            array_push($memberNames, $member);
            array_push($branchNames, $branch);
            array_push($principalAmounts, $account->amount);
            /*array_push($interestDues, $interestDue);*/
        }
        return view('accounting.register.otsRegister.viewOtsPrincipalPayment',['payments'=>$payments,'memberNames'=>$memberNames,'branchNames'=>$branchNames,'principalAmounts'=>$principalAmounts/*,'interestDues'=>$interestDues*/]);  
    }

    public function addOtsPrincipalPayment(){

        $paymentNo = AccOTSRegisterPrincipalPayment::max('paymentNo') + 1;
        $paymentId = "OTSPP".str_pad($paymentNo,5,'0',STR_PAD_LEFT);
        return view('accounting.register.otsRegister.addOtsPrincipalPayment',['paymentId'=>$paymentId]);  
    }

    public function storePrincipalPayment(Request $request){

        if ($request->paymentMode==2) {
             $rules = array(
            'bankAccNumber' => 'required',           
            'chequeNumber' => 'required|numeric|digits_between:1,20'           
          );
        }
        else{
             $rules = array(           
          );
        }

        $attributeNames = array(
            'bankAccNumber' => 'Bank Account Number',
            'chequeNumber' => 'Cheque Number'            
        );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
          if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
          }

          ///Store Information
        else{

             if ($request->paymentMode==1){
                //Payment Method is Cash, Cash In hand Lefger ID is 350 in database.
                $ledgerId = 350;
                $chequeNumber = null;
             }
             else{
                $ledgerId = $request->bankAccNumber;
                $chequeNumber = $request->chequeNumber;
             }

        $paymentNo = AccOTSRegisterPrincipalPayment::max('paymentNo') + 1;
        $paymentId = "OTSPP".str_pad($paymentNo,5,'0',STR_PAD_LEFT);

        $accClosingCharge = DB::table('acc_ots_closing_charge')->where('status',1)->orderBy('id','desc')->value('amount');

        $payment = new AccOTSRegisterPrincipalPayment;
        $payment->paymentId = $paymentId;
        $payment->paymentNo = $paymentNo;
        $payment->accId_fk = $request->accId;
        $payment->accNo = $request->accNo;
        $payment->amount = $request->amount;
        $payment->dueAmount = $request->dueAmount;
        $payment->accClosingCharge = $accClosingCharge;
        $payment->createdAt = Carbon::toDay();

        $payment->ledgerId_fk = $ledgerId;
        $payment->chequeNumber = $chequeNumber;

        if ($request->closingDate!='') {
            $closingDate = Carbon::parse($request->closingDate);
        }
        else{
             $closingDate = Carbon::toDay();
        }

         $payment->closingDate = $closingDate;

         $payment->save();

         $account = AccOTSRegisterAccount::find($request->accId);
         $account->status = 0;
         $account->closingDate = $closingDate;
         $account->save();

         /*Add Interest Payment Information if Interest Due is greater than Zero*/
         $dueAmount = (float) json_decode($request->dueAmount);
         if ($dueAmount>0) {            
            $paymentNo = DB::table('acc_ots_payment')->max('paymentNo')+1;
            $paymentId = "OTSP".str_pad($paymentNo,5,'0',STR_PAD_LEFT);
            
            $payment = new AccOTSRegisterPayment;

            $payment->paymentId = $paymentId;
            $payment->paymentNo = $paymentNo;
            $payment->paymentDate = $closingDate;
            $payment->amount = $dueAmount;
            $payment->createdAt = Carbon::toDay();
            $payment->save();

           
            //Get current interest rate of this account
            $accId= $request->accId;
            $periodId = (int) DB::table('acc_ots_account')->where('id',$accId)->value('periodId_fk');
            $inetestRate = (float) DB::table('acc_ots_period')->where('id',$periodId)->value('interestRate');

            $payDetails = new AccOTSRegisterPaymentDetails;

            $payDetails->paymentId_fk =  AccOTSRegisterPayment::max('id');
            $payDetails->accId_fk =  $request->accId;
            $payDetails->amount =  $dueAmount;
            $payDetails->dueAmount =  0;
            $payDetails->interestRate =  $inetestRate;
            $payDetails->paymentDate =  $closingDate;
            $payDetails->createdAt =  Carbon::today();
            $payDetails->save();              
            
             
         }
         /*End Add Interest Payment Information if Interest Due is greater than Zero*/

        }/*End Store Information*/

         return response::json('success');
    }

    public function deletePrincipalPayment(Request $request)
    {
        $principalPayment = AccOTSRegisterPrincipalPayment::find($request->DMpaymentId);
        /*Change Account Info*/
        $account = AccOTSRegisterAccount::find($principalPayment->accId_fk);
        $account->status = 1;
        $account->closingDate = null;
        $account->save();
        /*End Change Account Info*/

        /*Delete Payment Data*/
        $hasPaymentDetails = (int) AccOTSRegisterPaymentDetails::where('accId_fk',$principalPayment->accId_fk)->where('paymentDate',$principalPayment->closingDate)->orderBy('id','desc')->value('id');

        if ($hasPaymentDetails>0) {
            $paymentDetails = AccOTSRegisterPaymentDetails::where('accId_fk',$principalPayment->accId_fk)->where('paymentDate',$principalPayment->closingDate)->orderBy('id','desc')->first();
        
            $paymentId = (int) $paymentDetails->paymentId_fk;
            $payment =AccOTSRegisterPayment::find($paymentId);

            $payment->delete();
            $paymentDetails->delete();
        }
        
        
        /*End Delete Payment Data*/

        $principalPayment->delete();

        return redirect("viewOtsPrincipalPayment");
    }

    public function getOtsAccBaseOnBranch(Request $request)
    {
        $branchId = array();
        $periodId = array();

        if ($request->branchId=='' || $request->branchId==0) {
            $branchId = DB::table('gnr_branch')->pluck('id')->toArray();
        }
        else{
            array_push($branchId, $request->branchId);
        }

         if ($request->periodId=='') {
            $periodId = DB::table('acc_ots_period')->pluck('id')->toArray();
        }
        else{
            array_push($periodId, $request->periodId);
        }


        $accounts = DB::table('acc_ots_account')->where('status',1)->whereIn('branchId_fk',$branchId)->whereIn('periodId_fk',$periodId)->select('id','accNo')->get();

        return response::json($accounts);
    }


    public function getAccDetails(Request $request)
    {
        $accId = $request->accId;

        $account = DB::table('acc_ots_account')->select('id','memberId_fk','accNo','certificateNo','amount','periodId_fk','interestRate','openingBalance','openingDate')->where('id',$accId)->first();

        $memberName = DB::table('acc_ots_member')->where('id',$account->memberId_fk)->value('name');

        $accNature = DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('name');

        $totalInterests = (float) DB::table('acc_ots_interest_details')->where('accId_fk',$accId)->sum('amount');

        $totalPayments = (float) DB::table('acc_ots_payment_details')->where('accId_fk',$accId)->sum('amount');

        $serviceCharge = (float) DB::table('acc_ots_closing_charge')->where('status',1)->orderBy('id','desc')->value('amount');

        $dueAmount = (float) $account->openingBalance + $totalInterests - $totalPayments;

        $payableAmount = (float) $account->amount + $dueAmount - $serviceCharge;

        $openingDate = date('d-m-Y',strtotime($account->openingDate));

        

        $data = array(
            'accId' => $accId,
            'openingDate' => $openingDate,
            'accNo' => $account->accNo,
            'certificateNo' => $account->certificateNo,
            'memberName' => $memberName,
            'accNature' => $accNature,
            'principalAmount' => $account->amount,
            'interestAmount' => $totalInterests,
            'dueAmount' => $dueAmount,
            'serviceCharge' => $serviceCharge,
            'payableAmount' => $payableAmount
        );

        return response::json($data);
        
    }


    public function getPricipalPaymentInfo(Request $request)
    {
        $payment = AccOTSRegisterPrincipalPayment::find($request->paymentId);
        $account = AccOTSRegisterAccount::find($payment->accId_fk);
        $interestDue = $this->getInterestDue($account->id);

        $memberName = DB::table('acc_ots_member')->where('id',$account->memberId_fk)->value('name');
        $accNature = DB::table('acc_ots_period')->where('id',$account->periodId_fk)->value('name');
        $branchLocation = DB::table('gnr_branch')->where('id',$account->branchId_fk)->value('name');

        $bankAccNo = DB::table('acc_account_ledger')->where('id',$payment->ledgerId_fk)->value('name');

        $data = array(
            'account' => $account,
            'memberName' => $memberName,
            'accNo' => $account->accNo,
            'branchLocation' => $branchLocation,
            'certificateNo' => $account->certificateNo,
            'openingDate' => date('d-m-Y',strtotime($account->openingDate)),
            'closingDate' => date('d-m-Y',strtotime($payment->closingDate)),

            'accNature' => $accNature,
            'principalAmount' => number_format($account->amount,2,'.',','),
            'interestDue' => number_format($interestDue,2,'.',','),
            'closingCharge' => number_format($payment->accClosingCharge,2,'.',','),
            'paidAmount' => number_format($payment->amount,2,'.',','),
            'ledgerId' => $payment->ledgerId_fk,
            'bankAccNo' => $bankAccNo,
            'chequeNumber' => $payment->chequeNumber

            );

        return response::json($data);
        
    }

    ///////////////////End Pricipal Payment///////


    /* start OTS Register Period*/

    /*view Period List*/

     public function viewPeriod(){
    
        $accOTSRegisterPeriod = AccOTSRegisterPeriod::all();
        return view('accounting.register.otsRegister.viewOtsPeriod',['accOTSRegisterPeriods'=>$accOTSRegisterPeriod]);  
    }

    /* Add Period Form*/
    public function addPeriod()
    {
        return view('accounting.register.otsRegister.addOtsPeriod');  
    }

/* Store Period */
    public function storeOtsRegisterPeriod(Request $request){

        $rules = array(
              'name' =>'required|unique:acc_ots_period',
              'interestRate' => 'required',
              'months' =>'required|unique:acc_ots_period'
        );

        $attributeNames = array(

              'name'  => 'Period Name',
              'interestRate' => 'Interest Rate ',
              'months'=>'Month'
              
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));          
        } 

        else{                         
            $accOTSRegisterPeriod = new AccOTSRegisterPeriod;
            $accOTSRegisterPeriod->name = $request->name;
            $accOTSRegisterPeriod->interestRate=$request->interestRate;
            $accOTSRegisterPeriod->months =$request->months;
            $accOTSRegisterPeriod->save(); 
            return response::json('success');
        }
    }

    public function deleteOtsRegisterPeriod(Request $request){
         $accOTSRegisterPeriod= AccOTSRegisterPeriod::find($request->id);
         $accOTSRegisterPeriod->delete();
         return response::json('success');
    }

    public function viewOtsRegisterPeriodInfo( Request $request){
        $accOTSRegisterPeriod =  AccOTSRegisterPeriod::find($request->id);
        $data = array(
            'accOTSRegisterPeriod' => $accOTSRegisterPeriod
        );

        return response::json($data);
    }

    public function editOtsRegisterPeriod(Request $request){

        $rules = array(
            'name' => 'required|unique:acc_ots_period,name,'.$request->id,
            'interestRate' => 'required',
            'months' => 'required|unique:acc_ots_period,months,'.$request->id
        );

        $attributeNames = array(
            'name'  => 'Period',
            'interestRate' => ' Interest Rate ',
            'months'=>'Months'              
        );


        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()){
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));          
        } 

        else{                             
            $accOTSRegisterPeriod = 
            AccOTSRegisterPeriod::where('id',$request->id)->first();
            $accOTSRegisterPeriod->name = $request->name;
            $accOTSRegisterPeriod->interestRate=$request->interestRate;
            $accOTSRegisterPeriod->months =$request->months;
            $accOTSRegisterPeriod->save(); 
            return response::json('success');
        }
    }

}

?>