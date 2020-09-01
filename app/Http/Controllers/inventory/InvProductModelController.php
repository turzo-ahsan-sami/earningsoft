<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductModel;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class InvProductModelController extends Controller
{
  public function index(){
    $productModels = InvProductModel::all();
    return view('inventory/product/productSetting/model/viewProductModel',['productModels' => $productModels]);
  }

  public function addProductModel(){
    return view('inventory/product/productSetting/model/addProductModel');
  }
//insert function
  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required|unique:inv_product_model',
    'productGroupId' => 'required',
    'productCategoryId' => 'required',
    'productSubCategoryId' => 'required',
    'productBrandId' => 'required'
  );
   $attributeNames = array(
    'name' => 'Product Model',
    'productGroupId' => 'Product Group',
    'productCategoryId' => 'Product Category',
    'productSubCategoryId' => 'Product Subcategory',
    'productBrandId' => 'Product Brand' 
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = InvProductModel::create($req->all());
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductModelController',
      'tableName'  => 'inv_product_model',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('inv_product_model')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {
 $rules = array(
  'name' => 'required|unique:inv_product_model,name,'.$req->id,
  'productGroupId' => 'required',
  'productCategoryId' => 'required',
  'productSubCategoryId' => 'required',
  'productBrandId' => 'required'
);
 $attributeNames = array(
  'name' => 'Product Model',
  'productGroupId' => 'Product Group',
  'productCategoryId' => 'Product Category',
  'productSubCategoryId' => 'Product Subcategory',
  'productBrandId' => 'Product Brand' 
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
 $previousdata = InvProductModel::find ($req->id);
 $productModel = InvProductModel::find ($req->id);
 $productModel->name = $req->name;
 $productModel->productGroupId = $req->productGroupId;
 $productModel->productCategoryId = $req->productCategoryId;
 $productModel->productSubCategoryId = $req->productSubCategoryId;
 $productModel->productBrandId = $req->productBrandId;
 $productModel->save();

 $productGroupName       = DB::table('inv_product_group')->select('name')->where('id',$req->productGroupId)->first();
 $productCategoryName    = DB::table('inv_product_category')->select('name')->where('id',$req->productCategoryId)->first();
 $productSubCategoryName = DB::table('inv_product_sub_category')->select('name')->where('id',$req->productSubCategoryId)->first();
 $productBrandName       = DB::table('inv_product_brand')->select('name')->where('id',$req->productBrandId)->first();
 $data = array(
  'productModel'            => $productModel,
  'productGroupName'        => $productGroupName,
  'productCategoryName'     => $productCategoryName,
  'productSubCategoryName'  => $productSubCategoryName,
  'productBrandName'        => $productBrandName,
  'slno'                    => $req->slno
);
 $logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvProductModelController',
  'tableName'  => 'inv_product_model',
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
  $previousdata=InvProductModel::find($req->id);
  InvProductModel::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductModelController',
    'tableName'  => 'inv_product_model',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}

public function productBrandChange(Request $req){
  $productBrandList =  DB::table('inv_product_brand')->where('productSubCategoryId',$req->productSubCategoryId)->pluck('name', 'id');
  return response()->json($productBrandList);
}
}
