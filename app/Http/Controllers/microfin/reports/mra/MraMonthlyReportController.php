<?php

namespace App\Http\Controllers\microfin\reports\mra;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use Response;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\microfin\MicroFin;

class MraMonthlyReportController extends Controller {

    public function index() {

        $userBranchId=Auth::user()->branchId;

        /// Branch
        $branchList = DB::table('gnr_branch');

        if ($userBranchId!=1) {
            $branchList = $branchList->where('id', $userBranchId);
        }

        $branchList = $branchList
                       ->orderBy('branchCode')
                       ->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
                       ->pluck('nameWithCode', 'id')
                       ->all();

        // Year
        $yearsOption = MicroFin::getYearsOption();

        // Month
        $monthsOption = MicroFin::getMonthsOption();

        // LLp period
        $llpOptions = array(
            '1-6' => 'Jan - Jun',
            '7-12' => 'Jul - Dec'
        );

        // Report type
        $reportType = array(
            '1' => 'Half Yearly Report',
            '2' => 'Monthly Report',
        );

        // Funding Organization List
        $fundingOrgList = DB::table('mfn_funding_organization')
                                ->pluck('name','id')
                                ->toArray();

        $filteringArray = array(
            'branchList'          => $branchList,
            'yearsOption'         => $yearsOption,
            'monthsOption'        => $monthsOption,
            'fundingOrgList'      => $fundingOrgList,
            'userBranchId'        => $userBranchId,
            'llpOptions'          => $llpOptions,
            'reportType'          => $reportType
        );
        return view('microfin.reports.mra.mraMonthlyReport.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){
        $maleMembersTotal = array();
        $femaleMembersTotal = array();
        $maleBorrowersTotal = array();
        $femaleBorrowersTotal = array();
        $savingsProduct = array();
        $savingsTotal = array();
        $loanProduct = array();
        $loanProductCategory = array();
        $outstandingTotal = array();
        $disbursmentTotal = array();

        $generalOutstandingTotal = 0;
        $microOutstandingTotal = 0;
        $agricultureOutstandingTotal = 0;
        $othersOutstandingTotal = 0;
        $generaldisbursmentTotal = 0;
        $microdisbursmentTotal = 0;
        $agriculturedisbursmentTotal = 0;
        $othersdisbursmentTotal = 0;
        $recoveryTotal = 0;

        $reportTypes = $req->filReportType;

        $savingsProduct = DB::table('mfn_saving_product')->get();

        $loanProduct = DB::table('mfn_loans_product')->get();

        $loanProductCategory = DB::table('mfn_loans_product_category')->get();

        // dd($req->filReportType );

        if ($req->filReportType == 1) {
          // code...
          list($firstMonth, $lastMonth) = explode('-', $req->filLlpPeriod);
          // dd($lastMonth);

          $filDate = Carbon::parse('01-'.$lastMonth.'-'.$req->filYear)->endOfMonth()->format('Y-m-d');

          $crateDate = date_create("01-$firstMonth-$req->filYear");
          $startDate = date_format($crateDate,"Y-m-d");

            switch ($lastMonth) {
                case 6:
                    $reportingMonth = 'January - June, '.$req->filYear;
                    break;
                case 12:
                    $reportingMonth = 'July - December, '.$req->filYear;
                    break;
            }

            // dd($reportingMonth);
        }
        else {
          // code...
          $filDate = Carbon::parse('01-'.$req->filMonth.'-'.$req->filYear)->endOfMonth()->format('Y-m-d');

          $reportingMonth = Carbon::parse('01-'.$req->filMonth.'-'.$req->filYear)->format('F, Y');

          $crateDate = date_create("01-$req->filMonth-$req->filYear");
          $startDate = date_format($crateDate,"Y-m-d");
          // dd($req->filMonth);
        }
        $MON = $req->filMonth;
        $YEA = $req->filYear;
        // $filDate = Carbon::parse('01-'.$req->filMonth.'-'.$req->filYear)->endOfMonth()->format('Y-m-d');

        // $crateDate = date_create("01-$req->filMonth-$req->filYear");
        // $startDate = date_format($crateDate,"Y-m-d");

        // dd($filDate);

        // Month End Loan Info
        $loanInfo = DB::table('mfn_month_end_process_loans')
                            ->where('date',$filDate);

        // dd($loanInfo);

        $memberInfo = DB::table('mfn_month_end_process_members')
                            ->where('date',$filDate);

        if ($req->filBranch!='') {
            $loanInfo = $loanInfo->where('branchIdFk',$req->filBranch);
            $memberInfo = $memberInfo->where('branchIdFk',$req->filBranch);
        }

        // dd($loanInfo);

        if ($req->filFundingOrg!='') {
            if ($req->filFundingOrg=='-1') {
                $memberInfo = $memberInfo->where('fundingOrgIdFk','!=',3);
            }
            else{
                $memberInfo = $memberInfo->where('fundingOrgIdFk',$req->filFundingOrg);
            }
            $loanInfo = $loanInfo->where('branchIdFk',$req->filBranch);
        }

        $loanInfo = $loanInfo->get();
        $memberInfo = $memberInfo->get();

        $data = array(
            'filBranch'         => $req->filBranch,
            'filYear'           => $req->filYear,
            'filMonth'          => $req->filMonth,
            'loanInfo'          => $loanInfo
        );

        // dd($req->sType);

        if ($req->filBranch == '') {
            // dd($filDate);
            if ($req->filFundingOrg == '') {
                // dd($filDate);
                $maleMembersTotal = DB::table('mfn_month_end_process_total_members')
                    ->where([['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('mclosingMember');
                    // ->get();

                $femaleMembersTotal = DB::table('mfn_month_end_process_total_members')
                    ->where([['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('fclosingMember');

                    // dd($femaleMembersTotal);

                $maleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                    ->where([['genderTypeId', '=', 1],['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('closingBorrowerNo');

                $femaleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                    ->where([['genderTypeId', '=', 2], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('closingBorrowerNo');

                foreach ($savingsProduct as $key => $savingsProduct1) {
                    $savingsTotal[$savingsProduct1->id] = DB::table('mfn_month_end_process_savings')
                        ->where([['date', '>=', $startDate], ['date', '<=', $filDate], ['savingProductIdFk', $savingsProduct1->id]])
                        ->sum('closingBalance');
                }

                if ($req->sType == 1) {
                  foreach ($loanProductCategory as $key => $loanProductCategory1) {
                      $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                          ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                          ->where([['mfn_month_end_process_loans.date', '>=', $startDate],
                              ['mfn_month_end_process_loans.date', '<=', $filDate],
                              ['mfn_loans_product.productCategoryId', $loanProductCategory1->id]])
                          ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
                  }
                }
                elseif ($req->sType == 2) {
                  foreach ($loanProductCategory as $key => $loanProductCategory1) {
                      $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                          ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                          ->where([['mfn_month_end_process_loans.date', '>=', $startDate],
                              ['mfn_month_end_process_loans.date', '<=', $filDate],
                              ['mfn_loans_product.productCategoryId', $loanProductCategory1->id]])
                          ->sum('mfn_month_end_process_loans.closingOutstandingAmount');
                  }
                }

                foreach ($outstandingTotal as $key => $outstandingTotal1) {
                  if ($key == 1 || $key == 3) {
                    $generalOutstandingTotal = $generalOutstandingTotal + $outstandingTotal1;
                  }
                  elseif ($key == 2) {
                    $microOutstandingTotal = $outstandingTotal1;
                  }
                  elseif ($key == 4) {
                    $agricultureOutstandingTotal = $outstandingTotal1;
                  }
                  elseif ($key == 5) {
                    $othersOutstandingTotal = $outstandingTotal1;
                  }
                }

                // dd($femaleMembersTotal);

                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                  // $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                  //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  //     ->where([
                  //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                  //         ['mfn_loans_product.productCategoryId', $loanProductCategory1->id]])
                  //     ->sum('mfn_month_end_process_loans.disbursedAmount');

                  $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_loan')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->where([['mfn_loan.disbursementDate', '<=', $filDate],
                      ['mfn_loan.softDel',0],
                      ['mfn_loans_product.productCategoryId', $loanProductCategory1->id]])
                    ->sum('mfn_loan.loanAmount');
                }

                foreach ($disbursmentTotal as $key => $disbursmentTotal1) {
                  if ($key == 1 || $key == 3) {
                    $generaldisbursmentTotal = $generaldisbursmentTotal + $disbursmentTotal1;
                  }
                  elseif ($key == 2) {
                    $microdisbursmentTotal = $disbursmentTotal1;
                  }
                  elseif ($key == 4) {
                    $agriculturedisbursmentTotal = $disbursmentTotal1;
                  }
                  elseif ($key == 5) {
                    $othersdisbursmentTotal = $disbursmentTotal1;
                  }
                }

                if ($req->sType == 1) {
                  // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                  //     ->where([['date', '<=', $filDate]])
                  //     ->sum('recoveryAmount');

                  $recoveryTotal = DB::table('mfn_loan_collection')
                    ->where([['collectionDate', '<=', $filDate], ['softDel',0]])
                    ->sum('amount');
                }
                elseif ($req->sType == 2) {
                  // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                  //     ->where([['date', '<=', $filDate]])
                  //     ->sum('principalRecoveryAmount');

                  $recoveryTotal = DB::table('mfn_loan_collection')
                    ->where([['collectionDate', '<=', $filDate], ['softDel',0]])
                    ->sum('principalAmount');
                }

            }
            elseif ($req->filFundingOrg != '' and $req->filFundingOrg != '-1') {
              // start of the selected funding organization but with grihayan..................................................................

              $maleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['fundingOrgIdFk', $req->filFundingOrg], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('mclosingMember');
                  // ->get();

              $femaleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['fundingOrgIdFk', $req->filFundingOrg], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('fclosingMember');

              $maleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 1],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              $femaleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 2],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              foreach ($savingsProduct as $key => $savingsProduct1) {
                  $savingsTotal[$savingsProduct1->id] = DB::table('mfn_month_end_process_savings')
                      ->join('mfn_loans_product', 'mfn_month_end_process_savings.productIdFk', '=', 'mfn_loans_product.id')
                      ->where([['mfn_month_end_process_savings.date', '>=', $startDate],
                        ['mfn_month_end_process_savings.date', '<=', $filDate],
                        ['mfn_month_end_process_savings.savingProductIdFk', $savingsProduct1->id],
                        ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                      ->sum('mfn_month_end_process_savings.closingBalance');
              }

              if ($req->sType == 1) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
                }
              }
              elseif ($req->sType == 2) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmount');
                }
              }

              foreach ($outstandingTotal as $key => $outstandingTotal1) {
                if ($key == 1 || $key == 3) {
                  $generalOutstandingTotal = $generalOutstandingTotal + $outstandingTotal1;
                }
                elseif ($key == 2) {
                  $microOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 4) {
                  $agricultureOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 5) {
                  $othersOutstandingTotal = $outstandingTotal1;
                }
              }

              foreach ($loanProductCategory as $key => $loanProductCategory1) {
                // $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                //         ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                //     ->sum('mfn_month_end_process_loans.disbursedAmount');

                $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_loan')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.disbursementDate', '<=', $filDate],
                    ['mfn_loan.softDel',0],
                    ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                  ->sum('mfn_loan.loanAmount');
              }

              foreach ($disbursmentTotal as $key => $disbursmentTotal1) {
                if ($key == 1 || $key == 3) {
                  $generaldisbursmentTotal = $generaldisbursmentTotal + $disbursmentTotal1;
                }
                elseif ($key == 2) {
                  $microdisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 4) {
                  $agriculturedisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 5) {
                  $othersdisbursmentTotal = $disbursmentTotal1;
                }
              }

              if ($req->sType == 1) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                //     ->sum('mfn_month_end_process_loans.recoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                  ->sum('mfn_loan_collection.amount');
              }
              elseif ($req->sType == 2) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                //     ->sum('mfn_month_end_process_loans.principalRecoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                  ->sum('mfn_loan_collection.principalAmount');
              }

              // end of the selected funding organization but with grihayan.......................................................................
            }
            elseif ($req->filFundingOrg != '' and $req->filFundingOrg == '-1') {
              // start of the selected funding organization but without grihayan..................................................................

              $maleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['fundingOrgIdFk', '!=', 3], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('mclosingMember');
                  // ->get();

              $femaleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['fundingOrgIdFk', '!=', 3], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('fclosingMember');

              $maleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 1],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              $femaleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 2],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              foreach ($savingsProduct as $key => $savingsProduct1) {
                  $savingsTotal[$savingsProduct1->id] = DB::table('mfn_month_end_process_savings')
                      ->join('mfn_loans_product', 'mfn_month_end_process_savings.productIdFk', '=', 'mfn_loans_product.id')
                      ->where([['mfn_month_end_process_savings.date', '>=', $startDate],
                        ['mfn_month_end_process_savings.date', '<=', $filDate],
                        ['mfn_month_end_process_savings.savingProductIdFk', $savingsProduct1->id],
                        ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                      ->sum('mfn_month_end_process_savings.closingBalance');
              }

              if ($req->sType == 1) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
                }
              }
              elseif ($req->sType == 2) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmount');
                }
              }

              foreach ($outstandingTotal as $key => $outstandingTotal1) {
                if ($key == 1 || $key == 3) {
                  $generalOutstandingTotal = $generalOutstandingTotal + $outstandingTotal1;
                }
                elseif ($key == 2) {
                  $microOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 4) {
                  $agricultureOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 5) {
                  $othersOutstandingTotal = $outstandingTotal1;
                }
              }

              foreach ($loanProductCategory as $key => $loanProductCategory1) {
                // $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                //         ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                //     ->sum('mfn_month_end_process_loans.disbursedAmount');

                $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_loan')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.disbursementDate', '<=', $filDate],
                    ['mfn_loan.softDel',0],
                    ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                  ->sum('mfn_loan.loanAmount');
              }

              foreach ($disbursmentTotal as $key => $disbursmentTotal1) {
                if ($key == 1 || $key == 3) {
                  $generaldisbursmentTotal = $generaldisbursmentTotal + $disbursmentTotal1;
                }
                elseif ($key == 2) {
                  $microdisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 4) {
                  $agriculturedisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 5) {
                  $othersdisbursmentTotal = $disbursmentTotal1;
                }
              }

              if ($req->sType == 1) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                //     ->sum('mfn_month_end_process_loans.recoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                  ->sum('mfn_loan_collection.amount');
              }
              elseif ($req->sType == 2) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                //     ->sum('mfn_month_end_process_loans.principalRecoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                  ->sum('mfn_loan_collection.principalAmount');
              }

              // end of the selected funding organization but without grihayan....................................................................
            }
        }
        else{
            if ($req->filFundingOrg == '') {
                // dd($filDate);
                $maleMembersTotal = DB::table('mfn_month_end_process_total_members')
                    ->where([['branchIdFk', $req->filBranch], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('mclosingMember');
                    // ->get();

                $femaleMembersTotal = DB::table('mfn_month_end_process_total_members')
                    ->where([['branchIdFk', $req->filBranch], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('fclosingMember');

                $maleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                    ->where([['branchIdFk', $req->filBranch], ['genderTypeId', '=', 1], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('closingBorrowerNo');

                $femaleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                    ->where([['branchIdFk', $req->filBranch], ['genderTypeId', '=', 2], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                    ->sum('closingBorrowerNo');

                foreach ($savingsProduct as $key => $savingsProduct1) {
                    $savingsTotal[$savingsProduct1->id] = DB::table('mfn_month_end_process_savings')
                        ->where([['branchIdFk', $req->filBranch], ['date', '>=', $startDate], ['date', '<=', $filDate], ['savingProductIdFk', $savingsProduct1->id]])
                        ->sum('closingBalance');
                }

                if ($req->sType == 1) {
                  foreach ($loanProductCategory as $key => $loanProductCategory1) {
                      $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                          ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                          ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                              ['mfn_month_end_process_loans.date', '>=', $startDate],
                              ['mfn_month_end_process_loans.date', '<=', $filDate],
                              ['mfn_loans_product.productCategoryId', $loanProductCategory1->id]])
                          ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
                  }
                }
                elseif ($req->sType == 2) {
                  foreach ($loanProductCategory as $key => $loanProductCategory1) {
                      $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                          ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                          ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                              ['mfn_month_end_process_loans.date', '>=', $startDate],
                              ['mfn_month_end_process_loans.date', '<=', $filDate],
                              ['mfn_loans_product.productCategoryId', $loanProductCategory1->id]])
                          ->sum('mfn_month_end_process_loans.closingOutstandingAmount');
                  }
                }

                foreach ($outstandingTotal as $key => $outstandingTotal1) {
                  if ($key == 1 || $key == 3) {
                    $generalOutstandingTotal = $generalOutstandingTotal + $outstandingTotal1;
                  }
                  elseif ($key == 2) {
                    $microOutstandingTotal = $outstandingTotal1;
                  }
                  elseif ($key == 4) {
                    $agricultureOutstandingTotal = $outstandingTotal1;
                  }
                  elseif ($key == 5) {
                    $othersOutstandingTotal = $outstandingTotal1;
                  }
                }

                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                  // $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                  //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  //     ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                  //
                  //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                  //         ['mfn_loans_product.productCategoryId', $loanProductCategory1->id]])
                  //     ->sum('mfn_month_end_process_loans.disbursedAmount');

                  $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_loan')
                    ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                    ->where([['mfn_loan.disbursementDate', '<=', $filDate],
                      ['mfn_loan.softDel',0],
                      ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                      ['mfn_loan.branchIdFk', $req->filBranch]])
                    ->sum('mfn_loan.loanAmount');
                }

                foreach ($disbursmentTotal as $key => $disbursmentTotal1) {
                  if ($key == 1 || $key == 3) {
                    $generaldisbursmentTotal = $generaldisbursmentTotal + $disbursmentTotal1;
                  }
                  elseif ($key == 2) {
                    $microdisbursmentTotal = $disbursmentTotal1;
                  }
                  elseif ($key == 4) {
                    $agriculturedisbursmentTotal = $disbursmentTotal1;
                  }
                  elseif ($key == 5) {
                    $othersdisbursmentTotal = $disbursmentTotal1;
                  }
                }

                if ($req->sType == 1) {
                  // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                  //     ->where([['branchIdFk', $req->filBranch], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  //     ->sum('recoveryAmount');

                  $recoveryTotal = DB::table('mfn_loan_collection')
                    ->where([['branchIdFk', $req->filBranch],
                      ['softDel',0],
                      ['collectionDate', '<=', $filDate]])
                    ->sum('amount');
                }
                elseif ($req->sType == 2) {
                  // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                  //     ->where([['branchIdFk', $req->filBranch], ['date', '<=', $filDate]])
                  //     ->sum('principalRecoveryAmount');

                  $recoveryTotal = DB::table('mfn_loan_collection')
                    ->where([['branchIdFk', $req->filBranch],
                      ['softDel',0],
                      ['collectionDate', '<=', $filDate]])
                    ->sum('principalAmount');
                }

            }
            elseif ($req->filFundingOrg != '' and $req->filFundingOrg != '-1') {
              // start of the selected funding organization but with grihayan..................................................................

              $maleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['branchIdFk', $req->filBranch], ['fundingOrgIdFk', $req->filFundingOrg], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('mclosingMember');
                  // ->get();

              $femaleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['branchIdFk', $req->filBranch], ['fundingOrgIdFk', $req->filFundingOrg], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('fclosingMember');

              $maleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 1],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              $femaleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 2],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              foreach ($savingsProduct as $key => $savingsProduct1) {
                  $savingsTotal[$savingsProduct1->id] = DB::table('mfn_month_end_process_savings')
                      ->join('mfn_loans_product', 'mfn_month_end_process_savings.productIdFk', '=', 'mfn_loans_product.id')
                      ->where([['mfn_month_end_process_savings.branchIdFk', $req->filBranch],
                        ['mfn_month_end_process_savings.date', '>=', $startDate],
                        ['mfn_month_end_process_savings.date', '<=', $filDate],
                        ['mfn_month_end_process_savings.savingProductIdFk', $savingsProduct1->id],
                        ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                      ->sum('mfn_month_end_process_savings.closingBalance');
              }

              if ($req->sType == 1) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
                }
              }
              elseif ($req->sType == 2) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmount');
                }
              }

              foreach ($outstandingTotal as $key => $outstandingTotal1) {
                if ($key == 1 || $key == 3) {
                  $generalOutstandingTotal = $generalOutstandingTotal + $outstandingTotal1;
                }
                elseif ($key == 2) {
                  $microOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 4) {
                  $agricultureOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 5) {
                  $othersOutstandingTotal = $outstandingTotal1;
                }
              }

              foreach ($loanProductCategory as $key => $loanProductCategory1) {
                // $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                //         ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                //     ->sum('mfn_month_end_process_loans.disbursedAmount');

                $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_loan')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.disbursementDate', '<=', $filDate],
                    ['mfn_loan.softDel',0],
                    ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                    ['mfn_loan.branchIdFk', $req->filBranch],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                  ->sum('mfn_loan.loanAmount');
              }

              foreach ($disbursmentTotal as $key => $disbursmentTotal1) {
                if ($key == 1 || $key == 3) {
                  $generaldisbursmentTotal = $generaldisbursmentTotal + $disbursmentTotal1;
                }
                elseif ($key == 2) {
                  $microdisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 4) {
                  $agriculturedisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 5) {
                  $othersdisbursmentTotal = $disbursmentTotal1;
                }
              }

              if ($req->sType == 1) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                //     ->sum('mfn_month_end_process_loans.recoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.branchIdFk', $req->filBranch],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                  ->sum('mfn_loan_collection.amount');
              }
              elseif ($req->sType == 2) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                //     ->sum('mfn_month_end_process_loans.principalRecoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.branchIdFk', $req->filBranch],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loans_product.fundingOrganizationId', $req->filFundingOrg]])
                  ->sum('mfn_loan_collection.principalAmount');
              }

              // end of the selected funding organization but with grihayan.......................................................................
            }
            elseif ($req->filFundingOrg != '' and $req->filFundingOrg == '-1') {
              // start of the selected funding organization but without grihayan..................................................................

              $maleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['branchIdFk', $req->filBranch], ['fundingOrgIdFk', '!=', 3], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('mclosingMember');
                  // ->get();

              $femaleMembersTotal = DB::table('mfn_month_end_process_total_members')
                  ->where([['branchIdFk', $req->filBranch], ['fundingOrgIdFk', '!=', 3], ['date', '>=', $startDate], ['date', '<=', $filDate]])
                  ->sum('fclosingMember');

              $maleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 1],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              $femaleBorrowersTotal = DB::table('mfn_month_end_process_loans')
                  ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3],
                    ['mfn_month_end_process_loans.genderTypeId', '=', 2],
                    ['mfn_month_end_process_loans.date', '>=', $startDate],
                    ['mfn_month_end_process_loans.date', '<=', $filDate]])
                  ->sum('mfn_month_end_process_loans.closingBorrowerNo');

              foreach ($savingsProduct as $key => $savingsProduct1) {
                  $savingsTotal[$savingsProduct1->id] = DB::table('mfn_month_end_process_savings')
                      ->join('mfn_loans_product', 'mfn_month_end_process_savings.productIdFk', '=', 'mfn_loans_product.id')
                      ->where([['mfn_month_end_process_savings.branchIdFk', $req->filBranch],
                        ['mfn_month_end_process_savings.date', '>=', $startDate],
                        ['mfn_month_end_process_savings.date', '<=', $filDate],
                        ['mfn_month_end_process_savings.savingProductIdFk', $savingsProduct1->id],
                        ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                      ->sum('mfn_month_end_process_savings.closingBalance');
              }

              if ($req->sType == 1) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmountWithServicesCharge');
                }
              }
              elseif ($req->sType == 2) {
                foreach ($loanProductCategory as $key => $loanProductCategory1) {
                    $outstandingTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                        ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                        ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                            ['mfn_month_end_process_loans.date', '>=', $startDate],
                            ['mfn_month_end_process_loans.date', '<=', $filDate],
                            ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                            ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                        ->sum('mfn_month_end_process_loans.closingOutstandingAmount');
                }
              }

              foreach ($outstandingTotal as $key => $outstandingTotal1) {
                if ($key == 1 || $key == 3) {
                  $generalOutstandingTotal = $generalOutstandingTotal + $outstandingTotal1;
                }
                elseif ($key == 2) {
                  $microOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 4) {
                  $agricultureOutstandingTotal = $outstandingTotal1;
                }
                elseif ($key == 5) {
                  $othersOutstandingTotal = $outstandingTotal1;
                }
              }

              foreach ($loanProductCategory as $key => $loanProductCategory1) {
                // $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                //         ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                //     ->sum('mfn_month_end_process_loans.disbursedAmount');

                $disbursmentTotal[$loanProductCategory1->id] = DB::table('mfn_loan')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.disbursementDate', '<=', $filDate],
                    ['mfn_loan.softDel',0],
                    ['mfn_loans_product.productCategoryId', $loanProductCategory1->id],
                    ['mfn_loan.branchIdFk', $req->filBranch],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                  ->sum('mfn_loan.loanAmount');
              }

              foreach ($disbursmentTotal as $key => $disbursmentTotal1) {
                if ($key == 1 || $key == 3) {
                  $generaldisbursmentTotal = $generaldisbursmentTotal + $disbursmentTotal1;
                }
                elseif ($key == 2) {
                  $microdisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 4) {
                  $agriculturedisbursmentTotal = $disbursmentTotal1;
                }
                elseif ($key == 5) {
                  $othersdisbursmentTotal = $disbursmentTotal1;
                }
              }

              if ($req->sType == 1) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                //         ['mfn_month_end_process_loans.date', '>=', $startDate],
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                //     ->sum('mfn_month_end_process_loans.recoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.branchIdFk', $req->filBranch],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                  ->sum('mfn_loan_collection.amount');
              }
              elseif ($req->sType == 2) {
                // $recoveryTotal = DB::table('mfn_month_end_process_loans')
                //     ->join('mfn_loans_product', 'mfn_month_end_process_loans.productIdFk', '=', 'mfn_loans_product.id')
                //     ->where([['mfn_month_end_process_loans.branchIdFk', $req->filBranch],
                //
                //         ['mfn_month_end_process_loans.date', '<=', $filDate],
                //         ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                //     ->sum('mfn_month_end_process_loans.principalRecoveryAmount');

                $recoveryTotal = DB::table('mfn_loan_collection')
                  ->join('mfn_loans_product', 'mfn_loan_collection.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan_collection.branchIdFk', $req->filBranch],
                    ['mfn_loan_collection.softDel',0],
                    ['mfn_loan_collection.collectionDate', '<=', $filDate],
                    ['mfn_loans_product.fundingOrganizationId', '!=', 3]])
                  ->sum('mfn_loan_collection.principalAmount');
              }

              // end of the selected funding organization but without grihayan....................................................................
            }

        }

        $D = array(
          'end' => $filDate,
          'fmale' => $femaleMembersTotal,
          'month' => $MON,
          'year'  => $YEA,
          'start' => $startDate,
          'filLlpPeriod'  => $req->filLlpPeriod
        );

        $isRoundUp = $req->sRound;
        // dd($req->sRound);

        return view('microfin.reports.mra.mraMonthlyReport.reportBody', $data, compact('maleMembersTotal', 'femaleMembersTotal',
            'maleBorrowersTotal', 'femaleBorrowersTotal', 'savingsProduct', 'savingsTotal', 'generalOutstandingTotal', 'loanProduct',
            'microOutstandingTotal', 'agricultureOutstandingTotal', 'othersOutstandingTotal', 'recoveryTotal', 'generaldisbursmentTotal',
            'microdisbursmentTotal', 'agriculturedisbursmentTotal', 'othersdisbursmentTotal', 'reportingMonth', 'D', 'reportTypes', 'isRoundUp'));
    }

}
