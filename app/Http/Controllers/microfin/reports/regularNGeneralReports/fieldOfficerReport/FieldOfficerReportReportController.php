<?php

namespace App\Http\Controllers\microfin\reports\regularNGeneralReports\fieldOfficerReport;
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

class FieldOfficerReportReportController extends Controller {

    public function index(){

        $branchList = MicroFin::getBranchList();

        // Year
        $yearsOption = MicroFin::getYearsOption();

        // Month
        $monthsOption = MicroFin::getMonthsOption();

        // Category
        $categoryList = MicroFin::getAllProductCategoryList();

        $data = array(
            'branchList'    => $branchList,
            'yearsOption'   => $yearsOption,
            'monthsOption'  => $monthsOption,
            'categoryList'  => $categoryList,
        );

        return view('microfin.reports.regularNGeneralReports.fieldOfficerReport.reportFilteringPart', $data);  
    }

    public function getReport(Request $req){


        $fieldOfficer = DB::table('hr_emp_general_info')
                            ->where('id',$req->filFieldOfficer)
                            ->select('emp_id','emp_name_english')
                            ->first();

        $savingsProducts = DB::table('mfn_saving_product')
                                ->select('id','shortName')
                                ->get();

        $dates = explode(',',$req->filWeek);

        $startDate = $dates[0];
        $endDate = $dates[1];

        $samities = DB::select("SELECT t1.id, t1.name,t1.code, IF(?<t2.effectiveDate,t2.`fieldOfficerId`,t1.fieldOfficerId) as fieldOfficerId FROM `mfn_samity` as t1
                LEFT JOIN mfn_samity_field_officer_change as t2 ON t1.id = t2.samityId", [$startDate]);
        $samities = collect($samities);

        $samities = $samities->where('fieldOfficerId',$req->filFieldOfficer);

        

        /*$members = DB::select("SELECT t1.id, IF(t2.transferDate>?,t2.previousSamityIdFk,t1.`samityId`) as samityId FROM `mfn_member_information` as t1
            LEFT JOIN mfn_member_samity_transfer as t2 ON t1.id = t2.memberIdFk WHERE t1.admissionDate<=?", [$startDate,$startDate]);*/

            $members = DB::select("SELECT t1.id, IF(t2.transferDate>?,t2.previousSamityIdFk,t1.`samityId`) as samityId, IF(t3.transferDate>?,t3.oldPrimaryProductFk,t1.`primaryProductId`) as primaryProductId FROM `mfn_member_information` as t1
LEFT JOIN (SELECT memberIdFk,previousSamityIdFk,transferDate FROM mfn_member_samity_transfer WHERE softDel=0 AND transferDate>=? ORDER BY transferDate DESC) as t2 ON t1.id = t2.memberIdFk 
LEFT JOIN (SELECT memberIdFk,oldPrimaryProductFk,transferDate FROM mfn_loan_primary_product_transfer WHERE softDel=0 AND transferDate>=? ORDER BY transferDate DESC) as t3 ON t1.id = t3.memberIdFk
WHERE t1.admissionDate<=?", [$startDate,$startDate,$startDate,$startDate,$startDate]);

        $members = collect($members);

        if ($req->filLoanProduct!='' || $req->filLoanProduct!=null) {
            $members = $members->where('primaryProductId',$req->filLoanProduct);
        }
        else if($req->filProductCategory!='' || $req->filProductCategory!=null){
            $loanProductIds = DB::table('mfn_loans_product')->where('productCategoryId',$req->filProductCategory)->pluck('id')->toArray();
            $members = $members->whereIn('primaryProductId',$loanProductIds);
        }

        $members = $members->whereIn('samityId',$samities->pluck('id'));

        $savingsDeposit = DB::table('mfn_savings_deposit')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->whereIn('memberIdFk',$members->pluck('id'))
                                ->where('depositDate','<=',$endDate)
                                ->select('accountIdFk','samityIdFk','depositDate','productIdFk','amount')
                                ->get(); 

        $savingsWithdraw = DB::table('mfn_savings_withdraw')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->whereIn('memberIdFk',$members->pluck('id'))
                                ->where('withdrawDate','<=',$endDate)
                                ->select('accountIdFk','samityIdFk','withdrawDate','productIdFk','amount')
                                ->get();

        $savingsInterest = DB::table('mfn_savings_interest')
                                    ->whereIn('samityIdFk',$samities->pluck('id'))
                                    ->whereIn('memberIdFk',$members->pluck('id'))
                                    ->where('effectiveDate','<=',$endDate)
                                    ->get();


        //// Part-B
        $memberAdmission = DB::select("SELECT t1.id,IF(t2.transferDate>?,t2.previousSamityIdFk,t1.`samityId`) AS samityId FROM `mfn_member_information` as t1
LEFT JOIN mfn_member_samity_transfer as t2 ON t1.id=t2.memberIdFk
    WHERE t1.admissionDate>=? AND t1.admissionDate<=? AND t1.branchId=?", [$startDate,$startDate,$endDate,$req->filBranch]);
        $memberAdmission = collect($memberAdmission);
        $memberAdmission = $memberAdmission->whereIn('samityId',$samities->pluck('id'));

        $memberAdmissionByTransfer = DB::table('mfn_member_samity_transfer')
                                    ->where('softDel',0)
                                    ->where('transferDate','>=',$startDate)
                                    ->where('transferDate','<=',$endDate)
                                    ->whereIn('newSamityIdFk',$samities->pluck('id'))
                                    ->select('newSamityIdFk')
                                    ->get();

        $memberClosing = DB::table('mfn_member_closing')
                                ->where('softDel',0)
                                ->whereIn('samityIdFk',$samities->pluck('id'))
                                ->select('memberIdFk','samityIdFk')
                                ->get();

        $memberClosingByTransfer = DB::table('mfn_member_samity_transfer')
                                    ->where('softDel',0)
                                    ->where('transferDate','>=',$startDate)
                                    ->where('transferDate','<=',$endDate)
                                    ->whereIn('previousSamityIdFk',$samities->pluck('id'))
                                    ->select('previousSamityIdFk')
                                    ->get();

        $writeOffs = DB::table('mfn_loan_write_off')
                        ->whereIn('samityIdFk',$samities->pluck('id'));

        $loans = DB::table('mfn_loan')
                    ->where('softDel',0)
                    ->whereIn('samityIdFk',$samities->pluck('id'));
                    
        if ($req->filLoanProduct!='' || $req->filLoanProduct!=null) {
            $writeOffs = $writeOffs->where('productIdFk',$req->filLoanProduct);
            $loans = $loans->where('productIdFk',$req->filLoanProduct);
        }
        else if($req->filProductCategory!='' || $req->filProductCategory!=null){
            $loanProductIds = DB::table('mfn_loans_product')->where('productCategoryId',$req->filProductCategory)->pluck('id')->toArray();
            $writeOffs = $writeOffs->whereIn('productIdFk',$loanProductIds);
            $loans = $loans->whereIn('productIdFk',$loanProductIds);
        }
        $writeOffs = $writeOffs->where('date','<=',$endDate)->get();
        $loans = $loans->where('disbursementDate','<=',$endDate)->select('id','samityIdFk','disbursementDate','loanAmount','totalRepayAmount','insuranceAmount','additionalFee','lastInstallmentDate','isLoanCompleted','loanCompletedDate')
                    ->get();

        $collections = DB::table('mfn_loan_collection')
                            ->where('softDel',0)
                            ->whereIn('loanIdFk',$loans->pluck('id'))
                            ->where('collectionDate','<=',$endDate)
                            ->select('loanIdFk','samityIdFk','amount','principalAmount','collectionDate')
                            ->get();

        $schedules = DB::table('mfn_loan_schedule')
                            ->where('softDel',0)
                            ->whereIn('loanIdFk',$loans->pluck('id'))
                            ->where('scheduleDate','<=',$endDate)
                            ->select('loanIdFk','installmentAmount','principalAmount','scheduleDate')
                            ->get();

        /*if ($loans->where('id',952)->count()>0) {
            $temp = $loans->where('disbursementDate','<',$startDate)->where('isLoanCompleted',1)->where('loanCompletedDate','<',$startDate)->count();
            dd($temp,$startDate,$loans->where('id',952));
        }*/
        
        $data = array(
            'filBranch'                     => $req->filBranch,
            'filSavingsInterest'            => $req->filSavingsInterest,
            'filServiceCharge'              => $req->filServiceCharge,
            'fieldOfficer'                  => $fieldOfficer,
            'savingsProducts'               => $savingsProducts,
            'samities'                      => $samities,
            'members'                       => $members,
            'memberAdmission'               => $memberAdmission,
            'memberAdmissionByTransfer'     => $memberAdmissionByTransfer,
            'memberClosing'                 => $memberClosing,
            'memberClosingByTransfer'       => $memberClosingByTransfer,
            'loans'                         => $loans,

            'savingsDeposit'                => $savingsDeposit,
            'savingsInterest'               => $savingsInterest,
            'savingsWithdraw'               => $savingsWithdraw,
            'writeOffs'                     => $writeOffs,
            'collections'                   => $collections,
            'schedules'                     => $schedules,
            'startDate'                     => $startDate,
            'endDate'                       => $endDate,

            
        );

        return view('microfin.reports.regularNGeneralReports.fieldOfficerReport.fieldOfficerReport', $data);
        
        
    }

}
