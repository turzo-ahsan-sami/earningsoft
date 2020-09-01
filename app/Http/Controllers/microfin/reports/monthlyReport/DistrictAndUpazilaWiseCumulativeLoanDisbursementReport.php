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

use App\District;
use App\gnr\GnrBranch;
use App\microfin\settings\MfnFundingOrganization;
use App\microfin\loan\MfnLoan;

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

    rsort($UniqueLoanYear);

    $monthsOption = MicroFin::getMonthsOption();

    return view('microfin.reports.monthlyReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportForm', compact('districts',
      'UniqueLoanYear', 'monthsOption'));
  }

  // METHOD'S AND QUERY FOR CALCULATION........................................................................................
  public function districtQuery($requestedDistrict){
    $districtInfos = array();

    if ($requestedDistrict == 'All') {
      $districtInfos = DB::table('gnr_district')
        ->select('id', 'name', 'code')
        // ->where('id', '!=', 17)
        ->get();
    }
    else {
      $districtInfos = DB::table('gnr_district')
        ->select('id', 'name', 'code')
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
        ->where('fundingOrganizationId', '!=', 3)
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

      $ThanaOrupazilaInfos[$districtInfo->id] = DB::select("SELECT DISTINCT(upazilaId) FROM `gnr_working_area` WHERE districtId='$districtInfo->id'");
    }

    return $ThanaOrupazilaInfos;
  }

  public function branchInfos($districtInfos, $LoanProCatInfos, $requestedType, $startDate, $endDate, $fiscalYearStartDate, $ThanaOrupazilaInfos) {

    $branchInfos = array();
    $currentBorrowerInfos = array();
    $currentMembers = array();
    $currentFYBorrowers = array();
    $branchIds = array();
    $Products = array();
    $branches = array();
    $Samities = array();
    $checkSamityArray = array();

    $checkCummulative = 0;
    $checkOpTotal     = 0;
    $checkSamity      = 0;
    $checkTotalCumOp  = 0;

    $checkCurrentBorrowes = array();

    $checkLoanIdsArray = array();
    $countIndex = 0;

    foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfos1) {
      // code...
      foreach ($ThanaOrupazilaInfos1 as $key => $ThanaOrupazilaInfos2) {
        // code...
        foreach ($LoanProCatInfos as $key => $LoanProCatInfos1) {
          // code...
          // $branchInfos[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id] = array();
          $branchInfos[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id]['loanAmount'] = 0;
          $branchInfos[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id]['currentBorrower'] = 0;
          $branchInfos[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id]['currentMember'] = 0;
          $branchInfos[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id]['curentFiscalYearloanAmount'] = 0;
          $branchInfos[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id]['curentFiscalYearBorrower'] = 0;
          $currentBorrowerInfos[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id] = array();
          $currentMembers[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id] = array();
          $currentFYBorrowers[$ThanaOrupazilaInfos2->upazilaId][$LoanProCatInfos1->id] = array();
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
      // START OF LOAN AMOUNT INFORMATION
      // $allkindOfloans = DB::table('mfn_samity as t1')
      //     ->join('mfn_loan as t2','t2.samityIdFk','=','t1.id')
      //     ->join('gnr_working_area as t3','t1.workingAreaId','=','t3.id')
      //     ->join('mfn_loans_product as t4', 't2.productIdFk', '=', 't4.id')
      //     ->where([['t2.softDel', '=', 0], ['t2.disbursementDate', '<=', $endDate]])
      //     ->where(function ($query) use ($endDate) {
      //         $query->where([['closingDate', '>=', $endDate]])
      //         ->orWhere([['closingDate', '=', '0000-00-00']])
      //         ->orWhere([['closingDate', '=', null]]);
      //     })
      //     ->select('t1.workingAreaId','t3.upazilaId','t1.branchId','t2.productIdFk','t2.loanAmount', 't4.productCategoryId',
      //         't2.disbursementDate', 't2.id as loanId', 't2.memberIdFk')
      //     // ->limit(15)
      //     ->get();

      // foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfo) {
      //   foreach ($ThanaOrupazilaInfo as $key => $ThanaOrupazilaInfo1) {

      //     foreach ($allkindOfloans as $key => $allkindOfloan) {
      //       // code...
      //       if ($allkindOfloan->upazilaId == $ThanaOrupazilaInfo1->upazilaId) {
      //         // code...
      //         foreach ($LoanProCatInfos as $key2 => $LoanProCatInfos1) {
      //           // code...
      //           if ($LoanProCatInfos1->id == $allkindOfloan->productCategoryId and $allkindOfloan->disbursementDate <= $endDate) {
      //             // code...
      //             $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['loanAmount'] = $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['loanAmount'] + $allkindOfloan->loanAmount;
      //             array_push($currentBorrowerInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id], $allkindOfloan->memberIdFk);
      //             $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['currentBorrower'] = sizeof(array_unique($currentBorrowerInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]));
      //           }
      //         }
      //       }
      //     }

      //   }
      // }
      // END OF LOAN AMOUNT INFORMATION

      // START OF CURRENT MEMBERS
      foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfo) {
        foreach ($ThanaOrupazilaInfo as $key => $ThanaOrupazilaInfo1) {

          $distinctWorkingAreaId = DB::table('gnr_working_area')
              ->where('upazilaId', $ThanaOrupazilaInfo1->upazilaId)
              ->groupBy('id')
              ->pluck('id')
              ->toArray();

          $distinctSamityId = DB::table('mfn_samity')
              ->where('softDel', '=', 0)
              // ->where([['openingDate', '<=', $endDate]])
              // ->where(function ($query) use ($endDate) {
              //     $query->where([['softDel', '=', 0], ['closingDate', '>=', $endDate]])
              //     ->orWhere([['softDel', '=', 0], ['closingDate', '=', '0000-00-00']])
              //     ->orWhere([['softDel', '=', 0], ['closingDate', '=', null]]);
              // })
              // ->where('branchId', '=', 8)
              ->whereIn('workingAreaId', $distinctWorkingAreaId)
              ->groupBy('id')
              ->pluck('id')
              ->toArray();

          if ($ThanaOrupazilaInfo1->upazilaId == 568) {
              // dd($distinctWorkingAreaId, $distinctSamityId);

          }

          $checkSamity += sizeof($distinctSamityId);

          $dbMembers = DB::table('mfn_member_information')
              ->where(function ($query) use ($endDate) {
                  $query->where([['softDel', '=', 0],['admissionDate', '<=', $endDate], ['closingDate', '>=', $endDate]])
                  ->orWhere([['softDel', '=', 0],['admissionDate', '<=', $endDate], ['closingDate', '=', '0000-00-00']])
                  ->orWhere([['softDel', '=', 0],['admissionDate', '<=', $endDate], ['closingDate', '=', null]]);
              })
              ->whereIn('samityId', $distinctSamityId)
              ->get();

          $primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
              ->where('softDel',0)
              ->whereIn('samityIdFk', $distinctSamityId)
              ->where('transferDate','>',$endDate)
              ->get();

          $primaryProductTransfers = $primaryProductTransfers->sortBy('transferDate')->unique('memberIdFk');

          foreach ($primaryProductTransfers as $key => $primaryProductTransfer) {
              if ($dbMembers->where('id',$primaryProductTransfer->memberIdFk)->first()!=null) {
                  $dbMembers->where('id',$primaryProductTransfer->memberIdFk)->first()->primaryProductId = $primaryProductTransfer->oldPrimaryProductFk;
              }
          }

          // dd($dbMembers);

          foreach ($LoanProCatInfos as $key2 => $LoanProCatInfos1) {

              $primaryProductIds = DB::Table('mfn_loans_product')
                  ->where('productCategoryId', $LoanProCatInfos1->id)
                  ->where('fundingOrganizationId', '!=', 3)
                  // ->where('isPrimaryProduct', '=', 1)
                  ->pluck('id')
                  ->toArray();

              $memberCount = $dbMembers
                  ->whereIn('primaryProductId', $primaryProductIds)
                  ->whereIn('samityId', $distinctSamityId)
                  ->count('id');

              $memberCountArray = $dbMembers
                  ->whereIn('primaryProductId', $primaryProductIds)
                  ->whereIn('samityId', $distinctSamityId)
                  ->pluck('id')
                  ->toArray();


              $cumulativeLoanAmount = DB::table('mfn_loan')
                  ->where([['softDel', '=', 0], ['isFromOpening', '=', 0], ['disbursementDate', '<=', $endDate]])
                  ->whereIn('productIdFk', $primaryProductIds)
                  ->whereIn('samityIdFk', $distinctSamityId)
                  ->sum('loanAmount');

              $checkLoanIds = DB::table('mfn_loan')
                  ->where([['softDel', '=', 0], ['isFromOpening', '=', 0], ['disbursementDate', '=', $endDate]])
                  ->whereIn('productIdFk', $primaryProductIds)
                  ->whereIn('samityIdFk', $distinctSamityId)
                  ->pluck('id')
                  ->toArray();

              $checkLoanIdsArray[++$countIndex] = $checkLoanIds;

              $checkCummulative += $cumulativeLoanAmount;

              $mfnOpeningLoanInfoSamityWise = DB::table('mfn_opening_loan_info_samity_wise')
                  ->where('proCatIdFk', $LoanProCatInfos1->id)
                  ->whereIn('samityIdFk', $distinctSamityId)
                  ->sum('cumLoanAmount');

              $checkOpTotal    += $mfnOpeningLoanInfoSamityWise;
              $checkTotalCumOp += ($cumulativeLoanAmount + $mfnOpeningLoanInfoSamityWise);

              $currentLoanBorrower = DB::table('mfn_loan')
                  ->where([['softDel', '=', 0], ['disbursementDate', '<=', $endDate]])
                  ->where(function ($query) use ($endDate) {
                      $query->where([['loanCompletedDate', '>=', $endDate]])
                      ->orWhere([['loanCompletedDate', '=', '0000-00-00']])
                      ->orWhere([['loanCompletedDate', '=', null]]);
                  })
                  // ->whereIn('primaryProductIdFk', $primaryProductIds)
                  // ->whereIn('samityIdFk', $distinctSamityId)
                  ->whereIn('memberIdFk', $memberCountArray)
                  ->distinct('memberIdFk')
                  ->count('memberIdFk');

              // dd($currentLoanBorrower);

              // if ($ThanaOrupazilaInfo1->upazilaId == 558) {
              //     $two = [7,8,9,10,28,29,47];
              //     $check = [1415];
              //     $currentLoanBorrowers = DB::table('mfn_loan')
              //       ->where([['softDel', '=', 0], ['disbursementDate', '<=', $endDate]])
              //       ->where(function ($query) use ($endDate) {
              //           $query->where([['loanCompletedDate', '>=', $endDate]])
              //           ->orWhere([['loanCompletedDate', '=', '0000-00-00']])
              //           ->orWhere([['loanCompletedDate', '=', null]]);
              //       })
              //       ->whereIn('primaryProductIdFk', $two)
              //       ->whereIn('samityIdFk', $check)
              //       ->groupBy('memberidFk')
              //       ->pluck('memberidFk')
              //       ->toArray();

              //     $workingAreaReCheck =[593];
              //     $distinctSamityIdReCheck = DB::table('mfn_samity')
              //       ->where('openingDate', '<=', $endDate)
              //       ->where('softDel', 1)
              //       ->where(function ($query) use ($endDate) {
              //           $query->where('closingDate', '>=', $endDate)
              //           ->orWhere('closingDate', '=', '0000-00-00')
              //           ->orWhere('closingDate', '=', null);
              //       })
              //       ->whereIn('workingAreaId', $workingAreaReCheck)
              //       // ->groupBy('id')
              //       ->pluck('id')
              //       ->toArray();

              //     dd($endDate, $currentLoanBorrowers, $distinctSamityIdReCheck, $distinctSamityId, $distinctWorkingAreaId, $ThanaOrupazilaInfos);
              // }

              $curentFiscalYearloanAmount = DB::table('mfn_loan')
                  ->where([['softDel', '=', 0], ['disbursementDate', '>=', $fiscalYearStartDate], ['disbursementDate', '<=', $endDate]])
                  // ->where(function ($query) use ($endDate) {
                  //     $query->where([['loanCompletedDate', '>=', $endDate]])
                  //     ->orWhere([['loanCompletedDate', '=', '0000-00-00']])
                  //     ->orWhere([['loanCompletedDate', '=', null]]);
                  // })
                  ->whereIn('primaryProductIdFk', $primaryProductIds)
                  ->whereIn('samityIdFk', $distinctSamityId)
                  ->sum('loanAmount');

              $curentFiscalYearBorrower = DB::table('mfn_loan')
                  ->where([['softDel', '=', 0], ['disbursementDate', '>=', $fiscalYearStartDate], ['disbursementDate', '<=', $endDate]])
                  // ->where(function ($query) use ($endDate) {
                  //     $query->where([['loanCompletedDate', '>=', $endDate]])
                  //     ->orWhere([['loanCompletedDate', '=', '0000-00-00']])
                  //     ->orWhere([['loanCompletedDate', '=', null]]);
                  // })
                  ->whereIn('primaryProductIdFk', $primaryProductIds)
                  ->whereIn('samityIdFk', $distinctSamityId)
                  ->distinct('memberIdFk')
                  ->count('memberIdFk');

              $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['loanAmount']    = $cumulativeLoanAmount + $mfnOpeningLoanInfoSamityWise;
              $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['currentMember'] = $memberCount;
              $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['currentBorrower'] = $currentLoanBorrower;
              $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['curentFiscalYearloanAmount'] = $curentFiscalYearloanAmount;
              $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['curentFiscalYearBorrower'] = $curentFiscalYearBorrower;

              $mfnOpeningLoanInfoSamityWise = 0;
              $cumulativeLoanAmount         = 0;
          }

        }
      }
      
      $checkOpTotal1 = 0;
      foreach ($LoanProCatInfos as $key => $LoanProCatInfos1) {
          $checkOpTotal1 += DB::table('mfn_opening_loan_info_samity_wise')
              ->where('proCatIdFk', $LoanProCatInfos1->id)
              ->sum('cumLoanAmount');
      }
      // dd($checkCummulative, $checkOpTotal, $checkOpTotal1, $checkTotalCumOp, $ThanaOrupazilaInfos, $branchInfos);
      // END OF CURRENT MEMBERS

      // START OF CURRENT FISCAL YEAR LOAN DUSBURSMENT INFORMATION
      // $allkindOfloans = DB::table('mfn_samity as t1')
      //     ->join('mfn_loan as t2','t2.samityIdFk','=','t1.id')
      //     ->join('gnr_working_area as t3','t1.workingAreaId','=','t3.id')
      //     ->join('mfn_loans_product as t4', 't2.productIdFk', '=', 't4.id')
      //     ->where([['t2.softDel', '=', 0], ['t2.disbursementDate', '>=', $fiscalYearStartDate], ['t2.disbursementDate', '<=', $endDate]])
      //     ->where(function ($query) use ($endDate) {
      //         $query->where([['closingDate', '>=', $endDate]])
      //         ->orWhere([['closingDate', '=', '0000-00-00']])
      //         ->orWhere([['closingDate', '=', null]]);
      //     })
      //     ->select('t1.workingAreaId','t3.upazilaId','t1.branchId','t2.productIdFk','t2.loanAmount', 't4.productCategoryId',
      //         't2.disbursementDate', 't2.id as loanId', 't2.memberIdFk')
      //     // ->limit(15)
      //     ->get();

      // foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfo) {
      //   foreach ($ThanaOrupazilaInfo as $key => $ThanaOrupazilaInfo1) {

      //     foreach ($allkindOfloans as $key => $allkindOfloan) {
      //       // code...
      //       if ($allkindOfloan->upazilaId == $ThanaOrupazilaInfo1->upazilaId) {
      //         // code...
      //         foreach ($LoanProCatInfos as $key2 => $LoanProCatInfos1) {
      //           // code...
      //           if ($LoanProCatInfos1->id == $allkindOfloan->productCategoryId and $allkindOfloan->disbursementDate <= $endDate) {
      //             // code...
      //             $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['curentFiscalYearloanAmount'] = $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['curentFiscalYearloanAmount'] + $allkindOfloan->loanAmount;
      //             array_push($currentFYBorrowers[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id], $allkindOfloan->memberIdFk);
      //             $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]['curentFiscalYearBorrower'] = sizeof(array_unique($currentFYBorrowers[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id]));
      //           }
      //         }
      //       }
      //     }

      //   }
      // }
      // END OF CURRENT FISCAL YEAR LOAN DUSBURSMENT INFORMATION
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
            if ($allkindOfloan->upazilaId == $ThanaOrupazilaInfo1->upazilaId) {
              // code...
              foreach ($LoanProCatInfos as $key2 => $LoanProCatInfos1) {
                // code...
                if ($LoanProCatInfos1->id == $allkindOfloan->productIdFk and $allkindOfloan->disbursementDate <= $endDate) {
                  // code...
                  $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id] = $branchInfos[$ThanaOrupazilaInfo1->upazilaId][$LoanProCatInfos1->id] + $allkindOfloan->loanAmount;
                }
              }
            }
          }

        }
      }

    }
    // dd($branchInfos, $currentMembers, $currentBorrowerInfos, $allkindOfloans, $allkindOfMembers, $fiscalYearStartDate);
    // dd($checkSamity);
    // $checkOnlyLoanIds = array();
    // $checkLoanIdsArray = array_filter($checkLoanIdsArray);
    // foreach ($checkLoanIdsArray as $checkLoanIdsArrayValue1) {
    //   foreach ($checkLoanIdsArrayValue1 as $checkLoanIdsArrayValue2) {
    //     $checkOnlyLoanIds[] = $checkLoanIdsArrayValue2;
    //   }
    // }
    // dd($checkOnlyLoanIds);

    return $branchInfos;
  }


  // MAIN(getReport) METHOD TO CALL............................................................................................
  public function getReport(Request $request){
    // REQUESTED DATA VARIABLES................................................................................................
    $requestedDistrict = $request->searchDistrict;
    $requestedYear = $request->searchYear;
    $requestedmonth = $request->searchMonth;
    $requestedType = 1;
    $poRows = 0;

    // DECLARED ARRAY..........................................................................................................
    $districtInfos = array();
    $LoanProCatInfos = array();
    $ThanaOrupazilaInfos = array();
    $branchInfos = array();
    $currentBorrowerInfos = array();

    // CREATED DATE............................................................................................................
    $endDate = date("Y-m-t", strtotime('01-'.$requestedmonth.'-'.$requestedYear));
    // $endDate = date('Y-m-d', strtotime('14-02-2019'));
    $crateDate = date_create("01-$requestedmonth-$requestedYear");
    $startDate = date_format($crateDate,"Y-m-d");

    $fiscalYearStartDate = DB::table('gnr_fiscal_year')
        ->where([['fyStartDate', '<=', $endDate], ['fyEndDate', '>=', $endDate]])
        ->value('fyStartDate');

    // Check is there any samity id fk problem has or not!!
    // $checkSamityDifference = array();
    // $checkSamityDifferenceCount = 0;

    // $allLoanee = DB::table('mfn_loan')
    //     ->where([['softDel', 0], ['disbursementDate', '<=', $endDate]])
    //     ->where( function ($query) use ($endDate) {
    //         $query->where('loanCompletedDate', '>=', $endDate)
    //               ->orWhere('loanCompletedDate', '=', '0000-00-00')
    //               ->orWhere('loanCompletedDate', '=', null);
    //     })
    //     ->select('id', 'memberIdFk', 'samityIdFk', 'primaryProductIdFk')
    //     ->get();

    // foreach ($allLoanee as $key => $allLoaneeValue) {
      
    //   foreach ($allLoanee as $key => $allLoaneeValues) {
    //     if ((($allLoaneeValue->memberIdFk == $allLoaneeValues->memberIdFk) and ($allLoaneeValue->samityIdFk != $allLoaneeValues->samityIdFk)) || (($allLoaneeValue->memberIdFk == $allLoaneeValues->memberIdFk) and ($allLoaneeValue->primaryProductIdFk != $allLoaneeValues->primaryProductIdFk))) {
    //       $checkSamityDifference[$checkSamityDifferenceCount]['id']         = $allLoaneeValue->memberIdFk;
    //       $checkSamityDifference[$checkSamityDifferenceCount]['memberIdFk'] = $allLoaneeValue->memberIdFk;
    //       $checkSamityDifference[$checkSamityDifferenceCount]['samityIdFk'] = $allLoaneeValue->samityIdFk;
    //       $checkSamityDifference[$checkSamityDifferenceCount]['primaryProductIdFk'] = $allLoaneeValue->samityIdFk;
    //     }
    //   }
      
    // }

    // dd($checkSamityDifference);

    // USE OF METHOD'S AND QUERY TO CALL.......................................................................................
    $districtInfos = $this->districtQuery($requestedDistrict);

    $LoanProCatInfos = $this->LoanProCatInfosQuery($requestedType);

    $ThanaOrupazilaInfos = $this->thanaOrupazilaInfosQuery($districtInfos);

    // dd($ThanaOrupazilaInfos);

    foreach ($ThanaOrupazilaInfos as $key => $ThanaOrupazilaInfos1) {
      // code...
      foreach ($ThanaOrupazilaInfos1 as $key => $ThanaOrupazilaInfos2) {
          ++$poRows;
      }
    }

    $branchInfos = $this->branchInfos($districtInfos, $LoanProCatInfos, $requestedType, $startDate, $endDate, $fiscalYearStartDate, $ThanaOrupazilaInfos);

    // dd($branchInfos);

    // dd($districtInfos, $LoanProCatInfos, $ThanaOrupazilaInfos, $branchInfos);

    return view('microfin.reports.monthlyReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportViews.districtAndUpazilaWiseCumulativeLoanDisbursementReportTable', compact('districtInfos',
      'requestedDistrict', 'startDate', 'endDate', 'LoanProCatInfos', 'ThanaOrupazilaInfos', 'branchInfos', 'currentBorrowerInfos', 'requestedDistrict', 'poRows'));
  }


}
