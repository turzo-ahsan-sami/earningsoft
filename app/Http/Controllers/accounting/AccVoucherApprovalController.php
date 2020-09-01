<?php

namespace App\Http\Controllers\accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\gnr\GnrProject;
use DB;
class AccVoucherApprovalController extends Controller
{
   public function index(){
    $gnrProjectInfos = GnrProject::all();
    $designations = DB::table('gnr_position')->get();
    $departments = DB::table('gnr_department')->get();
    //dd($departmens);
   	return view('accounting.approvals.addAccApproval',[
   		'gnrProjectInfos'=>$gnrProjectInfos,
         'designations'=>$designations,
   		'departments'=>$departments
   	]);
   }

   // public function viewGroupById(Request $request){
   // 		$gnrCompanies = DB::table('gnr_company')->where('groupId','=' ,$request->id)->get();
   // 		return response()->json($gnrCompanies);
   //  }

   //  public function projectTypeItem(Request $request){
   // 		$gnrProjects = DB::table('gnr_project')->where('companyId','=' ,$request->id)->get();
   // 		return response()->json($gnrProjects);
   //  } 
   //   public function branchTypeItem(Request $request){
   // 		$gnrProjects = DB::table('gnr_branch')->where('projectId','=' ,$request->projectTypeId)->get();
   // 		return response()->json($gnrProjects);
   //  }
  
}
