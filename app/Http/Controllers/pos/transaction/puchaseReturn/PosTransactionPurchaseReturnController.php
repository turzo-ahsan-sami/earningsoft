<?php

namespace App\Http\Controllers\pos\transaction\puchaseReturn;

use Illuminate\Http\Request;

use App\Http\Requests;
// use App\fams\FamsDepreciation as FamsDep;
// use App\fams\FamsDepDetails;
use App\pos\PosSale;
use App\pos\PosProduct;
use App\pos\PosSupplier;
use App\pos\PosCustomer;
use App\pos\PosPurchase;
use App\pos\PosPayments;
use App\pos\PurchaseDetails;
use App\pos\PosPurchaseReturnDetails;
use App\pos\PosPurchaseReturn;
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
use App\gnr\GnrCompany;
class PosTransactionPurchaseReturnController extends Controller
{
	public function index()
	{
		$products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->get();
		
		$purchases = DB::table('pos_purchase_return')
						->join('pos_supplier', 'pos_purchase_return.supplierId', '=', 'pos_supplier.id')
						->join('pos_product', 'pos_purchase_return.productId', '=', 'pos_product.id')
						->where('pos_purchase_return.companyId', Auth::user()->company_id_fk)
						->select('pos_purchase_return.*', 'pos_supplier.name as supName', 'pos_product.name as productName')
						->get(); 

		return view('pos/transaction/purchaseReturn/viewPurchasesReturnList',[
			//'products'=>$products,
			'purchases'=>$purchases,
			 //'branches' => $branches
			]);
	}

	public function monthEndCheck(Request $request)
    {   
        $checkValue = new TransactionCheckHelper();
        $checkValueResult = $checkValue->monthEndCheck($request->purchaseReturnId, 'purchase return');
        return response()->json($checkValueResult);
    }

	public function addPurchaseReturnItem()
	{

		$productNames = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code')->get();
		$prefix = DB::table('fams_product_prefix')->where('status',1)->value('name');
		$purchaseSuppliers = DB::table('pos_supplier')->where('companyId', Auth::user()->company_id_fk)->get();

		$products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code')->get();

        $customers =  DB::table('pos_customer')->where('companyId', Auth::user()->company_id_fk)->get();
        $payments =  DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectCode = DB::table('gnr_project')->select('projectCode')->first();

		$setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

      	$purchaseBillNo = DB::table('pos_purchase_return')->select('billNo')->get();
       	//dd($salesBillNo);

       	if($purchaseBillNo->count() > 0 ){
            foreach ($purchaseBillNo as $key => $purchase) {
                        $saleNoArr[] = (int)explode(".", $purchase->billNo)[2];    

            }
			$saleNoMax = max($saleNoArr);
			$newSaleNo =  $saleNoMax + 1;
              
       	}else{

          $newSaleNo = 1;

       	}

        $saleNoFormat = str_pad($newSaleNo, 5, '0', STR_PAD_LEFT);
        $projectCodeFormat = str_pad($projectCode->projectCode, 5, '0', STR_PAD_LEFT);
        $purchaseReturnBillNo  = 'PRB.'.$projectCodeFormat.'.'.$saleNoFormat;
		   
		$projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
		$projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
		$branch = Auth::user()->branchId;

		$transaction = new TransactionCheckHelper();
		$dateRange = $transaction->monthEndYearEndCheck();
       
		return view('pos/transaction/purchaseReturn/addPurchaseReturn',
					['purchaseSuppliers'=>$purchaseSuppliers,
					'productNames'=>$productNames,
					'purchaseReturnBillNo'=>$purchaseReturnBillNo,
					'projects' => $projects, 
                    'projectTypes' => $projectTypes, 
					'branch' => $branch,
					'setting' => $setting,
					'dateRange' => $dateRange]);

	}

	public function getProjectDetails(Request $request)
    {
        $purchase =  DB::table('pos_purchase')->where('billNo',$request->purchaseBillNo)
                                        ->where('companyId', Auth::user()->company_id_fk)->first();

        $project = DB::table('gnr_project')->where('id', $purchase->projectId)
                                            ->where('companyId', Auth::user()->company_id_fk)->first();

        $projectType = DB::table('gnr_project_type')->where('id', $purchase->projectTypeId)
                                                    ->where('companyId', Auth::user()->company_id_fk)->first();

        $branch = DB::table('gnr_branch')->where('id', $purchase->branchId)
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

	public function getBillNoOnChangeSupplier(Request $request)
	{
	  	$purchases = DB::table('pos_purchase')->where('supplierId',$request->supplierId)->select('billNo','productId')->get();
	   	return response()->json($purchases);
	}

	public function getProductOnChangeSupplier(Request $request)
	{
		//dd($request->all());
		
	   // $purchases = DB::table('pos_purchase')
	  	// 			->join('pos_purchase_deatis', 'pos_product.id', '=', 'pos_purchase_deatis.productId')
	   // 				->join('pos_product', 'pos_product.id', '=', 'pos_purchase.productId')
	   // 				->select('pos_purchase.*','pos_product.name','pos_product.code')->get();
	   // 				->where('billNo',$request->billNo)
	   	// $purchases = DB::table('pos_purchase')
        //     ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=', 'pos_purchase.id')
        //     ->join('pos_product', 'pos_purchase_details.productId', '=', 'pos_product.id')
        //     ->select('pos_purchase.*','pos_purchase_details.*','pos_product.name as productName')
		// 	->where('billNo',$request->billNo)
		// 	->where('pos_purchase.companyId', Auth::user()->company_id_fk)
        //     ->get(); 
		   // 	//dd($purchases);
		   
			$purchases = DB::table('pos_purchase')
							->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=', 'pos_purchase.id')
							->join('pos_product', 'pos_purchase_details.productId', '=', 'pos_product.id')
							->select('pos_purchase.*','pos_purchase_details.*','pos_product.name as productName', 'pos_purchase.id as purchaseId')
							->where('billNo', $request->billNo)
							->where('pos_purchase.companyId', Auth::user()->company_id_fk)
							->get(); 

			// Check Purchase Return
			foreach($purchases as $record)
			{	
				$totalPurchaseReturnQty = DB::table('pos_purchase_return_details as t1')
											->join('pos_purchase_return as t2', 't2.id', 't1.purchaseId')
											->where('t2.companyId', Auth::user()->company_id_fk)
											->where('t1.productId', $record->productId)
											->where('t1.purchaseTableId', $record->purchaseId)
											->sum('t1.quantity');
	
				$record->quantity = $record->quantity - $totalPurchaseReturnQty;
			}

			// Check Sales 
			foreach($purchases as $record)
			{	
				$totalSalesQty = DB::table('pos_sales_details as t1')
											->join('pos_sales as t2', 't2.id', 't1.salesId')
											->where('t2.companyId', Auth::user()->company_id_fk)
											->where('t1.productId', $record->productId)
											->where('t1.purchaseTableId', $record->purchaseId)
											->sum('t1.quantity');

				$record->quantity = $record->quantity - $totalSalesQty;
			}

			// Check Sales Return
			foreach($purchases as $record)
			{	
				$totalSalesReturnQty = DB::table('pos_sales_return_details as t1')
											->join('pos_sales_return as t2', 't2.id', 't1.salesId')
											->where('t2.companyId', Auth::user()->company_id_fk)
											->where('t1.productId', $record->productId)
											->where('t1.purchaseTableId', $record->purchaseId)
											->sum('t1.quantity');
			

				$record->quantity = $record->quantity + $totalSalesReturnQty;
			}
						
	   	return response()->json($purchases);
	}

	public function posSavePurchaseReturnItem(Request $request)
	{	
		//dd(Auth::user()->company_id_fk);
		
		$purchase = new PosPurchaseReturn();

		$purchase->companyId = Auth::user()->company_id_fk;

		$purchase->billNo  = $request->billNo;
		$purchase->supplierId = $request->supplierId;
		$purchase->productId = $request->productId;
		$purchase->projectId = $request->projectId;
        $purchase->projectTypeId = $request->projectTypeId;
        $purchase->branchId = $request->branchId;
        $purchase->qty = $request->qty;
		$purchase->totalAmount = $request->totalAmount;
		$purchase->returnDate = date("y-m-d",strtotime($request->purchaseDate));
		
		
		if($purchase->save()){
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
			foreach (json_decode($request->purchaseId) as $key => $value) {
				$data5[] = $value;
				$data['purchaseId'] = $data5;
			}

			$length = count($data['quantity']);
			
			for($i=0;$i<$length;$i++)
			{
				$posPurchaseDetails = new PosPurchaseReturnDetails();
				$posPurchaseDetails->productId=$data['productID'][$i];
				$posPurchaseDetails->quantity=$data['quantity'][$i];
				$posPurchaseDetails->price=$data['price'][$i];
				$posPurchaseDetails->total=$data['total'][$i];
				$posPurchaseDetails->total=$data['total'][$i];
				$posPurchaseDetails->purchaseTableId = $data['purchaseId'][$i];
				$posPurchaseDetails->purchaseId=$purchase->id;
				$posPurchaseDetails->save();
				
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
		$voucher->globalNarration = 'Auto Voucher From Purchase Return';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->createdDate = Carbon::now();
		$voucher->purchaseReturnId = $purchase->id;
		$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$supplierLedegrId = DB::table('pos_supplier')->where('id', $request->supplierId)->select('accAccountLedgerId')->first();
		$purchase = DB::table('pos_purchase')->where('supplierId', $request->supplierId)->select('id')->first();
		$productId =  DB::table('pos_purchase_details')->where('purchaseId', $purchase->id)->get();
		//dd($productId);
		$company = GnrCompany::where('id', Auth::user()->company_id_fk)->first();

		foreach ($productId as $key => $product) {
		    $product = DB::table('pos_product')->where('id', $product->productId)->first();
		    if($product->type="product"){

		    }
		  
		}
		
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');

		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		$voucherDetails->debitAcc = (int)$supplierLedegrId->accAccountLedgerId;
		if($company->business_type == 'manufacture'){

		foreach ($productId as $key => $product) {
		    $product = DB::table('pos_product')->where('id', $product->productId)->first();
		    $voucherDetails->creditAcc = ($product->type == 'product') ? (int)$voucherConfig->finished_goods : (int)$voucherConfig->raw_materials;
		  
		}
		}else{
			$voucherDetails->creditAcc = ($stockExist[0] == 1) ? (int)$voucherConfig->inventory : (int)$voucherConfig->purchase;
		}
		
		
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();		
		// Auto JV Voucher Stop
		
		$data = array(
			'purchaseReturnId' 	=> $purchase->id,
			'msg'				=> 'success'
		);
		
		return response()->json($data);

	}

	public function viewPurchaseReturnItem($id)
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
        $purchaseReturn = DB::table('pos_purchase_return as t1')->where('t1.id', $id)
                                    ->where('t1.companyId', Auth::user()->company_id_fk)
                                    ->join('gnr_project as t2', 't2.id', 't1.projectId')
                                    ->join('gnr_project_type as t3', 't3.id', 't1.projectTypeId')
                                    ->join('gnr_branch as t4', 't4.id', 't1.branchId')
                                    ->select('t2.name as projectName', 't4.name as branchName',
                                    't1.billNo', 't1.returnDate')->first();
                            
        $data['transactionInfo'] = [
            'project'       => $purchaseReturn->projectName,
            'branch'        => $purchaseReturn->branchName,
            'billNo'        => $purchaseReturn->billNo,
            'date'          => $purchaseReturn->returnDate
		];

        // Sales Details Information
        $detailsInfo = DB::table('pos_purchase_return as t1')->where('t1.id', $id)
									->where('t1.companyId', Auth::user()->company_id_fk)
									->join('pos_purchase_return_details as t2', 't2.purchaseId', 't1.id')
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
        $amountInfo = DB::table('pos_purchase_return')->where('id', $id)->first();
        $data['amountInfo'] = [
            'totalAmount'   => $amountInfo->totalAmount
		];

		return view('pos.transaction.purchaseReturn.viewPurchaseReturn',['data'=>$data]);
	}

	public function editPurchaseReturnItem($id)
	{
		$purchaseSuppliers = DB::table('pos_supplier')->where('companyId', Auth::user()->company_id_fk)->get();
		$purchase = PosPurchaseReturn::find($id);
		
	    $productNames = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code')->get();
	    $purchaseDetails = DB::table('pos_purchase_return_details')
            ->join('pos_product', 'pos_purchase_return_details.productId', '=', 'pos_product.id')
            ->select('pos_purchase_return_details.*', 'pos_product.name','pos_product.code')
            ->where('purchaseId',$id)
            ->get();
		$purchaseBill = DB::table('pos_purchase')->where('supplierId',$purchase->supplierId)->select('billNo','productId')->first();
		
		$projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
		$projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
		$branch = Auth::user()->branchId;

		$transaction = new TransactionCheckHelper();
		$dateRange = $transaction->monthEndYearEndCheck();
        
		return view('pos.transaction.purchaseReturn.editPurchaseReturn',
						['purchase'=>$purchase,
						'purchaseSuppliers'=>$purchaseSuppliers,
						'productNames'=>$productNames,
						'purchaseDetails'=>$purchaseDetails,
						'purchaseBill'=>$purchaseBill,
						'projects' => $projects, 
            			'projectTypes' => $projectTypes, 
						'branch' => $branch,
						'dateRange' => $dateRange]);
	}

	public function posupdatePurchaseReturnItem(Request $request)
	{	

		$purchase = PosPurchaseReturn::find($request->purchaseId);
		PosPurchaseReturnDetails::where('purchaseId',$purchase->id)->delete();
		$purchase->billNo  = $request->billNo;
		$purchase->supplierId = $request->supplierId;
		$purchase->productId = $purchase->productId;
		$purchase->projectId = $request->projectId;
        $purchase->projectTypeId = $request->projectTypeId;
        $purchase->branchId = $request->branchId;
		$purchase->qty = $request->qty;
		$purchase->totalAmount = $request->totalAmount;
		$purchase->returnDate = date("y-m-d",strtotime($request->purchaseDate));
		$purchase->save();


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
		foreach (json_decode($request->setPurchaseId) as $key => $value) {
			$data5[] = $value;
			$data['setPurchaseId'] = $data5;
		}

		$length = count($data['quantity']);
		
		for($i=0;$i<$length;$i++)
        {
            $posPurchaseDetails = new PosPurchaseReturnDetails();
            $posPurchaseDetails->productId=$data['productID'][$i];
            $posPurchaseDetails->quantity=$data['quantity'][$i];
            $posPurchaseDetails->price=$data['price'][$i];
            $posPurchaseDetails->total=$data['total'][$i];
			$posPurchaseDetails->total=$data['total'][$i];
			$posPurchaseDetails->purchaseTableId = $data['setPurchaseId'][$i];
            $posPurchaseDetails->purchaseId=$purchase->id;
            $posPurchaseDetails->save();
            
		}
		
		// Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('purchaseReturnId', $purchase->id)->where('companyId', Auth::user()->company_id_fk)->get();

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
		$voucher->globalNarration = 'Auto Voucher From Purchase Return';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->createdDate = Carbon::now();
		$voucher->purchaseReturnId = $purchase->id;
		$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$supplierLedegrId = DB::table('pos_supplier')->where('id', $request->supplierId)->select('accAccountLedgerId')->first();
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');
		$productId =  DB::table('pos_purchase_details')->where('purchaseId', $purchase->id)->get();
		//dd($productId);
		$company = GnrCompany::where('id', Auth::user()->company_id_fk)->first();
		
		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		$voucherDetails->debitAcc = (int)$supplierLedegrId->accAccountLedgerId;
		//$voucherDetails->creditAcc = ($stockExist[0] == 1) ? (int)$voucherConfig->inventory : (int)$voucherConfig->purchase;
		if($company->business_type == 'manufacture'){

		foreach ($productId as $key => $product) {
		    $product = DB::table('pos_product')->where('id', $product->productId)->first();
		    $voucherDetails->creditAcc = ($product->type == 'product') ? (int)$voucherConfig->finished_goods : (int)$voucherConfig->raw_materials;
		  
		}
		}else{
			$voucherDetails->creditAcc = ($stockExist[0] == 1) ? (int)$voucherConfig->inventory : (int)$voucherConfig->purchase;
		}
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();		
		// Auto JV Voucher Stop
		
		return response()->json("Purchase Updated successfully!");
	}


	public function deletePurchaseReturnItem(Request $request){
		
		$purchase = PosPurchaseReturn::find($request->id);
		PosPurchaseReturnDetails::where('purchaseId',$purchase->id)->delete();
		$purchase->delete();

		// Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('purchaseReturnId', $request->id)->where('companyId', Auth::user()->company_id_fk)->get();

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

		return response()->json("Purchase deleted successfully!");
	}

} 
