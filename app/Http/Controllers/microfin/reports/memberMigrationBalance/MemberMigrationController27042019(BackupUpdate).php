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
use App\microfin\loan\MfnLoanCollection;

// use App\microfin\loan\MfnMemberInformation;

class MemberMigrationController extends Controller {

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
                'endDateValue'      => $endDateValue
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

        // $membersOfSamityArr=DB::table('mfn_member_information')->where('branchId', $branchValue)->where('samityId', $samityValue)->where('status', 1)->pluck('id')->toArray();

        $membersOfSamityArr = DB::table('mfn_member_information')
            ->where(function ($query) use ($endDateValue, $branchValue, $samityValue) {
                $query->where([['samityId', $samityValue], ['branchId', $branchValue], ['closingDate', '>=', $endDateValue], ['softDel', '=', 0]])
                ->orWhere([['samityId', $samityValue], ['branchId', $branchValue], ['closingDate', '=', '0000-00-00'], ['softDel', '=', 0]]);
            })
            ->pluck('id')->toArray();

        // $assignLoanProductIdsArr=DB::table('mfn_loan')->whereIn('memberIdFk', $membersOfSamityArr)->where('isLoanCompleted', 0)->where('softDel', 0)->pluck('productIdFk')->toArray();

        $assignLoanProductIdsArr=DB::table('mfn_loan')
            ->where(function ($query) use ($endDateValue, $branchValue, $samityValue) {
                $query->where([['loanCompletedDate', '>=', $endDateValue], ['softDel', '=', 0]])
                ->orWhere([['loanCompletedDate', '=', '0000-00-00'], ['softDel', '=', 0]]);
            })
            ->whereIn('memberIdFk', $membersOfSamityArr)
            ->pluck('productIdFk')->toArray();

        // $primaryLoanProductsArr=DB::table('mfn_member_information')->where('branchId', $branchValue)->where('samityId', $samityValue)->where('softDel', 0)->pluck('primaryProductId')->toArray();

        $primaryLoanProductsArr = DB::table('mfn_member_information')
            ->where(function ($query) use ($endDateValue, $branchValue, $samityValue) {
                $query->where([['samityId', $samityValue], ['branchId', $branchValue], ['closingDate', '>=', $endDateValue], ['softDel', '=', 0]])
                ->orWhere([['samityId', $samityValue], ['branchId', $branchValue], ['closingDate', '=', '0000-00-00'], ['softDel', '=', 0]]);
            })
            ->pluck('primaryProductId')->toArray();

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

    // NEW MEMBER MIGRATION REPORT HAS STARTED
    public function memberMigrationReport (Request $req) {
        // dd($req->filServiceCharge);
        // dd(MfnLoanCollection::where('softDel',0)->where('amount','>',0));
        // $transactionCollection = MfnLoanCollection::where('softDel',0)->where('amount','>',0)->where('status', '=', 1);
        // $memberCollection = MfnMemberInformation::where('softDel',0)->where('status', '=', 1);
        // dd($memberCollection->get(), $transactionCollection);

        $branchCollection            = DB::table('gnr_branch');
        $samityCollection            = DB::table('mfn_samity');
        $savingsProductCollection    = DB::table('mfn_saving_product');
        $memberInformationCollection = DB::table('mfn_member_information');
        $loanProductCollection       = DB::table('mfn_loans_product');
        $loanCollection              = DB::table('mfn_loan');

        $savingsProductInfo = $savingsProductCollection->select('id', 'name', 'shortName')->get();

        $branchName = $branchCollection->where('id', $req->filBranch)
                        ->pluck('name')
                        ->toArray();

        $branchCode = $branchCollection->where('id', $req->filBranch)
                        ->pluck('branchCode')
                        ->toArray();

        $branchNameAndCode = $branchName[0].' & '.str_pad($branchCode[0],3,0,STR_PAD_LEFT);

        if ($req->filSamity == '-1') {
            $samityNameAndCode = 'All';
        }
        else {
            $samityName = $samityCollection->where('id', $req->filSamity)
                        ->pluck('name')
                        ->toArray();

            $samityCode = $samityCollection->where('id', $req->filSamity)
                        ->pluck('code')
                        ->toArray();

            $samityNameAndCode = $samityName[0].' & '.$samityCode[0];
        }

        $startDateValue = DB::table('gnr_branch')
                        ->where('id', $req->filBranch)
                        ->value('branchOpeningDate');

        $endDateValue   = date_format(date_create($req->filEndDate), 'Y-m-d');

        // FOR SINGLE SAMITY START
        if ($req->filSamity != '-1') {
            $branchValue               = (int)$req->filBranch;
            $samityValue               = (int)$req->filSamity;
            $endDateValue              = Carbon::parse($req->filEndDate)
                                        ->format('Y-m-d');
            $serviceChargeValue        = (int)$req->filServiceCharge;
            $fundingOrgValue           = (int)$req->filFundingOrg;
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
                'savingProductIdArr'  => $savingProductIdArr,
                'branchNameAndCode'  => $branchNameAndCode,
                'samityNameAndCode'  => $samityNameAndCode,
                'startDateValue'     => $startDateValue,
                'endDateValue'       => $endDateValue
                
            );

            $reportingArr=$this->singleSamityReport($filteringArr);

            return view('microfin.reports.memberMigrationBalance.singleSamityReport',$reportingArr);
        }
        // FOR SINGLE SAMITY END

        // FOR ALL SAMITY CALCULATION START
        $getAllSamity = $samityCollection->where('branchId', $req->filBranch)
                        ->where(function ($query) use ($endDateValue) {
                            $query->where('closingDate', $endDateValue)
                            ->orWhere('closingDate', '=', '0000-00-00')
                            ->orWhere('closingDate', '=', null);
                        })
                        ->select('id', 'name', 'code')
                        ->get();

        // $getAllSamity = DB::table('mfn_samity')
        //                 ->where([['branchId', $req->filBranch], ['status', '=', 1]])
        //                 ->select('id', 'name', 'code')
        //                 ->get();
        // FOR ALL SAMITY CALCULATION END

        $damageData = array(
            'savingsProductInfo'          => $savingsProductInfo,
            'branchNameAndCode'           => $branchNameAndCode,
            'samityNameAndCode'           => $samityNameAndCode,
            'startDateValue'              => $startDateValue,
            'endDateValue'                => $endDateValue,
            'getAllSamity'                => $getAllSamity,
            'memberInformationCollection' => $memberInformationCollection,
            'loanProductCollection'       => $loanProductCollection,
            'loanCollection'              => $loanCollection,
            'branchId'                    => $req->filBranch,
            'samityId'                    => $req->filSamity,
            'withServiceCharge'           => $req->filServiceCharge,
            'fundingOrg'                  => $req->filFundingOrg,
            // 'transactionCollection'       => $transactionCollection,
            // 'memberCollection'            => $memberCollection
        );

        // dd($memberInformationCollection->get());
        // dd($memberInformationCollection, $loanCollection);

        return view('microfin.reports.memberMigrationBalance.allSamityReport', $damageData);
        
    }
    // NEW MEMBER MIGRATION REPORT HAS ENDED

    public function memberMigrationReportOld(Request $request) {
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

        $mfnSavingsAccountCollection            = DB::table('mfn_savings_account')->where('branchIdFk', $branchValue);
        $mfnSavingsDepositCollection            = DB::table('mfn_savings_deposit')->where([['branchIdFk', $branchValue], ['depositDate', '<=', $endDateValue]]);
        $mfnOpeningSavingsAccountInfoCollection = DB::table('mfn_opening_savings_account_info');
        $mfnSavingsWithdrawCollection           = DB::table('mfn_savings_withdraw')->where([['branchIdFk', $branchValue], ['withdrawDate', '<=', $endDateValue]]);

    	$filteringArr = array(
    		'branchValue'         => $branchValue,
    		'samityValue'         => $samityValue,
            'startDateValue'      => $startDateValue,
    		'endDateValue'        => $endDateValue,
    		'serviceChargeValue'  => $serviceChargeValue,
            'fundingOrgValue'     => $fundingOrgValue,
            'MicroFinance'        => $this->MicroFinance,
            'savingProductArr'    => $savingProductArr,
            'savingProductIdArr'  => $savingProductIdArr,
            'mfnSavingsAccountCollection' => $mfnSavingsAccountCollection,
            'mfnSavingsDepositCollection' => $mfnSavingsDepositCollection,
            'mfnOpeningSavingsAccountInfoCollection' => $mfnOpeningSavingsAccountInfoCollection,
            'mfnSavingsWithdrawCollection'           => $mfnSavingsWithdrawCollection
    	);

        // var_dump($filteringArr);
        // exit();

        // dd($filteringArr, $request, $mfnSavingsAccountCollection, $mfnSavingsDepositCollection, $mfnOpeningSavingsAccountInfoCollection, $mfnSavingsWithdrawCollection);

        // return view('microfin.reports.memberMigrationBalance.allSamityReport', $filteringArr);

        if ($samityValue==-1) {

            $reportingArr=$this->allSamityReport($filteringArr);

            return view('microfin.reports.memberMigrationBalance.allSamityReport', $reportingArr);

        }
		else {

            $reportingArr=$this->singleSamityReport($filteringArr);

            return view('microfin.reports.memberMigrationBalance.singleSamityReport',$reportingArr);

        }

    }       //memberMigrationReport






}       //MemberMigrationController
