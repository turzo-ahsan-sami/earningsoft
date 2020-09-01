<?php

namespace App\Http\Controllers\microfin\reports\mfnFieldOfficerReportControllerNew;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
//
// use Illuminate\Http\Request;
// use App\Http\Requests;
// use Validator;
// use Response;
// use App\Traits\GetSoftwareDate;
// use Illuminate\Support\Facades\Input;
// use Illuminate\Support\Facades\Hash;
// use App\Http\Controllers\Controller;

/**
 *
 */
class MfnFieldOfficerReportControllerNew extends Controller
{

  public function getBranchName()
  {
    $ProductCategorys = DB::table('mfn_loans_product_category')->get();
    $Products = DB::table('mfn_loans_product')->get();

    $BranchDatas = DB::table('gnr_branch')->get();                                                          //getting all the branch value from database

    $BranchOpeningYears = DB::table('gnr_branch')
                            ->select(DB::raw('DISTINCT YEAR(branchOpeningDate) as branchOpeningDate'))     //raw SQL have used for getting the distinct year from database
                            ->get();
    return view('microfin.reports.mfnFieldOfficerReport.mfnFieldreport1_new', compact('BranchDatas', 'BranchOpeningYears', 'ProductCategorys', 'Products'));
  }

  public function getBranchOfficer(Request $request){
    $Officerdatas = DB::table('mfn_samity')
                      ->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
                      ->select('mfn_samity.branchCode', 'mfn_samity.branchId', 'mfn_samity.id', 'hr_emp_general_info.id', 'hr_emp_general_info.emp_name_english')
                      ->get();

    return response()->json($Officerdatas);
  }

  public function getWeekDatas(Request $request){

    $monthFirstDate = Carbon::parse($request->years.'-'.$request->months.'-01');
    $monthLastDate  = $monthFirstDate->copy()->endOfMonth();

    $result = array();

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
                    ->where('date', '>', $firstDate)
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

  public function getFieldOfficerReport(Request $request){
    $samityMembers = array();
    $WeeklySavings = array();
    $MonthlySavings = array();
    $SomriddhySavings = array();
    $OneTimeSavings = array();
    $SavingsProducts1 = array();
    $disbursmentDates = array();
    $SavingsProduct = 0;
    $dateFirst = '';
    $dateSecond = '';

    $FOid = $request->fieldofficer;
    $Interest = $request->savingsinterest;
    $BranchNameID =  $request->searchBranch;
    $Component = $request->productCtg;
    $Product = $request->product;
    $date = $request->weekDays;
    $ProductCategorys = $request->productCtg;
    $ProductTypes = $request->product;
    $dates = explode(" ",$date);

    for ($i=0; $i < sizeof($dates); $i++) {
      if($i == 0){
        $dateFirst = $dates[$i];
      }
      elseif ($i == 2) {
        $dateSecond = $dates[$i];
      }
    }
    // dd($dateSecond);
    $dateformate1 = strtotime($dateFirst);
    $newdateformate1 = date('Y-m-d', $dateformate1);
    $dateformate2 = strtotime($dateSecond);
    $newdateformate2 = date('Y-m-d', $dateformate2);
    // dd($newdateformate);

    $BName = DB::table('gnr_branch')->where('id', $BranchNameID)->first();

    if($Component<=20){
      $CName = DB::table('mfn_loans_product_category')->where('id', $Component)->first();
    }
    else{
      $CName = $Component;
    }

    $fieldofficerByBranch = DB::table('mfn_samity')
                                ->select(DB::raw('DISTINCT(fieldOfficerId) as foid'))
                                ->where('branchId', $BranchNameID)
                                ->get();
    //dd($fieldofficerByBranch);

    $fieldofficerInfos = DB::table('hr_emp_general_info')
                            ->where('id', $FOid)
                            ->first();
    $ShamityInfos = DB::table('mfn_samity')
                            ->where('fieldOfficerId', $FOid)
                            ->get();
    // dd($fieldofficerInfos);
    $MemberInfos = array();
    foreach ($ShamityInfos as $ShamityInfo) {
      $ShamityInfo1 = $ShamityInfo->id;
      $ShamityInfo2 = $ShamityInfo->branchId;
      $MemberInfos[] = DB::table('mfn_member_information')
                              ->where('samityId', $ShamityInfo1)
                              ->where('branchId', $ShamityInfo2)
                              ->count();
    }
    //dd($MemberInfos);
    //dd($ShamityInfo1);

    $ProductNames = DB::table('mfn_saving_product')
                              ->select('name')
                              ->get();
    $DynamicRow1 = DB::table('mfn_saving_product')->count();
    $DynamicRow1a = $DynamicRow1 + 1;

    $SavingsProducts = DB::table('mfn_saving_product')
                                ->select('id')
                                ->get();
    //dd($SavingsProducts);
    $SavingsProductsCount = DB::table('mfn_saving_product')
                                    ->select('id')
                                    ->count();
    $SavingsProductsIDFk = DB::table('mfn_savings_account')
                                    ->select(DB::raw('DISTINCT savingsProductIdFk'))
                                    ->get();
    //dd($SavingsProductsIDFk);

    /*Start of savings Column Programs*/
    $ShamityInfosIds = DB::table('mfn_samity')
                            ->where('fieldOfficerId', $FOid)
                            ->pluck('id')
                            ->toArray();
    $BranchInfoIds = DB::table('mfn_samity')
                            ->where('fieldOfficerId', $FOid)
                            ->pluck('branchId')
                            ->toArray();

    // dd($BranchInfoIds);
    // dd($ShamityInfosIds);
    $spId1=1;
    $spId2=2;
    $spId3=3;
    $spId4=4;

    $SCount = 1;
    $BCount = 1;

    $SCount1 = 1;
    $BCount1 = 1;

    $SCount2 = 1;
    $BCount2 = 1;

    $SCount3 = 1;
    $BCount3 = 1;
    foreach ($ShamityInfosIds as $ShamityInfosId) {
      foreach ($BranchInfoIds as $BranchInfoId) {
        if($SCount == $BCount){
          $WeeklySavings[] = DB::table('mfn_savings_deposit')
                                ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'), 'productIdFk', 'samityIdFk')
                                ->where('samityIdFk', $ShamityInfosId)
                                ->where('productIdFk', $spId1)
                                ->where('branchIdFk', $BranchInfoId)
                                ->get();
        }
        $BCount = $BCount + 1;
      }
      $SCount = $SCount + 1;
      $BCount = 1;
    }
    //dd($WeeklySavings);

    foreach ($ShamityInfosIds as $ShamityInfosId) {
      foreach ($BranchInfoIds as $BranchInfoId) {
        if($SCount1 == $BCount1){
          $MonthlySavings[] = DB::table('mfn_savings_deposit')
                                ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'), 'productIdFk', 'samityIdFk')
                                ->where('samityIdFk', $ShamityInfosId)
                                ->where('productIdFk', $spId2)
                                ->where('branchIdFk', $BranchInfoId)
                                ->get();
        }
        $BCount1 = $BCount1 + 1;
      }
      $SCount1 = $SCount1 + 1;
      $BCount1 = 1;
    }
    //dd($MonthlySavings);

    foreach ($ShamityInfosIds as $ShamityInfosId) {
      // $SomriddhySavings[] = DB::table('mfn_savings_deposit')
      //                       ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'), 'productIdFk', 'samityIdFk')
      //                       // ->select(DB::raw('SUM(mfn_savings_account.savingsAmount)'), 'samityIdFk')
      //                       // ->select('samityIdFk')
      //                       // ->join('mfn_samity', 'mfn_savings_account.samityIdFk', '=', 'mfn_samity.id')
      //                       ->join('mfn_samity', 'mfn_savings_deposit.branchIdFk', '=', 'mfn_samity.branchId')
      //                       ->join('mfn_saving_product', 'mfn_savings_deposit.productIdFk', '=', 'mfn_saving_product.id')
      //                       ->where('samityIdFk', $ShamityInfosId)
      //                       ->where('productIdFk', $spId3)
      //                       ->get();
      foreach ($BranchInfoIds as $BranchInfoId) {
        if($SCount2 == $BCount2){
          $SomriddhySavings[] = DB::table('mfn_savings_deposit')
                                ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'), 'productIdFk', 'samityIdFk')
                                ->where('samityIdFk', $ShamityInfosId)
                                ->where('productIdFk', $spId3)
                                ->where('branchIdFk', $BranchInfoId)
                                ->get();
        }
        $BCount2 = $BCount2 + 1;
      }
      $SCount2 = $SCount2 + 1;
      $BCount2 = 1;
    }
    //dd($SomriddhySavings);

    foreach ($ShamityInfosIds as $ShamityInfosId) {
      foreach ($BranchInfoIds as $BranchInfoId) {
        if($SCount3 == $BCount3){
          $OneTimeSavings[] = DB::table('mfn_savings_deposit')
                                ->select(DB::raw('SUM(mfn_savings_deposit.amount) as Total'), 'productIdFk', 'samityIdFk')
                                ->where('samityIdFk', $ShamityInfosId)
                                ->where('productIdFk', $spId4)
                                ->where('branchIdFk', $BranchInfoId)
                                ->get();
        }
        $BCount3 = $BCount3 + 1;
      }
      $SCount3 = $SCount3 + 1;
      $BCount3 = 1;
    }
    // dd($OneTimeSavings);
    /*End of savings Column Programs*/

    foreach ($ShamityInfosIds as $ShamityInfosId) {
      $disbursmentDates[] = DB::table('mfn_loan')
                              ->select('disbursementDate', 'loanAmount', 'samityIdFk')
                              ->where('disbursementDate', '>=', $newdateformate1)
                              ->where('disbursementDate', '<=', $newdateformate2)
                              ->where('loanTypeId', $ProductCategorys)
                              ->where('productIdFk', $ProductTypes)
                              ->where('branchIdFk', $BranchNameID)
                              ->where('samityIdFk', $ShamityInfosId)
                              ->get();
    }
    dd($disbursmentDates);

    if ($Interest == 1) {
      return view('microfin.reports.mfnFieldOfficerReport.mfnFieldreport1_new_table', compact('fieldofficerInfos', 'ShamityInfos', 'samityMembers', 'ProductNames', 'DynamicRow1a', 'BName', 'CName', 'MemberInfos', 'WeeklySavings', 'MonthlySavings', 'SomriddhySavings', 'OneTimeSavings'));
    }
    elseif ($Interest == 2) {
      return view('microfin.reports.mfnFieldOfficerReport.mfnFieldreport1_new_Anothertable');

    }
  }

}
