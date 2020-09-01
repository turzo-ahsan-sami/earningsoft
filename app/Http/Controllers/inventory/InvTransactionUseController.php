<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\inventory\InvTrnsUse;
use App\inventory\InvTrnsUseDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


class InvTransactionUseController extends Controller
{
    
	 public function index(){
	  	$user = Auth::user();
		Session::put('branchId', $user->branchId);
		$gnrBranchId = Session::get('branchId');

	 		if($gnrBranchId==1){
      			$productUses = InvTrnsUse::orderBy('useDate','desc')->get();
      			return view('inventory/transaction/trnsuse/viewUse',['productUses' => $productUses]);
      		}else{
      			$productUses = InvTrnsUse::where('branchId',$gnrBranchId)->orderBy('useDate','desc')->get();
      			return view('inventory/transaction/trnsuse/viewUse',['productUses' => $productUses]);
      		}
    }
	
    public function addUse(){
		return view('inventory/transaction/trnsuse/addUse');
    }
	
	public function addProductUseItem(Request $req) {
	  $rules = array(
                //'employeeId' => 'required'
              );
 	  $attributeNames = array(
              //'employeeId' => 'Employee Name'
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
		  	$useMaxId = DB::table('inv_tra_use')->where('branchId',Auth::user()->branchId)->max('useNumber')+1;
		  	$branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
			$valueForField = 'US.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $useMaxId);

		  	$now = Carbon::now();
		  	if ($req->date=="") {
		  		$useDate = $now;
		  	}
		  	else{
		  		$useDate = Carbon::parse($req->date);
		  	}

		  	$departmentIds = "";
		  	if ($req->useType=='room') {
			  	foreach ($req->departmentId as $key => $departmentId) {
			  		if ($key>0) {
			  			$departmentIds = $departmentIds . ',' .$departmentId ;
			  		}
			  		else{
			  			$departmentIds = $departmentId ;
			  		}
			  		
			  	}
		  	}
		  	
		  	$req->merge(['departmentId' => $departmentIds]);
	  		$req->request->add(['useBillNo' => $valueForField, 'useDate' => $useDate,'useNumber'=>$useMaxId]);
            $create = InvTrnsUse::create($req->all());
    		//return response()->json($create);
		    $productDetails = new InvTrnsUseDetails;
			 
	  	$dataSet = [];
	  	for ($i=0; $i < $forCountAppnProId; $i++){
		  $dataSet[]= array(
			  'useId'			=> $create->id,
			  'useBillNo'		=> $req->useBillNo,
        	  'productId'       => $req->productId5[$i],
			  'productName'     => $req->productName[$i],
			  'productQuantity' => $req->productQntty5[$i],
			  'costPrice' 		=> $req->productPrice[$i],
			  'totalCostPrice' 	=> $req->proTotalPrice[$i],
			  'createdDate'		=> $useDate
		  );
		}
		DB::table('inv_tra_use_details')->insert($dataSet);
		$create = InvTrnsUse::find ($req->id);
	    return response()->json($req->departmentId); 
  	}
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
		$useDetail =  InvTrnsUse::where('id',$req->id)->first();		
		
		$brnchName = DB::table('gnr_branch')->select('name')->where('id',$useDetail->branchId)->first();
		$employeeName = DB::table('hr_emp_general_info')->where('id',$useDetail->employeeId)->value('emp_name_english');
		$roomName	   = DB::table('gnr_room')->where('id',$useDetail->roomId)->value('name');
		$useDetailsTables =  InvTrnsUseDetails::where('useId',$useDetail->id)->get();
		$date = $useDetail->useDate;
		
		$departmentIds = explode(',',$useDetail->departmentId);
		$departmentName = "";
		foreach ($departmentIds as $key => $departmentId) {
			if ($key>0) {
				$departmentName = $departmentName . ' / ' . DB::table('gnr_department')->where('id',$departmentId)->value('name');
			}
			else{
				$departmentName = DB::table('gnr_department')->where('id',$departmentId)->value('name');
			}
			
		}
			   
		//$dateFromarte = date('d-m-Y', strtotime($date));

		$data = array(
		'useDetails'		=> $useDetail,
		'brnchName'	    	=> $brnchName,
		'employeeName'		=> $employeeName,
		'roomName'			=> $roomName,
		'departmentName'	=> $departmentName,
		'useDetailsTables'	=> $useDetailsTables,
		'dateUse'			=> $date
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

	/*public function useDetaisDeleteIdSend(Request $req){
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
	}*/

	// Show data in use edit modal
	public function deitedDataUseShow(Request $req){
		$useTabelRowData = InvTrnsUse::where('id', $req->id)->get();
		return response()->json($useTabelRowData);
	}

	//edit function
	public function editUseItem(Request $req){
		$rules = array(
                //'employeeId' => 'required'
               
              );
 	  $attributeNames = array(
              //'employeeId' => 'Employee Name'
			  
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
		  		
			  $dataSet[]= array(
				  'useId'			=> $req->id,
				  'useBillNo'		=> $req->useBillNo,
	        	  'productId'       => $req->productId5[$i],
				  'productName'     => $req->productName[$i],
				  'productQuantity' => $req->productQntty5[$i],
				  'costPrice' 		=> $req->productPrice[$i],
				  'totalCostPrice' 	=> $req->proTotalPrice[$i],
				  'createdDate'		=> DB::table('inv_tra_use')->where('id', $req->id)->value('useDAte')
			  );
			}
			  
			DB::table('inv_tra_use_details')->insert($dataSet);

		  $departmentIds = "";
		  if ($req->useType=='room') {
		  	foreach ($req->departmentId as $key => $departmentId) {
		  		if ($key>0) {
		  			$departmentIds = $departmentIds . ',' .$departmentId ;
		  		}
		  		else{
		  			$departmentIds = $departmentId ;
		  		}
		  		
		  	}
		  }
		  	
		  	
		  

		  $totalQty = InvTrnsUseDetails::where('useId', $req->id)->sum('productQuantity');
		  $totalAmount = InvTrnsUseDetails::where('useId', $req->id)->sum('totalCostPrice');
		  $updateUseTable = InvTrnsUse::find ($req->id);
	      $updateUseTable->useBillNo = $req->useBillNo;
	      $updateUseTable->requisitionNo = $req->requisitionNo;
	      $updateUseTable->requisition = $req->requisition;
	      $updateUseTable->branchId = $req->branchId;
	      $updateUseTable->employeeId = $req->employeeId;
	      $updateUseTable->roomId = $req->roomId;
	      $updateUseTable->departmentId = $departmentIds;
	      $updateUseTable->totlalUseQuantity = $totalQty;
	      $updateUseTable->totalUseAmount = $totalAmount;
	      $updateUseTable->save();

	      $updateDatas = InvTrnsUse::where('id', $req->id)->get();
	      foreach($updateDatas as $updateData){
	      $brnchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
	      $employeehName = DB::table('hr_emp_general_info')->where('id',$updateData->employeeId)->value('emp_name_english');
	      $roomName = DB::table('gnr_room')->where('id',$updateData->roomId)->value('name');
	      $dateFromarte = $updateData->useDate;
	  		}
	      $data = array(
	      		'updateDatas'		=> $updateDatas,
	      		'brnchName'			=> $brnchName,
	      		'employeehName'		=> $employeehName,
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
		 $count = InvTrnsUseDetails::where(['useId' => $idCount])->count();
		 $rowFectcs = InvTrnsUseDetails::select('id')->where('useId',$idCount)->get();
		 
		 InvTrnsUse::find($req->id)->delete();
		 foreach($rowFectcs as $rowFectc){
			 InvTrnsUseDetails::find($rowFectc->id)->delete();
		 }
      return response()->json($count);
    }


    public function getdepOnChangeRoom(Request $req){

    	if ($req->roomId!='') {
    		$departmentString = DB::table('gnr_room')->where('id',$req->roomId)->value('departmentId');
	    	$departmentString = str_replace(['"','[',']'], '', $departmentString);
	    	$departmentIds = explode(',', $departmentString);
	    	$departmentList = DB::table('gnr_department')->whereIn('id',$departmentIds)->select('id','name')->get();
    	}
    	else{
    		$departmentList = DB::table('gnr_department')->select('id','name')->get();
    	}

    	return response::json($departmentList);
    	
    }
}

