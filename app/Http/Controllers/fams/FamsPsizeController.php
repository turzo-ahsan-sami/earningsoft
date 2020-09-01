<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPsize;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsPsizeController extends Controller
{
  public function index(){
    $productSizes = FamsPsize::all();
    return view('fams/product/productSetting/size/viewPsize',['productSizes' => $productSizes]);
  }

  public function addPsize(){
    return view('fams/product/productSetting/size/addPsize');
  }

  public function storeSize(Request $req) 
  {
   $rules = array(
    'name' => 'required |  unique:fams_product_size'
  );
   $attributeNames = array(
    'name' => 'Product Size'
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $create = FamsPsize::create($req->all());
    $logArray = array(
      'moduleId'  => 2,
      'controllerName'  => 'FamsPsizeController',
      'tableName'  => 'fams_product_size',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('fams_product_size')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editSize(Request $req) {
 $rules = array(
  'name' => 'required |  unique:fams_product_size,name,'.$req->id
);
 $attributeNames = array(
  'name' => 'Product Size'
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = FamsPsize::find ($req->id);
  $productSize = FamsPsize::find ($req->id);
  $productSize->name = $req->name;
  $productSize->save();
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPsizeController',
    'tableName'  => 'fams_product_size',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);

  return response()->json("success");
}
}

 //delete
public function deleteSize(Request $req) {
 $previousdata=FamsPsize::find($req->id);
 FamsPsize::find($req->id)->delete();
 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsPsizeController',
  'tableName'  => 'fams_product_size',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return redirect('viewFamsPsize');
}

public function productModelChange(Request $req){
  $productBrandList = \ DB::table('fams_product_model')->where('productBrandId',$req->productBrandId)->pluck('name', 'id');
  return response()->json($productBrandList);
}
}
