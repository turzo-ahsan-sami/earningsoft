<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\pos\PosProductSubCategory;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosProductSubCategoryController extends Controller
{
   public function index(){
      $productSubCategories = PosProductSubCategory::all();
      return view('pos/product/productSetting/subCategory/viewProductSubCategory',['productSubCategories' => $productSubCategories]);
    }

    public function addProductSubCategory(){
      return view('pos/product/productSetting/subCategory/addProductSubcategory');
    }
//insert function
  public function addItem(Request $req) 
  {
          $rules = array(
                'name' => 'required|unique:pos_product_sub_category',
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
            $create = PosProductSubCategory::create($req->all());
    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }

//edit function
    public function editItem(Request $req) {

 $rules = array(
                'name' => 'required|unique:pos_product_sub_category,name,'.$req->id,
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
      else {
      $productSubCategory = PosProductSubCategory::find ($req->id);
      $productSubCategory->name = $req->name;
      $productSubCategory->productGroupId = $req->productGroupId;
      $productSubCategory->productCategoryId = $req->productCategoryId;
      $productSubCategory->save();

      $productGroupName     = DB::table('pos_product_group')->select('name')->where('id',$req->productGroupId)->first();
      $productCategoryName  = DB::table('pos_product_category')->select('name')->where('id',$req->productCategoryId)->first();
      $data = array(
          'productSubCategory'  => $productSubCategory,
          'productGroupName'    => $productGroupName,
          'productCategoryName' => $productCategoryName,
          'slno'                => $req->slno
        );
      return response()->json($data);
    }
  }

 //delete
    public function deleteItem(Request $req) {
      PosProductSubCategory::find($req->id)->delete();
      return response()->json();
    }   
}
