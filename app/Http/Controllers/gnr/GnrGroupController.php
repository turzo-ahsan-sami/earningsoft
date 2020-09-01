<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\gnr\GnrGroup;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrGroupController extends Controller
{
	public function index(){
    $groups = GnrGroup::all();
    return view('gnr.tools.companySetting.group.viewGroup',['groups' => $groups]);
  }

  public function addGroup(){
    return view('gnr.tools.companySetting.group.addGroup');
      // return view('inventory/tools/companySetting/group/addGroup');
  }

  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required',
    'email' => 'required|email',
    'phone' => 'required|regex:/(01)[0-9]{9}$/',
    'address' => 'required',
    'website' => 'required'
  );
   $messages = array(
             // 'phone.required' => 'The Mobile Number Should Be 11 Digits.'
    'phone.regex' => 'The :attribute number is invalid , accepted format: 01xxxxxxxxx Should Be 11 Digits.'
  );
   $attributeNames = array(
     'name'    => 'Group Name',
     'email'   => 'Email',
     'phone'   => 'Phone',
     'address' => 'Address',
     'website' => 'Website'   
   );

   $validator = Validator::make ( Input::all (), $rules, $messages);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = GnrGroup::create($req->all());
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrGroupController',
      'tableName'  => 'gnr_group',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_group')->max('id')]
    );
    Service::createLog($logArray); 
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {
  $rules = array(
    'name' => 'required',
    'email' => 'required|email',
    'phone' => 'required|regex:/(01)[0-9]{9}$/',
    'address' => 'required',
    'website' => 'required'
  );
  $messages = array(
             // 'phone.required' => 'The Mobile Number Should Be 11 Digits.'
    'phone.regex' => 'The :attribute number is invalid , accepted format: 01xxxxxxxxx Should Be 11 Digits.'
  );
  $attributeNames = array(
   'name'    => 'Group Name',
   'email'   => 'Email',
   'phone'   => 'Phone',
   'address' => 'Address',
   'website' => 'Website'   
 );

  $validator = Validator::make ( Input::all (), $rules, $messages);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $previousdata = GnrGroup::find ($req->id);
    $group = GnrGroup::find ($req->id);
    $group->name = $req->name;
    $group->email = $req->email;
    $group->phone = $req->phone;
    $group->address = $req->address;
    $group->website = $req->website;
    $group->save();
      /*$group = GnrGroup::find($req->id)->update($req->all());
      $groups = GnrGroup::all();*/
      $data = array(
        'group'     => $group,
        'slno'      => $req->slno
      );
      $logArray = array(
        'moduleId'  => 7,
        'controllerName'  => 'GnrGroupController',
        'tableName'  => 'gnr_group',
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
    $previousdata=GnrGroup::find($req->id);
    GnrGroup::find($req->id)->delete();
    $groups = GnrGroup::all();
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrGroupController',
      'tableName'  => 'gnr_group',
      'operation'  => 'delete',
      'previousData'  => $previousdata,
      'primaryIds'  => [$previousdata->id]
    );
    Service::createLog($logArray);
    return response()->json($groups);
  }   
}


