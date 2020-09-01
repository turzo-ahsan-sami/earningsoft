<?php

namespace App\Http\Controllers\gnr;

use App\gnr\GnrFunction;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Response;
use Validator;

class GnrFunctionController extends Controller
{
    public function index(Request $req)
    {
        $moduleList = ['' => '--All--'] + DB::table('gnr_module')->orderBy('name')->pluck('name', 'id')->toArray();
        $functions = GnrFunction::orderBy('moduleIdFK');

        if ($req->filModule != '' || $req->filModule != null) {
            $functions = $functions->where('moduleIdFK', $req->filModule);
        }

        $functions = $functions->paginate(50);

        $data = array(
            'moduleList' => $moduleList,
            'functions' => $functions,
        );
        return view('gnr.function.viewFunction', $data);
    }
    public function addFunction()
    {
        return view('gnr.function.addFunction');
    }

    //add data
    public function storeFunction(Request $req)
    {

        if( $req->functionCode ){
            if( $req->moduleId != substr($req->functionCode,0,2)){
                return response::json(array('errors' => ['functionCode' => 'Module prefix does not match.']));
            }
        }

        
        $rules = array(
            'name' => 'required',
            'moduleId' => 'required',
            'functionCode' => 'unique:gnr_function',
        );

        $attributeNames = array(
            'name' => 'Function Name',
            'moduleId' => 'Module',
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        $flag = 0;

        if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } elseif ($req->moduleId != '' || $req->moduleId != null) {
            $dataExits = (int) DB::table('gnr_function')->where('moduleIdFK', $req->moduleId)->where('name', $req->name)->value('id');
            if ($dataExits > 0) {
                $flag = 0;
                return response::json(array('errors' => ['name' => 'Name Should be Unique Against Module.']));
            } else {
                $flag = 1;
            }
        }
        if ($flag == 1) {

            $function = new GnrFunction;
            $function->name = $req->name;
            $function->moduleIdFK = $req->moduleId;
            $function->description = $req->description;
            $function->functionCode = $req->functionCode ?? '';
            $function->createdAt = Carbon::now();
            $function->save();

            return response()->json(['responseText' => 'Data successfully inserted!'], 200);
        }
    }

    public function updateFunction(Request $req)
    {
        if( $req->functionCode ){
            if( $req->moduleId != substr($req->functionCode,0,2)){
                return response::json(array('errors' => ['functionCode' => 'Module prefix does not match.']));
            }
        }

        $rules = array(
            'name' => 'required',
            'moduleId' => 'required',
            'functionCode' => 'unique:gnr_function,id,'.$req->EMfunctionId,
        );

        $attributeNames = array(
            'name' => 'Function Name',
            'moduleId' => 'Module',
        );

        $validator = Validator::make(Input::all(), $rules);
        $validator->setAttributeNames($attributeNames);
        $flag = 0;
        if ($validator->fails()) {
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        } elseif ($req->moduleId != '' || $req->moduleId != null) {
            $dataExits = (int) DB::table('gnr_function')->where('moduleIdFK', $req->moduleId)->where('name', $req->name)->where('id', '!=', $req->EMfunctionId)->value('id');
            if ($dataExits > 0) {
                $flag = 0;
                return response::json(array('errors' => ['name' => 'Name Should be Unique Against Module.']));
            } else {
                $flag = 1;
            }
        }
        if ($flag == 1) {

            $function = GnrFunction::find($req->EMfunctionId);
            $function->name = $req->name;
            $function->moduleIdFK = $req->moduleId;
            $function->description = $req->description;
            $function->functionCode = $req->functionCode ?? '';
            $function->save();

            return response()->json('success');
        }
    }
    //delete
    public function deleteFunction(Request $req)
    {
        GnrFunction::find($req->id)->delete();
        return response()->json();
    }
}
