<?php

namespace App\Http\Controllers\microfin\reports\branchManagerReportController;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;

class BranchManagerReportController extends Controller
{
  public function getBranchName(){
    $LoanYear       = array();
    $LoanYearArray  = array();
    $UniqueLoanYear = array();

    $BranchDatas      = DB::table('gnr_branch')->get();
    $ProductCatDatas  = DB::table('mfn_loans_product_category')->get();

    $LoanDatas = DB::table('mfn_loan')
                   ->select(DB::raw('EXTRACT(YEAR FROM disbursementDate) as Year'))
                   ->get();

    foreach ($LoanDatas as $key => $LoanData) {
      $LoanYear[] = $LoanData->Year;
    }
    $LoanYearArray = array($LoanYear);

    foreach ($LoanYearArray as $key => $LoanYearArrays) {
      // foreach ($LoanYearArrays as $key => $LoanYearArrayss) {
        $UniqueLoanYear = array_unique($LoanYearArrays);
      // }
    }
    // // dd($UniqueLoanYear);

    return view('microfin.reports.branchManagerReportViews.BranchManagerReportForm', compact('BranchDatas', 'ProductCatDatas', 'UniqueLoanYear'));

  }

  public function getProduct(Request $request){
    // $BranchId = $request->id;
    $ProductID = $request->id;

    $ProductList = array();

    if ($ProductID != 'All') {
      $ProductList = DB::table('mfn_loans_product')
                    ->where('productCategoryId', $ProductID)
                    ->get();
    }
    elseif ($ProductID == 'All') {
      $ProductList = DB::table('mfn_loans_product')
                    ->get();
    }

    return response()->json($ProductList);

  }

  public function getWeek(Request $request){
    $RequestYear = $request->getYear;
    $RequestMonth = $request->getMonth;

    $Date = $RequestYear.'-'.$RequestMonth.'-1';
    $DateConv = strtotime($Date);
    $FixedDate = date("Y-m-d", $DateConv);
    $FormatedfixedDate = DateTime::createFromFormat('Y-m-d', $Date);
    $FormatedfixedDay = $FormatedfixedDate->format('D');
    // // $AllTheDays[] = cal_days_in_month ( CAL_GREGORIAN , $RequestMonth , $RequestYear );

    $list = array();
    $Week = array();
    $TotalDay = cal_days_in_month(CAL_GREGORIAN, $RequestMonth, $RequestYear);
    $WeekList = array();
    $WeekArray = array();

    for($d=1; $d<=31; $d++)
    {
        $time=mktime(12, 0, 0, $RequestMonth, $d, $RequestYear);
        if (date('m', $time)==$RequestMonth)
            $list[]=date('Y-m-d-D', $time);
    }
    // // dd($list);
    $Count = 0;
    foreach ($list as $key => $lists) {
        $Count = $Count + $key;

      if (substr($lists,11) == 'Sat' || substr($lists,11) == 'Thu' ) {
        $Week[] = $lists;
      }
      elseif (substr($lists,11) != 'Fri' and $Count == 0) {
        $Week[] = $lists;
        // $Count = 1;
      }
      elseif (($TotalDay-1) == $key and substr($lists,11) != 'Fri') {
        $Week[] = $lists;
      }

     if ($key == 0 and substr($lists,11) == 'Thu') {
       $Week[] = $lists;
     }

     if (($TotalDay-1) == $key and substr($lists,11) == 'Sat') {
       $Week[] = $lists;
     }
    }
    // // dd($Week);
    foreach ($Week as $key => $Weeks) {
      $WeekList[] = substr($Weeks, 0, 10);
    }

    $StartDate = '';
    $EndDate = '';
    foreach ($WeekList as $key => $WeekLists) {
      if ($StartDate == null) {
        $StartDate = $WeekLists;
      }
      elseif ($EndDate == null) {
        $EndDate = $WeekLists;
      }

      if ($StartDate != null and $EndDate != null) {
        $WeekArray[] = $StartDate.' to '.$EndDate;
        $StartDate = '';
        $EndDate = '';
      }
    }

    return response()->json($WeekArray);
  }

  public function getAllEmployee($branch,$dateArray){

    $dateFrom = $dateArray[0];
    $employeeArray = array();
    $employeeArray = DB::table('hr_emp_org_info')
              ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id','hr_settings_position.name as PositionName','hr_emp_org_info.emp_id_fk')
              ->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', '=', 'hr_settings_position.id')
              ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
              ->where([['hr_emp_org_info.branch_id_fk', $branch], ['hr_emp_org_info.joining_date', '<=', $dateFrom]])
              ->get()
              ->keyBy('emp_id_fk')->toArray();
              //->toSQL();
              //->get();
              //dd($employeeArray);

    return $employeeArray;
  }

  public function BranchManagerQuery($branch,$dateArray){
    $Manager = array();

    //GET ALL BRANCH MANAGER OF THE BRANCH
    $employeeManagerArray = DB::table('hr_emp_org_info')
                            ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id','hr_settings_position.name as PositionName','hr_emp_org_info.emp_id_fk')
                            ->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', '=', 'hr_settings_position.id')
                            ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
                            ->where([['hr_emp_org_info.branch_id_fk', $branch], ['hr_emp_org_info.joining_date', '<=', $dateArray[0]], ['hr_settings_position.name', '=', 'Branch Manager']])
                            ->get()
                            ->keyBy('emp_id_fk'); 
                           
    $employeeIdArray  = array_column($employeeManagerArray->toArray(),'emp_id_fk');
    //dd($employeeManagerArray);

    if(count($employeeManagerArray) > 1){
      //check if in transfer table
      $ManagerInTransfer = DB::table('hr_transfer')
                        ->select('hr_transfer.*','users.emp_id_fk')
                        ->join('users', 'users.id', '=', 'hr_transfer.users_id_fk')
                        ->whereIn('users.emp_id_fk',$employeeIdArray)
                        ->where([['hr_transfer.cur_branch_id_fk', $branch], ['hr_transfer.effect_month', '<=', $dateArray[0]]])
                        ->get()
                        ->keyBy('emp_id_fk')->toArray();
                        // ->toSQL();
                        //->get();
                        //dd($employeeArray);

      if(count($ManagerInTransfer) > 0){
        foreach ($ManagerInTransfer as $key => $value) {
          $Manager = $employeeManagerArray->where('emp_id_fk',$key);
        }

        return $Manager;
      }
      
    }elseif(count($employeeManagerArray) == 1){
        return $employeeManagerArray;
    }

    //CHECK IF ANYONE IN ACTING AS BRANCH MANAGER
    $ActingBenefit = DB::table('hr_acting_benefit')
                  ->join('users', 'users.id', '=', 'hr_acting_benefit.users_id_fk')
                  ->join('hr_emp_org_info', 'hr_emp_org_info.emp_id_fk', '=', 'users.emp_id_fk')
                  ->join('hr_emp_general_info', 'hr_emp_general_info.id', '=', 'hr_emp_org_info.emp_id_fk') 
                  ->join('hr_settings_position', 'hr_settings_position.id', '=', 'hr_emp_org_info.position_id_fk')       
                  ->where([['hr_emp_org_info.branch_id_fk',$branch], ['hr_settings_position.name', '=', 'Branch Manager']])
                  //->select('hr_acting_benefit.*')
                  ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id','hr_settings_position.name as PositionName','hr_emp_org_info.emp_id_fk')
                  ->get()
                  //->toSQL();
                  ->keyBy('emp_id_fk')->toArray();
    return $ActingBenefit;

  }

  public function FieldOfficerQuery($branch,$dateArray){
    $FieldOfficer = array();
    $date = $dateArray[0];

    $fieldOfficerArray = DB::table('mfn_samity')
              ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id', 'hr_emp_general_info.id', 'mfn_samity.branchId', 'mfn_samity.id as Samity_id')
              ->join('hr_emp_general_info', 'mfn_samity.fieldOfficerId', '=', 'hr_emp_general_info.id')
              //addding  hr_emp_org_info
              ->join('hr_emp_org_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
              ->where([['mfn_samity.branchId', $branch], ['hr_emp_org_info.joining_date', '<=', $dateArray[0]]])
              ->where(function ($query) use ($date) {
                            $query->where('hr_emp_org_info.terminate_resignation_date' ,'>=', $date)
                            ->orWhere('hr_emp_org_info.terminate_resignation_date' ,'=', '0000-00-00')
                            ->orWhere('hr_emp_org_info.terminate_resignation_date' ,'=',  null);
                        })
              ->groupBy('hr_emp_general_info.emp_id')
              //->toSQL();
              ->get()
              ->keyBy('id');

              //dd($fieldOfficerArray,$branch,$dateArray);

    $employeeIdArray  = array_column($fieldOfficerArray->toArray(),'emp_id_fk');

    if(count($fieldOfficerArray) > 1){

      //dd($fieldOfficerArray);
      //check if in transfer table
      $fieldOfficerInTransfer = DB::table('hr_transfer')
                        ->select('hr_transfer.*','users.emp_id_fk')
                        ->join('users', 'users.id', '=', 'hr_transfer.users_id_fk')
                        ->whereIn('users.emp_id_fk',$employeeIdArray)
                        ->where([['hr_transfer.cur_branch_id_fk', $branch], ['hr_transfer.effect_month', '<=', $dateArray[0]]])
                        ->get()
                        ->keyBy('emp_id_fk')->toArray();
                        // ->toSQL();
                        //->get();
                        //dd($fieldOfficerInTransfer);

      if(count($fieldOfficerInTransfer) > 0){
        foreach ($fieldOfficerInTransfer as $key => $value) {
          $FieldOfficer = $fieldOfficerArray->where('emp_id_fk',$key);
        }
        return $FieldOfficer;
      }
      
    }//elseif(count($fieldOfficerArray) == 1){
        return $fieldOfficerArray;
    //}

  }

  public function SamityQuery($fieldOfficers, $RequestedProductCategoryID, $RequestedProductID, $DateArray){
    $SamityIn = array();
    $date     = $DateArray[0];
    $fieldOfficerIdArray = array_column($fieldOfficers->toArray(), 'id');
    $branchIdArray       = array_column($fieldOfficers->toArray(), 'branchId');

    if ($RequestedProductCategoryID == 'All' and $RequestedProductID == 'All') {
      $Samity = DB::table('mfn_samity') 
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select('fieldOfficerId',DB::raw("(COUNT(mfn_samity.id)) as 'samityCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
    }
    elseif ($RequestedProductCategoryID != 'All' and $RequestedProductID == 'All') {
     $primaryProductIdArray = DB::table('mfn_loans_product')
                                ->select('id')
                                ->where('productCategoryId', $RequestedProductCategoryID)
                                ->pluck('id')->toArray();

      $Samity = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->whereIn('mfn_member_information.primaryProductId', $primaryProductIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select('fieldOfficerId',DB::raw("(COUNT(DISTINCT(mfn_samity.id))) as 'samityCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();







     /* $products = DB::table('mfn_loans_product')
                ->where('productCategoryId', $RequestedProductCategoryID)
                ->pluck('id')
                ->toArray();
      foreach ($fieldOfficers as $key => $fieldOfficer) {
        $Fid = $fieldOfficer->id;
        $Bid = $fieldOfficer->branchId;

        $Samity[$Fid] = DB::table('mfn_samity')
                ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                // ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                // ->where([['mfn_samity.branchId', $Bid], ['mfn_samity.fieldOfficerId', $Fid], ['mfn_samity.status', '=', 1],['mfn_samity.softDel', '=', 0], ['mfn_member_information.admissionDate', '<', $DateArray[0]]])
                ->where(function ($query) use ($date, $Fid, $Bid) {
                    $query->where([['mfn_samity.fieldOfficerId', $Fid], ['mfn_samity.branchId', $Bid], ['mfn_member_information.closingDate', '>',  $date], ['mfn_samity.closingDate', '>',  $date], ['mfn_samity.softDel', '=', 0], ['mfn_member_information.admissionDate', '<', $date]])
                    ->orWhere([['mfn_samity.fieldOfficerId', $Fid], ['mfn_samity.branchId', $Bid], ['mfn_member_information.closingDate', '=',  '0000-00-00'], ['mfn_samity.closingDate', '=', '0000-00-00'], ['mfn_samity.softDel', '=', 0], ['mfn_member_information.admissionDate', '<', $date]])
                    ->orWhere([['mfn_samity.fieldOfficerId', $Fid], ['mfn_samity.branchId', $Bid], ['mfn_member_information.closingDate', '=',  null], ['mfn_samity.closingDate', '=',  null], ['mfn_samity.softDel', '=', 0], ['mfn_member_information.admissionDate', '<', $date]]);
                })
                ->whereIn('mfn_member_information.primaryProductId', $products)
                // ->orWhereIn('mfn_loan.productIdFk', $products)
                ->groupBy('mfn_samity.id')
                ->pluck('mfn_samity.id')
                ->toArray();

        // // dd($Samity);
        // return $Samity;
      }

      foreach ($Samity as $key => $Samity1) {
        $SamityIn[$key] = sizeof($Samity1);
      }*/
    }
    else {

      $Samity = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_member_information.primaryProductId', $RequestedProductID)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where('admissionDate', '<=', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select('fieldOfficerId',DB::raw("(COUNT(DISTINCT(mfn_samity.id))) as 'samityCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();


      /*$products = DB::table('mfn_loans_product')
                ->where('productCategoryId', $RequestedProductCategoryID)
                ->pluck('id')
                ->toArray();
                
      foreach ($fieldOfficers as $key => $fieldOfficer) {
        $Fid = $fieldOfficer->id;
        $Bid = $fieldOfficer->branchId;

        $Samity[$Fid] = DB::table('mfn_samity')
                ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                // ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->where([['mfn_samity.branchId', $Bid], ['mfn_samity.fieldOfficerId', $Fid], ['mfn_member_information.primaryProductId', $RequestedProductID], ['mfn_member_information.admissionDate', '<', $DateArray[0]]])
                // ->orWhere([['mfn_samity.branchId', $Bid], ['mfn_samity.fieldOfficerId', $Fid], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_member_information.admissionDate', '<', $DateArray[0]]])
                ->whereIn('mfn_member_information.primaryProductId', $products)
                // ->orWhereIn('mfn_loan.productIdFk', $products)
                ->groupBy('mfn_samity.id')
                ->pluck('mfn_samity.id')
                ->toArray();

        // // dd($Samity);
        // return $Samity;
      }

      foreach ($Samity as $key => $Samity1) {
        $SamityIn[$key] = sizeof($Samity1);
      }*/
    }
    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $Samity)){
         $SamityIn[$value] = $Samity[$value]->samityCount;
      }else{
        $SamityIn[$value] = 0;
      }
    }
//dd($SamityIn);
    return $SamityIn;
  }

  //MEMBER COUNT FOR BEGINNIG OF THE WEEK
  public function MemberQueryBOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID){
    $Samity       = array();
    $Product      = array();
    $Member       = array();
    $MemberCount  = 0;
    $TotalMember  = array();
    $date         = $Date[0];

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {
        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where('admissionDate', '<', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
                    //dd($Member);

      }else{
        $primaryProductIdArray = DB::table('mfn_loans_product')
                                ->select('id')
                                ->where('productCategoryId', $RequestedProductCategoryID)
                                ->pluck('id')->toArray();

        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->whereIn('mfn_member_information.primaryProductId', $primaryProductIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where('admissionDate', '<', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

      }

    }else{

      $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_member_information.primaryProductId', $RequestedProductID)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where('admissionDate', '<', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

    }

    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $Member)){
         $TotalMember[$value] = $Member[$value]->memberCount;
      }else{
        $TotalMember[$value] = 0;
      }
    }

    return $TotalMember;
  }

  public function SavingsProductQueryBOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity = array();
    $SavingsProduct = array();
    $LoanProducts = array();
    $Members = array();
    $Savings = array();
    $Totalsavings = array();

    $date = $Date[0];

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')
        // ->where([['fieldOfficerId', $fId], ['branchId', $FO->branchId], ['closingDate', '>',  1], ['softDel', '=', 0]])
        ->where(function ($query) use ($date, $fId, $bId) {
            $query->where([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '>',  $date], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  '0000-00-00'], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  null], ['softDel', '=', 0]]);
        })
        ->get();
    }

    // // dd($FOs);

    foreach ($Samity as $key1 => $Samity1) {
      foreach ($Samity1 as $key2 => $Samity2) {
        $samityID = $Samity2->id;
        $SavingsProduct[$key1][$Samity2->id] = DB::table('mfn_savings_account')
                  ->select('savingsProductIdFk')
                  // ->where([['samityIdFk', $Samity2->id], ['branchIdFk', $RequestedBranchID], ['accountOpeningDate', '<', $date], ['closingDate']])
                  ->where(function ($query) use ($date, $samityID, $RequestedBranchID) {
                      $query->where([['samityIdFk', $samityID], ['branchIdFk', $RequestedBranchID], ['accountOpeningDate', '<', $date], ['closingDate', '>=', $date]])
                      ->orWhere([['samityIdFk', $samityID], ['branchIdFk', $RequestedBranchID], ['accountOpeningDate', '<', $date], ['closingDate', '=', '0000-00-00']])
                      ->orWhere([['samityIdFk', $samityID], ['branchIdFk', $RequestedBranchID], ['accountOpeningDate', '<', $date], ['closingDate', '=', null]]);
                  })
                  ->groupBy('savingsProductIdFk')
                  ->get();
      }
    }

    //dd($SavingsProduct);
    $LoanProducts = DB::table('mfn_loans_product')->where('productCategoryId', $RequestedProductCategoryID)->get();
    // // dd($LoanProducts);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {

//my code starts
      /* $SavingsProduct = array();
         $SavingsAccount =DB::table('mfn_samity')
                    ->join('mfn_savings_account', 'mfn_samity.id', '=', 'mfn_savings_account.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_savings_account.softDel', '=', 0)
                    ->where(function ($query) use ($date) {
                      $query->where([['accountOpeningDate', '<', $date], ['mfn_savings_account.closingDate', '>', $date]])
                      ->orWhere([['accountOpeningDate', '<', $date], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                      ->orWhere([['accountOpeningDate', '<', $date], ['mfn_savings_account.closingDate', '=', null]]);
                  })
                    ->groupBy('fieldOfficerId','savingsProductIdFk')
                    //->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    //->select('fieldOfficerId',DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),DB::raw("(GROUP_CONCAT(DISTINCT mfn_savings_account.savingsProductIdFk SEPARATOR ',')) as 'savingsproductId'"),DB::raw("(GROUP_CONCAT(DISTINCT mfn_savings_account.id SEPARATOR ',')) as 'savingsId'"))

                    ->select('fieldOfficerId','mfn_savings_account.savingsProductIdFk')
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
                    dd($SavingsAccount);
          if(count($SavingsAccount) > 0){
            foreach ($SavingsAccount as $fieldOfficerId => $value) {
              //dd($value);
              $savingsIdArray = explode(',', $value->savingsId);
              $deposit = DB::table('mfn_savings_deposit')
                              ->where([['softDel', '=', 0],['depositDate', '<', $date]])
                              ->whereIn('accountIdFk',$savingsIdArray)
                              ->sum('amount');

              $openingBalance = DB::table('mfn_opening_savings_account_info')
                              ->whereIn('savingsAccIdFk',$savingsIdArray)
                              ->where([['softDel', '=', 0]])
                              ->sum('openingBalance');

              $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                              ->whereIn('accountIdFk',$savingsIdArray)
                              ->where([['withdrawDate', '<', $date], ['softDel', '=', 0]])
                              ->sum('amount');
              $SavingsProduct[$fieldOfficerId] =  $deposit + $openingBalance - $savingWithdrawAmount;

             

            }

            foreach ($fieldOfficerIdArray as $key => $value) {
              if(array_key_exists($value, $SavingsProduct)){
                 $TotalSavings[$value] = $SavingsProduct[$value];
              }else{
                $TotalSavings[$value] = 0;
              }
            }

            dd($TotalSavings);
             return  $TotalSavings;
          }*/

          //my code ennds

        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

                // WE HAVE REMOVED ACCOUNT OPENING DATE (['accountOpeningDate', '<', $date])
                $savingAccIdArr = DB::table('mfn_savings_account')
                    // ->where([['samityIdFk', $samity->id], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProduct->id]])
                    ->where(function ($query) use ($date, $savingsProductIdFk, $key2) {
                        $query->where([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '>', $date]])
                        ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', '0000-00-00']])
                        ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', null]]);
                    })
                    ->pluck('id')
                    ->toArray();
                // dd($savingAccIdArr);

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                    ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                        ['depositDate', '<', $date]])
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->sum('amount');

                $openingBalance = DB::table('mfn_opening_savings_account_info')
                    ->whereIn('savingsAccIdFk',$savingAccIdArr)
                    ->sum('openingBalance');

                $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->where([['withdrawDate', '<', $date], ['softDel', '=', 0]])
                    ->sum('amount');

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
            }
          }
        }
      }
      elseif ($RequestedProductID != 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              
              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              $savingAccIdArr = DB::table('mfn_savings_account')
                  ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                  ->where(function ($query) use ($date, $savingsProductIdFk, $key2, $RequestedProductID) {
                      $query->where([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>', $date]])
                      ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                      ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                  })
                  ->pluck('mfn_savings_account.id')
                  ->toArray();
              // dd($savingAccIdArr);

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                  ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                      ['depositDate', '<=', $date]])
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->sum('amount');

              $openingBalance = DB::table('mfn_opening_savings_account_info')
                  ->whereIn('savingsAccIdFk',$savingAccIdArr)
                  ->sum('openingBalance');

              $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->where([['withdrawDate', '<=', $date], ['softDel', '=', 0]])
                  ->sum('amount');

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
            }
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              foreach ($LoanProducts as $key => $LoanProduct) {
                
                $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;
                $RequestedProductID1 = $LoanProduct->id;

                $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;
                
                $savingAccIdArr = DB::table('mfn_savings_account')
                      ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                      // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                      ->where(function ($query) use ($date, $savingsProductIdFk, $key2, $RequestedProductID1) {
                          $query->where([['mfn_loan.productIdFk', $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>', $date]])
                          ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                          ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                      })
                      ->pluck('mfn_savings_account.id')
                      ->toArray();
                  // dd($savingAccIdArr);

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                    ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                        ['depositDate', '<=', $date]])
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->sum('amount');

                $openingBalance = DB::table('mfn_opening_savings_account_info')
                    ->whereIn('savingsAccIdFk',$savingAccIdArr)
                    ->sum('openingBalance');

                $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->where([['withdrawDate', '<=', $date], ['softDel', '=', 0]])
                    ->sum('amount');

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
              }
            }
          }
        }
      }
      elseif ($RequestedProductID != 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              $savingAccIdArr = DB::table('mfn_savings_account')
                ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                ->where(function ($query) use ($date, $savingsProductIdFk, $key2, $RequestedProductID) {
                    $query->where([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>', $date]])
                    ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                    ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                })
                ->pluck('mfn_savings_account.id')
                ->toArray();
            // dd($savingAccIdArr);

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                  ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                      ['depositDate', '<=', $date]])
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->sum('amount');

              $openingBalance = DB::table('mfn_opening_savings_account_info')
                  ->whereIn('savingsAccIdFk',$savingAccIdArr)
                  ->sum('openingBalance');

              $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->where([['withdrawDate', '<=', $date], ['softDel', '=', 0]])
                  ->sum('amount');

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
            }
          }
        }
      }
    }

     //dd($Savings);

    return $Savings;
  }

  public function LoanDisburseNoQueryBOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity       = array();
    $MemberCount  = 0;
    $Loans        = array();
    $TotalLoans   = array();
    $date         = $Date[0];

    /*foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }*/

    // // dd($FOs);

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {

        $Loans = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();
        
      }else{

        $ProductArray = DB::table('mfn_loans_product')
                  ->select('id')
                  ->where('productCategoryId', $RequestedProductCategoryID)
                  ->pluck('id')->toArray();
         
        $Loans = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->whereIn('productIdFk', $ProductArray)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();

      }
    }else{

      $Loans = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<', $date)
                    ->where('productIdFk',$RequestedProductID)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

    }
    foreach ($fieldOfficerIdArray as $key => $value) {
          if(array_key_exists($value, $Loans)){
            $TotalLoans[$value] = $Loans[$value]->loanCount;
          }else{
            $TotalLoans[$value] = 0;
          }
        }
        return $TotalLoans;


















    /*if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {

        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;

            $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]]])
                      ->where(function ($query) use ($date, $samityID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        foreach ($Loans as $key => $Loan) {
          $TotalLoans[$key] = array_sum($Loan);
        }
      }
      else {
          // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        foreach ($Loans as $key => $Loan) {
          $TotalLoans[$key] = array_sum($Loan);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
          // code.....
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $RequestedProductID1 = $Products->id;
                $Loans[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $Products->id]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->count('id');
              }
            }
          }


          foreach ($Loans as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoans[$key1] = $MemberCount;
            $MemberCount = 0;
          }
      }
      else {
          // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        foreach ($Loans as $key => $Loan) {
          $TotalLoans[$key] = array_sum($Loan);
        }
      }
    }*/

    

    //dd($TotalLoans);

    return $TotalLoans;

  }

  public function LoanDisburseAmountsQueryBOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanAmounts = array();
    $TotalLoanAmounts = array();

    $date = $Date[0];
    $date = date('Y-m-d', strtotime($date .' -1 day'));

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {
            if ($RequestedServiceCharge == 'WithServiceCharge') {
              $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<=', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
            }else{
               $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<=', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
            }      
      }else{

        $ProductArray = DB::table('mfn_loans_product')
                  ->select('id')
                  ->where('productCategoryId', $RequestedProductCategoryID)
                  ->pluck('id')->toArray();

        if ($RequestedServiceCharge == 'WithServiceCharge') {
          $LoanAmounts = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->whereIn('productIdFk', $ProductArray)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();

        }else{
          $LoanAmounts = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->whereIn('productIdFk', $ProductArray)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();
        }
      }
    }else{
      if ($RequestedServiceCharge == 'WithServiceCharge') {
        $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<', $date)
                    ->where('productIdFk',$RequestedProductID)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
      }else{
        $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<', $date)
                    ->where('productIdFk',$RequestedProductID)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
      }
    }
    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $LoanAmounts)){
        $TotalLoanAmounts[$value] = $LoanAmounts[$value]->loanAmount;
      }else{
        $TotalLoanAmounts[$value] = 0;
      }
    }
    return $TotalLoanAmounts;


    /*foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')
        // ->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])
        ->where(function ($query) use ($date, $fId, $bId) {
            $query->where([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '>',  $date], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  '0000-00-00'], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  null], ['softDel', '=', 0]]);
        })
        ->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            if (sizeof($Samity1) > 0) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('totalrepayAmount');
              }
            }
            else {
              $LoanAmounts[$key1][0] = 0;
            }
            
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            if (sizeof($Samity1) > 0) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('loanAmount');
              }
            }
            else {
              $LoanAmounts[$key1][0] = 0;
            }
            
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
      else {
        //productCategory all but product selected
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {

                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;

                $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $Products->id]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                    })
                    ->sum('totalrepayAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {

                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;

                $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $Products->id]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('loanAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanAmounts;*/

  }

  public function LoanfullyPaidNoQueryBOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity               = array();
    $MemberCount          = 0;
    $LoanFullyPaids       = array();
    $TotalLoanFullyPaids  = array();
    $date                 = $Date[0];
     //dd($FOs);


    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {

        $LoanFullyPaids = DB::table('mfn_samity')
                          ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                          ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                          ->where('mfn_samity.branchId',$RequestedBranchID)
                          ->where('mfn_samity.softDel', '=', 0)
                          ->whereDate('mfn_loan.disbursementDate', '<', $date)
                          ->whereDate('mfn_loan.loanCompletedDate', '<', $date)
                          ->whereDate('mfn_loan.loanCompletedDate', '!=', '0000-00-00')
                          ->where(function ($query) use ($date) {
                                  $query->where('mfn_samity.closingDate', '>',  $date)
                                  ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00');
                           })
                          ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                          ->groupBy('fieldOfficerId')
                          //->toSQL();
                          ->get()
                          ->keyBy('fieldOfficerId')->toArray();
        dd($LoanFullyPaids,$date,$fieldOfficerIdArray,$branchIdArray);

      }else{
        $ProductArray = DB::table('mfn_loans_product')
                  ->select('id')
                  ->where('productCategoryId', $RequestedProductCategoryID)
                  ->pluck('id')->toArray();

        $LoanFullyPaids = DB::table('mfn_samity')
                          ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                          ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                          ->whereIn('mfn_samity.branchId',$branchIdArray)
                          ->whereIn('productIdFk', $ProductArray)
                          ->where('mfn_samity.softDel', '=', 0)
                          ->whereDate('mfn_loan.loanCompletedDate', '<', $date)
                          ->whereDate('mfn_loan.loanCompletedDate', '!=', '0000-00-00')
                          ->whereDate('mfn_loan.loanCompletedDate', '!=', null)
                          ->where(function ($query) use ($date) {
                                  $query->where('mfn_samity.closingDate', '>',  $date)
                                  ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                                  ->orWhere('mfn_samity.closingDate', '=',  null);
                          })                          
                          ->groupBy('fieldOfficerId')
                          ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                          //->toSQL();
                          ->get()
                          ->keyBy('fieldOfficerId')->toArray();

      }
    }else{
      $LoanFullyPaids = DB::table('mfn_samity')
                          ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                          ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                          ->whereIn('mfn_samity.branchId',$branchIdArray)
                          ->where('productIdFk',$RequestedProductID)
                          ->where('mfn_samity.softDel', '=', 0)
                          ->whereDate('mfn_loan.loanCompletedDate', '<', $date)
                          ->whereDate('mfn_loan.loanCompletedDate', '!=', '0000-00-00')
                          ->whereDate('mfn_loan.loanCompletedDate', '!=', null)
                          ->where(function ($query) use ($date) {
                                  $query->where('mfn_samity.closingDate', '>',  $date)
                                  ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                                  ->orWhere('mfn_samity.closingDate', '=',  null);
                          })
                          ->groupBy('fieldOfficerId')
                          ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                          //->toSQL();
                          ->get()
                          ->keyBy('fieldOfficerId')->toArray();

    }

     dd($LoanFullyPaids);
    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $LoanFullyPaids)){
        $TotalLoanFullyPaids[$value] = $LoanFullyPaids[$value]->loanId;
      }else{
        $TotalLoanFullyPaids[$value] = 0;
      }
    }
    dd($TotalLoanFullyPaids);
    return $TotalLoanFullyPaids;







    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

  
    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {

            $samityID = $Samity2->id;

            $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 1]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['loanCompletedDate', '<', $date], ['loanCompletedDate', '!=', null], ['loanCompletedDate', '!=', '0000-00-00'], ['softDel', '=', 0]]);
                      })
                      ->count('id');
                      //dd($LoanFullyPaids);
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
          $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {

            $samityID = $Samity2->id;

            $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 1], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '<', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
          $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            foreach ($Product as $key => $Products) {

              $samityID = $Samity2->id;
              $RequestedProductID1 = $Products->id;

              $LoanFullyPaids[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 1], ['productIdFk', $Products->id]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                            $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<', $date], ['loanCompletedDate', '<', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                        })
                        ->count('id');
            }
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalLoanFullyPaids[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {

            $samityID = $Samity2->id;

            $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 1], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $date], ['loanCompletedDate', '<', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
          $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
        }
      }
    }

     dd($TotalLoanFullyPaids);

    return $TotalLoanFullyPaids;
  }

  public function LoanFullyPaidAmountsQueryBOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity                 = array();
    $MemberCount            = 0;
    $LoanFullyAmounts       = array();
    $TotalLoanFullyAmounts  = array();
    $date = $Date[0];

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

   /*if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {
            if ($RequestedServiceCharge == 'WithServiceCharge') {
              $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
            }else{
               $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<=', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
            }      
      }else{

        $ProductArray = DB::table('mfn_loans_product')
                  ->select('id')
                  ->where('productCategoryId', $RequestedProductCategoryID)
                  ->pluck('id')->toArray();

        if ($RequestedServiceCharge == 'WithServiceCharge') {
          $LoanAmounts = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->whereIn('productIdFk', $ProductArray)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();

        }else{
          $LoanAmounts = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->whereIn('productIdFk', $ProductArray)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();
        }
      }
    }else{
      if ($RequestedServiceCharge == 'WithServiceCharge') {
        $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<', $date)
                    ->where('productIdFk',$RequestedProductID)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
      }else{
        $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<', $date)
                    ->where('productIdFk',$RequestedProductID)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
      }
    }
    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $LoanAmounts)){
        $TotalLoanAmounts[$value] = $LoanAmounts[$value]->loanAmount;
      }else{
        $TotalLoanAmounts[$value] = 0;
      }
    }

    dd($TotalLoanAmounts);
    return $TotalLoanAmounts;*/















    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softdel', '=', 0]])
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
            $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softdel', '=', 0]])
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
            $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $RequestedProductID], ['softdel', '=', 0]])
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
            $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $RequestedProductID], ['softdel', '=', 0]])
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
            $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                $LoanFullyAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $Products->id], ['softdel', '=', 0]])
                          ->sum('totalrepayAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanFullyAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanFullyAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $Products->id], ['loanCompletedDate', '<', $Date[0]], ['softdel', '=', 0]])
                          ->sum('loanAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $RequestedProductID], ['softdel', '=', 0]])
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
            $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $RequestedProductID], ['softdel', '=', 0]])
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
            $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }

    //dd($TotalLoanFullyAmounts);

    return $TotalLoanFullyAmounts;
  }

  public function LoanExpiredNoQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanExpiredNo = array();
    $TotalLoanExpiredNo = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id]  = DB::table('mfn_loan')
                // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softdel', '=', 0]])
                ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                    $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                })
                // ->select('id', 'totalRepayAmount', 'lastInstallmentDate')
                // ->get();
                ->count('id');

            // $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
            //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
            //           ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanExpired) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<',$date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<',$date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<',$date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanExpired) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            foreach ($Product as $key => $Products) {
              $samityID = $Samity2->id;
              $RequestedProductID1 = $Products->id;
              $LoanExpiredNo[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                        })
                        ->count('id');
            }
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalLoanExpiredNo[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanFullyPaid) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanFullyPaid);
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanExpiredNo;
  }

  public function LoanExpiredAmountQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanExpiredAmounts = array();
    $TotalLoanExpiredAmounts = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }

          // dd($TotalLoanExpiredAmounts);
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('totalRepayAmount');
                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('amount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('paidLoanAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          //dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
           if(is_array($Member1)){
            foreach ($Member1 as $key2 => $Member2) {
              $MemberCount = 0;
              if(is_array($Member2)){
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }
            }

            }
           }
            
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('loanAmount');

                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'loanAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('principalAmount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('principalAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              // foreach ($Member2 as $key => $Member3) {
              //   $MemberCount = $MemberCount + $Member3;
              // }
              $MemberCount = $MemberCount + $Member2;
            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // dd($TotalLoanExpiredAmounts);
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }

    // dd($TotalLoanExpiredAmounts);

    return $TotalLoanExpiredAmounts;
  }

  public function LoanCurrentNoQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanCurrentNo = array();
    $TotalLoanCurrentNo = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanCurrentNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        // dd($LoanCurrentNo);

        foreach ($LoanCurrentNo as $key => $LoanCurrent) {
          $TotalLoanCurrentNo[$key] = array_sum($LoanCurrent);
        }
        // dd($TotalLoanCurrentNo);
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanCurrentNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanCurrentNo as $key => $LoanCurrent) {
          $TotalLoanCurrentNo[$key] = array_sum($LoanCurrent);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            foreach ($Product as $key => $Products) {
              $RequestedProductID1 = $Products->id;
              $LoanCurrentNo[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                        })
                        ->count('id');
            }
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanCurrentNo as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalLoanCurrentNo[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanCurrentNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanCurrentNo as $key => $LoanCurrent) {
          $TotalLoanCurrentNo[$key] = array_sum($LoanCurrent);
        }
      }
    }

    // dd($TotalLoanCurrentNo);

    return $TotalLoanCurrentNo;
  }

  public function LoanCurrentAmountsQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanCurrentAmounts = array();
    $TotalLoanCurrentAmounts = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }

          // dd($TotalLoanExpiredAmounts);
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('loanAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->sum('loanAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanFullyAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('loanAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanFullyAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('totalRepayAmount');
                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->sum('totalRepayAmount');
              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanCurrentAmounts as $key1 => $Member1) {
            /*foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }*/
            $MemberCount = 0;
            $TotalLoanCurrentAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }

          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('loanAmount');

                $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->sum('loanAmount');
              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanCurrentAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              // foreach ($Member2 as $key => $Member3) {
              //   $MemberCount = $MemberCount + $Member3;
              // }
              $MemberCount = $MemberCount + $Member2;
            }
            $TotalLoanCurrentAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // dd($TotalLoanExpiredAmounts);
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('loanAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanCurrentAmounts;
  }

  public function LoanCurrentDueNoQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $LoaneeCount = 0;

    $Samity = array();
    $LoanIds = array();
    $LoanCurrentDueNoCollection = array();
    $LoanCurrentDueNoSchedule = array();
    $TotalLoanCurrentDueNo = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                ->select('id')
                // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                    $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                })
                ->groupBy('id')
                ->get();

            // $loanWiseScheduleAmount = DB::table('mfn_loan_schedule')
            //     ->where('loanIdFk', )
          }
        }

        // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        // ->select('mfn_loan.id', 'mfn_loan_collection.amount')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');
                        // ->get();

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        // ->select('mfn_loan.id', 'mfn_loan_schedule.installmentAmount')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
                        // ->get();
            }
          }
        }

        // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
          foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
            foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
              // code...
              foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                if ($key1 == $key1A) {
                  foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                    if ($key2 == $key2A) {
                      foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                        if ($key3 == $key3A) {
                          // code...
                          if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                            ++$LoaneeCount;
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
          $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
          $LoaneeCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');


              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              ++$LoaneeCount;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
            $LoaneeCount = 0;
          }

        // // dd($TotalLoanCurrentDueNo);

      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                      ->select('mfn_loan.id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[0]], ['lastInstallmentDate', '>', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID, $RequestedProductCategoryID) {
                          $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '>=', $date], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                          ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                          ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]]);
                      })
                      ->groupBy('mfn_loan.id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');


              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              ++$LoaneeCount;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
            $LoaneeCount = 0;
          }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
              
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              ++$LoaneeCount;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
            $LoaneeCount = 0;
          }
      }
    }
    // dd($TotalLoanCurrentDueNo);
    // dd($TotalLoanCurrentDueNo);

    return $TotalLoanCurrentDueNo;
  }

  public function LoanCurrentDueAmountsQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $LoaneeAmountCount = 0;

    $Samity = array();
    $LoanIds = array();
    $LoanCurrentDueNoCollection = array();
    $LoanCurrentDueNoSchedule = array();
    $TotalLoanCurrentDueNo = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[0]], ['lastInstallmentDate', '>', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        // ->select('mfn_loan.id', 'mfn_loan_collection.amount')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');
                        // ->get();

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        // ->select('mfn_loan.id', 'mfn_loan_schedule.installmentAmount')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
                        // ->get();
            }
          }
        }

        foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
          foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
            foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
              // code...
              foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                if ($key1 == $key1A) {
                  foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                    if ($key2 == $key2A) {
                      foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                        if ($key3 == $key3A) {
                          // code...
                          if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                            $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
          $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
          $LoaneeAmountCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan_collection.collectionDate', '<=', $Date[0]], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
            $LoaneeAmountCount = 0;
          }

        // // dd($TotalLoanCurrentDueNo);

      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                      ->select('mfn_loan.id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID, $RequestedProductCategoryID) {
                          $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '>=', $date], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                          ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                          ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]]);
                      })
                      ->groupBy('mfn_loan.id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
            $LoaneeAmountCount = 0;
          }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
            $LoaneeAmountCount = 0;
          }
      }
    }
    /**/

    // // dd($LoanCurrentDueNoSchedule);
    // // dd($TotalLoanCurrentDueNo);

    return $TotalLoanCurrentDueNo;
  }

  public function LoanExpiredDueNoQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $LoaneeCount = 0;
    $MemberCount = 0;
    $Samity = array();
    $LoanIds = array();
    $LoanCurrentDueNoCollection = array();
    $LoanCurrentDueNoSchedule = array();
    $TotalLoanCurrentDueNo = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id]  = DB::table('mfn_loan')
                // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['loanCompletedDate', '<', $Date[0]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softdel', '=', 0]])
                ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                    $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                })
                // ->select('id', 'totalRepayAmount', 'lastInstallmentDate')
                // ->get();
                ->count('id');

            // $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
            //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
            //           ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanExpired) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<',$date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<',$date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<',$date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanExpired) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            foreach ($Product as $key => $Products) {
              $samityID = $Samity2->id;
              $RequestedProductID1 = $Products->id;
              $LoanExpiredNo[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                        })
                        ->count('id');
            }
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalLoanExpiredNo[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanFullyPaid) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanFullyPaid);
        }
      }
    }


    // // dd($TotalLoanCurrentDueNo);

    return $TotalLoanExpiredNo;
  }
/*Will start from here*/
  public function LoanExpiredDueAmountsQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $LoaneeAmountCount = 0;
    $MemberCount = 0;
    $Samity = array();
    $LoanIds = array();
    $LoanCurrentDueNoCollection = array();
    $LoanCurrentDueNoSchedule = array();
    $TotalLoanCurrentDueNo = array();

    $date = $Date[0];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }

          // dd($TotalLoanExpiredAmounts);
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('totalRepayAmount');
                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('amount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('paidLoanAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            /*foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }*/
            $MemberCount = 0;

            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('loanAmount');

                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'loanAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('principalAmount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('principalAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              // foreach ($Member2 as $key => $Member3) {
              //   $MemberCount = $MemberCount + $Member3;
              // }
              $MemberCount = $MemberCount + $Member2;
            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // dd($TotalLoanExpiredAmounts);
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }


    // // dd($TotalLoanCurrentDueNo);

    return $TotalLoanExpiredAmounts;
  }

  public function TotalOutstandingQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $LoaneeAmountCount = 0;

    $Samity = array();
    $LoanIds = array();
    $LoanCurrentDueNoCollection = array();
    $LoanCurrentDueNoSchedule = array();
    $TotalLoanCurrentDueNo = array();

    $date = $Date[0];
    $date = date('Y-m-d', strtotime($date .' -1 day'));

    $loanAmountTotal = 0;
    $loanPaidTotal   = 0;

    // foreach ($FOs as $key => $FO) {
    //   $fId = $FO->id;
    //   $sId = $FO->Samity_id;
    //   $bId = $FO->branchId;

    //   $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    // }

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')
        // ->where([['fieldOfficerId', $fId], ['branchId', $FO->branchId], ['closingDate', '>',  1], ['softDel', '=', 0]])
        ->where(function ($query) use ($date, $fId, $bId) {
            $query->where([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '>',  $date], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  '0000-00-00'], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  null], ['softDel', '=', 0]]);
        })
        ->get();

    }

    if ($RequestedProductID == 'All' and $RequestedProductCategoryID == 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                    })
                    ->groupBy('id')
                    ->get();

          // $infoIds[] = 
        }
      }

      // dd($LoanIds);

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['disbursementDate', '<=', $date], ['id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');

              $loanAmountTotal = $loanAmountTotal + $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id];
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where([['loanIdFk', $LoanIds3->id], ['softDel', '=', 0]])
                        ->sum('paidLoanAmountOB');

              $loanPaidTotal = $loanPaidTotal + $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id];
            }
          }
        }

        // dd($loanAmountTotal, $loanPaidTotal);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['disbursementDate', '<=', $date], ['id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.principalAmount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where([['loanIdFk', $LoanIds3->id], ['softDel', '=', 0]])
                        ->sum('principalAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...
    }
    elseif ($RequestedProductID == 'All' and $RequestedProductCategoryID != 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                    })
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where([['loanIdFk', $LoanIds3->id], ['softDel', '=', 0]])
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.principalAmount');


              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where([['loanIdFk', $LoanIds3->id], ['softDel', '=', 0]])
                        ->sum('principalAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...
    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID == 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                    })
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where([['loanIdFk', $LoanIds3->id], ['softDel', '=', 0]])
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.principalAmount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('principalAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...

    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID != 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                    })
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where([['loanIdFk', $LoanIds3->id], ['softDel', '=', 0]])
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan_collection.collectiondate', '<', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.principalAmount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('principalAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...
    }

    if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
      foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
        foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
          foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
            // code...
            foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
              if ($key1 == $key1A) {
                foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                  if ($key2 == $key2A) {
                    foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                      if ($key3 == $key3A) {
                        // code...
                        if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                          $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
        $LoaneeAmountCount = 0;
      }
    }
    elseif (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) == 0) {
      foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
        foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
          foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
            // code...
            $LoaneeAmountCount = $LoaneeAmountCount + $LoanCurrentDueNoSchedule3;
          }
        }
        $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
        $LoaneeAmountCount = 0;
      }
    }
    else {
      foreach ($LoanIds as $key => $LoanId) {
        $TotalLoanCurrentDueNo[$key] = $LoaneeAmountCount;
      }
    }

    // // dd($TotalLoanCurrentDueNo);

    return $TotalLoanCurrentDueNo;
  }

  public function AdditionalFeeCollectionQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge){
    $LoaneeAmountCount = 0;

    $Samity = array();
    $LoanIds = array();
    $AdditionalFee = array();
    $TotalAdditionalFeeCollection = array();
    $TotalAdditionalFee = array();



    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where('fieldOfficerId', $fId)->get();
    }

    // // dd($FOs);

    foreach ($Samity as $key1 => $Samity1) {
      foreach ($Samity1 as $key2 => $Samity2) {
        $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->select('id')
                  ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                  ->groupBy('id')
                  ->get();
      }
    }

    if ($RequestedProductID == 'All' and $RequestedProductCategoryID == 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $key2], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                        ->sum('additionalFee');
          }
        }
      }

    }
    elseif ($RequestedProductID == 'All' and $RequestedProductCategoryID != 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.id', $LoanIds3->id]])
                        ->sum('mfn_loan.additionalFee');
          }
        }
      }

    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID == 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $key2], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                        ->sum('additionalFee');
          }
        }
      }

    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID != 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $key2], ['productIdFk', $RequestedProductID], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                        ->sum('additionalFee');
          }
        }
      }
    }

    // // dd($AdditionalFee);

    foreach ($AdditionalFee as $key1 => $Additional) {
      // code...
      foreach ($Additional as $key2 => $AdditionalCollection) {
        // code...
        $TotalAdditionalFee[$key1][$key2] = array_sum($AdditionalCollection);
      }
    }

    foreach ($TotalAdditionalFee as $key => $TotalAdditional) {
      // code...
      $TotalAdditionalFeeCollection[$key] = array_sum($TotalAdditional);
    }

    // dd($TotalAdditionalFeeCollection);

    return $TotalAdditionalFeeCollection;

  }

  public function WithSavingsInterestQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID){
    $Samity = array();
    $SavingsProduct = array();
    $LoanProducts = array();
    $Members = array();
    $Savings = array();
    $Totalsavings = array();
    $SavingsAccId = array();
    $SavingsInterest = array();
    $SavingsInterestSamity = array();
    $SavingsInterestAll = array();

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where('fieldOfficerId', $fId)->get();
    }

    // // dd($FOs);

    foreach ($Samity as $key1 => $Samity1) {
      foreach ($Samity1 as $key2 => $Samity2) {
        $SavingsProduct[$key1][$Samity2->id] = DB::table('mfn_savings_account')
                  ->select('savingsProductIdFk')
                  ->where('samityIdFk', $Samity2->id)
                  ->groupBy('savingsProductIdFk')
                  ->get();
      }
    }
    $LoanProducts = DB::table('mfn_loans_product')->where('productCategoryId', $RequestedProductCategoryID)->get();
    // dd($SavingsAccId);

    if ($RequestedProductCategoryID == 'All' and $RequestedProductID == 'All') {
      // code...
      foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
        foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
          foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

            $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                    ->where([['samityIdFk', $key2], ['productIdFk', $SavingsProduct3->savingsProductIdFk], ['date', '<', $Date[0]]])
                    ->sum('interestAmount');
          }
        }
      }

      // dd($SavingsInterest);
    }
    elseif ($RequestedProductCategoryID != 'All' and $RequestedProductID == 'All') {
      // code...
      foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
        foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
          foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
            $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                    ->join('mfn_loans_product', 'mfn_savings_interest.primaryProductIdFk', '=', 'mfn_loans_product.id')
                    ->where([['mfn_savings_interest.samityIdFk', $key2], ['mfn_savings_interest.productIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_savings_interest.date', '<', $Date[0]]])
                    ->sum('mfn_savings_interest.interestAmount');
          }
        }
      }
    }
    elseif ($RequestedProductCategoryID == 'All' and $RequestedProductID != 'All') {
      // code...
      foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
        foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
          foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

            $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                    ->where([['samityIdFk', $key2], ['productIdFk', $SavingsProduct3->savingsProductIdFk], ['primaryProductIdFk', $RequestedProductID], ['date', '<', $Date[0]]])
                    ->sum('interestAmount');
          }
        }
      }
    }
    elseif ($RequestedProductCategoryID != 'All' and $RequestedProductID != 'All') {
      // code...
      foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
        foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
          foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

            $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                    ->where([['samityIdFk', $key2], ['productIdFk', $SavingsProduct3->savingsProductIdFk], ['primaryProductIdFk', $RequestedProductID], ['date', '<', $Date[0]]])
                    ->sum('interestAmount');
          }
        }
      }
    }

    foreach ($SavingsInterest as $key1 => $SavingsInter) {
      // code...
      foreach ($SavingsInter as $key2 => $Savings) {
        // code...
        $SavingsInterestSamity[$key1][$key2] = array_sum($Savings);
      }
    }

    foreach ($SavingsInterestSamity as $key => $SavingsInterestS) {
      // code...
      $SavingsInterestAll[$key] = array_sum($SavingsInterestS);
    }

    // dd($SavingsInterestAll);

    return $SavingsInterest;
  }
  /* End of the Begining of the week */

  /* Start of the for the week */
  public function MemberQuery2A($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID){
    $Samity = array();
    $Product = array();
    $Member = array();
    $MemberCount = 0;
    $TotalMember = array();

    $date1 = $Date[0];
    $date2 = $Date[1];


    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    
        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->whereDate('admissionDate', '>=', $date1)
                    ->whereDate('admissionDate', '<=', $date2)
                    ->where(function ($query) use ($date2) {
                            $query->where('mfn_samity.closingDate', '>',  $date2)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date2) {
                            $query->where('mfn_member_information.closingDate', '>',  $date2)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
                    //dd($Member);




   /* foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')
        // ->where('fieldOfficerId', $fId)
        ->where(function ($query) use ($date2, $fId, $bId) {
            $query->where([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '>',  $date2], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  '0000-00-00'], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  null], ['softDel', '=', 0]]);
        })
        ->get();

    }

   

    foreach ($Samity as $key1 => $Samity1) {
      if (sizeof($Samity1) > 0) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $Member[$key1][$Samity2->id] = $dbMembers
            ->where('softDel', '=', 0)
            ->where('samityId', $samityID)
            ->where('admissionDate', '>=', $date1)
            ->where('admissionDate', '<=', $date2)
            ->filter(function ($value) use ($date2) {
                if (($value->closingDate > $date2) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null)) {
                    return true;
                }
                else {
                    return false;
                }
            })
            ->groupBy('id')
            ->count('id');
        }
        foreach ($Member as $key => $Members) {
          $TotalMember[$key] = array_sum($Members);
        }
      }
      else{
          $TotalMember[$key1] = 0;
      }
    }

    // dd($Member, $TotalMember);

    return $TotalMember;*/

    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $Member)){
         $TotalMember[$value] = $Member[$value]->memberCount;
      }else{
        $TotalMember[$value] = 0;
      }
    }
//dd($TotalMember);
    return $TotalMember;
  }

  //MEMBER COUNT MIDDLE OF THE WEEK

  public function MemberQueryMOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID){
    $Samity = array();
    $Product = array();
    $Member = array();
    $MemberCount = 0;
    $TotalMember = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {
        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->whereDate('admissionDate', '>=', $date1)
                    ->whereDate('admissionDate', '<=', $date2)
                    /*->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_samity.closingDate', '<',  $date1)
                            ->whereDate('mfn_samity.closingDate', '>',  $date2)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_member_information.closingDate', '<',  $date1)
                            ->whereDate('mfn_member_information.closingDate', '>',  $date2)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    
                    
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

                  //dd($Member);

      }else{
        $primaryProductIdArray = DB::table('mfn_loans_product')
                                ->select('id')
                                ->where('productCategoryId', $RequestedProductCategoryID)
                                ->pluck('id')->toArray();

        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->whereIn('mfn_member_information.primaryProductId', $primaryProductIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->whereDate('admissionDate', '>=', $date1)
                    ->whereDate('admissionDate', '<=', $date2)
                    /*->where(function ($query) use ($date1,$date2) {
                            $query->where('mfn_samity.closingDate', '>',  $date1)
                            ->orWhere('mfn_samity.closingDate', '<',  $date2)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date1,$date2) {
                            $query->where('mfn_member_information.closingDate', '>',  $date1)
                            ->orWhere('mfn_member_information.closingDate', '<',  $date2)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

      }

    }else{

      $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_member_information.primaryProductId', $RequestedProductID)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->whereDate('admissionDate', '>=', $date1)
                    ->whereDate('admissionDate', '<=', $date2)
                    /*->where(function ($query) use ($date1,$date2) {
                            $query->where('mfn_samity.closingDate', '>',  $date1)
                            ->orWhere('mfn_samity.closingDate', '<',  $date2)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date1,$date2) {
                            $query->where('mfn_member_information.closingDate', '>',  $date1)
                            ->orWhere('mfn_member_information.closingDate', '<',  $date2)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

    }

    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $Member)){
         $TotalMember[$value] = $Member[$value]->memberCount;
      }else{
        $TotalMember[$value] = 0;
      }
    }

    return $TotalMember;

    /*foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')
        // ->where('fieldOfficerId', $fId)
        ->where(function ($query) use ($date2, $fId, $bId) {
            $query->where([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '>',  $date2], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  '0000-00-00'], ['softDel', '=', 0]])
            ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  null], ['softDel', '=', 0]]);
        })
        ->get();

    }

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        foreach ($Samity as $key1 => $Samity1) {
          if (sizeof($Samity1) > 0) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $Member[$key1][$Samity2->id] = $dbMembers
                ->where('softDel', '=', 0)
                ->where('samityId', $samityID)
                ->where('admissionDate', '>=', $date1)
                ->where('admissionDate', '<=', $date2)
                ->filter(function ($value) use ($date2) {
                    if (($value->closingDate > $date2) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                })
                ->groupBy('id')
                ->count('id');
            }
            foreach ($Member as $key => $Members) {
              $TotalMember[$key] = array_sum($Members);
            }
          }
          else{
              $TotalMember[$key1] = 0;
          }
        }
        // dd($TotalMember, $Member);
      }
      else {
        foreach ($Samity as $key1 => $Samity1) {
          if (sizeof($Samity1) > 0) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $Member[$key1][$Samity2->id] = $dbMembers
                ->where('softDel', '=', 0)
                ->where('samityId', $samityID)
                ->where('admissionDate', '>=', $date1)
                ->where('admissionDate', '<=', $date2)
                ->where('mfn_loan.productIdFk', $RequestedProductID)
                ->filter(function ($value) use ($date2) {
                    if (($value->closingDate > $date2) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                })
                ->groupBy('id')
                ->count('id');
            }
            foreach ($Member as $key => $Members) {
              $TotalMember[$key] = array_sum($Members);
            }
          }
          else{
              $TotalMember[$key1] = 0;
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            foreach ($Product as $key => $Product1) {
              $Member[$key1][$Samity2->id][$Product1->id] = $dbMembers
                ->where('softDel', '=', 0)
                ->where('samityId', $samityID)
                ->where('admissionDate', '>=', $date1)
                ->where('admissionDate', '<=', $date2)
                ->where('mfn_loan.productIdFk', $Product1->id)
                ->filter(function ($value) use ($date2) {
                    if (($value->closingDate > $date2) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                })
                ->groupBy('id')
                ->count('id');
            }

          }
        }
        foreach ($Member as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalMember[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        foreach ($Samity as $key1 => $Samity1) {
          if (sizeof($Samity1) > 0) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $Member[$key1][$Samity2->id] = $dbMembers
                ->where('softDel', '=', 0)
                ->where('samityId', $samityID)
                ->where('admissionDate', '>=', $date1)
                ->where('admissionDate', '<=', $date2)
                ->where('mfn_loan.productIdFk', $RequestedProductID)
                ->filter(function ($value) use ($date2) {
                    if (($value->closingDate > $date2) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                })
                ->groupBy('id')
                ->count('id');
            }
            foreach ($Member as $key => $Members) {
              $TotalMember[$key] = array_sum($Members);
            }
          }
          else{
              $TotalMember[$key1] = 0;
          }
        }
      }
    }*/
    // // dd($TotalMember);

    // // dd($Member);

    return $TotalMember;
  }

  //MEMBER CLOSING QUERY MIDDLE OF THE WEEK
  public function MemberClosingQueryMOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID){
    $Samity       = array();
    $Product      = array();
    $Member       = array();
    $MemberCount  = 0;
    $TotalMember  = array();

    $date1 = $Date[0];
    $date2 = $Date[1];


    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {
        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_closing', 'mfn_samity.id', '=', 'mfn_member_closing.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_closing.softDel', '=', 0)
                    ->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_samity.closingDate', '>=',  $date1)
                            ->whereDate('mfn_samity.closingDate', '<=',  $date2);
                        })
                    ->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_member_closing.closingDate', '>=',  $date1)
                            ->whereDate('mfn_member_closing.closingDate', '<=',  $date2);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_closing.id)) as 'memberCount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

                    //dd($Member);

      }else{
        $primaryProductIdArray = DB::table('mfn_loans_product')
                                ->select('id')
                                ->where('productCategoryId', $RequestedProductCategoryID)
                                ->pluck('id')->toArray();

        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_closing', 'mfn_samity.id', '=', 'mfn_member_closing.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->whereIn('mfn_member_closing.primaryProductIdFk', $primaryProductIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_closing.softDel', '=', 0)
                    ->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_samity.closingDate', '>=',  $date1)
                            ->whereDate('mfn_samity.closingDate', '<=',  $date2);
                        })
                    ->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_member_closing.closingDate', '>=',  $date1)
                            ->whereDate('mfn_member_closing.closingDate', '<=',  $date2);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_closing.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

      }

    }else{

      $Member = DB::table('mfn_samity')
                    ->join('mfn_member_closing', 'mfn_samity.id', '=', 'mfn_member_closing.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_member_closing.primaryProductIdFk', $RequestedProductID)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_closing.softDel', '=', 0)
                   ->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_samity.closingDate', '>=',  $date1)
                            ->whereDate('mfn_samity.closingDate', '<=',  $date2);
                        })
                    ->where(function ($query) use ($date1,$date2) {
                            $query->whereDate('mfn_member_closing.closingDate', '>=',  $date1)
                            ->whereDate('mfn_member_closing.closingDate', '<=',  $date2);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_closing.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

    }

    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $Member)){
         $TotalMember[$value] = $Member[$value]->memberCount;
      }else{
        $TotalMember[$value] = 0;
      }
    }

    return $TotalMember;



















    /*foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')
      // ->where('fieldOfficerId', $fId)->get();
      ->where(function ($query) use ($date2, $fId, $bId) {
          $query->where([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '>',  $date2], ['softDel', '=', 0]])
          ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  '0000-00-00'], ['softDel', '=', 0]])
          ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  null], ['softDel', '=', 0]]);
      })
      ->select('id')
      ->get();
    }

    // dd($Samity);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        foreach ($Samity as $key1 => $Samity1) {
          if (sizeof($Samity1) > 0) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $Member[$key1][$Samity2->id] = DB::table('mfn_member_closing')->where([['samityIdFk', $Samity2->id], ['closingDate', '>=', $Date[0]], ['closingDate', '<=', $Date[1]]])->count('id');
            }
            foreach ($Member as $key => $Members) {
              $TotalMember[$key] = array_sum($Members);
            }
          }
          else {
            $TotalMember[$key1] = 0;
          }
        }

        // dd($FOs, $Samity, $TotalMember, $Member);
      }
      else {
        foreach ($Samity as $key1 => $Samity1) {
          if (sizeof($Samity1) > 0) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $Member[$key1][$Samity2->id] = DB::table('mfn_member_closing')
                  ->join('mfn_loan', 'mfn_member_closing.memberIdFk', '=', 'mfn_loan.memberIdFk')
                  ->where([['mfn_member_closing.samityIdFk', $Samity2->id], ['mfn_member_closing.closingDate', '>=', $Date[0]], ['mfn_member_closing.closingDate', '<=', $Date[1]], ['mfn_loan.productIdFk', $RequestedProductID]])
                  ->count('mfn_member_closing.id');
            }
          }
          else {
            foreach ($Member as $key => $Members) {
              $TotalMember[$key] = array_sum($Members);
            }
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            foreach ($Product as $key => $Product1) {
              $Member[$key1][$Samity2->id][$Product1->id] = DB::table('mfn_member_closing')
                  ->join('mfn_loan', 'mfn_member_closing.memberIdFk', '=', 'mfn_loan.memberIdFk')
                  // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.productCategoryId')
                  ->where([['mfn_member_closing.samityIdFk', $Samity2->id], ['mfn_member_closing.closingDate', '>=', $Date[0]], ['mfn_member_closing.closingDate', '<=', $Date[1]], ['mfn_loan.productIdFk', $Product1->id]])
                  ->count('mfn_member_closing.id');
            }

          }
        }
        foreach ($Member as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalMember[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        foreach ($Samity as $key1 => $Samity1) {
          if (sizeof($Samity1) > 0) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $Member[$key1][$Samity2->id] = DB::table('mfn_member_closing')
                  ->join('mfn_loan', 'mfn_member_closing.memberIdFk', '=', 'mfn_loan.memberIdFk')
                  ->where([['mfn_member_closing.samityIdFk', $Samity2->id], ['mfn_member_closing.closingDate', '>=', $Date[0]], ['mfn_member_closing.closingDate', '<=', $Date[1]], ['mfn_loan.productIdFk', $RequestedProductID]])
                  ->count('mfn_member_closing.id');
            }
          }
          else {
            foreach ($Member as $key => $Members) {
              $TotalMember[$key] = array_sum($Members);
            }
          }
        }
      }
    }*/
    // // dd($TotalMember);

    // // dd($Member);

    return $TotalMember;
  }

  public function SavingsProductQuery2($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity = array();
    $SavingsProduct = array();
    $LoanProducts = array();
    $Members = array();
    $Savings = array();
    $Totalsavings = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    foreach ($Samity as $key1 => $Samity1) {
      foreach ($Samity1 as $key2 => $Samity2) {
        $SavingsProduct[$key1][$Samity2->id] = DB::table('mfn_savings_account')
                  ->select('savingsProductIdFk')
                  ->where([['samityIdFk', $Samity2->id], ['branchIdFk', $RequestedBranchID]])
                  ->groupBy('savingsProductIdFk')
                  ->get();
      }
    }
    $LoanProducts = DB::table('mfn_loans_product')->where('productCategoryId', $RequestedProductCategoryID)->select('id')->get();
    // // dd($LoanProducts);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

              // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_loan')
              //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
              //         ->join('mfn_savings_deposit', 'mfn_savings_account.id', 'mfn_savings_deposit.accountIdFk')
              //         ->where([['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_deposit.depositDate', '>=', $Date[0]], ['mfn_savings_deposit.depositDate', '<=', $Date[1]]])
              //         ->sum('mfn_savings_deposit.amount');

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              // WE HAVE REMOVED ACCOUNT OPENING DATE (['accountOpeningDate', '<=', $date2])
              $savingAccIdArr = DB::table('mfn_savings_account')
                  // ->where([['samityIdFk', $samity->id], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProduct->id]])
                  ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2) {
                      $query->where([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk],  ['closingDate', '>=', $date2]])
                      ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk],  ['closingDate', '=', '0000-00-00']])
                      ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk],  ['closingDate', '=', null]]);
                  })
                  ->pluck('id')
                  ->toArray();
              // dd($savingAccIdArr);

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                  ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                      ['depositDate', '>=', $date1], ['depositDate', '<=', $date2]])
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->sum('amount');
            }
          }
        }
      }
      elseif ($RequestedProductID != 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_loan')
              //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
              //         ->join('mfn_savings_deposit', 'mfn_savings_account.id', 'mfn_savings_deposit.accountIdFk')
              //         ->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_deposit.depositDate', '>=', $Date[0]], ['mfn_savings_deposit.depositDate', '<=', $Date[1]]])
              //         ->sum('mfn_savings_deposit.amount');

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              $savingAccIdArr = DB::table('mfn_savings_account')
                  ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                  ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2, $RequestedProductID) {
                      $query->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>=', $date2]])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                  })
                  ->pluck('mfn_savings_account.id')
                  ->toArray();
              // dd($savingAccIdArr);

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                  ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                      ['depositDate', '>=', $date1], ['depositDate', '<=', $date2]])
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->sum('amount');
            }
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              foreach ($LoanProducts as $key => $LoanProduct) {
                // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk][$LoanProduct->id] = DB::table('mfn_loan')
                //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
                //         ->join('mfn_savings_deposit', 'mfn_savings_account.id', 'mfn_savings_deposit.accountIdFk')
                //         // ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                //         ->where([['mfn_loan.productIdFk', $LoanProduct->id], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_deposit.depositDate', '>=', $Date[0]], ['mfn_savings_deposit.depositDate', '<=', $Date[1]]])
                //         ->sum('mfn_savings_deposit.amount');

                $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;
                $RequestedProductID1 = $LoanProduct->id;

                // $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

                $savingAccIdArr = DB::table('mfn_savings_account')
                      ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                      // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                      ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2, $RequestedProductID1) {
                          $query->where([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>=', $date2]])
                          ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                          ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                      })
                      ->pluck('mfn_savings_account.id')
                      ->toArray();

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                    ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                        ['depositDate', '<=', $date2]])
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->sum('amount');
              }
            }
          }
        }
      }
      elseif ($RequestedProductID != 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_loan')
              //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
              //         ->join('mfn_savings_deposit', 'mfn_savings_account.id', 'mfn_savings_deposit.accountIdFk')
              //         ->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_deposit.depositDate', '>=', $Date[0]], ['mfn_savings_deposit.depositDate', '<=', $Date[1]]])
              //         ->sum('mfn_savings_deposit.amount');

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              $savingAccIdArr = DB::table('mfn_savings_account')
                  ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                  ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2, $RequestedProductID) {
                      $query->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>=', $date2]])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                  })
                  ->pluck('mfn_savings_account.id')
                  ->toArray();
              // dd($savingAccIdArr);

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                  ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                      ['depositDate', '>=', $date1], ['depositDate', '<=', $date2]])
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->sum('amount');
            }
          }
        }
      }
    }

    // // dd($Savings);

    return $Savings;
  }

  public function SavingsRefundQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity = array();
    $SavingsProduct = array();
    $LoanProducts = array();
    $Members = array();
    $Savings = array();
    $Totalsavings = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    foreach ($Samity as $key1 => $Samity1) {
      foreach ($Samity1 as $key2 => $Samity2) {
        $SavingsProduct[$key1][$Samity2->id] = DB::table('mfn_savings_account')
                  ->select('savingsProductIdFk')
                  ->where([['samityIdFk', $Samity2->id], ['branchIdFk', $RequestedBranchID]])
                  ->groupBy('savingsProductIdFk')
                  ->get();
      }
    }
    $LoanProducts = DB::table('mfn_loans_product')->where('productCategoryId', $RequestedProductCategoryID)->get();
    // // dd($LoanProducts);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_loan')
              //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
              //         ->join('mfn_savings_withdraw', 'mfn_savings_account.id', 'mfn_savings_withdraw.accountIdFk')
              //         ->where([['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_withdraw.withdrawDate', '>=', $Date[0]], ['mfn_savings_withdraw.withdrawDate', '<=', $Date[1]]])
              //         ->sum('mfn_savings_withdraw.amount');

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              $savingAccIdArr = DB::table('mfn_savings_account')
                  // ->where([['samityIdFk', $samity->id], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProduct->id]])
                  ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2) {
                      $query->where([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['accountOpeningDate', '<=', $date2], ['closingDate', '>=', $date2]])
                      ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['accountOpeningDate', '<=', $date2], ['closingDate', '=', '0000-00-00']])
                      ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['accountOpeningDate', '<=', $date2], ['closingDate', '=', null]]);
                  })
                  ->pluck('id')
                  ->toArray();
              // dd($savingAccIdArr);

              $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_withdraw')
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->where([['withdrawDate', '>=', $date1], ['withdrawDate', '<=', $date2], ['softDel', '=', 0]])
                    ->sum('amount');
            }
          }
        }
      }
      elseif ($RequestedProductID != 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_loan')
              //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
              //         ->join('mfn_savings_withdraw', 'mfn_savings_account.id', 'mfn_savings_withdraw.accountIdFk')
              //         ->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_withdraw.withdrawDate', '>=', $Date[0]], ['mfn_savings_withdraw.withdrawDate', '<=', $Date[1]]])
              //         ->sum('mfn_savings_withdraw.amount');

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              $savingAccIdArr = DB::table('mfn_savings_account')
                  ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                  ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2, $RequestedProductID) {
                      $query->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>=', $date2]])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                  })
                  ->pluck('mfn_savings_account.id')
                  ->toArray();

              $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->where([['withdrawDate', '>=', $date1], ['withdrawDate', '<=', $date2], ['softDel', '=', 0]])
                  ->sum('amount');
            }
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              foreach ($LoanProducts as $key => $LoanProduct) {
                // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk][$LoanProduct->id] = DB::table('mfn_loan')
                //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
                //         ->join('mfn_savings_withdraw', 'mfn_savings_account.id', 'mfn_savings_withdraw.accountIdFk')
                //         // ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                //         ->where([['mfn_loan.productIdFk', $LoanProduct->id], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_withdraw.withdrawDate', '>=', $Date[0]], ['mfn_savings_withdraw.withdrawDate', '<=', $Date[1]]])
                //         ->sum('mfn_savings_withdraw.amount');

                $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;
                $RequestedProductID1 = $LoanProduct->id;

                // $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

                $savingAccIdArr = DB::table('mfn_savings_account')
                      ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                      // ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                      ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2, $RequestedProductID1) {
                          $query->where([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>=', $date]])
                          ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                          ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID1], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                      })
                      ->pluck('mfn_savings_account.id')
                      ->toArray();

                $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->where([['withdrawDate', '>=', $date1], ['withdrawDate', '<=', $date2], ['softDel', '=', 0]])
                  ->sum('amount');
              }
            }
          }
        }
      }
      elseif ($RequestedProductID != 'All') {
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              // $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_loan')
              //         ->join('mfn_savings_account', 'mfn_loan.samityIdFk', '=', 'mfn_savings_account.samityIdFk')
              //         ->join('mfn_savings_withdraw', 'mfn_savings_account.id', 'mfn_savings_withdraw.accountIdFk')
              //         ->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.savingsProductIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_savings_withdraw.withdrawDate', '>=', $Date[0]], ['mfn_savings_withdraw.withdrawDate', '<=', $Date[1]]])
              //         ->sum('mfn_savings_withdraw.amount');

              $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

              $savingAccIdArr = DB::table('mfn_savings_account')
                  ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                  ->where(function ($query) use ($date1, $date2, $savingsProductIdFk, $key2, $RequestedProductID) {
                      $query->where([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>=', $date2]])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                      ->orWhere([['mfn_loan.productIdFk', $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                  })
                  ->pluck('mfn_savings_account.id')
                  ->toArray();

              $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                  ->whereIn('accountIdFk', $savingAccIdArr)
                  ->where([['withdrawDate', '>=', $date1], ['withdrawDate', '<=', $date2], ['softDel', '=', 0]])
                  ->sum('amount');
            }
          }
        }
      }
    }

    // // dd($Savings);

    return $Savings;

  }

  public function LoanDisburseNoQueryMOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $Loans = array();
    $TotalLoans = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {

        $Loans = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '>=', $date1)
                ->where('disbursementDate', '<=', $date2)
                /*->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })*/
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();
        
      }else{

        $ProductArray = DB::table('mfn_loans_product')
                  ->select('id')
                  ->where('productCategoryId', $RequestedProductCategoryID)
                  ->pluck('id')->toArray();
         
        $Loans = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '>=', $date1)
                ->where('disbursementDate', '<=', $date2)
                ->whereIn('productIdFk', $ProductArray)
                /*->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })*/
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();

      }
    }else{

      $Loans = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '>=', $date1)
                    ->where('disbursementDate', '<=', $date2)
                    ->where('productIdFk',$RequestedProductID)
                    /*->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

    }
    foreach ($fieldOfficerIdArray as $key => $value) {
          if(array_key_exists($value, $Loans)){
            $TotalLoans[$value] = $Loans[$value]->loanCount;
          }else{
            $TotalLoans[$value] = 0;
          }
        }
        return $TotalLoans;

  /*  foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
          // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]]])
                      ->where(function ($query) use ($date1, $date2, $samityID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        foreach ($Loans as $key => $Loan) {
          $TotalLoans[$key] = array_sum($Loan);
        }
      }
      else {
          // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $RequestedProductID]])
                      ->count('id');
          }
        }

        foreach ($Loans as $key => $Loan) {
          $TotalLoans[$key] = array_sum($Loan);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
          // code.....
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                $Loans[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $Products->id]])
                          ->count('id');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($Loans as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoans[$key1] = $MemberCount;
            $MemberCount = 0;
          }
      }
      else {
          // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $RequestedProductID]])
                      ->count('id');
          }
        }

        foreach ($Loans as $key => $Loan) {
          $TotalLoans[$key] = array_sum($Loan);
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoans;*/
  }

  public function LoanDisburseAmountsQueryMOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity           = array();
    $MemberCount      = 0;
    $LoanAmounts      = array();
    $TotalLoanAmounts = array();
    $date1            = $Date[0];
    $date2            = $Date[1];
    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {
            if ($RequestedServiceCharge == 'WithServiceCharge') {
              $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<=', $date1)
                    ->where('disbursementDate', '>=', $date2)
                    /*->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
            }else{
               $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<=', $date1)
                    ->where('disbursementDate', '>=', $date2)
                    /*->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
            }      
      }else{

        $ProductArray = DB::table('mfn_loans_product')
                  ->select('id')
                  ->where('productCategoryId', $RequestedProductCategoryID)
                  ->pluck('id')->toArray();

        if ($RequestedServiceCharge == 'WithServiceCharge') {
          $LoanAmounts = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<=', $date1)
                    ->where('disbursementDate', '>=', $date2)
                ->whereIn('productIdFk', $ProductArray)
                /*->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })*/
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();

        }else{
          $LoanAmounts = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<=', $date1)
                    ->where('disbursementDate', '>=', $date2)
                ->whereIn('productIdFk', $ProductArray)
                /*->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })*/
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();
        }
      }
    }else{
      if ($RequestedServiceCharge == 'WithServiceCharge') {
        $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<=', $date1)
                    ->where('disbursementDate', '>=', $date2)
                    ->where('productIdFk',$RequestedProductID)
                    /*->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
      }else{
        $LoanAmounts = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                   ->where('disbursementDate', '<=', $date1)
                    ->where('disbursementDate', '>=', $date2)
                    ->where('productIdFk',$RequestedProductID)
                    /*->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })*/
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
      }
    }
    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $LoanAmounts)){
        $TotalLoanAmounts[$value] = $LoanAmounts[$value]->loanAmount;
      }else{
        $TotalLoanAmounts[$value] = 0;
      }
    }
    return $TotalLoanAmounts;

    /*foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]]])
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]]])
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
      else {
        //productCategory all but product selected
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                
                $RequestedProductID1 = $Products->id;
                $samityID = $Samity2->id;
                
                $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $Products->id]])
                          ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['productIdFk', $Products->id], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['productIdFk', $Products->id], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['productIdFk', $Products->id], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('totalrepayAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $Products->id]])
                          ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['productIdFk', $Products->id], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['productIdFk', $Products->id], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['productIdFk', $Products->id], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('loanAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $date1], ['disbursementDate', '<=', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanAmounts;*/
  }

  public function LoanfullyPaidNoQueryMOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity               = array();
    $MemberCount          = 0;
    $LoanFullyPaids       = array();
    $TotalLoanFullyPaids  = array();
    $date1                = $Date[0];
    $date2                = $Date[1];

    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

      if ($RequestedProductID == 'All') {
        if ($RequestedProductCategoryID == 'All') {
          $LoanFullyPaids = DB::table('mfn_samity')
                            ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                            ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                            ->whereIn('mfn_samity.branchId',$branchIdArray)
                            ->where('mfn_samity.softDel', '=', 0)
                            ->where('mfn_loan.softDel', '=', 0)
                            /*->whereDate('mfn_loan.disbursementDate', '<', $date1)*/
                            ->whereDate('mfn_loan.loanCompletedDate', '<=', $date1)
                            ->whereDate('mfn_loan.loanCompletedDate', '>=', $date2)
                            ->groupBy('fieldOfficerId')
                            ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                            //->toSQL();
                            ->get()
                            ->keyBy('fieldOfficerId')->toArray();

        }else{
          $ProductArray = DB::table('mfn_loans_product')
                    ->select('id')
                    ->where('productCategoryId', $RequestedProductCategoryID)
                    ->pluck('id')->toArray();

          $LoanFullyPaids = DB::table('mfn_samity')
                            ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                            ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                            ->whereIn('mfn_samity.branchId',$branchIdArray)
                            ->whereIn('productIdFk', $ProductArray)
                            ->where('mfn_samity.softDel', '=', 0)
                            ->where('mfn_loan.softDel', '=', 0)
                            /*->whereDate('mfn_loan.disbursementDate', '<', $date1)*/
                            ->whereDate('mfn_loan.loanCompletedDate', '<=', $date1)
                            ->whereDate('mfn_loan.loanCompletedDate', '>=', $date2)
                            ->groupBy('fieldOfficerId')
                            ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                            //->toSQL();
                            ->get()
                            ->keyBy('fieldOfficerId')->toArray();
        }
      }else{
        $LoanFullyPaids = DB::table('mfn_samity')
                            ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                            ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                            ->whereIn('mfn_samity.branchId',$branchIdArray)
                            ->where('productIdFk',$RequestedProductID)
                            ->where('mfn_samity.softDel', '=', 0)
                            ->where('mfn_loan.softDel', '=', 0)
                            /*->whereDate('mfn_loan.disbursementDate', '<', $date1)*/
                            ->whereDate('mfn_loan.loanCompletedDate', '<=', $date1)
                            ->whereDate('mfn_loan.loanCompletedDate', '>=', $date2)
                            ->groupBy('fieldOfficerId')
                            ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                            //->toSQL();
                            ->get()
                            ->keyBy('fieldOfficerId')->toArray();

      }
      foreach ($fieldOfficerIdArray as $key => $value) {
        if(array_key_exists($value, $LoanFullyPaids)){
          $TotalLoanFullyPaids[$value] = $LoanFullyPaids[$value]->loanId;
        }else{
          $TotalLoanFullyPaids[$value] = 0;
        }
      }
      return $TotalLoanFullyPaids;

   /* foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]])
                      // ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      //       $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '<=', $date2], ['softDel', '=', 0]])
                      //       ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['softDel', '=', 0]])
                      //       ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                      //   })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
          $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $RequestedProductID], ['softDel', '=', 0]])
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
          $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            foreach ($Product as $key => $Products) {
              $LoanFullyPaids[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $Products->id], ['softDel', '=', 0]])
                        ->count('id');
            }
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalLoanFullyPaids[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $RequestedProductID], ['softDel', '=', 0]])
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
          $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanFullyPaids;*/
  }

  public function LoanFullyPaidAmountsQuery2($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanAmounts = array();
    $TotalLoanAmounts = array();

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]])
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]])
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
      else {
        //productCategory all but product selected
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $Products->id]])
                          ->sum('totalrepayAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $Products->id]])
                          ->sum('loanAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->sum('totalrepayAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '>=', $Date[0]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->sum('loanAmount');
            }
          }

          foreach ($LoanAmounts as $key => $LoanAmount) {
            $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          }
        }
      }
    }

    // // dd($LoanAmounts);

    // // dd($TotalLoans);

    return $TotalLoanAmounts;
  }

  public function LoanExpiredNoQuery2($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanExpiredNo = array();
    $TotalLoanExpiredNo = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                // ->where([['samityIdFk', $Samity2->id], ['lastInstallmentDate', '>=', $Date[0]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0]])
                ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                    $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date1], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                })
                ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanExpired) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                // ->where([['samityIdFk', $Samity2->id], ['lastInstallmentDate', '>=', $Date[0]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                    $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date1], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                })
                ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanExpired) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            foreach ($Product as $key => $Products) {
              $RequestedProductID1 = $Products->id;
              $LoanExpiredNo[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['lastInstallmentDate', '>=', $Date[0]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date1], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                        })
                        ->count('id');
            }
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalLoanExpiredNo[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                // ->where([['samityIdFk', $Samity2->id], ['lastInstallmentDate', '>=', $Date[0]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                    $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date1], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                    ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                })
                ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanExpiredNo as $key => $LoanFullyPaid) {
          $TotalLoanExpiredNo[$key] = array_sum($LoanFullyPaid);
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanExpiredNo;
  }

  public function LoanExpiredAmountQuery2($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanExpiredAmounts = array();
    $TotalLoanExpiredAmounts = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }

          // dd($TotalLoanExpiredAmounts);
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('totalRepayAmount');
                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('amount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('paidLoanAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('loanAmount');

                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('principalAmount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('principalAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // dd($TotalLoanExpiredAmounts);
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');
              // dd($date1, $date2);

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '>=', $date2], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['lastInstallmentDate', '>=', $date1], ['lastInstallmentDate', '<', $date2], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanExpiredAmounts;
  }

  public function RegularRecoverableQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanExpiredAmounts = array();
    $TotalLoanExpiredAmounts = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0]]);
                        })
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0]]);
                        })
                        ->sum('mfn_loan_schedule.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_schedule.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $RequestedProductID1 = $Products->id;
                $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $Products->id]])
                          ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                          })
                          ->sum('mfn_loan_schedule.installmentAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $RequestedProductID1 = $Products->id;
                $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $Products->id]])
                          ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                          })
                          ->sum('mfn_loan_schedule.principalAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1[0]], ['mfn_loan_schedule.scheduleDate', '<=', $date2[1]], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_schedule.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanExpiredAmounts;
  }

  public function RegularRecoveryQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
    $Samity = array();
    $MemberCount = 0;
    $LoanExpiredAmounts = array();
    $TotalLoanExpiredAmounts = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchid', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0]]);
                        })
                        ->sum('mfn_loan_collection.amount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0]]);
                        })
                        ->sum('mfn_loan_collection.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_collection.amount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_collection.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $RequestedProductID1 = $Products->id;
                $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $Products->id]])
                          ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                              $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk',  $RequestedProductID1]])
                              ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk',  $RequestedProductID1]])
                              ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk',  $RequestedProductID1]]);
                          })
                          ->sum('mfn_loan_collection.amount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $RequestedProductID1 = $Products->id;
                $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $Products->id]])
                          ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID1) {
                              $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk',  $RequestedProductID1]])
                              ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk',  $RequestedProductID1]])
                              ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk',  $RequestedProductID1]]);
                          })
                          ->sum('mfn_loan_collection.principalAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_collection.amount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_schedule.scheduleDate', '>=', $date1], ['mfn_loan_schedule.scheduleDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]]);
                        })
                        ->sum('mfn_loan_schedule.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanExpiredAmounts;
  }

  public function ExpiredDueRecoveryQuery($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge){
    $Samity = array();
    $MemberCount = 0;
    $LoanExpiredAmounts = array();
    $TotalLoanExpiredAmounts = array();

    $date1 = $Date[0];
    $date2 = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where('fieldOfficerId', $fId)->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        // ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date1, $date2, $samityID, $RequestedProductID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '>=', $date2], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date2], ['mfn_loan_collection.collectionDate', '>=', $date1], ['mfn_loan_collection.collectionDate', '<=', $date2], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0]]);
                        })
                        ->sum('mfn_loan_collection.amount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0]])
                        ->sum('mfn_loan_collection.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->sum('mfn_loan_collection.amount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->sum('mfn_loan_collection.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $Products->id]])
                          ->sum('mfn_loan_collection.amount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $Products->id]])
                          ->sum('mfn_loan_collection.principalAmount');
              }
            }
          }

          // // dd($LoanAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_collection.collectionDate', '>=', $Date[0]], ['mfn_loan_collection.collectionDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->sum('mfn_loan_collection.amount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $Samity2->id], ['mfn_loan.lastInstallmentDate', '<', $Date[0]], ['mfn_loan_schedule.scheduleDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.productIdFk', $RequestedProductID]])
                        ->sum('mfn_loan_schedule.principalAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }

    // // dd($TotalLoans);

    return $TotalLoanExpiredAmounts;
  }

  public function AdditionalFeeCollectionQuery2($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge){
    $LoaneeAmountCount = 0;

    $Samity = array();
    $LoanIds = array();
    $AdditionalFee = array();
    $TotalAdditionalFeeCollection = array();
    $TotalAdditionalFee = array();



    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where('fieldOfficerId', $fId)->get();
    }

    // // dd($FOs);

    foreach ($Samity as $key1 => $Samity1) {
      foreach ($Samity1 as $key2 => $Samity2) {
        $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->select('id')
                  ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0]])
                  ->groupBy('id')
                  ->get();
      }
    }

    if ($RequestedProductID == 'All' and $RequestedProductCategoryID == 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $key2], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                        ->sum('additionalFee');
          }
        }
      }

    }
    elseif ($RequestedProductID == 'All' and $RequestedProductCategoryID != 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.id', $LoanIds3->id]])
                        ->sum('mfn_loan.additionalFee');
          }
        }
      }

    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID == 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $key2], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                        ->sum('additionalFee');
          }
        }
      }

    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID != 'All') {
      // code...
      foreach ($LoanIds as $key1 => $LoanIds1) {
        foreach ($LoanIds1 as $key2 => $LoanIds2) {
          foreach ($LoanIds2 as $key3 => $LoanIds3) {
            $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $key2], ['productIdFk', $RequestedProductID], ['disbursementDate', '>=', $Date[0]], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                        ->sum('additionalFee');
          }
        }
      }
    }

    // // dd($AdditionalFee);

    foreach ($AdditionalFee as $key1 => $Additional) {
      // code...
      foreach ($Additional as $key2 => $AdditionalCollection) {
        // code...
        $TotalAdditionalFee[$key1][$key2] = array_sum($AdditionalCollection);
      }
    }

    foreach ($TotalAdditionalFee as $key => $TotalAdditional) {
      // code...
      $TotalAdditionalFeeCollection[$key] = array_sum($TotalAdditional);
    }

    // // dd($TotalAdditionalFeeCollection);

    return $TotalAdditionalFeeCollection;

  }

  /* End of the for the week */

  /* Start of the End of the week */

  public function MemberQueryEOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID){
    $Samity       = array();
    $Product      = array();
    $Member       = array();
    $MemberCount  = 0;
    $TotalMember  = array();
    $date         = $Date[1];


    $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
    $branchIdArray       = array_column($FOs->toArray(), 'branchId');

    if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {
        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where('admissionDate', '<=', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();
          //dd($Member,$date);

      }else{
        $primaryProductIdArray = DB::table('mfn_loans_product')
                                ->select('id')
                                ->where('productCategoryId', $RequestedProductCategoryID)
                                ->pluck('id')->toArray();

        $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->whereIn('mfn_member_information.primaryProductId', $primaryProductIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where('admissionDate', '<=', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

      }

    }else{

      $Member = DB::table('mfn_samity')
                    ->join('mfn_member_information', 'mfn_samity.id', '=', 'mfn_member_information.samityId')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_member_information.primaryProductId', $RequestedProductID)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_member_information.softDel', '=', 0)
                    ->where('admissionDate', '<=', $date)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_member_information.closingDate', '>',  $date)
                            ->orWhere('mfn_member_information.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_member_information.closingDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_member_information.id)) as 'memberCount'"))
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

    }

    foreach ($fieldOfficerIdArray as $key => $value) {
      if(array_key_exists($value, $Member)){
         $TotalMember[$value] = $Member[$value]->memberCount;
      }else{
        $TotalMember[$value] = 0;
      }
    }

    return $TotalMember;

    /*foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')
      // ->where('fieldOfficerId', $fId)->get();
      ->where(function ($query) use ($date, $fId, $bId) {
          $query->where([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '>',  $date ], ['softDel', '=', 0]])
          ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  '0000-00-00'], ['softDel', '=', 0]])
          ->orWhere([['fieldOfficerId', $fId], ['branchId', $bId], ['closingDate', '=',  null], ['softDel', '=', 0]]);
      })
      ->select('id')
      ->get();
    }

     //dd($Samity);
    $check = array();

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        foreach ($Samity as $key1 => $Samity1) {
          if (sizeof($Samity1) > 0) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $date = $Date[1];
              $Member[$key1][$Samity2->id] = $dbMembers
                ->where('softDel', '=', 0)
                ->where('samityId', $samityID)
                ->where('admissionDate', '<=', $date)
                ->filter(function ($value) use ($date) {
                    if (($value->closingDate > $date) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null)) {
                        return true;
                    }
                    else {
                        return false;
                    }
                })
                ->groupBy('id')
                ->count('id');
            }
            foreach ($Member as $key => $Members) {
              $TotalMember[$key] = array_sum($Members);
            }
          }
          else{
             $TotalMember[$key1] = 0;
          }
        }
      }
      else {
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $date = $Date[1];
            $Member[$key1][$Samity2->id] = $dbMembers
              ->where('softDel', '=', 0)
              ->where('samityId', $samityID)
              ->where('admissionDate', '<=', $date)
              ->where('primaryProductId', $RequestedProductID)
              ->filter(function ($value) use ($date) {
                  // return $value > 2;
                  if (($value->closingDate > $date) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null) ) {
                      return true;
                  }
                  else {
                      return false;
                  }
              })
              ->count('id');
          }
        }
        foreach ($Member as $key => $Members) {
          $TotalMember[$key] = array_sum($Members);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            $samityID = $Samity2->id;
            $date = $Date[1];
            
            foreach ($Product as $key => $Product1) {
              $RequestedProductID1 = $Product1->id;
              $Member[$key1][$Samity2->id][$Product1->id] = $dbMembers
                ->where('softDel', '=', 0)
                ->where('samityId', $samityID)
                ->where('admissionDate', '<=', $date)
                ->where('primaryProductId', $RequestedProductID1)
                ->filter(function ($value) use ($date) {
                    // return $value > 2;
                    if (($value->closingDate > $date) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null) ) {
                        return true;
                    }
                    else {
                        return false;
                    }
                })
                ->count('id');
            }

          }
        }
        foreach ($Member as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalMember[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $date = $Date[1];
            $Member[$key1][$Samity2->id] = $dbMembers
                ->where('softDel', '=', 0)
                ->where('samityId', $samityID)
                ->where('admissionDate', '<=', $date)
                ->where('primaryProductId', $RequestedProductID)
                ->filter(function ($value) use ($date) {
                    // return $value > 2;
                    if (($value->closingDate > $date) || ($value->closingDate == '0000-00-00') || ($value->closingDate == null) ) {
                        return true;
                    }
                    else {
                        return false;
                    }
                })
                ->count('mfn_member_information.id');
          }
        }
        foreach ($Member as $key => $Members) {
          $TotalMember[$key] = array_sum($Members);
        }
      }
    }*/

      // // dd($Member);

      return $TotalMember;
    }

    public function SavingsProductQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $Samity = array();
      $SavingsProduct = array();
      $LoanProducts = array();
      $Members = array();
      $Savings = array();
      $Totalsavings = array();
      $date = $Date[1];

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $SavingsProduct[$key1][$Samity2->id] = DB::table('mfn_savings_account')
                    ->select('savingsProductIdFk')
                    ->where([['samityIdFk', $Samity2->id], ['branchIdFk', $RequestedBranchID]])
                    ->groupBy('savingsProductIdFk')
                    ->get();
        }
      }
      $LoanProducts = DB::table('mfn_loans_product')->where('productCategoryId', $RequestedProductCategoryID)->get();
      // // dd($LoanProducts);
      $totalAccount = 0;

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
          foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
            foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
              foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

                $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

                // WE HAVE A REMOVED ACCOUNT OPENING DATE (['accountOpeningDate', '<=', $date])
                $savingAccIdArr = DB::table('mfn_savings_account')
                    // ->where([['samityIdFk', $samity->id], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProduct->id]])
                    ->where(function ($query) use ($date, $savingsProductIdFk, $key2) {
                        $query->where([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '>', $date]])
                        ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', '0000-00-00']])
                        ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', null]]);
                    })
                    ->pluck('id')
                    ->toArray();
                // dd($savingAccIdArr);

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                    ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                        ['depositDate', '<=', $date]])
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->sum('amount');

                $openingBalance = DB::table('mfn_opening_savings_account_info')
                    ->whereIn('savingsAccIdFk',$savingAccIdArr)
                    ->sum('openingBalance');

                $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->where([['withdrawDate', '<=', $date], ['softDel', '=', 0]])
                    ->sum('amount');

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
              }
            }
          }

          // dd($totalAccount);
        }
        elseif ($RequestedProductID != 'All') {
          foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
            foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
              foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

                $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

                $savingAccIdArr = DB::table('mfn_savings_account')
                    ->join('mfn_loan', 'mfn_savings_account.samityIdFk', '=', 'mfn_loan.samityIdFk')
                    ->where(function ($query) use ($date, $savingsProductIdFk, $key2, $RequestedProductID) {
                        $query->where([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '>', $date]])
                        ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', '0000-00-00']])
                        ->orWhere([['mfn_loan.productIdFk',  $RequestedProductID], ['mfn_savings_account.samityIdFk', $key2], ['mfn_savings_account.softDel', '=', 0], ['mfn_savings_account.savingsProductIdFk', $savingsProductIdFk], ['mfn_savings_account.closingDate', '=', null]]);
                    })
                    ->pluck('mfn_savings_account.id')
                    ->toArray();
                // dd($savingAccIdArr);

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                    ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                        ['depositDate', '<=', $date]])
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->sum('amount');

                $openingBalance = DB::table('mfn_opening_savings_account_info')
                    ->whereIn('savingsAccIdFk',$savingAccIdArr)
                    ->sum('openingBalance');

                $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->where([['withdrawDate', '<=', $date], ['softDel', '=', 0]])
                    ->sum('amount');

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
              }
            }
          }
        }
      }
      else {
        if ($RequestedProductID == 'All') {
          foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
            foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
              foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
                foreach ($LoanProducts as $key => $LoanProduct) {

                  $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

                  $savingAccIdArr = DB::table('mfn_savings_account')
                      ->where(function ($query) use ($date, $savingsProductIdFk, $key2) {
                          $query->where([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '>', $date]])
                          ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', '0000-00-00']])
                          ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', null]]);
                      })
                      ->pluck('id')
                      ->toArray();
                  // dd($savingAccIdArr);

                  $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                      ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                          ['depositDate', '<=', $date]])
                      ->whereIn('accountIdFk', $savingAccIdArr)
                      ->sum('amount');

                  $openingBalance = DB::table('mfn_opening_savings_account_info')
                      ->whereIn('savingsAccIdFk',$savingAccIdArr)
                      ->sum('openingBalance');

                  $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                      ->whereIn('accountIdFk', $savingAccIdArr)
                      ->where([['withdrawDate', '<=', $date], ['softDel', '=', 0]])
                      ->sum('amount');

                  $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
                }
              }
            }
          }
        }
        elseif ($RequestedProductID != 'All') {
          foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
            foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
              foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

                $savingsProductIdFk = $SavingsProduct3->savingsProductIdFk;

                $savingAccIdArr = DB::table('mfn_savings_account')
                    ->where(function ($query) use ($date, $savingsProductIdFk, $key2) {
                        $query->where([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '>', $date]])
                        ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', '0000-00-00']])
                        ->orWhere([['samityIdFk', $key2], ['softDel', '=', 0], ['savingsProductIdFk', $savingsProductIdFk], ['closingDate', '=', null]]);
                    })
                    ->pluck('id')
                    ->toArray();
                // dd($savingAccIdArr);

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                    ->where([['samityIdFk', $key2], ['softDel', '=', 0], ['productIdFk', $SavingsProduct3->savingsProductIdFk],
                        ['depositDate', '<=', $date]])
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->sum('amount');

                $openingBalance = DB::table('mfn_opening_savings_account_info')
                    ->whereIn('savingsAccIdFk',$savingAccIdArr)
                    ->sum('openingBalance');

                $savingWithdrawAmount = DB::table('mfn_savings_withdraw')
                    ->whereIn('accountIdFk', $savingAccIdArr)
                    ->where([['withdrawDate', '<=', $date], ['softDel', '=', 0]])
                    ->sum('amount');

                $Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = ($Savings[$key1][$key2][$SavingsProduct3->savingsProductIdFk] + $openingBalance) - $savingWithdrawAmount;
              }
            }
          }
        }
      }


      // // dd($Savings);

      return $Savings;
    }

    public function LoanDisburseNoQueryEOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $Samity       = array();
      $MemberCount  = 0;
      $Loans        = array();
      $TotalLoans   = array();
      $date         = $Date[1];

      $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
      $branchIdArray       = array_column($FOs->toArray(), 'branchId');


      //need to work in here
      if ($RequestedProductID == 'All') {
      if ($RequestedProductCategoryID == 'All') {

        $Loans = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();
        
      }else{

        $ProductArray = DB::table('mfn_loans_product')
                  ->select('id')
                  ->where('productCategoryId', $RequestedProductCategoryID)
                  ->pluck('id')->toArray();
         
        $Loans = DB::table('mfn_samity')
                ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                ->whereIn('mfn_samity.branchId',$branchIdArray)
                ->where('mfn_samity.softDel', '=', 0)
                ->where('mfn_loan.softDel', '=', 0)
                ->where('disbursementDate', '<', $date)
                ->whereIn('productIdFk', $ProductArray)
                ->where(function ($query) use ($date) {
                        $query->where('mfn_samity.closingDate', '>',  $date)
                        ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                        ->orWhere('mfn_samity.closingDate', '=',  null);
                    })
                ->where(function ($query) use ($date) {
                        $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                        ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                    })
                ->groupBy('fieldOfficerId')
                ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                //->toSQL();
                ->get()
                ->keyBy('fieldOfficerId')->toArray();

      }
    }else{

      $Loans = DB::table('mfn_samity')
                    ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                    ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                    ->whereIn('mfn_samity.branchId',$branchIdArray)
                    ->where('mfn_samity.softDel', '=', 0)
                    ->where('mfn_loan.softDel', '=', 0)
                    ->where('disbursementDate', '<', $date)
                    ->where('productIdFk',$RequestedProductID)
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_samity.closingDate', '>',  $date)
                            ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                            ->orWhere('mfn_samity.closingDate', '=',  null);
                        })
                    ->where(function ($query) use ($date) {
                            $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                            ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                        })
                    ->groupBy('fieldOfficerId')
                    ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(COUNT(mfn_loan.id)) as 'loanCount'"))
                    //->toSQL();
                    ->get()
                    ->keyBy('fieldOfficerId')->toArray();

    }
    foreach ($fieldOfficerIdArray as $key => $value) {
          if(array_key_exists($value, $Loans)){
            $TotalLoans[$value] = $Loans[$value]->loanCount;
          }else{
            $TotalLoans[$value] = 0;
          }
        }
        return $TotalLoans;


    /*  foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
            // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;

              $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                  // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]]])
                  ->where(function ($query) use ($date, $samityID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->count('id');
            }
          }

          foreach ($Loans as $key => $Loan) {
            $TotalLoans[$key] = array_sum($Loan);
          }
        }
        else {
            // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                  // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->count('id');
            }
          }

          foreach ($Loans as $key => $Loan) {
            $TotalLoans[$key] = array_sum($Loan);
          }
        }
      }
      else {
        if ($RequestedProductID == 'All') {
            // code.....
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                $samityID = $Samity2->id;
                foreach ($Product as $key => $Products) {
                  $RequestedProductID1 = $Products->id;
                  $Loans[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $Products->id]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                          $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
                }
              }
            }

            // // dd($LoanAmounts);

            foreach ($Loans as $key1 => $Member1) {
              foreach ($Member1 as $key2 => $Member2) {
                foreach ($Member2 as $key => $Member3) {
                  $MemberCount = $MemberCount + $Member3;
                }

              }
              $TotalLoans[$key1] = $MemberCount;
              $MemberCount = 0;
            }
        }
        else {
            // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $Loans[$key1][$Samity2->id] = DB::table('mfn_loan')
                  // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->count('id');
            }
          }

          foreach ($Loans as $key => $Loan) {
            $TotalLoans[$key] = array_sum($Loan);
          }
        }
      }

      // // dd($TotalLoans);

      return $TotalLoans;*/

    }

    public function LoanDisburseAmountsQueryEOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
      $Samity           = array();
      $MemberCount      = 0;
      $LoanAmounts      = array();
      $TotalLoanAmounts = array();
      $date             = $Date[1];

      $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
      $branchIdArray       = array_column($FOs->toArray(), 'branchId');

      if ($RequestedProductID == 'All') {
        if ($RequestedProductCategoryID == 'All') {
              if ($RequestedServiceCharge == 'WithServiceCharge') {
                $LoanAmounts = DB::table('mfn_samity')
                      ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                      ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                      ->whereIn('mfn_samity.branchId',$branchIdArray)
                      ->where('mfn_samity.softDel', '=', 0)
                      ->where('mfn_loan.softDel', '=', 0)
                      ->where('disbursementDate', '<=', $date)
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_samity.closingDate', '>',  $date)
                              ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                              ->orWhere('mfn_samity.closingDate', '=',  null);
                          })
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                          })
                      ->groupBy('fieldOfficerId')
                      ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                      //->toSQL();
                      ->get()
                      ->keyBy('fieldOfficerId')->toArray();
              }else{
                 $LoanAmounts = DB::table('mfn_samity')
                      ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                      ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                      ->whereIn('mfn_samity.branchId',$branchIdArray)
                      ->where('mfn_samity.softDel', '=', 0)
                      ->where('mfn_loan.softDel', '=', 0)
                      ->where('disbursementDate', '<=', $date)
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_samity.closingDate', '>',  $date)
                              ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                              ->orWhere('mfn_samity.closingDate', '=',  null);
                          })
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                          })
                      ->groupBy('fieldOfficerId')
                      ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                      //->toSQL();
                      ->get()
                      ->keyBy('fieldOfficerId')->toArray();
              }      
        }else{

          $ProductArray = DB::table('mfn_loans_product')
                    ->select('id')
                    ->where('productCategoryId', $RequestedProductCategoryID)
                    ->pluck('id')->toArray();

          if ($RequestedServiceCharge == 'WithServiceCharge') {
            $LoanAmounts = DB::table('mfn_samity')
                  ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                  ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                  ->whereIn('mfn_samity.branchId',$branchIdArray)
                  ->where('mfn_samity.softDel', '=', 0)
                  ->where('mfn_loan.softDel', '=', 0)
                  ->where('disbursementDate', '<=', $date)
                  ->whereIn('productIdFk', $ProductArray)
                  ->where(function ($query) use ($date) {
                          $query->where('mfn_samity.closingDate', '>',  $date)
                          ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                          ->orWhere('mfn_samity.closingDate', '=',  null);
                      })
                  ->where(function ($query) use ($date) {
                          $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                          ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                          ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                      })
                  ->groupBy('fieldOfficerId')
                  ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                  //->toSQL();
                  ->get()
                  ->keyBy('fieldOfficerId')->toArray();

          }else{
            $LoanAmounts = DB::table('mfn_samity')
                  ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                  ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                  ->whereIn('mfn_samity.branchId',$branchIdArray)
                  ->where('mfn_samity.softDel', '=', 0)
                  ->where('mfn_loan.softDel', '=', 0)
                  ->where('disbursementDate', '<', $date)
                  ->whereIn('productIdFk', $ProductArray)
                  ->where(function ($query) use ($date) {
                          $query->where('mfn_samity.closingDate', '>',  $date)
                          ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                          ->orWhere('mfn_samity.closingDate', '=',  null);
                      })
                  ->where(function ($query) use ($date) {
                          $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                          ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                          ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                      })
                  ->groupBy('fieldOfficerId')
                  ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                  //->toSQL();
                  ->get()
                  ->keyBy('fieldOfficerId')->toArray();
          }
        }
      }else{
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          $LoanAmounts = DB::table('mfn_samity')
                      ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                      ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                      ->whereIn('mfn_samity.branchId',$branchIdArray)
                      ->where('mfn_samity.softDel', '=', 0)
                      ->where('mfn_loan.softDel', '=', 0)
                      ->where('disbursementDate', '<=', $date)
                      ->where('productIdFk',$RequestedProductID)
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_samity.closingDate', '>',  $date)
                              ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                              ->orWhere('mfn_samity.closingDate', '=',  null);
                          })
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                          })
                      ->groupBy('fieldOfficerId')
                      ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(totalrepayAmount)) as 'loanAmount'"))
                      //->toSQL();
                      ->get()
                      ->keyBy('fieldOfficerId')->toArray();
        }else{
          $LoanAmounts = DB::table('mfn_samity')
                      ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                      ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                      ->whereIn('mfn_samity.branchId',$branchIdArray)
                      ->where('mfn_samity.softDel', '=', 0)
                      ->where('mfn_loan.softDel', '=', 0)
                      ->where('disbursementDate', '<=', $date)
                      ->where('productIdFk',$RequestedProductID)
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_samity.closingDate', '>',  $date)
                              ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                              ->orWhere('mfn_samity.closingDate', '=',  null);
                          })
                      ->where(function ($query) use ($date) {
                              $query->where('mfn_loan.loanCompletedDate', '>',  $date)
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  '0000-00-00')
                              ->orWhere('mfn_loan.loanCompletedDate', '=',  null);
                          })
                      ->groupBy('fieldOfficerId')
                      ->select(DB::raw("(GROUP_CONCAT(DISTINCT mfn_samity.id SEPARATOR ',')) as 'samityId'"),'fieldOfficerId',DB::raw("(SUM(loanAmount)) as 'loanAmount'"))
                      //->toSQL();
                      ->get()
                      ->keyBy('fieldOfficerId')->toArray();
        }
      }
      foreach ($fieldOfficerIdArray as $key => $value) {
        if(array_key_exists($value, $LoanAmounts)){
          $TotalLoanAmounts[$value] = $LoanAmounts[$value]->loanAmount;
        }else{
          $TotalLoanAmounts[$value] = 0;
        }
      }
      return $TotalLoanAmounts;

     /* foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('totalrepayAmount');
              }
            }

            foreach ($LoanAmounts as $key => $LoanAmount) {
              $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            }
          }
          else {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('loanAmount');
              }
            }

            foreach ($LoanAmounts as $key => $LoanAmount) {
              $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            }
          }
        }
        else {
          //productCategory all but product selected
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('totalrepayAmount');
              }
            }

            foreach ($LoanAmounts as $key => $LoanAmount) {
              $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            }
          }
          else {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('loanAmount');
              }
            }

            foreach ($LoanAmounts as $key => $LoanAmount) {
              $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            }
          }
        }
      }
      else {
        if ($RequestedProductID == 'All') {
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code... here......
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                foreach ($Product as $key => $Products) {

                  $samityID = $Samity2->id;
                  $RequestedProductID1 = $Products->id;

                  $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $Products->id]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->sum('totalrepayAmount');
                }
              }
            }

            // // dd($LoanAmounts);

            foreach ($LoanAmounts as $key1 => $Member1) {
              foreach ($Member1 as $key2 => $Member2) {
                foreach ($Member2 as $key => $Member3) {
                  $MemberCount = $MemberCount + $Member3;
                }

              }
              $TotalLoanAmounts[$key1] = $MemberCount;
              $MemberCount = 0;
            }
            // // dd($TotalLoanAmounts);

            // foreach ($LoanAmounts as $key => $LoanAmount) {
            //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            // }
          }
          else {
            // code...
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                foreach ($Product as $key => $Products) {

                  $samityID = $Samity2->id;
                  $RequestedProductID1 = $Products->id;

                  $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                            // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $Products->id]])
                            ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                                $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                                ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                                ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                            })
                            ->sum('loanAmount');
                }
              }
            }

            // // dd($LoanAmounts);

            foreach ($LoanAmounts as $key1 => $Member1) {
              foreach ($Member1 as $key2 => $Member2) {
                foreach ($Member2 as $key => $Member3) {
                  $MemberCount = $MemberCount + $Member3;
                }

              }
              $TotalLoanAmounts[$key1] = $MemberCount;
              $MemberCount = 0;
            }
          }
        }
        else {
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('totalrepayAmount');
              }
            }

            foreach ($LoanAmounts as $key => $LoanAmount) {
              $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            }
          }
          else {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {

                $samityID = $Samity2->id;

                $LoanAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['productIdFk', $RequestedProductID]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                          })
                          ->sum('loanAmount');
              }
            }

            foreach ($LoanAmounts as $key => $LoanAmount) {
              $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            }
          }
        }
      }

      // // dd($TotalLoans);

      return $TotalLoanAmounts;*/

    }

    public function LoanfullyPaidNoQueryEOW($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $Samity               = array();
      $MemberCount          = 0;
      $LoanFullyPaids       = array();
      $TotalLoanFullyPaids  = array();

      $date = $Date[1];

      $fieldOfficerIdArray = array_column($FOs->toArray(), 'id');
      $branchIdArray       = array_column($FOs->toArray(), 'branchId');

      if ($RequestedProductID == 'All') {
        if ($RequestedProductCategoryID == 'All') {
          $LoanFullyPaids = DB::table('mfn_samity')
                            ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                            ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                            ->whereIn('mfn_samity.branchId',$branchIdArray)
                            ->where('mfn_samity.softDel', '=', 0)
                            ->where('mfn_loan.softDel', '=', 0)
                            /*->whereDate('mfn_loan.disbursementDate', '<', $date)*/
                            ->whereDate('mfn_loan.loanCompletedDate', '<=', $date)
                            ->where(function ($query) use ($date) {
                                    $query->where('mfn_samity.closingDate', '>',  $date)
                                    ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                                    ->orWhere('mfn_samity.closingDate', '=',  null);
                            })
                            
                            ->groupBy('fieldOfficerId')
                            ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                            //->toSQL();
                            ->get()
                            ->keyBy('fieldOfficerId')->toArray();

        }else{
          $ProductArray = DB::table('mfn_loans_product')
                    ->select('id')
                    ->where('productCategoryId', $RequestedProductCategoryID)
                    ->pluck('id')->toArray();

          $LoanFullyPaids = DB::table('mfn_samity')
                            ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                            ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                            ->whereIn('mfn_samity.branchId',$branchIdArray)
                            ->whereIn('productIdFk', $ProductArray)
                            ->where('mfn_samity.softDel', '=', 0)
                            ->where('mfn_loan.softDel', '=', 0)
                            /*->whereDate('mfn_loan.disbursementDate', '<', $date)*/
                            ->whereDate('mfn_loan.loanCompletedDate', '<=', $date)
                            ->where(function ($query) use ($date) {
                                    $query->where('mfn_samity.closingDate', '>',  $date)
                                    ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                                    ->orWhere('mfn_samity.closingDate', '=',  null);
                            })
                            
                            ->groupBy('fieldOfficerId')
                            ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                            //->toSQL();
                            ->get()
                            ->keyBy('fieldOfficerId')->toArray();

        }
      }else{
        $LoanFullyPaids = DB::table('mfn_samity')
                            ->join('mfn_loan', 'mfn_samity.id', '=', 'mfn_loan.samityIdFk')
                            ->whereIn('mfn_samity.fieldOfficerId',$fieldOfficerIdArray)
                            ->whereIn('mfn_samity.branchId',$branchIdArray)
                            ->where('productIdFk',$RequestedProductID)
                            ->where('mfn_samity.softDel', '=', 0)
                            ->where('mfn_loan.softDel', '=', 0)
                            /*->whereDate('mfn_loan.disbursementDate', '<', $date)*/
                            ->whereDate('mfn_loan.loanCompletedDate', '<=', $date)
                            ->where(function ($query) use ($date) {
                                    $query->where('mfn_samity.closingDate', '>',  $date)
                                    ->orWhere('mfn_samity.closingDate', '=',  '0000-00-00')
                                    ->orWhere('mfn_samity.closingDate', '=',  null);
                            })
                            ->groupBy('fieldOfficerId')
                            ->select(DB::raw("(COUNT(mfn_loan.id)) as 'loanId'"))
                            //->toSQL();
                            ->get()
                            ->keyBy('fieldOfficerId')->toArray();

      }
      foreach ($fieldOfficerIdArray as $key => $value) {
        if(array_key_exists($value, $LoanFullyPaids)){
          $TotalLoanFullyPaids[$value] = $LoanFullyPaids[$value]->loanId;
        }else{
          $TotalLoanFullyPaids[$value] = 0;
        }
      }
      return $TotalLoanFullyPaids;

      /*foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]])
                        // ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        //     $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '<=', $date], ['softDel', '=', 0]])
                        //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['softDel', '=', 0]])
                        //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                        // })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
            $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
          }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '<=', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]])
                        // ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        //     $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '<=', $date], ['softDel', '=', 0]])
                        //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '!=', '0000-00-00'], ['softDel', '=', 0]])
                        //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                        // })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
            $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
          }
        }
      }
      else {
        if ($RequestedProductID == 'All') {
          // code.....
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {

                $RequestedProductID1 = $Products->id;
                $samityID = $Samity2->id;

                $LoanFullyPaids[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                    ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $Products->id], ['softDel', '=', 0]])
                    // ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                    //     $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID1], ['disbursementDate', '<=', $date], ['loanCompletedDate', '<=', $date], ['softDel', '=', 0]])
                    //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '!=', '0000-00-00'], ['softDel', '=', 0]])
                    //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                    // })
                    ->count('id');
              }
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanFullyPaids as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanFullyPaids[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {

              $samityID = $Samity2->id;

              $LoanFullyPaids[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['disbursementDate', '<=', $date], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['productIdFk', $RequestedProductID]])
                        // ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        //     $query->where([['samityIdFk', $samityID], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '<=', $date], ['softDel', '=', 0]])
                        //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '!=', '0000-00-00'], ['softDel', '=', 0]])
                        //     ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['productIdFk', $RequestedProductID1], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]]);
                        // })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanFullyPaids as $key => $LoanFullyPaid) {
            $TotalLoanFullyPaids[$key] = array_sum($LoanFullyPaid);
          }
        }
      }

      // // dd($TotalLoans);

      return $TotalLoanFullyPaids;*/
    }

    public function LoanFullyPaidAmountsQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
      $Samity = array();
      $MemberCount = 0;
      $LoanFullyAmounts = array();
      $TotalLoanFullyAmounts = array();

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
          // code.....
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]])
                          ->sum('totalrepayAmount');
              }
            }

            foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
              $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
            }
          }
          else {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0]])
                          ->sum('loanAmount');
              }
            }

            foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
              $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
            }
          }
        }
        else {
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]],['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->sum('totalrepayAmount');
              }
            }

            foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
              $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
            }
          }
          else {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->sum('loanAmount');
              }
            }

            foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
              $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
            }
          }
        }
      }
      else {
        if ($RequestedProductID == 'All') {
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code... here......
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                foreach ($Product as $key => $Products) {
                  $LoanFullyAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                            ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $Products->id]])
                            ->sum('totalrepayAmount');
                }
              }
            }

            // // dd($LoanAmounts);

            foreach ($LoanFullyAmounts as $key1 => $Member1) {
              foreach ($Member1 as $key2 => $Member2) {
                foreach ($Member2 as $key => $Member3) {
                  $MemberCount = $MemberCount + $Member3;
                }

              }
              $TotalLoanFullyAmounts[$key1] = $MemberCount;
              $MemberCount = 0;
            }
            // // dd($TotalLoanAmounts);

            // foreach ($LoanAmounts as $key => $LoanAmount) {
            //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
            // }
          }
          else {
            // code...
            $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                foreach ($Product as $key => $Products) {
                  $LoanAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                            ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $Products->id]])
                            ->sum('loanAmount');
                }
              }
            }

            // // dd($LoanAmounts);

            foreach ($LoanAmounts as $key1 => $Member1) {
              foreach ($Member1 as $key2 => $Member2) {
                foreach ($Member2 as $key => $Member3) {
                  $MemberCount = $MemberCount + $Member3;
                }

              }
              $TotalLoanAmounts[$key1] = $MemberCount;
              $MemberCount = 0;
            }
          }
        }
        else {
          if ($RequestedServiceCharge == 'WithServiceCharge') {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->sum('totalrepayAmount');
              }
            }

            foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
              $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
            }
          }
          else {
            // code...
            foreach ($Samity as $key1 => $Samity1) {
              foreach ($Samity1 as $key2 => $Samity2) {
                $LoanFullyAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['loanCompletedDate', '<=', $Date[1]], ['loanCompletedDate', '!=', '0000-00-00'], ['loanCompletedDate', '!=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->sum('loanAmount');
              }
            }

            foreach ($LoanFullyAmounts as $key => $LoanFullyAmount) {
              $TotalLoanFullyAmounts[$key] = array_sum($LoanFullyAmount);
            }
          }
        }
      }

      // // dd($TotalLoans);

      return $TotalLoanFullyAmounts;
    }

    public function LoanExpiredNoQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $Samity = array();
      $MemberCount = 0;
      $LoanExpiredNo = array();
      $TotalLoanExpiredNo = array();

      $date = $Date[1];

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key => $LoanExpired) {
            $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
          }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key => $LoanExpired) {
            $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
          }
        }
      }
      else {
        if ($RequestedProductID == 'All') {
          // code.....
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $RequestedProductID1 = $Products->id;
                $LoanExpiredNo[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                          })
                          ->count('id');
              }
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredNo[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key => $LoanFullyPaid) {
            $TotalLoanExpiredNo[$key] = array_sum($LoanFullyPaid);
          }
        }
      }

      // // dd($TotalLoans);

      return $TotalLoanExpiredNo;
    }

    public function LoanExpiredAmountQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
      $Samity = array();
      $MemberCount = 0;
      $LoanExpiredAmounts = array();
      $TotalLoanExpiredAmounts = array();

      $date = $Date[1];

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }

          // dd($TotalLoanExpiredAmounts);
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('totalRepayAmount');
                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('amount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('paidLoanAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('loanAmount');

                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('principalAmount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('principalAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // dd($TotalLoanExpiredAmounts);
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();


              if(count($loanExpiredInfos) > 0){
                //dd($loanExpiredInfos);
                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

              }
              

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }


      // // dd($TotalLoans);

      return $TotalLoanExpiredAmounts;
    }

    public function LoanCurrentNoQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $Samity = array();
      $MemberCount = 0;
      $LoanCurrentNo = array();
      $TotalLoanCurrentNo = array();

      $date = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanCurrentNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->count('id');
          }
        }

        // dd($LoanCurrentNo);

        foreach ($LoanCurrentNo as $key => $LoanCurrent) {
          $TotalLoanCurrentNo[$key] = array_sum($LoanCurrent);
        }
        // dd($TotalLoanCurrentNo);
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanCurrentNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanCurrentNo as $key => $LoanCurrent) {
          $TotalLoanCurrentNo[$key] = array_sum($LoanCurrent);
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            foreach ($Product as $key => $Products) {
              $RequestedProductID1 = $Products->id;
              $LoanCurrentNo[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                        })
                        ->count('id');
            }
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanCurrentNo as $key1 => $Member1) {
          foreach ($Member1 as $key2 => $Member2) {
            foreach ($Member2 as $key => $Member3) {
              $MemberCount = $MemberCount + $Member3;
            }

          }
          $TotalLoanCurrentNo[$key1] = $MemberCount;
          $MemberCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanCurrentNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->count('id');
          }
        }

        // // dd($LoanFullyPaids);

        foreach ($LoanCurrentNo as $key => $LoanCurrent) {
          $TotalLoanCurrentNo[$key] = array_sum($LoanCurrent);
        }
      }
    }

      // // dd($TotalLoans);

      return $TotalLoanCurrentNo;
    }

    public function LoanCurrentAmountsQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
      $Samity = array();
      $MemberCount = 0;
      $LoanCurrentAmounts = array();
      $TotalLoanCurrentAmounts = array();

      $date = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }

          // dd($TotalLoanExpiredAmounts);
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('loanAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->sum('loanAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanFullyAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('loanAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanFullyAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('totalRepayAmount');
                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->sum('totalRepayAmount');
              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanCurrentAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanCurrentAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('loanAmount');

                $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->sum('loanAmount');
              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanCurrentAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanCurrentAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // dd($TotalLoanExpiredAmounts);
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanCurrentAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $LoanCurrentAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->sum('loanAmount');
            }
          }

          foreach ($LoanCurrentAmounts as $key => $LoanCurrentAmount) {
            $TotalLoanCurrentAmounts[$key] = array_sum($LoanCurrentAmount);
          }
        }
      }
    }

      // // dd($TotalLoans);

      return $TotalLoanCurrentAmounts;
    }

    public function LoanCurrentDueNoQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $LoaneeCount = 0;

      $Samity = array();
      $LoanIds = array();
      $LoanCurrentDueNoCollection = array();
      $LoanCurrentDueNoSchedule = array();
      $TotalLoanCurrentDueNo = array();

      $date = $Date[1];

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchID', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->select('id')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '>=', $Date[1]], ['isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->groupBy('id')
                        ->get();
            }
          }

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          // ->select('mfn_loan.id', 'mfn_loan_collection.amount')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_collection.amount');
                          // ->get();

                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                          DB::table('mfn_opening_balance_loan')
                          ->where('loanIdFk', $LoanIds3->id)
                          ->sum('paidLoanAmountOB');
              }
            }
          }

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          // ->select('mfn_loan.id', 'mfn_loan_schedule.installmentAmount')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_schedule.installmentAmount');
                          // ->get();
              }
            }
          }

          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              ++$LoaneeCount;
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
            $LoaneeCount = 0;
          }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->select('id')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '>=', $Date[1]], ['isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->groupBy('id')
                        ->get();
            }
          }

          // // dd($LoanIds);

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_collection.amount');

                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                          DB::table('mfn_opening_balance_loan')
                          ->where('loanIdFk', $LoanIds3->id)
                          ->sum('paidLoanAmountOB');
              }
            }
          }

          // // dd($LoanCurrentDueNoCollection);

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_schedule.installmentAmount');
              }
            }
          }

          // // dd($LoanCurrentDueNoSchedule);

          // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
            foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
              foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
                foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                  // code...
                  foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                    if ($key1 == $key1A) {
                      foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                        if ($key2 == $key2A) {
                          foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                            if ($key3 == $key3A) {
                              // code...
                              if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                                ++$LoaneeCount;
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
              $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
              $LoaneeCount = 0;
            }

          // // dd($TotalLoanCurrentDueNo);

        }
      }
      else {
        if ($RequestedProductID == 'All') {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->select('id')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '>=', $Date[1]], ['isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID, $RequestedProductCategoryID) {
                            $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '>=', $date], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                            ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]]);
                        })
                        ->groupBy('id')
                        ->get();
            }
          }

          // // dd($LoanIds);

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_collection.amount');

                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                          DB::table('mfn_opening_balance_loan')
                          ->where('loanIdFk', $LoanIds3->id)
                          ->sum('paidLoanAmountOB');
              }
            }
          }

          // // dd($LoanCurrentDueNoCollection);

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_schedule.installmentAmount');
              }
            }
          }

          // // dd($LoanCurrentDueNoSchedule);

          // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
            foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
              foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
                foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                  // code...
                  foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                    if ($key1 == $key1A) {
                      foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                        if ($key2 == $key2A) {
                          foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                            if ($key3 == $key3A) {
                              // code...
                              if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                                ++$LoaneeCount;
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
              $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
              $LoaneeCount = 0;
            }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                        ->select('id')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '>=', $Date[1]], ['isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->groupBy('id')
                        ->get();
            }
          }

          // // dd($LoanIds);

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_collection.amount');

                $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                          DB::table('mfn_opening_balance_loan')
                          ->where('loanIdFk', $LoanIds3->id)
                          ->sum('paidLoanAmountOB');
              }
            }
          }

          // // dd($LoanCurrentDueNoCollection);

          foreach ($LoanIds as $key1 => $LoanIds1) {
            foreach ($LoanIds1 as $key2 => $LoanIds2) {
              foreach ($LoanIds2 as $key3 => $LoanIds3) {
                $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.lastInstallmentDate', '>=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[1]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                          ->sum('mfn_loan_schedule.installmentAmount');
              }
            }
          }

          // // dd($LoanCurrentDueNoSchedule);

          // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
            foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
              foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
                foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                  // code...
                  foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                    if ($key1 == $key1A) {
                      foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                        if ($key2 == $key2A) {
                          foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                            if ($key3 == $key3A) {
                              // code...
                              if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                                ++$LoaneeCount;
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
              $TotalLoanCurrentDueNo[$key1] = $LoaneeCount;
              $LoaneeCount = 0;
            }
        }
      }

      // // dd($TotalLoanCurrentDueNo);

      return $TotalLoanCurrentDueNo;
    }

    public function LoanCurrentDueAmountsQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $LoaneeAmountCount = 0;

      $Samity = array();
      $LoanIds = array();
      $LoanCurrentDueNoCollection = array();
      $LoanCurrentDueNoSchedule = array();
      $TotalLoanCurrentDueNo = array();

    $date = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    // // dd($FOs);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[0]], ['lastInstallmentDate', '>', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        // ->select('mfn_loan.id', 'mfn_loan_collection.amount')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');
                        // ->get();

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        // ->select('mfn_loan.id', 'mfn_loan_schedule.installmentAmount')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
                        // ->get();
            }
          }
        }

        foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
          foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
            foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
              // code...
              foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                if ($key1 == $key1A) {
                  foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                    if ($key2 == $key2A) {
                      foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                        if ($key3 == $key3A) {
                          // code...
                          if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                            $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
          $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
          $LoaneeAmountCount = 0;
        }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan_collection.collectionDate', '<=', $Date[0]], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
            $LoaneeAmountCount = 0;
          }

        // // dd($TotalLoanCurrentDueNo);

      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                      ->select('mfn_loan.id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID, $RequestedProductCategoryID) {
                          $query->where([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '>=', $date], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                          ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', '0000-00-00'], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]])
                          ->orWhere([['mfn_loan.samityIdFk', $samityID], ['mfn_loan.disbursementDate', '<=', $date], ['mfn_loan.lastInstallmentDate', '>=', $date], ['mfn_loan.loanCompletedDate', '=', null], ['mfn_loan.softDel', '=', 0], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID]]);
                      })
                      ->groupBy('mfn_loan.id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
            $LoaneeAmountCount = 0;
          }
      }
      else {
        // code.....
        foreach ($Samity as $key1 => $Samity1) {
          foreach ($Samity1 as $key2 => $Samity2) {
            $samityID = $Samity2->id;
            $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                      ->select('id')
                      // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '>=', $Date[0]], ['isLoanCompleted', '=', 0]])
                      ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                          $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                          ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '>=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                      })
                      ->groupBy('id')
                      ->get();
          }
        }

        // // dd($LoanIds);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectiondate', '<=', $Date[0]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_schedule', 'mfn_loan.id', '=', 'mfn_loan_schedule.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<', $Date[0]], ['mfn_loan.lastInstallmentDate', '>=', $Date[0]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_schedule.scheduleDate', '<=', $Date[0]], ['mfn_loan_schedule.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_schedule.installmentAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        // if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
          foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
            foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
              foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
                // code...
                foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
                  if ($key1 == $key1A) {
                    foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                      if ($key2 == $key2A) {
                        foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                          if ($key3 == $key3A) {
                            // code...
                            if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                              $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
            $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
            $LoaneeAmountCount = 0;
          }
      }
    }
      /**/

      return $TotalLoanCurrentDueNo;
    }

    public function LoanExpiredDueNoQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedBranchID){
      $LoaneeCount = 0;

      $Samity = array();
      $MemberCount = 0;
      $LoanExpiredNo = array();
      $TotalLoanExpiredNo = array();

      $date = $Date[1];

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
        if ($RequestedProductID == 'All') {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                        })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key => $LoanExpired) {
            $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
          }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key => $LoanExpired) {
            $TotalLoanExpiredNo[$key] = array_sum($LoanExpired);
          }
        }
      }
      else {
        if ($RequestedProductID == 'All') {
          // code.....
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              foreach ($Product as $key => $Products) {
                $RequestedProductID1 = $Products->id;
                $LoanExpiredNo[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                          // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                          ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                              $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                              ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                          })
                          ->count('id');
              }
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredNo[$key1] = $MemberCount;
            $MemberCount = 0;
          }
        }
        else {
          // code.....
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $samityID = $Samity2->id;
              $LoanExpiredNo[$key1][$Samity2->id] = DB::table('mfn_loan')
                        // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['lastInstallmentDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                            $query->where([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                            ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                        })
                        ->count('id');
            }
          }

          // // dd($LoanFullyPaids);

          foreach ($LoanExpiredNo as $key => $LoanFullyPaid) {
            $TotalLoanExpiredNo[$key] = array_sum($LoanFullyPaid);
          }
        }
      }

      // // dd($TotalLoanCurrentDueNo);

      return $TotalLoanExpiredNo;
    }
  /*Will start from here*/
    public function LoanExpiredDueAmountsQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedServiceCharge, $RequestedProductID,  $RequestedBranchID){
      $LoaneeAmountCount = 0;

      $Samity = array();
      $MemberCount = 0;
      $LoanExpiredAmounts = array();
      $TotalLoanExpiredAmounts = array();

      $date = $Date[1];

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
      }

      // // dd($FOs);

      if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // code.....
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('totalRepayAmount');
            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }

          // dd($TotalLoanExpiredAmounts);
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanFullyAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanFullyAmount);
          }
        }
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code... here......
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('totalRepayAmount');
                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('amount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('paidLoanAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // // dd($TotalLoanAmounts);

          // foreach ($LoanAmounts as $key => $LoanAmount) {
          //   $TotalLoanAmounts[$key] = array_sum($LoanAmount);
          // }
        }
        else {
          // code...
          $Product = DB::table('mfn_loans_product')->select('id')->where('productCategoryId', $RequestedProductCategoryID)->get();
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              foreach ($Product as $key => $Products) {
                // $LoanExpiredAmounts[$key1][$Samity2->id][$Products->id] = DB::table('mfn_loan')
                //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $Products->id]])
                //           ->sum('loanAmount');

                $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
                $samityID = $Samity2->id;
                $RequestedProductID1 = $Products->id;
                $loanExpiredInfos = DB::table('mfn_loan')
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID1) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID1]]);
                    })
                    ->select('id', 'lastInstallmentDate', 'totalRepayAmount')
                    ->get();

                foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                  $loanCollectedAmount = DB::table('mfn_loan_collection')
                      ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                      ->sum('principalAmount');

                  $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                      ->where('loanIdFk', $loanExpiredInfo->id)
                      ->sum('principalAmountOB');

                  if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                    $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                  }
                  
                }

              }
            }
          }

          // dd($LoanExpiredAmounts);

          foreach ($LoanExpiredAmounts as $key1 => $Member1) {
            foreach ($Member1 as $key2 => $Member2) {
              foreach ($Member2 as $key => $Member3) {
                $MemberCount = $MemberCount + $Member3;
              }

            }
            $TotalLoanExpiredAmounts[$key1] = $MemberCount;
            $MemberCount = 0;
          }
          // dd($TotalLoanExpiredAmounts);
        }
      }
      else {
        if ($RequestedServiceCharge == 'WithServiceCharge') {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('totalRepayAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('amount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('paidLoanAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->loanAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->loanAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
        else {
          // code...
          foreach ($Samity as $key1 => $Samity1) {
            foreach ($Samity1 as $key2 => $Samity2) {
              // $LoanExpiredAmounts[$key1][$Samity2->id] = DB::table('mfn_loan')
              //           ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['lastInstallmentDate', '<=', $Date[0]], ['isLoanCompleted', '=', 0], ['productIdFk', $RequestedProductID]])
              //           ->sum('loanAmount');

              $LoanExpiredAmounts[$key1][$Samity2->id] = 0;
              $samityID = $Samity2->id;
              $loanExpiredInfos = DB::table('mfn_loan')
                  ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                      $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                      ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['lastInstallmentDate', '<', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                  })
                  ->select('id', 'lastInstallmentDate', 'loanAmount')
                  ->get();

              foreach ($loanExpiredInfos as $key => $loanExpiredInfo) {
                $loanCollectedAmount = DB::table('mfn_loan_collection')
                    ->where([['loanIdFk', $loanExpiredInfo->id], ['softDel', '=', 0]])
                    ->sum('principalAmount');

                $loanOpeningBalance = DB::table('mfn_opening_balance_loan')
                    ->where('loanIdFk', $loanExpiredInfo->id)
                    ->sum('principalAmountOB');

                if (($loanCollectedAmount + $loanOpeningBalance) < $loanExpiredInfo->totalRepayAmount) {
                  $LoanExpiredAmounts[$key1][$Samity2->id] = $LoanExpiredAmounts[$key1][$Samity2->id] + ($loanExpiredInfo->totalRepayAmount - ($loanCollectedAmount + $loanOpeningBalance));
                }
                
              }

            }
          }

          foreach ($LoanExpiredAmounts as $key => $LoanExpiredAmount) {
            $TotalLoanExpiredAmounts[$key] = array_sum($LoanExpiredAmount);
          }
        }
      }
    }


      // // dd($TotalLoanCurrentDueNo);

      return $TotalLoanExpiredAmounts;
    }

    public function TotalOutstandingQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge, $RequestedBranchID){
      $LoaneeAmountCount = 0;

      $Samity = array();
      $LoanIds = array();
      $LoanCurrentDueNoCollection = array();
      $LoanCurrentDueNoSchedule = array();
      $TotalLoanCurrentDueNo = array();

    $date = $Date[1];

    foreach ($FOs as $key => $FO) {
      $fId = $FO->id;
      $sId = $FO->Samity_id;
      $bId = $FO->branchId;

      $Samity[$fId] = DB::table('mfn_samity')->where([['fieldOfficerId', $fId], ['branchId', $RequestedBranchID]])->get();
    }

    if ($RequestedProductID == 'All' and $RequestedProductCategoryID == 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0]]);
                    })
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['disbursementDate', '<=', $Date[1]], ['id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['disbursementDate', '<=', $Date[1]], ['id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id], ['mfn_loan_collection.softDel', '=', 0]])
                        ->sum('mfn_loan_collection.principalAmount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('principalAmountOB');

              
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...
    }
    elseif ($RequestedProductID == 'All' and $RequestedProductCategoryID != 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                    })
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loans_product', 'mfn_loan.productIdFk', 'mfn_loans_product.id')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.principalAmount');


              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...
    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID == 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                    })
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $Date[1]], ['id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $Date[1]], ['id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.principalAmount');

              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] + 
                        DB::table('mfn_opening_balance_loan')
                        ->where('loanIdFk', $LoanIds3->id)
                        ->sum('paidLoanAmountOB');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...

    }
    elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID != 'All') {
      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $samityID = $Samity2->id;
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    // ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<', $Date[0]], ['isLoanCompleted', '=', 0]])
                    ->where(function ($query) use ($date, $samityID, $RequestedProductID) {
                        $query->where([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '>=', $date], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]])
                        ->orWhere([['samityIdFk', $samityID], ['disbursementDate', '<=', $date], ['loanCompletedDate', '=', null], ['softDel', '=', 0], ['productIdFk', $RequestedProductID]]);
                    })
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedServiceCharge == 'WithServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $Date[1]], ['id', $LoanIds3->id]])
                          ->sum('totalRepayAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.amount');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      elseif ($RequestedServiceCharge == 'WithOutServiceCharge') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoSchedule[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['disbursementDate', '<=', $Date[1]], ['id', $LoanIds3->id]])
                          ->sum('loanAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoSchedule);

        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $LoanCurrentDueNoCollection[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                        ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                        ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loan.productIdFk', $RequestedProductID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan_collection.collectiondate', '<=', $Date[1]], ['mfn_loan_collection.loanIdFk', $LoanIds3->id]])
                        ->sum('mfn_loan_collection.principalAmount');
            }
          }
        }

        // // dd($LoanCurrentDueNoCollection);
      }
      // code...
    }

    if (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) > 0) {
      foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
        foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
          foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
            // code...
            foreach ($LoanCurrentDueNoCollection as $key1A => $LoanCurrentDueNoCollection1) {
              if ($key1 == $key1A) {
                foreach ($LoanCurrentDueNoCollection1 as $key2A => $LoanCurrentDueNoCollection2) {
                  if ($key2 == $key2A) {
                    foreach ($LoanCurrentDueNoCollection2 as $key3A => $LoanCurrentDueNoCollection3) {
                      if ($key3 == $key3A) {
                        // code...
                        if ($LoanCurrentDueNoSchedule3 > $LoanCurrentDueNoCollection3) {
                          $LoaneeAmountCount = $LoaneeAmountCount + ($LoanCurrentDueNoSchedule3 - $LoanCurrentDueNoCollection3);
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
        $LoaneeAmountCount = 0;
      }
    }
    elseif (sizeof($LoanCurrentDueNoSchedule) > 0 and sizeof($LoanCurrentDueNoCollection) == 0) {
      foreach ($LoanCurrentDueNoSchedule as $key1 => $LoanCurrentDueNoSchedule1) {
        foreach ($LoanCurrentDueNoSchedule1 as $key2 => $LoanCurrentDueNoSchedule2) {
          foreach ($LoanCurrentDueNoSchedule2 as $key3 => $LoanCurrentDueNoSchedule3) {
            // code...
            $LoaneeAmountCount = $LoaneeAmountCount + $LoanCurrentDueNoSchedule3;
          }
        }
        $TotalLoanCurrentDueNo[$key1] = $LoaneeAmountCount;
        $LoaneeAmountCount = 0;
      }
    }
    else {
      foreach ($LoanIds as $key => $LoanId) {
        $TotalLoanCurrentDueNo[$key] = $LoaneeAmountCount;
      }
    }

      // // dd($TotalLoanCurrentDueNo);

      return $TotalLoanCurrentDueNo;
    }

    public function AdditionalFeeCollectionQuery1($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID, $RequestedServiceCharge){
      $LoaneeAmountCount = 0;

      $Samity = array();
      $LoanIds = array();
      $AdditionalFee = array();
      $TotalAdditionalFeeCollection = array();
      $TotalAdditionalFee = array();



      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where('fieldOfficerId', $fId)->get();
      }

      // // dd($FOs);

      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $LoanIds[$key1][$Samity2->id] = DB::table('mfn_loan')
                    ->select('id')
                    ->where([['samityIdFk', $Samity2->id], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0]])
                    ->groupBy('id')
                    ->get();
        }
      }

      if ($RequestedProductID == 'All' and $RequestedProductCategoryID == 'All') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                          ->sum('additionalFee');
            }
          }
        }

      }
      elseif ($RequestedProductID == 'All' and $RequestedProductCategoryID != 'All') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                          ->where([['mfn_loan.samityIdFk', $key2], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_loan.disbursementDate', '<=', $Date[1]], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan.id', $LoanIds3->id]])
                          ->sum('mfn_loan.additionalFee');
            }
          }
        }

      }
      elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID == 'All') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                          ->sum('additionalFee');
            }
          }
        }

      }
      elseif ($RequestedProductID != 'All' and $RequestedProductCategoryID != 'All') {
        // code...
        foreach ($LoanIds as $key1 => $LoanIds1) {
          foreach ($LoanIds1 as $key2 => $LoanIds2) {
            foreach ($LoanIds2 as $key3 => $LoanIds3) {
              $AdditionalFee[$key1][$key2][$LoanIds3->id] = DB::table('mfn_loan')
                          ->where([['samityIdFk', $key2], ['productIdFk', $RequestedProductID], ['disbursementDate', '<=', $Date[1]], ['isLoanCompleted', '=', 0], ['id', $LoanIds3->id]])
                          ->sum('additionalFee');
            }
          }
        }
      }

      // // dd($AdditionalFee);

      foreach ($AdditionalFee as $key1 => $Additional) {
        // code...
        foreach ($Additional as $key2 => $AdditionalCollection) {
          // code...
          $TotalAdditionalFee[$key1][$key2] = array_sum($AdditionalCollection);
        }
      }

      foreach ($TotalAdditionalFee as $key => $TotalAdditional) {
        // code...
        $TotalAdditionalFeeCollection[$key] = array_sum($TotalAdditional);
      }

      // // dd($TotalAdditionalFeeCollection);

      return $TotalAdditionalFeeCollection;

    }

    public function WithSavingsInterest1Query($Date, $FOs, $RequestedProductCategoryID, $RequestedProductID){
      $Samity = array();
      $SavingsProduct = array();
      $LoanProducts = array();
      $Members = array();
      $Savings = array();
      $Totalsavings = array();
      $SavingsAccId = array();
      $SavingsInterest = array();
      $SavingsInterestSamity = array();
      $SavingsInterestAll = array();

      foreach ($FOs as $key => $FO) {
        $fId = $FO->id;
        $sId = $FO->Samity_id;
        $bId = $FO->branchId;

        $Samity[$fId] = DB::table('mfn_samity')->where('fieldOfficerId', $fId)->get();
      }

      // // dd($FOs);

      foreach ($Samity as $key1 => $Samity1) {
        foreach ($Samity1 as $key2 => $Samity2) {
          $SavingsProduct[$key1][$Samity2->id] = DB::table('mfn_savings_account')
                    ->select('savingsProductIdFk')
                    ->where('samityIdFk', $Samity2->id)
                    ->groupBy('savingsProductIdFk')
                    ->get();
        }
      }
      $LoanProducts = DB::table('mfn_loans_product')->where('productCategoryId', $RequestedProductCategoryID)->get();
      // dd($SavingsAccId);

      if ($RequestedProductCategoryID == 'All' and $RequestedProductID == 'All') {
        // code...
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

              $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                      ->where([['samityIdFk', $key2], ['productIdFk', $SavingsProduct3->savingsProductIdFk], ['date', '<=', $Date[1]]])
                      ->sum('interestAmount');
            }
          }
        }

        // dd($SavingsInterest);
      }
      elseif ($RequestedProductCategoryID != 'All' and $RequestedProductID == 'All') {
        // code...
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {
              $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                      ->join('mfn_loans_product', 'mfn_savings_interest.primaryProductIdFk', '=', 'mfn_loans_product.id')
                      ->where([['mfn_savings_interest.samityIdFk', $key2], ['mfn_savings_interest.productIdFk', $SavingsProduct3->savingsProductIdFk], ['mfn_loans_product.productCategoryId', $RequestedProductCategoryID], ['mfn_savings_interest.date', '<=', $Date[1]]])
                      ->sum('mfn_savings_interest.interestAmount');
            }
          }
        }
      }
      elseif ($RequestedProductCategoryID == 'All' and $RequestedProductID != 'All') {
        // code...
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

              $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                      ->where([['samityIdFk', $key2], ['productIdFk', $SavingsProduct3->savingsProductIdFk], ['primaryProductIdFk', $RequestedProductID], ['date', '<=', $Date[1]]])
                      ->sum('interestAmount');
            }
          }
        }
      }
      elseif ($RequestedProductCategoryID != 'All' and $RequestedProductID != 'All') {
        // code...
        foreach ($SavingsProduct as $key1 => $SavingsProduct1) {
          foreach ($SavingsProduct1 as $key2 => $SavingsProduct2) {
            foreach ($SavingsProduct2 as $key3 => $SavingsProduct3) {

              $SavingsInterest[$key1][$key2][$SavingsProduct3->savingsProductIdFk] = DB::table('mfn_savings_interest')
                      ->where([['samityIdFk', $key2], ['productIdFk', $SavingsProduct3->savingsProductIdFk], ['primaryProductIdFk', $RequestedProductID], ['date', '<=', $Date[1]]])
                      ->sum('interestAmount');
            }
          }
        }
      }

      foreach ($SavingsInterest as $key1 => $SavingsInter) {
        // code...
        foreach ($SavingsInter as $key2 => $Savings) {
          // code...
          $SavingsInterestSamity[$key1][$key2] = array_sum($Savings);
        }
      }

      foreach ($SavingsInterestSamity as $key => $SavingsInterestS) {
        // code...
        $SavingsInterestAll[$key] = array_sum($SavingsInterestS);
      }

      // dd($SavingsInterest);

      return $SavingsInterest;
    }


  /* End of the End of the Week */

  public function getReport(Request $request){

    //dd($request);
    $RequestedBranchID          = $request->searchBranch;
    $RequestedProductCategoryID = $request->ProductCategory;
    $RequestedProductID         = $request->Product;
    $RequestedWeek              = $request->Week;
    $RequestedServiceCharge     = $request->SearchType;
    $RequestedSavingsInterest   = $request->InterestType;

    $BranchInfos        = array();
    $ProductInfos       = array();
    $BranchManagerInfos = array();
    $FieldOfficerInfos  = array();
    $SamityInfos        = array();

    /*Begining of the week variable define*/
    $MemberInfos              = array();
    $DateArray                = array();
    $SavingsProduct           = array();
    $SavingsProductInfos      = array();
    $LoanDisburseNo           = array();
    $LoanDisburseAmounts      = array();
    $LoanfullyPaidNo          = array();
    $LoanFullyPaidAmounts     = array();
    $LoanExpiredNo            = array();
    $LoanExpiredAmounts       = array();
    $LoanCurrentNo            = array();
    $LoanCurrentAmounts       = array();
    $LoanCurrentDueNo         = array();
    $LoanCurrentDueAmounts    = array();
    $LoanExpiredDueNo         = array();
    $LoanExpiredDueAmounts    = array();
    $TotalOutstanding         = array();
    $AdditionalFeeCollection  = array();
    $WithSavingsInterest      = array();

    /*Middle of the week*/
    $MemberInfos2ABC                    = array();
    $MemberInfos2                       = array();
    $MemberColosingInfos2               = array();
    $SavingsProductInfos1A2             = array();
    $SavingsRefund                      = array();
    $LoanDisburseNo2                    = array();
    $LoanDisburseAmounts2               = array();
    $LoanfullyPaidNo2                   = array();
    $LoanFullyPaidAmounts2              = array();
    $LoanExpiredNo2                     = array();
    $LoanExpiredAmounts2                = array();
    $RegularRecoverable                 = array();
    $RegularRecovery                    = array();
    $ExpiredDueRecovery                 = array();
    $AdditionalFeeCollectionMOW  = array();

    /*End of the week*/
    $MemberInfos1                         = array();
    $DateArray1                           = array();
    $SavingsProduct1                      = array();
    $SavingsProductInfos1A1               = array();
    $LoanDisburseNo1                      = array();
    $LoanDisburseAmounts1                 = array();
    $LoanfullyPaidNo1                     = array();
    $LoanFullyPaidAmounts1                = array();
    $LoanExpiredNo1                       = array();
    $LoanExpiredAmounts1                  = array();
    $LoanCurrentNo1                       = array();
    $LoanCurrentAmounts1                  = array();
    $LoanCurrentDueNo1                    = array();
    $LoanCurrentDueAmounts1               = array();
    $LoanExpiredDueNo1                    = array();
    $LoanExpiredDueAmounts1               = array();
    $TotalOutstanding1                    = array();
    $AdditionalFeeCollectionEOW  = array();
    $WithSavingsInterest1                 = array();

    $DateArray      = explode(' to ', $RequestedWeek);
    $SavingsProduct = DB::table('mfn_saving_product')->select('id','name')->get();
    $BranchInfos    = DB::table('gnr_branch')->select('id','name','branchCode','address')->where('id', $RequestedBranchID)->get();
    //dd('sss');
    
    //$AllEmployeeInfos   = $this->getAllEmployee($RequestedBranchID,$DateArray);
    $BranchManagerInfos   = $this->BranchManagerQuery($RequestedBranchID,$DateArray);
    $FieldOfficerInfos    = $this->FieldOfficerQuery($RequestedBranchID,$DateArray);
    $SamityInfos          = $this->SamityQuery($FieldOfficerInfos, $RequestedProductCategoryID, $RequestedProductID, $DateArray);
    //dd('sss');

    // STARTED THE CALCULATION FOR BEGINING OF THE WEEK
    $endDateValue   = $DateArray[0];
    $branchId       = $RequestedBranchID;

    /*$dbMembers1             = DB::table('mfn_member_information')
                              ->where(function ($query) use ($endDateValue, $branchId) {
                                $query->where([['softDel', '=', 0],['admissionDate', '<=', $endDateValue], ['closingDate', '>', $endDateValue], ['branchId', $branchId]])
                                ->orWhere([['softDel', '=', 0],['admissionDate', '<=', $endDateValue], ['closingDate', '=', '0000-00-00'], ['branchId', $branchId]])
                                ->orWhere([['softDel', '=', 0],['admissionDate', '<=', $endDateValue], ['closingDate', '=', null], ['branchId', $branchId]]);
                              })
                              ->select('id')
                              ->get();

    $primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
                              ->where('softDel',0)
                              ->where('branchIdFk',$branchId)
                              ->where('transferDate','>',$endDateValue)
                              ->select('id','memberIdFk','oldPrimaryProductFk')
                              ->get();

    $primaryProductTransfers  = $primaryProductTransfers->sortBy('transferDate')->unique('memberIdFk');

    foreach ($primaryProductTransfers as $key => $primaryProductTransfer) {
        if ($dbMembers1->where('id',$primaryProductTransfer->memberIdFk)->first()!=null) {
          $dbMembers1->where('id',$primaryProductTransfer->memberIdFk)->first()->primaryProductId = $primaryProductTransfer->oldPrimaryProductFk;
        }                
    }

    $samityTransfers          = DB::table('mfn_member_samity_transfer')
                              ->where('softDel',0)
                              ->where('branchIdFk',$branchId)
                              ->where('transferDate','>',$endDateValue)
                              ->select('id')
                              ->get();

    $samityTransfers          = $samityTransfers->sortBy('transferDate')->unique('memberIdFk');

    foreach ($samityTransfers as $key => $samityTransfer) {
        if ($dbMembers1->where('id',$samityTransfer->memberIdFk)->first()!=null) {
            $dbMembers1->where('id',$samityTransfer->memberIdFk)->first()->samityId = $samityTransfer->previousSamityIdFk;
        }                
    }
    // ENDED THE CALCULATION FOR BEGINING OF THE WEEK
    
    // STARTED THE CALCULATION FOR END OF THE WEEK
    $endDateValue = $DateArray[1];
    $branchId = $RequestedBranchID;

    $dbMembers2 = DB::table('mfn_member_information')
      ->where(function ($query) use ($endDateValue, $branchId) {
          $query->where([['softDel', '=', 0],['admissionDate', '<=', $endDateValue], ['closingDate', '>', $endDateValue], ['branchId', $branchId]])
          ->orWhere([['softDel', '=', 0],['admissionDate', '<=', $endDateValue], ['closingDate', '=', '0000-00-00'], ['branchId', $branchId]])
          ->orWhere([['softDel', '=', 0],['admissionDate', '<=', $endDateValue], ['closingDate', '=', null], ['branchId', $branchId]]);
      })
      ->select('id')
      ->get();

    /*$primaryProductTransfers = DB::table('mfn_loan_primary_product_transfer')
                              ->where('softDel',0)
                              ->where('branchIdFk',$branchId)
                              ->where('transferDate','>',$endDateValue)
                              ->get();
    $primaryProductTransfers = $primaryProductTransfers->sortBy('transferDate')->unique('memberIdFk');*/

   /* foreach ($primaryProductTransfers as $key => $primaryProductTransfer) {
        if ($dbMembers2->where('id',$primaryProductTransfer->memberIdFk)->first()!=null) {
            $dbMembers2->where('id',$primaryProductTransfer->memberIdFk)->first()->primaryProductId = $primaryProductTransfer->oldPrimaryProductFk;
        }                
    }

   /* $samityTransfers = DB::table('mfn_member_samity_transfer')
                        ->where('softDel',0)
                        ->where('branchIdFk',$branchId)
                        ->where('transferDate','>',$endDateValue)
                        ->get();

    $samityTransfers = $samityTransfers->sortBy('transferDate')->unique('memberIdFk');*/

   /* foreach ($samityTransfers as $key => $samityTransfer) {
        if ($dbMembers2->where('id',$samityTransfer->memberIdFk)->first()!=null) {
            $dbMembers2->where('id',$samityTransfer->memberIdFk)->first()->samityId = $samityTransfer->previousSamityIdFk;
        }                
    }*/
    // ENDED THE CALCULATION FOR END OF THE WEEK

    //dd($dbMembers1, $dbMembers2);
    $DA   = $DateArray; 
    $FIO  = $FieldOfficerInfos;
    $RPCI = $RequestedProductCategoryID;
    $RPI  = $RequestedProductID;
    $RBI  = $RequestedBranchID;
    $RSC  = $RequestedServiceCharge;


    // Start of the beginning of the week
    $MemberInfos              = $this->MemberQueryBOW($DA, $FIO, $RPCI, $RPI);
    $SavingsProductInfos      = $this->SavingsProductQueryBOW($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanDisburseNo           = $this->LoanDisburseNoQueryBOW($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanDisburseAmounts      = $this->LoanDisburseAmountsQueryBOW($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanfullyPaidNo          = $this->LoanfullyPaidNoQueryBOW($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanFullyPaidAmounts     = $this->LoanFullyPaidAmountsQueryBOW($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanExpiredNo            = $this->LoanExpiredNoQuery($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanExpiredAmounts       = $this->LoanExpiredAmountQuery($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanCurrentNo            = $this->LoanCurrentNoQuery($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanCurrentAmounts       = $this->LoanCurrentAmountsQuery($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanCurrentDueNo         = $this->LoanCurrentDueNoQuery($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanCurrentDueAmounts    = $this->LoanCurrentDueAmountsQuery($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanExpiredDueNo         = $this->LoanExpiredDueNoQuery($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanExpiredDueAmounts    = $this->LoanExpiredDueAmountsQuery($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $TotalOutstanding         = $this->TotalOutstandingQuery($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $AdditionalFeeCollection  = $this->AdditionalFeeCollectionQuery($DA, $FIO, $RPCI, $RPI, $RSC);
    $WithSavingsInterest      = $this->WithSavingsInterestQuery($DA, $FIO, $RPCI, $RPI);
    // End of the beginning of the week

    // Start of the for the week
    $MemberInfos2ABC            = $this->MemberQuery2A($DA, $FIO, $RPCI, $RPI);
    $MemberInfos2               = $this->MemberQueryMOW($DA, $FIO, $RPCI, $RPI);
    $MemberColosingInfos2       = $this->MemberClosingQueryMOW($DA, $FIO, $RPCI, $RPI);
    $SavingsProductInfos1A2     = $this->SavingsProductQuery2($DA, $FIO, $RPCI, $RPI, $RBI);
    $SavingsRefund              = $this->SavingsRefundQuery($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanDisburseNo2            = $this->LoanDisburseNoQueryMOW($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanDisburseAmounts2       = $this->LoanDisburseAmountsQueryMOW($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanfullyPaidNo2           = $this->LoanfullyPaidNoQueryMOW($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanFullyPaidAmounts2      = $this->LoanFullyPaidAmountsQuery2($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanExpiredNo2             = $this->LoanExpiredNoQuery2($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanExpiredAmounts2        = $this->LoanExpiredAmountQuery2($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $RegularRecoverable         = $this->RegularRecoverableQuery($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $RegularRecovery            = $this->RegularRecoveryQuery($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $ExpiredDueRecovery         = $this->ExpiredDueRecoveryQuery($DA, $FIO, $RPCI, $RPI, $RSC);
    $AdditionalFeeCollectionMOW = $this->AdditionalFeeCollectionQuery2($DA, $FIO, $RPCI, $RPI, $RSC);
    // End of the for the week

    // Start of the end of the week
    $MemberInfos1               = $this->MemberQueryEOW($DA, $FIO, $RPCI, $RPI);
    $SavingsProductInfos1A1     = $this->SavingsProductQuery1($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanDisburseNo1            = $this->LoanDisburseNoQueryEOW($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanDisburseAmounts1       = $this->LoanDisburseAmountsQueryEOW($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanfullyPaidNo1           = $this->LoanfullyPaidNoQueryEOW($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanFullyPaidAmounts1      = $this->LoanFullyPaidAmountsQuery1($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanExpiredNo1             = $this->LoanExpiredNoQuery1($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanExpiredAmounts1        = $this->LoanExpiredAmountQuery1($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanCurrentNo1             = $this->LoanCurrentNoQuery1($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanCurrentAmounts1        = $this->LoanCurrentAmountsQuery1($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $LoanCurrentDueNo1          = $this->LoanCurrentDueNoQuery1($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanCurrentDueAmounts1     = $this->LoanCurrentDueAmountsQuery1($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanExpiredDueNo1          = $this->LoanExpiredDueNoQuery1($DA, $FIO, $RPCI, $RPI, $RBI);
    $LoanExpiredDueAmounts1     = $this->LoanExpiredDueAmountsQuery1($DA, $FIO, $RPCI, $RSC, $RPI, $RBI);
    $TotalOutstanding1          = $this->TotalOutstandingQuery1($DA, $FIO, $RPCI, $RPI, $RSC, $RBI);
    $AdditionalFeeCollectionEOW = $this->AdditionalFeeCollectionQuery1($DA, $FIO, $RPCI, $RPI, $RSC);
    $WithSavingsInterest1       = $this->WithSavingsInterest1Query($DA, $FIO, $RPCI, $RPI);
    // End of the end of the week

    //dd($DateArray, $FIO, $RequestedProductCategoryID, $RPI);
    //dd($LoanDisburseNo,$LoanDisburseNo1,$LoanDisburseNo2);

    if ($RequestedProductCategoryID == 'All') {
      if ($RequestedProductID == 'All') {
        // when ProductCategory and Product both are selected as ALL
        $ProductInfos = DB::table('mfn_loans_product')
                      ->select('mfn_loans_product.id as ProductId', 'mfn_loans_product.shortName as ProductName', 'mfn_loans_product.code as ProductCode', 'mfn_loans_product_category.id as ProductCategoryId', 'mfn_loans_product_category.shortName as ProductCategoryName')
                      ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                      ->get();

        // // dd($MemberInfos);
      }
      else {
        // when ProductCategory selected ALL but Product selected as single
        $ProductInfos = DB::table('mfn_loans_product')
                      ->select('mfn_loans_product.id as ProductId', 'mfn_loans_product.shortName as ProductName', 'mfn_loans_product.code as ProductCode', 'mfn_loans_product_category.id as ProductCategoryId', 'mfn_loans_product_category.shortName as ProductCategoryName')
                      ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                      ->where('mfn_loans_product.id', $RequestedProductID)
                      ->get();
      }
    }
    else {
      if ($RequestedProductID == 'All') {
        // when ProductCategory selected as single but Product selected as all
        $ProductInfos = DB::table('mfn_loans_product')
                      ->select('mfn_loans_product.id as ProductId', 'mfn_loans_product.shortName as ProductName', 'mfn_loans_product.code as ProductCode', 'mfn_loans_product_category.id as ProductCategoryId', 'mfn_loans_product_category.shortName as ProductCategoryName')
                      ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                      ->where('mfn_loans_product_category.id', $RequestedProductCategoryID)
                      ->get();
      }
      else {
        // when ProductCategory and Product both are selected as single
        $ProductInfos = DB::table('mfn_loans_product')
                      ->select('mfn_loans_product.id as ProductId', 'mfn_loans_product.shortName as ProductName', 'mfn_loans_product.code as ProductCode', 'mfn_loans_product_category.id as ProductCategoryId', 'mfn_loans_product_category.shortName as ProductCategoryName')
                      ->join('mfn_loans_product_category', 'mfn_loans_product.productCategoryId', 'mfn_loans_product_category.id')
                      ->where('mfn_loans_product.id', $RequestedProductID)
                      ->where('mfn_loans_product_category.id', $RequestedProductCategoryID)
                      ->get();
      }
    }

    return view('microfin.reports.branchManagerReportViews.BranchManagerReportTable', compact('BranchInfos', 'ProductInfos', 'RequestedProductID', 'RequestedProductCategoryID', 'RequestedWeek', 'RequestedBranchID', 'BranchManagerInfos',
    'FieldOfficerInfos', 'SamityInfos', 'MemberInfos', 'SavingsProduct', 'SavingsProductInfos', 'LoanDisburseNo', 'LoanDisburseAmounts', 'LoanfullyPaidNo', 'LoanFullyPaidAmounts', 'LoanExpiredNo', 'LoanExpiredAmounts', 'LoanCurrentNo',
    'LoanCurrentAmounts', 'LoanCurrentDueNo', 'LoanCurrentDueAmounts', 'LoanExpiredDueNo', 'LoanExpiredDueAmounts', 'TotalOutstanding', 'MemberInfos1', 'SavingsProduct1', 'SavingsProductInfos1A1', 'LoanDisburseNo1', 'LoanDisburseAmounts1',
    'LoanfullyPaidNo1', 'LoanFullyPaidAmounts1', 'LoanExpiredNo1', 'LoanExpiredAmounts1', 'LoanCurrentNo1', 'LoanCurrentAmounts1', 'LoanCurrentDueNo1', 'LoanCurrentDueAmounts1', 'LoanExpiredDueNo1', 'LoanExpiredDueAmounts1', 'TotalOutstanding1',
    'MemberInfos2', 'MemberColosingInfos2', 'SavingsProductInfos1A2', 'RequestedProductCategoryID', 'RequestedProductID', 'SavingsRefund', 'LoanDisburseNo2', 'LoanDisburseAmounts2', 'LoanfullyPaidNo2', 'LoanFullyPaidAmounts2', 'LoanExpiredNo2',
    'LoanExpiredAmounts2', 'RegularRecoverable', 'RegularRecovery', 'ExpiredDueRecovery', 'AdditionalFeeCollection', 'AdditionalFeeCollectionMOW', 'AdditionalFeeCollectionEOW', 'MemberInfos2ABC', 'WithSavingsInterest', 'WithSavingsInterest1',
    'RequestedSavingsInterest'));
  }

}
