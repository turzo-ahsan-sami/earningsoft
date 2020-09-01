<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrSubFunction;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrSubFunctionController extends Controller
{
    public function index(){
      $subfunctionalities = GnrSubFunction::all();
      return view('gnr.subFunction.viewSubFunction',['subfunctionalities' => $subfunctionalities]);
    }
    public function addSubFunctionality(){
    return view('gnr.subFunction.addSubFunction');
    }

    //add data
    public function addItem(Request $req) 
  {
         $rules = array(
                'subFunctionName' => 'required',
                'description'     => 'required'
              );

      $attributeNames = array(
           'subFunctionName' => 'Sub Function Name'
        );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  		else{ 
              $create = GnrSubFunctionality::create($req->all());
        		return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        	}
    }

    //edit 
    public function editItem(Request $req) {
       $rules = array(
                'subFunctionName' => 'required',
                'description'     => 'required'
              );
      
      $attributeNames = array(
           'subFunctionName' => 'Sub Functionality Name'
        );

      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{

      $updateSubfunction = GnrSubFunction::find ($req->id);
      $updateSubfunction->subFunctionName = $req->subFunctionName;
      $updateSubfunction->description = $req->description;
      $updateSubfunction->save();
              
      $data = array(
        'updateSubfunction'          => $updateSubfunction,
        'slno'                       => $req->slno
      );        
      return response()->json($data); 
    }
   }
   //delete
    public function deleteItem(Request $req) {
      GnrSubFunction::find($req->id)->delete();
      return response()->json();
    }  
}
