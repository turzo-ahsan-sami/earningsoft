<?php

namespace App\Http\Controllers\inventory\transaction\issue;

use App\Http\Controllers\Controller;
use App\Http\Controllers\gnr\Service;
use App\inventory\transaction\issue\InvTraIssue;
use App\inventory\transaction\issue\InvTraIssueDetails;
use App\GnrBranch;
use DB;
use Validator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Http\Requests;
use App\inventory\InvEmployeeRequisition;
use App\inventory\InvEmployeeRequisitionDetails;
use Response;

class InvTransactionIssueController extends Controller
{
  public function index(){
    $issues = InvTraIssue::orderBy('issueDate','desc')->orderBy('issueBillNo','desc')->get();
    return view('inventory/transaction/issue/issue',['issues' => $issues]); 
  }

  public function addIssue()
  {
    return view('inventory/transaction/issue/addIssue');
  }

  public function addItem(Request $req){

    $rules = array(
      'branchId' => 'required'
    );
    $attributeNames = array(
      'branchId' => 'Branch Name'
    );
    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
      $forCountAppnProId = count($req->productId5);
      $forCountAppnProQty = count($req->productQntty5);

      if($forCountAppnProId<1){return response()->json('false'); return false;}

            //$useBillNo  = substr($req->useBillNo, 2);
      $useMaxId = DB::table('inv_tra_issue')->max('id')+1;
      $branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
      $user = Auth::user()->emp_id_fk;
      $valueForField = 'IS.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $useMaxId);



      $now = Carbon::now();
      if ($req->date=="") {
        $issueDate = $now;
      }
      else{
        $issueDate = Carbon::parse($req->date);
      }


      $req->request->add(['issueBillNo' => $valueForField, 'issueDate' => $issueDate,'createdBy'=> $user]);
      $create = InvTraIssue::create($req->all());
            //return response()->json($create);
      $productDetails = new InvTraIssueDetails;

      $dataSet = [];
      for ($i=0; $i < $forCountAppnProId; $i++){
        $dataSet[]= array(
          'issueId'         => $create->id,
          'issueBillNo'     => $create->issueBillNo,
          'issueProductId'  => $req->productId5[$i],
          'issueQuantity'   => $req->productQntty5[$i],
          'price'           => $req->productPrice[$i],
          'totalPrice'      => $req->proTotalPrice[$i],
          'createdDate'     => $issueDate
        );
      }
      DB::table('inv_tra_issue_details')->insert($dataSet);
      $create = InvTraIssue::find ($req->id);
      $logArray = array(
        'moduleId'  => 1,
        'controllerName'  => 'InvTransactionIssueController',
        'tableName'  => 'inv_tra_issue',
        'operation'  => 'insert',
        'primaryIds'  => [DB::table('inv_tra_issue')->max('id')]
      );
      Service::createLog($logArray);
      return response()->json('Success'); 
    }

  }

  

    //for edit append rows
  public function issueEditAppendRows(Request $req){
    $useDetailsTables =  InvTraIssueDetails::where('issueId',$req->id)->get();

    $productId = DB::table('inv_product')->select('name','id')->get();

    $data = array(
      'useDetailsTables'  => $useDetailsTables,
      'productId'         => $productId

    );
    return response()->json($data);
            //return response()->json($useDetailsTables);
  }

  public function editInvIssue(Request $req){

    $rules = array(
      'branchId' => 'required'
    );
    $attributeNames = array(
      'branchId' => 'Branch Name'
    );
    $validator = Validator::make ( Input::all (), $rules);
    $validator->setAttributeNames($attributeNames);
    if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
    else{
      $idCount = count($req->productId5);
      if($idCount>0){

        $rowFectcs = InvTraIssueDetails::select('id')->where('issueId',$req->id)->get();
        foreach($rowFectcs as $rowFectc){
         InvTraIssueDetails::find($rowFectc->id)->delete();
       }

       $productDetails = new InvTraIssueDetails;

       $dataSet = [];
       for ($i=0; $i < $idCount; $i++){

        $dataSet[]= array(
          'issueId'         => $req->id,
          'issueBillNo'     => $req->issueBillNo,
          'issueProductId'  => $req->productId5[$i],
          'issueQuantity'   => $req->productQntty5[$i],
          'price'           => $req->productPrice[$i],
          'totalPrice'      => $req->proTotalPrice[$i],
          'createdDate'     => DB::table('inv_tra_issue')->where('id', $req->id)->value('issueDate')
        );
      }
      DB::table('inv_tra_issue_details')->insert($dataSet);
      $previousdata = InvTraIssue::find ($req->id);
      $updateIsssueTable = InvTraIssue::find ($req->id);
      $updateIsssueTable->branchId = $req->branchId;
      $updateIsssueTable->orderNo = $req->orderNo;
      $updateIsssueTable->issueOrderNo = $req->issueOrderNo;
      $updateIsssueTable->totalQuantity = $req->totalQuantity;
      $updateIsssueTable->projectId = $req->projectId;
      $updateIsssueTable->projectTypeId = $req->projectTypeId;
      $updateIsssueTable->totalAmount = $req->totalAmount;
      $updateIsssueTable->save();

      $updateDatas = InvTraIssue::where('id', $req->id)->get();
      foreach($updateDatas as $updateData){
        $brnchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();


        $dateFromarte = $updateData->issueDate;
      }
      $data = array(
        'updateDatas'       => $updateDatas,
        'brnchName'         => $brnchName,
        'dateFromarte'      => $dateFromarte,
        'slno'              => $req->slno
      );
      $logArray = array(
        'moduleId'  => 1,
        'controllerName'  => 'InvTransactionIssueController',
        'tableName'  => 'inv_tra_issue',
        'operation'  => 'update',
        'previousData'  => $previousdata,
        'primaryIds'  => [$previousdata->id]
      );
      Service::createLog($logArray);
      return response()->json($data);
    }
  }
}

    //delete
public function deleteIssue(Request $req) {
 $idCount       = $req->id;
 $count         = InvTraIssueDetails::where(['issueId' => $idCount])->count();
 $rowFectcs     = InvTraIssueDetails::select('id')->where('issueId',$idCount)->get();
 $previousdata=InvTraIssue::find($req->id);
 InvTraIssue::find($req->id)->delete();
 foreach($rowFectcs as $rowFectc){
   InvTraIssueDetails::find($rowFectc->id)->delete();
 }
 $logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvTransactionIssueController',
  'tableName'  => 'inv_tra_issue',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response()->json($count);
}

public function viewIssueData(Request $req){

  $issueDatas =  InvTraIssue::where('id',$req->id)->get();
  foreach ($issueDatas as $issueData) {
    $branchName = DB::table('gnr_branch')->where('id',$issueData->branchId)->value('name');
    $projectId = DB::table('gnr_project')->where('id',$issueData->projectId)->value('name');
    $issueDetailsTables =  InvTraIssueDetails::where('issueId',$issueData->id)->get();
    $projectTypeId = DB::table('gnr_project_type')->where('id',$issueData->projectTypeId)->value('name');

    $employeeName = DB::table('hr_emp_general_info')->select('emp_id','emp_name_english')->where('id',$issueData->createdBy)->first();

    $employeeDeg = DB::table('hr_emp_org_info')->where('emp_id_fk',$issueData->createdBy)->value('position_id_fk');

    $employeeDege = DB::table('hr_settings_position')->where('id',$employeeDeg)->value('name');
    $productName = array();

    $InvIssueDetails =  InvTraIssueDetails::where('issueId',$issueData->id)->get();
    foreach ($InvIssueDetails as $InvIssueDetail) {
     $temProductName = DB::table('inv_product')->where('id',$InvIssueDetail->issueProductId)->value('name');

     array_push($productName, $temProductName);
   }

 }



 $chalanDate = $issueData->issueDate;
 $issueDate = date('d-m-Y', strtotime($chalanDate));

 $data = array(
  'issueData'          => $issueData,
  'branchName'         => $branchName,
  'projectId'          => $projectId,
  'projectTypeId'      => $projectTypeId,
  'productName'        => $productName,
  'issueDetailsTables' => $issueDetailsTables,
  'issueDate'          => $issueDate,
  'employeeName'       => $employeeName,
  'employeeDege'       => $employeeDege,
  'InvIssueDetail'     => $InvIssueDetail,
  'slno'               => $req->slno
);
 return response()->json($data);

}



public function onChangeCategory(Request $request){
  if ($request->productGroupId=="" && $request->productCategoryId == "") {

    $productSubCategoryList =  DB::table('inv_product_sub_category')->pluck('id','name');
    $productBrandList =  DB::table('inv_product_brand')->pluck('id','name');
    $productList =  DB::table('inv_product')->pluck('id','name');
  }

  elseif ($request->productGroupId !="" && $request->productCategoryId == "") {
    $productSubCategoryList =  DB::table('inv_product_sub_category')->where('productGroupId',(int)json_decode($request->productGroupId))->pluck('id','name');
    $productBrandList =  DB::table('inv_product_brand')->where('productGroupId',(int)json_decode($request->productGroupId))->pluck('id','name');
    $productList =  DB::table('inv_product')->where('groupId',(int)json_decode($request->productGroupId))->pluck('id','name');
  }

  elseif($request->productGroupId =="" && $request->productCategoryId != ""){

    $productSubCategoryList =  DB::table('inv_product_sub_category')->where('productCategoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');
    $productBrandList =  DB::table('inv_product_brand')->where('productCategoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');
    $productList =  DB::table('inv_product')->where('categoryId',(int)json_decode($request->productCategoryId))->pluck('id','name');
  }

  else{

    $productSubCategoryList =  DB::table('inv_product_sub_category')->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');

    $productBrandList =  DB::table('inv_product_brand')->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');

    $productList =  DB::table('inv_product')->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)]])->pluck('id','name');

  }        

  $data = array(
    'productSubCategoryList' => $productSubCategoryList,
    'productBrandList' => $productBrandList,
    'productList'         => $productList
  );
  return response()->json($data);
}



public function onChangeSubCategory(Request $request){


  if ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubCategoryId == "") {
    $productBrandList =  DB::table('inv_product_brand')->pluck('id','name');
    $productList =  DB::table('inv_product')->pluck('id','name');
  }


  elseif ($request->productGroupId=="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

    $productBrandList =  DB::table('inv_product_brand')
    ->where('productSubCategoryId',(int)json_decode($request->productSubCategoryId))
    ->pluck('id','name');

    $productList =  DB::table('inv_product')
    ->where('subCategoryId',(int)json_decode($request->productSubCategoryId))
    ->pluck('id','name');
  }

  elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

    $productBrandList =  DB::table('inv_product_brand')
    ->where('productCategoryId',(int)json_decode($request->productCategoryId))
    ->pluck('id','name');

    $productList =  DB::table('inv_product')
    ->where('categoryId',(int)json_decode($request->productCategoryId))
    ->pluck('id','name');
  }

  elseif ($request->productGroupId=="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

    $productBrandList =  DB::table('inv_product_brand')
    ->where([['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');

    $productList =  DB::table('inv_product')
    ->where([['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');
  }

  elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId == "") {

    $productBrandList =  DB::table('inv_product_brand')
    ->where('productGroupId',(int)json_decode($request->productGroupId))
    ->pluck('id','name');

    $productList =  DB::table('inv_product')
    ->where('groupId',(int)json_decode($request->productGroupId))
    ->pluck('id','name');
  }

  elseif ($request->productGroupId!="" && $request->productCategoryId == "" && $request->productSubCategoryId != "") {

    $productBrandList =  DB::table('inv_product_brand')
    ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');
  }

  elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId == "") {

    $productBrandList =  DB::table('inv_product_brand')
    ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)]])
    ->pluck('id','name');

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)]])
    ->pluck('id','name');
  }

  elseif ($request->productGroupId!="" && $request->productCategoryId != "" && $request->productSubCategoryId != "") {

    $productBrandList =  DB::table('inv_product_brand')
    ->where([['productGroupId',(int)json_decode($request->productGroupId)],['productCategoryId',(int)json_decode($request->productCategoryId)],['productSubCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');
  }


  $data = array(
    'productBrandList' => $productBrandList,
    'productList'         => $productList
  );
  return response()->json($data);
}


function onChangeBrand(Request $request){

  if ($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId =="") {

    $productList =  DB::table('inv_product')->pluck('id','name');
  }

  elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where('brandId',(int)json_decode($request->productBrandId))
    ->pluck('id','name');

  }

  elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

    $productList =  DB::table('inv_product')
    ->where('subCategoryId',(int)json_decode($request->productSubCategoryId))
    ->pluck('id','name');
  }

  elseif($request->productGroupId =="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where([['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
    ->pluck('id','name');

  }
  elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

    $productList =  DB::table('inv_product')
    ->where('categoryId',(int)json_decode($request->productCategoryId))
    ->pluck('id','name');

  }

  elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where([['categoryId',(int)json_decode($request->productCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

    $productList =  DB::table('inv_product')
    ->where([['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId =="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where([['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

    $productList =  DB::table('inv_product')
    ->where('groupId',(int)json_decode($request->productGroupId))
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['brandId',(int)json_decode($request->productBrandId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId == "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId ==""){

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId == "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId ==""){

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)]])
    ->pluck('id','name');

  }

  elseif($request->productGroupId !="" && $request->productCategoryId != "" && $request->productSubCategoryId != "" && $request->productBrandId !=""){

    $productList =  DB::table('inv_product')
    ->where([['groupId',(int)json_decode($request->productGroupId)],['categoryId',(int)json_decode($request->productCategoryId)],['subCategoryId',(int)json_decode($request->productSubCategoryId)],['brandId',(int)json_decode($request->productBrandId)]])
    ->pluck('id','name');

  }

  $data = array(
    'productList'         => $productList
  );
  return response()->json($data);
}

}
