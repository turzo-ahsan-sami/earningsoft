<?php
namespace App\Http\Controllers\pos\report;
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

use App\Service\DatabasePartitionHelper;

class PosCollectionReportController extends Controller
{
    public function index(Request $req) {
    	
	    $user = Auth::user();
        Session::put('branchId', $user->branchId);
        $gnrBranchId        = Session::get('branchId');
        $logedUserName      = $user->name;

        $posCollectionReports  = DB::table('pos_collection');

        if($req->clientCompanyId > 0)
        	$posCollectionReport = $posCollectionReports->where('clientCompanyId',$req->clientCompanyId);

        if($req->salesTypeId > 0)
            $posCollectionReport = $posCollectionReports->where('salesType',$req->salesTypeId);

        if($req->salesBillNo > 0)
        	$posCollectionReport = $posCollectionReports->where('salesBillNo',$req->salesBillNo);

        if($req->startDate != null)
    		$posCollectionReport = $posCollectionReports->where('collectionDate','>=',$req->startDate);

    	if($req->endDate != null)
    		$posCollectionReport = $posCollectionReports->where('collectionDate','<=',$req->endDate);

    	$posCollectionReport  = $posCollectionReports->orderBy('collectionDate', 'DESC')->orderBy('installmentNo')->get();
    				
        return view('pos/report/posCollectionReport',['posCollectionReport'=>$posCollectionReport,'selectedBillNo'=>$req->salesBillNo]);
    }

    public function salesBillNoFillter(Request $req) {
      $partitionName = DatabasePartitionHelper::getUserWisePartitionName();

      //*
        if(($req->salesTypeId > 0) && ($req->clientCompanyId > 0)) {
          $companySalesBillNo = DB::table(DB::raw("pos_sales PARTITION ($partitionName)"))->where('salesType',$req->salesTypeId)->where('companyId',$req->clientCompanyId)->select('salesBillNo')->get();  
        } else if($req->clientCompanyId > 0) {
        $companySalesBillNo = DB::table(DB::raw("pos_sales PARTITION ($partitionName)"))->where('companyId',$req->clientCompanyId)->select('salesBillNo')->get();
        } else if($req->salesTypeId > 0) {
          $companySalesBillNo = DB::table(DB::raw("pos_sales PARTITION ($partitionName)"))->where('salesType',$req->salesTypeId)->select('salesBillNo')->get();  
        }
      /*/

      /*
        if(($req->salesTypeId > 0) && ($req->clientCompanyId > 0)) {
          $companySalesBillNo = DB::table('pos_sales')->where('salesType',$req->salesTypeId)->where('companyId',$req->clientCompanyId)->select('salesBillNo')->get();  
        } else if($req->clientCompanyId > 0) {
        $companySalesBillNo = DB::table('pos_sales')->where('companyId',$req->clientCompanyId)->select('salesBillNo')->get();
        } else if($req->salesTypeId > 0) {
          $companySalesBillNo = DB::table('pos_sales')->where('salesType',$req->salesTypeId)->select('salesBillNo')->get();  
        }
      */
        return response()->json($companySalesBillNo);

    }


}