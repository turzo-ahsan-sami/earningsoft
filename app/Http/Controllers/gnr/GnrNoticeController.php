<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrNotice;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrNoticeController extends Controller
{
 public function index(){
      // $projects = GnrProject::all();
  $notices = GnrNotice::orderBy('id')->get();
  return view('gnr.tools.notice.viewNotice',['notices' => $notices]);
}

public function addNotice(){
  return view('gnr.tools.notice.addNotice');
}


public function addItem(Request $req) {

  $rules = array(
    'name' => 'required',
    'branchId'  =>  'required',
    'startDate'  =>  'required',
    'endDate'  =>  'required'        
  );

  $attributesNames = array(
   'name'      => 'Notice Title',
   'branchId'  =>  'branch',
   'startDate'  =>  'Start Date',
   'endDate'  =>  'End Date' 
 );

  $validator = Validator::make(Input::all(), $rules);
  $validator->setAttributeNames($attributesNames);

  if($validator->fails()) 
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else {
    $startDate = Carbon::parse($req->startDate);
    $endDate = Carbon::parse($req->endDate);
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now,'startDate' => $startDate,'endDate' => $endDate]);
    //dd($req->all());
    $create = GnrNotice::create($req->all());
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrNoticeController',
      'tableName'  => 'gnr_notice',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_notice')->max('id')]
    );
    Service::createLog($logArray);
    return response::json(['responseText' => 'Data successfully saved.'], 200);
  }
}


    //edit function
public function editItem1(Request $req) {
  $rules = array(
    'name' => 'required'
  );
  $attributeNames = array(
   'name'      => 'Notice Title'
 );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
   $previousdata = GnrNotice::find ($req->id);
   $notice = GnrNotice::find ($req->id);
   $notice->name = $req->name;
   $notice->status = $req->status;

   $notice->save();


   $data = array(
    'notice'                 => $notice,

    'slno'                    => $req->slno
  );
   $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrNoticeController',
    'tableName'  => 'gnr_notice',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
   Service::createLog($logArray);
      //$projects = GnrProject::all();
   return response()->json($data); 
 }
} 



public function editItem(Request $req) {

  $rules = array(
    'name'    => 'required|unique:gnr_notice,name,'.$req->id,
    'branchId'  =>  'required',
    'startDate'  =>  'required',
    'endDate'  =>  'required'        
  );

  $attributesNames = array(
    'name'    =>  'Notice Title',
    'branchId'  =>  'branch',
    'startDate'  =>  'Start Date',
    'endDate'  =>  'End Date' 
  );

  $validator = Validator::make(Input::all(), $rules);
  $validator->setAttributeNames($attributesNames);

  if($validator->fails()) 
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else {
    $startDate = Carbon::parse($req->startDate);
    $endDate = Carbon::parse($req->endDate);
    $previousdata = GnrNotice::find ($req->id);
    $noticeNewAssign = GnrNotice::find($req->id);
    $noticeNewAssign->name = $req->name;
    $noticeNewAssign->status = $req->status;
    $noticeNewAssign->branchId = $req->branchId;
    $noticeNewAssign->startDate = $startDate;
    $noticeNewAssign->endDate = $endDate;
    $noticeNewAssign->save();

    $data = array(
      // 'notice'       => $noticeNewAssign,
      // 'responseText' =>   $req->branchId
      //'responseText' =>   $branchId['branchId'],
      'responseText' =>  'Data successfully updated.'
    );
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrNoticeController',
      'tableName'  => 'gnr_notice',
      'operation'  => 'update',
      'previousData'  => $previousdata,
      'primaryIds'  => [$previousdata->id]
    );
    Service::createLog($logArray);

    return response()->json($data);
  }
}



public function checkNoticeAssign(Request $req) {

  $checkBranchAvailability = DB::table('gnr_notice')->select('branchId')->get();
  $branchIdArr = json_decode($checkBranchAvailability, true);

  $alreadyAssign = 0;

  foreach($branchIdArr as $branchId):
    $branchId = json_decode($branchId['branchId'], true);

    if(in_array($req->getBranchId, $branchId)):
      $alreadyAssign = 1;
      break;
    else:
      $alreadyAssign = 0;
    endif;
  endforeach;

  $data = array(
    'alreadyAssign' =>  $alreadyAssign,
    'responseTitle' =>  'Warning!',
    'responseText'  =>  'This branch is already assigned to another Notice.'
  );

  return response()->json($data, 200);
}

    //delete
public function deleteItem(Request $req) {
 $previousdata=GnrNotice::find($req->id);
 GnrNotice::find($req->id)->delete();
 $responseTitle = 'Success!';
 $responseText = 'Your selected Notice deleted successfully.';
 $data = array(
  'responseTitle' =>  $responseTitle,
  'responseText'  =>  $responseText
);
 $logArray = array(
  'moduleId'  => 7,
  'controllerName'  => 'GnrNoticeController',
  'tableName'  => 'gnr_notice',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response()->json($data);
}  


public function deleteItem1(Request $req) {
  $previousdata=GnrNotice::find($req->id);

  $areaUsedCheck = DB::table('gnr_zone')->select('areaId')->get();
  $areaUsedCheck = json_decode($areaUsedCheck, true);

  $areaUsed = 0;

  foreach($areaUsedCheck as $areaUsedId):
    $areaUsedArr = json_decode($areaUsedId['areaId'], true);
    if(in_array($req->id, $areaUsedArr)):
      $areaUsed = 1;
      break;
    endif;
  endforeach;

  if($areaUsed==0):
    GnrArea::find($req->id)->delete();
    $responseTitle = 'Success!';
    $responseText = 'Your selected area deleted successfully.';
  else:
    $responseTitle = 'Warning!';
    $responseText = 'You are not permitted to delete this area.<br/> It is used in another zone.';
  endif;

  $data = array(
    'responseTitle' =>  $responseTitle,
    'responseText'  =>  $responseText
  );

  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrAreaController',
    'tableName'  => 'gnr_area',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response()->json($data);
}
}
