<?php

namespace App\Http\Controllers\microfin\reports\regularNGeneralReports;
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

class LoanClassificationReportController extends Controller {

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

        // Report Type
        $reportType = array(
            '1' => 'Half Yearly Report',
            '2' => 'Monthly Report',
        );
       
        // Year
        $yearsOption = MicroFin::getYearsOption();

        // Month
        $monthsOption = MicroFin::getMonthsOption();

        // LLP Options
        $llpOptions = array(
            '6' => 'Jan - Jun',
            '12' => 'Jul - Dec'
        );

        // Funding Organization List
        $fundingOrgList = DB::table('mfn_funding_organization')
                                ->pluck('name','id')
                                ->toArray();
        /// Loan Options
        $loanOptions = array(
            1   => 'Loan Product',
            2   => 'Loan Product Category'
        );

        $filteringArray = array(
            'branchList'          => $branchList,
            'reportType'          => $reportType,
            'yearsOption'         => $yearsOption,
            'monthsOption'        => $monthsOption,
            'llpOptions'          => $llpOptions,
            'fundingOrgList'      => $fundingOrgList,
            'loanOptions'         => $loanOptions,
            'userBranchId'        => $userBranchId
        );
        return view('microfin.reports.regularNGeneralReports.loanClassificationReport.reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){
        
        if ($req->filBranch=='' || $req->filBranch==null) {
            $filBranchIds = DB::table('gnr_branch')->pluck('id')->toArray();
        }
        else{
            $filBranchIds = [$req->filBranch];
        }

        if ($req->filReportType==1) {
            $month = str_pad($req->filLlpPeriod, 2, '0',STR_PAD_LEFT);
            if ($req->filLlpPeriod==6) {
                $reportDuration = '01-01-'.$req->filYear.' to '.'30-06-'.$req->filYear;
            }
            else{
                $reportDuration = '01-07-'.$req->filYear.' to '.'31-12-'.$req->filYear;
            }
        }
        else{
            $month = str_pad($req->filMonth, 2, '0',STR_PAD_LEFT);
            $monthEndDate = Carbon::parse('01-'.$month.'-'.$req->filYear)->endOfMonth()->format('d-m-Y');
            $reportDuration = '01-'.$month.'-'.$req->filYear.' to '.$monthEndDate;
        }
        
        $filDate = Carbon::parse('01-'.$month.'-'.$req->filYear)->endOfMonth()->format('Y-m-d');
        

        // Month End Loan Info
        $loanInfo = DB::table('mfn_month_end_process_loans')
                            ->whereIn('branchIdFk',$filBranchIds)
                            ->where('date',$filDate)
                            ->get();

        $fundingOrgs = DB::table('mfn_funding_organization');

        if ($req->filFundingOrg!='') {
            $fundingOrgs = $fundingOrgs->where('id',$req->filFundingOrg);
        }

        $activeFundingOrgIds = DB::table('mfn_loans_product')
                                ->whereIn('id',$loanInfo->pluck('productIdFk'))
                                ->pluck('fundingOrganizationId')
                                ->toArray();

        $loanProducts = DB::table('mfn_loans_product')
                            ->whereIn('id',$loanInfo->pluck('productIdFk'))
                            ->select('id','name','shortName','productCategoryId','fundingOrganizationId','isPrimaryProduct')
                            ->get();

        $loanCategories = DB::table('mfn_loans_product_category')
                            ->whereIn('id',$loanProducts->pluck('productCategoryId'))
                            ->select('id','name','shortName')
                            ->get();

        $fundingOrgs = $fundingOrgs->whereIn('id',$activeFundingOrgIds)->select('id','name','savingInterestRate','projectIdFk','projectTypeIdFk')->get();
        
        
        $data = array(
            'filBranch'         => $req->filBranch,
            'filReportType'     => $req->filReportType,
            'filYear'           => $req->filYear,
            'filMonth'          => $req->filMonth,
            'filFundingOrg'     => $req->filFundingOrg,
            'filLoanOption'     => $req->filLoanOption,
            'fundingOrgs'       => $fundingOrgs,
            'loanProducts'      => $loanProducts,
            'loanCategories'    => $loanCategories,
            'reportDuration'    => $reportDuration,
            'loanInfo'          => $loanInfo
        );        

        if ($req->filLoanOption==1) {
            return view('microfin.reports.regularNGeneralReports.loanClassificationReport.loanClassificationProductWise',$data);
        }
        else{
            return view('microfin.reports.regularNGeneralReports.loanClassificationReport.loanClassificationCategoryWise',$data);
        }
    }

   

}
