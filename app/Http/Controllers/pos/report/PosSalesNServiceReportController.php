<?php
namespace App\Http\Controllers\pos\report;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosSales;
use App\pos\PosCollection;
use App\pos\PosSalesDetails;
use App\pos\PosCustomer;
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

class PosSalesNServiceReportController extends Controller
{
    public function index(Request $req){

        $dbhelper = new DatabasePartitionHelper();

        $pos_sales = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_sales', 'ps');
        $posAllSales = DB::table($pos_sales)->where('ps.companyId', Auth::user()->company_id_fk);
        
        $startDate = date('Y-m-d', strtotime($req->startDate));
        $endDate = date('Y-m-d', strtotime($req->endDate));

        if($req->customerTypeId != 'all') $posAllSales = $posAllSales->where('customerId', $req->customerTypeId);
        if($req->salesTypeId != 'all') $posAllSales = $posAllSales->where('saleBillNo', $req->salesTypeId);
        if($req->startDate != null) $posAllSales = $posAllSales->where('salesDate','>=', $startDate);
        if($req->endDate != null) $posAllSales = $posAllSales->where('salesDate','<=', $endDate);
                
        $pos_product = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_product', 'pd');
        
        $posSalesReports = $posAllSales
        ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'ps.id')
        ->join('pos_customer', 'pos_customer.id', '=', 'ps.customerId')
        ->Join($pos_product, 'pd.id', '=','pos_sales_details.productId')    
        ->select('ps.*','pos_sales_details.*', 'pos_customer.name as customerName','pd.name as productName')
        ->orderBy('ps.salesDate','ASC')
        ->orderBy('ps.saleBillNo','ASC')
        ->orderBy('pd.id','ASC')
        ->orderBy('ps.id','DESC')
        ->get();
        

        $salesDate = ""; 
        $saleBillNo = "";
        $customerName = "";
        foreach($posSalesReports as $sale){
            $sale->salesDate != $salesDate ? $salesDate = $sale->salesDate : $sale->salesDate = "";
            $sale->saleBillNo != $saleBillNo ? $saleBillNo = $sale->saleBillNo : $sale->saleBillNo = "";
            $sale->customerName != $customerName ? $customerName = $sale->customerName : $sale->customerName = "";
        }

        for($i = 0; $i < count($posSalesReports); $i++){
            $rs1 = 0;
            if($posSalesReports[$i]->salesDate){
                $rs1 = 1;
                for($j = $i+1; $j < count($posSalesReports); $j++){
                    if($posSalesReports[$j]->salesDate) break;                    
                    $rs1++;
                }
            }
            $posSalesReports[$i]->daterow = $rs1;
            
            $rs2 = 0;
            if($posSalesReports[$i]->saleBillNo){
                $rs2 = 1;
                for($j = $i+1; $j < count($posSalesReports); $j++){
                    if($posSalesReports[$j]->saleBillNo) break;                    
                    $rs2++;
                }
            }
            $posSalesReports[$i]->billrow = $rs2;
            
            $rs3 = 0;
            if($posSalesReports[$i]->customerName){
                $rs3 = 1;
                for($j = $i+1; $j < count($posSalesReports); $j++){
                    if($posSalesReports[$j]->customerName) break;                    
                    $rs3++;
                }
            }
            $posSalesReports[$i]->customerrow = $rs3;
        }

                   
        $data['posSalesReports'] = $posSalesReports;
        // $data['customerNames'] = PosCustomer::all();
        $data['customerNames'] = DB::table("pos_customer")->where('companyId', Auth::user()->company_id_fk)->get();
        $data['companyName'] = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->first();
        $data['currentDate'] = Carbon::now();
        $data['startDate'] = $req->startDate;
        $data['endDate'] = $req->endDate;
        
		return view('pos/report/posSaleReport/salesNServiceReport', $data);
    }


    public function getBillNoCustomer(Request $request){
        $partitionName = DatabasePartitionHelper::getUserWisePartitionName();
        $sales = DB::table(DB::raw("pos_sales PARTITION ($partitionName)"))->where('customerId', $request->customerId)->get();
        return response()->json($sales);
    }

    
  
}