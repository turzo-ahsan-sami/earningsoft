<?php

namespace App\Http\Controllers\inventory\reports;

use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\inventory\InvPurchase;

class InvStockPurchaseReportController extends Controller
{
    public function index(Request $request){

      $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

      $projectId = array();
      $projectTypeId = array();
      $branchId = array();
      $billNoId = array();
     

      //Project
      if ($request->searchProject==null) {        
        
          if ($user_branch_id == 1) {
            $projectSelected = 0;
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

       //Branch
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

       //Bill No
     if ($request->searchBillNo==null) {
        $billSelected = null;

        $billNoId = DB::table('inv_purchase')->pluck('billNo');
      }
      else{
        $billSelected = (int) json_decode($request->searchBillNo);
        array_push($billNoId, DB::table('inv_purchase')->where('id',$request->searchBillNo)->value('billNo'));
      }
 
     

    //Search Method
      if ($request->searchMethod==null) {
        $searchMethodSelected = null;        
      }
      else{
        $searchMethodSelected = (int) json_decode($request->searchMethod);        
      }

      //Fiscal Year
      if($request->fiscalYear==""){
        $today = Carbon::today()->toDateTimeString();
        $fiscalYearSelected = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$today)->where('fyEndDate','>=',$today)->value('id');        
      }
      else{
        $fiscalYearSelected = (int) json_decode($request->fiscalYear);
      }

      //Date From       
      if ($request->dateFrom==null) {
          $dateFromSelected = null;        
      }
      else{
          $dateFromSelected = $request->dateFrom;        
      }

      //Date To      
      if ($request->dateTo==null) {
         $dateToSelected = null;        
      }
      else{
         $dateToSelected = $request->dateTo;        
      }
      
      //Get Start Date and End Date
      if ($searchMethodSelected==null) {
          $today = Carbon::today();
          $currentFiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$today)->where('fyEndDate','>=',$today)->first();
          $startDate = date('Y-m-d', strtotime($currentFiscalYear->fyStartDate));
          $endDate = date('Y-m-d', strtotime($currentFiscalYear->fyEndDate));
      }
      elseif ($searchMethodSelected==1) {
         $startDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate')));
         $endDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate')));
      }
      elseif ($searchMethodSelected==2 || $searchMethodSelected==3) {
        $startDate = date('Y-m-d',strtotime($dateFromSelected));
        $endDate = date('Y-m-d',strtotime($dateToSelected));
      }


        $projects = DB::table('gnr_project')->get();
        $projectTypes = DB::table('gnr_project_type')->whereIn('projectId',$projectId)->get();
        $branches = DB::table('gnr_branch')->whereIn('projectId',$projectId)->orWhere('id',1)->get();
       
        $fiscalYears = DB::table('gnr_fiscal_year')->pluck('name','id');
    
           $billNos = DB::table('inv_purchase')->get();
        if($request->searchBranch=='' || $request->searchBranch==null ) {

           $filteredbranch=DB::table('gnr_branch')->whereIn('projectId',$projectId)->whereIn('projectTypeId',$projectTypeId)->whereIn('id',$branchId)->orWhere('id',1)->pluck('id');

        }
        elseif($request->searchBranch==0) {

           $filteredbranch=DB::table('gnr_branch')->whereIn('projectId',$projectId)->whereIn('projectTypeId',$projectTypeId)->whereIn('id',$branchId)->where('id','!=',1)->pluck('id');
       
        }
        else {

         $filteredbranch=[$request->searchBranch];
        
        }

      $filteredBranches=DB::table('inv_purchase')->whereIn('branchId', $filteredbranch)->pluck('id');

        $purchase = DB::table('inv_purchase')->whereIn('id', $filteredBranches)
                              ->whereIn('billNo',$billNoId)
                              ->where(function ($query) use ($startDate,$endDate){
                                      $query->where('purchaseDate','>=',$startDate)
                                      ->where('purchaseDate','<=',$endDate);
                                    })
                              ->orderBy('purchaseDate','asc')
                              ->get();
   
         //Is it the First Request
        if ($request->fiscalYear==null) {
            $firstRequest = 1;
          }
        else{
            $firstRequest = 0;
        }

            return view('inventory/reports/stockPurchase/viewInvStockPurchaseReport',['projects'=>$projects,'projectTypes'=>$projectTypes,'branches'=>$branches,'billNos'=>$billNos,'fiscalYears'=>$fiscalYears,'startDate'=>$startDate,'endDate'=>$endDate,'projectSelected'=>$projectSelected,'projectTypeSelected'=>$projectTypeSelected,'branchSelected'=>$branchSelected,'billSelected'=>$billSelected,'searchMethodSelected'=>$searchMethodSelected,'fiscalYearSelected'=>$fiscalYearSelected,'dateFromSelected'=>$dateFromSelected,'dateToSelected'=>$dateToSelected,'firstRequest'=>$firstRequest,'purchase'=>$purchase]);
        } 



   
}
