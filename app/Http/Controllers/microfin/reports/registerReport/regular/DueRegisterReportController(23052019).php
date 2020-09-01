<?php

namespace App\Http\Controllers\microfin\reports\registerReport\regular;
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

class DueRegisterReportController extends Controller {

    public function index() {

        $userBranchId=Auth::user()->branchId;
        
        /// Report Level
        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

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
        /// Area
        $areaList = DB::table('gnr_area')
        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'branchId')
        ->get();
        /// Zone
        $zoneList = DB::table('gnr_zone')
        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'areaId')
        ->get();
        /// Region
        $regionList = DB::table('gnr_region')
        ->select(DB::raw("CONCAT ( LPAD(code,3,0) ,' - ', name ) AS name"), 'id', 'zoneId')
        ->get();
        /// Year
        $yearsOption = MicroFin::getYearsOption();

        /// Month
        $monthsOption = MicroFin::getMonthsOption();

        /// Loan Options
        $loanOptions = array(
            1   => 'Product Wise',
            2   => 'Category Wise'
        );

        /// Due Types
        $dueTypes = array(
            1   => 'Current Due',
            2   => 'Over Due',
            3   => 'Regular Due'
        );

        /// Funding Organization List
        $fundingOrgList = DB::table('mfn_funding_organization')
        ->pluck('name','id')
        ->toArray();

        $filteringArray = array(
            'reportLevelList'     => $reportLevelList,
            'areaList'            => $areaList,
            'zoneList'            => $zoneList,
            'regionList'          => $regionList,
            'branchList'          => $branchList,
            'yearsOption'         => $yearsOption,
            'monthsOption'        => $monthsOption,
            'loanOptions'         => $loanOptions,
            'dueTypes'            => $dueTypes,
            'fundingOrgList'      => $fundingOrgList,
            'userBranchId'        => $userBranchId
        );

        return view('microfin/reports/registerReport/regular/dueRegiser/reportFilteringPart', $filteringArray);
    }

    public function getReport(Request $req){
        if ($req->filBranch == '' || $req->filBranch == null) {
            $req->filBranch = Auth::user()->branchId;
        }

        // dd($req, $req->filBranch);
        // Auth::user()->branchId

        $dateTo = Carbon::parse($req->filDateTo)->format('Y-m-d');
        $branchId = $req->filBranch;

        $loans = DB::table('mfn_loan as t1')
        ->join('mfn_samity as t2','t1.samityIdFk','t2.id')
        ->where('t1.softDel',0)
        ->where(function($query) use ($dateTo){
            $query->where('t1.loanCompletedDate','>',$dateTo)
            ->orWhere('t1.loanCompletedDate','0000-00-00')
            ->orWhere('t1.loanCompletedDate','');
        })
        ->where('t1.branchIdFk',$req->filBranch);

        if ($req->filFieldOfficer!='' || $req->filFieldOfficer!=null) {
            $samityIds = DB::table('mfn_samity')
            ->where('softDel',0)
            ->where('fieldOfficerId',$req->filFieldOfficer)
            ->pluck('id')
            ->toArray();
            $loans = $loans->whereIn('t1.samityIdFk',$samityIds);            
        }

        if ($req->filLoanProduct!='' || $req->filLoanProduct!=null) {
            $loans = $loans->where('t1.productIdFk',$req->filLoanProduct);
        }
        else{
            if ($req->filFundingOrg!='' || $req->filFundingOrg!=null) {
                $loanProductIds = DB::table('mfn_loans_product')
                ->where('softDel',0)
                ->where('fundingOrganizationId',$req->filFundingOrg)
                ->pluck('id')
                ->toArray();
                $loans = $loans->whereIn('t1.productIdFk',$loanProductIds);
            }
            if ($req->filCategory!='' || $req->filCategory!=null) {
                $loanProductIds = DB::table('mfn_loans_product')
                ->where('softDel',0)
                ->where('productCategoryId',$req->filCategory)
                ->pluck('id')
                ->toArray();
                $loans = $loans->whereIn('t1.productIdFk',$loanProductIds);
            }
        }


        $loans = $loans->orderBy('t2.code')->orderBy('t1.memberIdFk')->select('t1.id','t1.loanCode','t1.productIdFk','t1.memberIdFk','t1.primaryProductIdFk','t1.samityIdFk','t1.disbursementDate','t1.loanAmount','t1.totalRepayAmount','t1.isLoanCompleted','t1.loanCompletedDate')->get();

        $allLoanIds = $loans->pluck('id')->toArray();
        $overDueLoanIds = [];
        foreach ($loans as $loan) {
            $thisLoanMaxScheduleDate = DB::table('mfn_loan_schedule')
            ->where('softDel',0)
            ->where('loanIdFk',$loan->id)
            ->max('scheduleDate');
            if ($thisLoanMaxScheduleDate<$dateTo) {
                array_push($overDueLoanIds, $loan->id);
            }
        }

        // dd($overDueLoanIds);
        $regularLoanIds = array_diff($allLoanIds, $overDueLoanIds);

        if ($req->filDueType==1 || $req->filDueType==3) {
            $loans = $loans->whereIn('id',$regularLoanIds);
        }
        // over due
        elseif ($req->filDueType==2) {
            $loans = $loans->whereIn('id',$overDueLoanIds);
        }


        $contedLoanIds = [];
        $loanInfo = [];

        $openingLoanInfo = DB::table('mfn_opening_balance_loan')->where('softDel',0)->whereIn('loanIdFk',$loans->pluck('id'))->select('loanIdFk','paidLoanAmountOB','principalAmountOB')->get();

        // with service charge
        if ($req->filServiceCharge==1) {

            foreach ($loans as $loan) {
                $amountToPay =  DB::table('mfn_loan_schedule')
                ->where('softDel',0)
                ->where('loanIdFk',$loan->id)
                ->where('scheduleDate','<=',$dateTo)
                ->sum('installmentAmount'); 

                $amountPaid = DB::table('mfn_loan_collection')
                ->where('softDel',0)
                ->where('loanIdFk',$loan->id)
                ->where('collectionDate','<=',$dateTo)
                ->sum('amount');

                $amountPaid += $openingLoanInfo->where('loanIdFk',$loan->id)->sum('paidLoanAmountOB');

                $amountPaid +=  DB::table('mfn_loan_waivers')
                ->where('loanIdFk',$loan->id)
                ->where('date','<=',$dateTo)
                ->sum('principalAmount');
                
                $amountPaid +=  DB::table('mfn_loan_rebates')
                ->where('loanIdFk',$loan->id)
                ->where('date','<=',$dateTo)
                ->sum('amount');
                
                $amountPaid +=  DB::table('mfn_loan_write_off')
                ->where('loanIdFk',$loan->id)
                ->where('date','<=',$dateTo)
                ->sum('amount');

                $dueAmount = $amountToPay - $amountPaid;
                $dueAmount = $dueAmount<=0 ? 0 : $dueAmount;

                $outstandingAmount = $loan->totalRepayAmount - $amountPaid;
                    ////////////                  

                if ($dueAmount>0) {
                    array_push($contedLoanIds, $loan->id);
                    $info = array(
                        'loanId'            => $loan->id,
                        'dueAmount'         => $dueAmount,
                        'outstandingAmount' => $outstandingAmount,
                    );
                    array_push($loanInfo,$info);
                }
            }
        }

            // without service charge
        else{
            foreach ($loans as $loan) {

             $amountToPay =  DB::table('mfn_loan_schedule')
             ->where('softDel',0)
             ->where('loanIdFk',$loan->id)
             ->where('scheduleDate','<=',$dateTo)
             ->sum('principalAmount'); 

             $amountPaid = DB::table('mfn_loan_collection')
             ->where('softDel',0)
             ->where('loanIdFk',$loan->id)
             ->where('collectionDate','<=',$dateTo)
             ->sum('principalAmount');

             $amountPaid += $openingLoanInfo->where('loanIdFk',$loan->id)->sum('principalAmountOB');

             $amountPaid +=  DB::table('mfn_loan_waivers')
             ->where('loanIdFk',$loan->id)
             ->where('date','<=',$dateTo)
             ->sum('principalAmount');

             $amountPaid +=  DB::table('mfn_loan_write_off')
             ->where('loanIdFk',$loan->id)
             ->where('date','<=',$dateTo)
             ->sum('principalAmount');

             $dueAmount = $amountToPay - $amountPaid;
             $dueAmount = $dueAmount<1 ? 0 : $dueAmount;

             $outstandingAmount = $loan->loanAmount - $amountPaid;

             if ($dueAmount>0) {
                array_push($contedLoanIds, $loan->id);
                $info = array(
                    'loanId'            => $loan->id,
                    'dueAmount'         => $dueAmount,
                    'outstandingAmount' => $outstandingAmount
                );
                array_push($loanInfo,$info);
            }
        }
    }


    $loans = $loans->whereIn('id',$contedLoanIds);        

    $samitys = DB::table('mfn_samity')->whereIn('id',$loans->pluck('samityIdFk'))->select('id','name')->get();

    $member = DB::table('mfn_member_information')->whereIn('id',$loans->pluck('memberIdFk'))->select('id','name','code')->get();

    $loanProducts = DB::table('mfn_loans_product')->whereIn('id',$loans->pluck('productIdFk'))->select('id','shortName','productCategoryId')->get();

    $loanCategories = DB::table('mfn_loans_product_category')
    ->whereIn('id',$loanProducts->pluck('productCategoryId'))
    ->select('id','shortName')
    ->get();

    $savingsDeposits = DB::table('mfn_savings_deposit')->where('softDel',0)->where('depositDate','<=',$dateTo)->whereIn('memberIdFk',$loans->pluck('memberIdFk'))->select('primaryProductIdFk','samityIdFk','memberIdFk','amount')->get();

    $savingsWithdraws = DB::table('mfn_savings_withdraw')->where('softDel',0)->where('withdrawDate','<=',$dateTo)->whereIn('memberIdFk',$loans->pluck('memberIdFk'))->select('primaryProductIdFk','samityIdFk','memberIdFk','amount')->get();

    $loanInfo = collect($loanInfo);

    $openingSavingsInfo = DB::table('mfn_opening_savings_account_info')->whereIn('memberIdFk',$loans->pluck('memberIdFk'))->get();

    $primmaryProductIds = DB::table('mfn_loans_product')->where('isPrimaryProduct',1)->pluck('id')->toArray();

    $data = array(
        'loans'                 => $loans,
        'filBranch'             => $branchId,
        'filDueType'            => $req->filDueType,
        'samitys'               => $samitys,
        'member'                => $member,
        'loanProducts'          => $loanProducts,
        'loanCategories'        => $loanCategories,
        'loanInfo'              => $loanInfo,
        'savingsDeposits'       => $savingsDeposits,
        'savingsWithdraws'      => $savingsWithdraws,
        'primmaryProductIds'    => $primmaryProductIds,
        'openingSavingsInfo'    => $openingSavingsInfo
    );        

    if ($req->filLoanOption==1) {
        return view('microfin.reports.registerReport.regular.dueRegiser.dueRegisterReportProduct',$data);
    }
    else{
        return view('microfin.reports.registerReport.regular.dueRegiser.dueRegisterReportCategory',$data);
    }
}

}
