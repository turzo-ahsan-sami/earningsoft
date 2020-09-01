<?php

namespace App\Http\Controllers\pos\report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\pos\PosSales;
use App\pos\PosSalesDetails;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use App\Service\DatabasePartitionHelper;

class PosSalesProfitReportController extends Controller
{
    public function salesWiseProfitReport(Request $request)
    {   
        $data['companyName'] = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->first();
        $data['currentDate'] = Carbon::now();

        if(!$request)
        {
            $data['posSalesReports'] = array();
        }
        else
        {   
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));

            $dbhelper = new DatabasePartitionHelper();
            $pos_product = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_product', 'pd');
            $pos_sales = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_sales', 'ps');

            $posAllSales = DB::table($pos_sales)->where('ps.companyId', Auth::user()->company_id_fk); 
            
            $posSalesReports = $posAllSales
                            ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'ps.id')
                            ->Join($pos_product, 'pos_sales_details.productId', '=', 'pd.id')
                            ->where('ps.salesDate','>=', $startDate)
                            ->where('ps.salesDate','<=', $endDate)
                            ->select('ps.*','pos_sales_details.*','pd.name as productName', 'pd.costPrice as productCostPrice')
                            ->orderBy('ps.salesDate','ASC')
                            ->orderBy('ps.saleBillNo','ASC')
                            ->orderBy('ps.id','DESC')
                            ->get();
		
            $salesDate = ""; 
            $saleBillNo = "";
            foreach($posSalesReports as $sales){
                $sales->salesDate != $salesDate ? $salesDate = $sales->salesDate : $sales->salesDate = "";
                $sales->saleBillNo != $saleBillNo ? $saleBillNo = $sales->saleBillNo : $sales->saleBillNo = "";
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
            }
    
            $data['posSalesReports'] = $posSalesReports;
        }
            
        
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;
        
        return view('pos/report/salesWiseProfitReport',$data);

    }

    public function invoiceWiseProfitReport(Request $request)
    {
        $data['companyName'] = DB::table('gnr_company')->where('id', Auth::user()->company_id_fk)->first();
        $data['currentDate'] = Carbon::now();

        if(!$request)
        {
            $data['posSalesReports'] = array();
        }
        else
        {   
            $startDate = date('Y-m-d', strtotime($request->startDate));
            $endDate = date('Y-m-d', strtotime($request->endDate));

            $dbhelper = new DatabasePartitionHelper();
            $pos_product = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_product', 'pd');
            $pos_sales = $dbhelper->getPartitionWiseDBTableNameForJoin('pos_sales', 'ps');

            $posAllSales = DB::table($pos_sales)->where('ps.companyId', Auth::user()->company_id_fk);
            

            $posAllSales = $posAllSales->where('ps.salesDate','>=', $startDate)
                                        ->where('ps.salesDate','<=', $endDate)
                                        ->orderBy('ps.salesDate','ASC')
                                        ->orderBy('ps.id','ASC')
                                        ->get();

            foreach($posAllSales as $key => $record)
            {   
                $totalCostPrice = 0;
                $salesDetails = DB::table("pos_sales_details")->where('salesId', $record->id)->get();
                
                foreach($salesDetails as $rec)
                {
                    $productCostPrice = DB::table($pos_product)->where('pd.id', $rec->productId)->select('costPrice')->first();
                    $totalCostPrice += $productCostPrice->costPrice;
                }

                $posAllSales[$key]->totalCostPrice = $totalCostPrice;
            }

            $salesDate = "";            
            foreach($posAllSales as $sales){
                $sales->salesDate != $salesDate ? $salesDate = $sales->salesDate : $sales->salesDate = "";
            }

            for($i = 0; $i < count($posAllSales); $i++){
                $rs1 = 0;
                if($posAllSales[$i]->salesDate){
                    $rs1 = 1;
                    for($j = $i+1; $j < count($posAllSales); $j++){
                        if($posAllSales[$j]->salesDate) break;                    
                        $rs1++;
                    }
                }
                $posAllSales[$i]->daterow = $rs1;                        
            }

            $data['posSalesReports'] = $posAllSales;
        }
        
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;

        return view('pos/report/invoiceWiseProfitReport',$data);
    }
}
