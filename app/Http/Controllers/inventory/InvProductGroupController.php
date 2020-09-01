<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvProductGroup;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class InvProductGroupController extends Controller
{
  public function index(){
    $productGroups = InvProductGroup::all();
    return view('inventory/product/productSetting/group/viewProductGroup',['productGroups' => $productGroups]);
  }

  public function addProductGroup(){
    return view('inventory/product/productSetting/group/addProductGroup');
  }
//insert function
  public function addItem(Request $req) 
  {
    $rules = array(
      'name' => 'required|unique:inv_product_group'
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
      $create = InvProductGroup::create($req->all());
      $logArray = array(
        'moduleId'  => 1,
        'controllerName'  => 'InvProductGroupController',
        'tableName'  => 'inv_product_group',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('inv_product_group')->max('id')]
      );
      Service::createLog($logArray);
      return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
    }
  }

    //edit function
  public function editItem(Request $req) {
    $rules = array(
     'name' => 'required|unique:inv_product_group,name,'.$req->id,
   );
    $attributeNames = array(
     'name' => 'Product Group'   
   );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
     return response::json(array('errors' => $validator->getMessageBag()->toArray()));
   else{
     $previousdata = InvProductGroup::find ($req->id);
     $productGroup = InvProductGroup::find ($req->id);
     $productGroup->name = $req->name;
     $productGroup->save();
     $data = array(
      'productGroup'  => $productGroup,
      'slno'          => $req->slno 
    );
     $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvProductGroupController',
      'tableName'  => 'inv_product_group',
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
  $previousdata=InvProductGroup::find($req->id);
  InvProductGroup::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 1,
    'controllerName'  => 'InvProductGroupController',
    'tableName'  => 'inv_product_group',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}   
}
