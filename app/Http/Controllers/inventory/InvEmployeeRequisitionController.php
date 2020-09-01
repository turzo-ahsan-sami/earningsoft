<?php

namespace App\Http\Controllers\inventory;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\gnr\Service;
use App\inventory\InvEmployeeRequisition;
use App\inventory\InvEmployeeRequisitionDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvEmployeeRequisitionController extends Controller
{
	public function index(){
		$user = Auth::user();
		Session::put('branchId', $user->branchId);
		$gnrBranchId = Session::get('branchId');

		if($gnrBranchId==1){
			$requisitions = InvEmployeeRequisition::where('status', 1)->get();
			return view('inventory/transaction/employeeRequisition/viewRequisition',['requisitions' => $requisitions]);
		}else{
			$requisitions = InvEmployeeRequisition::where('branchId', $gnrBranchId)
			->where('status', 1)
			->get();
			return view('inventory/transaction/employeeRequisition/viewRequisition',['requisitions' => $requisitions]);
		}

	}

	public function addInvProEmployeeRequisition(){
		return view('inventory/transaction/employeeRequisition/addRequisition');
	}

	public function addItem(Request $req) 
	{
		$rules = array(
			'requisitionNo' => 'required',
			'branchId' => 'required',
			'employeeId' => 'required',
			'totalQuantity' => 'required'
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
		  	//Requestion bill number
			$requisitionMaxId = DB::table('inv_employee_requisition')->where('branchId',Auth::user()->branchId)->count()+1;
			$branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode'); 
			$valueForField = 'RE.'.sprintf('%04d.', $branchCode) . sprintf('%06d', $requisitionMaxId);

			$now = Carbon::now();
			$req->request->add(['totalAmount' => 0, 'requisitionNo' =>$valueForField, 'createdDate' => $now]);
			$create = InvEmployeeRequisition::create($req->all());
    		//return response()->json($create);
			$productDetails = new InvEmployeeRequisitionDetails;

			$dataSet = [];
			for ($i=0; $i < $forCountAppnProId; $i++){
				$dataSet[]= array(
					'empRequisitionId'		=> $create->id,
					'requisitionNo'			=> $req->requisitionNo,
					'productId'       		=> $req->productId5[$i],
					'productQuantity' 		=> $req->productQntty5[$i],
					'price' 					=> 0,
					'totalPrice' 			    => 0,
					'createdDate' 			=> $now
				);
			}
			DB::table('inv_employee_requisition_details')->insert($dataSet);
			$create = InvEmployeeRequisition::find ($req->id);
			$logArray = array(
				'moduleId'  => 1,
				'controllerName'  => 'InvEmployeeRequisitionController',
				'tableName'  => 'inv_employee_requisition',
				'operation'  => 'insert',
				'primaryIds'  => [DB::table('inv_employee_requisition')->max('id')]
			);
			Service::createLog($logArray);
			return response()->json('Success'); 
		}
	}

	public function editAppendRows(Request $req){
		$requiDetailsTables =  InvEmployeeRequisitionDetails::where('empRequisitionId',$req->id)->get();
		$productId =  DB::table('inv_product')->select('name','id')->get(); 
		
		$data = array(
			'requiDetailsTables'	=> $requiDetailsTables,
			'productId'	    		=> $productId
		);
		return response()->json($data);
		//return response()->json($useDetailsTables);
	}

	public function editRequItem(Request $req){
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
				$rowFectcs = InvEmployeeRequisitionDetails::select('id')->where('empRequisitionId',$req->id)->get();
				foreach($rowFectcs as $rowFectc){
					InvEmployeeRequisitionDetails::find($rowFectc->id)->delete();
				}

				$employeeRequi = new InvEmployeeRequisitionDetails;

				$dataSet = [];
				for ($i=0; $i < $idCount; $i++){
		  		//$productPrice =  DB::table('inv_product')->select('costPrice')->where('id',$req->productId5[$i])->first();
		  		//$totalPrice = ($req->productQntty5[$i])*($productPrice->costPrice);
					$createdDate = DB::table('inv_employee_requisition')->where('id',$req->id)->value('createdDate');
					$dataSet[]= array(
						'empRequisitionId'		=> $req->id,
						'requisitionNo'			=> $req->requisitionNo,
						'productId'       		=> $req->productId5[$i],
						'productQuantity' 		=> $req->productQntty5[$i],
						'price' 					=> 0,
						'totalPrice' 				=> 0,
						'createdDate' 			=> $createdDate
					);
				}
				DB::table('inv_employee_requisition_details')->insert($dataSet);

				$totalQty = InvEmployeeRequisitionDetails::where('empRequisitionId', $req->id)->sum('productQuantity');
				$totalAmount = InvEmployeeRequisitionDetails::where('empRequisitionId', $req->id)->sum('totalPrice');
				$previousdata = InvEmployeeRequisition::find ($req->id);
				$updateEmpReqTable = InvEmployeeRequisition::find ($req->id);
				$updateEmpReqTable->requisitionNo = $req->requisitionNo;
				$updateEmpReqTable->branchId 		= $req->branchId;
				$updateEmpReqTable->employeeId 	= $req->employeeId;
				$updateEmpReqTable->totalQuantity = $totalQty;
				$updateEmpReqTable->totalAmount   = 0;
				$updateEmpReqTable->description   = $req->description;
				$updateEmpReqTable->save();

				$updateDatas = InvEmployeeRequisition::where('id', $req->id)->get();
				foreach($updateDatas as $updateData){
					$brnchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
					$employeehName = DB::table('hr_emp_general_info')->select('emp_name_english')->where('id',$updateData->employeeId)->first();
					$dateFromarte  = $updateData->createdDate;
				}
				$data = array(
					'updateDatas'		=> $updateDatas,
					'brnchName'			=> $brnchName,
					'employeehName'		=> $employeehName,
					'dateFromarte'		=> $dateFromarte,
					'slno'				=> $req->slno
				);
				$logArray = array(
					'moduleId'  => 1,
					'controllerName'  => 'InvEmployeeRequisitionController',
					'tableName'  => 'inv_employee_requisition',
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
		$count 	= InvEmployeeRequisitionDetails::where(['empRequisitionId' => $idCount])->count();
		$rowFectcs = InvEmployeeRequisitionDetails::select('id')->where('empRequisitionId',$idCount)->get();
		$previousdata=InvEmployeeRequisition::find($req->id);

		$updateBrnReqTable = InvEmployeeRequisition::find($req->id);
		$updateBrnReqTable->status  = 0;
		$updateBrnReqTable->save();

		foreach($rowFectcs as $rowFectc){
			$updateBrnReqTable = InvEmployeeRequisitionDetails::find($rowFectc->id);
			$updateBrnReqTable->status = 0;
			$updateBrnReqTable->save();
		}

		 /*InvEmployeeRequisition::find($req->id)->delete();
		 foreach($rowFectcs as $rowFectc){
			 InvEmployeeRequisitionDetails::find($rowFectc->id)->delete();
			}*/

			$logArray = array(
				'moduleId'  => 1,
				'controllerName'  => 'InvEmployeeRequisitionController',
				'tableName'  => 'inv_employee_requisition',
				'operation'  => 'delete',
				'previousData'  => $previousdata,
				'primaryIds'  => [$previousdata->id]
			);
			Service::createLog($logArray);
			return response()->json($count);
		}


		public function requsitionDetails(Request $req){
			$empRequisitions =  InvEmployeeRequisition::where('id',$req->id)->get();
			foreach ($empRequisitions as $empRequiDetail) {
				$brnchName = DB::table('gnr_branch')->select('name')->where('id',$empRequiDetail->branchId)->first();
		//$employeehName = DB::table('gnr_employee')->select('name')->where('id',$empRequiDetail->employeeId)->first();



		//$employeeId = DB::table('hr_emp_org_info')->where('id',$empRequiDetail->employeeId)->value('id');
				$employeehName = DB::table('hr_emp_general_info')->select('id','emp_id','emp_name_english')->where('id',$empRequiDetail->employeeId)->first();




				$InvEmpRequiDetails =  InvEmployeeRequisitionDetails::where('empRequisitionId',$empRequiDetail->id)->get();
				foreach ($InvEmpRequiDetails as $InvEmpRequiDetail) {
					$productName[] = DB::table('inv_product')->select('name')->where('id',$InvEmpRequiDetail->productId)->get();
				}

				$date = $empRequiDetail->createdDate;
			}
			$dateFromarte = date('d-m-Y', strtotime($date));

			$data = array(
				'empRequisitions'		=> $empRequisitions,
				'brnchName'	    		=> $brnchName,
				'employeehName'			=> $employeehName,
				'InvEmpRequiDetails'	=> $InvEmpRequiDetails,
				'productName'			=> $productName,
				'dateFormateDate'		=> $dateFromarte
			);
			return response()->json($data);
		}

	//change dropdown
		public function invEmpReqOnCngGrp(Request $req){

			if($req->productGroupId=='' && $req->productCategoryId==''){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->pluck('name', 'id');
				$productCategoryList    =  DB::table('inv_product_category')->pluck('name', 'id');
				$data = array(
					'productSubCategoryList' => $productSubCategoryList,
					'productCategoryList'    => $productCategoryList
				);
				return response()->json($data); 
			}

			else if($req->productGroupId && $req->productCategoryId==''){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
				$productCategoryList    =  DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

				$data = array(
					'productSubCategoryList' => $productSubCategoryList,
					'productCategoryList'    => $productCategoryList

				);
				return response()->json($data);
			}

			else if($req->productCategoryId && $req->productGroupId==''){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->where('productCategoryId',$req->productCategoryId)->pluck('name', 'id');
			//$productCategoryList    =  DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

				$data = array(
					'productSubCategoryList' => $productSubCategoryList
			//'productCategoryList'    => $productCategoryList

				);
				return response()->json($data);
			}

			else if($req->productCategoryId && $req->productCategoryId){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->where('productGroupId',$req->productGroupId)
				->pluck('name', 'id');
				$productCategoryList    =  DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
				$data = array(
					'productSubCategoryList' => $productSubCategoryList,
					'productCategoryList'    => $productCategoryList

				); 
				return response()->json($data);
			}

		}

		public function invEmpReqOnCngCtg(Request $req){

			if($req->productGroupId=='' && $req->productCategoryId==''){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->pluck('name', 'id');
				$productCategoryList    =  DB::table('inv_product_category')->pluck('name', 'id');
				$data = array(
					'productSubCategoryList' => $productSubCategoryList,
					'productCategoryList'    => $productCategoryList
				);
				return response()->json($data); 
			}

			else if($req->productGroupId && $req->productCategoryId==''){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
				$productCategoryList    =  DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

				$data = array(
					'productSubCategoryList' => $productSubCategoryList,
					'productCategoryList'    => $productCategoryList

				);
				return response()->json($data);
			}

			else if($req->productCategoryId && $req->productGroupId==''){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->where('productCategoryId',$req->productCategoryId)->pluck('name', 'id');
			//$productCategoryList    =  DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

				$data = array(
					'productSubCategoryList' => $productSubCategoryList
			//'productCategoryList'    => $productCategoryList

				);
				return response()->json($data);
			}

			else if($req->productCategoryId && $req->productCategoryId){
				$productSubCategoryList =  DB::table('inv_product_sub_category')->where('productGroupId',$req->productGroupId)
				->where('productCategoryId',$req->productCategoryId)
				->pluck('name', 'id');
			//$productCategoryList    =  DB::table('inv_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
				$data = array(
					'productSubCategoryList' => $productSubCategoryList
			//'productCategoryList'    => $productCategoryList

				); 
				return response()->json($data);
			}
		}				


	}
