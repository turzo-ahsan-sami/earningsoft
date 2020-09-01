<?php

namespace App\Http\Controllers\microfin\reports\monthlyReport;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

use App\Http\Controllers\microfin\MicroFin;

//
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Validator;
// use Response;
// use App\Traits\GetSoftwareDate;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Controller;

class DistrictAndUpazilaWiseCumulativeLoanDisbursementReport extends Controller
{
  public function getBranchName(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();
    $districts = array();

    $BranchDatas = DB::table('gnr_branch')->get();
    $FundingOrganization = DB::table('mfn_funding_organization')->get();
    $LoanDatas = DB::table('mfn_loan')
                   ->select(DB::raw('EXTRACT(YEAR FROM disbursementDate) as Year'))
                   ->get();

    $districts = DB::table('district')->get();

    foreach ($LoanDatas as $key => $LoanData) {
      $LoanYear[] = $LoanData->Year;
    }
    $LoanYearArray = array($LoanYear);

    foreach ($LoanYearArray as $key => $LoanYearArrays) {
      // foreach ($LoanYearArrays as $key => $LoanYearArrayss) {
        $UniqueLoanYear = array_unique($LoanYearArrays);
      // }
    }

    $monthsOption = MicroFin::getMonthsOption();

    return view('microfin.reports.monthlyReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportForm', compact('districts',
      'UniqueLoanYear', 'monthsOption'));
  }

  // METHOD'S AND QUERY FOR CALCULATION........................................................................................
  public function districtQuery($requestedDistrict){
    $districtInfos = array();

    if ($requestedDistrict == 'All') {
      $districtInfos = DB::table('district')
        ->select('id', 'district_name')
        ->where('id', '!=', 17)
        ->get();
    }
    else {
      $districtInfos = DB::table('district')
        ->select('id', 'district_name')
        ->where('id', $requestedDistrict)
        ->get();
    }

    return $districtInfos;
  }

  public function LoanProCatInfosQuery($requestedType){
    $LoanProCatInfos = array();

    if ($requestedType == 1) {
      $LoanProCatInfos = DB::table('mfn_loans_product_category')
        ->get();
    }
    elseif ($requestedType == 2) {
      $LoanProCatInfos = DB::table('mfn_loans_product')
        ->get();
    }

    return $LoanProCatInfos;
  }

  public function thanaOrupazilaInfosQuery($districtInfos) {
    $ThanaOrupazilaInfos = array();

    foreach ($districtInfos as $key => $districtInfo) {
      // $ThanaOrupazilaInfos[$districtInfo->id] = DB::table('gnr_working_area')
      //   ->where('districtId', $districtInfo->id)
      //   ->groupBy('branchId')
      //   ->count('id');

      $ThanaOrupazilaInfos[$districtInfo->id] = DB::select("SELECT DISTINCT(upazilaId) AS
        totalNumberOfBranch FROM `gnr_working_area` WHERE districtId='$districtInfo->id'");
    }

    return $ThanaOrupazilaInfos;
  }

  public function branchInfos($districtInfos, $LoanProCatInfos, $requestedType, $startDate, $endDate, $ThanaOrupazilaInfos) {
    // dd($endDate);
    $branchInfos = array();
    $currentBorrowerInfos = array();
    $branchIds = array();
    $Products = array();
    $branches = array();
    $Samities = array();

    foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfos1) {
      // code...
      foreach ($ThanaOrupazilaInfos1 as $key => $ThanaOrupazilaInfos2) {
        // code...
        foreach ($LoanProCatInfos as $key => $LoanProCatInfos1) {
          // code...
          $branchInfos[$ThanaOrupazilaInfos2->totalNumberOfBranch][$LoanProCatInfos1->id] = 0;
        }

      }
    }

    foreach ($districtInfos as $key => $districtInfo) {
      $branchIds[$districtInfo->id] = DB::table('gnr_working_area')
        ->select('branchId')
        ->where('districtId', $districtInfo->id)
        ->groupBy('branchId')
        // ->count('id');
        ->get();
    }

    // dd($ThanaOrupazilaInfos);

    if ($requestedType == 1) {
      $allkindOfloans = DB::table('mfn_samity as t1')
                                    ->join('mfn_loan as t2','t2.samityIdFk','=','t1.id')
                                    ->join('gnr_working_area as t3','t1.workingAreaId','=','t3.id')
                                    ->join('mfn_loans_product as t4', 't2.productIdFk', '=', 't4.id')
                                    ->where([['t2.softDel', '=', 0], ['t2.disbursementDate', '<=', $endDate]])
                                    ->where(function ($query) use ($endDate) {
                                        $query->where([['closingDate', '>=', $endDate]])
                                        ->orWhere([['closingDate', '=', '0000-00-00']])
                                        ->orWhere([['closingDate', '=', null]]);
                                    })
                                    ->select('t1.workingAreaId','t3.upazilaId','t1.branchId','t2.productIdFk','t2.loanAmount', 't4.productCategoryId', 't2.disbursementDate', 't2.id as loanId', 't2.memberIdFk')
                                    ->get();

      foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfo) {
        foreach ($ThanaOrupazilaInfo as $key => $ThanaOrupazilaInfo1) {

          foreach ($allkindOfloans as $key => $allkindOfloan) {
            // code...
            if ($allkindOfloan->upazilaId == $ThanaOrupazilaInfo1->totalNumberOfBranch) {
              // code...
              foreach ($LoanProCatInfos as $key2 => $LoanProCatInfos1) {
                // code...
                if ($LoanProCatInfos1->id == $allkindOfloan->productCategoryId and $allkindOfloan->disbursementDate <= $endDate) {
                  // code...
                  $branchInfos[$ThanaOrupazilaInfo1->totalNumberOfBranch][$LoanProCatInfos1->id]= $branchInfos[$ThanaOrupazilaInfo1->totalNumberOfBranch][$LoanProCatInfos1->id] + $allkindOfloan->loanAmount;
                }
              }
            }
          }

        }
      }

    }
    else {

      $allkindOfloans = DB::table('mfn_samity as t1')
                                    ->join('mfn_loan as t2','t2.samityIdFk','=','t1.id')
                                    ->join('gnr_working_area as t3','t1.workingAreaId','=','t3.id')
                                    // ->join('mfn_loans_product as t4', 't2.productIdFk', '=', 't4.id')
                                    ->where([['t2.softDel', '=', 0], ['t2.disbursementDate', '<=', $endDate]])
                                    ->where(function ($query) use ($endDate) {
                                        $query->where([['closingDate', '>=', $endDate]])
                                        ->orWhere([['closingDate', '=', '0000-00-00']])
                                        ->orWhere([['closingDate', '=', null]]);
                                    })
                                    ->select('t1.workingAreaId','t3.upazilaId','t1.branchId','t2.productIdFk','t2.loanAmount', 't2.disbursementDate')
                                    ->get();

      foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfo) {
        foreach ($ThanaOrupazilaInfo as $key => $ThanaOrupazilaInfo1) {

          foreach ($allkindOfloans as $key => $allkindOfloan) {
            // code...
            if ($allkindOfloan->upazilaId == $ThanaOrupazilaInfo1->totalNumberOfBranch) {
              // code...
              foreach ($LoanProCatInfos as $key2 => $LoanProCatInfos1) {
                // code...
                if ($LoanProCatInfos1->id == $allkindOfloan->productIdFk and $allkindOfloan->disbursementDate <= $endDate) {
                  // code...
                  $branchInfos[$ThanaOrupazilaInfo1->totalNumberOfBranch][$LoanProCatInfos1->id] = $branchInfos[$ThanaOrupazilaInfo1->totalNumberOfBranch][$LoanProCatInfos1->id] + $allkindOfloan->loanAmount;
                }
              }
            }
          }

        }
      }

    }
    // dd($branchInfos, $allkindOfloans);

    return $branchInfos;
  }


  // MAIN(getReport) METHOD TO CALL............................................................................................
  public function getReport(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $requestedDistrict = $request->searchDistrict;
    $requestedYear = $request->searchYear;
    $requestedmonth = $request->searchMonth;
    $requestedType = $request->searchProCat;

    // DECLARED ARRAY..........................................................................................................
    $districtInfos = array();
    $LoanProCatInfos = array();
    $ThanaOrupazilaInfos = array();
    $branchInfos = array();
    $currentBorrowerInfos = array();

    // CREATED DATE............................................................................................................
    // $endDate = Carbon::parse('01-'.$requestedmonth.'-'.$requestedYear)->endOfMonth()->format('Y-m-d');
    $endDate = date("Y-m-t", strtotime('01-'.$requestedmonth.'-'.$requestedYear));
    $crateDate = date_create("01-$requestedmonth-$requestedYear");
    $startDate = date_format($crateDate,"Y-m-d");

    // USE OF METHOD'S AND QUERY TO CALL.......................................................................................
    $districtInfos = $this->districtQuery($requestedDistrict);

    $LoanProCatInfos = $this->LoanProCatInfosQuery($requestedType);

    $ThanaOrupazilaInfos = $this->thanaOrupazilaInfosQuery($districtInfos);

    $branchInfos = $this->branchInfos($districtInfos, $LoanProCatInfos, $requestedType, $startDate, $endDate, $ThanaOrupazilaInfos);

    // dd($ThanaOrupazilaInfos);
    // return 'OK!';
    return view('microfin.reports.monthlyReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportTable', compact('districtInfos',
      'requestedDistrict', 'startDate', 'endDate', 'LoanProCatInfos', 'ThanaOrupazilaInfos', 'branchInfos', 'currentBorrowerInfos', 'requestedDistrict'));
  }


}
