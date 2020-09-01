<?php

namespace App\Http\Controllers\accounting\advRegReports;

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
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;

class AccAdvRegReportController extends Controller {

    public function index(Request $request) {

    
        $registerType = DB::table('acc_adv_register_type')->get();
        $project = DB::table('gnr_project')->get();
        $projectType = DB::table('gnr_project_type')->get();
  
     
        $advRegisterTypeArray = array();
        $projectArray = array();
        $projectTypeArray = array();
     

        if($request->searchproject==null) {

            $projectSelected = null;
            $projectArray = DB::table('gnr_project')->pluck('id')->toArray();
        }
        else
        {
            $projectSelected = $request->searchProject;
            array_push($projectArray,$request->searchproject);
        }

        if($request->searchProjectType==null) {

            $projectTypeSelected = null;
            $projectTypeArray= DB::table('gnr_project_type')->pluck('id')->toArray();
        }
        else{
            $projectTypeSelected = $request->searchProjectType;
            array_push($projectTypeArray,$request->searchProjectType);

        }
        //Search AdvRegisterType

        if ($request->searchRegisterType==null) {

            $regTypeSelected = null;
            $advRegisterTypeArray= DB::table('acc_adv_register_type')->pluck('id')->toArray();
                   
        }
        else{

            $regTypeSelected = $request->searchRegisterType;
            array_push($advRegisterTypeArray,$request->searchRegisterType);
                
      }


       //Search AdvRegisterType

        $accAdvRegister = DB::table('acc_adv_register');


       //Search AdvRegister Category

        $categorySelected=null;

        if($request->searchcategory==1) {
            $accAdvRegister=$accAdvRegister->where('houseOwnerId','>',0);
            $categorySelected =$request->searchcategory;   
        }
        else if($request->searchcategory==2){
            $accAdvRegister=$accAdvRegister->where('supplierId','>',0);
            $categorySelected =$request->searchcategory;

        }
        else if($request->searchcategory==3){
            $accAdvRegister=$accAdvRegister->where('employeeId','>',0);
            $categorySelected =$request->searchcategory;
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
//$today = Carbon::today()->toDateTimeString();
            $startDate = date('Y-m-d', strtotime('2000-01-01'));
            $endDate = date("Y-m-d");
        
            $fiscalYearSelected = DB::table('gnr_fiscal_year')->where('fyStartDate','<=',$startDate)->where('fyEndDate','>=',$endDate)->value('id');        
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
            $startDate = date('Y-m-d', strtotime('2000-01-01'));
            $endDate = date("Y-m-d");

        }
        elseif ($searchMethodSelected==1) {

            $startDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyStartDate')));
            $endDate = date('Y-m-d', strtotime(DB::table('gnr_fiscal_year')->where('id',$fiscalYearSelected)->value('fyEndDate')));

        }
        elseif ($searchMethodSelected==2 || $searchMethodSelected==3) {
            $startDate = date('Y-m-d',strtotime($dateFromSelected));
            $endDate = date('Y-m-d',strtotime($dateToSelected));
        }

        $accAdvRegister =$accAdvRegister->where('advPaymentDate','>=',$startDate)->where('advPaymentDate','<=',$endDate)->whereIn('projectId',$projectArray)->whereIn('projectTypeId',$projectTypeArray)->whereIn('advRegType',$advRegisterTypeArray)->get();

  
          //hide Row
        $filteredRegTypeArray = array();
        foreach ($accAdvRegister as $obj) {
            array_push($filteredRegTypeArray,$obj->advRegType);
                    
        }

        $projectName = DB::table('gnr_project')->whereIn('id',$filteredRegTypeArray)->get();
        $projectTypeName = DB::table('gnr_project_type')->whereIn('id',$filteredRegTypeArray)->get();

        $registerType = DB::table('acc_adv_register_type')->whereIn('id',$filteredRegTypeArray)->get();
        $searchRegisterType = DB::table('acc_adv_register_type')->get();

        $searchProject = DB::table('gnr_project')->get();
        $searchProjectType = DB::table('gnr_project_type')->get();
        $fiscalYears = DB::table('gnr_fiscal_year')->pluck('name','id');

      //Is it the First Request
        if ($request->fiscalYear==null) {
            $firstRequest = 1;
        }
            else{
            $firstRequest = 0;
        }

      //$prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');

        return view('accounting.registerReport.advRegister.advRegReport',['registerType'=>$registerType,'projectName'=>$projectName,'projectTypeName'=> $projectTypeName,'accAdvRegister'=>$accAdvRegister,'regTypeSelected'=>$regTypeSelected,'fiscalYears'=>$fiscalYears,'startDate'=>$startDate,'endDate'=>$endDate,'searchMethodSelected'=>$searchMethodSelected,'fiscalYearSelected'=>$fiscalYearSelected,'dateFromSelected'=>$dateFromSelected,'projectSelected'=>$projectSelected,'projectTypeSelected'=>$projectTypeSelected,'searchProject'=>$searchProject,'searchProjectType'=>$searchProjectType,'dateToSelected'=>$dateToSelected,'firstRequest'=>$firstRequest,'categorySelected'=>$categorySelected,'searchRegisterType'=>$searchRegisterType]);
    } 

    public function changeProjectType(Request $request){

        $projectId = (int)json_decode($request->projectId);
        if($request->projectId==""){
            $projectTypeList =  DB::table('gnr_project_type')->select('id','name','projectTypeCode')->get();
          }
        else{
            $projectTypeList =  DB::table('gnr_project_type')->where('projectId',$projectId)->select('id','name','projectTypeCode')->get();
        }

        $data = array(            
           'projectTypeList' => $projectTypeList
          
        );
        
        return response()->json($data);
      }
}
