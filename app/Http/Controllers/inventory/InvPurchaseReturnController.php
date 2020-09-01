<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;
use App\Http\Controllers\gnr\Service;

use App\Http\Requests;
use App\inventory\InvPurchaseReturn;
use App\inventory\InvPurchaseReturnDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\InvStockReportController;

class InvPurchaseReturnController extends Controller
{
  public function index(){
    $user = Auth::user();
    Session::put('branchId', $user->branchId);
    $gnrBranchId = Session::get('branchId');
    $logedUserName = $user->name;
    
    if($gnrBranchId==1 || $logedUserName=='Head Office'){
      $purchaseRets = InvPurchaseReturn::all();
      return view('inventory/transaction/purchaseReturn/viewPurchaseReturn',['purchaseRets'=>$purchaseRets]);
    }else{
      $purchaseRets = InvPurchaseReturn::where('branchId',$gnrBranchId)->get();
      return view('inventory/transaction/purchaseReturn/viewPurchaseReturn',['purchaseRets'=>$purchaseRets]);
    }
  }

  public function addInvPurchaseReqReturnF(){
   return view('inventory/transaction/purchaseReturn/addPurchaseReturn');
 }

 public function dataSendToPurchaseReturnTable(Request $req){

  if( $req->billNo){
   $purchaseTables = DB::table('inv_purchase')->where('id', $req->billNo)->get();
   foreach($purchaseTables as $purchaseTable){
     $supplierName = DB::table('gnr_supplier')->select('supplierCompanyName','id')->where('id', $purchaseTable->supplierId)->get();
   }
   $productIds = DB::table('inv_purchase_details')->select('productId')->where('purchaseId', $req->billNo)->get();
   foreach($productIds as $productId){
    $productNames[] = DB::table('inv_product')->select('id','name')->where('id', $productId->productId)->first();
  }

  $data = array(
    'purchaseTables' => $purchaseTables,
    'supplierName'   => $supplierName,
    'productNames'   => $productNames
  );
  return response()->json($data);
}else{
  $data = array(
    'purchaseTables' => '',
    'supplierName'   => '',
    'productNames'   => ''
  );
  return response()->json($data);
}
}

public function totalamoutofproduct(Request $req){
 $totalAmount = DB::table('inv_purchase_details')->select('totalPrice', 'quantity', 'price')->where('billNo', $req->billNo)
 ->where('productId', $req->productId)
 ->first();
 return response()->json($totalAmount);
}

public function addItem(Request $req){
 $rules = array(
  'purchaseBillNo1'     => 'required'
);
 $attributeNames = array(
  'purchaseBillNo1'     => 'pruchase BillNo.'
);

 $validator = Validator::make ( Input::all (), $rules);
 $validator->setAttributeNames($attributeNames);
 if ($validator->fails())
  return response::json(array('errors' => $validator->getMessageBag()->toArray()));
else{
  $forCountAppnProId  = count($req->productId5);
  $forCountAppnProQty = count($req->productQntty5);
  if($forCountAppnProId<1){return response()->json('false'); return false;}
  $ifThisBillNoExistInRtnTbl = InvPurchaseReturn::where('purchaseBillNo', $req->purchaseBillNo)->count();

  if($ifThisBillNoExistInRtnTbl>0){
              //Purchase return details table update;
   for ($i=0; $i < $forCountAppnProId; $i++){
    $detailsId = InvPurchaseReturnDetails::where('purchaseBillNo', $req->purchaseBillNo)
    ->where('productId', $req->productId5[$i])
    ->value('id');
    if($detailsId){
      $updatePurRetTable = InvPurchaseReturnDetails::find ($detailsId);
      $updatePurRetTable->purchaseReturnBillNo      = $req->purchaseReturnBillNo;
      $updatePurRetTable->purchaseBillNo            = $req->purchaseBillNo;
      $updatePurRetTable->productId                 = $req->productId5[$i];
      $updatePurRetTable->quantity                  = $req->productQntty5[$i];
      $updatePurRetTable->price                     = $req->productTotalPriceApnTable[$i];
      $updatePurRetTable->totalPrice                = $req->productPriceApnTable[$i];
      $updatePurRetTable->save();

    }else{
      $now = Carbon::now();
      $purchaseReturnId = InvPurchaseReturn::where('purchaseBillNo', $req->purchaseBillNo)->value('id');
      $updatePurRetTable = new InvPurchaseReturnDetails;
      $updatePurRetTable->purchaseReturnId     = $purchaseReturnId;
      $updatePurRetTable->purchaseReturnBillNo = $req->purchaseReturnBillNo;
      $updatePurRetTable->purchaseBillNo       = $req->purchaseBillNo;
      $updatePurRetTable->productId            = $req->productId5[$i];
      $updatePurRetTable->quantity             = $req->productQntty5[$i];
      $updatePurRetTable->price                = $req->productTotalPriceApnTable[$i];
      $updatePurRetTable->totalPrice           = $req->productPriceApnTable[$i];
      $updatePurRetTable->createdDate          = $now;
      $updatePurRetTable->save();
    }
  }
            //Purchase table update;
  $id = InvPurchaseReturn::where('purchaseBillNo', $req->purchaseBillNo)->value('id');
                //Calcuation Total Quanitty
  $totalQuantity  = (int)InvPurchaseReturnDetails::where('purchaseBillNo', $req->purchaseBillNo)->sum('quantity');
                  //Calcuation Total Amount
  $totalAmount  = (int)InvPurchaseReturnDetails::where('purchaseBillNo', $req->purchaseBillNo)->sum('totalPrice');
                  //Discount percent
  $discountPercent = (int)InvPurchaseReturn::where('purchaseBillNo', $req->purchaseBillNo)->value('discountPercent');
                  //Calcuation Total discount
  $discount = (int)$totalAmount*$discountPercent/100;
                  //Calcuation After discount
  $amountAfterDiscount = (int)$totalAmount-$discount;
                  //Calculation gross total
                  //$grossTotal = (int)$amountAfterDiscount-$;
  $req->request->add(['totalQuantity' => $totalQuantity, 'totalAmount'=> $totalAmount, 'discount' => $discount, 'amountAfterDiscount' => $amountAfterDiscount, 'grossTotal' => $amountAfterDiscount]);
  $updateExist    = InvPurchaseReturn::find($id)->update($req->all());

}else{

  $purchseMaxId = DB::table('inv_purchase_return')->max('id')+1;
  $branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
  $valueForField = 'PRR.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $purchseMaxId);
  $now = Carbon::now();
  $req->request->add(['createdDate' => $now, 'purchaseReturnBillNo' => $valueForField]);    
  $create = InvPurchaseReturn::create($req->all());

  $purchaseReturnDetails = new InvPurchaseReturnDetails;

  $dataSet = [];
  for ($i=0; $i < $forCountAppnProId; $i++){
    $dataSet[]= array(
      'purchaseReturnId'      => $create->id,
      'purchaseReturnBillNo'  => $req->purchaseReturnBillNo,
      'purchaseBillNo'        => $req->purchaseBillNo,
      'productId'             => $req->productId5[$i],
      'quantity'              => $req->productQntty5[$i],
      'price'                 => $req->productTotalPriceApnTable[$i],
      'totalPrice'            => $req->productPriceApnTable[$i],
      'createdDate'           => $create->createdDate
    );
  }
  DB::table('inv_purchase_return_details')->insert($dataSet);
}
}
$logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvPurchaseReturnController',
  'tableName'  => 'inv_purchase_return',
  'operation'  => 'insert',
  'primaryIds'  => [DB::table('inv_purchase_return')->max('id')]
);
Service::createLog($logArray);
return response()->json('success');
}

public function editPurchaseRetAppnItem(Request $req){

  $purRetDetailsTables =  InvPurchaseReturnDetails::where('purchaseReturnId',$req->id)->get();
  foreach($purRetDetailsTables as $purRetDetailsTable){
    $billNo = $purRetDetailsTable->purchaseBillNo;
    $purchaseQuantity[] = DB::table('inv_purchase_details')->select('quantity')
    ->where('billNo', $purRetDetailsTable->purchaseBillNo)
    ->where('productId', $purRetDetailsTable->productId)
    ->first();
  }
  $forFetchProducts = DB::table('inv_purchase_details')->select('productId', 'quantity')->where('billNo', $billNo)->get(); 
  foreach ($forFetchProducts as $forFetchProduct) {
    $productId[] = DB::table('inv_product')->select('name','id')->where('id',$forFetchProduct->productId)->first();
  }

  $data = array(
    'purRetDetailsTables'   => $purRetDetailsTables,
    'productId'             => $productId,
    'purchaseQuantity'      => $purchaseQuantity
  );
  return response()->json($data);
        //return response()->json($useDetailsTables);
}

    // Edit function of purchase
public function editItem(Request $req){
  $rules = array(

    'purchaseBillNo1'     => 'required'

  );
  $attributeNames = array(
    'purchaseBillNo1'     => 'pruchase BillNo.'
  );
  $validator = Validator::make ( Input::all (), $rules);
  $validator->setAttributeNames($attributeNames);
  if ($validator->fails())
    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
  else{
    $idCount = count($req->productId5);
    if($idCount>0){

      $rowFectcs = InvPurchaseReturnDetails::select('id')->where('purchaseReturnId',$req->id)->get();
      foreach($rowFectcs as $rowFectc){
       InvPurchaseReturnDetails::find($rowFectc->id)->delete();
     }

     $productDetails = new InvPurchaseReturnDetails;

     $dataSet = [];
     for ($i=0; $i < $idCount; $i++){
      $dataSet[]= array(
        'purchaseReturnId'      => $req->id,
        'purchaseReturnBillNo'  => $req->purchaseReturnBillNo,
        'purchaseBillNo'        => $req->purchaseBillNo2,
        'productId'             => $req->productId5[$i],
        'quantity'              => $req->productQntty5[$i],
        'price'                 => $req->productTotalPriceApnTable[$i],
        'totalPrice'            => $req->productPriceApnTable[$i],
        'createdDate'           => $req->createdDate
      );

    }
    DB::table('inv_purchase_return_details')->insert($dataSet);
    $previousdata = InvPurchaseReturn::find ($req->id);
    $updatePurRetTable = InvPurchaseReturn::find ($req->id);
    $updatePurRetTable->purchaseReturnBillNo = $req->purchaseReturnBillNo;
    $updatePurRetTable->purchaseBillNo = $req->purchaseBillNo2;
    $updatePurRetTable->supplierId = $req->supplierId;
    $updatePurRetTable->remark = $req->remark;
    $updatePurRetTable->purchaseDate = $req->purchaseDate;
    $updatePurRetTable->purchaseReturnDate = $req->purchaseReturnDate;
    $updatePurRetTable->totalAmount = $req->totalAmount;
    $updatePurRetTable->totalQuantity = $req->totalQuantity;
    $updatePurRetTable->totalAmount = $req->totalAmount;
    $updatePurRetTable->discountPercent = $req->discountPercent;
    $updatePurRetTable->discount = $req->discount;
    $updatePurRetTable->amountAfterDiscount = $req->amountAfterDiscount;
    $updatePurRetTable->vatPercent = $req->vatPercent;
    $updatePurRetTable->vat = $req->vat;
    $updatePurRetTable->grossTotal = $req->grossTotal;
    $updatePurRetTable->save();

    $updateDatas = InvPurchaseReturn::where('id', $req->id)->get();
    foreach($updateDatas as $updateData){
      /*$branchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();*/
      $supplierName = DB::table('gnr_supplier')->select('supplierCompanyName')->where('id',$updateData->supplierId)->first();
      $dateFromarte = date('d-m-Y', strtotime($updateData->purchaseReturnDate));
    }
    $data = array(
      'updateDatas'       => $updateDatas,
      'supplierName'      => $supplierName,
      'dateFromarte'      => $dateFromarte,
      'slno'              => $req->slno
    );
    $logArray = array(
      'moduleId'  => 1,
      'controllerName'  => 'InvPurchaseReturnController',
      'tableName'  => 'inv_purchase_return',
      'operation'  => 'update',
      'previousData'  => $previousdata,
      'primaryIds'  => [$previousdata->id]
    );
    Service::createLog($logArray);
    return response()->json($data);
  }
}
}
//end edit function of purchase

    //delete purchase Itme
public function deleteItem(Request $req) {
 $idCount = $req->id;
 $count = InvPurchaseReturnDetails::where(['purchaseReturnId' => $idCount])->count();
 $rowFectcs = InvPurchaseReturnDetails::select('id')->where('purchaseReturnId',$idCount)->get();
 $previousdata = InvPurchaseReturn::find ($req->id);
 InvPurchaseReturn::find($req->id)->delete();
 foreach($rowFectcs as $rowFectc){
   InvPurchaseReturnDetails::find($rowFectc->id)->delete();
 }
 $logArray = array(
  'moduleId'  => 1,
  'controllerName'  => 'InvPurchaseReturnController',
  'tableName'  => 'inv_purchase_return',
  'operation'  => 'delete',
  'previousData'  => $previousdata,
  'primaryIds'  => [$previousdata->id]
);
 Service::createLog($logArray);
 return response()->json($count);
}
//end purchase delete

    // select product accordint to bill no
public function selectBillNoProduct(Request $req){

  $productIds = DB::table('inv_purchase_details')->select('productId')->where('purchaseId', $req->billNo)->get();
  foreach($productIds as $productId){
    $productNames[] = DB::table('inv_product')->select('id','name')->where('id', $productId->productId)->first();
  }
  return response()->json($productNames);
}


}
