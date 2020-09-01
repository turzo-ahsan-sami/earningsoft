<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsPgroup;
use App\fams\FamsPname;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;

class FamsProductNameController extends Controller
{
    public function index()
    {
      $productNames = FamsPname::all();
      return view('fams/product/productSetting/productName/viewFamsPname',['productNames' => $productNames]);
    }
    public function addProductName()
    {
        return view('fams/product/productSetting/productName/addFamsPname');
    }

  //insert function
  public function storeProductName(Request $req)
  {
      $rules = array(
        'productGroupId' => 'required',
        'productCategoryId' => 'required',
        'productSubCategoryId' => 'required',
        'productTypeId' => 'required',
        'name' => 'required | unique:fams_product_name',
        'productNameCode' => 'required| digits:3 | unique:fams_product_name'
      );
      $attributeNames = array(
          'productGroupId' => 'Product Group',
          'productCategoryId' => 'Product Category',
          'productSubCategoryId' => 'Product Sub Category',
          'productTypeId' => 'Product Type',
          'name' => 'Product Name',
          'productNameCode' => 'Product Name Code'
      );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
          return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
          DB::table('fams_product_name')->insert(['name'=>$req->name,'productNameCode'=>$req->productNameCode,'productGroupId'=>$req->productGroupId,'productCategoryId'=>$req->productCategoryId,'productSubCategoryId'=>$req->productSubCategoryId,'productTypeId'=>$req->productTypeId]);
          
          return response()->json("success");
      }


  }
   //edit function
 public function editProductName(Request $req) {

     $rules = array(
         'productGroupId' => 'required',
        'productCategoryId' => 'required',
        'productSubCategoryId' => 'required',
        'productTypeId' => 'required',
        'name' => 'required|unique:fams_product_name,name,'.$req->productNameId,
        'productNameCode' => 'required|digits:3|unique:fams_product_name,productNameCode,'.$req->productNameId
     );
     $attributeNames = array(
         'productGroupId' => 'Product Group',
          'productCategoryId' => 'Product Category',
          'productSubCategoryId' => 'Product Sub Category',
          'productTypeId' => 'Product Type',
          'name' => 'Product Name Name',
          'productNameCode' => 'Product Name Code'
     );

     $validator = Validator::make ( Input::all (), $rules);

     $validator->setAttributeNames($attributeNames);

     if ($validator->fails()){
         return response::json(array('errors' => $validator->getMessageBag()->toArray()));
       }


     else{
      
      $pName = FamsPname::find($req->productNameId);
      $pName->name = $req->name;
      $pName->productNameCode = $req->productnameCode;
      $pName->productGroupId = $req->productGroupId;
      $pName->productCategoryId = $req->productCategoryId;
      $pName->productSubCategoryId = $req->productSubCategoryId;
      $pName->productTypeId = $req->productTypeId;
      $pName->save();         

      return response()->json("success");
     }


  }

 //delete
    public function deleteProductName(Request $req) {

        DB::table('fams_product_item')->where('id',$req->itemId)->delete();
        return redirect('viewFamsPitem');
    }   

}
