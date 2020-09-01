<?php

namespace App\Http\Controllers\pos\transaction\supplierPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\pos\PosPurchase;
use App\pos\PosSupplierPayment;
use App\pos\PosSupplier;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\Service\TransactionCheckHelper;

class PosSupplierPaymentController extends Controller
{
    public function viewSupplierPayment(){
        $data['posPaymentInfo'] =PosSupplierPayment::where('companyId',Auth::user()->company_id_fk)->orderBy('paymentDate','desc')->get();
        //dd($data);
        return view('pos/transaction/supplierPayment/viewSupplierPayment',$data);
    }
    public function addSupplierPayment()
    {   
        $purchaseInfo = DB::table('pos_purchase')->where('companyId', Auth::user()->company_id_fk)
                                                ->where('dueAmount', '!=', 0)->get();
        
        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();

        $suppliers = array();
        foreach($purchaseInfo as $purchaseRecord)
        {   
            $paymentInfo = DB::table('pos_supplier_payment')->where('supplierId', $purchaseRecord->supplierId)
                                                        ->where('purchaseId', $purchaseRecord->id)
                                                        ->select(DB::raw('sum(paidAmount) as paidAmount'))
                                                        ->first();
            
            $finalAmount = $purchaseRecord->grossTotal - $purchaseRecord->payAmount;

            if($paymentInfo->paidAmount != null && $paymentInfo->paidAmount < $finalAmount)
            {
                array_push($suppliers, $purchaseRecord->supplierId);
            }
            if($paymentInfo->paidAmount == null) array_push($suppliers, $purchaseRecord->supplierId);
        }

        $suppliers = array_unique($suppliers);
        $suppliers = DB::table('pos_supplier')->whereIn('id', $suppliers)->get();

        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch = Auth::user()->branchId;

        $setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

        $checkValue = new TransactionCheckHelper();
        $checkTransaction = $checkValue->monthEndYearEndCheck();

        return view('pos/transaction/supplierPayment/addPayment', 
                compact('suppliers', 'projects', 'projectTypes', 'branch', 'payments', 'setting', 'checkTransaction'));
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

    public function getPurchaseBillNo(Request $request)
    {

        $purchaseInfo = DB::table('pos_purchase')->where('supplierId',$request->supplierId)
                                          ->where('dueAmount', '!=', 0)
                                          ->where('companyId', Auth::user()->company_id_fk)->get();
        $data = array();
        foreach($purchaseInfo as $purchaseRecord)
        {   
            $paymentInfo = DB::table('pos_supplier_payment')->where('supplierId', $purchaseRecord->supplierId)
                                                        ->where('purchaseId', $purchaseRecord->id)
                                                        ->select(DB::raw('sum(paidAmount) as paidAmount'))
                                                        ->first();

            $finalAmount = $purchaseRecord->grossTotal - $purchaseRecord->payAmount;

            if($paymentInfo->paidAmount != null && $paymentInfo->paidAmount < $finalAmount)
            {
                array_push($data, $purchaseRecord);
            }
            if($paymentInfo->paidAmount == null) array_push($data, $purchaseRecord);
        }

        return response()->json($data);
    }

    public function getPurchaseInfo(Request $request)
    {
        
        $purchaseInfo = DB::table('pos_purchase')->where('id', $request->purchaseId)->first();
        
        $paymentInfo = DB::table('pos_supplier_payment')->where('purchaseId', $purchaseInfo->id)
                            ->select('paidAmount', DB::raw('DATE_FORMAT(paymentDate, "%d-%m-%Y") as paymentDate'))
                            ->orderBy('paymentDate', 'ASC')->get();


        $data = array(
            'totalAmount' => $purchaseInfo->totalAmount,
            'discountAmount' => $purchaseInfo->discountAmount,
            'totalAmountAfterDis' => $purchaseInfo->totalAmaountAfterDis,
            'vatAmount'  => $purchaseInfo->vatAmount,
            'payAmount'  => $purchaseInfo->payAmount,
            'dueAmount'  => $purchaseInfo->dueAmount,
            'grossTotal' => $purchaseInfo->grossTotal,
            'paymentInfo' => $paymentInfo
                
        );

        return response()->json($data);
    }

    public function insertPayment(Request $request)
    {   
        $rules = array(
            'supplierId'     => 'required|not_in:0',
            'purchaseBillNo' => 'required|not_in:0',
            'paymentDate'    => 'required',
            'paidAmount'     => 'required',
            'projectId'        => 'required|not_in:0',
            'paymentType'       => 'required|not_in:0',
            'cashBankLedger'   => 'required|not_in:0'
            
        );
        $attributeNames = array(
            'supplierId'      => 'Supplier',
            'purchaseBillNo'  => 'Purchase Bill No',
            'paymentDate'     => 'Payment Date',
            'paidAmount'      => 'Pay Amount',
            'projectId'       => 'Project',
            'paymentType'     => 'Payment Type',
            'cashBankLedger'  => 'Ledger'

        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else
        {   

            $purchase = DB::table('pos_purchase')->where('companyId', Auth::user()->company_id_fk)
                                        ->where('id', $request->purchaseBillNo)
                                        ->where('supplierId', $request->supplierId)->first();

            $dataSet[] = array(
                'purchaseBillNo' => $purchase->billNo,
                'purchaseId' => (int)$request->purchaseBillNo,
                'supplierId' => (int)$request->supplierId,
                'companyId' => Auth::user()->company_id_fk,
                'projectId' => $request->projectId,
                'projectTypeId' => $request->projectTypeId,
                'branchId'  => $request->branchId,
                'paidAmount' => $request->paidAmount,
                'paymentDate' => date('Y-m-d', strtotime($request->paymentDate)),
                'cashBankLedgerId' => $request->cashBankLedger,
                'paymentType'   => $request->paymentType,
                'status' => 1
            );

            $response = DB::table('pos_supplier_payment')->insert($dataSet);

            $existPaymentData = DB::table('pos_supplier_payment')->orderBy('id', 'DESC')->first();

            // Auto DR Voucher Start
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
            $voucher->voucherDate = date("y-m-d",strtotime($request->paymentDate));
            $voucher->voucherCode = $voucherCode;
            $voucher->globalNarration = 'Auto Voucher From Supplier Payment';
            $voucher->branchId = $request->branchId;
            $voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->supplierPaymentId = $existPaymentData->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->save();

            $supplierLedegrId = DB::table('pos_supplier')->where('id', $request->supplierId)->select('accAccountLedgerId')->first();

            $voucherDetails = new AddVoucherDetails();
            $voucherDetails->voucherId = $voucher->id;
            $voucherDetails->debitAcc = (int)$supplierLedegrId->accAccountLedgerId;
            $voucherDetails->creditAcc = (int)$request->cashBankLedger;
            $voucherDetails->amount = (int)$request->paidAmount;
            $voucherDetails->localNarration = 'Debit Voucher';
            $voucherDetails->createdDate = Carbon::now();
            $voucherDetails->save();		
            // Auto DR Voucher Stop
            

            if($response == true)
            {
                $data = array(
                    'status' => true,
                    'msg' => 'Payment Add Successfully'
                );
            }
            else
            {
                $data = array(
                    'status' => false,
                );
            }

            
            return response::json($data);

        }
    }

    public function editPayment($id){

        $purchaseInfo = DB::table('pos_purchase')->where('companyId', Auth::user()->company_id_fk)
                                                ->where('dueAmount', '!=', 0)->get();
        
        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();

        $suppliers = array();
        foreach($purchaseInfo as $purchaseRecord)
        {   
            $paymentInfo = DB::table('pos_supplier_payment')->where('supplierId', $purchaseRecord->supplierId)
                                                        ->where('purchaseId', $purchaseRecord->id)
                                                        ->select(DB::raw('sum(paidAmount) as paidAmount'))
                                                        ->first();
            
            $finalAmount = $purchaseRecord->grossTotal - $purchaseRecord->payAmount;

            if($paymentInfo->paidAmount != null && $paymentInfo->paidAmount < $finalAmount)
            {
                array_push($suppliers, $purchaseRecord->supplierId);
            }
            if($paymentInfo->paidAmount == null) array_push($suppliers, $purchaseRecord->supplierId);
        }

        $suppliers = array_unique($suppliers);
        $suppliers = DB::table('pos_supplier')->whereIn('id', $suppliers)->get();

        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch = Auth::user()->branchId;

        $setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

        $checkValue = new TransactionCheckHelper();
        $checkTransaction = $checkValue->monthEndYearEndCheck();
        $posSupPayment = PosSupplierPayment::find($id);
       
        $supplierInfo = DB::table('pos_supplier_payment')->where('supplierId', $posSupPayment->supplierId)->first();
        //dd($supplierInfo);
        $purchaseInfo = DB::table('pos_purchase')->where('id', $posSupPayment->purchaseId)->first();
        $payment = DB::table('pos_payment')->where('id', $posSupPayment->paymentType)
                        ->where('companyId', Auth::user()->company_id_fk)->first();
                        //dd($payment);
        $ledgerData = DB::table('acc_account_ledger')
                        ->where('parentId', $payment->ledgerHeadId)
                        ->where('companyIdFk', Auth::user()->company_id_fk)
                        ->get();
                        //dd($ledgerData);
        return view('pos/transaction/supplierPayment/editSupplierPayment', 
                compact('suppliers', 'projects', 'projectTypes', 'branch', 'payments', 'setting', 'checkTransaction','posSupPayment','supplierInfo','ledgerData'));
    }

    public function updateSupplierPayment(Request $request){
       $rules = array(
            'supplierId'     => 'required|not_in:0',
            'purchaseBillNo' => 'required|not_in:0',
            'paymentDate'    => 'required',
            'paidAmount'     => 'required',
            'projectId'        => 'required|not_in:0',
            'paymentType'       => 'required|not_in:0',
            'cashBankLedger'   => 'required|not_in:0'
            
        );
        $attributeNames = array(
            'supplierId'      => 'Supplier',
            'purchaseBillNo'  => 'Purchase Bill No',
            'paymentDate'     => 'Payment Date',
            'paidAmount'      => 'Pay Amount',
            'projectId'       => 'Project',
            'paymentType'     => 'Payment Type',
            'cashBankLedger'  => 'Ledger'

        );

        $validator = Validator::make ( Input::all (), $rules);
        $validator->setAttributeNames($attributeNames);

        if ($validator->fails())
            return response::json(array('errors' => $validator->getMessageBag()->toArray()));
        else
        {   

            $purchase = DB::table('pos_purchase')->where('companyId', Auth::user()->company_id_fk)
                                        ->where('id', $request->purchaseBillNo)
                                        ->where('supplierId', $request->supplierId)->first();

           
            $posSupPayment = PosSupplierPayment::find($request->id);
            $posSupPayment->purchaseBillNo = $purchase->billNo;
            $posSupPayment->purchaseId = (int)$request->purchaseBillNo;
            $posSupPayment->supplierId = $request->supplierId;
            $posSupPayment->companyId = Auth::user()->company_id_fk;
            $posSupPayment->projectId = $request->projectId;
            $posSupPayment->branchId = $request->branchId;
            $posSupPayment->paidAmount = $request->paidAmount;
            $posSupPayment->paymentDate = date('Y-m-d', strtotime($request->paymentDate));
            $posSupPayment->cashBankLedgerId = $request->cashBankLedger;
            $posSupPayment->paymentType = $request->paymentType;
            $posSupPayment->status = 1;
            //dd($posSupPayment);
            $posSupPayment->save();
            
            
            if($posSupPayment->save())
            {
                $data = array(
                    'status' => true,
                    'msg' => 'Collection updated Successfully'
                );
            }
            else
            {
                $data = array(
                    'status' => false,
                );
            }
           

            
            return response::json($data);

        }
    }

    public function deletePayment(Request $request){
        //dd($request->id);
        PosSupplierPayment::find($request->id)->delete();
        return response::json('Data Deleted Successfully');
    }
}