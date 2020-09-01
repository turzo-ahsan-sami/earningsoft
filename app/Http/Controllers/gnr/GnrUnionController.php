<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\gnr\GnrUnion;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\gnr\GnrWorkingArea;

class GnrUnionController extends Controller {

  public $route;

  public function __construct() {

    $this->route = $this->layoutDynamic();
  }

  public function layoutDynamic() {

    $path = Route::current()->action['prefix'];

            // dd($path);

    if($path=='/mfn') {
      $layout = 'layouts/microfin_layout';
    } elseif($path == '/acc') {
      $layout = 'layouts/acc_layout';
    } elseif($path == '/gnr'){
      $layout = 'layouts/gnr_layout';
    } elseif($path == '/inv'){
      $layout = 'layouts/inventory_layout';
    } elseif($path == '/fams'){
      $layout = 'layouts/fams_layout';
    } elseif($path == '/pos'){
      $layout = 'layouts/pos_layout';
    }

    $route = array(
      'layout'        => $layout,
      'path'          => $path
    );

            // dd($route);

    return $route;

  }

  public function index(){
    $unions = GnrUnion::all();
    return view('gnr.tools.address.union.viewUnion', ['route'=>$this->route, 'unions' => $unions]);
  }

  public function addUnion(){
    return view('gnr.tools.address.union.addUnion', ['route'=>$this->route]);
  }

  public function districtChange(Request $req){
    $divisionId = DB::table('gnr_district')->where('divisionId',$req->divisionId)->pluck('name', 'id');
    return response()->json($divisionId);
  }

  public function upazilaChange(Request $req){
    $districtId = DB::table('gnr_upzilla')->where('districtId',$req->districtId)->pluck('name', 'id');
    return response()->json($districtId);
  }
//insert function
  public function addItem(Request $req)
  {
   $rules = array(
    'name'       => 'required',
    'divisionId' => 'required',
    'districtId' => 'required',
    'upzillaId'  => 'required'
  );
   $attributeNames = array(
     'name'       => 'Union Name',
     'divisionId' => 'Division Name',
     'districtId' => 'District Name',
     'upzillaId'  => 'Upzilla Name'
   );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $req->request->add(['createdDate' => $now]);
    $create = GnrUnion::create($req->all());
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrUnionController',
      'tableName'  => 'gnr_union',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_union')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200);
  }
}

public function editItem(Request $req) {
 $rules = array(
  'name' => 'required',
  'divisionId' => 'required',
  'districtId' => 'required',
  'upzillaId' => 'required'
);
 $attributeNames = array(
   'name'       => 'Union Name',
   'divisionId' => 'Division Name',
   'districtId' => 'District Name',
   'upzillaId'  => 'Upzilla Name'
 );
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = GnrUnion::find ($req->id);
  $union = GnrUnion::find ($req->id);
  $union->name = $req->name;
  $union->divisionId = $req->divisionId;
  $union->districtId = $req->districtId;
  $union->upzillaId = $req->upzillaId;
  $union->save();

  $upzillaName  = DB::table('gnr_upzilla')->select('name')->where('id',$req->upzillaId)->first();
  $districtName = DB::table('gnr_district')->select('name')->where('id',$req->districtId)->first();
  $divisionName = DB::table('gnr_division')->select('name')->where('id',$req->divisionId)->first();

  $data = array(
    'union'              => $union,
    'upzillaName'        => $upzillaName,
    'districtName'       => $districtName,
    'divisionName'       => $divisionName,
    'slno'               => $req->slno
  );
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrUnionController',
    'tableName'  => 'gnr_union',
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

  $isAssigned = (int) DB::table('gnr_village')
  ->where('unionId', $req->id)
  ->value('id');

  if ($isAssigned > 0) {
    $data = array(
      'responseTitle' => 'Warning!',
      'responseText'  => 'You can not delete this because it is assign to Village.'
    );

    return response()->json($data);

  }


      // NEED TO CHECK WHAT IF AN UNION IS ASSIGNED IN A WORKING AREA OR NOT
  $previousdata=GnrWorkingArea::find($req->id);
  $countUnion = GnrWorkingArea::where('unionId', $req->id)->count('id');

  if ($countUnion > 0) {
    $data = array(
      'responseTitle' => 'Warning!',
      'responseText'  => 'This Union is already assigned in working area!'
    );

    return response()->json($data);
  }

  $data = array(
    'responseTitle' => 'Success!',
    'responseText'  => 'You have successfully delete this union!'
  );

  GnrUnion::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrUnionController',
    'tableName'  => 'gnr_union',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
}
}
