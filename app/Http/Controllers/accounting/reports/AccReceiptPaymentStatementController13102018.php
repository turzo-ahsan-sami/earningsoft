<?php

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
use Carbon\Carbon;

class AccReceiptPaymentStatementController extends Controller
{

 	public function index(Request $request){

	    $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');
      $user_company_id = Auth::user()->company_id_fk;

      $projectId     = array();
      $projectTypeId = array();
      $branchId      = array();
      $categoryId    = array();
      $productTypeId = array();
      $voucherTypeId = array();

      //Project
      if ($request->searchProject==null) {

          if ($user_branch_id == 1) {
            $projectSelected = 1;
            $projectId = DB::table('gnr_project')->pluck('id');
          }
          else{
            $projectSelected = $user_project_id;
            array_push($projectId, $projectSelected);
          }

      }
      else{
        $projectSelected = (int)json_decode($request->searchProject);
        array_push($projectId, $projectSelected);
      }

      //Project Type
       if ($request->searchProjectType==null) {

        if ($user_branch_id == 1) {
            $projectTypeSelected = 0;
            $projectTypeId = DB::table('gnr_project_type')->pluck('id');
          }
          else{
            $projectTypeSelected = $user_project_type_id;
            array_push($projectTypeId, $projectTypeSelected);
          }
      }
      else{
        $projectTypeSelected = (int) json_decode($request->searchProjectType);
        array_push($projectTypeId, $projectTypeSelected);
      }

      // Branch
      if ($request->searchBranch==null) {

        if ($user_branch_id == 1) {
          $branchSelected = null;
            $branchId = DB::table('gnr_branch')->pluck('id');
          }
          else{
            $branchSelected = $user_branch_id;
            array_push($branchId, $branchSelected);
          }

      }
      else{
        $branchSelected = (int) json_decode($request->searchBranch);

        if ($request->searchBranch==0) {
          $branchId = DB::table('gnr_branch')->where('id','!=',1)->pluck('id');
        }
        else{
          array_push($branchId, $branchSelected);
        }

      }

       //Search Method
      if ($request->searchMethod==null) {
        $searchMethodSelected = 1;
      }
      else{
        $searchMethodSelected = (int) json_decode($request->searchMethod);
      }

      //Fiscal Year
      if($request->fiscalYear==""){
        $today = Carbon::today()->toDateTimeString();
        $fiscalYearSelected = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$today)->where('fyEndDate','>=',$today)->value('id');
        $startDate =  DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
        $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
      }
      else{
        $fiscalYearSelected = (int) json_decode($request->fiscalYear);
         $startDate =  DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
        $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
      }


      //Date From
      if ($request->dateFrom==null) {
          $dateFromSelected = null;
          $startDate = null;
          if ($searchMethodSelected==1) {
              $startDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
              $dateFromSelected = $startDate;
          }
      }
      else{
          $dateFromSelected = $request->dateFrom;
          $startDate = date('Y-m-d', strtotime($request->dateFrom));
      }

      // *********** extra line
      if($searchMethodSelected==2){
          $startDate = '2017-07-01';
          $dateFromSelected = $startDate;
      }
      // dd($startDate);
      // *********** extra line

      //Date To
      if ($request->dateTo==null) {
        $dateToSelected = null;
        $endDate = null;
          if ($searchMethodSelected==1) {
          $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
          $dateToSelected = $endDate;
        }
      }
      else{
        $dateToSelected = $request->dateTo;
        $endDate = date('Y-m-d', strtotime($request->dateTo));
      }

      $projects = DB::table('gnr_project')->get();
      $projectTypes = DB::table('gnr_project_type')->whereIn('projectId',$projectId)->get();
      $branches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->get();
      $fiscalYears = DB::table('gnr_fiscal_year')->orderBy('fyStartDate','desc')->pluck('name','id');

      if($branchSelected===0) {
      	$newBranches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->where('id','!=',1)->pluck('id')->toArray();
      }
      elseif($branchSelected==null){
      	$newBranches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->pluck('id')->toArray();
      	//$newBranches = DB::table('gnr_branch')->where('id',1)->pluck('id')->toArray();
      }
      else{
      	$newBranches = DB::table('gnr_branch')->where('id',$branchSelected)->pluck('id')->toArray();
      }

       //Is it the First Request    /// Round Up
      if ($request->roundUp==null) {
        $firstRequest = 1;
        $roundUpSelected = 1;
      }
      else{
        $firstRequest = 0;
        $roundUpSelected = $request->roundUp;
      }


      //Depth Level
      if ($request->depthLevel==null) {
      	$depthLevelSelected = null;
        $depthLevel = 5;
      }
      else{
      	$depthLevelSelected = $request->depthLevel;
        $depthLevel = $request->depthLevel;
      }

      //With or Without zeros
      if ($request->withZero==null) {
        $withZeroSelected = null;
      }
      else{
        $withZeroSelected = $request->withZero;
      }

      // Voucher Type
      // Without JV
      if ($request->voucherType==null) {
        $voucherTypeSelected = null;
        $voucherTypeId = [1,2,4,5];
      }
      // WIth JV
      elseif($request->voucherType==1){
        $voucherTypeSelected = $request->voucherType;
        $voucherTypeId = [1,2,3,4,5];
      }
      // Only JV
      elseif($request->voucherType==2){
        $voucherTypeSelected = $request->voucherType;
        $voucherTypeId = [3,5];
      }


      if ($searchMethodSelected==1) {
        $startDate = $startDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate');
        $endDate = DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate');
      }

      if(isset($request->searchProject)){
          $fundTvoucherIds = DB::table('acc_voucher')->where('voucherTypeId',5)->pluck('id')->toArray();


      $allLedgers = DB::table('acc_account_ledger')->select("id","projectBranchId")->get();



        $firstLedgerMatchedId=array();

        foreach ($allLedgers as $singleLedger) {
            $splitArray=str_replace(array('[', ']', '"', ''), '',  $singleLedger->projectBranchId);

            $array_length=substr_count($splitArray, ",");
            $arrayProjects=array();

            for($i=0; $i<$array_length+1; $i++){

                $splitArrayFirstValue = explode(",", $splitArray);

                $splitArraySecondValue = explode(":", $splitArrayFirstValue[$i]);
                $firstIndexValue=(int)$splitArraySecondValue[0];
                $secondIndexValue=(int)$splitArraySecondValue[1];

                if($firstIndexValue==0){

                    if($secondIndexValue==0){
                        array_push($firstLedgerMatchedId, $singleLedger->id);
                    }
                }else{

                        if($firstIndexValue==$projectSelected){
                          if ($secondIndexValue==0) {
                            array_push($firstLedgerMatchedId, $singleLedger->id);
                          }
                          if ($branchSelected!=null || $branchSelected!=0) {
                            if ($secondIndexValue==$branchSelected) {
                              array_push($firstLedgerMatchedId, $singleLedger->id);
                            }
                          }
                          else{
                            foreach ($newBranches as $selectedBranch) {
                            if ($secondIndexValue==$selectedBranch) {
                              array_push($firstLedgerMatchedId, $singleLedger->id);
                            }
                          }
                          }

                        }


                }
            }   //for
        }       //foreach
        ///////////////////////

        if (sizeof($firstLedgerMatchedId)<=0) {
          array_push($firstLedgerMatchedId, 0);
        }


        /////////// Get ledgers which and who's parents are in Cash or Bank type account and in the selected project and branch
        /*$secondLedgerMatchedId = array();
        $secondLedgers = DB::table('acc_account_ledger')->whereIn('id',$firstLedgerMatchedId)->whereIn('accountTypeId',[4,5])->where('isGroupHead',0)->select("id","parentId")->get();
        foreach ($secondLedgers as $secondLedger) {
          array_push($secondLedgerMatchedId, $secondLedger->id);

          $firstLevelParent = DB::table('acc_account_ledger')->where('id',$secondLedger->parentId)->select("id","parentId")->first();
          array_push($secondLedgerMatchedId, $firstLevelParent->id);

          $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
          array_push($secondLedgerMatchedId, $secondLevelParent->id);

          $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
          array_push($secondLedgerMatchedId, $thirdLevelParent->id);

          $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id")->first();
          array_push($secondLedgerMatchedId, $fourthLevelParent->id);
        }*/
        //////////


        //Get the ledgers which are in selected project and branch and types of Cash or Bank
        $secondLedgerMatchedId = DB::table('acc_account_ledger')->whereIn('id',$firstLedgerMatchedId)->whereIn('accountTypeId',[4,5])->pluck('id')->toArray();
        //



         //previous fiscal year id
        $currentFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyStartDate');

        $dateToCompare = date('Y-m-d', strtotime('-1 day', strtotime($currentFiscalYearStartDate)));
        $previousfiscalYearId = DB::table('gnr_fiscal_year')->where('fyEndDate',$dateToCompare)->value('id');
        if ($previousfiscalYearId!=null) {
          $previousfiscalYearStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyStartDate');
        $previousfiscalYearEndDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyEndDate');
        }


        /// for fund transfers get the vouchers
        $bankNCashLedgerIds = DB::table('acc_account_ledger')
                                ->where(function ($query) {
                                    $query->where('accountTypeId',4)
                                          ->orWhere('accountTypeId',5);
                                })
                                ->where('isGroupHead',0)
                                ->pluck('id')
                                ->toArray();
        // Without JV

        if ($request->voucherType==null) {
          $excludeVoucherIds = DB::table('acc_voucher_details')
                              ->whereIn('voucherId',$fundTvoucherIds)
                              ->whereNotIn('debitAcc',$bankNCashLedgerIds)
                              ->whereNotIn('creditAcc',$bankNCashLedgerIds)
                              ->pluck('voucherId')
                              ->toArray();
        }
        // With JV
        elseif ($request->voucherType==1) {
           $excludeVoucherIds = [];
        }
        // Only JV
        else{
          $excludeVoucherIds = DB::table('acc_voucher_details')
                              ->whereIn('voucherId',$fundTvoucherIds)
                              ->where(function($query) use ($bankNCashLedgerIds){
                                $query->whereIn('debitAcc',$bankNCashLedgerIds)
                                    ->orWhereIn('creditAcc',$bankNCashLedgerIds);
                              })
                              ->pluck('voucherId')
                              ->toArray();
        }


        ////// Get Vouchers
        if (!is_null($startDate) && !is_null($endDate)) {

          $vouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();

          $thisMonthStartDate = date('Y-m-01', strtotime($endDate));
          $thisMonthVoucherIds = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$thisMonthStartDate)->where('voucherDate','<=',$endDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
        }
        else{
          $vouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
          $thisMonthVoucherIds = [0];
        }

        if ($previousfiscalYearId!=null) {
          $previousfiscalYearVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$previousfiscalYearStartDate)->where('voucherDate','<=',$previousfiscalYearEndDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
        }
        else{
          $previousfiscalYearVouchers = [0];
        }



        if (!is_null($startDate) && !is_null($endDate)) {
        $fiscalYearId = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('id');
        $fiscalEndDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyEndDate');
      }
      else{
        $fiscalYearId=1;
        $fiscalEndDate="2017-06-30";
      }

        if (!is_null($startDate) && !is_null($endDate)) {
          $openingVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','<',$startDate)->where('voucherDate','>',$fiscalEndDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
        }
        else{
          $openingVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();
        }
        ////// End Get Vouchers


        $jvouchers = DB::table('acc_voucher')->whereIn('id',$vouchers)->where('voucherTypeId',3)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();



        //Get the legders which are in Receipt
       /* $tempRecpeitLegderIds = array();
        $tempReceiptCreditLedgerIds = DB::table('acc_voucher_details')->whereNotIn('voucherId',$jvouchers)->whereIn('debitAcc',$secondLedgerMatchedId)->pluck('creditAcc')->toArray() + DB::table('acc_voucher_details')->whereIn('voucherId',$jvouchers)->whereIn('debitAcc',$secondLedgerMatchedId)->pluck('debitAcc')->toArray();
        $receiptCreditLedgerIds = array_unique($tempReceiptCreditLedgerIds);
        foreach ($receiptCreditLedgerIds as $receiptCreditLedgerId) {
          array_push($tempRecpeitLegderIds, $receiptCreditLedgerId);//5

          $firstLevelParent = DB::table('acc_account_ledger')->where('id',$receiptCreditLedgerId)->select("id","parentId")->first();
          //array_push($tempRecpeitLegderIds, $firstLevelParent->parentId);

          $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
          array_push($tempRecpeitLegderIds, $secondLevelParent->id);//4

          $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
          array_push($tempRecpeitLegderIds, $thirdLevelParent->id);//3

          $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id","parentId")->first();
          array_push($tempRecpeitLegderIds, $fourthLevelParent->id);//2

          $fifthLevelParent = DB::table('acc_account_ledger')->where('id',$fourthLevelParent->parentId)->select("id")->first();
          array_push($tempRecpeitLegderIds, $fifthLevelParent->id);//1
        }*/
        //$recpeitLegderIds = array_unique($tempRecpeitLegderIds);
        $recpeitLegderIds = DB::table('acc_account_ledger')->whereNotIn('accountTypeId',[13,4,5])->whereNotIn('id',[285,348])->pluck('id')->toArray();
        //$recpeitLegderIds = DB::table('acc_account_ledger')->pluck('id')->toArray();
        //////


        //Get the legders which are in Payment
       /* $tempPaymentLegderIds = array();
        $tempPaymentDebitLedgerIds = DB::table('acc_voucher_details')->whereNotIn('voucherId',$jvouchers)->whereIn('creditAcc',$secondLedgerMatchedId)->pluck('debitAcc')->toArray() + DB::table('acc_voucher_details')->whereIn('voucherId',$jvouchers)->whereIn('creditAcc',$secondLedgerMatchedId)->pluck('creditAcc')->toArray();
        $receiptDebitLedgerIds = array_unique($tempPaymentDebitLedgerIds);
        foreach ($receiptDebitLedgerIds as $receiptDebitLedgerId) {
          array_push($tempPaymentLegderIds, $receiptDebitLedgerId);//5

          $firstLevelParent = DB::table('acc_account_ledger')->where('id',$receiptDebitLedgerId)->select("id","parentId")->first();
          //array_push($tempRecpeitLegderIds, $firstLevelParent->parentId);

          $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
          array_push($tempPaymentLegderIds, $secondLevelParent->id);//4

          $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
          array_push($tempPaymentLegderIds, $thirdLevelParent->id);//3

          $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id","parentId")->first();
          array_push($tempPaymentLegderIds, $fourthLevelParent->id);//2

          $fifthLevelParent = DB::table('acc_account_ledger')->where('id',$fourthLevelParent->parentId)->select("id")->first();
          array_push($tempPaymentLegderIds, $fifthLevelParent->id);//1
        }*/
        //$paymentLegderIds = array_unique($tempPaymentLegderIds);
        $paymentLegderIds = DB::table('acc_account_ledger')->whereNotIn('accountTypeId',[12,4,5])->whereNotIn('id',[285,348])->pluck('id')->toArray();
        //$paymentLegderIds = DB::table('acc_account_ledger')->pluck('id')->toArray();
        //////






        //This hold the Ledger ids which are in the selected project and branch and type of Cash and Bank
        //$thirdMatchedLedgerId = array_intersect($firstLedgerMatchedId,$secondLedgerMatchedId);






         ///////////////////////
        $voucherMachedDebitLedgerId = array();
        $voucherMachedCreditLedgerId = array();
        //$tempSecondLedgerMatchedId = array();
         $voucherMachedDebitLedgerIds = DB::table('acc_voucher_details')->whereIn('voucherId',$vouchers)->pluck('debitAcc')->toArray();
         $voucherMachedCreditLedgerIds = DB::table('acc_voucher_details')->whereIn('voucherId',$vouchers)->pluck('creditAcc')->toArray();
         //$tempSecondLedgerMatchedId = $voucherMachedDebitLedgerIds + $voucherMachedCreditLedgerIds;

         foreach ($voucherMachedDebitLedgerIds as $secondDebitLedger) {
           $matchedLedger = DB::table('acc_account_ledger')->where('id',$secondDebitLedger)->select('id','parentId')->first();

            array_push($voucherMachedDebitLedgerId, $matchedLedger->id);

          $firstLevelParent = DB::table('acc_account_ledger')->where('id',$matchedLedger->parentId)->select("id","parentId")->first();
          array_push($voucherMachedDebitLedgerId, $firstLevelParent->id);

          $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
          array_push($voucherMachedDebitLedgerId, $secondLevelParent->id);

          $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
          array_push($voucherMachedDebitLedgerId, $thirdLevelParent->id);

          $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id")->first();
          array_push($voucherMachedDebitLedgerId, $fourthLevelParent->id);
         }

          foreach ($voucherMachedCreditLedgerIds as $secondCeditLedger) {

          $matchedLedger = DB::table('acc_account_ledger')->where('id',$secondCeditLedger)->select('id','parentId')->first();

          array_push($voucherMachedCreditLedgerId, $matchedLedger->id);

          $firstLevelParent = DB::table('acc_account_ledger')->where('id',$matchedLedger->parentId)->select("id","parentId")->first();
          array_push($voucherMachedCreditLedgerId, $firstLevelParent->id);

          $secondLevelParent = DB::table('acc_account_ledger')->where('id',$firstLevelParent->parentId)->select("id","parentId")->first();
          array_push($voucherMachedCreditLedgerId, $secondLevelParent->id);

          $thirdLevelParent = DB::table('acc_account_ledger')->where('id',$secondLevelParent->parentId)->select("id","parentId")->first();
          array_push($voucherMachedCreditLedgerId, $thirdLevelParent->id);

          $fourthLevelParent = DB::table('acc_account_ledger')->where('id',$thirdLevelParent->parentId)->select("id")->first();
          array_push($voucherMachedCreditLedgerId, $fourthLevelParent->id);
         }
        ///////////////////////
         $thirdMatchedLedgerId = [0];

         $debitLedgerMatchedId = array_intersect($thirdMatchedLedgerId,$voucherMachedDebitLedgerId);
         $creditLedgerMatchedId = array_intersect($thirdMatchedLedgerId,$voucherMachedCreditLedgerId);
         //$ledgerMatchedId = array_intersect($firstLedgerMatchedId,$tempSecondLedgerMatchedId);
         //$ledgerMatchedId = $secondLedgerMatchedId;


         $ledgers = DB::table('acc_account_ledger')->where('parentId',0)->orderBy('ordering', 'asc')->get();

         //Cash Type Ledgers
        $cashTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',4)->pluck('id')->toArray();

        //Bank Type Ledgers
        $bankTypeLedgers = DB::table('acc_account_ledger')->where('accountTypeId',5)->pluck('id')->toArray();


         //Get data for Opening Balance for the Current Year Filteing Option
         $thisMonthStartDate = date('Y-m-01', strtotime($endDate));
         $thisFiscalYearStartDate = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$startDate)->value('fyStartDate');

         $thisMonthVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','<',$thisMonthStartDate)->where('voucherDate','>=',$thisFiscalYearStartDate)/*->whereNotIn('id',$excludeVoucherIds)*/->pluck('id')->toArray();

         // echo "<br><br><br><br><br>";
         // // print_r(count($thisMonthVouchers));
         // echo $projectSelected;
         // echo "<br>";
         // print_r($projectTypeId);
         // echo "<br>";

         // print_r($voucherTypeId);
         // echo "$thisMonthStartDate<br>";
         // echo "$thisFiscalYearStartDate<br>";

         $cashTypeMatchedDebitLedgerId = array_intersect($debitLedgerMatchedId,$cashTypeLedgers);

         $thisMonthTransactionOfCashTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

         // echo $thisMonthTransactionOfCashTypeDebit;
         // print_r($cashTypeLedgers);


         $thisMonthTransactionOfCashTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');

         // echo $thisMonthTransactionOfCashTypeCredit;

          $bankTypeMatchedDebitLedgerId = array_intersect($debitLedgerMatchedId,$bankTypeLedgers);

         $thisMonthTransactionOfBankTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');

         // echo $thisMonthTransactionOfBankTypeDebit;

         $thisMonthTransactionOfBankTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisMonthVouchers)->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');

         // echo $thisMonthTransactionOfBankTypeCredit;
         //////


         //Get data for Opening Balance for the Date Range Filteing Option
         if ($previousfiscalYearId!=null) {
           $thisperoidStartDate = DB::table('gnr_fiscal_year')->where('id',$previousfiscalYearId)->value('fyEndDate');

         $thisPeriodVouchers = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>',$thisperoidStartDate)->where('voucherDate','<',$startDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();

         $thisPeriodVoucherIds = DB::table('acc_voucher')->where('projectId',$projectSelected)->whereIn('projectTypeId',$projectTypeId)->whereIn('branchId',$branchId)->whereIn('voucherTypeId',$voucherTypeId)->where('voucherDate','>=',$startDate)->where('voucherDate','<=',$endDate)->whereNotIn('id',$excludeVoucherIds)->pluck('id')->toArray();

         }else{
          $thisPeriodVouchers = [0];
          $thisPeriodVoucherIds = [0];
         }


          $thisPeriodTransactionOfCashTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('debitAcc',$cashTypeLedgers)->sum('amount');

          $thisPeriodTransactionOfCashTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('creditAcc',$cashTypeLedgers)->sum('amount');
          $thisPeriodTransactionOfBankTypeDebit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('debitAcc',$bankTypeLedgers)->sum('amount');
          $thisPeriodTransactionOfBankTypeCredit = DB::table('acc_voucher_details')->whereIn('voucherId',$thisPeriodVouchers)->whereIn('creditAcc',$bankTypeLedgers)->sum('amount');
         //////
         ///

       $data = array(
          'ledgers' => $ledgers,
          'projects'=>$projects,
          'projectTypes'=>$projectTypes,
          'branches'=>$branches,
          'startDate'=>$startDate,
          'endDate'=>$endDate,
          'projectSelected'=>$projectSelected,
          'projectTypeSelected'=>$projectTypeSelected,
          'branchSelected'=>$branchSelected,
          'dateFromSelected'=>$dateFromSelected,
          'dateToSelected'=>$dateToSelected,
          'firstRequest'=>$firstRequest,
          'roundUpSelected'=>$roundUpSelected,
          'depthLevel'=>$depthLevel,
          'depthLevelSelected'=>$depthLevelSelected,
          'debitLedgerMatchedId'=>$debitLedgerMatchedId,
          'creditLedgerMatchedId'=>$creditLedgerMatchedId,
          'newBranches'=>$newBranches,
          'vouchers'=>$vouchers,
          'openingVouchers'=>$openingVouchers,
          'fiscalYearId'=>$fiscalYearId,
          'withZeroSelected'=>$withZeroSelected,
          'searchMethodSelected'=>$searchMethodSelected,
          'fiscalYears'=>$fiscalYears,
          'fiscalYearSelected'=>$fiscalYearSelected,
          'voucherTypeSelected'=>$voucherTypeSelected,
          'thisMonthTransactionOfCashTypeDebit'=>$thisMonthTransactionOfCashTypeDebit,
          'thisMonthTransactionOfCashTypeCredit'=>$thisMonthTransactionOfCashTypeCredit,
          'thisMonthTransactionOfBankTypeDebit'=>$thisMonthTransactionOfBankTypeDebit,
          'thisMonthTransactionOfBankTypeCredit'=>$thisMonthTransactionOfBankTypeCredit,
          'thisPeriodTransactionOfCashTypeDebit'=>$thisPeriodTransactionOfCashTypeDebit,
          'thisPeriodTransactionOfCashTypeCredit'=>$thisPeriodTransactionOfCashTypeCredit,
          'thisPeriodTransactionOfBankTypeDebit'=>$thisPeriodTransactionOfBankTypeDebit,
          'thisPeriodTransactionOfBankTypeCredit'=>$thisPeriodTransactionOfBankTypeCredit,
          'previousfiscalYearVouchers'=>$previousfiscalYearVouchers,
          'previousfiscalYearId'=>$previousfiscalYearId,
          'thisPeriodVouchers'=>$thisPeriodVouchers,
          'thisMonthVoucherIds'=>$thisMonthVoucherIds,
          'thisPeriodVoucherIds'=>$thisPeriodVoucherIds,
          'projectTypeId'=>$projectTypeId,
          'branchId'=>$branchId,
          'recpeitLegderIds'=>$recpeitLegderIds,
          'paymentLegderIds'=>$paymentLegderIds ,
          'user_company_id' => $user_company_id
       );
      }else{
        $data = array(
          'projects'=>$projects,
          'projectSelected'=>$projectSelected,
          'projectTypes'=>$projectTypes,
          'branches'=>$branches,
          'branchSelected'=>$branchSelected,
          'voucherTypeSelected'=>$voucherTypeSelected,
          'roundUpSelected'=>$roundUpSelected,
          'depthLevel'=>$depthLevel,
          'depthLevelSelected'=>$depthLevelSelected,
          'withZeroSelected'=>$withZeroSelected,
          'searchMethodSelected'=>$searchMethodSelected,
          'fiscalYears'=>$fiscalYears,
          'fiscalYearSelected'=>$fiscalYearSelected,
          'dateFromSelected'=>$dateFromSelected,
          'dateToSelected'=>$dateToSelected,
          'firstRequest'=>$firstRequest,
          'projectTypeSelected'=>$projectTypeSelected,

       );
      }



		return view('accounting.reports.receiptPaymentStatement',$data);




	}



}		//End AccLedgerReportsController
