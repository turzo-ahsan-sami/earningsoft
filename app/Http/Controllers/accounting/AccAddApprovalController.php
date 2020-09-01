<?php

namespace App\Http\Controllers\accounting;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\accounting\AccApprovals;
use App\accounting\AccComments;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use DB;
use App\gnr\GnrProject;
use App\gnr\GnrDepartment;
use App\gnr\GnrPosition;
use App\gnr\GnrCompany;
use App\accounting\AddVoucher;
use Illuminate\Support\Facades\Auth;

class AccAddApprovalController extends Controller
{

  public function index(){
    $user_branch_id = Auth::user()->branchId;
    $userCompanyId = Auth::user()->company_id_fk;
    $branch = Auth::user()->branch;
    $user_project_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->where('companyId',$userCompanyId)->value('projectId');
    $user_project_type_id = (int) DB::table('gnr_branch')->where('id',$user_branch_id)->where('companyId',$userCompanyId)->value('projectTypeId');
   
    if ($branch->branchCode == 0) {
        $gnrProjectInfos = GnrProject::select('id','name')->where('companyId',$userCompanyId)->get();
        //dd($projects);
        //$projectTypes = GnrProjectType::select('id','name')->where('companyId',$userCompanyId)->get();
    }else{
        $gnrProjectInfos = GnrProject::select('id','name')->where('id', $user_project_id)->where('companyId',$userCompanyId)->get();
        //$projectTypes = GnrProjectType::select('id','name')->where('id', $user_project_type_id)->where('companyId',$userCompanyId)->get();
    }

     
    // $gnrProjectInfos = GnrProject::where('companyId',$userCompanyId)->get();
    
    $designations = GnrPosition::where('companyId',$userCompanyId)->get();
    $departments = GnrDepartment::where('companyId',$userCompanyId)->get();
    $v_approval_step = GnrCompany::where('id',Auth::user()->company_id_fk)->value('voucher_type_step');
 
    return view('accounting.approvals.addAccApproval',[
      'gnrProjectInfos'=>$gnrProjectInfos,
      'designations'=>$designations,
      'departments'=>$departments,
      'v_approval_step'=>$v_approval_step
    ]);
  }

  public function addFirstStepApproval(Request $request){
    //dd( $request->all());
      $accComment = new AccComments;

      if($request->verified_by != 0){
          $accComment->voucher_id = $request->voucher_id;
          $accComment->verified_by = $request->verified_by;
          $accComment->comments_details_verify = $request->comments_details_verify;
          $accComment->status = 'Approved';
          $accComment->save();
          DB::table('acc_voucher')->where('id', $request->voucher_id)->update(['authBy' => $request->verified_by]);
      }elseif($request->reviewed_by != 0){
          $accComment->voucher_id = $request->voucher_id;
          $accComment->reviewed_by = $request->reviewed_by;
          $accComment->comments_details_review = $request->comments_details_review;
          $accComment->status = 'Approved';
          //dd($accComment);
          $accComment->save();
          DB::table('acc_voucher')->where('id', $request->voucher_id)->update(['authBy' => $request->reviewed_by]);
      }elseif($request->approved_by != 0){
        //dd('ok3');
          $accComment->voucher_id = $request->voucher_id;
          $accComment->approved_by = $request->approved_by;
          $accComment->comments_details_approve = $request->comments_details_approve;
          $accComment->status = 'Approved';
          $accComment->save();
          DB::table('acc_voucher')->where('id', $request->voucher_id)->update(['authBy' => $request->approved_by]);
      }

    return response()->json(['responseText' => 'Data approved successfully!'], 200);
  }


  public function getPositionVerifiedBy(Request $req){
      $positions = GnrPosition::where('dep_id_fk',$req->verifiedByDepId)->get();
      return response()->json($positions);

  } 
  public function getPositionReviewedBy(Request $req){
      $positions = GnrPosition::where('dep_id_fk',$req->reviewedByDepId)->get();
      return response()->json($positions);

  }
  public function getPositionAprovedBy(Request $req){
      $positions = GnrPosition::where('dep_id_fk',$req->approvedByDepId)->get();
      return response()->json($positions);

  }

  public function addAccApprovalItem(Request $request){
  	//dd($request->all());
    $userCompanyId = Auth::user()->company_id_fk;
      	$rules = array(
    	    'projectId' => 'required',
    	    'branch' => 'required',
    	    'date' => 'required',
    	    // 'reviewedById' => 'required',
    	    // 'verifiedById' => 'required',
    	    // 'approvedById' => 'required',
     	);

  	$attributeNames = array(
	     'projectId'    => 'Project',
	     'branch'   => 'Branch',
	     'date'   => 'Date',
	     // 'reviewedById'   => 'Reviewed By',
	     // 'verifiedById'   => 'Verified By',
	     // 'approvedById'   => 'Approved By',

    );
    $reviewedById =array('designation'=>$request->reviewedById,'department'=>$request->reviewedByDepId);
    $verifiedById =array('designation'=>$request->verifiedById,'department'=>$request->verifiedByDepId);
    $approvedById =array('designation'=>$request->approvedById,'department'=>$request->approvedByDepId);
    //dd($verifiedById, $verifiedById, $approvedById);

    $validator = Validator::make ( Input::all (), $rules);
   	$validator->setAttributeNames($attributeNames);

   	if ($validator->fails()){
   		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
   	}else{
   		$accApproval = new AccApprovals();
    	$accApproval->projectId = $request->projectId;
    	$accApproval->branch = $request->branch;
    	$accApproval->date = $request->date;
      $accApproval->companyId = $userCompanyId;
    	$accApproval->reviewedById = json_encode($reviewedById);
    	$accApproval->verifiedById = json_encode($verifiedById);
    	$accApproval->approvedById = json_encode($approvedById);
		  $accApproval->save();
	  	return response()->json(['responseText' => 'Data successfully inserted!'], 200);
   	}
  }

 //===================================viewSetting=======================================//

  public function viewApprovalSetting(){
    $userCompanyId = Auth::user()->company_id_fk;
   // dd($userCompanyId);
    $viewApprovalSettings = AccApprovals::where('companyId',  $userCompanyId)->get();
    $v_approval_step = GnrCompany::where('id',Auth::user()->company_id_fk)->value('voucher_type_step');
    //dd($viewApprovalSettings );
    $settingsArr=[];

    foreach ($viewApprovalSettings as  $key => $viewApprovalSetting) {

        $settings['project'] = GnrProject::where('id',$viewApprovalSetting->projectId)->where('companyId',  $userCompanyId)->value('name');
        $settings['date'] = AccApprovals::where('id',$viewApprovalSetting->id)->value('date');
        $settings['branch'] = AccApprovals::where('id',$viewApprovalSetting->id)->value('branch');
        $settings['id'] = AccApprovals::where('id',$viewApprovalSetting->id)->value('id');

        foreach(json_decode($viewApprovalSetting->verifiedById) as $key => $verified){

          if($key == 'designation'){
             $verifi[$key] =GnrPosition::where('id',$verified)->where('companyId',  $userCompanyId)->value('name');
           }elseif($key == 'department')

            $verifi[$key] =GnrDepartment::where('id',$verified)->where('companyId',  $userCompanyId)->value('name');
        }

        foreach(json_decode($viewApprovalSetting->reviewedById) as $key => $reviewed){

          if($key == 'designation'){
             $review[$key] =GnrPosition::where('id',$reviewed)->where('companyId',  $userCompanyId)->value('name');
           }elseif($key == 'department')

            $review[$key] =GnrDepartment::where('id',$reviewed)->where('companyId',  $userCompanyId)->value('name');
        }

        foreach(json_decode($viewApprovalSetting->approvedById) as $key => $approved){

          if($key == 'designation'){
             $approve[$key] =GnrPosition::where('id',$approved)->where('companyId',  $userCompanyId)->value('name');
           }elseif($key == 'department')

            $approve[$key] =GnrDepartment::where('id',$approved)->where('companyId',  $userCompanyId)->value('name');
        }

        $settings['verified'] = $verifi;
        $settings['reviewed'] = $review;
        $settings['approved'] = $approve;
        $settingsArr[] = $settings;
    }
   //dd($settingsArr) ;

    return view('accounting.approvals.viewApprovalSetting',[
      'settingsArr'=>$settingsArr,
      'v_approval_step'=>$v_approval_step,
    ]);
  }


 //===================================edit Approval Setting by id=======================================//

  public function editApprovalSettingById($id){
  	$editApprovalSettingById = AccApprovals::find($id);
    $userCompanyId = Auth::user()->company_id_fk;
    $projects = GnrProject::where('companyId',$userCompanyId)->get();
    $designations = GnrPosition::where('companyId',$userCompanyId)->get();
    $departments = GnrDepartment::where('companyId',$userCompanyId)->get();
    $v_approval_step = GnrCompany::where('id',Auth::user()->company_id_fk)->value('voucher_type_step');

  	
    $settings=[];

      foreach(json_decode($editApprovalSettingById->verifiedById) as $key => $verified){
        if($key == 'designation'){
           $verifi[$key] =GnrPosition::where('id',$verified)->where('companyId',  $userCompanyId)->value('id');
        }elseif($key == 'department'){
          $verifi[$key] =GnrDepartment::where('id',$verified)->where('companyId',  $userCompanyId)->value('id');
        }

      }


      foreach(json_decode($editApprovalSettingById->reviewedById) as $key => $reviewed){

          if($key == 'designation'){
             $review[$key] =GnrPosition::where('id',$reviewed)->where('companyId',  $userCompanyId)->value('id');
           }elseif($key == 'department')

            $review[$key] =GnrDepartment::where('id',$reviewed)->where('companyId',  $userCompanyId)->value('id');
      }

      foreach(json_decode($editApprovalSettingById->approvedById) as $key => $approved){

          if($key == 'designation'){
             $approve[$key] =GnrPosition::where('id',$approved)->where('companyId',  $userCompanyId)->value('id');
           }elseif($key == 'department')

            $approve[$key] =GnrDepartment::where('id',$approved)->where('companyId',  $userCompanyId)->value('id');
      }


      $settings['verified'] = $verifi;
      $settings['reviewed'] = $review;
      $settings['approved'] = $approve;
      //dd($settings);

    return view('accounting.approvals.editApprovalSetting',[
      'editApprovalSettingById'=>$editApprovalSettingById,
      'settings'=>$settings,'projects'=>$projects,
      'designations'=>$designations,
      'departments'=>$departments,
      'v_approval_step'=>$v_approval_step
    ]);
  }

 //===================================update Approval Setting =======================================//


  public function updateApprovalSetting(Request $request){
  	//dd($request->all());
  	$rules = array(
	    'projectId' => 'required',
	    'branch' => 'required',
	    'date' => 'required',
	    // 'reviewedById' => 'required',
	    // 'verifiedById' => 'required',
	    // 'approvedById' => 'required',
 	);

  	$attributeNames = array(
	     'projectId'    => 'Project',
	     'branch'   => 'Branch',
	     'date'   => 'Date',
	     // 'reviewedById'   => 'Reviewed By',
	     // 'verifiedById'   => 'Verified By',
	     // 'approvedById'   => 'Approved By',

    );
    $reviewedById =array('designation'=>$request->reviewedById,'department'=>$request->reviewedByDepId);
    $verifiedById =array('designation'=>$request->verifiedById,'department'=>$request->verifiedByDepId);
    $approvedById =array('designation'=>$request->approvedById,'department'=>$request->approvedByDepId);

    $validator = Validator::make ( Input::all (), $rules);
   	$validator->setAttributeNames($attributeNames);

     	if ($validator->fails()){
     		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
     	}
      else{
     		$accApproval = AccApprovals::find($request->ApprovalId);
      	$accApproval->projectId = $request->projectId;
        $accApproval->branch = $request->branch;
        $accApproval->date = $request->date;
        $accApproval->reviewedById = json_encode($reviewedById);
        $accApproval->verifiedById = json_encode($verifiedById);
        $accApproval->approvedById = json_encode($approvedById);
        //dd($accApproval);
        $accApproval->save();
        return response()->json(['responseText' => 'Data successfully inserted!'], 200);
     	}
  }

  public function deleteAccApprovalItem(Request $request){
   AccApprovals::find($request->id)->delete();
   return response()->json(['responseText' => 'Approved Setting deleted successfully!']);

  }

  //comments settings

  public function addAccCommentsItem(Request $request){
   //dd($request->all());
     $rules = array(
        'commentsDetails' => 'required',
    );
    $attributeNames = array(
        'commentsDetails'    => 'commentsDetails',
    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
      if ($validator->fails()){
          return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      }else{
            $accComment = new AccComments();
            $accComment->voucher_id = $request->voucherId;
            $accComment->verified_by = $request->verifiedBy;
            $accComment->comments_details_verify = $request->commentsDetails;
            $accComment->status = 'Verified';
            //dd($accComment);
            $accComment->save();
      }


    return response()->json(['responseText' => 'First Step added successfully!'],200);
  }
  public function rejectedAccCommentsItem(Request $request){
    //dd($request->all());
    $rules = array(
        'commentsDetails' => 'required',
    );

    $attributeNames = array(
       'commentsDetails'    => 'commentsDetails',
    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    
    if ($validator->fails()){
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    }else{
      $accComment = new AccComments();
        $accComment->comments_details_verify = $request->commentsDetails;
        $accComment->rejected_by = $request->userEmployeeId;
        $accComment->voucher_id = $request->voucherId;
        $accComment->status = 'Rejected';
        // dd($accComment);
        $accComment->save();
    }
      return response()->json(['responseText' => 'Rrejected!'],200);
  }

  //reject comments
 public function secondStepApproval(Request $request){
  //dd($request->all());

   $accComment = AccComments::orderBy('updated_at','DESC')->first();
   //dd($accComment);
      $rules = array(
        'comments_details_approve' => 'required',
     );

    $attributeNames = array(
        'comments_details_approve'    => 'approveCommentsDetails',
    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails()){
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    }else{
      if($accComment){
          if($accComment->status == 'Verified' ){
            $accComment->voucher_id = $request->voucher_id;
            $accComment->comments_details_verify = $accComment->comments_details_verify;
            $accComment->comments_details_approve = $request->comments_details_approve;
            $accComment->verified_by = $accComment->verified_by;
            $accComment->approved_by = $request->approve_emp_id;
            $accComment->status = 'Approved';
            //dd($accComment);
            $accComment->save();
          
          }           
      }
    }
    DB::table('acc_voucher')->where('id', $request->voucher_id)->update(['authBy' => $request->approve_emp_id]);

    return response()->json(['responseText' => 'Data inserted successfully!'],200);

 }
  public function settingsApprovalProcced(Request $request){
   //dd($request->all());
    $accComment = AccComments::orderBy('updated_at','DESC')->first();
      
    $rules = array(
      'comments_details_review' => 'required',
    );
    
    $attributeNames = array(
      'comments_details_verify'    => 'comments_details_verify',
    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);

    if ($validator->fails()){
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    }else{
      if($accComment){
          if($accComment->status == 'Verified' ){
            $accComment->voucher_id = $request->voucher_id;
            $accComment->comments_details_verify = $accComment->comments_details_verify;
            $accComment->comments_details_review = $request->comments_details_review;
            $accComment->verified_by = $accComment->verified_by;
            $accComment->reviewed_by = $request->review_emp_id;
            $accComment->status = 'Reviewed';
            // dd($accComment);
            $accComment->save();
          }
         elseif($accComment->status == 'Reviewed'){
            $accComment->voucher_id = $request->voucher_id;
            $accComment->comments_details_verify = $accComment->comments_details_verify;
            $accComment->comments_details_review = $accComment->comments_details_review;
            $accComment->comments_details_approve = $request->comments_details_approve;
            $accComment->verified_by = $accComment->verified_by;
            $accComment->reviewed_by = $accComment->reviewed_by;
            $accComment->approved_by = $request->approve_emp_id;
            $accComment->status = 'Reviewed';
            //dd($accComment);
            $accComment->save();
        }

      }
    }
      return response()->json(['responseText' => 'Please insert comment!'],200);
  }

      //update approval system

  public function updateApprovalSettingProccesing(Request $request){
      // dd(1);

      $accComment = AccComments::orderBy('updated_at','DESC')->first();
      // $accComment = AccComments::select('id','rejected_by','voucher_id','verified_by','reviewed_by','approved_by','comments_details_verify','comments_details_review','created_at','status')->orderBy('updated_at','DESC')->first();

      $rules = array(
          'comments_details_review' => 'required',
      );
      $attributeNames = array(
          'comments_details_review'    => 'comments_details_review',
      );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);

      if ($validator->fails()){
          return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      }else{
          if($accComment){
              if($accComment->status == 'Verified' ){
                  $accComment->voucher_id = $request->voucher_id;
                  $accComment->comments_details_verify = $accComment->comments_details_verify;
                  $accComment->comments_details_review = $request->comments_details_review;
                  $accComment->verified_by = $accComment->verified_by;
                  $accComment->reviewed_by = $request->review_emp_id;
                  $accComment->status = 'Approved';
                  //dd($accComment);
                  $accComment->save();
              }
              elseif($accComment->status == 'Reviewed'){
                  $accComment->voucher_id = $request->voucher_id;
                  $accComment->comments_details_verify = $accComment->comments_details_verify;
                  $accComment->comments_details_review = $accComment->comments_details_review;
                  $accComment->comments_details_approve = $request->comments_details_approve;
                  $accComment->verified_by = $accComment->verified_by;
                  $accComment->reviewed_by = $accComment->reviewed_by;
                  $accComment->approved_by = $request->approve_emp_id;
                  $accComment->status = 'Approved';
                  //dd($accComment);
                  $accComment->save();
              }

          }
      }
      DB::table('acc_voucher')->where('id', $request->voucher_id)->update(['authBy' => $request->approve_emp_id]);

  }
    //Reject last approval system

    public function updateApprovalSettingReject(Request $request){

      $accComment = AccComments::orderBy('updated_at','DESC')->first();

      $rules = array(
        'comments_details_approve' => 'required',
      );

      $attributeNames = array(
        'comments_details_approve'    => 'comments_details_approve',
      );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    
    if ($validator->fails()){
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    }else{
       if($accComment->comments_details_verify != '' && $accComment->comments_details_review == ''){
       
          $accComment->verified_by = 0;
          $accComment->comments_details_verify ='';
          $accComment->comments_details_review = $request->comments_details_review;
          $accComment->comments_details_approve ='';
          $accComment->reviewed_by = 0;
          $accComment->approved_by = 0;
          $accComment->rejected_by = $request->userEmployeeId;
          $accComment->status = 'Rejected';
           //dd($accComment);
          $accComment->save();

      }elseif($accComment->comments_details_review != ''){
        $accComment->verified_by = 0;
        $accComment->comments_details_verify ='';
        $accComment->comments_details_review = '';
        $accComment->comments_details_approve =$request->comments_details_approve;
        $accComment->reviewed_by = 0;
        $accComment->approved_by = 0;
        $accComment->rejected_by = $request->userEmployeeId;
        $accComment->status = 'Rejected';
         //dd($accComment);
        $accComment->save();
      }
    }

  }

  public function settingsApprovalReject(Request $request){
    
      $accComment = AccComments::orderBy('updated_at','DESC')->first();

      $rules = array(
        'comments_details_review' => 'required',
      );

      $attributeNames = array(
        'comments_details_verify'    => 'comments_details_verify',
      );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);

      if ($validator->fails()){
          return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      }else{
        if($accComment->comments_details_verify != '' && $accComment->comments_details_review == ''){
          $accComment->verified_by = 0;
         $accComment->comments_details_verify ='';
         $accComment->comments_details_review = $request->comments_details_review;
         $accComment->comments_details_approve ='';
         $accComment->reviewed_by = 0;
         $accComment->approved_by = 0;
         $accComment->rejected_by = $request->userEmployeeId;
         $accComment->status = 'Rejected';
         //dd($accComment);
         $accComment->save();
        }elseif($accComment->comments_details_review != ''){
          $accComment->verified_by = 0;
         $accComment->comments_details_verify ='';
         $accComment->comments_details_review = '';
         $accComment->comments_details_approve =$request->comments_details_approve;
         $accComment->reviewed_by = 0;
         $accComment->approved_by = 0;
         $accComment->rejected_by = $request->userEmployeeId;
         $accComment->status = 'Rejected';
         //dd($accComment);
          $accComment->save();
        }
      }
  }

}
