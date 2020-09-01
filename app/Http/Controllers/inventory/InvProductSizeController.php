<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductSize;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductSizeController extends Controller
{
  public function index(){
    $productSizes = InvProductSize::all();
    return view('inventory/product/productSetting/size/viewProductSize',['productSizes' => $productSizes]);
  }

  public function addProductSize(){
    return view('inventory/product/productSetting/size/addProductSize');
  }

  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required|unique:inv_product_size',
    'productGroupId' => 'required',
    'productCategoryId' => 'required',
    'productSubCategoryId' => 'required',
    'productBrandId' => 'required',
    'productModelId' => 'required'
  );
   $attributeNames = array(
    'name' => 'Product Size',
    'productGroupId' => 'Product Group',
    'productCategoryId' => 'Product Category',
    'productSubCategoryId' => 'Product Subcategory',
    'productBrandId' => 'Product Brand',
    'productModelId' => 'Product Model'
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = InvProductSize::create($req->all());
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductSizeController',
      'tableName'  => 'inv_product_size',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('inv_product_size')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {

 $rules = array(
  'name' => 'required|unique:inv_product_size,name,'.$req->id,
  'productGroupId' => 'required',
  'productCategoryId' => 'required',
  'productSubCategoryId' => 'required',
  'productBrandId' => 'required',
  'productModelId' => 'required'
);
 $attributeNames = array(
  'name' => 'Product Size',
  'productGroupId' => 'Product Group',
  'productCategoryId' => 'Product Category',
  'productSubCategoryId' => 'Product Subcategory',
  'productBrandId' => 'Product Brand',
  'productModelId' => 'Product Model'
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
 $previousdata = InvProductSize::find ($req->id);
 $productSize = InvProductSize::find ($req->id);
 $productSize->name = $req->name;
 $productSize->productGroupId = $req->productGroupId;
 $productSize->productCategoryId = $req->productCategoryId;
 $productSize->productSubCategoryId = $req->productSubCategoryId;
 $productSize->productBrandId = $req->productBrandId;
 $productSize->productModelId = $req->productModelId;
 $productSize->save();

 $productGroupName       = DB::table('inv_product_group')->select('name')->where('id',$req->productGroupId)->first();
 $productCategoryName    = DB::table('inv_product_category')->select('name')->where('id',$req->productCategoryId)->first();
 $productSubCategoryName = DB::table('inv_product_sub_category')->select('name')->where('id',$req->productSubCategoryId)->first();
 $productBrandName       = DB::table('inv_product_brand')->select('name')->where('id',$req->productBrandId)->first();
 $productModelName       = DB::table('inv_product_model')->select('name')->where('id',$req->productModelId)->first();
 $data = array(
  'productSize'             => $productSize,
  'productGroupName'        => $productGroupName,
  'productCategoryName'     => $productCategoryName,
  'productSubCategoryName'  => $productSubCategoryName,
  'productBrandName'        => $productBrandName,
  'productModelName'        => $productModelName,
  'slno'                    => $req->slno
);
 $logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvProductSizeController',
  'tableName'  => 'inv_product_size',
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
 $previousdata=InvProductSize::find($req->id);
 InvProductSize::find($req->id)->delete();
 $logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvProductSizeController',
  'tableName'  => 'inv_product_model',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response()->json();
}

public function productModelChange(Request $req){
  $productBrandList = DB::table('inv_product_model')->where('productBrandId',$req->productBrandId)->pluck('name', 'id');
  return response()->json($productBrandList);
}
}
