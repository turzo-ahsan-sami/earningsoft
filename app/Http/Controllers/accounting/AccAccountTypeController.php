<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AddAccountType;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;

class AccAccountTypeController extends Controller {

  public function index(){
    $accountTypes = AddAccountType::all()->sortByDesc('id');
    return view('accounting.accountType.viewAccountType',['accountTypes' => $accountTypes]);
  }

  public function addAccountType(){

   $accountTypes = AddAccountType::all()->where('parentId',0);
        // $accountTypes = DB::table('acc_account_type')
        // ->where('parentId',0)
        // ->get();

   return view('accounting.accountType.addAccountType',['accountTypes'=> $accountTypes]);
 }

 public function addItem(Request $req) {
  $rules = array(
    'name' => 'required',
    'parentId' => 'required'
  );
  $attributeNames = array(
   'name'    => 'Account Name',
   'parentId'   => 'Parent',  
   'description'   => 'Description',  
   'isParent'   => 'Parent'
 );

  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);

  if ($validator->fails())
   return response::json(array('errors' => $validator->getMessageBag()->toArray()));
 else{
  $now = Carbon::now();
                // $now = date('Y-m-d H:i:s');

  $req->request->add(['createdDate' => $now]);
  $create = AddAccountType::create($req->all());
  $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccAccountTypeController',
    'tableName'  => 'acc_account_type',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('acc_account_type')->max('id')]
  );
  Service::createLog($logArray);
  return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
}
}

public function editItem(Request $req) {
  $rules = array(
    'name' => 'required',
    'parentId' => 'required'
  );
  $attributeNames = array(
    'name'    => 'Account Name',
    'parentId'   => 'Parent',
    'description'   => 'Description',
    'isParent'   => 'Parent'
  );

  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
   $previousdata = AddAccountType::find ($req->id);
   $accountType = AddAccountType::find ($req->id);
   $accountType->name = $req->name;
   $accountType->parentId = $req->parentId;
   $accountType->description = $req->description;
   $accountType->isParent = $req->isParent;
   $accountType->save();

   $data = array(
    'accountType'     => $accountType,
    'slno'      => $req->slno
  );
   $logArray = array(
    'moduleId'  => 4,
    'controllerName'  => 'AccAccountTypeController',
    'tableName'  => 'acc_account_type',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
   Service::createLog($logArray);
   return response()->json($data);
 }
}


public function deleteItem(Request $req){
 $previousdata=AddAccountType::find($req->id);
 AddAccountType::find($req->id)->delete();
 $logArray = array(
  'moduleId'  => 4,
  'controllerName'  => 'AccAccountTypeController',
  'tableName'  => 'acc_account_type',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
//        $accountTypes = AddAccountType::all();
        return response()->json();     //json($accountTypes);
      }

    }
