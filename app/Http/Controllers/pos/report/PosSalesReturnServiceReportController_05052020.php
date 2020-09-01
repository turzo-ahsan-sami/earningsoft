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

class PosSalesReturnServiceReportController extends Controller
{
    public function index(Request $req){
    //dd($req->all());
	   
      $data['customerNames'] = PosCustomer::all();
      $posAllSales = DB::table('pos_sales_return');
      $data['companyName'] =DB::table('gnr_company')->orderBy('id','DESC')->first();
      $data['currentDate'] = Carbon::now();
    if($req->customerTypeId == 'all' && $req->salesTypeId == 'all' && $req->startDate == null && $req->end == null){
      $data['posSalesReports'] = $posAllSales
                                 ->Join('pos_sales_return_details','pos_sales_return_details.salesId', '=', 'pos_sales_return.id')
                                ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales_return.customerId')
                                ->join('pos_product', 'pos_sales_return_details.productId', '=', 'pos_product.id')
                                ->select('pos_sales_return.*','pos_sales_return_details.*','pos_customer.name as customerName','pos_product.name as productName')
                                ->orderBy('pos_sales_return.returnDate','DESC')
                                ->get();
        }elseif($req->customerTypeId > 0 || $req->salesTypeId > 0 && $req->startDate != null && $req->endDate != null){
            if($req->salesTypeId == 'all')  $req->salesTypeId = 0;
			if($req->customerTypeId == 'all') $req->customerTypeId = 0;
			
          $data['posSalesReports'] = $posAllSales
                                   ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales_return.customerId')
                                    ->Join('pos_sales_return_details','pos_sales_return_details.salesId', '=', 'pos_sales_return.id')
                                    ->join('pos_product', 'pos_sales_return_details.productId', '=', 'pos_product.id')
                                    ->where('customerId',$req->customerTypeId)
                                    ->where('billNo',$req->salesTypeId)
                                    ->where('returnDate','>=',$req->startDate)
                                    ->where('returnDate','<=',$req->endDate)
                                    ->select('pos_sales_return.*','pos_sales_return_details.*','pos_customer.name as customerName','pos_product.name as productName')
                                  
                                    ->orderBy('pos_sales_return.returnDate','DESC')
                                    ->get();
        }elseif($req->customerTypeId == 'all' && $req->salesTypeId == 'all'  && $req->startDate != null && $req->endDate != null){
            $data['posSalesReports'] = $posAllSales
                                      ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales_return.customerId')
                                      ->Join('pos_sales_return_details','pos_sales_return_details.salesId', '=', 'pos_sales_return.id')
                                      ->join('pos_product', 'pos_sales_return_details.productId', '=', 'pos_product.id')
                                      ->where('returnDate','>=',$req->startDate)
                                      ->where('returnDate','<=',$req->endDate)
                                      ->select('pos_sales_return.*','pos_sales_return_details.*','pos_customer.name as customerName','pos_product.name as productName')
                                    
                                      ->orderBy('pos_sales_return.returnDate','DESC')
                                      ->get();
        }else{
            if($req->startDate == '' || $req->endDate == '')
			{
				$req->startDate = '0000-00-00';
				$req->endDate = '0000-00-00';
			}
          $data['posSalesReports'] = $posAllSales
                                    ->Join('pos_sales_return_details','pos_sales_return_details.salesId', '=', 'pos_sales_return.id')
                                    ->join('pos_customer', 'pos_customer.id', '=', 'pos_sales_return.customerId')
                                    ->where('customerId',$req->customerTypeId)
                                    ->where('billNo',$req->salesTypeId)
                                    ->where('returnDate',$req->startDate)
                                    ->where('returnDate',$req->endDate)
                                     ->join('pos_product', 'pos_sales_return_details.productId', '=', 'pos_product.id')
                                    ->select('pos_sales_return.*','pos_sales_return_details.*','pos_customer.name as customerName','pos_product.name as productName')
                            
                                    ->orderBy('pos_sales_return.returnDate','DESC')
                                    ->get();
        }
     	  
          $data['startDate'] = $req->startDate;
          $data['endDate'] = $req->endDate;
        
		      return view('pos/report/salesReturnServiceReport',$data);
    }


    public function getSellsBillNoCustomer(Request $request){
        $sales = DB::table('pos_sales_return')->where('customerId',$request->customerId)->get();
        //dd($sales);
         return response()->json($sales);
    }

    public function posSalesNServiceFilteringResult(Request $request){
        //dd($request->all());
    }
  
}