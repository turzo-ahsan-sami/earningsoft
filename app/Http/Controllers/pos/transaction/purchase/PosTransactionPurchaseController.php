<?php

namespace App\Http\Controllers\pos\transaction\purchase;

use Illuminate\Http\Request;

use App\Http\Requests;
// use App\fams\FamsDepreciation as FamsDep;
// use App\fams\FamsDepDetails;
use App\pos\PosSale;
use App\pos\PosProduct;
use App\pos\PosSupplier;
use App\pos\PosCustomer;
use App\pos\PosPurchase;
use App\pos\PurchaseDetails;
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
class PosTransactionPurchaseController extends Controller
{
	public function index()
	{	

		$products = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->get();
		$branches = GnrBranch::all();
		$purchases = DB::table('pos_purchase')
            ->join('pos_supplier', 'pos_purchase.supplierId', '=', 'pos_supplier.id')
			->join('pos_product', 'pos_purchase.productId', '=', 'pos_product.id')
			->where('pos_purchase.companyId', Auth::user()->company_id_fk)
            ->select('pos_purchase.*', 'pos_supplier.name as supName', 'pos_product.name as productName')
			->get(); 
  		
		return view('pos/transaction/purchase/viewPurchasesList',[
			'products'=> $products,
			'purchases'=> $purchases,
			'branches' => $branches,
		]);
	}
	
    public function monthEndCheck(Request $request)
    {   
        $checkValue = new TransactionCheckHelper();
        $checkValueResult = $checkValue->monthEndCheck($request->purchaseId, 'purchase');
        return response()->json($checkValueResult);
    }

	public function posAddPurchase()
	{	
		$productNames = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code', 'type')->get();
		$purchaseSuppliers = DB::table('pos_supplier')->where('companyId', Auth::user()->company_id_fk)->get();
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
					
		return view('pos/transaction/purchase/addPurchase',
					['purchaseSuppliers'=> $purchaseSuppliers,
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

	public function purchaseAddProductNameOnChangeProductCode(Request $request){
	   $data = DB::table('pos_product')->where('id',$request->productCode)->first();
	   
	   return response()->json($data);
	}
	 public function getBillNoOnChangeSupplierId(Request $request){
	 	//dd($request->all());
	   $supplierCode = DB::table('pos_supplier')->where('id',$request->supplierId)->value('code');
	   $purchases = DB::table('pos_purchase')->where('supplierId',$request->supplierId)->select('billNo')->get();
	 
	   if($purchases->count() > 0 ){
		   	foreach ($purchases as $key => $purchase) {
		   				$purchaseNoArr[] = (int)explode(".", $purchase->billNo)[2];	   				
		    }
	     $purchaseNoMax = max($purchaseNoArr);
	     $newPurchaseNo =  $purchaseNoMax + 1;
	   		  
	   }else{

	   	  $newPurchaseNo = 1;
	   }
	   $purchaseNoFormat = str_pad($newPurchaseNo, 5, '0', STR_PAD_LEFT);
	   $supplierCodeFormat = str_pad($supplierCode, 5, '0', STR_PAD_LEFT);
	   $purchaseBillNo  = 'PR.'.$supplierCodeFormat.'.'.$purchaseNoFormat;

	   return response()->json($purchaseBillNo);
	}

	public function posSavePurchaseItem(Request $request)
	{	//dd($request->all());
		$purchase = new PosPurchase();

		$purchase->companyId = Auth::user()->company_id_fk;

		$purchase->billNo  = $request->billNo;
		$purchase->conPerson  = $request->conPerson;
		$purchase->supplierId = $request->supplierId;
		$purchase->productId = $request->productId;
		$purchase->projectId = $request->projectId;
        $purchase->projectTypeId = $request->projectTypeId;
        $purchase->branchId = $request->branchId;
        $purchase->remark = $request->remark;
		$purchase->qty = $request->qty;
		$purchase->totalAmount = $request->totalAmount;
		$purchase->discountAmount = ($request->discountAmount == null) ? 0 : $request->discountAmount;
		$purchase->totalAmaountAfterDis = $request->totalAmaountAfterDis;
		$purchase->vatAmount = ($request->vatAmount == null) ? 0 : $request->vatAmount;
		$purchase->grossTotal = $request->grossTotal;
		$purchase->payAmount = ($request->payAmount == null) ? 0 : $request->payAmount;
		$purchase->dueAmount = $request->dueAmount;
		$purchase->cashBankLedgerId = $request->cashBankLedger;
		$purchase->paymentType = $request->paymentType;
		//$purchase->purchaseDate = date("y-m-d",strtotime($request->purchaseDate));


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
			//dd($data['productID']);
			//dd($data);
			$length = count($data['quantity']);
			$productId = count($data['productID']);
			

			for($i=0; $i < $productId; $i++)
			{
				$posPurchaseDetails = new PurchaseDetails();
				$posPurchaseDetails->productId = $data['productID'][$i];
				$posPurchaseDetails->quantity  = $data['quantity'][$i];
				$posPurchaseDetails->price     =$data['price'][$i];
				$posPurchaseDetails->total     =$data['total'][$i];
				$posPurchaseDetails->purchaseId =$purchase->id;
				//dd($posPurchaseDetails);
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
		$voucher->globalNarration = 'Auto Voucher From Purchase';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->createdDate = Carbon::now();
		$voucher->purchaseId = $purchase->id;
		$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$supplierLedegrId = DB::table('pos_supplier')->where('id', $request->supplierId)->select('accAccountLedgerId')->first();
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();
		$productType = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->where('type',$request->productType)->first();
		$company = GnrCompany::where('id',Auth::user()->company_id_fk)->first();
		
		$stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');
		
		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		//dd($company->business_type);
		if($company->business_type == 'manufacture'){
			$voucherDetails->debitAcc = ($productType->type == 'product') ? (int)$voucherConfig->finished_goods : (int)$voucherConfig->raw_materials;
		}else{
			$voucherDetails->debitAcc = ($stockExist[0] == 1) ? (int)$voucherConfig->inventory : (int)$voucherConfig->purchase;
		}
		
		
		$voucherDetails->creditAcc = (int)$supplierLedegrId->accAccountLedgerId;
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();
		
		if($request->vatAmount != 0)
		{	
			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = $voucherConfig->vat;
			$voucherDetails->creditAcc = $supplierLedegrId->accAccountLedgerId;
			$voucherDetails->amount = $request->vatAmount;
			$voucherDetails->localNarration = 'Journal Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
		}
		// Auto JV Voucher Stop


		// Auto DR Voucher Start
		if($request->payAmount != 0)
		{
			$dr = DB::table('acc_voucher_type')->where('shortName', 'DR')->select('id', 'shortName')->first();

			$existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
								->where('projectId', $request->projectId)->where('branchId', $request->branchId)
								->where('voucherTypeId', $dr->id)
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

				$voucherCode = $dr->shortName.'.'.$branchCode.'.'.$projectCode.'.'.'00001';
			}

			$voucher = new AddVoucher();
			$voucher->companyId = Auth::user()->company_id_fk;
			$voucher->voucherTypeId = $dr->id;
			$voucher->projectId = $request->projectId;
			$voucher->projectTypeId = $request->projectTypeId;
			$voucher->voucherDate = date("y-m-d",strtotime($request->purchaseDate));
			$voucher->voucherCode = $voucherCode;
			$voucher->globalNarration = 'Auto Voucher From Purchase';
			$voucher->branchId = $request->branchId;
			$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->createdDate = Carbon::now();
			$voucher->purchaseId = $purchase->id;
			$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->save();

			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = $supplierLedegrId->accAccountLedgerId;
			$voucherDetails->creditAcc = $request->cashBankLedger;
			$voucherDetails->amount = $request->payAmount;
			$voucherDetails->localNarration = 'Debit Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
			
		}
		// Auto DR Voucher Stop

		$data = array(
			'purchaseId'  => $purchase->id,
			'msg'		  => 'success'
		);
			
		return response()->json($data);

	}

	public function viewPurchaseItem($id)
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
        $purchase = DB::table('pos_purchase as t1')->where('t1.id', $id)
                                    ->where('t1.companyId', Auth::user()->company_id_fk)
                                    ->join('gnr_project as t2', 't2.id', 't1.projectId')
                                    ->join('gnr_project_type as t3', 't3.id', 't1.projectTypeId')
                                    ->join('gnr_branch as t4', 't4.id', 't1.branchId')
                                    ->select('t2.name as projectName', 't4.name as branchName',
                                    't1.billNo', 't1.purchaseDate')->first();
                                  
                            	//dd(PosPurchase::find($id));
        $data['transactionInfo'] = [
            'project'       => $purchase->projectName,
            'branch'        => $purchase->branchName,
            'billNo'        => $purchase->billNo,
            'date'          => $purchase->purchaseDate
		];

        // Sales Details Information
        $detailsInfo = DB::table('pos_purchase as t1')->where('t1.id', $id)
									->where('t1.companyId', Auth::user()->company_id_fk)
									->join('pos_purchase_details as t2', 't2.purchaseId', 't1.id')
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
        //dd($data);
        // Sales Amount Information
        $amountInfo = DB::table('pos_purchase')->where('id', $id)->first();
        $data['amountInfo'] = [
            'totalAmount'                   => $amountInfo->totalAmount,
            'discountAmount'                => $amountInfo->discountAmount,
            'totalAmountAfterDiscount'      => $amountInfo->totalAmaountAfterDis,
            'vatAmount'                     => $amountInfo->vatAmount,
            'grossTotal'                    => $amountInfo->grossTotal,
            'paidAmount'                    => $amountInfo->payAmount,
            'dueAmount'                     => $amountInfo->dueAmount
		];
		
		return view('pos.transaction.purchase.viewPurchase',['data'=>$data]);
	}


	public function editPurchaseItem($id)
	{
		$purchaseSuppliers = DB::table('pos_supplier')->where('companyId', Auth::user()->company_id_fk)->get();
		$purchase = PosPurchase::find($id);

		$payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();

		$productNames = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->select('id','name','code', 'type')->get();
	    $purchaseDetails = DB::table('pos_purchase_details')
            ->join('pos_product', 'pos_purchase_details.productId', '=', 'pos_product.id')
            ->select('pos_purchase_details.*', 'pos_product.name','pos_product.code')
            ->where('purchaseId',$id)
            ->get();
		
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
		
		$productType = DB::table('pos_product')->where('id', $purchase->productId)
							->where('companyId', Auth::user()->company_id_fk)->first();
		
		$hourBaseProduct = DB::table('pos_product')->where('type', 'hour')
								->where('companyId', Auth::user()->company_id_fk)->first();

		$transaction = new TransactionCheckHelper();
		$dateRange = $transaction->monthEndYearEndCheck();

		return view('pos.transaction.purchase.editPurchase',
						['purchase'=>$purchase,
						'purchaseSuppliers'=>$purchaseSuppliers,
						'productNames'=>$productNames,
						'purchaseDetails'=>$purchaseDetails,
						'projects' => $projects, 
            			'projectTypes' => $projectTypes, 
						'branch' => $branch,
						'payments' => $payments,
						'productType' => $productType,
						'hourBaseProduct' => $hourBaseProduct,
						'dateRange' => $dateRange]);
	}

	public function posUpdatePurchaseItem(Request $request){
		//dd($request->all());
		$purchase = PosPurchase::find($request->purchaseId);
		//dd($purchase);
		PurchaseDetails::where('purchaseId',$purchase->id)->delete();
		$purchase->billNo  = $request->billNo;
		$purchase->conPerson  = $request->conPerson;
		$purchase->supplierId = $request->supplierId;
		$purchase->productId = $purchase->productId;
		$purchase->projectId = $request->projectId;
        $purchase->projectTypeId = $request->projectTypeId;
        $purchase->branchId = $request->branchId;
        $purchase->remark = $request->remark;
		$purchase->qty = $request->qty;
		$purchase->totalAmount = $request->totalAmount;
		$purchase->discountAmount = ($request->discountAmount == null) ? 0 : $request->discountAmount;
		$purchase->totalAmaountAfterDis = $request->totalAmaountAfterDis;
		$purchase->vatAmount = ($request->vatAmount == null) ? 0 : $request->vatAmount;
		$purchase->grossTotal = $request->grossTotal;
		$purchase->payAmount = ($request->payAmount == null) ? 0 : $request->payAmount;
		$purchase->dueAmount = $request->dueAmount;
		$purchase->cashBankLedgerId = $request->cashBankLedger;
		$purchase->paymentType = $request->paymentType;
		$purchase->purchaseDate = date("y-m-d",strtotime($request->purchaseDate));
		//dd($purchase);
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
			$data['productId'] = $data4;
		}

		$length = count($data['quantity']);
		
		for($i=0;$i<$length;$i++)
        {
            $posPurchaseDetails = new PurchaseDetails();
            $posPurchaseDetails->productId=$data['productId'][$i];
            $posPurchaseDetails->quantity=$data['quantity'][$i];
            $posPurchaseDetails->price=$data['price'][$i];
            $posPurchaseDetails->total=$data['total'][$i];
            $posPurchaseDetails->total=$data['total'][$i];
            $posPurchaseDetails->purchaseId=$purchase->id;
            //dd($posPurchaseDetails);
            $posPurchaseDetails->save();
        }

		// Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('purchaseId', $purchase->id)->where('companyId', Auth::user()->company_id_fk)->get();

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
		$voucher->globalNarration = 'Auto Voucher From Purchase';
		$voucher->branchId = $request->branchId;
		$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->createdDate = Carbon::now();
		$voucher->purchaseId = $purchase->id;
		$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
		$voucher->save();

		$supplierLedegrId = DB::table('pos_supplier')->where('id', $request->supplierId)->select('accAccountLedgerId')->first();
		$voucherConfig = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

		$stockExist = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->pluck('stock_type');

		$productType = DB::table('pos_product')->where('companyId', Auth::user()->company_id_fk)->where('type',$request->productType)->first();
		$company = GnrCompany::where('id',Auth::user()->company_id_fk)->first();

		$voucherDetails = new AddVoucherDetails();
		$voucherDetails->voucherId = $voucher->id;
		if($company->business_type == 'manufacture'){
			$voucherDetails->debitAcc = ($productType->type == 'product') ? (int)$voucherConfig->finished_goods : (int)$voucherConfig->raw_materials;
		}else{
			$voucherDetails->debitAcc = ($stockExist[0] == 1) ? (int)$voucherConfig->inventory : (int)$voucherConfig->purchase;
		}
		$voucherDetails->creditAcc = (int)$supplierLedegrId->accAccountLedgerId;
		$voucherDetails->amount = (int)$request->totalAmount;
		$voucherDetails->localNarration = 'Journal Voucher';
		$voucherDetails->createdDate = Carbon::now();
		$voucherDetails->save();
		
		if($request->vatAmount != 0)
		{	
			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = $voucherConfig->vat;
			$voucherDetails->creditAcc = $supplierLedegrId->accAccountLedgerId;
			$voucherDetails->amount = $request->vatAmount;
			$voucherDetails->localNarration = 'Journal Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
		}
		// Auto JV Voucher Stop

		// Auto DR Voucher Start
		if($request->payAmount != 0)
		{
			$dr = DB::table('acc_voucher_type')->where('shortName', 'DR')->select('id', 'shortName')->first();

			$existLedger = DB::table('acc_voucher')->where('companyId', Auth::user()->company_id_fk)
								->where('projectId', $request->projectId)->where('branchId', $request->branchId)
								->where('voucherTypeId', $dr->id)
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

				$voucherCode = $dr->shortName.'.'.$branchCode.'.'.$projectCode.'.'.'00001';
			}

			$voucher = new AddVoucher();
			$voucher->companyId = Auth::user()->company_id_fk;
			$voucher->voucherTypeId = $dr->id;
			$voucher->projectId = $request->projectId;
			$voucher->projectTypeId = $request->projectTypeId;
			$voucher->voucherDate = date("y-m-d",strtotime($request->purchaseDate));
			$voucher->voucherCode = $voucherCode;
			$voucher->globalNarration = 'Auto Voucher From Purchase';
			$voucher->branchId = $request->branchId;
			$voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->createdDate = Carbon::now();
			$voucher->purchaseId = $purchase->id;
			$voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
			$voucher->save();

			$voucherDetails = new AddVoucherDetails();
			$voucherDetails->voucherId = $voucher->id;
			$voucherDetails->debitAcc = $supplierLedegrId->accAccountLedgerId;
			$voucherDetails->creditAcc = $request->cashBankLedger;
			$voucherDetails->amount = $request->payAmount;
			$voucherDetails->localNarration = 'Debit Voucher';
			$voucherDetails->createdDate = Carbon::now();
			$voucherDetails->save();
			
		}
		// Auto DR Voucher Stop
		
		return response()->json("Purchase Updated successfully!");
	}

	public function deletePurchaseItem(Request $request)
	{
		$purchase = PosPurchase::find($request->id);
		PurchaseDetails::where('purchaseId',$request->id)->delete();
		$purchase->delete();

		// Delete Exist Vouchers Start
		$existVoucherData = AddVoucher::where('purchaseId', $request->id)->where('companyId', Auth::user()->company_id_fk)->get();

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
