<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrUnion;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrUnionController extends Controller
{
    public function index(){
      $unions = GnrUnion::all();
      return view('gnr.tools.address.union.viewUnion',['unions' => $unions]);
    }

    public function addUnion(){
    return view('gnr.tools.address.union.addUnion');
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
      return response()->json($data); 
    }
   }

   //delete
    public function deleteItem(Request $req) {
      GnrUnion::find($req->id)->delete();
      return response()->json();
    } 
}
