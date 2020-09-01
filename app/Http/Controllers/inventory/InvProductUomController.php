<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductUom;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductUomController extends Controller
{
  public function index(){
    $productUoms = InvProductUom::all();
    return view('inventory/product/productSetting/uom/viewProductUom',['productUoms' => $productUoms]);
  }

  public function addProductUom(){
    return view('inventory/product/productSetting/uom/addProductUom');
  }
//insert function
  public function addItem(Request $req) 
  {
    $rules = array(
      'name' => 'required|unique:inv_product_uom',
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
      $create = InvProductUom::create($req->all());
      $logArray = array(
        'moduleId'  => 1,
        'controllerName'  => 'InvProductUomController',
        'tableName'  => 'inv_product_uom',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('inv_product_uom')->max('id')]
      );
      Service::createLog($logArray);
      return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
    }
  }

//edit function
  public function editItem(Request $req) {
    $rules = array(
      'name' => 'required|unique:inv_product_uom,name,'.$req->id,
    );
    $attributeNames = array(
     'name'    => 'UOM' 
   );
    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
     $previousdata = InvProductUom::find ($req->id);
     $productUom = InvProductUom::find ($req->id);
     $productUom->name = $req->name;
     $productUom->save();

     $data = array(
      'productUom'    => $productUom,
      'slno'          => $req->slno
    );
     $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductUomController',
      'tableName'  => 'inv_product_uom',
      'operation'  => 'update',
      'previousData'  => $previousdata,
      'primaryIds'  => [$previousdata->id]
    );
     Service::createLog($logArray);
     return response()->json($data);
   }

 }

 //delete
 public function deleteItem(Request $req) {
   $previousdata=InvProductUom::find($req->id);
   InvProductUom::find($req->id)->delete();
   $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductUomController',
    'tableName'  => 'inv_product_uom',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
   Service::createLog($logArray);
   return response()->json();
 }

}
