<?php

namespace App\Http\Controllers\pos\transaction\salesReturn;

use Illuminate\Http\Request;

use App\Http\Requests;
// use App\fams\FamsDepreciation as FamsDep;
// use App\fams\FamsDepDetails;
use App\pos\PosSale;
use App\pos\PosProduct;
use App\pos\PosCustomer;
use App\pos\PosPayments;
use App\pos\PosSalesReturn;
use App\pos\PosSalesReturnDetails;
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

class PosTransactionSalesReturnController extends Controller
{
    public function index()
    {
        $sales = DB::table('pos_sales_return')
            ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales_return.customerId')
            ->join('pos_product', 'pos_sales_return.productId', '=', 'pos_product.id')
            ->select('pos_sales_return.*', 'pos_customer.name as cusName', 'pos_product.name as productName')
            ->where('pos_sales_return.companyId', Auth::user()->company_id_fk)
            ->get(); 

        return view('pos/transaction/salesReturn/viewSalesReturnList',['sales'=>$sales]);
    }

    public function monthEndCheck(Request $request)
    {   
        $checkValue = new TransactionCheckHelper();
        $checkValueResult = $checkValue->monthEndCheck($request->salesReturnId, 'sales return');
        return response()->json($checkValueResult);
    }

    public function addSalesReturnItem()
    {   
        $products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code')->get();
        $customers = DB::table('pos_customer')->where('companyId', Auth::user()->company_id_fk)->get();
        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectCode = DB::table('gnr_project')->select('projectCode')->first();
        $setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

        $salesBillNo = DB::table('pos_sales_return')->where('companyId', Auth::user()->company_id_fk)->select('billNo')->get();
       
        if($salesBillNo->count() > 0 ){
            foreach ($salesBillNo as $key => $saleBillNo) {
                $saleNoArr[] = (int)explode(".", $saleBillNo->billNo)[2];    

            }
         $saleNoMax = max($saleNoArr);
         $newSaleNo =  $saleNoMax + 1;
              
       }else{

          $newSaleNo = 1;

       }

        $saleNoFormat = str_pad($newSaleNo, 5, '0', STR_PAD_LEFT);
        $projectCodeFormat = str_pad($projectCode->projectCode, 5, '0', STR_PAD_LEFT);
        $saleBillNo  = 'SLB.'.$projectCodeFormat.'.'.$saleNoFormat;

        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch = Auth::user()->branchId;

        $transaction = new TransactionCheckHelper();
        $dateRange = $transaction->monthEndYearEndCheck();

        return view('pos/transaction/salesReturn/addSalesReturn',
                   ['customers'=> $customers,
                   'saleBillNo'=> $saleBillNo,
                   'products'=> $products, 
                   'payments' => $payments,
                   'projects' => $projects, 
                   'projectTypes' => $projectTypes,
                   'branch' => $branch,
                   'setting' => $setting,
                   'dateRange' => $dateRange]);

    }

    public function getProjectDetails(Request $request)
    {
        $sale =  DB::table('pos_sales')->where('saleBillNo',$request->saleBillNo)
                                        ->where('companyId', Auth::user()->company_id_fk)->first();

        $project = DB::table('gnr_project')->where('id', $sale->projectId)
                                            ->where('companyId', Auth::user()->company_id_fk)->first();

        $projectType = DB::table('gnr_project_type')->where('id', $sale->projectTypeId)
                                                    ->where('companyId', Auth::user()->company_id_fk)->first();

        $branch = DB::table('gnr_branch')->where('id', $sale->branchId)
                                           ->where('companyId', Auth::user()->company_id_fk)->first();

        $data[] = array(
            'projectId' => $project->id,
            'project' => $project->name,
            'projectTypeId' => $projectType->id,
            'projectType' => $projectType->name,
            'branchId'  => $branch->id,
            'branch'  => $branch->name
        );

        return response()->json($data);
    }

    public function getBillNoOnChangeCustomerReturn(Request $request)
    {
       $sales = DB::table('pos_sales')->where('customerId',$request->customerId)->get();

       $data = array();
       foreach($sales as $record)
       {   
          $salesReturn = DB::table('pos_sales_return')->where('salesTableId', $record->id)->first();
          if(!$salesReturn) $data[] = $record;
       }

       return response()->json($data);
    }

    public function getProductOnChangeCustomer(Request $request)
    {
		$sales = DB::table('pos_sales')
                    ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'pos_sales.id')
                    ->join('pos_product', 'pos_sales_details.productId', '=', 'pos_product.id')
                    ->select('pos_sales.*','pos_sales_details.*','pos_product.name as productName', 
                    'pos_sales_details.purchaseTableId as purchaseId', DB::raw('sum(pos_sales_details.quantity) as quantity'),
                    'pos_sales.id as salesId')
                    ->where('saleBillNo',$request->id)
                    ->groupBy('pos_sales_details.productId')
                    ->where('pos_sales.companyId', Auth::user()->company_id_fk)
                    ->get(); 
	   
	   	return response()->json($sales);
    }
    
    public function posSaveSalesReturnItem(Request $request)
    {   

        $sales = new PosSalesReturn();

        $sales->companyId = Auth::user()->company_id_fk;
        $sales->salesTableId = $request->salesId;
        $sales->billNo  = $request->billNo;
        $sales->customerId = $request->customerId;
        $sales->productId = $request->productId;
        $sales->projectId = $request->projectId;
        $sales->projectTypeId = $request->projectTypeId;
        $sales->branchId = $request->branchId;
        $sales->qty = $request->qty;
        $sales->totalAmount = $request->totalAmount;
        $sales->returnDate = date("y-m-d",strtotime($request->purchaseDate));
        
        if($sales->save()){
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
    
                // Set Final Data
                foreach($existData as $key => $existDataRecord)
                {  
                    
                    if($existDataRecord->quantity > $data['quantity'][$i] || $existDataRecord->quantity == $data['quantity'][$i])
                    {
                        $posSalesDetails = new PosSalesReturnDetails();
                        $posSalesDetails->productId=$data['productID'][$i];
                        $posSalesDetails->quantity=$data['quantity'][$i];
                        $posSalesDetails->costPrice = $existDataRecord->costPrice;
                        $posSalesDetails->price=$data['price'][$i];
                        $posSalesDetails->total=$data['total'][$i];
                        $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                        $posSalesDetails->salesId = $sales->id;
                        $posSalesDetails->save();
                        break;

                    }
                    else
                    {
                        $posSalesDetails = new PosSalesReturnDetails();
                        $posSalesDetails->productId=$data['productID'][$i];
                        $posSalesDetails->quantity = $existDataRecord->quantity;
                        $posSalesDetails->costPrice = $existDataRecord->costPrice;
                        $posSalesDetails->price=$data['price'][$i];
                        $posSalesDetails->total=$data['total'][$i];
                        $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                        $posSalesDetails->salesId = $sales->id;
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
		$voucher->voucherDate = date("y-m-d",strtotime($request->purchaseDate));
		$voucher->voucherCode = $voucherCode;
		$voucher->globalNarration = 'Auto Voucher From Sales Return';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
        $voucher->createdDate = Carbon::now();
        $voucher->salesReturnId = $sales->id;
        $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$customerLedegrId = DB::table('pos_customer')->where('id', $request->customerId)->select('accAccountLedgerId')->first();
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		$voucherDetails->debitAcc = (int)$voucherConfig->sales_return;
		$voucherDetails->creditAcc = (int)$customerLedegrId->accAccountLedgerId;
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();
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
            $voucher->voucherDate = date("y-m-d",strtotime($request->purchaseDate));
            $voucher->voucherCode = $voucherCode;
            $voucher->globalNarration = 'Auto Voucher From Sales Return';
            $voucher->branchId = $request->branchId;
            $voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->salesReturnId = $sales->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->save();

            $voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

            $voucherDetails = new AddVoucherDetails();
            $voucherDetails->voucherId = $voucher->id;
            $voucherDetails->debitAcc = (int)$voucherConfig->inventory;
            $voucherDetails->creditAcc = (int)$voucherConfig->cost_of_good_sold;
            $voucherDetails->amount = (int)$request->totalAmount;
            $voucherDetails->localNarration = 'Journal Voucher';
            $voucherDetails->createdDate = Carbon::now();
            $voucherDetails->save();
        
        }
        // Auto JV Voucher Stop
        
        $data = array(
            'salesReturnId' => $sales->id,
            'msg'           => 'success'
        );

        return response()->json($data);
    }

   
    public function editSalesReturnItem($id)
    {
        $products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code')->get();
        $customers = DB::table('pos_customer')->where('companyId', Auth::user()->company_id_fk)->get();
        $sale = PosSalesReturn::find($id);
        
        $sales  = DB::table('pos_sales_return_details')
                ->join('pos_product', 'pos_sales_return_details.productId', '=', 'pos_product.id')
                ->join('pos_sales_return', 'pos_sales_return_details.salesId', '=', 'pos_sales_return.id')
                ->select('pos_sales_return_details.*', 'pos_product.name','pos_product.code', 
                'pos_sales_return_details.productId as productId', DB::raw('sum(pos_sales_return_details.quantity) as quantity'), 
                'pos_sales_return.salesTableId')
                ->groupBy('productId')
                ->where('salesId',$sale->id)
                ->get();

        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();
        $salesBill = DB::table('pos_sales')->where('customerId',$sale->customerId)->first();

        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch = Auth::user()->branchId;

        $transaction = new TransactionCheckHelper();
        $dateRange = $transaction->monthEndYearEndCheck();

        return view('pos/transaction/salesReturn/editSalesReturn',[
			'products'=>$products,
            'salesBill'=>$salesBill,
            'payments'=>$payments,
            'sales'=>$sales,
            'customers'=>$customers,
            'sale'=>$sale,
            'projects' => $projects, 
            'projectTypes' => $projectTypes, 
            'branch' => $branch,
            'dateRange' => $dateRange
        ]);
        
    }

    public function deleteSalesReturnItem(Request $request)
    {
        $sale = PosSalesReturn::find($request->id);
         PosSalesReturnDetails::where('salesId',$sale->id)->delete();
        $sale->delete();

        // Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('salesReturnId', $request->id)->where('companyId', Auth::user()->company_id_fk)->get();

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

    
    public function posUpdateSalesReturnItem(Request $request)
    {   
        // return response()->json($request);

        //dd($request->all());
        $sales = PosSalesReturn::find($request->saleId);
       // dd();
        PosSalesReturnDetails::where('salesId',$sales->id)->delete();
  
        $sales->billNo  = $request->billNo;
        $sales->salesTableId = $request->salesTableId;
        $sales->customerId = $request->customerId;
        $sales->productId = $sales->productId;
        $sales->projectId = $request->projectId;
        $sales->projectTypeId = $request->projectTypeId;
        $sales->branchId = $request->branchId;
        $sales->qty = $request->qty;

        $sales->totalAmount = $request->totalAmount;
        $sales->returnDate = date("y-m-d",strtotime($request->purchaseDate));

        //Insert purchase Details 
        if($sales->save()){
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
    
                // Set Final Data
                foreach($existData as $key => $existDataRecord)
                {  
                    
                    if($existDataRecord->quantity > $data['quantity'][$i] || $existDataRecord->quantity == $data['quantity'][$i])
                    {
                        $posSalesDetails = new PosSalesReturnDetails();
                        $posSalesDetails->productId=$data['productID'][$i];
                        $posSalesDetails->quantity=$data['quantity'][$i];
                        $posSalesDetails->costPrice = $existDataRecord->costPrice;
                        $posSalesDetails->price=$data['price'][$i];
                        $posSalesDetails->total=$data['total'][$i];
                        $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                        $posSalesDetails->salesId = $sales->id;
                        $posSalesDetails->save();
                        break;

                    }
                    else
                    {
                        $posSalesDetails = new PosSalesReturnDetails();
                        $posSalesDetails->productId=$data['productID'][$i];
                        $posSalesDetails->quantity = $existDataRecord->quantity;
                        $posSalesDetails->costPrice = $existDataRecord->costPrice;
                        $posSalesDetails->price=$data['price'][$i];
                        $posSalesDetails->total=$data['total'][$i];
                        $posSalesDetails->purchaseTableId = $existDataRecord->purchaseId;
                        $posSalesDetails->salesId = $sales->id;
                        $posSalesDetails->save();

                        $data['quantity'][$i] = $data['quantity'][$i] - $existDataRecord->quantity;
                    }
                }
                // Check Cost Price Scale & Generate Data Stop
            } 
        }

        // Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('salesReturnId', $sales->id)->where('companyId', Auth::user()->company_id_fk)->get();

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
		$voucher->voucherDate = date("y-m-d",strtotime($request->purchaseDate));
		$voucher->voucherCode = $voucherCode;
		$voucher->globalNarration = 'Auto Voucher From Sales Return';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
        $voucher->createdDate = Carbon::now();
        $voucher->salesReturnId = $sales->id;
        $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$customerLedegrId = DB::table('pos_customer')->where('id', $request->customerId)->select('accAccountLedgerId')->first();
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		$voucherDetails->debitAcc = (int)$voucherConfig->sales_return;
		$voucherDetails->creditAcc = (int)$customerLedegrId->accAccountLedgerId;
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();
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
            $voucher->voucherDate = date("y-m-d",strtotime($request->purchaseDate));
            $voucher->voucherCode = $voucherCode;
            $voucher->globalNarration = 'Auto Voucher From Sales Return';
            $voucher->branchId = $request->branchId;
            $voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->salesReturnId = $sales->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->save();

            $voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

            $voucherDetails = new AddVoucherDetails();
            $voucherDetails->voucherId = $voucher->id;
            $voucherDetails->debitAcc = (int)$voucherConfig->inventory;
            $voucherDetails->creditAcc = (int)$voucherConfig->cost_of_good_sold;
            $voucherDetails->amount = (int)$request->totalAmount;
            $voucherDetails->localNarration = 'Journal Voucher';
            $voucherDetails->createdDate = Carbon::now();
            $voucherDetails->save();
        
        }
        // Auto JV Voucher Stop

        return response()->json("success");
    }

   public function viewSalesReturnItem($id)
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
        $salesReturn = DB::table('pos_sales_return as t1')->where('t1.id', $id)
                                    ->where('t1.companyId', Auth::user()->company_id_fk)
                                    ->join('gnr_project as t2', 't2.id', 't1.projectId')
                                    ->join('gnr_project_type as t3', 't3.id', 't1.projectTypeId')
                                    ->join('gnr_branch as t4', 't4.id', 't1.branchId')
                                    ->select('t2.name as projectName', 't4.name as branchName',
                                    't1.billNo', 't1.returnDate')->first();
      
        $data['transactionInfo'] = [
            'project'       => $salesReturn->projectName,
            'branch'        => $salesReturn->branchName,
            'billNo'        => $salesReturn->billNo,
            'date'          => $salesReturn->returnDate
        ];

        // Sales Details Information
        $detailsInfo = DB::table('pos_sales_return as t1')->where('t1.id', $id)
                                            ->where('t1.companyId', Auth::user()->company_id_fk)
                                            ->join('pos_sales_return_details as t2', 't2.salesId', 't1.id')
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
        $amountInfo = DB::table('pos_sales_return')->where('id', $id)->first();
        $data['amountInfo'] = [
            'totalAmount'  => $amountInfo->totalAmount
        ];

        return view('pos.transaction.salesReturn.viewSalesReturn',['data'=>$data]);
    }

}
