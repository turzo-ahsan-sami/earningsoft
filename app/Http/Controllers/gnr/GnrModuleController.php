<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrBranch;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrModuleController extends Controller
{
    public function index(){
      $suppliers = GnrModule::all();
      return view('gnr.tools.supplier.viewSupplier',['suppliers' => $suppliers]);
    } 

    public function addModule(){
        return view('gnr.tools.module.addModule');
    }

    public function serchModule(Request $request){
        $searchDate = $request->startDate;
        $branchName = DB::table('gnr_branch');
        if($request->projectName!=null){
            $branchName =$branchName->where('projectId',$request->projectName);
        }
        if($request->branchName!=null){
            $branchName = $branchName->where('id',$request->branchName);
        }
        $branchName = $branchName->select('id','name')->get();
        $data=array(
            'branchName' =>$branchName,
            'searchDate'=>$searchDate
        );
        return response::json($data);

    }

     //add data
    
    public function fiieldValidation (Request $request){

        $project = $request->projectName;
        
        $rules = array(
            'projectName'     => 'required',
            'moduleName'      => 'required'
        );
        $attributeNames = array(
            'projectName'      =>'Project Name',
            'moduleName'       =>'Module Name',
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        else{
            $string = str_replace("",null,$request->moduleName);
            $branchId =DB::table('gnr_branch')->select('id','name')->get();
            if($request->branchName =='') {
                foreach($branchId as $branchId) {
                    $branchName = GnrBranch::where('id',$branchId->id)->first();
                    $branchName->moduleStartDate=json_encode(array([
                        'ModuleId'=>$string,
                        'StartDate'=>$request->startDate,
                    ]));
                    $branchName->save();
               }
            }
            if($request->branchName !=null){
                foreach($branchId as $branchId) {
                    $branchName = GnrBranch::where('id',$branchId->id)->first();
                    $branchName->moduleStartDate=json_encode([
                        'ModuleId'=>$string,
                        'StartDate'=>$request->startDate,
                    ]);
                    $branchName->update();
                }
            }
        }
        return response::json($string);
    }


   //delete
    public function deleteItem(Request $req) {
      GnrSupplier::find($req->id)->delete();
      return response()->json();
    }  
   
    public function projectchange(Request $request){
        $projectId = json_decode($request->projectId);
        if($projectId==''){


            $branchName = DB::table('gnr_branch')->select('id','name','branchCode')->get();
        }else{

            $branchName = DB::table('gnr_branch')->where('projectId',$projectId)->select('id','name','branchCode')->get();

            
        }
        $data= array(
            'branchName'      => $branchName,
        );
        return response()->json($data);
    }

    
}
