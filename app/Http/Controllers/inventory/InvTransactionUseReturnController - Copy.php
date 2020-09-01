<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\inventory\InvTrnsUse;
use App\inventory\InvTrnsUseDetails;
use App\inventory\InvTrnsUseReturn;
use App\inventory\InvTrnsUseReturnDetails;
use Validator;
use Response;
use DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;


class InvTransactionUseReturnController extends Controller
{
    
	 public function index(){
      $productUses = InvTrnsUse::all();
      return view('inventory/transaction/trnsuse/viewUse',['productUses' => $productUses]);
    }
	
    public function addUseReturnF(){
		return view('inventory/transaction/trnsuseReturn/addUseReturn');
    }

    public function invUseBillOnCng(Request $req){
    	if($req->useId !==''){
    		$invUseTable = InvTrnsUse::where('id',$req->useId)
    					->where('useBillNo',$req->useBillNo)
    					->get();
    		$productNames = InvTrnsUseDetails::select('productId')
    											->where('useId',$req->useId)
	    										->where('useBillNo',$req->useBillNo)
	    										->get();
	    	foreach($productNames as $productName){
	    		$products[] = DB::table('inv_product')->select('name','id')
	    												->where('id',$productName->productId)
	    												->first();
	    	}									
	    																				
    		$data = array(
    			'invUseTable'	=> $invUseTable,
    			'products'	    => $products
    			);			
			return response()->json($data);
		}else{
			$data = array(
    			'invUseTable'	=> '',
    			'products'	    => ''
    			);		
			return response()->json($data);
		}	
    }

    public function getQtyFroUseDtlTbl(Request $req) {
    	$productQty = InvTrnsUseDetails::select('productQuantity', 'costPrice')->where('productId',$req->productId)->first();
    	return response()->json($productQty);
    }
	
	public function addItem(Request $req) {
	  $rules = array(
                'employeeId' => 'required'
              );
 	  $attributeNames = array(
              'employeeId' => 'Employee Name'
          );

	  $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
		    $forCountAppnProId = count($req->productId5);
		    $forCountAppnProQty = count($req->productQntty5);
		  
		  if($forCountAppnProId<1){return response()->json('false'); return false;}
		  	
		 
            $create = InvTrnsUseReturn::create($req->all());
    		
		    $productDetails = new InvTrnsUseReturnDetails;
			 
	  	$dataSet = [];
	  	for ($i=0; $i < $forCountAppnProId; $i++){

		  $dataSet[]= array(
			  'useId'			=> $create->id,
			  'useBillNo'		=> $req->useBillNo,
        	  'productId'       => $req->productId5[$i],
			  'productName'     => $req->productName[$i],
			  'productQuantity' => $req->productQntty5[$i],
			  'costPrice' 		=> $req->productPrice[$i],
			  'totalCostPrice' 	=> $req->proTotalPrice[$i]
		  );
		}
		DB::table('inv_tra_use_details')->insert($dataSet);
		$create = InvTrnsUse::find ($req->id);
	    return response()->json('$productPrice'); 
  	}
  }
	
	/*public function getProductPrice(Request $req)
    {
		$productPrice = DB::table('inv_product')->select('costPrice')->where('id',$req->productId)->first();
		return response()->json($productPrice); 
    }
	
	public function productCatagoryChange(Request $req){
		if($req->productGroupId){
        $productCategoryList = DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
		$productList = DB::table('inv_product')->where('groupId',$req->productGroupId)->pluck('name', 'id');
		
		$data = array(
		'productCategoryList' => $productCategoryList,
		'productList'         => $productList
		);
        return response()->json($data);
		}
		else{
			$productList = DB::table('inv_product')->pluck('name','id')->all();
			$data = array(
			'productList'   => $productList
			);
			return response()->json($data);
		}
    }
	
	public function productUseDetails(Request $req){
		$useDetails =  InvTrnsUse::where('id',$req->id)->get();
		foreach ($useDetails as $useDetail) {
		$brnchName = DB::table('gnr_branch')->select('name')->where('id',$useDetail->branchId)->first();
		$employeehName = DB::table('gnr_employee')->select('name')->where('id',$useDetail->employeeId)->first();
		$useDetailsTables =  InvTrnsUseDetails::where('useId',$useDetail->id)->get();
		$date = $useDetail->useDate;
		}
		$dateFromarte = date('d-m-Y', strtotime($date));

		$data = array(
		'useDetails'		=> $useDetails,
		'brnchName'	    	=> $brnchName,
		'employeehName'		=> $employeehName,
		'useDetailsTables'	=> $useDetailsTables,
		'dateUse'			=> $dateFromarte
		);
		return response()->json($data);
	}


	//for edit append rows
	public function editAppendRows(Request $req){
		$useDetailsTables =  InvTrnsUseDetails::where('useId',$req->id)->get();
		$productId = DB::table('inv_product')->select('name','id')->get(); 
		
		$data = array(
		'useDetailsTables'	=> $useDetailsTables,
		'productId'	    	=> $productId
		);
		return response()->json($data);
		//return response()->json($useDetailsTables);
	}

	public function useDetaisDeleteIdSend(Request $req){
		//InvTrnsUseDetails::find($req->id)->delete();
		$productQtyAndTotalAmount = InvTrnsUse::select('id', 'totlalUseQuentity', 'totalUseAmount')->where('id',$req->useTableId)->first();
		$productUseQnt = $productQtyAndTotalAmount->totlalUseQuentity;
		$productUseQnt = $productUseQnt-$req->reduceAmount;
		$productPrice = \ DB::table('inv_product')->select('costPrice')->where('id',$req->productIdforPrice)->first();
		$totalPrice = ($req->reduceAmount)*($productPrice->costPrice);
		$toralPriceToSave = ($productQtyAndTotalAmount->totalUseAmount)-($totalPrice);
		$delUpd = InvTrnsUse::find ($req->useTableId);
        $delUpd->totlalUseQuentity = $req->deleteQty;
        $delUpd->totalUseAmount = $req->deleteAmount;
        $delUpd->save();
		return response()->json($delUpd);
	}

	//edit function
	public function editUseItem(Request $req){
		$rules = array(
                'employeeId' => 'required'
               
              );
 	  $attributeNames = array(
              'employeeId' => 'Employee Name'
			  
          );
	  $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
		$idCount = count($req->productId5);
		if($idCount>0){
			//$projectType = InvTrnsUse::find ($req->id);
			//$useBillNo  = substr($req->useBillNo, 2);
	  		//$req->request->add(['useBillNo' => $useBillNo]);

	  		$rowFectcs = InvTrnsUseDetails::select('id')->where('useId',$req->id)->get();
	  		foreach($rowFectcs as $rowFectc){
			 InvTrnsUseDetails::find($rowFectc->id)->delete();
		 	}

			$productDetails = new InvTrnsUseDetails;
			 
		  	$dataSet = [];
		  	for ($i=0; $i < $idCount; $i++){
		  		$productPrice = DB::table('inv_product')->select('costPrice')->where('id',$req->productId5[$i])->first();
		  		$totalPrice = ($req->productQntty5[$i])*($productPrice->costPrice);
		  		$productName = DB::table('inv_product')->select('name')->where('id',$req->productId5[$i])->first();
			  $dataSet[]= array(
				  'useId'			=> $req->id,
				  'useBillNo'		=> $req->useBillNo,
	        	  'productId'       => $req->productId5[$i],
				  'productName'     => $productName->name,
				  'productQuantity' => $req->productQntty5[$i],
				  'costPrice' 		=> $req->productPrice[$i],
				  'totalCostPrice' 	=> $totalPrice
			  );
			}
			DB::table('inv_tra_use_details')->insert($dataSet);

		  $totalQty = InvTrnsUseDetails::where('useId', $req->id)->sum('productQuantity');
		  $totalAmount = InvTrnsUseDetails::where('useId', $req->id)->sum('totalCostPrice');
		  $updateUseTable = InvTrnsUse::find ($req->id);
	      $updateUseTable->useBillNo = $req->useBillNo;
	      $updateUseTable->requisitionNo = $req->requisitionNo;
	      $updateUseTable->requisition = $req->requisition;
	      $updateUseTable->branchId = $req->branchId;
	      $updateUseTable->employeeId = $req->employeeId;
	      $updateUseTable->totlalUseQuentity = $totalQty;
	      $updateUseTable->totalUseAmount = $totalAmount;
	      $updateUseTable->save();

	      $updateDatas = InvTrnsUse::where('id', $req->id)->get();
	      foreach($updateDatas as $updateData){
	      $brnchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
	      $employeehName = DB::table('gnr_employee')->select('name')->where('id',$updateData->employeeId)->first();
	      $dateFromarte = $updateData->useDate;
	  		}
	      $data = array(
	      		'updateDatas'		=> $updateDatas,
	      		'brnchName'			=> $brnchName,
	      		'employeehName'		=> $employeehName,
	      		'dateFromarte'		=> $dateFromarte,
	      		'slno'				=> $req->slno
	      	);
		return response()->json($data);
		}
		}
	}


	//delete
     public function deleteItem(Request $req) {
		 $idCount = $req->id;
		 $count = InvTrnsUseDetails::where(['useId' => $idCount])->count();
		 $rowFectcs = InvTrnsUseDetails::select('id')->where('useId',$idCount)->get();
		 
		 InvTrnsUse::find($req->id)->delete();
		 foreach($rowFectcs as $rowFectc){
			 InvTrnsUseDetails::find($rowFectc->id)->delete();
		 }
      return response()->json($count);
    }*/
}

