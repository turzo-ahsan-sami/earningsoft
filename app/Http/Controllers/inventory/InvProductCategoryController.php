<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductCategory;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductCategoryController extends Controller
{
  public function index(){
    $productCategories = InvProductCategory::all();
    return view('inventory/product/productSetting/category/viewProductCategory',['productCategories' => $productCategories]);
  }

  public function addProductCategory(){
    return view('inventory/product/productSetting/category/addProductCategory');
  }
//insert function
  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required|unique:inv_product_category',
    'productGroupId' => 'required'
  );
   $attributeNames = array(
     'productGroupId' => 'Product Group',
     'name' => 'Product Category' 
   );

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = InvProductCategory::create($req->all());
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductCategoryController',
      'tableName'  => 'inv_product_category',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('inv_product_category')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {
 $rules = array(
  'name' => 'required|unique:inv_product_category,name,'.$req->id,
  'productGroupId' => 'required'
);

 $attributeNames = array(
   'name' => 'Product Category',
   'productGroupId' => 'Product Group'
 );
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
 $previousdata = InvProductCategory::find ($req->id);
 $productCategory = InvProductCategory::find ($req->id);
 $productCategory->name = $req->name;
 $productCategory->productGroupId = $req->productGroupId;
 $productCategory->save();

 $productGroupName = DB::table('inv_product_group')->select('name')->where('id',$req->productGroupId)->first();
 $data = array(
  'productCategory'  => $productCategory,
  'productGroupName' => $productGroupName,
  'slno'             => $req->slno
);
 $logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvProductCategoryController',
  'tableName'  => 'inv_product_category',
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
  $previousdata=InvProductCategory::find($req->id);
  InvProductCategory::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductCategoryController',
    'tableName'  => 'inv_product_category',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}   
}
