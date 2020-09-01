<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\pos\PosProductBrand;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosProductBrandController extends Controller
{
      public function index(){
      $productBrands = PosProductBrand::all();
      return view('pos/product/productSetting/brand/viewProductBrand',['productBrands' => $productBrands]);
    }

    public function addProductBrand(){
      return view('pos/product/productSetting/brand/addProductBrand');
    }

  public function addItem(Request $req) 
  {
         $rules = array(
                'name' => 'required|unique:pos_product_brand',
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
            $create = PosProductBrand::create($req->all());
    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }

//edit function
public function editItem(Request $req) {
 $rules = array(
                'name' => 'required|unique:pos_product_brand,name,'.$req->id,
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
      $productBrand = PosProductBrand::find ($req->id);
      $productBrand->name = $req->name;
      $productBrand->productGroupId = $req->productGroupId;
      $productBrand->productCategoryId = $req->productCategoryId;
      $productBrand->productSubCategoryId = $req->productSubCategoryId;
      $productBrand->save();

      $productGroupName       = DB::table('pos_product_group')->select('name')->where('id',$req->productGroupId)->first();
      $productCategoryName    = DB::table('pos_product_category')->select('name')->where('id',$req->productCategoryId)->first();
      $productSubCategoryName = DB::table('pos_product_sub_category')->select('name')->where('id',$req->productSubCategoryId)->first();
       $data = array(
          'productBrand'            => $productBrand,
          'productGroupName'        => $productGroupName,
          'productCategoryName'     => $productCategoryName,
          'productSubCategoryName'  => $productSubCategoryName,
          'slno'                    => $req->slno
        );
      return response()->json($data);
    }
  }

 //delete
    public function deleteItem(Request $req) {
      PosProductBrand::find($req->id)->delete();
      return response()->json();
    }

    public function productCatagoryChange(Request $req){
        $productCategoryList =  DB::table('pos_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
        return response()->json($productCategoryList);
    }

    public function productSubCategoryChange(Request $req){
        $productSubCategoryList = DB::table('pos_product_sub_category')->where('productCategoryId',$req->productCategoryId)->pluck('name', 'id');
        return response()->json($productSubCategoryList);
    }
}
