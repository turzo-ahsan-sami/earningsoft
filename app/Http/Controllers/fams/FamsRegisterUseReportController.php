<?php

namespace App\Http\Controllers\fams;

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

class FamsRegisterUseReportController extends Controller
{  



  public function index(Request $request)
  {


    //Get Start Date and End Date
      if ($request->searchMethod==null) {
         $today = Carbon::today();
        $currentFiscalYear = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$today)->where('fyEndDate','>=',$today)->first();
         $startDate = date('Y-m-d', strtotime($currentFiscalYear->fyStartDate));
         $endDate = date('Y-m-d', strtotime($currentFiscalYear->fyEndDate));
      }
      elseif ($request->searchMethod==1) {
        $startDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$request->fiscalYear)->value('fyStartDate')));
        $endDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$request->fiscalYear)->value('fyEndDate')));
      }
      elseif ($request->searchMethod==2 || $request->searchMethod==3) {
        $startDate = date('Y-m-d',strtotime($request->dateFrom));
        $endDate = date('Y-m-d',strtotime($request->dateTo));
      }



    $productList = DB::table('fams_product');
    if ($request->searchProject!='') {
      $productList = $productList->where('projectId',$request->searchProject);
    }
    if ($request->searchProjectType!='') {
      $productList = $productList->where('projectTypeId',$request->searchProjectType);
    }
    if ($request->searchBranch!='') {
      if ($request->searchBranch==0) {
        $productList = $productList->where('branchId','!=',1);
      }
      else{
        $productList = $productList->where('branchId',$request->searchBranch);
      }      
    }
    if ($request->searchCategory!='') {
      $productList = $productList->where('categoryId',$request->searchCategory);
    }
    if ($request->searchProductType!='') {
      $productList = $productList->where('productTypeId',$request->searchProductType);
    }
    
    $productList = $productList/*->where('purchaseDate','>=',$startDate)->where('purchaseDate','<=',$endDate)*/->pluck('id')->toArray();



    $useAllProducts = DB::table('fams_tra_use_details')->whereIn('productId',$productList)->where('useDate','>=',$startDate)->where('useDate','<=',$endDate)->get();

    $lastUseList = array();
    foreach ($useAllProducts as $key => $useProduct) {
      $lastUse = DB::table('fams_tra_use_details')->where('productId',$useProduct->productId)->orderBy('useDate','desc')->value('id');
      array_push($lastUseList, $lastUse);
    }


    $useProducts =  DB::table('fams_tra_use_details')->whereIn('id',$lastUseList)->get();
    


    

     return view('fams/reports/view_register_use_report',['useProducts'=>$useProducts,'startDate'=>$startDate,'endDate'=>$endDate]);
    
  }


























     public function index2(Request $request){


      $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

      $projectId = array();
      $projectTypeId = array();
      $branchId = array();
      $categoryId = array();
      $productTypeId = array();

      //Project
      if ($request->searchProject==null) {        
        
          if ($user_branch_id == 1) {
            $projectSelected = 0;
            $projectId = DB::table('gnr_project')->pluck('id')->toArray();
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
            $projectTypeId = DB::table('gnr_project_type')->pluck('id')->toArray();
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

      //Category
      if ($request->searchCategory==null) {
        $categorySelected = null;
        $categoryId = DB::table('fams_product_category')->pluck('id');
      }
      else{
        $categorySelected = (int) json_decode($request->searchCategory);
        array_push($categoryId, $categorySelected);
      }

      //Product Type
      if ($request->searchProductType==null) {
        $productTypeSelected = null;
        $productTypeId = DB::table('fams_product_type')->pluck('id');
      }
      else{
        $productTypeSelected = (int) json_decode($request->searchProductType);
        array_push($productTypeId, $productTypeSelected);
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
      $categories = DB::table('fams_product_category')->get();
      $productTypes = DB::table('fams_product_type')->whereIn('productCategoryId',$categoryId)->get();
      $fiscalYears = DB::table('gnr_fiscal_year')->pluck('name','id');
       

      //Is it the First Request
      if ($request->fiscalYear==null) {
        $firstRequest = 1;
      }
      else{
        $firstRequest = 0;
      }

      $prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');

       return view('fams/reports/view_register_use_report',['branches'=>$branches,'projectId'=>$projectId,'projectTypeId'=>$projectTypeId,'projects'=>$projects,'projectTypes'=>$projectTypes,'branchIds'=>$branchId,'categories'=>$categories,'productTypes'=>$productTypes,'fiscalYears'=>$fiscalYears,'startDate'=>$startDate,'endDate'=>$endDate,'projectSelected'=>$projectSelected,'projectTypeSelected'=>$projectTypeSelected,'branchSelected'=>$branchSelected,'categorySelected'=>$categorySelected,'productTypeSelected'=>$productTypeSelected,'searchMethodSelected'=>$searchMethodSelected,'fiscalYearSelected'=>$fiscalYearSelected,'dateFromSelected'=>$dateFromSelected,'dateToSelected'=>$dateToSelected,'firstRequest'=>$firstRequest,'categoryId'=>$categoryId,'productTypeId'=>$productTypeId,'prefix'=>$prefix]);



        
     }   

}
