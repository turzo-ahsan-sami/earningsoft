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

class PosSalesProfitReportController extends Controller
{
    public function salesWiseProfitReport(Request $request)
    {   
        dd( Auth::user());
        $posAllSales = DB::table('pos_sales');
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

            $data['posSalesReports'] = $posAllSales
                                        ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'pos_sales.id')
                                        ->join('pos_product', 'pos_sales_details.productId', '=', 'pos_product.id')
                                        ->where('salesDate','>=', $startDate)
                                        ->where('salesDate','<=', $endDate)
                                        ->select('pos_sales.*','pos_sales_details.*','pos_product.name as productName', 'pos_product.costPrice as productCostPrice')
                                        ->orderBy('pos_sales.salesDate','DESC')
                                        ->orderBy('pos_sales.id','DESC')
            							->get();
		
        }
        	

        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;

        return view('pos/report/salesWiseProfitReport',$data);

    }

    public function invoiceWiseProfitReport(Request $request)
    {
        $posAllSales = DB::table('pos_sales');
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

            $posAllSales = $posAllSales->where('salesDate','>=', $startDate)
                                        ->where('salesDate','<=', $endDate)
                                        ->orderBy('pos_sales.salesDate','DESC')
                                        ->orderBy('pos_sales.id','DESC')
                                        ->get();

            foreach($posAllSales as $key => $record)
            {   
                $totalCostPrice = 0;
                $salesDetails = DB::table("pos_sales_details")->where('salesId', $record->id)->get();
                
                foreach($salesDetails as $rec)
                {
                    $productCostPrice = DB::table("pos_product")->where('id', $rec->productId)->select('costPrice')->first();
                    $totalCostPrice += $productCostPrice->costPrice;
                }

                $posAllSales[$key]->totalCostPrice = $totalCostPrice;
            }

            $data['posSalesReports'] = $posAllSales;
        }
        	
        $data['startDate'] = $request->startDate;
        $data['endDate'] = $request->endDate;

        return view('pos/report/invoiceWiseProfitReport',$data);
    }
}
