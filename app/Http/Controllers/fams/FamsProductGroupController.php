<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;
use App\Http\Controllers\gnr\Service;

use App\Http\Requests;
use App\fams\FamsPgroup;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsProductGroupController extends Controller
{
  public function index()
  {
    $productGroups = FamsPgroup::all();
    return view('fams/product/productSetting/group/viewFamsPgroup',['productGroups' => $productGroups]);
  }
  public function addFamsPGroup()
  {
    return view('fams/product/productSetting/group/addFamsPGroup');
  }

  //insert function
  public function addItem(Request $req) 
  {
    $rules = array(
      'name'      => 'required',
      'groupCode' => 'required| digits:2 | unique:fams_product_group'
    );
    $attributeNames = array(
     'name'      => 'Product Group',
     'groupCode' => 'Group Code'  
   );
    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
      $create = FamsPgroup::create($req->all());
      $logArray = array(
        'moduleId'  => 2,
        'controllerName'  => 'FamsProductGroupController',
        'tableName'  => 'fams_product_group',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('fams_product_group')->max('id')]
      );
      Service::createLog($logArray);

      return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
    }
  }
   //edit function
  public function editItem(Request $req) {
   $rules = array(
    'name' => 'required',
    'groupCode' => 'required | digits:2 | unique:fams_product_group,groupCode,'.$req->id,
  );
   $attributeNames = array(
     'name'      => 'Product Group',
     'groupCode' => 'Group Code'   
   );

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
   $previousdata = FamsPgroup::find ($req->id);
   $productGroup = FamsPgroup::find ($req->id);
   $productGroup->name = $req->name;
   $productGroup->groupCode = $req->groupCode;
   $productGroup->save();
   $data = array(
    'productGroup'  => $productGroup,
    'slno'          => $req->slno 
  );
   $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsProductGroupController',
    'tableName'  => 'fams_product_group',
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
  $previousdata=FamsPgroup::find($req->id);
  FamsPgroup::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsProductGroupController',
    'tableName'  => 'fams_product_group',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}   

}
