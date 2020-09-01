<?php
namespace App\Http\Controllers\pos\transaction\payments;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosSales;
use App\pos\PosCollection;
use App\pos\PosSalesDetails;
use App\pos\PosPayments;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class PosPaymentsController extends Controller
{
	public function index()
	{
		$posPayments = PosPayments::where('companyId', Auth::user()->company_id_fk)->get();
		return view('pos/transaction/payment/viewPayment',['posPayments'=>$posPayments]);
    }

	public function addPayment()
	{
		$ledgerHeads = DB::table('acc_account_ledger as t1')->where('t1.code', '14000')
									->where('t1.companyIdFk', Auth::user()->company_id_fk)
									->join('acc_account_ledger AS t2', 't1.id', '=', 't2.parentId')
									->where('t1.id', '<>', 't2.id')->get();

		return view('pos/transaction/payment/addPayment', compact('ledgerHeads'));
    }

	public function addPaymentItem(Request $req)
	{
    	$rules = array(
			// 'name'                  => 'required|unique:pos_payment,name',
			// 'code'                  => 'required|unique:pos_payment,code',
			'name' =>[
	            'required',
	             Rule::unique('pos_payment')->where('companyId', Auth::user()->company_id_fk),
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_payment')->where('companyId', Auth::user()->company_id_fk),
  			],
			'description'			=> 'required',
			'ledgerHeadId'			=> 'required|not_in:0'
			

		);
		$attributeNames = array(
			'name'                  => 'Name',
			'code'                  => 'Code',
			'description'           => 'Description',
			'ledgerHeadId'			=> 'Ledger Head'
			
		);

		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		
		else {

			$posPayment                 = new PosPayments();

			$posPayment->companyId = Auth::user()->company_id_fk;

			$posPayment->name           = $req->name;
			$posPayment->code           = $req->code;
			$posPayment->description    = $req->description;
			$posPayment->ledgerHeadId 	= $req->ledgerHeadId;
			$posPayment->createdDate    = Carbon::now();
			//dd($PosPayment);
			$posPayment->save(); 

			return response()->json('success');    
    	
   		}

    }

	public function paymentDetailItem(Request $req)
	{
		$paymentId =  PosPayments::with('ledgerHead')->where('id',$req->id)->first();
		$paymentName        	= $paymentId->name;
		$paymentCode       	    = $paymentId->code;
		$paymentDescription     = $paymentId->description;
		$paymentLedgerHead      = $paymentId->ledgerHead->name;

		$data = array(
			'paymentName'                      => $paymentName,
			'paymentCode'                      => $paymentCode,
			'paymentDescription'               => $paymentDescription,
			'paymentLedgerHead'				   => $paymentLedgerHead 

		);

		return response()->json($data);
	}

	public function getPaymentInfo(Request $req){
		$payment = PosPayments::with('ledgerHead')->find($req->id);

		$ledgerHeads = DB::table('acc_account_ledger as t1')->where('t1.name', 'Cash & Bank')
							->where('t1.companyIdFk', Auth::user()->company_id_fk)
							->join('acc_account_ledger AS t2', 't1.id', '=', 't2.parentId')
							->where('t1.id', '<>', 't2.id')->get();

		$data = array(
			'payment'             => $payment,
			'ledgerHeads'		  => $ledgerHeads
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
	public function editPaymentItem(Request $req){
		// dd($req->all());
		// return response()->json($req->all());    
		$rules = array(
			// 'name'                  => 'required',
			// 'code'                  => 'required',
			'name' =>[
	            'required',
	             Rule::unique('pos_payment')->where('companyId', Auth::user()->company_id_fk).$req->id,
  			],
  			'code' =>[
	            'required',
	             Rule::unique('pos_payment')->where('companyId', Auth::user()->company_id_fk).$req->id,
  			],
			'description'			=> 'required',
			'ledgerHeadId'			=> 'required|not_in:0'
			

		);
		$attributeNames = array(
			'name'                  => 'Name',
			'code'                  => 'Code',
			'description'           => 'Description',
			'ledgerHeadId'			=> 'Ledger Head'
			
		);

		$validator = Validator::make ( Input::all (), $rules);
		$validator->setAttributeNames($attributeNames);
		if ($validator->fails())
		return response::json(array('errors' => $validator->getMessageBag()->toArray()));
		else {

			$posPayment                 = PosPayments::find($req->id);
			$posPayment->name           = $req->name;
			$posPayment->code           = $req->code;
			$posPayment->description    = $req->description;
			$posPayment->ledgerHeadId 	= $req->ledgerHeadId;
			$posPayment->save();
			    

			}
		return response()->json('success');    
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
    public function deletePaymentItem(Request $req) {
		PosPayments::find($req->id)->delete();
        return response()->json();
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