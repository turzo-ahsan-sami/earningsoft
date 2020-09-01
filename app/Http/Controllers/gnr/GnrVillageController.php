<?php 

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\gnr\GnrVillage;
use Validator;
use Response;
use DB;
use Route;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrVillageController extends Controller {

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

    //addVillage
  public function index(){
    $villages = GnrVillage::all();
    return view('gnr.tools.address.village.viewVillage',['route'=>$this->route, 'villages' => $villages]);
  }

  public function addVillage(){
    return view('gnr.tools.address.village.addVillage', ['route'=>$this->route]);
  }

  public function changeUnion(Request $req){
    $upzillaId =  DB::table('gnr_union')->where('upzillaId',$req->upzillaId)->pluck('name', 'id');
    return response()->json($upzillaId);

  }
//insert
  public function addItem(Request $req) 
  {
    $rules = array(
      'name' => 'required',
      'divisionId' => 'required',
      'districtId' => 'required',
      'upzillaId' => 'required',
      'unionId' => 'required'
    );
    $attributeNames = array(
     'name'       => 'Village Name',
     'divisionId' => 'Division Name',
     'districtId' => 'District Name',
     'upzillaId'  => 'Upzilla Name',
     'unionId'    => 'Union Name'   
   );
    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
      $now = Carbon::now();
      $req->request->add(['createdDate' => $now]);
      $create = GnrVillage::create($req->all());
      $logArray = array(
        'moduleId'  => 7,
        'controllerName'  => 'GnrVillageController',
        'tableName'  => 'gnr_village',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('gnr_village')->max('id')]
      );
      Service::createLog($logArray);
      return response()->json(['responseText' => 'Data successfully inserted!'], 200);
    }
  }
//deit item
  public function editItem(Request $req) {

   $rules = array(
    'name' => 'required',
    'divisionId' => 'required',
    'districtId' => 'required',
    'upzillaId' => 'required',
    'unionId' => 'required'
  );
   $attributeNames = array(
     'name'       => 'Village Name',
     'divisionId' => 'Division Name',
     'districtId' => 'District Name',
     'upzillaId'  => 'Upzilla Name',
     'unionId'    => 'Union Name'   
   );
   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $previousdata = GnrVillage::find ($req->id);
    $village = GnrVillage::find ($req->id);
    $village->name = $req->name;
    $village->divisionId = $req->divisionId;
    $village->districtId = $req->districtId;
    $village->upzillaId = $req->upzillaId;
    $village->unionId = $req->unionId;
    $village->save();

    $upzillaName  = DB::table('gnr_upzilla')->select('name')->where('id',$req->upzillaId)->first();
    $districtName = DB::table('gnr_district')->select('name')->where('id',$req->districtId)->first();
    $divisionName = DB::table('gnr_division')->select('name')->where('id',$req->divisionId)->first();
    $unionName    = DB::table('gnr_union')->select('name')->where('id',$req->unionId)->first();


    $data = array(
      'village'            => $village,
      'upzillaName'        => $upzillaName,
      'districtName'       => $districtName,
      'divisionName'       => $divisionName,
      'unionName'          => $unionName,
      'slno'               => $req->slno
    );
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrVillageController',
      'tableName'  => 'gnr_village',
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

  $isAssigned = (int) DB::table('gnr_working_area')
  ->where('villageId', $req->id)
  ->value('id');

  if ($isAssigned > 0) {
    $data = array(
      'responseTitle' => 'Warning!',
      'responseText' => 'You can not delete this because it is assign to Village.'
    );

    return response()->json($data);
  }


  $previousdata=GnrVillage::find($req->id);
  GnrVillage::find($req->id)->delete();
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrVillageController',
    'tableName'  => 'gnr_village',
    'operation'  => 'delete',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
  );
  Service::createLog($logArray);
  return response()->json();
} 
}
