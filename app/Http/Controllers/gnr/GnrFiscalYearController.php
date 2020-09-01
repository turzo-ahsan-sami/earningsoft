<?php

namespace App\Http\Controllers\gnr;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\gnr\FiscalYear;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class GnrFiscalYearController extends Controller
{
  public function index(){
      // dd(Carbon::parse('2018-07-01')->addYear()->subDay());
    $fiscalYears = FiscalYear::all();
    foreach ($fiscalYears as $key => $fiscalYear) {
        $fyStartDate = $fiscalYear->fyStartDate;
        $fyEndDate = $fiscalYear->fyEndDate;

        $checkTransaction = DB::table('acc_voucher')->where('voucherDate', '>=', $fyStartDate)->where('voucherDate', '<=', $fyEndDate)->first();
        $checkTransactionForYear[$fiscalYear->id] = $checkTransaction == null ? 0 : 1;
    }
    // dd($checkTransactionForYear);
    return view('gnr/tools/companySetting/fyscalyear/viewFiscalYear',['fiscalYears' => $fiscalYears, 'checkTransactionForYear' => $checkTransactionForYear]);
  }

  public function addFiscalYear(){
    return view('gnr/tools/companySetting/fyscalyear/addFiscalYear');
  }

  public function addItem(Request $req)
  {
   $rules = array(
    'name'        => 'required',
    'companyId'   => 'required',
    'fyStartDate' => 'required'
  );

   $attributeNames = array(
     'name'       => 'FiscalYear Name',
     'companyId'  => 'Company Name',
     'fyStartDate'=> 'FiscalYear Star tDate'
   );

   $validator = Validator::make ( Input::all (), $rules);
   $validator->setAttributeNames($attributeNames);
   if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $now = Carbon::now();
    $fyStartDate  = $req->fyStartDate;
    $fyStrDt      = date('Y-m-d', strtotime($fyStartDate));
    $fyEndDate    = Carbon::parse($fyStrDt)->addYear()->subDay()->format('Y-m-d');
    $req->request->add(['fyStartDate' => $fyStrDt, 'fyEndDate' => $fyEndDate, 'createdDate' => $now]);
    $create = FiscalYear::create($req->all());
    $logArray = array(
      'moduleId'  => 7,
      'controllerName'  => 'GnrFiscalYearController',
      'tableName'  => 'gnr_fiscal_year',
      'operation'  => 'insert',
      'primaryIds'  => [DB::table('gnr_fiscal_year')->max('id')]
    );
    Service::createLog($logArray);
    return response()->json(['responseText' => 'Data successfully inserted!'], 200);
  }
}

//edit function
public function editItem(Request $req) {
 $rules = array(
  'name'        => 'required',
  'companyId'   => 'required',
  'fyStartDate' => 'required'
);

 $attributeNames = array(
   'name'       => 'FiscalYear Name',
   'companyId'  => 'Company Name',
   'fyStartDate'=> 'FiscalYear Star tDate'
 );
 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $previousdata = FiscalYear::find ($req->id);
  $fyStartDate  = $req->fyStartDate;
  $fyStrDt      = date('Y-m-d', strtotime($fyStartDate));
  $fyEndDate    = date("Y-m-d", strtotime(date("Y-m-d", strtotime($fyStartDate)) . " + 364 day"));
  $fiscalyear = FiscalYear::find ($req->id);
  $fiscalyear->name = $req->name;
  $fiscalyear->companyId = $req->companyId;
  $fiscalyear->fyStartDate = $fyStrDt;
  $fiscalyear->fyEndDate = $fyEndDate;
  $fiscalyear->save();

  $companyName = DB::table('gnr_company')->select('name')->where('id',$req->companyId)->first();
  $data = array(
    'fiscalyear'   => $fiscalyear,
    'companyName'  => $companyName,
    'slno'         => $req->slno
  );
  $logArray = array(
    'moduleId'  => 7,
    'controllerName'  => 'GnrFiscalYearController',
    'tableName'  => 'gnr_fiscal_year',
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
 $previousdata=FiscalYear::find($req->id);
 FiscalYear::find($req->id)->delete();
 $logArray = array(
  'moduleId'  => 7,
  'controllerName'  => 'GnrFiscalYearController',
  'tableName'  => 'gnr_fiscal_year',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response()->json();
}

}
