<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrRouteOperation;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class GnrRouteOperationController extends Controller{

    public function index(Request $req){

        $moduleList = [''=>'--All--'] + DB::table('gnr_module')->orderBy('name')->pluck('name','id')->toArray();
        $subFunctionList = [''=>'--All--'] + DB::table('gnr_sub_function')->orderBy('subFunctionName')->pluck('subFunctionName','id')->toArray();

        $routeOperations = GnrRouteOperation::orderBy('moduleIdFK')->orderBy('functionIdFK')->orderBy('subFunctionIdFK');

        if ($req->filModule!='' || $req->filModule!=null) {
            $routeOperations = $routeOperations->where('moduleIdFK',$req->filModule);
            $functionList = [''=>'--All--'] + DB::table('gnr_function')->where('moduleIdFK',$req->filModule)->orderBy('name')->pluck('name','id')->toArray();
        }
        else{
            $functionList = [''=>'--All--'] + DB::table('gnr_function')->orderBy('name')->pluck('name','id')->toArray();
        }

        if ($req->filFunction!='' || $req->filFunction!=null) {
            $routeOperations = $routeOperations->where('functionIdFK',$req->filFunction);
        }
        if ($req->filSubFunction!='' || $req->filSubFunction!=null) {
            $routeOperations = $routeOperations->where('subFunctionIdFK',$req->filSubFunction);
        }

        $routeOperations = $routeOperations->paginate(50);
        
        $data = array(
            'moduleList'        => $moduleList,
            'functionList'      => $functionList,
            'subFunctionList'   => $subFunctionList,
            'routeOperations'   => $routeOperations
        );  
        return view('gnr.routeOperation.viewRouteOperation',$data);
    }

    public function addRouteOperation(){
        return view('gnr.routeOperation.addRouteOperation');
    }

    //add data
    public function addItem(Request $req){
        $rules = array(
            'routeName'     => 'required|unique:gnr_route_operation',
            'moduleId'      => 'required',
            'functionId'    => 'required',
            'subFunctionId' => 'required'
        );

        $attributeNames = array(
            'routeName'     => 'Route Name',
            'moduleId'      => 'Module',
            'functionId'    => 'Function',
            'subFunctionId' => 'Sub Function'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));

  		else{ 
            $routeOperation = new GnrRouteOperation;
            $routeOperation->routeName = $req->routeName;
            $routeOperation->moduleIdFK = $req->moduleId;
            $routeOperation->functionIdFK = $req->functionId;
            $routeOperation->subFunctionIdFK = $req->subFunctionId;
            $routeOperation->description = $req->description;
            $routeOperation->createdDate = Carbon::now();
            $routeOperation->save();
        	return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
        }
    }

    //edit 
    public function editItem(Request $req) {
        $rules = array(
            'routeName'     => 'required|unique:gnr_route_operation,routeName,'.$req->EMrouteOperationId,
            'moduleId'      => 'required',
            'functionId'    => 'required',
            'subFunctionId' => 'required'
        );

        $attributeNames = array(
            'routeName'     => 'Route Name',
            'moduleId'      => 'Module',
            'functionId'    => 'Function',
            'subFunctionId' => 'Sub Function'
        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{

            $routeOperation = GnrRouteOperation::find($req->EMrouteOperationId);
            $routeOperation->routeName = $req->routeName;
            $routeOperation->moduleIdFK = $req->moduleId;
            $routeOperation->functionIdFK = $req->functionId;
            $routeOperation->subFunctionIdFK = $req->subFunctionId;
            $routeOperation->description = $req->description;
            $routeOperation->save();
             
            return response()->json('success'); 
        }
    }

    //delete
    public function deleteItem(Request $req) {
        GnrRouteOperation::find($req->id)->delete();
        return response()->json();
    }

    public function getFunctionBaseOnModule(Request $req){

        $functions = DB::table('gnr_function');

        if ($req->moduleId!='') {
            $functions = $functions->where('moduleIdFK',$req->moduleId);
        }

        $functions = $functions->orderBy('name')->select('id','name')->get();

        return response::json($functions);

    }
}
