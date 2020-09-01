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

class MraDbms6ReportController extends Controller {

    public function index(){

        /*$ledger = DB::table('acc_account_ledger')->where('code','52800')->first();
        $temp = MicroFin::getAllChildsOfAParentInLedger($ledger->id);
        echo implode(',',$temp);
        exit();*/

        $fiscalYears = DB::table('gnr_fiscal_year')->get();

        $branchList = [''=>'--All--'] + MicroFin::getBranchList();

        $funOrgList =  [''=>'--All--'] + MicroFin::getFundingOrgList() + ['-1'=>'PKSF & Others'];

        $data = array(
            'userBranchId'  => Auth::user()->branchId,
            'branchList'   => $branchList,
            'funOrgList'   => $funOrgList,
            'fiscalYears'   => $fiscalYears,
        );    
        return view('microfin.reports.mra.mraDbms6.reportFilteringPart',$data);
    }

    public function getReport(Request $req){

        $currFY = DB::table('gnr_fiscal_year')->where('id','=',$req->filFiscalYear)->first();

        $preFY = null;
        $preFYendDate = null;

        if (count($currFY)>0) {
            $preFYendDate = Carbon::parse($currFY->fyStartDate)->subDay()->format('Y-m-d');
            $preFY = DB::table('gnr_fiscal_year')->where('fyEndDate','=',$preFYendDate)->first();
        }
        else{
            echo '<div style="text-align:center;">Data not available.</div>';
            exit();
        }        

        $openingBalanceInfo = DB::table('acc_opening_balance')
                                ->where('projectId',1)
                                ->where('openingDate',$preFYendDate);                                

        $voucherIds = DB::table('acc_voucher')
                        ->where('projectId',1)
                        ->where('voucherDate','>=',$currFY->fyStartDate)
                        ->where('voucherDate','<=',$currFY->fyEndDate);

        if ($req->filFunOrg!='' || $req->filFunOrg!=null) {

            if ($req->filFunOrg=='-1') {
                $funOrgs = DB::table('mfn_funding_organization')->whereIn('id',[1,2])->get();
                $projectIds = $funOrgs->unique('projectIdFk')->pluck('projectIdFk')->toArray();
                $projectTypeIds = $funOrgs->unique('projectTypeIdFk')->pluck('projectTypeIdFk')->toArray();

                $openingBalanceInfo = $openingBalanceInfo->whereIn('projectId',$projectIds)->whereIn('projectTypeId',$projectTypeIds);

                $voucherIds = $voucherIds->whereIn('projectId',$projectIds)->whereIn('projectTypeId',$projectTypeIds);
            }
            else{
                $funOrg = DB::table('mfn_funding_organization')->where('id',$req->filFunOrg)->first();
                $openingBalanceInfo = $openingBalanceInfo->where('projectId',$funOrg->projectIdFk)->where('projectTypeId',$funOrg->projectTypeIdFk);

                $voucherIds = $voucherIds->where('projectId',$funOrg->projectIdFk)->where('projectTypeId',$funOrg->projectTypeIdFk);
            }            
        }

        if ($req->filBranch!='' || $req->filBranch!=null) {
            $openingBalanceInfo = $openingBalanceInfo->where('branchId',$req->filBranch);

            $voucherIds = $voucherIds->where('branchId',$req->filBranch);
        }


        $openingBalanceInfo = $openingBalanceInfo->get();
        $voucherIds = $voucherIds->pluck('id')->toArray();

        $voucherDetails = DB::table('acc_voucher_details')
                                ->whereIn('voucherId',$voucherIds)
                                ->get();

        $ledgerSettings = DB::table('mfn_mra_ledger_mapping')->where('mraReportName','dbms6')->get();


        $data = array(
            'ledgerSettings'        => $ledgerSettings,
            'currFY'                => $currFY,
            'preFY'                 => $preFY,
            'openingBalanceInfo'    => $openingBalanceInfo,
            'voucherDetails'        => $voucherDetails,
        );
        return view('microfin.reports.mra.mraDbms6.reportBody',$data);
    }
}
