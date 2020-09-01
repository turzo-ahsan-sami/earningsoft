<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrSupplier;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrSupplierController extends Controller
{
  public function index(){
    $suppliers = GnrSupplier::all();
    return view('gnr.tools.supplier.viewSupplier',['suppliers' => $suppliers]);
  } 

  public function addSupplier(){
    return view('gnr.tools.supplier.addSupplier');
  }

     //add data
  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required',
    'supplierCompanyName' => 'required',
    'email' => 'required|email',
    'phone' => 'required|regex:/(01)[0-9]{9}$/',
    'refNo' => 'required',
    'attentionFirst' => 'required'
  );
   $messages = array(
     'phone.required' => 'The Mobile Number Should Be 11 Digits.'
   );
   $attributeNames = array(
     'name'    => 'Supplier Name',
     'email'   => 'Email',
     'phone'   => 'Phone',
     'refNo' => 'Reference Number',
     'supplierCompanyName' => 'Supplier Company Name',
     'attentionFirst' => 'Attention First' 
   );

   $validator = Validator::make ( Input::all (), $rules, $messages);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
     return response::json(array('errors' => $validator->getMessageBag()->toArray()));
   else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = GnrSupplier::create($req->all());
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrSupplierController',
      'tableName'  => 'gnr_supplier',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_supplier')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}
   //edit 
public function editItem(Request $req) {

 $rules = array(
  'name' => 'required',
  'supplierCompanyName' => 'required',
  'email' => 'required|email',
  'phone' => 'required|regex:/(01)[0-9]{9}$/',
  'refNo' => 'required',
  'attentionFirst' => 'required'
);
 $messages = array(
   'phone.required' => 'The Mobile Number Should Be 11 Digits.'
 );
 $attributeNames = array(
   'name'    => 'Supplier Name',
   'email'   => 'Email',
   'phone'   => 'Phone',
   'refNo' => 'Reference Number',
   'supplierCompanyName' => 'Supplier Company Name',
   'attentionFirst' => 'Attention First' 
 );

 $validator = Validator::make ( Input::all (), $rules, $messages);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = GnrSupplier::find ($req->id);
  $supplier = GnrSupplier::find ($req->id);
  $supplier->name = $req->name;
  $supplier->supplierCompanyName = $req->supplierCompanyName;
  $supplier->email = $req->email;
  $supplier->mailForNotify = $req->mailForNotify;
  $supplier->phone = $req->phone;
  $supplier->address = $req->address;
  $supplier->website = $req->website;
  $supplier->description = $req->description;
  $supplier->refNo = $req->refNo;
  $supplier->attentionFirst = $req->attentionFirst;
  $supplier->attentionSecond = $req->attentionSecond;
  $supplier->attentionThird = $req->attentionThird;
  $supplier->save();
      // $suppliers = GnrSupplier::find($req->id)->update($req->all());
      // $supplier = GnrSupplier::all();
  $data = array(
    'supplier'   => $supplier,
    'slno'       => $req->slno
  );
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrSupplierController',
    'tableName'  => 'gnr_supplier',
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
 $previousdata=GnrSupplier::find($req->id);
 GnrSupplier::find($req->id)->delete();
 $logArray = array(
  'moduleId'  => 7,
  'controllerName'  => 'GnrSupplierController',
  'tableName'  => 'gnr_supplier',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response()->json();
}  
}
