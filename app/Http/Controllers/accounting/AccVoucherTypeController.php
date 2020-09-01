<?php

namespace App\Http\Controllers\accounting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
// use App\accounting\AddAccountType;
use App\accounting\AddVoucherType;
use Validator;
use Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use DB;

class AccVoucherTypeController extends Controller
{
    public function index(){
        $voucherTypes = AddVoucherType::all()->sortByDesc('id');
        return view('accounting.voucherType.viewVoucherType',['voucherTypes' => $voucherTypes]);
    }

    public function addVoucherType(){

        // $accountTypes = AddAccountType::all()->where('parentId',0);
        // $accountTypes = DB::table('acc_account_type')
        // ->where('parentId',0)
        // ->get();

        return view('accounting.voucherType.addVoucherType');
    }

    public function addItem(Request $request) {
       $rules = array(
        'name' => 'required',
        'titleName' => 'required',
        'shortName' => 'required'
    );
       $attributeNames = array(
         'name'    => 'Voucher Name',
         'titleName'   => 'Title Name', 
         'shortName'   => 'Short Name', 
     );

       $validator = Validator::make ( Input::all (), $rules);
       $validator->setAttributeNames($attributeNames);

       if ($validator->fails())
           return response::json(array('errors' => $validator->getMessageBag()->toArray()));
       else{

        $now = Carbon::now();
        $request->request->add(['createdDate' => $now]);    
        $create = AddVoucherType::create($request->all());
        $logArray = array(
            'moduleId'  => 4,
            'controllerName'  => 'AccVoucherTypeController',
            'tableName'  => 'acc_voucher_type',
            'operation'  => 'insert',
            'primaryIds'  => [DB::table('acc_voucher_type')->max('id')]
        );
        Service::createLog($logArray);

            // $create = AddVoucherType::create($req->all());
        return response()->json(['responseText' => 'Data successfully inserted!'], 200); 
    }
}

public function editItem(Request $req) {
    $rules = array(
        'name' => 'required',
        'parentId' => 'required'
    );
    $attributeNames = array(
        'name'    => 'Account Name',
        'parentId'   => 'Parent',
        'description'   => 'Description',
        'isParent'   => 'Parent'
    );

    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
        $accountType = AddAccountType::find ($req->id);
        $accountType->name = $req->name;
        $accountType->parentId = $req->parentId;
        $accountType->description = $req->description;
        $accountType->isParent = $req->isParent;
        $accountType->save();

        $data = array(
            'accountType'     => $accountType,
            'slno'      => $req->slno
        );
        return response()->json($data);
    }
}


public function deleteItem(Request $req){
    AddAccountType::find($req->id)->delete();
//        $accountTypes = AddAccountType::all();
        return response()->json();     //json($accountTypes);
    }

}
