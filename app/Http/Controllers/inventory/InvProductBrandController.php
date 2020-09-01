<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductBrand;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductBrandController extends Controller
{
  public function index(){
    $productBrands = InvProductBrand::all();
    return view('inventory/product/productSetting/brand/viewProductBrand',['productBrands' => $productBrands]);
  }

  public function addProductBrand(){
    return view('inventory/product/productSetting/brand/addProductBrand');
  }

  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required|unique:inv_product_brand',
    'productGroupId' => 'required',
    'productCategoryId' => 'required',
    'productSubCategoryId' => 'required'
  );
   $attributeNames = array(
    'name' => 'Product Brand',
    'productGroupId' => 'Product Group',
    'productCategoryId' => 'Product Category',
    'productSubCategoryId' => 'Product Subcategory'  
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = InvProductBrand::create($req->all());
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductBrandController',
      'tableName'  => 'inv_product_brand',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('inv_product_brand')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {
 $rules = array(
  'name' => 'required|unique:inv_product_brand,name,'.$req->id,
  'productGroupId' => 'required',
  'productCategoryId' => 'required',
  'productSubCategoryId' => 'required'
);
 $attributeNames = array(
  'name' => 'Product Brand',
  'productGroupId' => 'Product Group',
  'productCategoryId' => 'Product Category',
  'productSubCategoryId' => 'Product Subcategory'  
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = InvProductBrand::find ($req->id);
  $productBrand = InvProductBrand::find ($req->id);
  $productBrand->name = $req->name;
  $productBrand->productGroupId = $req->productGroupId;
  $productBrand->productCategoryId = $req->productCategoryId;
  $productBrand->productSubCategoryId = $req->productSubCategoryId;
  $productBrand->save();

  $productGroupName       = DB::table('inv_product_group')->select('name')->where('id',$req->productGroupId)->first();
  $productCategoryName    = DB::table('inv_product_category')->select('name')->where('id',$req->productCategoryId)->first();
  $productSubCategoryName = DB::table('inv_product_sub_category')->select('name')->where('id',$req->productSubCategoryId)->first();
  $data = array(
    'productBrand'            => $productBrand,
    'productGroupName'        => $productGroupName,
    'productCategoryName'     => $productCategoryName,
    'productSubCategoryName'  => $productSubCategoryName,
    'slno'                    => $req->slno
  );
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductBrandController',
    'tableName'  => 'inv_product_brand',
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
  $previousdata=InvProductBrand::find($req->id);
  InvProductBrand::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductBrandController',
    'tableName'  => 'inv_product_brand',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}

public function productCatagoryChange(Request $req){
  $productCategoryList =  DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
  return response()->json($productCategoryList);
}

public function productSubCategoryChange(Request $req){
  $productSubCategoryList = DB::table('inv_product_sub_category')->where('productCategoryId',$req->productCategoryId)->pluck('name', 'id');
  return response()->json($productSubCategoryList);
}
}
