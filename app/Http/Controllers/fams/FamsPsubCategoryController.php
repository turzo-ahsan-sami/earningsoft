<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPsubcategory;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsPsubCategoryController extends Controller
{
 public function index(){
  $productSubCategories = FamsPsubcategory::all();
  return view('fams/product/productSetting/subCategory/viewPsubCtg',['productSubCategories' => $productSubCategories]);
}

public function addPsubCtg(){
  return view('fams/product/productSetting/subCategory/addPsubCtg');
}
//insert function
public function addItem(Request $req) 
{
 $rules = array(
   'name' => 'required|unique:fams_product_sub_category',
   'subCategoryCode' => 'required | digits:3 |unique:fams_product_sub_category',
   'productGroupId' => 'required',
   'productCategoryId' => 'required'
 );
 $attributeNames = array(                 
   'name' => 'Product Subcategory',
   'subCategoryCode' => 'Subcategory Code',
   'productGroupId' => 'Product Group',
   'productCategoryId' => 'Product Category' 
 );

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
   return response::json(array('errors' => $validator->getMessageBag()->toArray()));
 else{             
  $create = FamsPsubcategory::create($req->all());
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPsubCategoryController',
    'tableName'  => 'fams_product_sub_category',
    'operation'  => 'insert',
    'primaryIds'  => [DB::table('fams_product_sub_category')->max('id')]
  );
  Service::createLog($logArray);
  return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
}
}

//edit function
public function editItem(Request $req) {
  $rules = array(
   'name' => 'required| unique:fams_product_sub_category,name,'.$req->id,
   'subCategoryCode' => 'required | digits:3 | unique:fams_product_sub_category,subCategoryCode,'.$req->id,
   'productGroupId' => 'required',
   'productCategoryId' => 'required'
 );
  $attributeNames = array(                 
   'name' => 'Product Subcategory',
   'subCategoryCode' => 'Subcategory Code',
   'productGroupId' => 'Product Group',
   'productCategoryId' => 'Product Category' 
 );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
   return response::json(array('errors' => $validator->getMessageBag()->toArray()));
 else{
   $previousdata = FamsPsubcategory::find ($req->id);
   $productSubCategory = FamsPsubcategory::find ($req->id);
   $productSubCategory->name = $req->name;
   $productSubCategory->subCategoryCode = $req->subCategoryCode;
   $productSubCategory->productGroupId = $req->productGroupId;
   $productSubCategory->productCategoryId = $req->productCategoryId;
   $productSubCategory->save();

   $productGroupName     = \DB::table('fams_product_group')->select('name')->where('id',$req->productGroupId)->first();
   $productCategoryName  = \DB::table('fams_product_category')->select('name')->where('id',$req->productCategoryId)->first();
   $data = array(
     'productSubCategory'  => $productSubCategory,
     'productGroupName'    => $productGroupName,
     'productCategoryName' => $productCategoryName,
     'subCategoryCode'    =>  $req->subCategoryCode,
     'slno'                => $req->slno
   );
   $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPsubCategoryController',
    'tableName'  => 'fams_product_sub_category',
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
  $previousdata=FamsPsubcategory::find($req->id);
  FamsPsubcategory::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPsubCategoryController',
    'tableName'  => 'fams_product_sub_category',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
} 

public function productCatagoryChange(Request $req)
{
  $productCategoryList = \ DB::table('fams_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
  return response()->json($productCategoryList);
}  
}
