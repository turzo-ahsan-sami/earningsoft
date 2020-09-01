<?php

namespace App\Http\Controllers\microfin\reports\consolidatedReport;

use App\Http\Controllers\Controller;
use DB;
use App\Http\Requests;
use Response;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DateTime;
use App\globalFunction\MfnHalfYearlyEmploymentInfo;
use App\globalFunction\AccLedgerMonthEndSummary;
//include ('App/globalFunction/mfnHalfYearlyEmploymentInfo.php');
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
class ConsolidatedBalancingReportController extends Controller
{
  public function getBranchName(){
    $LoanYear = array();
    $LoanYearArray = array();
    $UniqueLoanYear = array();
    $FundingOrganization = array();

    $BranchDatas = DB::table('gnr_branch')->get();

    $ProdCats = DB::table("mfn_loans_product_category")->get();

    $LoanDatas = DB::table('mfn_loan')
                   ->select(DB::raw('EXTRACT(YEAR FROM disbursementDate) as Year'))
                   ->get();
    $FundingOrganization = DB::table('mfn_funding_organization')
                  ->get();

    foreach ($LoanDatas as $key => $LoanData) {
      $LoanYear[] = $LoanData->Year;
    }

    $LoanYearArray = array($LoanYear);

    foreach ($LoanYearArray as $key => $LoanYearArrays) {
        $UniqueLoanYear = array_unique($LoanYearArrays);
    }

    return view('microfin.reports.consolidatedBalancingReport.ConsolidatedBalancingReportForm', compact('BranchDatas', 'ProdCats', 'UniqueLoanYear', 'FundingOrganization'));

  }

  public function getSamity(Request $request){
    $BranchId = $request->id;

    $SamityName = array();

    if ($BranchId != 'All') {
      $SamityName = DB::table('mfn_samity')
                      ->where('branchId', $BranchId)
                      ->get();
    }
    elseif ($BranchId == 'All') {
      $SamityName = DB::table('mfn_samity')->get();
    }

    return response()->json($SamityName);

  }

  // Start of Branch Information Query
  public function BranchQuery($RequestedBranchID){
    $Branch = array();

    if ($RequestedBranchID == 'All') {
      $Branch = DB::table('gnr_branch')
              ->get();
    }
    else {
      $Branch = DB::table('gnr_branch')
              ->where('id', $RequestedBranchID)
              ->get();
    }

    // dd($Branch);

    return $Branch;
  }
  // End of Branch Information Query

  // Start of the loan product category Query
  public function LoanProductCategoryQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat){
    $ProCat = array();

    if ($RequestedOrganization == 'All') {
      $ProCat = DB::table('mfn_loans_product_category')->get();
    }
    else {
      $ProCat = DB::table('mfn_loans_product_category')
              ->select('mfn_loans_product_category.id', 'mfn_loans_product_category.name')
              ->join('mfn_loans_product', 'mfn_loans_product_category.id', 'mfn_loans_product.productCategoryId')
              ->where([['mfn_loans_product.fundingOrganizationId', $RequestedOrganization]])
              ->groupBy('mfn_loans_product_category.id')
              ->get();
    }

    return $ProCat;
  }
  // End of the loan Product Category Query

  // Start of the Loan Product Query
  public function LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat){
    // dd($RequestedOrganization);
    $LoanProduct = array();
    $LoanProductCount = array();
    $TotalLoanProduct = array();
    $ProCat = array();
    $Branch = array();
    $Product = array();

    $ProCat = $this->LoanProductCategoryQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    // dd($ProCat);

    $Branch = $this->BranchQuery($RequestedBranchID);

    if ($RequestedBranchID == 'All' and $RequestedOrganization == 'All') {
      foreach ($Branch as $key => $Branch1) {
        foreach ($ProCat as $key => $ProCat1) {
          $LoanProduct[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loans_product.productCategoryId', $ProCat1->id]])
                  ->groupBy('mfn_loans_product.id')
                  ->get();

          $LoanProductCount[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  // ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loans_product.productCategoryId', $ProCat1->id]])
                  ->distinct('mfn_loans_product.id')
                  ->count('mfn_loans_product.id');
        }
      }
    }
    elseif ($RequestedBranchID != 'All' and $RequestedOrganization == 'All') {
      foreach ($Branch as $key => $Branch1) {
        foreach ($ProCat as $key => $ProCat1) {
          $LoanProduct[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.branchIdFk', $Branch1->id], ['mfn_loans_product.productCategoryId', $ProCat1->id]])
                  ->groupBy('mfn_loans_product.id')
                  ->get();

          $LoanProductCount[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  // ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.branchIdFk', $Branch1->id], ['mfn_loans_product.productCategoryId', $ProCat1->id]])
                  ->distinct('mfn_loans_product.id')
                  ->count('mfn_loans_product.id');
        }
      }
    }
    elseif ($RequestedBranchID == 'All' and $RequestedOrganization != 'All') {
      foreach ($Branch as $key => $Branch1) {
        foreach ($ProCat as $key => $ProCat1) {
          $LoanProduct[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loans_product.productCategoryId', $ProCat1->id], ['mfn_loans_product.fundingOrganizationId', $RequestedOrganization]])
                  ->groupBy('mfn_loans_product.id')
                  ->get();

          $LoanProductCount[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  // ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loans_product.productCategoryId', $ProCat1->id], ['mfn_loans_product.fundingOrganizationId', $RequestedOrganization]])
                  ->distinct('mfn_loans_product.id')
                  ->count('mfn_loans_product.id');
        }
      }
    }
    elseif ($RequestedBranchID != 'All' and $RequestedOrganization != 'All') {
      foreach ($Branch as $key => $Branch1) {
        foreach ($ProCat as $key => $ProCat1) {
          $LoanProduct[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.branchIdFk', $Branch1->id], ['mfn_loans_product.productCategoryId', $ProCat1->id], ['mfn_loans_product.fundingOrganizationId', $RequestedOrganization]])
                  ->groupBy('mfn_loans_product.id')
                  ->get();

          $LoanProductCount[$Branch1->id][$ProCat1->id] = DB::table('mfn_loan')
                  // ->select('mfn_loans_product.id', 'mfn_loans_product.name', 'mfn_loans_product.shortName')
                  ->join('mfn_loans_product', 'mfn_loan.productIdFk', '=', 'mfn_loans_product.id')
                  ->where([['mfn_loan.branchIdFk', $Branch1->id], ['mfn_loans_product.productCategoryId', $ProCat1->id], ['mfn_loans_product.fundingOrganizationId', $RequestedOrganization]])
                  ->distinct('mfn_loans_product.id')
                  ->count('mfn_loans_product.id');
        }
      }
    }

    // dd($LoanProduct);

    foreach ($LoanProductCount as $key => $LoanProductCount1) {
      $TotalLoanProduct[$key] = array_sum($LoanProductCount1);
    }

    // dd($TotalLoanProduct);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        foreach ($LoanProductCount as $key1A => $LoanProductCount1) {
          if ($key1 == $key1A) {
            foreach ($LoanProductCount1 as $key2A => $LoanProductCount2) {

              if ($key2 == $key2A) {
                foreach ($TotalLoanProduct as $key1Aa => $TotalLoanProduct1) {
                  if ($key1Aa == $key1A) {
                    if (sizeof($LoanProduct2) > 0) {
                      $Product[$key1."/".$key2."/".$LoanProductCount2."/".$TotalLoanProduct1] = $LoanProduct2;

                    }
                  }
                }
              }

            }
          }
        }
      }
    }

    // dd($Product);

    return $Product;
  }
  // End of the loan Product Query

  // Start of No. of samity Query
  public function NumberOfSamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat){
    $Samity = array();

    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    // dd($LoanProduct);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $Samity[$Br][$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id]])
                    ->distinct('samityIdFk')
                    ->count('samityIdFk');
      }
    }

    // dd($Samity);

    return $Samity;
  }
  // End of No. of samity Query

  // Start of No. of samity Query
  public function SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat){
    $Samity = array();
    $SamityByProduct = array();

    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    $Branch = $this->BranchQuery($RequestedBranchID);

    // dd($LoanProduct);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $Samity[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->select('samityIdFk')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id]])
                    ->groupBy('samityIdFk')
                    ->get();
      }
    }

    // foreach ($Samity as $key1 => $Samity1) {
    //   foreach ($Samity1 as $key2 => $Samity2) {
    //     // code...
    //   }
    // }

    // dd($Samity);

    return $Samity;
  }
  // End of No. of samity Query

  // Start of the Member Query
  public function NumberOfMemberQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat){
    $Member = array();
    $TotalMember = array();
    $tt = 0;
    $Ts = 0;

    $Samity = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($Samity as $key1 => $Samity1) {
      foreach ($Samity1 as $key2 => $Samity2) {
        // foreach ($Samity2 as $key => $Samity3) {
          $Member[$key1.'/'.$key2] = DB::table('mfn_member_information')
                    ->where([['branchId', $key1], ['samityId', $Samity2->samityIdFk]])
                    ->count('id');
        // }
      }
    }

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {

        list($Br1, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);
        if ($Ts != $LoanProduct2->id) {
          $Ts = $LoanProduct2->id;
          $tt = 0;
        }
        // dd($Ts);

        foreach ($Member as $key3 => $Member1) {
          list($Br, $Prod, $no) = explode("/", $key3);
          if ($Br1 == $Br and $Prod == $LoanProduct2->id) {
            $tt = $tt + $Member1;
            $TotalMember[$Br1.'/'.$Prod] = $tt;
          }
        }

      }
    }

    // dd($TotalMember);

    return $TotalMember;
  }
  // End of the Member Query

  // Start of No. of Borrower Query
  public function NumberOfBorrowerQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat){
    $Member = array();

    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $Member[$Br][$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0]])
                    ->distinct('memberIdFk')
                    ->count('memberIdFk');
      }
    }

    // dd($Member);

    return $Member;
  }
  // End of No. of Borrower Query

  // Start of the Current Loan
  public function CurrentLoanQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth){
    $CurrentLoan = array();

    $Date = '';
    $DisbursedDate = '';

    switch ($RequestedMonth) {
       case 1:
           $Date = $RequestedYear.'-01-31';
           $DisbursedDate = $RequestedYear.'-01-01';
           // January
           break;
       case 2:
           $Date = $RequestedYear.'-02-28';
           $DisbursedDate = $RequestedYear.'-02-01';
           // February
           break;
       case 3:
           $Date = $RequestedYear.'-03-31';
           $DisbursedDate = $RequestedYear.'-03-01';
           // March
           break;
       case 4:
           $Date = $RequestedYear.'-04-30';
           $DisbursedDate = $RequestedYear.'-04-01';
           // Appril
           break;
       case 5:
           $Date = $RequestedYear.'-05-31';
           $DisbursedDate = $RequestedYear.'-05-01';
           // May
           break;
       case 6:
           $Date = $RequestedYear.'-06-30';
           $DisbursedDate = $RequestedYear.'-06-01';
           // June
           break;
       case 7:
           $Date = $RequestedYear.'-07-31';
           $DisbursedDate = $RequestedYear.'-07-01';
           // July
           break;
       case 8:
           $Date = $RequestedYear.'-08-31';
           $DisbursedDate = $RequestedYear.'-08-01';
           // August
           break;
       case 9:
           $Date = $RequestedYear.'-09-30';
           $DisbursedDate = $RequestedYear.'-09-01';
           // September
           break;
       case 10:
           $Date = $RequestedYear.'-10-31';
           $DisbursedDate = $RequestedYear.'-010-01';
           // October
           break;
       case 11:
           $Date = $RequestedYear.'-11-30';
           $DisbursedDate = $RequestedYear.'-11-01';
           // November
           break;
       case 12:
           $Date = $RequestedYear.'-12-31';
           $DisbursedDate = $RequestedYear.'-12-01';
           // December
           break;
       default:
           echo "No Date Found!";
   }


    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $CurrentLoan[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<=', $Date], ['lastInstallmentDate', '>', $Date],])
                    // ->distinct('memberIdFk')
                    ->sum('totalRepayAmount');
      }
    }
    // dd($CurrentLoan);

    return $CurrentLoan;
  }
  // End of the Current Loan

  // Start of the Lan Outstanding PRinciple amount
  public function LoanOutstandingPrincipleQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth){
    $DisbursmentAmount = array();
    $CurrentCollectionAmount = array();
    $LoanOutstandingPrinciple = array();
    $Date = '';
    $DisbursedDate = '';

    switch ($RequestedMonth) {
       case 1:
           $Date = $RequestedYear.'-01-31';
           $DisbursedDate = $RequestedYear.'-01-01';
           // January
           break;
       case 2:
           $Date = $RequestedYear.'-02-28';
           $DisbursedDate = $RequestedYear.'-02-01';
           // February
           break;
       case 3:
           $Date = $RequestedYear.'-03-31';
           $DisbursedDate = $RequestedYear.'-03-01';
           // March
           break;
       case 4:
           $Date = $RequestedYear.'-04-30';
           $DisbursedDate = $RequestedYear.'-04-01';
           // Appril
           break;
       case 5:
           $Date = $RequestedYear.'-05-31';
           $DisbursedDate = $RequestedYear.'-05-01';
           // May
           break;
       case 6:
           $Date = $RequestedYear.'-06-30';
           $DisbursedDate = $RequestedYear.'-06-01';
           // June
           break;
       case 7:
           $Date = $RequestedYear.'-07-31';
           $DisbursedDate = $RequestedYear.'-07-01';
           // July
           break;
       case 8:
           $Date = $RequestedYear.'-08-31';
           $DisbursedDate = $RequestedYear.'-08-01';
           // August
           break;
       case 9:
           $Date = $RequestedYear.'-09-30';
           $DisbursedDate = $RequestedYear.'-09-01';
           // September
           break;
       case 10:
           $Date = $RequestedYear.'-10-31';
           $DisbursedDate = $RequestedYear.'-010-01';
           // October
           break;
       case 11:
           $Date = $RequestedYear.'-11-30';
           $DisbursedDate = $RequestedYear.'-11-01';
           // November
           break;
       case 12:
           $Date = $RequestedYear.'-12-31';
           $DisbursedDate = $RequestedYear.'-12-01';
           // December
           break;
       default:
           echo "No Date Found!";
   }

    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<=', $Date], ['lastInstallmentDate', '>', $Date]])
                    ->sum('loanAmount');

        $CurrentCollectionAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                    ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<=', $Date], ['mfn_loan.lastInstallmentDate', '>', $Date], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
                    ->sum('mfn_loan_collection.principalAmount');
      }
    }

    // dd($CurrentCollectionAmount);

    foreach ($CurrentCollectionAmount as $key1 => $CurrentCollectionAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($DisbursmentAmount as $key2 => $DisbursmentAmount1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $DisbursmentAmount1 >= $CurrentCollectionAmount1) {
          $LoanOutstandingPrinciple[$Br1.'/'.$Prod1] = $DisbursmentAmount1 - $CurrentCollectionAmount1;
        }
      }
    }

    // dd($LoanOutstandingPrinciple);

    return $LoanOutstandingPrinciple;
  }
  // End of the Lan Outstanding PRinciple amount

  // Start of the loan Outstanding Service Charge amount
  public function LoanOutstandingServiceChargeQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth){
    $DisbursmentAmount = array();
    $RepayAmount = array();
    $CurrentCollectionAmount = array();
    $TotalServiceCharge = array();
    $OutstandingServiceCharge = array();
    $Date = '';
    $DisbursedDate = '';

    // dd($RequestedMonth);

    switch ($RequestedMonth) {
       case 1:
           $Date = $RequestedYear.'-01-31';
           $DisbursedDate = $RequestedYear.'-01-01';
           // January
           break;
       case 2:
           $Date = $RequestedYear.'-02-28';
           $DisbursedDate = $RequestedYear.'-02-01';
           // February
           break;
       case 3:
           $Date = $RequestedYear.'-03-31';
           $DisbursedDate = $RequestedYear.'-03-01';
           // March
           break;
       case 4:
           $Date = $RequestedYear.'-04-30';
           $DisbursedDate = $RequestedYear.'-04-01';
           // Appril
           break;
       case 5:
           $Date = $RequestedYear.'-05-31';
           $DisbursedDate = $RequestedYear.'-05-01';
           // May
           break;
       case 6:
           $Date = $RequestedYear.'-06-30';
           $DisbursedDate = $RequestedYear.'-06-01';
           // June
           break;
       case 7:
           $Date = $RequestedYear.'-07-31';
           $DisbursedDate = $RequestedYear.'-07-01';
           // July
           break;
       case 8:
           $Date = $RequestedYear.'-08-31';
           $DisbursedDate = $RequestedYear.'-08-01';
           // August
           break;
       case 9:
           $Date = $RequestedYear.'-09-30';
           $DisbursedDate = $RequestedYear.'-09-01';
           // September
           break;
       case 10:
           $Date = $RequestedYear.'-10-31';
           $DisbursedDate = $RequestedYear.'-010-01';
           // October
           break;
       case 11:
           $Date = $RequestedYear.'-11-30';
           $DisbursedDate = $RequestedYear.'-11-01';
           // November
           break;
       case 12:
           $Date = $RequestedYear.'-12-31';
           $DisbursedDate = $RequestedYear.'-12-01';
           // December
           break;
       default:
           echo "No Date Found!";
   }

    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0]])
                    ->sum('loanAmount');

        $RepayAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0]])
                    ->sum('totalRepayAmount');

        $CurrentCollectionAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                    ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<=', $Date], ['mfn_loan.lastInstallmentDate', '>', $Date], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
                    ->sum('mfn_loan_collection.interestAmount');
      }
    }

    foreach ($RepayAmount as $key1 => $RepayAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($DisbursmentAmount as $key2 => $DisbursmentAmount1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $RepayAmount1 >= $DisbursmentAmount1) {
          $TotalServiceCharge[$Br1.'/'.$Prod1] = $RepayAmount1 - $DisbursmentAmount1;
        }
      }
    }

    foreach ($CurrentCollectionAmount as $key1 => $CurrentCollectionAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($TotalServiceCharge as $key2 => $TotalServiceCharge1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $TotalServiceCharge1 >= $CurrentCollectionAmount1) {
          $OutstandingServiceCharge[$Br1.'/'.$Prod1] = $TotalServiceCharge1 - $CurrentCollectionAmount1;
        }
      }
    }

    // dd($OutstandingServiceCharge);

    return $OutstandingServiceCharge;
  }
  // End of the laon Outstanding Service Charge amount

  // Start of the Outstanding Total
  public function OutstandingTotalQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth){
    $DisbursmentAmount = array();
    $RepayAmount = array();
    $CurrentCollectionAmount = array();
    $TotalServiceCharge = array();
    $OutstandingServiceCharge = array();
    $DisbursmentAmountPrinciple = array();
    $CurrentCollectionAmountPrinciple = array();
    $OutstandingPrincipleAmount = array();
    $TotalOutstandings = array();
    $Date = '';
    $DisbursedDate = '';

    switch ($RequestedMonth) {
       case 1:
           $Date = $RequestedYear.'-01-31';
           $DisbursedDate = $RequestedYear.'-01-01';
           // January
           break;
       case 2:
           $Date = $RequestedYear.'-02-28';
           $DisbursedDate = $RequestedYear.'-02-01';
           // February
           break;
       case 3:
           $Date = $RequestedYear.'-03-31';
           $DisbursedDate = $RequestedYear.'-03-01';
           // March
           break;
       case 4:
           $Date = $RequestedYear.'-04-30';
           $DisbursedDate = $RequestedYear.'-04-01';
           // Appril
           break;
       case 5:
           $Date = $RequestedYear.'-05-31';
           $DisbursedDate = $RequestedYear.'-05-01';
           // May
           break;
       case 6:
           $Date = $RequestedYear.'-06-30';
           $DisbursedDate = $RequestedYear.'-06-01';
           // June
           break;
       case 7:
           $Date = $RequestedYear.'-07-31';
           $DisbursedDate = $RequestedYear.'-07-01';
           // July
           break;
       case 8:
           $Date = $RequestedYear.'-08-31';
           $DisbursedDate = $RequestedYear.'-08-01';
           // August
           break;
       case 9:
           $Date = $RequestedYear.'-09-30';
           $DisbursedDate = $RequestedYear.'-09-01';
           // September
           break;
       case 10:
           $Date = $RequestedYear.'-10-31';
           $DisbursedDate = $RequestedYear.'-010-01';
           // October
           break;
       case 11:
           $Date = $RequestedYear.'-11-30';
           $DisbursedDate = $RequestedYear.'-11-01';
           // November
           break;
       case 12:
           $Date = $RequestedYear.'-12-31';
           $DisbursedDate = $RequestedYear.'-12-01';
           // December
           break;
       default:
           echo "No Date Found!";
   }

    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    //

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<=', $Date]])
                    ->sum('loanAmount');

        $RepayAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<=', $Date]])
                    ->sum('totalRepayAmount');

        $CurrentCollectionAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                    ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<=', $Date], ['mfn_loan.lastInstallmentDate', '>', $Date], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
                    ->sum('mfn_loan_collection.interestAmount');
      }
    }

    foreach ($RepayAmount as $key1 => $RepayAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($DisbursmentAmount as $key2 => $DisbursmentAmount1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $RepayAmount1 >= $DisbursmentAmount1) {
          $TotalServiceCharge[$Br1.'/'.$Prod1] = $RepayAmount1 - $DisbursmentAmount1;
        }
      }
    }

    foreach ($CurrentCollectionAmount as $key1 => $CurrentCollectionAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($TotalServiceCharge as $key2 => $TotalServiceCharge1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $TotalServiceCharge1 >= $CurrentCollectionAmount1) {
          $OutstandingServiceCharge[$Br1.'/'.$Prod1] = $TotalServiceCharge1 - $CurrentCollectionAmount1;
        }
      }
    }

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $DisbursmentAmountPrinciple[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<=', $Date], ['lastInstallmentDate', '>', $Date]])
                    ->sum('loanAmount');

        $CurrentCollectionAmountPrinciple[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                    ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<=', $Date], ['mfn_loan.lastInstallmentDate', '>', $Date], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
                    ->sum('mfn_loan_collection.principalAmount');
      }
    }

    // dd($CurrentCollectionAmount);

    foreach ($CurrentCollectionAmountPrinciple as $key1 => $CurrentCollectionAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($DisbursmentAmountPrinciple as $key2 => $DisbursmentAmount1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $DisbursmentAmount1 >= $CurrentCollectionAmount1) {
          $OutstandingPrincipleAmount[$Br1.'/'.$Prod1] = $DisbursmentAmount1 - $CurrentCollectionAmount1;
        }
      }
    }

    foreach ($OutstandingServiceCharge as $key1 => $OutstandingServiceCharge1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($OutstandingPrincipleAmount as $key2 => $OutstandingPrincipleAmount1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $OutstandingPrincipleAmount1 >= $OutstandingServiceCharge1) {
          $TotalOutstandings[$Br1.'/'.$Prod1] = $OutstandingPrincipleAmount1 + $OutstandingServiceCharge1;
        }
      }
    }

    //

    // foreach ($LoanProduct as $key1 => $LoanProduct1) {
    //   foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
    //     list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);
    //
    //     $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
    //                 ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<', $DisbursedDate], ['lastInstallmentDate', '>', $Date]])
    //                 ->sum('loanAmount');
    //
    //     $CurrentCollectionAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
    //                 ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
    //                 ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<', $DisbursedDate], ['mfn_loan.lastInstallmentDate', '>', $Date], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
    //                 ->sum('mfn_loan_collection.amount');
    //   }
    // }
    //
    // foreach ($CurrentCollectionAmount as $key1 => $CurrentCollectionAmount1) {
    //   list($Br1, $Prod1) = explode("/", $key1);
    //   foreach ($DisbursmentAmount as $key2 => $DisbursmentAmount1) {
    //     list($Br2, $Prod2) = explode("/", $key2);
    //     if ($Br1 == $Br2 and $Prod1 == $Prod2 and $DisbursmentAmount1 >= $CurrentCollectionAmount1) {
    //       $LoanOutstandingPrinciple[$Br1.'/'.$Prod1] = $DisbursmentAmount1 - $CurrentCollectionAmount1;
    //     }
    //   }
    // }

    // dd($TotalOutstandings);

    return $TotalOutstandings;
  }
  // End of the Outstanding Total

  // Start of the Overdue Principal
  public function OverduePrincipalQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth){
    $DisbursmentAmount = array();
    $CurrentCollectionAmount = array();
    $LoanOutstandingPrinciple = array();
    $Date = '';
    $DisbursedDate = '';

    switch ($RequestedMonth) {
       case 1:
           $Date = $RequestedYear.'-01-31';
           $DisbursedDate = $RequestedYear.'-01-01';
           // January
           break;
       case 2:
           $Date = $RequestedYear.'-02-28';
           $DisbursedDate = $RequestedYear.'-02-01';
           // February
           break;
       case 3:
           $Date = $RequestedYear.'-03-31';
           $DisbursedDate = $RequestedYear.'-03-01';
           // March
           break;
       case 4:
           $Date = $RequestedYear.'-04-30';
           $DisbursedDate = $RequestedYear.'-04-01';
           // Appril
           break;
       case 5:
           $Date = $RequestedYear.'-05-31';
           $DisbursedDate = $RequestedYear.'-05-01';
           // May
           break;
       case 6:
           $Date = $RequestedYear.'-06-30';
           $DisbursedDate = $RequestedYear.'-06-01';
           // June
           break;
       case 7:
           $Date = $RequestedYear.'-07-31';
           $DisbursedDate = $RequestedYear.'-07-01';
           // July
           break;
       case 8:
           $Date = $RequestedYear.'-08-31';
           $DisbursedDate = $RequestedYear.'-08-01';
           // August
           break;
       case 9:
           $Date = $RequestedYear.'-09-30';
           $DisbursedDate = $RequestedYear.'-09-01';
           // September
           break;
       case 10:
           $Date = $RequestedYear.'-10-31';
           $DisbursedDate = $RequestedYear.'-010-01';
           // October
           break;
       case 11:
           $Date = $RequestedYear.'-11-30';
           $DisbursedDate = $RequestedYear.'-11-01';
           // November
           break;
       case 12:
           $Date = $RequestedYear.'-12-31';
           $DisbursedDate = $RequestedYear.'-12-01';
           // December
           break;
       default:
           echo "No Date Found!";
   }

    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<', $DisbursedDate], ['lastInstallmentDate', '<', $DisbursedDate]])
                    ->sum('loanAmount');

        $CurrentCollectionAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                    ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<', $DisbursedDate], ['mfn_loan.lastInstallmentDate', '<', $DisbursedDate], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
                    ->sum('mfn_loan_collection.principalAmount');
      }
    }

    foreach ($CurrentCollectionAmount as $key1 => $CurrentCollectionAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($DisbursmentAmount as $key2 => $DisbursmentAmount1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $DisbursmentAmount1 >= $CurrentCollectionAmount1) {
          $LoanOutstandingPrinciple[$Br1.'/'.$Prod1] = $DisbursmentAmount1 - $CurrentCollectionAmount1;
        }
      }
    }

    // dd($LoanOutstandingPrinciple);

    return $LoanOutstandingPrinciple;
  }
  // End of the Overdue Principal

  // Start of the Overdue Service Charge
  public function  OverdueServiceChargeQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth){
    $DisbursmentAmount = array();
    $CurrentCollectionAmount = array();
    $LoanOutstandingPrinciple = array();
    $Date = '';
    $DisbursedDate = '';

    switch ($RequestedMonth) {
       case 1:
           $Date = $RequestedYear.'-01-31';
           $DisbursedDate = $RequestedYear.'-01-01';
           // January
           break;
       case 2:
           $Date = $RequestedYear.'-02-28';
           $DisbursedDate = $RequestedYear.'-02-01';
           // February
           break;
       case 3:
           $Date = $RequestedYear.'-03-31';
           $DisbursedDate = $RequestedYear.'-03-01';
           // March
           break;
       case 4:
           $Date = $RequestedYear.'-04-30';
           $DisbursedDate = $RequestedYear.'-04-01';
           // Appril
           break;
       case 5:
           $Date = $RequestedYear.'-05-31';
           $DisbursedDate = $RequestedYear.'-05-01';
           // May
           break;
       case 6:
           $Date = $RequestedYear.'-06-30';
           $DisbursedDate = $RequestedYear.'-06-01';
           // June
           break;
       case 7:
           $Date = $RequestedYear.'-07-31';
           $DisbursedDate = $RequestedYear.'-07-01';
           // July
           break;
       case 8:
           $Date = $RequestedYear.'-08-31';
           $DisbursedDate = $RequestedYear.'-08-01';
           // August
           break;
       case 9:
           $Date = $RequestedYear.'-09-30';
           $DisbursedDate = $RequestedYear.'-09-01';
           // September
           break;
       case 10:
           $Date = $RequestedYear.'-10-31';
           $DisbursedDate = $RequestedYear.'-010-01';
           // October
           break;
       case 11:
           $Date = $RequestedYear.'-11-30';
           $DisbursedDate = $RequestedYear.'-11-01';
           // November
           break;
       case 12:
           $Date = $RequestedYear.'-12-31';
           $DisbursedDate = $RequestedYear.'-12-01';
           // December
           break;
       default:
           echo "No Date Found!";
   }

    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        // $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
        //             ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<', $DisbursedDate], ['lastInstallmentDate', '<', $DisbursedDate]])
        //             ->sum('loanAmount');

        $CurrentCollectionAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                    ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<', $DisbursedDate], ['mfn_loan.lastInstallmentDate', '<', $DisbursedDate], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
                    ->sum('mfn_loan_collection.interestAmount');
      }
    }

    // foreach ($CurrentCollectionAmount as $key1 => $CurrentCollectionAmount1) {
    //   list($Br1, $Prod1) = explode("/", $key1);
    //   foreach ($DisbursmentAmount as $key2 => $DisbursmentAmount1) {
    //     list($Br2, $Prod2) = explode("/", $key2);
    //     if ($Br1 == $Br2 and $Prod1 == $Prod2 and $DisbursmentAmount1 >= $CurrentCollectionAmount1) {
    //       $LoanOutstandingPrinciple[$Br1.'/'.$Prod1] = $DisbursmentAmount1 - $CurrentCollectionAmount1;
    //     }
    //   }
    // }

    // dd($LoanOutstandingPrinciple);

    return $CurrentCollectionAmount;
  }
  // End of the Overdue Service Charge

  // Start of the Overdue Total
  public function  OverdueTotalQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth){
    $DisbursmentAmount = array();
    $CurrentCollectionAmount = array();
    $LoanOutstandingPrinciple = array();
    $Date = '';
    $DisbursedDate = '';

    switch ($RequestedMonth) {
       case 1:
           $Date = $RequestedYear.'-01-31';
           $DisbursedDate = $RequestedYear.'-01-01';
           // January
           break;
       case 2:
           $Date = $RequestedYear.'-02-28';
           $DisbursedDate = $RequestedYear.'-02-01';
           // February
           break;
       case 3:
           $Date = $RequestedYear.'-03-31';
           $DisbursedDate = $RequestedYear.'-03-01';
           // March
           break;
       case 4:
           $Date = $RequestedYear.'-04-30';
           $DisbursedDate = $RequestedYear.'-04-01';
           // Appril
           break;
       case 5:
           $Date = $RequestedYear.'-05-31';
           $DisbursedDate = $RequestedYear.'-05-01';
           // May
           break;
       case 6:
           $Date = $RequestedYear.'-06-30';
           $DisbursedDate = $RequestedYear.'-06-01';
           // June
           break;
       case 7:
           $Date = $RequestedYear.'-07-31';
           $DisbursedDate = $RequestedYear.'-07-01';
           // July
           break;
       case 8:
           $Date = $RequestedYear.'-08-31';
           $DisbursedDate = $RequestedYear.'-08-01';
           // August
           break;
       case 9:
           $Date = $RequestedYear.'-09-30';
           $DisbursedDate = $RequestedYear.'-09-01';
           // September
           break;
       case 10:
           $Date = $RequestedYear.'-10-31';
           $DisbursedDate = $RequestedYear.'-010-01';
           // October
           break;
       case 11:
           $Date = $RequestedYear.'-11-30';
           $DisbursedDate = $RequestedYear.'-11-01';
           // November
           break;
       case 12:
           $Date = $RequestedYear.'-12-31';
           $DisbursedDate = $RequestedYear.'-12-01';
           // December
           break;
       default:
           echo "No Date Found!";
   }

    $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    foreach ($LoanProduct as $key1 => $LoanProduct1) {
      foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
        list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

        $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<', $DisbursedDate], ['lastInstallmentDate', '<', $DisbursedDate]])
                    ->sum('loanAmount');

        $CurrentCollectionAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                    ->join('mfn_loan_collection', 'mfn_loan.id', '=', 'mfn_loan_collection.loanIdFk')
                    ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<', $DisbursedDate], ['mfn_loan.lastInstallmentDate', '<', $DisbursedDate], ['mfn_loan.isLoanCompleted', '=', 0], ['mfn_loan_collection.collectionDate', '<=', $Date]])
                    ->sum('mfn_loan_collection.amount');
      }
    }

    foreach ($CurrentCollectionAmount as $key1 => $CurrentCollectionAmount1) {
      list($Br1, $Prod1) = explode("/", $key1);
      foreach ($DisbursmentAmount as $key2 => $DisbursmentAmount1) {
        list($Br2, $Prod2) = explode("/", $key2);
        if ($Br1 == $Br2 and $Prod1 == $Prod2 and $DisbursmentAmount1 >= $CurrentCollectionAmount1) {
          $LoanOutstandingPrinciple[$Br1.'/'.$Prod1] = $DisbursmentAmount1 - $CurrentCollectionAmount1;
        }
      }
    }

    // dd($LoanOutstandingPrinciple);

    return $LoanOutstandingPrinciple;
  }
  // End of the Overdue Total

  // Start of the Savings Product Query
  public function SavingsProductQury(){
    $SavingsProduct = array();

    $SavingsProduct = DB::table('mfn_saving_product')->get();

    return $SavingsProduct;
  }
  // End of the Savings Product Query

  // Start of the Savings Balance Query
   public function SavingsBalanceQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth, $RequestedSavingsBalance){
     $SavingsBalance = array();
     $BalanceAmount = array();
     $SavingsBalanceAmount = array();

     $Date = '';
     $DisbursedDate = '';

     switch ($RequestedMonth) {
        case 1:
            $Date = $RequestedYear.'-01-31';
            $DisbursedDate = $RequestedYear.'-01-01';
            // January
            break;
        case 2:
            $Date = $RequestedYear.'-02-28';
            $DisbursedDate = $RequestedYear.'-02-01';
            // February
            break;
        case 3:
            $Date = $RequestedYear.'-03-31';
            $DisbursedDate = $RequestedYear.'-03-01';
            // March
            break;
        case 4:
            $Date = $RequestedYear.'-04-30';
            $DisbursedDate = $RequestedYear.'-04-01';
            // Appril
            break;
        case 5:
            $Date = $RequestedYear.'-05-31';
            $DisbursedDate = $RequestedYear.'-05-01';
            // May
            break;
        case 6:
            $Date = $RequestedYear.'-06-30';
            $DisbursedDate = $RequestedYear.'-06-01';
            // June
            break;
        case 7:
            $Date = $RequestedYear.'-07-31';
            $DisbursedDate = $RequestedYear.'-07-01';
            // July
            break;
        case 8:
            $Date = $RequestedYear.'-08-31';
            $DisbursedDate = $RequestedYear.'-08-01';
            // August
            break;
        case 9:
            $Date = $RequestedYear.'-09-30';
            $DisbursedDate = $RequestedYear.'-09-01';
            // September
            break;
        case 10:
            $Date = $RequestedYear.'-10-31';
            $DisbursedDate = $RequestedYear.'-010-01';
            // October
            break;
        case 11:
            $Date = $RequestedYear.'-11-30';
            $DisbursedDate = $RequestedYear.'-11-01';
            // November
            break;
        case 12:
            $Date = $RequestedYear.'-12-31';
            $DisbursedDate = $RequestedYear.'-12-01';
            // December
            break;
        default:
            echo "No Date Found!";
    }

     $SamityQuery = $this->SamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
     $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

     // dd($LoanProduct);

     foreach ($LoanProduct as $key1 => $LoanProduct1) {
       foreach ($LoanProduct1 as $key2 => $LoanProduct2) {
         list($Br, $PC, $CategoryRowSize, $BranchRowSize) = explode("/", $key1);

         // $DisbursmentAmount[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
         //             ->where([['branchIdFk', $Br], ['productIdFk', $LoanProduct2->id], ['isLoanCompleted', '=', 0], ['disbursementDate', '<', $DisbursedDate], ['lastInstallmentDate', '<', $DisbursedDate]])
         //             ->sum('loanAmount');

         $SavingsBalance[$Br.'/'.$LoanProduct2->id] = DB::table('mfn_loan')
                     ->select('mfn_loan.branchIdFk', 'mfn_loan.productIdFk', 'mfn_savings_account.id', 'mfn_savings_account.savingsProductIdFk')
                     ->join('mfn_savings_account', 'mfn_loan.memberIdFk', '=', 'mfn_savings_account.memberIdFk')
                     ->where([['mfn_loan.branchIdFk', $Br], ['mfn_loan.productIdFk', $LoanProduct2->id], ['mfn_loan.disbursementDate', '<', $DisbursedDate], ['mfn_loan.lastInstallmentDate', '>', $Date], ['mfn_loan.isLoanCompleted', '=', 0]])
                     // ->sum('mfn_loan_collection.amount');
                     ->groupBy()
                     ->get();
       }
     }

     // dd($SavingsBalance);

     if ($RequestedSavingsBalance == 'All') {
       foreach ($SavingsBalance as $key1 => $SavingsBalance1) {
         foreach ($SavingsBalance1 as $key2 => $SavingsBalance2) {
           // $BalanceAmount[$key1][$SavingsBalance2->savingsProductIdFk] = DB::table('mfn_savings_account')
           //            ->join('mfn_savings_account_collection', 'mfn_savings_account.id', '=', 'mfn_savings_account_collection.savingsAccountId')
           //            ->where([['mfn_savings_account.branchIdFk', $SavingsBalance2->branchIdFk], ['mfn_savings_account.id', $SavingsBalance2->id], ['mfn_savings_account.savingsProductIdFk', $SavingsBalance2->savingsProductIdFk]])
           //            ->sum('mfn_savings_account_collection.savingsCollectionAmount');
           $BalanceAmount[$key1][$SavingsBalance2->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                        ->where([['accountIdFk', $SavingsBalance2->id], ['branchIdFk', $SavingsBalance2->branchIdFk], ['productIdFk', $SavingsBalance2->savingsProductIdFk], ['depositDate', '<=', $Date]])
                        ->sum('amount');
         }
       }
       // dd($BalanceAmount);
       // $SavingsBalanceAmount = array_sum($BalanceAmount);

       foreach ($BalanceAmount as $key => $BalanceAmount1) {
         // list($Br, $Prod, $SavPro) = explode("/", $key);

         $SavingsBalanceAmount[$key] = array_sum($BalanceAmount1);
       }
       // dd($SavingsBalanceAmount);
     }
     else {
       foreach ($SavingsBalance as $key1 => $SavingsBalance1) {
         foreach ($SavingsBalance1 as $key2 => $SavingsBalance2) {
           // $SavingsBalanceAmount[$key1.'/'.$SavingsBalance2->savingsProductIdFk] = DB::table('mfn_savings_account')
           //            ->join('mfn_savings_account_collection', 'mfn_savings_account.id', '=', 'mfn_savings_account_collection.savingsAccountId')
           //            ->where([['mfn_savings_account.branchIdFk', $SavingsBalance2->branchIdFk], ['mfn_savings_account.id', $SavingsBalance2->id], ['mfn_savings_account.savingsProductIdFk', $SavingsBalance2->savingsProductIdFk]])
           //            ->sum('mfn_savings_account_collection.savingsCollectionAmount');
           $SavingsBalanceAmount[$key1.'/'.$SavingsBalance2->savingsProductIdFk] = DB::table('mfn_savings_deposit')
                        ->where([['accountIdFk', $SavingsBalance2->id], ['branchIdFk', $SavingsBalance2->branchIdFk], ['productIdFk', $SavingsBalance2->savingsProductIdFk], ['depositDate', '<=', $Date]])
                        ->sum('amount');
         }
       }
     }

     // dd($SavingsBalanceAmount);

     return $SavingsBalanceAmount;
   }
  // End of the Savings BAlance Query

  // Start of the Branch Manager Query
  public function BranchManagerNameQuery($RequestedBranchID) {
    $Branch = array();
    $Manager = array();

    if ($RequestedBranchID == 'All') {
      $Branch = $this->BranchQuery($RequestedBranchID);

      foreach ($Branch as $key => $Branch1) {
        $Manager[] = DB::table('hr_emp_org_info')
                  ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id', 'gnr_branch.id', 'hr_settings_position.name as PositionName')
                  ->join('gnr_branch', 'hr_emp_org_info.branch_id_fk', '=', 'gnr_branch.id')
                  ->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', '=', 'hr_settings_position.id')
                  ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
                  ->where([['gnr_branch.id', $Branch1->id], ['hr_settings_position.name', '=', 'Branch Manager']])
                  ->get();
      }
    }
    else {
      $Manager = DB::table('hr_emp_org_info')
                ->select('hr_emp_general_info.emp_name_english', 'hr_emp_general_info.emp_id', 'gnr_branch.id', 'hr_settings_position.name as PositionName')
                ->join('gnr_branch', 'hr_emp_org_info.branch_id_fk', '=', 'gnr_branch.id')
                ->join('hr_settings_position', 'hr_emp_org_info.position_id_fk', '=', 'hr_settings_position.id')
                ->join('hr_emp_general_info', 'hr_emp_org_info.emp_id_fk', '=', 'hr_emp_general_info.id')
                ->where([['gnr_branch.id', $RequestedBranchID], ['hr_settings_position.name', '=', 'Branch Manager']])
                ->get();
    }

    // dd($Manager);

    return $Manager;
  }
  // End of the Branch Manager Query

  public function getReport(Request $request){
    // Just For Global Function Test

    // Half Yearly Employment Information start................................................
    // $b = 93;
    // $d1 = '2017-12-31';
    // // $d1 = '2018-06-30';
    // // $Hello =  new MfnHalfYearlyEmploymentInfo();
    // $a = MfnHalfYearlyEmploymentInfo::halfYearlyEmploymentInfo($b, $d1);
    //
    // dd($a);
    // Half Yearly Employment Information end..................................................

    // Ledger Month End start..................................................................
    // $bn = 1;
    // $ledId = 151;
    // // $fDate = '2018-01-01';
    // $tDate = '2018-04-30';
    // $companyId = 1;
    //
    // $fo = AccLedgerMonthEndSummary::ledgerMonthEndSummary($bn, $tDate, $companyId);
    // Ledger Month End end....................................................................

    // End of Global Function Test

    // Requested required field and decalred variable
    $RequestedBranchID = $request->searchBranch;
    $RequestedOrganization = $request->fundingOrganization;
    $RequestedProCat = $request->searchProCat;
    $RequestedSavingsBalance = $request->savingsBalance;
    $RequestedYear = $request->Year;
    $RequestedMonth = $request->month;

    // Necessary array
    $BranchInfos = array();
    $ProCat = array();
    $LoanProduct = array();
    $NumberOfSamity = array();
    $NumberOfMember = array();
    $NumberOfBorrower = array();
    $CurrentLoan = array();
    $LoanOutstandingPrinciple = array();
    $LoanOutstandingServiceCharge = array();
    $LoanOutstandingTotal = array();
    $OverduePrincipal = array();
    $OverdueServiceCharge = array();
    $OverdueTotal = array();
    $SavingsProduct = array();
    $SavingsBalance = array();
    $BranchManagerName = array();
    $BranchAvailable = array();
    $ProductsAvailable = array();

    // Necessary Variables
    $UniqueBranch = 0;
    // $UniqueLoanProducts = 0;

    // function call and results
    $BranchInfos = $this->BranchQuery($RequestedBranchID);

    $ProCat = $this->LoanProductCategoryQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    $LoanProduct = $this->LoanProductQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    $NumberOfSamity = $this->NumberOfSamityQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    $NumberOfMember = $this->NumberOfMemberQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);
    // dd($NumberOfMember);

    $NumberOfBorrower = $this->NumberOfBorrowerQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat);

    // dd($NumberOfBorrower);

    $CurrentLoan = $this->CurrentLoanQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth);

    $LoanOutstandingPrinciple = $this->LoanOutstandingPrincipleQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth);

    // dd($LoanOutstandingPrinciple);

    $LoanOutstandingServiceCharge = $this->LoanOutstandingServiceChargeQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth);

    $LoanOutstandingTotal = $this->OutstandingTotalQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth);

    $OverduePrincipal = $this->OverduePrincipalQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth);

    $OverdueServiceCharge = $this->OverdueServiceChargeQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth);

    $OverdueTotal = $this->OverdueTotalQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth);

    $SavingsProduct = $this->SavingsProductQury();

    $SavingsBalance = $this->SavingsBalanceQuery($RequestedBranchID, $RequestedOrganization, $RequestedProCat, $RequestedYear, $RequestedMonth, $RequestedSavingsBalance);

    $BranchManagerName = $this->BranchManagerNameQuery($RequestedBranchID);

    // $BranchAvailable =

    // dd($BranchManagerName);

    foreach ($NumberOfMember as $key1 => $NumberOfMember1) {
      list($Br, $Prod) = explode("/", $key1);

      foreach ($BranchInfos as $key1a => $BranchInfos1) {
        // $NumberOfMemberTracker = $BranchInfos1->id;
        if ($BranchInfos1->id == $Br and array_sum($NumberOfMember) > 0 and $UniqueBranch != $BranchInfos1->id) {
          $BranchAvailable[] = $BranchInfos1;
          $UniqueBranch = $BranchInfos1->id;
        }
      }
    }

    // dd($LoanProduct);

    return view('microfin.reports.consolidatedBalancingReport.ConsolidatedBalancingReportTable', compact('BranchInfos', 'RequestedYear', 'RequestedMonth', 'RequestedBranchID',
    'ProCat', 'LoanProduct', 'NumberOfSamity', 'NumberOfBorrower', 'NumberOfMember', 'CurrentLoan', 'LoanOutstandingPrinciple', 'LoanOutstandingServiceCharge', 'LoanOutstandingTotal',
    'OverduePrincipal', 'OverdueServiceCharge', 'OverdueTotal', 'RequestedSavingsBalance', 'SavingsProduct', 'SavingsBalance', 'BranchManagerName', 'BranchAvailable'));
  }

}
