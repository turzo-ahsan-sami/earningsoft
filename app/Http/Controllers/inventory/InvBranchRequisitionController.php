<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvBranchRequisition;
use App\inventory\InvBranchRequisitionDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvBranchRequisitionController extends Controller
{
	public function index(){
		$user = Auth::user();
		Session::put('branchId', $user->branchId);
		$gnrBranchId = Session::get('branchId');

		if($gnrBranchId==1){
			$requisitions = InvBranchRequisition::where('status', 1)->get();
			return view('inventory/transaction/branchRequisition/viewRequisition',['requisitions' => $requisitions]);
		}else{
			$requisitions = InvBranchRequisition::where('branchId', $gnrBranchId)
			->where('status', 1)
			->get();	
			return view('inventory/transaction/branchRequisition/viewRequisition',['requisitions' => $requisitions]);
		}
	}

	public function addInvBrnRequiF(){
		return view('inventory/transaction/branchRequisition/addRequisition');
	}

	public function addItem(Request $req) 
	{

		$forCountAppnProId = count($req->productId5);
		$forCountAppnProQty = count($req->productQntty5);

		if($forCountAppnProId<1){return response()->json('false'); return false;}
	  	//Requisition bill number;
		$requisitionMaxId = DB::table('inv_branch_requisition')->where('branchId',Auth::user()->branchId)->count()+1;
		$branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
		$valueForField = 'REB.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $requisitionMaxId);

		$now = Carbon::now();
		$req->request->add(['totalAmount' => 0, 'requisitionNo' => $valueForField, 'createdDate' => $now]);
		$create = InvBranchRequisition::create($req->all());
		//return response()->json($create);
		$productDetails = new InvBranchRequisitionDetails;

		$dataSet = [];
		for ($i=0; $i < $forCountAppnProId; $i++){
			$dataSet[]= array(
				'branchReqId'				=> $create->id,
				'requisitionNo'			=> $req->requisitionNo,
				'productId'       		=> $req->productId5[$i],
				'productQuantity' 		=> $req->productQntty5[$i],
				'price' 					=> 0,
				'totalPrice' 			    => 0,
				'createdDate' 			=> $now
			);
		}
		DB::table('inv_branch_requisition_details')->insert($dataSet);
		$create = InvBranchRequisition::find ($req->id);
		$logArray = array(
			'moduleId'  => 1,
			'controllerName'  => 'InvBranchRequisitionController',
			'tableName'  => 'inv_branch_requisition',
			'operation'  => 'insert',
			'primaryIds'  => [DB::table('inv_branch_requisition')->max('id')]
		);
		Service::createLog($logArray);
		return response()->json('$productPrice'); 
	}

	public function editAppendRows(Request $req){
		$requiDetailsTables =  InvBranchRequisitionDetails::where('branchReqId',$req->id)->get();
		$productId = DB::table('inv_product')->select('name','id')->get(); 
		
		$data = array(
			'requiDetailsTables'	=> $requiDetailsTables,
			'productId'	    		=> $productId
		);
		return response()->json($data);
		//return response()->json($useDetailsTables);
	}


	public function editRequItem(Request $req){
		
		$idCount = count($req->productId5);
		if($idCount>0){
			$rowFectcs = InvBranchRequisitionDetails::select('id')->where('branchReqId',$req->id)->get();
			foreach($rowFectcs as $rowFectc){
				$updateStatus = InvBranchRequisitionDetails::find($rowFectc->id)->delete();
			 //$updateStatus->status = 0;
			 //$updateStatus->sav();
			}

			$employeeRequi = new InvBranchRequisitionDetails;

			$dataSet = [];
			for ($i=0; $i < $idCount; $i++){
		  		//$productPrice = DB::table('inv_product')->select('costPrice')->where('id',$req->productId5[$i])->first();
		  		//$totalPrice = ($req->productQntty5[$i])*($productPrice->costPrice);
				$createdDate = DB::table('inv_branch_requisition')->where('id', $req->id)->value('createdDate');
				$dataSet[]= array(
					'branchReqId'				=> $req->id,
					'requisitionNo'			=> $req->requisitionNo,
					'productId'       		=> $req->productId5[$i],
					'productQuantity' 		=> $req->productQntty5[$i],
					'price' 					=> 0,
					'totalPrice' 				=> 0,
					'createdDate' 			=> $createdDate
				);
			}
			DB::table('inv_branch_requisition_details')->insert($dataSet);

			$totalQty = InvBranchRequisitionDetails::where('branchReqId', $req->id)->sum('productQuantity');
			$totalAmount = InvBranchRequisitionDetails::where('branchReqId', $req->id)->sum('totalPrice');
			$previousdata = InvBranchRequisition::find ($req->id);
			$updateBrnReqTable = InvBranchRequisition::find ($req->id);
			$updateBrnReqTable->requisitionNo = $req->requisitionNo;
			$updateBrnReqTable->branchId 		= $req->branchId;
			$updateBrnReqTable->requisitionTo = $req->requisitionTo;
			$updateBrnReqTable->totalQuantity = $totalQty;
			$updateBrnReqTable->totalAmount   = 0;
			$updateBrnReqTable->description   = $req->description;
			$updateBrnReqTable->save();

			$updateDatas = InvBranchRequisition::where('id', $req->id)->get();
			foreach($updateDatas as $updateData){
				$brnchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
				$requisitiontTo = DB::table('gnr_branch')->select('name')->where('id',$updateData->requisitionTo)->first();
				$dateFromarte  = $updateData->createdDate;
			}
			$data = array(
				'updateDatas'		=> $updateDatas,
				'brnchName'			=> $brnchName,
				'requisitiontTo'	=> $requisitiontTo,
				'dateFromarte'		=> $dateFromarte,
				'slno'				=> $req->slno
			);
			$logArray = array(
				'moduleId'  => 1,
				'controllerName'  => 'InvBranchRequisitionController',
				'tableName'  => 'inv_branch_requisition',
				'operation'  => 'update',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);
			return response()->json($data);

		}else{return response()->json('false');}
	}

//delete
	public function deleteItem(Request $req) {
		$idCount = $req->id;
		$count = InvBranchRequisitionDetails::where(['branchReqId' => $idCount])->count();
		$rowFectcs = InvBranchRequisitionDetails::select('id')->where('branchReqId',$idCount)->get();
		$previousdata=InvBranchRequisition::find($req->id);

		$updateBrnReqTable = InvBranchRequisition::find($req->id);
		$updateBrnReqTable->status  = 0;
		$updateBrnReqTable->save();
		foreach($rowFectcs as $rowFectc){
			$updateBrnReqTable = InvBranchRequisitionDetails::find($rowFectc->id);
			$updateBrnReqTable->status = 0;
			$updateBrnReqTable->save();
		}
		$logArray = array(
			'moduleId'  => 1,
			'controllerName'  => 'InvBranchRequisitionController',
			'tableName'  => 'inv_branch_requisition',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response()->json($count);
	}


	public function requisitionDetails(Request $req){
		$brnRequisitions =  InvBranchRequisition::where('id',$req->id)->get();
		foreach ($brnRequisitions as $brnRequisition) {
			$brnchName = DB::table('gnr_branch')->select('name')->where('id',$brnRequisition->branchId)->first();
			$requisitiontTo = DB::table('gnr_branch')->select('name')->where('id',$brnRequisition->requisitionTo)->first();

			$InvBrnRequiDetails =  InvBranchRequisitionDetails::where('branchReqId',$brnRequisition->id)->get();
			foreach ($InvBrnRequiDetails as $InvBrnRequiDetail) {
				$productName[] = DB::table('inv_product')->select('name')->where('id',$InvBrnRequiDetail->productId)->get();
			}

			$date = $brnRequisition->createdDate;
		}
		$dateFromarte = date('d-m-Y', strtotime($date));

		$data = array(
			'brnRequisitions'		=> $brnRequisitions,
			'brnchName'	    		=> $brnchName,
			'requisitiontTo'		=> $requisitiontTo,
			'InvBrnRequiDetails'	=> $InvBrnRequiDetails,
			'productName'			=> $productName,
			'dateFormateDate'		=> $dateFromarte
		);
		return response()->json($data);
	}

	

}
