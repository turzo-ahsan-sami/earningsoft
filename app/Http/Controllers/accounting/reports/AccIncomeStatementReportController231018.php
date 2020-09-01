<?php

namespace App\Http\Controllers\accounting\reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\accounting\AddVoucher;
use App\accounting\AddVoucherDetails;
use App\accounting\AddLedger;
use App\accounting\AddVoucherType;
use App\gnr\GnrProject;
use App\gnr\GnrProjectType;
use App\gnr\GnrBranch;
use Validator;
use Response;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\microfin\MicroFin;
use App\Service\Service;


class AccIncomeStatementReportController extends Controller{

    public function index(){
        $userBranchId = Auth::user()->branchId;

        $dbBranches = DB::table('gnr_branch')->select('id','branchCode','name')->get();
        $dbAreas = DB::table('gnr_area')->select('id','code','name','branchId')->get();
        $dbZones = DB::table('gnr_zone')->select('id','code','name','areaId')->get();
        $dbRegions = DB::table('gnr_region')->select('id','code','name','zoneId')->get();
        
        $projectList = [''=>'--All--'] + MicroFin::getProjectList();
        $projectTypeList = [''=>'--All--'] + MicroFin::getProjectTypeList();
        $branchList = MicroFin::getBranchList();
        $fiscalYearList = MicroFin::getFiscalYearList();

        $reportLevelList = array(
            'Branch'  =>  'Branch',
            'Area'    =>  'Area',
            'Zone'    =>  'Zone',
            'Region'  =>  'Region'
        );

        $data = array(
            'userBranchId'      => $userBranchId,
            'dbBranches'        => $dbBranches,
            'dbAreas'           => $dbAreas,
            'dbZones'           => $dbZones,
            'dbRegions'         => $dbRegions,
            'projectList'       => $projectList,
            'projectTypeList'   => $projectTypeList,
            'branchList'        => $branchList,
            'fiscalYearList'    => $fiscalYearList,
            'reportLevelList'   => $reportLevelList,
        );

        return view('accounting/reports/incomeStatement/filteringPart',$data);
    }

    public function getRport(Request $req){

        $filBranchIds = MicroFin::getFilteredBranhIds($req->filReportLevel,$req->filBranch,$req->filArea,$req->filZone,$req->filRegion);

        if ($req->filProject!='') {
            $ledgerIds = MicroFin::getFilteredLedgerIds($req->filProject,$filBranchIds,1);
            $ledgers = DB::table('acc_account_ledger')->whereIn('id',$ledgerIds)->whereIn('accountTypeId',[12,13])->orderBy('ordering', 'asc')->get();
        }
        else{
            $ledgers = DB::table('acc_account_ledger')->whereIn('accountTypeId',[12,13])->orderBy('ordering', 'asc')->get();
        }

        $incomeLedgers = $ledgers->where('isGroupHead',0)->where('accountTypeId',12);
        $expenceLedgers = $ledgers->where('isGroupHead',0)->where('accountTypeId',13);

        // $traLedgerIds = $ledgers->where('parentId',0)->pluck('id')->toArray();

        $dbVouchers = DB::table('acc_voucher');
        $dbOpeningBalance = DB::table('acc_opening_balance');

        ///// filtering

        /*Branch*/
        if ($req->filReportLevel=='Branch') {
            if ($req->filBranch!='') {
                if ($req->filBranch=='allBranch') {
                    $dbVouchers = $dbVouchers->where('branchId','!=',1);
                    $dbOpeningBalance = $dbOpeningBalance->where('branchId','!=',1);
                }
                else{
                    $dbVouchers = $dbVouchers->where('branchId',$req->filBranch);
                    $dbOpeningBalance = $dbOpeningBalance->where('branchId',$req->filBranch);
                }
            }
        }
        else{
            $dbVouchers = $dbVouchers->whereIn('branchId',$filBranchIds);
            $dbOpeningBalance = $dbOpeningBalance->whereIn('branchId',$filBranchIds);
        }
        /*end branch*/

        /*Project*/
        if ($req->filProject!='') {
            $dbVouchers = $dbVouchers->where('projectId',$req->filProject);
            $dbOpeningBalance = $dbOpeningBalance->where('projectId',$req->filProject);
        }
        /*end Project*/

        /*Project Type*/
        if ($req->filProjectType!='') {
            $dbVouchers = $dbVouchers->where('projectTypeId',$req->filProjectType);
            $dbOpeningBalance = $dbOpeningBalance->where('projectTypeId',$req->filProjectType);
        }
        /*end Project Type*/
        
        ///// end filtering

        $dbOpeningBalance = $dbOpeningBalance->select('openingDate','ledgerId','debitAmount','creditAmount')->get();


        if ($req->searchMethod=='Fiscal Year') {
            
            $dbFiscalyear = DB::table('gnr_fiscal_year')->get();
            $currentFiscalYear = $dbFiscalyear->where('id',$req->filFiscalYear)->first();
            $preFiscalYear = $dbFiscalyear->where('fyEndDate',Carbon::parse($currentFiscalYear->fyStartDate)->subDay()->format('Y-m-d'))->first();

            /*NOTE: if it is searching previous fiscal years, then we will show the report from the opening balance only, we will not consider the vouchers. If $minRunningDay is greater than the $currentFiscalYear end date, then will take data from acc_opening_balance only, else for the current fiscal year we will take data from voucher table.*/
            $minRunningDay = DB::table('acc_day_end')->whereIn('branchIdFk',$filBranchIds)->where('isDayEnd',1)->min('date');

            if ($minRunningDay>$currentFiscalYear->fyEndDate) {
                // Current Fiscal Year Closing Balance
                $cfyCB = $dbOpeningBalance->where('openingDate',$currentFiscalYear->fyEndDate);
                // Current Fiscal Year Opening Balance
                $cfyOB = $dbOpeningBalance->where('openingDate',Carbon::parse($currentFiscalYear->fyStartDate)->subDay(1)->format('Y-m-d'));

                // make the result
                $cfyR = collect();
                foreach ($incomeLedgers as $ledger) {
                    $balance = ($cfyCB->where('ledgerId',$ledger->id)->sum('creditAmount') - $cfyCB->where('ledgerId',$ledger->id)->sum('debitAmount')) - ($cfyOB->where('ledgerId',$ledger->id)->sum('creditAmount') - $cfyOB->where('ledgerId',$ledger->id)->sum('debitAmount'));
                    $result = array(
                        'ledgerId'  => $ledger->id,
                        'balance'  => $balance
                    );

                    $cfyR->push($result);
                }
                foreach ($expenceLedgers as $ledger) {
                    $balance = ($cfyCB->where('ledgerId',$ledger->id)->sum('debitAmount') - $cfyCB->where('ledgerId',$ledger->id)->sum('creditAmount')) - ($cfyOB->where('ledgerId',$ledger->id)->sum('debitAmount') - $cfyOB->where('ledgerId',$ledger->id)->sum('creditAmount'));
                    $result = array(
                        'ledgerId'  => $ledger->id,
                        'balance'  => $balance
                    );

                    $cfyR->push($result);
                }
            }

            // make result for parents
            $parentLedgers = $ledgers->where('isGroupHead',1);
            foreach ($parentLedgers as $parentLedger) {
                $childIds = MicroFin::getAllChildsOfAParentInLedger($parentLedger->id);
                $balance = $cfyR->whereIn('ledgerId',$childIds)->sum('balance');
                $result = array(
                    'ledgerId'  => $parentLedger->id,
                    'balance'  => $balance
                );

                $cfyR->push($result);
            }


        } /*end Fiscal Year*/

        if ($req->searchMethod=='Current Year') {
            
        }/*end Current Year*/


        $data = array(
            'ledgers'   => $ledgers,
            'cfyR'      => $cfyR,
        );

        return view('accounting/reports/incomeStatement/reportBodyFiscalYear',$data);
    }


}