<?php

namespace App\Http\Controllers\pos\transaction\order;

use Illuminate\Http\Request;

use App\Http\Requests;
// use App\fams\FamsDepreciation as FamsDep;
// use App\fams\FamsDepDetails;
use App\pos\PosSale;
use App\pos\PosProduct;
use App\pos\PosSupplier;
use App\pos\PosCustomer;
use App\pos\PosOrder;
use App\pos\PosOrderDetails;
use App\pos\PurchaseDetails;
use App\pos\PosIssueDetails;
// use App\fams\FamsAdditionalCharge;
use App\gnr\GnrBranch;
use App\Http\Controllers\gnr\Service;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\Service\TransactionCheckHelper;

class PosTransactionOrderController extends Controller
{
	public function index()
	{	
		// dd(1);
		$products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->get();
		$branches = GnrBranch::all();
		$orders = DB::table('pos_order')
            ->join('pos_customer', 'pos_order.customerId', '=', 'pos_customer.id')
			->where('pos_order.companyId', Auth::user()->company_id_fk)
            ->select('pos_order.*', 'pos_customer.name as cusName')
			->get(); 
  		
		return view('pos/transaction/order/viewOrdersList',[
			'products'=> $products,
			'orders'=> $orders,
			'branches' => $branches,
		]);
	}

    public function monthEndCheck(Request $request)
    {   
        $checkValue = new TransactionCheckHelper();
        $checkValueResult = $checkValue->monthEndCheck($request->OrderId, 'order');
        //dd($checkValueResult);
        return response()->json($checkValueResult);
    }

	public function posAddOrder()
	{	
		$productNames = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code', 'type')->get();
		$orderCustomers = DB::table('pos_customer')->where('companyId', Auth::user()->company_id_fk)->get();
		$prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');
		$payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();

		$projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
		$projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
		$branch = Auth::user()->branchId;

		$setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$hourBaseProduct = DB::table('pos_product')->where('type', 'hour')
							->where('companyId', Auth::user()->company_id_fk)->first();

		$transaction = new TransactionCheckHelper();
		$dateRange = $transaction->monthEndYearEndCheck();
					
		return view('pos/transaction/order/addOrder',
					[
					'orderCustomers'=> $orderCustomers,
					'productNames'=> $productNames,
					'prefix'=>$prefix,
					'projects' => $projects, 
                    'projectTypes' => $projectTypes, 
					'branch' => $branch,
					'payments' => $payments,
					'hourBaseProduct' => $hourBaseProduct,
					'setting' => $setting,
					'dateRange' => $dateRange]);

	}

	public function getLedger(Request $request)
    {   
        $payment = DB::table('pos_payment')->where('id', $request->paymentTypeId)
                        ->where('companyId', Auth::user()->company_id_fk)->first();

        $ledgerData = DB::table('acc_account_ledger')
                        ->where('parentId', $payment->ledgerHeadId)
                        ->where('companyIdFk', Auth::user()->company_id_fk)
                        ->get();

        return response()->json($ledgerData);
    }

	public function OrderAddProductNameOnChangeProductCode(Request $request){
		//dd($request->all());
	   $data = DB::table('pos_product')->where('id',$request->productCode)->first();
	   
	   return response()->json($data);
	}
	 public function getBillNoOnChangeCustomerId(Request $request){
	   //dd($request->all());
	   $customerCode = DB::table('pos_customer')->where('id',$request->customerId)->value('code');
	   $orders = DB::table('pos_order')->where('customerId',$request->customerId)->select('billNo')->get();
	   //dd($orders);
	 
	   if($orders->count() > 0 ){
		   	foreach ($orders as $key => $order) {
		   				$orderNoArr[] = (int)explode(".", $order->billNo)[2];	   				
		    }
	     $orderNoMax = max($orderNoArr);
	     $newOrderNo =  $orderNoMax + 1;
	   		  
	   }else{

	   	  $newOrderNo = 1;
	   }

	   $orderNoFormat = str_pad($newOrderNo, 5, '0', STR_PAD_LEFT);
	   $customerCodeFormat = str_pad($customerCode, 5, '0', STR_PAD_LEFT);
	   $orderBillNo  = 'OR.'.$customerCodeFormat.'.'.$orderNoFormat;
	   return response()->json($orderBillNo);
	}

	public function posSaveOrderItem(Request $request)
	{	
		// dd($request->all());
		$order = new PosOrder();

		$order->companyId = Auth::user()->company_id_fk;

		$order->billNo  = $request->billNo;
		$order->conPerson  = $request->conPerson;
		$order->customerId = $request->customerId;
		$order->projectId = $request->projectId;
        $order->projectTypeId = $request->projectTypeId;
        $order->branchId = $request->branchId;
        $order->remark = $request->remark;
		$order->totalAmount = $request->totalAmount;
		$order->orderDate = date("y-m-d",strtotime($request->orderDate));
		//dd($order);

		if($order->save()){

			$data = [];
			foreach (json_decode($request->quantity) as $key => $value) {
				$data1[] = $value;
				$data['quantity'] = $data1;
			}
			
			foreach (json_decode($request->productID) as $key => $value) {
				$data4[] = $value;
				$data['productID'] = $data4;
			}
			//dd($data['productID']);
			// dd($data);
			$productCount = count($data['productID']);
			$otherCostVoucherAmount = [];
			$otherCostLedgerIdArr = [];
			$rawMaterialAmount = 0;
			
			// dd($data['productID']);

			for($i=0; $i < $productCount; $i++)
			{
				$posOrderDetails = new PosOrderDetails();
				$posOrderDetails->productId = (int)$data['productID'][$i];
				$posOrderDetails->quantity  = (int)$data['quantity'][$i];
				
				$posOrderDetails->orderId   = $order->id;
				// $posOrderDetails->save();
				

			    if($posOrderDetails->save()){
					$costSheetDetails = DB::table('pos_cost_sheet')
									->where('companyId',Auth::user()->company_id_fk)
									->where('productId',$posOrderDetails->productId)
									->orderByDesc('effectDate')
									->first();
					//dd($costSheetDetails);
					
					if ($costSheetDetails) {
						$productInfos = json_decode($costSheetDetails->productInfo);

						foreach ($productInfos as $key => $productInfo) {
							$posIssueDetails = new PosIssueDetails();
							$posIssueDetails->productId = $posOrderDetails->productId;
							$posIssueDetails->orderId = $posOrderDetails->orderId;
							$posIssueDetails->orderQty = $posOrderDetails->quantity;

							$posIssueDetails->rawMaterialId = $productInfo->productId;
							$posIssueDetails->rawMaterialIdAmount = $posOrderDetails->quantity * ($productInfo->qty * $productInfo->costPrice);
							$rawMaterialAmount += $posIssueDetails->rawMaterialIdAmount;
							//dd($posIssueDetails);
							$posIssueDetails->save();
							//$amount = $posOrderDetails->quantity * ($productInfo->qty * $productInfo->costPrice);
							//dd($posIssueDetails) ;
						}

						// other cost calculation
						$otherCosts = json_decode($costSheetDetails->otherCost);

						if (count($otherCosts) > 0) {
							foreach ($otherCosts as $key => $otherCost) {

								if ($otherCost->isVoucher == true) {
									
									$otherCostLedgerId = DB::table('pos_other_cost')->where('id', $otherCost->otherCostId)->where('companyId',Auth::user()->company_id_fk)->value('ledger_id');
									$otherCostLedgerIdArr[] = $otherCostLedgerId;
									$otherCostVoucherAmount[$otherCostLedgerId] = isset($otherCostVoucherAmount[$otherCostLedgerId]) 
																				? $otherCostVoucherAmount[$otherCostLedgerId] + ($posOrderDetails->quantity * $otherCost->costAmount)
																				: $posOrderDetails->quantity * $otherCost->costAmount;
								}
							}
						}
						
					}

				}
				
			}

			// Auto JV Voucher for order Start
			$jv = DB::table('acc_voucher_type')->where('shortName', 'JV')->select('id', 'shortName')->first();
			$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();
			$existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
				->where('projectId', $request->projectId)->where('branchId', $request->branchId)
				->where('voucherTypeId', $jv->id)
				->select('voucherCode')
				->orderBy('voucherCode', 'DESC')
				->first();

			if ($existLedger) {
				$existVoucherCode = explode(".", $existLedger->voucherCode);
				$voucherCode = $existVoucherCode[0] . '.' . $existVoucherCode[1] . '.' . $existVoucherCode[2] . '.' . str_pad($existVoucherCode[3] + 1, 5, '0', STR_PAD_LEFT);
			} else {
				$branchCode = DB::table('gnr_branch')->where('id', $request->branchId)->pluck('branchCode')->first();
				$branchCode = str_pad($branchCode, 3, '0', STR_PAD_LEFT);

				$projectCode = DB::table('gnr_project')->where('id', $request->projectId)->pluck('projectCode')->first();
				$projectCode = str_pad($projectCode, 5, '0', STR_PAD_LEFT);

				$voucherCode = $jv->shortName . '.' . $branchCode . '.' . $projectCode . '.' . '00001';
			}

			$voucher = new AddVoucher();
			$voucher->companyId = Auth::user()->company_id_fk;
			$voucher->voucherTypeId = $jv->id;
			$voucher->projectId = $request->projectId;
			$voucher->projectTypeId = $request->projectTypeId;
			$voucher->voucherDate = date("y-m-d", strtotime($order->orderDate));
			$voucher->voucherCode = $voucherCode;
			$voucher->globalNarration = 'Auto Voucher From Order';
			$voucher->branchId = $request->branchId;
			$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->createdDate = Carbon::now();
			$voucher->orderId = $order->id;
			$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->save();

			// raw materials
			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = (int)$voucherConfig->finished_goods;
			$voucherDetails->creditAcc = (int)$voucherConfig->raw_materials;
			$voucherDetails->amount = $rawMaterialAmount; // need to work here fifo method
			$voucherDetails->localNarration = 'Journal Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();

			// other costs
			$otherCostLedgerIdArr = array_unique($otherCostLedgerIdArr);
			if (count($otherCostLedgerIdArr) > 0) {
				foreach ($otherCostLedgerIdArr as $key => $id) {

					if ($otherCost->isVoucher == true) {
						$voucherDetails = new AddVoucherDetails();
						$voucherDetails->voucherId = $voucher->id;
						$voucherDetails->debitAcc = (int)$voucherConfig->finished_goods;
						$voucherDetails->creditAcc = $id;
						$voucherDetails->amount = $otherCostVoucherAmount[$id];
						$voucherDetails->localNarration = 'Journal Voucher';
						$voucherDetails->createdDate = Carbon::now();
						$voucherDetails->save();
					}
				}
			}
			// // Auto JV Voucher Stop

		}
		// dd('stop');

		$data = array(
			'purchaseId'  => $order->id,
			'msg'		  => 'success'
		);
			
		return response()->json($data);

	}

	public function viewOrderItem($id)
	{
		$data = array();
        //dd($id);
        // Company Information 
        $companyDetails = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)
                                                  ->select('name','address')->first();
        $data['companyInfo'] = [
            'name'      => $companyDetails->name,
            'address'   => $companyDetails->address
        ];

       
        // Sales Information
        $order = DB::table('pos_order as t1')->where('t1.id', $id)
                                    ->where('t1.companyId', Auth::user()->company_id_fk)
                                    ->join('gnr_project as t2', 't2.id', 't1.projectId')
                                    ->join('gnr_project_type as t3', 't3.id', 't1.projectTypeId')
                                    ->join('gnr_branch as t4', 't4.id', 't1.branchId')
                                    ->select('t2.name as projectName', 't4.name as branchName',
                                    't1.billNo', 't1.orderDate')->first();
                                  
                            	//dd(PosPurchase::find($id));
        $data['transactionInfo'] = [
            'project'       => $order->projectName,
            'branch'        => $order->branchName,
            'billNo'        => $order->billNo,
            'date'          => $order->orderDate
		];
		//dd($data);
        // Sales Details Information
        $detailsInfo = DB::table('pos_order as t1')->where('t1.id', $id)
									->where('t1.companyId', Auth::user()->company_id_fk)
									->join('pos_order_details as t2', 't2.orderId', 't1.id')
									->join('pos_product as t3', 't3.id', 't2.productId')
									->select('t3.name as productName', 't3.code', 't2.quantity')
									->get();

									//dd($detailsInfo);
        $data['transactionDetails'] = array();
        foreach($detailsInfo as $key => $record)
        {
            $data['transactionDetails'][$key] = [
                'product'   => $record->productName,
                'code'      => $record->code,
                'quantity'  => $record->quantity,
              
            ];
        }
        //dd($data);
        // Sales Amount Information
        $amountInfo = DB::table('pos_order')->where('id', $id)->first();
        $data['amountInfo'] = [
            'totalAmount'                   => $amountInfo->totalAmount,
		];
		//dd($data);
		return view('pos.transaction.order.viewOrder',['data'=>$data]);
	}


	public function editOrderItem($id)
	{
		//dd($id);
		$customers = DB::table('pos_customer')->where('companyId', Auth::user()->company_id_fk)->get();
	
		$order = PosOrder::find($id);

		// $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();

		$productNames = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code', 'type')->get();

	    $orderDetails = DB::table('pos_order_details')
            ->join('pos_product', 'pos_order_details.productId', '=', 'pos_product.id')
            ->select('pos_order_details.*', 'pos_product.name','pos_product.code')
            ->where('orderId',$id)
            ->get();
		// dd($orderDetails);
		$projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
		$projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
		$branch = Auth::user()->branchId;

		// $ledgerData = DB::table('acc_account_type')
        //                 ->join('acc_account_ledger', 'acc_account_ledger.accountTypeId', 'acc_account_type.id')
        //                 ->whereIn('acc_account_type.name', ['Cash', 'Bank'])
        //                 ->where('acc_account_ledger.companyIdFk', Auth::user()->company_id_fk)
        //                 ->where('acc_account_ledger.isGroupHead', 0)
        //                 ->select('acc_account_ledger.id', 'acc_account_ledger.name', 'acc_account_type.name as ledgerType')
        //                 ->get();

        // foreach($ledgerData as $data)
        // {
        //     if($data->ledgerType == 'Cash' && $purchase->cashBankLedgerId == $data->id) $existLedgerType = 'Cash';
        //     if($data->ledgerType == 'Bank' && $purchase->cashBankLedgerId == $data->id) $existLedgerType = 'Bank';
        // }
		
		// $productType = DB::table('pos_product')->where('id', $order->productId)
		// 					->where('companyId', Auth::user()->company_id_fk)->first();
		$productType = DB::table('pos_product')
							->where('companyId', Auth::user()->company_id_fk)->first();
		//dd($productType);
		$hourBaseProduct = DB::table('pos_product')->where('type', 'hour')
								->where('companyId', Auth::user()->company_id_fk)->first();

		$transaction = new TransactionCheckHelper();
		$dateRange = $transaction->monthEndYearEndCheck();

		return view('pos.transaction.order.editOrder',
						['order'=>$order,
						'customers'=>$customers,
						'productNames'=>$productNames,
						'orderDetails'=>$orderDetails,
						'projects' => $projects, 
            			'projectTypes' => $projectTypes, 
						'branch' => $branch,
						'productType' => $productType,
						'hourBaseProduct' => $hourBaseProduct,
						'dateRange' => $dateRange]);
	}

	public function posUpdateorderItem(Request $request){
		//dd($request->all());
		$order = Posorder::find($request->orderId);
		//dd($order);
		//PosOrderDetails::where('orderId',$order->id)->delete();
		$order->billNo  = $request->billNo;
		$order->conPerson  = $request->conPerson;
		$order->customerId = $request->customerId;
		$order->projectId = $request->projectId;
        $order->projectTypeId = $request->projectTypeId;
        $order->branchId = $request->branchId;
        $order->remark = $request->remark;
		$order->totalAmount = $request->totalAmount;
		
		$order->orderDate = date("y-m-d",strtotime($request->orderDate));
		//dd($order);
		$order->save();

		$existsOrderDetails = PosOrderDetails::where('orderId',$request->orderId)->get();
		
		foreach ($existsOrderDetails as $key => $orderDetail) {
			$orderDetail->delete();
		}

		$existsIssueDetails = PosIssueDetails::where('orderId',$request->orderId)->get();
			foreach ($existsIssueDetails as $key => $issueDetail) {
				$issueDetail->delete();
			}

		$data = [];
		foreach (json_decode($request->quantity) as $key => $value) {
			$data1[] = $value;
			$data['quantity'] = $data1;
		}
		
		foreach (json_decode($request->productID) as $key => $value) {
			$data4[] = $value;
			$data['productId'] = $data4;
		}

		$otherCostVoucherAmount = [];
		$otherCostLedgerIdArr = [];
		$rawMaterialAmount = 0;

		$length = count($data['quantity']);
		
		for($i=0;$i<$length;$i++)
        {
            $posOrderDetails = new PosOrderDetails();
            $posOrderDetails->productId=$data['productId'][$i];
            $posOrderDetails->quantity=$data['quantity'][$i];
            $posOrderDetails->orderId=$order->id;
            //dd($posOrderDetails);
            $posOrderDetails->save();

              //Delete and update   issueDetails
       
		    // if($posOrderDetails->save()){
		    	
		    	
		    	//dd($posOrderDetails->productId);
				$costSheetDetails = DB::table('pos_cost_sheet')
								->where('companyId',Auth::user()->company_id_fk)
								->where('productId',$posOrderDetails->productId)
								->orderByDesc('effectDate')
								->first();
		    	//dd($costSheetDetails);
				if ($costSheetDetails) {
					$productInfos = json_decode($costSheetDetails->productInfo);
					//dd($productInfos);
					foreach ($productInfos as $key => $productInfo) {
						$posIssueDetails = new PosIssueDetails();
						$posIssueDetails->productId = $posOrderDetails->productId;
						$posIssueDetails->orderId = $posOrderDetails->orderId;
						$posIssueDetails->orderQty = $posOrderDetails->quantity;
						$posIssueDetails->rawMaterialId = $productInfo->productId;
						$posIssueDetails->rawMaterialIdAmount = $posOrderDetails->quantity * ($productInfo->qty * $productInfo->costPrice);

						$posIssueDetails->save();
						
						//dd($posIssueDetails) ;
					}

					// other cost calculation
						$otherCosts = json_decode($costSheetDetails->otherCost);

						if (count($otherCosts) > 0) {
							foreach ($otherCosts as $key => $otherCost) {

								if ($otherCost->isVoucher == true) {
									
									$otherCostLedgerId = DB::table('pos_other_cost')->where('id', $otherCost->otherCostId)->where('companyId',Auth::user()->company_id_fk)->value('ledger_id');
									$otherCostLedgerIdArr[] = $otherCostLedgerId;
									$otherCostVoucherAmount[$otherCostLedgerId] = isset($otherCostVoucherAmount[$otherCostLedgerId]) 
																				? $otherCostVoucherAmount[$otherCostLedgerId] + ($posOrderDetails->quantity * $otherCost->costAmount)
																				: $posOrderDetails->quantity * $otherCost->costAmount;
								}
							}
						}
				}
			//}
        }

      
				
		// Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('orderId', $order->id)->where('companyId', Auth::user()->company_id_fk)->get();
		//dd($existVoucherData);
		foreach($existVoucherData as $voucherData)
		{
			$existVoucherDetailsData  = AddVoucherDetails::where('voucherId', $voucherData->id)->get();
			foreach($existVoucherDetailsData as $voucherDetailsData)
			{
				$voucherDetailsData->delete();
			}
			$voucherData->delete();
		}
		// // Delete Exist Vouchers Stop

		// // Auto JV Voucher Start
		$jv = DB::table('acc_voucher_type')->where('shortName', 'JV')->select('id', 'shortName')->first();	
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();		
		$existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
							->where('projectId', $request->projectId)->where('branchId', $request->branchId)
							->where('voucherTypeId', $jv->id)
							->select('voucherCode')
							->orderBy('voucherCode', 'DESC')
							->first();
							//dd($existLedger);
		if($existLedger)
		{
			$existVoucherCode = explode(".", $existLedger->voucherCode);
			$voucherCode = $existVoucherCode[0].'.'.$existVoucherCode[1].'.'.$existVoucherCode[2].'.'.str_pad($existVoucherCode[3] + 1, 5, '0', STR_PAD_LEFT);
		}
		else
		{
			$branchCode = DB::table('gnr_branch')->where('id', $request->branchId)->pluck('branchCode')->first();
			$branchCode = str_pad($branchCode, 3, '0', STR_PAD_LEFT);

			$projectCode = DB::table('gnr_project')->where('id', $request->projectId)->pluck('projectCode')->first();
			$projectCode = str_pad($projectCode, 5, '0', STR_PAD_LEFT);

			$voucherCode = $jv->shortName.'.'.$branchCode.'.'.$projectCode.'.'.'00001';
		}

		$voucher = new AddVoucher();
		$voucher->companyId = Auth::user()->company_id_fk;
		$voucher->voucherTypeId = $jv->id;
		$voucher->projectId = $request->projectId;
		$voucher->projectTypeId = $request->projectTypeId;
		$voucher->voucherDate = date("y-m-d", strtotime($order->orderDate));
		$voucher->voucherCode = $voucherCode;
		$voucher->globalNarration = 'Auto Voucher From Order';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->createdDate = Carbon::now();
		$voucher->orderId = $order->id;
		$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		// raw materials
		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		$voucherDetails->debitAcc = (int)$voucherConfig->finished_goods;
		$voucherDetails->creditAcc = (int)$voucherConfig->raw_materials;
		$voucherDetails->amount = $rawMaterialAmount; // need to work here fifo method
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();

		// other costs
			$otherCostLedgerIdArr = array_unique($otherCostLedgerIdArr);
			//dd($otherCostLedgerIdArr);
			if (count($otherCostLedgerIdArr) > 0) {
				foreach ($otherCostLedgerIdArr as $key => $id) {

					if ($otherCost->isVoucher == true) {
						$voucherDetails = new AddVoucherDetails();
						$voucherDetails->voucherId = $voucher->id;
						$voucherDetails->debitAcc = (int)$voucherConfig->finished_goods;
						$voucherDetails->creditAcc = $id;
						$voucherDetails->amount = $otherCostVoucherAmount[$id];
						$voucherDetails->localNarration = 'Journal Voucher';
						$voucherDetails->createdDate = Carbon::now();
						$voucherDetails->save();
					}
				}
			}
			// // Auto JV Voucher Stop
		
		return response()->json("Order Updated successfully!");
	}

	public function deleteOrderItem(Request $request)
	{
		$order = PosOrder::find($request->id);
		
		// Delete Exist OrderDeatils Start
		$existsOrderDetails = PosOrderDetails::where('orderId',$request->id)->get();
		foreach ($existsOrderDetails as $key => $orderDetail) {
			$orderDetail->delete();
		}

		// Delete Exist issueDeatils Start
		$existsIssueDetails = PosIssueDetails::where('orderId',$request->id)->get();
		foreach ($existsIssueDetails as $key => $issueDetail) {
			$issueDetail->delete();
		}
		
		//Delete Exist Vouchers and voucherDetails
		$existVoucherData = AddVoucher::where('orderId', $request->id)->where('companyId', Auth::user()->company_id_fk)->get();
		
		foreach($existVoucherData as $voucherData)
		{
			$existVoucherDetailsData  = AddVoucherDetails::where('voucherId', $voucherData->id)->get();
			foreach($existVoucherDetailsData as $voucherDetailsData)
			{
				$voucherDetailsData->delete();
			}
			$voucherData->delete();
		}

		$order->delete();
		// Delete Exist Vouchers Stop

		return response()->json("Order deleted successfully!");
	}

	public function addToChartOrderItem(Request $request){

		//dd($request->all());
		$rawMaterialAmount = 0;
		$otherCostAmount = 0;

		for ($i=0; $i < count($request['productId']); $i++) {

			$costSheetDetails = DB::table('pos_cost_sheet')
							->where('companyId', Auth::user()->company_id_fk)
							->where('productId', $request['productId'][$i])
							->orderByDesc('effectDate')
							->first();
			//dd($costSheetDetails);
			if ($costSheetDetails) {

				//raw material calculation
				$productInfos = json_decode($costSheetDetails->productInfo);
				foreach ($productInfos as $key => $productInfo) {
					$rawMaterialAmount += $request['quantity'][$i] * ($productInfo->qty * $productInfo->costPrice);
				}

				// other cost calculation
				$otherCosts = json_decode($costSheetDetails->otherCost);

				if (count($otherCosts) > 0) {
					foreach ($otherCosts as $key => $otherCost) {

						if ($otherCost->isVoucher == true) {
							$otherCostAmount += $request['quantity'][$i] * $otherCost->costAmount;
						}
					}
				}

			}
		}

		$totalAmount = $rawMaterialAmount + $otherCostAmount;
		//dd($totalAmount);

		return response()->json($totalAmount);


	}
}
