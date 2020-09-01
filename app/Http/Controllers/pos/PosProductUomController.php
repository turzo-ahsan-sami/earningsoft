<?php

namespace App\Http\Controllers\pos;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\pos\PosProductUom;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class PosProductUomController extends Controller
{
    public function index(){
      $productUoms = PosProductUom::all();
      return view('pos/product/productSetting/uom/viewProductUom',['productUoms' => $productUoms]);
    }

    public function addProductUom(){
      return view('pos/product/productSetting/uom/addProductUom');
    }
//insert function
  public function addItem(Request $req) 
  {
          $rules = array(
                'name' => 'required|unique:pos_product_uom',
              );
   			  $attributeNames = array(
             'name'    => 'UOM' 
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  	else{
            $now = Carbon::now();
            $req->request->add(['createdDate' => $now]);
            $create = PosProductUom::create($req->all());
    		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
  }

//edit function
public function editItem(Request $req) {
  $rules = array(
                'name' => 'required|unique:pos_product_uom,name,'.$req->id,
              );
  $attributeNames = array(
             'name'    => 'UOM' 
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
      $productUom = PosProductUom::find ($req->id);
      $productUom->name = $req->name;
      $productUom->save();

      $data = array(
        'productUom'    => $productUom,
        'slno'          => $req->slno
        );
      return response()->json($data);
    }
    
    }

 //delete
    public function deleteItem(Request $req) {
      PosProductUom::find($req->id)->delete();
      return response()->json();
    }

}
