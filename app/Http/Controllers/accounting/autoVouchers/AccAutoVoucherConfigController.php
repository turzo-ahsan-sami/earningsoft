<?php

namespace App\Http\Controllers\accounting\autoVouchers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\accounting\AccMisType;
use App\accounting\AddVoucherType;
use App\accounting\AccMisConfiguration;
use App\accounting\AccAutoVoucherConfig;
use Validator;
use Response;
use App\Http\Controllers\gnr\Service;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use DB;
use Carbon\Carbon;
use App\microfin\savings\MfnSavingsProduct; 
use App\microfin\loan\MfnProduct;
use App\Http\Controllers\accounting\Accounting;
use App\Http\Controllers\microfin\MicroFinance;

class AccAutoVoucherConfigController extends Controller
{
    protected $Accounting;
    protected $MicroFinance;

    public function __construct() {
        $this->Accounting = new Accounting;
        $this->MicroFinance = New MicroFinance;
    }


    public function viewAutoVoucherConfig(){

        $savingsProductOption=$this->MicroFinance->getSavingsProductsOption();
        // $loanProductOption=$this->MicroFinance->getLoanProductsOption();
        $loanProductOption= DB::table('mfn_loans_product')->where('status',1)->where('softDel',0)->select('id','name','fundingOrganizationId')->orderBy('fundingOrganizationId')->get();
        $fundOrgArr= DB::table('mfn_funding_organization')->where('status',1)->where('softDel',0)->pluck('name','id')->toArray();


        $loanConfig=DB::table('acc_mfn_av_config_loan')->select( 'id','loanProductId','ledgerIdForPrincipal', 'ledgerIdForInterest', 'ledgerIdForRiskInsurance')->get();
        $savingsConfig=DB::table('acc_mfn_av_config_saving')->select( 'id', 'loanProductId', 'savingProductId', 'ledgerIdForPrincipal', 'ledgerIdForInterest', 'ledgerIdForInterestProvision', 'ledgerIdForUnsettledClaim')->get();
        $sktConfig=DB::table('acc_mfn_av_config_interest_skt_amount')->select( 'id', 'loanProductId', 'ledgerIdForInsurence1', 'ledgerIdForInsurence2', 'ledgerIdForSktAmount')->get();

        $ledgerCodeArr=array('' => '', '0' => '')
        +
        DB::table('acc_account_ledger')->where('isGroupHead', 0)->pluck('code','id')->toArray();


        $childLedgerIdsArray=array();
        $childLedgerIds=DB::table('acc_account_ledger')->where('isGroupHead', 0)->select('code')->get();
        foreach ($childLedgerIds as $key => $childLedgerId) {
            array_push($childLedgerIdsArray, (int) $childLedgerId->code);
        }


        $passingArr = array('loanProductOption'     => $loanProductOption, 
            'fundOrgArr'            => $fundOrgArr, 
            'savingsProductOption'  => $savingsProductOption,
            'loanConfig'            => $loanConfig,
            'savingsConfig'         => $savingsConfig,
            'sktConfig'             => $sktConfig,
            'ledgerCodeArr'         => $ledgerCodeArr,
            'childLedgerIdsArray'   => $childLedgerIdsArray,
        );

        return view('accounting.autoVouchers.autoVoucherConfig.viewAutoVoucherConfig', $passingArr);
    }

    public function addAutoVoucherConfigItem(Request $request) {

        $now = Carbon::now();
        $ledgerCodeArr=array('0' => 0)+DB::table('acc_account_ledger')->where('isGroupHead', 0)->pluck('id','code')->toArray();

        $loanConfigInsert=[];
        $savingConfigInsert=[];
        $sktConfigInsert=[];

        foreach ($request->loanProductIdForLoan as $loanIndex => $loanValue) {

            if( ($request->principalCodeForLoan[$loanIndex]!=null) || ($request->interestCodeForLoan[$loanIndex]!=null) || ($request->riskInsuranceCodeForLoan[$loanIndex]!=null) ) {

                $loanConfigInsert[] = array(
                    'loanProductId'             => (int) $loanValue,
                    'ledgerIdForPrincipal'      => $ledgerCodeArr[(int) $request->principalCodeForLoan[$loanIndex]],
                    'ledgerIdForInterest'       => $ledgerCodeArr[(int) $request->interestCodeForLoan[$loanIndex]],
                    'ledgerIdForRiskInsurance'  => $ledgerCodeArr[(int) $request->riskInsuranceCodeForLoan[$loanIndex]],
                    'createdDate'               => $now
                );
            }
        }
        if (!empty($loanConfigInsert)) {
            DB::table('acc_mfn_av_config_loan')->truncate();
            $insertAutoConfigForLoan=DB::table('acc_mfn_av_config_loan')->insert($loanConfigInsert);
        }

        foreach ($request->savingProductIdForSaving as $savingsIndex => $savingsValue) {

            if( ($request->principalCodeForSaving[$savingsIndex]!=null) || ($request->interestCodeForSaving[$savingsIndex]!=null) || ($request->interestProvisionForSaving[$savingsIndex]!=null) || ($request->unsettledClaimForSaving[$savingsIndex]!=null) ) {

                $savingConfigInsert[] = array(         
                    'loanProductId'                 => (int) $request->loanProductIdForSaving[$savingsIndex],
                    'savingProductId'               => (int) $savingsValue,
                    'ledgerIdForPrincipal'          => $ledgerCodeArr[(int) $request->principalCodeForSaving[$savingsIndex]],
                    'ledgerIdForInterest'           => $ledgerCodeArr[(int) $request->interestCodeForSaving[$savingsIndex]],
                    'ledgerIdForInterestProvision'  => $ledgerCodeArr[(int) $request->interestProvisionForSaving[$savingsIndex]],
                    'ledgerIdForUnsettledClaim'     => $ledgerCodeArr[(int) $request->unsettledClaimForSaving[$savingsIndex]],
                    'createdDate'                   => $now
                );
            }
        }
        if (!empty($savingConfigInsert)) {
            DB::table('acc_mfn_av_config_saving')->truncate();
            $insertAutoConfigForSaving=DB::table('acc_mfn_av_config_saving')->insert($savingConfigInsert);
        }

        foreach ($request->loanProductIdForSKT as $sktIndex => $sktValue) {

            if( ($request->insurenceCode1ForSKT[$sktIndex]!=null) || ($request->insurenceCode2ForSKT[$sktIndex]!=null) || ($request->sktAmountCodeForSKT[$sktIndex]!=null) ) {

                $sktConfigInsert[] = array(
                    'loanProductId'         => (int) $sktValue,
                    'ledgerIdForInsurence1' => $ledgerCodeArr[(int) $request->insurenceCode1ForSKT[$sktIndex]],
                    'ledgerIdForInsurence2' => $ledgerCodeArr[(int) $request->insurenceCode2ForSKT[$sktIndex]],
                    'ledgerIdForSktAmount'  => $ledgerCodeArr[(int) $request->sktAmountCodeForSKT[$sktIndex]],
                    'createdDate'           => $now
                );
            }
        }

        if (!empty($sktConfigInsert)) {
            DB::table('acc_mfn_av_config_interest_skt_amount')->truncate();
            $insertAutoConfigForSKT=DB::table('acc_mfn_av_config_interest_skt_amount')->insert($sktConfigInsert);
        }

            // $data=array(
            //     // 'ledgerCodeArrphp'=>$ledgerCodeArrphp,
            //     'loanConfigInsert'   =>$loanConfigInsert,
            //     'savingConfigInsert' =>$savingConfigInsert,
            //     'sktConfigInsert'    =>$sktConfigInsert
            //     );

        if ( (isset($insertAutoConfigForLoan)) || (isset($insertAutoConfigForSaving)) || (isset($insertAutoConfigForSKT)) ) {
            $data = array(
                'responseTitle' =>  'Success!',
                'responseText'  =>  ' Auto Voucher Configuration Save Successful.'
            );
        }else{
            $data = array(
                'responseTitle' =>  'Warning!',
                'responseText'  =>  ' Auto Voucher Configuration Save Unsuccessful.'
            );          
        }


        return response()->json($data);
            // return response()->json(['responseText' => 'Data successfully inserted!'], 200);
    }

}
