<?php
namespace App\Http\Controllers\pos\transaction\sales;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosSales;
use App\pos\PosCollection;
use App\pos\PosSalesDetails;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PosSalesController extends Controller
{
    public function index(){

	    $user = Auth::user();
		Session::put('branchId', $user->branchId);
		$gnrBranchId = Session::get('branchId');
		$logedUserName = $user->name;
		$posSales = PosSales::all();
			
		return view('pos/transaction/sales/viewSales',['posSales'=>$posSales]);
    }

    public function addPosSalesRequiF(){
    	$maxSales = PosSales::max('id')+1;
        $maxSalesNumber = 'SB'.str_pad($maxSales,6,'0',STR_PAD_LEFT);

    	return view('pos/transaction/sales/addSales',['maxSalesNumber'=>$maxSalesNumber]);
    }

    public function addItem(Request $req){
    	$rules = array(
                'paymentType' => 'required'
                
              );
 	    $attributeNames = array(
            'paymentType' => 'Payment Type'
              
                
          );
	    $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);
        if ($validator->fails())
        return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else{
		    $forCountAppnProId  = count($req->productId5);
		    $forCountAppnProQty = count($req->productQntty5);
		    $forCountAppnProBranch = count($req->optBranchName);
		  
		    if($forCountAppnProId<1){return response()->json('false'); return false;}
		  
		  	//$branchCode = DB::table('gnr_branch')->where('id', Auth::user()->branchId)->value('branchCode');
		  	$user = Auth::user()->emp_id_fk;
		  	$branchId = Auth::user()->branchId;
		  	
            $posSales = new PosSales;
            $posSales->salesBillNo          = substr($req->billNo,2);
            $posSales->branchId             = $branchId;
            $posSales->employeeId           = $user;
            $posSales->salesType            = 1;
            $posSales->companyId            = $req->clientCompanyId;
            $posSales->salesPerson          = json_encode($req->salesPerson);
            $posSales->salesDate            = $req->salesDate;
            $posSales->totalSalesQuantity   = $req->totalQuantity;
            $posSales->totalSalesAmount     = $req->totalAmount;
            $posSales->discountRate         = $req->discountPercent;
            $posSales->salesDiscount        = $req->discount;
            $posSales->tcAfterDiscount      = $req->amountAfterDiscount;
            $posSales->vatRate              = $req->vatPercent;
            $posSales->salesVat             = $req->vat;
            $posSales->salesPayAmount       = $req->payAmount;
            $posSales->salesDue             = $req->due;
            $posSales->totalSaleGrossAmount = $req->grossTotal;
            
           	$posSales->paymentType          = $req->paymentType;
            
            
            $posSales->bankName             = $req->bankName;
            $posSales->checkNo              = $req->checkNo;
            $posSales->bankDate             = $req->bankDate;
            $posSales->createdDate          = Carbon::now();
            $posSales->save();
            $collectionInstNo = DB::table("pos_collection")->where('salesId',$posSales->id)->value('installmentNo');
            if($collectionInstNo==null){
            	$installmentNo = 1;
            } else {
            	$installmentNo = $collectionInstNo+1;
            }
            $maxCollectionBillNo = DB::table('pos_collection')->max('id');
            if($maxCollectionBillNo==null) {
            	$maxCollectionBillNo = 1;
            } else {
            	$maxCollectionBillNo = $maxCollectionBillNo+1;
            }
            $PosCollection = new PosCollection;
            $PosCollection->salesId          	 = $posSales->id;
            $PosCollection->salesBillNo          = substr($req->billNo,2);
            $PosCollection->collectionBillNo     = $maxCollectionBillNo;
            $PosCollection->salesType          	 = 1;
            $PosCollection->branchId          	 = $posSales->branchId;
            $PosCollection->clientCompanyId      = $req->clientCompanyId;
            $PosCollection->totalSalesQuantity   = $req->totalQuantity;
            $PosCollection->totalSalesAmount     = $req->grossTotal;
            $PosCollection->salesPayAmount     	 = $req->payAmount;
            $PosCollection->salesDue     	 	 = $req->due;
            $PosCollection->salesDate     	 	 = $req->salesDate;
            $PosCollection->collectionDate     	 = $req->salesDate;
            $PosCollection->installmentNo     	 = $installmentNo;
            $PosCollection->paymentType 		 = $req->paymentType;
            $PosCollection->bankName 			 = $req->bankName;
            $PosCollection->checkNo 			 = $req->checkNo;
            $PosCollection->bankDate 			 = $req->bankDate;
            $PosCollection->createdDate          = Carbon::now();
            $PosCollection->save();
    		
		$salesDetails = new PosSalesDetails;
			 
	  	$dataSet = [];
	  	for ($i=0; $i < $forCountAppnProId; $i++){
		  $dataSet[]= array(
			  'salesId'		         => $posSales->id,
              'productId'            => $req->productId5[$i],
              'branchId'             => $req->productId6[$i],
			  'salesProductQuantity' => $req->productQntty5[$i],
			  'unitPrice' 		     => $req->productTotalPriceApnTable[$i],
			  'totalAmount' 	     => $req->productPriceApnTable[$i],
			  'createdDate'		     => $posSales->createdDate,
			  'salesDate'            => $posSales->salesDate, 
			  'salesType'            => 1, 
		  );
		}
		DB::table('pos_sales_details')->insert($dataSet);
  	}
  		//self::salesInvoicePrint($posSales->id);
    	$data = array(
    		'salesId'=>$posSales->id
    	);
    	return response()->json($data);
    	//self::salesInvoicePrint($posSales->id)

}

	public function editdata(Request $req){
	    $salesTables =  PosSales::where('id',$req->id)->first();

	    $servicePersonStr =  str_replace(array('"', '[', ']'),'', $salesTables->salesPerson);
	    $servicePersonArr = array_map('intval', explode(',', $servicePersonStr));

	  	$data=array(
	  		'salesTables' => $salesTables,
	  		'servicePersonArr' => $servicePersonArr,
	  	);

	    return response()->json($data);
	}

    public function editAppendRows(Request $req){

		$salesDetailsTables =  PosSalesDetails::where('salesId',$req->id)->get();
		$salesTables =  PosSales::where('id',$req->id)->first();
		$servicePersonStr =  str_replace(array('"', '[', ']'),'', $salesTables->salesPerson);
        $servicePersonArr = array_map('intval', explode(',', $servicePersonStr));
		
		foreach($salesDetailsTables as $salesDetailsTable){
            $supplierId[] = $salesDetailsTable->productId;
            $branchId[]   = $salesDetailsTable->branchId;
            $quantity[]   = $salesDetailsTable->salesProductQuantity;
         }

        $fromProductTable = DB::table('pos_product')->select('name','id')->get();
        $productId = $fromProductTable->whereIn('id',$supplierId);

		$data = array(
			'salesDetailsTables'	 => $salesDetailsTables,
			'productId'	    		 => $productId,
			'salesTables'            => $salesTables,
	  		'servicePersonArr'       => $servicePersonArr,
		);
		return response()->json($data);
		
	}

// Edit function of purchase
	public function editSalesItem(Request $req){
		$rules = array(
	               
	        );
	 	$attributeNames = array(
	             
	        );
		$validator = Validator::make ( Input::all (), $rules);
	    $validator->setAttributeNames($attributeNames);
	    if ($validator->fails())
	    return response::json(array('errors' => $validator->getMessageBag()->toArray()));
	    else{
			$idCount = count($req->productId5);
			if($idCount>0){
				
		  		$rowFectcs = PosSalesDetails::select('id')->where('salesId',$req->id)->get();
		  		
		  		foreach($rowFectcs as $rowFectc){
				 	PosSalesDetails::find($rowFectc->id)->delete();
			 	}

		        $productDetails = new PosSalesDetails;
				 
			  	$dataSet = [];
			  	for ($i=0; $i < $idCount; $i++){

			  	    $updateDate = '';
				    $dataSet[]= array(
					  	'salesId'		         => $req->id,
	                  	'productId'            => $req->productId5[$i],
	                  	'branchId'             => $req->productId6[$i],
				      	'salesProductQuantity' => $req->productQntty5[$i],
				      	'salesDate'            => $req->salesDate,
				      	'unitPrice' 		     => $req->productTotalPriceApnTable[$i],
				      	'totalAmount' 	     => $req->productPriceApnTable[$i],
				      	'salesType' 	         => 1,
				      	'createdDate'          => Carbon::now(),

				    );
				}
			    DB::table('pos_sales_details')->insert($dataSet);

			  	$updateSalesTable = PosSales::find($req->id);
		      	$updateSalesTable->salesBillNo           = $req->billNo;
		      	$updateSalesTable->companyId             = $req->CompanyName;
		        //$updateSalesTable->employeeId            = $req->CompanyName;
		      	$updateSalesTable->salesPerson           = json_encode($req->salesPerson);
		      	$updateSalesTable->salesDate             = $req->salesDate;
		      	$updateSalesTable->totalSalesQuantity    = $req->totalQuantity;
		      	$updateSalesTable->totalSalesAmount      = $req->totalAmount;
		      	$updateSalesTable->discountRate          = $req->discountPercent;
		      	$updateSalesTable->salesDiscount         = $req->discount;
		      	$updateSalesTable->tcAfterDiscount       = $req->amountAfterDiscount;
		      	$updateSalesTable->vatRate               = $req->vatPercent;
		      	$updateSalesTable->salesVat              = $req->vat;
		      	$updateSalesTable->totalSaleGrossAmount  = $req->grossTotal;
		      	$updateSalesTable->salesPayAmount        = $req->payAmount;
		      	$updateSalesTable->salesDue              = $req->due;
		      	if($req->paymentType == null){
		      		$updateSalesTable->paymentType       	= 0;
		      	}else{
		      		$updateSalesTable->paymentType       = $req->paymentType;
		      	}
		      
		      	$updateSalesTable->bankName              = $req->bankName;
		      	$updateSalesTable->checkNo               = $req->checkNo;
		      	$updateSalesTable->bankDate              = $req->bankDate;
		      	$updateSalesTable->save();

		      	$updateCollectionTable = PosCollection::where('salesId',$req->id)->first();
		      	$updateCollectionTable->salesId = $updateSalesTable->id;
		      	$updateCollectionTable->salesBillNo=$updateSalesTable->salesBillNo;
		      	$updateCollectionTable->salesType = $updateSalesTable->salesType;
		      	$updateCollectionTable->clientCompanyId  = $updateSalesTable->companyId;
		      	$updateCollectionTable->totalSalesQuantity= $updateSalesTable->totalSalesQuantity;
		      	$updateCollectionTable->totalSalesAmount = $updateSalesTable->totalSalesAmount;
		      	$updateCollectionTable->salesPayAmount = $updateSalesTable->salesPayAmount;
		      	$updateCollectionTable->salesDue = $updateSalesTable->salesDue;
		      	$updateCollectionTable->salesDate = $updateSalesTable->salesDate;
		      	$updateCollectionTable->createdDate = $updateSalesTable->createdDate;
		      	$updateCollectionTable->save();

		      	$updateDatas = PosSales::where('id', $req->id)->get();
		      
		        $data = array(
		      		'updateDatas'		=> $updateDatas,
		      		'slno'				=> $req->slno
		      	);
			    return response()->json($data);
			}
		}
	}
//end edit function of purchase

// Purchase Details
	public function posSalesDetails(Request $req){

		$salesDetails =  PosSales::where('id',$req->id)->get();
		foreach ($salesDetails as $salesDetail) {
			$branchName = DB::table('gnr_branch')->select('name')->where('branchCode',$salesDetail->branchId)->first();
			$companyName = DB::table('pos_client')->select('clientCompanyName')->where('id',$salesDetail->companyId)->first();
			$salesDetailsTable =  PosSalesDetails::where('salesId',$salesDetail->id)->get();
			$date = $salesDetail->salesDate;
			$createdDate = $salesDetail->createdDate;

			$employeeName = DB::table('hr_emp_general_info')->select('emp_id','emp_name_english')->where('id',$salesDetail->createdBy)->first();

			$employeeDeg = DB::table('hr_emp_org_info')->where('emp_id_fk',$salesDetail->createdBy)->value('position_id_fk');

			$employeeDege = DB::table('hr_settings_position')->where('id',$employeeDeg)->value('name');


            $salesPersonStr =  str_replace(array('"', '[', ']'),'', $salesDetail->salesPerson);
            $salesPersonArr = array_map('intval', explode(',', $salesPersonStr));
          
            
            foreach ($salesPersonArr as $key => $salesPersonId) {
                $temp = DB::table('hr_emp_general_info')->where('id',$salesPersonId)->value('emp_name_english');
                if ($key==0) {
                    $personName=$temp;
                }else{
                    $personName=$personName.', '.$temp;
                }
            }

			$posSalesDetails =  PosSalesDetails::where('salesId',$salesDetail->id)->get();

			$productName = array();
			foreach ($posSalesDetails as $posSalesDetail) {
			    $tempProductName= DB::table('pos_product')->where('id',$posSalesDetail->productId)->value('name');
			    $branchId = $posSalesDetail->branchId;
	            array_push($productName,$tempProductName);

			}
		}

		$dateFromarte = date('d-m-Y', strtotime($date));
		$dateFromartecreate = date('d-m-Y', strtotime($createdDate));
		
	    $data = array(
			'salesDetails'		      => $salesDetails,
			'branchName'	          => $branchName,
			'salesDetailsTable'	      => $salesDetailsTable,
			'productName'		      => $productName,
			'companyName'             => $companyName,
			'personName'              => $personName,
			'dateFromarte'            => $dateFromarte,
			'branchId'                => $branchId,
			'dateFromartecreate'      => $dateFromartecreate,
			'employeeName'            => $employeeName,
			'employeeDege'            => $employeeDege
	    );
		return response()->json($data);
	}
//end pruchase details

//delete purchase Itme
    public function deleteItem(Request $req) {

		$idCount = $req->id;
		$count = PosSalesDetails::where(['salesId' => $idCount])->count();
		$rowFectcs = PosSalesDetails::select('id')->where('salesId',$idCount)->get();
		$rowCountFectcs = PosCollection::select('id')->where('salesId',$idCount)->get();
		$countColl = PosCollection::where(['salesId' => $idCount])->count();
		PosSales::find($req->id)->delete();
		foreach($rowFectcs as $rowFectc){
			PosSalesDetails::find($rowFectc->id)->delete();
		}
		foreach($rowCountFectcs as $rowCountFectc){
			PosCollection::find($rowCountFectc->id)->delete();
		}
        return response()->json($count);
    }

	public function changeProduct (Request $request) {
	     
	    $clientCompanyId = (int)json_decode($request->clientCompanyId);

	    if($request->clientCompanyId=='') {
	        $productName =  DB::table('pos_product')->select('name')->get();
	    }
	    else {
	        $productId =DB::table('pos_product_assaign')
	                    ->where('clientcompanyId',$request->clientCompanyId)
	                    ->value('productId');

	        $productName =DB::table('pos_product')
	                    ->where('id',$productId)->select('name','id')
	                    ->get();

	        $salesPersonIds =DB::table('pos_product_assaign')
	                             ->where('clientcompanyId',$request->clientCompanyId)
	                             ->value('salesPerson');
	                
	        if($request->clientCompanyId!=null){
	            $salesPersonStr =  str_replace(array('"', '[', ']'),'', $salesPersonIds);
	            $salesPersonArr = array_map('intval', explode(',', $salesPersonStr));
	         }
	    }

	    $data = array(            
	        'productName'        => $productName,
	        'salesPersonArr'     => $salesPersonArr,
	     );

	      return response()->json($data);
	}

    public function posChangeBranch (Request $request){
        $branchId = (int)json_decode($request->branchId);

        if($request->branchId==1) {
            $productPrice =DB::table('pos_product_assaign')
             			->where('productId',$request->productId)
                        ->value('salesPriceHo');
        }
        elseif($request->branchId==2) {
            $productPrice =DB::table('pos_product_assaign')
                        ->where('productId',$request->productId)
                        ->value('salesPriceBo');
        }
        else{
        	$productPrice='';
        }
         $data = array(            
            'productPrice'         => $productPrice,
          );
        return response()->json($data);
    }

    public function productPrice (Request $request){
        $branchId = (int)json_decode($request->branchId);
        if($request->branchId==1) {
            $productPrice =DB::table('pos_product_assaign')
             			    ->where('productId',$request->productId)
                            ->value('serviceChargeHo');
        }
        elseif($request->branchId==2) {
            $productPrice =DB::table('pos_product_assaign')
                        ->where('productId',$request->productId)
                        ->value('serviceChargeBo');
        }
        else{
        	$productPrice='';
        }
        $data = array(            
            'productPrice'         => $productPrice,           
        );
        return response()->json($data);
    }

	public function salesInvoicePrint ($id){
        $empoyeeId=Auth::user()->emp_id_fk;
        $employeeName = DB::table('hr_emp_general_info')->where('id',$empoyeeId)->value('emp_name_english');
        $employeePositionName = DB::table('hr_settings_position')->where('id',DB::table('hr_emp_org_info')->where('emp_id_fk',$empoyeeId)->value('position_id_fk'))->value('name');
		$salesId = $id;
        $salesDate = DB::table('pos_sales')->where('id',$salesId)->value('salesDate');
        $salesBillNo = DB::table('pos_sales')->where('id',$salesId)->value('salesBillNo');
        $salesCompanyId = DB::table('pos_sales')->where('id',$salesId)->value('companyId');
        $clientInformationReceive = DB::table('pos_client')->where('id',$salesCompanyId)->first();
        $salesYear = Carbon::parse($salesDate)->format('Y');
        $salesMonth = Carbon::parse($salesDate)->format('m');
		$receiveAllDataBySalesIds = DB::table('pos_sales_details')->where('salesId',$salesId)->get();
		$productIdFirst = $receiveAllDataBySalesIds['0']->productId;
		$productName    = DB::table('pos_product')->where('id',$productIdFirst)->value('name');
        $ownerCompanyName = DB::table('gnr_company')->value('name');


		return view('pos/transaction/sales/salesPrintInvoice',['productName'=>$productName,'productIdFirst'=>$productIdFirst,'salesYear'=>$salesYear,'receiveAllDataBySalesIds'=>$receiveAllDataBySalesIds,'salesMonth'=>$salesMonth,'salesBillNo'=>$salesBillNo,'clientInformationReceive'=>$clientInformationReceive,'ownerCompanyName'=>$ownerCompanyName,'employeeName'=>$employeeName,'employeePositionName'=>$employeePositionName]);
	}

}