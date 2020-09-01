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

class PosPurchaseReturnReportController extends Controller
{


    public function index(Request $req){        
        $user = Auth::user();
        $logedUserName = $user->name;

        Session::put('branchId', $user->branchId);
        $gnrBranchId = Session::get('branchId');

        $partitionName = DatabasePartitionHelper::getUserWisePartitionName();    
        
        $purchaseSuppliers = DB::table("pos_supplier")->where('companyId', Auth::user()->company_id_fk)->get();
        $productNames = DB::table(DB::raw("pos_product PARTITION ($partitionName)"))->where('companyId', Auth::user()->company_id_fk)->select('id','name','code')->get();        
        $purchaseNo = DB::table(DB::raw("pos_purchase_return PARTITION ($partitionName)"))->where('companyId', Auth::user()->company_id_fk)->select('id','billNo')->get();

        return view('pos/report/posPurchaseReturnReport/posPurchaseReturnReportForm',['purchaseNo'=>$purchaseNo]);
    }


    public function getPurchaseReturnReport(Request $req){
        $dbhelper = new DatabasePartitionHelper();

        $user = Auth::user();

        Session::put('branchId', $user->branchId);
        $gnrBranchId = Session::get('branchId');

        $pos_purchase_return = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_purchase_return', 'pr');
        $posAllPurchaseReturn = DB::table($pos_purchase_return)->where('pr.companyId', Auth::user()->company_id_fk);;

        $startDate = date('Y-m-d', strtotime($req->startDate));
        $endDate = date('Y-m-d', strtotime($req->endDate));


        if($req->billNo > 0) $posAllPurchaseReturn = $posAllPurchaseReturn->where('pr.id',$req->billNo);
        if($req->startDate != null) $posAllPurchaseReturn = $posAllPurchaseReturn->where('returnDate','>=', $startDate);
        if($req->endDate != null) $posAllPurchaseReturn = $posAllPurchaseReturn->where('returnDate','<=', $endDate);

        $pos_product = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_product', 'pd');

        $posPurchaseReturnReports = $posAllPurchaseReturn
        ->Join('pos_purchase_return_details','pos_purchase_return_details.purchaseId', '=', 'pr.id')
        ->Join($pos_product,'pd.id', '=','pos_purchase_return_details.productId')        
        ->select('pr.*','pos_purchase_return_details.*','pd.*')
        ->orderBy('pr.returnDate','ASC')        
        ->orderBy('pr.billNo','ASC')
        ->orderBy('pd.id','ASC')
        ->orderBy('pr.id','DESC')
        ->get();

        $returnDate = ""; 
        $billNo = "";
        foreach($posPurchaseReturnReports as $purchase){
            $purchase->returnDate != $returnDate ? $returnDate = $purchase->returnDate : $purchase->returnDate = "";
            $purchase->billNo != $billNo ? $billNo = $purchase->billNo : $purchase->billNo = "";
        }

        for($i = 0; $i < count($posPurchaseReturnReports); $i++){
            $rs1 = 0;
            if($posPurchaseReturnReports[$i]->returnDate){
                $rs1 = 1;
                for($j = $i+1; $j < count($posPurchaseReturnReports); $j++){
                    if($posPurchaseReturnReports[$j]->returnDate) break;                    
                    $rs1++;
                }
            }
            $posPurchaseReturnReports[$i]->daterow = $rs1;
            
            $rs2 = 0;
            if($posPurchaseReturnReports[$i]->billNo){
                $rs2 = 1;
                for($j = $i+1; $j < count($posPurchaseReturnReports); $j++){
                    if($posPurchaseReturnReports[$j]->billNo) break;                    
                    $rs2++;
                }
            }
            $posPurchaseReturnReports[$i]->billrow = $rs2;
        }

        $startDate=$req->startDate;
        $endDate=$req->endDate;

        $companyName = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->first();

        return view('pos/report/posPurchaseReturnReport/posPurchaseReturnReportTable',['posPurchaseReturnReports'=>$posPurchaseReturnReports,'startDate'=>$startDate,'endDate'=>$endDate,'companyName'=>$companyName]);
    }


}