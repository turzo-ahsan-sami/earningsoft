<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPgroup;
use App\fams\FamsPtype;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;

class FamsProductTypeController extends Controller
{
  public function index()
  {
    $productTypes = FamsPtype::all();
    return view('fams/product/productSetting/productType/viewFamsPtype',['productTypes' => $productTypes]);
  }
  public function addProductType()
  {
    return view('fams/product/productSetting/productType/addFamsPtype');
  }

  //insert function
  public function storeProductType(Request $req)
  {
    $rules = array(
      'productGroupId' => 'required',
      'productCategoryId' => 'required',
      'productSubCategoryId' => 'required',
      'name' => 'required | unique:fams_product_type',
      'productTypeCode' => 'required| digits:3 | unique:fams_product_type'
    );
    $attributeNames = array(
      'productGroupId' => 'Product Group',
      'productCategoryId' => 'Product Category',
      'productSubCategoryId' => 'Product Sub Category',
      'name' => 'Product Type Name',
      'productTypeCode' => 'Product Type Code'
    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
      DB::table('fams_product_type')->insert(['name'=>$req->name,'productTypeCode'=>$req->productTypeCode,'productGroupId'=>$req->productGroupId,'productCategoryId'=>$req->productCategoryId,'productSubCategoryId'=>$req->productSubCategoryId]);
      $logArray = array(
        'moduleId'  => 2,
        'controllerName'  => 'FamsProductTypeController',
        'tableName'  => 'fams_product_type',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('fams_product_type')->max('id')]
      );
      Service::createLog($logArray);

      return response()->json("success");
    }


  }
   //edit function
  public function editProductType(Request $req) {

   $rules = array(
     'productGroupId' => 'required',
     'productCategoryId' => 'required',
     'productSubCategoryId' => 'required',
     'name' => 'required|unique:fams_product_type,name,'.$req->productTypeId,
     'productTypeCode' => 'required|digits:3|unique:fams_product_type,productTypeCode,'.$req->productTypeId
   );
   $attributeNames = array(
     'productGroupId' => 'Product Group',
     'productCategoryId' => 'Product Category',
     'productSubCategoryId' => 'Product Sub Category',
     'name' => 'Product Type Name',
     'productTypeCode' => 'Product Type Code'
   );

   $validator = Validator::make ( Input::all (), $rules);

   $validator->setAttributeNames($attributeNames);

   if ($validator->fails()){
     return response::json(array('errors' => $validator->getMessageBag()->toArray()));
   }


   else{
    $previousdata = FamsPtype::find ($req->productTypeId);

    $pType = FamsPtype::find($req->productTypeId);
    $pType->name = $req->name;
    $pType->productTypeCode = $req->productTypeCode;
    $pType->productGroupId = $req->productGroupId;
    $pType->productCategoryId = $req->productCategoryId;
    $pType->productSubCategoryId = $req->productSubCategoryId;
    $pType->save();
    $logArray = array(
      'moduleId'  => 2,
      'controllerName'  => 'FamsProductTypeController',
      'tableName'  => 'fams_product_type',
      'operation'  => 'update',
      'previousData'  => $previousdata,
      'primaryIds'  => [$previousdata->id]
    );
    Service::createLog($logArray);         

    return response()->json("success");
  }


}

 //delete
public function deleteProductType(Request $req) {
 $previousdata=FamsPtype::find($req->id);

 DB::table('fams_product_type')->where('id',$req->itemId)->delete();
 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsProductTypeController',
  'tableName'  => 'fams_product_type',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return redirect('viewFamsPitem');
}   

}
