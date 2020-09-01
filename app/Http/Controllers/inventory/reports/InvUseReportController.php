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

class InvUseReportController extends Controller
{
    public function index(Request $request){

      $user_branch_id = Auth::user()->branchId;
      $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectId');
      $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->value('projectTypeId');

      $projectId = array();
      $projectTypeId = array();
      $branchId = array();
      $categoryId = array();
      $subCategoryId = array();
      $productId = array();
     

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

      //Category
      if ($request->searchCategory==null) {
        $categorySelected = null;
        $categoryId = DB::table('inv_product_category')->pluck('id');
      }
      else{
        $categorySelected = (int) json_decode($request->searchCategory);
        array_push($categoryId, $categorySelected);
      }

       //Sub Category
      if ($request->searchSubCategory==null) {
         $subCategorySelected = null;
         $subCategoryId = DB::table('inv_product_sub_category')->pluck('id');
      }
      else{
        $subCategorySelected = (int) json_decode($request->searchSubCategory);
        array_push($subCategoryId, $subCategorySelected);
      }

      //Product
      if ($request->searchProduct==null) {
        $productSelected = null;
        $productId = DB::table('inv_product')->pluck('id');
      }
      else{
        $productSelected = (int) json_decode($request->searchProduct);
        array_push($productId, $productSelected);
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
        
        $categories = DB::table('inv_product_category')->get();
        $subCategories = DB::table('inv_product_sub_category')->whereIn('productCategoryId',$categoryId)->get();
        $products = DB::table('inv_product')->whereIn('categoryId',$categoryId)->get();
        
        $fiscalYears = DB::table('gnr_fiscal_year')->pluck('name','id');
    

        if($request->searchBranch=='' || $request->searchBranch==null ) {

           $filteredbranch=DB::table('gnr_branch')->whereIn('projectId',$projectId)->whereIn('projectTypeId',$projectTypeId)->whereIn('id',$branchId)->orWhere('id',1)->pluck('id');

        }
        elseif($request->searchBranch==0) {

           $filteredbranch=DB::table('gnr_branch')->whereIn('projectId',$projectId)->whereIn('projectTypeId',$projectTypeId)->whereIn('id',$branchId)->where('id','!=',1)->pluck('id');
       
        }
        else {

         $filteredbranch=[$request->searchBranch];
        
        }

        $filteredBranches=DB::table('inv_tra_use')->whereIn('branchId', $filteredbranch)->pluck('id');

        $filteredProduct=DB::table('inv_product')->whereIn('categoryId',$categoryId)->whereIn('subCategoryId',$subCategoryId)->whereIn('id',$productId)->pluck('id');

        $filteredUseProduct=DB::table('inv_tra_use_details')->whereIn('productId',$filteredProduct)->pluck('id');

       $filteredUseDetails = DB::table('inv_tra_use_details')->whereIn('useId',$filteredBranches)->pluck('id');
       
            $useDetails = DB::table('inv_tra_use_details')->whereIn('id', $filteredUseProduct)
                            ->whereIn('id',$filteredUseDetails)
                            ->where(function ($query) use ($startDate,$endDate){
                                      $query->where('createdDate','>=',$startDate)
                                      ->where('createdDate','<=',$endDate);
                                    })
                              ->orderBy('createdDate','asc')
                              ->get();
   
         //Is it the First Request
        if ($request->fiscalYear==null) {
            $firstRequest = 1;
          }
        else{
            $firstRequest = 0;
        }

            return view('inventory/reports/useReport/viewInvUseReport',['projects'=>$projects,'projectTypes'=>$projectTypes,'branches'=>$branches,'categories'=>$categories,'subCategories'=>$subCategories,'products'=>$products,'fiscalYears'=>$fiscalYears,'startDate'=>$startDate,'endDate'=>$endDate,'projectSelected'=>$projectSelected,'projectTypeSelected'=>$projectTypeSelected,'branchSelected'=>$branchSelected,'categorySelected'=>$categorySelected,'subCategorySelected'=>$subCategorySelected,'productSelected'=>$productSelected,'searchMethodSelected'=>$searchMethodSelected,'fiscalYearSelected'=>$fiscalYearSelected,'dateFromSelected'=>$dateFromSelected,'dateToSelected'=>$dateToSelected,'firstRequest'=>$firstRequest,'useDetails'=>$useDetails]);
        } 



    public function onChangeCategory(Request $request) {

        $productCategoryId = (int)json_decode($request->productCategoryId);
        if($request->productCategoryId==""){
             $subCategories =  DB::table('inv_product_sub_category')->select('id','name')->get();
             $product =  DB::table('inv_product')->select('id','name')->get();
        }
        else{
            $subCategories =  DB::table('inv_product_sub_category')->where('productCategoryId',$productCategoryId)->select('id','name')->get();
            $product =  DB::table('inv_product')->where('categoryId',$productCategoryId)->select('id','name')->get();
            
        }

        $data = array(            
            'subCategories' => $subCategories,
            'product'       => $product
            
        );
        return response()->json($data);
    }

    public function onChangeSubCategory(Request $request) {

        $productSubCategoryId = (int)json_decode($request->productSubCategoryId);
        if($request->productSubCategoryId==""){
             
             $product =  DB::table('inv_product')->select('id','name')->get();
        }
        else{
            
            $product =  DB::table('inv_product')->where('subCategoryId',$productSubCategoryId)->select('id','name')->get();
            
        }

        $data = array(            
            'product'       => $product
            
        );
        return response()->json($data);
    }

}
