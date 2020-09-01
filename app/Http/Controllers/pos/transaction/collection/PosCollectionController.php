<?php

namespace App\Http\Controllers\pos\transaction\collection;

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
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\Service\TransactionCheckHelper;

class PosCollectionController extends Controller {

    public function listCollection(){
        $data['posCollectionInfo'] = PosCollection::where('companyId',Auth::user()->company_id_fk)->orderBy('collectionDate','desc')->get();
        return view('pos/transaction/collection/listCollection',$data);
    }
    
    public function addPosCollection()
    {   
        $salesInfo = DB::table('pos_sales')->where('companyId', Auth::user()->company_id_fk)
                                            ->where('dueAmount', '!=', 0)->get();
        
        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();

        $customers = array();
        foreach($salesInfo as $salesRecord)
        {   
            $collectionInfo = DB::table('pos_collection')->where('customerId', $salesRecord->customerId)
                                                        ->where('salesId', $salesRecord->id)
                                                        ->select(DB::raw('sum(collectionAmount) as totalCollectionAmount'))
                                                        ->first();

            $finalAmount = $salesRecord->grossTotal - $salesRecord->payAmount;

            if($collectionInfo->totalCollectionAmount != null && $collectionInfo->totalCollectionAmount < $finalAmount)
            {
                array_push($customers, $salesRecord->customerId);
            }
            if($collectionInfo->totalCollectionAmount == null) array_push($customers, $salesRecord->customerId);
        }

        $customers = array_unique($customers);
        $customers = DB::table('pos_customer')->whereIn('id', $customers)->get();

        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch = Auth::user()->branchId;

        $setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

        $checkValue = new TransactionCheckHelper();
        $checkTransaction = $checkValue->monthEndYearEndCheck();

        return view('pos/transaction/collection/addCollection', 
              compact('customers', 'projects', 'projectTypes', 'branch', 'payments', 'setting', 'checkTransaction'));
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

    public function getSalesBillNo(Request $request)
    {
        $salesInfo = DB::table('pos_sales')->where('customerId',$request->customerId)
                                          ->where('dueAmount', '!=', 0)
                                          ->where('companyId', Auth::user()->company_id_fk)->get();
                                          //dd($salesInfo);
        $data = array();
        foreach($salesInfo as $salesRecord)
        {   
            $collectionInfo = DB::table('pos_collection')->where('customerId', $salesRecord->customerId)
                                                        ->where('salesId', $salesRecord->id)
                                                        ->select(DB::raw('sum(collectionAmount) as totalCollectionAmount'))
                                                        ->first();
                                                        
            $finalAmount = $salesRecord->grossTotal - $salesRecord->payAmount;

            if($collectionInfo->totalCollectionAmount != null && $collectionInfo->totalCollectionAmount < $finalAmount)
            {
                array_push($data, $salesRecord);
            }
            if($collectionInfo->totalCollectionAmount == null) array_push($data, $salesRecord);
        }

        return response()->json($data);
    }

    public function getSalesInfo(Request $request)
    {
        $salesInfo = DB::table('pos_sales')->where('id', $request->salesId)->first();

        $collectionInfo = DB::table('pos_collection')->where('salesId', $salesInfo->id)
                            ->select('collectionAmount', DB::raw('DATE_FORMAT(collectionDate, "%d-%m-%Y") as collectionDate'))
                            ->orderBy('collectionDate', 'ASC')->get();

        $data = array(
            'totalAmount' => $salesInfo->totalAmount,
            'discountAmount' => $salesInfo->discountAmount,
            'totalAmountAfterDis' => $salesInfo->totalAmountAfterDis,
            'vatAmount'  => $salesInfo->vatAmount,
            'payAmount'  => $salesInfo->payAmount,
            'dueAmount'  => $salesInfo->dueAmount,
            'grossTotal' => $salesInfo->grossTotal,
            'collectionInfo' => $collectionInfo
                
        );

        return response()->json($data);
    }

    public function insertCollection(Request $request)
    {   
        $rules = array(
            'customerId'       => 'required|not_in:0',
            'salesBillNo'      => 'required|not_in:0',
            'collectionDate'   => 'required',
            'paidAmount'       => 'required',
            'projectId'        => 'required|not_in:0',
            'paymentType'      => 'required|not_in:0',
            'cashBankLedger'   => 'required|not_in:0'
            
        );
        $attributeNames = array(
            'customerId'      => 'Customer',
            'salesBillNo'     => 'Sales Bill No',
            'collectionDate'  => 'Collection Date',
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
            $sale = DB::table('pos_sales')->where('companyId', Auth::user()->company_id_fk)
                                         ->where('id', $request->salesBillNo)
                                         ->where('customerId', $request->customerId)->first();

            $dataSet[] = array(
                'salesBillNo' => $sale->saleBillNo,
                'salesId' => (int)$request->salesBillNo,
                'customerId' => (int)$request->customerId,
                'companyId' => Auth::user()->company_id_fk,
                'projectId' => $request->projectId,
                'projectTypeId' => $request->projectTypeId,
                'branchId'  => $request->branchId,
                'collectionAmount' => $request->paidAmount,
                'collectionDate' => date('Y-m-d', strtotime($request->collectionDate)),
                'cashBankLedgerId' => $request->cashBankLedger,
                'paymentType'   => $request->paymentType,
                'status' => 1
            );

            $response = DB::table('pos_collection')->insert($dataSet);

            $existCollectionData = DB::table('pos_collection')->orderBy('id', 'DESC')->first();

            // Auto CR Voucher Start
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
            $voucher->voucherDate = date("y-m-d",strtotime($request->collectionDate));
            $voucher->voucherCode = $voucherCode;
            $voucher->globalNarration = 'Auto Voucher From Collection';
            $voucher->branchId = $request->branchId;
            $voucher->prepBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->createdDate = Carbon::now();
            $voucher->collectionId = $existCollectionData->id;
            $voucher->authBy = (Auth::user()->emp_id_fk == null) ? 0 : Auth::user()->emp_id_fk;
            $voucher->save();

            $customerLedegrId = DB::table('pos_customer')->where('id', $request->customerId)->select('accAccountLedgerId')->first();

            $voucherDetails = new AddVoucherDetails();
            $voucherDetails->voucherId = $voucher->id;
            $voucherDetails->debitAcc = (int)$request->cashBankLedger;
            $voucherDetails->creditAcc = (int)$customerLedegrId->accAccountLedgerId;
            $voucherDetails->amount = (int)$request->paidAmount;
            $voucherDetails->localNarration = 'Credit Voucher';
            $voucherDetails->createdDate = Carbon::now();
            $voucherDetails->save();		
            // Auto CR Voucher Stop
            
            if($response == true)
            {
                $data = array(
                    'status' => true,
                    'msg' => 'Collection Add Successfully'
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

    public function editPosCollection($id){
         $salesInfo = DB::table('pos_sales')->where('companyId', Auth::user()->company_id_fk)
                                            ->where('dueAmount', '!=', 0)->get();
        
        $payments = DB::table('pos_payment')->where('companyId', Auth::user()->company_id_fk)->get();

        $customers = array();
        foreach($salesInfo as $salesRecord)
        {   
            $collectionInfo = DB::table('pos_collection')->where('customerId', $salesRecord->customerId)
                                                        ->where('salesId', $salesRecord->id)
                                                        ->select(DB::raw('sum(collectionAmount) as totalCollectionAmount'))
                                                        ->first();

            $finalAmount = $salesRecord->grossTotal - $salesRecord->payAmount;

            if($collectionInfo->totalCollectionAmount != null && $collectionInfo->totalCollectionAmount < $finalAmount)
            {
                array_push($customers, $salesRecord->customerId);
            }
            if($collectionInfo->totalCollectionAmount == null) array_push($customers, $salesRecord->customerId);
        }

        $customers = array_unique($customers);
        $customers = DB::table('pos_customer')->whereIn('id', $customers)->get();
        $posCollection = PosCollection::find($id);

        $projects = DB::table('gnr_project')->where('companyId', Auth::user()->company_id_fk)->get();
        $projectTypes = DB::table('gnr_project_type')->where('companyId', Auth::user()->company_id_fk)->get();
        $branch = Auth::user()->branchId;

        $setting = DB::table('pos_voucher_setting')->where('company_id', Auth::user()->company_id_fk)->first();

        $checkValue = new TransactionCheckHelper();
        $checkTransaction = $checkValue->monthEndYearEndCheck();

        $customerInfo = DB::table('pos_collection')->where('customerId', $posCollection->customerId)->first();
     
       
        //dd($customerInfo);

        $payment = DB::table('pos_payment')->where('id', $posCollection->paymentType)
                        ->where('companyId', Auth::user()->company_id_fk)->first();
                        //dd($payment);
        $ledgerData = DB::table('acc_account_ledger')
                        ->where('parentId', $payment->ledgerHeadId)
                        ->where('companyIdFk', Auth::user()->company_id_fk)
                        ->get();
                        //dd($ledgerData);
        $salesInfo = DB::table('pos_sales')->where('id', $posCollection->salesId)->first();
        //dd($salesInfo);
        return view('pos/transaction/collection/editCollection', 
              compact('customers', 'projects', 'projectTypes', 'branch', 'payments', 'setting', 'checkTransaction','posCollection','customerInfo','ledgerData','sales'));
    }

    public function updateCollection(Request $request){
       // dd($request->all());
        $rules = array(
            'customerId'       => 'required|not_in:0',
            'salesBillNo'      => 'required|not_in:0',
            'collectionDate'   => 'required',
            'paidAmount'       => 'required',
            'projectId'        => 'required|not_in:0',
            'paymentType'      => 'required|not_in:0',
            'cashBankLedger'   => 'required|not_in:0'
            
        );
        $attributeNames = array(
            'customerId'      => 'Customer',
            'salesBillNo'     => 'Sales Bill No',
            'collectionDate'  => 'Collection Date',
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
            $sale = DB::table('pos_sales')->where('companyId', Auth::user()->company_id_fk)
                                         ->where('customerId', $request->customerId)->first();
                                       
            $PosCollection = PosCollection::find($request->id);
            $PosCollection->salesBillNo = $sale->saleBillNo;
            $PosCollection->salesId = (int)$request->customerId;
            $PosCollection->customerId = $request->customerId;
            $PosCollection->projectId = $request->projectId;
            $PosCollection->branchId = $request->branchId;
            $PosCollection->collectionAmount = $request->paidAmount;
            $PosCollection->collectionDate = date("y-m-d",strtotime($request->collectionDate));
            $PosCollection->cashBankLedgerId = $request->cashBankLedger;
            $PosCollection->paymentType = $request->paymentType;
            $PosCollection->status = 1;
            //dd($PosCollection);
            $PosCollection->save();
            
            
            if($PosCollection->save())
            {
                $data = array(
                    'status' => true,
                    'msg' => 'Payment updated Successfully'
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

    public function deleteCollection(Request $request){
       PosCollection::find($request->id)->delete();
       return response::json('Data Deleted Successfully');
    }

}