<?php

namespace App\Http\Controllers\microfin\reports\MfnFieldOfficerReport;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use App\Traits\GetSoftwareDate;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\microfin\MicroFinance;

class MfnFieldOfficerReport extends Controller {
    use GetSoftwareDate;
    protected $MicroFinance;


    public function __construct() {
        $this->MicroFinance = New MicroFinance;
    }

    public function index(Request $request){

        $fundingOrgArray  = array();
        $branchArray      = array();
        $productCtgArray  = array();
        $productArray     = array();

        $userBranchId = Auth::user()->branchId;
        $id = $request->searchBranch;
        $branchId = DB::table('gnr_branch')
                    ->where('id',$id)
                    ->first();
                    // return dd($branchId);

        /// Branch
        if ($userBranchId!=1) {
            $branchList = DB::table('gnr_branch')
                          ->where('id', $userBranchId);
        }

        // selected Branch
        
        if($request->searchBranch == null) {

            $branchSelected = null;
            $branchArray = DB::table('gnr_branch')
                          ->pluck('id')
                          ->toArray();
        }

        else {
       
            $branchSelected = $request->searchBranch;
            array_push($branchArray,$request->searchBranch);
        }

        $savingsProducts = DB::table('mfn_saving_product')
                            ->select('shortName','id')
                            ->get();

        /// Field Officer
        $fieldofficer = DB::table('hr_emp_general_info')
                      ->join('mfn_samity','hr_emp_general_info.id','=','mfn_samity.fieldOfficerId')
                        ->select('hr_emp_general_info.emp_name_english','hr_emp_general_info.emp_id','hr_emp_general_info.id','mfn_samity.id')
                        ->get();

        $categories = DB::table('mfn_loans_product_category')
                         // ->select('id','shortName')
                         ->get();
        // Products
        $products = DB::table('mfn_loans_product')
                ->join('mfn_funding_organization','mfn_loans_product.fundingOrganizationId','=','mfn_funding_organization.id')  
                ->select('mfn_loans_product.id','mfn_loans_product.shortName','mfn_funding_organization.name')
                ->get();

    $searchBranchs  = DB::table('gnr_branch')
                     ->select('id','name','branchCode')
                     ->orderBy('branchCode')
                     ->get();

    $savingsProducts = DB::table('mfn_saving_product')
                        ->select('shortName','id')
                        ->get();
                        
        /// Year
        $yearsOption  = $this->MicroFinance->getYearsOption();

        // /// Month
        $monthsOption = $this->MicroFinance->getMonthsOption();

       $data = array( 
           'searchBranchs'    => $searchBranchs,
           // 'branchList'       => $branchList,
           'branchId'         => $branchId,
           'branchSelected'   => $branchSelected,
           'userBranchId'     => $userBranchId,
           'fieldofficer'     => $fieldofficer,
           'products'         => $products,
           'categories'       => $categories,
           'yearsOption'      => $yearsOption,
           'monthsOption'     => $monthsOption,
        );

        return view('microfin.reports.mfnFieldOfficerReport.mfnfield',$data);
       
    }
    
// AJax Filter Request
    public function ChangeFilter(Request $request){

        // $searchProductCtg  = (int)json_decode($request->changeProduct);
        $branchId = (int)json_decode($request->branchId);

        if ($request->branchId =="") {

             $branchId = DB::table('hr_emp_general_info')
                      ->join('mfn_samity','hr_emp_general_info.id','=','mfn_samity.fieldOfficerId')
                      ->select('hr_emp_general_info.emp_name_english','hr_emp_general_info.emp_id','hr_emp_general_info.id','mfn_samity.fieldOfficerId')
                      ->get();
        }

    else{
          $branchId =DB::table('hr_emp_general_info')
                    ->join('mfn_samity','hr_emp_general_info.id','=','mfn_samity.fieldOfficerId')
                    ->select('hr_emp_general_info.emp_name_english','hr_emp_general_info.emp_id','mfn_samity.fieldOfficerId')
                    ->where('mfn_samity.branchId',$branchId)
                    ->groupBy('mfn_samity.fieldOfficerId')
                    ->get();

    }
        
        $data = array( 
           'branchId' => $branchId,
        );
        
        return response()->json($data);
    }

    // SearchProductCtg
   public function SearchProductCtg(Request $request){

         $CatId  = (int)json_decode($request->CatId);
         

        if ($request->CatId == "0") {
            $productCtg =  DB::table('mfn_loans_product_category')
                           ->get();

            $product = DB::table('mfn_loans_product')
                      ->join('mfn_funding_organization','mfn_loans_product.fundingOrganizationId','=','mfn_funding_organization.id')  
                      ->select('mfn_loans_product.id','mfn_loans_product.shortName','mfn_funding_organization.name')
                      ->get();
        }
        else{
         
            $product =  DB::table('mfn_loans_product')
                    ->join('mfn_funding_organization','mfn_loans_product.fundingOrganizationId','=','mfn_funding_organization.id')  
                    ->select('mfn_loans_product.id','mfn_loans_product.shortName','mfn_funding_organization.name')
                    ->where('mfn_loans_product.productCategoryId',$CatId)
                    ->get();
        }

        $data = array( 
           'product'      => $product,
           // 'productCtg'   => $productCtg,
        );

         return response()->json($data);
   }


// Week Day Change

    public function getWeekDays(Request $request){

        $year    = (int)json_decode($request->year);    
        $month   = str_pad($request->filmonth, 2, '0',STR_PAD_LEFT);

        $monthFirstDate = Carbon::parse($year.'-'.$month.'-01');
        $monthLastDate  = $monthFirstDate->copy()->endOfMonth();

        $result = array();

           // return $monthLastDate;
     
          while ($monthFirstDate->lte($monthLastDate)) {

            $firstDate  = DB::table('mfn_setting_holiday')
                        ->where('date','>=',$monthFirstDate->format('Y-m-d'))
                        ->where('date','<=',$monthLastDate->format('Y-m-d'))
                        ->where('isWeeklyHoliday',1)
                        ->min('date');

            $nextHoliday  = DB::table('mfn_setting_holiday')
                            ->where('date','>=',$monthFirstDate->format('Y-m-d'))
                            ->where('date','<=',$monthLastDate->format('Y-m-d'))
                            ->where('isWeeklyHoliday',1)
                            ->where('date','>',$firstDate)
                            ->min('date');

            $secondDate   = Carbon::parse($nextHoliday)->subDay()->format('Y-m-d');

            $optionString = Carbon::parse($firstDate)
                        ->format('d-m-Y').' to '.Carbon::parse($secondDate)
                        ->format('d-m-Y');

            array_push($result,$optionString);

            $monthFirstDate =  Carbon::parse($nextHoliday);
        }
        
        $data = array(

            'result' => $result,

        );
                
        return response()->json($data);
    }


// Report Filtering

    public function getReport(Request $req){
        
        $filBranchIds = $this->getFilteredBranhIds($req->searchBranch,$req->fieldofficer,$req->productcategory,$req->filMonth,$req->filYear);

        $branchId       = $req->searchBranch;
        $fieldofficerid = $req->fieldofficer;
        $product        = $req->product;
        $filMonth       = $req->filMonth;
        $filYear        = $req->filYear;
        $service        = $req->service;
        $week           = $req->week;
        $branchArray    = array();
        


          // $loanProductsInfoOfBranch=DB::table('mfn_loans_product')
          // ->whereIn('id', $loanProductIdsOfBranchArr)
          // ->whereIn('fundingOrganizationId', $fundingOrgArr)
          // ->where('softDel', 0)
          // ->select('id','name','productCategoryId')
          // ->get();

      // return $fieldofficerid;

        $year    = (int)json_decode($req->filYear);    
        $month   = str_pad($req->filMonth, 2, '0',STR_PAD_LEFT);

        $monthFirstDate = Carbon::parse($year.'-'.$month.'-01');
        $monthLastDate  = $monthFirstDate->copy()->endOfMonth()->format('Y-m-d');

        // return $monthFirstDate;
        // return $monthLastDate;

        $result = array();

           // return $monthLastDate;
        
     // selected Branch
     
        if($req->searchBranch == null) {
            $branchSelected = null;
            $branchArray = DB::table('gnr_branch')
                          ->pluck('id')
                          ->toArray();
        }

        else {
       
            $branchSelected = $req->searchBranch;
            array_push($branchArray,$req->searchBranch);
        }

                /// Field Officer
        $fieldofficer = DB::table('hr_emp_general_info')
                      ->join('mfn_samity','hr_emp_general_info.id','=','mfn_samity.fieldOfficerId')
                        ->select('hr_emp_general_info.emp_name_english','hr_emp_general_info.emp_id','hr_emp_general_info.id','mfn_samity.id','mfn_samity.fieldOfficerId','mfn_samity.name','mfn_samity.code','hr_emp_general_info.emp_id')  
                        ->where('mfn_samity.fieldofficerid',$fieldofficerid)
                        ->first();

         $savingsProducts  = DB::table('mfn_saving_product')
                              ->select('shortName')
                              ->get();

        $samityId   = DB::table('mfn_samity')
                     ->where('branchId',$branchId)
                     ->select('id','name','code')
                     ->first();

         $allsamity = DB::table('mfn_samity')
                     ->join('hr_emp_general_info','mfn_samity.fieldOfficerId','=','hr_emp_general_info.id')
                     ->select('mfn_samity.id','mfn_samity.fieldOfficerId','mfn_samity.name','mfn_samity.code','hr_emp_general_info.emp_name_english','hr_emp_general_info.emp_id')
                     ->where('mfn_samity.fieldofficerid',$fieldofficerid)
                     // ->where('branchId',$branchId)
                     ->get();

            // return $allsamity;

        $productcategory = DB::table('mfn_loans_product_category')
                          ->select('id','shortName')
                          ->get();

        // product
        $product = DB::table('mfn_loans_product')
                        ->select('id','shortName','name')
                        ->where('id',$product)
                        ->first();

        $branchList = DB::table('gnr_branch')
                        ->select('branchCode','id','name','address')
                        ->where('id',$branchId)
                        ->first();

// return $week;
        $data = array(
    
            'branchList'         => $branchList,
            'fieldofficer'       => $fieldofficer,
            'branchSelected'     => $branchSelected,
            'product'            => $product,
            'savingsProducts'    => $savingsProducts,
            'allsamity'          => $allsamity,
            'service'            => $service,
            'year'               => $year,
            'month'              => $month,
            'week'               => $week,
            'monthLastDate'      => $monthLastDate,
            'monthFirstDate'     => $monthFirstDate,
        );

  
         if ($req->service == 1) {

      return view('microfin.reports.mfnFieldOfficerReport.mfnFieldreport1',$data);
        
        }

        else {

         return view('microfin.reports.mfnFieldOfficerReport.mfnFieldreport2',$data);

        }
       
}

    public function getFilteredBranhIds($filBranch){
        $userBranchId = Auth::user()->branchId;
        // $userBranchName = Auth::user()->branchName;
        
        if ($userBranchId!=1) {
            $filBranchIds = [$userBranchId];
        }

        
        return $filBranchIds;
    }

    

}
