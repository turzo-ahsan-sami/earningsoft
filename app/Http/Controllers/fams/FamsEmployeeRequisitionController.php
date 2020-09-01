<?php

namespace App\Http\Controllers\fams;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\fams\FamsEmployeeRequisition;
use App\fams\FamsEmployeeRequisitionDetails;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class FamsEmployeeRequisitionController extends Controller
{
    public function index(){
    	$user = Auth::user();
		Session::put('branchId', $user->branchId);
		$gnrBranchId = Session::get('branchId');

		if($gnrBranchId==1){
			$requisitions = FamsEmployeeRequisition::where('status', 1)->get();
      		return view('fams/transaction/employeeRequisition/viewRequisition',['requisitions' => $requisitions]);
		}else{
			$requisitions = FamsEmployeeRequisition::where('branchId', $gnrBranchId)
														->where('status', 1)
														->get();
      		return view('fams/transaction/employeeRequesition/viewRequisition',['requisitions' => $requisitions]);
		}

    }

    public function addFamsEmployeeRequisition(){
      return view('fams/transaction/employeeRequisition/addRequisition');
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
		  	
		  	$req->request->add(['totalAmount' => 0]);
            $create = FamsEmployeeRequisition::create($req->all());
    		//return response()->json($create);
		    $productDetails = new FamsEmployeeRequisitionDetails;
			 
	  	$dataSet = [];
	  	for ($i=0; $i < $forCountAppnProId; $i++){
		  $dataSet[]= array(
			  'empRequisitionId'		=> $create->id,
			  'requisitionNo'			=> $req->requisitionNo,
        	  'productId'       		=> $req->productId5[$i],
			  'productQuantity' 		=> $req->productQntty5[$i],
			  'price' 					=> $req->productPrice[$i],
			  'totalPrice' 			    => $req->proTotalPrice[$i]
		  );
		}
		\DB::table('fams_employee_requisition_details')->insert($dataSet);
		$create = FamsEmployeeRequisition::find ($req->id);
	    return response()->json('$productPrice'); 
  	}
}

public function editAppendRows(Request $req){
		$requiDetailsTables =  FamsEmployeeRequisitionDetails::where('empRequisitionId',$req->id)->get();
		$productId = \ DB::table('fams_product_sub_category')->select('name','id')->get(); 
		
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
	  		$rowFectcs = FamsEmployeeRequisitionDetails::select('id')->where('empRequisitionId',$req->id)->get();
	  		foreach($rowFectcs as $rowFectc){
			 FamsEmployeeRequisitionDetails::find($rowFectc->id)->delete();
		 	}

			$employeeRequi = new FamsEmployeeRequisitionDetails;
			 
		  	$dataSet = [];
		  	for ($i=0; $i < $idCount; $i++){
		  		//$productPrice = \ DB::table('fams_product')->select('costPrice')->where('id',$req->productId5[$i])->first();
		  		//$totalPrice = ($req->productQntty5[$i])*($productPrice->costPrice);
			  $dataSet[]= array(
				  'empRequisitionId'		=> $req->id,
				  'requisitionNo'			=> $req->requisitionNo,
	        	  'productId'       		=> $req->productId5[$i],
				  'productQuantity' 		=> $req->productQntty5[$i],
				  'price' 					=> 0,
				  'totalPrice' 				=> 0
			  );
			}
		   \DB::table('fams_employee_requisition_details')->insert($dataSet);

		  $totalQty = FamsEmployeeRequisitionDetails::where('empRequisitionId', $req->id)->sum('productQuantity');
		  $totalAmount = FamsEmployeeRequisitionDetails::where('empRequisitionId', $req->id)->sum('totalPrice');
		  $updateEmpReqTable = FamsEmployeeRequisition::find ($req->id);
	      $updateEmpReqTable->requisitionNo = $req->requisitionNo;
	      $updateEmpReqTable->branchId 		= $req->branchId;
	      $updateEmpReqTable->employeeId 	= $req->employeeId;
	      $updateEmpReqTable->totalQuantity = $totalQty;
	      $updateEmpReqTable->totalAmount   = 0;
	      $updateEmpReqTable->save();

	      $updateDatas = FamsEmployeeRequisition::where('id', $req->id)->get();
	      foreach($updateDatas as $updateData){
	      $brnchName = \DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
	      $employeehName = \DB::table('gnr_employee')->select('name')->where('id',$updateData->employeeId)->first();
	      $dateFromarte  = $updateData->createdDate;
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
		 $count 	= FamsEmployeeRequisitionDetails::where(['empRequisitionId' => $idCount])->count();
		 $rowFectcs = FamsEmployeeRequisitionDetails::select('id')->where('empRequisitionId',$idCount)->get();

		 $updateBrnReqTable = FamsEmployeeRequisition::find($req->id);
		 $updateBrnReqTable->status  = 0;
	     $updateBrnReqTable->save();

	     foreach($rowFectcs as $rowFectc){
			 $updateBrnReqTable = FamsEmployeeRequisitionDetails::find($rowFectc->id);
			 $updateBrnReqTable->status = 0;
	     	 $updateBrnReqTable->save();
		 }
		 
		 /*FamsEmployeeRequisition::find($req->id)->delete();
		 foreach($rowFectcs as $rowFectc){
			 FamsEmployeeRequisitionDetails::find($rowFectc->id)->delete();
		 }*/
      return response()->json($count);
    }


    public function requsitionDetails(Request $req){
		$empRequisitions =  FamsEmployeeRequisition::where('id',$req->id)->get();
		foreach ($empRequisitions as $empRequiDetail) {
		$brnchName = \DB::table('gnr_branch')->select('name')->where('id',$empRequiDetail->branchId)->first();
		$employeehName = \DB::table('gnr_employee')->select('name')->where('id',$empRequiDetail->employeeId)->first();

		$InvEmpRequiDetails =  FamsEmployeeRequisitionDetails::where('empRequisitionId',$empRequiDetail->id)->get();
		foreach ($InvEmpRequiDetails as $InvEmpRequiDetail) {
		$productName[] = \DB::table('fams_product_sub_category')->select('name')->where('id',$InvEmpRequiDetail->productId)->get();
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
	public function famsEmpReqOnCngGrp(Request $req){

			if($req->productGroupId=='' && $req->productCategoryId==''){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->pluck('name', 'id');
			$productCategoryList    = \ DB::table('fams_product_category')->pluck('name', 'id');
			$data = array(
			'productSubCategoryList' => $productSubCategoryList,
			'productCategoryList'    => $productCategoryList
			);
			return response()->json($data); 
		}

		 else if($req->productGroupId && $req->productCategoryId==''){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
			$productCategoryList    = \ DB::table('fams_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

			$data = array(
			'productSubCategoryList' => $productSubCategoryList,
			'productCategoryList'    => $productCategoryList
			
			);
			return response()->json($data);
		}

		else if($req->productCategoryId && $req->productGroupId==''){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->where('productCategoryId',$req->productCategoryId)->pluck('name', 'id');
			//$productCategoryList    = \ DB::table('fams_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

			$data = array(
			'productSubCategoryList' => $productSubCategoryList
			//'productCategoryList'    => $productCategoryList
			
			);
			return response()->json($data);
		}

		else if($req->productCategoryId && $req->productCategoryId){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->where('productGroupId',$req->productGroupId)
																			  ->pluck('name', 'id');
			$productCategoryList    = \ DB::table('fams_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
			$data = array(
			'productSubCategoryList' => $productSubCategoryList,
			'productCategoryList'    => $productCategoryList
			
			); 
			return response()->json($data);
		}

	}

	public function famsEmpReqOnCngCtg(Request $req){

		if($req->productGroupId=='' && $req->productCategoryId==''){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->pluck('name', 'id');
			$productCategoryList    = \ DB::table('fams_product_category')->pluck('name', 'id');
			$data = array(
			'productSubCategoryList' => $productSubCategoryList,
			'productCategoryList'    => $productCategoryList
			);
			return response()->json($data); 
		}

		 else if($req->productGroupId && $req->productCategoryId==''){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
			$productCategoryList    = \ DB::table('fams_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

			$data = array(
			'productSubCategoryList' => $productSubCategoryList,
			'productCategoryList'    => $productCategoryList
			
			);
			return response()->json($data);
		}

		else if($req->productCategoryId && $req->productGroupId==''){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->where('productCategoryId',$req->productCategoryId)->pluck('name', 'id');
			//$productCategoryList    = \ DB::table('fams_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');

			$data = array(
			'productSubCategoryList' => $productSubCategoryList
			//'productCategoryList'    => $productCategoryList
			
			);
			return response()->json($data);
		}

		else if($req->productCategoryId && $req->productCategoryId){
			$productSubCategoryList = \ DB::table('fams_product_sub_category')->where('productGroupId',$req->productGroupId)
																			  ->where('productCategoryId',$req->productCategoryId)
																			  ->pluck('name', 'id');
			//$productCategoryList    = \ DB::table('fams_product_category')->where('productGroupId',$req->productGroupId)->pluck('name', 'id');
			$data = array(
			'productSubCategoryList' => $productSubCategoryList
			//'productCategoryList'    => $productCategoryList
			
			); 
			return response()->json($data);
		}

	}


}
