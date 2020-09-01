<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrRoom;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class GnrRoomController extends Controller
{
  public function index(){
    $rooms = GnrRoom::all();
    return view('gnr/tools/departmentRoom/viewRoom',['rooms' => $rooms]);
  }

  public function addRoomForm(){
    return view('gnr/tools/departmentRoom/addRoomForm');
  }

  public function addItem(Request $req) 
  {
   $rules = array(
    'name'            => 'required',
    'departmentId'    => 'required'
  );

   $attributeNames = array(
     'name'           => 'Room Number',
     'departmentId'   => 'department Name' 
   );

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = GnrRoom::create($req->all());
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrRoomController',
      'tableName'  => 'gnr_room',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_room')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json($req->departmentId); 
  }
}

//edit function
public function editItem(Request $req) {
 $rules = array(
  'name'            => 'required',
  'departmentId'    => 'required'
);

 $attributeNames = array(
   'name'           => 'Room Number',
   'departmentId'   => 'department Name'  
 );
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{

      /*$room = GnrRoom::find ($req->id);
      $room->name         = $req->name;
      $room->departmentId = $req->departmentId;
      $room->save();*/
      $previousdata = GnrRoom::find ($req->id);
      $room = GnrRoom::find($req->id)->update($req->all());
      $room = GnrRoom::where('id', $req->id)->get();
      $data = array(
        'room'          => $room,
        'slno'           => $req->slno
      );
      $logArray = array(
        'moduleId'  => 7,
        'controllerName'  => 'GnrRoomController',
        'tableName'  => 'gnr_room',
        'operation'  => 'update',
        'previousData'  => $previousdata,
        'primaryIds'  => [$previousdata->id]
      );
      Service::createLog($logArray);
      return response()->json($data);
    }
  }

 //delete
  public function deleteItem(Request $req) {
   $previousdata=GnrRoom::find($req->id);
   GnrRoom::find($req->id)->delete();
   $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrRoomController',
    'tableName'  => 'gnr_room',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
   Service::createLog($logArray);
   return response()->json();
 }

// noting
 public function bringDepartments(Request $req) {
   $lemon  = GnrRoom::select('departmentId')->where('id', $req->id)->get();
   return response()->json($lemon);
 }

}
