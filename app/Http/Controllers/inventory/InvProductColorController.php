<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductColor;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductColorController extends Controller
{
  public function index(){
    $productColors = InvProductColor::all();
    return view('inventory/product/productSetting/color/viewProductColor',['productColors' => $productColors]);
  }

  public function addProductColor(){
    return view('inventory/product/productSetting/color/addProductColor');
  }

  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required|unique:inv_product_color',
    'productGroupId' => 'required',
    'productCategoryId' => 'required',
    'productSubCategoryId' => 'required',
    'productBrandId' => 'required',
    'productModelId' => 'required',
    'productSizeId' => 'required'
  );
   $attributeNames = array(
    'name' => 'Product Color',
    'productGroupId' => 'Product Group',
    'productCategoryId' => 'Product Category',
    'productSubCategoryId' => 'Product Subcategory',
    'productBrandId' => 'Product Brand',
    'productModelId' => 'Product Model',
    'productSizeId' => 'Product Size'
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = InvProductColor::create($req->all());
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductColorController',
      'tableName'  => 'inv_product_color',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('inv_product_color')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {

 $rules = array(
  'name' => 'required|unique:inv_product_color,name,'.$req->id,
  'productGroupId' => 'required',
  'productCategoryId' => 'required',
  'productSubCategoryId' => 'required',
  'productBrandId' => 'required',
  'productModelId' => 'required',
  'productSizeId' => 'required'
);
 $attributeNames = array(
  'name' => 'Product Color',
  'productGroupId' => 'Product Group',
  'productCategoryId' => 'Product Category',
  'productSubCategoryId' => 'Product Subcategory',
  'productBrandId' => 'Product Brand',
  'productModelId' => 'Product Model',
  'productSizeId' => 'Product Size'
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
 $previousdata = InvProductColor::find ($req->id);
 $productColor = InvProductColor::find ($req->id);
 $productColor->name = $req->name;
 $productColor->productGroupId = $req->productGroupId;
 $productColor->productCategoryId = $req->productCategoryId;
 $productColor->productSubCategoryId = $req->productSubCategoryId;
 $productColor->productBrandId = $req->productBrandId;
 $productColor->productSizeId = $req->productSizeId;
 $productColor->productModelId = $req->productModelId;
 $productColor->save();

 $productGroupName       = DB::table('inv_product_group')->select('name')->where('id',$req->productGroupId)->first();
 $productCategoryName    = DB::table('inv_product_category')->select('name')->where('id',$req->productCategoryId)->first();
 $productSubCategoryName = DB::table('inv_product_sub_category')->select('name')->where('id',$req->productSubCategoryId)->first();
 $productBrandName       = DB::table('inv_product_brand')->select('name')->where('id',$req->productBrandId)->first();
 $productModelName       = DB::table('inv_product_model')->select('name')->where('id',$req->productModelId)->first();
 $productSizeName        = DB::table('inv_product_size')->select('name')->where('id',$req->productSizeId)->first();
 $data = array(
  'productColor'            => $productColor,
  'productGroupName'        => $productGroupName,
  'productCategoryName'     => $productCategoryName,
  'productSubCategoryName'  => $productSubCategoryName,
  'productBrandName'        => $productBrandName,
  'productModelName'        => $productModelName,
  'productSizeName'         => $productSizeName,
  'slno'                    => $req->slno
);
 $logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvProductColorController',
  'tableName'  => 'inv_product_color',
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
  $previousdata=InvProductColor::find($req->id);
  InvProductColor::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductColorController',
    'tableName'  => 'inv_product_model',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}

public function productSizeChange(Request $req){
  $productBrandList = DB::table('inv_product_size')->where('productModelId',$req->productModelId)->pluck('name', 'id');
  return response()->json($productBrandList);
}
}
