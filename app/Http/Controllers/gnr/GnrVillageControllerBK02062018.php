<?php 

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrVillage;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrVillageController extends Controller
{
    //addVillage
    public function index(){
      $villages = GnrVillage::all();
      return view('gnr.tools.address.village.viewVillage',['villages' => $villages]);
    }

    public function addVillage(){
    return view('gnr.tools.address.village.addVillage');
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
      return response()->json($data);
    }
   }

   //delete
    public function deleteItem(Request $req) {
      GnrVillage::find($req->id)->delete();
      return response()->json();
    } 
}
