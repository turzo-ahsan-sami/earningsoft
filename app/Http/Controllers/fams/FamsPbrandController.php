<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPbrand;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsPbrandController extends Controller
{
  public function index(){
    $productBrands = FamsPbrand::all();
    return view('fams/product/productSetting/brand/viewPbrand',['productBrands' => $productBrands]);
  }

  public function addPbrand(){
    return view('fams/product/productSetting/brand/addPbrand');
  }

  public function storeBrand(Request $req) 
  {
   $rules = array(
    'name' => 'required |  unique:fams_product_brand'
  );
   $attributeNames = array(
    'name' => 'Product Brand'
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $create = FamsPbrand::create($req->all());
    $logArray = array(
      'moduleId'  => 2,
      'controllerName'  => 'FamsPbrandController',
      'tableName'  => 'fams_product_brand',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('fams_product_brand')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editBrand(Request $req) {
 $rules = array(
  'name' => 'required |  unique:fams_product_brand,name,'.$req->id
);
 $attributeNames = array(
  'name' => 'Product Brand'
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = FamsPbrand::find ($req->id);
  $productBrand = FamsPbrand::find ($req->id);
  $productBrand->name = $req->name;
  $productBrand->brandCode = $req->brandCode;
  $productBrand->save();
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPbrandController',
    'tableName'  => 'fams_product_brand',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response()->json("success");
}
}

 //delete
public function deleteBrand(Request $req) {
 $previousdata=FamsPbrand::find($req->productId);
 //dd($req->id);
 DB::table('fams_product_brand')->where('id',$req->productId)->delete();

 //FamsPbrand::find($req->id)->delete();

 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsPbrandController',
  'tableName'  => 'fams_product_brand',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return redirect('viewFamsPbrand');
 //return response()->json();
}

public function productSubCategoryChange(Request $req){
  $productSubCategoryList = \ DB::table('fams_product_sub_category')->where('productCategoryId',$req->productCategoryId)->pluck('name', 'id');
  return response()->json($productSubCategoryList);
}
}
