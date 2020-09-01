<?php

/**************************************************
* Programmer: Himel Dey                           *
*  Ambala IT                                      *
*  Topic: OTS Statement Report                    *
**************************************************/

namespace App\Http\Controllers\accounting\reports;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use \stdClass;
use Carbon\Carbon;
use App\Traits\GetSoftwareDate;

// use App\Http\Controllers\accounting\AccAjaxResponseController;

use App\Http\Controllers\accounting\Accounting;



class OtsAccountStatementController extends Controller
{


    public function __construct() {

        // $this->Accounting = new AccAjaxResponseController;
        $this->Accounting = new Accounting;
    }
public function index()
{
  $userBranchId = Auth::user()->branchId;

  $branchList =   array('--Select Branch--')  + $this->Accounting->getBranchList();
  $otsAccount =   array('--Select Account--');

                        //    $this->Accounting->getOTSAccount();
  $filteringArr=array(
                    'userBranchId'          => $userBranchId,

                    'branchList'            => $branchList,

                    'otsAccount'            => $otsAccount
                     );

return view('accounting/reports/OTS/reportFiltering',$filteringArr);
}


public function reportingTable(Request $request)
{
            $userBranchId = Auth::user()->branchId;
            $accountNumber  = (int)$request->filAccountNumber;
            // this account number will give the account id basically not the actual account number
            $Branch         = (int)$request->filBranch;
            $startDateValue     = Carbon::parse($request->filStartDate)->format('Y-m-d');
            $endDateValue       = Carbon::parse($request->filEndDate)->format('Y-m-d');
            $endDateValueTest   = Carbon::parse($request->filEndDate)->subDay()->format('Y-m-d');


                /* opening balance part starts here */
            $openingBalanceAmount = DB::table('acc_ots_account')
                                   ->select('acc_ots_account.amount','acc_ots_account.openingDate','acc_ots_account.openingBalance')
                                   ->where('acc_ots_account.id', '=', $accountNumber)
                                   ->first('acc_ots_account.amount');




       if($openingBalanceAmount->openingDate > $startDateValue )
        { $openingBalanceAmount->name="Account Opening"." ".$openingBalanceAmount->openingDate;
          $status=0;
        }
        else{

            $status=1;
            $openingBalanceAmount->name="Opening Balance";
          }
            $sum=   $openingBalanceAmount->amount+$openingBalanceAmount->openingBalance;
            $debitOpenning=0;
            $creditOpenning=$sum;


            $openingBalanceInterest = DB::table('acc_ots_interest_details')
                                      ->select('amount','generateDate')
                                      ->where('accId_fk', '=', $accountNumber)
                                      ->where(function ($query) use ($startDateValue,$endDateValue){
                                         $query->where('generateDate','<',$startDateValue); })
                                      ->get();
          //The query above is to get all the interest occurances which happaned till day before startdate
           $openingBalancePayment = DB::table('acc_ots_payment_details')
                                              ->select('amount','paymentDate')
                                              ->where('accId_fk', '=', $accountNumber)
                                              ->where(function ($query) use ($startDateValue,$endDateValue){
                                                  $query->where('paymentDate','<',$startDateValue); })
                                              ->get();

       //The query avobe is to get all the payment occurances which happaned till day before startdate


           if($openingBalanceInterest)
           {
             foreach ($openingBalanceInterest as $openingBalanceInterests) {
               // code...here I have added all the generated interests with opening Balance
               $sum= $sum+$openingBalanceInterests->amount;
               $creditOpenning=$creditOpenning+$openingBalanceInterests->amount;
             }
           }



           if($openingBalancePayment)
           {
           foreach ($openingBalancePayment as $openingBalancePayments) {
             // code... here I have substracted all the payments with opening balance
             $sum= $sum-$openingBalancePayments->amount;
             $debitOpenning=$debitOpenning+$openingBalancePayments->amount;
           }
        }


           /* opening balance part ends here */
        // }


        //$dateInterests varriable finds all the dates of interests
         $dateInterests = DB::table('acc_ots_interest_details')
                          ->select('generateDate')
                          ->where('accId_fk', '=', $accountNumber)
                          ->where(function ($query) use ($startDateValue,$endDateValue){
                              $query->where('generateDate','>=',$startDateValue)
                              ->where('generateDate','<=',$endDateValue);
                          })
                          ->get();
        //$datePayments varriable finds all the dates of payments
         $datePayments=  DB::table('acc_ots_payment_details')
                          ->select('acc_ots_payment_details.paymentDate')
                          ->where('acc_ots_payment_details.accId_fk', '=', $accountNumber)
                          ->where(function ($query) use ($startDateValue,$endDateValue){
                              $query->where('paymentDate','>=',$startDateValue)
                              ->where('paymentDate','<=',$endDateValue);
                          })
                          ->get();

      //$traDateArray is used here to get all the dates of both payments and interests since it is difficult, otherwise
      //to know in which date which occurance has been happened. Once we take all the values inside this array we can
      // easily find out occurances just only querying via this array dates
                          $traDateArray = array();
                          foreach ($dateInterests as $dateInterest) {
                          array_push($traDateArray,$dateInterest->generateDate);
                          }
                          foreach ($datePayments as $datePayment) {
                            array_push($traDateArray,$datePayment->paymentDate);
                          }

                        sort($traDateArray);// array sorting is important since I have to search accordingly dates

                        $reportingArr=array(
                              'accountNumber'            =>     $accountNumber,
                              'Branch'                   =>     $Branch,
                              'startDateValue'           =>     $startDateValue,
                              'endDateValue'             =>     $endDateValue,
                              'openingBalanceAmount'     =>  $openingBalanceAmount,
                              'openingBalanceInterest'   => $openingBalanceInterest,
                              'sum'                      =>  $sum,
                              'startDateValue'           => $startDateValue,
                              'endDateValue'             => $endDateValue,
                              'traDateArray'             => $traDateArray,
                              'debitOpenning'            => $debitOpenning,
                              'creditOpenning'           => $creditOpenning,
                              'status'                   => $status
                            );
         return view('accounting/reports/OTS/ots',$reportingArr);
}






public function AccountNumber(Request $request)
{
 $Branch         = (int)$request->branchValue;
 $Branch = $Branch;


 $otsAccount= DB::table('acc_ots_account')
                  ->join('acc_ots_member','acc_ots_account.id','=','acc_ots_member.id')
                  ->select('acc_ots_account.accNo','acc_ots_account.id','acc_ots_member.name')
                  ->where('acc_ots_account.branchId_fk',"=",$Branch )
                  ->get();


  $AccountList=array(
   'otsAccount'  => $otsAccount
  );
  return response()->json($otsAccount);
}


}		//End AccLedgerReportsController
