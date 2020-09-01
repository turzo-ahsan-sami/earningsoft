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
        Session::put('branchId', $user->branchId);
        $gnrBranchId = Session::get('branchId');
        $logedUserName = $user->name;
        $purchaseNo = DB::table('pos_purchase_return')->select('id','billNo')->get();
        return view('pos/report/posPurchaseReturnReport/posPurchaseReturnReportForm',['purchaseNo'=>$purchaseNo]);
    }


public function getPurchaseReturnReport(Request $req){
//dd($req->all());
//dd($req->endDate);
$user = Auth::user();
Session::put('branchId', $user->branchId);
$gnrBranchId = Session::get('branchId');
// $startDate=date('Y-m-d', strtotime($req->startDate));
// $endDate=date('Y-m-d', strtotime($req->endDate));

$posAllPurchaseReturn = DB::table('pos_purchase_return');

if($req->billNo > 0)
$posPurchaseReturnReports = $posAllPurchaseReturn->where('pos_purchase_return.id',$req->billNo);
if($req->startDate != null)
$posPurchaseReturnReports = $posAllPurchaseReturn->where('returnDate','>=', $req->startDate);
if($req->endDate != null)
$posPurchaseReturnReports = $posAllPurchaseReturn->where('returnDate','<=', $req->endDate);

 $posPurchaseReturnReports = $posAllPurchaseReturn
        ->Join('pos_purchase_return_details','pos_purchase_return_details.purchaseId', '=',
            'pos_purchase_return.id')
        ->Join('pos_product','pos_product.id', '=','pos_purchase_return.productId')
   
        ->select('pos_purchase_return.*','pos_purchase_return_details.*','pos_product.*')
        ->orderBy('pos_purchase_return.returnDate','DESC')
        ->get();

        //dd($posPurchaseReturnReports);
$startDate=$req->startDate;
$endDate=$req->endDate;

    return view('pos/report/posPurchaseReturnReport/posPurchaseReturnReportTable',['posPurchaseReturnReports'=>$posPurchaseReturnReports,'startDate'=>$startDate,'endDate'=>$endDate]);
}


}