<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

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

class InvPurchaseReturnController extends Controller
{
    public function index(){
    	$purchaseRets = InvPurchaseReturn::all();
    	return view('inventory/transaction/purchaseReturn/viewPurchaseReturn',['purchaseRets'=>$purchaseRets]);
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
                
            $now = Carbon::now();
            $req->request->add(['createdDate' => $now]);    
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
        return response()->json($req->pruchaseBillNo);
    }

    public function editPurchaseRetAppnItem(Request $req){
        $purRetDetailsTables =  InvPurchaseReturnDetails::where('purchaseReturnId',$req->id)->get();
        $productId = DB::table('inv_product')->select('name','id')->get(); 
        
        $data = array(
        'purRetDetailsTables'   => $purRetDetailsTables,
        'productId'             => $productId
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
                  'purchaseBillNo'        => $req->purchaseBillNo1,
                  'productId'             => $req->productId5[$i],
                  'quantity'              => $req->productQntty5[$i],
                  'price'                 => $req->productTotalPriceApnTable[$i],
                  'totalPrice'            => $req->productPriceApnTable[$i],
                  'createdDate'           => $req->createdDate
                  );

            }
            DB::table('inv_purchase_return_details')->insert($dataSet);

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
        return response()->json($data);
        }
        }
    }
//end edit function of purchase

}
