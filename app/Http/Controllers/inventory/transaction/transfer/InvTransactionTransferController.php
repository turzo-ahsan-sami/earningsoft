<?php

namespace App\Http\Controllers\inventory\transaction\transfer;

use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\inventory\InvTraTransfer;
use App\inventory\InvTraTransferDetails;
use App\Http\Requests;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class InvTransactionTransferController extends Controller
{
/*=================================================================================================
                            transfer view page
  =================================================================================================*/
    public function index(){
    	
        $transfers = InvTraTransfer::where('brancIdFrom', Auth::user()->branchId)->get();
    	return view('inventory/transaction/transfer/transfer',['transfers'=>$transfers]);
    	
    }

    public function addTransfer()
    {   
    	return view('inventory/transaction/transfer/addTransfer');
    }
/*=================================================================================================
                            for insert transfer and transferDetails table
  =================================================================================================*/
    public function addInvTransferItems(Request $req){

        $rules = array(
                'branchIdTo'      => 'required'
              );
      $attributeNames = array(
                'branchIdTo'      => 'Branch To.'
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
            $forCountAppnProId  = count($req->productId5);
            $forCountAppnProQty = count($req->productQntty5);
          
          if($forCountAppnProId<1){return response()->json('false'); return false;}
                //return response()->json($req);
            $now = Carbon::now();
            $transferMaxId = DB::table('inv_tra_transfer')->max('id')+1;
            $branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchIc)->value('branchCode');
            $valueForField = 'TRF.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $transferMaxId);
            $req->request->add(['transferBillNo' => $valueForField, 'transferDate' => $now]);    
            $create = InvTraTransfer::create($req->all());
            
            $transferDetails = new InvTraTransferDetails;
             
        $dataSet = [];
        for ($i=0; $i < $forCountAppnProId; $i++){
          $dataSet[]= array(
              'transferId'         => $create->id,
              'transferBillNo'     => $create->transferBillNo,
              'transferProductId'  => $req->productId5[$i],
              'transferQuantity'   => $req->productQntty5[$i],
              'price'              => $req->productPrice[$i],
              'totalPrice'         => $req->proTotalPrice[$i],
              'createdDate'        => $now
          );
        }
        DB::table('inv_tra_transfer_details')->insert($dataSet);
    }
        return response()->json('success');

    }
/*=================================================================================================
                                            Display data in edit modal
  =================================================================================================*/
     
    public function deitedInvTransferDataShow(Request $req){
        $editedDatas = InvTraTransfer::where('id', $req->id)->get();
       return response()->json($editedDatas);
    }
/*=================================================================================================
                                            for edit each append rows 
  =================================================================================================*/
    public function trnasferEditAppendRows(Request $req){
            $transferDetailsTables =  InvTraTransferDetails::where('transferId',$req->id)->get();
            $productId = DB::table('inv_product')->select('name','id')->get(); 
            
            $data = array(
            'transferDetailsTables'  => $transferDetailsTables,
            'productId'              => $productId
            );
            return response()->json($data);
            //return response()->json($useDetailsTables);
        }

/*=================================================================================================
                                            Edit trnasfer 
  =================================================================================================*/
    public function editInvTransfer(Request $req){
        
    $rules = array(
            'branchIdTo'      => 'required'
          );
    $attributeNames = array(
            'branchIdTo'      => 'Branch To.'
          );
      $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
            $idCount = count($req->productId5);
            if($idCount>0){

                $rowFectcs = InvTraTransferDetails::select('id')->where('transferId',$req->id)->get();
                foreach($rowFectcs as $rowFectc){
                 InvTraTransferDetails::find($rowFectc->id)->delete();
                }

                $productDetails = new InvTraTransferDetails;
                 
                $dataSet = [];
                for ($i=0; $i < $idCount; $i++){
                    
                  $dataSet[]= array(
                  'transferId'         => $req->id,
                  'transferBillNo'     => $req->transferBillNo,
                  'transferProductId'  => $req->productId5[$i],
                  'transferQuantity'   => $req->productQntty5[$i],
                  'price'              => $req->productPrice[$i],
                  'totalPrice'         => $req->proTotalPrice[$i],
                  'createdDate'        => DB::table('inv_tra_transfer')->where('id', $req->id)->value('transferDate')
                  );
                }
                DB::table('inv_tra_transfer_details')->insert($dataSet);

              $updateTransferTable = InvTraTransfer::find ($req->id);
              $updateTransferTable->orderNo               = $req->orderNo;
              $updateTransferTable->transferOrderNo       = $req->transferOrderNo;
              $updateTransferTable->brancIdFrom           = $req->brancIdFrom;
              $updateTransferTable->branchIdTo            = $req->branchIdTo;
              $updateTransferTable->totalTransferQuantity = $req->totalTransferQuantity;
              $updateTransferTable->totalTransferAmount   = $req->totalTransferAmount;
              $updateTransferTable->save();

              $updateDatas = InvTraTransfer::where('id', $req->id)->get();
              foreach($updateDatas as $updateData){
              $brnchFromName = DB::table('gnr_branch')->select('name')->where('id',$updateData->brancIdFrom)->first();
              $brnchToName   = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchIdTo)->first();
              $dateFromarte = $updateData->transferDate;
                }
              $data = array(
                    'updateDatas'           => $updateDatas,
                    'brnchFromName'         => $brnchFromName,
                    'brnchToName'           => $brnchToName,
                    'dateFromarte'          => $dateFromarte,
                    'slno'                  => $req->slno
                );
            return response()->json($data);
        }
    }
}

/*=================================================================================================
                            delete data from transfer and transfer details table
  =================================================================================================*/
    public function deleteTransfer(Request $req){

        DB::table('inv_tra_transfer')->where('id', $req->input("id"))->delete();
        DB::table('inv_tra_transfer_details')->where('transferId', $req->input("id"))->delete();

        return response()->json($req->input("id"));

    }

    
}
