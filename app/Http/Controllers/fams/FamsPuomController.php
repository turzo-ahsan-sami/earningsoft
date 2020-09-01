<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsPuom;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class FamsPuomController extends Controller
{
  public function index(){
    $productUoms = FamsPuom::all();
    return view('fams/product/productSetting/uom/viewFamsPUom',['productUoms' => $productUoms]);
  }

  public function addFamsPUom(){
    return view('fams/product/productSetting/uom/addFamsPUom');
  }
//insert function
  public function addItem(Request $req) 
  {
    $rules = array(
      'name' => 'required'
    );
    $attributeNames = array(
     'name'    => 'UOM' 
   );
    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
      $create = FamsPuom::create($req->all());
      $logArray = array(
        'moduleId'  => 2,
        'controllerName'  => 'FamsPuomController',
        'tableName'  => 'fams_product_uom',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('fams_product_uom')->max('id')]
      );
      Service::createLog($logArray);
      return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
    }
  }

//edit function
  public function editItem(Request $req) {
    $rules = array(
      'name' => 'required'
    );
    $attributeNames = array(
     'name'    => 'UOM' 
   );
    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
      $previousdata = FamsPuom::find ($req->id);
      $productUom = FamsPuom::find ($req->id);
      $productUom->name = $req->name;
      $productUom->save();

      $data = array(
        'productUom'    => $productUom,
        'slno'          => $req->slno
      );
      $logArray = array(
        'moduleId'  => 2,
        'controllerName'  => 'FamsPuomController',
        'tableName'  => 'fams_product_uom',
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
   $previousdata=FamsPuom::find($req->id);
   FamsPuom::find($req->id)->delete();
   $logArray = array(
    'moduleId'  => 2,
    'controllerName'  => 'FamsPuomController',
    'tableName'  => 'fams_product_uom',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
   Service::createLog($logArray);
   return response()->json();
 }

}
