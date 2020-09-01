<?php
namespace App\Http\Controllers\pos\report;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosSales;
use App\pos\PosCollection;
use App\pos\PosSalesDetails;
use App\pos\PosSupplier;
use Validator;
use Response;
use DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use App\Service\DatabasePartitionHelper;

class PosPurchaseReportController extends Controller
{
        
    public function index(Request $req){                                        
        $user = Auth::user();
        $logedUserName = $user->name;
        
        Session::put('branchId', $user->branchId);
        $gnrBranchId = Session::get('branchId');
               
        $partitionName = DatabasePartitionHelper::getUserWisePartitionName();
        
        $purchaseSuppliers = DB::table("pos_supplier")->where('companyId', Auth::user()->company_id_fk)->get();
        $productNames = DB::table(DB::raw("pos_product PARTITION ($partitionName)"))->where('companyId', Auth::user()->company_id_fk)->select('id','name','code')->get();
        $purchaseNo = DB::table(DB::raw("pos_purchase PARTITION ($partitionName)"))->where('companyId', Auth::user()->company_id_fk)->select('id','billNo')->get();                        
        
        return view('pos/report/posPurchaseReport/posPurchaseReportForm',['purchaseSuppliers'=>$purchaseSuppliers,'productNames'=>$productNames,'purchaseNo'=>$purchaseNo]);
    }


    public function getBillNoOnChangeSupplierPurchase(Request $request){
        $partitionName = DatabasePartitionHelper::getUserWisePartitionName();
        $purchases = DB::table(DB::raw("pos_purchase PARTITION ($partitionName)"))->where('supplierId',$request->supplierId)->where('companyId', Auth::user()->company_id_fk)->select('billNo','id')->get();        
        return response()->json($purchases);
    }


   public function getProductOnChangeSupplierPurchase(Request $request){
        $dbhelper = new DatabasePartitionHelper();
                
        $pos_purchase = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_purchase', 'pu');
        $pos_product = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_product', 'pd');
        
        $purchases = DB::table($pos_purchase)
        ->join($pos_product, 'pd.id', '=', 'pu.productId')
        ->where('pu.id', $request->id)
        ->select('pu.*','pd.id','pd.name','pd.code')->get();
    
        return response()->json($purchases);
    }

    public function getPurchaseReport(Request $req){
        
        $dbhelper = new DatabasePartitionHelper();

        $user = Auth::user();

        Session::put('branchId', $user->branchId);
        $gnrBranchId = Session::get('branchId');
        
        $pos_purchase = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_purchase', 'pu');
        $posAllPurchase = DB::table($pos_purchase)->where('pu.companyId', Auth::user()->company_id_fk);  
        
        $startDate = date('Y-m-d', strtotime($req->startDate));
        $endDate = date('Y-m-d', strtotime($req->endDate));

        if($req->billNo > 0) $posAllPurchase = $posAllPurchase->where('pu.id',$req->billNo);
        if($req->supplierId > 0) $posAllPurchase = $posAllPurchase->where('pu.supplierId',$req->supplierId);
        if($req->productId > 0) $posAllPurchase = $posAllPurchase->where('pu.productId',$req->productId);
        if($req->startDate != null) $posAllPurchase = $posAllPurchase->where('pu.purchaseDate','>=', $startDate);
        if($req->endDate != null) $posAllPurchase = $posAllPurchase->where('pu.purchaseDate','<=', $endDate);
                
        $pos_product = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_product', 'pd');
        
        $posPurchaseReports = $posAllPurchase
        ->Join('pos_purchase_details','pos_purchase_details.purchaseId', '=', 'pu.id')
        ->Join($pos_product, 'pd.id', '=','pos_purchase_details.productId')    
        ->select('pu.*','pos_purchase_details.*','pd.*')
        ->orderBy('pu.purchaseDate','ASC')
        ->orderBy('pu.billNo','ASC')
        ->orderBy('pu.supplierId','ASC')
        ->orderBy('pd.id','ASC')
        ->orderBy('pu.id','DESC')        
        ->get();

        $purchaseDate = ""; 
        $billNo = "";
        $supplierId = "";
        foreach($posPurchaseReports as $purchase){
            $purchase->purchaseDate != $purchaseDate ? $purchaseDate = $purchase->purchaseDate : $purchase->purchaseDate = "";
            $purchase->billNo != $billNo ? $billNo = $purchase->billNo : $purchase->billNo = "";
            $purchase->supplierId != $supplierId ? $supplierId = $purchase->supplierId : $purchase->supplierId = "";
        }

        for($i = 0; $i < count($posPurchaseReports); $i++){
            $rs1 = 0;
            if($posPurchaseReports[$i]->purchaseDate){
                $rs1 = 1;
                for($j = $i+1; $j < count($posPurchaseReports); $j++){
                    if($posPurchaseReports[$j]->purchaseDate) break;                    
                    $rs1++;
                }
            }
            $posPurchaseReports[$i]->daterow = $rs1;
            
            $rs2 = 0;
            if($posPurchaseReports[$i]->billNo){
                $rs2 = 1;
                for($j = $i+1; $j < count($posPurchaseReports); $j++){
                    if($posPurchaseReports[$j]->billNo) break;                    
                    $rs2++;
                }
            }
            $posPurchaseReports[$i]->billrow = $rs2;
            
            $rs3 = 0;
            if($posPurchaseReports[$i]->supplierId){
                $rs3 = 1;
                for($j = $i+1; $j < count($posPurchaseReports); $j++){
                    if($posPurchaseReports[$j]->supplierId) break;                    
                    $rs3++;
                }
            }
            $posPurchaseReports[$i]->supplierrow = $rs3;
        }
        
        $startDate=$req->startDate;
        $endDate=$req->endDate;

        $companyName = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->first();
    
        return view('pos/report/posPurchaseReport/posPurchaseReportTable',['posPurchaseReports'=>$posPurchaseReports,'startDate'=>$startDate,'endDate'=>$endDate,'companyName'=>$companyName]);
    }



}