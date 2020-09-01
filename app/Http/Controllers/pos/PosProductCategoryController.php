<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\pos\PosProductCategory;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosProductCategoryController extends Controller
{
    public function index(){
      $productCategories = PosProductCategory::all();
      return view('pos/product/productSetting/category/viewProductCategory',['productCategories' => $productCategories]);
    }

    public function addProductCategory(){
      return view('pos/product/productSetting/category/addProductCategory');
    }
//insert function
  public function addItem(Request $req) 
  {
         $rules = array(
                'name' => 'required|unique:pos_product_category',
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
            $create = PosProductCategory::create($req->all());
    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }

//edit function
public function editItem(Request $req) {
 $rules = array(
                'name' => 'required|unique:pos_product_category,name,'.$req->id,
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
      $productCategory = PosProductCategory::find ($req->id);
      $productCategory->name = $req->name;
      $productCategory->productGroupId = $req->productGroupId;
      $productCategory->save();

      $productGroupName = DB::table('pos_product_group')->select('name')->where('id',$req->productGroupId)->first();
      $data = array(
        'productCategory'  => $productCategory,
        'productGroupName' => $productGroupName,
        'slno'             => $req->slno
        );
      return response()->json($data);
    }
    }

 //delete
    public function deleteItem(Request $req) {
      PosProductCategory::find($req->id)->delete();
      return response()->json();
    }   
}
