<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;
use App\fams\FamsProductPrefix;
use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use Validator;
use Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;

class FamsProductPrefixController extends Controller{

  public function index() {

   $productPrefixs = FamsProductPrefix::all();

   return view('fams/product/productSetting/productPrefix/viewFamsProductPrefix',['productPrefixs'=>$productPrefixs]);
 }

 public function addFamsProductPrefix(){

   return view('fams/product/productSetting/productPrefix/addFamsProductPrefix');
 }

  //insert function
 public function storeFamsProductFrefix(Request $request) {

  $request->merge(array('name' => $request->name.'-'));

  $rules = array(
    'name'      => 'required|unique:fams_product_prefix',
    'code' => 'required|unique:fams_product_prefix'
  );
  $attributeNames = array(
   'name'      => 'Product Prifix',
   'code' => 'Prifix Code'  
 );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())

  {

    return response::json(array('errors' => $validator->getMessageBag()->toArray()));

  } 

  else{
   $productPrefixs =new FamsProductPrefix;
   $productPrefixs->name = $request->name;
   $productPrefixs->code = $request->code;
   $productPrefixs->createdAt = Carbon::now();
   $productPrefixs->save();
   $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsProductPrefixController',
    'tableName'  => 'fams_product_prefix',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('fams_product_prefix')->max('id')]
  );
   Service::createLog($logArray);
   return response::json('success');   
 }
}


public function getFamsProductFrefixinfo(Request $request){

 $productPrefixs =  FamsProductPrefix::find($request->id);

 $data = array(
  'productPrefixs'=>$productPrefixs

);


 return response::json($data); 



} 

   //edit function Product Prefix

public function editFamsProductPrefix(Request $request) {



 $request->merge(array('name' => $request->name.'-'));

 $rules = array(
  'name' => 'required|unique:fams_product_prefix,name,'.$request->id,
  'code' => 'required|unique:fams_product_prefix,code,'.$request->id,
  'status' => 'required'
);
 $attributeNames = array(
   'name'      => 'Product Prefix',
   'code' => 'Prefix Code',
   'status' => 'Prefix Status'   
 );

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails()){
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
} 
else{
  $previousdata = FamsProductPrefix::find($request->id);
  $productPrefixs = FamsProductPrefix::find($request->id);
  $productPrefixs->name = $request->name;
  $productPrefixs->code= $request->code;
  $productPrefixs->code= $request->code;
  if( $request->status==1){
    FamsProductPrefix::where('id','!=',$request->id)->update(['status'=>0]);
    $productPrefixs->status = $request->status;

  }else{
   $productPrefixs->status = $request->status;

 }
 $productPrefixs->status = $request->status;
 $productPrefixs->save(); 

 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsProductPrefixController',
  'tableName'  => 'fams_product_prefix',
  'operation'  => 'update',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
}

return response::json('success');
}

 //delete Product  Prefix
public function deleteFamsProductPrefix(Request $request) {
 $previousdata=FamsProductPrefix::find($request->id);
 $productPrefixs=FamsProductPrefix::find($request->id);
 $productPrefixs->delete();
 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsProductPrefixController',
  'tableName'  => 'fams_product_prefix',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response::json('success');


}   

}
