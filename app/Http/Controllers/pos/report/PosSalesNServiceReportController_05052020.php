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
    	//dd($req->all());
      $data['customerNames'] = PosCustomer::all();
      $posAllSales = DB::table('pos_sales');
      $data['companyName'] =DB::table('gnr_company')->orderBy('id','DESC')->first();
      $data['currentDate'] = Carbon::now();
      //dd($data);
    if($req->customerTypeId == 'all' && $req->salesTypeId == 'all' && $req->startDate == null && $req->end == null){
      $data['posSalesReports'] = $posAllSales
                                ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'pos_sales.id')
                                ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales.customerId')
                                ->join('pos_product', 'pos_sales_details.productId', '=', 'pos_product.id')
                                ->select('pos_sales.*','pos_sales_details.*','pos_customer.name as customerName','pos_product.name as productName')
                                ->orderBy('pos_sales.salesDate','DESC')
                                ->get();
                                //dd($data);
        }elseif($req->customerTypeId > 0 || $req->salesTypeId > 0 && $req->startDate != null && $req->endDate != null){
			
			if($req->salesTypeId == 'all')  $req->salesTypeId = 0;
			if($req->customerTypeId == 'all') $req->customerTypeId = 0;
			
          	$data['posSalesReports'] = $posAllSales
                                   ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales.customerId')
                                    ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'pos_sales.id')
                                    ->join('pos_product', 'pos_sales_details.productId', '=', 'pos_product.id')
                                    ->where('customerId',$req->customerTypeId)
                                    ->where('saleBillNo',$req->salesTypeId)
                                    ->where('salesDate','>=',$req->startDate)
                                    ->where('salesDate','<=',$req->endDate)
                                    ->select('pos_sales.*','pos_sales_details.*','pos_customer.name as customerName','pos_product.name as productName')
                                   
                                    ->orderBy('pos_sales.salesDate','DESC')
                                    ->get();
                                    //dd($data);
        }elseif($req->customerTypeId == 'all' && $req->salesTypeId == 'all'  && $req->startDate != null && $req->endDate != null){
            $data['posSalesReports'] = $posAllSales
                                      ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales.customerId')
                                      ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'pos_sales.id')
                                      ->join('pos_product', 'pos_sales_details.productId', '=', 'pos_product.id')
                                      ->where('salesDate','>=',$req->startDate)
                                      ->where('salesDate','<=',$req->endDate)
                                      ->select('pos_sales.*','pos_sales_details.*','pos_customer.name as customerName','pos_product.name as productName')
                                      ->orderBy('pos_sales.salesDate','DESC')
                                      ->get();
        }else{
			if($req->startDate == '' || $req->endDate == '')
			{
				$req->startDate = '0000-00-00';
				$req->endDate = '0000-00-00';
			}

          	$data['posSalesReports'] = $posAllSales
                                    ->Join('pos_sales_details','pos_sales_details.salesId', '=', 'pos_sales.id')
                                    ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales.customerId')
                                    ->join('pos_product', 'pos_sales_details.productId', '=', 'pos_product.id')
                                    ->where('customerId',$req->customerTypeId)
                                    ->where('saleBillNo',$req->salesTypeId)
                                    ->where('salesDate','>=', $req->startDate)
                                    ->where('salesDate','<=', $req->endDate)
                                    ->select('pos_sales.*','pos_sales_details.*','pos_customer.name as customerName','pos_product.name as productName')
                                    ->orderBy('pos_sales.salesDate','DESC')
									->get();
			
		}
        $data['startDate'] = $req->startDate;
        $data['endDate'] = $req->endDate;
        //dd($data);
		return view('pos/report/salesNServiceReport',$data);
    }


    public function getBillNoCustomer(Request $request){
        $sales = DB::table('pos_sales')->where('customerId',$request->customerId)->get();
        //dd($sales);
         return response()->json($sales);
    }

    public function posSalesNServiceFilteringResult(Request $request){
        //dd($request->all());
    }
  
}