<?php

namespace App\Http\Controllers\pos\transaction\sales;

use Illuminate\Http\Request;

use App\Http\Requests;
// use App\fams\FamsDepreciation as FamsDep;
// use App\fams\FamsDepDetails;
use App\pos\PosSale;
use App\pos\PosProduct;
use App\pos\PosCustomer;
use App\pos\PosPayments;
use App\pos\PosSales;
use App\pos\PosSalesDetails;
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

class PosTransactionSaleController extends Controller
{
    public function index()
    {   
        $sales = DB::table('pos_sales')
                    ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales.customerId')
                    // ->join('pos_product', 'pos_sales.productId', '=', 'pos_product.id')
                    // ->select('pos_sales.*', 'pos_customer.name as customerName', 'pos_product.name as productName')
                    ->where('pos_sales.companyId', Auth::user()->company_id_fk)
                    ->select('pos_sales.*', 'pos_customer.name as customerName')
                    ->get();


        // $productId = ['38', '39'];
        // $quantity = ['5', '6'];

        // $res = [];

        // for($i=0; $i<count($productId); $i++)
        // {
        //     $existData = DB::table('pos_purchase_details as t1')
        //                         ->join('pos_purchase as t2', 't2.id', 't1.purchaseId')
        //                         ->where('t2.companyId', Auth::user()->company_id_fk)
        //                         ->where('t1.productId', $productId[$i])
        //                         ->select('t1.quantity', 't1.price as costPrice', 't2.id as purchaseId', 't1.productId')
        //                         ->orderBy('t1.id', 'asc')
        //                         ->get();

        //     $existPurchaseReturn = DB::table('pos_purchase_return_details as t1')
        //                             ->join('pos_purchase_return as t2', 't2.id', 't1.purchaseId')
        //                             ->where('t2.companyId', Auth::user()->company_id_fk)
        //                             ->where('t1.productId', $productId[$i])
        //                             ->select('t1.quantity', 't1.price as costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
        //                             ->orderBy('t1.id', 'asc')
        //                             ->get();

            
        //     $existSalesData = DB::table('pos_sales_details as t1')
        //                         ->join('pos_sales as t2', 't2.id', 't1.salesId')
        //                         ->where('t2.companyId', Auth::user()->company_id_fk)
        //                         ->where('t1.productId', $productId[$i])
        //                         ->orderBy('t1.id', 'asc')
        //                         ->select('t1.quantity', 't1.costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
        //                         ->get();

        //     $existSalesReturnData = DB::table('pos_sales_return_details as t1')
        //                             ->join('pos_sales_return as t2', 't2.id', 't1.salesId')
        //                             ->where('t2.companyId', Auth::user()->company_id_fk)
        //                             ->where('t1.productId', $productId[$i])
        //                             ->orderBy('t1.id', 'asc')
        //                             ->select('t1.quantity', 't1.costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
        //                             ->get();
            
        //     // Check Purchase Return
        //     if($existPurchaseReturn)
        //     {
        //         foreach($existData as $key => $record)
        //         {
        //             foreach($existPurchaseReturn as $rec)
        //             {
        //                 if($record->purchaseId == $rec->purchaseId)
        //                 {    
        //                    $record->quantity = $record->quantity - $rec->quantity;
        //                    if($record->quantity == 0) unset($existData[$key]);
                            
        //                 }
        //             }
        //         }
        //     }

        //     // // Check Sales
        //     // if($existSalesData)
        //     // {
        //     //     foreach($existData as $key => $record)
        //     //     {  
        //     //         foreach($existSalesData as $rec)
        //     //         {
        //     //             if($record->purchaseId == $rec->purchaseId)
        //     //             {    
        //     //                $record->quantity = $record->quantity - $rec->quantity;
        //     //                if($record->quantity == 0) unset($existData[$key]);
                            
        //     //             }
        //     //         }
        //     //     }
        //     // }

        //     // Check Sales Return
        //     // if($existSalesReturnData)
        //     // {
        //     //     foreach($existData as $key => $record)
        //     //     {  
        //     //         foreach($existSalesReturnData as $rec)
        //     //         {
        //     //             if($record->purchaseId == $rec->purchaseId)
        //     //             {    
        //     //                $record->quantity = $record->quantity + $rec->quantity;
                            
        //     //             }
        //     //         }
        //     //     }
        //     // }

            

        //     // Set Final Data
        //     foreach($existData as $key => $record)
        //     {  
        //         if($record->quantity > $quantity[$i] || $record->quantity == $quantity[$i])
        //         {
        //             $res[$key]['qty'] = $quantity[$i];
        //             $res[$key]['costPrice'] = $record->costPrice;
        //             $res[$key]['purchaseId'] = $record->purchaseId;
        //             break;
        //         }
        //         else
        //         {
        //             $res[$key]['qty'] = $record->quantity;
        //             $res[$key]['costPrice'] = $record->costPrice;
        //             $res[$key]['purchaseId'] = $record->purchaseId;
        //             $quantity[$i] = $quantity[$i] - $record->quantity;
                    
        //         }
        //     }

        //     return $res;
        // }

        return view('pos/transaction/sale/viewSale',['sales'=>$sales]);
    }

    public function monthEndCheck(Request $request)
    {   
        $checkValue = new TransactionCheckHelper();
        $checkValueResult = $checkValue->monthEndCheck($request->salesId, 'sales');
        return response()->json($checkValueResult);
    }

    public function addSale()
    {
        $products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code', 'type')->get();
        $customers = DB::table('pos_customer')->where('companyId', Auth::user()->company_id_fk)->get();
        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectCode = DB::table('gnr_project')->select('projectCode')->first();

        $salesBillNo = DB::table('pos_sales')->where('companyId', Auth::user()->company_id_fk)->select('saleBillNo')->get();
        $setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();
        //dd($salesBillNo);

        if($salesBillNo->count() > 0 ){
                foreach ($salesBillNo as $key => $saleBillNo) {
                            $saleNoArr[] = (int)explode(".", $saleBillNo->saleBillNo)[2];    

                }
            $saleNoMax = max($saleNoArr);
            $newSaleNo =  $saleNoMax + 1;
                
        }else{

          $newSaleNo = 1;
        }

        $saleNoFormat = str_pad($newSaleNo, 5, '0', STR_PAD_LEFT);
        $projectCodeFormat = str_pad($projectCode->projectCode, 5, '0', STR_PAD_LEFT);
        $saleBillNo  = 'SL.'.$projectCodeFormat.'.'.$saleNoFormat;

        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch = Auth::user()->branchId;

        $hourBaseProduct = DB::table('pos_product')->where('type', 'hour')
                        ->where('companyId', Auth::user()->company_id_fk)->first();

        $transaction = new TransactionCheckHelper();
        $dateRange = $transaction->monthEndYearEndCheck();
        
        return view('pos/transaction/sale/addSale',
                    ['customers'=>$customers,
                    'saleBillNo'=>$saleBillNo,
                    'products'=>$products, 
                    'payments' => $payments, 
                    'projects' => $projects, 
                    'projectTypes' => $projectTypes, 
                    'branch' => $branch,
                    'hourBaseProduct' => $hourBaseProduct,
                    'setting' => $setting,
                    'dateRange' => $dateRange]);

    }

    public function checkQty(Request $request)
    {   
        $totalPurchaseQty = DB::table('pos_purchase_details as t1')
                        ->join('pos_purchase as t2', 't2.id', 't1.purchaseId')
                        ->where('t2.companyId', Auth::user()->company_id_fk)
                        ->where('t1.productId', $request->productId)
                        ->sum('t1.quantity');
        
        $totalPurchaseReturnQty = DB::table('pos_purchase_return_details as t1')
                                ->join('pos_purchase_return as t2', 't2.id', 't1.purchaseId')
                                ->where('t2.companyId', Auth::user()->company_id_fk)
                                ->where('t1.productId', $request->productId)
                                ->sum('t1.quantity');

        $totalSalesQty = DB::table('pos_sales_details as t1')
                        ->join('pos_sales as t2', 't2.id', 't1.salesId')
                        ->where('t2.companyId', Auth::user()->company_id_fk)
                        ->where('t1.productId', $request->productId)
                        ->sum('t1.quantity');
        
        $totalSalesReturnQty = DB::table('pos_sales_return_details as t1')
                                ->join('pos_sales_return as t2', 't2.id', 't1.salesId')
                                ->where('t2.companyId', Auth::user()->company_id_fk)
                                ->where('t1.productId', $request->productId)
                                ->sum('t1.quantity');
        
        if(!$totalPurchaseQty) $totalPurchaseQty = 0;  
        if(!$totalPurchaseReturnQty) $totalPurchaseReturnQty = 0;  
        if(!$totalSalesQty) $totalSalesQty = 0;  
        if(!$totalSalesReturnQty) $totalSalesReturnQty = 0;  

        $totalQty = ( $totalPurchaseQty - $totalPurchaseReturnQty ) - $totalSalesQty;
        $totalQty = $totalQty + $totalSalesReturnQty;
        
        return response()->json($totalQty);
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

    public function salesAddProductNameOnChangeProductCode(Request $request)
    {
       $data = DB::table('pos_product')->where('id',$request->productCode)->first();
       
       return response()->json($data);
    }

    // public function getBillNoOnChangeCustomer(Request $request){
    //    $projectCode = DB::table('gnr_project')->select('projectCode')->first();


    //    $salesBillNo = DB::table('pos_sales')->select('saleBillNo')->get();

    //    if($salesBillNo->count() > 0 ){
    //         foreach ($salesBillNo as $key => $saleBillNo) {
    //                     $saleNoArr[] = (int)explode(".", $saleBillNo->saleBillNo)[2];                 
    //         }
    //      $saleNoMax = max($saleNoArr);
    //      $newSaleNo =  $saleNoMax + 1;
              
    //    }else{

    //       $newSaleNo = 1;

    //    }

    //    $saleNoFormat = str_pad($newSaleNo, 5, '0', STR_PAD_LEFT);
    //    $projectCodeFormat = str_pad($projectCode, 5, '0', STR_PAD_LEFT);
    //    $saleBillNo  = 'SL.'.$projectCodeFormat.'.'.$saleNoFormat;
     
    //    return response()->json($saleBillNo);
    // }

    public function addPosSalesItem(Request $request)
    {      
        // return response()->json($request);
        
        $sales = new PosSales();

        $sales->companyId = Auth::user()->company_id_fk;

        $sales->saleBillNo  = $request->saleBillNo;
        $sales->conPerson  = $request->conPerson;
        $sales->customerId = $request->customerId;
        $sales->projectId = $request->projectId;
        $sales->projectTypeId = $request->projectTypeId;
        $sales->branchId = $request->branchId;
        $sales->productId = $request->productId;
        $sales->remark = $request->remark;
        $sales->stock = $request->stock;
        $sales->quantity = $request->qty;
        $sales->totalAmount = $request->totalAmount;
        $sales->discountAmount = ($request->discountAmount == null) ? 0 : $request->discountAmount;
        $sales->totalAmountAfterDis = $request->totalAmaountAfterDis;
        $sales->vatAmount = ($request->vatAmount == null) ? 0 : $request->vatAmount;
        $sales->grossTotal = $request->grossTotal;
        $sales->payAmount = ($request->payAmount == null) ? 0 : $request->payAmount;
        $sales->dueAmount = $request->dueAmount;
        $sales->cashBankLedgerId = $request->cashBankLedger;
        $sales->salesDate = date("y-m-d",strtotime($request->salesDate));
        $sales->paymentType = $request->paymentType;

        // dd($sales);
        //Insert purchase Details 

        if($sales->save())
        {
            $data = [];
            foreach (json_decode($request->quantity) as $key => $value) {
                $data1[] = $value;
                $data['quantity'] = $data1;
            }
            foreach (json_decode($request->price) as $key => $value) {
                $data2[] = $value;
                $data['price'] = $data2;
            }
            foreach (json_decode($request->total) as $key => $value) {
                $data3[] = $value;
                $data['total'] = $data3;
            }
            foreach (json_decode($request->productID) as $key => $value) {
                $data4[] = $value;
                $data['productID'] = $data4;
            }

            $length = count($data['quantity']);
            
            for($i=0;$i<$length;$i++)
            {   
                // Check Cost Price Scale & Generate Data Start
                $existData = DB::table('pos_purchase_details as t1')
                                    ->join('pos_purchase as t2', 't2.id', 't1.purchaseId')
                                    ->where('t2.companyId', Auth::user()->company_id_fk)
                                    ->where('t1.productId', $data['productID'][$i])
                                    ->select('t1.quantity', 't1.price as costPrice', 't2.id as purchaseId', 't1.productId')
                                    ->orderBy('t1.id', 'asc')
                                    ->get();

                $existPurchaseReturn = DB::table('pos_purchase_return_details as t1')
                                        ->join('pos_purchase_return as t2', 't2.id', 't1.purchaseId')
                                        ->where('t2.companyId', Auth::user()->company_id_fk)
                                        ->where('t1.productId', $data['productID'][$i])
                                        ->select('t1.quantity', 't1.price as costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
                                        ->orderBy('t1.id', 'asc')
                                        ->get();

                $existSalesData = DB::table('pos_sales_details as t1')
                                        ->join('pos_sales as t2', 't2.id', 't1.salesId')
                                        ->where('t2.companyId', Auth::user()->company_id_fk)
                                        ->where('t1.productId', $data['productID'][$i])
                                        ->orderBy('t1.id', 'asc')
                                        ->select('t1.quantity', 't1.costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
                                        ->get();

                $existSalesReturnData = DB::table('pos_sales_return_details as t1')
                                            ->join('pos_sales_return as t2', 't2.id', 't1.salesId')
                                            ->where('t2.companyId', Auth::user()->company_id_fk)
                                            ->where('t1.productId', $data['productID'][$i])
                                            ->orderBy('t1.id', 'asc')
                                            ->select('t1.quantity', 't1.costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
                                            ->get();

                
                // Check Purchase Return
                if($existPurchaseReturn)
                {
                    foreach($existData as $key => $record)
                    {
                        foreach($existPurchaseReturn as $rec)
                        {
                            if($record->purchaseId == $rec->purchaseId)
                            {    
                                $record->quantity = $record->quantity - $rec->quantity;
                                if($record->quantity == 0) unset($existData[$key]);
                            }
                        }
                    }
                }

                // Check Sales
                if($existSalesData)
                {
                    foreach($existData as $key => $record)
                    {  
                        foreach($existSalesData as $rec)
                        {
                            if($record->purchaseId == $rec->purchaseId)
                            {    
                                $record->quantity = $record->quantity - $rec->quantity;
                                if($record->quantity == 0) unset($existData[$key]);
                            }
                        }
                    }
                }

                // Check Sales Return
                if($existSalesReturnData)
                {
                    foreach($existData as $key => $record)
                    {  
                        foreach($existSalesReturnData as $rec)
                        {
                            if($record->purchaseId == $rec->purchaseId)
                            {    
                                $record->quantity = $record->quantity + $rec->quantity;
                            }
                        }
                    }
                }

                // Set Final Data
                foreach($existData as $key => $existDataRecord)
                {  
                    if($existDataRecord->quantity > $data['quantity'][$i] || $existDataRecord->quantity == $data['quantity'][$i])
                    {
                        $posSalesDetails = new PosSalesDetails();
                        $posSalesDetails->productId = $data['productID'][$i];
                        $posSalesDetails->quantity = $data['quantity'][$i];
                        $posSalesDetails->costPrice = $existDataRecord->costPrice;
                        $posSalesDetails->price = $data['price'][$i];
                        $posSalesDetails->total = $data['total'][$i];
                        $posSalesDetails->salesId = $sales->id;
                        $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                        $posSalesDetails->save();
                        break;

                    }
                    else
                    {
                        $posSalesDetails = new PosSalesDetails();
                        $posSalesDetails->productId = $data['productID'][$i];
                        $posSalesDetails->quantity = $existDataRecord->quantity;
                        $posSalesDetails->costPrice = $existDataRecord->costPrice;
                        $posSalesDetails->price = $data['price'][$i];
                        $posSalesDetails->total = $data['total'][$i];
                        $posSalesDetails->salesId = $sales->id;
                        $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                        $posSalesDetails->save();

                        $data['quantity'][$i] = $data['quantity'][$i] - $existDataRecord->quantity;
                    }
                }
                // Check Cost Price Scale & Generate Data Stop
            } 
        }

        // Auto JV Voucher Start
		$jv = DB::table('acc_voucher_type')->where('shortName', 'JV')->select('id', 'shortName')->first();			
		$existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
							->where('projectId', $request->projectId)->where('branchId', $request->branchId)
							->where('voucherTypeId', $jv->id)
							->select('voucherCode')
							->orderBy('voucherCode', 'DESC')
							->first();

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
		$voucher->voucherDate = date("y-m-d",strtotime($request->salesDate));
		$voucher->voucherCode = $voucherCode;
		$voucher->globalNarration = 'Auto Voucher From Sales';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
        $voucher->createdDate = Carbon::now();
        $voucher->salesId = $sales->id;
        $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$customerLedegrId = DB::table('pos_customer')->where('id', $request->customerId)->select('accAccountLedgerId')->first();
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		$voucherDetails->debitAcc = (int)$customerLedegrId->accAccountLedgerId;
		$voucherDetails->creditAcc = (int)$voucherConfig->sales;
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		//$voucherDetails->save();
		//dd($voucherDetails);
		if($request->vatAmount != 0)
		{	
			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = (int)$customerLedegrId->accAccountLedgerId;
			$voucherDetails->creditAcc = (int)$voucherConfig->vat;
			$voucherDetails->amount = $request->vatAmount;
			$voucherDetails->localNarration = 'Journal Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
		}
        // Auto JV Voucher Stop
        
        // Auto JV Voucher Start
        $stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');
        if($stockExist[0] == 1)
        {
            $jv = DB::table('acc_voucher_type')->where('shortName', 'JV')->select('id', 'shortName')->first();			
            $existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
                                ->where('projectId', $request->projectId)->where('branchId', $request->branchId)
                                ->where('voucherTypeId', $jv->id)
                                ->select('voucherCode')
                                ->orderBy('voucherCode', 'DESC')
                                ->first();

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
            $voucher->voucherDate = date("y-m-d",strtotime($request->salesDate));
            $voucher->voucherCode = $voucherCode;
            $voucher->globalNarration = 'Auto Voucher From Sales';
            $voucher->branchId = $request->branchId;
            $voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->salesId = $sales->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->save();

            $voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

            $voucherDetails = new AddVoucherDetails();
            $voucherDetails->voucherId = $voucher->id;
            $voucherDetails->debitAcc = (int)$voucherConfig->cost_of_good_sold;
            $voucherDetails->creditAcc = (int)$voucherConfig->inventory;
            $voucherDetails->amount = (int)$request->totalAmount;
            $voucherDetails->localNarration = 'Journal Voucher';
            $voucherDetails->createdDate = Carbon::now();
            $voucherDetails->save();
        }
        // Auto JV Voucher Stop

        // Auto CR Voucher Start
		if($request->payAmount != 0)
		{
			$cr = DB::table('acc_voucher_type')->where('shortName', 'CR')->select('id', 'shortName')->first();
            $existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
								->where('projectId', $request->projectId)->where('branchId', $request->branchId)
								->where('voucherTypeId', $cr->id)
								->select('voucherCode')
								->orderBy('voucherCode', 'DESC')
								->first();

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

				$voucherCode = $cr->shortName.'.'.$branchCode.'.'.$projectCode.'.'.'00001';
			}

			$voucher = new AddVoucher();
			$voucher->companyId = Auth::user()->company_id_fk;
			$voucher->voucherTypeId = $cr->id;
			$voucher->projectId = $request->projectId;
			$voucher->projectTypeId = $request->projectTypeId;
			$voucher->voucherDate = date("y-m-d",strtotime($request->salesDate));
			$voucher->voucherCode = $voucherCode;
			$voucher->globalNarration = 'Auto Voucher From Sales';
			$voucher->branchId = $request->branchId;
			$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->salesId = $sales->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->save();

			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = (int)$request->cashBankLedger;
			$voucherDetails->creditAcc = (int)$customerLedegrId->accAccountLedgerId;
			$voucherDetails->amount = $request->payAmount;
			$voucherDetails->localNarration = 'Credit Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
			
		}
        // Auto CR Voucher Stop
        
        $data = array(
            'salesId' => $sales->id,
            'msg'     => 'success'
        );
		
        return response()->json($data);

    }

    public function editsalesItem($id)
    {
        $products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code', 'type')->get();
        $customers = DB::table('pos_customer')->where('companyId', Auth::user()->company_id_fk)->get();
        $sale = PosSales::find($id);
        $salesDetails = DB::table('pos_sales_details')
            ->join('pos_product', 'pos_sales_details.productId', '=', 'pos_product.id')
            ->select('pos_sales_details.*', 'pos_product.name','pos_product.code', DB::raw('sum(pos_sales_details.quantity) as quantity'))
            ->where('salesId',$id)
            ->groupBy('pos_sales_details.productId')
            ->get();
        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();
        
        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch= Auth::user()->branchId;

        $productType = DB::table('pos_product')->where('id', $sale->productId)
							->where('companyId', Auth::user()->company_id_fk)->first();
		
		$hourBaseProduct = DB::table('pos_product')->where('type', 'hour')
                                ->where('companyId', Auth::user()->company_id_fk)->first();
                                
        $transaction = new TransactionCheckHelper();
        $dateRange = $transaction->monthEndYearEndCheck();

        return view('pos.transaction.sale.editSale',[
            'products'=>$products,
            'salesDetails'=>$salesDetails,
            'payments'=>$payments,
            'customers'=>$customers,
            'sale'=>$sale,
            'projects' => $projects, 
            'projectTypes' => $projectTypes, 
            'branch' => $branch,
            'productType' => $productType,
            'hourBaseProduct' => $hourBaseProduct,
            'dateRange'     => $dateRange
        ]);
    }

    public function deleteSalesItem(Request $request)
    {
        $sale = PosSales::find($request->id);
        PosSalesDetails::where('salesId',$sale->id)->delete();
        $sale->delete();

        // Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('salesId', $request->id)->where('companyId', Auth::user()->company_id_fk)->get();

		foreach($existVoucherData as $voucherData)
		{
			$existVoucherDetailsData  = AddVoucherDetails::where('voucherId', $voucherData->id)->get();
			foreach($existVoucherDetailsData as $voucherDetailsData)
			{
				$voucherDetailsData->delete();
			}
			$voucherData->delete();
		}
		// Delete Exist Vouchers Stop

        return response()->json("Sales data deleted successfully!");
    }

    
    public function updateSalesItem(Request $request)
    {
        // return response()->json($request);

        $sales = PosSales::find($request->saleId);
        PosSalesDetails::where('salesId',$sales->id)->delete();
       
        $sales->saleBillNo  = $request->saleBillNo;
        $sales->conPerson  = $request->conPerson;
        $sales->customerId = $request->customerId;
        $sales->productId = $request->productId;
        $sales->projectId = $request->projectId;
        $sales->projectTypeId = $request->projectTypeId;
        $sales->branchId = $request->branchId;
        $sales->remark = $request->remark;
        $sales->stock = $request->stock;
        $sales->quantity = $request->qty;
        $sales->totalAmount = $request->totalAmount;
        $sales->discountAmount = ($request->discountAmount == null) ? 0 : $request->discountAmount;
        $sales->totalAmountAfterDis = $request->totalAmaountAfterDis;
        $sales->vatAmount = ($request->vatAmount == null) ? 0 : $request->vatAmount;
        $sales->grossTotal = $request->grossTotal;
        $sales->payAmount = ($request->payAmount == null) ? 0 : $request->payAmount;
        $sales->dueAmount = $request->dueAmount;
        $sales->salesDate = date("y-m-d",strtotime($request->salesDate));
        $sales->cashBankLedgerId = $request->cashBankLedger;
        $sales->paymentType = $request->paymentType;
        $sales->save();


        //Insert purchase Details 
        $data = [];
        foreach (json_decode($request->quantity) as $key => $value) {
            $data1[] = $value;
            $data['quantity'] = $data1;
        }
        foreach (json_decode($request->price) as $key => $value) {
            $data2[] = $value;
            $data['price'] = $data2;
        }
        foreach (json_decode($request->total) as $key => $value) {
            $data3[] = $value;
            $data['total'] = $data3;
        }
        foreach (json_decode($request->productID) as $key => $value) {
            $data4[] = $value;
            $data['productID'] = $data4;
        }

        $length = count($data['quantity']);
        
        for($i=0;$i<$length;$i++)
        {   
            // Check Cost Price Scale & Generate Data Start
            $existData = DB::table('pos_purchase_details as t1')
                                ->join('pos_purchase as t2', 't2.id', 't1.purchaseId')
                                ->where('t2.companyId', Auth::user()->company_id_fk)
                                ->where('t1.productId', $data['productID'][$i])
                                ->select('t1.quantity', 't1.price as costPrice', 't2.id as purchaseId', 't1.productId')
                                ->orderBy('t1.id', 'asc')
                                ->get();

            $existPurchaseReturn = DB::table('pos_purchase_return_details as t1')
                                    ->join('pos_purchase_return as t2', 't2.id', 't1.purchaseId')
                                    ->where('t2.companyId', Auth::user()->company_id_fk)
                                    ->where('t1.productId', $data['productID'][$i])
                                    ->select('t1.quantity', 't1.price as costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
                                    ->orderBy('t1.id', 'asc')
                                    ->get();

            $existSalesData = DB::table('pos_sales_details as t1')
                                    ->join('pos_sales as t2', 't2.id', 't1.salesId')
                                    ->where('t2.companyId', Auth::user()->company_id_fk)
                                    ->where('t1.productId', $data['productID'][$i])
                                    ->orderBy('t1.id', 'asc')
                                    ->select('t1.quantity', 't1.costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
                                    ->get();

            $existSalesReturnData = DB::table('pos_sales_return_details as t1')
                                    ->join('pos_sales_return as t2', 't2.id', 't1.salesId')
                                    ->where('t2.companyId', Auth::user()->company_id_fk)
                                    ->where('t1.productId', $data['productID'][$i])
                                    ->orderBy('t1.id', 'asc')
                                    ->select('t1.quantity', 't1.costPrice', 't1.purchaseTableId as purchaseId', 't1.productId')
                                    ->get();

            // Check Purchase Return
            if($existPurchaseReturn)
            {
                foreach($existData as $key => $record)
                {
                    foreach($existPurchaseReturn as $rec)
                    {
                        if($record->purchaseId == $rec->purchaseId)
                        {    
                            $record->quantity = $record->quantity - $rec->quantity;
                            if($record->quantity == 0) unset($existData[$key]);
                        }
                    }
                }
            }

            // Check Sales
            if($existSalesData)
            {
                foreach($existData as $key => $record)
                {  
                    foreach($existSalesData as $rec)
                    {
                        if($record->purchaseId == $rec->purchaseId)
                        {    
                            $record->quantity = $record->quantity - $rec->quantity;
                            if($record->quantity == 0) unset($existData[$key]);
                        }
                    }
                }
            }

            // Check Sales Return
            if($existSalesReturnData)
            {
                foreach($existData as $key => $record)
                {  
                    foreach($existSalesReturnData as $rec)
                    {
                        if($record->purchaseId == $rec->purchaseId)
                        {    
                            $record->quantity = $record->quantity + $rec->quantity;
                        }
                    }
                }
            }

            // Set Final Data
            foreach($existData as $key => $existDataRecord)
            {  
                
                if($existDataRecord->quantity > $data['quantity'][$i] || $existDataRecord->quantity == $data['quantity'][$i])
                {
                    $posSalesDetails = new PosSalesDetails();
                    $posSalesDetails->productId = $data['productID'][$i];
                    $posSalesDetails->quantity = $data['quantity'][$i];
                    $posSalesDetails->costPrice = $existDataRecord->costPrice;
                    $posSalesDetails->price = $data['price'][$i];
                    $posSalesDetails->total = $data['total'][$i];
                    $posSalesDetails->salesId = $sales->id;
                    $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                    $posSalesDetails->save();
                    break;

                }
                else
                {
                    $posSalesDetails = new PosSalesDetails();
                    $posSalesDetails->productId = $data['productID'][$i];
                    $posSalesDetails->quantity = $existDataRecord->quantity;
                    $posSalesDetails->costPrice = $existDataRecord->costPrice;
                    $posSalesDetails->price = $data['price'][$i];
                    $posSalesDetails->total = $data['total'][$i];
                    $posSalesDetails->salesId = $sales->id;
                    $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                    $posSalesDetails->save();

                    $data['quantity'][$i] = $data['quantity'][$i] - $existDataRecord->quantity;
                }
            }
            // Check Cost Price Scale & Generate Data Stop
        } 

        // Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('salesId', $sales->id)->where('companyId', Auth::user()->company_id_fk)->get();

		foreach($existVoucherData as $voucherData)
		{
			$existVoucherDetailsData  = AddVoucherDetails::where('voucherId', $voucherData->id)->get();
			foreach($existVoucherDetailsData as $voucherDetailsData)
			{
				$voucherDetailsData->delete();
			}
			$voucherData->delete();
		}
        // Delete Exist Vouchers Stop
        
        
        // Auto JV Voucher Start
		$jv = DB::table('acc_voucher_type')->where('shortName', 'JV')->select('id', 'shortName')->first();			
		$existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
							->where('projectId', $request->projectId)->where('branchId', $request->branchId)
							->where('voucherTypeId', $jv->id)
							->select('voucherCode')
							->orderBy('voucherCode', 'DESC')
							->first();

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
		$voucher->voucherDate = date("y-m-d",strtotime($request->salesDate));
		$voucher->voucherCode = $voucherCode;
		$voucher->globalNarration = 'Auto Voucher From Sales';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
        $voucher->createdDate = Carbon::now();
        $voucher->salesId = $sales->id;
        $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$customerLedegrId = DB::table('pos_customer')->where('id', $request->customerId)->select('accAccountLedgerId')->first();
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		$voucherDetails->debitAcc = (int)$customerLedegrId->accAccountLedgerId;
		$voucherDetails->creditAcc = (int)$voucherConfig->sales;
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();
		
		if($request->vatAmount != 0)
		{	
			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = (int)$customerLedegrId->accAccountLedgerId;
			$voucherDetails->creditAcc = (int)$voucherConfig->vat;
			$voucherDetails->amount = $request->vatAmount;
			$voucherDetails->localNarration = 'Journal Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
		}
        // Auto JV Voucher Stop
        
        // Auto JV Voucher Start
        $stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');
        if($stockExist[0] == 1)
        {
            $jv = DB::table('acc_voucher_type')->where('shortName', 'JV')->select('id', 'shortName')->first();			
            $existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
                                ->where('projectId', $request->projectId)->where('branchId', $request->branchId)
                                ->where('voucherTypeId', $jv->id)
                                ->select('voucherCode')
                                ->orderBy('voucherCode', 'DESC')
                                ->first();

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
            $voucher->voucherDate = date("y-m-d",strtotime($request->salesDate));
            $voucher->voucherCode = $voucherCode;
            $voucher->globalNarration = 'Auto Voucher From Sales';
            $voucher->branchId = $request->branchId;
            $voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->salesId = $sales->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->save();

            $voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

            $voucherDetails = new AddVoucherDetails();
            $voucherDetails->voucherId = $voucher->id;
            $voucherDetails->debitAcc = (int)$voucherConfig->cost_of_good_sold;
            $voucherDetails->creditAcc = (int)$voucherConfig->inventory;
            $voucherDetails->amount = (int)$request->totalAmount;
            $voucherDetails->localNarration = 'Journal Voucher';
            $voucherDetails->createdDate = Carbon::now();
            $voucherDetails->save();
        }
        // Auto JV Voucher Stop

		// Auto CR Voucher Start
		if($request->payAmount != 0)
		{
			$cr = DB::table('acc_voucher_type')->where('shortName', 'CR')->select('id', 'shortName')->first();
            $existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
								->where('projectId', $request->projectId)->where('branchId', $request->branchId)
								->where('voucherTypeId', $cr->id)
								->select('voucherCode')
								->orderBy('voucherCode', 'DESC')
								->first();

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

				$voucherCode = $cr->shortName.'.'.$branchCode.'.'.$projectCode.'.'.'00001';
			}

			$voucher = new AddVoucher();
			$voucher->companyId = Auth::user()->company_id_fk;
			$voucher->voucherTypeId = $cr->id;
			$voucher->projectId = $request->projectId;
			$voucher->projectTypeId = $request->projectTypeId;
			$voucher->voucherDate = date("y-m-d",strtotime($request->salesDate));
			$voucher->voucherCode = $voucherCode;
			$voucher->globalNarration = 'Auto Voucher From Sales';
			$voucher->branchId = $request->branchId;
			$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->salesId = $sales->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->save();

			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = (int)$request->cashBankLedger;
			$voucherDetails->creditAcc = (int)$customerLedegrId->accAccountLedgerId;
			$voucherDetails->amount = $request->payAmount;
			$voucherDetails->localNarration = 'Credit Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
        
        }
		// Auto CR Voucher Stop
        
        return response()->json("success");
    }

   public function viewSalesDetailsItemId($id)
   {
        $data = array();
        
        // Company Information 
        $companyDetails = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)
                                                  ->select('name','address')->first();
        $data['companyInfo'] = [
            'name'      => $companyDetails->name,
            'address'   => $companyDetails->address
        ];

        // Sales Information
        $sales = DB::table('pos_sales as t1')->where('t1.id', $id)
                                    ->where('t1.companyId', Auth::user()->company_id_fk)
                                    ->join('gnr_project as t2', 't2.id', 't1.projectId')
                                    ->join('gnr_project_type as t3', 't3.id', 't1.projectTypeId')
                                    ->join('gnr_branch as t4', 't4.id', 't1.branchId')
                                    ->select('t2.name as projectName', 't4.name as branchName',
                                    't1.saleBillNo', 't1.salesDate')->first();
                                    //dd(PosSales::find($id));
                            
        $data['transactionInfo'] = [
            'project'       => $sales->projectName,
            'branch'        => $sales->branchName,
            'billNo'        => $sales->saleBillNo,
            'date'          => $sales->salesDate
        ];

        // Sales Details Information
        $detailsInfo = DB::table('pos_sales as t1')->where('t1.id', $id)
                                            ->where('t1.companyId', Auth::user()->company_id_fk)
                                            ->join('pos_sales_details as t2', 't2.salesId', 't1.id')
                                            ->join('pos_product as t3', 't3.id', 't2.productId')
                                            ->select('t3.name as productName', 't3.code', 't2.quantity', 't2.price', 't2.total')
                                            ->get();


        $data['transactionDetails'] = array();
        foreach($detailsInfo as $key => $record)
        {
            $data['transactionDetails'][$key] = [
                'product'   => $record->productName,
                'code'      => $record->code,
                'quantity'  => $record->quantity,
                'price'     => $record->price,
                'total'     => $record->total
            ];
        }

        // Sales Amount Information
        $amountInfo = DB::table('pos_sales')->where('id', $id)->first();
        $data['amountInfo'] = [
            'totalAmount'                   => $amountInfo->totalAmount,
            'discountAmount'                => $amountInfo->discountAmount,
            'totalAmountAfterDiscount'      => $amountInfo->totalAmountAfterDis,
            'vatAmount'                     => $amountInfo->vatAmount,
            'grossTotal'                    => $amountInfo->grossTotal,
            'paidAmount'                    => $amountInfo->payAmount,
            'dueAmount'                     => $amountInfo->dueAmount
        ];

       return view('pos.transaction.sale.viewSalesDetailsItem',['data'=>$data]);
    }

}
