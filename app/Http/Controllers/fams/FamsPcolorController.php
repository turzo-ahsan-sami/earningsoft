<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPcolor;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsPcolorController extends Controller
{
  public function index(){
    $productColors = FamsPcolor::all();
    return view('fams/product/productSetting/color/viewFamsPcolor',['productColors' => $productColors]);
  }

  public function addFamsPcolor(){
    return view('fams/product/productSetting/color/addFamsPcolor');
  }

  public function storeColor(Request $req) 
  {
   $rules = array(
    'name' => 'required |  unique:fams_product_color'
  );
   $attributeNames = array(
    'name' => 'Product Color'
  );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $create = FamsPcolor::create($req->all());
    $logArray = array(
      'moduleId'  => 2,
      'controllerName'  => 'FamsPcolorController',
      'tableName'  => 'fams_product_color',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('fams_product_color')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
  }
}

//edit function
public function editColor(Request $req) {
 $rules = array(
  'name' => 'required |  unique:fams_product_color,name,'.$req->id
);
 $attributeNames = array(
  'name' => 'Product Color'
);
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
 $previousdata = FamsPcolor::find($req->id);
 $productColor = FamsPcolor::find($req->id);
 $productColor->name = $req->name;
 $productColor->save();
 $logArray = array(
  'moduleId'  => 2,
  'controllerName'  => 'FamsPcolorController',
  'tableName'  => 'fams_product_color',
  'operation'  => 'update',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);

 return response()->json("success");
}
}

 //delete
public function deleteColor(Request $req) {
  $previousdata=FamsPcolor::find($req->id);
  FamsPcolor::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPcolorController',
    'tableName'  => 'fams_product_color',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return redirect('viewFamsPcolor');
}

public function productSizeChange(Request $req){
  $productBrandList = \ DB::table('fams_product_size')->where('productModelId',$req->productModelId)->pluck('name', 'id');
  return response()->json($productBrandList);
}
}
