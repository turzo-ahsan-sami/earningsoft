<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsBranchRequisition;
use App\fams\FamsBranchRequisitionDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FamsBranchRequisitionController extends Controller
{
	public function index(){
		$user = Auth::user();
		Session::put('branchId', $user->branchId);
		$gnrBranchId = Session::get('branchId');

		if($gnrBranchId==1){
			$requisitions = FamsBranchRequisition::where('status', 1)->get();
			return view('fams/transaction/branchRequisition/viewRequisition',['requisitions' => $requisitions]);
		}else{
			$requisitions = FamsBranchRequisition::where('branchId', $gnrBranchId)
			->where('status', 1)
			->get();	
			return view('fams/transaction/branchRequisition/viewRequisition',['requisitions' => $requisitions]);
		}
	}

	public function addFamsBrnRequiF(){
		return view('fams/transaction/branchRequisition/addRequisition');
	}

	public function addItem(Request $req) 
	{

		$forCountAppnProId 	= count($req->productId5);
		$forCountAppnProQty = count($req->productQntty5);

		if($forCountAppnProId<1){return response()->json('false'); return false;}

		$req->request->add(['totalAmount' => 0]);
		$create = FamsBranchRequisition::create($req->all());
		//return response()->json($create);
		$productDetails = new FamsBranchRequisitionDetails;

		$dataSet = [];
		for ($i=0; $i < $forCountAppnProId; $i++){
			$dataSet[]= array(
				'branchReqId'				=> $create->id,
				'requisitionNo'			=> $req->requisitionNo,
				'productId'       		=> $req->productId5[$i],
				'productQuantity' 		=> $req->productQntty5[$i],
				'price' 					=> 0,
				'totalPrice' 			    => 0
			);
		}
		\DB::table('fams_branch_requisition_details')->insert($dataSet);
		$create = FamsBranchRequisition::find ($req->id);
		$logArray = array(
			'moduleId'  => 2,
			'controllerName'  => 'FamsBranchRequisitionController',
			'tableName'  => 'fams_branch_requisition',
			'operation'  => 'insert',
			'primaryIds'  => [DB::table('fams_branch_requisition')->max('id')]
		);
		Service::createLog($logArray);
		return response()->json('Success'); 
	}

	public function editAppendRows(Request $req){
		$requiDetailsTables =  FamsBranchRequisitionDetails::where('branchReqId',$req->id)->get();
		$productId = \ DB::table('fams_product_sub_category')->select('name','id')->get(); 
		
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
			$rowFectcs = FamsBranchRequisitionDetails::select('id')->where('branchReqId',$req->id)->get();
			foreach($rowFectcs as $rowFectc){
				$updateStatus = FamsBranchRequisitionDetails::find($rowFectc->id)->delete();
			 //$updateStatus->status = 0;
			 //$updateStatus->sav();
			}

			$employeeRequi = new FamsBranchRequisitionDetails;

			$dataSet = [];
			for ($i=0; $i < $idCount; $i++){
		  		//$productPrice = \ DB::table('fams_product')->select('costPrice')->where('id',$req->productId5[$i])->first();
		  		//$totalPrice = ($req->productQntty5[$i])*($productPrice->costPrice);
				$dataSet[]= array(
					'branchReqId'				=> $req->id,
					'requisitionNo'			=> $req->requisitionNo,
					'productId'       		=> $req->productId5[$i],
					'productQuantity' 		=> $req->productQntty5[$i],
					'price' 					=> 0,
					'totalPrice' 				=> 0
				);
			}
			\DB::table('fams_branch_requisition_details')->insert($dataSet);

			$totalQty = FamsBranchRequisitionDetails::where('branchReqId', $req->id)->sum('productQuantity');
			$totalAmount = FamsBranchRequisitionDetails::where('branchReqId', $req->id)->sum('totalPrice');
			$previousdata = FamsBranchRequisition::find ($req->id);
			$updateBrnReqTable = FamsBranchRequisition::find ($req->id);
			$updateBrnReqTable->requisitionNo = $req->requisitionNo;
			$updateBrnReqTable->branchId 		= $req->branchId;
			$updateBrnReqTable->requisitionTo = $req->requisitionTo;
			$updateBrnReqTable->totalQuantity = $totalQty;
			$updateBrnReqTable->totalAmount   = 0;
			$updateBrnReqTable->save();

			$updateDatas = FamsBranchRequisition::where('id', $req->id)->get();
			foreach($updateDatas as $updateData){
				$brnchName = \DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
				$requisitiontTo = \DB::table('gnr_branch')->select('name')->where('id',$updateData->requisitionTo)->first();
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
				'moduleId'  => 2,
				'controllerName'  => 'FamsBranchRequisitionController',
				'tableName'  => 'fams_branch_requisition',
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
		$count = FamsBranchRequisitionDetails::where(['branchReqId' => $idCount])->count();
		$rowFectcs = FamsBranchRequisitionDetails::select('id')->where('branchReqId',$idCount)->get();
		$previousdata=FamsBranchRequisition::find($req->id);
		$updateBrnReqTable = FamsBranchRequisition::find($req->id);
		$updateBrnReqTable->status  = 0;
		$updateBrnReqTable->save();
		foreach($rowFectcs as $rowFectc){
			$updateBrnReqTable = FamsBranchRequisitionDetails::find($rowFectc->id);
			$updateBrnReqTable->status = 0;
			$updateBrnReqTable->save();
		}
		$logArray = array(
			'moduleId'  => 2,
			'controllerName'  => 'FamsBranchRequisitionController',
			'tableName'  => 'fams_branch_requisition',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response()->json($count);
	}


	public function requisitionDetails(Request $req){
		$brnRequisitions =  FamsBranchRequisition::where('id',$req->id)->get();
		foreach ($brnRequisitions as $brnRequisition) {
			$brnchName = \DB::table('gnr_branch')->select('name')->where('id',$brnRequisition->branchId)->first();
			$requisitiontTo = \DB::table('gnr_branch')->select('name')->where('id',$brnRequisition->requisitionTo)->first();

			$InvBrnRequiDetails =  FamsBranchRequisitionDetails::where('branchReqId',$brnRequisition->id)->get();
			foreach ($InvBrnRequiDetails as $InvBrnRequiDetail) {
				$productName[] = \DB::table('fams_product_sub_category')->select('name')->where('id',$InvBrnRequiDetail->productId)->get();
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
