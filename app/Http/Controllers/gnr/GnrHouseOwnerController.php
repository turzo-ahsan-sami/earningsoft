<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\GnrProjectType;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use App\gnr\GnrHouseOwner;
use App\gnr\GnrBranch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrHouseOwnerController extends Controller{


  /*---------------view List-----------------------------*/
  public function index() {

    $gnrHouseOwner = GnrHouseOwner::all();

    return view('gnr.houseowner.viewHouseOwner',['gnrHouseOwner'=> $gnrHouseOwner]);
}

/*--------------create Form--------------------*/

public function createHouseOwner() {

    return view('gnr.houseowner.createHouseOwnerFrom');

}

/*-----------------add data---------------------*/

public function addHouseOwner(Request $request) {

    $rules = array(

        'houseOwnerName'        => 'required',
        'projectName'           => 'required',
        'branchName'            => 'required',


    );

    $attributeNames = array(
        'houseOwnerName'           => 'HouseOwner Name',
        'projectName'              => 'required',
        'branchName'               =>'Branch Name',
    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails()) {

        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    }  

    else {

        $gnrHouseOwner = new GnrHouseOwner;
        $gnrHouseOwner->houseOwnerName = $request->houseOwnerName;
        $gnrHouseOwner->projectId = $request->projectName;
        $gnrHouseOwner->branchId = $request->branchName;
        $gnrHouseOwner->houseAddress = $request->branchAddress;
        $gnrHouseOwner->phoneNumber = $request->phoneNumber;
        $gnrHouseOwner->emailAddress = $request->emailAddress;
        $gnrHouseOwner->bankAccount = $request->bankAccount;
        $gnrHouseOwner->createdAt = Carbon::now();
        $gnrHouseOwner->save();

        $logArray = array(
            'moduleId'  => 7,
            'controllerName'  => 'GnrHouseOwnerController',
            'tableName'  => 'gnr_house_Owner',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('gnr_house_Owner')->max('id')]
        );
        Service::createLog($logArray);

        return response::json();
    }
}


/*----------------update Data---------------------------*/
public function houseOwnerInfoupdate(Request $request) {

    $rules = array(
        'houseOwnerName'           => 'required',
        'projectName'              => 'required',
        'branchName'               => 'required',
    );
    $attributeNames = array(

        'houseOwnerName'           =>'HouseOwner Name',
        'projectName'              => 'required',
        'branchName'               =>'Branch Name',

    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails()) {

        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    }

    else {
      $previousdata = GnrHouseOwner::find ($request->houseOwnerId);
      $gnrHouseOwner = GnrHouseOwner::find($request->houseOwnerId);
      $gnrHouseOwner->houseOwnerName = $request->houseOwnerName;
      $gnrHouseOwner->projectId = $request->projectName;
      $gnrHouseOwner->branchId = $request->branchName;
      $gnrHouseOwner->houseAddress = $request->houseAddress;
      $gnrHouseOwner->phoneNumber = $request->phoneNumber;
      $gnrHouseOwner->emailAddress = $request->emailAddress;
      $gnrHouseOwner->bankAccount = $request->bankAccount;
      $gnrHouseOwner->save(); 
  }

  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrHouseOwnerController',
    'tableName'  => 'gnr_house_Owner',
    'operation'  => 'update',
    'previousData'  => $previousdata,
    'primaryIds'  => [$previousdata->id]
);
  Service::createLog($logArray);

  return response::json('success');
}

/*--------------get Data--------------------------*/


public function getHouseOwnerInfo(Request $request) {

    $gnrHouseOwner = GnrHouseOwner::find($request->id);
    $projectName = DB::table('gnr_project')->where('id',$gnrHouseOwner->projectId)->value('name');
    $branchName = DB::table('gnr_branch')->where('id',$gnrHouseOwner->branchId)->value('name');

    $data = array(

        'gnrHouseOwner'        => $gnrHouseOwner,
        'projectName'          => $projectName,
        'branchName'           => $branchName,

    );

    return response::json($data);

}

/*------------ Delete Data--------------------*/

public function deletehouseOwner(Request $request) {
 $previousdata=GnrHouseOwner::find($request->id);

 $gnrHouseOwner=GnrHouseOwner::find($request->id);
 $gnrHouseOwner->delete();
 $logArray = array(
  'moduleId'  => 7,
  'controllerName'  => 'GnrHouseOwnerController',
  'tableName'  => 'gnr_house_Owner',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);

 return response::json('success');
}

/*------------------------------View data----------------------------*/

public function viewHouseOwnerInfo() {

    $gnrHouseOwner = GnrHouseOwner::find($request->id);
    $projectName = DB::table('gnr_project')->where('id',$gnrHouseOwner->projectId)->value('name');
    $branchName = DB::table('gnr_branch')->where('id',$gnrHouseOwner->branchId)->value('name');

    $data = array(

        'gnrHouseOwner'       => $gnrHouseOwner,
        'projectName'         => $projectName,
        'branchName'          => $branchName,
    );

    return response::json($data);
}
/*--------------------------------view Data----------------------------------*/

public function getHouseOwnerdata(Request $request) {

    $gnrHouseOwner = GnrHouseOwner::find($request->id);

    $projectName = DB::table('gnr_project')->where('id',$gnrHouseOwner->projectId)->value('name');

    $branchName = DB::table('gnr_branch')->where('id',$gnrHouseOwner->branchId)->value('name');

    $data = array(
        'gnrHouseOwner'        => $gnrHouseOwner,
        'projectName'          => $projectName,
        'branchName'           => $branchName,

    );

    return response::json($data);
}
}
