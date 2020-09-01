<?php

namespace App\Http\Controllers\inventory\transaction\purchase;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\inventory\InvPurchase;
use App\inventory\InvPurchaseDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class InvPurchaseController extends Controller
{
    public function index(){

$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
$logedUserName = $user->name;

  if($gnrBranchId==1 || $logedUserName=='Head Office'){
    		$purchases = InvPurchase::all();
    	}else{
    		$purchases = InvPurchase::where('branchId',$gnrBranchId)->get();
    	}
    	return view('inventory/transaction/purchase/viewPurchase',['purchases'=>$purchases]);
    }

    public function addInvPurchaseRequiF(){
    	 return view('inventory/transaction/purchase/addPurchase');
    }

    public function addItem(Request $req){
    	$rules = array(
                'supplierId' 	 => 'required'
                
              );
 	  $attributeNames = array(
            
                'supplierId' 	 => 'Supplier Name.'
                
          );
	  $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
		    $forCountAppnProId  = count($req->productId5);
		    $forCountAppnProQty = count($req->productQntty5);
		  
		  if($forCountAppnProId<1){return response()->json('false'); return false;}
		  	$maxId = DB::table('inv_purchase')->max('id')+1;
		  	$branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
		  	$user = Auth::user()->emp_id_fk;

		 


		  	$billNoDupAvoid = 'PR.'.sprintf('%04d.', $branchCode). sprintf('%06d', $maxId);	
		  	$now = Carbon::now();
		  	$req->request->add(['createdDate' => $now, 'billNo' => $billNoDupAvoid,'createdBy'=> $user]);	
            $create = InvPurchase::create($req->all());
    		
		    $purchaseDetails = new InvPurchaseDetails;
			 
	  	$dataSet = [];
	  	for ($i=0; $i < $forCountAppnProId; $i++){
		  $dataSet[]= array(
			  'purchaseId'		=> $create->id,
			  'billNo'			=> $req->billNo,
              'productId'       => $req->productId5[$i],
			  'quantity' 		=> $req->productQntty5[$i],
			  'price' 		    => $req->productTotalPriceApnTable[$i],
			  'totalPrice' 	    => $req->productPriceApnTable[$i],
			  'createdDate'		=> $create->createdDate
		  );
		}
		DB::table('inv_purchase_details')->insert($dataSet);
  	}
    	return response()->json('Success');
}

public function editAppendRows(Request $req){
		$purchaseDetailsTables =  InvPurchaseDetails::where('purchaseId',$req->id)->get();

		
		 
		foreach($purchaseDetailsTables as $purchaseDetailsTable){
            $supplierId[] = $purchaseDetailsTable->productId;
          }
        
          $fromProductTable = DB::table('inv_product')->select('name','id')->get();
          $productId = $fromProductTable->whereIn('id',$supplierId);
          
		$data = array(
		'purchaseDetailsTables'	=> $purchaseDetailsTables,
		'productId'	    		=> $productId
		);
		return response()->json($data);
		
	}

// Edit function of purchase
public function editPurchaseItem(Request $req){
		$rules = array(
                
                'supplierId' 	 => 'required',
                'totalQuantity'  => 'required',
                'totalAmount' 	 => 'required',
                'payAmount' 	 => 'required'
              );
 	  $attributeNames = array(
              
                'supplierId' 	 => 'Supplier Name.',
                'totalQuantity'  => 'Total Quantity',
                'totalAmount' 	 => 'Total Amount.',
                'payAmount' 	 => 'Pay Amount.'
          );
	  $validator = Validator::make ( Input::all (), $rules);
      $validator->setAttributeNames($attributeNames);
      if ($validator->fails())
      return response::json(array('errors' => $validator->getMessageBag()->toArray()));
      else{
		$idCount = count($req->productId5);
		if($idCount>0){
			
	  		$rowFectcs = InvPurchaseDetails::select('id')->where('purchaseId',$req->id)->get();
	  		foreach($rowFectcs as $rowFectc){
			 InvPurchaseDetails::find($rowFectc->id)->delete();
		 	}

			$productDetails = new InvPurchaseDetails;
			 
		  	$dataSet = [];
		  	for ($i=0; $i < $idCount; $i++){
		  		
		  		$updateDate = '';
			  $dataSet[]= array(
				  'purchaseId'		=> $req->id,
				  'billNo'			=> $req->billNo,
	        	  'productId'       => $req->productId5[$i],
				  'quantity' 		=> $req->productQntty5[$i],
				  'price' 		    => $req->productTotalPriceApnTable[$i],
				  'totalPrice' 	    => $req->productPriceApnTable[$i],
				  'createdDate'		=> $req->createdDate
				  );
			}
			DB::table('inv_purchase_details')->insert($dataSet);

		  $updatePurchaseTable = InvPurchase::find ($req->id);
	      $updatePurchaseTable->billNo = $req->billNo;
	      $updatePurchaseTable->orderNo = $req->orderNo;
	      $updatePurchaseTable->orderDate = $req->orderDate;
	      $updatePurchaseTable->chalanNo = $req->chalanNo;
	      $updatePurchaseTable->chalanDate = $req->chalanDate;
	      $updatePurchaseTable->projectId = $req->projectId;
	      $updatePurchaseTable->projectTypeId = $req->projectTypeId;
	      $updatePurchaseTable->supplierId = $req->supplierId;
	      $updatePurchaseTable->contactPerson = $req->contactPerson;
	      $updatePurchaseTable->purchaseDate = $req->purchaseDate;
	      $updatePurchaseTable->totalQuantity = $req->totalQuantity;
	      $updatePurchaseTable->totalAmount = $req->totalAmount;
	      $updatePurchaseTable->discountPercent = $req->discountPercent;
	      $updatePurchaseTable->discount = $req->discount;
	      $updatePurchaseTable->amountAfterDiscount = $req->amountAfterDiscount;
	      $updatePurchaseTable->vatPercent = $req->vatPercent;
	      $updatePurchaseTable->vat = $req->vat;
	      $updatePurchaseTable->grossTotal = $req->grossTotal;
	      $updatePurchaseTable->payAmount = $req->payAmount;
	      $updatePurchaseTable->due = $req->due;
	      $updatePurchaseTable->paymentStatus = $req->paymentStatus;
	      $updatePurchaseTable->remark = $req->remark;
	      $updatePurchaseTable->save();

	      $updateDatas = InvPurchase::where('id', $req->id)->get();
	      foreach($updateDatas as $updateData){
	      $branchName = DB::table('gnr_branch')->select('name')->where('id',$updateData->branchId)->first();
	      $supplierName = DB::table('gnr_supplier')->select('supplierCompanyName')->where('id',$updateData->supplierId)->first();
	      $projectName = DB::table('gnr_project')->select('name')->where('id',$updateData->projectId)->first();
	      $projectTypeName = DB::table('gnr_project_type')->select('name')->where('id',$updateData->projectTypeId)->first();
	      $dateFromarte = date('d-m-Y', strtotime($updateData->createdDate));

	  		}
	      $data = array(
	      		'updateDatas'		=> $updateDatas,
	      		'branchName'		=> $branchName,
	      		'supplierName'		=> $supplierName,
	      		'dateFromarte'		=> $dateFromarte,
	      		'projectName'       => $projectName,
	      		'projectTypeName'   => $projectTypeName,
	      		'slno'				=> $req->slno
	      	);
		return response()->json($data);
		}
		}
	}
//end edit function of purchase

// Purchase Details
	public function purchaseDetails(Request $req){

		$prchDetails =  InvPurchase::where('id',$req->id)->get();
		foreach ($prchDetails as $prchDetail) {
				$branchName = DB::table('gnr_branch')->select('name')->where('id',$prchDetail->branchId)->first();
				$purDetailsTables =  InvPurchaseDetails::where('purchaseId',$prchDetail->id)->get();
				$date = $prchDetail->createdDate;

				$employeeName = DB::table('hr_emp_general_info')->select('emp_id','emp_name_english')->where('id',$prchDetail->createdBy)->first();

			    $employeeDeg = DB::table('hr_emp_org_info')->where('emp_id_fk',$prchDetail->createdBy)->value('position_id_fk');

			    $employeeDege = DB::table('hr_settings_position')->where('id',$employeeDeg)->value('name');

				


				$supplierName = DB::table('gnr_supplier')->select('supplierCompanyName')->where('id',$prchDetail->supplierId)->first();
		        $supplierAddress = DB::table('gnr_supplier')->select('address')->where('id',$prchDetail->supplierId)->first();

				$supplierMobile = DB::table('gnr_supplier')->select('phone')->where('id',$prchDetail->supplierId)->first();

				$projectName = DB::table('gnr_project')->select('name')->where('id',$prchDetail->projectId)->first();
				
				$projectTypeName = DB::table('gnr_project_type')->where('id',$prchDetail->projectTypeId)->value('name');
			
				$InvPurDetails =  InvPurchaseDetails::where('purchaseId',$prchDetail->id)->get();

				$productName = array();
				foreach ($InvPurDetails as $InvPurDetail) {
				     $tempProductName= DB::table('inv_product')->where('id',$InvPurDetail->productId)->value('name');

				   array_push($productName,$tempProductName);
			    }
	    }
      
	    $orderDate = $prchDetail->orderDate;
		$chalanDate = $prchDetail->chalanDate;
		$dateFromarte = date('d-m-Y', strtotime($date));
		$orderdate= date('d-m-Y', strtotime($orderDate));
		$chalanDate = date('d-m-Y', strtotime($chalanDate));

	 $data = array(
		'prchDetails'		      => $prchDetails,
		'branchName'	          => $branchName,
		'purDetailsTables'	      => $purDetailsTables,
		'productName'		      => $productName,
		'supplierName'		      => $supplierName,
		'createdDate'		      => $dateFromarte,
		'projectName'             => $projectName,
		'projectTypeName'         => $projectTypeName,
		'supplierAddress'         => $supplierAddress,
		'supplierMobile'          => $supplierMobile,
		'orderDate'               => $orderdate,
		'chalanDate'              => $chalanDate,
		'employeeName'            => $employeeName,
	    'employeeDege'            => $employeeDege,
	);
		return response()->json($data);
	}
//end pruchase details

//delete purchase Itme
     public function deleteItem(Request $req) {
		 $idCount = $req->id;
		 $count = InvPurchaseDetails::where(['purchaseId' => $idCount])->count();
		 $rowFectcs = InvPurchaseDetails::select('id')->where('purchaseId',$idCount)->get();
		 
		 InvPurchase::find($req->id)->delete();
		 foreach($rowFectcs as $rowFectc){
			 InvPurchaseDetails::find($rowFectc->id)->delete();
		 }
      return response()->json($count);
    }
//end purchase delete

 // send to supplier 
 	public function sendToSuppler(){
    	return view('gnr.tools.supplier.addSupplier')->with('name','fromPurchaseF');
    }
 // end sent to supplier  

	public function productAccSuppId(Request $req){
    	$productName 	=  DB::table('inv_product')->where('supplierId',$req->supplierId)->pluck('name', 'id');
      return response()->json($productName);
    }

// purchase filtering start
    public function invPurOnCngSupl(Request $req){
    	 
    	if($req->supplierId){
			$productName 		=  DB::table('inv_product')->where('supplierId',$req->supplierId)->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->where('supplierId',$req->supplierId)->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('supplierId',$req->supplierId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('supplierId',$req->supplierId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('supplierId',$req->supplierId)->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$contactPerson = DB::table('gnr_supplier')->select('name')->where('id',$req->supplierId)->first();

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName,
			'contactPerson'			 => $contactPerson

			);
			return response()->json($data); 
		}else{

			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => ''/*$productName*/,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName,
			'contactPerson'			 => ''

			);
			return response()->json($data); 
		}
    }

//group id change
    public function invPurchaseOnCngGrp(Request $req){


    	
    	if($req->supplierId && $req->productGroupId){
			
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)	
															->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															->where('groupId',$req->productGroupId)
															->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('groupId',$req->productGroupId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
													        ->where('groupId',$req->productGroupId)
															->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productGroupId && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('groupId',$req->productGroupId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('groupId',$req->productGroupId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('groupId',$req->productGroupId)->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}
		else if($req->supplierId && $req->productGroupId==''){

			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();

			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));
			
			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
    	}

    	else if($req->productGroupId==''  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

}

//catagory chanage function
public function invPurchaseOnCngCtg(Request $req){
    	
    	if($req->supplierId && $req->productGroupId && $req->productCategoryId){
			$productName 		=  DB::table('inv_product')/*->where('supplierId',$req->supplierId)*/
															->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)	
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															/*->where('supplierId',$req->supplierId)*/
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															/*->where('supplierId',$req->supplierId)*/
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productCategoryId && $req->supplierId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('categoryId',$req->productCategoryId)->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('categoryId',$req->productCategoryId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('categoryId',$req->productCategoryId)->get();
			
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productGroupId  && $req->productCategoryId==''){
			$productName 		=  DB::table('inv_product')/*->where('supplierId',$req->supplierId)*/
															->where('groupId',$req->productGroupId)
															->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															/*->where('supplierId',$req->supplierId)*/
															->where('groupId',$req->productGroupId)
															->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															/*->where('supplierId',$req->supplierId)*/
															->where('groupId',$req->productGroupId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															/*->where('supplierId',$req->supplierId)*/
															->where('groupId',$req->productGroupId)
															->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName' 		 	 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productCategoryId  && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')/*->where('supplierId',$req->supplierId)*/
															->where('categoryId',$req->productCategoryId)
															->pluck('name', 'id');
			$groupIds	 		=  DB::table('inv_product')->select('groupId')
															/*->where('supplierId',$req->supplierId)*/
															->where('categoryId',$req->productCategoryId)
															->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															/*->where('supplierId',$req->supplierId)*/
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															/*->where('supplierId',$req->supplierId)*/
															->where('categoryId',$req->productCategoryId)
															->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName' 		 	 => $groupName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}


		else if($req->productGroupId && $req->productCategoryId  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productCategoryId==''  && $req->productGroupId==''){
			
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productGroupId && $req->productCategoryId==''  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('groupId',$req->productGroupId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('groupId',$req->productGroupId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('groupId',$req->productGroupId)->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productGroupId=='' && $req->productCategoryId==''  && $req->supplierId==''){
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}
}

// subcatory change
public function invPurchaseOnCngSubCtg(Request $req){

	if($req->supplierId && $req->productGroupId && $req->productCategoryId && $req->productSubCategoryId){
			$productName 		=  DB::table('inv_product')
															->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->get();
			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->productCategoryId=='' && $req->supplierId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('subCategoryId',$req->productSubCategoryId)->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('subCategoryId',$req->productSubCategoryId)->get();

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->supplierId && $req->productCategoryId==''  && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('subCategoryId',$req->productSubCategoryId)
															->pluck('name', 'id');
			$groupIds	 		=  DB::table('inv_product')->select('groupId')
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));

			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName' 		 	 => $groupName,
			'catagoryName'    	 	 => $catagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productGroupId && $req->productCategoryId && $req->productSubCategoryId==''){
			$productName 		=  DB::table('inv_product')
															->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)	
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)	
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId && $req->productGroupId=='' && $req->productCategoryId=='' && $req->productSubCategoryId==''){
			
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data);
		}

		else if($req->supplierId=='' && $req->productGroupId && $req->productCategoryId && $req->productSubCategoryId){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)	
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->get();

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->supplierId=='' && $req->productGroupId=='' && $req->productCategoryId && $req->productSubCategoryId){
			$productName 		=  DB::table('inv_product')->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->pluck('name', 'id');
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)	
															->get();

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->productGroupId && $req->supplierId=='' && $req->productCategoryId==''){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)
															->where('subCategoryId',$req->productSubCategoryId)
															->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')
															->where('groupId',$req->productGroupId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    	 	 => $catagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productSubCategoryId && $req->supplierId && $req->productCategoryId && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)
															->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')
															
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();										
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('categoryId',$req->productCategoryId)
															->where('subCategoryId',$req->productSubCategoryId)
															->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
															
			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName' 		 	 => $groupName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 

		}

		else if($req->productSubCategoryId=='' && $req->supplierId=='' && $req->productCategoryId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->pluck('name', 'id');
			$groupIds 			=  DB::table('inv_product')->select('groupId')->get();
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->get();
			foreach($groupIds as $groupId){
				$groupName []	=  DB::table('inv_product_group')->select('name','id')->where('id',$groupId->groupId)->get();
			}
			$groupName = array_map("unserialize", array_unique(array_map("serialize", $groupName)));
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'groupName'    			 => $groupName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productCategoryId && $req->supplierId=='' && $req->productSubCategoryId=='' && $req->productGroupId==''){
			$productName 		=  DB::table('inv_product')->where('categoryId', $req->productCategoryId)->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('categoryId',$req->productCategoryId)
															->get();
			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data); 
		}

		else if($req->productCategoryId=='' && $req->supplierId=='' && $req->productSubCategoryId=='' && $req->productGroupId){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)->pluck('name', 'id');
			$catagoryIds 		=  DB::table('inv_product')->select('categoryId')->where('groupId',$req->productGroupId)->get();
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')->where('groupId',$req->productGroupId)->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')->where('groupId',$req->productGroupId)->get();
			foreach($catagoryIds as $catagoryId){
				$catagoryName []	=  DB::table('inv_product_category')->select('name','id')->where('id',$catagoryId->categoryId)->get();
			}
			$catagoryName = array_map("unserialize", array_unique(array_map("serialize", $catagoryName)));

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'catagoryName'    		 => $catagoryName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data);
		}

		else if($req->productCategoryId && $req->supplierId=='' && $req->productSubCategoryId=='' && $req->productGroupId){
			$productName 		=  DB::table('inv_product')->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->pluck('name', 'id');
			$subCatagoryIds 	=  DB::table('inv_product')->select('subCategoryId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();
			$productBrandids 	=  DB::table('inv_product')->select('brandId')
															->where('groupId',$req->productGroupId)
															->where('categoryId',$req->productCategoryId)
															->get();

			foreach($subCatagoryIds as $subCatagoryId){
				$SubcatagoryName []	=  DB::table('inv_product_sub_category')->select('name','id')->where('id',$subCatagoryId->subCategoryId)->get();
			}
			$SubcatagoryName = array_map("unserialize", array_unique(array_map("serialize", $SubcatagoryName)));

			foreach($productBrandids as $productBrandid){
				$brandName []	=  DB::table('inv_product_brand')->select('name','id')->where('id',$productBrandid->brandId)->get();
			}
			$brandName = array_map("unserialize", array_unique(array_map("serialize", $brandName)));

			$data = array(
			'productName' 		 	 => $productName,
			'SubcatagoryName'    	 => $SubcatagoryName,
			'brandName'    	 		 => $brandName

			);
			return response()->json($data);
		}

}

    // end purchase filtering

	

}
