<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\pos\PosProductSize;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosProductSizeController extends Controller
{
    public function index(){
      $productSizes = PosProductSize::all();
      return view('pos/product/productSetting/size/viewProductSize',['productSizes' => $productSizes]);
    }

    public function addProductSize(){
      return view('pos/product/productSetting/size/addProductSize');
    }

  public function addItem(Request $req) 
  {
         $rules = array(
                'name' => 'required|unique:pos_product_size',
                'productGroupId' => 'required',
                'productCategoryId' => 'required',
                'productSubCategoryId' => 'required',
                'productBrandId' => 'required',
                'productModelId' => 'required'
              );
 			$attributeNames = array(
              'name' => 'Product Size',
              'productGroupId' => 'Product Group',
              'productCategoryId' => 'Product Category',
              'productSubCategoryId' => 'Product Subcategory',
              'productBrandId' => 'Product Brand',
              'productModelId' => 'Product Model'
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  	else{
            $now = Carbon::now();
            $req->request->add(['createdDate' => $now]);
            $create = PosProductSize::create($req->all());
    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }

//edit function
    public function editItem(Request $req) {

 $rules = array(
                'name' => 'required|unique:pos_product_size,name,'.$req->id,
                'productGroupId' => 'required',
                'productCategoryId' => 'required',
                'productSubCategoryId' => 'required',
                'productBrandId' => 'required',
                'productModelId' => 'required'
              );
$attributeNames = array(
              'name' => 'Product Size',
              'productGroupId' => 'Product Group',
              'productCategoryId' => 'Product Category',
              'productSubCategoryId' => 'Product Subcategory',
              'productBrandId' => 'Product Brand',
              'productModelId' => 'Product Model'
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
      $productSize = PosProductSize::find ($req->id);
      $productSize->name = $req->name;
      $productSize->productGroupId = $req->productGroupId;
      $productSize->productCategoryId = $req->productCategoryId;
      $productSize->productSubCategoryId = $req->productSubCategoryId;
      $productSize->productBrandId = $req->productBrandId;
      $productSize->productModelId = $req->productModelId;
      $productSize->save();

      $productGroupName       = DB::table('pos_product_group')->select('name')->where('id',$req->productGroupId)->first();
      $productCategoryName    = DB::table('pos_product_category')->select('name')->where('id',$req->productCategoryId)->first();
      $productSubCategoryName = DB::table('pos_product_sub_category')->select('name')->where('id',$req->productSubCategoryId)->first();
      $productBrandName       = DB::table('pos_product_brand')->select('name')->where('id',$req->productBrandId)->first();
      $productModelName       = DB::table('pos_product_model')->select('name')->where('id',$req->productModelId)->first();
      $data = array(
          'productSize'             => $productSize,
          'productGroupName'        => $productGroupName,
          'productCategoryName'     => $productCategoryName,
          'productSubCategoryName'  => $productSubCategoryName,
          'productBrandName'        => $productBrandName,
          'productModelName'        => $productModelName,
          'slno'                    => $req->slno
        );
      return response()->json($data);
    }
  }

 //delete
    public function deleteItem(Request $req) {
      PosProductSize::find($req->id)->delete();
      return response()->json();
    }

    public function productModelChange(Request $req){
        $productBrandList = DB::table('pos_product_model')->where('productBrandId',$req->productBrandId)->pluck('name', 'id');
        return response()->json($productBrandList);
    }
   
}
