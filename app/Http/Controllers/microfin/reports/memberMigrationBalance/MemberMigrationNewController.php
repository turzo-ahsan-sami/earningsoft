<?php

namespace App\Http\Controllers\microfin\reports\memberMigrationBalance;
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

use App\Http\Controllers\microfin\MicroFinance;

class MemberMigrationNewController extends Controller {

	protected $MicroFinance;

	public function __construct() {

		$this->MicroFinance = New MicroFinance;
	}

    public function index() {

		$userBranchId=Auth::user()->branchId;
		if ($userBranchId==1) {
			$branchesOption = $this->MicroFinance->getBranchOptions(1);
		}else{
			$branchesOption = DB::table('gnr_branch')->where('id', $userBranchId)
							->select(DB::raw("CONCAT(LPAD(branchCode, 3, 0), ' - ', name) AS nameWithCode"), 'id')
							->pluck('nameWithCode', 'id')
							->all();
		}

    	$fundingOrgsOption = $this->MicroFinance->getFundingOrganizationList();

    	$filteringArray = array(
            'branchesOption'             => $branchesOption,
    		'fundingOrgsOption'          => $fundingOrgsOption,
    		// 'yearsOption'             => $yearsOption,
    		// 'monthsOption'            => $monthsOption,
    		// 'samityDayOption'         => $samityDayOption,
      //       'productCategoryOption'   => $productCategoryOption,
    		'userBranchId'   => $userBranchId,
    		);

    	return view('microfin.reports.memberMigrationBalance.reportFilteringPart', $filteringArray);
    }       //index

    public function allSamityReport($filteringArr) {
        // dd($filteringArr);

        $branchValue        =$filteringArr['branchValue'];
        $samityValue        =$filteringArr['samityValue'];
        $startDateValue     =$filteringArr['startDateValue'];
        $endDateValue       =$filteringArr['endDateValue'];
        $serviceChargeValue =$filteringArr['serviceChargeValue'];
        $fundingOrgValue    =$filteringArr['fundingOrgValue'];
        // $MicroFinance       =$filteringArr['MicroFinance'];
        //

        $endDateValue = Carbon::parse($endDateValue)->format('Y-m-d');

        // dd($endDateValue);

        if($fundingOrgValue==-1){
            $fundingOrgArr=DB::table('mfn_funding_organization')->pluck('id')->toArray();
        }else{
            $fundingOrgArr=[$fundingOrgValue];
        }


        $samitysInfo=DB::table('mfn_samity')
        ->where('branchId', $branchValue)
        ->select('id','name', 'code')
        ->get();


        $allSamityIdArr=DB::table('mfn_samity')
                        ->where('branchId', $branchValue)
                        ->pluck('id')
                        ->toArray();

        $membersOfBranchArr=DB::table('mfn_member_information')
                            ->where('branchId', $branchValue)
                            ->whereIn('samityId', $allSamityIdArr)
                            ->where('status', 1)
                            ->where('softDel', 0)
                            ->pluck('id')
                            ->toArray();

        $assignLoanProductIdsArr=DB::table('mfn_loan')
                                    ->whereIn('memberIdFk', $membersOfBranchArr)
                                    ->where('isLoanCompleted', 0)
                                    ->where('softDel', 0)
                                    ->pluck('productIdFk')
                                    ->toArray();

        $primaryLoanProductsArr=DB::table('mfn_member_information')
                                    ->whereIn('id', $membersOfBranchArr)
                                    ->where('softDel', 0)
                                    ->where('status', 1)
                                    ->pluck('primaryProductId')
                                    ->toArray();

        $loanProductIdsOfBranchArr=array_unique(array_merge($primaryLoanProductsArr, $assignLoanProductIdsArr));
        sort($loanProductIdsOfBranchArr);

        $loanProductsInfoOfBranch=DB::table('mfn_loans_product')
                                    ->whereIn('id', $loanProductIdsOfBranchArr)
                                    ->whereIn('fundingOrganizationId', $fundingOrgArr)
                                    ->where('softDel', 0)
                                    ->select('id','name','productCategoryId')
                                    ->get();

        // $loanProductIdsOfBranchArr=DB::table('mfn_loans_product')->whereIn('id', $loansOfBranchArr)->whereIn('fundingOrganizationId', $fundingOrgArr)->where('softDel', 0)->pluck('id')->toArray();

        $loanProductCategoryIdsOfBranchArr=DB::table('mfn_loans_product')
                                            ->whereIn('id', $loanProductIdsOfBranchArr)
                                            ->whereIn('fundingOrganizationId', $fundingOrgArr)
                                            ->where('softDel', 0)
                                            ->pluck('productCategoryId')
                                            ->toArray();

        $loanProductCategoryInfoOfBranch=DB::table('mfn_loans_product_category')
                                            ->whereIn('id', $loanProductCategoryIdsOfBranchArr)
                                            ->where('softDel', 0)
                                            ->select('id','name')
                                            ->get();

        $branchLoanProductArr = $this->MicroFinance->branchWiseProductArr($branchValue, 'loanProductId' );

        $dbSavingsDeposits = DB::table('mfn_savings_deposit')
                                ->where('softDel',0)
                                ->where('branchIdFk',$branchValue)
                                ->where('depositDate','<=',$endDateValue)
                                ->get();

        $dbSavingsWithdraws = DB::table('mfn_savings_withdraw')
                                ->where('softDel',0)
                                ->where('branchIdFk',$branchValue)
                                ->where('withdrawDate','<=',$endDateValue)
                                ->get();

        $dbSavingsMembers = DB::table('mfn_member_information')
                                ->where('softDel',0)
                                ->where('branchId',$branchValue)
                                ->where('admissionDate','<=',$endDateValue)
                                ->select('id','gender')
                                ->get();

        $reportingArr = $filteringArr+array(
                // 'loanProductArr'        => $loanProductArr,
                'branchLoanProductArr'  => $branchLoanProductArr,
                // 'matchLoanCount'        => $matchLoanCount,
                'fundingOrgArr'        => $fundingOrgArr,
                'samitysInfo'           => $samitysInfo,
                'loanProductsInfoOfBranch'        => $loanProductsInfoOfBranch,
                'loanProductIdsOfBranchArr'        => $loanProductIdsOfBranchArr,
                'loanProductCategoryIdsOfBranchArr'        => $loanProductCategoryIdsOfBranchArr,
                'loanProductCategoryInfoOfBranch'        => $loanProductCategoryInfoOfBranch,
                // 'serviceChargeValue'  => $serviceChargeValue,
                // 'fundingOrgValue'     => $fundingOrgValue,
                // 'MicroFinance'        => $this->MicroFinance,
                'startDate'         => $startDateValue,
                'endDateValue'      => $endDateValue,
                'dbSavingsDeposits'    => $dbSavingsDeposits,
                'dbSavingsWithdraws'    => $dbSavingsWithdraws,
                '$dbSavingsMembers'    => $dbSavingsMembers,
            );

        // dd($reportingArr);

        return $reportingArr;

    }       //allSamityReport



    public function singleSamityReport($filteringArr) {

        $branchValue        =$filteringArr['branchValue'];
        $samityValue        =$filteringArr['samityValue'];
        $startDateValue     =$filteringArr['startDateValue'];
        $endDateValue       =$filteringArr['endDateValue'];
        $serviceChargeValue =$filteringArr['serviceChargeValue'];
        $fundingOrgValue    =$filteringArr['fundingOrgValue'];
        // $MicroFinance       =$filteringArr['MicroFinance'];


        if($fundingOrgValue==-1){
            $fundingOrgArr=DB::table('mfn_funding_organization')->pluck('id')->toArray();
        }else{
            $fundingOrgArr=[$fundingOrgValue];
        }

        $membersOfSamityArr=DB::table('mfn_member_information')->where('branchId', $branchValue)->where('samityId', $samityValue)->where('status', 1)->pluck('id')->toArray();

        $assignLoanProductIdsArr=DB::table('mfn_loan')->whereIn('memberIdFk', $membersOfSamityArr)->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('productIdFk')->toArray();

        $primaryLoanProductsArr=DB::table('mfn_member_information')->where('branchId', $branchValue)->where('samityId', $samityValue)->where('status', 1)->where('softDel', 0)->pluck('primaryProductId')->toArray();

        $loanProductIdsOfBranchArr=array_unique(array_merge($primaryLoanProductsArr, $assignLoanProductIdsArr));
        sort($loanProductIdsOfBranchArr);

        $loanProductsInfoOfBranch=DB::table('mfn_loans_product')->whereIn('id', $loanProductIdsOfBranchArr)->whereIn('fundingOrganizationId', $fundingOrgArr)->where('softDel', 0)->select('id','name','productCategoryId')->get();


        $reportingArr = $filteringArr+array(

                'membersOfSamityArr'         => $membersOfSamityArr,
                'loanProductIdsOfBranchArr'  => $loanProductIdsOfBranchArr,
                'loanProductsInfoOfBranch'   => $loanProductsInfoOfBranch,
            );


        return $reportingArr;

    }       //singleSamityReport

    public function memberMigrationReport(Request $request) {
        // dd($request);

    	$branchValue               = (int)$request->filBranch;
    	$samityValue               = (int)$request->filSamity;
    	$endDateValue              = Carbon::parse($request->filEndDate)
                                     ->format('Y-m-d');
    	$serviceChargeValue        = (int)$request->filServiceCharge;
    	$fundingOrgValue           = (int)$request->filFundingOrg;
        $startDateValue            = DB::table('gnr_branch')
                                    ->where('id', $branchValue)
                                    ->value('branchOpeningDate');

        $savingDepositTypeArr      = DB::table('mfn_savings_deposit_type')
                                      ->where('softDel', 0)
                                      ->pluck('id')
                                      ->toArray();

        $savingProductArr          = DB::table('mfn_saving_product')->where('softDel', 0)->orderBy('code')->pluck('name','id')->toArray();
        $savingProductIdArr        = DB::table('mfn_saving_product')->where('softDel', 0)->orderBy('code')->pluck('id')->toArray();


    	$filteringArr = array(
    		'branchValue'         => $branchValue,
    		'samityValue'         => $samityValue,
            'startDateValue'      => $startDateValue,
    		'endDateValue'        => $endDateValue,
    		'serviceChargeValue'  => $serviceChargeValue,
            'fundingOrgValue'     => $fundingOrgValue,
            'MicroFinance'        => $this->MicroFinance,
            'savingProductArr'    => $savingProductArr,
    		'savingProductIdArr'  => $savingProductIdArr
    		);

        // var_dump($filteringArr);
        // exit();

        // dd($filteringArr, $request);

        // return view('microfin.reports.memberMigrationBalance.allSamityReport', $filteringArr);

        if ($samityValue==-1) {

            $reportingArr=$this->allSamityReport($filteringArr);

            return view('microfin.reports.memberMigrationBalanceNew.allSamityReport', $reportingArr);

        }
		else {

            $reportingArr=$this->singleSamityReport($filteringArr);

            return view('microfin.reports.memberMigrationBalanceNew.singleSamityReport',$reportingArr);

        }

    }       //memberMigrationReport






}       //MemberMigrationController
