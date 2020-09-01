<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPcategory;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsProductCategoryController extends Controller
{
  public function index()
  {
    $productCategories = FamsPcategory::all();
    return view('fams/product/productSetting/category/viewFamsPcategory',['productCategories' => $productCategories]);
  }
  public function addFamsPctg()
  {
    return view('fams/product/productSetting/category/addFamsPcategory');
  }
//insert function
  public function addItem(Request $req) 
  {
   $rules = array(
    'name' => 'required|unique:fams_product_category',
    'categoryCode' => 'required|digits:2|unique:fams_product_category',
    'productGroupId' => 'required'
  );
   $attributeNames = array(
     'name' => 'Product Category',
     'categoryCode' => 'Product Category Code',
     'productGroupId' => 'Product Group'
   );

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $create = FamsPcategory::create($req->all());
    $logArray = array(
      'moduleId'  => 2,
      'controllerName'  => 'FamsProductCategoryController',
      'tableName'  => 'fams_product_category',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('fams_product_category')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editItem(Request $req) {
 $rules = array(
  'name' => 'required|unique:fams_product_category,name,'.$req->id,
  'categoryCode' => 'required | digits:2 | unique:fams_product_category,categoryCode,'.$req->id,
  'productGroupId' => 'required'
);

 $attributeNames = array(
   'name' => 'Product Category',
   'categoryCode' => 'Product Category Code',
   'productGroupId' => 'Product Group'
 );
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = FamsPcategory::find ($req->id);
  $productCategory = FamsPcategory::find ($req->id);
  $productCategory->name = $req->name;
  $productCategory->categoryCode = $req->categoryCode;
  $productCategory->productGroupId = $req->productGroupId;
  $productCategory->save();

  $productGroupName = \DB::table('fams_product_group')->select('name')->where('id',$req->productGroupId)->first();
  $data = array(
    'productCategory'  => $productCategory,
    'productGroupName' => $productGroupName,
    'slno'             => $req->slno
  );
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsProductCategoryController',
    'tableName'  => 'fams_product_category',
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
 $previousdata=FamsPcategory::find($req->id);
 FamsPcategory::find($req->id)->delete();
 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsProductCategoryController',
  'tableName'  => 'fams_product_category',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response()->json();
}   
}
