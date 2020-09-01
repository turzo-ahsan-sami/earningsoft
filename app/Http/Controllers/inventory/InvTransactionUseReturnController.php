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
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class InvTransactionUseReturnController extends Controller
{
    
	 public function index(){
	  	$branchId = Auth::user()->branchId;
	 		// if($branchId==1):
    //   			$useReturs = InvTrnsUseReturn::all();
    //   			return view('inventory/transaction/trnsuseReturn/viewUseReturn',['useReturs' => $useReturs]);
    //   		else:
      			$useReturs = InvTrnsUseReturn::where('branchId', $branchId)->get();
      			return view('inventory/transaction/trnsuseReturn/viewUseReturn',['useReturs' => $useReturs]);
      		//endif;
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
    	$productQty = InvTrnsUseDetails::select('productQuantity', 'costPrice')
    										->where('productId',$req->productId)
    										->where('useBillNo',$req->useBillNo)
    										->first();
    	return response()->json($productQty);
    }
	
	public function addItem(Request $req) {
	  $rules = array(
                //'employeeId' => 'required'
	  			'useBillNo' => 'required'
              );
 	  $attributeNames = array(
              //'employeeId' => 'Employee Name'
 	  			'useBillNo' => 'Use BillNo'
          );

	  $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
		    $forCountAppnProId  = count($req->productId5);
		    $forCountAppnProQty = count($req->productQntty5);
		  
		  if($forCountAppnProId<1){return response()->json('false'); return false;}

		  $ifThisBillNoExistInRtnTbl = InvTrnsUseReturn::where('useId', $req->useBillNo)->count();
 
		  	if($ifThisBillNoExistInRtnTbl>0){
		  		for ($i=0; $i < $forCountAppnProId; $i++){
		  			$useBillNoFUseT = DB::table('inv_tra_use')->where('id',$req->useBillNo)->value('useBillNo');
                    $detailsId = InvTrnsUseReturnDetails::where('useBillNo', $useBillNoFUseT)
                                  						->where('productId', $req->productId5[$i])
                                  						->value('id');
                    if($detailsId){
                    	//$perProPrice = DB::table('inv_product')->where('id',$req->productId5[$i])->value('costPrice');;
	  					//$totalPrice = $perProPrice*$req->productQntty5[$i];
		                $updatePurRetTable = InvTrnsUseReturnDetails::find ($detailsId);
		                $updatePurRetTable->productId        		= $req->productId5[$i];
		                $updatePurRetTable->productQuantity         = $req->productQntty5[$i];
		                $updatePurRetTable->price          			= $req->productPrice[$i];
		                $updatePurRetTable->totalPrice       		= $req->proTotalPrice[$i];
		                $updatePurRetTable->save();

              		}else{
              			$now = Carbon::now(); 
                		$useReturnId 		= InvTrnsUseReturn::where('useId', $req->useBillNo)->value('id'); 
                		$useReturnBillNo 	= InvTrnsUseReturn::where('useId', $req->useBillNo)->value('useReturnBillNo'); 
                		$useBillNo  		= InvTrnsUseReturn::where('useId', $req->useBillNo)->value('useBillNo');
                		//$perProPrice 		= DB::table('inv_product')->where('id',$req->productId5[$i])->value('costPrice');;
	  					//$totalPrice  		= $perProPrice*$req->productQntty5[$i];
	  					
              			$updatePurRetTable  = new InvTrnsUseReturnDetails;
              			$updatePurRetTable->useReturnId        		= $useReturnId ;
              			$updatePurRetTable->useReturnBillNo      	= $useReturnBillNo;
              			$updatePurRetTable->useBillNo        		= $useBillNo;
		                $updatePurRetTable->productId        		= $req->productId5[$i];
		                $updatePurRetTable->productQuantity         = $req->productQntty5[$i];
		                $updatePurRetTable->price          			= $req->productPrice[$i];
		                $updatePurRetTable->totalPrice       		= $req->proTotalPrice[$i];
		                $updatePurRetTable->createdDate 			= $now;
		                $updatePurRetTable->save();

		                
              			}
                	}

                  //Purchase table update;
              	  $id = InvTrnsUseReturn::where('useId', $req->useBillNo)->value('id');
              	  $useBillNoFUseT = DB::table('inv_tra_use')->where('id',$req->useBillNo)->value('useBillNo');
                  //Calcuation Total Quanitty
                  $totalQuantity  = (int)InvTrnsUseReturnDetails::where('useBillNo', $useBillNoFUseT)->sum('productQuantity');
                  //Calcuation Total Amount
                  $totalAmount  = (int)InvTrnsUseReturnDetails::where('useBillNo', $useBillNoFUseT)->sum('totalPrice');
                  //Update use return table
                  $req->request->add(['totalQuantity' => $totalQuantity, 'totalAmount'=> $totalAmount, 'useBillNo' => $useBillNoFUseT]);
                  $updateExist    = InvTrnsUseReturn::find($id)->update($req->all());
		  	}else{

		
	  		//Use return bill No 
	  		$useMaxId = DB::table('inv_tra_use_return')->max('id')+1;
	  		$branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
			$valueForField = 'USR.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $useMaxId);

		 	$useBillNoFUseT = DB::table('inv_tra_use')->where('id',$req->useBillNo)->value('useBillNo');
		 	$now = Carbon::now();
		 	$req->request->add([/*'totalAmount' => $totalAmount, */'useReturnBillNo' => $valueForField, 'useId'=> $req->useBillNo, 'useBillNo' => $useBillNoFUseT, 'createdDate' => $now]);
            $create = InvTrnsUseReturn::create($req->all());
		   $productDetails = new InvTrnsUseReturnDetails;
			 
	  	$dataSet = [];
	  	for ($i=0; $i < $forCountAppnProId; $i++){
	  		//$perProPrice = DB::table('inv_product')->where('id',$req->productId5[$i])->value('costPrice');;
	  		//$totalPrice = $perProPrice*$req->productQntty5[$i];

		  $dataSet[]= array(
			  'useReturnId'		=> $create->id,
			  'useReturnBillNo'	=> $create->useReturnBillNo,
			  'useBillNo'		=> $useBillNoFUseT,
        	  'productId'       => $req->productId5[$i],
			  'productQuantity' => $req->productQntty5[$i],
			  'price' 			=> $req->productPrice[$i],
			  'totalPrice' 	    => $req->proTotalPrice[$i],
			  'createdDate'     => $now
		  );
		}
		DB::table('inv_tra_use_return_details')->insert($dataSet);
		$create = InvTrnsUseReturn::find ($req->id);
	}
		
	    return response()->json('data successfully inserted'); 
  	}
  }
	

	//for edit append rows
	public function editAppnRows(Request $req){
		$useRtrnDetailsTables =  InvTrnsUseReturnDetails::where('useReturnId',$req->id)->get();
			foreach($useRtrnDetailsTables as $useRtrnDetailsTable){
				$purchaseQuantity[] = DB::table('inv_tra_use_details')->select('productQuantity')
																		->where('useBillNo', $useRtrnDetailsTable->useBillNo)
																		->where('productId', $useRtrnDetailsTable->productId)
																		->first();
			}

		$proIdFrmUseDetlsTbs   =  InvTrnsUseDetails::select('productId')->where('useId',$req->useId)->get();
		foreach($proIdFrmUseDetlsTbs as $proIdFrmUseDetlsTb){
            $productId[] = DB::table('inv_product')->select('name', 'id')->where('id',$proIdFrmUseDetlsTb->productId)->first();
          }
		//$productId = DB::table('inv_product')->select('name','id')->get(); 
		
		$data = array(
		'useRtrnDetailsTables'	=> $useRtrnDetailsTables,
		'productId'	    		=> $productId,
		'purchaseQuantity'	    => $purchaseQuantity
		);
		return response()->json($data);
		//return response()->json($useDetailsTables);
	}

	public function deitedDataUseReturnShow(Request $req){
		$useRetTblDatas = InvTrnsUseReturn::where('id', $req->id)->get();
		return response()->json($useRetTblDatas);
	}


	//edit function
	public function editItem(Request $req){
		$rules = array(
                //'employeeId' => 'required'
	  			'useBillNo' => 'required'
              );
 	  	$attributeNames = array(
              //'employeeId' => 'Employee Name'
 	  			'useBillNo' => 'Use BillNo'
          );

	  $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
		$idCount = count($req->productId5);
		if($idCount>0){

	  		$rowFectcs = InvTrnsUseReturnDetails::select('id')->where('useReturnId',$req->id)->get();
	  		foreach($rowFectcs as $rowFectc){
			 InvTrnsUseReturnDetails::find($rowFectc->id)->delete();
		 	}

			$productDetails = new InvTrnsUseReturnDetails;
			 
			 $useBillNoFUseT = DB::table('inv_tra_use')->where('id',$req->useBillNo)->value('useBillNo');
		  	$dataSet = [];
	  	for ($i=0; $i < $idCount; $i++){
		  		//$perProPrice = DB::table('inv_product')->where('id',$req->productId5[$i])->value('costPrice');;
		  		//$totalPrice = $perProPrice*$req->productQntty5[$i];

			  $dataSet[]= array(
				  'useReturnId'		=> $req->id,
				  'useReturnBillNo'	=> $req->useReturnBillNo,
				  'useBillNo'		=> $useBillNoFUseT,
	        	  'productId'       => $req->productId5[$i],
				  'productQuantity' => $req->productQntty5[$i],
				  'price' 			=> $req->productPrice[$i],
			  	  'totalPrice' 	    => $req->proTotalPrice[$i],
				  'createdDate'		=> $req->createdDate
			  );
			}
			DB::table('inv_tra_use_return_details')->insert($dataSet);

		  //$totalQty = InvTrnsUseReturnDetails::where('useReturnId', $req->id)->sum('productQuantity');
		  //$totalAmount = InvTrnsUseReturnDetails::where('useReturnId', $req->id)->sum('totalPrice');
		  $updateUseReTable = InvTrnsUseReturn::find ($req->id);
	      $updateUseReTable->useReturnBillNo = $req->useReturnBillNo;
	      $updateUseReTable->useId 			 = $req->useBillNo;
	      $updateUseReTable->useBillNo 		 = $useBillNoFUseT;
	      $updateUseReTable->branchId 		 = $req->branchId;
	      $updateUseReTable->employeeId 	 = $req->employeeId;
	      $updateUseReTable->roomId 		 = $req->roomId;
	      $updateUseReTable->totalQuantity 	 = $req->totalQuantity;
	      $updateUseReTable->totalAmount 	 = $req->totalAmount;
	      $updateUseReTable->save();

	      $updateDatas = InvTrnsUseReturn::where('id', $req->id)->get();
	      foreach($updateDatas as $updateData){
	      $brnchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
	      $employeeName = DB::table('gnr_employee')->where('id',$updateData->employeeId)->value('name');
	      $roomName = DB::table('gnr_room')->where('id',$updateData->roomId)->value('name');
	      $dateFromarte = date('d-m-Y', strtotime($updateData->createdDate));
	  		}
	      $data = array(
	      		'updateDatas'		=> $updateDatas,
	      		'brnchName'			=> $brnchName,
	      		'employeeName'		=> $employeeName,
	      		'roomName'			=> $roomName,
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
		 $count = InvTrnsUseReturnDetails::where(['useReturnId' => $idCount])->count();
		 $rowFectcs = InvTrnsUseReturnDetails::select('id')->where('useReturnId',$idCount)->get();
		 
		 InvTrnsUseReturn::find($req->id)->delete();
		 foreach($rowFectcs as $rowFectc){
			 InvTrnsUseReturnDetails::find($rowFectc->id)->delete();
		 }
      return response()->json($count);
    }

    
    public function useItemPerUseBillNo(Request $req){
    	$productAccPerBillNos = InvTrnsUseDetails::select('productId')->where('useId', $req->useId)->get();
    		foreach($productAccPerBillNos as $productAccPerBillNo){
    			$productName[] = DB::table('inv_product')->select('name','id')->where('id',$productAccPerBillNo->productId)->first();
    		}
    	return response()->json($productName);
    }
}

