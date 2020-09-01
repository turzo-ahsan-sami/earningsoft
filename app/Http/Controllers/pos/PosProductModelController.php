<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\pos\PosProductModel;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class PosProductModelController extends Controller
{
    public function index(){
      $productModels = PosProductModel::all();
      return view('pos/product/productSetting/model/viewProductModel',['productModels' => $productModels]);
    }

    public function addProductModel() {
      return view('pos/product/productSetting/model/addProductModel');
    }
//insert function
  public function addItem(Request $req) {
         $rules = array(
                'name' => 'required|unique:pos_product_model',
                'productGroupId' => 'required',
                'productCategoryId' => 'required',
                'productSubCategoryId' => 'required',
                'productBrandId' => 'required'
              );
 			$attributeNames = array(
              'name' => 'Product Model',
              'productGroupId' => 'Product Group',
              'productCategoryId' => 'Product Category',
              'productSubCategoryId' => 'Product Subcategory',
              'productBrandId' => 'Product Brand' 
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  	else{
            $now = Carbon::now();
            $req->request->add(['createdDate' => $now]);
            $create = PosProductModel::create($req->all());
    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }

//edit function
public function editItem(Request $req) {
       $rules = array(
                      'name' => 'required|unique:pos_product_model,name,'.$req->id,
                      'productGroupId' => 'required',
                      'productCategoryId' => 'required',
                      'productSubCategoryId' => 'required',
                      'productBrandId' => 'required'
                    );
       $attributeNames = array(
                    'name' => 'Product Model',
                    'productGroupId' => 'Product Group',
                    'productCategoryId' => 'Product Category',
                    'productSubCategoryId' => 'Product Subcategory',
                    'productBrandId' => 'Product Brand' 
                );
        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{
        $productModel = PosProductModel::find ($req->id);
        $productModel->name = $req->name;
        $productModel->productGroupId = $req->productGroupId;
        $productModel->productCategoryId = $req->productCategoryId;
        $productModel->productSubCategoryId = $req->productSubCategoryId;
        $productModel->productBrandId = $req->productBrandId;
        $productModel->save();

        $productGroupName       = DB::table('pos_product_group')->select('name')->where('id',$req->productGroupId)->first();
        $productCategoryName    = DB::table('pos_product_category')->select('name')->where('id',$req->productCategoryId)->first();
        $productSubCategoryName = DB::table('pos_product_sub_category')->select('name')->where('id',$req->productSubCategoryId)->first();
        $productBrandName       = DB::table('pos_product_brand')->select('name')->where('id',$req->productBrandId)->first();
        $data = array(
            'productModel'            => $productModel,
            'productGroupName'        => $productGroupName,
            'productCategoryName'     => $productCategoryName,
            'productSubCategoryName'  => $productSubCategoryName,
            'productBrandName'        => $productBrandName,
            'slno'                    => $req->slno
          );
        return response()->json($data);
      }
  }

    //delete
    public function deleteItem(Request $req) {
      PosProductModel::find($req->id)->delete();
      return response()->json();
    }

    public function productBrandChange(Request $req){
        $productBrandList =  DB::table('pos_product_brand')->where('productSubCategoryId',$req->productSubCategoryId)->pluck('name', 'id');
        return response()->json($productBrandList);
    }
}
