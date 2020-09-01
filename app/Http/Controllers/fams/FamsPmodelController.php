<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPmodel;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsPmodelController extends Controller
{
  public function index(){
    $productModels = FamsPmodel::all();
    return view('fams/product/productSetting/model/viewPmodel',['productModels' => $productModels]);
  }

  public function addPmodel(){
    return view('fams/product/productSetting/model/addPmodel');
  }
//insert function
  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required|unique:fams_product_model',
    'productBrandId' => 'required'
  );
   $attributeNames = array(
    'name' => 'Product Model',
    'productBrandId' => 'Product Brand' 
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $create = FamsPmodel::create($req->all());
    $logArray = array(
      'moduleId'  => 2,
      'controllerName'  => 'FamsPmodelController',
      'tableName'  => 'fams_product_model',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('fams_product_model')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {
  $rules = array(
    'name' => 'required|unique:fams_product_model,name,'.$req->id,
    'productBrandId' => 'required'
  );
  $attributeNames = array(
    'name' => 'Product Model',
    'productBrandId' => 'Product Brand' 
  );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
   $previousdata = FamsPmodel::find ($req->id);
   $productModel = FamsPmodel::find ($req->id);
   $productModel->name = $req->name;
   $productModel->productBrandId = $req->productBrandId;
   $productModel->save();

   $productBrandName       = \DB::table('fams_product_brand')->select('name')->where('id',$req->productBrandId)->first();
   $data = array(
    'productModel'            => $productModel,
    'productBrandName'        => $productBrandName,
    'slno'                    => $req->slno
  );

   $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPmodelController',
    'tableName'  => 'fams_product_model',
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
  $previousdata=FamsPmodel::find($req->id);
  FamsPmodel::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPmodelController',
    'tableName'  => 'fams_product_model',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}

public function productBrandChange(Request $req){
  $productBrandList = \ DB::table('fams_product_brand')->where('productSubCategoryId',$req->productSubCategoryId)->pluck('name', 'id');
  return response()->json($productBrandList);
}
}
