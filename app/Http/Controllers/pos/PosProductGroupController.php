<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\pos\PosProductGroup;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosProductGroupController extends Controller
{
    public function index(){
      $productGroups = PosProductGroup::all();
      return view('pos/product/productSetting/group/viewProductGroup',['productGroups' => $productGroups]);
    }

    public function addProductGroup(){
      return view('pos/product/productSetting/group/addProductGroup');
    }
//insert function
    public function addItem(Request $req) {
      $rules = array(
                'name' => 'required|unique:pos_product_group'
              );
 			$attributeNames = array(
           'name' => 'Product Group'   
        );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  		else{     
                $now = Carbon::now();
                $req->request->add(['createdDate' => $now]);
                $create = PosProductGroup::create($req->all());
        		    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        	}
    }

    //edit function
 public function editItem(Request $req) {
	 $rules = array(
	                'name' => 'required|unique:pos_product_group,name,'.$req->id,
	              );
	 $attributeNames = array(
           'name' => 'Product Group'   
        );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
	      if ($validator->fails()) {
	          return response::json(array('errors' => $validator->getMessageBag()->toArray()));
	      } else {
  	      $productGroup = PosProductGroup::find ($req->id);
  	      $productGroup->name = $req->name;
  	      $productGroup->save();
        $data = array(
          'productGroup'  => $productGroup,
          'slno'          => $req->slno 
          );
	      return response()->json($data);
	    }
	}

 //delete
    public function deleteItem(Request $req) {
      PosProductGroup::find($req->id)->delete();
      return response()->json();
    }   
}
