<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsTrnsUse;
use App\fams\FamsTrnsUseDetails;
use App\fams\FamsTrnsUseReturn;
use App\Http\Controllers\gnr\Service;
use App\fams\FamsTrnsUseReturnDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FamsTransactionUseReturnController extends Controller
{

	public function index(){
		$branchId = Auth::user()->branchId;
		if($branchId==1):
			$useReturs = FamsTrnsUseReturn::all();
			return view('fams/transaction/trnsuseReturn/viewUseReturn',['useReturs' => $useReturs]);
		else:
			$useReturs = FamsTrnsUseReturn::where('branchId', $branchId);
			return view('fams/transaction/trnsuseReturn/viewUseReturn',['useReturs' => $useReturs]);
		endif;
	}
	
	public function addFamsUseReturnF(){
		return view('fams/transaction/trnsuseReturn/addUseReturn');
	}

	public function invFamsUseBillOnCng(Request $req){
		if($req->useId !==''){
			$invUseTable = FamsTrnsUse::where('id',$req->useId)
			->where('useBillNo',$req->useBillNo)
			->get();
			$productNames = FamsTrnsUseDetails::select('productId')
			->where('useId',$req->useId)
			->where('useBillNo',$req->useBillNo)
			->get();
			foreach($productNames as $productName){
				$products[] = DB::table('fams_product')->select('productCode','id')
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

	public function getQtyFrmFnsUseDtlTbl(Request $req) {
		$productQty = FamsTrnsUseDetails::select('productQuantity', 'costPrice')
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
			$totalAmount = 0;
			for ($i=0; $i < $forCountAppnProId; $i++){
				$productPrice = DB::table('fams_product')->where('id',$req->productId5[$i])->value('costPrice');
				$qty = $req->productQntty5[$i];
				$totalPerPro = $qty*$productPrice;
				$totalAmount += $totalPerPro;
			}
			$useBillNoFUseT = DB::table('fams_tra_use')->where('id',$req->useBillNo)->value('useBillNo');
			$now = Carbon::now();
			$req->request->add(['totalAmount' => $totalAmount,'useId'=> $req->useBillNo, 'useBillNo' => $useBillNoFUseT, 'createdDate' => $now]);
			$create = FamsTrnsUseReturn::create($req->all());
			$productDetails = new FamsTrnsUseReturnDetails;

			$dataSet = [];
			for ($i=0; $i < $forCountAppnProId; $i++){
				$perProPrice = DB::table('fams_product')->where('id',$req->productId5[$i])->value('costPrice');;
				$totalPrice = $perProPrice*$req->productQntty5[$i];

				$dataSet[]= array(
					'useReturnId'		=> $create->id,
					'useReturnBillNo'	=> $create->useReturnBillNo,
					'useBillNo'		=> $useBillNoFUseT,
					'productId'       => $req->productId5[$i],
					'productQuantity' => $req->productQntty5[$i],
					'price' 			=> $perProPrice,
					'totalPrice' 	    => $totalPrice,
					'createdDate'     => $now
				);
			}
			DB::table('fams_tra_use_return_details')->insert($dataSet);
			$create = FamsTrnsUseReturn::find ($req->id);
			$logArray = array(
				'moduleId'  => 2,
				'controllerName'  => 'FamsTransactionUseReturnController',
				'tableName'  => 'fams_tra_use_return',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('fams_tra_use_return')->max('id')]
			);
			Service::createLog($logArray);
			return response()->json('$productPrice'); 
		}
	}
	
	//for edit append rows
	public function editAppnRows(Request $req){
		$useRtrnDetailsTables = FamsTrnsUseReturnDetails::where('useReturnId',$req->id)->get();
		foreach($useRtrnDetailsTables as $useRtrnDetailsTable){
			$purchaseQuantity[] = DB::table('fams_tra_use_details')->select('productQuantity')
			->where('useBillNo', $useRtrnDetailsTable->useBillNo)
			->where('productId', $useRtrnDetailsTable->productId)
			->first();
		}

		$proIdFrmUseDetlsTbs   =  FamsTrnsUseDetails::select('productId')->where('useId',$req->useId)->get();
		foreach($proIdFrmUseDetlsTbs as $proIdFrmUseDetlsTb){
			$productId[] = DB::table('fams_product')->select('productCode', 'id')->where('id',$proIdFrmUseDetlsTb->productId)->first();
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

				$rowFectcs = FamsTrnsUseReturnDetails::select('id')->where('useReturnId',$req->id)->get();
				foreach($rowFectcs as $rowFectc){
					FamsTrnsUseReturnDetails::find($rowFectc->id)->delete();
				}

				$productDetails = new FamsTrnsUseReturnDetails;

				$useBillNoFUseT = DB::table('fams_tra_use')->where('id',$req->useBillNo)->value('useBillNo');
				$dataSet = [];
				for ($i=0; $i < $idCount; $i++){
					$perProPrice = DB::table('fams_product')->where('id',$req->productId5[$i])->value('costPrice');;
					$totalPrice = $perProPrice*$req->productQntty5[$i];

					$dataSet[]= array(
						'useReturnId'		=> $req->id,
						'useReturnBillNo'	=> $req->useReturnBillNo,
						'useBillNo'		=> $useBillNoFUseT,
						'productId'       => $req->productId5[$i],
						'productQuantity' => $req->productQntty5[$i],
						'price' 			=> $perProPrice,
						'totalPrice' 	    => $totalPrice,
						'createdDate'		=> $req->createdDate
					);
				}
				DB::table('fams_tra_use_return_details')->insert($dataSet);
				$previousdata = FamsTrnsUseReturn::find ($req->id);
				$totalQty = FamsTrnsUseReturnDetails::where('useReturnId', $req->id)->sum('productQuantity');
				$totalAmount = FamsTrnsUseReturnDetails::where('useReturnId', $req->id)->sum('totalPrice');
				$updateUseReTable = FamsTrnsUseReturn::find ($req->id);
				$updateUseReTable->useReturnBillNo = $req->useReturnBillNo;
				$updateUseReTable->useId = $req->useBillNo;
				$updateUseReTable->useBillNo = $useBillNoFUseT;
				$updateUseReTable->branchId = $req->branchId;
				$updateUseReTable->employeeId = $req->employeeId;
				$updateUseReTable->roomId = $req->roomId;
				$updateUseReTable->totalQuantity = $totalQty;
				$updateUseReTable->totalAmount = $totalAmount;
				$updateUseReTable->save();

				$updateDatas = FamsTrnsUseReturn::where('id', $req->id)->get();
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
				$logArray = array(
					'moduleId'  => 2,
					'controllerName'  => 'FamsTransactionUseReturnController',
					'tableName'  => 'fams_tra_use_return',
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
	public function deleteItem(Request $req) {
		$idCount = $req->id;
		$previousdata=FamsTrnsUseReturn::find($req->id);
		$count = FamsTrnsUseReturnDetails::where(['useReturnId' => $idCount])->count();
		$rowFectcs = FamsTrnsUseReturnDetails::select('id')->where('useReturnId',$idCount)->get();

		FamsTrnsUseReturn::find($req->id)->delete();
		foreach($rowFectcs as $rowFectc){
			FamsTrnsUseReturnDetails::find($rowFectc->id)->delete();
		}
		$logArray = array(
			'moduleId'  => 2,
			'controllerName'  => 'FamsTransactionUseReturnController',
			'tableName'  => 'fams_tra_use_return',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response()->json($count);
	}


	public function famsUseItemPerUseBillNo(Request $req){
		$productAccPerBillNos = FamsTrnsUseDetails::select('productId')->where('useId', $req->useId)->get();
		foreach($productAccPerBillNos as $productAccPerBillNo){
			$productName[] = DB::table('fams_product')->select('productCode','id')->where('id',$productAccPerBillNo->productId)->first();
		}
		return response()->json($productName);
	}
}

