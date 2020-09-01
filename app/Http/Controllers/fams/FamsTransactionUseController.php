<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsTrnsUse;
use App\fams\FamsTrnsUseDetails;
use App\Http\Controllers\gnr\Service;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FamsTransactionUseController extends Controller
{
	public function index(){
		$branchId = Auth::user()->branchId;
		if($branchId==1):
			$productUses = FamsTrnsUse::all();
			return view('fams/transaction/trnsuse/famsViewUse',['productUses' => $productUses]);
		else:
			$productUses = FamsTrnsUse::where('branchId', $branchId);
			return view('fams/transaction/trnsuse/famsViewUse',['productUses' => $productUses]);
		endif;
	}
	
	public function addUse(){
		return view('fams/transaction/trnsuse/famsAddUse');
	}
	
	public function addProductUseItem(Request $req) {
		$rules = array(
                //'employeeId' => 'required'
			'useDate' => 'required'

		);
		$attributeNames = array(
              //'employeeId' => 'Employee Name'
			'useDate' => 'Use Date'

		);
		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
			return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else{
			$forCountAppnProId = count($req->productId5);
			$forCountAppnProQty = count($req->productQntty5);

			if($forCountAppnProId<1){return response()->json('false'); return false;}
			if ($req->useDate!='') {
				$useDate = Carbon::parse($req->useDate);
			}
			else{
				$useDate = Carbon::now();
			}
		  	//$now = Carbon::now();
	  		//$useBillNo  = substr($req->useBillNo, 2);
			$req->request->add(['useDate' => $useDate]);
			$create = FamsTrnsUse::create($req->all());
			$productDetails = new FamsTrnsUseDetails;

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
					'useDate' 	=> $useDate,
					'createdDate' 	=> Carbon::now()
				);
			}
			DB::table('fams_tra_use_details')->insert($dataSet);
			$create = FamsTrnsUse::find ($req->id);
			$logArray = array(
				'moduleId'  => 2,
				'controllerName'  => 'FamsTransactionUseController',
				'tableName'  => 'fams_tra_use',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('fams_tra_use')->max('id')]
			);
			Service::createLog($logArray);
			return response()->json('$productPrice'); 
		}
	}
	
	public function getProductPrice(Request $req)
	{
		$productPrice = DB::table('fams_product')->select('costPrice')->where('id',$req->productId)->first();
		return response()->json($productPrice); 
	}
	
	
	public function productUseDetails(Request $req){
		$useDetails =  FamsTrnsUse::where('id',$req->id)->get();
		foreach ($useDetails as $useDetail) {
			$brnchName = DB::table('gnr_branch')->select('name')->where('id',$useDetail->branchId)->first();
			/*$employeeName = DB::table('gnr_employee')->where('id',$useDetail->employeeId)->value('name');*/
			$employeeName = DB::table('hr_emp_general_info')->where('id',$useDetail->employeeId)->value('emp_name_english');
			$roomName = DB::table('gnr_room')->where('id',$useDetail->roomId)->value('name');
			$useDetailsTables =  FamsTrnsUseDetails::where('useId',$useDetail->id)->get();
			$date = $useDetail->useDate;
		}
		$dateFromarte = date('d-m-Y', strtotime($date));





		$roomDepartment = "";
		$departmentRoom = DB::table('gnr_room')->where('id',$useDetail->roomId)->value('name');
		if ($departmentRoom!=null) {
			$roomDepartment = $departmentRoom;

			$room = DB::table('gnr_room')->where('id',$useDetail->roomId)->first();
			$splitArray=str_replace(array('[', ']', '"', ''), '',  $room->departmentId); 
			$targetArray = explode(",",$splitArray);
			$arraySize = sizeof($targetArray);
			$j = 0;

			foreach($targetArray as $departmentId){

				$temp = DB::table('gnr_department')->where('id',$departmentId)->value('name');
				$roomDepartment = $roomDepartment."   ".$temp;
				$j++; 
				if($j<$arraySize){
					$roomDepartment = $roomDepartment." ";
				}
			}
		}







		$data = array(
			'useDetails'		=> $useDetails,
			'brnchName'	    	=> $brnchName,
			'employeeName'		=> $employeeName,
		// 'roomName'			=> $roomName,
			'roomName'			=> $roomDepartment,
			'useDetailsTables'	=> $useDetailsTables,
			'dateUse'			=> $dateFromarte
		);
		return response()->json($data);
	}
	
	//for edit append rows
	public function editAppendRows(Request $req){
		$useDetailsTables =  FamsTrnsUseDetails::where('useId',$req->id)->get();
		$productId = DB::table('fams_product')->select('productCode','id','name')->get(); 
		
		$data = array(
			'useDetailsTables'	=> $useDetailsTables,
			'productId'	    	=> $productId
		);
		return response()->json($data);
		//return response()->json($useDetailsTables);
	}

	/*public function useDetaisDeleteIdSend(Request $req){
		//FamsTrnsUseDetails::find($req->id)->delete();
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

				$rowFectcs = FamsTrnsUseDetails::select('id')->where('useId',$req->id)->get();
				foreach($rowFectcs as $rowFectc){
					FamsTrnsUseDetails::find($rowFectc->id)->delete();
				}

				$forCreatedDate = FamsTrnsUse::where('id', $req->id)->value('useDate');
				$productDetails = new FamsTrnsUseDetails;

				$dataSet = [];
				for ($i=0; $i < $idCount; $i++){
					$productPrice =  DB::table('fams_product')->select('costPrice')->where('id',$req->productId5[$i])->first();
					$totalPrice = ($req->productQntty5[$i])*($productPrice->costPrice);
					$productName =  DB::table('fams_product')->select('productCode')->where('id',$req->productId5[$i])->first();
					$dataSet[]= array(
						'useId'			=> $req->id,
						'useBillNo'		=> $req->useBillNo,
						'productId'       => $req->productId5[$i],
						'productName'     => $productName->productCode,
						'productQuantity' => $req->productQntty5[$i],
						'costPrice' 		=> $req->productPrice[$i],
						'totalCostPrice' 	=> $totalPrice,
						'createdDate'		=> $forCreatedDate
					);
				}
				DB::table('fams_tra_use_details')->insert($dataSet);
				$previousdata = FamsTrnsUse::find ($req->id);

				$totalQty = FamsTrnsUseDetails::where('useId', $req->id)->sum('productQuantity');
				$totalAmount = FamsTrnsUseDetails::where('useId', $req->id)->sum('totalCostPrice');
				$updateUseTable = FamsTrnsUse::find ($req->id);
				$updateUseTable->useBillNo = $req->useBillNo;
				$updateUseTable->requisitionNo = $req->requisitionNo;
				$updateUseTable->requisition = $req->requisition;
				$updateUseTable->branchId = $req->branchId;
				$updateUseTable->employeeId = $req->employeeId;
				$updateUseTable->roomId = $req->roomId;
				$updateUseTable->totlalUseQuantity = $totalQty;
				$updateUseTable->totalUseAmount = $totalAmount;
				$updateUseTable->save();

				$updateDatas = FamsTrnsUse::where('id', $req->id)->get();
				foreach($updateDatas as $updateData){
					$brnchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
					$employeehName = DB::table('gnr_employee')->where('id',$updateData->employeeId)->value('name');
					$roomName = DB::table('gnr_room')->where('id',$updateData->roomId)->value('name');
					$dateFromarte = date('m-d-Y', strtotime($updateData->useDate));
				}

				$data = array(
					'updateDatas'		=> $updateDatas,
					'brnchName'			=> $brnchName,
					'employeehName'		=> $employeehName,
					'roomName'		    => $roomName,
					'dateFromarte'		=> $dateFromarte,
					'slno'				=> $req->slno
				);
				$logArray = array(
					'moduleId'  => 2,
					'controllerName'  => 'FamsTransactionUseController',
					'tableName'  => 'fams_tra_use',
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
		$previousdata=FamsTrnsUse::find($req->id);
		$idCount = $req->id;
		$count = FamsTrnsUseDetails::where(['useId' => $idCount])->count();
		$rowFectcs = FamsTrnsUseDetails::select('id')->where('useId',$idCount)->get();

		FamsTrnsUse::find($req->id)->delete();
		foreach($rowFectcs as $rowFectc){
			FamsTrnsUseDetails::find($rowFectc->id)->delete();
		}
		$logArray = array(
			'moduleId'  => 2,
			'controllerName'  => 'FamsTransactionUseController',
			'tableName'  => 'fams_tra_use',
			'operation'  => 'delete',
			'previousData'  => $previousdata,
			'primaryIds'  => [$previousdata->id]
		);
		Service::createLog($logArray);
		return response()->json($count);
	}


	public function getFilteredProducts(Request $request)
	{
		$products = DB::table('fams_product');
		if ($request->branchId!='') {
			$products = $products->where('branchId',$request->branchId);
		}
		if ($request->productGroupId!='') {
			$products = $products->where('groupId',$request->productGroupId);
		}
		if ($request->productSubCategoryId!='') {
			$products = $products->where('subCategoryId',$request->productSubCategoryId);
		}

		$products = $products->select('id','name','productCode')->get();

		return response::json($products);
	}

	public function getEmployeeNRoomBaseOnBranch(Request $req) {
		$employeeList = DB::table('hr_emp_org_info')->where('branch_id_fk',$req->branchId)->pluck('emp_id_fk')->toArray();
		$employee = DB::table('hr_emp_general_info')->whereIn('id',$employeeList)->select('id','emp_id','emp_name_english')->get();
		$room = DB::table('gnr_room')->where('branchId',$req->branchId)->select('id','name')->get();

		$data = array(
			'employee' => $employee,
			'room' => $room
		);

		return response::json($data);
	}
}

