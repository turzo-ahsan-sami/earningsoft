<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductSubCategory;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductSubCategoryController extends Controller
{
 public function index(){
  $productSubCategories = InvProductSubCategory::all();
  return view('inventory/product/productSetting/subCategory/viewProductSubCategory',['productSubCategories' => $productSubCategories]);
}

public function addProductSubCategory(){
  return view('inventory/product/productSetting/subCategory/addProductSubcategory');
}
//insert function
public function addItem(Request $req) 
{
  $rules = array(
    'name' => 'required|unique:inv_product_sub_category',
    'productGroupId' => 'required',
    'productCategoryId' => 'required'
  );
  $attributeNames = array(
    'name' => 'Product Subcategory',
    'productGroupId' => 'Product Group',
    'productCategoryId' => 'Product Category' 
  );

  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = InvProductSubCategory::create($req->all());
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductSubCategoryController',
      'tableName'  => 'inv_product_sub_category',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('inv_product_sub_category')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {

 $rules = array(
  'name' => 'required|unique:inv_product_sub_category,name,'.$req->id,
  'productGroupId' => 'required',
  'productCategoryId' => 'required'
);
 $attributeNames = array(
  'name' => 'Product Subcategory',
  'productGroupId' => 'Product Group',
  'productCategoryId' => 'Product Category' 
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = InvProductSubCategory::find ($req->id);
  $productSubCategory = InvProductSubCategory::find ($req->id);
  $productSubCategory->name = $req->name;
  $productSubCategory->productGroupId = $req->productGroupId;
  $productSubCategory->productCategoryId = $req->productCategoryId;
  $productSubCategory->save();

  $productGroupName     = DB::table('inv_product_group')->select('name')->where('id',$req->productGroupId)->first();
  $productCategoryName  = DB::table('inv_product_category')->select('name')->where('id',$req->productCategoryId)->first();
  $data = array(
    'productSubCategory'  => $productSubCategory,
    'productGroupName'    => $productGroupName,
    'productCategoryName' => $productCategoryName,
    'slno'                => $req->slno
  );
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductSubCategoryController',
    'tableName'  => 'inv_product_sub_category',
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
  $previousdata=InvProductSubCategory::find($req->id);
  InvProductSubCategory::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductSubCategoryController',
    'tableName'  => 'inv_product_sub_category',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}   
}
